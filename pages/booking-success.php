<?php
// Simple Success Page
// No header/footer needed for this simple splash usually, or we can add it. 
// Original didn't have full header in file but was standalone properly.
// I'll keep it simple but change links to .php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed!</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 flex items-center justify-center h-screen text-center px-4">
    <div class="bg-white p-8 rounded-xl shadow-lg max-w-md">
        <div
            class="w-20 h-20 bg-green-100 text-green-500 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl">
            ✓
        </div>
        <h1 class="text-3xl font-bold mb-2">Booking Confirmed!</h1>
        <p class="text-gray-600 mb-6">Thank you for your booking. A confirmation email has been sent to you.</p>
        <a href="../index.php"
            class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition">Return
            to Home</a>
    </div>
</body>

</html>