<?php
// frontend/admin/index.php — Admin Dashboard Entry Point (display only)
// All database queries are delegated to backend/admin/dashboard.php

session_start();
require_once __DIR__ . '/../../config/db.php';

// Access control — admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header('Location: ../../login.php');
  exit;
}

$admin_name = $_SESSION['username'] ?? 'Admin';
$admin_initial = strtoupper(substr($admin_name, 0, 1));
$admin_name_js = addslashes(htmlspecialchars($admin_name));

// Shared header component variables
$user_initial = $admin_initial;
$user_name = $admin_name;

// Delegate ALL data-fetching to the backend dashboard file.
// Sets: $total_users, $total_students, $total_classes, $total_boys, $total_girls,
//       $classes_list, $students_by_class, $anns_list, $all_classes, $all_anns_list,
//       $timetable_by_class, $marks_summary, $active_panel, $open_panel_js
require_once __DIR__ . '/../../backend/admin/dashboard.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SCMS — Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link
    href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap"
    rel="stylesheet" />
  <?php require_once __DIR__ . '/assets/styles.php'; ?>
  <?php require_once __DIR__ . '/../../frontend/shared/components/data_table_styles.php'; ?>
</head>

<body class="bg-[#f0f4f8]">

  <?php require_once __DIR__ . '/assets/sidebar.php'; ?>

  <div class="ml-60 flex flex-col min-h-screen">

    <?php require_once __DIR__ . '/../../frontend/shared/components/page_header.php'; ?>

    <!-- Content -->
    <div class="px-7 py-6 flex-1">
      <div class="flex items-center justify-between mb-5">
        <h2 class="text-[18px] font-bold text-[#1a202c] m-0" id="page-heading">Welcome,
          <?= htmlspecialchars($admin_name) ?> 👋
        </h2>
        <span class="text-[12px] text-[#8b949e] font-mono" id="page-breadcrumb">SCMS / dashboard</span>
      </div>

      <?php require_once __DIR__ . '/components/dashboard.php'; ?>
      <?php require_once __DIR__ . '/components/classes.php'; ?>
      <?php require_once __DIR__ . '/components/marks.php'; ?>
      <?php require_once __DIR__ . '/components/attendance.php'; ?>
      <?php require_once __DIR__ . '/components/announcements.php'; ?>
      <?php require_once __DIR__ . '/components/students.php'; ?>

    </div>
  </div>

  <!-- Inline JS data from PHP -->
  <script>
    var ADMIN_NAME = '<?= $admin_name_js ?>';
    var INITIAL_PANEL = '<?= $open_panel_js ?>';
    var studentsByClass = <?= json_encode($students_by_class, JSON_HEX_TAG | JSON_HEX_APOS) ?>;
    var classIdMap = {};
    <?php foreach ($all_classes as $cls): ?>
      classIdMap[<?= json_encode($cls['class_name']) ?>] = <?= (int) $cls['id'] ?>;
    <?php endforeach; ?>
  </script>
  <!-- Simplified UI JavaScript (no AJAX/fetch calls) -->
  <script src="assets/ui.js"></script>
</body>

</html>
<?php mysqli_close($conn); ?>