const panelMeta = {
  dashboard: {
    heading: `Welcome, ${typeof ADMIN_NAME !== "undefined" ? ADMIN_NAME : "Admin"} 👋`,
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
    const h = document.getElementById("page-heading");
    const b = document.getElementById("page-breadcrumb");
    if (h) h.textContent = meta.heading;
    if (b) b.textContent = meta.crumb;
  }

  [
    "class-detail-panel",
    "timetable-panel",
    "marks-detail-panel",
    "att-record-panel",
  ].forEach((id) => {
    const el = document.getElementById(id);
    if (el) el.style.display = "none";
  });
  window.scrollTo(0, 0);
}

if (typeof INITIAL_PANEL !== "undefined" && INITIAL_PANEL !== "dashboard")
  showPanel(INITIAL_PANEL);

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

  let html = Array(firstDay).fill("<div></div>").join("");
  const today = new Date();
  for (let i = 1; i <= lastDate; i++) {
    const cls =
      i === today.getDate() &&
      month === today.getMonth() &&
      year === today.getFullYear()
        ? "today"
        : "";
    html += `<div class="${cls}">${i}</div>`;
  }
  calendarDates.innerHTML = html;
}

const prevMonth = () => {
  currentDate.setMonth(currentDate.getMonth() - 1);
  renderCalendar();
};
const nextMonth = () => {
  currentDate.setMonth(currentDate.getMonth() + 1);
  renderCalendar();
};

renderCalendar();

function toggleElement(id, state) {
  const el = document.getElementById(id);
  if (el)
    el.style.display =
      state === undefined
        ? el.style.display === "none"
          ? "block"
          : "none"
        : state
          ? "block"
          : "none";
}

const showElement = (id) => toggleElement(id, true);
const hideElement = (id) => toggleElement(id, false);
const escHtml = (s) =>
  String(s)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");

function submitFormWithAction(formId, actionName, actionValue) {
  const form = document.getElementById(formId);
  if (!form) return;
  const inp = document.createElement("input");
  inp.type = "hidden";
  inp.name = actionName;
  inp.value = actionValue;
  form.appendChild(inp);
  form.submit();
}

function viewClassDetail(className) {
  if (typeof studentsByClass === "undefined") return;
  const detail = document.getElementById("class-detail-name");
  if (detail) detail.textContent = className;
  hideElement("timetable-panel");

  const tbody = document.querySelector("#class-detail-panel .data-table tbody");
  const list = studentsByClass[className] || [];

  if (tbody) {
    tbody.innerHTML = list.length
      ? list
          .map(
            (s) => `
      <tr>
        <td style="font-family:'JetBrains Mono',monospace;font-size:12px;">${escHtml(s.reg_number)}</td>
        <td style="font-weight:600;">${escHtml(s.name)}</td>
        <td>${s.gender || "—"}</td>
        <td>${s.dob || "—"}</td>
        <td>${s.contact || "—"}</td>
        <td>${s.guardian_name || "—"}</td>
        <td>${s.address || "—"}</td>
        <td>${s.blood_group ? `<span class="badge badge-red">${s.blood_group}</span>` : "—"}</td>
      </tr>
    `,
          )
          .join("")
      : '<tr><td colspan="8" style="text-align:center;color:#94a3b8;padding:32px;font-size:13px;">No students in this class yet.</td></tr>';
  }
  showElement("class-detail-panel");
}

let newStudentCount = 0;

function addSubject() {
  const input = document.getElementById("subject-input");
  const val = input ? input.value.trim() : "";
  if (!val) return;

  const tagId = "subject-" + Date.now();
  const tagContainer = document.getElementById("subject-tags");
  const hiddenContainer = document.getElementById("subject-hidden-inputs");

  if (tagContainer)
    tagContainer.innerHTML += `<span class="subject-tag" id="${tagId}">${escHtml(val)}<button type="button" onclick="removeSubject('${tagId}')">×</button></span>`;
  if (hiddenContainer)
    hiddenContainer.innerHTML += `<input type="hidden" name="subjects[]" value="${escHtml(val)}" id="hidden-${tagId}" />`;
  if (input) input.value = "";
}

function removeSubject(tagId) {
  document.getElementById(tagId)?.remove();
  document.getElementById("hidden-" + tagId)?.remove();
}

function addNewStudentRow() {
  const i = newStudentCount++;
  const div = document.createElement("div");
  div.className = "student-entry";
  div.id = "new-student-" + i;
  div.innerHTML = `
    <div class="student-entry-header">
      <span class="student-entry-title">New Student #${i + 1}</span>
      <button type="button" class="remove-student-btn" onclick="this.closest('.student-entry').remove()">✕ Remove</button>
    </div>
    <div class="form-group"><label class="form-label">Full Name <span style="color:#f85149;">*</span></label>
      <input type="text" name="new_students[${i}][name]" class="form-input" placeholder="Student full name" required /></div>
    <div class="form-group"><label class="form-label">Contact</label>
      <input type="tel" name="new_students[${i}][contact]" class="form-input" placeholder="10-digit mobile" /></div>
    <div class="form-group"><label class="form-label">Email</label>
      <input type="email" name="new_students[${i}][email]" class="form-input" placeholder="student@email.com" /></div>
  `;
  document.getElementById("new-student-list")?.appendChild(div);
}

function confirmRemoveStudent(elId, regNo) {
  if (confirm(`Remove student ${regNo} from this class?`)) {
    const el = document.getElementById(elId);
    if (el) {
      const form = el.closest("form");
      if (form) {
        const inp = document.createElement("input");
        inp.type = "hidden";
        inp.name = "remove_students[]";
        inp.value = regNo;
        form.appendChild(inp);
      }
      Object.assign(el.style, { opacity: "0.3", pointerEvents: "none" });
    }
  }
}

function submitDeleteClass() {
  if (confirm("Delete this entire class? All data will be removed.")) {
    submitFormWithAction("edit-class-form-wrap", "delete_class", "1");
  }
}

function markAllAttendance(status) {
  document
    .querySelectorAll("." + status + "-radio")
    .forEach((i) => (i.checked = true));
}

function markAllAttendanceEdit(status) {
  document
    .querySelectorAll("." + status + "-edit-radio")
    .forEach((i) => (i.checked = true));
}

function deleteAttRecord(attId) {
  if (confirm("Delete this attendance record? Cannot be undone.")) {
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
    confirm("Delete ALL attendance records for this date? Cannot be undone.")
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

function loadEditAnnouncement(annId) {
  const wrap = document.getElementById("edit-ann-form-wrap");
  if (!wrap) return;
  if (!annId) {
    wrap.style.display = "none";
    return;
  }
  const input = document.getElementById("edit-ann-id");
  if (input) input.value = annId;
  wrap.style.display = "block";
}

function submitDeleteAnn() {
  if (confirm("Delete this announcement?"))
    submitFormWithAction("edit-ann-form-wrap", "delete_ann", "1");
}

document.getElementById("subject-input")?.addEventListener("keydown", (e) => {
  if (e.key === "Enter") {
    e.preventDefault();
    addSubject();
  }
});
