<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل وإضافة بصمات الموظفين</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <style>
        :root{--marsom-blue:#001f3f;--marsom-orange:#FF8C00;}
        body{font-family:'Tajawal',sans-serif;overflow-y:auto;background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);background-size:400% 400%;animation:grad 20s ease infinite;color:#343a40;}
        @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        .main-container{padding:30px 15px;z-index:1}
        .page-title{font-family:'El Messiri',sans-serif;font-weight:700;font-size:2.8rem;color:#fff;margin-bottom:32px;text-align:center;text-shadow:0 3px 6px rgba(0,0,0,.4)}
        .content-card{background:rgba(255,255,255,.92);backdrop-filter:blur(8px);border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,.15);padding:25px}
        .table thead th{background-color:#001f3f !important;color:#fff;text-align:center;vertical-align:middle;}
        .table tbody td{text-align:center;vertical-align:middle;font-size:14px;white-space:nowrap}
        .top-actions{position:fixed;top:12px;right:12px;display:flex;gap:10px;z-index:5}
        .top-actions a{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);color:#fff;text-decoration:none;border-radius:10px;padding:8px 14px;display:inline-flex;align-items:center;gap:8px;}
        .form-control-sm.time-input { max-width: 220px; display: inline-block; }
        .btn-save-punch { min-width: 80px; }
    </style>
</head>
<body>

<div class="top-actions">
    <a href="javascript:history.back()"><i class="fas fa-arrow-right"></i><span>رجوع</span></a>
    <a href="<?php echo site_url('users1/main_hr1'); ?>"><i class="fas fa-home"></i><span>الرئيسية</span></a>
</div>

<div class="main-container container-fluid">
    <div class="text-center"><h1 class="page-title">تعديل وإضافة بصمات الموظفين</h1></div>
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card content-card">
                <div class="card-body">
                    <div id="alert-container"></div>
                    
                    <form method="get" action="<?php echo site_url('users1/manual_attendance_edit'); ?>" class="row g-3 align-items-end bg-light p-3 rounded mb-4 border">
                        <div class="col-md-4">
                            <label for="date" class="form-label fw-bold">اختر التاريخ:</label>
                            <input type="date" class="form-control" id="date" name="date" value="<?php echo html_escape($selected_date); ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="employee_id" class="form-label fw-bold">الرقم الوظيفي (اختياري):</label>
                            <input type="text" class="form-control" id="employee_id" name="employee_id" placeholder="ابحث بالرقم الوظيفي..." value="<?php echo html_escape($selected_employee_id); ?>">
                        </div>
                        <div class="col-md-4 d-grid">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search me-2"></i>عرض البيانات</button>
                        </div>
                    </form>

                    <div class="text-end mb-3">
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addPunchModal">
                            <i class="fas fa-plus-circle me-2"></i>إضافة بصمة يدوية
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>الرقم الوظيفي</th>
                                    <th>اسم الموظف</th>
                                    <th>أول بصمة (دخول)</th>
                                    <th>آخر بصمة (خروج)</th>
                                    <th>إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($attendance_data)): ?>
                                    <?php foreach($attendance_data as $row): ?>
                                        <tr>
                                            <td><?php echo html_escape($row['emp_code']); ?></td>
                                            <td><?php echo html_escape($row['subscriber_name']); ?></td>
                                            <td>
                                                <?php if($row['first_punch_id']): ?>
                                                    <input type="datetime-local" class="form-control form-control-sm time-input" 
                                                           id="punch-<?php echo $row['first_punch_id']; ?>" 
                                                           value="<?php echo date('Y-m-d\TH:i', strtotime($row['first_punch_time'])); ?>">
                                                <?php else: echo '—'; endif; ?>
                                            </td>
                                            <td>
                                                <?php if($row['last_punch_id'] && $row['last_punch_id'] != $row['first_punch_id']): ?>
                                                    <input type="datetime-local" class="form-control form-control-sm time-input" 
                                                           id="punch-<?php echo $row['last_punch_id']; ?>" 
                                                           value="<?php echo date('Y-m-d\TH:i', strtotime($row['last_punch_time'])); ?>">
                                                <?php else: echo '—'; endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-success btn-save-punch" 
                                                        data-first-id="<?php echo $row['first_punch_id']; ?>"
                                                        data-last-id="<?php echo $row['last_punch_id']; ?>">
                                                    <i class="fas fa-save me-1"></i> حفظ
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center text-muted">لا توجد بيانات حضور لهذا اليوم.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addPunchModal" tabindex="-1" aria-labelledby="addPunchModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addPunchModalLabel">إضافة بصمة يدوية جديدة</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="add-punch-form">
            <div class="mb-3">
                <label for="add_employee_id" class="form-label">الموظف <span class="text-danger">*</span></label>
                <select class="form-select" id="add_employee_id" name="employee_id" required>
                    <option></option>
                    <?php if(!empty($all_employees)): ?>
                        <?php foreach($all_employees as $emp): ?>
                            <option value="<?php echo html_escape($emp['username']); ?>"><?php echo html_escape($emp['name']) . ' (' . html_escape($emp['username']) . ')'; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="add_punch_date" class="form-label">تاريخ البصمة <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="add_punch_date" name="punch_date" value="<?php echo html_escape($selected_date); ?>" required>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="add_check_in_time" class="form-label">وقت الدخول (اختياري)</label>
                    <input type="time" class="form-control" id="add_check_in_time" name="check_in_time">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="add_check_out_time" class="form-label">وقت الخروج (اختياري)</label>
                    <input type="time" class="form-control" id="add_check_out_time" name="check_out_time">
                </div>
            </div>
            <div id="addPunchError" class="text-danger d-none mb-3">يجب إدخال وقت الدخول أو وقت الخروج على الأقل.</div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
        <button type="button" class="btn btn-primary" id="btn-save-new-punch">حفظ البصمات</button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    
    // --- Initialize Select2 ---
    $('#add_employee_id').select2({
        placeholder: 'ابحث عن موظف بالاسم أو الرقم',
        dropdownParent: $('#addPunchModal'),
        theme: 'bootstrap-5'
    });

    function showAlert(message, type = 'success') {
        const alertHtml = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
        $('#alert-container').html(alertHtml);
        setTimeout(() => $('.alert').alert('close'), 5000);
    }

    // --- Logic for SAVING EDITS ---
    $('.btn-save-punch').on('click', function() {
        const btn = $(this);
        // ... (rest of your existing save logic is fine)
        const firstId = btn.data('first-id');
        const lastId = btn.data('last-id');
        let requests = [];

        if (firstId) {
            const firstTime = $('#punch-' + firstId).val();
            if(firstTime) requests.push({ record_id: firstId, new_time: firstTime.replace('T', ' ') + ':00' });
        }
        if (lastId && lastId !== firstId) {
            const lastTime = $('#punch-' + lastId).val();
            if(lastTime) requests.push({ record_id: lastId, new_time: lastTime.replace('T', ' ') + ':00' });
        }

        if (requests.length === 0) return;
        
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        let completed = 0, errors = 0;
        requests.forEach(req => {
            $.ajax({
                url: "<?php echo site_url('users1/ajax_update_punch'); ?>",
                type: 'POST',
                data: { record_id: req.record_id, new_time: req.new_time, '<?php echo $csrf_name; ?>': '<?php echo $csrf_hash; ?>' },
                dataType: 'json',
                success: res => { if (res.status !== 'success') errors++; },
                error: () => errors++,
                complete: () => {
                    completed++;
                    if (completed === requests.length) {
                        showAlert(errors > 0 ? 'حدث خطأ أثناء تحديث بعض السجلات.' : 'تم حفظ التغييرات بنجاح!', errors > 0 ? 'danger' : 'success');
                        btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> حفظ');
                    }
                }
            });
        });
    });

    // --- NEW: Logic for ADDING a NEW punch ---
    $('#btn-save-new-punch').on('click', function() {
        const btn = $(this);
        $('#addPunchError').addClass('d-none');
        
        const employeeId = $('#add_employee_id').val();
        const punchDate = $('#add_punch_date').val();
        const checkInTime = $('#add_check_in_time').val();
        const checkOutTime = $('#add_check_out_time').val();

        // Validation
        if (!employeeId || !punchDate) {
            showAlert('الرجاء اختيار الموظف وتحديد التاريخ.', 'danger');
            return;
        }
        if (!checkInTime && !checkOutTime) {
            $('#addPunchError').removeClass('d-none');
            return;
        }

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url: "<?php echo site_url('users1/ajax_add_punch'); ?>",
            type: 'POST',
            data: {
                employee_id: employeeId,
                punch_date: punchDate,
                check_in_time: checkInTime,
                check_out_time: checkOutTime,
                '<?php echo $csrf_name; ?>': '<?php echo $csrf_hash; ?>'
            },
            dataType: 'json',
            success: function(response) {
                // Update CSRF hash
                $('input[name="<?php echo $csrf_name; ?>"]').val(response.csrf_hash);
                
                if (response.status === 'success') {
                    showAlert(response.message, 'success');
                    $('#addPunchModal').modal('hide');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert(response.message || 'An unknown error occurred.', 'danger');
                }
            },
            error: function() {
                showAlert('حدث خطأ في الاتصال بالخادم.', 'danger');
            },
            complete: function() {
                 btn.prop('disabled', false).text('حفظ البصمات');
            }
        });
    });
});
</script>

</body>
</html>