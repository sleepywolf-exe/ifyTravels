<?php
// pages/error.php (Generic Error Template)
// Can be included by 404.php, 503.php, etc.

// Auto-detect error code from server redirect if not set
if (!isset($errorCode)) {
    $errorCode = http_response_code();
    if ($errorCode === 200 && isset($_SERVER['REDIRECT_STATUS'])) {
        $errorCode = $_SERVER['REDIRECT_STATUS'];
        http_response_code($errorCode);
    }
    // Fallback if still 200 or not set
    if ($errorCode === 200 || !$errorCode) {
        $errorCode = 404;
        http_response_code(404);
    }
}

// Map codes to messages
$titles = [
    403 => "Access Denied",
    404 => "Look like you're lost",
    500 => "Internal Server Error",
    503 => "Service Unavailable"
];

$messages = [
    403 => "You don't have permission to access this area.",
    404 => "The page you are looking for is not available!",
    500 => "Something went wrong on our end. Please try again later.",
    503 => "We are currently experiencing technical issues. Please try again later."
];

$errorTitle = $errorTitle ?? ($titles[$errorCode] ?? "Error $errorCode");
$errorMessage = $errorMessage ?? ($messages[$errorCode] ?? "An unexpected error occurred.");
$pageTitle = "Error $errorCode";

require_once __DIR__ . '/../includes/header.php';
?>

<style>
    /*======================
        404 page
    =======================*/
    .page_404 {
        padding: 80px 0;
        background: #fff;
        font-family: 'Plus Jakarta Sans', serif;
    }

    .four_zero_four_bg {
        background-image: url('<?php echo base_url("assets/images/404.gif"); ?>');
        height: 600px;
        /* Increased height */
        width: 100%;
        background-position: center;
        background-repeat: no-repeat;
        background-size: contain;
        /* Make sure it scales */
        position: relative;
        z-index: 10;
        display: flex;
        align-items: flex-start;
        /* Align text to top */
        justify-content: center;
    }

    .four_zero_four_bg h1 {
        font-size: 180px;
        /* Massive text */
        margin-bottom: 0;
        margin-top: -50px;
        /* overlap slightly */
        line-height: 1;
    }

    .link_404 {
        color: #fff !important;
        padding: 16px 40px;
        background: #0F766E;
        margin: 40px 0 20px;
        display: inline-block;
        border-radius: 99px;
        text-transform: uppercase;
        font-weight: 800;
        font-size: 16px;
        letter-spacing: 2px;
        transition: all 0.3s ease;
        box-shadow: 0 10px 30px rgba(15, 118, 110, 0.3);
    }

    .link_404:hover {
        background: #0d6962;
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(15, 118, 110, 0.4);
    }

    .contant_box_404 {
        margin-top: -80px;
        /* Pull content up over the bottom of the GIF */
        position: relative;
        z-index: 20;
    }
</style>

<section class="page_404 min-h-screen flex items-center justify-center bg-white">
    <div class="container mx-auto px-4">
        <div class="flex flex-col items-center justify-center text-center">

            <div class="w-full max-w-5xl">
                <div class="four_zero_four_bg w-full">
                    <h1
                        class="text-center font-heading font-black text-slate-900 drop-shadow-md tracking-tighter mix-blend-multiply opacity-90">
                        <?php echo $errorCode; ?>
                    </h1>
                </div>

                <div class="contant_box_404">
                    <h3 class="font-heading text-5xl md:text-6xl font-black mb-6 text-slate-900 tracking-tight">
                        <?php echo $errorTitle; ?>
                    </h3>

                    <p class="text-slate-500 mb-10 text-xl md:text-2xl max-w-2xl mx-auto leading-relaxed font-light">
                        <?php echo $errorMessage; ?>
                    </p>

                    <a href="<?php echo base_url(); ?>" class="link_404 group">
                        Go to Home
                        <i
                            class="fa-solid fa-arrow-right ml-3 text-lg group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>