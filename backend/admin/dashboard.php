<?php
/**
 * SCMS — Admin Dashboard Data
 * Fetches all data needed for admin dashboard
 */

// ============================================================
// STATISTICS
// ============================================================

// Count total users
$total_users = 0;
$data = getRow($conn, "SELECT COUNT(*) AS count FROM users");
if ($data)
    $total_users = (int) $data['count'];

// Count total students
$total_students = 0;
$data = getRow($conn, "SELECT COUNT(*) AS count FROM students");
if ($data)
    $total_students = (int) $data['count'];

// Count total classes
$total_classes = 0;
$data = getRow($conn, "SELECT COUNT(*) AS count FROM classes");
if ($data)
    $total_classes = (int) $data['count'];

// Count male students
$total_boys = 0;
$data = getRow($conn, "SELECT COUNT(*) AS count FROM students WHERE gender='Male'");
if ($data)
    $total_boys = (int) $data['count'];

// Count female students
$total_girls = 0;
$data = getRow($conn, "SELECT COUNT(*) AS count FROM students WHERE gender='Female'");
if ($data)
    $total_girls = (int) $data['count'];

// ============================================================
// CLASSES DATA
// ============================================================

// Fetch classes with student count and subjects
$sql = "SELECT c.id, c.class_name, c.academic_year,
               COUNT(DISTINCT s.id) AS total_students,
               GROUP_CONCAT(DISTINCT sub.subject_name ORDER BY sub.subject_name SEPARATOR ', ') AS subjects
        FROM classes c
        LEFT JOIN students s ON s.class_id = c.id
        LEFT JOIN subjects sub ON sub.class_id = c.id
        GROUP BY c.id
        ORDER BY c.class_name ASC";
$classes_list = getRows($conn, $sql);

// Fetch all classes for dropdowns
$all_classes = getRows($conn, "SELECT id, class_name, academic_year FROM classes ORDER BY class_name ASC");

// ============================================================
// STUDENTS BY CLASS (for JavaScript)
// ============================================================

$sql = "SELECT s.id, s.reg_number, s.name, s.gender, s.dob, s.contact, 
               s.blood_group, s.guardian_name, s.address, s.is_registered,
               c.class_name
        FROM students s
        JOIN classes c ON c.id = s.class_id
        ORDER BY c.class_name, s.name ASC";

$all_students = getRows($conn, $sql);
$students_by_class = [];

// Group students by class name
foreach ($all_students as $student) {
    $class_name = $student['class_name'];
    if (!isset($students_by_class[$class_name])) {
        $students_by_class[$class_name] = [];
    }
    $students_by_class[$class_name][] = $student;
}

// ============================================================
// ANNOUNCEMENTS
// ============================================================

// Latest 5 announcements for dashboard
$anns_list = getRows(
    $conn,
    "SELECT title, body, tag, created_at 
     FROM announcements 
     ORDER BY created_at DESC 
     LIMIT 5"
);

// All announcements for edit panel
$all_anns_list = getRows(
    $conn,
    "SELECT id, title, tag 
     FROM announcements 
     ORDER BY created_at DESC"
);

// ============================================================
// TIMETABLE (for JavaScript)
// ============================================================

$sql = "SELECT class_id, day, period_label, subject_name 
        FROM timetable 
        ORDER BY class_id, day, period_label";

$all_timetable = getRows($conn, $sql);
$timetable_by_class = [];

// Group timetable by class, day, period
foreach ($all_timetable as $slot) {
    $class_id = $slot['class_id'];
    $day = $slot['day'];
    $period = $slot['period_label'];
    $subject = $slot['subject_name'];

    if (!isset($timetable_by_class[$class_id])) {
        $timetable_by_class[$class_id] = [];
    }
    if (!isset($timetable_by_class[$class_id][$day])) {
        $timetable_by_class[$class_id][$day] = [];
    }

    $timetable_by_class[$class_id][$day][$period] = $subject;
}

// ============================================================
// MARKS SUMMARY
// ============================================================

$marks_summary = getRows(
    $conn,
    "SELECT c.id, c.class_name,
            COUNT(DISTINCT s.id) AS total_students,
            COALESCE(ROUND(SUM(m.marks_obtained) / NULLIF(SUM(m.max_marks), 0) * 100, 1), NULL) AS avg_pct
     FROM classes c
     LEFT JOIN students s ON s.class_id = c.id
     LEFT JOIN marks m ON m.student_id = s.id
     GROUP BY c.id
     ORDER BY c.class_name ASC"
);

// ============================================================
// ACTIVE PANEL DETERMINATION
// ============================================================

// Valid panel names
$valid_panels = [
    'dashboard',
    'class-view',
    'class-add',
    'class-edit',
    'marks-view',
    'marks-add',
    'marks-edit',
    'att-view',
    'att-add',
    'att-edit',
    'ann-view',
    'ann-add',
    'ann-edit',
    'student-view',
    'student-add',
    'student-edit'
];

// Get panel from URL
$active_panel = $_GET['panel'] ?? 'dashboard';

// Validate panel name
if (!in_array($active_panel, $valid_panels)) {
    $active_panel = 'dashboard';
}

$open_panel_js = $active_panel;

