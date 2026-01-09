-- Seed Data for International Destinations and Packages
-- Updated destination list focusing on international travel

-- Clear existing data
DELETE FROM packages;
DELETE FROM destinations;

-- International Destinations Data (15 destinations)
INSERT INTO destinations (id, name, slug, country, description, image_url, rating, type, best_time_to_visit, is_featured) VALUES
(1, 'Azerbaijan', 'azerbaijan', 'Azerbaijan', 'Land of Fire offers ancient culture, modern architecture in Baku, Caspian Sea coastline, and stunning mountain landscapes.', 'assets/images/destinations/azerbaijan.jpg', 4.6, 'International', 'April to June, September to November', 1),
(2, 'Georgia', 'georgia', 'Georgia', 'Discover the crossroads of Europe and Asia with wine culture, mountain villages, Black Sea beaches, and warm hospitality.', 'assets/images/destinations/georgia.jpg', 4.7, 'International', 'May to October', 1),
(3, 'Uzbekistan', 'uzbekistan', 'Uzbekistan', 'Journey through the Silk Road with stunning mosques, madrasas, ancient cities like Samarkand, and rich Persian heritage.', 'assets/images/destinations/uzbekistan.jpg', 4.5, 'International', 'April to June, September to November', 1),
(4, 'Kazakhstan', 'kazakhstan', 'Kazakhstan', 'Experience vast steppes, futuristic Astana, Almaty mountains, and the unique blend of nomadic and Soviet heritage.', 'assets/images/destinations/kazakhstan.jpg', 4.4, 'International', 'May to September', 0),
(5, 'Indonesia', 'indonesia', 'Indonesia', 'Explore thousands of islands featuring Bali beaches, Komodo dragons, Java temples, volcanic landscapes, and diverse cultures.', 'assets/images/destinations/indonesia.jpg', 4.8, 'International', 'April to October', 1),
(6, 'Sri Lanka', 'sri-lanka', 'Sri Lanka', 'The Pearl of the Indian Ocean offers ancient temples, tea plantations, wildlife safaris, pristine beaches, and rich Buddhist culture.', 'assets/images/destinations/sri-lanka.jpg', 4.7, 'International', 'December to March, July to August', 1),
(7, 'Dubai', 'dubai', 'UAE', 'A futuristic metropolis with record-breaking architecture, luxury shopping, desert safaris, and world-class dining experiences.', 'assets/images/destinations/dubai.jpg', 4.9, 'International', 'November to March', 1),
(8, 'Vietnam', 'vietnam', 'Vietnam', 'Discover Ha Long Bay cruises, bustling Hanoi, historic Hoi An, vibrant Ho Chi Minh City, and delicious street food culture.', 'assets/images/destinations/vietnam.jpg', 4.6, 'International', 'February to April, August to October', 1),
(9, 'Singapore', 'singapore', 'Singapore', 'The Lion City blends futuristic gardens, diverse cuisines, efficient infrastructure, Marina Bay, and multicultural neighborhoods.', 'assets/images/destinations/singapore.jpg', 4.8, 'International', 'February to April', 1),
(10, 'Malaysia', 'malaysia', 'Malaysia', 'Experience Kuala Lumpur towers, Penang food culture, Langkawi beaches, Borneo rainforests, and Islamic-Chinese fusion.', 'assets/images/destinations/malaysia.jpg', 4.7, 'International', 'December to February', 0),
(11, 'Thailand', 'thailand', 'Thailand', 'Land of Smiles offers Bangkok temples, Phuket beaches, Chiang Mai mountains, floating markets, and legendary Thai hospitality.', 'assets/images/destinations/thailand.jpg', 4.8, 'International', 'November to February', 1),
(12, 'Russia', 'russia', 'Russia', 'Explore Moscow Red Square, St Petersburg palaces, Trans-Siberian Railway, rich history, and magnificent Orthodox architecture.', 'assets/images/destinations/russia.jpg', 4.5, 'International', 'May to September', 0),
(13, 'Turkey', 'turkey', 'Turkey', 'Bridge between continents with Istanbul bazaars, Cappadocia balloons, Mediterranean coast, ancient ruins, and Turkish cuisine.', 'assets/images/destinations/turkey.jpg', 4.7, 'International', 'April to May, September to November', 1),
(14, 'Maldives', 'maldives', 'Maldives', 'Ultimate luxury paradise with overwater villas, crystal-clear lagoons, world-class diving, private islands, and romantic sunsets.', 'assets/images/destinations/maldives.jpg', 4.9, 'International', 'November to April', 1),
(15, 'Seychelles', 'seychelles', 'Seychelles', 'Pristine island paradise featuring granite boulders, white-sand beaches, rare wildlife, luxury resorts, and turquoise waters.', 'assets/images/destinations/seychelles.jpg', 4.8, 'International', 'April to May, October to November', 1);

