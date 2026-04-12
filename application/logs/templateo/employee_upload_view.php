<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>رفع بيانات الموظفين</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; }
        .container { max-width: 700px; }
        .card { border-radius: 15px; }
    </style>
</head>
<body>
<div class="container my-5">
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h3 class="mb-0">رفع بيانات الموظفين من ملف Excel</h3>
        </div>
        <div class="card-body">
            <p>يمكنك استخدام هذه الصفحة لإضافة أو تحديث بيانات الموظفين دفعة واحدة.</p>
            <div class="alert alert-info">
                <strong>تعليمات:</strong>
                <ul class="mb-0">
                    <li>يجب أن يكون الملف بصيغة <strong>.xlsx</strong>.</li>
                    <li>يجب أن يحتوي الصف الأول في الملف على أسماء الأعمدة (مثل <strong>employee_id</strong>, <strong>id_number</strong>, etc.) مطابقة تمامًا لأسماء الأعمدة في قاعدة البيانات.</li>
                    <li>سيتم استخدام عمود <code>employee_id</code> لتحديث السجلات الموجودة. إذا لم يتم العثور على موظف بنفس الرقم الوظيفي، سيتم إنشاء سجل جديد.</li>
                </ul>
            </div>
            <form action="<?= site_url('users1/process_employee_upload') ?>" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="employee_file" class="form-label">اختر ملف Excel</label>
                    <input class="form-control" type="file" name="employee_file" id="employee_file" required accept=".xlsx">
                </div>
                <div class="d-flex justify-content-end">
                    <a href="<?= site_url('users1/emp_data') ?>" class="btn btn-secondary me-2">رجوع</a>
                    <button type="submit" class="btn btn-success">رفع ومعالجة الملف</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>