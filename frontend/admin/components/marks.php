<?php
// frontend/admin/panels/marks.php
// Variables expected: $marks_summary, $all_classes
?>

<!-- ═══════ VIEW MARKS ═══════ -->
<div id="panel-marks-view" class="module-panel">
  <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#e8edf2] mb-5">
    <p class="text-[15px] font-bold text-[#1a202c] m-0 mb-1">View Marks</p>
    <p class="text-[12px] text-[#8b949e] m-0 mb-5">Select a class to view student marks, percentage, and grade summary.
    </p>
    <div class="table-scroll">
      <table class="data-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Class Name</th>
            <th>Total Students</th>
            <th>Avg Percentage</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($marks_summary)): ?>
            <tr>
              <td colspan="5" class="text-center text-[#94a3b8] py-8 text-[13px]">No classes or marks found.</td>
            </tr>
          <?php else:
            $mi = 1;
            foreach ($marks_summary as $mrow):
              $avg = $mrow['avg_pct'];
              if ($avg === null) {
                $badge_class = 'badge-orange';
                $avg_label = 'No marks';
              } elseif ($avg >= 75) {
                $badge_class = 'badge-green';
                $avg_label = $avg . '%';
              } elseif ($avg >= 50) {
                $badge_class = 'badge-blue';
                $avg_label = $avg . '%';
              } else {
                $badge_class = 'badge-red';
                $avg_label = $avg . '%';
              }
              ?>
              <tr>
                <td class="font-mono text-[12px] text-[#94a3b8]"><?= str_pad($mi++, 2, '0', STR_PAD_LEFT) ?></td>
                <td class="font-semibold"><?= htmlspecialchars($mrow['class_name']) ?></td>
                <td><?= (int) $mrow['total_students'] ?></td>
                <td><span class="badge <?= $badge_class ?>"><?= $avg_label ?></span></td>
                <td><button class="btn btn-primary btn-sm"
                    onclick="viewMarksDetail('<?= htmlspecialchars($mrow['class_name'], ENT_QUOTES) ?>', <?= (int) $mrow['id'] ?>)">View
                    Marks</button></td>
              </tr>
            <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Marks Detail Sub-panel -->
  <div id="marks-detail-panel" style="display:none;">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#e8edf2] mb-5">
      <div class="flex items-center gap-3 mb-[18px]">
        <button class="btn btn-secondary btn-sm"
          onclick="document.getElementById('marks-detail-panel').style.display='none'">← Back</button>
        <span class="text-[14px] font-bold text-[#1a202c]">Marks — <span id="marks-detail-class"></span></span>
      </div>
      <div class="flex gap-2 mb-[18px]">
        <button id="marks-view-all-btn" class="btn btn-primary btn-sm" onclick="switchMarksView('all')">📋 All Students
          Summary</button>
        <button id="marks-view-student-btn" class="btn btn-secondary btn-sm" onclick="switchMarksView('student')">👤
          Per-Student Breakdown</button>
      </div>

      <!-- All Students Summary -->
      <div id="marks-view-all">
        <div class="table-scroll">
          <table class="data-table" id="marks-detail-table">
            <thead id="marks-detail-thead">
              <tr>
                <th>Reg. No.</th>
                <th>Name</th>
                <th>Grand Total</th>
                <th>Percentage</th>
                <th>Grade</th>
              </tr>
            </thead>
            <tbody id="marks-detail-tbody">
              <tr>
                <td colspan="5" class="text-center text-[#94a3b8] py-8 text-[13px]">Loading marks…</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Per-Student Breakdown -->
      <div id="marks-view-student" style="display:none;">
        <div class="mb-[18px] max-w-xs">
          <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Select Student</label>
          <select id="marks-student-select"
            class="form-select w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none focus:border-[#58a6ff]"
            onchange="loadStudentMarksBreakdown(this.value)">
            <option value="">— Choose a student —</option>
          </select>
        </div>

        <div id="marks-student-summary" class="hidden mb-[18px]">
          <div class="flex gap-3 flex-wrap">
            <div
              class="flex-1 min-w-[120px] bg-[rgba(16,185,129,0.07)] border border-[rgba(16,185,129,0.2)] rounded-xl px-4 py-3">
              <div class="text-[11px] font-bold text-[#94a3b8] uppercase tracking-wider">Grand Total</div>
              <div id="ms-grand-total" class="text-[22px] font-extrabold text-[#10b981] font-mono mt-1">—</div>
            </div>
            <div
              class="flex-1 min-w-[120px] bg-[rgba(59,130,246,0.07)] border border-[rgba(59,130,246,0.2)] rounded-xl px-4 py-3">
              <div class="text-[11px] font-bold text-[#94a3b8] uppercase tracking-wider">Percentage</div>
              <div id="ms-percentage" class="text-[22px] font-extrabold text-[#3b82f6] font-mono mt-1">—</div>
            </div>
            <div
              class="flex-1 min-w-[120px] bg-[rgba(139,92,246,0.07)] border border-[rgba(139,92,246,0.2)] rounded-xl px-4 py-3">
              <div class="text-[11px] font-bold text-[#94a3b8] uppercase tracking-wider">Overall Grade</div>
              <div id="ms-grade" class="text-[26px] font-black text-[#8b5cf6] font-mono mt-1">—</div>
            </div>
            <div
              class="flex-1 min-w-[120px] bg-[rgba(248,81,73,0.07)] border border-[rgba(248,81,73,0.2)] rounded-xl px-4 py-3">
              <div class="text-[11px] font-bold text-[#94a3b8] uppercase tracking-wider">Result</div>
              <div id="ms-result" class="text-[16px] font-extrabold mt-1.5">—</div>
            </div>
          </div>
        </div>

        <div class="table-scroll">
          <table class="data-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Subject</th>
                <th style="color:#3b82f6;">Internal <span class="font-normal opacity-60">/20</span></th>
                <th style="color:#8b5cf6;">External <span class="font-normal opacity-60">/80</span></th>
                <th style="color:#10b981;font-weight:700;">Final <span class="font-normal opacity-60">/100</span></th>
                <th>Grade</th>
                <th>Result</th>
              </tr>
            </thead>
            <tbody id="marks-student-tbody">
              <tr>
                <td colspan="7" class="text-center text-[#94a3b8] py-8 text-[13px]">Select a student above to view their
                  marks.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ═══════ ADD MARKS ═══════ -->
