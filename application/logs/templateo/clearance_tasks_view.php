<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <title>مهام المخالصة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Tajawal', sans-serif; background-color: #f4f6f9; }
        .card { box-shadow: 0 0 15px rgba(0,0,0,.05); border:0; }
        .card-header { background-color: #001f3f; color: white; }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="card">
            <div class="card-header"><h3><i class="fas fa-tasks me-2"></i>مهام المخالصة المعينة لي</h3></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>رقم الطلب</th>
                                <th>اسم الموظف المستقيل</th>
                                <th>المهمة المطلوبة</th>
                                <th>الحالة</th>
                                <th>إجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($tasks)): ?>
                                <tr><td colspan="5" class="text-center text-muted py-4">لا توجد لديك مهام حالياً.</td></tr>
                            <?php else: ?>
                                <?php foreach ($tasks as $task): ?>
                                    <tr>
                                        <td><?php echo html_escape($task['request_id']); ?></td>
                                        <td><?php echo html_escape($task['resigning_employee_name']); ?></td>
                                        <td><?php echo html_escape($task['parameter_name']); ?></td>
                                        <td><span class="badge bg-warning text-dark"><?php echo html_escape($task['status']); ?></span></td>
                                        <td>
                                            <?php if ($task['task_type'] === 'initial_approval'): ?>
                                                <button class="btn btn-sm btn-success manager-action-btn" data-order-id="<?php echo $task['request_id']; ?>" data-action="2">موافقة</button>
                                                <button class="btn btn-sm btn-danger manager-action-btn" data-order-id="<?php echo $task['request_id']; ?>" data-action="3">رفض</button>
                                            <?php elseif ($task['task_type'] === 'department_task'): ?>
                                                <button class="btn btn-sm btn-success dept-action-btn" data-task-id="<?php echo $task['task_id']; ?>" data-action="approved">موافقة</button>
                                                <button class="btn btn-sm btn-danger dept-action-btn" data-task-id="<?php echo $task['task_id']; ?>" data-action="rejected">رفض</button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="actionModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header"><h5 class="modal-title" id="actionModalTitle">اتخاذ إجراء</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body">
            <form id="actionForm" enctype="multipart/form-data">
              <input type="hidden" name="task_id" id="modalTaskId">
              <input type="hidden" name="action" id="modalAction">
              <p>هل أنت متأكد من <strong id="actionText"></strong> لهذه المهمة؟</p>
              <div id="rejectionReasonGroup" class="mb-3 d-none">
                <label for="rejection_reason" class="form-label">سبب الرفض (مطلوب)</label>
                <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="3"></textarea>
              </div>
              <div class="mb-3">
                <label for="attachment" class="form-label">رفع مرفق (اختياري)</label>
                <input type="file" name="attachment" id="attachment" class="form-control">
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
            <button type="button" class="btn btn-primary" id="submitActionBtn">تأكيد</button>
          </div>
        </div>
      </div>
    </div>
    
    <div class="modal fade" id="managerRejectionModal" tabindex="-1">
        </div>
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function() {
        // --- Logic for DEPARTMENTAL tasks ---
        var actionModal = new bootstrap.Modal(document.getElementById('actionModal'));
        
        $('.dept-action-btn').on('click', function() {
            var taskId = $(this).data('task-id');
            var action = $(this).data('action');

            // Reset form and populate modal fields
            $('#actionForm')[0].reset();
            $('#modalTaskId').val(taskId);
            $('#modalAction').val(action);
            
            if (action === 'approved') {
                $('#actionText').text('الموافقة');
                $('#rejectionReasonGroup').addClass('d-none');
                $('#rejection_reason').prop('required', false);
            } else {
                $('#actionText').text('الرفض');
                $('#rejectionReasonGroup').removeClass('d-none');
                $('#rejection_reason').prop('required', true);
            }
            
            actionModal.show();
        });

        $('#submitActionBtn').on('click', function() {
            var form = document.getElementById('actionForm');
            var formData = new FormData(form);
            
            if (formData.get('action') === 'rejected' && formData.get('rejection_reason').trim() === '') {
                alert('سبب الرفض مطلوب.');
                return;
            }

            $.ajax({
                url: "<?php echo site_url('users1/submit_clearance_decision'); ?>",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        window.location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('حدث خطأ في الاتصال بالخادم.');
                }
            });
        });

        // --- Logic for MANAGER's initial approval ---
        var managerRejectionModal = new bootstrap.Modal(document.getElementById('managerRejectionModal'));
        // ... (The manager's logic you already have is fine) ...
    });
    </script>
</body>
</html>