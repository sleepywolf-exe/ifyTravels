<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Simple auth check
// session_start();
// if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
//     die("Access Denied. Admins only.");
// }

$pageTitle = "Setup Blog Database";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Blog DB</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-10 rounded-xl shadow-xl max-w-lg w-full text-center">
        <h1 class="text-2xl font-bold mb-4 text-slate-800">Initialize Blog Database</h1>

        <?php
        try {
            $db = Database::getInstance();
            $pdo = $db->getConnection();

            $sql = "CREATE TABLE IF NOT EXISTS posts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL UNIQUE,
                excerpt TEXT,
                content LONGTEXT,
                image_url VARCHAR(255),
                author VARCHAR(100) DEFAULT 'Admin',
                views INT DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

            $pdo->exec($sql);

            echo '<div class="p-4 bg-green-100 text-green-700 rounded-lg mb-4">
                    <p class="font-bold">✅ Success!</p>
                    <p>Table `posts` created successfully.</p>
                  </div>';

            echo '<a href="dashboard.php" class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 transition">Go to Dashboard</a>';

        } catch (Exception $e) {
            echo '<div class="p-4 bg-red-100 text-red-700 rounded-lg mb-4">
                    <p class="font-bold">❌ Error</p>
                    <p>' . htmlspecialchars($e->getMessage()) . '</p>
                  </div>';
        }
        ?>
    </div>
</body>

</html>