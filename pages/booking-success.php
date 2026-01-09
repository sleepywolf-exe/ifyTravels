<?php
// pages/booking-success.php
include __DIR__ . '/../includes/functions.php';
$db = Database::getInstance();

$id = $_GET['id'] ?? null;
$booking = null;

if ($id) {
    $booking = $db->fetch("SELECT * FROM bookings WHERE id = ?", [$id]);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - Ticket</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Courier+Prime:wght@400;700&family=Outfit:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                        mono: ['"Courier Prime"', 'monospace'],
                    },
                    colors: {
                        primary: '#0F766E', // Teal 700
                    }
                }
            }
        }
    </script>
    <style>
        .ticket-notch {
            position: absolute;
            width: 30px;
            height: 30px;
            background-color: #F3F4F6;
            /* Matches body bg */
            border-radius: 50%;
            z-index: 10;
        }

        .notch-top {
            top: -15px;
        }

        .notch-bottom {
            bottom: -15px;
        }

        .barcode-strip {
            background-image: repeating-linear-gradient(90deg,
                    #000 0px,
                    #000 2px,
                    transparent 2px,
                    transparent 4px,
                    #000 4px,
                    #000 8px);
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <!-- Ticket Container -->
    <div class="max-w-4xl w-full mx-auto animate-fade-in-up">

        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Booking Confirmed! <i
                    class="fas fa-check-circle text-green-500"></i></h1>
            <p class="text-gray-600">Your trip has been successfully booked. Here is your ticket.</p>
        </div>

        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row relative">

            <!-- Left Section (Main Ticket) -->
            <div class="flex-1 p-8 relative">
                <!-- Header -->
                <div class="flex justify-between items-start mb-8">
                    <div class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-primary" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        <div>
                            <span
                                class="block text-xl font-bold text-gray-800 tracking-wide uppercase"><?php echo e(get_setting('site_name', 'ifyTravels')); ?></span>
                            <span class="text-xs text-gray-400 font-mono tracking-widest">BOARDING PASS</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="block text-xs text-gray-400 uppercase tracking-widest">Initial</span>
                        <span class="font-bold text-primary font-mono text-lg">ECONOMY</span>
                    </div>
                </div>

                <!-- From / To Design -->
                <div class="flex items-center justify-between mb-8 px-4">
                    <div class="text-center w-1/3">
                        <span class="block text-4xl font-bold text-gray-800 mb-1">HOM</span>
                        <span class="text-xs text-gray-500 uppercase tracking-wider">Home</span>
                    </div>
                    <div class="flex-1 flex flex-col items-center px-4">
                        <i class="fas fa-plane text-primary text-xl mb-2 transform rotate-90 md:rotate-0"></i>
                        <div class="w-full h-0.5 bg-gray-200 relative">
                            <div
                                class="absolute left-0 top-1/2 transform -translate-y-1/2 w-2 h-2 bg-primary rounded-full">
                            </div>
                            <div
                                class="absolute right-0 top-1/2 transform -translate-y-1/2 w-2 h-2 bg-primary rounded-full">
                            </div>
                        </div>
                        <span
                            class="text-xs text-gray-400 mt-1"><?php echo $booking ? htmlspecialchars($booking['travel_date']) : date('Y-m-d'); ?></span>
                    </div>
                    <div class="text-center w-1/3">
                        <span class="block text-4xl font-bold text-gray-800 mb-1">DST</span>
                        <span class="text-xs text-gray-500 uppercase tracking-wider">Destination</span>
                    </div>
                </div>

                <!-- Passenger Details Grid -->
                <div
                    class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8 bg-gray-50 p-6 rounded-2xl border border-gray-100">
                    <div>
                        <span class="block text-xs text-gray-400 uppercase tracking-wider mb-1">Passenger</span>
                        <span
                            class="block font-bold text-gray-800 truncate"><?php echo $booking ? htmlspecialchars($booking['customer_name']) : 'Guest Passenger'; ?></span>
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <span class="block text-xs text-gray-400 uppercase tracking-wider mb-1">Package</span>
                        <span
                            class="block font-bold text-gray-800 truncate"><?php echo $booking ? htmlspecialchars($booking['package_name']) : 'Custom Tour Request'; ?></span>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-400 uppercase tracking-wider mb-1">Booking ID</span>
                        <span
                            class="block font-bold text-gray-800 font-mono">#<?php echo $booking ? str_pad($booking['id'], 6, '0', STR_PAD_LEFT) : '000000'; ?></span>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-400 uppercase tracking-wider mb-1">Date</span>
                        <span
                            class="block font-bold text-gray-800"><?php echo $booking ? date('d M Y', strtotime($booking['travel_date'])) : date('d M Y'); ?></span>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-400 uppercase tracking-wider mb-1">Status</span>
                        <span
                            class="inline-block px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-700 uppercase">Confirmed</span>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-400 uppercase tracking-wider mb-1">Gate</span>
                        <span class="block font-bold text-gray-800">TBD</span>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-400 uppercase tracking-wider mb-1">Seat</span>
                        <span class="block font-bold text-gray-800">ANY</span>
                    </div>
                </div>

                <!-- Footer / Barcode Area on Main Ticket -->
                <div class="flex justify-between items-end">
                    <div class="text-xs text-gray-400">
                        * Please present this ticket upon arrival.<br>
                        * Check your email for detailed itinerary.
                    </div>
                    <div class="h-10 w-48 barcode-strip opacity-80"></div>
                </div>
            </div>

            <!-- Vertical Separator (Dotted Line) -->
            <div class="relative w-full md:w-px bg-white md:border-r-2 border-dashed border-gray-300 flex-shrink-0">
                <!-- Notches for "Tear off" effect -->
                <div class="ticket-notch notch-top right-[50%] md:right-[-15px]"></div>
                <div class="ticket-notch notch-bottom right-[50%] md:right-[-15px]"></div>
            </div>

            <!-- Right Section (Stub) -->
            <div class="w-full md:w-72 bg-gray-50 p-8 flex flex-col justify-between border-l border-white/50 relative">
                <!-- Mobile Notches adjustment if needed, but styling above handles absolute positioning relative to container -->

                <div class="text-center border-b border-gray-200 pb-6 mb-6">
                    <span
                        class="block text-xl font-bold text-gray-800 mb-1"><?php echo e(get_setting('site_name', 'ifyTravels')); ?></span>
                    <span class="text-xs text-primary font-mono tracking-widest uppercase">BOARDING PASS</span>
                </div>

                <div class="space-y-4 text-center">
                    <div>
                        <span class="block text-xs text-gray-400 uppercase tracking-wider mb-0.5">Passenger</span>
                        <span
                            class="font-bold text-gray-800 block text-sm"><?php echo $booking ? htmlspecialchars($booking['customer_name']) : 'Guest'; ?></span>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-400 uppercase tracking-wider mb-0.5">From</span>
                        <span class="font-bold text-gray-800 text-xl tracking-wider">HOM</span>
                    </div>
                    <div>
                        <i class="fas fa-arrow-down text-gray-300"></i>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-400 uppercase tracking-wider mb-0.5">To</span>
                        <span class="font-bold text-gray-800 text-xl tracking-wider">DST</span>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-400 uppercase tracking-wider mb-0.5">Date</span>
                        <span
                            class="font-bold text-gray-800"><?php echo $booking ? date('d M', strtotime($booking['travel_date'])) : date('d M'); ?></span>
                    </div>
                </div>

                <div class="mt-8">
                    <div class="h-12 w-full barcode-strip opacity-80"></div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="text-center mt-8 space-x-4">
            <button onclick="window.print()"
                class="bg-gray-800 text-white px-6 py-2 rounded-full font-bold hover:bg-gray-900 transition shadow-lg inline-flex items-center">
                <i class="fas fa-print mr-2"></i> Print Ticket
            </button>
            <a href="../index.php"
                class="bg-white text-gray-800 px-6 py-2 rounded-full font-bold hover:bg-gray-100 transition shadow-lg inline-flex items-center border border-gray-200">
                <i class="fas fa-home mr-2"></i> Go Home
            </a>
        </div>
    </div>

</body>

</html>