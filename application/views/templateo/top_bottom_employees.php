<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>أفضل 10 موظفين تحقيقًا للمستهدف</title>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <style>
    :root { --primary-blue:#0A58CA; --light-blue:#E0F2FE; --dark-blue-text:#212529; --light-gray-bg:#f8f9fa; --medium-gray-border:#dee2e6; --soft-shadow:0 8px 25px rgba(0,0,0,.08); }
    body{ background:var(--light-gray-bg); font-family:'Inter',sans-serif; color:var(--dark-blue-text); animation:fadeInBackground 1s ease-out;}
    h1,h2,h3,h4,h5,h6{ font-family:'Cairo',sans-serif; font-weight:700;}
    .page-title{ color:var(--primary-blue); opacity:0; animation:fadeInFromTop .8s ease-out .3s forwards;}
    .card{ border:0; border-radius:1.25rem; box-shadow:var(--soft-shadow); overflow:hidden; opacity:0; animation:fadeInFromBottom .8s ease-out .6s forwards;}
    .badge-month{ font-size:.95rem; padding:.6em 1.2em; border-radius:2rem; background-color:var(--primary-blue)!important; color:#fff!important; box-shadow:0 4px 10px rgba(10,88,202,.3);}
    .form-label{ font-weight:500; margin-bottom:.4rem;}
    .table{ border-collapse:separate; border-spacing:0; font-size:.95rem;}
    .table thead th{ background:var(--primary-blue); color:#fff; padding:1.2rem 1rem; font-weight:600; border-bottom:none;}
    .table thead tr:first-child th:first-child{ border-top-right-radius:1.2rem;}
    .table thead tr:first-child th:last-child{ border-top-left-radius:1.2rem;}
    .table tbody tr{ transition:background-color .3s ease, transform .2s ease;}
    .table tbody tr:hover{ background:var(--light-blue); transform:scale(1.01); cursor:pointer;}
    .table tbody td{ padding:1rem 1rem; border-top:1px solid var(--medium-gray-border);}
    .table tbody td.emp-name-cell{ font-weight:700;}
    .table tfoot tr th,.table tfoot tr td{ background:var(--light-blue); font-weight:700; padding:1.2rem 1rem; border-top:1px solid var(--primary-blue); color:var(--primary-blue);}

    /* البادج والصفوف (ألوان ناعمة) */
    .pct-badge{padding:.35rem .55rem;border-radius:999px;display:inline-block;min-width:64px;border:1px solid transparent;background:transparent;font-weight:600}
    .pct-good{color:#1e7d44;border-color:rgba(30,125,68,.35)}
    .pct-mid{color:#a35d00;border-color:rgba(163,93,0,.35)}
    .pct-bad{color:#b3261e;border-color:rgba(179,38,30,.35)}
    .row-achieved{ background:linear-gradient(90deg, rgba(13,160,66,.08), rgba(13,160,66,.03)); border-inline-start:4px solid rgba(13,160,66,.35);}
    .row-mid{ background:linear-gradient(90deg, rgba(245,182,66,.10), rgba(245,182,66,.04)); border-inline-start:4px solid rgba(245,182,66,.35);}
    .row-low{ background:linear-gradient(90deg, rgba(220,53,69,.06), rgba(220,53,69,.03)); border-inline-start:4px solid rgba(220,53,69,.35);}
    .table tbody tr.row-achieved:hover{background:linear-gradient(90deg, rgba(13,160,66,.10), rgba(13,160,66,.05))}
    .table tbody tr.row-mid:hover{background:linear-gradient(90deg, rgba(245,182,66,.12), rgba(245,182,66,.06))}
    .table tbody tr.row-low:hover{background:linear-gradient(90deg, rgba(220,53,69,.08), rgba(220,53,69,.04))}

    @keyframes fadeInFromTop{from{opacity:0;transform:translateY(-20px)}to{opacity:1;transform:translateY(0)}}
    @keyframes fadeInFromBottom{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
    @keyframes fadeInBackground{from{opacity:.8}to{opacity:1)}
  </style>
</head>
<body>

<?php $home_url = isset($home_url) ? $home_url : base_url(); ?>

<div class="container py-5">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
      <h1 class="page-title h3 mb-0">أفضل 10 موظفين تحقيقًا للمستهدف</h1>
      <span class="badge-month">
        <?= htmlspecialchars($title_suffix ?? 'تقرير اليوم', ENT_QUOTES, 'UTF-8'); ?>
        — <?= htmlspecialchars($date_label ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>
      </span>
    </div>
    <div class="d-flex gap-2 flex-wrap">
  <!-- الرئيسية -->
  <a href="<?= site_url('users/ceo_report'); ?>"
     class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm d-inline-flex align-items-center">
    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
      <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
    </svg>
    <span class="ms-2">الرئيسية</span>
  </a>

  <!-- رجوع -->
  <button type="button"
          onclick="history.back()"
          class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm d-inline-flex align-items-center">
    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
      <path d="M11.67 3.87L9.9 2.1 0 12l9.9 9.9 1.77-1.77L3.54 12z"/>
    </svg>
    <span class="ms-2">رجوع</span>
  </button>
</div>
  </div>

  <!-- فلاتر المدى الزمني (ترسل تلقائيًا عند التغيير) -->
  <form id="rangeForm" class="row gy-3 gx-3 align-items-end mb-4" method="get" action="">
    <?php $range = isset($range) ? $range : 'today'; ?>
    <div class="col-12">
      <label class="form-label d-block mb-1">اختر التقرير</label>
      <div class="d-flex flex-wrap gap-3">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="range" id="rToday" value="today" <?= ($range==='today'?'checked':'') ?>>
          <label class="form-check-label" for="rToday">تقرير اليوم (<?= date('Y-m-d'); ?>)</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="range" id="rYesterday" value="yesterday" <?= ($range==='yesterday'?'checked':'') ?>>
          <label class="form-check-label" for="rYesterday">تقرير أمس (<?= date('Y-m-d', strtotime('-1 day')); ?>)</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="range" id="rBeforeYesterday" value="before_yesterday" <?= ($range==='before_yesterday'?'checked':'') ?>>
          <label class="form-check-label" for="rBeforeYesterday">تقرير قبل أمس (<?= date('Y-m-d', strtotime('-2 day')); ?>)</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="range" id="rMonth" value="month" <?= ($range==='month'?'checked':'') ?>>
          <label class="form-check-label" for="rMonth">تقرير الشهر الحالي</label>
        </div>
      </div>
    </div>
    <!-- لا يوجد زر تحديث -->
  </form>

  <?php
    // rows: employee_name, actual_amount (حسب الفلتر), achievement_pct (شهري دائمًا)
    $rows = isset($rows) && is_array($rows) ? $rows : [];

    // تلوين الصف حسب النسبة
    function rowClassByPct($pct){
      if ($pct === null) return '';
      if ($pct >= 100) return 'row-achieved';
      if ($pct >= 50)  return 'row-mid';
      return 'row-low';
    }
    function badgeClass($pct){
      if ($pct === null) return '';
      if ($pct >= 100) return 'pct-good';
      if ($pct >= 50)  return 'pct-mid';
      return 'pct-bad';
    }

    // تسمية عمود المبلغ حسب الفلتر (اختياري)
    $labels = [
      'today'            => 'مبلغ سداد اليوم',
      'yesterday'        => 'مبلغ سداد أمس',
      'before_yesterday' => 'مبلغ سداد قبل أمس',
      'month'            => 'مبلغ سداد الشهر',
    ];
    $amount_col = isset($labels[$range]) ? $labels[$range] : 'مبلغ السداد';
  ?>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle text-center">
          <thead>
  <tr>
    <th style="width:80px">#</th>
    <th>اسم الموظف</th>
    <th>اسم المشروع</th>
    <th><?= htmlspecialchars($amount_col, ENT_QUOTES, 'UTF-8'); ?></th>
    <th>نسبة التحقيق (شهري)</th>
  </tr>
</thead>
<tbody>
  <?php if (!empty($rows)): $i=1; foreach ($rows as $r):
    $name = $r['employee_name'] ?? '—';
    $proj = $r['project_name']  ?? '—';
    $amt  = (float)($r['actual_amount'] ?? 0.0);
    $pct  = isset($r['achievement_pct']) ? (float)$r['achievement_pct'] : null;
    $rowC = rowClassByPct($pct);
    $bdg  = badgeClass($pct);
  ?>
    <tr class="<?= $rowC ?>">
      <td><?= $i++; ?></td>
      <td class="text-start px-4 emp-name-cell"><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></td>
      <td class="text-start px-4"><?= htmlspecialchars($proj, ENT_QUOTES, 'UTF-8'); ?></td>
      <td><strong><?= number_format($amt, 2); ?></strong></td>
      <td>
        <?php if ($pct === null): ?>
          <span class="text-muted">—</span>
        <?php else: ?>
          <span class="pct-badge <?= $bdg ?>"><?= number_format($pct, 2); ?>%</span>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; else: ?>
    <tr><td colspan="5" class="text-muted py-4">لا توجد بيانات لعرض أفضل 10.</td></tr>
  <?php endif; ?>
</tbody>

        </table>
      </div>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // إرسال تلقائي عند تغيير الاختيار
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('input[name="range"]').forEach(function (inp) {
      inp.addEventListener('change', function () {
        document.getElementById('rangeForm').submit();
      });
    });
  });
</script>
</body>
</html>
