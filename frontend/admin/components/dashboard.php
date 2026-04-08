<?php
// frontend/admin/components/dashboard.php
// Variables: $total_users, $total_students, $total_classes, $total_boys, $total_girls, $anns_list
?>
<div id="panel-dashboard" class="module-panel active">

  <!-- Stats Grid -->
  <div class="grid grid-cols-5 gap-4 mb-6">
    <div class="stat-card blue bg-white rounded-2xl p-5 shadow-sm border border-[#e8edf2]">
      <div class="flex items-start justify-between mb-3.5">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-[rgba(88,166,255,0.12)]">
          <svg class="w-5 h-5" fill="none" stroke="#58a6ff" stroke-width="2" viewBox="0 0 24 24">
            <path
              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0" />
          </svg>
        </div>
        <span
          class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-[rgba(63,185,80,0.12)] text-[#3fb950]">Live</span>
      </div>
      <p class="text-[28px] font-bold text-[#1a202c] font-mono tracking-tight m-0"><?= htmlspecialchars($total_users) ?>
      </p>
      <p class="text-[12px] text-[#8b949e] mt-1 font-medium">Registered Users (DB)</p>
    </div>

    <div class="stat-card green bg-white rounded-2xl p-5 shadow-sm border border-[#e8edf2]">
      <div class="flex items-start justify-between mb-3.5">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-[rgba(63,185,80,0.12)]">
          <svg class="w-5 h-5" fill="none" stroke="#3fb950" stroke-width="2" viewBox="0 0 24 24">
            <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </div>
        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-[rgba(63,185,80,0.12)] text-[#3fb950]">↑
          1.1%</span>
      </div>
      <p class="text-[28px] font-bold text-[#1a202c] font-mono tracking-tight m-0"><?= $total_students ?></p>
      <p class="text-[12px] text-[#8b949e] mt-1 font-medium">Total Students</p>
    </div>

    <div class="stat-card orange bg-white rounded-2xl p-5 shadow-sm border border-[#e8edf2]">
      <div class="flex items-start justify-between mb-3.5">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-[rgba(210,153,34,0.12)]">
          <svg class="w-5 h-5" fill="none" stroke="#d29922" stroke-width="2" viewBox="0 0 24 24">
            <path
              d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
        </div>
        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-[rgba(63,185,80,0.12)] text-[#3fb950]">↑
          0.8%</span>
      </div>
      <p class="text-[28px] font-bold text-[#1a202c] font-mono tracking-tight m-0"><?= $total_classes ?></p>
      <p class="text-[12px] text-[#8b949e] mt-1 font-medium">Total Classes</p>
    </div>

    <div class="stat-card blue bg-white rounded-2xl p-5 shadow-sm border border-[#e8edf2]">
      <div class="flex items-start justify-between mb-3.5">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-[rgba(88,166,255,0.12)]">
          <svg class="w-5 h-5" fill="none" stroke="#58a6ff" stroke-width="2" viewBox="0 0 24 24">
            <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </div>
        <span
          class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-[rgba(63,185,80,0.12)] text-[#3fb950]">Live</span>
      </div>
      <p class="text-[28px] font-bold text-[#1a202c] font-mono tracking-tight m-0"><?= $total_boys ?></p>
      <p class="text-[12px] text-[#8b949e] mt-1 font-medium">Total Boys</p>
    </div>

    <div class="stat-card purple bg-white rounded-2xl p-5 shadow-sm border border-[#e8edf2]">
      <div class="flex items-start justify-between mb-3.5">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-[rgba(188,140,255,0.12)]">
          <svg class="w-5 h-5" fill="none" stroke="#bc8cff" stroke-width="2" viewBox="0 0 24 24">
            <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </div>
        <span
          class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-[rgba(63,185,80,0.12)] text-[#3fb950]">Live</span>
      </div>
      <p class="text-[28px] font-bold text-[#1a202c] font-mono tracking-tight m-0"><?= $total_girls ?></p>
      <p class="text-[12px] text-[#8b949e] mt-1 font-medium">Total Girls</p>
    </div>
  </div>

  <!-- Lower Grid -->
  <div class="grid grid-cols-2 gap-5">

    <!-- Shared calendar component -->
    <?php require_once __DIR__ . '/../../../frontend/shared/components/calendar.php'; ?>

    <!-- Announcements widget -->
    <div class="bg-white rounded-2xl p-[22px] shadow-sm border border-[#e8edf2]">
      <div class="flex items-center justify-between mb-[18px]">
        <div>
          <div class="text-[14px] font-bold text-[#1a202c]">Announcements</div>
          <div class="text-[12px] text-[#8b949e]">Latest updates</div>
        </div>
        <button onclick="showPanel('ann-add')"
          class="text-[11.5px] font-semibold text-[#58a6ff] bg-[rgba(88,166,255,0.1)] border-none rounded-md px-3 py-1 cursor-pointer">
          + Add New
        </button>
      </div>
      <div class="flex flex-col gap-3 max-h-[360px] overflow-y-auto pr-1">
        <?php if (empty($anns_list)): ?>
          <div class="py-6 text-center text-[#94a3b8] text-[13px]">No announcements yet.</div>
        <?php else:
          foreach ($anns_list as $a):
            $ann = [
              'title' => $a['title'],
              'tag' => $a['tag'],
              'tag_label' => ucfirst($a['tag']),
              'desc' => $a['body'],
              'date' => date('F j, Y', strtotime($a['created_at'])),
            ];
            $ann_cursor = 'cursor-pointer';
            require __DIR__ . '/../../../frontend/shared/components/announcement_card.php';
          endforeach;
        endif; ?>
      </div>
    </div>

  </div>
</div>