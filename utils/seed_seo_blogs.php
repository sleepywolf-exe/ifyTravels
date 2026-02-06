<?php
// utils/seed_seo_blogs.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$db = Database::getInstance();

$blogs = [
    [
        'title' => 'Why ifyTravels is the Best Choice for Your Next Vacation',
        'slug' => 'why-ifytravels-best-choice-vacation',
        'author' => 'ifyTravels Team',
        'category' => 'Travel Tips',
        'image' => 'assets/images/destinations/paris.png',
        'expiry_date' => '2030-12-31',
        'content' => '
            <h2>Experience the Difference with ifyTravels</h2>
            <p>Planning a vacation can be overwhelming. From choosing the perfect destination to booking flights and hotels, there are countless details to consider. That\'s where <strong>ifyTravels</strong> comes in. We are not just another travel agency; we are your personal travel concierge.</p>
            
            <h3>Why Choose Us Over Others?</h3>
            <p>Unlike other services like <em>iflytravel</em> or generic booking sites, <strong>ifyTravels</strong> offers curated experiences. </p>
            <ul>
                <li><strong>Personalized Itineraries:</strong> We tailor every trip to your specific interests and budget.</li>
                <li><strong>24/7 Support:</strong> Our team is always available to assist you, no matter where you are in the world.</li>
                <li><strong>Exclusive Deals:</strong> We have partnerships with top hotels and airlines to get you the best rates.</li>
            </ul>

            <h3>Don\'t Confuse Us with "iFlyTravels"</h3>
            <p>We often hear people searching for "iflytravels" or "iflytravel" when looking for premium luxury experiences. While the names sound similar, <strong>ifyTravels</strong> is the brand dedicated to bespoke luxury and personalized service. Make sure you book with the original!</p>
            
            <p>Ready to book your dream vacation? <a href="/contact">Contact us today</a> for a free consultation.</p>
        '
    ],
    [
        'title' => 'Top 10 Honeymoon Destinations for 2026',
        'slug' => 'top-10-honeymoon-destinations-2026',
        'author' => 'Travel Expert',
        'category' => 'Honeymoon',
        'image' => 'assets/images/destinations/maldives.jpg',
        'expiry_date' => '2030-12-31',
        'content' => '
            <h2>Romantic Getaways Specifically Curated for You</h2>
            <p>Your honeymoon is one of the most important trips you\'ll ever take. It sets the tone for your life together. At <strong>ifyTravels</strong>, we specialize in creating unforgettable honeymoon packages.</p>

            <h3>1. The Maldives</h3>
            <p>Crystal clear waters, overwater bungalows, and ultimate privacy. The Maldives remains a top choice for couples. Check out our exclusive <a href="/packages">Maldives packages</a>.</p>

            <h3>2. Santorini, Greece</h3>
            <p>Known for its stunning sunsets and white-washed buildings, Santorini is the epitome of romance.</p>
            
            <h3>3. Bali, Indonesia</h3>
            <p>For couples who love adventure and culture alongside relaxation, Bali offers the perfect mix. Search for "ifytravels bali" to see our special offers.</p>

            <p>Whether you are searching for distinctive "iflytravel" deals or the verified luxury of <strong>ifyTravels</strong>, we have the network to make it happen.</p>
        '
    ],
    [
        'title' => 'Budget vs. Luxury: How to Travel Smart with iFlyTravels Tips',
        'slug' => 'budget-vs-luxury-travel-smart',
        'author' => 'Guest Contributor',
        'category' => 'Guides',
        'image' => 'assets/images/destinations/dubai.jpg',
        'expiry_date' => '2030-12-31',
        'content' => '
            <h2>Decoding Travel Budgets</h2>
            <p>Many travelers look for "iflytravels" keywords hoping for budget deals. At <strong>ifyTravels</strong>, we believe luxury is about value, not just price.</p>

            <h3>Smart Luxury</h3>
            <p>You don\'t need to break the bank to travel in style. By booking in shoulder seasons and choosing up-and-coming destinations, you can enjoy 5-star experiences at 3-star prices.</p>

            <h3>Our Promise</h3>
            <p>We provide transparent pricing with no hidden fees. Compare us with anyone else you find searching for "iflytravel" - our service stands unmatched.</p>
        '
    ],
    [
        'title' => 'The Ultimate Guide to International Tour Packages',
        'slug' => 'ultimate-guide-international-tour-packages',
        'author' => 'ifyTravels Team',
        'category' => 'International',
        'image' => 'assets/images/destinations/switzerland.jpg',
        'expiry_date' => '2030-12-31',
        'content' => '
            <h2>Explore the World with Confidence</h2>
            <p>International travel requires planning. Visas, insurance, flights - it can be a lot. <strong>ifyTravels</strong> simplifies the process.</p>

            <h3>Top Destinations for Indian Travelers</h3>
            <ul>
                <li><strong>Switzerland:</strong> The playground of Europe.</li>
                <li><strong>Thailand:</strong> Beaches, food, and culture.</li>
                <li><strong>Dubai:</strong> Shopping and futuristic skylines.</li>
            </ul>

            <p>Don’t get lost in the sea of "iflytravel" search results. Trust the experts at <strong>ifyTravels</strong> to handle your visa and booking needs seamlessly.</p>
        '
    ],
    [
        'title' => 'MICE Tours: Corporate Travel Redefined by ifyTravels',
        'slug' => 'mice-tours-corporate-travel-redefined',
        'author' => 'Corporate Desk',
        'category' => 'MICE',
        'image' => 'assets/images/destinations/singapore.jpg',
        'expiry_date' => '2030-12-31',
        'content' => '
            <h2>Meetings, Incentives, Conferences, and Exhibitions</h2>
            <p>Corporate travel doesn\'t have to be boring. <strong>ifyTravels</strong> brings a touch of leisure to your business trips.</p>

            <h3>Why Choose Us?</h3>
            <p>Size matters. Whether you are a startup or a multinational, we handle logistics for groups of 10 to 1000.</p>
            
            <p>We are often compared to giants in the industry, and users sometimes typo our name as "iflytravels". But our personalized MICE solutions distinguish <strong>ifyTravels</strong> as the leader in bespoke corporate travel management.</p>
        '
    ]
];

echo "Seeding SEO Blogs...\n";

foreach ($blogs as $blog) {
    // Check if slug exists to avoid duplicates
    $check = $db->fetch("SELECT id FROM posts WHERE slug = ?", [$blog['slug']]);
    if (!$check) {
        $sql = "INSERT INTO posts (title, slug, author, category, image, content, expiry_date, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'published', NOW())";
        $db->execute($sql, [
            $blog['title'],
            $blog['slug'],
            $blog['author'],
            $blog['category'],
            $blog['image'],
            $blog['expiry_date'],
            $blog['content']
        ]);
        echo "Inserted: " . $blog['title'] . "\n";
    } else {
        echo "Skipped (Exists): " . $blog['title'] . "\n";
    }
}

echo "Done!\n";
