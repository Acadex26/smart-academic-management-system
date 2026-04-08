-- ============================================================
-- database.sql  —  SCMS Full Database Schema (v3 — with timetable)
-- ============================================================
CREATE DATABASE IF NOT EXISTS scms_db;
USE scms_db;
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    class VARCHAR(50) NOT NULL DEFAULT '',
    role VARCHAR(20) NOT NULL DEFAULT 'student'
);
CREATE TABLE IF NOT EXISTS otp_verifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL,
    otp_code VARCHAR(6) NOT NULL,
    expires_at DATETIME NOT NULL,
    is_used TINYINT(1) NOT NULL DEFAULT 0
);
INSERT IGNORE INTO users (name, email, password, class, role)
VALUES (
        'Admin',
        'admin@scms.com',
        'admin123',
        '',
        'admin'
    );
CREATE TABLE IF NOT EXISTS classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_name VARCHAR(100) NOT NULL UNIQUE,
    academic_year VARCHAR(20) NOT NULL DEFAULT ''
);
CREATE TABLE IF NOT EXISTS subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT NOT NULL,
    subject_name VARCHAR(100) NOT NULL,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT NOT NULL,
    reg_number VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    gender VARCHAR(10) NOT NULL DEFAULT '',
    dob DATE DEFAULT NULL,
    contact VARCHAR(15) NOT NULL DEFAULT '',
    blood_group VARCHAR(5) NOT NULL DEFAULT '',
    email VARCHAR(150) NOT NULL DEFAULT '',
    guardian_name VARCHAR(100) NOT NULL DEFAULT '',
    guardian_contact VARCHAR(15) NOT NULL DEFAULT '',
    address TEXT NOT NULL DEFAULT '',
    is_registered TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    body TEXT NOT NULL,
    tag VARCHAR(30) NOT NULL DEFAULT 'info',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);
-- ── Timetable table ──
-- Stores one subject per period/day/class slot.
-- period_label: e.g. "8:00 - 9:00"
-- day: 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'
CREATE TABLE IF NOT EXISTS timetable (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT NOT NULL,
    day VARCHAR(15) NOT NULL,
    period_label VARCHAR(30) NOT NULL,
    subject_name VARCHAR(100) NOT NULL DEFAULT '',
    UNIQUE KEY unique_slot (class_id, day, period_label),
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE
);
-- ── marks table ──
CREATE TABLE IF NOT EXISTS marks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    class_id INT NOT NULL,
    exam_type VARCHAR(20) NOT NULL DEFAULT 'internal',
    max_marks INT NOT NULL DEFAULT 100,
    marks_obtained DECIMAL(6, 2) NOT NULL DEFAULT 0,
    remarks TEXT,
    exam_date DATE DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_mark (student_id, subject_id, exam_type),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE
);
-- ── attendance table ──
CREATE TABLE IF NOT EXISTS attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    class_id INT NOT NULL,
    date DATE NOT NULL,
    status ENUM('present', 'absent') NOT NULL DEFAULT 'present',
    marked_by VARCHAR(100) NOT NULL DEFAULT 'Admin',
    UNIQUE KEY unique_att (student_id, subject_id, date),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE
);