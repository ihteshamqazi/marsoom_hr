<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>رفع قضية / تظلم عمالي</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        body { font-family: 'Tajawal'; background: #f8f9fa; }
        .main-container { max-width: 900px; margin: 50px auto; }
        .glass-card { background: white; border-radius: 12px; box-shadow: 0 5px 25px rgba(0,0,0,0.05); padding: 40px; border-top: 5px solid #dc3545; }
        .form-label { font-weight: bold; color: #333; }
        .alert-legal { background: #fff3cd; border: 1px solid #ffecb5; color: #856404; font-size: 0.9rem; }
    </style>
</head>
<body>

<div class="main-container">
    <div class="glass-card">
        <div class="text-center mb-4">
            <h2 class="fw-bold"><i class="fas fa-balance-scale text-danger"></i> نموذج تظلم / قضية عمالية</h2>
            <p class="text-muted">Labor Dispute & Grievance Form</p>
        </div>

        <div class="alert alert-legal mb-4">
            <i class="fas fa-exclamation-triangle"></i> <strong>تنبيه قانوني:</strong> 
            يتم التعامل مع هذا الطلب بسرية تامة وفقاً للمادة 81 و 82 من نظام العمل السعودي. يرجى تقديم معلومات دقيقة وإرفاق كافة المستندات الثبوتية.
        </div>

        <form id="caseForm" enctype="multipart/form-data">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">نوع القضية / التظلم</label>
                    <select name="case_type" class="form-select" required>
                        <option value="">-- اختر --</option>
                        <option value="Salary Dispute">تأخير / نقص رواتب (Salary)</option>
                        <option value="EOS Dispute">مستحقات نهاية الخدمة (EOS)</option>
                        <option value="Contract Violation">مخالفة عقد العمل (Contract)</option>
                        <option value="Arbitrary Dismissal">فصل تعسفي (Dismissal)</option>
                        <option value="Vacation Denial">رفض إجازات (Vacation)</option>
                        <option value="Work Environment">بيئة العمل / السلامة</option>
                        <option value="Harassment">تحرش / سوء معاملة</option>
                        <option value="Other">أخرى</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">تاريخ الواقعة (Incident Date)</label>
                    <input type="date" name="incident_date" class="form-control" required>
                </div>

                <div class="col-12">
                    <label class="form-label">ضد من؟ (اختياري)</label>
                    <input type="text" name="against_whom" class="form-control" placeholder="اسم المدير أو القسم (إذا وجد)">
                </div>

                <div class="col-12">
                    <label class="form-label">شرح تفصيلي للشكوى</label>
                    <textarea name="description" class="form-control" rows="5" required placeholder="اشرح بالتفصيل ما حدث..."></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label">النتيجة المطلوبة (Desired Outcome)</label>
                    <textarea name="desired_outcome" class="form-control" rows="2" placeholder="ماذا تتوقع كحل لهذه المشكلة؟ (تعويض، تصحيح وضع، إلخ)"></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label">الأدلة والمرفقات (Evidence)</label>
                    <input type="file" name="attachments[]" multiple class="form-control">
                    <div class="form-text">يرجى إرفاق صور إيميلات، كشوف حساب، أو أي مستند يدعم قضيتك.</div>
                </div>
            </div>

            <div class="mt-4 text-center">
                <button type="submit" class="btn btn-danger px-5 py-2 fw-bold">
                    <i class="fas fa-gavel me-2"></i> رفع القضية رسمياً
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$('#caseForm').on('submit', function(e){
    e.preventDefault();
    var formData = new FormData(this);
    
    Swal.fire({
        title: 'تأكيد الرفع؟',
        text: "هل أنت متأكد من البيانات؟ هذا طلب رسمي.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'نعم، اعتمد الرفع'
    }).then((res) => {
        if(res.isConfirmed) {
            $.ajax({
                url: '<?= site_url("users1/submit_labor_case_ajax") ?>',
                type: 'POST', data: formData, processData: false, contentType: false, dataType: 'json',
                success: function(resp) {
                    if(resp.status == 'success') Swal.fire('تم', resp.message, 'success').then(()=>location.reload());
                    else Swal.fire('خطأ', resp.message, 'error');
                }
            });
        }
    });
});
</script>
</body>
</html>