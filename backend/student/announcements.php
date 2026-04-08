<?php
// backend/student/announcements.php
// Fetches all announcements for the student announcements page.
// Included by frontend/student/index.php when page = announcements.
// Requires: $conn
// Provides: $announcements (full list, no limit)
// Note: dashboard.php also fetches $announcements limited to 10.
//       This file fetches all of them for the full announcements page.

require_once __DIR__ . '/../../config/db.php';

$announcements = [];

$_r = mysqli_query(
    $conn,
    "SELECT title, body, tag, created_at FROM announcements ORDER BY created_at DESC"
);

if ($_r) {
    while ($row = mysqli_fetch_assoc($_r)) {
        $announcements[] = [
            'title' => $row['title'],
            'tag' => $row['tag'],
            'tag_label' => ucfirst($row['tag']),
            'desc' => $row['body'],
            'date' => date('F j, Y', strtotime($row['created_at'])),
        ];
    }
    mysqli_free_result($_r);
}
