<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إضافة مستند جديد</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; background-color: #f8fbfd; padding: 30px; }
        .card { border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .card-header { background: linear-gradient(90deg, #537bbd, #6a93cb); color: white; border-radius: 15px 15px 0 0 !important; font-weight: bold; font-size: 1.2rem; }
        .btn-custom { background-color: #17a2b8; color: white; border-radius: 8px; padding: 10px 20px; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="card-header text-center">
            إرفاق مستندات الموظف
        </div>
        <div class="card-body">
            <form action="<?php echo site_url('users1/save_document'); ?>" method="post" enctype="multipart/form-data">
                
                <div class="mb-3">
                    <label class="form-label">الموظف</label>
                    <select name="employee_id" class="form-select" required>
                        <option value="">اختر الموظف...</option>
                        <?php foreach($employees as $emp): ?>
                            <option value="<?php echo $emp['employee_id']; ?>">
                                <?php echo $emp['employee_id'] . ' - ' . $emp['subscriber_name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">نوع المستند</label>
                    <select name="document_type" class="form-select" required>
                        <option value="">اختر النوع...</option>
                        <option value="هوية وطنية / إقامة">هوية وطنية / إقامة</option>
                        <option value="جواز سفر">جواز سفر</option>
                        <option value="عقد عمل">عقد عمل</option>
                        <option value="شهادة طبية">شهادة طبية</option>
                        <option value="أخرى">أخرى</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">تاريخ الانتهاء (اختياري)</label>
                    <input type="date" name="expiry_date" class="form-control">
                </div>

                <div class="mb-4">
                    <label class="form-label">الملفات المرفقة (يمكنك تحديد أكثر من ملف)</label>
                    <input type="file" name="documents[]" class="form-control" multiple required>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-custom">حفظ ورفع المستندات</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>