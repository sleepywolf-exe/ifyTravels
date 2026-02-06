<?php
// pages/error.php - Standalone Error Page
require_once __DIR__ . '/../includes/functions.php';

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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Error'; ?> - ifyTravels</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Great+Vibes&display=swap"
        rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0F766E',
                        secondary: '#0D9488',
                        dark: '#0f172a',
                    },
                    fontFamily: {
                        heading: ['"Plus Jakarta Sans"', 'sans-serif'],
                        body: ['"Plus Jakarta Sans"', 'sans-serif'],
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        signature: ['"Great Vibes"', 'cursive'],
                    }
                }
            }
        }
    </script>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Only keeping essential custom styles not easily doable with Tailwind */
        body {
            overflow-x: hidden;
        }

        .link_404 {
            color: #fff !important;
            padding: 15px 40px;
            background: #0F766E;
            display: inline-flex;
            align-items: center;
            border-radius: 99px;
            text-transform: uppercase;
            font-weight: 700;
            font-size: 14px;
            letter-spacing: 2px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(15, 118, 110, 0.3);
            text-decoration: none;
        }

        .link_404:hover {
            background: #0d6962;
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(15, 118, 110, 0.4);
        }
    </style>
</head>

<body class="bg-white text-slate-900">

    <section class="min-h-screen w-full flex items-center justify-center p-4 bg-slate-50">

        <!-- Main Card -->
        <div
            class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-3xl p-8 md:p-12 text-center relative overflow-hidden border border-slate-100">

            <div class="flex flex-col items-center justify-center">

                <!-- 1. Error Code (Inside Card, Big & Clear) -->
                <h1
                    class="font-heading font-black text-slate-900 leading-none text-[100px] md:text-[180px] mb-4 tracking-tighter">
                    <?php echo $errorCode; ?>
                </h1>

                <!-- 2. GIF Image (Inside Card, Large) -->
                <div class="w-full max-w-[280px] md:max-w-md mb-8">
                    <img src="<?php echo base_url('assets/images/404.gif'); ?>" alt="404 Animation"
                        class="w-full h-auto object-contain mx-auto">
                </div>

                <!-- 3. Content -->
                <div class="space-y-4 max-w-xl mx-auto">
                    <h3
                        class="font-heading font-black text-slate-800 tracking-tight leading-tight text-3xl md:text-5xl">
                        <?php echo $errorTitle; ?>
                    </h3>

                    <p class="text-slate-500 text-base md:text-lg leading-relaxed font-medium mb-8">
                        <?php echo $errorMessage; ?>
                    </p>

                    <div class="pt-2">
                        <a href="<?php echo base_url(); ?>"
                            class="link_404 group shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
                            Go to Home
                            <i
                                class="fa-solid fa-arrow-right ml-3 text-sm group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </section>

</body>

</html>