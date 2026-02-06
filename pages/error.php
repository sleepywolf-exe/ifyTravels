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

    <section class="min-h-screen flex items-center justify-center py-20">
        <div class="container mx-auto px-4">
            <div class="flex flex-col items-center justify-center text-center max-w-5xl mx-auto">
                
                <!-- 1. Error Code (Matched Size to Title) -->
                <h1 class="font-heading font-black text-slate-900 leading-none opacity-20 text-6xl md:text-8xl mb-[-20px] z-0">
                    <?php echo $errorCode; ?>
                </h1>

                <!-- 2. GIF Image -->
                <div class="w-full max-w-md md:max-w-lg relative z-10">
                    <img src="<?php echo base_url('assets/images/404.gif'); ?>" 
                         alt="404 Animation" 
                         class="w-full h-auto object-contain mx-auto">
                </div>

                <!-- 3. Content -->
                <div class="relative z-20 mt-[-20px]">
                    <h3 class="font-heading font-black text-slate-900 tracking-tight leading-none mb-4 text-4xl md:text-6xl">
                        <?php echo $errorTitle; ?>
                    </h3>

                    <p class="text-slate-500 text-lg md:text-xl max-w-2xl mx-auto leading-relaxed font-light mb-8">
                        <?php echo $errorMessage; ?>
                    </p>

                    <a href="<?php echo base_url(); ?>" class="link_404 group">
                        Go to Home
                        <i class="fa-solid fa-arrow-right ml-3 text-sm group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>

            </div>
        </div>
    </section>

</body>

</html>