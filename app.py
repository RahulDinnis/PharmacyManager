from flask import Flask, request, jsonify
from flask_cors import CORS
import chromadb
import ollama

app = Flask(__name__)

# Enable CORS for all routes and allow requests from Cloudflare Tunnel domains
CORS(app, resources={r"/api/*": {"origins": "*"}})

# Initialize ChromaDB persistent client and collection
chroma_client = chromadb.PersistentClient(path="./chroma_db")
collection = chroma_client.get_collection(name="pharmacy_knowledge")

@app.route('/api/chat', methods=['POST'])
def chat():
    data = request.json or {}
    user_query = data.get('message', '').strip()

    if not user_query:
        return jsonify({"reply": "Please provide a valid query."}), 400

    try:
        # 1. Embed user query using 'search_query:' prefix for nomic-embed-text accuracy
        query_embedding_res = ollama.embeddings(
            model="nomic-embed-text",
            prompt=f"search_query: {user_query}"
        )
        query_embedding = query_embedding_res["embedding"]

        # 2. Retrieve top 6 matching entries to ensure broad queries receive enough context
        results = collection.query(
            query_embeddings=[query_embedding],
            n_results=6
        )

        retrieved_docs = results.get('documents', [[]])[0]
        context = "\n\n---\n\n".join(retrieved_docs) if retrieved_docs else "No matching information found in knowledge base."

        # 3. System prompt configured for ReLife Men's Wellness knowledge base
        system_prompt = (
            "You are an AI Assistant for ReLife Men's Wellness.\n"
            "Answer the user's question accurately, directly, and concisely using ONLY the provided knowledge base context below.\n"
            "If the exact answer is not available in the context, politely state that you do not have that specific information.\n"
            "Always include a brief note advising users to consult a medical doctor for personalized health advice.\n\n"
            f"KNOWLEDGE BASE CONTEXT:\n{context}"
        )

        # 4. Generate response using llama3.2:3b
        llm_response = ollama.chat(
            model="llama3.2:3b",
            messages=[
                {"role": "system", "content": system_prompt},
                {"role": "user", "content": user_query}
            ]
        )

        bot_reply = llm_response['message']['content']
        return jsonify({"reply": bot_reply})

    except Exception as e:
        print(f"Error handling chat request: {e}")
        return jsonify({"reply": "Sorry, an internal error occurred while processing your request."}), 500

if __name__ == '__main__':
    # Listen on 0.0.0.0 so Cloudflare Tunnel can successfully forward incoming traffic
    app.run(host='0.0.0.0', port=5000, debug=True)