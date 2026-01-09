<?php
require 'auth_check.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Check
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $_SESSION['flash_message'] = "Security Token Expired. Please try again.";
        redirect('inquiries.php');
    }

    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? 0;

    if (!$id) {
        redirect('inquiries.php');
    }

    $db = Database::getInstance();

    if ($action === 'delete') {
        $db->execute("DELETE FROM inquiries WHERE id = ?", [$id]);
        $_SESSION['flash_message'] = "Inquiry deleted successfully.";
    } elseif ($action === 'update') {
        $status = sanitize_input($_POST['status']);
        $notes = sanitize_input($_POST['admin_notes']);

        $db->execute(
            "UPDATE inquiries SET status = ?, admin_notes = ? WHERE id = ?",
            [$status, $notes, $id]
        );
        $_SESSION['flash_message'] = "Inquiry updated successfully.";
    }

    redirect('inquiries.php');
}
?>