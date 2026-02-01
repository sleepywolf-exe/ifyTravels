<?php
http_response_code(503);
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Unavailable - ifyTravels</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind = {
            config: {
                theme: {
                    extend: {
                        colors: {
                            primary: '#0F766E',
                            secondary: '#D97706',
                            charcoal: '#111827',
                        },
                        fontFamily: {
                            heading: ['Playfair Display', 'serif'],
                            body: ['Outfit', 'sans-serif'],
                        }
                    }
                }
            }
        };
    </script>
</head>

<body class="bg-charcoal text-white h-screen flex items-center justify-center p-6 relative overflow-hidden font-body">

    <!-- Background Accents -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-secondary/10 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-primary/10 rounded-full blur-[100px]"></div>
    </div>

    <div class="max-w-xl w-full text-center relative z-10">
        <div
            class="w-24 h-24 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-8 border border-white/10 animate-pulse">
            <svg class="w-10 h-10 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                </path>
            </svg>
        </div>

        <h1 class="text-3xl md:text-5xl font-heading font-bold text-white mb-4">Under Maintenance</h1>
        <p class="text-gray-400 text-lg mb-8 leading-relaxed font-light">
            We are currently enhancing your experience. We will be back shortly with new journeys for you to explore.
        </p>

        <button onclick="window.location.reload()"
            class="px-8 py-4 bg-gradient-to-r from-secondary to-yellow-600 text-white font-bold rounded-xl hover:shadow-lg hover:shadow-orange-900/20 transition transform hover:-translate-y-1">
            Check Again
        </button>

        <p class="text-xs text-gray-500 mt-12 font-mono">
            STATUS: 503 SERVICE_UNAVAILABLE
        </p>
    </div>
</body>

</html>