<?php require 'auth_check.php';
$inquiries = Database::getInstance()->fetchAll("SELECT * FROM inquiries ORDER BY created_at DESC"); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Inquiries - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 flex h-screen"><?php include 'sidebar_inc.php'; ?>
    <main class="flex-1 overflow-y-auto p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Inquiries</h1>
        <div class="space-y-4"><?php foreach ($inquiries as $i): ?>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h4 class="font-bold text-lg text-gray-800"><?php echo e($i['subject']); ?></h4>
                            <p class="text-sm text-gray-500">From: <span
                                    class="text-charcoal font-medium"><?php echo e($i['name']); ?></span>
                                (<?php echo e($i['email']); ?>)</p>
                        </div><span
                            class="text-xs text-gray-400"><?php echo date('M d, H:i', strtotime($i['created_at'])); ?></span>
                    </div>
                    <p class="text-gray-600 bg-gray-50 p-4 rounded-lg"><?php echo nl2br(e($i['message'])); ?></p>
                </div><?php endforeach; ?>
        </div>
    </main>
</body>

</html>