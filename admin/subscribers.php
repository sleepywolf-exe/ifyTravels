<?php
$pageTitle = "Newsletter Subscribers";
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Auth Check
require_once __DIR__ . '/auth_check.php';

$db = Database::getInstance();

// Handle Delete
if (isset($_POST['delete_id'])) {
    $deleteId = (int) $_POST['delete_id'];
    $db->execute("DELETE FROM newsletter_subscribers WHERE id = ?", [$deleteId]);
    header("Location: subscribers.php?msg=deleted");
    exit;
}

// Fetch Subscribers
$subscribers = $db->fetchAll("SELECT * FROM newsletter_subscribers ORDER BY created_at DESC");

// Include Header/Sidebar
// Note: Adapting to admin layout structure
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $pageTitle; ?> - Admin
    </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900">

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include __DIR__ . '/sidebar_inc.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 ml-64 p-8 transition-all duration-300">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-slate-900">Newsletter Subscribers</h1>
                        <p class="text-slate-500 mt-1">Manage your email list (
                            <?php echo count($subscribers); ?> subscribers)
                        </p>
                    </div>
                    <button onclick="window.print()"
                        class="bg-white border border-slate-200 text-slate-600 px-4 py-2 rounded-lg hover:bg-slate-50 transition-colors">
                        <i class="fas fa-print mr-2"></i> Print List
                    </button>
                </div>

                <!-- List -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200">
                                    <th class="p-5 font-bold text-slate-700 text-sm uppercase tracking-wider">ID</th>
                                    <th class="p-5 font-bold text-slate-700 text-sm uppercase tracking-wider">Email</th>
                                    <th class="p-5 font-bold text-slate-700 text-sm uppercase tracking-wider">Joined
                                        Date</th>
                                    <th
                                        class="p-5 font-bold text-slate-700 text-sm uppercase tracking-wider text-right">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php if (empty($subscribers)): ?>
                                    <tr>
                                        <td colspan="4" class="p-10 text-center text-slate-400">
                                            No subscribers yet.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($subscribers as $sub): ?>
                                        <tr class="hover:bg-slate-50/50 transition-colors group">
                                            <td class="p-5 text-slate-500 font-mono text-sm leading-none">
                                                #
                                                <?php echo $sub['id']; ?>
                                            </td>
                                            <td class="p-5 font-bold text-slate-900">
                                                <a href="mailto:<?php echo htmlspecialchars($sub['email']); ?>"
                                                    class="hover:text-teal-600 transition-colors">
                                                    <?php echo htmlspecialchars($sub['email']); ?>
                                                </a>
                                            </td>
                                            <td class="p-5 text-slate-500 text-sm">
                                                <?php echo date('M j, Y h:i A', strtotime($sub['created_at'])); ?>
                                            </td>
                                            <td class="p-5 text-right">
                                                <form method="POST"
                                                    onsubmit="return confirm('Are you sure you want to remove this subscriber?');"
                                                    class="inline-block">
                                                    <input type="hidden" name="delete_id" value="<?php echo $sub['id']; ?>">
                                                    <button type="submit"
                                                        class="text-slate-400 hover:text-red-600 transition-colors p-2 rounded-full hover:bg-red-50">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

</body>

</html>