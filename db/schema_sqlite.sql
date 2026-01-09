-- SQLite Schema

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role VARCHAR(10) DEFAULT 'user', -- 'user' or 'admin'
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Site Settings (Key-Value Store for CMS)
CREATE TABLE IF NOT EXISTS site_settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    setting_key VARCHAR(50) UNIQUE NOT NULL,
    setting_value TEXT,
    description VARCHAR(255),
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Destinations Table
CREATE TABLE IF NOT EXISTS destinations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL, -- For SEO friendly URLs
    country VARCHAR(100) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    rating DECIMAL(2,1) DEFAULT 4.5,
    type VARCHAR(50) DEFAULT 'International', -- International, Domestic
    best_time_to_visit VARCHAR(100),
    is_featured BOOLEAN DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Packages Table
CREATE TABLE IF NOT EXISTS packages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    destination_id INTEGER,
    title VARCHAR(150) NOT NULL,
    slug VARCHAR(150) UNIQUE NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    duration VARCHAR(50) NOT NULL, -- e.g. "5 Days / 4 Nights"
    image_url VARCHAR(255),
    description TEXT,
    inclusions TEXT, -- JSON Array
    exclusions TEXT, -- JSON Array
    features TEXT, -- JSON Array (Highlights)
    is_popular BOOLEAN DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(destination_id) REFERENCES destinations(id) ON DELETE CASCADE
);

-- Bookings Table
CREATE TABLE IF NOT EXISTS bookings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER, -- Optional if guest booking
    package_id INTEGER, -- Optional if custom inquiry
    package_name VARCHAR(150), -- Snapshot in case package changes
    customer_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(20),
    travel_date DATE,
    special_requests TEXT,
    total_price DECIMAL(10, 2),
    status VARCHAR(20) DEFAULT 'Pending', -- Pending, Confirmed, Cancelled, Completed
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY(package_id) REFERENCES packages(id) ON DELETE SET NULL
);

-- Inquiries / Contact Messages
CREATE TABLE IF NOT EXISTS inquiries (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    subject VARCHAR(100),
    message TEXT,
    status VARCHAR(20) DEFAULT 'New', -- New, Read, Replied
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Initial Data: Site Settings
INSERT OR IGNORE INTO site_settings (setting_key, setting_value, description) VALUES 
('site_name', 'ifyTravels', 'Website Name'),
('hero_title', 'Discover the World''s Hidden Gems', 'Hero Section Title'),
('hero_subtitle', 'Curated luxury travel experiences designed just for you. From Bali''s beaches to Paris''s lights.', 'Hero Section Subtitle'),
('contact_email', 'hello@ifytravels.com', 'Contact Email Address'),
('contact_phone', '+91 987 654 3210', 'Contact Phone Number'),
('address', '123 Travel Lane, Connaught Place, New Delhi', 'Office Address'),
('social_facebook', '#', 'Facebook URL'),
('social_twitter', '#', 'Twitter/X URL'),
('social_instagram', '#', 'Instagram URL');

-- Initial Data: Admin User (password: admin)
-- Using PHP password_hash('admin', PASSWORD_DEFAULT)
INSERT OR IGNORE INTO users (name, email, password_hash, role) VALUES 
('Administrator', 'admin@ifytravels.com', '$2y$10$8.u.u.u.u.u.u.u.u.u.u.u.u.u.u.u.u.u.u.u.u.u.u.u.u.u', 'admin');
