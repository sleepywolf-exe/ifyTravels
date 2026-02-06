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
        /*======================
            404 page
        =======================*/
        body {
            overflow-x: hidden;
        }

        .page_404 {
            padding: 0;
            background: #fff;
            font-family: 'Plus Jakarta Sans', serif;
            min-height: 100vh;
            width: 100vw;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .four_zero_four_bg {
            background-image: url('<?php echo base_url("assets/images/404.gif"); ?>');
            height: 500px;
            width: 100%;
            background-position: center;
            background-repeat: no-repeat;
            background-size: contain;
            position: relative;
            z-index: 10;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            margin-bottom: 20px;
        }

        .four_zero_four_bg h1 {
            font-size: 160px;
            margin-bottom: 0;
            margin-top: 0;
            line-height: 1;
        }

        .link_404 {
            color: #fff !important;
            padding: 18px 45px;
            background: #0F766E;
            margin: 30px 0 0;
            display: inline-block;
            border-radius: 99px;
            text-transform: uppercase;
            font-weight: 800;
            font-size: 16px;
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

        .contant_box_404 {
            margin-top: -60px;
            position: relative;
            z-index: 20;
        }

        /* Stats Section */
        .stats-container {
            width: 100%;
            max-width: 1200px;
            margin-top: 80px;
            border-top: 1px solid #f1f5f9;
            padding-top: 60px;
            padding-bottom: 40px;
        }

        .stat-item {
            text-align: center;
            transition: transform 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            font-size: 2.5rem;
            color: #0F766E;
            margin-bottom: 1rem;
            opacity: 0.8;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            color: #0F766E;
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #64748b;
        }
    </style>
</head>

<body class="bg-white text-slate-900">

    <section class="page_404">
        <div class="container mx-auto px-4 flex-grow flex flex-col items-center justify-center">

            <div class="w-full max-w-6xl text-center">
                <div class="four_zero_four_bg w-full">
                    <h1
                        class="text-center font-heading font-black text-slate-900 drop-shadow-md tracking-tighter mix-blend-multiply opacity-90">
                        <?php echo $errorCode; ?>
                    </h1>
                </div>

                <div class="contant_box_404">
                    <h3 class="font-heading text-4xl md:text-5xl font-black mb-4 text-slate-900 tracking-tight">
                        <?php echo $errorTitle; ?>
                    </h3>

                    <p class="text-slate-500 mb-8 text-lg md:text-xl max-w-3xl mx-auto leading-relaxed font-light">
                        <?php echo $errorMessage; ?>
                    </p>

                    <a href="<?php echo base_url(); ?>" class="link_404 group">
                        Go to Home
                        <i
                            class="fa-solid fa-arrow-right ml-3 text-lg group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>

            <!-- Stats Section (Added to fill space) -->
            <div class="stats-container grid grid-cols-2 md:grid-cols-4 gap-8 md:gap-12 fade-in">
                <!-- Stat 1 -->
                <div class="stat-item group cursor-default">
                    <div class="stat-icon group-hover:scale-110 transition-transform duration-300">
                        <i class="fa-solid fa-plane-departure"></i>
                    </div>
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Luxury Trips</div>
                </div>

                <!-- Stat 2 -->
                <div class="stat-item group cursor-default">
                    <div class="stat-icon group-hover:scale-110 transition-transform duration-300">
                        <i class="fa-solid fa-star"></i>
                    </div>
                    <div class="stat-number">98%</div>
                    <div class="stat-label">5-Star Reviews</div>
                </div>

                <!-- Stat 3 -->
                <div class="stat-item group cursor-default">
                    <div class="stat-icon group-hover:scale-110 transition-transform duration-300">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </div>
                    <div class="stat-number">25+</div>
                    <div class="stat-label">Destinations</div>
                </div>

                <!-- Stat 4 -->
                <div class="stat-item group cursor-default">
                    <div class="stat-icon group-hover:scale-110 transition-transform duration-300">
                        <i class="fa-solid fa-headset"></i>
                    </div>
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Concierge</div>
                </div>
            </div>

        </div>
    </section>

</body>

</html>