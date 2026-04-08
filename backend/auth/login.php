<?php
/**
 * SCMS — Login Handler
 * Process login form submission
 */

require_once __DIR__ . '/../../config/db.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return;
}

// Get form data
$email = escape($conn, $_POST['login_id'] ?? '');
$password = escape($conn, $_POST['password'] ?? '');

// Validate
$error = '';

if (isEmpty($email)) {
    $error = 'Email is required.';
} elseif (isEmpty($password)) {
    $error = 'Password is required.';
}

// If validation passed, check database
if (empty($error)) {
    // Query: Find user by email
    $sql = "SELECT id, name, email, password, role, class FROM users WHERE email = '$email'";
    $user = getRow($conn, $sql);

    // Check if user exists
    if ($user == null) {
        $error = 'Invalid email or password.';
    }
    // Check if password matches
    elseif ($password != $user['password']) {
        $error = 'Invalid email or password.';
    }
    // Login success
    else {
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['name'];
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['class'] = $user['class'];

        // Redirect based on role
        if ($user['role'] == 'admin') {
            redirect('frontend/admin/index.php');
        } else {
            redirect('frontend/student/index.php');
        }
    }
}

// If error, return to login page

