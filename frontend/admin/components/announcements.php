<?php
// frontend/admin/panels/announcements.php
// Variables expected: $all_anns_full (result), $all_anns_list (array)
?>

<!-- ═══════ VIEW ANNOUNCEMENTS ═══════ -->
<div id="panel-ann-view" class="module-panel">
  <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#e8edf2] mb-5">
    <div class="flex justify-between items-center mb-5">
      <div>
        <p class="text-[15px] font-bold text-[#1a202c] m-0 mb-1">All Announcements</p>
        <p class="text-[12px] text-[#8b949e] m-0">Browse all published announcements sorted by latest first.</p>
      </div>
      <button onclick="showPanel('ann-add')" class="btn btn-primary">+ Add New</button>
    </div>
    <div class="flex flex-col gap-3">
      <?php
      $all_anns_full = mysqli_query($conn, "SELECT title, body, tag, created_at FROM announcements ORDER BY created_at DESC");
      if (!$all_anns_full || mysqli_num_rows($all_anns_full) === 0): ?>
        <div class="py-8 text-center text-[#94a3b8] text-[13px]">No announcements yet.</div>
      <?php else:
        while ($av = mysqli_fetch_assoc($all_anns_full)):
          $ann = [
            'title' => $av['title'],
            'tag' => $av['tag'],
            'tag_label' => ucfirst($av['tag']),
            'desc' => $av['body'],
            'date' => date('F j, Y', strtotime($av['created_at'])),
          ];
          $ann_cursor = 'cursor-default';
          require __DIR__ . '/../../../frontend/shared/components/announcement_card.php';
        endwhile;
      endif; ?>
    </div>
  </div>
</div>

<!-- ═══════ ADD ANNOUNCEMENT ═══════ -->
<div id="panel-ann-add" class="module-panel">
  <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#e8edf2] mb-5">
    <p class="text-[15px] font-bold text-[#1a202c] m-0 mb-1">Add Announcement</p>
    <p class="text-[12px] text-[#8b949e] m-0 mb-5">Create a new campus-wide announcement. It will appear on all student
      dashboards.</p>

    <form method="POST" action="../../backend/admin/announcements.php">
      <input type="hidden" name="action" value="add" />

      <div class="mb-4">
        <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Announcement Title <span
            class="text-[#f85149]">*</span></label>
        <input type="text" name="ann_title"
          class="w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none focus:border-[#58a6ff]"
          placeholder="Enter a clear, descriptive title" required />
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div class="mb-4">
          <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Category / Tag <span
              class="text-[#f85149]">*</span></label>
          <select name="ann_tag"
            class="form-select w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none"
            required>
            <option value="">— Select category —</option>
            <option value="urgent">🔴 Urgent</option>
            <option value="event">🟢 Event</option>
            <option value="info">🔵 Info</option>
          </select>
        </div>
        <div class="mb-4">
          <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Publish Date</label>
          <input type="date" name="ann_date"
            class="w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none" />
          <p class="text-[11px] text-[#94a3b8] mt-1">Leave blank to use today's date.</p>
        </div>
      </div>

      <div class="mb-4">
        <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Announcement Body <span
            class="text-[#f85149]">*</span></label>
        <textarea name="ann_body"
          class="w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none focus:border-[#58a6ff] resize-y min-h-[100px]"
          placeholder="Write the full announcement here…" required></textarea>
      </div>

      <div class="mb-4">
        <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Target Audience</label>
        <select name="ann_audience"
          class="form-select w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none">
          <option value="all">All (Students, Teachers, Staff)</option>
          <option value="students">Students Only</option>
          <option value="teachers">Teachers Only</option>
          <option value="staff">Staff Only</option>
        </select>
      </div>

      <div class="flex gap-2.5 justify-end mt-6 pt-5 border-t border-[#e8edf2]">
        <button type="button" onclick="showPanel('ann-view')" class="btn btn-secondary">Cancel</button>
        <button type="submit" class="btn btn-primary">📢 Publish Announcement</button>
      </div>
    </form>
  </div>
