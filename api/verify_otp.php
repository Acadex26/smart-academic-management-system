<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(array('success' => false, 'message' => 'Invalid request method.'));
    exit;
}

$email = '';
$otp = '';

if (isset($_POST['email'])) {
    $email = trim($_POST['email']);
}

if (isset($_POST['otp'])) {
    $otp = trim($_POST['otp']);
}

if ($email == '' || $otp == '') {
    echo json_encode(array('success' => false, 'message' => 'Email and OTP are required.'));
    exit;
}

if (strlen($otp) != 6 || !ctype_digit($otp)) {
    echo json_encode(array('success' => false, 'message' => 'OTP must be a 6-digit number.'));
    exit;
}

$email = mysqli_real_escape_string($conn, $email);
$otp = mysqli_real_escape_string($conn, $otp);

$query = "SELECT id, expires_at, is_used FROM otp_verifications WHERE email = '$email' AND otp_code = '$otp' ORDER BY id DESC LIMIT 1";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    mysqli_close($conn);
    echo json_encode(array('success' => false, 'message' => 'Incorrect OTP. Please check and try again.'));
    exit;
}

$row = mysqli_fetch_assoc($result);

if ($row['is_used'] == 1) {
    mysqli_close($conn);
    echo json_encode(array('success' => false, 'message' => 'This OTP has already been used. Please request a new one.'));
    exit;
}

if (time() > strtotime($row['expires_at'])) {
    mysqli_close($conn);
    echo json_encode(array('success' => false, 'message' => 'OTP has expired. Please request a new one.'));
    exit;
}

$otp_id = (int) $row['id'];
mysqli_query($conn, "UPDATE otp_verifications SET is_used = 1 WHERE id = $otp_id");
mysqli_close($conn);

echo json_encode(array('success' => true, 'message' => 'Email verified successfully!'));
