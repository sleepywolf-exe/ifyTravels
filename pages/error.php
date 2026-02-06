<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Error'; ?> - ifyTravels</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Great+Vibes&display=swap" rel="stylesheet">

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
            overflow: hidden; /* Prevent scrolling */
        }
        
        .page_404 {
            padding: 0;
            background: #fff;
            font-family: 'Plus Jakarta Sans', serif;
            height: 100vh;
            width: 100vw;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    
        .four_zero_four_bg {
            background-image: url('<?php echo base_url("assets/images/404.gif"); ?>');
            height: 600px;
            width: 100%;
            background-position: center;
            background-repeat: no-repeat;
            background-size: contain;
            position: relative;
            z-index: 10;
            display: flex;
            align-items: flex-start;
            justify-content: center;
        }
    
        .four_zero_four_bg h1 {
            font-size: 180px;
            margin-bottom: 0;
            margin-top: -50px;
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
            text-decoration: none;
        }
        
        .link_404:hover {
            background: #0d6962;
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(15, 118, 110, 0.4);
        }
    
        .contant_box_404 {
            margin-top: -80px;
            position: relative;
            z-index: 20;
        }
    </style>
</head>
<body class="bg-white">

    <section class="page_404">
        <div class="container mx-auto px-4">
            <div class="flex flex-col items-center justify-center text-center">
                
                <div class="w-full max-w-5xl">
                    <div class="four_zero_four_bg w-full">
                        <h1 class="text-center font-heading font-black text-slate-900 drop-shadow-md tracking-tighter mix-blend-multiply opacity-90">
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
                            <i class="fa-solid fa-arrow-right ml-3 text-lg group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>
                
            </div>
        </div>
    </section>

</body>
</html>