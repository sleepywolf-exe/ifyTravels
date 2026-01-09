<?php
$pageTitle = "Login";
// NOTE: Login logic is purely frontend simulation in prototype.
// Ideally we would start session here.
include __DIR__ . '/../includes/header.php';
?>

<div class="flex items-center justify-center min-h-[calc(100vh-200px)] py-12">

    <div class="max-w-md w-full m-4 bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="bg-primary/5 p-8 text-center pb-0">
            <h2 class="text-3xl font-bold text-charcoal mb-2">Welcome Back</h2>
            <p class="text-gray-500 mb-6">Sign in to manage your journeys</p>
        </div>

        <div class="p-8 pt-6">
            <form id="login-form" class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Email Address</label>
                    <input type="email" id="email" required placeholder="admin@ifytravels.com"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition">
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-bold text-gray-700">Password</label>
                        <a href="#" class="text-xs text-primary font-bold hover:underline">Forgot?</a>
                    </div>
                    <input type="password" id="password" required placeholder="••••••••"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition">
                </div>

                <div id="error-msg" class="hidden bg-red-50 text-red-600 text-sm p-3 rounded-lg border border-red-100">
                </div>

                <button type="submit"
                    class="w-full bg-primary hover:bg-teal-700 text-white font-bold py-4 rounded-xl shadow-lg transition transform hover:-translate-y-0.5">
                    Sign In
                </button>
            </form>

            <div class="mt-8 text-center text-sm text-gray-500">
                Don't have an account? <a href="#" class="text-primary font-bold hover:underline">Create Account</a>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-100 text-center">
                <a href="../index.php"
                    class="text-gray-400 hover:text-charcoal text-sm flex items-center justify-center gap-2">
                    <span>←</span> Back to Website
                </a>
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

        // Simple Simulation Logic
        if (email === 'admin@ifytravels.com' && password === 'admin') {
            // Success
            localStorage.setItem('isAuthenticated', 'true');
            localStorage.setItem('userRole', 'admin');

            // Button loading state
            const btn = e.target.querySelector('button');
            btn.innerHTML = 'Logging in...';
            btn.classList.add('opacity-75', 'cursor-not-allowed');

            setTimeout(() => {
                window.location.href = '../admin/dashboard.html'; // Admin is likely still static for now
            }, 1000);
        } else {
            errorMsg.textContent = 'Invalid credentials. Try admin@ifytravels.com / admin';
            errorMsg.classList.remove('hidden');
        }
    });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>