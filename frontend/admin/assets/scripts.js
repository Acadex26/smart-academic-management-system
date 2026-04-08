// frontend/admin/partials/scripts.js
// All JavaScript for the Admin Dashboard

function escHtml(s) {
  return String(s)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}

// ── Panel Switcher ──────────────────────────────────────────────────
const panelMeta = {
  dashboard: {
    heading: "Welcome, " + ADMIN_NAME + " 👋",
    crumb: "SCMS / dashboard",
  },
  "class-view": { heading: "View Classes", crumb: "SCMS / classes / view" },
  "class-add": { heading: "Add New Class", crumb: "SCMS / classes / add" },
  "class-edit": { heading: "Edit Class", crumb: "SCMS / classes / edit" },
  "marks-view": { heading: "View Marks", crumb: "SCMS / marks / view" },
  "marks-add": { heading: "Add Marks", crumb: "SCMS / marks / add" },
  "marks-edit": { heading: "Edit Marks", crumb: "SCMS / marks / edit" },
  "att-view": { heading: "View Attendance", crumb: "SCMS / attendance / view" },
  "att-add": { heading: "Add Attendance", crumb: "SCMS / attendance / add" },
  "att-edit": { heading: "Edit Attendance", crumb: "SCMS / attendance / edit" },
  "ann-view": {
    heading: "View Announcements",
    crumb: "SCMS / announcements / view",
  },
  "ann-add": {
    heading: "Add Announcement",
    crumb: "SCMS / announcements / add",
  },
  "ann-edit": {
    heading: "Edit Announcement",
    crumb: "SCMS / announcements / edit",
  },
  "student-view": { heading: "View Students", crumb: "SCMS / students / view" },
  "student-add": { heading: "Add Student", crumb: "SCMS / students / add" },
  "student-edit": { heading: "Edit Student", crumb: "SCMS / students / edit" },
};

function showPanel(panelId) {
  document
    .querySelectorAll(".module-panel")
    .forEach((p) => p.classList.remove("active"));
  const target = document.getElementById("panel-" + panelId);
  if (target) target.classList.add("active");
  const meta = panelMeta[panelId];
  if (meta) {
    document.getElementById("page-heading").textContent = meta.heading;
    document.getElementById("page-breadcrumb").textContent = meta.crumb;
  }
  [
    "class-detail-panel",
    "timetable-panel",
    "marks-detail-panel",
    "att-record-panel",
  ].forEach((sp) => {
    const el = document.getElementById(sp);
    if (el) el.style.display = "none";
  });
  window.scrollTo({ top: 0, behavior: "smooth" });
}

if (typeof INITIAL_PANEL !== "undefined" && INITIAL_PANEL !== "dashboard")
  showPanel(INITIAL_PANEL);

// ── Calendar ─────────────────────────────────────────────────────────
let currentDate = new Date();
function renderCalendar() {
  const monthYear = document.getElementById("monthYear");
  const calendarDates = document.getElementById("calendarDates");
  if (!monthYear || !calendarDates) return;
  const year = currentDate.getFullYear(),
    month = currentDate.getMonth();
  const firstDay = new Date(year, month, 1).getDay();
  const lastDate = new Date(year, month + 1, 0).getDate();
  monthYear.innerText = currentDate.toLocaleString("default", {
    month: "long",
    year: "numeric",
  });
  calendarDates.innerHTML = "";
  for (let i = 0; i < firstDay; i++) calendarDates.innerHTML += "<div></div>";
  const today = new Date();
  for (let i = 1; i <= lastDate; i++) {
    const isToday =
      i === today.getDate() &&
      month === today.getMonth() &&
      year === today.getFullYear();
    calendarDates.innerHTML += `<div class="${isToday ? "today" : ""}">${i}</div>`;
  }
}
function prevMonth() {
  currentDate.setMonth(currentDate.getMonth() - 1);
  renderCalendar();
}
function nextMonth() {
  currentDate.setMonth(currentDate.getMonth() + 1);
  renderCalendar();
}
renderCalendar();

// ── CLASS VIEW ─────────────────────────────────────────────────────────
function viewClassDetail(className) {
  document.getElementById("class-detail-name").textContent = className;
  document.getElementById("timetable-panel").style.display = "none";
  const tbody = document.querySelector("#class-detail-panel .data-table tbody");
  const list = studentsByClass[className] || [];
  if (!list.length) {
    tbody.innerHTML =
      '<tr><td colspan="8" style="text-align:center;color:#94a3b8;padding:32px;font-size:13px;">No students in this class yet.</td></tr>';
  } else {
    tbody.innerHTML = list
      .map(
        (s) => `
      <tr>
        <td style="font-family:'JetBrains Mono',monospace;font-size:12px;">${s.reg_number}</td>
        <td style="font-weight:600;">${s.name}</td>
        <td>${s.gender || "—"}</td>
        <td>${s.dob || "—"}</td>
        <td>${s.contact || "—"}</td>
        <td>${s.guardian_name || "—"}</td>
        <td>${s.address || "—"}</td>
        <td>${s.blood_group ? '<span class="badge badge-red">' + s.blood_group + "</span>" : "—"}</td>
      </tr>`,
      )
      .join("");
  }
  document.getElementById("class-detail-panel").style.display = "block";
}

let _currentTimetableClassId = null;
let _currentTimetableClassName = null;

