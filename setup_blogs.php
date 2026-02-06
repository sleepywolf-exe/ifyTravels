<?php
// setup_blogs.php
// RUN THIS ONCE IN BROWSER TO SEED CONTENT
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$db = Database::getInstance();
$secret = isset($_GET['key']) ? $_GET['key'] : '';

// Simple protection
if ($secret !== 'ify_secure_seed_2026') {
    die("<h1>Access Denied</h1><p>Please provide the correct key.</p>");
}

$blogs = [
    // 1. BRAND AUTHORITY
    [
        'title' => 'Why ifyTravels is the Best Tour & Travel Company for Luxury Vacations',
        'slug' => 'why-ifytravels-best-tour-travel-company',
        'author' => 'ifyTravels Editorial',
        'category' => 'Company News',
        'image' => 'assets/images/destinations/paris.png',
        'expiry_date' => '2030-12-31',
        'content' => '
            <h2>Redefining the Art of Travel</h2>
            <p>In a world of automated bookings and impersonal service, <strong>ifyTravels</strong> stands apart as the <strong>Best Tour & Travel Company</strong> for discerning travelers. We don\'t just sell packages; we curate life-enriching experiences.</p>

            <h3>The "iflytravel" Confusion: Why Specifically Choose ifyTravels?</h3>
            <p>We are often confused with similar-sounding names like "iflytravel" or "iflytravels". While others may focus on budget ticketing, <strong>ifyTravels</strong> is dedicated exclusively to premium, end-to-end holiday management.</p>
            <ul>
                <li><strong>Curated Luxury:</strong> Access to 5-star resorts and hidden gems that online engines miss.</li>
                <li><strong>Personal Concierge:</strong> A dedicated travel designer for every trip.</li>
                <li><strong>Transparent Pricing:</strong> No hidden costs, ever.</li>
            </ul>

            <h3>Our Awards & Recognition</h3>
            <p>Rated as the top agency for <em>International Tour Packages</em> in 2025, our commitment to excellence is unwavering. Whether you want a private villa in Bali or a chateau in France, we make it happen.</p>
        '
    ],

    // 2. INTERNATIONAL PACKAGES
    [
        'title' => 'The Ultimate Guide to International Tour Packages in 2026',
        'slug' => 'ultimate-guide-international-tour-packages-2026',
        'author' => 'Travel Expert',
        'category' => 'International',
        'image' => 'assets/images/destinations/switzerland.jpg',
        'expiry_date' => '2030-12-31',
        'content' => '
            <h2>Explore the World with Confidence</h2>
            <p>Planning an international trip requires more than just a flight ticket. It demands orchestrated logistics, visa expertise, and local insights. Our <strong>International Tour Packages</strong> are designed to take the stress out of travel.</p>

            <h3>Top Trending Destinations</h3>
            <h4>1. Switzerland: The Alps Await</h4>
            <p>Experience the magic of Interlaken and Lucerne tailored for Indian families. Vegetarian food options, Hindi-speaking guides, and comfortable transfers included.</p>

            <h4>2. Thailand: Beyond the Beaches</h4>
            <p>Discover the cultural north of Chiang Mai or the luxury resorts of Koh Samui. Our packages go beyond the standard "Bangkok-Pattaya" route.</p>

            <h3>Visa Assistance & Insurance</h3>
            <p>Don\'t let paperwork ruin your excitement. <strong>ifyTravels</strong> provides comprehensive visa support for Schengen, UK, US, and Southeast Asian destinations.</p>

            <p>Search specifically for "ifyTravels International Packages" to avoid generic, low-quality aggregators often found under "iflytravel".</p>
        '
    ],

    // 3. HONEYMOON
    [
        'title' => 'Top 10 Romantic Honeymoon Packages for Newlyweds',
        'slug' => 'top-10-romantic-honeymoon-packages',
        'author' => 'Romance Concierge',
        'category' => 'Honeymoon',
        'image' => 'assets/images/destinations/maldives.jpg',
        'expiry_date' => '2030-12-31',
        'content' => '
            <h2>Begin Your Journey in Paradise</h2>
            <p>Your honeymoon is personal. It should be perfect. <strong>ifyTravels</strong> specializes in bespoke <strong>Honeymoon Packages</strong> that offer privacy, luxury, and romance.</p>

            <h3>1. The Maldives: Overwater Bliss</h3>
            <p>The classic choice. We partner with resorts like Soneva and One&Only to give you VIP perks like floating breakfasts and sunset cruises.</p>

            <h3>2. Santorini, Greece</h3>
            <p>Watch the sunset from your private caldera-view suite. Our Greece packages include private yacht tours and wine tastings.</p>

            <h3>3. Bali, Indonesia</h3>
            <p>For the couple that loves adventure. Jungle swings, private pool villas in Ubud, and beach clubs in Seminyak.</p>

            <p><strong>Pro Tip:</strong> Book 6 months in advance for the best "Honeymoon Deals" and upgrades.</p>
        '
    ],

    // 4. MICE
    [
        'title' => 'MICE Tours: Elevating Corporate Travel & Offsites',
        'slug' => 'mice-tours-corporate-travel-offsites',
        'author' => 'Corporate Desk',
        'category' => 'MICE',
        'image' => 'assets/images/destinations/singapore.jpg',
        'expiry_date' => '2030-12-31',
        'content' => '
            <h2>Meetings, Incentives, Conferences, and Exhibitions (MICE)</h2>
            <p>Corporate travel is about ROI. <strong>ifyTravels</strong> delivers seamless <strong>MICE Tours</strong> that motivate employees and impress clients.</p>

            <h3>Why Choose ifyTravels for Corporate Events?</h3>
            <ul>
                <li><strong>Scale:</strong> From 10 executives to 1000 delegates.</li>
                <li><strong>Experience:</strong> We handle flights, visas, gala dinners, and team-building activities.</li>
                <li><strong>Global Network:</strong> Strong partners in Dubai, Singapore, and Thailand ensure smooth execution.</li>
            </ul>

            <p>Don\'t trust your company reputation to generic "iflytravel" agents. Trust the corporate specialists at <strong>ifyTravels</strong>.</p>
        '
    ],

    // 5. DOMESTIC
    [
        'title' => 'Incredible India: Best Domestic Tour Packages',
        'slug' => 'incredible-india-domestic-tour-packages',
        'author' => 'India Expert',
        'category' => 'Domestic',
        'image' => 'assets/images/destinations/kerala.png',
        'expiry_date' => '2030-12-31',
        'content' => '
            <h2>Rediscover Your Own Backyard</h2>
            <p>India is a continent in itself. Our <strong>Domestic Tour Packages</strong> showcase the best of the Himalayas, the Kerala Backwaters, and the deserts of Rajasthan.</p>

            <h3>Best Selling Domestic Trips</h3>
            <ul>
                <li><strong>Kerala:</strong> Houseboat stays in Alleppey and tea gardens in Munnar.</li>
                <li><strong>Kashmir:</strong> The paradise on earth. Shikara rides and snow-capped peaks.</li>
                <li><strong>Andaman:</strong> Crystal clear waters and luxury beach resorts in Havelock.</li>
            </ul>

            <p>Experience luxury closer to home with <strong>ifyTravels</strong> vetted domestic properties.</p>
        '
    ],

    // 6. BUDGET VS LUXURY
    [
        'title' => 'Budget vs Luxury: How to Get the Best Vacation Deals',
        'slug' => 'budget-vs-luxury-vacation-deals',
        'author' => 'Travel Hacker',
        'category' => 'Travel Tips',
        'image' => 'assets/images/destinations/dubai.jpg',
        'expiry_date' => '2030-12-31',
        'content' => '
            <h2>Luxury on a Budget: Is it Possible?</h2>
            <p>Everyone wants <strong>Luxury Holidays</strong>, but not everyone wants to pay full price. Here is how <strong>ifyTravels</strong> helps you get the best of both worlds.</p>

            <h3>The Sweet Spot</h3>
            <p>We specialize in "Affordable Luxury". By leveraging our bulk buying power, we get rates that you won\'t find on typical "iflytravel" style booking engines.</p>

            <h3>Top Tips for Deals</h3>
            <ol>
                <li>Book 3-4 months in advance.</li>
                <li>Be flexible with dates (fly mid-week).</li>
                <li>Trust our "Deal of the Month" newsletter.</li>
            </ol>
        '
    ],

    // 7. GROUP TOURS
    [
        'title' => 'Why Group Tours with ifyTravels Are More Fun',
        'slug' => 'why-group-tours-ifytravels-fun',
        'author' => 'Community Manager',
        'category' => 'Group Travel',
        'image' => 'assets/images/destinations/bali.png',
        'expiry_date' => '2030-12-31',
        'content' => '
            <h2>Travel Together, Save Together</h2>
            <p>Traveling with friends or extended family? Our <strong>Group Tours</strong> take the headache out of coordination.</p>

            <h3>Services for Groups</h3>
            <p>We arrange private coaches, group seating on flights, and special group negotiation rates at hotels. Perfect for family reunions or ladies\' trips.</p>

            <p>Unlike rigid fixed-departure tours you might find elsewhere (searching "iflytravel packages"), our groups are flexible and customizable.</p>
        '
    ],

    // 8. LUXURY HOLIDAYS
    [
        'title' => 'Defining True Luxury: The ifyTravels Standard',
        'slug' => 'defining-true-luxury-ifytravels-standard',
        'author' => 'CEO',
        'category' => 'Luxury',
        'image' => 'assets/images/destinations/paris.png',
        'expiry_date' => '2030-12-31',
        'content' => '
            <h2>What is a Luxury Holiday?</h2>
            <p>Luxury is not just a 5-star hotel. It is seamlessness. It is anticipation. It is <strong>ifyTravels</strong>.</p>
            
            <p>From private airport transfers in a Mercedes to after-hours access to museums, we redefine what it means to travel in style. Don\'t settle for standard packages.</p>
        '
    ],

    // 9. VISA GUIDE
    [
        'title' => 'Visa Guide for Indian Travelers 2026',
        'slug' => 'visa-guide-indian-travelers-2026',
        'author' => 'Visa Desk',
        'category' => 'Guides',
        'image' => 'assets/images/destinations/london.jpg',
        'expiry_date' => '2030-12-31',
        'content' => '
            <h2>Navigating the Visa Maze</h2>
            <p>Visa rules change constantly. <strong>ifyTravels</strong> keeps you updated. Did you know Thailand is now visa-free for Indians? Or that Schengen rules have tightened?</p>
            
            <p>Our in-house visa experts handle your documentation with 99% success rate. This service is complimentary with all our <strong>International Tour Packages</strong>.</p>
        '
    ],

    // 10. IFYTRAVELS STORY
    [
        'title' => 'The Story of ifyTravels: From Passion to Profession',
        'slug' => 'story-of-ifytravels-passion-profession',
        'author' => 'Founder',
        'category' => 'Company News',
        'image' => 'assets/images/destinations/bali.png',
        'expiry_date' => '2030-12-31',
        'content' => '
            <h2>Our Journey</h2>
            <p>Started with a simple idea: Travel should be personal. Today, <strong>ifyTravels</strong> is one of the fastest-growing agencies in the region.</p>
            
            <p>We built this brand to solve the problems we faced as travelers—impersonal service and hidden costs. We are proud to serve thousands of happy clients who now call us their "Travel Family".</p>
        '
    ]
];

