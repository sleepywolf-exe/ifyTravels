<?php
http_response_code(404);
$pageTitle = "Page Not Found";
require_once __DIR__ . '/../includes/header.php';
?>

<style>
    .page_404 {
        padding: 40px 0;
        background: #fff;
        font-family: 'Arvo', serif;
    }

    .page_404 img {
        width: 100%;
    }

    .four_zero_four_bg {
        background-image: url(https://cdn.dribbble.com/users/285475/screenshots/2083086/dribbble_1.gif);
        height: 400px;
        background-position: center;
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
        margin: 20px 0;
        display: inline-block;
    }

    .contant_box_404 {
        margin-top: -50px;
    }
</style>

<section class="page_404 min-h-screen flex items-center justify-center bg-white text-center">
    <div class="container mx-auto">
        <div class="row">
            <div class="col-sm-12 ">
                <div class="col-sm-10 col-sm-offset-1  text-center">
                    <div class="four_zero_four_bg bg-no-repeat bg-center h-[400px]"
                        style="background-image: url('https://cdn.dribbble.com/users/285475/screenshots/2083086/dribbble_1.gif');">
                        <h1 class="text-center text-9xl font-bold font-heading text-slate-800">404</h1>
                    </div>

                    <div class="contant_box_404 mt-[-50px]">
                        <h3 class="text-4xl font-heading font-bold mb-4">
                            Look like you're lost
                        </h3>

                        <p class="text-slate-600 mb-8 text-lg">the page you are looking for not avaible!</p>

                        <a href="<?php echo base_url(); ?>"
                            class="link_404 bg-primary text-white py-3 px-8 rounded-full shadow-lg hover:bg-teal-700 transition font-bold">Go
                            to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>