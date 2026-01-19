<?php
http_response_code(503);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Unavailable - ifyTravels</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 h-screen flex items-center justify-center p-6 text-center">
    <div class="max-w-lg w-full">
        <div class="w-20 h-20 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-4">System Maintenance</h1>
        <p class="text-gray-600 mb-8 leading-relaxed">
            We are currently performing essential system updates. <br>
            Please check back in a few minutes.
        </p>

        <button onclick="window.location.reload()"
            class="bg-gray-900 text-white px-8 py-3 rounded-lg font-bold hover:bg-black transition">
            Retry Connection
        </button>

        <p class="text-xs text-gray-400 mt-12">
            Error Code: 503 SERVICE_UNAVAILABLE
        </p>
    </div>
</body>

</html>