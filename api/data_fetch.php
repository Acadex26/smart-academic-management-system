<?php
// SCMS - API Data Fetch
// Returns JSON data for admin panel AJAX requests

header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

// Check if admin is logged in
if (!isLoggedIn()) {
    echo json_encode(array());
    exit;
}

// Get the fetch type from URL parameter
$fetch_type = isset($_GET['fetch']) ? $_GET['fetch'] : '';

// Route to the correct function based on fetch type
if ($fetch_type == 'attendance_records') {
    fetchAttendanceRecords();
} elseif ($fetch_type == 'marks') {
    fetchMarks();
} elseif ($fetch_type == 'marks_detail') {
    fetchMarksDetail();
} elseif ($fetch_type == 'student_attendance') {
    fetchStudentAttendance();
} elseif ($fetch_type == 'students_by_class') {
    fetchStudentsByClass();
} elseif ($fetch_type == 'subjects_by_class') {
    fetchSubjectsByClass();
} elseif ($fetch_type == 'timetable') {
    fetchTimetable();
} else {
    echo json_encode(array());
}


// Get attendance records for a specific date, class and subject
function fetchAttendanceRecords()
{
    global $conn;
    // Get parameters from URL
    $class_id = isset($_GET['class_id']) ? (int) $_GET['class_id'] : 0;
    $subject_id = isset($_GET['subject_id']) ? (int) $_GET['subject_id'] : 0;
    $att_date = isset($_GET['att_date']) ? escape($conn, $_GET['att_date']) : '';

    // Check if all parameters are provided
    if ($class_id == 0 || $subject_id == 0 || empty($att_date)) {
        echo json_encode(array());
        return;
    }

    // Query to get all students with their attendance status
    $sql = "SELECT s.id AS student_id, s.reg_number, s.name,
                   COALESCE(a.id, 0) AS att_id,
                   COALESCE(a.status, 'absent') AS status
            FROM students s
            LEFT JOIN attendance a ON a.student_id=s.id AND a.subject_id=$subject_id AND a.date='$att_date'
            WHERE s.class_id=$class_id
            ORDER BY s.name ASC";

    $rows = getRows($conn, $sql);
    echo json_encode($rows);
}

// Get marks for a specific subject and exam type
function fetchMarks()
{
    global $conn;
    // Get parameters from URL
    $class_id = isset($_GET['class_id']) ? (int) $_GET['class_id'] : 0;
    $subject_id = isset($_GET['subject_id']) ? (int) $_GET['subject_id'] : 0;
    $exam_type = isset($_GET['exam_type']) ? escape($conn, $_GET['exam_type']) : '';

    // Check if all parameters are provided
    if ($class_id == 0 || $subject_id == 0 || empty($exam_type)) {
        echo json_encode(array());
        return;
    }

    // Query to get all students with their marks
    $sql = "SELECT s.id AS student_id, s.reg_number, s.name,
                   COALESCE(m.id, 0) AS mark_id,
                   COALESCE(m.max_marks, 100) AS max_marks,
                   COALESCE(m.marks_obtained, '') AS marks_obtained,
                   COALESCE(m.remarks, '') AS remarks
            FROM students s
            LEFT JOIN marks m ON m.student_id=s.id AND m.subject_id=$subject_id AND m.exam_type='$exam_type'
            WHERE s.class_id=$class_id
            ORDER BY s.name ASC";

    $rows = getRows($conn, $sql);
    echo json_encode($rows);
}

