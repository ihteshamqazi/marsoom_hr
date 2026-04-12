<?php
// متغيرات قادمة من الكنترولر:
// $year,$month,$daysMap,$eventsByDay,$violationsByDay,$prevY,$prevM,$nextY,$nextM,$name,$username,$csrf_token_name,$csrf_hash
$daysInMonth  = (int)date('t', strtotime(sprintf('%04d-%02d-01', $year, $month)));
$firstWeekday = (int)date('w', strtotime(sprintf('%04d-%02d-01', $year, $month))); // 0=Sun..6=Sat

$monthNameAr = [1=>'يناير',2=>'فبراير',3=>'مارس',4=>'أبريل',5=>'مايو',6=>'يونيو',7=>'يوليو',8=>'أغسطس',9=>'سبتمبر',10=>'أكتوبر',11=>'نوفمبر',12=>'ديسمبر'];
$weekDaysAr  = ['الأحد','الإثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت'];

/* خريطة ويكند (جمعة/سبت) */
$WEEKEND_MAP = [];
for ($d=1; $d <= $daysInMonth; $d++){
  $date = sprintf('%04d-%02d-%02d',$year,$month,$d);
  $dow  = (int)date('w', strtotime($date));
  $WEEKEND_MAP[$date] = ($dow === 5 || $dow === 6);
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>الحضور</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet" crossorigin="anonymous">
<link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer"/>

<style>
:root{
  --marsom-blue:#001f3f; --marsom-orange:#FF8C00; --text-light:#fff;
  --glass-bg:rgba(255,255,255,.08); --glass-border:rgba(255,255,255,.2); --glass-shadow:rgba(0,0,0,.5);
  --vac:#1abc9c;   --half:#8e44ad;  --corr:#f39c12; --abs:#dc3545; --week:#6c757d;
}
body{
  font-family:'El Messiri',sans-serif;
  background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);
  background-size:400% 400%; animation:gradientAnimation 20s ease infinite;
  display:flex; justify-content:center; align-items:center; min-height:100vh; margin:0; padding:15px; position:relative; color:var(--text-light);
}
@keyframes gradientAnimation{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}

.particles{position:absolute; inset:0; overflow:hidden; z-index:0}
.particle{position:absolute; background:rgba(255,140,0,.1); clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%); animation:float 25s infinite ease-in-out; opacity:0; filter:blur(2px)}
.particle:nth-child(even){background:rgba(0,31,63,.1)}
@keyframes float{0%{transform:translateY(0) rotate(0);opacity:0}20%{opacity:1}80%{opacity:1}100%{transform:translateY(-100vh) rotate(360deg);opacity:0}}

.top-fixed-nav{position:fixed;top:20px;right:20px;z-index:100;display:flex;gap:10px;align-items:center}
.top-fixed-nav .btn{background-color:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.3); color:#fff; border-radius:10px; padding:6px 10px; font-weight:500; box-shadow:0 4px 10px rgba(0,0,0,.2)}
.top-fixed-nav img{height:36px;width:auto}

.main-screen-container{
  background:var(--glass-bg);backdrop-filter:blur(15px);-webkit-backdrop-filter:blur(15px);
  border:1px solid var(--glass-border);box-shadow:0 10px 50px 0 var(--glass-shadow);
  border-radius:20px; padding:20px; width:100%; max-width:1000px; z-index:1;
}
h2{font-weight:700;text-align:center;margin-bottom:12px;text-shadow:0 3px 6px rgba(0,0,0,.4)}

.month-nav{display:flex;justify-content:space-between;align-items:center;gap:8px;margin-bottom:8px}
.month-title{font-size:1.05rem;font-weight:700}
.btn-nav{background:rgba(255,255,255,.12);border:1px solid var(--glass-border);color:#fff;border-radius:10px;padding:6px 10px}

.legend{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:8px;font-size:.88rem}
.legend .chip{display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.1);border:1px solid var(--glass-border);padding:4px 8px;border-radius:10px}
.legend .dot{width:10px;height:10px;border-radius:50%}
.dot-vac{background:var(--vac)} .dot-half{background:var(--half)} .dot-corr{background:var(--corr)} .dot-abs{background:var(--abs)} .dot-week{background:var(--week)}

