-- Create Testimonials Table
CREATE TABLE IF NOT EXISTS testimonials (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    location TEXT,
    rating INTEGER DEFAULT 5,
    message TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Insert Sample Data (The current hardcoded ones)
INSERT INTO testimonials (name, location, rating, message) VALUES 
('Sarah Johnson', 'Bali, Indonesia', 5, 'Absolutely incredible experience in Bali! The villa was stunning and the service was impeccable. Will definitely book again!'),
('Rajesh Kumar', 'Paris, France', 5, 'Paris was a dream come true! Everything was perfectly organized. Thank you for making our anniversary so special!'),
('Maria Garcia', 'Spain', 5, 'Best travel agency ever! The 24/7 support was amazing and helped us throughout our journey. Highly recommended!');
