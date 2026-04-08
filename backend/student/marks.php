<?php
/**
 * SCMS — Student Marks Data
 * Fetches internal, external, and final marks for student
 */

require_once __DIR__ . '/../../config/db.php';

$internal_marks = [];
$external_marks = [];
$final_marks = [];

// Get marks only if student is found
if ($student_row) {
    $student_id = (int) $student_row['id'];

    // Fetch marks
    $sql = "SELECT sub.subject_name, m.exam_type, m.marks_obtained, m.max_marks, m.remarks
            FROM marks m
            JOIN subjects sub ON sub.id = m.subject_id
            WHERE m.student_id = $student_id AND m.exam_type IN ('internal', 'external')
            ORDER BY sub.subject_name ASC, m.exam_type ASC";

    $marks_rows = getRows($conn, $sql);

    // Group by subject
    $by_subject = [];
    foreach ($marks_rows as $row) {
        $subject = $row['subject_name'];
        $exam_type = $row['exam_type'];

        if (!isset($by_subject[$subject])) {
            $by_subject[$subject] = [];
        }

        $by_subject[$subject][$exam_type] = [
            'obtained' => (float) $row['marks_obtained'],
            'max' => (int) $row['max_marks'],
            'remarks' => $row['remarks']
        ];
    }

    // Process each subject
    foreach ($by_subject as $subject => $types) {
        // Internal marks
        if (isset($types['internal'])) {
            $mark = $types['internal'];
            $pct = $mark['max'] > 0 ? round(($mark['obtained'] / $mark['max']) * 100) : 0;

            $internal_marks[] = [
                'subject' => $subject,
                'obtained' => $mark['obtained'],
                'max' => $mark['max'],
                'remarks' => $mark['remarks'],
                'pct' => $pct
            ];
        }

        // External marks
        if (isset($types['external'])) {
            $mark = $types['external'];
            $pct = $mark['max'] > 0 ? round(($mark['obtained'] / $mark['max']) * 100) : 0;

            $external_marks[] = [
                'subject' => $subject,
                'obtained' => $mark['obtained'],
                'max' => $mark['max'],
                'remarks' => $mark['remarks'],
                'pct' => $pct
            ];
        }

        // Final marks (internal + external)
        if (isset($types['internal']) && isset($types['external'])) {
            $internal = $types['internal']['obtained'];
            $external = $types['external']['obtained'];
            $total = $internal + $external;
            $pct = round(($total / 100) * 100);

            $final_marks[] = [
                'subject' => $subject,
                'internal' => $internal,
                'external' => $external,
                'final' => $total,
                'max' => 100,
                'pct' => $pct
            ];
        }
    }
}

