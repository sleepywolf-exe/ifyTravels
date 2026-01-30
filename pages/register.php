<?php
$pageTitle = "Sign Up";
include __DIR__ . '/../includes/header.php';
?>

<div class="min-h-screen bg-charcoal flex items-center justify-center py-20 relative overflow-hidden">
    
    <!-- Background -->
    <div class="absolute inset-0">
        <img src="<?php echo base_url('assets/images/destinations/thailand.jpg'); ?>" class="w-full h-full object-cover opacity-20" alt="Register Background"> 
        <div class="absolute inset-0 bg-gradient-to-t from-charcoal via-charcoal/90 to-charcoal/80"></div>
    </div>

    <!-- Register Card -->
    <div class="w-full max-w-lg p-6 relative z-10 animate-fade-in-up">
        
        <div class="text-center mb-8">
            <h1 class="text-3xl font-heading font-bold text-white mb-2">Create Account</h1>
            <p class="text-gray-400">Join our exclusive community of travelers.</p>
        </div>

        <div class="glass-form !p-10 !bg-white/5 border border-white/10 rounded-3xl shadow-2xl relative">
            
            <form id="register-form" class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-300 mb-2 ml-1">Full Name</label>
                    <input type="text" required placeholder="John Doe"
                        class="glass-input w-full !bg-white/5 !border-white/10 text-white focus:!border-secondary transition">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-300 mb-2 ml-1">Email Address</label>
                    <input type="email" required placeholder="name@example.com"
                        class="glass-input w-full !bg-white/5 !border-white/10 text-white focus:!border-secondary transition">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-300 mb-2 ml-1">Password</label>
                    <input type="password" required placeholder="••••••••"
                        class="glass-input w-full !bg-white/5 !border-white/10 text-white focus:!border-secondary transition">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-300 mb-2 ml-1">Confirm Password</label>
                    <input type="password" required placeholder="••••••••"
                        class="glass-input w-full !bg-white/5 !border-white/10 text-white focus:!border-secondary transition">
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" required class="accent-secondary h-4 w-4 rounded border-gray-300">
                    <label class="text-sm text-gray-400">I agree to the <a href="#" class="text-secondary hover:underline">Terms & Conditions</a></label>
                </div>

                <button type="submit"
                    class="w-full bg-gradient-to-r from-secondary to-yellow-600 hover:to-secondary text-white font-bold py-4 rounded-xl shadow-lg shadow-orange-900/20 transition-all duration-300 transform hover:-translate-y-1">
                    Sign Up
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-gray-400 text-sm">Already have an account? <a href="<?php echo base_url('login'); ?>" class="text-secondary font-bold hover:text-white transition">Sign In</a></p>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('register-form').addEventListener('submit', (e) => {
        e.preventDefault();
        const btn = e.target.querySelector('button');
        
        btn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline play" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Creating Account...';
        btn.classList.add('opacity-75', 'cursor-not-allowed');

        setTimeout(() => {
            alert('Registration simulated! In a real app, this would create a user.');
            window.location.href = '<?php echo base_url('login'); ?>';
        }, 1500);
    });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
