<?php
// frontend/admin/partials/sidebar.php
// Variables expected: $active_panel (string)
?>
<style>
  /* Sidebar: CSS-only accordion (checkbox hack) — Tailwind doesn't cover this natively */
  .nav-toggle {
    display: none;
  }

  .nav-toggle:checked~.nav-btn {
    color: #58a6ff;
    background: rgba(88, 166, 255, 0.15);
    border-left: 3px solid #58a6ff;
  }

  .nav-toggle:checked~.nav-btn .chevron {
    transform: rotate(90deg);
  }

  .sub-menu {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
  }

  .nav-toggle:checked~.sub-menu {
    max-height: 200px;
  }

  .nav-btn {
    cursor: pointer;
  }

  /* Scrollbar */
  ::-webkit-scrollbar {
    width: 5px;
  }

  ::-webkit-scrollbar-track {
    background: transparent;
  }

  ::-webkit-scrollbar-thumb {
    background: #30363d;
    border-radius: 99px;
  }
</style>
<aside class="w-60 min-h-screen bg-[#0d1117] border-r border-[#21262d] flex flex-col fixed top-0 left-0 z-50">
  <!-- Logo -->
  <div class="px-[18px] pt-5 pb-4 border-b border-[#21262d]">
    <span class="text-[11px] font-semibold tracking-[0.12em] text-[#58a6ff] uppercase">SCMS</span>
    <p class="text-[13px] text-[#8b949e] mt-0.5">Admin Dashboard</p>
  </div>

  <!-- Nav -->
  <nav class="px-2 py-2.5 flex-1 overflow-y-auto">
    <div class="text-[10px] font-semibold tracking-[0.1em] text-[#444d56] uppercase px-2.5 py-2">Navigation Menu</div>

    <!-- Dashboard -->
    <div class="w-full">
      <label
        class="nav-btn flex items-center gap-2.5 w-full px-3 py-2 rounded-lg text-[13.5px] font-medium justify-between border-none bg-none
        <?= $active_panel === 'dashboard' ? 'text-[#58a6ff] bg-[rgba(88,166,255,0.15)] border-l-[3px] border-l-[#58a6ff]' : 'text-[#c9d1d9] hover:bg-[rgba(88,166,255,0.08)] hover:text-white' ?>"
        onclick="showPanel('dashboard')">
        <span class="flex items-center gap-2.5">
          <svg class="w-4 h-4 opacity-75" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <rect x="3" y="3" width="7" height="7" />
            <rect x="14" y="3" width="7" height="7" />
            <rect x="3" y="14" width="7" height="7" />
            <rect x="14" y="14" width="7" height="7" />
          </svg>
          Dashboard
        </span>
      </label>
    </div>

    <!-- Classes -->
    <div class="w-full">
      <input type="checkbox" class="nav-toggle" id="nav-classes" <?= in_array($active_panel, ['class-view', 'class-add', 'class-edit']) ? 'checked' : '' ?>>
      <label
        class="nav-btn flex items-center gap-2.5 w-full px-3 py-2 rounded-lg text-[13.5px] font-medium text-[#c9d1d9] hover:bg-[rgba(88,166,255,0.08)] hover:text-white justify-between"
        for="nav-classes">
        <span class="flex items-center gap-2.5">
          <svg class="w-4 h-4 opacity-75" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M12 14l9-5-9-5-9 5 9 5z" />
            <path d="M12 14l6.16-3.422A12.083 12.083 0 0112 21.5a12.083 12.083 0 01-6.16-10.922L12 14z" />
          </svg>Classes
        </span>
        <svg class="chevron w-3.5 h-3.5 transition-transform duration-200 text-[#444d56]" fill="none"
          stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path d="M9 18l6-6-6-6" />
        </svg>
      </label>
      <div class="sub-menu pl-9 pb-1">
        <a class="block px-2.5 py-[7px] text-[12.5px] rounded-md cursor-pointer text-[#8b949e] hover:bg-[rgba(88,166,255,0.06)] hover:text-[#c9d1d9] before:content-['·'] before:mr-2 before:text-[#58a6ff] before:text-base <?= $active_panel === 'class-view' ? 'text-[#58a6ff] bg-[rgba(88,166,255,0.1)]' : '' ?>"
          onclick="showPanel('class-view')" href="#">View Class</a>
        <a class="block px-2.5 py-[7px] text-[12.5px] rounded-md cursor-pointer text-[#8b949e] hover:bg-[rgba(88,166,255,0.06)] hover:text-[#c9d1d9] before:content-['·'] before:mr-2 before:text-[#58a6ff] before:text-base <?= $active_panel === 'class-add' ? 'text-[#58a6ff] bg-[rgba(88,166,255,0.1)]' : '' ?>"
          onclick="showPanel('class-add')" href="#">Add Class</a>
        <a class="block px-2.5 py-[7px] text-[12.5px] rounded-md cursor-pointer text-[#8b949e] hover:bg-[rgba(88,166,255,0.06)] hover:text-[#c9d1d9] before:content-['·'] before:mr-2 before:text-[#58a6ff] before:text-base <?= $active_panel === 'class-edit' ? 'text-[#58a6ff] bg-[rgba(88,166,255,0.1)]' : '' ?>"
          onclick="showPanel('class-edit')" href="#">Edit Class</a>
      </div>
    </div>

    <!-- Students -->
    <div class="w-full">
      <input type="checkbox" class="nav-toggle" id="nav-students" <?= in_array($active_panel, ['student-view', 'student-add', 'student-edit']) ? 'checked' : '' ?>>
      <label
        class="nav-btn flex items-center gap-2.5 w-full px-3 py-2 rounded-lg text-[13.5px] font-medium text-[#c9d1d9] hover:bg-[rgba(88,166,255,0.08)] hover:text-white justify-between"
        for="nav-students">
        <span class="flex items-center gap-2.5">
          <svg class="w-4 h-4 opacity-75" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>Students
        </span>
        <svg class="chevron w-3.5 h-3.5 transition-transform duration-200 text-[#444d56]" fill="none"
          stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path d="M9 18l6-6-6-6" />
        </svg>
      </label>
      <div class="sub-menu pl-9 pb-1">
        <a class="block px-2.5 py-[7px] text-[12.5px] rounded-md cursor-pointer text-[#8b949e] hover:bg-[rgba(88,166,255,0.06)] hover:text-[#c9d1d9] before:content-['·'] before:mr-2 before:text-[#58a6ff] before:text-base <?= $active_panel === 'student-view' ? 'text-[#58a6ff] bg-[rgba(88,166,255,0.1)]' : '' ?>"
          onclick="showPanel('student-view')" href="#">View Students</a>
        <a class="block px-2.5 py-[7px] text-[12.5px] rounded-md cursor-pointer text-[#8b949e] hover:bg-[rgba(88,166,255,0.06)] hover:text-[#c9d1d9] before:content-['·'] before:mr-2 before:text-[#58a6ff] before:text-base <?= $active_panel === 'student-add' ? 'text-[#58a6ff] bg-[rgba(88,166,255,0.1)]' : '' ?>"
          onclick="showPanel('student-add')" href="#">Add Student</a>
        <a class="block px-2.5 py-[7px] text-[12.5px] rounded-md cursor-pointer text-[#8b949e] hover:bg-[rgba(88,166,255,0.06)] hover:text-[#c9d1d9] before:content-['·'] before:mr-2 before:text-[#58a6ff] before:text-base <?= $active_panel === 'student-edit' ? 'text-[#58a6ff] bg-[rgba(88,166,255,0.1)]' : '' ?>"
          onclick="showPanel('student-edit')" href="#">Edit Student</a>
      </div>
    </div>

    <!-- Marks -->
    <div class="w-full">
      <input type="checkbox" class="nav-toggle" id="nav-marks" <?= in_array($active_panel, ['marks-view', 'marks-add', 'marks-edit']) ? 'checked' : '' ?>>
      <label
        class="nav-btn flex items-center gap-2.5 w-full px-3 py-2 rounded-lg text-[13.5px] font-medium text-[#c9d1d9] hover:bg-[rgba(88,166,255,0.08)] hover:text-white justify-between"
        for="nav-marks">
        <span class="flex items-center gap-2.5">
          <svg class="w-4 h-4 opacity-75" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path
              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            <path d="M9 12h6M9 16h4" />
          </svg>Marks
        </span>
        <svg class="chevron w-3.5 h-3.5 transition-transform duration-200 text-[#444d56]" fill="none"
          stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path d="M9 18l6-6-6-6" />
        </svg>
      </label>
      <div class="sub-menu pl-9 pb-1">
        <a class="block px-2.5 py-[7px] text-[12.5px] rounded-md cursor-pointer text-[#8b949e] hover:bg-[rgba(88,166,255,0.06)] hover:text-[#c9d1d9] before:content-['·'] before:mr-2 before:text-[#58a6ff] before:text-base <?= $active_panel === 'marks-view' ? 'text-[#58a6ff] bg-[rgba(88,166,255,0.1)]' : '' ?>"
          onclick="showPanel('marks-view')" href="#">View Marks</a>
        <a class="block px-2.5 py-[7px] text-[12.5px] rounded-md cursor-pointer text-[#8b949e] hover:bg-[rgba(88,166,255,0.06)] hover:text-[#c9d1d9] before:content-['·'] before:mr-2 before:text-[#58a6ff] before:text-base <?= $active_panel === 'marks-add' ? 'text-[#58a6ff] bg-[rgba(88,166,255,0.1)]' : '' ?>"
          onclick="showPanel('marks-add')" href="#">Add Marks</a>
        <a class="block px-2.5 py-[7px] text-[12.5px] rounded-md cursor-pointer text-[#8b949e] hover:bg-[rgba(88,166,255,0.06)] hover:text-[#c9d1d9] before:content-['·'] before:mr-2 before:text-[#58a6ff] before:text-base <?= $active_panel === 'marks-edit' ? 'text-[#58a6ff] bg-[rgba(88,166,255,0.1)]' : '' ?>"
          onclick="showPanel('marks-edit')" href="#">Edit Marks</a>
      </div>
    </div>

    <!-- Attendance -->
    <div class="w-full">
      <input type="checkbox" class="nav-toggle" id="nav-attendance" <?= in_array($active_panel, ['att-view', 'att-add', 'att-edit']) ? 'checked' : '' ?>>
      <label
        class="nav-btn flex items-center gap-2.5 w-full px-3 py-2 rounded-lg text-[13.5px] font-medium text-[#c9d1d9] hover:bg-[rgba(88,166,255,0.08)] hover:text-white justify-between"
        for="nav-attendance">
        <span class="flex items-center gap-2.5">
          <svg class="w-4 h-4 opacity-75" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path
              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
          </svg>Attendance
        </span>
        <svg class="chevron w-3.5 h-3.5 transition-transform duration-200 text-[#444d56]" fill="none"
          stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path d="M9 18l6-6-6-6" />
        </svg>
      </label>
      <div class="sub-menu pl-9 pb-1">
        <a class="block px-2.5 py-[7px] text-[12.5px] rounded-md cursor-pointer text-[#8b949e] hover:bg-[rgba(88,166,255,0.06)] hover:text-[#c9d1d9] before:content-['·'] before:mr-2 before:text-[#58a6ff] before:text-base <?= $active_panel === 'att-view' ? 'text-[#58a6ff] bg-[rgba(88,166,255,0.1)]' : '' ?>"
          onclick="showPanel('att-view')" href="#">View Attendance</a>
        <a class="block px-2.5 py-[7px] text-[12.5px] rounded-md cursor-pointer text-[#8b949e] hover:bg-[rgba(88,166,255,0.06)] hover:text-[#c9d1d9] before:content-['·'] before:mr-2 before:text-[#58a6ff] before:text-base <?= $active_panel === 'att-add' ? 'text-[#58a6ff] bg-[rgba(88,166,255,0.1)]' : '' ?>"
          onclick="showPanel('att-add')" href="#">Add Attendance</a>
        <a class="block px-2.5 py-[7px] text-[12.5px] rounded-md cursor-pointer text-[#8b949e] hover:bg-[rgba(88,166,255,0.06)] hover:text-[#c9d1d9] before:content-['·'] before:mr-2 before:text-[#58a6ff] before:text-base <?= $active_panel === 'att-edit' ? 'text-[#58a6ff] bg-[rgba(88,166,255,0.1)]' : '' ?>"
          onclick="showPanel('att-edit')" href="#">Edit Attendance</a>
      </div>
    </div>

    <!-- Announcement -->
    <div class="w-full">
      <input type="checkbox" class="nav-toggle" id="nav-ann" <?= in_array($active_panel, ['ann-view', 'ann-add', 'ann-edit']) ? 'checked' : '' ?>>
      <label
        class="nav-btn flex items-center gap-2.5 w-full px-3 py-2 rounded-lg text-[13.5px] font-medium text-[#c9d1d9] hover:bg-[rgba(88,166,255,0.08)] hover:text-white justify-between"
        for="nav-ann">
        <span class="flex items-center gap-2.5">
          <svg class="w-4 h-4 opacity-75" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path
              d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
          </svg>Announcement
        </span>
        <svg class="chevron w-3.5 h-3.5 transition-transform duration-200 text-[#444d56]" fill="none"
          stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path d="M9 18l6-6-6-6" />
        </svg>
      </label>
      <div class="sub-menu pl-9 pb-1">
        <a class="block px-2.5 py-[7px] text-[12.5px] rounded-md cursor-pointer text-[#8b949e] hover:bg-[rgba(88,166,255,0.06)] hover:text-[#c9d1d9] before:content-['·'] before:mr-2 before:text-[#58a6ff] before:text-base <?= $active_panel === 'ann-view' ? 'text-[#58a6ff] bg-[rgba(88,166,255,0.1)]' : '' ?>"
          onclick="showPanel('ann-view')" href="#">View Announcement</a>
        <a class="block px-2.5 py-[7px] text-[12.5px] rounded-md cursor-pointer text-[#8b949e] hover:bg-[rgba(88,166,255,0.06)] hover:text-[#c9d1d9] before:content-['·'] before:mr-2 before:text-[#58a6ff] before:text-base <?= $active_panel === 'ann-add' ? 'text-[#58a6ff] bg-[rgba(88,166,255,0.1)]' : '' ?>"
          onclick="showPanel('ann-add')" href="#">Add Announcement</a>
        <a class="block px-2.5 py-[7px] text-[12.5px] rounded-md cursor-pointer text-[#8b949e] hover:bg-[rgba(88,166,255,0.06)] hover:text-[#c9d1d9] before:content-['·'] before:mr-2 before:text-[#58a6ff] before:text-base <?= $active_panel === 'ann-edit' ? 'text-[#58a6ff] bg-[rgba(88,166,255,0.1)]' : '' ?>"
          onclick="showPanel('ann-edit')" href="#">Edit Announcement</a>
      </div>
    </div>
  </nav>

  <a href="../../logout.php"
    class="flex items-center gap-2.5 px-[18px] py-2.5 text-[#f85149] text-[13.5px] font-medium border-t border-[#21262d] hover:bg-[rgba(248,81,73,0.1)] no-underline">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
    </svg>
    Logout
  </a>
</aside>