.calendar{width:100%;border-collapse:separate;border-spacing:6px}
.calendar th{text-align:center;color:#fff;font-size:.95rem}
.day-cell{
  height:82px;border:1px solid var(--glass-border);border-radius:14px;background:rgba(255,255,255,.06);
  position:relative;padding:6px;cursor:pointer;transition:.2s;box-shadow:0 4px 12px rgba(0,0,0,.15)
}
.day-cell:hover{transform:translateY(-2px);background:rgba(255,255,255,.12)}
.day-num{position:absolute;top:6px;left:6px;font-weight:700;font-size:.95rem}
.today{outline:2px solid var(--marsom-orange)}
.has-attendance::after{content:'';position:absolute;width:8px;height:8px;border-radius:50%;bottom:6px;left:6px;background:#0dcaf0;box-shadow:0 0 8px rgba(13,202,240,.8)}
.selected{box-shadow:0 0 0 2px #fff inset}

/* نقطة مخالفة صغيرة */
.violation-dot{position:absolute;bottom:6px;right:6px;width:9px;height:9px;border-radius:50%;background:#dc3545;box-shadow:0 0 8px rgba(220,53,69,.8)}

.event-dots{position:absolute;bottom:6px;right:20px;display:flex;gap:4px;align-items:center}
.event-dots .e{width:9px;height:9px;border-radius:50%}
.e.vac{background:var(--vac)} .e.half{background:var(--half)} .e.corr{background:var(--corr)}
.event-dots .more{font-size:.72rem;background:rgba(255,255,255,.15);padding:0 5px;border-radius:8px;border:1px solid var(--glass-border)}

.details-card{margin-top:10px;background:rgba(255,255,255,.08);border:1px solid var(--glass-border);border-radius:14px;padding:12px;box-shadow:0 6px 18px rgba(0,0,0,.25)}
.details-card .badge{font-size:.78rem}

.scroll-area{max-height:75vh;overflow:auto;padding-right:4px}

/* حالات خاصة للخلايا */
.absent{background: rgba(220,53,69,.18) !important; border-color: rgba(220,53,69,.55) !important;}
.absent:hover{background: rgba(220,53,69,.28) !important;}
.weekend{background: rgba(108,117,125,.15); border-color: rgba(108,117,125,.45);}
.weekend:hover{background: rgba(108,117,125,.25);}

/* تحسينات الموبايل */
@media (max-width: 576px){
  .particles{display:none}
  body{padding:10px;min-height:auto}
  .top-fixed-nav{top:10px;right:10px;gap:6px}
  .top-fixed-nav img{display:none}
  .top-fixed-nav .btn{padding:6px 8px;font-size:.84rem}
  .main-screen-container{padding:12px;border-radius:16px}
  h2{font-size:1.05rem;margin-bottom:8px}
  .month-title{font-size:.98rem}
  .calendar{border-spacing:4px}
  .day-cell{height:60px;border-radius:12px;padding:6px}
  .day-num{font-size:.9rem}
  .event-dots{gap:3px}
  .scroll-area{max-height:68vh}
}
</style>
</head>
<body>

<!-- خلفية زخرفية -->
<div class="particles">
  <div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div>
  <div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div>
</div>

<!-- شريط علوي صغير -->
<div class="top-fixed-nav">
  <button class="btn btn-secondary">مرحباً <?= html_escape($name) ?: 'ضيف' ?></button>
  <button class="btn btn-secondary" onclick="location.href='<?= site_url('users/logout'); ?>'"><i class="fas fa-right-from-bracket me-2"></i> خروج</button>
  <button class="btn btn-secondary" id="btnAnnouncements"><i class="fas fa-bullhorn me-2"></i> الإعلانات</button>
  <button class="btn btn-secondary" id="btnNotifications"><i class="fas fa-bell me-2"></i> الإشعارات</button>
  <button class="btn btn-secondary" onclick="location.href='<?= site_url('users1/main_emp'); ?>'"><i class="fas fa-home"></i> الرئيسية</button>
  <img src="<?= base_url('assets/imeges/m2.PNG'); ?>" alt="Marsom Logo">
</div>

<div class="main-screen-container">
  <h2>الحضور</h2>

  <div class="scroll-area">

    <div class="month-nav">
      <a class="btn btn-nav" href="<?= site_url('users1/attendance?y='.$prevY.'&m='.$prevM) ?>"><i class="fa-solid fa-angles-right"></i> السابق</a>
      <div class="month-title"><?= $monthNameAr[$month] . ' ' . $year ?></div>
      <a class="btn btn-nav" href="<?= site_url('users1/attendance?y='.$nextY.'&m='.$nextM) ?>">التالي <i class="fa-solid fa-angles-left"></i></a>
    </div>

    <div class="legend">
      <span class="chip"><span class="dot dot-vac"></span> إجازة</span>
      <span class="chip"><span class="dot dot-half"></span> نصف يوم</span>
      <span class="chip"><span class="dot dot-corr"></span> تصحيح بصمة</span>
      <span class="chip"><span class="dot" style="background:#0dcaf0"></span> توجد بصمات</span>
      <span class="chip"><span class="dot dot-abs"></span> غياب</span>
      <span class="chip"><span class="dot dot-week"></span> نهاية الأسبوع</span>
      <span class="chip"><span class="dot" style="background:#dc3545"></span> يوم يحوي مخالفة</span>
    </div>

    <div class="table-responsive">
      <table class="calendar w-100">
        <thead>
          <tr><?php foreach($weekDaysAr as $wd): ?><th><?= $wd ?></th><?php endforeach; ?></tr>
        </thead>
        <tbody>
        <?php
        $w = $firstWeekday; echo '<tr>';
        for ($i=0;$i<$w;$i++) echo '<td></td>';

        $today = date('Y-m-d');
        for ($d=1;$d<=$daysInMonth;$d++){
          $date = sprintf('%04d-%02d-%02d',$year,$month,$d);
          $has  = isset($daysMap[$date]);
          $evs  = $eventsByDay[$date] ?? [];
          $hasViolation = !empty($violationsByDay[$date]);
          $isWeekend = !empty($WEEKEND_MAP[$date]);
          $isAbsent  = !$has && empty($evs) && !$isWeekend;

          $classes = 'day-cell';
          if ($has) $classes .= ' has-attendance';
          if ($date === $today) $classes .= ' today';
          if ($isAbsent) $classes .= ' absent';
          if ($isWeekend && !$has && empty($evs)) $classes .= ' weekend';

          echo '<td>';
          echo '<div class="'.$classes.'" data-date="'.$date.'">';
          echo '<div class="day-num">'.$d.'</div>';

          if ($has) {
            $firstDisp = $daysMap[$date]['first_in'] ? date('H:i', strtotime($daysMap[$date]['first_in'])) : '—';
            $lastDisp  = $daysMap[$date]['last_out'] ? date('H:i', strtotime($daysMap[$date]['last_out'])) : '—';
            echo '<div class="mt-4 small text-center" style="opacity:.95">';
            echo '<div>دخول: <strong>'.$firstDisp.'</strong></div>';
            echo '<div>خروج: <strong>'.$lastDisp.'</strong></div>';
            echo '</div>';
          } elseif ($isAbsent) {
            echo '<div class="mt-4 text-center"><span class="badge bg-danger">غياب</span></div>';
          } elseif ($isWeekend) {
            echo '<div class="mt-4 text-center"><span class="badge bg-secondary">إجازة نهاية الأسبوع</span></div>';
          } else {
            echo '<div class="mt-4 small text-center" style="opacity:.6">—</div>';
          }

          // نقاط الأحداث (إجازة/نصف يوم/تصحيح)
          if (!empty($evs)) {
            echo '<div class="event-dots">';
            $count = 0;
            foreach ($evs as $ev) {
              $type = $ev['type']==='vac_half'?'half':($ev['type']==='vac_full'?'vac':'corr');
              echo '<span class="e '.$type.'" title="'.html_escape($ev['title']).'"></span>';
              if (++$count>=4) break;
            }
            if (count($evs)>4) echo '<span class="more">+'.(count($evs)-4).'</span>';
            echo '</div>';
          }

          // نقطة مخالفة
          if ($hasViolation) {
            echo '<span class="violation-dot" title="يوجد مخالفة"></span>';
          }

          echo '</div>';
          echo '</td>';

          if (++$w>6){ $w=0; echo '</tr><tr>'; }
        }
        if ($w!==0){ for ($i=$w;$i<=6;$i++) echo '<td></td>'; echo '</tr>'; }
        ?>
        </tbody>
      </table>
    </div>

    <!-- تفاصيل اليوم -->
    <div class="details-card" id="detailsCard" style="display:none">
      <div class="d-flex align-items-center gap-2 mb-2">
        <i class="fa-solid fa-calendar-day" style="font-size:.95rem"></i>
        <div id="detailsDate" class="fw-bold"></div>
      </div>
      <div id="detailsNote" class="mb-2">—</div>
      <div id="detailsEvents"></div>
      <div class="mt-2 text-warning small">تنبيه: إذا تساوى وقت الدخول والخروج فهذا يعني وجود بصمة واحدة فقط.</div>
    </div>

  </div>
</div>

<!-- Toast ترحيبي -->
<div class="toast-container position-fixed bottom-0 start-0 p-3" style="z-index:1080">
  <div id="welcomeToast" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="2800">
    <div class="d-flex">
      <div class="toast-body">مرحباً <?= $name ?: 'ضيف' ?> (<?= $username ?>) 👋 — يسعدنا وجودك!</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script>
  // خرائط من السيرفر
  const EVENTS      = <?= json_encode($eventsByDay, JSON_UNESCAPED_UNICODE) ?>;
  const WEEKEND_MAP = <?= json_encode($WEEKEND_MAP) ?>;
  const VIOLATIONS  = <?= json_encode($violationsByDay, JSON_UNESCAPED_UNICODE) ?>;

  const STATUS_MAP = {
    0: {text:'بانتظار موافقة المسئول المباشر', cls:'bg-warning text-dark'},
    1: {text:'بانتظار موافقة الموارد البشرية', cls:'bg-info text-dark'},
    2: {text:'تمت الموافقة', cls:'bg-success'},
    3: {text:'مرفوض', cls:'bg-danger'}
  };

  const VIOLATION_STYLE = {
    'single':   { cls: 'bg-danger',  icon: 'fa-fingerprint' },
    'late_in':  { cls: 'bg-warning text-dark', icon: 'fa-clock' },
    'early_out':{ cls: 'bg-warning text-dark', icon: 'fa-clock-rotate-left' },
  };

  function renderEvents(date){
    const box = document.getElementById('detailsEvents');
    const items = EVENTS[date] || [];
    if (items.length === 0){ box.innerHTML = '<div class="text-muted">لا توجد طلبات في هذا اليوم.</div>'; return; }

    let html = '';
    items.forEach(ev=>{
      let typeLabel = ev.type==='vac_full' ? 'إجازة' :
                      ev.type==='vac_half' ? `إجازة نصف يوم (${ev.period||'—'})` :
                      'تصحيح بصمة';
      const st = STATUS_MAP[ev.status] || {text:'—', cls:'bg-secondary'};

      let extra = '';
      if (ev.type==='correction'){
        const inT  = ev.in  ? `<span class="me-2">تصحيح دخول: <strong>${ev.in}</strong></span>` : '';
        const outT = ev.out ? `<span>تصحيح خروج: <strong>${ev.out}</strong></span>` : '';
        extra = `<div class="mt-1 small">${inT}${outT}</div>`;
      }

      html += `
        <div class="p-2 mb-2 rounded" style="background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.2);">
          <div class="d-flex align-items-center justify-content-between">
            <div><i class="fa-solid fa-tag me-2" style="font-size:.9rem"></i>${typeLabel}</div>
            <span class="badge ${st.cls}">${st.text}</span>
          </div>
          ${extra}
        </div>`;
    });
    box.innerHTML = html;
  }

  function renderViolations(date){
    const list = VIOLATIONS[date] || [];
    if (!list.length) return '';
    let html = '<div class="mt-2">';
    list.forEach(v=>{
      const st = VIOLATION_STYLE[v.type] || {cls:'bg-secondary',icon:'fa-circle-exclamation'};
      const mins = v.minutes ? ` — ${v.minutes} دقيقة` : '';
      html += `
        <span class="badge ${st.cls} me-1 mb-1">
          <i class="fa-solid ${st.icon} me-1"></i>${v.label}${mins}
        </span>`;
    });
    html += '</div>';
    return html;
  }

  // ترحيب
  window.addEventListener('load', ()=> {
    const toastEl = document.getElementById('welcomeToast');
    if (toastEl) new bootstrap.Toast(toastEl).show();
  });

  // تفاعل التقويم
  document.querySelectorAll('.day-cell').forEach(cell=>{
    cell.addEventListener('click', function(){
      document.querySelectorAll('.day-cell.selected').forEach(c=>c.classList.remove('selected'));
      this.classList.add('selected');

      const date = this.getAttribute('data-date');
      if (!date) return;

      document.getElementById('detailsCard').style.display = 'block';
      document.getElementById('detailsDate').textContent = date;
      document.getElementById('detailsNote').innerHTML = '<span class="text-info">جاري جلب الحضور...</span>';

      renderEvents(date);

      // جلب الحضور عبر AJAX (تفاصيل إضافية لو endpoint لديك يعيد note)
      const formData = new FormData();
      formData.append('date', date);
      <?php if (!empty($csrf_token_name) && !empty($csrf_hash)): ?>
        formData.append('<?= $csrf_token_name ?>', '<?= $csrf_hash ?>');
      <?php endif; ?>

      fetch('<?= site_url('users1/attendance_day'); ?>', {
        method: 'POST',
        body: formData,
        headers: {'X-Requested-With':'XMLHttpRequest'}
      })
      .then(r=>r.json())
      .then(data=>{
        if (!data.ok){
          document.getElementById('detailsNote').innerHTML =
            '<span class="text-danger">'+(data.msg||'حدث خطأ')+'</span>' + renderViolations(date);
          return;
        }
        // عرض ملاحظة الخادم + المخالفات المحسوبة
        document.getElementById('detailsNote').innerHTML = (data.note || '') + renderViolations(date);
      })
      .catch(()=>{
        document.getElementById('detailsNote').innerHTML = '<span class="text-danger">تعذّر الاتصال بالخادم</span>' + renderViolations(date);
      });
    });
  });

  // مثال: إعادة استخدام Toast
  document.getElementById('btnNotifications')?.addEventListener('click', ()=>{
    const toastEl = document.getElementById('welcomeToast');
    if (toastEl) new bootstrap.Toast(toastEl).show();
  });
</script>
</body>
</html>
