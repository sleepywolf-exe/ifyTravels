<?php
// pages/error.php (Generic Error Template)
// Can be included by 404.php, 503.php, etc.
$errorCode = $errorCode ?? 404;
$errorTitle = $errorTitle ?? "Look like you're lost";
$errorMessage = $errorMessage ?? "the page you are looking for not avaible!";
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