-- Premium Packages for International Destinations
INSERT INTO packages (id, destination_id, title, slug, price, duration, image_url, description, inclusions, exclusions, features, is_popular) VALUES
(1, 1, 'Baku Explorer Package', 'baku-explorer', 72000, '5 Days / 4 Nights', 'assets/images/packages/azerbaijan.jpg', 'Discover Baku modern skyline, Flame Towers, Old City, Caspian Sea boulevard, and traditional Azerbaijani cuisine experiences.',
'["Airport transfers", "4-star hotel in Baku", "Daily breakfast", "City tour with guide", "Flame Towers visit", "Old City walking tour"]',
'["Flight tickets", "Visa fees", "Lunch and dinner", "Personal expenses"]',
'["Heydar Aliyev Center", "Gobustan rock art", "Mud volcanoes tour", "Carpet Museum visit"]', 1),

(2, 2, 'Georgian Wine & Mountains', 'georgian-wine-mountains', 68000, '6 Days / 5 Nights', 'assets/images/packages/georgia.jpg', 'Experience Tbilisi charm, Kakheti wine region, Kazbegi mountains, ancient monasteries, and authentic Georgian feast traditions.',
'["Airport pickup", "Hotel accommodation", "Daily breakfast", "Wine tasting tour", "Kazbegi excursion", "Cooking class"]',
'["Flight tickets", "Visa fees", "Some meals", "Travel insurance"]',
'["Svetitskhoveli Cathedral", "Ananuri Fortress", "Gergeti Trinity Church", "Sulfur baths experience"]', 1),

(3, 3, 'Silk Road Discovery', 'silk-road-uzbekistan', 85000, '7 Days / 6 Nights', 'assets/images/packages/uzbekistan.jpg', 'Journey through Samarkand Registan, Bukhara Ark, Khiva walls, ancient mosques, madrasas, and vibrant oriental bazaars.',
'["All transfers", "Hotel stays", "Daily breakfast", "English-speaking guide", "All entrance fees", "High-speed train tickets"]',
'["International flights", "Lunch and dinner", "Tips", "Personal shopping"]',
'["Registan Square sunset", "Shah-i-Zinda necropolis", "Bukhara Lyab-i-Hauz", "Khiva Ichan Kala fortress"]', 1),

(4, 4, 'Kazakhstan Highlights', 'kazakhstan-highlights', 78000, '6 Days / 5 Nights', 'assets/images/packages/kazakhstan.jpg', 'Explore futuristic Astana architecture, Almaty mountain views, Big Almaty Lake, Charyn Canyon, and nomadic culture experiences.',
'["Airport transfers", "4-star hotels", "Daily breakfast", "City tours", "Mountain excursion", "Cable car rides"]',
'["Flights", "Visa", "Meals not mentioned", "Optional activities"]',
'["Baiterek Tower", "Medeu skating rink", "Charyn Canyon trek", "Kazakh cuisine tasting"]', 0),

(5, 5, 'Indonesia Island Hopping', 'indonesia-islands', 95000, '8 Days / 7 Nights', 'assets/images/packages/indonesia.jpg', 'Discover Bali temples, Nusa Penida beaches, Ubud rice terraces, volcano sunrise trek, traditional dance, and island paradise.',
'["Domestic flights included", "Hotel & villa stays", "Daily breakfast", "Boat transfers", "Temple guides", "Volcano trek"]',
'["International flights", "Lunch & dinner", "Visa on arrival", "Travel insurance"]',
'["Nusa Penida snorkeling", "Mount Batur sunrise", "Tegallalang rice terraces", "Tanah Lot temple"]', 1),

(6, 6, 'Sri Lanka Cultural Triangle', 'sri-lanka-cultural', 62000, '6 Days / 5 Nights', 'assets/images/packages/sri-lanka.jpg', 'Visit Sigiriya Rock, Kandy Temple, Dambulla Caves, tea plantations, elephant orphanage, and colonial Galle Fort.',
'["Airport transfers", "Hotel accommodation", "Daily breakfast", "English guide", "All site tickets", "Train journey"]',
'["Flight tickets", "Visa fees", "Meals not specified", "Tips"]',
'["Sigiriya Lion Rock climb", "Temple of Tooth Relic", "Tea factory tour", "Safari at Minneriya"]', 1),

(7, 7, 'Dubai Luxury Escape', 'dubai-luxury', 115000, '5 Days / 4 Nights', 'assets/images/packages/dubai.jpg', 'Experience Burj Khalifa heights, Dubai Mall shopping, desert safari luxury, yacht cruise, Palm Jumeirah, and 7-star dining.',
'["Airport limousine", "5-star hotel", "Daily breakfast", "Burj Khalifa top", "Desert safari VIP", "Yacht cruise"]',
'["Flight tickets", "Visa fees", "Personal shopping", "Extra activities"]',
'["At the Top SKY", "Gold Souk visit", "Dubai Frame", "La Mer beach club"]', 1),

