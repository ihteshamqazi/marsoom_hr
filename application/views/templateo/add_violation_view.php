<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />

    <style>
        body { font-family: 'Tajawal', sans-serif; background-color: #f4f6f9; }
        .main-card { max-width: 800px; margin: 40px auto; background: white; border-radius: 15px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .form-label { font-weight: bold; color: #001f3f; }
    </style>
</head>
<body>

<div class="container">
    <div class="main-card">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h3 class="fw-bold text-dark"><i class="fas fa-file-contract text-warning me-2"></i> تسجيل ملاحظة / مخالفة</h3>
            <a href="<?= site_url('users1/violations_list') ?>" class="btn btn-outline-secondary btn-sm rounded-pill">
                <i class="fas fa-list me-1"></i> عرض السجل
            </a>
        </div>

        <form id="violationForm">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label">الموظف <span class="text-danger">*</span></label>
                    <select class="form-select select2" name="employee_id" required>
                        <option value="">بحث عن موظف...</option>
                        <?php foreach($employees as $emp): ?>
                            <option value="<?= $emp['employee_id'] ?>">
                                <?= $emp['subscriber_name'] ?> (<?= $emp['employee_id'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">القسم / الإدارة</label>
                    <select class="form-select" name="department" required>
                        <option value="">-- اختر --</option>
                        <option value="الموارد البشرية">الموارد البشرية</option>
                        <option value="المالية">المالية</option>
                        <option value="التشغيل">التشغيل</option>
                        <option value="تقنية المعلومات">تقنية المعلومات</option>
                        <option value="التحصيل">التحصيل</option>
                        <option value="القانونية">القانونية</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">تاريخ الملاحظة</label>
                    <input type="date" name="violation_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">المشرف المباشر</label>
                    <input type="text" name="supervisor_name" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">مبلغ الخصم (إن وجد)</label>
                    <div class="input-group">
                        <input type="number" step="0.01" name="amount" class="form-control" value="0">
                        <span class="input-group-text">SAR</span>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label">تفاصيل الملاحظة / المخالفة</label>
                    <textarea name="hr_note" class="form-control" rows="5" placeholder="اكتب تفاصيل الواقعة هنا..." required></textarea>
                </div>

                <div class="col-12 text-center mt-4">
                    <button type="submit" class="btn btn-primary px-5 fw-bold rounded-pill">
                        <i class="fas fa-save me-2"></i> حفظ وإرسال للموظف
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    $('.select2').select2({
        theme: 'bootstrap-5',
        dir: "rtl"
    });

    $('#violationForm').on('submit', function(e){
        e.preventDefault();
        
        $.ajax({
            url: '<?= site_url("users1/submit_violation_ajax") ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if(res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم الحفظ',
                        text: res.message,
                        confirmButtonText: 'حسناً'
                    }).then(() => {
                        window.location.href = '<?= site_url("users1/violations_list") ?>';
                    });
                } else {
                    Swal.fire('خطأ', res.message, 'error');
                }
            },
            error: function() {
                Swal.fire('خطأ', 'حدث خطأ في الاتصال', 'error');
            }
        });
    });
});
</script>
</body>
</html>