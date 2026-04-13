(function () {
  'use strict';
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('.btn-edit');
    if (!btn) return;
    var id = btn.getAttribute('data-id');
    var title = btn.getAttribute('data-title') || '';
    var base = document.getElementById('jobTitlesSettings');
    var updateBase = base ? base.getAttribute('data-update-base') : '';
    if (!updateBase) return;
    document.getElementById('editIdText').textContent = '#' + id;
    document.getElementById('edit_title').value = title;
    document.getElementById('editForm').action = updateBase + id;
  });
})();
