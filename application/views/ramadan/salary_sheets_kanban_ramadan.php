<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>أرشيف الفترات</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        body { font-family: 'Tajawal', sans-serif; background-color: #f0f2f5; }
        .page-header { background: #001f3f; color: white; padding: 25px 0; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .sheet-card { border: none; border-radius: 15px; transition: all 0.3s ease; overflow: hidden; height: 100%; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .sheet-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.15); }
        .card-header-custom { background: linear-gradient(135deg, #001f3f 0%, #003366 100%); color: white; padding: 15px; border-bottom: 4px solid #FF8C00; }
        .date-badge { background: #e3f2fd; color: #001f3f; padding: 6px 12px; border-radius: 20px; font-size: 0.9em; font-weight: bold; display: inline-flex; align-items: center; gap: 5px; }
        .action-btn { width: 100%; margin-bottom: 10px; border-radius: 10px; font-weight: 700; padding: 12px; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.2s; }
        
        /* Attendance Button Style */
        .btn-attendance { background-color: #fff; color: #FF8C00; border: 2px solid #FF8C00; }
        .btn-attendance:hover { background-color: #FF8C00; color: white; }
        
        /* Payroll Button Style */
        .btn-payroll { background-color: #fff; color: #001f3f; border: 2px solid #001f3f; }
        .btn-payroll:hover { background-color: #001f3f; color: white; }

        .meta-info { font-size: 0.85em; color: #6c757d; margin-top: 15px; border-top: 1px solid #eee; padding-top: 10px; display: flex; justify-content: space-between; }
        
        /* Delete Button Style */
        .btn-delete-card {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ff6b6b;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-delete-card:hover {
            background: #dc3545;
            color: white;
            transform: scale(1.1);
        }
    </style>
</head>
<body>

<div class="page-header text-center">
    <h1><i class="fas fa-layer-group me-2"></i>سجل الفترات والرواتب</h1>
    <p class="mb-0 opacity-75">اختر الفترة المطلوبة لعرض الحضور أو المسير</p>
</div>

<div class="container">
    <div class="d-flex justify-content-between mb-4 align-items-center">
        <a href="<?php echo site_url('users1/main_hr1'); ?>" class="btn btn-secondary px-4"><i class="fas fa-arrow-right me-2"></i>الرئيسية</a>
        
        <button class="btn btn-success px-4 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#payrollModal">
            <i class="fas fa-plus-circle me-2"></i>فترة جديدة
        </button>
    </div>

    <div class="row g-4">
        <?php if(empty($sheets)): ?>
            <div class="col-12 text-center py-5">
                <div class="text-muted opacity-50"><i class="fas fa-folder-open fa-4x mb-3"></i><br><h3>لا توجد فترات محفوظة.</h3></div>
            </div>
        <?php else: ?>
            <?php foreach($sheets as $sheet): ?>
                <div class="col-md-6 col-lg-4" id="sheet-card-<?php echo $sheet['id']; ?>">
                    <div class="card sheet-card">
                        <div class="card-header-custom">
                            <h5 class="card-title mb-0 d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-file-invoice me-2"></i><?php echo $sheet['type']; ?></span>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-warning text-dark fs-6">#<?php echo $sheet['id']; ?></span>
                                    <button class="btn-delete-card" onclick="deleteSheet(<?php echo $sheet['id']; ?>)" title="حذف الفترة">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="date-badge"><i class="fas fa-play"></i> <?php echo $sheet['start_date']; ?></div>
                                <i class="fas fa-arrow-left text-muted"></i>
                                <div class="date-badge"><i class="fas fa-stop"></i> <?php echo $sheet['end_date']; ?></div>
                            </div>
                            
                            <div class="row g-2">
                                <div class="col-6">
                                    <a href="<?php echo site_url('ramadan/m44_hr_ramadan/'.$sheet['id']); ?>" class="btn btn-attendance action-btn">
                                        <i class="fas fa-fingerprint"></i> الحضور
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="<?php echo site_url('ramadan/payroll_view101_ramadan/'.$sheet['id']); ?>" class="btn btn-payroll action-btn">
                                        <i class="fas fa-calculator"></i> الرواتب
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="<?php echo site_url('users1/violations/'.$sheet['id']); ?>" class="btn btn-payroll action-btn">
                                        <i class="fas fa-calculator"></i> مخالفات                                    </a>
                                </div>
                            </div>

                            <div class="meta-info">
                                <span><i class="fas fa-user-circle me-1"></i> <?php echo $sheet['name']; ?></span>
                                <span><i class="fas fa-clock me-1"></i> <?php echo $sheet['date']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="payrollModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>إنشاء فترة جديدة</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createSheetForm">
                    <div class="mb-3">
                        <label class="form-label fw-bold">اسم الفترة / المسير</label>
                        <input type="text" class="form-control" name="type" placeholder="مثال: رواتب شهر يناير 2025" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">من تاريخ</label>
                            <input type="date" class="form-control" name="start_date" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">إلى تاريخ</label>
                            <input type="date" class="form-control" name="end_date" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" form="createSheetForm" class="btn btn-success">حفظ وإنشاء</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Create Sheet Script
$('#createSheetForm').on('submit', function(e) {
    e.preventDefault();
    var formData = $(this).serialize();

    $.ajax({
        url: "<?php echo site_url('users1/create_new_salary_sheet'); ?>",
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'تم الإنشاء',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload(); 
                });
            } else {
                Swal.fire('خطأ', response.message, 'error');
            }
        },
        error: function() {
            Swal.fire('خطأ', 'فشل الاتصال بالخادم', 'error');
        }
    });
});

// Delete Sheet Function
function deleteSheet(id) {
    Swal.fire({
        title: 'هل أنت متأكد؟',
        text: "سيتم حذف هذه الفترة وجميع البيانات المرتبطة بها نهائياً!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'نعم، احذفها',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "<?php echo site_url('users1/delete_salary_sheet'); ?>", // PHP function we will create next
                type: 'POST',
                data: {id: id},
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Remove the card from HTML nicely
                        $('#sheet-card-' + id).fadeOut(300, function() { 
                            $(this).remove(); 
                            if($('.sheet-card').length === 0) {
                                location.reload(); // Reload if table is empty to show empty message
                            }
                        });
                        
                        Swal.fire(
                            'تم الحذف!',
                            'تم حذف الفترة بنجاح.',
                            'success'
                        );
                    } else {
                        Swal.fire('خطأ', 'حدث خطأ أثناء الحذف', 'error');
                    }
                },
                error: function() {
                    Swal.fire('خطأ', 'فشل الاتصال بالخادم', 'error');
                }
            });
        }
    });
}
</script>

</body>
</html>