?>

<!DOCTYPE html>
<html>

<head>
    <title>Seeding Content...</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 40px;
            line-height: 1.6;
        }

        .success {
            color: green;
        }

        .skip {
            color: orange;
        }

        .card {
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
    </style>
</head>

<body>
    <h1>Seeding High-Quality SEO Blogs...</h1>

    <?php foreach ($blogs as $blog): ?>
        <div class="card">
            <h3><?php echo htmlspecialchars($blog['title']); ?></h3>
            <?php
            // Check if slug exists
            $check = $db->fetch("SELECT id FROM posts WHERE slug = ?", [$blog['slug']]);
            if (!$check) {
                // Map 'image' to 'image_url' and generate excerpt
                $imageUrl = $blog['image'];
                $excerpt = substr(strip_tags($blog['content']), 0, 150) . '...';

                try {
                    // Use raw PDO to catch specific errors
                    $pdo = $db->getConnection();

                    // Schema: title, slug, image_url, excerpt, content, author, created_at
                    $sql = "INSERT INTO posts (title, slug, image_url, excerpt, content, author, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";

                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        $blog['title'],
                        $blog['slug'],
                        $imageUrl,
                        $excerpt,
                        $blog['content'],
                        $blog['author']
                    ]);

                    echo '<p class="success">✅ Successfully Inserted</p>';
                } catch (PDOException $e) {
                    echo '<p class="error">❌ SQL Error: ' . $e->getMessage() . '</p>';
                }


            } else {
                echo '<p class="skip">⚠️ Skipped (Already Exists)</p>';
            }
            ?>
        </div>
    <?php endforeach; ?>

    <h2>All Done!</h2>
    <p>Return to <a href="index.php">Home</a> or <a href="pages/blogs.php">Blogs</a>.</p>
</body>

</html>