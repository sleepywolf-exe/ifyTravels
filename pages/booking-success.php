<?php
// pages/booking-success.php
include __DIR__ . '/../includes/functions.php';

$id = $_GET['id'] ?? null;
$booking = null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
    $stmt->execute([$id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
}

$pageTitle = "Booking Confirmed";
include __DIR__ . '/../includes/header.php';
?>

<div class="min-h-screen bg-charcoal flex items-center justify-center py-20 relative overflow-hidden">
    
    <!-- Background Accents -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
        <div class="absolute top-0 right-0 w-96 h-96 bg-secondary/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-primary/10 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="max-w-3xl mx-auto text-center mb-10">
            <div class="w-20 h-20 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-6 animate-bounce">
                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h1 class="text-4xl md:text-5xl font-heading font-bold text-white mb-4">You're All Set!</h1>
            <p class="text-gray-400 text-lg">Your booking request has been successfully received. A concierge will be in touch shortly.</p>
        </div>

        <!-- Ticket Card -->
        <div class="max-w-4xl mx-auto bg-white rounded-3xl overflow-hidden shadow-2xl flex flex-col md:flex-row">
            
            <!-- Left: Main Ticket -->
            <div class="md:w-3/4 p-8 md:p-10 relative bg-white text-gray-900">
                <!-- Watermark -->
                <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 20px 20px;"></div>

                <!-- Header -->
                <div class="flex justify-between items-start mb-8">
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 bg-secondary rounded-full flex items-center justify-center text-white font-bold">iT</div>
                        <span class="font-heading font-bold text-xl tracking-tight">ifyTravels</span>
                    </div>
                    <div class="text-right">
                        <span class="block text-xs text-gray-400 uppercase tracking-wider mb-1">Boarding Pass</span>
                        <span class="font-bold font-mono text-lg bg-gray-100 px-3 py-1 rounded">PENDING</span>
                    </div>
                </div>

                <!-- Route -->
                <div class="flex items-center justify-between mb-10 px-4">
                    <div class="text-center">
                        <span class="block text-4xl font-bold text-gray-900 tracking-tighter">HOM</span>
                        <span class="text-xs text-gray-500 uppercase tracking-wider">Home</span>
                    </div>
                    <div class="flex-1 px-8 flex flex-col items-center">
                        <div class="w-full flex items-center gap-2 text-gray-300 mb-2">
                            <div class="h-px bg-gray-300 flex-1 border-t border-dashed"></div>
                            <svg class="w-5 h-5 text-secondary transform rotate-90 md:rotate-0" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
                            <div class="h-px bg-gray-300 flex-1 border-t border-dashed"></div>
                        </div>
                        <span class="text-xs font-bold text-gray-500"><?php echo $booking ? date('D, d M Y', strtotime($booking['travel_date'])) : date('D, d M Y'); ?></span>
                    </div>
                    <div class="text-center">
                        <span class="block text-4xl font-bold text-gray-900 tracking-tighter">DST</span>
                        <span class="text-xs text-gray-500 uppercase tracking-wider">Dest</span>
                    </div>
                </div>

                <!-- Details Grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 bg-gray-50 p-6 rounded-2xl border border-gray-100">
                    <div class="col-span-2">
                        <span class="text-xs text-gray-400 uppercase tracking-wider block mb-1">Passenger</span>
                        <span class="font-bold text-lg truncate block"><?php echo $booking ? htmlspecialchars($booking['customer_name']) : 'Guest'; ?></span>
                    </div>
                    <div class="col-span-2">
                         <span class="text-xs text-gray-400 uppercase tracking-wider block mb-1">Package</span>
                        <span class="font-bold text-sm truncate block"><?php echo $booking ? htmlspecialchars($booking['package_name']) : 'Custom'; ?></span>
                    </div>
                    <div>
                         <span class="text-xs text-gray-400 uppercase tracking-wider block mb-1">Booking Ref</span>
                        <span class="font-bold font-mono text-secondary">#<?php echo $booking ? str_pad($booking['id'], 6, '0', STR_PAD_LEFT) : '000000'; ?></span>
                    </div>
                    <div>
                         <span class="text-xs text-gray-400 uppercase tracking-wider block mb-1">Travelers</span>
                        <span class="font-bold text-gray-900"><?php echo $booking ? ($booking['adults'] + $booking['children']) : '1'; ?></span>
                    </div>
                </div>
                
                <div class="mt-8 flex justify-between items-end">
                    <p class="text-xs text-gray-400 italic max-w-xs">Please present this reference number when contacting our support team.</p>
                </div>
            </div>

            <!-- Right: Stub -->
            <div class="md:w-1/4 bg-gray-900 text-white p-8 flex flex-col justify-between relative border-l border-dashed border-gray-700">
                <!-- Perforation Circles -->
                <div class="absolute -top-3 -left-3 w-6 h-6 bg-charcoal rounded-full"></div>
                <div class="absolute -bottom-3 -left-3 w-6 h-6 bg-charcoal rounded-full"></div>

                <div class="text-center">
                    <span class="text-xs text-gray-500 uppercase tracking-wider block mb-2">Booking ID</span>
                     <span class="font-bold font-mono text-2xl text-white"><?php echo $booking ? str_pad($booking['id'], 6, '0', STR_PAD_LEFT) : '000'; ?></span>
                </div>

                <div class="space-y-4 text-center">
                    <div>
                        <span class="text-xs text-gray-500 uppercase tracking-wider block mb-1">Date</span>
                        <span class="font-bold text-white"><?php echo $booking ? date('d M', strtotime($booking['travel_date'])) : date('d M'); ?></span>
                    </div>
                    <div>
                         <span class="text-xs text-gray-500 uppercase tracking-wider block mb-1">Time</span>
                        <span class="font-bold text-white">10:00 AM</span>
                    </div>
                </div>

                <div class="mt-4 opacity-50">
                    <!-- Fake Barcode -->
                    <div class="h-10 w-full bg-repeating-linear-gradient-to-r from-white to-transparent via-white bg-[length:4px_100%]"></div>
                </div>
            </div>

        </div>

        <!-- Actions -->
        <div class="flex justify-center gap-4 mt-12">
            <button onclick="window.print()" class="glass-button flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Print Ticket
            </button>
            <a href="<?php echo base_url(); ?>" class="glass-button bg-white/10 hover:bg-white/20 border-white/20 flex items-center gap-2">
                Return Home
            </a>
        </div>
    </div>
</div>

<style>
    @media print {
        body * { visibility: hidden; }
        .container, .container * { visibility: visible; }
        .container { position: absolute; left: 0; top: 0; padding: 0; margin: 0; width: 100%; }
        .glass-button, h1, p.text-lg, .animate-bounce { display: none; }
        .bg-charcoal { background: white !important; }
        .text-white { color: black !important; }
        .shadow-2xl { shadow: none !important; border: 1px solid #ccc; }
    }
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>