import json
import chromadb
import ollama

def sync_knowledge_base():
    # 1. Load data from products.json
    try:
        with open('products.json', 'r', encoding='utf-8') as f:
            data = json.load(f)
    except FileNotFoundError:
        print("Error: products.json file not found!")
        return

    # 2. Connect to local ChromaDB
    chroma_client = chromadb.PersistentClient(path="./chroma_db")

    # Clear old collection to reset indexes
    try:
        chroma_client.delete_collection(name="pharmacy_knowledge")
    except Exception:
        pass

    # Create fresh collection
    collection = chroma_client.create_collection(name="pharmacy_knowledge")

    print(f"Starting ingestion of {len(data)} items into ChromaDB...")

    # 3. Generate embeddings via Ollama nomic-embed-text
    for index, item in enumerate(data):
        if 'question' in item and 'answer' in item:
            # Format text specifically for embedding clarity
            search_text = f"search_document: {item['question']} {item['answer']}"
            
            content = (
                f"Question: {item['question']}\n"
                f"Answer: {item['answer']}\n"
                f"Category: {item.get('category', 'General')}"
            )
            item_id = str(item.get('id', f"qa_{index+1}"))
            
            metadata = {
                "type": "qa",
                "category": str(item.get('category', 'General')),
                "question": str(item['question'])
            }
        else:
            search_text = f"search_document: {item.get('name', '')} {item.get('description', '')}"
            
            content = (
                f"Product ID: {item.get('id')}\n"
                f"Medicine Name: {item.get('name')}\n"
                f"Category: {item.get('category')}\n"
                f"Price: ${item.get('price')}\n"
                f"Description: {item.get('description')}\n"
                f"Dosage: {item.get('dosage')}\n"
                f"Side Effects: {item.get('side_effects')}\n"
                f"Prescription Required: {'Yes' if item.get('prescription_required', False) else 'No'}\n"
                f"In Stock: {item.get('stock', 0)} units"
            )
            item_id = str(item.get('id', f"prod_{index+1}"))
            
            metadata = {
                "type": "product",
                "name": str(item.get('name', '')),
                "category": str(item.get('category', ''))
            }

        # Generate local embedding using nomic prefixing for better retrieval accuracy
        response = ollama.embeddings(
            model="nomic-embed-text",
            prompt=search_text
        )
        embedding = response["embedding"]

        collection.upsert(
            ids=[item_id],
            embeddings=[embedding],
            documents=[content],
            metadatas=[metadata]
        )

    print(f"Sync complete! Successfully indexed {len(data)} items into ChromaDB.")

if __name__ == "__main__":
    sync_knowledge_base()