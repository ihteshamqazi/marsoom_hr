<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $page_title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        :root{--marsom-blue:#001f3f;--marsom-orange:#FF8C00;--text-light:#fff;--text-dark:#343a40;}
        body{font-family:'Tajawal',sans-serif;overflow-y:auto !important;background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);background-size:400% 400%;animation:grad 20s ease infinite;color:var(--text-dark);position:relative}
        @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        
        .main-container{padding:30px 15px;position:relative;z-index:1}
        .page-title{font-family:'El Messiri',sans-serif;font-weight:700;font-size:2.6rem;color:var(--text-light);margin-bottom:24px;text-align:center;position:relative;display:inline-block;padding-bottom:10px;text-shadow:0 3px 6px rgba(0,0,0,.4)}
        .page-title::after{content:'';position:absolute;width:160px;height:4px;background:linear-gradient(90deg,var(--marsom-blue),var(--marsom-orange));bottom:0;left:50%;transform:translateX(-50%);border-radius:2px}
        
        .form-card{background:rgba(255,255,255,.95);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.3);border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,.15);padding:25px}
        .top-actions{position:fixed;top:12px;right:12px;display:flex;gap:10px;z-index:5}
        .top-actions a{background:rgba(255,255,255,.12);border:1px solid var(--glass-border);color:#fff;text-decoration:none;border-radius:10px;padding:8px 14px;display:inline-flex;align-items:center;gap:8px;transition:.25s}
        .top-actions a:hover{background:rgba(255,255,255,.2);color:var(--marsom-orange)}
        
        .section-title{font-size:1.1rem;font-weight:700;color:#0b2447;margin-bottom:16px;padding-bottom:8px;border-bottom: 2px solid #eee;display:flex;align-items:center;gap:8px}
        .section-title i{opacity:.85}
        
        .form-label{font-weight:500;color:#333;font-size:.9rem;margin-bottom:6px}
        .form-control, .form-select { border-radius: 8px; }
    </style>
</head>
<body>

<div class="top-actions">
    <a href="<?php echo site_url('users1/emp_data101'); ?>"><i class="fas fa-arrow-right"></i><span>رجوع</span></a>
</div>

<div class="main-container container-fluid">
    <div class="text-center"><h1 class="page-title"><?= $page_title ?></h1></div>

    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="card form-card">
                <div class="card-body">
                    
                    <?php if($this->session->flashdata('success')): ?>
                        <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
                    <?php endif; ?>
                    <?php if($this->session->flashdata('error')): ?>
                        <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
                    <?php endif; ?>
                    <form action="<?= site_url('users1/save_employee') ?>" method="post">
                        <input type="hidden" name="id" value="<?= $employee['id'] ?? '' ?>">
                        
                        <div class="section-title"><i class="fa-solid fa-id-card"></i>البيانات الأساسية</div>
                        <div class="row g-3">
                            <div class="col-md-4"><label class="form-label">الرقم الوظيفي</label><input type="text" name="employee_id" class="form-control" value="<?= $employee['employee_id'] ?? '' ?>" required></div>
                            <div class="col-md-4"><label class="form-label">رقم الهوية</label><input type="text" name="id_number" class="form-control" value="<?= $employee['id_number'] ?? '' ?>" required></div>
                            <div class="col-md-4"><label class="form-label">اسم الموظف</label><input type="text" name="subscriber_name" class="form-control" value="<?= $employee['subscriber_name'] ?? '' ?>" required></div>
                            <div class="col-md-4"><label class="form-label">تاريخ الميلاد</label><input type="date" name="birth_date" class="form-control" value="<?= $employee['birth_date'] ?? '' ?>"></div>
                            <div class="col-md-4"><label class="form-label">الجنسية</label><input type="text" name="nationality" class="form-control" value="<?= $employee['nationality'] ?? '' ?>"></div>
                            <div class="col-md-4"><label class="form-label">الجنس</label><select name="gender" class="form-select"><option value="ذكر" <?= (($employee['gender'] ?? '') === 'ذكر') ? 'selected' : ''; ?>>ذكر</option><option value="أنثى" <?= (($employee['gender'] ?? '') === 'أنثى') ? 'selected' : ''; ?>>أنثى</option></select></div>
                            <div class="col-md-4"><label class="form-label">الحالة الاجتماعية</label><input type="text" name="marital" class="form-control" value="<?= $employee['marital'] ?? '' ?>"></div>
                            <div class="col-md-4"><label class="form-label">الديانة</label><input type="text" name="religion" class="form-control" value="<?= $employee['religion'] ?? '' ?>"></div>
                            <div class="col-md-4"><label class="form-label">تاريخ انتهاء الهوية</label><input type="date" name="id_expiry" class="form-control" value="<?= $employee['id_expiry'] ?? '' ?>"></div>
                        </div>

                        <div class="section-title mt-4"><i class="fa-solid fa-phone"></i>بيانات التواصل</div>
                        <div class="row g-3">
                            <div class="col-md-4"><label class="form-label">الهاتف</label><input type="text" name="phone" class="form-control" value="<?= $employee['phone'] ?? '' ?>"></div>
                            <div class="col-md-4"><label class="form-label">البريد الإلكتروني</label><input type="email" name="email" class="form-control" value="<?= $employee['email'] ?? '' ?>"></div>
                            <div class="col-md-4"><label class="form-label">العنوان</label><input type="text" name="address" class="form-control" value="<?= $employee['address'] ?? '' ?>"></div>
                        </div>

                        <div class="section-title mt-4"><i class="fa-solid fa-briefcase"></i>البيانات الوظيفية</div>
                        <div class="row g-3">
                            <div class="col-md-4"><label class="form-label">تاريخ الانضمام</label><input type="date" name="joining_date" class="form-control" value="<?= $employee['joining_date'] ?? '' ?>"></div>
                            <div class="col-md-4"><label class="form-label">المسمى الوظيفي</label><input type="text" name="profession" class="form-control" value="<?= $employee['profession'] ?? '' ?>"></div>
                            <div class="col-md-4"><label class="form-label">المدير المباشر</label><input type="text" name="manager" class="form-control" value="<?= $employee['manager'] ?? '' ?>"></div>
                            <div class="col-md-4"><label class="form-label">الشركة</label><input type="text" name="company_name" class="form-control" value="<?= $employee['company_name'] ?? '' ?>"></div>
                            <div class="col-md-4"><label class="form-label">القسم</label><input type="text" name="n1" class="form-control" value="<?= $employee['n1'] ?? '' ?>"></div>
                            <div class="col-md-4"><label class="form-label">الموقع</label><input type="text" name="location" class="form-control" value="<?= $employee['location'] ?? '' ?>"></div>
                        </div>

                        <div class="section-title mt-4"><i class="fa-solid fa-money-bill-wave"></i>البيانات المالية</div>
                        <div class="row g-3">
                            <div class="col-md-3"><label class="form-label">الراتب الأساسي</label><input type="number" step="0.01" name="base_salary" class="form-control" value="<?= $employee['base_salary'] ?? '' ?>"></div>
                            <div class="col-md-3"><label class="form-label">بدل السكن</label><input type="number" step="0.01" name="housing_allowance" class="form-control" value="<?= $employee['housing_allowance'] ?? '' ?>"></div>
                            <div class="col-md-3"><label class="form-label">بدل النقل</label><input type="number" step="0.01" name="commissions" class="form-control" value="<?= $employee['commissions'] ?? '' ?>"></div>
                            <div class="col-md-3"><label class="form-label">بدلات أخرى</label><input type="number" step="0.01" name="other_allowances" class="form-control" value="<?= $employee['other_allowances'] ?? '' ?>"></div>
                            <div class="col-md-3"><label class="form-label">إجمالي الراتب</label><input type="number" step="0.01" name="total_salary" class="form-control" value="<?= $employee['total_salary'] ?? '' ?>"></div>
                            <div class="col-md-4"><label class="form-label">اسم البنك</label><input type="text" name="n3" class="form-control" value="<?= $employee['n3'] ?? '' ?>"></div>
                            <div class="col-md-5"><label class="form-label">الآيبان</label><input type="text" name="n2" class="form-control" value="<?= $employee['n2'] ?? '' ?>"></div>
                        </div>

                        <div class="mt-4 pt-4 border-top d-flex justify-content-end">
                            <a href="<?= site_url('users1/emp_data101') ?>" class="btn btn-secondary me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary">حفظ البيانات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>