// Get detailed marks for all subjects in a class
function fetchMarksDetail()
{
    global $conn;
    // Get class ID
    $class_id = isset($_GET['class_id']) ? (int) $_GET['class_id'] : 0;

    if ($class_id == 0) {
        echo json_encode(array('subjects' => array(), 'rows' => array()));
        return;
    }

    // Get all subjects for this class
    $subjects_sql = "SELECT id, subject_name FROM subjects WHERE class_id=$class_id ORDER BY subject_name";
    $subjects = getRows($conn, $subjects_sql);

    if (empty($subjects)) {
        echo json_encode(array('subjects' => array(), 'rows' => array()));
        return;
    }

    // Get all students for this class
    $students_sql = "SELECT id, reg_number, name FROM students WHERE class_id=$class_id ORDER BY name";
    $students = getRows($conn, $students_sql);

    // Get all marks for this class
    $marks_sql = "SELECT student_id, subject_id, exam_type, marks_obtained FROM marks WHERE class_id=$class_id AND exam_type IN ('internal', 'external')";
    $all_marks = getRows($conn, $marks_sql);

    // Build a map of marks for quick lookup
    $marks_map = array();
    foreach ($all_marks as $m) {
        $sid = $m['student_id'];
        $subj_id = $m['subject_id'];
        $type = $m['exam_type'];
        if (!isset($marks_map[$sid]))
            $marks_map[$sid] = array();
        if (!isset($marks_map[$sid][$subj_id]))
            $marks_map[$sid][$subj_id] = array();
        $marks_map[$sid][$subj_id][$type] = (float) $m['marks_obtained'];
    }

    // Build output rows with calculated totals and percentages
    $output_rows = array();
    foreach ($students as $student) {
        $sid = $student['id'];
        $marks_by_subject = array();
        $total = 0;
        $max_total = 0;
        $has_marks = false;

        // Process each subject
        foreach ($subjects as $subject) {
            $subj_id = $subject['id'];
            $internal = null;
            $external = null;
            $final = null;

            // Get marks if they exist
            if (isset($marks_map[$sid][$subj_id])) {
                $internal = isset($marks_map[$sid][$subj_id]['internal']) ? $marks_map[$sid][$subj_id]['internal'] : null;
                $external = isset($marks_map[$sid][$subj_id]['external']) ? $marks_map[$sid][$subj_id]['external'] : null;

                // Calculate final marks and totals
                if ($internal !== null && $external !== null) {
                    $final = $internal + $external;
                    $total = $total + $final;
                    $max_total = $max_total + 100;
                    $has_marks = true;
                } elseif ($internal !== null) {
                    $total = $total + $internal;
                    $max_total = $max_total + 20;
                    $has_marks = true;
                } elseif ($external !== null) {
                    $total = $total + $external;
                    $max_total = $max_total + 80;
                    $has_marks = true;
                }
            }

            // Store marks for this subject
            $mark_entry = array();
            $mark_entry['internal'] = $internal;
            $mark_entry['external'] = $external;
            $mark_entry['final'] = $final;
            $marks_by_subject[$subject['subject_name']] = $mark_entry;
        }

        // Calculate percentage
        $percentage = ($max_total > 0) ? round(($total / $max_total) * 100, 1) : null;

        // Build student row
        $row = array();
        $row['reg_number'] = $student['reg_number'];
        $row['name'] = $student['name'];
        $row['marks'] = $marks_by_subject;
        $row['grand_total'] = $has_marks ? $total : '—';
        $row['grand_max'] = $max_total;
        $row['percentage'] = $percentage;

        $output_rows[] = $row;
    }

    // Build subject names list
    $subject_names = array();
    foreach ($subjects as $s) {
        $subject_names[] = $s['subject_name'];
    }

    echo json_encode(array('subjects' => $subject_names, 'rows' => $output_rows));
}

// Get attendance records for a specific student
function fetchStudentAttendance()
{
    global $conn;
    // Get student ID
    $student_id = isset($_GET['student_id']) ? (int) $_GET['student_id'] : 0;

    if ($student_id == 0) {
        echo json_encode(array('records' => array(), 'present' => 0, 'absent' => 0, 'total' => 0));
        return;
    }

    // Query to get all attendance records for this student
    $sql = "SELECT a.date, sub.subject_name, a.status, a.marked_by
            FROM attendance a
            JOIN subjects sub ON sub.id=a.subject_id
            WHERE a.student_id=$student_id
            ORDER BY a.date DESC";

    $records = getRows($conn, $sql);

    // Count present and absent records
    $present = 0;
    $absent = 0;
    foreach ($records as $record) {
        if ($record['status'] == 'present') {
            $present = $present + 1;
        } else {
            $absent = $absent + 1;
        }
    }

    echo json_encode(array('records' => $records, 'present' => $present, 'absent' => $absent, 'total' => count($records)));
}

// Get all students for a specific class
function fetchStudentsByClass()
{
    global $conn;
    // Get class ID
    $class_id = isset($_GET['class_id']) ? (int) $_GET['class_id'] : 0;

    if ($class_id == 0) {
        echo json_encode(array());
        return;
    }

    // Query to get all student details
    $sql = "SELECT id, reg_number, name, gender, dob, contact, blood_group, email, guardian_name, guardian_contact, address
            FROM students
            WHERE class_id=$class_id
            ORDER BY name ASC";

    $students = getRows($conn, $sql);
    echo json_encode($students);
}

// Get all subjects for a specific class
function fetchSubjectsByClass()
{
    global $conn;
    // Get class ID
    $class_id = isset($_GET['class_id']) ? (int) $_GET['class_id'] : 0;

    if ($class_id == 0) {
        echo json_encode(array());
        return;
    }

    // Query to get all subjects
    $sql = "SELECT id, subject_name FROM subjects WHERE class_id=$class_id ORDER BY subject_name ASC";
    $subjects = getRows($conn, $sql);
    echo json_encode($subjects);
}

// Get timetable for a specific class
function fetchTimetable()
{
    global $conn;
    // Get class ID
    $class_id = isset($_GET['class_id']) ? (int) $_GET['class_id'] : 0;

    if ($class_id == 0) {
        echo json_encode(array());
        return;
    }

    // Query to get all timetable slots, ordered by day and period
    $sql = "SELECT day, period_label, subject_name FROM timetable WHERE class_id=$class_id ORDER BY FIELD(day,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'), period_label";
    $slots = getRows($conn, $sql);

    // Build timetable grouped by day
    $timetable = array();
    foreach ($slots as $slot) {
        $day = $slot['day'];
        $period = $slot['period_label'];
        $subject = $slot['subject_name'];

        // Create day entry if not exists
        if (!isset($timetable[$day])) {
            $timetable[$day] = array();
        }
        // Add subject for this period
        $timetable[$day][$period] = $subject;
    }

    echo json_encode($timetable);
}
