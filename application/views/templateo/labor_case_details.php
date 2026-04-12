<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ملف القضية #<?= $req['id'] ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        body { font-family: 'Tajawal'; background: #f4f4f4; padding-bottom: 80px; }
        .main-container { max-width: 1000px; margin: 40px auto; }
        .case-header { background: #333; color: white; padding: 20px; border-radius: 10px 10px 0 0; display: flex; justify-content: space-between; align-items: center; }
        .paper-card { background: white; padding: 40px; box-shadow: 0 0 15px rgba(0,0,0,0.1); border-radius: 0 0 10px 10px; min-height: 600px; }
        
        .field-label { color: #666; font-size: 0.9rem; margin-bottom: 3px; }
        .field-value { font-weight: bold; font-size: 1.1rem; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 20px; display: block; }
        
        .section-title { border-bottom: 2px solid #dc3545; padding-bottom: 10px; margin: 30px 0 20px; font-weight: bold; color: #dc3545; text-transform: uppercase; }

        /* Print Styles */
        @media print {
            body { background: white; padding: 0; }
            .main-container { margin: 0; max-width: 100%; }
            .case-header, .action-bar, .btn { display: none !important; }
            .paper-card { box-shadow: none; padding: 0; }
            .print-header { display: block !important; text-align: center; margin-bottom: 30px; border-bottom: 2px solid black; padding-bottom: 20px; }
        }
    </style>
</head>
<body>

<div class="main-container">
    <div class="case-header">
        <h4 class="m-0">ملف القضية العمالية #<?= $req['id'] ?></h4>
        <div>
            <button onclick="window.print()" class="btn btn-light btn-sm"><i class="fas fa-print"></i> طباعة تقرير</button>
            <a href="javascript:history.back()" class="btn btn-outline-light btn-sm">خروج</a>
        </div>
    </div>

    <div class="paper-card">
        <div class="print-header" style="display:none;">
            <h2>شركة مرسوم لتحصيل الديون</h2>
            <h4>تقرير قضية / تظلم عمالي داخلي</h4>
            <small>تاريخ التقرير: <?= date('Y-m-d') ?></small>
        </div>

        <div class="row">
            <div class="col-md-6">
                <span class="field-label">المدعي (الموظف)</span>
                <span class="field-value"><?= $req['subscriber_name'] ?> (<?= $req['emp_code'] ?>)</span>
            </div>
            <div class="col-md-6">
                <span class="field-label">القسم / الإدارة</span>
                <span class="field-value"><?= $req['department'] ?></span>
            </div>
            <div class="col-md-6">
                <span class="field-label">تاريخ التعيين</span>
                <span class="field-value"><?= $req['joining_date'] ?></span>
            </div>
            <div class="col-md-6">
                <span class="field-label">الطرف الآخر (المدعى عليه)</span>
                <span class="field-value"><?= $req['against_whom'] ? $req['against_whom'] : 'الشركة (عام)' ?></span>
            </div>
        </div>

        <h5 class="section-title">تفاصيل الواقعة</h5>
        <div class="row">
            <div class="col-md-6">
                <span class="field-label">نوع القضية</span>
                <span class="field-value"><?= $req['case_type'] ?></span>
            </div>
            <div class="col-md-6">
                <span class="field-label">تاريخ الواقعة</span>
                <span class="field-value"><?= $req['incident_date'] ?></span>
            </div>
            <div class="col-12">
                <span class="field-label">وصف الشكوى</span>
                <div class="p-3 bg-light border rounded mb-3"><?= nl2br($req['description']) ?></div>
            </div>
            <div class="col-12">
                <span class="field-label">الطلبات / التعويض</span>
                <span class="field-value text-success"><?= $req['desired_outcome'] ?></span>
            </div>
        </div>

        <div class="d-print-none">
            <h5 class="section-title">الأدلة والمرفقات</h5>
            <?php if(!empty($req['attachments'])): 
                $files = explode(',', $req['attachments']);
                foreach($files as $f): if(trim($f)): ?>
                <a href="<?= base_url('uploads/documents/'.$f) ?>" target="_blank" class="btn btn-outline-secondary btn-sm m-1">
                    <i class="fas fa-paperclip"></i> ملف
                </a>
            <?php endif; endforeach; endif; ?>
        </div>

        <h5 class="section-title">القرارات الإدارية والقانونية</h5>
        
        <div class="mb-3">
            <span class="field-label">رأي الموارد البشرية (HR Opinion)</span>
            <div class="p-2 border bg-light"><?= $req['hr_notes'] ? $req['hr_notes'] : 'بانتظار المراجعة...' ?></div>
        </div>

        <div class="mb-3">
            <span class="field-label">الرأي القانوني / القرار النهائي</span>
            <div class="p-2 border bg-light"><?= $req['legal_notes'] ? $req['legal_notes'] : 'بانتظار المراجعة...' ?></div>
        </div>

        <div class="text-end mt-5">
            <strong>الحالة النهائية: </strong> 
            <span class="badge bg-dark fs-5"><?= $req['status'] ?></span>
        </div>
    </div>
</div>

<?php 
    $current_user = $this->session->userdata('username');
    if($req['status'] != 'Closed' && $req['current_approver'] == $current_user): 
?>
<div class="fixed-bottom bg-white border-top p-3 text-center d-print-none shadow-lg">
    <div class="container d-flex gap-2 justify-content-center align-items-center">
        <textarea id="admin_notes" class="form-control" rows="1" placeholder="أضف ملاحظاتك أو القرار هنا..." style="max-width:400px"></textarea>
        
        <button class="btn btn-success fw-bold" onclick="decide(<?= $req['id'] ?>, 'approve')">
            <i class="fas fa-check"></i> اعتماد / تحويل
        </button>
        <button class="btn btn-danger fw-bold" onclick="decide(<?= $req['id'] ?>, 'reject')">
            <i class="fas fa-times"></i> رفض وإغلاق
        </button>
    </div>
</div>
<?php endif; ?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function decide(id, action) {
    let notes = $('#admin_notes').val();
    if(!notes) { Swal.fire('تنبيه', 'يرجى كتابة الملاحظات أو القرار قبل الاعتماد', 'warning'); return; }

    $.post('<?= site_url("users1/do_labor_case_action") ?>', 
    { 
        req_id: id, action: action, notes: notes,
        '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
    }, function() {
        Swal.fire('تم', 'تم حفظ القرار بنجاح', 'success').then(()=>location.reload());
    });
}
</script>

</body>
</html>