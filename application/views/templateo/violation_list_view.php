<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= html_escape($page_title ?? 'سجل المخالفات') ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body { font-family: 'Tajawal', sans-serif; background-color: #f4f6f9; color: #333; }
        .main-container { max-width: 1000px; margin: 40px auto; padding: 0 15px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 2px solid #e9ecef; padding-bottom: 15px; }
        .card-custom { border: none; border-radius: 12px; background: white; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 25px; transition: transform 0.2s; }
        .card-header-custom { background-color: #fff; border-bottom: 1px solid #f0f0f0; padding: 15px 20px; border-radius: 12px 12px 0 0; display: flex; justify-content: space-between; align-items: center; }
        .card-body-custom { padding: 20px; }
        .hr-note-box { background-color: #f8f9fa; border-right: 4px solid #001f3f; padding: 15px; border-radius: 4px; margin-bottom: 15px; }
        .feedback-form-box { background-color: #fff; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; margin-top: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .feedback-textarea { resize: none; border: 1px solid #ced4da; background-color: #ffffff !important; }
        .feedback-textarea:focus { border-color: #80bdff; box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25); }
        .feedback-read-only { background-color: #e8f5e9; border-right: 4px solid #28a745; padding: 15px; border-radius: 4px; margin-top: 15px; }
        .badge-deduction { background-color: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
        .badge-warning-custom { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        /* Dropdown style */
        .action-btn { cursor: pointer; color: #6c757d; transition: color 0.2s; }
        .action-btn:hover { color: #343a40; }
    </style>
</head>
<body>

<div class="main-container">
    
    <div class="page-header">
        <div>
            <h3 class="fw-bold text-dark m-0"><i class="fas fa-clipboard-list text-primary me-2"></i> سجل الملاحظات والمخالفات</h3>
            <p class="text-muted small m-0 mt-1">عرض ومتابعة الملاحظات الإدارية والردود</p>
        </div>
        <div>
            <?php if(isset($is_hr) && $is_hr): ?>
                <a href="<?= site_url('users1/add_violation_note') ?>" class="btn btn-primary rounded-pill px-4 shadow-sm">
                    <i class="fas fa-plus me-2"></i> تسجيل جديد
                </a>
            <?php else: ?>
                <a href="<?= site_url('users1') ?>" class="btn btn-secondary rounded-pill px-4 shadow-sm">
                    <i class="fas fa-home me-2"></i> الرئيسية
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if(empty($violations)): ?>
        <div class="text-center py-5">
            <div class="mb-3"><i class="far fa-folder-open fa-3x text-muted opacity-50"></i></div>
            <h5 class="text-muted">لا توجد سجلات حالياً</h5>
        </div>
    <?php else: ?>
        
        <?php foreach($violations as $row): ?>
            <div class="card-custom" id="card-<?= $row['id'] ?>">
                <div class="card-header-custom">
                    <div class="d-flex align-items-center">
                        <span class="fw-bold text-dark">#<?= $row['id'] ?></span>
                        <span class="mx-2 text-muted">|</span>
                        <i class="far fa-calendar-alt text-secondary ms-1"></i> <?= $row['violation_date'] ?>
                        
                        <?php if(isset($is_hr) && $is_hr): ?>
                            <span class="mx-2 text-muted">|</span>
                            <span class="badge bg-light text-dark border">
                                <i class="far fa-user ms-1"></i> <?= html_escape($row['emp_name']) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <?php if($row['amount'] > 0): ?>
                                <span class="badge badge-deduction rounded-pill px-3 py-2">
                                    <i class="fas fa-minus-circle me-1"></i> خصم: <?= $row['amount'] ?> SAR
                                </span>
                            <?php else: ?>
                                <span class="badge badge-warning-custom rounded-pill px-3 py-2">
                                    <i class="fas fa-exclamation-circle me-1"></i> تنبيه إداري
                                </span>
                            <?php endif; ?>
                        </div>

                        <?php if(isset($is_hr) && $is_hr): ?>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light rounded-circle action-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                    <li>
                                        <a class="dropdown-item edit-violation-btn" href="#" 
                                           data-id="<?= $row['id'] ?>"
                                           data-amount="<?= $row['amount'] ?>"
                                           data-date="<?= $row['violation_date'] ?>"
                                           data-note="<?= html_escape($row['hr_note']) ?>">
                                           <i class="fas fa-edit text-warning me-2"></i> تعديل
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item delete-violation-btn text-danger" href="#" data-id="<?= $row['id'] ?>">
                                            <i class="fas fa-trash-alt me-2"></i> حذف
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card-body-custom">
                    <div class="row">
                        <div class="col-md-12">
                           <h6 class="fw-bold text-secondary mb-2">
                                <i class="fas fa-user-tie me-1"></i> ملاحظة الإدارة
                           </h6>
                            <div class="hr-note-box">
                                <?= nl2br(html_escape($row['hr_note'])) ?>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <small class="text-muted"><i class="fas fa-user-shield me-1"></i> المشرف: <strong><?= html_escape($row['supervisor_name']) ?></strong></small>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted"><i class="fas fa-building me-1"></i> القسم: <strong><?= html_escape($row['department']) ?></strong></small>
                        </div>
                    </div>

                    <hr class="my-3 opacity-25">

                    <?php if(!empty($row['employee_feedback'])): ?>
                        <div class="feedback-read-only">
                            <h6 class="fw-bold text-success mb-2"><i class="fas fa-reply me-1"></i> رد الموظف:</h6>
                            <p class="mb-0 text-dark"><?= nl2br(html_escape($row['employee_feedback'])) ?></p>
                        </div>
                    <?php else: ?>
                        <?php if($row['employee_id'] == $current_user): ?>
                            <div class="feedback-form-box">
                                <h6 class="fw-bold text-primary mb-3"><i class="fas fa-pen me-2"></i> كتابة رد / توضيح:</h6>
                                <form class="feedbackForm">
                                    <input type="hidden" name="violation_id" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                                    <div class="mb-3">
                                        <textarea name="employee_feedback" class="form-control feedback-textarea" rows="3" placeholder="يرجى كتابة ردك هنا..." required></textarea>
                                    </div>
                                    <div class="text-start">
                                        <button type="submit" class="btn btn-primary px-4 fw-bold"><i class="fas fa-paper-plane me-2"></i> إرسال</button>
                                    </div>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="mt-3 p-3 bg-light border rounded text-center text-muted fst-italic">
                                <i class="far fa-clock text-warning me-2"></i> بانتظار رد الموظف...
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">تعديل المخالفة</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editViolationForm">
        <div class="modal-body">
            <input type="hidden" name="id" id="edit_id">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            
            <div class="mb-3">
                <label class="form-label">تاريخ المخالفة</label>
                <input type="date" name="violation_date" id="edit_date" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">المبلغ (SAR)</label>
                <input type="number" step="0.01" name="amount" id="edit_amount" class="form-control" required>
                <div class="form-text">ضع 0 إذا كان مجرد تنبيه</div>
            </div>

            <div class="mb-3">
                <label class="form-label">ملاحظة الإدارة</label>
                <textarea name="hr_note" id="edit_note" class="form-control" rows="4" required></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
            <button type="submit" class="btn btn-warning">حفظ التعديلات</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {

    // 1. Submit Employee Feedback
    $(document).on('submit', '.feedbackForm', function(e){
        e.preventDefault();
        var form = $(this);
        var btn = form.find('button[type="submit"]');
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> جاري الإرسال...');

        $.ajax({
            url: '<?= site_url("users1/submit_violation_feedback") ?>',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(res) {
                if(res.status === 'success') {
                    Swal.fire({icon: 'success', title: 'تم الإرسال', timer: 1500, showConfirmButton: false}).then(() => location.reload());
                } else {
                    Swal.fire('خطأ', res.message, 'error');
                    btn.prop('disabled', false).html('إرسال');
                }
            }
        });
    });

    // 2. Open Edit Modal
    $(document).on('click', '.edit-violation-btn', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var amount = $(this).data('amount');
        var date = $(this).data('date');
        var note = $(this).data('note');

        $('#edit_id').val(id);
        $('#edit_amount').val(amount);
        $('#edit_date').val(date);
        $('#edit_note').val(note);

        $('#editModal').modal('show');
    });

    // 3. Submit Edit Form
    $('#editViolationForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        $.ajax({
            url: '<?= site_url("users1/update_violation_ajax") ?>',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(res) {
                if(res.status === 'success') {
                    $('#editModal').modal('hide');
                    Swal.fire('تم!', 'تم تعديل البيانات بنجاح', 'success').then(() => location.reload());
                } else {
                    Swal.fire('خطأ', res.message, 'error');
                }
            }
        });
    });

    // 4. Handle Delete
    $(document).on('click', '.delete-violation-btn', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "سيتم حذف المخالفة والخصم المرتبط بها نهائياً!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم، احذفها',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= site_url("users1/delete_violation_ajax") ?>',
                    type: 'POST',
                    data: {
                        id: id,
                        '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
                    },
                    dataType: 'json',
                    success: function(res) {
                        if(res.status === 'success') {
                            $('#card-' + id).fadeOut();
                            Swal.fire('تم الحذف!', res.message, 'success');
                        } else {
                            Swal.fire('خطأ', res.message, 'error');
                        }
                    }
                });
            }
        });
    });
});
</script>
</body>
</html>