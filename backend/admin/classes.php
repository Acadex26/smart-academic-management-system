<?php
/**
 * SCMS — Admin Classes Handler
 * Handle add, edit, delete classes, subjects, and students
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
$class_id = (int) ($_POST['class_id'] ?? 0);

// ============================================================
// DELETE CLASS
// ============================================================
if ($action === 'delete' || isset($_POST['delete_class'])) {
    if ($class_id == 0) {
        redirect('../../frontend/admin/index.php?error=invalid_id');
    }

    // Delete the class
    $sql = "DELETE FROM classes WHERE id = $class_id";
    runQuery($conn, $sql);

    redirect('../../frontend/admin/index.php?panel=class-view&deleted=1');
}

// ============================================================
// ADD NEW CLASS
// ============================================================
if ($action === 'add') {
    $class_name = escape($conn, $_POST['class_name'] ?? '');
    $academic_year = escape($conn, $_POST['academic_year'] ?? '');

    // Validate
    if (isEmpty($class_name)) {
        redirect('../../frontend/admin/index.php?error=Class name required');
    }

    // Insert class
    $sql = "INSERT INTO classes (class_name, academic_year) VALUES ('$class_name', '$academic_year')";
    runQuery($conn, $sql);

    // Get the new class ID
    $new_class_id = mysqli_insert_id($conn);

    // Add subjects if provided
    if (isset($_POST['subjects'])) {
        foreach ($_POST['subjects'] as $subject) {
            $subject = escape($conn, $subject);

            // Skip empty subjects
            if (isEmpty($subject))
                continue;

            // Insert subject
            $sql = "INSERT INTO subjects (class_id, subject_name) VALUES ($new_class_id, '$subject')";
            runQuery($conn, $sql);
        }
    }

    redirect('../../frontend/admin/index.php?panel=class-view&saved=1');
}

// ============================================================
// EDIT CLASS
// ============================================================
if ($action === 'edit') {
    if ($class_id == 0) {
        redirect('../../frontend/admin/index.php?error=invalid_id');
    }

    $class_name = escape($conn, $_POST['class_name'] ?? '');
    $academic_year = escape($conn, $_POST['academic_year'] ?? '');

    // Validate
    if (isEmpty($class_name)) {
        redirect('../../frontend/admin/index.php?error=Class name required');
    }

    // Update class
    $sql = "UPDATE classes SET class_name='$class_name', academic_year='$academic_year' WHERE id=$class_id";
    runQuery($conn, $sql);

    // Handle students update/removal
    if (isset($_POST['remove_students'])) {
        foreach ($_POST['remove_students'] as $reg_number) {
            $reg_number = escape($conn, $reg_number);
            if (!isEmpty($reg_number)) {
                $sql = "DELETE FROM students WHERE reg_number='$reg_number' AND class_id=$class_id";
                runQuery($conn, $sql);
            }
        }
    }

    // Update existing students
    if (isset($_POST['students'])) {
        foreach ($_POST['students'] as $student) {
            $student_id = (int) ($student['id'] ?? 0);

            if ($student_id == 0)
                continue;

            $reg_number = escape($conn, $student['reg_number'] ?? '');
            $name = escape($conn, $student['name'] ?? '');
            $gender = escape($conn, $student['gender'] ?? '');
            $dob = escape($conn, $student['dob'] ?? '');
            $contact = escape($conn, $student['contact'] ?? '');
            $blood_group = escape($conn, $student['blood_group'] ?? '');
            $email = escape($conn, $student['email'] ?? '');
            $guardian_name = escape($conn, $student['guardian_name'] ?? '');
            $guardian_contact = escape($conn, $student['guardian_contact'] ?? '');
            $address = escape($conn, $student['address'] ?? '');

            // Update student
            $sql = "UPDATE students 
                    SET name='$name', gender='$gender', dob='$dob', 
                        contact='$contact', blood_group='$blood_group', 
                        email='$email', guardian_name='$guardian_name',
                        guardian_contact='$guardian_contact', address='$address'
                    WHERE id=$student_id AND class_id=$class_id";

            runQuery($conn, $sql);
        }
    }

    // Add new students
    if (isset($_POST['new_students'])) {
        foreach ($_POST['new_students'] as $student) {
            $name = escape($conn, $student['name'] ?? '');

            // Skip empty names
            if (isEmpty($name))
                continue;

            $reg_number = 'STU' . date('Ymd') . rand(1000, 9999);
            $gender = escape($conn, $student['gender'] ?? '');
            $dob = escape($conn, $student['dob'] ?? '');
            $contact = escape($conn, $student['contact'] ?? '');
            $blood_group = escape($conn, $student['blood_group'] ?? '');
            $email = escape($conn, $student['email'] ?? '');
            $guardian_name = escape($conn, $student['guardian_name'] ?? '');
            $guardian_contact = escape($conn, $student['guardian_contact'] ?? '');
            $address = escape($conn, $student['address'] ?? '');

            // Insert new student
            $sql = "INSERT INTO students 
                    (class_id, reg_number, name, gender, dob, contact, blood_group, 
                     email, guardian_name, guardian_contact, address, is_registered)
                    VALUES 
                    ($class_id, '$reg_number', '$name', '$gender', '$dob', '$contact', '$blood_group',
                     '$email', '$guardian_name', '$guardian_contact', '$address', 0)";

            runQuery($conn, $sql);
        }
    }

    redirect('../../frontend/admin/index.php?panel=class-view&saved=1');
}

// Default redirect
redirect('../../frontend/admin/index.php');


mysqli_close($conn);

// Redirect back to the correct panel (student panels pass _redirect_panel)
$panel = trim($_POST['_redirect_panel'] ?? '');
if ($panel === 'student-add')
    redirect('../../frontend/admin/index.php?panel=student-add&saved=1');
if ($panel === 'student-edit')
    redirect('../../frontend/admin/index.php?panel=student-edit&updated=1');
redirect('../../frontend/admin/index.php?panel=class-view&updated=1');


mysqli_close($conn);
redirect('../../frontend/admin/index.php');

// ── HELPER: Save timetable rows ────────────────────────────
// Inserts non-blank cells from timetable[Day][Period] = Subject.
function save_timetable($conn, int $class_id, array $timetable): void
{
    foreach ($timetable as $day => $periods) {
        $day = trim($day);
        foreach ($periods as $period => $subject) {
            $subject = trim($subject);
            if ($subject === '')
                continue;
            $period = trim($period);
            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO timetable (class_id, day, period_label, subject_name)
                 VALUES (?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE subject_name=VALUES(subject_name)"
            );
            mysqli_stmt_bind_param($stmt, 'isss', $class_id, $day, $period, $subject);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
}
