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
        
        /* Validation Error Styling */
        .is-invalid { border-color: #dc3545; }
        .error-message { color: #dc3545; font-size: 0.8rem; margin-top: 4px; display: none; }
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
                    
                    <form action="<?= site_url('users1/store_employee') ?>" method="post" id="addEmployeeForm">
                        <input type="hidden" name="id" value="<?= $employee['id'] ?? '' ?>">
                        
                        <div class="section-title"><i class="fa-solid fa-id-card"></i>البيانات الأساسية</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">الرقم الوظيفي <span class="text-danger">*</span></label>
                                <input type="text" name="employee_code" class="form-control" value="<?= $employee['employee_id'] ?? '' ?>" required>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">رقم الهوية (10 أرقام) <span class="text-danger">*</span></label>
                                <input type="number" name="id_number" id="id_number" class="form-control" value="<?= $employee['id_number'] ?? '' ?>" required>
                                <div class="error-message" id="error-id">رقم الهوية يجب أن يكون 10 أرقام بالضبط</div>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">الاسم كاملاً (4 كلمات) <span class="text-danger">*</span></label>
                                <input type="text" name="full_name_ar" id="full_name_ar" class="form-control" value="<?= $employee['subscriber_name'] ?? '' ?>" placeholder="الاسم الأول الأب الجد العائلة" required>
                                <div class="error-message" id="error-name">يجب كتابة الاسم رباعياً (4 كلمات على الأقل)</div>
                            </div>

                            <div class="col-md-4"><label class="form-label">تاريخ الميلاد</label><input type="date" name="birth_date" class="form-control" value="<?= $employee['birth_date'] ?? '' ?>"></div>
                            <div class="col-md-4"><label class="form-label">الجنسية</label><input type="text" name="nationality" class="form-control" value="<?= $employee['nationality'] ?? '' ?>"></div>
                            <div class="col-md-4"><label class="form-label">الجنس</label><select name="gender" class="form-select"><option value="ذكر" <?= (($employee['gender'] ?? '') === 'ذكر') ? 'selected' : ''; ?>>ذكر</option><option value="أنثى" <?= (($employee['gender'] ?? '') === 'أنثى') ? 'selected' : ''; ?>>أنثى</option></select></div>
                            <div class="col-md-4"><label class="form-label">الحالة الاجتماعية</label><input type="text" name="marital_status" class="form-control" value="<?= $employee['marital'] ?? '' ?>"></div>
                            <div class="col-md-4"><label class="form-label">الديانة</label><input type="text" name="religion" class="form-control" value="<?= $employee['religion'] ?? '' ?>"></div>
                            <div class="col-md-4"><label class="form-label">تاريخ انتهاء الهوية</label><input type="date" name="id_expiry" class="form-control" value="<?= $employee['id_expiry'] ?? '' ?>"></div>
                        </div>

                        <div class="section-title mt-4"><i class="fa-solid fa-phone"></i>بيانات التواصل</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">الجوال (966XXXXXXXXX) <span class="text-danger">*</span></label>
                                <input type="number" name="mobile" id="mobile" class="form-control" value="<?= $employee['phone'] ?? '' ?>" placeholder="9665xxxxxxxx" required>
                                <div class="error-message" id="error-mobile">يجب أن يبدأ بـ 966 ويتكون من 12 رقم</div>
                            </div>
                            <div class="col-md-4"><label class="form-label">البريد الإلكتروني الشخصي</label><input type="email" name="personal_email" class="form-control" value="<?= $employee['email'] ?? '' ?>"></div>
                            <div class="col-md-4"><label class="form-label">إيميل الشركة</label><input type="email" name="company_email" class="form-control" value="<?= $employee['n13'] ?? '' ?>"></div>
                            <div class="col-md-12"><label class="form-label">العنوان</label><input type="text" name="address" class="form-control" value="<?= $employee['address'] ?? '' ?>"></div>
                        </div>

                        <div class="section-title mt-4"><i class="fa-solid fa-briefcase"></i>البيانات الوظيفية</div>
                        <div class="row g-3">
                            <div class="col-md-4"><label class="form-label">تاريخ الانضمام</label><input type="date" name="join_date" class="form-control" value="<?= $employee['joining_date'] ?? '' ?>" required></div>
                            <div class="col-md-4"><label class="form-label">المسمى الوظيفي</label><input type="text" name="job_title" class="form-control" value="<?= $employee['profession'] ?? '' ?>" required></div>
                            <div class="col-md-4"><label class="form-label">المدير المباشر</label><input type="text" name="direct_manager" class="form-control" value="<?= $employee['manager'] ?? '' ?>" required></div>
                            <div class="col-md-4"><label class="form-label">الشركة</label><input type="text" name="company" class="form-control" value="<?= $employee['company_name'] ?? '' ?>" required></div>
                            <div class="col-md-4"><label class="form-label">القسم</label><input type="text" name="department" class="form-control" value="<?= $employee['n1'] ?? '' ?>" required></div>
                            <div class="col-md-4"><label class="form-label">الموقع</label><input type="text" name="location" class="form-control" value="<?= $employee['location'] ?? '' ?>" required></div>
                            <div class="col-md-4"><label class="form-label">نوع الدوام</label><input type="text" name="employment_type" class="form-control" value="<?= $employee['type'] ?? '' ?>"></div>
                        </div>

                        <div class="section-title mt-4"><i class="fa-solid fa-money-bill-wave"></i>البيانات المالية</div>
                        <div class="row g-3">
                            <div class="col-md-3"><label class="form-label">الراتب الأساسي</label><input type="number" step="0.01" name="basic_salary" class="form-control salary-part" value="<?= $employee['base_salary'] ?? '' ?>" required></div>
                            <div class="col-md-3"><label class="form-label">بدل السكن</label><input type="number" step="0.01" name="housing_allowance" class="form-control salary-part" value="<?= $employee['housing_allowance'] ?? '' ?>" required></div>
                            <div class="col-md-3"><label class="form-label">بدل النقل</label><input type="number" step="0.01" name="transportation_allowance" class="form-control salary-part" value="<?= $employee['commissions'] ?? '' ?>" required></div>
                            <div class="col-md-3"><label class="form-label">بدل اتصالات</label><input type="number" step="0.01" name="communication_allowance" class="form-control salary-part" value="<?= $employee['n7'] ?? '' ?>" ></div>
                            
                            <div class="col-md-3"><label class="form-label">بدل طبيعة عمل</label><input type="number" step="0.01" name="work_nature_allowance" class="form-control salary-part" value="<?= $employee['n5'] ?? '' ?>"></div>
                            <div class="col-md-3"><label class="form-label">بدل سماعة</label><input type="number" step="0.01" name="headphone_allowance" class="form-control salary-part" value="<?= $employee['n6'] ?? '' ?>"></div>
                            <div class="col-md-3"><label class="form-label">أخرى</label><input type="number" step="0.01" name="other_allowance" class="form-control salary-part" value="<?= $employee['other_allowances'] ?? '' ?>" required></div>
                            
                            <div class="col-md-3">
                                <label class="form-label">إجمالي الراتب</label>
                                <input type="number" step="0.01" name="total_salary" id="total_salary" class="form-control" value="<?= $employee['total_salary'] ?? '' ?>" readonly>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">اسم البنك <span class="text-danger">*</span></label>
                                <select name="bank_name" class="form-select" required>
                                    <option value="">-- اختر البنك --</option>
                                    <?php 
                                    $banks = [
                                        'Al Rajhi Bank', 'AlBank AlSaudi AlFransi', 'Alinma Bank', 'Arab National Bank',
                                        'Bank Albilad', 'National Bank of Bahrain', 'Bank AlJazira', 'Bank Muscat',
                                        'Deutsche Bank', 'Emirates Bank Intl', 'Gulf International Bank', 'National Bank of Kuwait',
                                        'National Commercial Bank', 'Riyad Bank', 'First Abu Dhabi Bank', 'National Bank of Pakistan',
                                        'Saudi Investment Bank', 'State Bank of India', 'T.C. Ziraat Bankasi', 'The Saudi British Bank',
                                        'Standard Chartered Bank', 'Saudi Cairo Bank', 'Mufg Bank', 'JP Morgan Chase Bank', 'ICBK Bank'
                                    ];
                                    $current_bank = $employee['n3'] ?? '';
                                    foreach($banks as $bank): ?>
                                        <option value="<?= $bank ?>" <?= ($current_bank == $bank) ? 'selected' : '' ?>><?= $bank ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-8">
                                <label class="form-label">الآيبان (24 حرف) <span class="text-danger">*</span></label>
                                <input type="text" name="iban" id="iban" class="form-control" value="<?= $employee['n2'] ?? '' ?>" placeholder="SA..." required maxlength="24">
                                <div class="error-message" id="error-iban">يجب أن يكون الآيبان 24 خانة بالضبط (بدون مسافات)</div>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-top d-flex justify-content-end">
                            <a href="<?= site_url('users1/emp_data101') ?>" class="btn btn-secondary me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary" id="saveBtn">حفظ البيانات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Auto Calculate Salary
        const salaryInputs = document.querySelectorAll('.salary-part');
        const totalInput = document.getElementById('total_salary');

        function calculateTotal() {
            let sum = 0;
            salaryInputs.forEach(input => {
                let val = parseFloat(input.value);
                if (!isNaN(val)) sum += val;
            });
            totalInput.value = sum.toFixed(2);
        }

        salaryInputs.forEach(input => {
            input.addEventListener('input', calculateTotal);
        });

        // 2. Validation Logic
        // 2. Validation Logic
        const form = document.getElementById('addEmployeeForm');
        
        if(form) {
            form.addEventListener('submit', function(e) {
                let isValid = true;
                let errors = [];

                // Helper to remove error styles
                const clearError = (input) => { if(input) input.classList.remove('is-invalid'); };
                const setError = (input, msg) => { 
                    if(input) {
                        input.classList.add('is-invalid');
                        errors.push(msg);
                    }
                };

                // --- A. Identity Checks (Existing) ---
                const nameInput = document.querySelector('input[name="full_name_ar"]');
                clearError(nameInput);
                if (nameInput && nameInput.value.trim().split(/\s+/).filter(w => w.length > 0).length < 4) {
                    setError(nameInput, "الاسم يجب أن يكون رباعياً (4 كلمات على الأقل).");
                    isValid = false;
                }

                const idInput = document.querySelector('input[name="id_number"]');
                clearError(idInput);
                if (idInput && !/^\d{10}$/.test(idInput.value.trim())) {
                    setError(idInput, "رقم الهوية يجب أن يتكون من 10 أرقام بالضبط.");
                    isValid = false;
                }

                const mobileInput = document.querySelector('input[name="mobile"]');
                clearError(mobileInput);
                if (mobileInput && !/^966\d{9}$/.test(mobileInput.value.trim())) {
                    setError(mobileInput, "رقم الجوال يجب أن يبدأ بـ 966 ويتكون من 12 خانة.");
                    isValid = false;
                }

                const ibanInput = document.querySelector('input[name="iban"]');
                clearError(ibanInput);
                const ibanVal = ibanInput ? ibanInput.value.replace(/\s+/g, '') : '';
                if (ibanInput) ibanInput.value = ibanVal; // Clean spaces
                if (ibanInput && ibanVal.length !== 24) {
                    setError(ibanInput, "رقم الآيبان يجب أن يكون 24 خانة بالضبط.");
                    isValid = false;
                }

                // --- B. NEW: Job Details Checks ---
                const joinDate = document.querySelector('input[name="join_date"]');
                clearError(joinDate);
                if (joinDate && !joinDate.value) {
                    setError(joinDate, "تاريخ الانضمام مطلوب.");
                    isValid = false;
                }

                const companySelect = document.querySelector('select[name="company"]');
                clearError(companySelect);
                if (companySelect && !companySelect.value) {
                    setError(companySelect, "يرجى اختيار الشركة.");
                    isValid = false;
                }

                const managerInput = document.querySelector('input[name="direct_manager"]');
                clearError(managerInput);
                if (managerInput && !managerInput.value.trim()) {
                    setError(managerInput, "اسم المدير المباشر مطلوب.");
                    isValid = false;
                }

                // --- C. NEW: Financial Checks ---
                const baseSalary = document.querySelector('input[name="basic_salary"]');
                clearError(baseSalary);
                if (baseSalary && (baseSalary.value === '' || parseFloat(baseSalary.value) < 0)) {
                    setError(baseSalary, "الراتب الأساسي مطلوب ولا يمكن أن يكون أقل من صفر.");
                    isValid = false;
                }

                // Check all other allowances are not negative
                document.querySelectorAll('.salary-part').forEach(input => {
                    clearError(input);
                    if (input.value && parseFloat(input.value) < 0) {
                        setError(input, "البدلات لا يمكن أن تكون قيم سالبة.");
                        isValid = false;
                    }
                });

                // --- Conclusion ---
                if (!isValid) {
                    e.preventDefault(); // Stop submission
                    
                    // Create a nice list for the alert
                    let errorMsg = "تنبيه: يرجى تصحيح الأخطاء التالية:\n\n";
                    // Filter duplicates in error array
                    let uniqueErrors = [...new Set(errors)]; 
                    errorMsg += uniqueErrors.map(e => "- " + e).join("\n");
                    
                    alert(errorMsg);
                    
                    // Scroll to the first invalid element
                    const firstInvalid = document.querySelector('.is-invalid');
                    if(firstInvalid) firstInvalid.scrollIntoView({behavior: 'smooth', block: 'center'});
                } else {
                    if (!confirm('هل أنت متأكد من حفظ بيانات الموظف؟')) {
                        e.preventDefault();
                    }
                }
            });
        }
    });
</script>

</body>
</html>