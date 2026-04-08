<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$email = trim($_POST['email'] ?? '');

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}

$checkStmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($checkStmt, 's', $email);
mysqli_stmt_execute($checkStmt);
$checkResult = mysqli_stmt_get_result($checkStmt);

if ($checkResult && mysqli_num_rows($checkResult) > 0) {
    mysqli_stmt_close($checkStmt);
    mysqli_close($conn);
    echo json_encode(['success' => false, 'message' => 'This email is already registered. Please log in.']);
    exit;
}

mysqli_stmt_close($checkStmt);

$otp = (string) random_int(100000, 999999);
$expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));

$deleteStmt = mysqli_prepare($conn, "DELETE FROM otp_verifications WHERE email = ?");
mysqli_stmt_bind_param($deleteStmt, 's', $email);
mysqli_stmt_execute($deleteStmt);
mysqli_stmt_close($deleteStmt);

$isUsed = 0;
$insertStmt = mysqli_prepare(
    $conn,
    "INSERT INTO otp_verifications (email, otp_code, expires_at, is_used) VALUES (?, ?, ?, ?)"
);
mysqli_stmt_bind_param($insertStmt, 'sssi', $email, $otp, $expiresAt, $isUsed);
$saved = mysqli_stmt_execute($insertStmt);
mysqli_stmt_close($insertStmt);

if (!$saved) {
    mysqli_close($conn);
    echo json_encode(['success' => false, 'message' => 'Could not save OTP. Please try again.']);
    exit;
}

mysqli_close($conn);

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'acadex.ams.26@gmail.com';
    $mail->Password = 'innj eipl dzsk jvkj';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // ── Sender & recipient ──
    $mail->setFrom('acadex.ams.26@gmail.com', 'SCMS — Smart Campus');
    $mail->addAddress($email);

    // ── Email content ──
    $mail->isHTML(true);
    $mail->Subject = 'Your SCMS Registration OTP';
    $mail->Body = '
        <div style="font-family: DM Sans, Arial, sans-serif; max-width: 480px; margin: auto;
                    border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden;">

          <!-- Header -->
          <div style="background: linear-gradient(135deg, #6366f1, #4f46e5);
                      padding: 28px 32px; text-align: center;">
            <h1 style="color: white; margin: 0; font-size: 22px; letter-spacing: -0.5px;">
              Smart Campus Management
            </h1>
            <p style="color: rgba(255,255,255,0.75); margin: 4px 0 0; font-size: 13px;">
              Email Verification
            </p>
          </div>

          <!-- Body -->
          <div style="padding: 32px;">
            <p style="color: #374151; font-size: 15px; margin: 0 0 16px;">
              Hello,<br/>
              Use the code below to verify your email address. It expires in <strong>10 minutes</strong>.
            </p>

            <!-- OTP box -->
            <div style="background: #f1f5f9; border-radius: 10px; padding: 20px;
                        text-align: center; margin: 24px 0;">
              <p style="margin: 0 0 6px; font-size: 12px; color: #64748b;
                         letter-spacing: 0.08em; text-transform: uppercase; font-weight: 600;">
                Your OTP Code
              </p>
              <p style="margin: 0; font-size: 36px; font-weight: 800;
                         letter-spacing: 0.25em; color: #4f46e5; font-family: monospace;">
                ' . htmlspecialchars($otp) . '
              </p>
            </div>

            <p style="color: #94a3b8; font-size: 12px; margin: 0;">
              If you did not request this, you can safely ignore this email.
            </p>
          </div>

          <!-- Footer -->
          <div style="background: #f8fafc; padding: 16px 32px; text-align: center;
                      border-top: 1px solid #e2e8f0;">
            <p style="margin: 0; font-size: 11px; color: #94a3b8;">
              Smart Campus Management System &nbsp;·&nbsp; v1.0
            </p>
          </div>
        </div>
    ';

    $mail->AltBody = 'Your SCMS OTP is: ' . $otp . ' — Expires in 10 minutes.';

    $mail->send();

    echo json_encode(['success' => true, 'message' => 'OTP sent to ' . $email]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Email could not be sent. Error: ' . $mail->ErrorInfo
    ]);
}
