<?php
/**
 * SCMS — Student Dashboard Data
 * Fetches all data needed for student dashboard
 */

require_once __DIR__ . '/../../config/db.php';

// Get session variables
$student_class = $_SESSION['class'] ?? '';
$student_name = $_SESSION['username'] ?? '';

// ============================================================
// GET CLASS INFO
// ============================================================

$student_class_safe = escape($conn, $student_class);
$sql = "SELECT id, class_name, academic_year FROM classes WHERE class_name='$student_class_safe' LIMIT 1";
$cls_row = getRow($conn, $sql);
$class_id = $cls_row ? (int) $cls_row['id'] : null;

// ============================================================
// GET STUDENT PROFILE
// ============================================================

$student_row = null;
if ($class_id) {
    $student_name_safe = escape($conn, $student_name);
    $sql = "SELECT * FROM students WHERE class_id=$class_id AND name='$student_name_safe' LIMIT 1";
    $student_row = getRow($conn, $sql);
}

// ============================================================
// GET ANNOUNCEMENTS (Latest 10)
// ============================================================

$announcements = [];
$sql = "SELECT title, body, tag, created_at FROM announcements ORDER BY created_at DESC LIMIT 10";
$ann_rows = getRows($conn, $sql);

foreach ($ann_rows as $row) {
    $announcements[] = [
        'title' => $row['title'],
        'tag' => $row['tag'],
        'tag_label' => ucfirst($row['tag']),
        'desc' => $row['body'],
        'date' => date('F j, Y', strtotime($row['created_at']))
    ];
}

// ============================================================
// GET ATTENDANCE STATISTICS
// ============================================================

$dash_total_classes = 0;
$dash_present_classes = 0;
$dash_absent_classes = 0;
$dash_att_pct = 0;

if ($student_row) {
    $student_id = (int) $student_row['id'];

    // Get attendance count
    $sql = "SELECT 
            COUNT(*) AS total,
            SUM(CASE WHEN status='present' THEN 1 ELSE 0 END) AS present
            FROM attendance WHERE student_id=$student_id";

    $att_data = getRow($conn, $sql);

    if ($att_data) {
        $dash_total_classes = (int) $att_data['total'];
        $dash_present_classes = (int) $att_data['present'];
        $dash_absent_classes = $dash_total_classes - $dash_present_classes;

        // Calculate percentage
        if ($dash_total_classes > 0) {
            $dash_att_pct = round(($dash_present_classes / $dash_total_classes) * 100, 1);
        }
    }
}

