<?php
http_response_code(404);
$pageTitle = "Page Not Found - ifyTravels";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="pt-32 pb-20 container mx-auto px-6 text-center">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-9xl font-bold text-gray-200 font-heading">404</h1>
        <h2 class="text-4xl font-bold text-charcoal mb-6 mt-[-40px] relative z-10">Page Not Found</h2>
        <p class="text-gray-600 text-lg mb-8">
            Oops! The page you are looking for might have been removed or is temporarily unavailable.
        </p>

        <a href="<?php echo base_url(); ?>"
            class="inline-block bg-primary text-white font-bold py-4 px-10 rounded-full hover:bg-teal-700 transition shadow-lg transform hover:-translate-y-1">
            Back to Home
        </a>

        <div class="mt-12 p-8 bg-gray-50 rounded-2xl border border-gray-100">
            <h3 class="font-bold text-charcoal mb-4">Looking for a vacation?</h3>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="<?php echo base_url('destinations/kashmir'); ?>" class="text-primary hover:underline">Kashmir
                    Packages</a>
                <span class="text-gray-300">|</span>
                <a href="<?php echo base_url('destinations/maldives'); ?>" class="text-primary hover:underline">Maldives
                    Trips</a>
                <span class="text-gray-300">|</span>
                <a href="<?php echo base_url('packages'); ?>" class="text-primary hover:underline">All Packages</a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>