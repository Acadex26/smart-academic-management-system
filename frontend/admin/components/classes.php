<?php
// frontend/admin/panels/classes.php
// Variables expected: $classes_list, $all_classes, $timetable_by_class
$add_periods = ['9:30 - 10:30', '10:30 - 11:30', '11:45 - 12:45', '12:45 - 1:45', '2:00 - 3:00', '3:00 - 4:00'];
$add_days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
?>

<!-- ═══════ VIEW CLASS ═══════ -->
<div id="panel-class-view" class="module-panel">
  <div class="mod-card bg-white rounded-2xl p-6 shadow-sm border border-[#e8edf2] mb-5">
    <p class="text-[15px] font-bold text-[#1a202c] m-0 mb-1">All Classes</p>
    <p class="text-[12px] text-[#8b949e] m-0 mb-5">Browse all registered classes. Click "View Class" to see students, or
      "View Timetable" for the schedule.</p>
    <div class="table-scroll">
      <table class="w-full border-collapse text-[13px]">
        <thead>
          <tr>
            <th
              class="bg-[#f8fafc] text-[#64748b] text-[11px] font-semibold tracking-wider uppercase px-3.5 py-2.5 text-left border-b border-[#e2e8f0]">
              #</th>
            <th
              class="bg-[#f8fafc] text-[#64748b] text-[11px] font-semibold tracking-wider uppercase px-3.5 py-2.5 text-left border-b border-[#e2e8f0]">
              Class Name</th>
            <th
              class="bg-[#f8fafc] text-[#64748b] text-[11px] font-semibold tracking-wider uppercase px-3.5 py-2.5 text-left border-b border-[#e2e8f0]">
              Total Students</th>
            <th
              class="bg-[#f8fafc] text-[#64748b] text-[11px] font-semibold tracking-wider uppercase px-3.5 py-2.5 text-left border-b border-[#e2e8f0]">
              Subjects</th>
            <th
              class="bg-[#f8fafc] text-[#64748b] text-[11px] font-semibold tracking-wider uppercase px-3.5 py-2.5 text-left border-b border-[#e2e8f0]">
              Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($classes_list)): ?>
            <tr>
              <td colspan="5" class="text-center text-[#94a3b8] py-8 text-[13px]">No classes added yet.</td>
            </tr>
          <?php else:
            $i = 1;
            foreach ($classes_list as $row): ?>
              <tr class="border-b border-[#f1f5f9] hover:bg-[#f8fafc]">
                <td class="px-3.5 py-3 text-[#94a3b8] font-mono text-xs"><?= str_pad($i++, 2, '0', STR_PAD_LEFT) ?></td>
                <td class="px-3.5 py-3 font-semibold text-[#1a202c]"><?= htmlspecialchars($row['class_name']) ?></td>
                <td class="px-3.5 py-3"><span
                    class="inline-flex items-center text-[10.5px] font-semibold px-2.5 py-0.5 rounded-full bg-[rgba(88,166,255,0.1)] text-[#58a6ff]"><?= (int) $row['total_students'] ?>
                    Students</span></td>
                <td class="px-3.5 py-3 text-[12px] text-[#64748b]">
                  <?= $row['subjects'] ? htmlspecialchars($row['subjects']) : '—' ?>
                </td>
                <td class="px-3.5 py-3">
                  <div class="flex gap-1.5">
                    <button
                      class="inline-flex items-center gap-1.5 text-[12.5px] font-semibold px-2.5 py-1.5 rounded-lg bg-[#f1f5f9] text-[#374151] border border-[#e2e8f0] hover:bg-[#e2e8f0] cursor-pointer"
                      onclick="viewClassDetail('<?= htmlspecialchars($row['class_name'], ENT_QUOTES) ?>')">👁 View
                      Class</button>
                    <button
                      class="inline-flex items-center gap-1.5 text-[12.5px] font-semibold px-2.5 py-1.5 rounded-lg bg-[#f1f5f9] text-[#374151] border border-[#e2e8f0] hover:bg-[#e2e8f0] cursor-pointer"
                      onclick="viewTimetable('<?= htmlspecialchars($row['class_name'], ENT_QUOTES) ?>')">📅
                      Timetable</button>
                  </div>
                </td>
              </tr>
            <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Class Detail Sub-panel -->
  <div id="class-detail-panel" style="display:none;">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#e8edf2] mb-5">
      <div class="flex items-center gap-3 mb-[18px]">
        <button
          class="inline-flex items-center gap-1.5 text-[12.5px] font-semibold text-[#58a6ff] bg-[rgba(88,166,255,0.08)] border border-[rgba(88,166,255,0.15)] px-3 py-1.5 rounded-lg cursor-pointer hover:bg-[rgba(88,166,255,0.15)]"
          onclick="document.getElementById('class-detail-panel').style.display='none'">← Back to Classes</button>
        <span class="text-[14px] font-bold text-[#1a202c]" id="class-detail-name"></span>
      </div>
      <div class="table-scroll">
        <table class="w-full border-collapse text-[13px]">
          <thead>
            <tr>
              <th
                class="bg-[#f8fafc] text-[#64748b] text-[11px] font-semibold tracking-wider uppercase px-3.5 py-2.5 text-left border-b border-[#e2e8f0]">
                Reg. No.</th>
              <th
                class="bg-[#f8fafc] text-[#64748b] text-[11px] font-semibold tracking-wider uppercase px-3.5 py-2.5 text-left border-b border-[#e2e8f0]">
                Name</th>
              <th
                class="bg-[#f8fafc] text-[#64748b] text-[11px] font-semibold tracking-wider uppercase px-3.5 py-2.5 text-left border-b border-[#e2e8f0]">
                Gender</th>
              <th
                class="bg-[#f8fafc] text-[#64748b] text-[11px] font-semibold tracking-wider uppercase px-3.5 py-2.5 text-left border-b border-[#e2e8f0]">
                Date of Birth</th>
              <th
                class="bg-[#f8fafc] text-[#64748b] text-[11px] font-semibold tracking-wider uppercase px-3.5 py-2.5 text-left border-b border-[#e2e8f0]">
                Contact</th>
              <th
                class="bg-[#f8fafc] text-[#64748b] text-[11px] font-semibold tracking-wider uppercase px-3.5 py-2.5 text-left border-b border-[#e2e8f0]">
                Parent/Guardian</th>
              <th
                class="bg-[#f8fafc] text-[#64748b] text-[11px] font-semibold tracking-wider uppercase px-3.5 py-2.5 text-left border-b border-[#e2e8f0]">
                Address</th>
              <th
                class="bg-[#f8fafc] text-[#64748b] text-[11px] font-semibold tracking-wider uppercase px-3.5 py-2.5 text-left border-b border-[#e2e8f0]">
                Blood Group</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Timetable Sub-panel -->
  <div id="timetable-panel" style="display:none;">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#e8edf2] mb-5">
      <div class="flex items-center gap-3 mb-[18px]">
        <button
          class="inline-flex items-center gap-1.5 text-[12.5px] font-semibold text-[#58a6ff] bg-[rgba(88,166,255,0.08)] border border-[rgba(88,166,255,0.15)] px-3 py-1.5 rounded-lg cursor-pointer hover:bg-[rgba(88,166,255,0.15)]"
          onclick="document.getElementById('timetable-panel').style.display='none'">← Back to Classes</button>
        <span class="text-[14px] font-bold text-[#1a202c]">Timetable — <span id="timetable-class-name"></span></span>
        <button
          class="inline-flex items-center gap-1.5 text-[12.5px] font-semibold px-2.5 py-1.5 rounded-lg bg-[#58a6ff] text-white border-none cursor-pointer hover:bg-[#4090e8] ml-auto"
          onclick="openEditTimetableForClass()">✏️ Edit Timetable</button>
      </div>
      <div id="timetable-loading" class="text-center text-[#94a3b8] py-8 text-[13px]">Loading timetable…</div>
      <div class="table-scroll" id="timetable-table-wrap" style="display:none;">
        <table class="w-full border-collapse text-[13px]">
          <thead>
            <tr>
              <th
                class="bg-[#f8fafc] text-[#64748b] text-[11px] font-semibold tracking-wider uppercase px-3.5 py-2.5 text-left border-b border-[#e2e8f0]">
                Period</th>
              <th
                class="bg-[#f8fafc] text-[#64748b] text-[11px] font-semibold tracking-wider uppercase px-3.5 py-2.5 text-left border-b border-[#e2e8f0]">
                Monday</th>
              <th
                class="bg-[#f8fafc] text-[#64748b] text-[11px] font-semibold tracking-wider uppercase px-3.5 py-2.5 text-left border-b border-[#e2e8f0]">
                Tuesday</th>
              <th
                class="bg-[#f8fafc] text-[#64748b] text-[11px] font-semibold tracking-wider uppercase px-3.5 py-2.5 text-left border-b border-[#e2e8f0]">
                Wednesday</th>
              <th
                class="bg-[#f8fafc] text-[#64748b] text-[11px] font-semibold tracking-wider uppercase px-3.5 py-2.5 text-left border-b border-[#e2e8f0]">
                Thursday</th>
              <th
                class="bg-[#f8fafc] text-[#64748b] text-[11px] font-semibold tracking-wider uppercase px-3.5 py-2.5 text-left border-b border-[#e2e8f0]">
                Friday</th>
              <th
                class="bg-[#f8fafc] text-[#64748b] text-[11px] font-semibold tracking-wider uppercase px-3.5 py-2.5 text-left border-b border-[#e2e8f0]">
                Saturday</th>
            </tr>
          </thead>
          <tbody id="timetable-view-tbody"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- ═══════ ADD CLASS ═══════ -->
