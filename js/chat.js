/**
 * Chat Widget Logic
 * Injects a floating chat button and handles message storage (simulating sending to admin).
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Create Widget HTML
    const widget = document.createElement('div');
    widget.id = 'ify-chat-widget';
    widget.className = 'fixed bottom-6 right-6 z-50 flex flex-col items-end';

    widget.innerHTML = `
        <!-- Chat Window (Hidden by default) -->
        <div id="chat-window" class="hidden bg-white w-80 h-96 rounded-2xl shadow-2xl flex flex-col mb-4 overflow-hidden border border-gray-100 animate-fade-in-up">
            <div class="bg-primary p-4 text-white flex justify-between items-center">
                <h3 class="font-bold">Support Chat</h3>
                <button id="close-chat" class="hover:bg-white/20 rounded p-1">✕</button>
            </div>
            <div id="chat-messages" class="flex-1 p-4 overflow-y-auto space-y-3 bg-gray-50 border-b border-gray-100">
                <div class="bg-white p-3 rounded-lg rounded-tl-none shadow-sm max-w-[80%] text-sm text-gray-700">
                    Hello! Welcome to ifyTravels. How can I help you today?
                </div>
            </div>
            <form id="chat-form" class="p-3 bg-white flex gap-2">
                <input type="text" id="chat-input" class="flex-1 border rounded-full px-4 py-2 text-sm focus:outline-none focus:border-primary" placeholder="Type a message...">
                <button type="submit" class="bg-primary text-white rounded-full w-10 h-10 flex items-center justify-center hover:bg-teal-700 transition">➤</button>
            </form>
        </div>

        <!-- Toggle Button -->
        <button id="chat-toggle" class="bg-primary text-white w-14 h-14 rounded-full shadow-lg flex items-center justify-center text-2xl hover:bg-teal-700 transition transform hover:scale-110">
            💬
        </button>
    `;

    document.body.appendChild(widget);

    // 2. Event Listeners
    const toggleBtn = document.getElementById('chat-toggle');
    const chatWindow = document.getElementById('chat-window');
    const closeBtn = document.getElementById('close-chat');
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');
    const messagesContainer = document.getElementById('chat-messages');

    toggleBtn.addEventListener('click', () => {
        chatWindow.classList.toggle('hidden');
    });

    closeBtn.addEventListener('click', () => {
        chatWindow.classList.add('hidden');
    });

    chatForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const msg = chatInput.value.trim();
        if (!msg) return;

        // Add User Message to UI
        appendMessage(msg, 'user');
        chatInput.value = '';

        // Save to LocalStorage for Admin
        saveQueryToAdmin(msg);

        // Simulate Auto-Reply
        setTimeout(() => {
            appendMessage("Thanks! An agent will reply shortly.", 'bot');
        }, 1000);
    });

    function appendMessage(text, sender) {
        const div = document.createElement('div');
        const isUser = sender === 'user';
        div.className = `p-3 rounded-lg shadow-sm max-w-[80%] text-sm ${isUser ? 'bg-primary text-white ml-auto rounded-tr-none' : 'bg-white text-gray-700 rounded-tl-none'}`;
        div.textContent = text;
        messagesContainer.appendChild(div);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function saveQueryToAdmin(text) {
        const queries = JSON.parse(localStorage.getItem('userQueries')) || [];
        queries.push({
            id: Date.now(),
            user: "Guest User", // In real app, get logged in user
            message: text,
            date: new Date().toISOString(),
            read: false
        });
        localStorage.setItem('userQueries', JSON.stringify(queries));
    }
});