(8, 8, 'Vietnam Heritage Tour', 'vietnam-heritage', 71000, '7 Days / 6 Nights', 'assets/images/packages/vietnam.jpg', 'Cruise Ha Long Bay, explore Hanoi Old Quarter, Hoi An lanterns, Cu Chi Tunnels, Mekong Delta, and Vietnamese cuisine.',
'["Domestic flights", "Hotel stays", "Daily breakfast", "Ha Long Bay cruise", "City tours", "Cooking class"]',
'["International flights", "Lunch & dinner", "Visa fees", "Personal expenses"]',
'["Overnight junk boat", "Cyclo ride Hanoi", "Hoi An tailors", "Water puppets show"]', 1),

(9, 9, 'Singapore Stopover Special', 'singapore-stopover', 58000, '4 Days / 3 Nights', 'assets/images/packages/singapore.jpg', 'Discover Gardens by the Bay, Marina Bay Sands, Sentosa Island, hawker food culture, and efficient city exploration.',
'["Airport transfers", "4-star hotel", "Daily breakfast", "Gardens entry", "Universal Studios ticket", "Night Safari"]',
'["Flight tickets", "Meals", "Shopping", "Additional attractions"]',
'["Supertree Grove", "Marina Bay light show", "Sentosa beaches", "Hawker center tour"]', 1),

(10, 10, 'Malaysia Twin Destinations', 'malaysia-twin', 64000, '6 Days / 5 Nights', 'assets/images/packages/malaysia.jpg', 'Experience Kuala Lumpur Petronas Towers, Batu Caves, Georgetown Penang street art, hawker food, and tropical beaches.',
'["Domestic flight KL-Penang", "Hotel accommodation", "Daily breakfast", "City tours", "Cable car", "Food tour"]',
'["International flights", "Lunch & dinner", "Tips", "Personal shopping"]',
'["Petronas Sky Bridge", "Penang street art walk", "Langkawi cable car", "Night market food"]', 0),

(11, 11, 'Thailand Grand Tour', 'thailand-grand', 79000, '7 Days / 6 Nights', 'assets/images/packages/thailand.jpg', 'Bangkok temples, Ayutthaya ruins, Chiang Mai elephant sanctuary, floating markets, Phi Phi Islands, and Thai massage.',
'["Domestic flights", "Hotel stays", "Daily breakfast", "Temple guides", "Boat tours", "Elephant experience"]',
'["International flights", "Most meals", "Visa fees", "Personal expenses"]',
'["Grand Palace", "Phi Phi Maya Bay", "Elephant sanctuary ethical", "Muay Thai show"]', 1),

(12, 12, 'Russia Imperial Cities', 'russia-imperial', 98000, '7 Days / 6 Nights', 'assets/images/packages/russia.jpg', 'Moscow Red Square, Kremlin palaces, St Petersburg Hermitage, Catherine Palace, metro art, ballet, and Russian culture.',
'["High-speed train Moscow-SPB", "4-star hotels", "Daily breakfast", "Museum tickets", "English guides", "Metro tour"]',
'["Flights", "Visa invitation", "Lunch & dinner", "Optional ballet tickets"]',
'["Hermitage Museum", "Peterhof fountains", "Moscow metro stations", "Bolshoi Theatre area"]', 0),

(13, 13, 'Turkey Grand Circuit', 'turkey-grand', 88000, '8 Days / 7 Nights', 'assets/images/packages/turkey.jpg', 'Istanbul mosques, Cappadocia hot air balloons, Pamukkale travertines, Ephesus ruins, Turkish baths, and Bosphorus cruise.',
'["Domestic flights", "Hotel accommodation", "Daily breakfast", "All guided tours", "Hot air balloon", "Turkish bath"]',
'["International flights", "Lunch & dinner", "Tips", "Shopping"]',
'["Blue Mosque", "Balloon ride Cappadocia", "Pamukkale thermal pools", "Ephesus library"]', 1),

(14, 14, 'Maldives Honeymoon Bliss', 'maldives-honeymoon', 185000, '5 Days / 4 Nights', 'assets/images/packages/maldives.jpg', 'Ultimate romance in overwater villa, private infinity pool, underwater dining, couple spa, sunset cruise, and champagne breakfasts.',
'["Seaplane transfers", "Water villa luxury", "All-inclusive meals", "Spa treatments", "Water sports", "Champagne"]',
'["International flights", "Premium alcohol", "Personal expenses", "Tips"]',
'["Private deck ocean view", "Underwater restaurant", "Dolphin cruise", "Sandbank picnic"]', 1),

(15, 15, 'Seychelles Island Paradise', 'seychelles-paradise', 165000, '6 Days / 5 Nights', 'assets/images/packages/seychelles.jpg', 'Pristine beaches, Anse Source Argent, Mahe, Praslin, La Digue cycling, giant tortoises, Coco de Mer, and luxury relaxation.',
'["Inter-island transfers", "Beach resort stay", "Daily breakfast", "Island hopping", "Snorkeling gear", "Nature reserve"]',
'["Flights", "Lunch & dinner", "Diving certification", "Personal expenses"]',
'["Anse Lazio beach", "Vallee de Mai UNESCO", "Giant tortoise sanctuary", "Granite rock formations"]', 1);
