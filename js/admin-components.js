// admin-components.js
// Handles the Sidebar rendering and basic Admin functionality (Auth check)

const AdminComponents = {
    renderSidebar: (activePage) => {
        const sidebar = document.createElement('aside');
        sidebar.className = "w-64 bg-charcoal text-white flex flex-col h-full fixed left-0 top-0 bottom-0 z-50";

        const menuItems = [
            { name: 'Dashboard', link: 'dashboard.html', icon: '📊' },
            { name: 'Manage Packages', link: 'packages.html', icon: '📦' },
            { name: 'Bookings', link: 'bookings.html', icon: '📅' },
            { name: 'User Queries', link: 'queries.html', icon: '💬' },
            { name: 'Site Settings', link: 'settings.html', icon: '⚙️' },
        ];

        // Queries Badge Logic
        const queries = JSON.parse(localStorage.getItem('userQueries')) || [];
        const unreadQueries = queries.filter(q => !q.read).length;
        const queryBadge = unreadQueries > 0 ? `<span class="bg-red-500 text-xs px-2 py-0.5 rounded-full ml-auto">${unreadQueries}</span>` : '';

        sidebar.innerHTML = `
            <div class="p-6 border-b border-gray-700 flex items-center gap-2">
                <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center font-bold text-white text-xl">I</div>
                <h2 class="text-2xl font-bold tracking-tight">ify<span class="text-primary">Admin</span></h2>
            </div>
            
            <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-2 px-2">Main Menu</p>
                ${menuItems.map(item => {
            const isActive = activePage === item.name;
            const baseClass = "flex items-center gap-3 py-3 px-4 rounded-lg transition font-medium";
            const activeClass = "bg-primary text-white shadow-md";
            const inactiveClass = "text-gray-300 hover:bg-gray-800 hover:text-white";

            // Special logic for Queries badge
            const badge = item.name === 'User Queries' ? queryBadge : '';

            return `
                        <a href="${item.link}" class="${baseClass} ${isActive ? activeClass : inactiveClass}">
                            <span class="text-lg">${item.icon}</span>
                            ${item.name}
                            ${badge}
                        </a>
                    `;
        }).join('')}
            </nav>

            <div class="p-4 border-t border-gray-700 bg-gray-900/50">
                <a href="../index.html" target="_blank" class="flex items-center gap-3 py-2 px-4 rounded hover:bg-gray-800 transition text-sm text-gray-400 mb-2">
                    <span>🔗</span> View Live Website
                </a>
                <button onclick="AdminComponents.logout()" class="w-full flex items-center gap-3 py-2 px-4 rounded hover:bg-red-900/20 text-red-400 hover:text-red-300 transition text-sm font-bold">
                    <span>🚪</span> Logout
                </button>
            </div>
        `;

        // Inject into body
        // Ensure body has padding-left to accommodate fixed sidebar
        document.body.classList.add('pl-64');
        document.body.prepend(sidebar);
    },

    logout: () => {
        if (confirm('Log out of Admin Panel?')) {
            window.location.href = '../index.html';
        }
    },

    init: (activePage) => {
        document.addEventListener('DOMContentLoaded', () => {
            AdminComponents.renderSidebar(activePage);
        });
    }
};

// Expose globally
window.AdminComponents = AdminComponents;
