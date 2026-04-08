<?php
/**
 * SCMS — Admin Marks Handler
 * Handle add, edit student marks
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

$action = $_POST['action'] ?? 'add';
$marks_list = $_POST['marks'] ?? [];

// ============================================================
// ADD MARKS
// ============================================================
if ($action === 'add') {
    $class_id = (int) ($_POST['class_id'] ?? 0);
    $subject_id = (int) ($_POST['subject_id'] ?? 0);
    $exam_type = escape($conn, $_POST['exam_type'] ?? '');
    $exam_date = escape($conn, $_POST['exam_date'] ?? '');

    // Validate
    if ($class_id == 0 || $subject_id == 0 || isEmpty($exam_type)) {
        redirect('../../frontend/admin/index.php?error=Missing required fields');
    }

    // Set maximum marks based on exam type
    $max_marks = 0;
    if ($exam_type === 'internal')
        $max_marks = 20;
    elseif ($exam_type === 'external')
        $max_marks = 80;
    else {
        redirect('../../frontend/admin/index.php?error=Invalid exam type');
    }

    // Insert/update marks for each student
    foreach ($marks_list as $mark_entry) {
        $student_id = (int) ($mark_entry['student_id'] ?? 0);

        if ($student_id == 0)
            continue;

        $obtained = (float) ($mark_entry['obtained'] ?? 0);

        // Clamp marks between 0 and max_marks
        if ($obtained < 0)
            $obtained = 0;
        if ($obtained > $max_marks)
            $obtained = $max_marks;

        $remarks = escape($conn, $mark_entry['remarks'] ?? '');

        // Check if mark already exists
        $sql = "SELECT id FROM marks WHERE student_id=$student_id AND subject_id=$subject_id AND exam_type='$exam_type'";
        $existing = getRow($conn, $sql);

        if ($existing) {
            // Update existing mark
            $sql = "UPDATE marks 
                    SET marks_obtained=$obtained, remarks='$remarks', exam_date='$exam_date'
                    WHERE id=" . $existing['id'];
            runQuery($conn, $sql);
        } else {
            // Insert new mark
            $sql = "INSERT INTO marks (student_id, subject_id, class_id, exam_type, max_marks, marks_obtained, remarks, exam_date)
                    VALUES ($student_id, $subject_id, $class_id, '$exam_type', $max_marks, $obtained, '$remarks', '$exam_date')";
            runQuery($conn, $sql);
        }
    }

    redirect('../../frontend/admin/index.php?panel=marks-view&saved=1');
}

// ============================================================
// EDIT MARKS
// ============================================================
if ($action === 'edit') {
    foreach ($marks_list as $mark_entry) {
        $mark_id = (int) ($mark_entry['mark_id'] ?? 0);

        if ($mark_id == 0)
            continue;

        $obtained = (float) ($mark_entry['obtained'] ?? 0);
        $remarks = escape($conn, $mark_entry['remarks'] ?? '');

        // Get the max marks for this record
        $sql = "SELECT max_marks FROM marks WHERE id=$mark_id";
        $mark_row = getRow($conn, $sql);

        // Clamp marks between 0 and max_marks
        if ($mark_row) {
            $max_marks = (float) $mark_row['max_marks'];
            if ($obtained < 0)
                $obtained = 0;
            if ($obtained > $max_marks)
                $obtained = $max_marks;
        }

        // Update the mark
        $sql = "UPDATE marks SET marks_obtained=$obtained, remarks='$remarks' WHERE id=$mark_id";
        runQuery($conn, $sql);
    }

    redirect('../../frontend/admin/index.php?panel=marks-view&updated=1');
}

// Default redirect
redirect('../../frontend/admin/index.php');

