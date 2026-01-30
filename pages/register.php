<?php
$pageTitle = "Sign Up";
include __DIR__ . '/../includes/header.php';
?>

<div class="min-h-screen bg-slate-50 flex items-center justify-center py-20 relative overflow-hidden">

    <!-- Background -->
    <div class="absolute inset-0">
        <img src="<?php echo base_url('assets/images/destinations/thailand.jpg'); ?>"
            class="w-full h-full object-cover opacity-10 blur-sm" alt="Register Background">
        <div class="absolute inset-0 bg-gradient-to-t from-slate-50 via-slate-50/90 to-slate-50/80"></div>
    </div>

    <!-- Register Card -->
    <div class="w-full max-w-lg p-6 relative z-10 animate-fade-in-up">

        <div class="text-center mb-8">
            <h1 class="text-3xl font-heading font-bold text-slate-900 mb-2">Create Account</h1>
            <p class="text-slate-500">Join our exclusive community of travelers.</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-3xl shadow-2xl relative p-10">

            <form id="register-form" class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Full Name</label>
                    <input type="text" required placeholder="John Doe"
                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition placeholder-slate-400">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Email Address</label>
                    <input type="email" required placeholder="name@example.com"
                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition placeholder-slate-400">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Password</label>
                    <input type="password" required placeholder="••••••••"
                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition placeholder-slate-400">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Confirm Password</label>
                    <input type="password" required placeholder="••••••••"
                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition placeholder-slate-400">
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" required class="accent-primary h-4 w-4 rounded border-gray-300">
                    <label class="text-sm text-slate-500">I agree to the <a href="#"
                            class="text-primary hover:underline">Terms & Conditions</a></label>
                </div>

                <button type="submit"
                    class="w-full bg-gradient-to-r from-primary to-secondary hover:to-primary text-white font-bold py-4 rounded-xl shadow-lg shadow-primary/20 transition-all duration-300 transform hover:-translate-y-1 magnetic-btn">
                    Sign Up
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-slate-500 text-sm">Already have an account? <a href="<?php echo base_url('login'); ?>"
                        class="text-primary font-bold hover:underline transition">Sign In</a></p>
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