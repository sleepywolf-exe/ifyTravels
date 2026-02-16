<?php
$currentPage = basename($_SERVER['PHP_SELF']);
function navItem($file, $label, $iconPath, $activeColorClass = 'text-blue-600', $bgColorClass = 'bg-blue-50')
{
    global $currentPage;
    $isActive = ($currentPage === $file);

    // Extract base color text class for hover state if not active
    // Simple heuristic: usage like 'text-indigo-500' -> hover:text-indigo-600

    if ($isActive) {
        $className = "group flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 $bgColorClass $activeColorClass font-medium shadow-sm";
    } else {
        $className = "group flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 text-gray-500 hover:bg-gray-50 hover:$activeColorClass";
    }

    echo "<a href='$file' class='$className'>";
    // Icon
    echo "<svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5 transition-transform group-hover:scale-110' fill='none' viewBox='0 0 24 24' stroke='currentColor'>$iconPath</svg>";
    // Label
    echo "<span>$label</span>";
    echo "</a>";
}
?>
<aside class="w-64 bg-white border-r border-gray-200 hidden md:flex flex-col shadow-sm z-10 font-outfit">
    <div class="h-20 flex items-center px-8 border-b border-gray-50 mb-4">
        <a href="dashboard.php" class="flex items-center gap-2 group">
            <div
                class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-md group-hover:shadow-lg transition-all duration-300">
                i
            </div>
            <span
                class="text-xl font-bold text-gray-800 tracking-tight group-hover:text-blue-600 transition-colors">ify<span
                    class="text-blue-600">Admin</span></span>
        </a>
    </div>

    <nav class="flex-1 px-4 space-y-2 overflow-y-auto custom-scrollbar">
        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-2 mt-2">Main Menu</div>
        <?php
        navItem('dashboard.php', 'Dashboard', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />', 'text-indigo-600', 'bg-indigo-50');
        navItem('inquiries.php', 'Inquiries', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />', 'text-orange-500', 'bg-orange-50');
        navItem('bookings.php', 'Bookings', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />', 'text-blue-600', 'bg-blue-50');

        echo '<div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-2 mt-6">Management</div>';

        navItem('packages.php', 'Packages', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />', 'text-purple-600', 'bg-purple-50');
        navItem('blogs.php', 'Blogs', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />', 'text-teal-600', 'bg-teal-50');
        navItem('seo-dashboard.php', 'SEO Refresh', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />', 'text-indigo-500', 'bg-indigo-50');
        navItem('internal_linking_report.php', 'Link Graph', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />', 'text-pink-500', 'bg-pink-50');
        navItem('destinations.php', 'Destinations', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />', 'text-pink-600', 'bg-pink-50');
        navItem('affiliates.php', 'Affiliates', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />', 'text-emerald-600', 'bg-emerald-50');
        navItem('testimonials.php', 'Testimonials', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />', 'text-cyan-600', 'bg-cyan-50');
        navItem('subscribers.php', 'Subscribers', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />', 'text-yellow-600', 'bg-yellow-50');

        echo '<div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-2 mt-6">System</div>';

        navItem('settings.php', 'Site Settings', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />', 'text-gray-600', 'bg-gray-100');

        ?>
        <button onclick="openIndexingModal()"
            class="w-full text-left group flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 text-gray-500 hover:bg-gray-50 hover:text-green-600 cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform group-hover:scale-110"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            <span>Instant Indexing</span>
            <span
                class="ml-auto bg-green-100 text-green-700 text-[10px] font-bold px-1.5 py-0.5 rounded border border-green-200">API</span>
        </button>
    </nav>

    <div class="p-4 border-t border-gray-100">
        <a href="change_password.php"
            class="flex items-center space-x-3 px-4 py-2.5 text-gray-500 hover:bg-emerald-50 rounded-xl hover:text-emerald-600 transition-all duration-200 group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform group-hover:scale-110"
                fill="none" viewBox='0 0 24 24' stroke='currentColor'>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            <span>Change Password</span>
        </a>
        <a href="logout.php"
            class="flex items-center space-x-3 px-4 py-2.5 mt-1 text-red-500 hover:bg-red-50 rounded-xl hover:text-red-700 transition-all duration-200 group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform group-hover:scale-110"
                fill="none" viewBox='0 0 24 24' stroke='currentColor'>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span>Logout</span>
        </a>
    </div>
</aside>

<!-- Indexing Modal -->
<div id="indexingModal"
    class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 transform scale-95 transition-transform duration-300"
        id="indexingModalContent">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <span class="bg-green-100 text-green-600 p-2 rounded-lg"><i class="fas fa-bolt"></i></span>
                Instant Indexing
            </h3>
            <button onclick="closeIndexingModal()" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <p class="text-sm text-gray-500 mb-6 leading-relaxed">
            Directly notify Google to crawl your site. This uses the <strong>Indexing API</strong> for immediate
            results.
            <br><span class="text-xs text-orange-500 mt-2 block"><i class="fas fa-exclamation-triangle mr-1"></i>
                Requires Service Account JSON</span>
        </p>

        <div class="space-y-3">
            <button onclick="triggerIndexing('index_home', this)"
                class="w-full flex items-center justify-between p-4 bg-gray-50 border border-gray-200 rounded-xl hover:border-blue-500 hover:bg-blue-50 transition group">
                <div class="flex items-center gap-3">
                    <div class="bg-white p-2 rounded-lg shadow-sm text-blue-500"><i class="fas fa-home"></i></div>
                    <div class="text-left">
                        <p class="font-bold text-gray-800 text-sm">Index Homepage</p>
                        <p class="text-xs text-gray-400">Update the main landing page</p>
                    </div>
                </div>
                <i class="fas fa-arrow-right text-gray-300 group-hover:text-blue-500 transition"></i>
            </button>

            <button onclick="triggerIndexing('index_all', this)"
                class="w-full flex items-center justify-between p-4 bg-gray-50 border border-gray-200 rounded-xl hover:border-purple-500 hover:bg-purple-50 transition group">
                <div class="flex items-center gap-3">
                    <div class="bg-white p-2 rounded-lg shadow-sm text-purple-500"><i class="fas fa-layer-group"></i>
                    </div>
                    <div class="text-left">
                        <p class="font-bold text-gray-800 text-sm">Index Main Pages</p>
                        <p class="text-xs text-gray-400">Home, Destinations, Packages, Blog, Contact</p>
                    </div>
                </div>
                <i class="fas fa-arrow-right text-gray-300 group-hover:text-purple-500 transition"></i>
            </button>
        </div>

        <div id="indexResult" class="mt-6 hidden"></div>
    </div>
</div>

<script>
    function openIndexingModal() {
        const modal = document.getElementById('indexingModal');
        const content = document.getElementById('indexingModalContent');
        modal.classList.remove('hidden');
        // Small timeout for transition
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }, 10);
    }

    function closeIndexingModal() {
        const modal = document.getElementById('indexingModal');
        const content = document.getElementById('indexingModalContent');
        modal.classList.add('opacity-0');
        content.classList.remove('scale-100');
        content.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            document.getElementById('indexResult').classList.add('hidden');
        }, 300);
    }

    function triggerIndexing(action, btn) {
        const originalContent = btn.innerHTML;
        const resultDiv = document.getElementById('indexResult');

        // Loading State
        btn.disabled = true;
        btn.innerHTML = `<div class="flex items-center justify-center gap-2"><i class="fas fa-circle-notch fa-spin"></i> Processing...</div>`;
        resultDiv.classList.add('hidden');

        const formData = new FormData();
        formData.append('action', action);

        fetch('indexing_action.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                resultDiv.classList.remove('hidden');
                if (data.status === 'success') {
                    let successCount = 0;
                    let failCount = 0;
                    let detailsHtml = '';

                    data.data.forEach(item => {
                        const isSuccess = item.result.status === 'success';
                        if (isSuccess) successCount++; else failCount++;

                        // Truncate URL for better display
                        const shortUrl = item.url.replace(/^https?:\/\/[^\/]+/, '');

                        detailsHtml += `
                            <li class="flex items-start justify-between gap-2 p-2 rounded hover:bg-white transition">
                                <span class="text-xs font-mono text-gray-600 truncate" title="${item.url}">${shortUrl || '/'}</span>
                                ${isSuccess
                                ? '<span class="shrink-0 text-[10px] font-bold bg-green-100 text-green-700 px-1.5 py-0.5 rounded">OK</span>'
                                : '<span class="shrink-0 text-[10px] font-bold bg-red-100 text-red-700 px-1.5 py-0.5 rounded" title="' + (item.result.message || 'Error') + '">FAIL</span>'}
                            </li>`;
                    });

                    let html = `
                        <div class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden">
                            <!-- Summary Header -->
                            <div class="flex items-center justify-between px-4 py-3 bg-white border-b border-gray-100">
                                <div class="flex items-center gap-4">
                                    <div class="flex flex-col">
                                        <span class="text-xs text-gray-400 uppercase font-bold tracking-wider">Total</span>
                                        <span class="text-lg font-bold text-gray-800">${data.data.length}</span>
                                    </div>
                                    <div class="w-px h-8 bg-gray-100"></div>
                                    <div class="flex flex-col">
                                        <span class="text-xs text-green-500 uppercase font-bold tracking-wider">Success</span>
                                        <span class="text-lg font-bold text-green-600">${successCount}</span>
                                    </div>
                                    <div class="w-px h-8 bg-gray-100"></div>
                                    <div class="flex flex-col">
                                        <span class="text-xs text-red-500 uppercase font-bold tracking-wider">Failed</span>
                                        <span class="text-lg font-bold text-red-600">${failCount}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Scrollable Details -->
                            <div class="max-h-60 overflow-y-auto custom-scrollbar p-2 space-y-0.5 bg-gray-50">
                                <ul class="space-y-1">
                                    ${detailsHtml}
                                </ul>
                            </div>
                        </div>`;

                    resultDiv.innerHTML = html;
                } else {
                    resultDiv.innerHTML = `<div class="bg-red-50 text-red-700 p-3 rounded-lg text-sm border border-red-200"><i class="fas fa-times-circle mr-2"></i>${data.message}</div>`;
                }
            })
            .catch(error => {
                resultDiv.classList.remove('hidden');
                resultDiv.innerHTML = `<div class="bg-red-50 text-red-700 p-3 rounded-lg text-sm border border-red-200"><i class="fas fa-exclamation-triangle mr-2"></i>Network Error</div>`;
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalContent;
            });
    }
</script>