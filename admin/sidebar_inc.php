<?php // admin/sidebar_inc.php ?>
<aside class="w-64 bg-white border-r border-gray-200 hidden md:flex flex-col">
    <div class="h-16 flex items-center px-6 border-b border-gray-200">
        <span class="text-xl font-bold text-blue-600">ifyAdmin</span>
    </div>
    <nav class="flex-1 p-4 space-y-1">
        <a href="dashboard.php"
            class="flex items-center space-x-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg hover:text-blue-600 transition">
            <span>📊</span> <span>Dashboard</span>
        </a>
        <a href="settings.php"
            class="flex items-center space-x-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg hover:text-blue-600 transition">
            <span>⚙️</span> <span>Site Settings</span>
        </a>
        <a href="destinations.php"
            class="flex items-center space-x-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg hover:text-blue-600 transition">
            <span>🌍</span> <span>Destinations</span>
        </a>
        <a href="packages.php"
            class="flex items-center space-x-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg hover:text-blue-600 transition">
            <span>📦</span> <span>Packages</span>
        </a>
        <a href="bookings.php"
            class="flex items-center space-x-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg hover:text-blue-600 transition">
            <span>📅</span> <span>Bookings</span>
        </a>
        <a href="testimonials.php"
            class="flex items-center space-x-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg hover:text-blue-600 transition">
            <span>💬</span> <span>Testimonials</span>
        </a>
        <a href="inquiries.php"
            class="flex items-center space-x-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg hover:text-blue-600 transition">
            <span>📩</span> <span>Inquiries</span>
        </a>
    </nav>
    <div class="p-4 border-t border-gray-200">
        <a href="logout.php"
            class="flex items-center space-x-3 px-4 py-2 text-red-500 hover:bg-red-50 rounded-lg transition">
            <span>🚪</span> <span>Logout</span>
        </a>
    </div>
</aside>