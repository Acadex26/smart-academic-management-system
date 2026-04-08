<?php
// frontend/admin/panels/students.php
// Variables expected: $all_classes, $all_students_view (array)
$all_students_view = getRows(
  $conn,
  "SELECT s.id, s.reg_number, s.name, s.gender, s.contact, s.guardian_name,
          s.blood_group, c.class_name, c.id AS class_id
   FROM students s JOIN classes c ON c.id = s.class_id
   ORDER BY c.class_name, s.name ASC"
);
?>

<!-- ═══════ VIEW STUDENTS ═══════ -->
<div id="panel-student-view" class="module-panel">
  <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#e8edf2] mb-5">
    <div class="flex justify-between items-center mb-5">
      <div>
        <p class="text-[15px] font-bold text-[#1a202c] m-0 mb-1">All Students</p>
        <p class="text-[12px] text-[#8b949e] m-0">Browse all registered students across all classes.</p>
      </div>
      <button onclick="showPanel('student-add')" class="btn btn-primary">+ Add Student</button>
    </div>

    <div class="mb-[18px] max-w-[280px]">
      <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Filter by Class</label>
      <select
        class="form-select w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none"
        id="student-view-class-filter" onchange="filterStudentView(this.value)">
        <option value="">— All Classes —</option>
        <?php foreach ($all_classes as $cls): ?>
          <option value="<?= $cls['id'] ?>"><?= htmlspecialchars($cls['class_name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="table-scroll">
      <table class="data-table" id="student-view-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Reg. No.</th>
            <th>Name</th>
            <th>Class</th>
            <th>Gender</th>
            <th>Contact</th>
            <th>Guardian</th>
            <th>Blood Group</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="student-view-tbody">
          <?php if (empty($all_students_view)): ?>
            <tr>
              <td colspan="9" class="text-center text-[#94a3b8] py-8 text-[13px]">No students added yet. <a href="#"
                  onclick="showPanel('student-add');return false;" class="text-[#58a6ff] font-semibold">Add your first
                  student →</a></td>
            </tr>
          <?php else:
            $sv_i = 1;
            foreach ($all_students_view as $svr): ?>
              <tr data-class-id="<?= $svr['class_id'] ?>">
                <td class="text-[12px] text-[#94a3b8] font-mono"><?= str_pad($sv_i++, 2, '0', STR_PAD_LEFT) ?></td>
                <td class="font-mono text-[12px]"><?= htmlspecialchars($svr['reg_number']) ?></td>
                <td class="font-semibold"><?= htmlspecialchars($svr['name']) ?></td>
                <td><span class="badge badge-blue"><?= htmlspecialchars($svr['class_name']) ?></span></td>
                <td><?= htmlspecialchars($svr['gender'] ?: '—') ?></td>
                <td><?= htmlspecialchars($svr['contact'] ?: '—') ?></td>
                <td><?= htmlspecialchars($svr['guardian_name'] ?: '—') ?></td>
                <td>
                  <?= $svr['blood_group'] ? '<span class="badge badge-red">' . htmlspecialchars($svr['blood_group']) . '</span>' : '—' ?>
                </td>
                <td><button class="btn btn-secondary btn-sm" onclick="openEditStudentById(<?= (int) $svr['id'] ?>)">✏️
                    Edit</button></td>
              </tr>
            <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- ═══════ ADD STUDENT ═══════ -->
<div id="panel-student-add" class="module-panel">
  <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#e8edf2] mb-5">
    <p class="text-[15px] font-bold text-[#1a202c] m-0 mb-1">Add Student</p>
    <p class="text-[12px] text-[#8b949e] m-0 mb-5">Select a class and fill in student details. Registration number is
      auto-assigned on save.</p>

    <?php if (isset($_GET['saved']) && ($_GET['panel'] ?? '') === 'student-add'): ?>
      <div
        class="bg-[rgba(63,185,80,0.08)] border border-[rgba(63,185,80,0.25)] rounded-lg px-3.5 py-2.5 text-[12.5px] text-[#3fb950] mb-4">
        ✅ Student added successfully! Registration number auto-assigned.
      </div>
    <?php endif; ?>

    <form method="POST" action="../../backend/admin/classes.php" id="add-student-form">
      <input type="hidden" name="action" value="edit" />
      <input type="hidden" name="class_id" id="add-student-class-id" value="" />
      <input type="hidden" name="class_name" id="add-student-class-name" value="" />
      <input type="hidden" name="academic_year" value="" />

      <div class="mb-5 max-w-xs">
        <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Select Class <span
            class="text-[#f85149]">*</span></label>
        <select
          class="form-select w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none"
          id="add-student-class-sel"
          onchange="document.getElementById('add-student-class-id').value=this.value; var opt=this.options[this.selectedIndex]; document.getElementById('add-student-class-name').value=opt.dataset.name||'';"
          required>
          <option value="">— Choose a class —</option>
          <?php foreach ($all_classes as $cls): ?>
            <option value="<?= $cls['id'] ?>" data-name="<?= htmlspecialchars($cls['class_name'], ENT_QUOTES) ?>"
              data-year="<?= htmlspecialchars($cls['academic_year'], ENT_QUOTES) ?>">
              <?= htmlspecialchars($cls['class_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <p class="text-[11px] text-[#94a3b8] mt-1">The student will be enrolled in this class.</p>
      </div>

      <hr class="border-none border-t border-[#e8edf2] my-5">
      <p class="text-[12px] font-bold text-[#475569] tracking-wider uppercase mb-3">Student Details</p>

      <div
        class="bg-[rgba(88,166,255,0.06)] border border-[rgba(88,166,255,0.2)] rounded-lg px-3.5 py-2.5 text-[12px] text-[#475569] flex items-start gap-2 mb-4">
        <svg class="flex-shrink-0 mt-px" width="14" height="14" fill="none" stroke="#58a6ff" stroke-width="2"
          viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="10" />
          <path d="M12 8v4m0 4h.01" />
        </svg>
        Registration number is <strong>automatically assigned</strong> by the system when you save. Do not enter it
        manually.
      </div>

      <div class="grid grid-cols-3 gap-4">
        <div class="mb-4"><label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Full Name <span
              class="text-[#f85149]">*</span></label><input type="text" name="new_students[0][name]"
            class="w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none focus:border-[#58a6ff]"
            placeholder="Student full name" required /></div>
        <div class="mb-4"><label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Gender</label><select
            name="new_students[0][gender]"
            class="form-select w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none">
            <option value="">Select</option>
            <option>Male</option>
            <option>Female</option>
            <option>Other</option>
          </select></div>
        <div class="mb-4"><label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Date of
            Birth</label><input type="date" name="new_students[0][dob]"
            class="w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none" /></div>
        <div class="mb-4"><label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Contact
            Number</label><input type="tel" name="new_students[0][contact]"
            class="w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none"
            placeholder="10-digit mobile" /></div>
        <div class="mb-4"><label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Blood
            Group</label><select name="new_students[0][blood_group]"
            class="form-select w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none">
            <option value="">Select</option>
            <option>A+</option>
            <option>A−</option>
            <option>B+</option>
            <option>B−</option>
            <option>O+</option>
            <option>O−</option>
            <option>AB+</option>
            <option>AB−</option>
          </select></div>
        <div class="mb-4"><label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Email
            Address</label><input type="email" name="new_students[0][email]"
            class="w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none"
            placeholder="student@email.com" /></div>
        <div class="mb-4"><label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Guardian
            Name</label><input type="text" name="new_students[0][guardian_name]"
            class="w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none"
            placeholder="Parent or guardian name" /></div>
        <div class="mb-4"><label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Guardian
            Contact</label><input type="tel" name="new_students[0][guardian_contact]"
            class="w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none"
            placeholder="Parent mobile number" /></div>
      </div>
      <div class="mb-4"><label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Residential
          Address</label><input type="text" name="new_students[0][address]"
          class="w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none"
          placeholder="Full residential address" /></div>

      <div class="flex gap-2.5 justify-end mt-6 pt-5 border-t border-[#e8edf2]">
        <button type="button" onclick="showPanel('student-view')" class="btn btn-secondary">Cancel</button>
        <button type="submit" class="btn btn-primary" onclick="return validateAddStudent()">💾 Add Student</button>
      </div>
    </form>
  </div>
</div>

<!-- ═══════ EDIT STUDENT ═══════ -->
<div id="panel-student-edit" class="module-panel">
  <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#e8edf2] mb-5">
    <p class="text-[15px] font-bold text-[#1a202c] m-0 mb-1">Edit Student</p>
    <p class="text-[12px] text-[#8b949e] m-0 mb-5">Select a class, then choose a student to edit their details.</p>

    <?php if (isset($_GET['updated']) && ($_GET['panel'] ?? '') === 'student-edit'): ?>
      <div
        class="bg-[rgba(63,185,80,0.08)] border border-[rgba(63,185,80,0.25)] rounded-lg px-3.5 py-2.5 text-[12.5px] text-[#3fb950] mb-4">
        ✅ Student details updated successfully!
      </div>
    <?php endif; ?>

    <div class="grid grid-cols-2 gap-4 mb-5">
      <div>
        <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Select Class</label>
        <select
          class="form-select w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none"
          id="edit-student-class-sel"
          onchange="loadEditStudentDropdown(this.value); var opt=this.options[this.selectedIndex]; document.getElementById('edit-student-class-name-hidden').value=opt.dataset.name||''; document.getElementById('edit-student-academic-year-hidden').value=opt.dataset.year||'';">
          <option value="">— Choose a class —</option>
          <?php foreach ($all_classes as $cls): ?>
            <option value="<?= $cls['id'] ?>" data-name="<?= htmlspecialchars($cls['class_name'], ENT_QUOTES) ?>"
              data-year="<?= htmlspecialchars($cls['academic_year'], ENT_QUOTES) ?>">
              <?= htmlspecialchars($cls['class_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Select Student</label>
        <select
          class="form-select w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none"
          id="edit-student-sel" onchange="loadEditStudentForm(this.value)">
          <option value="">— Select class first —</option>
        </select>
      </div>
    </div>

    <div id="edit-student-form-wrap" style="display:none;">
      <form method="POST" action="../../backend/admin/classes.php" id="edit-student-form-inner">
        <input type="hidden" name="action" value="edit" />
        <input type="hidden" name="class_id" id="edit-student-class-id-hidden" value="" />
        <input type="hidden" name="class_name" id="edit-student-class-name-hidden" value="" />
        <input type="hidden" name="academic_year" id="edit-student-academic-year-hidden" value="" />

        <hr class="border-none border-t border-[#e8edf2] my-5">
        <p class="text-[12px] font-bold text-[#475569] tracking-wider uppercase mb-3">Student Details</p>

        <div class="grid grid-cols-3 gap-4" id="edit-student-fields-inner">
          <!-- Dynamically filled by JS -->
        </div>
        <div class="mb-4" id="edit-student-address-row">
          <!-- Address filled by JS -->
        </div>

        <div class="flex gap-2.5 justify-end mt-6 pt-5 border-t border-[#e8edf2]">
          <button type="button" onclick="showPanel('student-view')" class="btn btn-secondary">Cancel</button>
          <button type="submit" class="btn btn-primary">💾 Save Changes</button>
        </div>
      </form>
    </div>

    <div id="edit-student-placeholder" class="text-center text-[#94a3b8] text-[13px] py-8">
      Select a class and student above to edit their details.
    </div>
  </div>
</div>