</div>

<!-- ═══════ EDIT ANNOUNCEMENT ═══════ -->
<div id="panel-ann-edit" class="module-panel">
  <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#e8edf2] mb-5">
    <p class="text-[15px] font-bold text-[#1a202c] m-0 mb-1">Edit Announcement</p>
    <p class="text-[12px] text-[#8b949e] m-0 mb-5">Select an existing announcement to edit its content, or delete it
      permanently.</p>

    <div class="mb-5 max-w-sm">
      <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Select Announcement</label>
      <select name="ann_select"
        class="form-select w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none"
        onchange="loadEditAnnouncement(this.value)">
        <option value="">— Choose an announcement —</option>
        <?php foreach ($all_anns_list as $ae): ?>
          <option value="<?= $ae['id'] ?>"><?= htmlspecialchars($ae['title']) ?></option>
        <?php endforeach; ?>
        <?php if (empty($all_anns_list)): ?>
          <option value="">No announcements yet</option><?php endif; ?>
      </select>
    </div>

    <div id="edit-ann-form-wrap" style="display:none;">
      <form method="POST" action="../../backend/admin/announcements.php">
        <input type="hidden" name="action" value="edit" />
        <input type="hidden" name="ann_id" id="edit-ann-id" value="" />

        <div id="ann-delete-confirm"
          class="hidden bg-[rgba(248,81,73,0.06)] border border-[rgba(248,81,73,0.2)] rounded-lg px-3.5 py-2.5 text-[12px] text-[#f85149] mb-4">
          ⚠️ Are you sure you want to <strong>delete this announcement</strong>? This action cannot be undone.
          <div class="mt-2 flex gap-2">
            <button type="button" onclick="submitDeleteAnn()" class="btn btn-danger btn-sm">Yes, Delete</button>
            <button type="button" onclick="document.getElementById('ann-delete-confirm').classList.add('hidden')"
              class="btn btn-secondary btn-sm">Cancel</button>
          </div>
        </div>

        <div class="mb-4">
          <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Announcement Title</label>
          <input type="text" name="ann_title" id="edit-ann-title"
            class="w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none focus:border-[#58a6ff]"
            placeholder="Announcement title" />
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div class="mb-4">
            <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Category / Tag</label>
            <select name="ann_tag" id="edit-ann-tag"
              class="form-select w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none">
              <option value="urgent">🔴 Urgent</option>
              <option value="event">🟢 Event</option>
              <option value="info">🔵 Info</option>
            </select>
          </div>
          <div class="mb-4">
            <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Publish Date</label>
            <input type="date" name="ann_date" id="edit-ann-date"
              class="w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none" />
          </div>
        </div>

        <div class="mb-4">
          <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Announcement Body</label>
          <textarea name="ann_body" id="edit-ann-body"
            class="w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none resize-y min-h-[100px]"
            placeholder="Announcement content…"></textarea>
        </div>

        <div class="mb-4">
          <label class="block text-[12px] font-semibold text-[#374151] mb-1.5">Target Audience</label>
          <select name="ann_audience" id="edit-ann-audience"
            class="form-select w-full px-3 py-2.5 border border-[#e2e8f0] rounded-lg text-[13px] bg-[#fdfdfd] outline-none">
            <option value="all">All (Students, Teachers, Staff)</option>
            <option value="students">Students Only</option>
            <option value="teachers">Teachers Only</option>
            <option value="staff">Staff Only</option>
          </select>
        </div>

        <div class="flex gap-2.5 justify-end mt-6 pt-5 border-t border-[#e8edf2]">
          <button type="button" onclick="document.getElementById('ann-delete-confirm').classList.remove('hidden')"
            class="btn btn-danger">🗑 Delete Announcement</button>
          <button type="button" onclick="showPanel('ann-view')" class="btn btn-secondary">Cancel</button>
          <button type="submit" class="btn btn-primary">💾 Update Announcement</button>
        </div>
      </form>
    </div>
  </div>
</div>