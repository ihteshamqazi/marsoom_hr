<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>إضافة مهمة جديدة - إدارة المهام</title>

  <!-- Bootstrap 5 RTL -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <!-- Google Font: El Messiri -->
  <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;600;700&display=swap" rel="stylesheet">
  <!-- Bootstrap Icons (اختياري) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    :root{
      --glass-bg: rgba(255,255,255,0.12);
      --glass-stroke: rgba(255,255,255,0.35);
      --primary: #6d28d9; /* بنفسجي أنيق */
      --accent:  #F29840;
    }
    *{box-sizing:border-box}
    body{
      font-family: "El Messiri", system-ui, -apple-system, "Segoe UI", sans-serif;
      background: radial-gradient(1200px 600px at 10% -10%, #0E1F3B 0%, #0d1530 60%, #0a1026 100%);
      min-height: 100vh; color:#f8fafc;
    }
    .page-header{text-align:center; margin:34px 0 18px;}
    .page-header h2{font-weight:800; letter-spacing:.5px; margin-bottom:6px;}
    .page-sub{color:#cbd5e1; margin:0}

    .glass-card{
      background: var(--glass-bg);
      border: 1px solid var(--glass-stroke);
      border-radius: 20px;
      backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px);
      box-shadow: 0 20px 50px rgba(0,0,0,.25);
      overflow: hidden;
    }
    .glass-head{
      padding: 16px 18px;
      background: linear-gradient(90deg, rgba(255,255,255,.08), rgba(255,255,255,.02));
      border-bottom: 1px solid rgba(255,255,255,.18);
    }
    .glass-body{ padding: 18px; }
    .form-section-title{ font-weight:800; color:#fff; margin:8px 0 12px; }
    .form-label{ color:#e5e7eb; font-weight:700;}
    .required::after{content:" *"; color:#ff5757;}

    /* الحقول الزجاجية */
    .form-control, textarea.form-control{
      background: rgba(255,255,255,.08); color:#fff;
      border:1px solid rgba(255,255,255,.18); border-radius:14px;
    }
    .form-control::placeholder{color:#cbd5e1}
    .form-control:focus, textarea.form-control:focus{
      background: rgba(255,255,255,.12);
      border-color: rgba(255,255,255,.45);
      box-shadow: 0 0 0 .25rem rgba(109,40,217,.25);
      color:#fff;
    }

    /* القوائم: نص داكن وخلفية فاتحة ليتضح عند الاستعراض */
    .form-select,
    .form-select:focus{
      color:#111827; background:#ffffff; /* واضح */
      border:1px solid rgba(255,255,255,.45); border-radius:14px;
      box-shadow: 0 0 0 .15rem rgba(109,40,217,.18);
    }
    .form-select option, .form-select optgroup{
      color:#111827; background:#ffffff;
    }

    .btn-primary{
      background: linear-gradient(90deg, var(--primary), #8b5cf6);
      border: none; font-weight:800; border-radius:14px;
      box-shadow: 0 12px 28px rgba(109,40,217,.35);
    }
    .btn-outline-light{
      border-radius:14px; border-color: rgba(255,255,255,.45); color:#fff;
    }
    .badge-soft{
      background: rgba(242,152,64,.12);
      color: #ffd6a8;
      border: 1px solid rgba(242,152,64,.35);
      border-radius: 999px;
      padding: .35rem .7rem; font-weight:700;
    }
    .alert{ border-radius:14px; border:1px solid rgba(255,255,255,.25); }
  </style>
</head>
<body>
  <div class="container" style="max-width: 980px;">
    <div class="page-header">
      <h2>إضافة مهمة جديدة</h2>
      <p class="page-sub">    ادارة تقنية المعلومات - وحدة التطبيقات والبرمجة</p>
    </div>

    <?php if ($this->session->flashdata('success')): ?>
      <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('error')): ?>
      <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
    <?php endif; ?>

    <div class="glass-card mb-4">
      <div class="glass-head d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
          <span class="badge-soft"><i class="bi bi-ui-checks-grid"></i> إدارة المهام</span>
        </div>
        <small class="text-light-50">        المهام البرمجية    </small>
      </div>

      <div class="glass-body">
        <form method="post" action="<?= site_url('users1/store'); ?>" novalidate>
          <div class="row g-3">
            <!-- التصنيف -->
            <div class="col-md-6">
              <label class="form-label required">التصنيف</label>
              <select name="category" id="category" class="form-select" required>
                <option value="" disabled selected>اختر التصنيف</option>
                <?php foreach ($categories as $val => $label): ?>
                  <?php if (is_string($val) && $val === '__other__'): ?>
                    <option value="__other__">أخرى</option>
                  <?php else: ?>
                    <option value="<?= is_string($val) ? $val : $label; ?>"><?= is_string($val) ? $label : $label; ?></option>
                  <?php endif; ?>
                <?php endforeach; ?>
              </select>
              <input class="form-control mt-2 d-none" type="text" name="category_other" id="category_other" placeholder="اكتب تصنيفًا آخر">
            </div>

            <!-- جهة الطلب -->
            <div class="col-md-6">
              <label class="form-label required">جهة الطلب</label>
              <select name="requester" id="requester" class="form-select" required>
                <option value="" disabled selected>اختر جهة الطلب</option>
                <?php foreach ($requesters as $val => $label): ?>
                  <?php if (is_string($val) && $val === '__other__'): ?>
                    <option value="__other__">أخرى</option>
                  <?php else: ?>
                    <option value="<?= is_string($val) ? $val : $label; ?>"><?= is_string($val) ? $label : $label; ?></option>
                  <?php endif; ?>
                <?php endforeach; ?>
              </select>
              <input class="form-control mt-2 d-none" type="text" name="requester_other" id="requester_other" placeholder="اكتب جهة طلب أخرى">
            </div>

            <!-- تفاصيل المهمة -->
            <div class="col-12">
              <label class="form-label required">تفاصيل المهمة</label>
              <textarea name="details" rows="4" class="form-control" placeholder="اكتب تفاصيل واضحة للمهمة" required></textarea>
            </div>

            <!-- الموظف المنفذ -->
            <div class="col-md-6">
              <label class="form-label required">الموظف المنفذ</label>
              <select name="assignee" class="form-select" required>
                <option value="" disabled selected>اختر الموظف</option>
                <?php foreach ($assignees as $x): ?>
                  <option value="<?= $x; ?>"><?= $x; ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- تاريخ الانتهاء (اختيار من التقويم) -->
            <div class="col-md-3">
              <label class="form-label required">تاريخ الانتهاء </label>
              <!-- التقويم -->
              <input type="date" class="form-control" id="due_date_picker" required>
              <!-- القيمة المرسلة بصيغة YYYY/MM/DD -->
              <input type="hidden" name="due_date" id="due_date_value">
              
            </div>

            <!-- حالة الطلب -->
            <div class="col-md-3">
              <label class="form-label required">حالة الطلب</label>
              <select name="status" class="form-select" required>
                <option value="" disabled selected>اختر الحالة</option>
                <?php foreach ($statuses as $s): ?>
                  <option value="<?= $s; ?>"><?= $s; ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-12 text-center pt-2">
              <button class="btn btn-primary px-4"><i class="bi bi-check2-circle"></i> حفظ المهمة</button>
              <button class="btn btn-outline-light px-3" type="reset"><i class="bi bi-arrow-counterclockwise"></i> تفريغ</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <p class="text-center" style="color:#cbd5e1">© <?= date('Y'); ?>    ادارة تقنية المعلومات - وحدة التطبيقات والبرمجة</p>
  </div>

  <script>
    // إظهار/إخفاء حقل "أخرى" للتصنيف
    const cat = document.getElementById('category');
    const catOther = document.getElementById('category_other');
    cat.addEventListener('change', function() {
      if (this.value === '__other__') { catOther.classList.remove('d-none'); catOther.required = true; }
      else { catOther.classList.add('d-none'); catOther.required = false; catOther.value=''; }
    });

    // إظهار/إخفاء حقل "أخرى" لجهة الطلب
    const req = document.getElementById('requester');
    const reqOther = document.getElementById('requester_other');
    req.addEventListener('change', function() {
      if (this.value === '__other__') { reqOther.classList.remove('d-none'); reqOther.required = true; }
      else { reqOther.classList.add('d-none'); reqOther.required = false; reqOther.value=''; }
    });

    // تحويل قيمة input[type=date] (yyyy-mm-dd) إلى الصيغة المطلوبة yyyy/mm/dd قبل الإرسال
    (function(){
      const picker = document.getElementById('due_date_picker');
      const hidden = document.getElementById('due_date_value');
      const form   = document.querySelector('form');

      function syncDate(){
        if (!picker.value) { hidden.value = ''; return; }
        const parts = picker.value.split('-'); // [yyyy, mm, dd]
        hidden.value = (parts.length === 3) ? (parts[0] + '/' + parts[1] + '/' + parts[2]) : '';
      }

      picker.addEventListener('change', syncDate);
      form.addEventListener('submit', function(){
        syncDate();
        if (!hidden.value) { picker.reportValidity(); }
      });
    })();
  </script>
</body>
</html>
