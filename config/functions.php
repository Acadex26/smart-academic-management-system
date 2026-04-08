<?php
/**
 * SCMS — Simple Helper Functions
 * Common functions used across the project
 */

// ============================================================
// DATABASE QUERY HELPERS
// ============================================================

/**
 * Run a SELECT query and return all rows
 */
function getRows($conn, $sql)
{
    // Run query
    $result = mysqli_query($conn, $sql);

    // Check if query failed
    if (!$result) {
        return [];
    }

    // Get all rows as array
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    return $rows;
}

/**
 * Run a SELECT query and return first row
 */
function getRow($conn, $sql)
{
    // Run query
    $result = mysqli_query($conn, $sql);

    // Check if query failed
    if (!$result) {
        return null;
    }

    // Get first row
    $row = mysqli_fetch_assoc($result);

    return $row;
}

/**
 * Run a query (INSERT, UPDATE, DELETE)
 */
function runQuery($conn, $sql)
{
    // Execute query
    $result = mysqli_query($conn, $sql);

    // Return true if success, false if failed
    return $result ? true : false;
}

/**
 * Escape string for safe database use
 */
function escape($conn, $value)
{
    return mysqli_real_escape_string($conn, trim($value));
}

// ============================================================
// VALIDATION HELPERS
// ============================================================

/**
 * Check if email is valid
 */
function isValidEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? true : false;
}

/**
 * Check if password meets requirements
 */
function isValidPassword($password)
{
    return strlen($password) >= 6 ? true : false;
}

/**
 * Check if value is empty
 */
function isEmpty($value)
{
    return empty(trim($value)) ? true : false;
}

// ============================================================
// SESSION & AUTH HELPERS
// ============================================================

/**
 * Check if user is logged in
 */
function isLoggedIn()
{
    return isset($_SESSION['role']) ? true : false;
}

/**
 * Check if user is admin
 */
function isAdmin()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin' ? true : false;
}

/**
 * Check if user is student
 */
function isStudent()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'student' ? true : false;
}

/**
 * Get user ID from session
 */
function getUserId()
{
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

/**
 * Get user role from session
 */
function getUserRole()
{
    return isset($_SESSION['role']) ? $_SESSION['role'] : null;
}

// ============================================================
// STRING HELPERS
// ============================================================

/**
 * Safe HTML output / prevent XSS
 */
function safe($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Get initials from name
 */
function getInitials($name)
{
    return strtoupper(substr(str_replace(' ', '', $name), 0, 1));
}

// ============================================================
// REDIRECT HELPERS
// ============================================================

/**
 * Redirect to page
 */
function redirect($page)
{
    header('Location: ' . $page);
    exit;
}

/**
 * Redirect with message
 */
function redirectWithMsg($page, $msg)
{
    header('Location: ' . $page . '?msg=' . urlencode($msg));
    exit;
}
