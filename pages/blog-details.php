<?php
$pageTitle = "Blog Details";
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Fetch Post by Slug
$slug = $_GET['slug'] ?? '';
$post = null;

if ($slug) {
    try {
        $db = Database::getInstance();
        $pdo = $db->getConnection();

        // Update views
        $viewStmt = $pdo->prepare("UPDATE posts SET views = views + 1 WHERE slug = ?");
        $viewStmt->execute([$slug]);

        // Get Post
        $stmt = $pdo->prepare("SELECT * FROM posts WHERE slug = ?");
        $stmt->execute([$slug]);
        $post = $stmt->fetch();

        if ($post) {
            $pageTitle = htmlspecialchars($post['title']);
        }
    } catch (Exception $e) {
        $post = null;
    }
}

include __DIR__ . '/../includes/header.php';

if (!$post):
    ?>
    <div class="min-h-[60vh] flex flex-col items-center justify-center text-center px-6">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6 text-gray-400">
            <i class="fas fa-search text-4xl"></i>
        </div>
        <h1 class="text-3xl font-bold text-gray-800 mb-4">Story Not Found</h1>
        <p class="text-gray-500 mb-8 max-w-md">The travel story you are looking for might have been moved or deleted.</p>
        <a href="<?php echo base_url('blogs'); ?>"
            class="px-8 py-3 bg-primary text-white rounded-xl font-bold hover:bg-secondary transition">
            Back to Blogs
        </a>
    </div>
<?php else: ?>

    <!-- Hero Section -->
    <div class="relative min-h-[50vh] md:h-[60vh] min-h-[400px]">
        <?php if ($post['image_url']): ?>
            <img src="<?php echo base_url($post['image_url']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>"
                class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-black/30"></div>
        <?php else: ?>
            <div class="absolute inset-0 bg-slate-900"></div>
        <?php endif; ?>

        <div class="absolute bottom-0 left-0 w-full p-6 md:p-12 lg:p-20 pt-24">
            <div class="container mx-auto">
                <a href="<?php echo base_url('blogs'); ?>"
                    class="inline-flex items-center text-white/80 hover:text-white mb-6 transition text-sm font-bold uppercase tracking-wider backdrop-blur-md bg-white/10 px-4 py-2 rounded-full border border-white/20">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Journal
                </a>

                <h1
                    class="text-3xl md:text-5xl lg:text-7xl font-heading font-bold text-white mb-6 leading-tight drop-shadow-lg max-w-5xl">
                    <?php echo htmlspecialchars($post['title']); ?>
                </h1>

                <div class="flex flex-wrap items-center gap-6 text-white/90 font-medium">
                    <div class="flex items-center gap-2">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($post['author'] ?? 'Admin'); ?>&background=random"
                            class="w-10 h-10 rounded-full border-2 border-white shadow-sm" alt="Author">
                        <span>
                            <?php echo htmlspecialchars($post['author'] ?? 'Admin'); ?>
                        </span>
                    </div>
                    <span class="w-1.5 h-1.5 bg-white/50 rounded-full"></span>
                    <div class="flex items-center gap-2">
                        <i class="far fa-calendar-alt opacity-70"></i>
                        <span>
                            <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="bg-white min-h-screen py-16 md:py-24">
        <div class="container mx-auto px-6">
            <div class="max-w-3xl mx-auto">
                <div
                    class="prose prose-lg md:prose-xl prose-slate hover:prose-a:text-primary transition-colors prose-img:rounded-3xl prose-img:shadow-lg prose-headings:font-heading prose-headings:font-bold first-letter:text-5xl first-letter:font-heading first-letter:font-bold first-letter:float-left first-letter:mr-3 first-letter:mt-[-10px] first-letter:text-primary">
                    <?php echo nl2br($post['content']); // Using nl2br for basic text formatting if raw text is saved ?>
                </div>

                <!-- Share / Tags Section could go here -->
                <div
                    class="mt-16 pt-10 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="text-slate-500 font-medium italic">
                        Share this story with your travel buddies!
                    </div>
                    <div class="flex gap-4">
                        <button
                            class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition"><i
                                class="fab fa-facebook-f"></i></button>
                        <button
                            class="w-12 h-12 rounded-full bg-sky-50 text-sky-500 flex items-center justify-center hover:bg-sky-500 hover:text-white transition"><i
                                class="fab fa-twitter"></i></button>
                        <button
                            class="w-12 h-12 rounded-full bg-green-50 text-green-600 flex items-center justify-center hover:bg-green-600 hover:text-white transition"><i
                                class="fab fa-whatsapp"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>