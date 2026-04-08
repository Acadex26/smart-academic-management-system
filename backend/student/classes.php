<?php
/**
 * SCMS — Student Classes Data
 * Fetches classmates, subjects, and timetable
 */

require_once __DIR__ . '/../../config/db.php';

// ============================================================
// GET CLASSMATES
// ============================================================

$students = [];
if ($class_id) {
    $sql = "SELECT reg_number AS regno, name, dob, gender 
            FROM students WHERE class_id=$class_id ORDER BY reg_number ASC";
    $students = getRows($conn, $sql);
}

// ============================================================
// GET SUBJECTS
// ============================================================

$subjects_list = [];
if ($class_id) {
    $sql = "SELECT subject_name FROM subjects WHERE class_id=$class_id ORDER BY subject_name ASC";
    $subjects = getRows($conn, $sql);

    foreach ($subjects as $sub) {
        $subjects_list[] = $sub['subject_name'];
    }
}

// ============================================================
// GET TIMETABLE
// ============================================================

$tt_days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
$timetable = [];

if ($class_id) {
    $sql = "SELECT day, period_label, subject_name FROM timetable
            WHERE class_id=$class_id
            ORDER BY FIELD(day,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'), period_label ASC";

    $tt_rows = getRows($conn, $sql);

    // Group by day and period
    foreach ($tt_rows as $row) {
        $day = $row['day'];
        $period = $row['period_label'];
        $subject = $row['subject_name'];

        if (!isset($timetable[$day])) {
            $timetable[$day] = [];
        }

        $timetable[$day][$period] = $subject;
    }
}

// ============================================================
// SCHEDULE PERIODS
// ============================================================

$tt_schedule = [
    ['type' => 'period', 'label' => '9:30 - 10:30', 'note' => 'Period 1'],
    ['type' => 'period', 'label' => '10:30 - 11:30', 'note' => 'Period 2'],
    ['type' => 'break', 'label' => '11:30 - 11:45', 'note' => '☕ Break'],
    ['type' => 'period', 'label' => '11:45 - 12:45', 'note' => 'Period 3'],
    ['type' => 'period', 'label' => '12:45 - 1:45', 'note' => 'Period 4'],
    ['type' => 'break', 'label' => '1:45 - 2:00', 'note' => '🍽 Lunch'],
    ['type' => 'period', 'label' => '2:00 - 3:00', 'note' => 'Period 5'],
    ['type' => 'period', 'label' => '3:00 - 4:00', 'note' => 'Period 6'],
];

// Get today's day name
$today_name = date('l');

// Check if timetable exists
$has_timetable = !empty($timetable);

