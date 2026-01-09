require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$db = Database::getInstance();

echo "Updating Schema...\n";

try {
// Add activities column if not exists
$columns = $db->fetchAll("PRAGMA table_info(packages)"); // For SQLite check, but we are on MySQL likely?
// Actually config said MySQL default.
// Let's just try ADD COLUMN and catch error if exists

// For MySQL
try {
$db->execute("ALTER TABLE packages ADD COLUMN activities TEXT DEFAULT NULL");
echo "Added 'activities' column.\n";
} catch (Exception $e) {
echo "'activities' column might already exist: " . $e->getMessage() . "\n";
}

try {
$db->execute("ALTER TABLE packages ADD COLUMN themes TEXT DEFAULT NULL");
echo "Added 'themes' column.\n";
} catch (Exception $e) {
echo "'themes' column might already exist: " . $e->getMessage() . "\n";
}

} catch (Exception $e) {
echo "Fatal Error: " . $e->getMessage();
}
?>