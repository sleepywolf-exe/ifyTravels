<?php
// admin/upload_test.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Upload Diagnostic Tool</h1>";

// 1. Check PHP Config
echo "<h2>1. PHP Configuration</h2>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "post_max_size: " . ini_get('post_max_size') . "<br>";
echo "file_uploads: " . ini_get('file_uploads') . "<br>";

// 2. Check Directories and Permissions
echo "<h2>2. Directory Permissions</h2>";
$uploadDir = __DIR__ . '/../assets/images/uploads/';
echo "Target Directory: " . $uploadDir . "<br>";

if (is_dir($uploadDir)) {
    echo "Directory exists: YES<br>";
    if (is_writable($uploadDir)) {
        echo "Directory is writable: YES<br>";

        // Try writing a test file
        $testFile = $uploadDir . 'deploy_test.txt';
        if (file_put_contents($testFile, 'Write Test Successful')) {
            echo "Test write successful: YES<br>";
            unlink($testFile); // Cleanup
        } else {
            echo "Test write successful: <span style='color:red'>NO</span><br>";
        }
    } else {
        echo "Directory is writable: <span style='color:red'>NO</span> (Check chmod permissions)<br>";
    }
} else {
    echo "Directory exists: <span style='color:red'>NO</span><br>";
    // Attempt to create
    if (mkdir($uploadDir, 0755, true)) {
        echo "Created directory: YES<br>";
    } else {
        echo "Created directory: <span style='color:red'>NO</span> (Check parent permissions)<br>";
    }
}

// 3. Simple Upload Form
echo "<h2>3. Test Upload</h2>";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['test_file'])) {
        echo "<pre>";
        print_r($_FILES['test_file']);
        echo "</pre>";

        if ($_FILES['test_file']['error'] === UPLOAD_ERR_OK) {
            $dest = $uploadDir . 'test_upload_' . time() . '_' . basename($_FILES['test_file']['name']);
            if (move_uploaded_file($_FILES['test_file']['tmp_name'], $dest)) {
                echo "<h3 style='color:green'>Upload Successful!</h3>";
                echo "Saved to: $dest";
            } else {
                echo "<h3 style='color:red'>Move Failed!</h3>";
                echo "Ensure web server has write access.";
            }
        } else {
            echo "<h3 style='color:red'>Upload Error Code: " . $_FILES['test_file']['error'] . "</h3>";
        }
    }
}
?>

<form method="POST" enctype="multipart/form-data" style="background:#f0f0f0; padding:20px; border:1px solid #ccc;">
    <input type="file" name="test_file">
    <button type="submit">Test Upload</button>
</form>