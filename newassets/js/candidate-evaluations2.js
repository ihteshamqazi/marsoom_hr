(function () {
  'use strict';
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('.btn-edit-eval');
    if (!btn) return;
    var id = btn.getAttribute('data-id');
    var base = document.getElementById('ce2Settings');
    var updateBase = base ? base.getAttribute('data-update-base') : '';
    if (!updateBase) return;
    document.getElementById('editEvalIdText').textContent = '#' + id;
    document.getElementById('editEvalForm').action = updateBase + id;
    document.getElementById('edit_evaluator').value = btn.getAttribute('data-evaluator') || '';
    document.getElementById('edit_status').value = btn.getAttribute('data-status') || 'pending';
    document.getElementById('edit_score').value = btn.getAttribute('data-score') || '';
    document.getElementById('edit_salary').value = btn.getAttribute('data-salary') || '';
    document.getElementById('edit_requested').value = btn.getAttribute('data-requested') || '';
    document.getElementById('edit_notes').value = btn.getAttribute('data-notes') || '';
  });
})();
