-- Add slug columns to destinations and packages tables
ALTER TABLE destinations ADD COLUMN slug VARCHAR(255) UNIQUE;
ALTER TABLE packages ADD COLUMN slug VARCHAR(255) UNIQUE;

-- Create indexes for performance
CREATE INDEX idx_destinations_slug ON destinations(slug);
CREATE INDEX idx_packages_slug ON packages(slug);

-- Generate slugs for existing destinations
UPDATE destinations SET slug = 
  LOWER(
    REPLACE(
      REPLACE(
        REPLACE(
          REPLACE(name, ' ', '-'),
        ',', ''),
      '''', ''),
    '.', '')
  );

-- Generate slugs for existing packages  
UPDATE packages SET slug = 
  LOWER(
    REPLACE(
      REPLACE(
        REPLACE(
          REPLACE(
            REPLACE(name, ' ', '-'),
          ',', ''),
        '''', ''),
      '.', ''),
    '&', 'and')
  );

-- Ensure uniqueness by appending numbers if needed
-- This will be handled by application logic for new entries
