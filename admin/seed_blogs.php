<?php
require_once __DIR__ . '/../includes/db.php';

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    $blogs = [
        [
            'title' => 'Top 10 Hidden Gems in Kashmir You Must Visit in 2026',
            'slug' => 'hidden-gems-in-kashmir-2026',
            'image_url' => 'https://images.unsplash.com/photo-1598091383021-15ddea10925d?auto=format&fit=crop&q=80&w=1200',
            'excerpt' => 'Discover the untouched beauty of Kashmir beyond Dal Lake and Gulmarg. Explore Gurez Valley, Warwan, and more in our ultimate travel guide.',
            'content' => '<p>Kashmir, often called "Paradise on Earth," is famous for its iconic destinations like Srinagar, Gulmarg, and Pahalgam. However, the true magic of this Himalayan region lies in its hidden valleys and untouched landscapes. If you are planning a trip in 2026, here are the top hidden gems you simply cannot miss.</p>

            <h3>1. Gurez Valley</h3>
            <p>Located near the Line of Control, Gurez is a pristine valley that offers breathtaking views of the Habba Khatoon peak. The Kishenganga River flows through it, making it a perfect spot for camping and fishing. It remains snowbound for six months, so summer is the best time to visit.</p>

            <h3>2. Warwan Valley</h3>
            <p>For the hardcore trekkers, Warwan Valley is the ultimate challenge. Accessible only through a tough trek or a long drive via Anantnag, this valley is completely cut off from the modern world. Imagine lush green meadows, waterfalls, and silence that speaks to the soul.</p>

            <h3>3. Daksum</h3>
            <p>A picnic spot that looks like a page out of a fairytale. Daksum is surrounded by dense forests and is the starting point for the trek to Sinthan Top. It is perfect for those who want to escape the tourist crowds.</p>

            <h3>4. Bangus Valley</h3>
            <p>Slowly gaining popularity, Bangus requires prior permission to visit but is worth every bit of effort. The vast meadows filled with wildflowers are a sight to behold.</p>

            <p><strong>Travel Tip:</strong> Always carry warm clothes, even in summer, as the weather in these high-altitude areas can be unpredictable. Book your offbeat Kashmir package with <em>ifyTravels</em> today for a seamless experience!</p>',
            'author' => 'Aarav Sharma'
        ],
        [
            'title' => 'A First Timer’s Guide to Backpacking in Himachal Pradesh',
            'slug' => 'backpacking-guide-himachal-pradesh',
            'image_url' => 'https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?auto=format&fit=crop&q=80&w=1200',
            'excerpt' => 'Planning a solo trip to the mountains? Here is everything you need to know about backpacking in Himachal – from budget hostels to bus routes.',
            'content' => '<p>Himachal Pradesh is the backpacking capital of India. With its vibrant hostel culture, affordable cafes, and stunning landscapes, it beckons travelers from across the globe. If this is your first time backpacking, this guide is for you.</p>

            <h3>Best Time to Visit</h3>
            <p>March to June is perfect for pleasant weather. However, if you want to see snow, head there between December and February. For Spiti Valley, plan strictly between June and September.</p>

            <h3>Top Routes for Backpackers</h3>
            <ul>
                <li><strong>Parvati Valley Loop:</strong> Kasol -> Chalal -> Tosh -> Kheerganga. This route is famous for its cafes and scenic treks.</li>
                <li><strong>Spiti Circuit:</strong> Shimla -> Kalpa -> Kaza -> Chandratal -> Manali. Ideally requires 10-12 days.</li>
                <li><strong>Dharamshala & McLeodGanj:</strong> Great for culture lovers and digital nomads. Visit the Dalai Lama Temple and trek to Triund.</li>
            </ul>

            <h3>Budget Tips</h3>
            <p>Stay in hostels like Zostel or The Hosteller to meet fellow travelers. Eat at local dhabas instead of fancy cafes save big. Use HRTC buses—they are reliable, cheap, and go everywhere.</p>

            <p>Pack your bags and get ready for an adventure of a lifetime. The mountains are calling!</p>',
            'author' => 'Priya Verma'
        ],
        [
            'title' => 'Why Kerala Should Be Your Next Honeymoon Destination',
            'slug' => 'kerala-honeymoon-destination-2026',
            'image_url' => 'https://images.unsplash.com/photo-1602216056096-3b40cc0c9944?auto=format&fit=crop&q=80&w=1200',
            'excerpt' => 'From romantic houseboat stays in Alleppey to misty mornings in Munnar, find out why Kerala is God’s Own Country for couples.',
            'content' => '<p>Kerala offers a unique blend of experiences that few other destinations can match. It is not just about the places; it is about the feeling. For honeymooners, Kerala provides the perfect mix of privacy, luxury, and nature.</p>

            <h3>Romantic Experiences</h3>
            <p><strong>Houseboat Cruise in Alleppey:</strong> Drift through the calm backwaters while sipping fresh coconut water. A private houseboat stay is a non-negotiable part of a Kerala honeymoon.</p>

            <p><strong>Tea Gardens of Munnar:</strong> Walk hand-in-hand through endless rolling hills of green tea plantations. The mist covering the mountains in the morning creates a magical atmosphere.</p>

            <p><strong>Beach Sunsets in Varkala:</strong> Unlike the crowded beaches of Goa, Varkala offers cliffs overlooking the Arabian Sea. It is quiet, serene, and incredibly romantic.</p>

            <h3>Ayurvedic Rejuvenation</h3>
            <p>Couples can indulge in authentic Ayurvedic couple massages to relax after the wedding stress. Kerala is the home of Ayurveda, and the treatments here are world-class.</p>

            <p>Experience love in God\'s Own Country. Check out our exclusive Kerala Honeymoon Packages at <em>ifyTravels</em>.</p>',
            'author' => 'Rohan Mehta'
        ]
    ];

    echo "<h2>Seeding Blogs...</h2>";
    echo "<ul>";

    foreach ($blogs as $blog) {
        // Check if slug exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE slug = ?");
        $stmt->execute([$blog['slug']]);

        if ($stmt->fetchColumn() == 0) {
            $insert = $pdo->prepare("INSERT INTO posts (title, slug, image_url, excerpt, content, author) VALUES (?, ?, ?, ?, ?, ?)");
            $insert->execute([
                $blog['title'],
                $blog['slug'],
                $blog['image_url'],
                $blog['excerpt'],
                $blog['content'],
                $blog['author']
            ]);
            echo "<li style='color: green;'>Created: " . htmlspecialchars($blog['title']) . "</li>";
        } else {
            echo "<li style='color: orange;'>Skipped (Exists): " . htmlspecialchars($blog['title']) . "</li>";
        }
    }
    echo "</ul>";
    echo "<p>Done! <a href='../pages/blogs.php'>View Blogs</a></p>";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>