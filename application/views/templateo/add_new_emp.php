<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>إضافة موظف جديد</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet" />
  
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

  <style>
    :root{ --marsom-blue:#001f3f; --marsom-orange:#FF8C00; --text-light:#fff; --text-dark:#343a40; }
    body{font-family:'Tajawal',sans-serif; overflow-y:auto; background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%); background-size:400% 400%; animation:grad 20s ease infinite; color:var(--text-dark); position:relative; min-height: 100vh;}
    @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
    
    /* Particles & Loader */
    .particles{position:fixed; inset:0; overflow:hidden; z-index:-1;}
    .particle{position:absolute; background:rgba(255,140,0,.1); animation:float 20s infinite ease-in-out; opacity:0; filter:blur(2px);}
    .particle:nth-child(1){width:60px; height:60px; left:10%; top:20%;}
    .particle:nth-child(2){width:40px; height:40px; left:80%; top:60%; animation-delay:2s;}
    @keyframes float{0%{transform:translateY(0) rotate(0); opacity:0;} 50%{opacity:1;} 100%{transform:translateY(-100vh) rotate(360deg); opacity:0;}}

    #loading-screen{position:fixed; inset:0; background:var(--marsom-blue); z-index:9999; display:flex; align-items:center; justify-content:center; flex-direction:column; transition:opacity .5s;}
    .loader{width:50px; height:50px; border:5px solid rgba(255,255,255,.3); border-top:5px solid var(--marsom-orange); border-radius:50%; animation:spin 1s linear infinite; margin-bottom:16px;}
    @keyframes spin{to{transform:rotate(360deg);}}

    /* Layout */
    .main-container{padding:30px 15px; visibility:hidden; opacity:0; transition:opacity .5s; position:relative; z-index:1;}
    .page-title{font-family:'El Messiri',sans-serif; font-weight:700; font-size:2.6rem; color:var(--text-light); margin-bottom:24px; text-align:center; position:relative; display:inline-block; padding-bottom:10px; text-shadow:0 3px 6px rgba(0,0,0,.4);}
    .page-title::after{content:''; position:absolute; width:120px; height:4px; background:linear-gradient(90deg,var(--marsom-blue),var(--marsom-orange)); bottom:0; left:50%; transform:translateX(-50%); border-radius:2px;}
    
    .table-card{background:rgba(255,255,255,.95); backdrop-filter:blur(10px); border:1px solid rgba(255,255,255,.3); border-radius:15px; box-shadow:0 10px 30px rgba(0,0,0,.2); padding:25px;}

    /* Top Actions */
    .top-actions{position:fixed; top:12px; right:12px; display:flex; gap:10px; z-index:100;}
    .top-actions a{background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,0.3); color:#fff; text-decoration:none; border-radius:10px; padding:8px 14px; display:inline-flex; align-items:center; gap:8px; transition:.25s; backdrop-filter: blur(4px);}
    .top-actions a:hover{background:rgba(255,255,255,.3); color:var(--marsom-orange);}

    /* Forms */
    .form-label{font-weight:600; color:#001f3f;}
    .required::after{content:' *'; color:#dc3545;}
    .hint{font-size:.8rem; color:#6c757d; margin-top:4px;}
    .section-title{font-size:1.1rem; font-weight:700; color:#FF8C00; margin:20px 0 15px; display:flex; align-items:center; gap:10px; border-bottom: 2px solid #eee; padding-bottom: 10px;}
    .section-title i{color: var(--marsom-blue);}
    
    /* Validation Errors */
    .is-invalid { border-color: #dc3545 !important; background-image: none !important; }
    .invalid-feedback { display: block; font-size: 0.8rem; margin-top: 4px; }
  </style>
</head>
<body>

<div class="particles"><div class="particle"></div><div class="particle"></div><div class="particle"></div></div>

<div id="loading-screen">
  <div class="loader"></div>
  <h3 style="color:#fff">جارٍ تحميل الشاشة ...</h3>
</div>

<div class="top-actions">
  <a href="javascript:history.back()"><i class="fas fa-arrow-right"></i><span>رجوع</span></a>
  <a href="<?php echo site_url('dashboard'); ?>"><i class="fas fa-home"></i><span>الرئيسية</span></a>
</div>

<div class="main-container container-fluid">
  <div class="text-center">
    <h1 class="page-title">إضافة موظف جديد</h1>
  </div>

  <div class="row justify-content-center">
    <div class="col-12 col-xl-11">
      <div class="card table-card">
        <div class="card-body">
          
          <?php echo validation_errors('<div class="alert alert-danger">','</div>'); ?>
          
          <?php echo form_open_multipart('users1/store_employee', ['id'=>'addEmployeeForm', 'novalidate'=>true]); ?>

          <div class="section-title"><i class="fa-solid fa-id-card"></i> البيانات الأساسية</div>
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label required">الرقم الوظيفي</label>
              <input type="text" class="form-control" name="employee_code" required placeholder="مثال: 10234" />
            </div>
            
            <div class="col-md-4">
              <label class="form-label required">الاسم كاملاً (عربي)</label>
              <input type="text" class="form-control" name="full_name_ar" id="full_name_ar" required placeholder="الاسم الرباعي (4 كلمات)" />
              <div class="invalid-feedback">يجب أن يتكون الاسم من 4 كلمات على الأقل.</div>
            </div>

            <div class="col-md-2">
              <label class="form-label required">الجنسية</label>
              <input type="text" class="form-control" name="nationality" required placeholder="سعودي" />
            </div>

            <div class="col-md-3">
              <label class="form-label required">رقم الهوية/الإقامة</label>
              <input type="number" class="form-control" name="id_number" id="id_number" required placeholder="10 أرقام" />
              <div class="invalid-feedback">رقم الهوية يجب أن يتكون من 10 أرقام.</div>
            </div>

            <div class="col-md-3">
              <label class="form-label required">تاريخ الانضمام</label>
              <input type="date" class="form-control" name="join_date" required />
            </div>
            <div class="col-md-3">
              <label class="form-label">القسم</label>
              <input type="text" class="form-control" name="department" placeholder="الموارد البشرية" />
            </div>
            <div class="col-md-3">
              <label class="form-label">تاريخ الميلاد</label>
              <input type="date" class="form-control" name="birth_date" />
            </div>
            <div class="col-md-3">
              <label class="form-label required">المسمى الوظيفي</label>
              <input type="text" class="form-control" name="job_title" required placeholder="محاسب / مشرف" />
            </div>

            <div class="col-md-3">
              <label class="form-label">الجنس</label>
              <select class="form-select" name="gender">
                <option value="">— اختر —</option>
                <option value="ذكر">ذكر</option>
                <option value="أنثى">أنثى</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">الحالة الاجتماعية</label>
              <select class="form-select" name="marital_status">
                <option value="">— اختر —</option>
                <option value="أعزب">أعزب</option>
                <option value="متزوج">متزوج</option>
                <option value="مطلق">مطلق</option>
                <option value="أرمل">أرمل</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">الديانة</label>
              <select class="form-select" name="religion">
                <option value="الإسلام" selected>الإسلام</option>
                <option value="المسيحية">المسيحية</option>
                <option value="أخرى">أخرى</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">تاريخ انتهاء الهوية</label>
              <input type="date" class="form-control" name="id_expiry" />
            </div>
          </div>

          <div class="row g-3 mt-1">
            <div class="col-md-4">
              <label class="form-label">البريد الإلكتروني الشخصي</label>
              <input type="email" class="form-control" name="personal_email" placeholder="email@gmail.com" />
            </div>
            <div class="col-md-4">
              <label class="form-label">إيميل الشركة</label>
              <input type="email" class="form-control" name="company_email" placeholder="user@marsoom.sa" />
            </div>
            <div class="col-md-4">
              <label class="form-label required">رقم الجوال</label>
              <input type="tel" class="form-control" name="mobile" id="mobile" required placeholder="9665XXXXXXXX" />
              <div class="hint">مثال: 966554999888</div>
              <div class="invalid-feedback">يجب أن يبدأ بـ 966 ويتكون من 12 خانة.</div>
            </div>
          </div>

          <div class="row g-3 mt-1">
             <div class="col-md-6">
               <label class="form-label">العنوان</label>
               <input type="text" class="form-control" name="address" placeholder="المدينة - الحي" />
             </div>
             <div class="col-md-3">
               <label class="form-label">نوع الدوام</label>
               <select class="form-select" name="employment_type">
                 <option value="دوام كامل">دوام كامل</option>
                 <option value="دوام جزئي">دوام جزئي</option>
                 <option value="عن بعد">عن بعد</option>
               </select>
             </div>
             <div class="col-md-3">
               <label class="form-label">الشركة</label>
               <select class="form-select" name="company">
                 <option value="مرسوم">مرسوم</option>
                 <option value="مكتب الدكتور صالح الجربوع">مكتب الدكتور</option>
               </select>
             </div>
             <div class="col-md-3">
               <label class="form-label">الموقع</label>
               <input type="text" class="form-control" name="location" placeholder="الرياض" />
             </div>
             <div class="col-md-3">
               <label class="form-label">المدير المباشر</label>
               <input type="text" class="form-control" name="direct_manager" />
             </div>
          </div>

          <hr class="my-4" />

          <div class="section-title"><i class="fa-solid fa-building-columns"></i> بيانات الحساب البنكي</div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label required">رقم الآيبان</label>
              <input type="text" class="form-control" name="iban" id="iban" required placeholder="SA..." maxlength="29" dir="ltr" />
              <div class="hint text-end">يجب أن يتكون من 24 خانة بالضبط</div>
              <div class="invalid-feedback text-end">رقم الآيبان يجب أن يكون 24 خانة.</div>
            </div>
            <div class="col-md-6">
              <label class="form-label required">اسم البنك</label>
              <select class="form-select" name="bank_name" id="bank_name" required>
                <option value="">— اختر البنك —</option>
                <option value="Al Rajhi Bank">Al Rajhi Bank</option>
                <option value="AlBank AlSaudi AlFransi">AlBank AlSaudi AlFransi</option>
                <option value="Alinma Bank">Alinma Bank</option>
                <option value="Arab National Bank">Arab National Bank</option>
                <option value="Bank Albilad">Bank Albilad</option>
                <option value="National Bank of Bahrain">National Bank of Bahrain</option>
                <option value="Bank AlJazira">Bank AlJazira</option>
                <option value="Bank Muscat">Bank Muscat</option>
                <option value="Deutsche Bank">Deutsche Bank</option>
                <option value="Emirates Bank Intl">Emirates Bank Intl</option>
                <option value="Gulf International Bank">Gulf International Bank</option>
                <option value="National Bank of Kuwait">National Bank of Kuwait</option>
                <option value="National Commercial Bank">National Commercial Bank</option>
                <option value="Riyad Bank">Riyad Bank</option>
                <option value="First Abu Dhabi Bank">First Abu Dhabi Bank</option>
                <option value="National Bank of Pakistan">National Bank of Pakistan</option>
                <option value="Saudi Investment Bank">Saudi Investment Bank</option>
                <option value="State Bank of India">State Bank of India</option>
                <option value="T.C. Ziraat Bankasi">T.C. Ziraat Bankasi</option>
                <option value="The Saudi British Bank">The Saudi British Bank</option>
                <option value="Standard Chartered Bank">Standard Chartered Bank</option>
                <option value="Saudi Cairo Bank">Saudi Cairo Bank</option>
                <option value="Mufg Bank">Mufg Bank</option>
                <option value="JP Morgan Chase Bank">JP Morgan Chase Bank</option>
                <option value="ICBK Bank">ICBK Bank</option>
              </select>
            </div>
          </div>

          <hr class="my-4" />

          <div class="section-title"><i class="fa-solid fa-money-bill-trend-up"></i> الرواتب والبدلات</div>
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label required">الراتب الأساسي</label>
              <input type="number" step="0.01" class="form-control salary-part" name="basic_salary" id="basic_salary" required placeholder="0.00" />
            </div>
            <div class="col-md-3">
              <label class="form-label">بدل سكن</label>
              <input type="number" step="0.01" class="form-control salary-part" name="housing_allowance" id="housing_allowance" placeholder="0.00" />
            </div>
            <div class="col-md-3">
              <label class="form-label">بدل مواصلات</label>
              <input type="number" step="0.01" class="form-control salary-part" name="transportation_allowance" placeholder="0.00" />
            </div>
            <div class="col-md-3">
              <label class="form-label">بدل اتصالات</label>
              <input type="number" step="0.01" class="form-control salary-part" name="communication_allowance" placeholder="0.00" />
            </div>
            <div class="col-md-3"><label class="form-label">بدل طبيعة عمل</label><input type="number" step="0.01" class="form-control salary-part" name="work_nature_allowance" /></div>
            <div class="col-md-3"><label class="form-label">بدل سماعة</label><input type="number" step="0.01" class="form-control salary-part" name="headphone_allowance" /></div>
            <div class="col-md-3"><label class="form-label">أخرى</label><input type="number" step="0.01" class="form-control salary-part" name="other_allowance" /></div>
            <div class="col-md-3"><label class="form-label">بدل محروقات</label><input type="number" step="0.01" class="form-control salary-part" name="fuel_allowance" /></div>
            <div class="col-md-3"><label class="form-label">مواصلات إضافي</label><input type="number" step="0.01" class="form-control salary-part" name="extra_transport_allowance" /></div>
            <div class="col-md-3"><label class="form-label">بدل إشراف</label><input type="number" step="0.01" class="form-control salary-part" name="supervision_allowance" /></div>
            <div class="col-md-3"><label class="form-label">بدل إعاشة</label><input type="number" step="0.01" class="form-control salary-part" name="subsistence_allowance" /></div>

            <div class="col-md-3 pt-4">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="auto_total" checked />
                <label class="form-check-label" for="auto_total">حساب تلقائي</label>
              </div>
            </div>
            
            <div class="col-md-3">
              <label class="form-label required">كامل الراتب</label>
              <input type="number" step="0.01" class="form-control" name="total_salary" id="total_salary" required placeholder="0.00" />
            </div>
          </div>

          <hr class="my-4" />

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> حفظ البيانات</button>
            <a href="<?php echo site_url('dashboard'); ?>" class="btn btn-secondary"><i class="fa-solid fa-times"></i> إلغاء</a>
          </div>

          <?php echo form_close(); ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // --- 1. Loading Screen ---
    const loading = document.getElementById('loading-screen');
    const main = document.querySelector('.main-container');
    if(loading) {
        loading.style.opacity = '0';
        setTimeout(() => { 
            loading.style.display = 'none'; 
            document.body.style.overflow = 'auto'; 
            if(main) { main.style.visibility = 'visible'; main.style.opacity = '1'; }
        }, 400);
    }

    // --- 2. Auto Total Calculation ---
    const parts = document.querySelectorAll('.salary-part');
    const totalField = document.getElementById('total_salary');
    const autoToggle = document.getElementById('auto_total');

    function recalcTotal() {
        if(!autoToggle || !autoToggle.checked) return;
        let sum = 0;
        parts.forEach(el => { sum += parseFloat(el.value || 0); });
        if(totalField) totalField.value = sum.toFixed(2);
    }

    if(parts.length > 0) {
        parts.forEach(el => el.addEventListener('input', recalcTotal));
        if(autoToggle) {
            autoToggle.addEventListener('change', function() {
                if(totalField) totalField.readOnly = this.checked;
                if(this.checked) recalcTotal();
            });
        }
        // Run once on load
        recalcTotal();
    }

    // --- 3. FORM VALIDATION ON SUBMIT ---
    const form = document.getElementById('addEmployeeForm');
    
    if(form) {
        form.addEventListener('submit', function(e) {
            let errors = [];

            // A. Name Validation (4 Words)
            const nameVal = document.querySelector('input[name="full_name_ar"]').value.trim();
            if (nameVal.split(/\s+/).filter(w => w.length > 0).length < 4) {
                errors.push("الاسم يجب أن يكون رباعياً (4 كلمات على الأقل).");
            }

            // B. ID Validation (10 Digits)
            const idVal = document.querySelector('input[name="id_number"]').value.trim();
            if (!/^\d{10}$/.test(idVal)) {
                errors.push("رقم الهوية يجب أن يتكون من 10 أرقام بالضبط.");
            }

            // C. Mobile Validation (966 + 9 Digits)
            const mobileVal = document.querySelector('input[name="mobile"]').value.trim();
            if (!/^966\d{9}$/.test(mobileVal)) {
                errors.push("رقم الجوال يجب أن يبدأ بـ 966 ويتبعه 9 أرقام (مثال: 966554999888).");
            }

            // D. IBAN Validation (24 Characters)
            const ibanInput = document.querySelector('input[name="iban"]');
            // Strip spaces before checking length
            const ibanVal = ibanInput.value.replace(/\s+/g, '');
            
            // Set the cleaned value back to input so controller gets clean data
            ibanInput.value = ibanVal;

            if (ibanVal.length !== 24) {
                errors.push("رقم الآيبان يجب أن يكون 24 خانة بالضبط.");
            }

            // E. Show Errors or Submit
            if (errors.length > 0) {
                e.preventDefault(); // Stop submission
                alert("تنبيه:\n- " + errors.join("\n- "));
            } else {
                // If Valid, ensure total salary is calculated one last time
                recalcTotal();
            }
        });
    }
});
</script>
</body>
</html>