function viewTimetable(className) {
  document.getElementById("class-detail-panel").style.display = "none";
  const classId = classIdMap[className];
  _currentTimetableClassId = classId;
  _currentTimetableClassName = className;
  const loading = document.getElementById("timetable-loading");
  const tableWrap = document.getElementById("timetable-table-wrap");
  const tbody = document.getElementById("timetable-view-tbody");
  const nameSpan = document.getElementById("timetable-class-name");
  nameSpan.textContent = className;
  tbody.innerHTML = "";
  tableWrap.style.display = "none";
  loading.style.display = "block";
  loading.style.color = "#94a3b8";
  loading.textContent = "Loading timetable…";
  document.getElementById("timetable-panel").style.display = "block";
  if (!classId) {
    loading.textContent = "⚠ Class ID not found.";
    loading.style.color = "#f85149";
    return;
  }

  fetch("../../api/data_fetch.php?fetch=timetable&class_id=" + classId)
    .then((r) => {
      if (!r.ok) throw new Error("HTTP " + r.status);
      return r.json();
    })
    .then((data) => {
      loading.style.display = "none";
      tableWrap.style.display = "block";
      const schedule = [
        { type: "period", label: "9:30 - 10:30", note: "Period 1" },
        { type: "period", label: "10:30 - 11:30", note: "Period 2" },
        { type: "break", label: "11:30 - 11:45", note: "☕ Break" },
        { type: "period", label: "11:45 - 12:45", note: "Period 3" },
        { type: "period", label: "12:45 - 1:45", note: "Period 4" },
        { type: "break", label: "1:45 - 2:00", note: "🍽 Lunch" },
        { type: "period", label: "2:00 - 3:00", note: "Period 5" },
        { type: "period", label: "3:00 - 4:00", note: "Period 6" },
      ];
      const days = [
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday",
        "Saturday",
      ];
      const badgeCols = [
        "badge-blue",
        "badge-green",
        "badge-orange",
        "badge-purple",
        "badge-red",
        "badge-blue",
      ];
      const periods = schedule
        .filter((x) => x.type === "period")
        .map((x) => x.label);
      let hasAny = false;
      days.forEach((d) =>
        periods.forEach((p) => {
          if (data[d] && data[d][p]) hasAny = true;
        }),
      );
      if (!data || !hasAny) {
        tableWrap.style.display = "none";
        loading.style.display = "block";
        loading.innerHTML =
          'No timetable set for this class yet. Click <strong style="color:#58a6ff;">✏️ Edit Timetable</strong> above to add one.';
        return;
      }
      tbody.innerHTML = schedule
        .map((slot) => {
          if (slot.type === "break") {
            return `<tr style="background:#f8fafc;"><td style="font-weight:700;font-size:12px;white-space:nowrap;color:#94a3b8;font-style:italic;">${escHtml(slot.note)}<br><span style="font-size:10px;font-weight:400;">${escHtml(slot.label)}</span></td>${days.map(() => `<td style="text-align:center;color:#cbd5e1;font-size:11px;font-style:italic;">—</td>`).join("")}</tr>`;
          }
          const cells = days
            .map((d, di) => {
              const subj =
                data[d] && data[d][slot.label] ? data[d][slot.label] : "";
              return subj
                ? `<td><span class="badge ${badgeCols[di % badgeCols.length]}">${escHtml(subj)}</span></td>`
                : `<td style="color:#94a3b8;font-size:12px;">—</td>`;
            })
            .join("");
          return `<tr><td style="font-weight:700;font-size:12px;white-space:nowrap;">${escHtml(slot.note)}<br><span style="font-size:10px;font-weight:400;color:#64748b;">${escHtml(slot.label)}</span></td>${cells}</tr>`;
        })
        .join("");
    })
    .catch(() => {
      loading.style.display = "block";
      tableWrap.style.display = "none";
      loading.textContent = "Error loading timetable. Please try again.";
      loading.style.color = "#f85149";
    });
}

function openEditTimetableForClass() {
  if (!_currentTimetableClassId) return;
  document.getElementById("timetable-panel").style.display = "none";
  showPanel("class-edit");
  const sel = document.getElementById("edit-class-select");
  sel.value = _currentTimetableClassId;
  const opt = sel.options[sel.selectedIndex];
  loadEditClass(_currentTimetableClassId, opt);
  setTimeout(() => {
    const ttTable = document.getElementById("edit-timetable-table");
    if (ttTable) ttTable.scrollIntoView({ behavior: "smooth", block: "start" });
  }, 600);
}

// ── MARKS ─────────────────────────────────────────────────────────────
let _marksDetailClassId = null;
let _marksDetailData = null;

function switchMarksView(mode) {
  const allDiv = document.getElementById("marks-view-all");
  const stuDiv = document.getElementById("marks-view-student");
  const allBtn = document.getElementById("marks-view-all-btn");
  const stuBtn = document.getElementById("marks-view-student-btn");
  if (mode === "all") {
    allDiv.style.display = "block";
    stuDiv.style.display = "none";
    allBtn.className = "btn btn-primary btn-sm";
    stuBtn.className = "btn btn-secondary btn-sm";
  } else {
    allDiv.style.display = "none";
    stuDiv.style.display = "block";
    allBtn.className = "btn btn-secondary btn-sm";
    stuBtn.className = "btn btn-primary btn-sm";
  }
}

function viewMarksDetail(className, classId) {
  _marksDetailClassId = classId;
  document.getElementById("marks-detail-class").textContent = className;
  document.getElementById("marks-detail-panel").style.display = "block";
  switchMarksView("all");
  const thead = document.getElementById("marks-detail-thead");
  const tbody = document.getElementById("marks-detail-tbody");
  tbody.innerHTML =
    '<tr><td colspan="5" style="text-align:center;padding:24px;color:#94a3b8;">Loading…</td></tr>';
  document.getElementById("marks-student-select").innerHTML =
    '<option value="">— Choose a student —</option>';
  document.getElementById("marks-student-tbody").innerHTML =
    '<tr><td colspan="7" style="text-align:center;color:#94a3b8;padding:28px;font-size:13px;">Select a student above to view their marks.</td></tr>';
  document.getElementById("marks-student-summary").style.display = "none";

  fetch("../../api/data_fetch.php?fetch=marks_detail&class_id=" + classId)
    .then((r) => r.json())
    .then((data) => {
      _marksDetailData = data;
      if (!data.rows || !data.rows.length) {
        thead.innerHTML =
          "<tr><th>Reg. No.</th><th>Name</th><th>Grand Total</th><th>Percentage</th><th>Grade</th></tr>";
        tbody.innerHTML =
          '<tr><td colspan="5" style="text-align:center;color:#94a3b8;padding:28px;font-size:13px;">No marks recorded for this class yet.</td></tr>';
        return;
      }
      thead.innerHTML = `<tr><th>Reg. No.</th><th>Name</th><th>Grand Total</th><th>Percentage</th><th>Grade</th></tr>`;
      tbody.innerHTML = data.rows
        .map((r) => {
          const pct = r.percentage !== null ? r.percentage : null;
          const grade =
            pct === null
              ? "—"
              : pct >= 75
                ? "A"
                : pct >= 60
                  ? "B"
                  : pct >= 45
                    ? "C"
                    : "D";
          const bCls =
            pct === null
              ? "badge-orange"
              : pct >= 75
                ? "badge-green"
                : pct >= 60
                  ? "badge-blue"
                  : pct >= 45
                    ? "badge-orange"
                    : "badge-red";
          const gColor =
            grade === "A"
              ? "#3fb950"
              : grade === "B"
                ? "#58a6ff"
                : grade === "C"
                  ? "#d29922"
                  : grade === "D"
                    ? "#f85149"
                    : "#94a3b8";
          const gt =
            r.grand_max > 0 ? `${r.grand_total} / ${r.grand_max}` : "—";
          return `<tr><td style="font-family:'JetBrains Mono',monospace;font-size:12px;">${escHtml(r.reg_number)}</td><td style="font-weight:600;">${escHtml(r.name)}</td><td style="font-family:'JetBrains Mono',monospace;font-weight:700;">${gt}</td><td><span class="badge ${bCls}">${pct !== null ? pct + "%" : "No marks"}</span></td><td><span style="font-size:13px;font-weight:900;color:${gColor};">${grade}</span></td></tr>`;
        })
        .join("");
      const sel = document.getElementById("marks-student-select");
      sel.innerHTML =
        '<option value="">— Choose a student —</option>' +
        data.rows
          .map(
            (r, i) =>
              `<option value="${i}">${escHtml(r.reg_number)} — ${escHtml(r.name)}</option>`,
          )
          .join("");
    })
    .catch(() => {
      document.getElementById("marks-detail-tbody").innerHTML =
        '<tr><td colspan="5" style="text-align:center;color:#f85149;padding:20px;">Error loading marks.</td></tr>';
    });
}

