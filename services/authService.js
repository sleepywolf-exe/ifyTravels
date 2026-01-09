/**
 * Authentication Service (Placeholder).
 * Manages user login, registration, and session.
 */

const AuthService = {
    user: null,

    login: async (email, password) => {
        console.log(`Logging in user: ${email}`);
        // TODO: Call Backend API
        return new Promise((resolve) => {
            setTimeout(() => {
                if (email && password) {
                    AuthService.user = { id: 1, name: 'John Doe', email };
                    console.log('Login successful');
                    localStorage.setItem('user', JSON.stringify(AuthService.user));
                    resolve({ success: true, user: AuthService.user });
                } else {
                    resolve({ success: false, message: 'Invalid credentials' });
                }
            }, 800);
        });
    },

    register: async (name, email, password) => {
        console.log(`Registering user: ${name}, ${email}`);
        // TODO: Call Backend API
        return new Promise((resolve) => {
            setTimeout(() => {
                resolve({ success: true });
            }, 800);
        });
    },

    logout: () => {
        console.log("Logging out...");
        AuthService.user = null;
        localStorage.removeItem('user');
        window.location.href = '/index.html';
    },

    getCurrentUser: () => {
        if (!AuthService.user) {
            const stored = localStorage.getItem('user');
            if (stored) AuthService.user = JSON.parse(stored);
        }
        return AuthService.user;
    },

    requireAuth: () => {
        if (!AuthService.getCurrentUser()) {
            window.location.href = '/pages/login.html';
            return false;
        }
        return true;
    }
};

window.AuthService = AuthService;
