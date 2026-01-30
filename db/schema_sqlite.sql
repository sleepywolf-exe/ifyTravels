CREATE TABLE IF NOT EXISTS testimonials (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    location TEXT,
    rating INTEGER DEFAULT 5,
    message TEXT NOT NULL,
    status TEXT DEFAULT 'approved',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    password_hash TEXT NOT NULL,
    role TEXT DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS bookings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    package_id INTEGER,
    status TEXT DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS destinations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    slug TEXT UNIQUE,
    image_url TEXT,
    description TEXT,
    is_featured BOOLEAN DEFAULT 0,
    is_new BOOLEAN DEFAULT 0,
    rating REAL DEFAULT 5.0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS packages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    destination_id INTEGER,
    title TEXT NOT NULL,
    slug TEXT UNIQUE,
    price REAL,
    duration TEXT,
    image_url TEXT,
    description TEXT,
    is_popular BOOLEAN DEFAULT 0,
    is_new BOOLEAN DEFAULT 0,
    features TEXT, -- JSON
    inclusions TEXT, -- JSON
    exclusions TEXT, -- JSON
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Seed Data
INSERT OR IGNORE INTO testimonials (name, location, rating, message) VALUES 
('Paras', 'Delhi, India', 5, 'This is a real review from the local database! The system is now working perfectly.');

INSERT OR IGNORE INTO users (name, email, password_hash, role) VALUES 
('Admin', 'admin@ifytravels.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'); 
-- Password: password
