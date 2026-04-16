<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>تفاصيل الموظف</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        :root{--marsom-blue:#001f3f;--marsom-orange:#FF8C00;--text-light:#fff;--text-dark:#343a40;}
        body{font-family:'Tajawal',sans-serif;overflow-y:auto !important;background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);background-size:400% 400%;animation:grad 20s ease infinite;color:var(--text-dark);position:relative}
        @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        .particles{position:fixed;inset:0;overflow:hidden;z-index:-1;pointer-events: none;}
        .particle{position:absolute;background:rgba(255,140,0,.1);clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);animation:float 25s infinite ease-in-out;opacity:0;filter:blur(2px)}
        .particle:nth-child(even){background:rgba(0,31,63,.1)}
        .particle:nth-child(1){width:40px;height:40px;left:10%;top:20%;animation-duration:18s}.particle:nth-child(2){width:70px;height:70px;left:25%;top:50%;animation-duration:22s;animation-delay:2s}.particle:nth-child(3){width:55px;height:55px;left:40%;top:10%;animation-duration:25s;animation-delay:5s}.particle:nth-child(4){width:80px;height:80px;left:60%;top:70%;animation-duration:20s;animation-delay:8s}.particle:nth-child(5){width:60px;height:60px;left:80%;top:30%;animation-duration:23s;animation-delay:10s}.particle:nth-child(6){width:45px;height:45px;left:5%;top:85%;animation-duration:19s;animation-delay:3s}.particle:nth-child(7){width:90px;height:90px;left:70%;top:5%;animation-duration:28s;animation-delay:6s}.particle:nth-child(8){width:35px;height:35px;left:90%;top:40%;animation-duration:17s;animation-delay:12s}.particle:nth-child(9){width:75px;height:75px;left:20%;top:75%;animation-duration:21s;animation-delay:1s}.particle:nth-child(10){width:65px;height:65px;left:50%;top:90%;animation-duration:24s;animation-delay:4s}
        @keyframes float{0%{transform:translateY(0) translateX(0) rotate(0);opacity:0}20%{opacity:1}80%{opacity:1}100%{transform:translateY(-100vh) translateX(50px) rotate(300deg);opacity:0}}
        #loading-screen{position:fixed;inset:0;background:var(--marsom-blue);z-index:9999;display:flex;align-items:center;justify-content:center;flex-direction:column;transition:opacity .5s}
        .loader{width:50px;height:50px;border:5px solid rgba(255,255,255,.3);border-top:5px solid var(--marsom-orange);border-radius:50%;animation:spin 1s linear infinite;margin-bottom:16px}
        @keyframes spin{to{transform:rotate(360deg)}}
        .main-container{padding:30px 15px;visibility:hidden;opacity:0;transition:opacity .5s;position:relative;z-index:1}
        .page-title{font-family:'El Messiri',sans-serif;font-weight:700;font-size:2.6rem;color:var(--text-light);margin-bottom:24px;text-align:center;position:relative;display:inline-block;padding-bottom:10px;text-shadow:0 3px 6px rgba(0,0,0,.4)}
        .page-title::after{content:'';position:absolute;width:160px;height:4px;background:linear-gradient(90deg,var(--marsom-blue),var(--marsom-orange));bottom:0;left:50%;transform:translateX(-50%);border-radius:2px}
        .table-card{background:rgba(255,255,255,.9);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.3);border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,.15);padding:25px}
        .top-actions{position:fixed;top:12px;right:12px;display:flex;gap:10px;z-index:5}
        .top-actions a{background:rgba(255,255,255,.12);border:1px solid var(--glass-border);color:#fff;text-decoration:none;border-radius:10px;padding:8px 14px;display:inline-flex;align-items:center;gap:8px;transition:.25s}
        .top-actions a:hover{background:rgba(255,255,255,.2);color:var(--marsom-orange)}
        .section-title{font-size:1.05rem;font-weight:700;color:#0b2447;margin:10px 0 14px;display:flex;align-items:center;gap:8px}
        .section-title i{opacity:.85}
        .info-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px}
        @media (max-width:992px){.info-grid{grid-template-columns:1fr}}
        .info-item{display:flex;flex-direction:column;background:rgba(255,255,255,.9);border:1px solid rgba(0,0,0,.06);border-radius:12px;padding:12px}
        .info-item .label{font-weight:700;color:#001f3f;font-size:.95rem;margin-bottom:6px}
        .info-item .value{font-size:1rem; min-height: 38px; display: flex; align-items: center;}
        .edit-mode { display: none; }
        .badge-pill{border-radius:50rem}
        .file-card{border:1px dashed rgba(0,0,0,.15);border-radius:12px;padding:12px;background:#fff}
        .file-card .meta{font-size:.85rem;color:#6c757d}
    </style>
</head>
<body>

<div class="particles">
    <div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div>
</div>
<div id="loading-screen">
    <div class="loader"></div>
    <h3 style="color:#fff">جارٍ تحميل التفاصيل ...</h3>
</div>
<div class="top-actions">
    <a href="<?php echo site_url('users1/main_hr1'); ?>"><i class="fas fa-arrow-right"></i><span>رجوع</span></a>
    <a href="<?php echo site_url('users1/main_hr1'); ?>"><i class="fas fa-home"></i><span>الرئيسية</span></a>
</div>

<div class="main-container container-fluid">
    <div class="text-center"><h1 class="page-title">تفاصيل الموظف</h1></div>

    <form id="editEmployeeForm">
        <input type="hidden" name="id" value="<?php echo $employee['id'] ?? 0; ?>">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

        <div class="row g-3 mb-3">
            <div class="col-12">
                <div class="table-card">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:64px;height:64px;border-radius:50%;background:#fff;border:2px solid #eee;display:flex;align-items:center;justify-content:center">
                                <i class="fa-solid fa-user fa-lg" style="color:#001f3f"></i>
                            </div>
                            <div>
                                <div class="fw-bold fs-5"><?php echo htmlspecialchars($employee['full_name_ar'] ?? '—'); ?></div>
                                <div class="text-muted">رقم وظيفي: <?php echo htmlspecialchars($employee['employee_code'] ?? '—'); ?></div>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <div id="view-mode-buttons">
                                <a class="btn btn-primary" id="editButton" href="#"><i class="fa fa-pen"></i> تعديل</a>
                                <button type="button" class="btn btn-outline-secondary" onclick="window.print()"><i class="fa fa-print"></i> طباعة</button>
                            </div>
                            <div id="edit-mode-buttons" style="display: none;">
                                <button class="btn btn-success" type="button" id="saveButton"><i class="fa fa-save"></i> حفظ التغييرات</button>
                                <button class="btn btn-secondary" type="button" id="cancelButton"><i class="fa fa-times"></i> إلغاء</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-7">
                <div class="table-card mb-3">
                    <div class="section-title"><i class="fa-solid fa-id-card"></i> البيانات الأساسية</div>
                    <div class="info-grid">
                        <div class="info-item"><div class="label">الرقم الوظيفي</div><div class="value"><span class="view-mode"><?php echo htmlspecialchars($employee['employee_code'] ?? '—'); ?></span><input type="text" name="employee_id" class="form-control edit-mode" value="<?php echo htmlspecialchars($employee['employee_code'] ?? ''); ?>"></div></div>
                        <div class="info-item"><div class="label">الاسم كاملاً (عربي)</div><div class="value"><span class="view-mode"><?php echo htmlspecialchars($employee['full_name_ar'] ?? '—'); ?></span><input type="text" name="subscriber_name" class="form-control edit-mode" value="<?php echo htmlspecialchars($employee['full_name_ar'] ?? ''); ?>"></div></div>
                        <div class="info-item"><div class="label">الجنسية</div><div class="value"><span class="view-mode"><?php echo htmlspecialchars($employee['nationality'] ?? '—'); ?></span><input type="text" name="nationality" class="form-control edit-mode" value="<?php echo htmlspecialchars($employee['nationality'] ?? ''); ?>"></div></div>
                        <div class="info-item"><div class="label">رقم الهوية/الإقامة</div><div class="value"><span class="view-mode"><?php echo htmlspecialchars($employee['id_number'] ?? '—'); ?></span><input type="text" name="id_number" class="form-control edit-mode" value="<?php echo htmlspecialchars($employee['id_number'] ?? ''); ?>"></div></div>
                        <div class="info-item"><div class="label">تاريخ الانضمام</div><div class="value"><span class="view-mode"><?php echo htmlspecialchars($employee['join_date'] ?? '—'); ?></span><input type="date" name="joining_date" class="form-control edit-mode" value="<?php echo !empty($employee['join_date']) ? date('Y-m-d', strtotime($employee['join_date'])) : ''; ?>"></div></div>
                        <div class="info-item"><div class="label">القسم</div><div class="value"><span class="view-mode"><?php echo htmlspecialchars($employee['department'] ?? '—'); ?></span><input type="text" name="n1" class="form-control edit-mode" value="<?php echo htmlspecialchars($employee['department'] ?? ''); ?>"></div></div>
                        <div class="info-item"><div class="label">تاريخ الميلاد</div><div class="value"><span class="view-mode"><?php echo htmlspecialchars($employee['birth_date'] ?? '—'); ?></span><input type="date" name="birth_date" class="form-control edit-mode" value="<?php echo !empty($employee['birth_date']) ? date('Y-m-d', strtotime($employee['birth_date'])) : ''; ?>"></div></div>
                        <div class="info-item"><div class="label">المسمى الوظيفي</div><div class="value"><span class="view-mode badge bg-info-subtle text-dark badge-pill"><?php echo htmlspecialchars($employee['job_title'] ?? '—'); ?></span><input type="text" name="profession" class="form-control edit-mode" value="<?php echo htmlspecialchars($employee['job_title'] ?? ''); ?>"></div></div>
                        <div class="info-item"><div class="label">الجنس</div><div class="value"><span class="view-mode"><?php echo htmlspecialchars($employee['gender'] ?? '—'); ?></span><select name="gender" class="form-select edit-mode"><option value="ذكر" <?php echo (($employee['gender'] ?? '') === 'ذكر') ? 'selected' : ''; ?>>ذكر</option><option value="أنثى" <?php echo (($employee['gender'] ?? '') === 'أنثى') ? 'selected' : ''; ?>>أنثى</option></select></div></div>
                        <div class="info-item"><div class="label">الحالة الاجتماعية</div><div class="value"><span class="view-mode"><?php echo htmlspecialchars($employee['marital_status'] ?? '—'); ?></span><input type="text" name="marital" class="form-control edit-mode" value="<?php echo htmlspecialchars($employee['marital_status'] ?? ''); ?>"></div></div>
                        <div class="info-item"><div class="label">الديانة</div><div class="value"><span class="view-mode"><?php echo htmlspecialchars($employee['religion'] ?? '—'); ?></span><input type="text" name="religion" class="form-control edit-mode" value="<?php echo htmlspecialchars($employee['religion'] ?? ''); ?>"></div></div>
                        <div class="info-item"><div class="label">تاريخ انتهاء الهوية</div><div class="value"><span class="view-mode"><?php echo htmlspecialchars($employee['id_expiry'] ?? '—'); ?></span><input type="date" name="id_expiry" class="form-control edit-mode" value="<?php echo !empty($employee['id_expiry']) ? date('Y-m-d', strtotime($employee['id_expiry'])) : ''; ?>"></div></div>
                        <div class="info-item"><div class="label">البريد الإلكتروني الشخصي</div><div class="value"><span class="view-mode"><a href="mailto:<?php echo htmlspecialchars($employee['personal_email'] ?? '#'); ?>"><?php echo htmlspecialchars($employee['personal_email'] ?? '—'); ?></a></span><input type="email" name="email" class="form-control edit-mode" value="<?php echo htmlspecialchars($employee['personal_email'] ?? ''); ?>"></div></div>
                        <div class="info-item"><div class="label">إيميل مرسوم</div><div class="value"><span class="view-mode"><a href="mailto:<?php echo htmlspecialchars($employee['company_email'] ?? '#'); ?>"><?php echo htmlspecialchars($employee['company_email'] ?? '—'); ?></a></span><input type="email" name="n2" class="form-control edit-mode" value="<?php echo htmlspecialchars($employee['company_email'] ?? ''); ?>"></div></div>
                        <div class="info-item"><div class="label">رقم الجوال</div><div class="value"><span class="view-mode"><a href="tel:<?php echo htmlspecialchars($employee['mobile'] ?? ''); ?>"><?php echo htmlspecialchars($employee['mobile'] ?? '—'); ?></a></span><input type="tel" name="phone" class="form-control edit-mode" value="<?php echo htmlspecialchars($employee['mobile'] ?? ''); ?>"></div></div>
                        <div class="info-item"><div class="label">العنوان</div><div class="value"><span class="view-mode"><?php echo htmlspecialchars($employee['address'] ?? '—'); ?></span><textarea name="address" class="form-control edit-mode"><?php echo htmlspecialchars($employee['address'] ?? ''); ?></textarea></div></div>
                        <div class="info-item"><div class="label">نوع الدوام</div><div class="value"><span class="view-mode"><?php echo htmlspecialchars($employee['employment_type'] ?? '—'); ?></span><input type="text" name="type" class="form-control edit-mode" value="<?php echo htmlspecialchars($employee['employment_type'] ?? ''); ?>"></div></div>
                        <div class="info-item"><div class="label">الشركة</div><div class="value"><span class="view-mode"><?php echo htmlspecialchars($employee['company'] ?? '—'); ?></span><input type="text" name="company_name" class="form-control edit-mode" value="<?php echo htmlspecialchars($employee['company'] ?? ''); ?>"></div></div>
                        <div class="info-item"><div class="label">الموقع</div><div class="value"><span class="view-mode"><?php echo htmlspecialchars($employee['location'] ?? '—'); ?></span><input type="text" name="location" class="form-control edit-mode" value="<?php echo htmlspecialchars($employee['location'] ?? ''); ?>"></div></div>
                        <div class="info-item"><div class="label">المدير المباشر</div><div class="value"><span class="view-mode"><?php echo htmlspecialchars($employee['direct_manager'] ?? '—'); ?></span><input type="text" name="manager" class="form-control edit-mode" value="<?php echo htmlspecialchars($employee['direct_manager'] ?? ''); ?>"></div></div>
                    </div>
                </div>

                <div class="table-card mb-3">
                    <div class="section-title"><i class="fa-solid fa-building-columns"></i> الحساب البنكي</div>
                    <div class="info-grid">
                        <div class="info-item"><div class="label">رقم الآيبان</div><div class="value"><span class="view-mode"><?php echo htmlspecialchars($employee['iban'] ?? '—'); ?></span><input type="text" name="n3" class="form-control edit-mode" value="<?php echo htmlspecialchars($employee['iban'] ?? ''); ?>"></div></div>
                        <div class="info-item"><div class="label">اسم البنك</div><div class="value"><span class="view-mode"><?php echo htmlspecialchars($employee['bank_name'] ?? '—'); ?></span><input type="text" name="n4" class="form-control edit-mode" value="<?php echo htmlspecialchars($employee['bank_name'] ?? ''); ?>"></div></div>
                    </div>
                </div>

                <div class="table-card mb-3">
                    <div class="section-title"><i class="fa-solid fa-money-bill-trend-up"></i> الرواتب والبدلات</div>
                    <div class="info-grid">
                        <div class="info-item"><div class="label">الراتب الأساسي</div><div class="value"><span class="view-mode"><?php echo number_format((float)($employee['basic_salary'] ?? 0), 2); ?></span><input type="number" step="0.01" name="base_salary" class="form-control edit-mode" value="<?php echo ($employee['basic_salary'] ?? 0); ?>"></div></div>
                        <div class="info-item"><div class="label">بدل سكن</div><div class="value"><span class="view-mode"><?php echo number_format((float)($employee['housing_allowance'] ?? 0), 2); ?></span><input type="number" step="0.01" name="housing_allowance" class="form-control edit-mode" value="<?php echo ($employee['housing_allowance'] ?? 0); ?>"></div></div>
                        <div class="info-item"><div class="label">بدل مواصلات</div><div class="value"><span class="view-mode"><?php echo number_format((float)($employee['transportation_allowance'] ?? 0), 2); ?></span><input type="number" step="0.01" name="commissions" class="form-control edit-mode" value="<?php echo ($employee['n4'] ?? 0); ?>"></div></div>
                        <div class="info-item"><div class="label">بدل اتصالات</div><div class="value"><span class="view-mode"><?php echo number_format((float)($employee['communication_allowance'] ?? 0), 2); ?></span><input type="number" step="0.01" name="other_allowances" class="form-control edit-mode" value="<?php echo ($employee['communication_allowance'] ?? 0); ?>"></div></div>
                        <div class="info-item"><div class="label">بدل طبيعة العمل</div><div class="value"><span class="view-mode"><?php echo number_format((float)($employee['work_nature_allowance'] ?? 0), 2); ?></span><input type="number" step="0.01" name="n5" class="form-control edit-mode" value="<?php echo ($employee['work_nature_allowance'] ?? 0); ?>"></div></div>
                        <div class="info-item"><div class="label">بدل سماعة</div><div class="value"><span class="view-mode"><?php echo number_format((float)($employee['headphone_allowance'] ?? 0), 2); ?></span><input type="number" step="0.01" name="n6" class="form-control edit-mode" value="<?php echo ($employee['headphone_allowance'] ?? 0); ?>"></div></div>
                        <div class="info-item"><div class="label">أخرى</div><div class="value"><span class="view-mode"><?php echo number_format((float)($employee['other_allowance'] ?? 0), 2); ?></span><input type="number" step="0.01" name="n7" class="form-control edit-mode" value="<?php echo ($employee['other_allowance'] ?? 0); ?>"></div></div>
                        <div class="info-item"><div class="label">بدل المحروقات</div><div class="value"><span class="view-mode"><?php echo number_format((float)($employee['fuel_allowance'] ?? 0), 2); ?></span><input type="number" step="0.01" name="n8" class="form-control edit-mode" value="<?php echo ($employee['fuel_allowance'] ?? 0); ?>"></div></div>
                        <div class="info-item"><div class="label">بدل مواصلات إضافي</div><div class="value"><span class="view-mode"><?php echo number_format((float)($employee['extra_transport_allowance'] ?? 0), 2); ?></span><input type="number" step="0.01" name="n9" class="form-control edit-mode" value="<?php echo ($employee['extra_transport_allowance'] ?? 0); ?>"></div></div>
                        <div class="info-item"><div class="label">بدل الإشراف</div><div class="value"><span class="view-mode"><?php echo number_format((float)($employee['supervision_allowance'] ?? 0), 2); ?></span><input type="number" step="0.01" name="n10" class="form-control edit-mode" value="<?php echo ($employee['supervision_allowance'] ?? 0); ?>"></div></div>
                        <div class="info-item"><div class="label">بدل إعاشة</div><div class="value"><span class="view-mode"><?php echo number_format((float)($employee['subsistence_allowance'] ?? 0), 2); ?></span><input type="number" step="0.01" name="n11" class="form-control edit-mode" value="<?php echo ($employee['subsistence_allowance'] ?? 0); ?>"></div></div>
                        <div class="info-item"><div class="label">كامل الراتب</div><div class="value"><span class="badge bg-success-subtle text-dark badge-pill"><?php echo number_format((float)($employee['total_salary'] ?? 0), 2); ?></span></div></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="table-card mb-3">
    <div class="section-title"><i class="fa-solid fa-suitcase-rolling"></i> رصيد الإجازات</div>
    
    <?php 
        // The controller now provides the $leave_balance variable.
        // This sets default values of 0 if a specific balance isn't found.
        $lb = $leave_balance ?? []; 
    ?>
    
    <div class="row g-3 text-center">
        <div class="col-6 col-md-4">
            <div class="info-item">
                <div class="label">سنوية</div>
                <div class="value fw-bold fs-5 text-primary"><?php echo (float)($lb['annual'] ?? 0); ?></div>
                <div class="text-muted small">يوم</div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="info-item">
                <div class="label">مرضية</div>
                <div class="value fw-bold fs-5 text-success"><?php echo (float)($lb['sick'] ?? 0); ?></div>
                <div class="text-muted small">يوم</div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="info-item">
                <div class="label">زواج</div>
                <div class="value fw-bold fs-5 text-info"><?php echo (float)($lb['marriage'] ?? 0); ?></div>
                 <div class="text-muted small">يوم</div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="info-item">
                <div class="label">أمومة</div>
                <div class="value fw-bold fs-5 text-danger"><?php echo (float)($lb['maternity'] ?? 0); ?></div>
                 <div class="text-muted small">يوم</div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="info-item">
                <div class="label">حج</div>
                <div class="value fw-bold fs-5 text-secondary"><?php echo (float)($lb['hajj'] ?? 0); ?></div>
                 <div class="text-muted small">يوم</div>
            </div>
        </div>
    </div>
</div>

                <div class="table-card mb-3">
                    <div class="section-title"><i class="fa-solid fa-paperclip"></i> المرفقات</div>
                    <?php $files = $attachments ?? []; ?>
                    <?php if(empty($files)): ?>
                        <div class="alert alert-light m-0">لا توجد مرفقات.</div>
                    <?php else: ?>
                        <div class="vstack gap-2">
                            <?php foreach($files as $f): ?>
                                <div class="file-card d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div><i class="fa-regular fa-file-lines fa-lg"></i></div>
                                        <div>
                                            <div class="fw-semibold"><?php echo htmlspecialchars($f['title'] ?? ($f['type'] ?? 'مرفق')); ?></div>
                                            <div class="meta">اسم الملف: <?php echo htmlspecialchars($f['file_name'] ?? '—'); ?> · تاريخ الرفع: <?php echo htmlspecialchars($f['uploaded_at'] ?? '—'); ?></div>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a class="btn btn-sm btn-outline-primary" target="_blank" href="<?php echo htmlspecialchars($f['url'] ?? '#'); ?>"><i class="fa fa-eye"></i> عرض</a>
                                        <a class="btn btn-sm btn-primary" href="<?php echo site_url('users2/download_attachment/'.urlencode($f['id'] ?? '')); ?>"><i class="fa fa-download"></i> تحميل</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="table-card">
                    <div class="section-title"><i class="fa-solid fa-clock-rotate-left"></i> سجلات</div>
                    <div class="info-item">
                        <div class="label">تاريخ الإنشاء</div>
                        <div class="value"><?php echo htmlspecialchars($employee['created_at'] ?? '—'); ?></div>
                    </div>
                    <div class="info-item mt-2">
                        <div class="label">آخر تحديث</div>
                        <div class="value"><?php echo htmlspecialchars($employee['updated_at'] ?? '—'); ?></div>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    // --- TOGGLE EDIT MODE ---
    $('#editButton').on('click', function(e) {
        e.preventDefault();
        $('.view-mode').hide();
        $('.edit-mode').css('display', 'block');
        $('#view-mode-buttons').hide();
        $('#edit-mode-buttons').show();
    });

    $('#cancelButton').on('click', function() {
        $('.edit-mode').hide();
        $('.view-mode').show();
        $('#edit-mode-buttons').hide();
        $('#view-mode-buttons').show();
    });

    // --- SAVE DATA VIA AJAX ---
    $('#saveButton').on('click', function() {
        var saveBtn = $(this);
        var formData = $('#editEmployeeForm').serialize();

        saveBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جارٍ الحفظ...');

        $.ajax({
            url: "<?php echo site_url('users1/update_employee_data'); ?>",
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alert(response.message);
                    location.reload(); 
                } else {
                    alert('Error: ' + (response.message || 'An unknown error occurred.'));
                }
            },
            error: function(xhr, status, error) {
                alert('An error occurred while connecting to the server. Please try again.');
                console.error("AJAX Error:", status, error, xhr.responseText);
            },
            complete: function() {
                saveBtn.prop('disabled', false).html('<i class="fa fa-save"></i> حفظ التغييرات');
            }
        });
    });
});

// --- CORRECTED Loading Screen Logic ---
window.addEventListener('load', function() {
    const loading = document.getElementById('loading-screen');
    const main = document.querySelector('.main-container');
    
    if (loading && main) {
        loading.style.opacity = '0';
        setTimeout(function() { 
            loading.style.display = 'none'; 
            document.body.style.overflow = 'auto'; 
            main.style.visibility = 'visible'; 
            main.style.opacity = '1'; 
        }, 30);
    } else {
        // If something is still wrong, hide the loading screen anyway after a delay
        setTimeout(function() {
            if(loading) loading.style.display = 'none';
        }, 60);
    }
});
</script>
</body>
</html>