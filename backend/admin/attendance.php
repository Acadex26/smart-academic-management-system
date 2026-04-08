<?php
/**
 * SCMS — Admin Attendance Handler
 * Handle add, edit, delete attendance records
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
if (isset($_POST['delete_all'])) {
    $action = 'delete_all';
}

$att_id = (int) ($_POST['att_id'] ?? 0);
$class_id = (int) ($_POST['class_id'] ?? 0);
$subject_id = (int) ($_POST['subject_id'] ?? 0);
$att_date = escape($conn, $_POST['att_date'] ?? '');
$marked_by = $_SESSION['username'] ?? 'Admin';

// ============================================================
// DELETE SINGLE ATTENDANCE RECORD
// ============================================================
if ($action === 'delete') {
    if ($att_id > 0) {
        $sql = "DELETE FROM attendance WHERE id=$att_id";
        runQuery($conn, $sql);
    }
    redirect('../../frontend/admin/index.php?panel=att-edit&deleted=1');
}

// ============================================================
// VALIDATE REQUIRED FIELDS
// ============================================================
if ($class_id == 0 || $subject_id == 0 || isEmpty($att_date)) {
    redirect('../../frontend/admin/index.php?error=Missing required fields');
}

// ============================================================
// DELETE ALL ATTENDANCE FOR DATE+CLASS+SUBJECT
// ============================================================
if ($action === 'delete_all') {
    $sql = "DELETE FROM attendance WHERE class_id=$class_id AND subject_id=$subject_id AND date='$att_date'";
    runQuery($conn, $sql);
    redirect('../../frontend/admin/index.php?panel=att-view&deleted=1');
}

// ============================================================
// ADD OR EDIT ATTENDANCE
// ============================================================
if ($action === 'add' || $action === 'edit') {
    $att_list = $_POST['attendance'] ?? [];

    foreach ($att_list as $entry) {
        $student_id = (int) ($entry['student_id'] ?? 0);
        $att_id_current = (int) ($entry['att_id'] ?? 0);
        $status = (($entry['status'] ?? 'absent') === 'present') ? 'present' : 'absent';

        // Skip if no student ID
        if ($student_id == 0)
            continue;

        if ($action === 'add') {
            // Check if attendance already exists
            $sql = "SELECT id FROM attendance WHERE student_id=$student_id AND subject_id=$subject_id AND date='$att_date'";
            $existing = getRow($conn, $sql);

            if ($existing) {
                // Update existing
                $sql = "UPDATE attendance SET status='$status', marked_by='$marked_by' WHERE id=" . $existing['id'];
            } else {
                // Insert new
                $sql = "INSERT INTO attendance (student_id, subject_id, class_id, date, status, marked_by)
                        VALUES ($student_id, $subject_id, $class_id, '$att_date', '$status', '$marked_by')";
            }
            runQuery($conn, $sql);
        } elseif ($action === 'edit' && $att_id_current > 0) {
            // Update specific record
            $sql = "UPDATE attendance SET status='$status' WHERE id=$att_id_current";
            runQuery($conn, $sql);
        }
    }

    $redirect_action = ($action === 'add') ? 'saved' : 'updated';
    redirect('../../frontend/admin/index.php?panel=att-view&' . $redirect_action . '=1');
}

// Default redirect
redirect('../../frontend/admin/index.php');

