CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS destinations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    image VARCHAR(255),
    rating DECIMAL(2,1) DEFAULT 5.0,
    is_featured BOOLEAN DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS packages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    destination_id INTEGER,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    image VARCHAR(255),
    price DECIMAL(10,2) NOT NULL,
    duration VARCHAR(50),
    isPopular BOOLEAN DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS testimonials (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100),
    message TEXT,
    rating INTEGER,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS bookings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    package_id INTEGER,
    customer_name VARCHAR(100),
    customer_email VARCHAR(100),
    customer_phone VARCHAR(20),
    travel_date DATE,
    travelers INTEGER,
    total_price DECIMAL(10,2),
    status VARCHAR(20) DEFAULT 'pending',
    affiliate_id INTEGER DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS affiliates (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL,
    status VARCHAR(20) DEFAULT 'active',
    password_hash VARCHAR(255) DEFAULT NULL,
    commission_rate DECIMAL(5,2) DEFAULT 10.00,
    last_login DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS referral_clicks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    affiliate_id INTEGER NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    referrer_url TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Seed Data: Admin User (password: admin123)
-- Hash generated via password_hash('admin123', PASSWORD_BCRYPT)
INSERT OR IGNORE INTO users (username, password_hash, email) VALUES
('admin', '$2y$10$X.s1u.jFkk1z.Xz.Xz.Xz.Xz.Xz.Xz.Xz.Xz.Xz.Xz.Xz.Xz', 'admin@ifytravels.com');

-- Seed Data: Destinations
INSERT OR IGNORE INTO destinations (name, slug, image, rating, is_featured) VALUES
('Kashmir', 'kashmir', 'assets/images/kashmir.jpg', 4.9, 1),
('Paris', 'paris', 'assets/images/paris.jpg', 4.8, 1),
('Bali', 'bali', 'assets/images/bali.jpg', 4.7, 1),
('Dubai', 'dubai', 'assets/images/dubai.jpg', 4.6, 1);

-- Seed Data: Packages
INSERT OR IGNORE INTO packages (title, slug, price, duration, isPopular, image) VALUES
('Paradise on Earth - Kashmir Tour', 'kashmir-paradise', 25000, '5 Days / 4 Nights', 1, 'assets/images/kashmir-pkg.jpg'),
('Experience The Beauty Of Paris', 'paris-beauty', 99000, '7 Days / 6 Nights', 1, 'assets/images/paris-pkg.jpg'),
('Explore The Beauty Singapore', 'singapore-explore', 85000, '5 Days / 4 Nights', 1, 'assets/images/singapore.jpg');

-- Seed Data: Testimonials
INSERT OR IGNORE INTO testimonials (name, message, rating) VALUES
('Sarah Johnson', 'Absolutely amazing experience! The booking process was smooth and the trip was unforgettable.', 5),
('Rajesh Kumar', 'Great service and support. They customized the package exactly as we wanted.', 5),
('Mike & Caroline', 'Best honeymoon ever! Thanks to IfyTravels for making it so special.', 5);
