<?php
/**
 * SCMS — Admin Announcements Handler
 * Handle add, edit, delete announcements
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

// Get action from form
$action = $_POST['action'] ?? 'add';
if (isset($_POST['delete_ann'])) {
    $action = 'delete';
}

$ann_id = (int) ($_POST['ann_id'] ?? 0);
$title = escape($conn, $_POST['ann_title'] ?? '');
$body = escape($conn, $_POST['ann_body'] ?? '');
$tag = escape($conn, $_POST['ann_tag'] ?? 'info');
$ann_date = escape($conn, $_POST['ann_date'] ?? '');

// ============================================================
// DELETE ANNOUNCEMENT
// ============================================================
if ($action === 'delete') {
    if ($ann_id == 0) {
        redirect('../../frontend/admin/index.php?error=invalid_id');
    }

    $sql = "DELETE FROM announcements WHERE id=$ann_id";
    runQuery($conn, $sql);
    redirect('../../frontend/admin/index.php?panel=ann-view&deleted=1');
}

// ============================================================
// VALIDATE FIELDS
// ============================================================
if (isEmpty($title) || isEmpty($body)) {
    redirect('../../frontend/admin/index.php?error=Title and body are required');
}

// ============================================================
// ADD ANNOUNCEMENT
// ============================================================
if ($action === 'add') {
    // Set created_at
    if (isEmpty($ann_date)) {
        $created_at = date('Y-m-d H:i:s');
    } else {
        $created_at = $ann_date . ' 00:00:00';
    }

    $sql = "INSERT INTO announcements (title, body, tag, created_at)
            VALUES ('$title', '$body', '$tag', '$created_at')";

    runQuery($conn, $sql);
    redirect('../../frontend/admin/index.php?panel=ann-view&saved=1');
}

// ============================================================
// EDIT ANNOUNCEMENT
// ============================================================
if ($action === 'edit') {
    if ($ann_id == 0) {
        redirect('../../frontend/admin/index.php?error=invalid_id');
    }

    // Set created_at
    if (isEmpty($ann_date)) {
        $created_at = date('Y-m-d H:i:s');
    } else {
        $created_at = $ann_date . ' 00:00:00';
    }

    $sql = "UPDATE announcements 
            SET title='$title', body='$body', tag='$tag', created_at='$created_at'
            WHERE id=$ann_id";

    runQuery($conn, $sql);
    redirect('../../frontend/admin/index.php?panel=ann-view&updated=1');
}

// Default redirect
redirect('../../frontend/admin/index.php');

