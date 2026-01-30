<?php
$pageTitle = "Sign In";
include __DIR__ . '/../includes/header.php';
?>

<div class="min-h-screen bg-charcoal flex items-center justify-center py-20 relative overflow-hidden">
    
    <!-- Background -->
    <div class="absolute inset-0">
        <img src="<?php echo base_url('assets/images/destinations/maldives.jpg'); ?>" class="w-full h-full object-cover opacity-20" alt="Login Background"> 
        <div class="absolute inset-0 bg-gradient-to-t from-charcoal via-charcoal/90 to-charcoal/80"></div>
    </div>

    <!-- Login Card -->
    <div class="w-full max-w-md p-6 relative z-10 animate-fade-in-up">
        
        <div class="text-center mb-8">
            <h1 class="text-3xl font-heading font-bold text-white mb-2">Welcome Back</h1>
            <p class="text-gray-400">Sign in to access your bespoke itinerary.</p>
        </div>

        <div class="glass-form !p-10 !bg-white/5 border border-white/10 rounded-3xl shadow-2xl relative">
            <!-- Glow Effect -->
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-secondary/10 rounded-full blur-2xl pointer-events-none"></div>

            <form id="login-form" class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-300 mb-2 ml-1">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <input type="email" id="email" required placeholder="name@example.com"
                            class="glass-input w-full !pl-12 !bg-white/5 !border-white/10 text-white focus:!border-secondary transition">
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-bold text-gray-300 ml-1">Password</label>
                        <a href="#" class="text-xs text-secondary hover:text-white transition">Forgot Password?</a>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input type="password" id="password" required placeholder="••••••••"
                            class="glass-input w-full !pl-12 !bg-white/5 !border-white/10 text-white focus:!border-secondary transition">
                    </div>
                </div>

                <div id="error-msg" class="hidden bg-red-500/10 text-red-200 text-sm p-3 rounded-lg border border-red-500/20 text-center">
                    Invalid credentials.
                </div>

                <button type="submit"
                    class="w-full bg-gradient-to-r from-secondary to-yellow-600 hover:to-secondary text-white font-bold py-4 rounded-xl shadow-lg shadow-orange-900/20 transition-all duration-300 transform hover:-translate-y-1">
                    Sign In
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-gray-400 text-sm">Don't have an account? <a href="<?php echo base_url('register'); ?>" class="text-secondary font-bold hover:text-white transition">Sign Up</a></p>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('login-form').addEventListener('submit', (e) => {
        e.preventDefault();
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const errorMsg = document.getElementById('error-msg');
        const btn = e.target.querySelector('button');

        // Reset
        errorMsg.classList.add('hidden');
        btn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline play" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Signing In...';
        btn.classList.add('opacity-75', 'cursor-not-allowed');

        // Simple Simulation Logic
        setTimeout(() => {
            if (email === 'admin@ifytravels.com' && password === 'admin') {
                localStorage.setItem('isAuthenticated', 'true');
                localStorage.setItem('userRole', 'admin');
                window.location.href = '../admin/dashboard.html';
            } else {
                errorMsg.textContent = 'Invalid credentials. Try admin@ifytravels.com / admin';
                errorMsg.classList.remove('hidden');
                btn.innerHTML = 'Sign In';
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
            }
        }, 1500);
    });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>