<?php
// frontend/admin/panels/attendance.php
// Variables expected: $all_classes
?>

<!-- ═══════ VIEW ATTENDANCE ═══════ -->
<div id="panel-att-view" class="module-panel">
  <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#e8edf2] mb-5">
    <p class="text-[15px] font-bold text-[#1a202c] m-0 mb-1">View Attendance</p>
    <p class="text-[12px] text-[#8b949e] m-0 mb-5">Select a class and then a student to view their full attendance
      record.</p>

    <div class="grid grid-cols-2 gap-4 mb-5">
      <div>
        <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Select Class</label>
        <select name="att_view_class"
          class="form-select w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none"
          onchange="loadAttStudents(this.value)">
          <option value="">— Choose a class —</option>
          <?php foreach ($all_classes as $cls): ?>
            <option value="<?= $cls['id'] ?>"><?= htmlspecialchars($cls['class_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Select Student</label>
        <select id="att-student-select" name="att_view_student"
          class="form-select w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none"
          onchange="loadStudentAttendance(this.value)">
          <option value="">— Select class first —</option>
        </select>
      </div>
    </div>

    <!-- Attendance Record Sub-panel -->
    <div id="att-record-panel" style="display:none;">
      <hr class="border-none border-t border-[#e8edf2] my-5">
      <div class="flex justify-between items-center mb-3.5">
        <p class="text-[13px] font-bold text-[#1a202c] m-0">Attendance Record — <span id="att-student-name"></span></p>
        <div class="flex gap-2">
          <span class="badge badge-green">Present: <strong id="att-present-count">0</strong></span>
          <span class="badge badge-red">Absent: <strong id="att-absent-count">0</strong></span>
          <span class="badge badge-blue">Total: <strong id="att-total-count">0</strong></span>
        </div>
      </div>
      <div class="table-scroll">
        <table class="data-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Subject</th>
              <th>Status</th>
              <th>Marked By</th>
            </tr>
          </thead>
          <tbody id="att-record-body">
            <tr>
              <td colspan="4" class="text-center text-[#94a3b8] py-6 text-[13px]">Select a student above to view their
                attendance.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- ═══════ ADD ATTENDANCE ═══════ -->
<div id="panel-att-add" class="module-panel">
  <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#e8edf2] mb-5">
    <p class="text-[15px] font-bold text-[#1a202c] m-0 mb-1">Add Attendance</p>
    <p class="text-[12px] text-[#8b949e] m-0 mb-5">Select class, subject, and date, then mark attendance for all
      students.</p>

    <form method="POST" action="../../backend/admin/attendance.php">
      <input type="hidden" name="action" value="add" />
      <div class="grid grid-cols-3 gap-4 mb-5">
        <div>
          <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Select Class <span
              class="text-[#f85149]">*</span></label>
          <select name="class_id"
            class="form-select w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none"
            required
            onchange="loadSubjectsForSelect('#panel-att-add select[name=\'subject_id\']',this.value,function(){});loadStudentsForAttAdd(this.value);">
            <option value="">— Choose class —</option>
            <?php foreach ($all_classes as $cls): ?>
              <option value="<?= $cls['id'] ?>"><?= htmlspecialchars($cls['class_name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Select Subject <span
              class="text-[#f85149]">*</span></label>
          <select name="subject_id" id="att-add-subject"
            class="form-select w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none"
            required>
            <option value="">— Select class first —</option>
          </select>
        </div>
        <div>
          <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Date <span
              class="text-[#f85149]">*</span></label>
          <input type="date" name="att_date"
            class="w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none"
            required />
        </div>
      </div>

      <hr class="border-none border-t border-[#e8edf2] my-5">
      <div class="flex justify-between items-center mb-3">
        <p class="text-[12px] font-bold text-[#475569] tracking-wider uppercase m-0">Mark Attendance</p>
        <div class="flex gap-2">
          <button type="button" onclick="markAllAttendance('present')" class="btn btn-green btn-sm">✓ Mark All
            Present</button>
          <button type="button" onclick="markAllAttendance('absent')" class="btn btn-danger btn-sm">✗ Mark All
            Absent</button>
        </div>
      </div>

      <div class="table-scroll">
        <table class="data-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Reg. No.</th>
              <th>Student Name</th>
              <th class="text-center">Present</th>
              <th class="text-center">Absent</th>
            </tr>
          </thead>
          <tbody id="att-add-tbody">
            <tr id="att-add-empty-row">
              <td colspan="5" class="text-center text-[#94a3b8] py-6 text-[13px]">Select class and subject above to load
                students.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex gap-2.5 justify-end mt-6 pt-5 border-t border-[#e8edf2]">
        <button type="button" onclick="showPanel('att-view')" class="btn btn-secondary">Cancel</button>
        <button type="submit" class="btn btn-primary">💾 Save Attendance</button>
      </div>
    </form>
  </div>
</div>

<!-- ═══════ EDIT ATTENDANCE ═══════ -->
<div id="panel-att-edit" class="module-panel">
  <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#e8edf2] mb-5">
    <p class="text-[15px] font-bold text-[#1a202c] m-0 mb-1">Edit Attendance</p>
    <p class="text-[12px] text-[#8b949e] m-0 mb-5">Select the class, subject, and date to load and modify existing
      attendance records.</p>

    <form method="POST" action="../../backend/admin/attendance.php">
      <input type="hidden" name="action" value="edit" />
      <div class="grid grid-cols-3 gap-4 mb-5">
        <div>
          <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Select Class</label>
          <select name="class_id"
            class="form-select w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none"
            onchange="loadSubjectsForSelect('#panel-att-edit select[name=\'subject_id\']',this.value,function(){});">
            <option value="">— Choose class —</option>
            <?php foreach ($all_classes as $cls): ?>
              <option value="<?= $cls['id'] ?>"><?= htmlspecialchars($cls['class_name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Select Subject</label>
          <select name="subject_id" id="att-edit-subject"
            class="form-select w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none">
            <option value="">— Select class first —</option>
          </select>
        </div>
        <div>
          <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Select Date</label>
          <input type="date" name="att_date"
            class="w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none" />
        </div>
      </div>

      <div
        class="bg-[rgba(88,166,255,0.06)] border border-[rgba(88,166,255,0.2)] rounded-lg px-3.5 py-2.5 text-[12px] text-[#475569] flex items-start gap-2 mb-4">
        <svg class="flex-shrink-0 mt-px" width="14" height="14" fill="none" stroke="#58a6ff" stroke-width="2"
          viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="10" />
          <path d="M12 8v4m0 4h.01" />
        </svg>
        Existing attendance will be loaded. Modify status and save, or delete the entire record for this date.
      </div>

      <div class="flex justify-between items-center mb-3">
        <p class="text-[12px] font-bold text-[#475569] tracking-wider uppercase m-0">Edit / Delete Record</p>
        <div class="flex gap-2">
          <button type="button" onclick="markAllAttendanceEdit('present')" class="btn btn-green btn-sm">✓ All
            Present</button>
          <button type="button" onclick="markAllAttendanceEdit('absent')" class="btn btn-danger btn-sm">✗ All
            Absent</button>
        </div>
      </div>

      <div class="table-scroll">
        <table class="data-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Reg. No.</th>
              <th>Student Name</th>
              <th class="text-center">Present</th>
              <th class="text-center">Absent</th>
              <th class="text-center">Delete Record</th>
            </tr>
          </thead>
          <tbody>
            <tr id="att-edit-empty-row">
              <td colspan="6" class="text-center text-[#94a3b8] py-6 text-[13px]">Select class, subject, and date above
                to load records.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex gap-2.5 justify-end mt-6 pt-5 border-t border-[#e8edf2]">
        <button type="button" onclick="deleteAllAttendance()" class="btn btn-danger">🗑 Delete All Records for This
          Date</button>
        <button type="button" onclick="showPanel('att-view')" class="btn btn-secondary">Cancel</button>
        <button type="submit" class="btn btn-primary">💾 Update Attendance</button>
      </div>
    </form>
  </div>
</div>