<div id="panel-class-add" class="module-panel">
  <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#e8edf2] mb-5">
    <p class="text-[15px] font-bold text-[#1a202c] m-0 mb-1">Add New Class</p>
    <p class="text-[12px] text-[#8b949e] m-0 mb-5">Enter class details and define subjects. To add students, use
      <strong>Edit Class</strong> after saving.
    </p>

    <form method="POST" action="../../backend/admin/classes.php" id="add-class-form">
      <input type="hidden" name="action" value="add" />

      <p class="text-[12px] font-bold text-[#475569] tracking-wider uppercase mb-3">① Class Information</p>
      <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="mb-4">
          <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Class Name <span
              class="text-[#f85149]">*</span></label>
          <input type="text" name="class_name"
            class="w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] text-[#1a202c] bg-[#fdfdfd] outline-none focus:border-[#58a6ff] focus:ring-[3px] focus:ring-[rgba(88,166,255,0.08)]"
            placeholder="e.g. Class X - A" required />
        </div>
        <div class="mb-4">
          <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Academic Year <span
              class="text-[#f85149]">*</span></label>
          <input type="text" name="academic_year"
            class="w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] text-[#1a202c] bg-[#fdfdfd] outline-none focus:border-[#58a6ff] focus:ring-[3px] focus:ring-[rgba(88,166,255,0.08)]"
            placeholder="e.g. 2025–2026" required />
        </div>
      </div>

      <hr class="border-none border-t border-[#e8edf2] my-5">

      <p class="text-[12px] font-bold text-[#475569] tracking-wider uppercase mb-3">② Subjects for this Class</p>
      <div
        class="bg-[rgba(88,166,255,0.06)] border border-[rgba(88,166,255,0.2)] rounded-lg px-3.5 py-2.5 text-[12px] text-[#475569] flex items-start gap-2 mb-4">
        <svg class="flex-shrink-0 mt-px" width="14" height="14" fill="none" stroke="#58a6ff" stroke-width="2"
          viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="10" />
          <path d="M12 8v4m0 4h.01" />
        </svg>
        Type a subject name and press Enter or click "+ Add Subject". After saving, go to <strong>Edit Class</strong> to
        add students and set the timetable.
      </div>
      <div id="subject-tags" class="flex flex-wrap gap-2 mt-2">
        <style>
          .subject-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(88, 166, 255, 0.08);
            border: 1px solid rgba(88, 166, 255, 0.2);
            color: #58a6ff;
            font-size: 12px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 6px;
          }

          .subject-tag button {
            background: none;
            border: none;
            color: #58a6ff;
            cursor: pointer;
            font-size: 14px;
            line-height: 1;
            padding: 0;
            opacity: .6;
          }

          .subject-tag button:hover {
            opacity: 1;
          }
        </style>
      </div>
      <div class="flex gap-2 mt-2">
        <input type="text" id="subject-input"
          class="flex-1 px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] text-[#1a202c] bg-[#fdfdfd] outline-none focus:border-[#58a6ff]"
          placeholder="Type subject name…" />
        <button type="button" onclick="addSubject()"
          class="inline-flex items-center gap-1.5 text-[12.5px] font-semibold px-3.5 py-2 rounded-lg bg-[#58a6ff] text-white border-none cursor-pointer hover:bg-[#4090e8]">+
          Add Subject</button>
      </div>
      <div id="subject-hidden-inputs"></div>

      <hr class="border-none border-t border-[#e8edf2] my-5">

      <p class="text-[12px] font-bold text-[#475569] tracking-wider uppercase mb-3">③ Timetable (Optional)</p>
      <div class="table-scroll">
        <table class="w-full border-collapse text-[13px]" id="add-timetable-table">
          <thead>
            <tr>
              <th
                class="bg-[#f8fafc] text-[#64748b] text-[11px] font-semibold tracking-wider uppercase px-3.5 py-2.5 text-left border-b border-[#e2e8f0]">
                Period</th>
              <?php foreach ($add_days as $ad): ?>
                <th
                  class="bg-[#f8fafc] text-[#64748b] text-[11px] font-semibold tracking-wider uppercase px-3.5 py-2.5 text-left border-b border-[#e2e8f0]">
                  <?= htmlspecialchars($ad) ?>
                </th><?php endforeach; ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($add_periods as $ap): ?>
              <tr>
                <td class="px-3.5 py-3 font-bold text-[12px] whitespace-nowrap border-b border-[#f1f5f9]">
                  <?= htmlspecialchars($ap) ?>
                </td>
                <?php foreach ($add_days as $ad): ?>
                  <td class="px-3.5 py-3 border-b border-[#f1f5f9]">
                    <input type="text"
                      name="timetable[<?= htmlspecialchars($ad, ENT_QUOTES) ?>][<?= htmlspecialchars($ap, ENT_QUOTES) ?>]"
                      class="w-full min-w-[90px] px-2 py-1.5 border border-[#e2e8f0] rounded-lg text-[12px] bg-[#fdfdfd] outline-none focus:border-[#58a6ff]"
                      placeholder="—" />
                  </td>
                <?php endforeach; ?>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="flex gap-2.5 justify-end mt-6 pt-5 border-t border-[#e8edf2]">
        <button type="button" onclick="showPanel('class-view')"
          class="inline-flex items-center gap-1.5 text-[12.5px] font-semibold px-3.5 py-[7px] rounded-lg bg-[#f1f5f9] text-[#374151] border border-[#e2e8f0] hover:bg-[#e2e8f0] cursor-pointer">Cancel</button>
        <button type="submit"
          class="inline-flex items-center gap-1.5 text-[12.5px] font-semibold px-3.5 py-[7px] rounded-lg bg-[#58a6ff] text-white border-none cursor-pointer hover:bg-[#4090e8]">💾
          Save Class</button>
      </div>
    </form>
  </div>
