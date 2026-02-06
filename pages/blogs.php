<?php
$pageTitle = "Travel Blogs & Stories";
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';

// Fetch Blogs
try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    // Pagination
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $limit = 9;
    $offset = ($page - 1) * $limit;

    // Count total posts
    $countStmt = $pdo->query("SELECT COUNT(*) FROM posts");
    $totalPosts = $countStmt->fetchColumn();
    $totalPages = ceil($totalPosts / $limit);

    // Fetch posts
    $stmt = $pdo->prepare("SELECT * FROM posts ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $posts = $stmt->fetchAll();

    $posts = $stmt->fetchAll();

} catch (Exception $e) {
    $posts = [];
    // DEBUG: Show error
    echo "<div class='bg-red-500 text-white p-4 absolute top-0 left-0 w-full z-50'>";
    echo "<b>DEBUG ERROR:</b> " . $e->getMessage();
    echo "</div>";
    error_log($e->getMessage());
}
?>

<!-- Hero Section -->
<section class="relative min-h-[50vh] flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1499591934245-40b55745b905?auto=format&fit=crop&q=80&w=2000"
            class="w-full h-full object-cover object-center brightness-[0.40]" alt="Travel Blog">
        <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/30 to-black/70"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10 text-center pt-24">
        <span
            class="inline-block px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 text-white font-bold tracking-widest uppercase text-sm rounded-full mb-6">
            Travel Journal
        </span>
        <h1 class="text-4xl md:text-5xl lg:text-7xl font-heading font-bold text-white mb-6 drop-shadow-2xl">
            Latest <span class="text-primary">Stories</span>
        </h1>
        <p class="text-white/90 text-lg md:text-xl font-light leading-relaxed max-w-2xl mx-auto drop-shadow-lg">
            Tips, guides, and inspiration from our travel experts.
        </p>
    </div>
</section>

<!-- Blog Grid -->
<div class="bg-gray-50 min-h-screen py-20">
    <div class="container mx-auto px-6">

        <?php if (empty($posts)): ?>
            <div class="text-center py-20">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-200 rounded-full mb-6">
                    <i class="fas fa-feather-alt text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-2xl font-bold text-slate-700 mb-2">No stories yet</h3>
                <p class="text-slate-500">Check back soon for new travel updates!</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                <?php foreach ($posts as $index => $post): ?>
                    <article
                        class="group bg-white rounded-[2rem] shadow-sm hover:shadow-xl transition-all duration-500 overflow-hidden border border-slate-100 flex flex-col h-full hover:-translate-y-2">
                        <!-- Image -->
                        <a href="<?php echo base_url('blogs/' . $post['slug']); ?>" class="block relative h-64 overflow-hidden">
                            <?php if ($post['image_url']): ?>
                                <img src="<?php echo base_url($post['image_url']); ?>"
                                    alt="<?php echo htmlspecialchars($post['title']); ?>"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <?php else: ?>
                                <div class="w-full h-full bg-slate-200 flex items-center justify-center text-slate-400">
                                    <i class="fas fa-image text-4xl"></i>
                                </div>
                            <?php endif; ?>
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>
                        </a>

                        <!-- Content -->
                        <div class="p-8 flex-1 flex flex-col">
                            <div
                                class="flex items-center gap-4 text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">
                                <span>
                                    <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
                                </span>
                                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                <span>
                                    <?php echo htmlspecialchars($post['author'] ?? 'Admin'); ?>
                                </span>
                            </div>

                            <h2
                                class="text-2xl font-heading font-bold text-slate-900 mb-3 leading-tight group-hover:text-primary transition-colors">
                                <a href="<?php echo base_url('blogs/' . $post['slug']); ?>">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </a>
                            </h2>

                            <p class="text-slate-500 line-clamp-3 mb-6 flex-1 leading-relaxed">
                                <?php echo htmlspecialchars($post['excerpt'] ?? substr(strip_tags($post['content']), 0, 120)); ?>...
                            </p>

                            <a href="<?php echo base_url('blogs/' . $post['slug']); ?>"
                                class="inline-flex items-center font-bold text-sm text-primary hover:text-secondary transition-colors uppercase tracking-wider">
                                Read Article <i
                                    class="fas fa-arrow-right ml-2 text-xs transition-transform group-hover:translate-x-1"></i>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="flex justify-center mt-20 gap-2">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>"
                            class="w-12 h-12 flex items-center justify-center rounded-full font-bold transition-all <?php echo ($i === $page) ? 'bg-primary text-white shadow-lg shadow-primary/30 scale-110' : 'bg-white text-slate-600 hover:bg-slate-100'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>