function loadStudentMarksBreakdown(idx) {
  const tbody = document.getElementById("marks-student-tbody");
  const summary = document.getElementById("marks-student-summary");
  if (idx === "" || !_marksDetailData) {
    tbody.innerHTML =
      '<tr><td colspan="7" style="text-align:center;color:#94a3b8;padding:28px;font-size:13px;">Select a student above to view their marks.</td></tr>';
    summary.style.display = "none";
    return;
  }
  const r = _marksDetailData.rows[parseInt(idx)];
  const subjects = _marksDetailData.subjects;
  if (!r) return;
  let grandTotal = 0,
    grandMax = 0,
    allPass = true;
  const rows = subjects.map((subj, i) => {
    const m = r.marks[subj] || {};
    const intV =
      m.internal !== null && m.internal !== undefined ? m.internal : null;
    const extV =
      m.external !== null && m.external !== undefined ? m.external : null;
    const finV = m.final !== null && m.final !== undefined ? m.final : null;
    const intCell =
      intV !== null
        ? `<span style="color:#3b82f6;font-weight:700;font-family:'JetBrains Mono',monospace;">${intV} <span style="font-weight:400;font-size:10px;color:#94a3b8;">/ 20</span></span>`
        : '<span style="color:#94a3b8;">—</span>';
    const extCell =
      extV !== null
        ? `<span style="color:#8b5cf6;font-weight:700;font-family:'JetBrains Mono',monospace;">${extV} <span style="font-weight:400;font-size:10px;color:#94a3b8;">/ 80</span></span>`
        : '<span style="color:#94a3b8;">—</span>';
    let grade = "—",
      gradeColor = "#94a3b8",
      passText = "—",
      passColor = "#94a3b8";
    if (finV !== null) {
      grandTotal += finV;
      grandMax += 100;
      grade = finV >= 75 ? "A" : finV >= 60 ? "B" : finV >= 45 ? "C" : "D";
      gradeColor =
        grade === "A"
          ? "#3fb950"
          : grade === "B"
            ? "#58a6ff"
            : grade === "C"
              ? "#d29922"
              : "#f85149";
      const pass =
        finV >= 40 &&
        (intV === null || intV >= 8) &&
        (extV === null || extV >= 32);
      if (!pass) allPass = false;
      passText = pass ? "✓ Pass" : "✗ Fail";
      passColor = pass ? "#3fb950" : "#f85149";
    } else {
      allPass = false;
    }
    const finCell =
      finV !== null
        ? `<span style="color:#10b981;font-weight:800;font-size:14px;font-family:'JetBrains Mono',monospace;">${finV} <span style="font-weight:400;font-size:10px;color:#94a3b8;">/ 100</span></span>`
        : '<span style="color:#94a3b8;">—</span>';
    return `<tr><td style="font-size:12px;color:#94a3b8;">${i + 1}</td><td style="font-weight:600;color:#1a202c;">${escHtml(subj)}</td><td style="text-align:center;">${intCell}</td><td style="text-align:center;">${extCell}</td><td style="text-align:center;">${finCell}</td><td style="text-align:center;"><span style="font-weight:900;font-size:14px;color:${gradeColor};">${grade}</span></td><td style="text-align:center;"><span style="font-size:11px;font-weight:700;padding:3px 12px;border-radius:99px;background:${passColor === "#3fb950" ? "rgba(63,185,80,0.1)" : passColor === "#f85149" ? "rgba(248,81,73,0.1)" : "rgba(148,163,184,0.1)"};color:${passColor};">${passText}</span></td></tr>`;
  });
  tbody.innerHTML =
    rows.join("") ||
    '<tr><td colspan="7" style="text-align:center;color:#94a3b8;padding:24px;font-size:13px;">No marks recorded for this student yet.</td></tr>';
  const overallPct =
    grandMax > 0
      ? parseFloat(((grandTotal / grandMax) * 100).toFixed(1))
      : null;
  const overallGrade =
    overallPct === null
      ? "—"
      : overallPct >= 75
        ? "A"
        : overallPct >= 60
          ? "B"
          : overallPct >= 45
            ? "C"
            : "D";
  const resultPass = grandMax > 0 && allPass && overallPct >= 40;
  document.getElementById("ms-grand-total").textContent =
    grandMax > 0 ? `${grandTotal} / ${grandMax}` : "—";
  document.getElementById("ms-percentage").textContent =
    overallPct !== null ? overallPct + "%" : "—";
  document.getElementById("ms-grade").textContent = overallGrade;
  const resEl = document.getElementById("ms-result");
  resEl.innerHTML =
    grandMax > 0
      ? `<span style="font-size:16px;font-weight:800;color:${resultPass ? "#3fb950" : "#f85149"};">${resultPass ? "✓ Pass" : "✗ Fail"}</span>`
      : "—";
  summary.style.display = "block";
}

// ── SHARED: Subject dropdown loader ───────────────────────────────────
function loadSubjectsForSelect(selector, classId, callback) {
  const sel = document.querySelector(selector);
  if (!sel || !classId) return;
  sel.innerHTML = '<option value="">Loading…</option>';
  fetch("../../api/data_fetch.php?fetch=subjects_by_class&class_id=" + classId)
    .then((r) => r.json())
    .then((subjects) => {
      sel.innerHTML = '<option value="">— Choose subject —</option>';
      subjects.forEach((s) => {
        sel.innerHTML += `<option value="${s.id}">${escHtml(s.subject_name)}</option>`;
      });
      if (!subjects.length)
        sel.innerHTML = '<option value="">No subjects found</option>';
      callback(subjects);
    })
    .catch(() => {
      sel.innerHTML = '<option value="">Error loading</option>';
    });
}

