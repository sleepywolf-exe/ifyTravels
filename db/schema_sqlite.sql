-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    password_hash TEXT NOT NULL,
    role TEXT DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Packages Table
CREATE TABLE IF NOT EXISTS packages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    slug TEXT UNIQUE NOT NULL,
    description TEXT,
    price REAL NOT NULL,
    duration TEXT,
    image_url TEXT,
    is_popular INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Destinations Table
CREATE TABLE IF NOT EXISTS destinations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    slug TEXT UNIQUE NOT NULL,
    image_url TEXT,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Bookings Table
CREATE TABLE IF NOT EXISTS bookings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    package_id INTEGER NOT NULL,
    status TEXT DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id),
    FOREIGN KEY(package_id) REFERENCES packages(id)
);

-- Affiliates Table
CREATE TABLE IF NOT EXISTS affiliates (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    code TEXT UNIQUE NOT NULL,
    password_hash TEXT NOT NULL,
    status TEXT DEFAULT 'active',
    last_login DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- SEED DATA --

-- Sample Users (Pass: password)
INSERT OR IGNORE INTO users (name, email, password_hash, role) VALUES 
('Demo User', 'demo@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- Sample Destinations
INSERT OR IGNORE INTO destinations (name, slug, image_url) VALUES 
('Maldives', 'maldives', 'assets/images/destinations/maldives.jpg'),
('Dubai', 'dubai', 'assets/images/destinations/dubai.jpg'),
('Swiss Alps', 'swiss-alps', 'assets/images/destinations/swiss.jpg'),
('Paris', 'paris', 'assets/images/destinations/paris.jpg');

-- Sample Packages
INSERT OR IGNORE INTO packages (title, slug, description, price, duration, image_url, is_popular) VALUES 
('Magical Maldives', 'magical-maldives', 'Experience the turquoise waters and luxury water villas.', 120000, '5 Days / 4 Nights', 'assets/images/destinations/maldives.jpg', 1),
('Dubai Desert Safari', 'dubai-safari', 'Thrilling desert safari with BBQ dinner and city tour.', 45000, '4 Days / 3 Nights', 'assets/images/destinations/dubai.jpg', 1),
('Romantic Paris', 'romantic-paris', 'Eiffel tower dinner and Seine river cruise.', 150000, '6 Days / 5 Nights', 'assets/images/destinations/paris.jpg', 1),
('Swiss Wonderland', 'swiss-wonderland', 'Explore the snowy peaks and scenic trains.', 200000, '7 Days / 6 Nights', 'assets/images/destinations/swiss.jpg', 1);

-- Sample Affiliate (Pass: password)
INSERT OR IGNORE INTO affiliates (name, email, code, password_hash) VALUES 
('Best Travel Agent', 'partner@agency.com', 'AGENCY01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
