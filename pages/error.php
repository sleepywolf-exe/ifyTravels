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

    <section class="min-h-screen flex items-center justify-center py-20 overflow-hidden relative">
        <div class="container mx-auto px-4 relative">

            <!-- 1. Background Watermark (Massive 404) -->
            <div
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full text-center z-0 pointer-events-none select-none">
                <h1
                    class="font-heading font-black text-slate-900 leading-none opacity-[0.03] text-[200px] md:text-[400px]">
                    <?php echo $errorCode; ?>
                </h1>
            </div>

            <!-- 2. Foreground Content (Centered) -->
            <div class="flex flex-col items-center justify-center text-center max-w-5xl mx-auto relative z-10">

                <!-- GIF Image -->
                <div class="w-full max-w-sm md:max-w-md mb-6">
                    <img src="<?php echo base_url('assets/images/404.gif'); ?>" alt="404 Animation"
                        class="w-full h-auto object-contain mx-auto mix-blend-multiply">
                </div>

                <!-- Text Content -->
                <div>
                    <h3
                        class="font-heading font-black text-slate-800 tracking-tight leading-tight mb-3 text-3xl md:text-5xl">
                        <?php echo $errorTitle; ?>
                    </h3>

                    <p class="text-slate-500 text-base md:text-lg max-w-xl mx-auto leading-relaxed font-medium mb-8">
                        <?php echo $errorMessage; ?>
                    </p>

                    <a href="<?php echo base_url(); ?>"
                        class="link_404 group shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300">
                        Go to Home
                        <i
                            class="fa-solid fa-arrow-right ml-3 text-sm group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>

            </div>
        </div>
    </section>

</body>

</html>