<div id="panel-marks-add" class="module-panel">
  <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#e8edf2] mb-5">
    <p class="text-[15px] font-bold text-[#1a202c] m-0 mb-1">Add Marks</p>
    <p class="text-[12px] text-[#8b949e] m-0 mb-5">Select the class, subject, and exam type, then enter marks for each
      student.</p>

    <form method="POST" action="../../backend/admin/marks.php">
      <input type="hidden" name="action" value="add" />
      <div class="grid grid-cols-3 gap-4 mb-5">
        <div>
          <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Select Class <span
              class="text-[#f85149]">*</span></label>
          <select name="class_id"
            class="form-select w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none"
            required
            onchange="loadSubjectsForSelect('#panel-marks-add select[name=\'subject_id\']',this.value,function(){});loadStudentsForMarksAdd(this.value);">
            <option value="">— Choose class —</option>
            <?php foreach ($all_classes as $cls): ?>
              <option value="<?= $cls['id'] ?>"><?= htmlspecialchars($cls['class_name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Select Subject <span
              class="text-[#f85149]">*</span></label>
          <select name="subject_id" id="marks-add-subject"
            class="form-select w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none"
            required>
            <option value="">— Select class first —</option>
          </select>
        </div>
        <div>
          <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Exam Type <span
              class="text-[#f85149]">*</span></label>
          <select name="exam_type" id="marks-add-exam-type"
            class="form-select w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none"
            required
            onchange="updateMarksAddMaxHint(this.value);loadStudentsForMarksAdd(document.querySelector('#panel-marks-add select[name=\'class_id\']').value);">
            <option value="">— Choose type —</option>
            <option value="internal">Internal (out of 20)</option>
            <option value="external">External (out of 80)</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4 mb-5">
        <div>
          <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Max Marks</label>
          <input type="text" id="marks-add-max-display"
            class="w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[rgba(148,163,184,0.08)] text-[#64748b] cursor-not-allowed"
            readonly value="Select exam type above" />
        </div>
        <div>
          <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Exam Date</label>
          <input type="date" name="exam_date"
            class="w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none" />
        </div>
      </div>

      <hr class="border-none border-t border-[#e8edf2] my-5">
      <p class="text-[12px] font-bold text-[#475569] tracking-wider uppercase mb-3">Enter Marks for Each Student</p>

      <div class="table-scroll">
        <table class="data-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Reg. No.</th>
              <th>Student Name</th>
              <th>Marks Obtained <span class="text-[#f85149]">*</span></th>
              <th>Remarks</th>
            </tr>
          </thead>
          <tbody id="add-marks-tbody">
            <tr id="marks-empty-row">
              <td colspan="5" class="text-center text-[#94a3b8] py-6 text-[13px]">Select class and subject above to load
                students.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex gap-2.5 justify-end mt-6 pt-5 border-t border-[#e8edf2]">
        <button type="button" onclick="showPanel('marks-view')" class="btn btn-secondary">Cancel</button>
        <button type="submit" class="btn btn-primary">💾 Save Marks</button>
      </div>
    </form>
  </div>
</div>

<!-- ═══════ EDIT MARKS ═══════ -->
<div id="panel-marks-edit" class="module-panel">
  <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#e8edf2] mb-5">
    <p class="text-[15px] font-bold text-[#1a202c] m-0 mb-1">Edit Marks</p>
    <p class="text-[12px] text-[#8b949e] m-0 mb-5">Select the class, subject, and exam type to load existing marks for
      editing.</p>

    <form method="POST" action="../../backend/admin/marks.php">
      <input type="hidden" name="action" value="edit" />
      <div class="grid grid-cols-3 gap-4 mb-5">
        <div>
          <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Select Class</label>
          <select name="class_id"
            class="form-select w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none"
            onchange="loadSubjectsForSelect('#panel-marks-edit select[name=\'subject_id\']',this.value,function(){});">
            <option value="">— Choose class —</option>
            <?php foreach ($all_classes as $cls): ?>
              <option value="<?= $cls['id'] ?>"><?= htmlspecialchars($cls['class_name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Select Subject</label>
          <select name="subject_id" id="marks-edit-subject"
            class="form-select w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none">
            <option value="">— Select class first —</option>
          </select>
        </div>
        <div>
          <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Exam Type</label>
          <select name="exam_type"
            class="form-select w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none">
            <option value="">— Choose type —</option>
            <option value="internal">Internal (out of 20)</option>
            <option value="external">External (out of 80)</option>
          </select>
        </div>
      </div>

      <div
        class="bg-[rgba(88,166,255,0.06)] border border-[rgba(88,166,255,0.2)] rounded-lg px-3.5 py-2.5 text-[12px] text-[#475569] flex items-start gap-2 mb-4">
        <svg class="flex-shrink-0 mt-px" width="14" height="14" fill="none" stroke="#58a6ff" stroke-width="2"
          viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="10" />
          <path d="M12 8v4m0 4h.01" />
        </svg>
        Previously entered marks will be pre-filled. Modify and save to update records.
      </div>

      <div class="table-scroll">
        <table class="data-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Reg. No.</th>
              <th>Student Name</th>
              <th>Max Marks</th>
              <th>Marks Obtained</th>
              <th>Remarks</th>
            </tr>
          </thead>
          <tbody id="edit-marks-tbody">
            <tr id="edit-marks-empty-row">
              <td colspan="6" class="text-center text-[#94a3b8] py-6 text-[13px]">Select class, subject, and exam type
                above to load marks.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex gap-2.5 justify-end mt-6 pt-5 border-t border-[#e8edf2]">
        <button type="button" onclick="showPanel('marks-view')" class="btn btn-secondary">Cancel</button>
        <button type="submit" class="btn btn-primary">💾 Update Marks</button>
      </div>
    </form>
  </div>
</div>