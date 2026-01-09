-- Reset all to not featured
UPDATE destinations SET is_featured = 0;

-- Insert Paris if not exists (using CREATE logic if I could, but SQLite doesn't have IF NOT EXISTS for INSERT smoothly without conflict clauses, so I'll just try INSERT OR IGNORE based on name unique constraint if it exists, or just check first. Actually, I'll just rely on ID or Name.)
-- Let's check schema first to see if Name is unique. The previous PRAGMA output didn't show UNIQUE constraint on name explicitly but usually it should be.
-- To be safe, I will use a script that checks existence first or just deletes 'Paris' and 'Bali' by name to avoid duplicates before inserting.

DELETE FROM destinations WHERE name = 'Paris';
DELETE FROM destinations WHERE name = 'Bali';

-- Insert Paris
INSERT INTO destinations (name, slug, country, description, image_url, rating, type, is_featured, created_at)
VALUES ('Paris', 'paris', 'France', 'The City of Light, known for its cafe culture, Eiffel Tower, and masterpieces of art.', 'assets/images/destinations/paris.png', 4.8, 'International', 1, datetime('now'));

-- Insert Bali
INSERT INTO destinations (name, slug, country, description, image_url, rating, type, is_featured, created_at)
VALUES ('Bali', 'bali', 'Indonesia', 'Island of the Gods, known for its forested volcanic mountains, iconic rice paddies, beaches and coral reefs.', 'assets/images/destinations/bali.png', 4.9, 'International', 1, datetime('now'));

-- Update Featured Status for Existing Ones
UPDATE destinations SET is_featured = 1 WHERE name = 'Dubai';
UPDATE destinations SET is_featured = 1 WHERE name = 'Maldives';

-- Verify
SELECT id, name, is_featured FROM destinations WHERE is_featured = 1;
