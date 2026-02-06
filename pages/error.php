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
        /* Adapted to site font */
    }

    .page_404 img {
        width: 100%;
    }

    .four_zero_four_bg {
        background-image: url('<?php echo base_url("assets/images/404.gif"); ?>');
        height: 400px;
        background-position: center;
        background-repeat: no-repeat;
    }

    .four_zero_four_bg h1 {
        font-size: 80px;
    }

    .four_zero_four_bg h3 {
        font-size: 80px;
    }

    .link_404 {
        color: #fff !important;
        padding: 10px 20px;
        background: #39ac31;
        /* Keeping original green or use site primary? User said 'use this' so keeping closest, maybe adapting to variable */
        background: #0F766E;
        /* Adapting to site Primary for consistency */
        margin: 20px 0;
        display: inline-block;
        border-radius: 99px;
        /* Modern touch */
    }

    .contant_box_404 {
        margin-top: -50px;
    }
</style>

<section class="page_404 min-h-[80vh] flex items-center justify-center">
    <div class="container mx-auto">
        <div class="row">
            <div class="col-sm-12 ">
                <div class="col-sm-10 col-sm-offset-1 text-center mx-auto">
                    <div class="four_zero_four_bg">
                        <h1 class="text-center font-heading font-bold text-slate-800">
                            <?php echo $errorCode; ?>
                        </h1>
                    </div>

                    <div class="contant_box_404">
                        <h3 class="h2 text-3xl font-bold mb-2">
                            <?php echo $errorTitle; ?>
                        </h3>

                        <p class="text-slate-600 mb-6">
                            <?php echo $errorMessage; ?>
                        </p>

                        <a href="<?php echo base_url(); ?>"
                            class="link_404 shadow-lg hover:shadow-xl transition-all hover:scale-105">Go to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>