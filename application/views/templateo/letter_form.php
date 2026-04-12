<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إنشاء خطاب</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; background-color: #f8f9fa; }
        .form-container { max-width: 800px; margin: 2rem auto; background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="text-primary">إنشاء خطاب <?php echo get_letter_type_name($letter_slug); ?></h3>
                <a href="javascript:window.close()" class="btn btn-outline-secondary">إغلاق</a>
            </div>

            <div class="alert alert-info">
                <h6>بيانات الموظف:</h6>
                <p class="mb-1"><strong>الاسم:</strong> <?php echo $employee->subscriber_name; ?></p>
                <p class="mb-1"><strong>الرقم الوظيفي:</strong> <?php echo $employee->employee_id; ?></p>
                <p class="mb-1"><strong>المسمى الوظيفي:</strong> <?php echo $employee->profession; ?></p>
            </div>

            <form id="letterForm">
                <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                <input type="hidden" name="employee_id" value="<?php echo $employee_id; ?>">
                <input type="hidden" name="letter_slug" value="<?php echo $letter_slug; ?>">

                <div class="mb-3">
                    <label class="form-label">اسم المستلم</label>
                    <input type="text" class="form-control" name="recipient_name" 
                           value="<?php echo isset($existing_letter['recipient_name']) ? $existing_letter['recipient_name'] : 'مصرف الراجحي'; ?>" required>
                </div>

                <?php if ($letter_slug == 'salary-certificate'): ?>
                <div class="mb-3">
                    <label class="form-label">الراتب الشهري (ريال سعودي)</label>
                    <input type="number" step="0.01" class="form-control" name="salary_amount" 
                           value="<?php echo isset($existing_letter['salary_amount']) ? $existing_letter['salary_amount'] : $employee->total_salary; ?>" required>
                </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label">ملاحظات إضافية (اختياري)</label>
                    <textarea class="form-control" name="additional_notes" rows="3"><?php echo isset($existing_letter['additional_notes']) ? $existing_letter['additional_notes'] : ''; ?></textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> حفظ البيانات
                    </button>
                    <button type="button" class="btn btn-primary" onclick="generateLetter()">
                        <i class="fas fa-print"></i> إنشاء وطباعة الخطاب
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $('#letterForm').on('submit', function(e) {
            e.preventDefault();
            saveLetterData();
        });

        function saveLetterData() {
            const formData = new FormData(document.getElementById('letterForm'));
            
            $.ajax({
                url: '<?php echo site_url("users1/save_letter_data"); ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status === 'success') {
                        alert('تم حفظ البيانات بنجاح');
                    } else {
                        alert('فشل في حفظ البيانات: ' + response.message);
                    }
                },
                error: function() {
                    alert('حدث خطأ أثناء حفظ البيانات');
                }
            });
        }

        function generateLetter() {
            // Save first, then generate
            const formData = new FormData(document.getElementById('letterForm'));
            
            $.ajax({
                url: '<?php echo site_url("users1/save_letter_data"); ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status === 'success') {
                        // Open print window
                        const orderId = '<?php echo $order_id; ?>';
                        const letterSlug = '<?php echo $letter_slug; ?>';
                        window.open('<?php echo site_url("users1/generate_letter_print"); ?>/' + orderId + '/' + letterSlug, '_blank');
                    } else {
                        alert('فشل في حفظ البيانات: ' + response.message);
                    }
                },
                error: function() {
                    alert('حدث خطأ أثناء حفظ البيانات');
                }
            });
        }
    </script>
</body>
</html>

<?php
function get_letter_type_name($slug) {
    $types = [
        'salary-certificate' => 'إثبات مزايا وظيفية',
        'salary-commitment' => 'التزام تحويل راتب',
        'salary-commitment-marsoom' => 'التزام تحويل راتب (مرسوم)',
        'embassy-letter' => 'خطاب للسفارة',
        'eos-certificate' => 'إفادة نهاية الخدمة'
    ];
    return $types[$slug] ?? $slug;
}
?>