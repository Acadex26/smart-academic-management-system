<?php
/**
 * SCMS — Registration Handler
 * Process registration form submission
 */

require_once __DIR__ . '/../../config/db.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return;
}

// Get form data
$name = escape($conn, $_POST['name'] ?? '');
$email = escape($conn, $_POST['email'] ?? '');
$password = escape($conn, $_POST['password'] ?? '');
$confirm = escape($conn, $_POST['confirm_password'] ?? '');
$class = escape($conn, $_POST['class'] ?? '');

// Validate
$errors = [];

if (isEmpty($name)) {
    $errors[] = 'Full name is required.';
}

if (isEmpty($email) || !isValidEmail($email)) {
    $errors[] = 'Valid email is required.';
}

if (isEmpty($password) || !isValidPassword($password)) {
    $errors[] = 'Password must be at least 6 characters.';
}

if ($password != $confirm) {
    $errors[] = 'Passwords do not match.';
}

if (isEmpty($class)) {
    $errors[] = 'Please select your class.';
}

// Check if email already exists
if (count($errors) == 0) {
    $sql = "SELECT id FROM users WHERE email = '$email'";
    $result = getRow($conn, $sql);

    if ($result != null) {
        $errors[] = 'Email already registered. Please sign in instead.';
    }
}

// If validation passed, insert user
if (count($errors) == 0) {
    $sql = "INSERT INTO users (name, email, password, class, role) 
            VALUES ('$name', '$email', '$password', '$class', 'student')";

    if (runQuery($conn, $sql)) {
        redirect('../../login.php?registered=1');
    } else {
        $errors[] = 'Registration failed. Please try again.';
    }
}

// If errors, return to register page

