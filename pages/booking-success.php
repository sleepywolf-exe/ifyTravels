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
    <link rel="shortcut icon" href="<?php echo base_url('assets/images/favicon.png?v=' . time()); ?>"
        type="image/x-icon">
    <link rel="apple-touch-icon" href="<?php echo base_url('assets/images/favicon.png?v=' . time()); ?>">
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
        .barcode-strip {
            background-image: repeating-linear-gradient(90deg,
                    #000 0px,
                    #000 2px,
                    transparent 2px,
                    transparent 4px,
                    #000 4px,
                    #000 8px);
        }

        @media print {
            @page {
                margin: 0;
                size: auto;
            }

            body {
                padding: 0;
                margin: 0;
                background: white;
                height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .no-print {
                display: none !important;
            }

            .print-full-width {
                max-width: 100% !important;
                width: 100% !important;
                box-shadow: none !important;
                border: 2px solid #000 !important;
                border-radius: 0 !important;
                margin: 0 !important;
                transform: scale(1) !important;
            }

            /* Adjust Ticket for Print */
            .ticket-card {
                border: 2px solid #e5e7eb;
                break-inside: avoid;
            }

            .bg-gray-100 {
                background-color: white !important;
            }
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center py-6 px-4 font-sans overflow-x-hidden">

    <!-- Ticket Container -->
    <div class="max-w-7xl w-full mx-auto animate-fade-in-up">

        <div class="text-center mb-6 no-print">
            <h1 class="text-3xl md:text-5xl font-bold text-gray-900 mb-2 tracking-tight">Booking Request Sent <i
                    class="fas fa-paper-plane text-primary ml-2"></i></h1>
            <p class="text-base text-gray-600 max-w-2xl mx-auto">Your trip request has been successfully received. We
                will contact you shortly to finalize details.</p>
        </div>

        <!-- Ticket Card -->
        <div
            class="ticket-card bg-white rounded-[2rem] md:rounded-[2.5rem] shadow-2xl overflow-hidden flex flex-col md:flex-row relative border border-gray-100 ring-1 ring-gray-900/5 print-full-width">

            <!-- Left Section (Main Ticket) -->
            <div class="flex-[2] p-8 md:p-10 relative bg-white">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-[0.03] pointer-events-none"
                    style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 20px 20px;">
                </div>

                <!-- Header -->
                <div class="flex justify-between items-start mb-8 relative">
                    <div class="flex items-center gap-6">
                        <img src="<?php echo base_url('assets/images/logo-color.png'); ?>" alt="ifyTravels" width="200" height="60" loading="lazy" class="h-12 w-auto">
                        <span
                            class="hidden md:inline-block text-xs text-primary/80 font-mono tracking-[0.2em] font-bold uppercase border-l-2 border-gray-200 pl-6 ml-2">Booking
                            Request</span>
                    </div>
                    <div class="text-right">
                        <span
                            class="block text-[0.6rem] text-gray-400 uppercase tracking-widest font-semibold mb-1">Class</span>
                        <span
                            class="font-bold text-gray-900 font-mono text-lg bg-gray-100 px-3 py-1 rounded-lg">STD</span>
                    </div>
                </div>

                <!-- From / To Design -->
                <div class="flex items-center justify-between mb-10 px-2 md:px-6 relative">
                    <div class="text-center w-1/3">
                        <span
                            class="block text-4xl md:text-5xl font-bold text-gray-900 mb-2 tracking-tighter">HOM</span>
                        <span
                            class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-[0.6rem] font-bold uppercase tracking-wider">Home</span>
                    </div>

                    <div class="flex-1 flex flex-col items-center px-4 md:px-8">
                        <div class="flex items-center justify-center gap-4 w-full mb-3 text-gray-300">
                            <div class="h-[2px] w-full bg-dashed bg-gray-200"></div>
                            <i class="fas fa-plane text-primary text-xl transform rotate-90 md:rotate-0"></i>
                            <div class="h-[2px] w-full bg-dashed bg-gray-200"></div>
                        </div>
                        <span
                            class="text-xs font-semibold text-gray-500 font-mono"><?php echo $booking ? date('D, d M Y', strtotime($booking['travel_date'])) : date('D, d M Y'); ?></span>
                        <span class="text-[0.65rem] text-gray-400 mt-1">One Way</span>
                    </div>

                    <div class="text-center w-1/3">
                        <span
                            class="block text-4xl md:text-5xl font-bold text-gray-900 mb-2 tracking-tighter">DST</span>
                        <span
                            class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-[0.6rem] font-bold uppercase tracking-wider">Dest</span>
                    </div>
                </div>

                <!-- Passenger Details Grid -->
                <div
                    class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8 bg-gray-50/80 p-6 rounded-3xl border border-gray-100 backdrop-blur-sm">
                    <div class="col-span-2">
                        <span
                            class="block text-[0.65rem] text-gray-400 uppercase tracking-wider mb-1 font-semibold">Primary
                            Passenger</span>
                        <span
                            class="block text-lg font-bold text-gray-900 truncate"><?php echo $booking ? htmlspecialchars($booking['customer_name']) : 'Guest Passenger'; ?></span>
                    </div>

                    <div class="col-span-2">
                        <span
                            class="block text-[0.65rem] text-gray-400 uppercase tracking-wider mb-1 font-semibold">Chosen
                            Package</span>
                        <span
                            class="block text-base font-bold text-gray-900 truncate"><?php echo $booking ? htmlspecialchars($booking['package_name']) : 'Custom Tour Request'; ?></span>
                    </div>

                    <div>
                        <span
                            class="block text-[0.65rem] text-gray-400 uppercase tracking-wider mb-1 font-semibold">Reference
                            ID</span>
                        <span
                            class="block text-base font-bold text-primary font-mono select-all">#<?php echo $booking ? str_pad($booking['id'], 6, '0', STR_PAD_LEFT) : '000000'; ?></span>
                    </div>
                    <div>
                        <span
                            class="block text-[0.65rem] text-gray-400 uppercase tracking-wider mb-1 font-semibold">Duration</span>
                        <span
                            class="block text-base font-bold text-gray-900"><?php echo $booking['duration'] ?? 'Flexible'; ?></span>
                    </div>
                    <div>
                        <span
                            class="block text-[0.65rem] text-gray-400 uppercase tracking-wider mb-1 font-semibold">Status</span>
                        <span
                            class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full text-[0.65rem] font-bold bg-blue-50 text-blue-600 border border-blue-100 uppercase tracking-wide">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                            Request Sent
                        </span>
                    </div>
                    <div>
                        <span
                            class="block text-[0.65rem] text-gray-400 uppercase tracking-wider mb-1 font-semibold">Date</span>
                        <span
                            class="block text-base font-bold text-gray-900"><?php echo $booking ? date('d M', strtotime($booking['travel_date'])) : date('d M'); ?></span>
                    </div>
                </div>

                <!-- Footer / Barcode Area on Main Ticket -->
                <div class="flex justify-between items-end relative">
                    <div class="text-xs text-gray-400 italic leading-tight">
                        * A travel expert will contact you within 24 hours.<br>
                        * Check your clean inbox/spam for next steps.
                    </div>
                    <div class="h-10 w-48 barcode-strip opacity-70 mix-blend-multiply"></div>
                </div>
            </div>

            <!-- Vertical Separator (Realistic Perforation) -->
            <div
                class="relative w-full md:w-px bg-transparent flex-shrink-0 flex flex-col justify-between overflow-hidden">
                <div class="absolute inset-y-0 -left-[1px] border-l-2 border-dashed border-gray-300"></div>
                <!-- Top Notch -->
                <div
                    class="absolute -top-6 left-1/2 -translate-x-1/2 w-12 h-12 bg-gray-100 rounded-full shadow-inner box-border border border-gray-200 z-20">
                </div>
                <!-- Bottom Notch -->
                <div
                    class="absolute -bottom-6 left-1/2 -translate-x-1/2 w-12 h-12 bg-gray-100 rounded-full shadow-inner box-border border border-gray-200 z-20">
                </div>
            </div>

            <!-- Right Section (Stub) -->
            <div
                class="w-full md:w-80 bg-gray-50/50 p-8 flex flex-col justify-between relative border-l border-gray-100">

                <div
                    class="text-center border-b border-gray-200/60 pb-6 mb-6 flex justify-center flex-col items-center">
                    <img src="<?php echo base_url('assets/images/logo-color.png'); ?>" alt="Logo"
                        class="h-6 w-auto mb-2 opacity-90 grayscale hover:grayscale-0 transition-all duration-300">
                    <span
                        class="text-[0.6rem] bg-gray-900 text-white px-2 py-0.5 rounded-full font-mono tracking-widest uppercase">Stub</span>
                </div>

                <div class="space-y-4 text-center flex-1 flex flex-col justify-center">
                    <div>
                        <span
                            class="block text-[0.65rem] text-gray-400 uppercase tracking-wider mb-1 font-semibold">Passenger</span>
                        <span
                            class="font-bold text-gray-900 block text-sm truncate px-4"><?php echo $booking ? htmlspecialchars($booking['customer_name']) : 'Guest'; ?></span>
                    </div>

                    <div class="flex items-center justify-center gap-4">
                        <div class="text-center">
                            <span class="block text-[0.65rem] text-gray-400 uppercase tracking-wider mb-1">From</span>
                            <span class="font-bold text-gray-900 text-xl">HOM</span>
                        </div>
                        <i class="fas fa-arrow-right text-gray-300 text-sm"></i>
                        <div class="text-center">
                            <span class="block text-[0.65rem] text-gray-400 uppercase tracking-wider mb-1">To</span>
                            <span class="font-bold text-gray-900 text-xl">DST</span>
                        </div>
                    </div>

                    <div>
                        <span
                            class="block text-[0.65rem] text-gray-400 uppercase tracking-wider mb-1 font-semibold">Date</span>
                        <span
                            class="font-bold text-gray-900 text-lg font-mono"><?php echo $booking ? date('d M', strtotime($booking['travel_date'])) : date('d M'); ?></span>
                    </div>
                </div>

                <div class="mt-8">
                    <div class="h-12 w-full barcode-strip opacity-70 mix-blend-multiply"></div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="text-center mt-8 space-x-4 no-print">
            <button onclick="window.print()"
                class="bg-gray-900 text-white px-6 py-3 rounded-full font-bold text-base hover:bg-black transition-all shadow-xl hover:shadow-2xl hover:-translate-y-1 inline-flex items-center gap-2">
                <i class="fas fa-print"></i> Print Receipt
            </button>
            <a href="../index.php"
                class="bg-white text-gray-900 px-6 py-3 rounded-full font-bold text-base hover:bg-gray-50 transition-all shadow-lg hover:shadow-xl hover:-translate-y-1 inline-flex items-center gap-2 border border-gray-200">
                <i class="fas fa-home"></i> Return Home
            </a>
        </div>
    </div>

</body>

</html>