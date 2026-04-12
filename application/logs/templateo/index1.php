<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>   ادارة المهام</title>

  <!-- Bootstrap 5 RTL -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <!-- Google Font: El Messiri -->
  <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;600;700&display=swap" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<!-- أضِف هذا داخل قسم الـ CSS لديك (ضمن @media print الموجودة أو كقسم جديد) -->

<!-- أضِف/حدّث هذا المقطع داخل ملف الـ CSS لديك -->

<!-- أضف/حدّث هذا الـ CSS -->
<style>
  /* افتراضيًا: أخفِ نسخة الطباعة */
  .only-print { display: none; }

  /* على الشاشة: أظهر نسخة الشاشة وأخفِ نسخة الطباعة */
  @media screen {
    .only-screen { display: inline-block; }
    .only-print  { display: none !important; }
  }

  /* عند الطباعة: أخفِ نسخة الشاشة وأظهر نسخة الطباعة */
  @media print {
    .only-screen { display: none !important; }
    .only-print  { display: inline-block !important; }

    /* تحسين طباعة الألوان إن لزم */
    .logo { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
  }
</style>


<style>
  @media print{
    /* جعل نص "ملخص حسب التصنيف" والإيقونة بالأسود عند الطباعة */
    .glass-head,
    .glass-head .badge-soft,
    .glass-head .badge-soft *{
      color: #000 !important;
    }
    /* (اختياري) إزالة خلفية الشارة أثناء الطباعة لزيادة الوضوح */
    .glass-head .badge-soft{
      background: transparent !important;
      border-color: #000 !important;
    }
  }
</style>



<style>
  @media print{
    th.no-print,
    td.no-print{ display:none !important; }
  }
</style>

  <style>
    :root{
      --glass-bg: rgba(255,255,255,0.12);
      --glass-stroke: rgba(255,255,255,0.35);
      --primary: #6d28d9;
      --accent:  #F29840;
      --ok:#10b981; --warn:#f59e0b; --info:#3b82f6; --muted:#64748b; --danger:#ef4444;
    }
    body{
      font-family: "El Messiri", system-ui, -apple-system, "Segoe UI", sans-serif;
      background: radial-gradient(1200px 600px at 10% -10%, #0E1F3B 0%, #0d1530 60%, #0a1026 100%);
      min-height:100vh; color:#f8fafc;
    }
    .page-header{text-align:center; margin:28px 0 16px;}
    .page-header h2{font-weight:800;margin-bottom:6px;}
    .page-sub{color:#cbd5e1;margin:0}

    .glass-card{
      background: var(--glass-bg);
      border: 1px solid var(--glass-stroke);
      border-radius: 20px;
      backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px);
      box-shadow: 0 20px 50px rgba(0,0,0,.25);
      overflow: hidden;
    }
    .glass-head{padding: 14px 16px; border-bottom: 1px solid rgba(255,255,255,.18);}
    .glass-body{padding: 16px;}
    .badge-soft{background: rgba(242,152,64,.12); color:#ffd6a8; border:1px solid rgba(242,152,64,.35); border-radius:999px; padding:.35rem .7rem; font-weight:700}

    /* الفلاتر */
    .filters .form-control, .filters .form-select{
      background: rgba(255,255,255,.96);
      color:#111827;
      border-radius: 14px;
      border:1px solid rgba(255,255,255,.45);
    }
    .filters .form-select option{ color:#111827; }

    /* قائمة الحالات كـ List + تحديد الكل */
    .status-list{
      background: rgba(255,255,255,.96);
      color:#111827;
      border:1px solid rgba(255,255,255,.45);
      border-radius:14px;
      padding:12px;
      max-height: 240px;
      overflow:auto;
    }
    .status-list .form-check-label{ color:#111827; font-weight:700; }

    /* صناديق الداشبورد */
    .stat-card{
      border:1px solid rgba(255,255,255,.22);
      background: rgba(255,255,255,.06);
      border-radius:16px;
      padding:14px; height:100%;
    }
    .stat-title{font-weight:800; font-size:16px; margin-bottom:8px;}
    .stat-line{display:flex; align-items:center; justify-content:space-between; font-weight:700; margin:2px 0}
    .progress{height:8px; background:rgba(255,255,255,.2)}
    .progress-bar{background: linear-gradient(90deg, var(--ok), #22d3ee)}

    /* جدول المهام */
    .table thead th{
      color:#0f172a; background:rgba(255,255,255,.88);
      border-bottom:1px solid rgba(0,0,0,.08);
    }
    .table{
      background:rgba(255,255,255,.9); color:#111827;
      border-radius:14px; overflow:hidden;
    }
    .status-badge{display:inline-block; padding:.25rem .6rem; border-radius:999px; font-weight:800; font-size:.85rem}
    .s-done{background:#d1fae5;color:#065f46}
    .s-inprog{background:#dbeafe;color:#1e40af}
    .s-future{background:#fef3c7;color:#92400e}
    .s-study{background:#ede9fe;color:#5b21b6}
    .s-reject{background:#fee2e2;color:#991b1b}

    @media print {
      body{background:#fff; color:#000}
      .filters, .actions-bar, .page-sub { display:none !important; }
      .glass-card, .stat-card, .table { box-shadow:none; border-color:#000 }
    }
  </style>
</head>
<body>
  <div class="container" style="max-width: 1200px;">
    <div class="page-header">
       
     <!-- ضع هذين الوسمين بدل الوسم الواحد -->
<img class="logo only-screen" src="<?php echo base_url();?>/assets/imeges/m2.PNG" width="200" alt="Marsom Logo">
<img class="logo only-print"  src="<?php echo base_url('assets/imeges/m1.PNG'); ?>" width="200" alt="Marsom Logo (Print)">

</br>
</br>
 
     

      <h2>المهام البرمجية </h2>
      <h6>   ادارة تقنية المعلومات - وحدة التطبيقات والبرامج </h6>
       
    </div>

    <!-- فلاتر -->
    <div class="glass-card filters mb-3">
      <div class="glass-head"><span class="badge-soft"><i class="bi bi-funnel"></i> فلاتر التقرير</span></div>
      <div class="glass-body">
        <form method="get" class="row g-3 align-items-end">
          <div class="col-md-4">
            <label class="form-label">التصنيف</label>
            <select name="category" class="form-select">
              <option value="">الكل</option>
              <?php foreach ($categories as $c): ?>
                <option value="<?= htmlspecialchars($c) ?>" <?= (!empty($filters['category']) && $filters['category']==$c)?'selected':''; ?>>
                  <?= htmlspecialchars($c) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label">حالة الطلب</label>
            <div class="status-list">
              <!-- تحديد الكل -->
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" id="status_all">
                <label class="form-check-label" for="status_all"><strong>تحديد الكل</strong></label>
              </div>
              <!-- الحالات الفردية -->
              <?php foreach ($statuses as $s):
                $checked = (!empty($filters['statuses']) && is_array($filters['statuses']))
                           ? in_array($s, $filters['statuses'])
                           : false; ?>
                <div class="form-check">
                  <input
                    class="form-check-input status-item"
                    type="checkbox"
                    name="statuses[]"
                    value="<?= $s; ?>"
                    id="st-<?= md5($s); ?>"
                    <?= $checked ? 'checked' : ''; ?>>
                  <label class="form-check-label" for="st-<?= md5($s); ?>"><?= $s; ?></label>
                </div>
              <?php endforeach; ?>
            </div>
            <div class="form-text text-light">يمكن اختيار أكثر من حالة أو تحديد الكل</div>
          </div>

          <div class="col-md-2">
            <label class="form-label">من تاريخ الإنجاز</label>
            <input type="date" name="date_from" class="form-control"
                   value="<?= htmlspecialchars(str_replace('/','-',$filters['date_from']??'')) ?>">
          </div>
          <div class="col-md-2">
            <label class="form-label">إلى تاريخ الإنجاز</label>
            <input type="date" name="date_to" class="form-control"
                   value="<?= htmlspecialchars(str_replace('/','-',$filters['date_to']??'')) ?>">
          </div>

          <div class="col-md-12 d-flex gap-2 actions-bar">
            <button class="btn btn-primary"><i class="bi bi-search"></i> بحث</button>
            <a class="btn btn-outline-light" href="<?= site_url('users1/index11'); ?>">
              <i class="bi bi-arrow-counterclockwise"></i> تفريغ
            </a>
            <!-- تصدير CSV (Excel) بنفس الفلاتر -->
            <a class="btn btn-success" href="<?= site_url('users1/export').'?'.http_build_query($filters); ?>">
              <i class="bi bi-file-earmark-spreadsheet"></i> تصدير Excel
            </a>
            <button type="button" class="btn btn-secondary" onclick="window.print()">
              <i class="bi bi-printer"></i> طباعة
            </button>
            <!-- ضع هذا الزر داخل شريط الإجراءات (actions-bar) لفتح شاشة users1/create كبوب-أب متوسط -->
<button type="button"
        class="btn btn-warning"
        onclick="openCenteredPopup('<?= site_url('users1/create'); ?>', 900, 650);">
  <i class="bi bi-plus-circle"></i> إضافة مهمة جديدة
</button>

<!-- أضِف هذا السكربت مرة واحدة أسفل الصفحة قبل </body> -->
<script>
  function openCenteredPopup(url, w, h) {
    // قياسات الشاشة/النافذة الحالية
    const dualLeft = window.screenLeft !== undefined ? window.screenLeft : screen.left;
    const dualTop  = window.screenTop  !== undefined ? window.screenTop  : screen.top;

    const winW = window.innerWidth  || document.documentElement.clientWidth  || screen.width;
    const winH = window.innerHeight || document.documentElement.clientHeight || screen.height;

    const left = dualLeft + Math.max(0, (winW - w) / 2);
    const top  = dualTop  + Math.max(0, (winH - h) / 2);

    const features = `scrollbars=yes,resizable=yes,width=${w},height=${h},left=${left},top=${top}`;
    const popup = window.open(url, 'createTaskPopup', features);
    if (popup && popup.focus) popup.focus();
    return false;
  }
</script>

          </div>
        </form>
      </div>
    </div>

    <!-- الداشبورد -->
    <div class="glass-card mb-3">
      <div class="glass-head"><span class="badge-soft"><i class="bi bi-grid-3x3-gap"></i> ملخص حسب التصنيف</span></div>
      <div class="glass-body">
        <?php if (empty($stats)): ?>
          <div class="text-center text-light">لا توجد نتائج مطابقة للفلاتر الحالية</div>
        <?php else: ?>
          <div class="row g-3">
            <?php foreach ($stats as $row): ?>
              <div class="col-md-4 col-lg-3">
                <div class="stat-card">
                  <div class="stat-title"><?= htmlspecialchars($row['category'] ?: 'غير محدد'); ?></div>

                  <div class="stat-line"><span>تم الإنجاز</span><span><?= (int)$row['done_count']; ?></span></div>
                  <div class="stat-line"><span>جاري التنفيذ</span><span><?= (int)$row['inprog_count']; ?></span></div>
                  <div class="stat-line"><span>طلب مستقبلي</span><span><?= (int)$row['future_count']; ?></span></div>
                  <div class="stat-line"><span>جاري الدراسة</span><span><?= (int)$row['study_count']; ?></span></div>
                  <div class="stat-line"><span>مرفوض</span><span><?= (int)$row['reject_count']; ?></span></div>

                  <div class="mt-2 small text-light-50">نسبة الإنجاز: <?= $row['done_percent']; ?>%</div>
                  <div class="progress mt-1" role="progressbar" aria-label="نسبة الإنجاز">
                    <div class="progress-bar" style="width: <?= $row['done_percent']; ?>%"></div>
                  </div>

                  <div class="mt-2 text-light-50 small">الإجمالي: <?= (int)$row['total_count']; ?></div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- جدول التفاصيل -->
    <div class="glass-card mb-4">
      <div class="glass-head"><span class="badge-soft"><i class="bi bi-table"></i> تفاصيل المهام</span></div>
      <div class="glass-body p-0">
        <div class="table-responsive">
          <table class="table table-striped table-hover mb-0">
            <!-- استبدل رأس الجدول بهذا (أضف عمود "الخيارات") -->
<thead>
  <tr>
    <th style="width:60px">#</th>
    <th>التصنيف</th>
    <th>جهة الطلب</th>
    <th>المنفذ</th>
    <th>تاريخ الإنجاز</th>
    <th>الحالة</th>
    <th style="min-width:240px">التفاصيل</th>
   
<th class="no-print" style="width:170px">الخيارات</th>

  </tr>
</thead>

            <!-- استبدل جسم الجدول بالكامل بهذا -->
<tbody>
  <?php if (empty($tasks)): ?>
    <tr><td colspan="8" class="text-center">لا توجد مهام مطابقة</td></tr>
  <?php else: $i=1; foreach ($tasks as $t): ?>
    <tr>
      <td><?= $i++; ?></td>
      <td><?= htmlspecialchars($t['category']); ?></td>
      <td><?= htmlspecialchars($t['requester']); ?></td>
      <td><?= htmlspecialchars($t['assignee']); ?></td>
      <td><?= htmlspecialchars($t['due_date']); ?></td>
      <td>
        <?php
          $badgeClass = 's-inprog';
          switch ($t['status']) {
            case 'تم الانجاز':       $badgeClass='s-done';   break;
            case 'جاري التنفيذ':     $badgeClass='s-inprog'; break;
            case 'طلب مستقبلي':     $badgeClass='s-future'; break;
            case 'جاري دراسة الطلب': $badgeClass='s-study';  break;
            case 'طلب مرفوض':       $badgeClass='s-reject'; break;
          }
        ?>
        <span class="status-badge <?= $badgeClass; ?>"><?= htmlspecialchars($t['status']); ?></span>
      </td>
      <td><?= nl2br(htmlspecialchars($t['details'])); ?></td>

      <!-- عمود الخيارات -->
     <td class="no-print">
  <button class="btn btn-sm btn-outline-primary w-100"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#opts-<?= (int)$t['id']; ?>"
          aria-expanded="false"
          aria-controls="opts-<?= (int)$t['id']; ?>">
    خيارات
  </button>

        <div class="collapse mt-2" id="opts-<?= (int)$t['id']; ?>">
          <div class="d-grid gap-2">
            <!-- تعديل الموظف المسؤول -->
            <button type="button"
                    class="btn btn-light btn-sm text-start"
                    data-bs-toggle="modal"
                    data-bs-target="#assigneeModal"
                    data-id="<?= (int)$t['id']; ?>"
                    data-assignee="<?= htmlspecialchars($t['assignee']); ?>">
              <i class="bi bi-person-gear"></i>
              تعديل الموظف المسؤول (حقل <strong>المنفذ</strong>)
            </button>

            <!-- تعديل تاريخ الإنجاز -->
            <button type="button"
                    class="btn btn-light btn-sm text-start"
                    data-bs-toggle="modal"
                    data-bs-target="#dateModal"
                    data-id="<?= (int)$t['id']; ?>"
                    data-date="<?= htmlspecialchars($t['due_date']); ?>">
              <i class="bi bi-calendar-event"></i>
              تعديل تاريخ الإنجاز (حقل <strong>تاريخ الإنجاز</strong>)
            </button>

            <!-- تعديل حالة الطلب -->
            <button type="button"
                    class="btn btn-light btn-sm text-start"
                    data-bs-toggle="modal"
                    data-bs-target="#statusModal"
                    data-id="<?= (int)$t['id']; ?>"
                    data-status="<?= htmlspecialchars($t['status']); ?>">
              <i class="bi bi-sliders"></i>
              تعديل حالة الطلب (حقل <strong>الحالة</strong>)
            </button>
          </div>
        </div>
      </td>
    </tr>
  <?php endforeach; endif; ?>
</tbody>

          </table>
        </div>
      </div>
    </div>

    <p class="text-center" style="color:#000000">© <?= date('Y'); ?>  ادارة تقنية المعلومات - وحدة التطبيقات والبرمجة  </p>
  </div>

  <!-- Modal: تحديث حالة المهمة -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="background: rgba(255,255,255,.96); border-radius:16px;">
      <div class="modal-header">
        <h6 class="modal-title">تعديل حالة الطلب</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
      </div>
      <form method="post" action="<?= site_url('Users1/update_status'); ?>">
        <div class="modal-body">
          <input type="hidden" name="id" value="">
          <input type="hidden" name="redirect" value="">
          <div class="mb-3">
            <label class="form-label">اختر الحالة الجديدة</label>
            <select name="status" class="form-select" required>
              <?php foreach ($statuses as $s): ?>
                <option value="<?= $s; ?>"><?= $s; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="text-muted small">
            عند الحفظ سيتم تسجيل: التاريخ (Y/m/d) والوقت (h:i:s) واسم المستخدم والاسم من الجلسة.
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">إلغاء</button>
          <button class="btn btn-primary" type="submit">حفظ</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal: تحديث تاريخ الإنجاز -->
<div class="modal fade" id="dateModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="background: rgba(255,255,255,.96); border-radius:16px;">
      <div class="modal-header">
        <h6 class="modal-title">تعديل تاريخ الإنجاز</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
      </div>
      <form method="post" action="<?= site_url('users1/update_due_date'); ?>">
        <div class="modal-body">
          <input type="hidden" name="id" value="">
          <input type="hidden" name="redirect" value="">
          <div class="mb-3">
            <label class="form-label">اختر التاريخ الجديد</label>
            <input type="date" name="due_date_picker" class="form-control" required>
          </div>
          <div class="text-muted small">سيتم حفظ التاريخ بصيغة YYYY/MM/DD وتسجيل بيانات المستخدم والوقت.</div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">إلغاء</button>
          <button class="btn btn-primary" type="submit">حفظ</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal: تحديث الموظف المسؤول -->
<div class="modal fade" id="assigneeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="background: rgba(255,255,255,.96); border-radius:16px;">
      <div class="modal-header">
        <h6 class="modal-title">تعديل الموظف المسؤول</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
      </div>
      <form method="post" action="<?= site_url('users1/update_assignee'); ?>">
        <div class="modal-body">
          <input type="hidden" name="id" value="">
          <input type="hidden" name="redirect" value="">
           <div class="mb-3">
  <label class="form-label">اسم الموظف</label>

  <!-- الحقل الذي يقرأه الكنترولر + يُعبّئه سكربت المودال الحالي -->
  <input type="hidden" name="assignee" id="assignee_input">

  <!-- قائمة اختيار الموظف -->
  <select id="assignee_select" class="form-select" required>
    <option value="" disabled selected>اختر الموظف</option>
    <option value="صالح السفياني">صالح السفياني</option>
    <option value="صهيب خطيب">صهيب خطيب</option>
  </select>
</div>

<script>
  // مزامنة القائمة مع الحقل المخفي + التهيئة عند فتح مودال assigneeModal
  (function(){
    const modalEl = document.getElementById('assigneeModal');
    if (!modalEl) return;

    // عند فتح المودال: اضبط select بحسب قيمة الحقل المخفي
    modalEl.addEventListener('show.bs.modal', function () {
      const sel = modalEl.querySelector('#assignee_select');
      const hid = modalEl.querySelector('#assignee_input');
      Array.from(sel.options).forEach(o => { o.selected = (o.value === hid.value); });
    });

    // عند تغيير الاختيار: انسخ القيمة إلى الحقل المخفي (الذي يرسَل للكنترولر)
    modalEl.addEventListener('change', function(e){
      if (e.target && e.target.id === 'assignee_select') {
        modalEl.querySelector('#assignee_input').value = e.target.value || '';
      }
    });
  })();
</script>

          <div class="text-muted small">سيتم تسجيل بيانات المستخدم والوقت مع هذا التعديل.</div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">إلغاء</button>
          <button class="btn btn-primary" type="submit">حفظ</button>
        </div>
      </form>
    </div>
  </div>
</div>





<!-- إن لم يكن مضافًا مسبقًا -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // تعبئة المودال بالبيانات من زر "تعديل"
  (function(){
    const modalEl = document.getElementById('statusModal');
    if (!modalEl) return;

    modalEl.addEventListener('show.bs.modal', function (event) {
      const btn = event.relatedTarget;    
      const id = btn?.getAttribute('data-id');
      const current = btn?.getAttribute('data-status') || '';

      const form = modalEl.querySelector('form');
      form.querySelector('input[name=id]').value = id || '';
      form.querySelector('input[name=redirect]').value = window.location.href;

      const select = form.querySelector('select[name=status]');
      Array.from(select.options).forEach(o => { o.selected = (o.value === current); });
    });
  })();
</script>


  <script>
    (function(){
      const allToggle = document.getElementById('status_all');
      const items = Array.from(document.querySelectorAll('.status-item'));

      function refreshAllToggle(){
        const allChecked = items.length && items.every(ch => ch.checked);
        allToggle.checked = allChecked;
        allToggle.indeterminate = !allChecked && items.some(ch => ch.checked);
      }

      if (allToggle){
        allToggle.addEventListener('change', () => {
          items.forEach(ch => ch.checked = allToggle.checked);
        });
      }
      items.forEach(ch => ch.addEventListener('change', refreshAllToggle));
      refreshAllToggle();
    })();
  </script>

  <script>
  // تعبئة مودال "تعديل التاريخ"
  (function(){
    const modalEl = document.getElementById('dateModal');
    if (!modalEl) return;
    modalEl.addEventListener('show.bs.modal', function (event) {
      const btn = event.relatedTarget;
      const id  = btn?.getAttribute('data-id') || '';
      const d   = btn?.getAttribute('data-date') || ''; // صيغة المخزن: YYYY/MM/DD

      const form = modalEl.querySelector('form');
      form.querySelector('input[name=id]').value = id;
      form.querySelector('input[name=redirect]').value = window.location.href;

      // حوّل YYYY/MM/DD إلى yyyy-mm-dd لملء input[type=date]
      let pickerVal = '';
      if (d && d.indexOf('/') > -1) {
        const p = d.split('/'); // [YYYY, MM, DD]
        if (p.length === 3) pickerVal = `${p[0]}-${p[1]}-${p[2]}`;
      }
      const picker = form.querySelector('input[name=due_date_picker]');
      picker.value = pickerVal;
    });
  })();

  // تعبئة مودال "تعديل الموظف"
  (function(){
    const modalEl = document.getElementById('assigneeModal');
    if (!modalEl) return;
    modalEl.addEventListener('show.bs.modal', function (event) {
      const btn = event.relatedTarget;
      const id  = btn?.getAttribute('data-id') || '';
      const a   = btn?.getAttribute('data-assignee') || '';

      const form = modalEl.querySelector('form');
      form.querySelector('input[name=id]').value = id;
      form.querySelector('input[name=redirect]').value = window.location.href;
      form.querySelector('input[name=assignee]').value = a;
    });
  })();
</script>



</body>
</html>
