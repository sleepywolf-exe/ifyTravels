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

    // ID is required for single actions, but not for bulk/delete_all logic
    if (!$id && !in_array($action, ['bulk_delete', 'delete_all'])) {
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
    } elseif ($action === 'bulk_delete') {
        // Handle Multiple Delete (Comma Separated IDs)
        $ids = sanitize_input($_POST['ids'] ?? '');
        if (!empty($ids)) {
            // Convert to array and validate integers
            $idArray = array_map('intval', explode(',', $ids));
            $idArray = array_filter($idArray); // Remove zeros

            if (!empty($idArray)) {
                $placeholders = implode(',', array_fill(0, count($idArray), '?'));
                $db->execute("DELETE FROM inquiries WHERE id IN ($placeholders)", $idArray);
                $_SESSION['flash_message'] = count($idArray) . " inquiries deleted successfully.";
            }
        }
    } elseif ($action === 'delete_all') {
        // Handle Delete ALL (DANGEROUS)
        $db->execute("DELETE FROM inquiries");
        // Optional: Reset Auto Increment
        // $db->execute("ALTER TABLE inquiries AUTO_INCREMENT = 1");

        $_SESSION['flash_message'] = "ALL inquiries have been deleted successfully.";
    }

    redirect('inquiries.php');
}
?>