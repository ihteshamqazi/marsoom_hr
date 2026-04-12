<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>تصدير بيانات الموظفين (رفع CSV)</title>

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
    .page-title::after{content:'';position:absolute;width:160px;height:4px;background:linear-gradient(90deg,var(--marsom-blue),var(--marsom-orange));bottom:0;left:50%;transform:translateX(-50%);border-radius:2px}
    .table-card{background:rgba(255,255,255,.9);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.3);border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,.15);padding:25px}

    .top-actions{position:fixed;top:12px;right:12px;display:flex;gap:10px;z-index:5}
    .top-actions a, .top-actions button{background:rgba(255,255,255,.12);border:1px solid var(--glass-border);color:#fff;text-decoration:none;border-radius:10px;padding:8px 14px;display:inline-flex;align-items:center;gap:8px;transition:.25s}
    .top-actions a:hover, .top-actions button:hover{background:rgba(255,255,255,.2);color:var(--marsom-orange)}

    .hint{font-size:.9rem;color:#6c757d}
    .csv-badge{font-family:monospace;background:#001f3f;color:#fff;padding:.15rem .35rem;border-radius:.35rem}
    .preview-table th{background:#001f3f;color:#fff}
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
  <h3 style="color:#fff">جارٍ تجهيز شاشة التصدير ...</h3>
</div>

<!-- أزرار رجوع/الرئيسية -->
<div class="top-actions">
  <a href="javascript:history.back()"><i class="fas fa-arrow-right"></i><span>رجوع</span></a>
  <a href="<?php echo site_url('dashboard'); ?>"><i class="fas fa-home"></i><span>الرئيسية</span></a>
</div>

<div class="main-container container-fluid">
  <div class="text-center">
    <h1 class="page-title">تصدير بيانات الموظفين</h1>
  </div>

  <div class="row g-3">
    <div class="col-12">
      <div class="card table-card">
        <div class="card-body">
          <p class="mb-3">قم برفع ملف <span class="csv-badge">CSV</span> يحتوي على بيانات الموظفين وفق الأعمدة القياسية. يمكنك أيضًا تنزيل <strong>نموذج CSV</strong> جاهز بالترتيب الصحيح.</p>

          <div class="d-flex flex-wrap gap-2 mb-3">
            <a class="btn btn-success" href="<?php echo site_url('users2/download_employees_template'); ?>"><i class="fa fa-download"></i> تنزيل نموذج الرفع (CSV)</a>
            <a class="btn btn-outline-light" href="<?php echo site_url('users2/employees'); ?>"><i class="fa fa-list"></i> قائمة الموظفين</a>
          </div>

          <?php echo form_open_multipart('users2/employees_import', ['id'=>'importForm']); ?>

          <!-- وضع الرفع -->
          <div class="mb-3">
            <label class="form-label fw-bold">طريقة المعالجة</label>
            <div class="row g-2">
              <div class="col-md-6">
                <div class="form-check border rounded p-3 h-100">
                  <input class="form-check-input" type="radio" name="import_mode" id="mode_replace" value="replace" checked>
                  <label class="form-check-label" for="mode_replace">
                    <span class="fw-semibold">رفع مع حذف جميع البيانات الحالية</span>
                    <div class="hint">سيتم تفريغ جدول الموظفين أولًا ثم استيراد بيانات الملف.</div>
                  </label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-check border rounded p-3 h-100">
                  <input class="form-check-input" type="radio" name="import_mode" id="mode_append" value="append">
                  <label class="form-check-label" for="mode_append">
                    <span class="fw-semibold">رفع البيانات الجديدة فقط</span>
                    <div class="hint">يتم إدراج السجلات الجديدة فقط (حسب الرقم الوظيفي). يمكن لاحقًا إضافة وضع "تحديث إن وجد".</div>
                  </label>
                </div>
              </div>
            </div>
          </div>

          <!-- اختيار الملف -->
          <div class="mb-3">
            <label class="form-label fw-bold">ملف CSV</label>
            <input class="form-control" type="file" name="csv_file" id="csv_file" accept=".csv" required>
            <div class="hint mt-1">التنسيق: ترميز UTF-8، الفاصل (,) فاصلة. يُنصح باستخدام النموذج الرسمي.</div>
          </div>

          <!-- معاينة أولية -->
          <div class="mb-3">
            <label class="form-label fw-bold">معاينة سريعة (أول 10 أسطر)</label>
            <div class="table-responsive">
              <table class="table table-bordered preview-table" id="previewTable">
                <thead><tr id="previewHead"><th>—</th></tr></thead>
                <tbody id="previewBody"><tr><td class="text-muted">لا توجد معاينة بعد.</td></tr></tbody>
              </table>
            </div>
          </div>

          <!-- الأعمدة المتوقعة -->
          <div class="mb-3">
            <div class="accordion" id="colsAccordion">
              <div class="accordion-item">
                <h2 class="accordion-header" id="h1">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c1">
                    الأعمدة المطلوبة بالترتيب
                  </button>
                </h2>
                <div id="c1" class="accordion-collapse collapse" data-bs-parent="#colsAccordion">
                  <div class="accordion-body">
                    <div class="table-responsive">
                      <table class="table table-sm table-striped">
                        <thead><tr><th>#</th><th>اسم العمود</th></tr></thead>
                        <tbody id="colsList"></tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-upload"></i> رفع الملف</button>
            <button type="reset" class="btn btn-secondary"><i class="fa-solid fa-rotate"></i> مسح</button>
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

  // الأعمدة المتوقعة (مطابقة لشاشة الإضافة + حقول إضافية إن وجدت)
  const requiredCols = [
    'employee_code','full_name_ar','nationality','id_number','join_date','department','birth_date','job_title',
    'gender','marital_status','religion','id_expiry','personal_email','company_email','mobile','address',
    'employment_type','company','location','direct_manager',
    'iban','bank_name',
    'basic_salary','housing_allowance','transportation_allowance','communication_allowance','work_nature_allowance','headphone_allowance','other_allowance','fuel_allowance','extra_transport_allowance','supervision_allowance','subsistence_allowance','total_salary'
  ];

  // بناء جدول الأعمدة المتوقعة
  const colsList = document.getElementById('colsList');
  requiredCols.forEach((c,i)=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${i+1}</td><td><code>${c}</code></td>`;
    colsList.appendChild(tr);
  });

  // قراءة CSV ومعاينة سريعة (بسيطة بدون مكتبات خارجية)
  const fileInput = document.getElementById('csv_file');
  const headRow = document.getElementById('previewHead');
  const body = document.getElementById('previewBody');

  fileInput.addEventListener('change', function(){
    const file = this.files[0];
    if(!file) return;
    const reader = new FileReader();
    reader.onload = function(e){
      const text = e.target.result; 
      const lines = text.replace(/\r\n/g,'\n').replace(/\r/g,'\n').split('\n').filter(x=>x.trim()!=='');
      const rows = lines.slice(0, 11).map(parseCSVLine);
      renderPreview(rows);
      validateHeaders(rows[0]||[]);
    };
    reader.readAsText(file, 'UTF-8');
  });

  function parseCSVLine(line){
    // يدعم القيم بين "" بما فيها الفواصل، تبسيط كافٍ للمعاينة
    const result = [];
    let cur = '', insideQuotes = false;
    for(let i=0;i<line.length;i++){
      const ch = line[i];
      if(ch === '"'){
        if(insideQuotes && line[i+1] === '"'){ cur += '"'; i++; }
        else { insideQuotes = !insideQuotes; }
      } else if(ch === ',' && !insideQuotes){
        result.push(cur); cur='';
      } else {
        cur += ch;
      }
    }
    result.push(cur);
    return result;
  }

  function renderPreview(rows){
    headRow.innerHTML='';
    body.innerHTML='';
    if(rows.length===0){
      headRow.innerHTML='<th>—</th>'; body.innerHTML='<tr><td class="text-muted">لا توجد معاينة.</td></tr>'; return;
    }
    // Header
    rows[0].forEach(h=>{ const th = document.createElement('th'); th.textContent = h; headRow.appendChild(th); });
    // Body
    for(let r=1;r<rows.length;r++){
      const tr = document.createElement('tr');
      rows[r].forEach(cell=>{ const td = document.createElement('td'); td.textContent = cell; tr.appendChild(td); });
      body.appendChild(tr);
    }
  }

  function validateHeaders(header){
    if(header.length===0) return;
    const missing = requiredCols.filter(c=>!header.includes(c));
    if(missing.length){
      alert('تنبيه: الأعمدة التالية مفقودة في الملف\n\n'+missing.join(', '));
    }
  }
</script>
</body>
</html>