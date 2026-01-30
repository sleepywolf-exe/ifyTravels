<?php
// pages/booking-success.php
include __DIR__ . '/../includes/functions.php';

$id = $_GET['id'] ?? null;
$booking = null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
    $stmt->execute([$id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch full package details if available
    $packageDetails = null;
    if ($booking && !empty($booking['package_id'])) {
        $stmt2 = $pdo->prepare("SELECT * FROM packages WHERE id = ?");
        $stmt2->execute([$booking['package_id']]);
        $packageDetails = $stmt2->fetch(PDO::FETCH_ASSOC);
        
        if ($packageDetails) {
            $packageDetails['features'] = json_decode($packageDetails['features'] ?? '[]', true);
            $packageDetails['inclusions'] = json_decode($packageDetails['inclusions'] ?? '[]', true);
            $packageDetails['themes'] = json_decode($packageDetails['themes'] ?? '[]', true);
        }
    }
}

$pageTitle = "Booking Confirmed";
include __DIR__ . '/../includes/header.php';
?>

<div class="min-h-screen bg-slate-50 pt-40 pb-20 relative overflow-hidden print:pt-0 print:pb-0 print:bg-white">

    <!-- Background Accents -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none no-print">
        <div
            class="absolute top-0 right-0 w-96 h-96 bg-secondary/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2">
        </div>
        <div
            class="absolute bottom-0 left-0 w-96 h-96 bg-primary/10 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2">
        </div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="max-w-3xl mx-auto text-center mb-12 no-print">
            <div
                class="w-24 h-24 bg-emerald-100/50 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl ring-4 ring-white animate-bounce">
                <svg class="w-12 h-12 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-4xl md:text-6xl font-heading font-black text-slate-900 mb-4 tracking-tight">You're All Set!
            </h1>
            <p class="text-slate-500 text-lg font-medium max-w-lg mx-auto leading-relaxed">
                Your booking request has been securely received. Our concierge team is already reviewing your details.
            </p>
        </div>

        <!-- Ticket Card -->
        <div
            class="max-w-7xl mx-auto bg-white rounded-[2rem] overflow-hidden shadow-2xl shadow-slate-200 flex flex-col md:flex-row ticket-card border border-slate-100 relative print:shadow-none print:border-black print:rounded-none">

            <!-- Left: Main Ticket -->
            <div class="md:w-3/4 p-8 md:p-10 relative bg-white text-gray-900">
                <!-- Watermark -->
                <div class="absolute inset-0 opacity-[0.03] pointer-events-none"
                    style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 24px 24px;">
                </div>

                <!-- Header -->
                <div class="flex justify-between items-start mb-10 relative z-10">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-12 h-12 bg-slate-900 rounded-xl flex items-center justify-center text-white font-bold print-color-adjust shadow-lg">
                            <span class="text-xl">iT</span>
                        </div>
                        <div>
                            <span
                                class="font-heading font-black text-2xl tracking-tight text-slate-900 block leading-none">ifyTravels</span>
                            <span class="text-xs text-slate-400 font-bold uppercase tracking-widest">Boarding
                                Pass</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span
                            class="font-bold font-mono text-xs bg-emerald-100 text-emerald-700 px-3 py-1.5 rounded-lg border border-emerald-200 uppercase tracking-wider">CONFIRMED
                            REQUEST</span>
                    </div>
                </div>

                <!-- Route -->
                <div class="flex items-center justify-between mb-12 px-2 relative z-10">
                    <div class="text-center">
                        <div
                            class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-2 border border-slate-100">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                </path>
                            </svg>
                        </div>
                        <span class="text-xs text-slate-500 uppercase font-bold tracking-widest">Home</span>
                    </div>

                    <div class="flex-1 px-6 flex flex-col items-center -mt-6">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Travel Date</span>
                        <span
                            class="text-sm font-black text-slate-900 bg-slate-50 px-3 py-1 rounded-lg border border-slate-100 mb-3">
                            <?php echo $booking ? date('D, d F Y', strtotime($booking['travel_date'])) : date('D, d F Y'); ?>
                        </span>

                        <div class="w-full flex items-center gap-3 text-slate-300">
                            <div class="h-0.5 bg-slate-100 flex-1 relative overflow-hidden">
                                <div class="absolute inset-0 bg-slate-200 w-1/2 animate-[shimmer_2s_infinite]"></div>
                            </div>
                            <svg class="w-6 h-6 text-primary transform rotate-90 md:rotate-0 print:text-black"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z">
                                </path>
                            </svg>
                            <div class="h-0.5 bg-slate-100 flex-1"></div>
                        </div>
                    </div>

                    <div class="text-center">
                        <div
                            class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-2 border border-primary/20">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <span class="text-xs text-slate-500 uppercase font-bold tracking-widest">Dest</span>
                    </div>
                </div>

                <!-- Detailed Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 bg-slate-50 p-8 rounded-2xl border border-slate-100 print:bg-white print:border-gray-200 relative z-10">
                    
                    <!-- Basic Detials -->
                    <div class="md:col-span-4 space-y-6">
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest block mb-1.5">Passenger Name</span>
                            <span class="font-bold text-lg text-slate-900 block truncate">
                                <?php echo $booking ? htmlspecialchars($booking['customer_name']) : 'Guest'; ?>
                            </span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest block mb-1.5">Selected Package</span>
                            <span class="font-bold text-base text-slate-900 block leading-tight" title="<?php echo $booking ? htmlspecialchars($booking['package_name']) : 'Custom'; ?>">
                                <?php echo $booking ? htmlspecialchars($booking['package_name']) : 'Custom Package'; ?>
                            </span>
                        </div>
                        <div class="flex gap-8">
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest block mb-1.5">Travelers</span>
                                <span class="font-bold text-lg text-slate-900">
                                    <?php echo $booking ? ($booking['adults'] + $booking['children']) : '1'; ?> <span class="text-sm font-normal text-slate-400">Ppl</span>
                                </span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest block mb-1.5">Ref No.</span>
                                <span class="font-mono text-lg font-bold text-primary print:text-black">
                                    #<?php echo $booking ? str_pad($booking['id'], 6, '0', STR_PAD_LEFT) : '000000'; ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Package Features/Inclusions -->
                    <?php if ($packageDetails): ?>
                    <div class="md:col-span-8 grid grid-cols-1 sm:grid-cols-2 gap-6 pl-0 md:pl-8 border-t md:border-t-0 md:border-l border-slate-200 pt-6 md:pt-0">
                        <!-- Themes -->
                        <?php if(!empty($packageDetails['themes'])): ?>
                        <div class="col-span-1 sm:col-span-2">
                             <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest block mb-2">Experience Type</span>
                             <div class="flex flex-wrap gap-2">
                                <?php foreach(array_slice($packageDetails['themes'], 0, 5) as $theme): ?>
                                    <span class="px-2 py-1 bg-white border border-slate-200 rounded text-xs font-bold text-slate-600 uppercase tracking-wider"><?php echo htmlspecialchars($theme); ?></span>
                                <?php endforeach; ?>
                             </div>
                        </div>
                        <?php endif; ?>

                        <!-- Features -->
                        <?php if(!empty($packageDetails['features'])): ?>
                        <div>
                             <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest block mb-2">Highlights</span>
                             <ul class="space-y-1.5">
                                <?php foreach(array_slice($packageDetails['features'], 0, 4) as $feat): ?>
                                    <li class="col-span-1 md:col-span-2 text-xs font-semibold text-slate-700 flex items-start gap-2">
                                        <svg class="w-3.5 h-3.5 text-emerald-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        <span class="leading-tight"><?php echo htmlspecialchars($feat); ?></span>
                                    </li>
                                <?php endforeach; ?>
                             </ul>
                        </div>
                        <?php endif; ?>

                        <!-- Inclusions -->
                        <?php if(!empty($packageDetails['inclusions'])): ?>
                        <div>
                             <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest block mb-2">Inclusions</span>
                             <ul class="space-y-1.5">
                                <?php foreach(array_slice($packageDetails['inclusions'], 0, 4) as $inc): ?>
                                    <li class="col-span-1 md:col-span-2 text-xs font-semibold text-slate-700 flex items-start gap-2">
                                        <svg class="w-3.5 h-3.5 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span class="leading-tight"><?php echo htmlspecialchars($inc); ?></span>
                                    </li>
                                <?php endforeach; ?>
                             </ul>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="mt-8 flex justify-center md:justify-start">
                    <p
                        class="text-[10px] text-slate-400 italic bg-white px-3 py-1 rounded-full border border-slate-100 shadow-sm inline-flex items-center gap-1">
                        <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Please save this reference number for future communication.
                    </p>
                </div>
            </div>

            <!-- Right: Stub -->
            <div
                class="md:w-1/4 bg-slate-900 text-white p-8 flex flex-col justify-between relative border-l-2 border-dashed border-slate-700/50 print:bg-gray-100 print:text-black print:border-gray-300">
                <!-- Perforation Circles -->
                <div class="absolute -top-3 -left-3 w-6 h-6 bg-slate-50 rounded-full print:hidden"></div>
                <div class="absolute -bottom-3 -left-3 w-6 h-6 bg-slate-50 rounded-full print:hidden"></div>

                <div class="absolute inset-0 bg-noise opacity-10"></div>

                <div class="text-center relative z-10">
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest block mb-2">Booking
                        ID</span>
                    <span class="font-bold font-mono text-3xl text-white print:text-black tracking-widest">
                        <?php echo $booking ? str_pad($booking['id'], 6, '0', STR_PAD_LEFT) : '000'; ?>
                    </span>
                </div>

                <div class="space-y-6 text-center relative z-10 my-8">
                    <div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest block mb-1">Boarding
                            Time</span>
                        <span class="font-bold text-white print:text-black text-xl">10:00 AM</span>
                    </div>
                    <div>
                        <span
                            class="text-[10px] text-slate-400 font-bold uppercase tracking-widest block mb-1">Gate</span>
                        <span class="font-bold text-white print:text-black text-xl">TBD</span>
                    </div>
                </div>

                <div class="mt-auto opacity-40 relative z-10">
                    <!-- Fake Barcode -->
                    <div
                        class="h-12 w-full bg-repeating-linear-gradient-to-r from-white to-transparent via-white bg-[length:4px_100%] print:brightness-0 mix-blend-overlay">
                    </div>
                </div>
            </div>

        </div>

        <!-- Actions -->
        <div class="flex flex-col md:flex-row justify-center gap-4 mt-12 no-print">
            <button onclick="window.print()"
                class="px-8 py-4 rounded-xl bg-slate-900 text-white font-bold hover:bg-slate-800 transition-all shadow-xl shadow-slate-900/20 flex items-center justify-center gap-2 group">
                <svg class="w-5 h-5 group-hover:-translate-y-1 transition-transform" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                    </path>
                </svg>
                Download Ticket
            </button>
            <a href="<?php echo base_url(); ?>"
                class="px-8 py-4 rounded-xl bg-white text-slate-700 font-bold hover:bg-slate-50 transition-all border border-slate-200 shadow-lg shadow-slate-200/50 flex items-center justify-center gap-2">
                Return Home
            </a>
        </div>
    </div>
</div>


<style>
    @media print {
        @page {
            margin: 0;
            size: auto;
        }

        body {
            background: white !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        nav,
        footer,
        .no-print,
        #navbar,
        #mobile-menu-btn {
            display: none !important;
        }

        .min-h-screen {
            min-height: 0 !important;
            padding: 20px !important;
            display: block !important;
        }

        .container {
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .ticket-card {
            box-shadow: none !important;
            border: 2px solid #000 !important;
            border-radius: 12px !important;
            margin: 0 auto !important;
            width: 100% !important;
            max-width: 800px !important;
        }

        /* Force text colors for thermal printers / b&w */
        .text-white {
            color: black !important;
        }

        .text-gray-400 {
            color: #555 !important;
        }

        .bg-charcoal,
        .bg-gray-900 {
            background-color: white !important;
        }
    }
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>