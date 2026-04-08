<?php
/**
 * SCMS — Delete Single Attendance Record
 */

require_once __DIR__ . '/../../config/db.php';

// Check if admin
if (!isAdmin()) {
    redirect('../../login.php');
}

// Check if POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../../frontend/admin/index.php');
}

$action = $_POST['action'] ?? '';
$att_id = (int) ($_POST['att_id'] ?? 0);

// Delete attendance record
if ($action === 'delete_single' && $att_id > 0) {
    $sql = "DELETE FROM attendance WHERE id=$att_id";
    runQuery($conn, $sql);
}

// Redirect back
redirect('../../frontend/admin/index.php?panel=att-edit&deleted=1');