</div>

<!-- ═══════ EDIT CLASS ═══════ -->
<div id="panel-class-edit" class="module-panel">
  <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#e8edf2] mb-5">
    <p class="text-[15px] font-bold text-[#1a202c] m-0 mb-1">Edit Class</p>
    <p class="text-[12px] text-[#8b949e] m-0 mb-5">Select a class to modify its details, manage the timetable, or delete
      the class.</p>

    <div class="mb-5 max-w-xs">
      <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Select Class to Edit</label>
      <select id="edit-class-select" name="edit_class_select"
        class="form-select w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] text-[#1a202c] bg-[#fdfdfd] outline-none focus:border-[#58a6ff]"
        onchange="loadEditClass(this.value, this.options[this.selectedIndex])">
        <option value="">— Choose a class —</option>
        <?php foreach ($all_classes as $cls): ?>
          <option value="<?= $cls['id'] ?>" data-name="<?= htmlspecialchars($cls['class_name'], ENT_QUOTES) ?>"
            data-year="<?= htmlspecialchars($cls['academic_year'], ENT_QUOTES) ?>">
            <?= htmlspecialchars($cls['class_name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div id="edit-class-form-wrap" style="display:none;">
      <form method="POST" action="../../backend/admin/classes.php" id="edit-class-form">
        <input type="hidden" name="class_id" id="edit_class_id" value="" />
        <input type="hidden" name="action" value="edit" />

        <div id="delete-confirm-box"
          class="hidden bg-[rgba(248,81,73,0.06)] border border-[rgba(248,81,73,0.2)] rounded-lg px-3.5 py-2.5 text-[12px] text-[#f85149] mb-4">
          ⚠️ Are you sure you want to <strong>delete this class</strong>? This will remove all associated students,
          marks, and attendance records permanently.
          <div class="mt-2 flex gap-2">
            <button type="button" onclick="submitDeleteClass()"
              class="text-[11.5px] font-semibold px-2.5 py-1.5 rounded-lg bg-[rgba(248,81,73,0.1)] text-[#f85149] border-none cursor-pointer hover:bg-[rgba(248,81,73,0.2)]">Yes,
              Delete</button>
            <button type="button" onclick="document.getElementById('delete-confirm-box').classList.add('hidden')"
              class="text-[11.5px] font-semibold px-2.5 py-1.5 rounded-lg bg-[#f1f5f9] text-[#374151] border border-[#e2e8f0] cursor-pointer">Cancel</button>
          </div>
        </div>

        <p class="text-[12px] font-bold text-[#475569] tracking-wider uppercase mb-2.5">① Class Information</p>
        <div class="grid grid-cols-2 gap-4 mb-4">
          <div><label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Class Name</label>
            <input type="text" name="class_name" id="edit-class-name"
              class="w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none focus:border-[#58a6ff]"
              placeholder="Class name" />
          </div>
          <div><label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Academic Year</label>
            <input type="text" name="academic_year" id="edit-academic-year"
              class="w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none focus:border-[#58a6ff]"
              placeholder="e.g. 2025–2026" />
          </div>
        </div>

        <hr class="border-none border-t border-[#e8edf2] my-5">

        <p class="text-[12px] font-bold text-[#475569] tracking-wider uppercase mb-3">② Students in this Class</p>
        <div id="edit-student-list">
          <div id="edit-student-list-inner">
            <div class="text-center text-[#94a3b8] text-[13px] py-6">Select a class above to load students.</div>
          </div>
        </div>
        <!-- New students appended here by JS -->
        <div id="new-student-list"></div>
        <button type="button" onclick="addNewStudentRow()"
          class="inline-flex items-center gap-1.5 text-[12.5px] font-semibold px-3 py-2 rounded-lg bg-[rgba(63,185,80,0.1)] text-[#3fb950] border-none cursor-pointer hover:bg-[rgba(63,185,80,0.2)] mt-3">
          + Add New Student
        </button>

        <hr class="border-none border-t border-[#e8edf2] my-5">

        <p class="text-[12px] font-bold text-[#475569] tracking-wider uppercase mb-3">③ Timetable</p>
        <div class="table-scroll">
          <table class="w-full border-collapse text-[13px]" id="edit-timetable-table">
            <thead>
              <tr>
                <th
                  class="bg-[#f8fafc] text-[#64748b] text-[11px] font-semibold tracking-wider uppercase px-3.5 py-2.5 text-left border-b border-[#e2e8f0]">
                  Period</th>
                <th
                  class="bg-[#f8fafc] text-[#64748b] text-[11px] font-semibold tracking-wider uppercase px-3.5 py-2.5 text-left border-b border-[#e2e8f0]">
                  Monday</th>
                <th
                  class="bg-[#f8fafc] text-[#64748b] text-[11px] font-semibold tracking-wider uppercase px-3.5 py-2.5 text-left border-b border-[#e2e8f0]">
                  Tuesday</th>
                <th
                  class="bg-[#f8fafc] text-[#64748b] text-[11px] font-semibold tracking-wider uppercase px-3.5 py-2.5 text-left border-b border-[#e2e8f0]">
                  Wednesday</th>
                <th
                  class="bg-[#f8fafc] text-[#64748b] text-[11px] font-semibold tracking-wider uppercase px-3.5 py-2.5 text-left border-b border-[#e2e8f0]">
                  Thursday</th>
                <th
                  class="bg-[#f8fafc] text-[#64748b] text-[11px] font-semibold tracking-wider uppercase px-3.5 py-2.5 text-left border-b border-[#e2e8f0]">
                  Friday</th>
                <th
                  class="bg-[#f8fafc] text-[#64748b] text-[11px] font-semibold tracking-wider uppercase px-3.5 py-2.5 text-left border-b border-[#e2e8f0]">
                  Saturday</th>
              </tr>
            </thead>
            <tbody id="edit-timetable-body">
              <tr>
                <td colspan="7" class="text-center text-[#94a3b8] py-5 text-[13px]">Select a class to load timetable.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="flex gap-2.5 justify-end mt-6 pt-5 border-t border-[#e8edf2]">
          <button type="button" onclick="document.getElementById('delete-confirm-box').classList.remove('hidden')"
            class="inline-flex items-center gap-1.5 text-[12.5px] font-semibold px-3.5 py-[7px] rounded-lg bg-[rgba(248,81,73,0.1)] text-[#f85149] border-none cursor-pointer hover:bg-[rgba(248,81,73,0.2)]">🗑
            Delete Class</button>
          <button type="button" onclick="showPanel('class-view')"
            class="inline-flex items-center text-[12.5px] font-semibold px-3.5 py-[7px] rounded-lg bg-[#f1f5f9] text-[#374151] border border-[#e2e8f0] hover:bg-[#e2e8f0] cursor-pointer">Cancel</button>
          <button type="submit"
            class="inline-flex items-center gap-1.5 text-[12.5px] font-semibold px-3.5 py-[7px] rounded-lg bg-[#58a6ff] text-white border-none cursor-pointer hover:bg-[#4090e8]">💾
            Save Changes</button>
        </div>
      </form>
    </div>
  </div>

  <style>
    /* Student entry rows */
    .student-entry {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 10px;
      padding: 16px;
      margin-bottom: 12px;
      position: relative;
    }

    .student-entry-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 12px;
    }

    .student-entry-title {
      font-size: 12.5px;
      font-weight: 700;
      color: #475569;
    }

    .remove-student-btn {
      background: none;
      border: none;
      color: #f85149;
      font-size: 11.5px;
      font-weight: 600;
      cursor: pointer;
      padding: 3px 8px;
      border-radius: 6px;
    }

    .remove-student-btn:hover {
      background: rgba(248, 81, 73, 0.08);
    }

    /* Form shared styles used in JS-generated HTML */
    .form-grid-3 {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: 16px;
    }

    .form-group {
      margin-bottom: 16px;
    }

    .form-label {
      display: block;
      font-size: 12px;
      font-weight: 600;
      color: #374151;
      margin-bottom: 6px;
      letter-spacing: .03em;
    }

    .form-input {
      width: 100%;
      padding: 9px 12px;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      font-size: 13px;
      color: #1a202c;
      font-family: 'Sora', sans-serif;
      outline: none;
      background: #fdfdfd;
    }

    .form-input:focus {
      border-color: #58a6ff;
      background: white;
      box-shadow: 0 0 0 3px rgba(88, 166, 255, 0.08);
    }

    /* Badges used in JS-generated HTML */
    .badge {
      display: inline-flex;
      align-items: center;
      font-size: 10.5px;
      font-weight: 600;
      padding: 3px 10px;
      border-radius: 99px;
    }

    .badge-blue {
      background: rgba(88, 166, 255, 0.1);
      color: #58a6ff;
    }

    .badge-green {
      background: rgba(63, 185, 80, 0.1);
      color: #3fb950;
    }

    .badge-orange {
      background: rgba(210, 153, 34, 0.1);
      color: #d29922;
    }

    .badge-red {
      background: rgba(248, 81, 73, 0.1);
      color: #f85149;
    }

    .badge-purple {
      background: rgba(188, 140, 255, 0.1);
      color: #bc8cff;
    }

    /* Buttons used in JS-generated HTML */
    .btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 12.5px;
      font-weight: 600;
      padding: 7px 14px;
      border-radius: 8px;
      border: none;
      cursor: pointer;
      font-family: 'Sora', sans-serif;
    }

    .btn-primary {
      background: #58a6ff;
      color: white;
    }

    .btn-primary:hover {
      background: #4090e8;
    }

    .btn-secondary {
      background: #f1f5f9;
      color: #374151;
      border: 1px solid #e2e8f0;
    }

    .btn-secondary:hover {
      background: #e2e8f0;
    }

    .btn-danger {
      background: rgba(248, 81, 73, 0.1);
      color: #f85149;
    }

    .btn-danger:hover {
      background: rgba(248, 81, 73, 0.2);
    }

    .btn-green {
      background: rgba(63, 185, 80, 0.1);
      color: #3fb950;
    }

    .btn-green:hover {
      background: rgba(63, 185, 80, 0.2);
    }

    .btn-sm {
      padding: 5px 10px;
      font-size: 11.5px;
    }

    /* Data table used in JS-generated HTML */
    .data-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 13px;
    }

    .data-table th {
      background: #f8fafc;
      color: #64748b;
      font-size: 11px;
      font-weight: 600;
      letter-spacing: .06em;
      text-transform: uppercase;
      padding: 10px 14px;
      text-align: left;
      border-bottom: 1px solid #e2e8f0;
    }

    .data-table td {
      padding: 12px 14px;
      border-bottom: 1px solid #f1f5f9;
      color: #374151;
      vertical-align: middle;
    }

    .data-table tr:last-child td {
      border-bottom: none;
    }

    .data-table tr:hover td {
      background: #f8fafc;
    }
  </style>
</div>