// ── MARKS ADD ─────────────────────────────────────────────────────────
function updateMarksAddMaxHint(examType) {
  const display = document.getElementById("marks-add-max-display");
  if (!display) return;
  if (examType === "internal") display.value = "Internal — max 20 marks";
  else if (examType === "external") display.value = "External — max 80 marks";
  else display.value = "Select exam type above";
}

function loadStudentsForMarksAdd(classId) {
  const examType = document.getElementById("marks-add-exam-type")
    ? document.getElementById("marks-add-exam-type").value
    : "";
  const maxMark =
    examType === "internal" ? 20 : examType === "external" ? 80 : "?";
  const tbody = document.getElementById("add-marks-tbody");
  if (!tbody) return;
  if (!classId) {
    tbody.innerHTML =
      '<tr id="marks-empty-row"><td colspan="5" style="text-align:center;color:#94a3b8;padding:24px;font-size:13px;">Select class and subject above to load students.</td></tr>';
    return;
  }
  tbody.innerHTML =
    '<tr><td colspan="5" style="text-align:center;padding:20px;color:#94a3b8;">Loading students…</td></tr>';
  fetch("../../api/data_fetch.php?fetch=students_by_class&class_id=" + classId)
    .then((r) => r.json())
    .then((students) => {
      if (!students.length) {
        tbody.innerHTML =
          '<tr><td colspan="5" style="text-align:center;color:#94a3b8;padding:24px;font-size:13px;">No students in this class.</td></tr>';
        return;
      }
      const thMarks = document.querySelector(
        "#panel-marks-add thead th:nth-child(4)",
      );
      if (thMarks)
        thMarks.innerHTML = `Marks Obtained <span style="font-weight:400;opacity:.7;font-size:11px;">/ ${maxMark}</span> <span style="color:#f85149;">*</span>`;
      tbody.innerHTML = students
        .map(
          (s, i) =>
            `<tr><td style="font-size:12px;color:#94a3b8;">${i + 1}</td><td style="font-family:'JetBrains Mono',monospace;font-size:12px;">${escHtml(s.reg_number)}</td><td style="font-weight:600;">${escHtml(s.name)}</td><td><input type="number" name="marks[${i}][obtained]" class="form-input" style="width:80px;" placeholder="0" min="0" max="${maxMark}" /><input type="hidden" name="marks[${i}][student_id]" value="${s.id}" /></td><td><input type="text" name="marks[${i}][remarks]" class="form-input" style="width:160px;" placeholder="Optional" /></td></tr>`,
        )
        .join("");
    })
    .catch(() => {
      tbody.innerHTML =
        '<tr><td colspan="5" style="color:#f85149;padding:16px;text-align:center;">Error loading students.</td></tr>';
    });
}

function loadMarksForEdit(classId, subjectId, examType) {
  const tbody = document.getElementById("edit-marks-tbody");
  if (!tbody) return;
  const maxLabel =
    examType === "internal" ? "20" : examType === "external" ? "80" : "?";
  const thMax = document.querySelector(
    "#panel-marks-edit thead th:nth-child(4)",
  );
  if (thMax) thMax.textContent = "Max Marks (/" + maxLabel + ")";
  tbody.innerHTML =
    '<tr><td colspan="6" style="text-align:center;padding:20px;color:#94a3b8;">Loading…</td></tr>';
  fetch(
    `../../api/data_fetch.php?fetch=marks&class_id=${classId}&subject_id=${subjectId}&exam_type=${encodeURIComponent(examType)}`,
  )
    .then((r) => r.json())
    .then((rows) => {
      if (!rows.length) {
        tbody.innerHTML =
          '<tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:24px;font-size:13px;">No marks found.</td></tr>';
        return;
      }
      tbody.innerHTML = rows
        .map(
          (r, i) =>
            `<tr><td style="font-size:12px;color:#94a3b8;">${i + 1}</td><td style="font-family:'JetBrains Mono',monospace;font-size:12px;">${escHtml(r.reg_number)}</td><td style="font-weight:600;">${escHtml(r.name)}</td><td style="color:#64748b;">${r.max_marks}</td><td><input type="number" name="marks[${i}][obtained]" class="form-input" style="width:80px;" value="${r.marks_obtained !== "" ? r.marks_obtained : ""}" min="0" max="${r.max_marks}" placeholder="0" /><input type="hidden" name="marks[${i}][student_id]" value="${r.student_id}" /><input type="hidden" name="marks[${i}][mark_id]" value="${r.mark_id}" /></td><td><input type="text" name="marks[${i}][remarks]" class="form-input" style="width:160px;" value="${escHtml(r.remarks)}" placeholder="Optional" /></td></tr>`,
        )
        .join("");
    })
    .catch(() => {
      tbody.innerHTML =
        '<tr><td colspan="6" style="color:#f85149;padding:16px;text-align:center;">Error loading marks.</td></tr>';
    });
}

// ── ATTENDANCE ────────────────────────────────────────────────────────
function loadStudentsForAttAdd(classId) {
  const tbody = document.getElementById("att-add-tbody");
  if (!tbody) return;
  if (!classId) {
    tbody.innerHTML =
      '<tr id="att-add-empty-row"><td colspan="5" style="text-align:center;color:#94a3b8;padding:24px;font-size:13px;">Select class and subject above to load students.</td></tr>';
    return;
  }
  tbody.innerHTML =
    '<tr><td colspan="5" style="text-align:center;padding:20px;color:#94a3b8;">Loading students…</td></tr>';
  fetch("../../api/data_fetch.php?fetch=students_by_class&class_id=" + classId)
    .then((r) => r.json())
    .then((students) => {
      if (!students.length) {
        tbody.innerHTML =
          '<tr><td colspan="5" style="text-align:center;color:#94a3b8;padding:24px;">No students found.</td></tr>';
        return;
      }
      tbody.innerHTML = students
        .map(
          (s, i) =>
            `<tr><td style="font-size:12px;color:#94a3b8;">${i + 1}</td><td style="font-family:'JetBrains Mono',monospace;font-size:12px;">${escHtml(s.reg_number)}</td><td style="font-weight:600;">${escHtml(s.name)}</td><td style="text-align:center;"><input type="radio" name="attendance[${i}][status]" value="present" class="att-radio present-radio" style="accent-color:#3fb950;width:16px;height:16px;" checked /><input type="hidden" name="attendance[${i}][student_id]" value="${s.id}" /></td><td style="text-align:center;"><input type="radio" name="attendance[${i}][status]" value="absent" class="att-radio absent-radio" style="accent-color:#f85149;width:16px;height:16px;" /></td></tr>`,
        )
        .join("");
    })
    .catch(() => {
      tbody.innerHTML =
        '<tr><td colspan="5" style="color:#f85149;padding:16px;text-align:center;">Error loading.</td></tr>';
    });
}

