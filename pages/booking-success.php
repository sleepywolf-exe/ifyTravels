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

        <!-- "Ultra-Wide" Innovative Ticket -->
        <div
            class="w-full max-w-[95%] mx-auto bg-white rounded-[3rem] overflow-hidden shadow-2xl shadow-slate-200 flex flex-col xl:flex-row ticket-card border border-slate-100 relative print:shadow-none print:border-black print:rounded-none">

            <!-- Main Content (Wide) -->
            <div class="flex-1 p-8 md:p-12 relative bg-white text-gray-900 grid grid-cols-1 xl:grid-cols-12 gap-12">
                <!-- Background Pattern (Guilloche Style) -->
                <div class="absolute inset-0 opacity-[0.02] pointer-events-none"
                    style="background-image: repeating-linear-gradient(45deg, #000 0, #000 1px, transparent 0, transparent 50%); background-size: 20px 20px;">
                </div>

                <!-- Column 1: Identity (4 Cols) -->
                <div
                    class="xl:col-span-4 flex flex-col justify-between border-b xl:border-b-0 xl:border-r border-slate-100 pb-8 xl:pb-0 pr-0 xl:pr-12">
                    <div>
                        <!-- Brand Header -->
                        <div class="flex items-center gap-4 mb-10">
                            <div
                                class="w-14 h-14 bg-slate-900 rounded-2xl flex items-center justify-center text-white font-bold shadow-lg shadow-slate-900/20">
                                <span class="text-2xl font-black">iT</span>
                            </div>
                            <div>
                                <span
                                    class="font-heading font-black text-3xl tracking-tight text-slate-900 block leading-none">ifyTravels</span>
                                <span
                                    class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-1 block">Official
                                    Boarding Document</span>
                            </div>
                        </div>

                        <!-- Passenger Info -->
                        <div class="mb-8">
                            <span
                                class="text-[10px] text-slate-400 font-bold uppercase tracking-widest block mb-1">Passenger
                                Name</span>
                            <h2 class="font-heading font-black text-3xl md:text-4xl text-slate-900 leading-tight">
                                <?php echo $booking ? htmlspecialchars($booking['customer_name']) : 'Guest Name'; ?>
                            </h2>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <span
                                    class="text-[10px] text-slate-400 font-bold uppercase tracking-widest block mb-1">Status</span>
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold uppercase tracking-wider">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Confirmed
                                </span>
                            </div>
                            <div>
                                <span
                                    class="text-[10px] text-slate-400 font-bold uppercase tracking-widest block mb-1">Travelers</span>
                                <span class="font-bold text-lg text-slate-900">
                                    <?php echo $booking ? ($booking['adults'] + $booking['children']) : '1'; ?> <span
                                        class="text-xs font-normal text-slate-400 uppercase">Pax</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer ID -->
                    <div class="hidden xl:block pt-8 opacity-40">
                        <span class="font-mono text-[10px] block mb-1">ISSUED BY IFYTRAVELS LTD.</span>
                        <span class="font-mono text-[10px] block">REF: <?php echo uniqid(); ?></span>
                    </div>
                </div>

                <!-- Column 2: Journey & Itinerary (5 Cols) -->
                <div
                    class="xl:col-span-5 flex flex-col justify-center border-b xl:border-b-0 xl:border-r border-slate-100 pb-8 xl:pb-0 px-0 xl:px-12 relative">

                    <!-- Journey Graphic -->
                    <div class="flex items-center justify-between mb-10 w-full">
                        <div class="text-center">
                            <span class="text-4xl md:text-5xl font-black text-slate-200 font-heading">HOM</span>
                            <span
                                class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mt-1">Origin</span>
                        </div>

                        <div class="flex-1 px-8 relative flex items-center justify-center">
                            <div class="absolute inset-x-0 h-px bg-slate-200 border-t border-dashed border-slate-300">
                            </div>
                            <div
                                class="relative bg-white px-3 py-1 border border-slate-100 rounded-full flex items-center gap-2 shadow-sm">
                                <span class="w-2 h-2 rounded-full bg-primary"></span>
                                <span class="text-xs font-bold text-slate-600">
                                    <?php echo $booking ? date('d M Y', strtotime($booking['travel_date'])) : date('d M Y'); ?>
                                </span>
                            </div>
                        </div>

                        <div class="text-center">
                            <span class="text-4xl md:text-5xl font-black text-slate-900 font-heading">DST</span>
                            <span
                                class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mt-1">Dest</span>
                        </div>
                    </div>

                    <!-- Package Name & Tags -->
                    <div class="text-center w-full">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest block mb-3">Your
                            Experience</span>
                        <h3 class="text-2xl font-bold text-slate-900 leading-tight mb-4 max-w-md mx-auto">
                            <?php echo $booking ? htmlspecialchars($booking['package_name']) : 'Custom Travel Package'; ?>
                        </h3>

                        <?php if ($packageDetails && !empty($packageDetails['themes'])): ?>
                            <div class="flex flex-wrap justify-center gap-2">
                                <?php foreach (array_slice($packageDetails['themes'], 0, 3) as $theme): ?>
                                    <span
                                        class="px-2 py-1 bg-slate-50 border border-slate-200 text-[10px] font-bold text-slate-500 uppercase rounded-sm"><?php echo htmlspecialchars($theme); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Column 3: Highlights Checklist (3 Cols) -->
                <div class="xl:col-span-3 flex flex-col justify-center pl-0 xl:pl-4">
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest block mb-6">Trip
                        Inclusions</span>

                    <?php if ($packageDetails): ?>
                        <ul class="space-y-4">
                            <?php
                            $highlights = !empty($packageDetails['features']) ? array_slice($packageDetails['features'], 0, 4) : ['Luxury Accommodation', 'Private Transfers', 'Guided Tours', '24/7 Support'];
                            foreach ($highlights as $item):
                                ?>
                                <li class="flex items-start gap-3 group">
                                    <div
                                        class="w-5 h-5 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center shrink-0 mt-0.5 group-hover:bg-emerald-500 group-hover:text-white transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <span
                                        class="text-sm font-medium text-slate-600 leading-tight group-hover:text-slate-900 transition-colors">
                                        <?php echo htmlspecialchars($item); ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-sm text-slate-400 italic">Details will be shared by agent.</p>
                    <?php endif; ?>

                    <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <span
                                class="text-[10px] text-slate-400 font-bold uppercase tracking-widest block">Reference</span>
                            <span
                                class="font-mono text-lg font-bold text-primary">#<?php echo $booking ? str_pad($booking['id'], 6, '0', STR_PAD_LEFT) : '000000'; ?></span>
                        </div>
                        <div
                            class="w-10 h-10 border border-slate-200 rounded flex items-center justify-center bg-slate-50">
                            <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Stub (Dark) -->
            <div
                class="xl:w-80 bg-slate-900 text-white p-8 xl:p-10 flex flex-col justify-between relative border-l-2 border-dashed border-slate-700/50">
                <!-- Perforation -->
                <div class="absolute -top-3 -left-3 w-6 h-6 bg-slate-50 rounded-full print:hidden"></div>
                <div class="absolute -bottom-3 -left-3 w-6 h-6 bg-slate-50 rounded-full print:hidden"></div>

                <!-- Stub Header -->
                <div class="text-center border-b border-white/10 pb-8">
                    <span class="text-[10px] font-bold text-white/40 uppercase tracking-[0.2em] block mb-2">Gate
                        Pass</span>
                    <span
                        class="font-heading font-black text-4xl block text-white"><?php echo rand(1, 9); ?><?php echo chr(rand(65, 90)); ?></span>
                </div>

                <!-- Stub Info -->
                <div class="space-y-6 my-auto py-8">
                    <div>
                        <span class="text-[10px] font-bold text-white/40 uppercase tracking-widest block mb-1">Boarding
                            Time</span>
                        <span class="text-xl font-bold">10:00 AM</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-white/40 uppercase tracking-widest block mb-1">Gate
                            Closes</span>
                        <span class="text-xl font-bold text-red-400">10:45 AM</span>
                    </div>
                </div>

                <!-- Barcode -->
                <div class="bg-white p-2 rounded">
                    <div
                        class="h-10 bg-[url('https://upload.wikimedia.org/wikipedia/commons/thumb/d/d0/QR_code_for_mobile_English_Wikipedia.svg/1200px-QR_code_for_mobile_English_Wikipedia.svg.png')] bg-contain bg-center opacity-80 mix-blend-multiply">
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