<?php
// --- Calculate Service Period ---
$service_period_string = '—';
if (!empty($employee['join_date'])) {
    try {
        $join_date = new DateTime($employee['join_date']);
        $today = new DateTime(); // Calculate until today
        $diff = $join_date->diff($today);
        
        $parts = [];
        if ($diff->y > 0) $parts[] = $diff->y . ' سنة';
        if ($diff->m > 0) $parts[] = $diff->m . ' شهر';
        if ($diff->d > 0) $parts[] = $diff->d . ' يوم';
        
        $service_period_string = !empty($parts) ? implode(' و ', $parts) : 'أقل من يوم';
    } catch (Exception $e) {
        $service_period_string = 'تاريخ غير صالح';
    }
}

// --- CONFIG: Base URL for Recruitment Files ---
// If the HR system and Recruitment system share the same folder, use base_url()
// If they are different projects, put the full URL of the recruitment folder here:
// e.g., 'https://services.marsoom.net/recruitment2/'
//$file_base_url = base_url(); 
 $file_base_url = 'https://services.marsoom.net/recruitment2/'; // Use this if files are on the other domain

// --- MAPPING: Map DB Columns to Display Titles ---
$attachment_map = [
    'national_address_file'     => 'العنوان الوطني',
    'commencement_form_file'    => 'نموذج المباشرة',
    'job_description_file'      => 'الوصف الوظيفي',
    'confidentiality_form_file' => 'تعهد السرية',
    'gosi_subscription_file'    => 'اشتراك التأمينات',
    'experience_file'           => 'شهادة الخبرة',
    'clearance_cert_file'       => 'شهادة المخالصة',
    'medical_invoice'           => 'فاتورة الفحص الطبي',
    'employment_guarantee_file' => 'الضمان الوظيفي',
    'lawyer_license_file'       => 'رخصة المحاماة',
    'criminal_record_file'      => 'صحيفة السوابق',
    'medical_result'            => 'نتيجة الفحص الطبي',
    'family_data_file'          => 'بيانات العائلة',
    'immediate_work_file'       => 'المباشرة الفورية',
    'iqama_file'                => 'صورة الهوية/الإقامة',
    'degree_file'               => 'المؤهل العلمي',
    'bank_iban_file'            => 'شهادة الايبان'
];
?>
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
        .particles{position:fixed;inset:0;overflow:hidden;z-index:-1}
        .particle{position:absolute;background:rgba(255,140,0,.1);clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);animation:float 25s infinite ease-in-out;opacity:0;filter:blur(2px)}
        @keyframes float{0%{transform:translateY(0) translateX(0) rotate(0);opacity:0}20%{opacity:1}80%{opacity:1}100%{transform:translateY(-100vh) translateX(50px) rotate(300deg);opacity:0}}
        
        /* Loader Styles */
        #loading-screen{position:fixed;inset:0;background:var(--marsom-blue);z-index:9999;display:flex;align-items:center;justify-content:center;flex-direction:column;transition:opacity .5s}
        .loader{width:50px;height:50px;border:5px solid rgba(255,255,255,.3);border-top:5px solid var(--marsom-orange);border-radius:50%;animation:spin 1s linear infinite;margin-bottom:16px}
        @keyframes spin{to{transform:rotate(360deg)}}
        
        .main-container{padding:30px 15px;visibility:hidden;opacity:0;transition:opacity .5s;position:relative;z-index:1}
        
        .page-title{font-family:'El Messiri',sans-serif;font-weight:700;font-size:2.6rem;color:var(--text-light);margin-bottom:24px;text-align:center;position:relative;display:inline-block;padding-bottom:10px;text-shadow:0 3px 6px rgba(0,0,0,.4)}
        .page-title::after{content:'';position:absolute;width:160px;height:4px;background:linear-gradient(90deg,var(--marsom-blue),var(--marsom-orange));bottom:0;left:50%;transform:translateX(-50%);border-radius:2px}
        .table-card{background:rgba(255,255,255,.9);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.3);border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,.15);padding:25px}
        .top-actions{position:fixed;top:12px;right:12px;display:flex;gap:10px;z-index:5}
        .top-actions a{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.3);color:#fff;text-decoration:none;border-radius:10px;padding:8px 14px;display:inline-flex;align-items:center;gap:8px;transition:.25s}
        .top-actions a:hover{background:rgba(255,255,255,.2);color:var(--marsom-orange)}
        .section-title{font-size:1.05rem;font-weight:700;color:#0b2447;margin:10px 0 14px;display:flex;align-items:center;gap:8px}
        .info-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px}
        @media (max-width:992px){.info-grid{grid-template-columns:1fr}}
        .info-item{display:flex;flex-direction:column;background:rgba(255,255,255,.9);border:1px solid rgba(0,0,0,.06);border-radius:12px;padding:12px}
        .info-item .label{font-weight:700;color:#001f3f;font-size:.95rem;margin-bottom:6px}
        .info-item .value{font-size:1rem; min-height: 38px; display: flex; align-items: center;}
        .edit-mode { display: none; }
        .badge-pill{border-radius:50rem}
        .file-card{border:1px dashed rgba(0,0,0,.15);border-radius:12px;padding:12px;background:#fff; transition: all 0.2s ease;}
        .file-card:hover { border-color: var(--marsom-orange); background: #fffcf5; }
        .file-card .meta{font-size:.85rem;color:#6c757d}
        
        /* Validation Styles */
        .is-invalid { border-color: #dc3545 !important; background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e"); background-repeat: no-repeat; background-position: right calc(.375em + .1875rem) center; background-size: calc(.75em + .375rem) calc(.75em + .375rem); padding-left: 10px !important; }
        .is-valid { border-color: #198754 !important; background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e"); background-repeat: no-repeat; background-position: right calc(.375em + .1875rem) center; background-size: calc(.75em + .375rem) calc(.75em + .375rem); }
        
        input[name="n2"] { font-family: monospace; letter-spacing: 1px; text-transform: uppercase; }
        .iban-error { font-size: 0.8rem; margin-top: 2px; display: block; }
    </style>
</head>
<body>

<div class="particles">
    <div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div>
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
                                <button type="button" class="btn btn-danger shadow-sm ms-2" onclick="openSuspensionModal()">
    <i class="fas fa-ban me-2"></i> إيقاف الراتب/الإجازات
</button>
                                <button type="button" class="btn btn-info text-white" id="showRequestsBtn">
                                    <i class="fa fa-list-ul"></i> سجل الطلبات
                                </button>
                            </div>
                            <?php 
                            // Only show to HR
                            $hr_ids_view = ['2230', '2515', '2774', '2784', '1835', '2901'];
                            if(in_array($this->session->userdata('username'), $hr_ids_view)): 
                            ?>
                            <button type="button" class="btn btn-warning text-dark" id="btnSendCert">
                                <i class="fa-solid fa-certificate"></i> إرسال شهادة خبرة
                            </button>
                            <?php endif; ?>
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
                        
                        <div class="info-item" style="background-color: #e3f2fd; border-color: #bbdefb;">
                            <div class="label text-primary">مدة الخدمة</div>
                            <div class="value fw-bold text-dark">
                                <?php echo $service_period_string; ?>
                            </div>
                        </div>

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
                        <div class="info-item">
                            <div class="label">المدير المباشر</div>
                            <div class="value">
                                <span class="view-mode">
                                    <?php
                                    if (isset($direct_manager_details) && !empty($direct_manager_details['manager_name'])) {
                                        echo htmlspecialchars($direct_manager_details['manager_name']);
                                    } else {
                                        echo '—';
                                    }
                                    ?>
                                </span>
                                <input type="text" readonly class="form-control-plaintext edit-mode" value="<?php echo isset($direct_manager_details['manager_name']) ? htmlspecialchars($direct_manager_details['manager_name']) : '—'; ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-card mb-3">
                    <div class="section-title"><i class="fa-solid fa-building-columns"></i> الحساب البنكي</div>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="label">رقم الآيبان</div>
                            <div class="value">
                                <span class="view-mode"><?php echo htmlspecialchars($employee['iban'] ?? '—'); ?></span>
                                <input type="text" name="iban" class="form-control edit-mode" 
                                       value="<?php echo htmlspecialchars($employee['iban'] ?? ''); ?>"
                                       maxlength="24"
                                       placeholder="SAXXXXXXXXXXXXXXXXXXXXXX"
                                       dir="ltr">
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="label">اسم البنك</div>
                            <div class="value">
                                <span class="view-mode">
                                    <?php echo htmlspecialchars($employee['bank_name'] ?? '—'); ?>
                                </span>
                                <select name="bank_name" class="form-select edit-mode" dir="ltr">
                                    <option value="">-- Select Bank --</option>
                                    <?php 
                                    $bank_list = [
                                        'Al Rajhi Bank', 'AlBank AlSaudi AlFransi', 'Alinma Bank', 'Arab National Bank',
                                        'Bank Albilad', 'National Bank of Bahrain', 'Bank AlJazira', 'Bank Muscat',
                                        'Deutsche Bank', 'Emirates Bank Intl', 'Gulf International Bank', 'National Bank of Kuwait',
                                        'National Commercial Bank', 'Riyad Bank', 'First Abu Dhabi Bank', 'National Bank of Pakistan',
                                        'Saudi Investment Bank', 'State Bank of India', 'T.C. Ziraat Bankasi', 'The Saudi British Bank',
                                        'Standard Chartered Bank', 'Saudi Cairo Bank', 'Mufg Bank', 'JP Morgan Chase Bank', 'ICBK Bank'
                                    ];
                                    $current_bank = trim($employee['bank_name'] ?? '');
                                    foreach ($bank_list as $bank) {
                                        $selected = ($current_bank === $bank) ? 'selected' : '';
                                        echo '<option value="' . htmlspecialchars($bank) . '" ' . $selected . '>' . htmlspecialchars($bank) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="table-card mb-3">
                    <div class="section-title">
                        <i class="fa-solid fa-money-bill-trend-up"></i> الرواتب والبدلات
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="label">الراتب الأساسي</div>
                            <div class="value">
                                <span class="view-mode"><?php echo number_format((float)($employee['basic_salary'] ?? 0), 2); ?></span>
                                <input type="number" step="0.01" name="base_salary" class="form-control edit-mode salary-part" value="<?php echo ($employee['basic_salary'] ?? 0); ?>">
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="label">بدل سكن</div>
                            <div class="value">
                                <span class="view-mode"><?php echo number_format((float)($employee['housing_allowance'] ?? 0), 2); ?></span>
                                <input type="number" step="0.01" name="housing_allowance" class="form-control edit-mode salary-part" value="<?php echo ($employee['housing_allowance'] ?? 0); ?>">
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="label">بدل مواصلات (n4)</div>
                            <div class="value">
                                <span class="view-mode"><?php echo number_format((float)($employee['n4'] ?? 0), 2); ?></span>
                                <input type="number" step="0.01" name="n4" class="form-control edit-mode salary-part" value="<?php echo ($employee['n4'] ?? 0); ?>">
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="label">بدل اتصالات (n7)</div>
                            <div class="value">
                                <span class="view-mode"><?php echo number_format((float)($employee['n7'] ?? 0), 2); ?></span>
                                <input type="number" step="0.01" name="n7" class="form-control edit-mode salary-part" value="<?php echo ($employee['n7'] ?? 0); ?>">
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="label">بدل طبيعة العمل (n5)</div>
                            <div class="value">
                                <span class="view-mode"><?php echo number_format((float)($employee['work_nature_allowance'] ?? 0), 2); ?></span>
                                <input type="number" step="0.01" name="n5" class="form-control edit-mode salary-part" value="<?php echo ($employee['work_nature_allowance'] ?? 0); ?>">
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="label">بدل سماعة (n6)</div>
                            <div class="value">
                                <span class="view-mode"><?php echo number_format((float)($employee['headphone_allowance'] ?? 0), 2); ?></span>
                                <input type="number" step="0.01" name="n6" class="form-control edit-mode salary-part" value="<?php echo ($employee['headphone_allowance'] ?? 0); ?>">
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="label">أخرى</div>
                            <div class="value">
                                <span class="view-mode"><?php echo number_format((float)($employee['other_allowance'] ?? 0), 2); ?></span>
                                <input type="number" step="0.01" name="other_allowances" class="form-control edit-mode salary-part" value="<?php echo ($employee['other_allowance'] ?? 0); ?>">
                            </div>
                        </div>
                        
                        <input type="hidden" name="commissions" class="salary-part" value="<?php echo ($employee['commissions'] ?? 0); ?>">

                        <div class="info-item">
                            <div class="label">بدل المحروقات (n8)</div>
                            <div class="value">
                                <span class="view-mode"><?php echo number_format((float)($employee['fuel_allowance'] ?? 0), 2); ?></span>
                                <input type="number" step="0.01" name="n8" class="form-control edit-mode salary-part" value="<?php echo ($employee['fuel_allowance'] ?? 0); ?>">
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="label">بدل مواصلات إضافي (n9)</div>
                            <div class="value">
                                <span class="view-mode"><?php echo number_format((float)($employee['extra_transport_allowance'] ?? 0), 2); ?></span>
                                <input type="number" step="0.01" name="n9" class="form-control edit-mode salary-part" value="<?php echo ($employee['extra_transport_allowance'] ?? 0); ?>">
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="label">بدل الإشراف (n10)</div>
                            <div class="value">
                                <span class="view-mode"><?php echo number_format((float)($employee['supervision_allowance'] ?? 0), 2); ?></span>
                                <input type="number" step="0.01" name="n10" class="form-control edit-mode salary-part" value="<?php echo ($employee['supervision_allowance'] ?? 0); ?>">
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="label">بدل إعاشة (n11)</div>
                            <div class="value">
                                <span class="view-mode"><?php echo number_format((float)($employee['subsistence_allowance'] ?? 0), 2); ?></span>
                                <input type="number" step="0.01" name="n11" class="form-control edit-mode salary-part" value="<?php echo ($employee['subsistence_allowance'] ?? 0); ?>">
                            </div>
                        </div>
                        <input type="hidden" name="n12" class="salary-part" value="<?php echo ($employee['n12'] ?? 0); ?>">

                        <div class="info-item" style="background: #f0f8ff; border: 1px solid #cce5ff;">
                            <div class="label text-primary">كامل الراتب (Total Salary)</div>
                            <div class="value">
                                <span class="view-mode badge bg-success-subtle text-dark badge-pill fs-6" id="totalSalaryView">
                                    <?php echo number_format((float)($employee['total_salary'] ?? 0), 2); ?>
                                </span>
                                <div class="input-group edit-mode" style="display:none; direction: ltr;">
                                    <button class="btn btn-outline-success" type="button" id="btnAutoCalc" title="جمع كل البدلات تلقائياً">
                                        <i class="fa-solid fa-calculator"></i> Auto
                                    </button>
                                    <input type="number" step="0.01" name="total_salary" id="total_salary_input" class="form-control text-center fw-bold text-success" value="<?php echo ($employee['total_salary'] ?? 0); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="table-card mb-3">
                    <div class="section-title"><i class="fa-solid fa-suitcase-rolling"></i> رصيد الإجازات</div>
                    <?php $lb = $leave_balance ?? []; ?>
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
                    <div class="section-title"><i class="fa-solid fa-paperclip"></i> المرفقات والوثائق</div>
                    
                    <?php 
                    $has_files = false;
                    foreach($attachment_map as $db_col => $title) {
                        if (!empty($employee[$db_col])) {
                            $has_files = true;
                            break;
                        }
                    }
                    ?>

                    <?php if(!$has_files): ?>
                        <div class="alert alert-light m-0 text-muted"><i class="fa fa-info-circle"></i> لا توجد مرفقات محفوظة لهذا الموظف.</div>
                    <?php else: ?>
                        <div class="vstack gap-2">
                            <?php foreach($attachment_map as $db_col => $title): ?>
                                <?php if (!empty($employee[$db_col])): ?>
                                    <?php 
                                        $file_path = $employee[$db_col];
                                        $file_url = $file_base_url . $file_path;
                                        $file_ext = pathinfo($file_path, PATHINFO_EXTENSION);
                                        $icon = 'fa-file';
                                        if(in_array(strtolower($file_ext), ['pdf'])) $icon = 'fa-file-pdf text-danger';
                                        if(in_array(strtolower($file_ext), ['jpg', 'jpeg', 'png'])) $icon = 'fa-file-image text-primary';
                                        if(in_array(strtolower($file_ext), ['doc', 'docx'])) $icon = 'fa-file-word text-primary';
                                    ?>
                                    <div class="file-card d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-3">
                                            <div style="font-size: 1.5rem;"><i class="fa-regular <?php echo $icon; ?>"></i></div>
                                            <div style="overflow: hidden;">
                                                <div class="fw-semibold text-truncate" title="<?php echo $title; ?>"><?php echo $title; ?></div>
                                                <div class="meta text-truncate" style="font-size: 0.75rem; direction: ltr; text-align: left;">
                                                    <?php echo basename($file_path); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <a class="btn btn-sm btn-outline-primary" target="_blank" href="<?php echo htmlspecialchars($file_url); ?>">
                                                <i class="fa fa-eye"></i> عرض
                                            </a>
                                            <a class="btn btn-sm btn-primary" href="<?php echo htmlspecialchars($file_url); ?>" download>
                                                <i class="fa fa-download"></i> تحميل
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
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
    <div class="col-12 mt-4">
    <div class="info-card">
        <div class="card-header-custom justify-content-between">
            <div>
                <i class="fas fa-history me-2 text-secondary"></i> سجل إيقاف الراتب / الإجازات
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4">من تاريخ</th>
                            <th>إلى تاريخ</th>
                            <th>السبب</th>
                            <th>إيقاف الإجازات؟</th>
                            <th>الحالة</th>
                            <th class="text-end px-4">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($suspensions)): ?>
                            <?php foreach($suspensions as $sus): 
                                // Determine Status
                                $isActive = false;
                                $today = date('Y-m-d');
                                if (empty($sus->end_date) || $sus->end_date >= $today) {
                                    $isActive = true;
                                }
                            ?>
                            <tr>
                                <td class="px-4 fw-bold"><?php echo $sus->start_date; ?></td>
                                <td>
                                    <?php echo $sus->end_date ? $sus->end_date : '<span class="badge bg-dark">مفتوح (لا يوجد نهاية)</span>'; ?>
                                </td>
                                <td><?php echo $sus->reason; ?></td>
                                <td>
                                    <?php if($sus->stop_leave_accrual): ?>
                                        <span class="text-danger"><i class="fas fa-check-circle"></i> نعم</span>
                                    <?php else: ?>
                                        <span class="text-muted">لا (راتب فقط)</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($isActive): ?>
                                        <span class="badge bg-danger">نشط حالياً</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">منتهي</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end px-4">
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteSuspension(<?php echo $sus->id; ?>)">
                                        <i class="fas fa-trash-alt"></i> حذف
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">لا يوجد سجلات إيقاف سابقة</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>

<div class="modal fade" id="requestsModal" tabindex="-1" aria-labelledby="requestsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title" id="requestsModalLabel">
            <i class="fa-solid fa-clock-rotate-left text-primary"></i> سجل طلبات الموظف
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover text-center m-0 align-middle">
                <thead class="table-dark sticky-top">
                    <tr>
                        <th width="10%">#</th>
                        <th width="25%">نوع الطلب</th>
                        <th width="20%">التاريخ</th>
                        <th width="15%">الحالة</th>
                        <th width="10%">مرفق</th>
                        <th width="20%">تفاصيل</th> 
                    </tr>
                </thead>
                <tbody id="requestsTableBody">
                    </tbody>
            </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="suspensionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">إيقاف الراتب & أرصدة الإجازات</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="suspensionForm">
                <div class="modal-body">
                    <input type="hidden" name="emp_id" value="<?php echo $employee['employee_id']; ?>">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

                    <div class="alert alert-warning small">
                        <i class="fas fa-info-circle"></i> 
                        سيتم احتساب الراتب بشكل نسبي (Pro-rata) خلال أشهر الإيقاف، ولن يتم ترحيل رصيد الإجازات لهذه الفترة.
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">تاريخ البداية (من)</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">تاريخ النهاية (إلى)</label>
                        <input type="date" name="end_date" class="form-control">
                        <div class="form-text text-muted">اتركه فارغاً للإيقاف المفتوح</div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="stop_leaves" id="stopLeaves" value="1" checked>
                        <label class="form-check-label" for="stopLeaves">إيقاف رصيد الإجازات أيضاً (موصى به)</label>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">السبب</label>
                        <textarea name="reason" class="form-control" rows="2" required placeholder="مثال: إجازة بدون راتب، انقطاع عن العمل..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">تأكيد الإيقاف</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    
    // --- 1. IBAN VALIDATION ---
    function validateIBAN() {
        const ibanInput = $('input[name="iban"]');
        let ibanValue = ibanInput.val().replace(/\s+/g, ''); // Remove spaces
        ibanInput.val(ibanValue); 
        
        ibanInput.removeClass('is-invalid is-valid');
        ibanInput.closest('.value').find('.iban-error').remove();
        
        if (ibanValue === '') { return true; }
        
        if (ibanValue.length !== 24) {
            ibanInput.addClass('is-invalid');
            const errorElement = $('<div class="iban-error text-danger small mt-1"></div>');
            errorElement.text('رقم الآيبان يجب أن يكون 24 خانة بالضبط (الحالي: ' + ibanValue.length + ')');
            ibanInput.closest('.value').append(errorElement);
            return false;
        }
        
        ibanInput.addClass('is-valid');
        return true;
    }

    $('input[name="iban"]').on('input blur', function() {
        validateIBAN();
    });

    // --- 2. EDIT/CANCEL TOGGLE ---
    $('#editButton').on('click', function(e) {
        e.preventDefault();
        $('.view-mode').hide();
        $('#view-mode-buttons').hide();
        $('.edit-mode').show();
        $('#edit-mode-buttons').show();
        $('.edit-mode').removeClass('is-invalid is-valid');
        $('.iban-error').remove();
        
        // Calculate total salary when entering edit mode
        setTimeout(function() {
            calculateTotalSalary();
        }, 100);
    });

    $('#cancelButton').on('click', function() {
        $('.edit-mode').hide();
        $('#edit-mode-buttons').hide();
        $('.view-mode').show();
        $('#view-mode-buttons').show();
        $('.edit-mode').removeClass('is-invalid is-valid');
        $('.iban-error').remove();
    });

    // --- 3. AUTO CALCULATE SALARY ---
    function calculateTotalSalary() {
        let total = 0;
        // Loop through all inputs tagged with 'salary-part'
        $('.salary-part').each(function() {
            let val = parseFloat($(this).val());
            if (!isNaN(val)) {
                total += val;
            }
        });

        // Update the total salary input
        $('#total_salary_input').val(total.toFixed(2));
        // Also update the view mode display
        $('#totalSalaryView').text(total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
        
        // Visual feedback
        $('#total_salary_input').fadeOut(100).fadeIn(100);
    }

    $('#btnAutoCalc').on('click', function() {
        calculateTotalSalary();
    });

    // --- 4. SAVE DATA ---
    $('#saveButton').on('click', function(e) {
        e.preventDefault();

        if (validateIBAN() === false) {
            alert('لا يمكن الحفظ: رقم الآيبان غير صحيح. يجب أن يكون 24 خانة.');
            $('input[name="iban"]').focus();
            return;
        }

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
                    alert(response.message);
                    if(response.csrf_name && response.csrf_hash) {
                        $('input[name="' + response.csrf_name + '"]').val(response.csrf_hash);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error("Full Error:", xhr.responseText); 
                let msg = "حدث خطأ أثناء الاتصال بالخادم.\n";
                if (xhr.status == 403) {
                    msg += "السبب: تم رفض الوصول (403). يرجى تحديث الصفحة والمحاولة مرة أخرى.";
                } else {
                    msg += "رمز الخطأ: " + xhr.status + "\n" + error;
                }
                alert(msg);
            },
            complete: function() {
                saveBtn.prop('disabled', false).html('<i class="fa fa-save"></i> حفظ التغييرات');
            }
        });
    });

    // --- 5. REQUEST LOGS ---
    $('#showRequestsBtn').on('click', function(e) {
        e.preventDefault();
        var empId = $('input[name="employee_id"]').val();
        
        if(!empId) {
            alert('خطأ: لم يتم العثور على الرقم الوظيفي');
            return;
        }

        var modal = new bootstrap.Modal(document.getElementById('requestsModal'));
        $('#requestsTableBody').html('<tr><td colspan="6" class="p-4"><div class="spinner-border text-primary" role="status"></div><br>جارٍ تحميل البيانات...</td></tr>');
        modal.show();

        $.ajax({
            url: "<?php echo site_url('users1/get_employee_requests_ajax'); ?>",
            type: "POST",
            data: {
                employee_id: empId, 
                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
            },
            success: function(response) {
                $('#requestsTableBody').html(response);
            },
            error: function(xhr, status, error) {
                console.error(error);
                $('#requestsTableBody').html('<tr><td colspan="6" class="text-danger p-3">حدث خطأ أثناء الاتصال بالخادم. حاول مرة أخرى.</td></tr>');
            }
        });
    });

    // --- 6. EXPERIENCE CERTIFICATE ---
    $('#btnSendCert').on('click', function(e) {
        e.preventDefault();
        
        if(!confirm('هل أنت متأكد من إنشاء وإرسال شهادة خبرة لهذا الموظف عبر البريد الإلكتروني؟')) {
            return;
        }

        var btn = $(this);
        var originalText = btn.html();
        var empId = $('input[name="employee_id"]').val();
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
        var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جاري الإرسال...');

        $.ajax({
            url: "<?php echo site_url('users1/send_experience_certificate'); ?>",
            type: "POST",
            dataType: "json",
            data: {
                employee_id: empId,
                [csrfName]: csrfHash
            },
            success: function(response) {
                csrfHash = response.csrfHash; 
                if ($('input[name="' + csrfName + '"]').length) {
                    $('input[name="' + csrfName + '"]').val(csrfHash);
                }

                if (response.status === 'success') {
                    alert(response.message);
                } else {
                    alert('خطأ: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                alert('حدث خطأ أثناء الاتصال بالخادم.');
            },
            complete: function() {
                btn.prop('disabled', false).html(originalText);
            }
        });
    });

});

// --- 7. LOAD ANIMATION (FIXED) ---
// Moved outside $(document).ready to avoid race conditions if assets load too fast
function removeLoader() {
    const loading = $('#loading-screen');
    const main = $('.main-container');
    
    // Only run if elements exist and are still visible
    if (loading.length && loading.css('display') !== 'none') {
        loading.css('opacity', '0');
        setTimeout(function() { 
            loading.hide(); 
            $('body').css('overflow', 'auto'); 
            main.css({
                'visibility': 'visible',
                'opacity': '1'
            }); 
        }, 300);
    }
}

// Run on window load (all assets loaded)
$(window).on('load', removeLoader);

// Fallback: If window load fails/hangs (e.g. slow CDN), force remove after 1 second
setTimeout(removeLoader, 1000);

</script>
<script>
function openSuspensionModal() {
    new bootstrap.Modal(document.getElementById('suspensionModal')).show();
}
function deleteSuspension(id) {
    if(!confirm('هل أنت متأكد من حذف هذا الإيقاف؟\nسيتم إعادة احتساب الراتب والإجازات وكأن الإيقاف لم يحدث.')) {
        return;
    }

    $.ajax({
        url: '<?php echo base_url("users1/delete_salary_suspension"); ?>',
        type: 'POST',
        data: { 
            id: id,
            '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
        },
        dataType: 'json',
        success: function(response) {
            if(response.status === 'success') {
                alert(response.message);
                location.reload();
            } else {
                alert('خطأ: ' + response.message);
            }
        },
        error: function() {
            alert('حدث خطأ في الاتصال بالخادم');
        }
    });
}

$('#suspensionForm').on('submit', function(e) {
    e.preventDefault();
    if(!confirm('هل أنت متأكد من هذا الإجراء؟ سيؤثر على الراتب والإجازات.')) return;

    $.ajax({
        url: '<?php echo base_url("users1/save_salary_suspension"); ?>',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(res) {
            if(res.status === 'success') {
                alert(res.message);
                location.reload();
            } else {
                alert(res.message);
            }
        }
    });
});
</script>
</body>
</html>