function loadAttendanceForEdit(classId, subjectId, attDate) {
  const tbody = document.querySelector("#panel-att-edit .data-table tbody");
  if (!tbody) return;
  tbody.innerHTML =
    '<tr><td colspan="6" style="text-align:center;padding:20px;color:#94a3b8;">Loading…</td></tr>';
  fetch(
    `../../api/data_fetch.php?fetch=attendance_records&class_id=${classId}&subject_id=${subjectId}&att_date=${attDate}`,
  )
    .then((r) => r.json())
    .then((rows) => {
      if (!rows.length) {
        tbody.innerHTML =
          '<tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:24px;font-size:13px;">No records for this selection.</td></tr>';
        return;
      }
      tbody.innerHTML = rows
        .map(
          (r, i) =>
            `<tr><td style="font-size:12px;color:#94a3b8;">${i + 1}</td><td style="font-family:'JetBrains Mono',monospace;font-size:12px;">${escHtml(r.reg_number)}</td><td style="font-weight:600;">${escHtml(r.name)}</td><td style="text-align:center;"><input type="radio" name="attendance[${i}][status]" value="present" class="att-edit-radio present-edit-radio" style="accent-color:#3fb950;width:16px;height:16px;" ${r.status === "present" ? "checked" : ""} /><input type="hidden" name="attendance[${i}][att_id]" value="${r.att_id}" /><input type="hidden" name="attendance[${i}][student_id]" value="${r.student_id}" /></td><td style="text-align:center;"><input type="radio" name="attendance[${i}][status]" value="absent" class="att-edit-radio absent-edit-radio" style="accent-color:#f85149;width:16px;height:16px;" ${r.status === "absent" ? "checked" : ""} /></td><td style="text-align:center;">${r.att_id > 0 ? `<button type="button" class="btn btn-danger btn-sm" onclick="deleteAttRecord(${r.att_id})">🗑 Delete</button>` : '<span style="color:#94a3b8;font-size:12px;">—</span>'}</td></tr>`,
        )
        .join("");
    })
    .catch(() => {
      tbody.innerHTML =
        '<tr><td colspan="6" style="color:#f85149;padding:16px;text-align:center;">Error loading.</td></tr>';
    });
}

function loadAttStudents(classId) {
  const select = document.getElementById("att-student-select");
  if (!classId) {
    select.innerHTML = '<option value="">— Select class first —</option>';
    return;
  }
  select.innerHTML = '<option value="">Loading…</option>';
  fetch("../../api/data_fetch.php?fetch=students_by_class&class_id=" + classId)
    .then((r) => r.json())
    .then((students) => {
      select.innerHTML = '<option value="">— Select a student —</option>';
      students.forEach((s) => {
        select.innerHTML += `<option value="${s.id}">${escHtml(s.reg_number)} — ${escHtml(s.name)}</option>`;
      });
      if (!students.length)
        select.innerHTML = '<option value="">No students found</option>';
    });
}

function loadStudentAttendance(studentId) {
  const panel = document.getElementById("att-record-panel");
  if (!studentId) {
    panel.style.display = "none";
    return;
  }
  panel.style.display = "block";
  document.getElementById("att-student-name").textContent =
    document.getElementById("att-student-select").selectedOptions[0].text;
  fetch(
    "../../api/data_fetch.php?fetch=student_attendance&student_id=" + studentId,
  )
    .then((r) => r.json())
    .then((data) => {
      document.getElementById("att-present-count").textContent = data.present;
      document.getElementById("att-absent-count").textContent = data.absent;
      document.getElementById("att-total-count").textContent = data.total;
      const tbody = document.getElementById("att-record-body");
      if (!data.records.length) {
        tbody.innerHTML =
          '<tr><td colspan="4" style="text-align:center;color:#94a3b8;padding:24px;font-size:13px;">No attendance records found.</td></tr>';
        return;
      }
      tbody.innerHTML = data.records
        .map(
          (r) =>
            `<tr><td style="font-family:'JetBrains Mono',monospace;font-size:12px;">${r.date}</td><td>${escHtml(r.subject_name)}</td><td><span class="badge ${r.status === "present" ? "badge-green" : "badge-red"}">${r.status.charAt(0).toUpperCase() + r.status.slice(1)}</span></td><td style="font-size:12px;color:#64748b;">${escHtml(r.marked_by)}</td></tr>`,
        )
        .join("");
    });
}

function markAllAttendance(status) {
  document
    .querySelectorAll(`.${status}-radio`)
    .forEach((r) => (r.checked = true));
}
function markAllAttendanceEdit(status) {
  document
    .querySelectorAll(`.${status}-edit-radio`)
    .forEach((r) => (r.checked = true));
}

function deleteAttRecord(attId) {
  if (confirm("Delete this attendance record? This cannot be undone.")) {
    const f = document.createElement("form");
    f.method = "POST";
    f.action = "../../backend/admin/delete_attendance.php";
    f.innerHTML = `<input type="hidden" name="att_id" value="${attId}" /><input type="hidden" name="action" value="delete_single" />`;
    document.body.appendChild(f);
    f.submit();
  }
}

function deleteAllAttendance() {
  if (
    confirm(
      "Delete ALL attendance records for this date/class/subject? Cannot be undone.",
    )
  ) {
    const form = document.querySelector("#panel-att-edit form");
    if (!form) {
      alert("Select class, subject and date first.");
      return;
    }
    const inp = document.createElement("input");
    inp.type = "hidden";
    inp.name = "delete_all";
    inp.value = "1";
    form.appendChild(inp);
    form.submit();
  }
}

// ── ANNOUNCEMENTS ─────────────────────────────────────────────────────
function loadEditAnnouncement(annId) {
  const wrap = document.getElementById("edit-ann-form-wrap");
  if (!annId) {
    wrap.style.display = "none";
    return;
  }
  document.getElementById("edit-ann-id").value = annId;
  wrap.style.display = "block";
}

function submitDeleteAnn() {
  const form = document
    .getElementById("edit-ann-form-wrap")
    .querySelector("form");
  const inp = document.createElement("input");
  inp.type = "hidden";
  inp.name = "delete_ann";
  inp.value = "1";
  form.appendChild(inp);
  form.submit();
}

