<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>إضافة موظف جديد</title>

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet" />
  
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet" />

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

  <style>
    :root{
      --marsom-blue:#001f3f;--marsom-orange:#FF8C00;--text-light:#fff;--text-dark:#343a40;
      --glass-bg:rgba(255,255,255,.08);--glass-border:rgba(255,255,255,.2);--glass-shadow:rgba(0,0,0,.5)
    }
    body{font-family:'Tajawal',sans-serif;overflow:hidden;background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);background-size:400% 400%;animation:grad 20s ease infinite;color:var(--text-dark);position:relative}
    @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}

    .particles{position:fixed;inset:0;overflow:hidden;z-index:-1}
    .particle{position:absolute;background:rgba(255,140,0,.1);clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);animation:float 25s infinite ease-in-out;opacity:0;filter:blur(2px)}
    .particle:nth-child(even){background:rgba(0,31,63,.1)}
    .particle:nth-child(1){width:40px;height:40px;left:10%;top:20%;animation-duration:18s}
    .particle:nth-child(2){width:70px;height:70px;left:25%;top:50%;animation-duration:22s;animation-delay:2s}
    .particle:nth-child(3){width:55px;height:55px;left:40%;top:10%;animation-duration:25s;animation-delay:5s}
    .particle:nth-child(4){width:80px;height:80px;left:60%;top:70%;animation-duration:20s;animation-delay:8s}
    .particle:nth-child(5){width:60px;height:60px;left:80%;top:30%;animation-duration:23s;animation-delay:10s}
    .particle:nth-child(6){width:45px;height:45px;left:5%;top:85%;animation-duration:19s;animation-delay:3s}
    .particle:nth-child(7){width:90px;height:90px;left:70%;top:5%;animation-duration:28s;animation-delay:6s}
    .particle:nth-child(8){width:35px;height:35px;left:90%;top:40%;animation-duration:17s;animation-delay:12s}
    .particle:nth-child(9){width:75px;height:75px;left:20%;top:75%;animation-duration:21s;animation-delay:1s}
    .particle:nth-child(10){width:65px;height:65px;left:50%;top:90%;animation-duration:24s;animation-delay:4s}
    @keyframes float{0%{transform:translateY(0) translateX(0) rotate(0);opacity:0}20%{opacity:1}80%{opacity:1}100%{transform:translateY(-100vh) translateX(50px) rotate(360deg);opacity:0}}

    #loading-screen{position:fixed;inset:0;background:transparent;z-index:9999;display:flex;align-items:center;justify-content:center;flex-direction:column;transition:opacity .5s}
    .loader{width:50px;height:50px;border:5px solid rgba(255,255,255,.3);border-top:5px solid var(--marsom-orange);border-radius:50%;animation:spin 1s linear infinite;margin-bottom:16px}
    @keyframes spin{to{transform:rotate(360deg)}}

    .main-container{padding:30px 15px;visibility:hidden;opacity:0;transition:opacity .5s;position:relative;z-index:1}
    .page-title{font-family:'El Messiri',sans-serif;font-weight:700;font-size:2.6rem;color:var(--text-light);margin-bottom:24px;text-align:center;position:relative;display:inline-block;padding-bottom:10px;text-shadow:0 3px 6px rgba(0,0,0,.4)}
    .page-title::after{content:'';position:absolute;width:120px;height:4px;background:linear-gradient(90deg,var(--marsom-blue),var(--marsom-orange));bottom:0;left:50%;transform:translateX(-50%);border-radius:2px}
    .table-card{background:rgba(255,255,255,.9);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.3);border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,.15);padding:25px}

    .top-actions{position:fixed;top:12px;right:12px;display:flex;gap:10px;z-index:5}
    .top-actions a{background:rgba(255,255,255,.12);border:1px solid var(--glass-border);color:#fff;text-decoration:none;border-radius:10px;padding:8px 14px;display:inline-flex;align-items:center;gap:8px;transition:.25s}
    .top-actions a:hover{background:rgba(255,255,255,.2);color:var(--marsom-orange)}

    .form-label{font-weight:600}
    .required::after{content:' *'; color:#dc3545}
    .hint{font-size:.85rem;color:#6c757d}
    .section-title{font-size:1.05rem;font-weight:700;color:#0b2447;margin:10px 0 14px;display:flex;align-items:center;gap:8px}
    .section-title i{opacity:.85}
  </style>
</head>
<body>

<!-- الخلفية المتحركة -->
<div class="particles">
  <div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div>
  <div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div>
</div>

<!-- شاشة التحميل -->
<div id="loading-screen">
  <div class="loader"></div>
  <h3 style="color:#fff">جارٍ تحميل الشاشة ...</h3>
</div>

<!-- أزرار رجوع/الرئيسية -->
<div class="top-actions">
  <a href="javascript:history.back()"><i class="fas fa-arrow-right"></i><span>رجوع</span></a>
  <a href="<?php echo site_url('dashboard'); ?>"><i class="fas fa-home"></i><span>الرئيسية</span></a>
</div>

<div class="main-container container-fluid">
  <div class="text-center">
    <h1 class="page-title">إضافة موظف جديد</h1>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card table-card">
        <div class="card-body">
          <?php echo validation_errors('<div class="alert alert-danger">','</div>'); ?>
          <?php echo form_open_multipart('users2/store_employee', ['id'=>'addEmployeeForm', 'novalidate'=>true]); ?>

          <!-- البيانات الأساسية -->
          <div class="section-title"><i class="fa-solid fa-id-card"></i> البيانات الأساسية</div>
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label required">الرقم الوظيفي</label>
              <input type="text" class="form-control" name="employee_code" id="employee_code" required placeholder="مثال: 10234" />
            </div>
            <div class="col-md-4">
              <label class="form-label required">الاسم كاملاً (عربي)</label>
              <input type="text" class="form-control" name="full_name_ar" id="full_name_ar" required placeholder="الاسم الثلاثي" />
            </div>
            <div class="col-md-2">
              <label class="form-label required">الجنسية</label>
              <input type="text" class="form-control" name="nationality" id="nationality" required placeholder="سعودي / مصري ..." />
            </div>
            <div class="col-md-3">
              <label class="form-label required">رقم الهوية/الإقامة</label>
              <input type="text" class="form-control" name="id_number" id="id_number" required placeholder="10 أرقام" />
            </div>

            <div class="col-md-3">
              <label class="form-label required">تاريخ الانضمام</label>
              <input type="date" class="form-control" name="join_date" id="join_date" required />
            </div>
            <div class="col-md-3">
              <label class="form-label">القسم</label>
              <input type="text" class="form-control" name="department" id="department" placeholder="مثال: الموارد البشرية" />
            </div>
            <div class="col-md-3">
              <label class="form-label">تاريخ الميلاد</label>
              <input type="date" class="form-control" name="birth_date" id="birth_date" />
            </div>
            <div class="col-md-3">
              <label class="form-label required">المسمى الوظيفي</label>
              <input type="text" class="form-control" name="job_title" id="job_title" placeholder="مثال: مشرف عمليات" required />
            </div>

            <div class="col-md-3">
              <label class="form-label">الجنس</label>
              <select class="form-select" name="gender" id="gender">
                <option value="">— اختر —</option>
                <option value="ذكر">ذكر</option>
                <option value="أنثى">أنثى</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">الحالة الاجتماعية</label>
              <select class="form-select" name="marital_status" id="marital_status">
                <option value="">— اختر —</option>
                <option value="أعزب">أعزب</option>
                <option value="متزوج">متزوج</option>
                <option value="مطلق">مطلق</option>
                <option value="أرمل">أرمل</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">الديانة</label>
              <select class="form-select" name="religion" id="religion">
                <option value="">— اختر —</option>
                <option value="الإسلام">الإسلام</option>
                <option value="المسيحية">المسيحية</option>
                <option value="أخرى">أخرى</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">تاريخ انتهاء الهوية</label>
              <input type="date" class="form-control" name="id_expiry" id="id_expiry" />
            </div>

            <div class="col-md-4">
              <label class="form-label">البريد الإلكتروني الشخصي</label>
              <input type="email" class="form-control" name="personal_email" id="personal_email" placeholder="example@gmail.com" />
            </div>
            <div class="col-md-4">
              <label class="form-label">إيميل مرسوم</label>
              <input type="email" class="form-control" name="company_email" id="company_email" placeholder="name@marsoom.sa" />
            </div>
            <div class="col-md-4">
              <label class="form-label">رقم الجوال</label>
              <input type="tel" class="form-control" name="mobile" id="mobile" placeholder="05XXXXXXXX" />
            </div>

            <div class="col-md-6">
              <label class="form-label">العنوان</label>
              <input type="text" class="form-control" name="address" id="address" placeholder="المدينة - الحي - الشارع" />
            </div>
            <div class="col-md-3">
              <label class="form-label">نوع الدوام</label>
              <select class="form-select" name="employment_type" id="employment_type">
                <option value="">— اختر —</option>
                <option value="دوام كامل">دوام كامل</option>
                <option value="دوام جزئي">دوام جزئي</option>
                <option value="مرن">مرن</option>
                <option value="عن بعد">عن بعد</option>
                <option value="مؤقت">مؤقت</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">الشركة</label>
              <select class="form-select" name="company" id="company">
                <option value="">— اختر —</option>
                <option value="مرسوم">مرسوم</option>
                <option value="مكتب الدكتور صالح الجربوع">مكتب الدكتور صالح الجربوع</option>
              </select>
            </div>

            <div class="col-md-3">
              <label class="form-label">الموقع</label>
              <input type="text" class="form-control" name="location" id="location" placeholder="الرياض / جدة ..." />
            </div>
            <div class="col-md-3">
              <label class="form-label">المدير المباشر</label>
              <input type="text" class="form-control" name="direct_manager" id="direct_manager" placeholder="اسم المدير" />
            </div>
          </div>

          <hr class="my-4" />

          <!-- بيانات الحساب البنكي -->
          <div class="section-title"><i class="fa-solid fa-building-columns"></i> بيانات الحساب البنكي</div>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label required">رقم الآيبان</label>
              <input type="text" class="form-control" name="iban" id="iban" required placeholder="SA•••• •••• •••• •••• ••••" maxlength="34" />
              <div class="hint">يُفضّل التنسيق بصيغة SA متبوعة بـ 22 رقمًا للمملكة.</div>
            </div>
            <div class="col-md-4">
              <label class="form-label">اسم البنك</label>
              <input type="text" class="form-control" name="bank_name" id="bank_name" placeholder="مثال: الراجحي" />
            </div>
          </div>

          <hr class="my-4" />

          <!-- الرواتب والبدلات -->
          <div class="section-title"><i class="fa-solid fa-money-bill-trend-up"></i> الرواتب والبدلات</div>
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label required">الراتب الأساسي</label>
              <input type="number" step="0.01" min="0" class="form-control salary-part" name="basic_salary" id="basic_salary" required placeholder="0.00" />
            </div>
            <div class="col-md-3">
              <label class="form-label">بدل سكن</label>
              <input type="number" step="0.01" min="0" class="form-control salary-part" name="housing_allowance" id="housing_allowance" placeholder="0.00" />
            </div>
            <div class="col-md-3">
              <label class="form-label">بدل مواصلات</label>
              <input type="number" step="0.01" min="0" class="form-control salary-part" name="transportation_allowance" id="transportation_allowance" placeholder="0.00" />
            </div>
            <div class="col-md-3">
              <label class="form-label">بدل اتصالات</label>
              <input type="number" step="0.01" min="0" class="form-control salary-part" name="communication_allowance" id="communication_allowance" placeholder="0.00" />
            </div>

            <div class="col-md-3">
              <label class="form-label">بدل طبيعة العمل</label>
              <input type="number" step="0.01" min="0" class="form-control salary-part" name="work_nature_allowance" id="work_nature_allowance" placeholder="0.00" />
            </div>
            <div class="col-md-3">
              <label class="form-label">بدل سماعة</label>
              <input type="number" step="0.01" min="0" class="form-control salary-part" name="headphone_allowance" id="headphone_allowance" placeholder="0.00" />
            </div>
            <div class="col-md-3">
              <label class="form-label">أخرى</label>
              <input type="number" step="0.01" min="0" class="form-control salary-part" name="other_allowance" id="other_allowance" placeholder="0.00" />
            </div>
            <div class="col-md-3">
              <label class="form-label">بدل المحروقات</label>
              <input type="number" step="0.01" min="0" class="form-control salary-part" name="fuel_allowance" id="fuel_allowance" placeholder="0.00" />
            </div>

            <div class="col-md-3">
              <label class="form-label">بدل مواصلات إضافي</label>
              <input type="number" step="0.01" min="0" class="form-control salary-part" name="extra_transport_allowance" id="extra_transport_allowance" placeholder="0.00" />
            </div>
            <div class="col-md-3">
              <label class="form-label">بدل الإشراف</label>
              <input type="number" step="0.01" min="0" class="form-control salary-part" name="supervision_allowance" id="supervision_allowance" placeholder="0.00" />
            </div>
            <div class="col-md-3">
              <label class="form-label">بدل إعاشة</label>
              <input type="number" step="0.01" min="0" class="form-control salary-part" name="subsistence_allowance" id="subsistence_allowance" placeholder="0.00" />
            </div>

            <div class="col-md-3">
              <div class="form-check form-switch mt-4">
                <input class="form-check-input" type="checkbox" role="switch" id="auto_total" checked />
                <label class="form-check-label" for="auto_total">احسب كامل الراتب تلقائيًا</label>
              </div>
            </div>
            <div class="col-md-3">
              <label class="form-label required">كامل الراتب</label>
              <input type="number" step="0.01" min="0" class="form-control" name="total_salary" id="total_salary" required placeholder="0.00" />
              <div class="hint">إن كان التحديث التلقائي مفعّلًا فسيتم جمع جميع الحقول أعلاه تلقائيًا.</div>
            </div>
          </div>

          <hr class="my-4" />

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> حفظ</button>
            <button type="reset" class="btn btn-secondary"><i class="fa-solid fa-arrow-rotate-right"></i> مسح</button>
          </div>

          <?php echo form_close(); ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- jQuery + Bootstrap -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // شاشة التحميل
  window.addEventListener('load', function(){
    const loading = document.getElementById('loading-screen');
    const main = document.querySelector('.main-container');
    loading.style.opacity='0';
    setTimeout(function(){ loading.style.display='none'; document.body.style.overflow='auto'; main.style.visibility='visible'; main.style.opacity='1'; }, 400);
  });

  // تنسيق IBAN + تحقق أولي
  const ibanInput = document.getElementById('iban');
  ibanInput.addEventListener('input', function(){
    let v = this.value.toUpperCase().replace(/\s+/g, '');
    v = v.replace(/(.{4})/g, '$1 ').trim();
    this.value = v;
  });
  function ibanIsRoughlyValid(raw){
    const v = raw.replace(/\s+/g,'').toUpperCase();
    if(!/^[A-Z]{2}[0-9A-Z]{13,34}$/.test(v)) return false;
    if(v.startsWith('SA') && v.length !== 24) return false; // SA + 22 رقم
    return true;
  }

  // جمع تلقائي لكامل الراتب
  const parts = document.querySelectorAll('.salary-part');
  const totalField = document.getElementById('total_salary');
  const autoToggle = document.getElementById('auto_total');
  function recalcTotal(){
    if(!autoToggle.checked) return;
    let sum = 0;
    parts.forEach(el => { sum += parseFloat(el.value || 0); });
    totalField.value = sum.toFixed(2);
  }
  parts.forEach(el => el.addEventListener('input', recalcTotal));
  autoToggle.addEventListener('change', function(){
    totalField.readOnly = this.checked;
    if(this.checked) recalcTotal();
  });
  totalField.readOnly = true; recalcTotal();

  // تحقق أساسي قبل الإرسال
  document.getElementById('addEmployeeForm').addEventListener('submit', function(e){
    // تحقق IBAN
    const rawIban = ibanInput.value || '';
    if(!ibanIsRoughlyValid(rawIban)){
      e.preventDefault();
      alert('رجاءً تحقّق من رقم الآيبان.');
      ibanInput.focus();
      return false;
    }
    // تحقق الهوية/الإقامة (طول تقريبي)
    const idNum = (document.getElementById('id_number').value || '').trim();
    if(idNum.length < 8){
      e.preventDefault();
      alert('رقم الهوية/الإقامة غير صحيح.');
      return false;
    }
    // تحقق الجوال السعودي (اختياري إن وُجد)
    const mobile = (document.getElementById('mobile').value || '').trim();
    if(mobile && !/^05\d{8}$/.test(mobile)){
      e.preventDefault();
      alert('رقم الجوال يجب أن يكون بصيغة سعودية صحيحة: 05XXXXXXXX');
      return false;
    }

    // إذا التحديث التلقائي مفعّل، احسب قبل الإرسال
    if(autoToggle.checked) recalcTotal();

    // تأكيد
    if(!confirm('هل تريد حفظ بيانات الموظف؟')){
      e.preventDefault();
      return false;
    }
  });
</script>
</body>
</html>