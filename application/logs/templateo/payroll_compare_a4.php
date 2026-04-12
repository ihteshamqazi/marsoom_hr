<?php /* application/views/templateo/payroll_compare_a4.php */ ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>مقارنة الرواتب</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
<style>
  @page { size: A4; margin: 12mm 12mm 14mm 12mm; }
  html, body { font-family:'Tajawal',system-ui,-apple-system,"Segoe UI",Arial,sans-serif; background:#f4f6f9; color:#111; }
  .a4-sheet { width:210mm; min-height:297mm; margin:10mm auto; background:#fff; box-shadow:0 6px 24px rgba(0,0,0,.08); padding:14mm; }

  .no-print { display:flex; flex-wrap:wrap; gap:.5rem; justify-content:center; margin:12px auto; }
  .btn { border:1px solid #001f3f; color:#001f3f; background:#fff; padding:8px 14px; border-radius:8px; font-weight:700; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:8px; }
  .btn:hover { background:#001f3f; color:#fff; }
  .btn-secondary { border-color:#999; color:#444; }
  .btn-secondary:hover { background:#444; color:#fff; }
  .btn-submit { border-color:#FF8C00; color:#FF8C00; }
  .btn-submit:hover { background:#FF8C00; color:#fff; }
  input[type="month"]{padding:8px 10px;border:1px solid #ccc;border-radius:8px;}

  .header { display:flex; align-items:center; justify-content:space-between; gap:16px; padding-bottom:10px; border-bottom:2px solid #001f3f; margin-bottom:14px; }
  .brand { display:flex; align-items:center; gap:10px; }
  .brand img { height:50px; width:auto; }
  .brand h1 { font-size:20px; margin:0; font-weight:800; color:#001f3f; letter-spacing:.2px; }
  .meta { text-align:left; font-size:12px; color:#444; }
  .meta .date { font-weight:700; }

  .section-title { margin:14px 0 8px; padding:8px 10px; background:#f7f9fc; border-right:4px solid #FF8C00; font-weight:800; }
  .print-table { width:100%; border-collapse:collapse; margin-top:6px; }
  .print-table th, .print-table td { border:1px solid #d9dee3; padding:6px 8px; font-size:13px; vertical-align:middle; }
  .print-table th { background:#001f3f; color:#fff; font-weight:700; text-align:center; }
  .center{text-align:center}

  .summary { display:grid; grid-template-columns: repeat(2, 1fr); gap:10px; margin-top:10px; }
  .card { border:1px solid #e7ebf0; border-radius:10px; padding:10px; background:#fafbff; }
  .card .label { font-size:12px; color:#555; }
  .card .value { font-size:18px; font-weight:800; }

  .delta.up { color:#137333; font-weight:800; }
  .delta.down { color:#b42318; font-weight:800; }
  .delta.flat { color:#444; font-weight:800; }

  @media print {
    body { background:#fff; }
    .a4-sheet { margin:0; width:auto; min-height:auto; box-shadow:none; padding:0; }
    .no-print { display:none !important; }
    a { color:inherit; text-decoration:none; }
  }
</style>
</head>
<body>

<!-- شريط أدوات للشاشة فقط -->
<div class="no-print">
  <form method="get" action="<?php echo site_url('users1/payroll_compare'); ?>" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
    <label>الشهر الأول:</label>
    <input type="month" name="m1" value="<?php echo html_escape($m1 ?? ''); ?>" required>
    <label>الشهر الثاني:</label>
    <input type="month" name="m2" value="<?php echo html_escape($m2 ?? ''); ?>" required>
    <button class="btn btn-submit" type="submit">عرض المقارنة</button>
    <a class="btn" href="javascript:window.print()">طباعة</a>
    <a class="btn btn-secondary" href="javascript:history.back()">رجوع</a>
  </form>
</div>

<div class="a4-sheet">
  <div class="header">
    <div class="brand">
      <img src="<?php echo base_url('assets/imeges/m1.png'); ?>" alt="شعار" style="height:40px; width:auto;">
      <h1>مقارنة الرواتب</h1>
    </div>
    <div class="meta">
      <div class="date">التاريخ: <?php echo date('Y/m/d'); ?></div>
      <div class="small">الفترة: <?php echo html_escape($m1); ?> ↔ <?php echo html_escape($m2); ?></div>
    </div>
  </div>

  <?php foreach ($companies as $code => $comp): ?>
    <?php
      $name     = $comp['name'];
      $leftOnly = $comp['leftOnly'];
      $rightOnly= $comp['rightOnly'];
      $sum1     = $comp['sum1'];
      $sum2     = $comp['sum2'];
      $leftTotals  = $comp['leftTotals'];
      $rightTotals = $comp['rightTotals'];
    ?>

    <div class="section-title"><?php echo html_escape($name); ?> — المقارنات التفصيلية</div>

    <!-- موظفون في الشهر الأول فقط -->
    <div class="section-title" style="border-right-color:#1e40af;background:#f1f5ff">الموجودون في <?php echo html_escape($m1); ?> وغير موجودين في <?php echo html_escape($m2); ?></div>
    <?php if (!empty($leftOnly)): ?>
      <table class="print-table">
        <thead>
          <tr>
            <th>الرقم الوظيفي</th>
            <th>اسم الموظف</th>
            <th>إجمالي الراتب </th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($leftOnly as $r): ?>
          <tr>
            <td class="center"><?php echo html_escape($r['emp_no']); ?></td>
            <td><?php echo html_escape($r['emp_name']); ?></td>
            <td class="center"><?php echo number_format($r['total_salary'], 2); ?></td>
          </tr>
          <?php endforeach; ?>
          <tr>
            <td class="center"><strong>الإجمالي</strong></td>
            <td class="center"><strong><?php echo (int)$leftTotals['count']; ?> موظف</strong></td>
            <td class="center"><strong><?php echo number_format($leftTotals['salary'], 2); ?></strong></td>
          </tr>
        </tbody>
      </table>
    <?php else: ?>
      <div class="card"><div class="label">لا يوجد فرق: جميع موظفي الشهر الأول موجودون في الشهر الثاني.</div></div>
    <?php endif; ?>

    <!-- موظفون في الشهر الثاني فقط -->
    <div class="section-title" style="border-right-color:#059669;background:#ecfdf5">الموجودون في <?php echo html_escape($m2); ?> وغير موجودين في <?php echo html_escape($m1); ?></div>
    <?php if (!empty($rightOnly)): ?>
      <table class="print-table">
        <thead>
          <tr>
            <th>الرقم الوظيفي</th>
            <th>اسم الموظف</th>
            <th>إجمالي الراتب </th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($rightOnly as $r): ?>
          <tr>
            <td class="center"><?php echo html_escape($r['emp_no']); ?></td>
            <td><?php echo html_escape($r['emp_name']); ?></td>
            <td class="center"><?php echo number_format($r['total_salary'], 2); ?></td>
          </tr>
          <?php endforeach; ?>
          <tr>
            <td class="center"><strong>الإجمالي</strong></td>
            <td class="center"><strong><?php echo (int)$rightTotals['count']; ?> موظف</strong></td>
            <td class="center"><strong><?php echo number_format($rightTotals['salary'], 2); ?></strong></td>
          </tr>
        </tbody>
      </table>
    <?php else: ?>
      <div class="card"><div class="label">لا يوجد فرق: جميع موظفي الشهر الثاني كانوا موجودين في الشهر الأول.</div></div>
    <?php endif; ?>

    <!-- الملخصات الشهرية والمقارنة -->
    <div class="section-title">ملخصات شهرية ومؤشرات التغيير</div>
    <table class="print-table">
      <thead>
        <tr>
          <th>البند</th>
          <th><?php echo html_escape($m1); ?></th>
          <th><?php echo html_escape($m2); ?></th>
          <th>التغير</th>
          <th>% النسبة</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($comp['changes'] as $ch): ?>
          <tr>
            <td><?php echo html_escape($ch['label']); ?></td>
            <td class="center"><?php echo number_format($ch['v1'], 2); ?></td>
            <td class="center"><?php echo number_format($ch['v2'], 2); ?></td>
            <td class="center delta <?php echo $ch['trend']; ?>">
              <?php
                $sign = $ch['delta'] > 0 ? '+' : ($ch['delta'] < 0 ? '−' : '');
                echo $sign . number_format(abs($ch['delta']), 2);
              ?>
            </td>
            <td class="center delta <?php echo $ch['trend']; ?>">
              <?php echo number_format($ch['pct'], 2); ?>%
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="summary">
      <div class="card">
        <div class="label">عدد الموظفين — <?php echo html_escape($m1); ?></div>
        <div class="value"><?php echo number_format($sum1['emp_count'] ?? 0); ?></div>
      </div>
      <div class="card">
        <div class="label">عدد الموظفين — <?php echo html_escape($m2); ?></div>
        <div class="value"><?php echo number_format($sum2['emp_count'] ?? 0); ?></div>
      </div>
    </div>

    <hr style="margin:14px 0;border:0;border-top:1px dashed #e7ebf0">
  <?php endforeach; ?>

</div><!-- /.a4-sheet -->
</body>
</html>