// ── EDIT CLASS ────────────────────────────────────────────────────────
function loadEditClass(classId, optionEl) {
  const wrap = document.getElementById("edit-class-form-wrap");
  if (!classId) {
    wrap.style.display = "none";
    return;
  }
  document.getElementById("edit_class_id").value = classId;
  wrap.style.display = "block";
  if (optionEl) {
    document.getElementById("edit-class-name").value =
      optionEl.dataset.name || "";
    document.getElementById("edit-academic-year").value =
      optionEl.dataset.year || "";
  }
  const newStudentList = document.getElementById("new-student-list");
  if (newStudentList) {
    newStudentList.innerHTML = "";
  }
  newStudentCount = 0;

  fetch("../../api/data_fetch.php?fetch=students_by_class&class_id=" + classId)
    .then((r) => r.json())
    .then((students) => {
      const inner = document.getElementById("edit-student-list-inner");
      if (!students.length) {
        inner.innerHTML =
          '<div style="text-align:center;color:#94a3b8;font-size:13px;padding:20px;">No students in this class yet. Use &quot;+ Add Student&quot; below to add some.</div>';
        return;
      }
      const bgOpts = ["A+", "A−", "B+", "B−", "O+", "O−", "AB+", "AB−"];
      inner.innerHTML = students
        .map(
          (s, i) => `
        <div class="student-entry" id="edit-student-${i}">
          <div class="student-entry-header"><span class="student-entry-title">${escHtml(s.reg_number)} — ${escHtml(s.name)}</span><button type="button" class="remove-student-btn" onclick="confirmRemoveStudent('edit-student-${i}','${escHtml(s.reg_number)}')">&#x2715; Remove from Class</button></div>
          <input type="hidden" name="students[${i}][id]" value="${s.id}" />
          <div class="form-grid-3">
            <div class="form-group"><label class="form-label">Registration Number</label><input type="text" name="students[${i}][reg_number]" class="form-input" value="${escHtml(s.reg_number)}" /></div>
            <div class="form-group"><label class="form-label">Full Name</label><input type="text" name="students[${i}][name]" class="form-input" value="${escHtml(s.name)}" /></div>
            <div class="form-group"><label class="form-label">Gender</label><select name="students[${i}][gender]" class="form-input form-select"><option value="">Select</option><option ${s.gender === "Male" ? "selected" : ""}>Male</option><option ${s.gender === "Female" ? "selected" : ""}>Female</option><option ${s.gender === "Other" ? "selected" : ""}>Other</option></select></div>
            <div class="form-group"><label class="form-label">Date of Birth</label><input type="date" name="students[${i}][dob]" class="form-input" value="${escHtml(s.dob || "")}" /></div>
            <div class="form-group"><label class="form-label">Contact</label><input type="tel" name="students[${i}][contact]" class="form-input" value="${escHtml(s.contact || "")}" /></div>
            <div class="form-group"><label class="form-label">Blood Group</label><select name="students[${i}][blood_group]" class="form-input form-select"><option value="">Select</option>${bgOpts.map((bg) => `<option ${s.blood_group === bg ? "selected" : ""}>${bg}</option>`).join("")}</select></div>
            <div class="form-group"><label class="form-label">Email</label><input type="email" name="students[${i}][email]" class="form-input" value="${escHtml(s.email || "")}" /></div>
            <div class="form-group"><label class="form-label">Guardian Name</label><input type="text" name="students[${i}][guardian_name]" class="form-input" value="${escHtml(s.guardian_name || "")}" /></div>
            <div class="form-group"><label class="form-label">Guardian Contact</label><input type="tel" name="students[${i}][guardian_contact]" class="form-input" value="${escHtml(s.guardian_contact || "")}" /></div>
          </div>
          <div class="form-group"><label class="form-label">Residential Address</label><input type="text" name="students[${i}][address]" class="form-input" value="${escHtml(s.address || "")}" /></div>
        </div>`,
        )
        .join("");
    });
  loadEditTimetable(classId);
}

// ── ADD CLASS — Subjects ──────────────────────────────────────────────
function addSubject() {
  const input = document.getElementById("subject-input");
  const val = input.value.trim();
  if (!val) return;
  const tagId = "subject-" + Date.now();
  document.getElementById("subject-tags").innerHTML +=
    `<span class="subject-tag" id="${tagId}">${escHtml(val)}<button type="button" onclick="removeSubject('${tagId}')">×</button></span>`;
  document.getElementById("subject-hidden-inputs").innerHTML +=
    `<input type="hidden" name="subjects[]" value="${escHtml(val)}" id="hidden-${tagId}" />`;
  input.value = "";
}
function removeSubject(tagId) {
  ["", "hidden-"].forEach((p) => {
    const el = document.getElementById(p + tagId);
    if (el) el.remove();
  });
}
document
  .getElementById("subject-input")
  ?.addEventListener("keydown", function (e) {
    if (e.key === "Enter") {
      e.preventDefault();
      addSubject();
    }
  });

// ── EDIT CLASS — New Student Row ──────────────────────────────────────
let newStudentCount = 0;
function addNewStudentRow() {
  const i = newStudentCount++;
  const div = document.createElement("div");
  div.className = "student-entry";
  div.id = "new-student-" + i;
  div.innerHTML = `<div class="student-entry-header"><span class="student-entry-title">New Student #${i + 1} <span style="font-weight:400;color:#94a3b8;font-size:11px;"> — Reg# auto-assigned by DB on save</span></span><button type="button" class="remove-student-btn" onclick="this.closest('.student-entry').remove()">✕ Remove</button></div><div class="form-grid-3"><div class="form-group"><label class="form-label">Full Name <span style="color:#f85149;">*</span></label><input type="text" name="new_students[${i}][name]" class="form-input" placeholder="Student full name" required /></div><div class="form-group"><label class="form-label">Gender</label><select name="new_students[${i}][gender]" class="form-input form-select"><option value="">Select</option><option>Male</option><option>Female</option><option>Other</option></select></div><div class="form-group"><label class="form-label">Date of Birth</label><input type="date" name="new_students[${i}][dob]" class="form-input" /></div><div class="form-group"><label class="form-label">Contact Number</label><input type="tel" name="new_students[${i}][contact]" class="form-input" placeholder="10-digit mobile" /></div><div class="form-group"><label class="form-label">Blood Group</label><select name="new_students[${i}][blood_group]" class="form-input form-select"><option value="">Select</option><option>A+</option><option>A−</option><option>B+</option><option>B−</option><option>O+</option><option>O−</option><option>AB+</option><option>AB−</option></select></div><div class="form-group"><label class="form-label">Email Address</label><input type="email" name="new_students[${i}][email]" class="form-input" placeholder="student@email.com" /></div><div class="form-group"><label class="form-label">Guardian Name</label><input type="text" name="new_students[${i}][guardian_name]" class="form-input" placeholder="Parent or guardian name" /></div><div class="form-group"><label class="form-label">Guardian Contact</label><input type="tel" name="new_students[${i}][guardian_contact]" class="form-input" placeholder="Parent mobile number" /></div></div><div class="form-group"><label class="form-label">Residential Address</label><input type="text" name="new_students[${i}][address]" class="form-input" placeholder="Full address" /></div>`;
  document.getElementById("new-student-list").appendChild(div);
}

