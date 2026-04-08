<?php
/**
 * SCMS — Student Attendance Data
 * Fetches subject-wise attendance for student
 */

require_once __DIR__ . '/../../config/db.php';

$attendance = [];
$total_conducted = 0;
$total_attended = 0;
$overall_pct = 0;

// Get attendance only if student is found
if ($student_row) {
    $student_id = (int) $student_row['id'];

    // Fetch attendance by subject
    $sql = "SELECT sub.subject_name AS subject,
                   SUM(CASE WHEN a.status='present' THEN 1 ELSE 0 END) AS attended,
                   COUNT(*) AS conducted
            FROM attendance a
            JOIN subjects sub ON sub.id = a.subject_id
            WHERE a.student_id = $student_id
            GROUP BY a.subject_id
            ORDER BY sub.subject_name ASC";

    $att_rows = getRows($conn, $sql);

    // Format attendance data
    foreach ($att_rows as $row) {
        $attendance[] = [
            'subject' => $row['subject'],
            'attended' => (int) $row['attended'],
            'conducted' => (int) $row['conducted']
        ];
    }

    // Calculate totals
    $total_conducted = array_sum(array_column($attendance, 'conducted'));
    $total_attended = array_sum(array_column($attendance, 'attended'));

    // Calculate percentage
    if ($total_conducted > 0) {
        $overall_pct = round(($total_attended / $total_conducted) * 100);
    }
}

