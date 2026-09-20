<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReLife AI Pharmacy Assistant</title>
    
    <!-- Tailwind CSS & FontAwesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdfa',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                        }
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        .chat-scroll::-webkit-scrollbar { width: 5px; }
        .chat-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .typing-dot { animation: typing 1.4s infinite ease-in-out both; }
        .typing-dot:nth-child(1) { animation-delay: 0s; }
        .typing-dot:nth-child(2) { animation-delay: 0.2s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }
        @keyframes typing { 0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; } 40% { transform: scale(1); opacity: 1; } }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex flex-col">

    <!-- Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30">
        <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="#" class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-lg bg-brand-600 flex items-center justify-center text-white text-lg font-bold">
                    <i class="fa-solid fa-heart-pulse"></i>
                </div>
                <span class="text-xl font-bold text-slate-900">ReLife<span class="text-brand-600">.AI</span></span>
            </a>
            <button onclick="toggleChat()" class="bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 rounded-full text-xs font-semibold flex items-center gap-2 transition-colors">
                <i class="fa-solid fa-robot"></i> AI Assistant
            </button>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow max-w-6xl mx-auto px-4 py-8 w-full">
        <!-- Hero Banner -->
        <section class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm mb-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Welcome to ReLife</h1>
                <p class="text-slate-600 text-xs mt-1">Explore our 7 wellness products or ask our AI Assistant for details.</p>
            </div>
            <button onclick="toggleChat()" class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl text-xs transition-colors shadow-sm whitespace-nowrap">
                Ask AI Assistant
            </button>
        </section>

        <!-- Product Listing Section -->
        <section>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-capsules text-brand-600"></i> Our 7 Products
                </h2>
                <span class="text-xs text-slate-500 font-semibold bg-slate-200/60 px-2.5 py-1 rounded-full">
                    7 Products Available
                </span>
            </div>

            <!-- Product Grid Container -->
            <div id="productGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <!-- Javascript will render the 7 explicit products here -->
            </div>
        </section>
    </main>

    <!-- Chat Widget Popup -->
    <div id="chatWidget" class="fixed bottom-5 right-5 z-50 flex flex-col items-end">
        <!-- Auto-opened on load -->
        <div id="chatBox" class="flex w-[90vw] sm:w-[380px] h-[500px] bg-white rounded-2xl shadow-2xl border border-slate-200 flex-col overflow-hidden mb-3">
            
            <!-- Chat Header -->
            <div class="bg-slate-900 text-white p-4 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-brand-600 flex items-center justify-center text-white font-bold text-sm">
                        <i class="fa-solid fa-robot"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm">ReLife Assistant</h3>
                        <p class="text-[10px] text-emerald-400 font-medium">Connected to Local RAG</p>
                    </div>
                </div>
                <button onclick="toggleChat()" class="text-slate-400 hover:text-white text-sm"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <!-- Messages Area -->
            <div id="chatMessages" class="flex-1 p-4 overflow-y-auto space-y-3 bg-slate-50 text-xs chat-scroll">
                <div class="flex gap-2 items-start">
                    <div class="w-6 h-6 rounded-full bg-brand-600 text-white flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5">
                        <i class="fa-solid fa-robot"></i>
                    </div>
                    <div class="bg-white p-3 rounded-xl border border-slate-200 text-slate-800 max-w-[85%]">
                        Hello! 👋 Welcome to ReLife! How can I assist you with our products today?
                    </div>
                </div>
            </div>

            <!-- Quick Suggestions -->
            <div class="px-3 py-2 bg-white border-t border-slate-100 flex gap-1.5 overflow-x-auto text-[11px]">
                <button onclick="sendQuickPrompt('List all 7 products available at ReLife')" class="px-2.5 py-1 bg-slate-100 hover:bg-brand-50 text-slate-700 rounded-full border whitespace-nowrap">
                    📋 List 7 Products
                </button>
                <button onclick="sendQuickPrompt('Tell me about the Men\'s Performance Duo Combo')" class="px-2.5 py-1 bg-slate-100 hover:bg-brand-50 text-slate-700 rounded-full border whitespace-nowrap">
                    📦 Performance Duo
                </button>
            </div>

            <!-- Chat Input -->
            <div class="p-3 bg-white border-t border-slate-200">
                <form onsubmit="handleUserMessage(event)" class="flex items-center gap-2">
                    <input type="text" id="userChatInput" placeholder="Ask about any product..." class="flex-1 bg-slate-100 border border-slate-200 rounded-full px-3.5 py-2 text-xs focus:outline-none focus:border-brand-500">
                    <button type="submit" class="w-8 h-8 bg-brand-600 hover:bg-brand-700 text-white rounded-full flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-paper-plane text-xs"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Floating Toggle Button -->
        <button onclick="toggleChat()" class="bg-brand-600 hover:bg-brand-700 text-white p-4 rounded-full shadow-xl flex items-center justify-center">
            <i class="fa-solid fa-robot text-xl"></i>
        </button>
    </div>

    <script>
        // Exact 7 ReLife Products
        const productsList = [
            { id: 1, name: "Erection Support Capsules", price: "INR 1,499" },
            { id: 2, name: "Timing Support Capsules", price: "INR 1,499" },
            { id: 3, name: "Sperm Booster Capsules", price: "INR 1,299" },
            { id: 4, name: "Men's Wellness Oil", price: "INR 699" },
            { id: 5, name: "Men's Performance Duo Combo", price: "INR 2,499" },
            { id: 6, name: "Men's Performance & Vitality Combo", price: "INR 2,700" },
            { id: 7, name: "Performance & Vitality Plus Combo", price: "INR 3,999" }
        ];

        document.addEventListener('DOMContentLoaded', () => {
            renderProducts();
        });

        function renderProducts() {
            const grid = document.getElementById('productGrid');
            grid.innerHTML = '';

            productsList.forEach((item) => {
                const card = document.createElement('div');
                card.className = "bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow";
                
                card.innerHTML = `
                    <div>
                        <div class="flex items-center justify-between text-[11px] mb-2">
                            <span class="bg-brand-50 text-brand-700 px-2 py-0.5 rounded-md font-medium border border-brand-100">Product #${item.id}</span>
                            <span class="font-bold text-slate-900">${escapeHTML(item.price)}</span>
                        </div>
                        <h3 class="font-bold text-sm text-slate-800 mb-3 leading-snug">${escapeHTML(item.name)}</h3>
                    </div>
                    <button onclick="askAboutProduct('${escapeHTML(item.name)}')" class="w-full py-1.5 px-3 bg-slate-100 hover:bg-brand-600 hover:text-white text-slate-700 rounded-lg text-xs font-semibold flex items-center justify-center gap-1.5 transition-colors">
                        <i class="fa-solid fa-comment-dots"></i> Ask AI Details
                    </button>
                `;
                grid.appendChild(card);
            });
        }

        function askAboutProduct(productName) {
            const chatBox = document.getElementById('chatBox');
            if (chatBox.classList.contains('hidden')) {
                toggleChat();
            }
            sendQuickPrompt(`Tell me details about ${productName}`);
        }

        function toggleChat() {
            const chatBox = document.getElementById('chatBox');
            chatBox.classList.toggle('hidden');
            chatBox.classList.toggle('flex');
        }

        function sendQuickPrompt(text) {
            document.getElementById('userChatInput').value = text;
            handleUserMessage(new Event('submit'));
        }

        async function handleUserMessage(e) {
            e.preventDefault();
            const input = document.getElementById('userChatInput');
            const message = input.value.trim();
            if (!message) return;

            const chatContainer = document.getElementById('chatMessages');

            // Render User Query
            const userDiv = document.createElement('div');
            userDiv.className = "flex gap-2 items-start justify-end";
            userDiv.innerHTML = `<div class="bg-brand-600 text-white p-3 rounded-xl max-w-[85%]">${escapeHTML(message)}</div>`;
            chatContainer.appendChild(userDiv);
            input.value = '';
            chatContainer.scrollTop = chatContainer.scrollHeight;

            // Render Typing Indicator
            const typingDiv = document.createElement('div');
            typingDiv.id = "typingIndicator";
            typingDiv.className = "flex gap-2 items-start";
            typingDiv.innerHTML = `
                <div class="w-6 h-6 rounded-full bg-brand-600 text-white flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5">
                    <i class="fa-solid fa-robot"></i>
                </div>
                <div class="bg-white p-3 rounded-xl border border-slate-200 text-slate-800 flex gap-1 items-center">
                    <span class="w-1.5 h-1.5 bg-brand-500 rounded-full typing-dot"></span>
                    <span class="w-1.5 h-1.5 bg-brand-500 rounded-full typing-dot"></span>
                    <span class="w-1.5 h-1.5 bg-brand-500 rounded-full typing-dot"></span>
                </div>
            `;
            chatContainer.appendChild(typingDiv);
            chatContainer.scrollTop = chatContainer.scrollHeight;

            // Send payload to Python Flask backend
            try {
                const res = await fetch('https://align-session-create-arlington.trycloudflare.com/api/chat', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: message })
                });

                const data = await res.json();
                document.getElementById('typingIndicator')?.remove();

                const botDiv = document.createElement('div');
                botDiv.className = "flex gap-2 items-start";
                botDiv.innerHTML = `
                    <div class="w-6 h-6 rounded-full bg-brand-600 text-white flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5">
                        <i class="fa-solid fa-robot"></i>
                    </div>
                    <div class="bg-white p-3 rounded-xl border border-slate-200 text-slate-800 max-w-[85%] leading-relaxed">
                        ${escapeHTML(data.reply || "No reply received.")}
                    </div>
                `;
                chatContainer.appendChild(botDiv);
                chatContainer.scrollTop = chatContainer.scrollHeight;

            } catch (err) {
                document.getElementById('typingIndicator')?.remove();
                const errDiv = document.createElement('div');
                errDiv.className = "p-2.5 bg-red-50 text-red-600 rounded-lg text-[11px]";
                errDiv.innerText = "Error: Could not connect to Python backend (https://align-session-create-arlington.trycloudflare.com).";
                chatContainer.appendChild(errDiv);
            }
        }

        function escapeHTML(str) {
            return String(str).replace(/[&<>'"]/g, 
                tag => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[tag] || tag)
            );
        }
    </script>
</body>
</html>