function loadEditTimetable(classId) {
  const tbody = document.getElementById("edit-timetable-body");
  const periods = [
    "9:30 - 10:30",
    "10:30 - 11:30",
    "11:45 - 12:45",
    "12:45 - 1:45",
    "2:00 - 3:00",
    "3:00 - 4:00",
  ];
  const days = [
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday",
  ];
  tbody.innerHTML =
    '<tr><td colspan="7" style="text-align:center;color:#94a3b8;padding:16px;">Loading timetable…</td></tr>';
  fetch("../../api/data_fetch.php?fetch=timetable&class_id=" + classId)
    .then((r) => r.json())
    .then((data) => {
      const schedule = [
        { type: "period", label: "9:30 - 10:30", note: "Period 1" },
        { type: "period", label: "10:30 - 11:30", note: "Period 2" },
        { type: "break", label: "11:30 - 11:45", note: "☕ Break" },
        { type: "period", label: "11:45 - 12:45", note: "Period 3" },
        { type: "period", label: "12:45 - 1:45", note: "Period 4" },
        { type: "break", label: "1:45 - 2:00", note: "🍽 Lunch Break" },
        { type: "period", label: "2:00 - 3:00", note: "Period 5" },
        { type: "period", label: "3:00 - 4:00", note: "Period 6" },
      ];
      tbody.innerHTML = schedule
        .map((slot) => {
          if (slot.type === "break")
            return `<tr style="background:#f8fafc;"><td style="font-weight:700;font-size:12px;white-space:nowrap;color:#94a3b8;font-style:italic;" colspan="7">${escHtml(slot.note)} &nbsp;<span style="font-size:11px;font-weight:400;">(${escHtml(slot.label)})</span></td></tr>`;
          return `<tr><td style="font-weight:700;font-size:12px;white-space:nowrap;">${escHtml(slot.note)}<br><span style="font-size:10px;color:#64748b;font-weight:400;">${escHtml(slot.label)}</span></td>${days.map((d) => `<td><input type="text" name="timetable[${d}][${slot.label}]" class="form-input" style="min-width:90px;font-size:12px;padding:5px 8px;" value="${escHtml(data[d] && data[d][slot.label] ? data[d][slot.label] : "")}" placeholder="—" /></td>`).join("")}</tr>`;
        })
        .join("");
    })
    .catch(() => {
      tbody.innerHTML =
        '<tr><td colspan="7" style="color:#f85149;text-align:center;padding:14px;">Error loading timetable.</td></tr>';
    });
}

function confirmRemoveStudent(elId, regNo) {
  if (
    confirm(
      "Remove student " +
      regNo +
      " from this class? Their marks and attendance will also be removed.",
    )
  ) {
    const el = document.getElementById(elId);
    if (el) {
      const form = el.closest("form");
      const inp = document.createElement("input");
      inp.type = "hidden";
      inp.name = "remove_students[]";
      inp.value = regNo;
      form.appendChild(inp);
      el.style.opacity = "0.3";
      el.style.pointerEvents = "none";
    }
  }
}

function submitDeleteClass() {
  const form = document
    .getElementById("edit-class-form-wrap")
    .querySelector("form");
  const inp = document.createElement("input");
  inp.type = "hidden";
  inp.name = "delete_class";
  inp.value = "1";
  form.appendChild(inp);
  form.submit();
}

// ── STUDENTS ──────────────────────────────────────────────────────────
function filterStudentView(classId) {
  document
    .querySelectorAll("#student-view-tbody tr[data-class-id]")
    .forEach((row) => {
      row.style.display =
        !classId || row.dataset.classId === classId ? "" : "none";
    });
}

function validateAddStudent() {
  const classId = document.getElementById("add-student-class-id").value;
  const className = document.getElementById("add-student-class-name").value;
  const name = document
    .querySelector('#add-student-form input[name="new_students[0][name]"]')
    .value.trim();
  if (!classId || !className) {
    alert("Please select a class before saving.");
    return false;
  }
  if (!name) {
    alert("Student full name is required.");
    return false;
  }
  return true;
}

function loadEditStudentDropdown(classId) {
  const sel = document.getElementById("edit-student-sel");
  document.getElementById("edit-student-form-wrap").style.display = "none";
  document.getElementById("edit-student-placeholder").style.display = "block";
  if (!classId) {
    sel.innerHTML = '<option value="">— Select class first —</option>';
    return;
  }
  sel.innerHTML = '<option value="">Loading…</option>';
  fetch("../../api/data_fetch.php?fetch=students_by_class&class_id=" + classId)
    .then((r) => r.json())
    .then((students) => {
      sel.innerHTML = '<option value="">— Choose a student —</option>';
      students.forEach((s) => {
        const opt = document.createElement("option");
        opt.value = s.id;
        opt.textContent = s.reg_number + " — " + s.name;
        Object.keys(s).forEach((k) => (opt.dataset[k] = s[k] || ""));
        sel.appendChild(opt);
      });
      if (!students.length)
        sel.innerHTML = '<option value="">No students in this class</option>';
    })
    .catch(() => {
      sel.innerHTML = '<option value="">Error loading students</option>';
    });
}

function openEditStudentById(studentId) {
  showPanel("student-edit");
  const classSel = document.getElementById("edit-student-class-sel");
  const classOptions = Array.from(classSel.options).filter((o) => o.value);
  let found = false;
  function tryNextClass(idx) {
    if (idx >= classOptions.length || found) return;
    const classId = classOptions[idx].value;
    fetch(
      "../../api/data_fetch.php?fetch=students_by_class&class_id=" + classId,
    )
      .then((r) => r.json())
      .then((students) => {
        const match = students.find(
          (s) => parseInt(s.id) === parseInt(studentId),
        );
        if (match) {
          found = true;
          classSel.value = classId;
          loadEditStudentDropdown(classId);
          setTimeout(() => {
            const sel = document.getElementById("edit-student-sel");
            sel.value = studentId;
            loadEditStudentForm(studentId);
          }, 600);
        } else {
          tryNextClass(idx + 1);
        }
      });
  }
  tryNextClass(0);
}

