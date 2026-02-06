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
        padding: 40px 0;
        background: #fff;
        font-family: 'Plus Jakarta Sans', serif;
    }

    .four_zero_four_bg {
        background-image: url('<?php echo base_url("assets/images/404.gif"); ?>');
        height: 400px;
        background-position: center;
        background-repeat: no-repeat;
        /* Ensure image doesn't look cut off */
        background-size: contain; 
        position: relative;
        z-index: 10;
    }

    .four_zero_four_bg h1 {
        font-size: 80px;
        margin-bottom: 20px;
    }

    .link_404 {
        color: #fff !important;
        padding: 12px 30px;
        background: #0F766E;
        margin: 20px 0;
        display: inline-block;
        border-radius: 99px;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 1px;
        transition: all 0.3s ease;
    }
    
    .link_404:hover {
        background: #0d6962;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(15, 118, 110, 0.2);
    }

    .contant_box_404 {
        margin-top: -30px;
        position: relative;
        z-index: 20;
    }
</style>

<section class="page_404 min-h-[85vh] flex items-center justify-center bg-slate-50/50">
    <div class="container mx-auto px-4">
        <div class="flex flex-col items-center justify-center text-center">
            
            <div class="w-full max-w-3xl">
                <div class="four_zero_four_bg w-full flex items-start justify-center pt-8">
                    <h1 class="text-center font-heading font-bold text-slate-900 text-6xl md:text-9xl drop-shadow-sm">
                        <?php echo $errorCode; ?>
                    </h1>
                </div>

                <div class="contant_box_404">
                    <h3 class="font-heading text-3xl md:text-4xl font-bold mb-4 text-slate-800">
                        <?php echo $errorTitle; ?>
                    </h3>

                    <p class="text-slate-500 mb-8 text-lg max-w-md mx-auto leading-relaxed">
                        <?php echo $errorMessage; ?>
                    </p>

                    <a href="<?php echo base_url(); ?>" class="link_404">
                        Go to Home
                        <i class="fa-solid fa-arrow-right ml-2 text-sm"></i>
                    </a>
                </div>
            </div>
            
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>