<?php
// get_students_by_class.php — Returns students for a class as JSON.
// NOTE: This is kept for backward compatibility.
// The same data is available via data_fetch.php?fetch=students_by_class&class_id=X
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['role'])) {
    echo json_encode([]);
    exit;
}

$class_id = (int) ($_GET['class_id'] ?? 0);
if (!$class_id) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT id, reg_number, name, gender, dob, contact,
               blood_group, email, guardian_name, guardian_contact, address
        FROM   students
        WHERE  class_id = ?
        ORDER  BY name ASC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $class_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$students = [];
while ($row = mysqli_fetch_assoc($result)) {
    $students[] = $row;
}
mysqli_stmt_close($stmt);
mysqli_close($conn);
echo json_encode($students);