function loadEditStudentForm(studentId) {
  const wrap = document.getElementById("edit-student-form-wrap");
  const placeholder = document.getElementById("edit-student-placeholder");
  if (!studentId) {
    wrap.style.display = "none";
    placeholder.style.display = "block";
    return;
  }
  const sel = document.getElementById("edit-student-sel");
  const opt = sel.options[sel.selectedIndex];
  if (!opt || !opt.value) return;
  const classId = document.getElementById("edit-student-class-sel").value;
  const classSel = document.getElementById("edit-student-class-sel");
  const classOpt = classSel.options[classSel.selectedIndex];
  document.getElementById("edit-student-class-id-hidden").value = classId;
  document.getElementById("edit-student-class-name-hidden").value = classOpt
    ? classOpt.dataset.name || ""
    : "";
  document.getElementById("edit-student-academic-year-hidden").value = classOpt
    ? classOpt.dataset.year || ""
    : "";
  const s = opt.dataset;
  const bgOpts = ["A+", "A−", "B+", "B−", "O+", "O−", "AB+", "AB−"];
  const idx = 0;
  document.getElementById("edit-student-fields-inner").innerHTML = `
    <input type="hidden" name="students[${idx}][id]" value="${escHtml(String(studentId))}" />
    <div class="form-group"><label class="form-label">Registration Number</label><input type="text" name="students[${idx}][reg_number]" class="form-input" value="${escHtml(s.reg_number || "")}" /></div>
    <div class="form-group"><label class="form-label">Full Name <span style="color:#f85149;">*</span></label><input type="text" name="students[${idx}][name]" class="form-input" value="${escHtml(s.name || "")}" required /></div>
    <div class="form-group"><label class="form-label">Gender</label><select name="students[${idx}][gender]" class="form-input form-select"><option value="">Select</option><option ${s.gender === "Male" ? "selected" : ""}>Male</option><option ${s.gender === "Female" ? "selected" : ""}>Female</option><option ${s.gender === "Other" ? "selected" : ""}>Other</option></select></div>
    <div class="form-group"><label class="form-label">Date of Birth</label><input type="date" name="students[${idx}][dob]" class="form-input" value="${escHtml(s.dob || "")}" /></div>
    <div class="form-group"><label class="form-label">Contact Number</label><input type="tel" name="students[${idx}][contact]" class="form-input" value="${escHtml(s.contact || "")}" /></div>
    <div class="form-group"><label class="form-label">Blood Group</label><select name="students[${idx}][blood_group]" class="form-input form-select"><option value="">Select</option>${bgOpts.map((bg) => `<option ${s.blood_group === bg ? "selected" : ""}>${bg}</option>`).join("")}</select></div>
    <div class="form-group"><label class="form-label">Email Address</label><input type="email" name="students[${idx}][email]" class="form-input" value="${escHtml(s.email || "")}" /></div>
    <div class="form-group"><label class="form-label">Guardian Name</label><input type="text" name="students[${idx}][guardian_name]" class="form-input" value="${escHtml(s.guardian_name || "")}" /></div>
    <div class="form-group"><label class="form-label">Guardian Contact</label><input type="tel" name="students[${idx}][guardian_contact]" class="form-input" value="${escHtml(s.guardian_contact || "")}" /></div>`;
  document.getElementById("edit-student-address-row").innerHTML =
    `<label class="form-label">Residential Address</label><input type="text" name="students[${idx}][address]" class="form-input" value="${escHtml(s.address || "")}" />`;
  wrap.style.display = "block";
  placeholder.style.display = "none";
}

// ── CHANGE EVENT DELEGATION ───────────────────────────────────────────
document.addEventListener("change", function (e) {
  if (e.target.closest("#panel-marks-add") && e.target.name === "class_id") {
    loadSubjectsForSelect(
      '#panel-marks-add select[name="subject_id"]',
      e.target.value,
      function () { },
    );
    loadStudentsForMarksAdd(e.target.value);
    return;
  }
  if (e.target.closest("#panel-marks-edit")) {
    const classId = document.querySelector(
      '#panel-marks-edit select[name="class_id"]',
    ).value;
    const subjectId = document.querySelector(
      '#panel-marks-edit select[name="subject_id"]',
    ).value;
    const examType = document.querySelector(
      '#panel-marks-edit select[name="exam_type"]',
    ).value;
    if (e.target.name === "class_id")
      loadSubjectsForSelect(
        '#panel-marks-edit select[name="subject_id"]',
        classId,
        function () { },
      );
    if (classId && subjectId && examType)
      loadMarksForEdit(classId, subjectId, examType);
    return;
  }
  if (e.target.closest("#panel-att-add") && e.target.name === "class_id") {
    loadSubjectsForSelect(
      '#panel-att-add select[name="subject_id"]',
      e.target.value,
      function () { },
    );
    loadStudentsForAttAdd(e.target.value);
    return;
  }
  if (e.target.closest("#panel-att-edit")) {
    const classId = document.querySelector(
      '#panel-att-edit select[name="class_id"]',
    ).value;
    const subjectId = document.querySelector(
      '#panel-att-edit select[name="subject_id"]',
    ).value;
    const attDate = document.querySelector(
      '#panel-att-edit input[name="att_date"]',
    ).value;
    if (e.target.name === "class_id")
      loadSubjectsForSelect(
        '#panel-att-edit select[name="subject_id"]',
        classId,
        function () { },
      );
    if (classId && subjectId && attDate)
      loadAttendanceForEdit(classId, subjectId, attDate);
    return;
  }
  if (e.target.name === "edit_class_select") {
    loadEditClass(e.target.value, e.target.options[e.target.selectedIndex]);
    return;
  }
});

document.addEventListener("input", function (e) {
  if (e.target.closest("#panel-att-edit") && e.target.name === "att_date") {
    const classId = document.querySelector(
      '#panel-att-edit select[name="class_id"]',
    ).value;
    const subjectId = document.querySelector(
      '#panel-att-edit select[name="subject_id"]',
    ).value;
    if (classId && subjectId && e.target.value)
      loadAttendanceForEdit(classId, subjectId, e.target.value);
  }
});

// ── REDIRECT HELPERS ─────────────────────────────────────────────────
document.addEventListener("DOMContentLoaded", function () {
  const addForm = document.getElementById("add-student-form");
  if (addForm) {
    addForm.addEventListener("submit", function () {
      const inp = document.createElement("input");
      inp.type = "hidden";
      inp.name = "_redirect_panel";
      inp.value = "student-add";
      addForm.appendChild(inp);
    });
  }
  const editForm = document.getElementById("edit-student-form-inner");
  if (editForm) {
    editForm.addEventListener("submit", function () {
      const inp = document.createElement("input");
      inp.type = "hidden";
      inp.name = "_redirect_panel";
      inp.value = "student-edit";
      editForm.appendChild(inp);
    });
  }
});
