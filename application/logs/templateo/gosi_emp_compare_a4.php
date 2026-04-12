<?php /* application/views/templateo/gosi_emp_compare_a4.php */ ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>مقارنة التأمينات الاجتماعية مع سجل الموظفين</title>
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

  .header { display:flex; align-items:center; justify-content:space-between; gap:16px; padding-bottom:10px; border-bottom:2px solid #001f3f; margin-bottom:14px; }
  .brand { display:flex; align-items:center; gap:10px; }
  .brand img { height:46px; width:auto; }
  .brand h1 { font-size:18px; margin:0; font-weight:800; color:#001f3f; letter-spacing:.2px; }
  .meta { text-align:left; font-size:12px; color:#444; }
  .meta .date { font-weight:700; }

  .section-title { margin:14px 0 8px; padding:8px 10px; background:#f7f9fc; border-right:4px solid #FF8C00; font-weight:800; }
  .print-table { width:100%; border-collapse:collapse; margin-top:6px; }
  .print-table th, .print-table td { border:1px solid #d9dee3; padding:6px 8px; font-size:13px; vertical-align:middle; }
  .print-table th { background:#001f3f; color:#fff; font-weight:700; text-align:center; }
  .center { text-align:center; }

  .summary { display:grid; grid-template-columns: repeat(4, 1fr); gap:10px; margin-top:10px; }
  .card { border:1px solid #e7ebf0; border-radius:10px; padding:10px; background:#fafbff; }
  .card .label { font-size:12px; color:#555; }
  .card .value { font-size:18px; font-weight:800; }

  .badge { display:inline-block; padding:.25rem .5rem; border-radius:999px; font-size:12px; font-weight:800; }
  .up   { background:#e6f4ea; color:#137333; }
  .down { background:#fde8e8; color:#b42318; }
  .flat { background:#eef2ff; color:#1e40af; }

  @media print {
    body { background:#fff; }
    .a4-sheet { margin:0; width:auto; min-height:auto; box-shadow:none; padding:0; }
    .no-print { display:none !important; }
    a { color:inherit; text-decoration:none; }
  }
</style>
</head>
<body>

<div class="no-print">
  <form method="get" action="<?php echo site_url('users1/gosi_emp_compare'); ?>" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
    <label>الشركة:</label>
    <select name="c" style="padding:8px 10px;border:1px solid #ccc;border-radius:8px;min-width:220px;">
      <option value="">الكل</option>
      <option value="1" <?php echo (isset($company) && $company==='1')?'selected':''; ?>>شركة مرسوم</option>
      <option value="2" <?php echo (isset($company) && $company==='2')?'selected':''; ?>>مكتب الدكتور صالح الجربوع</option>
    </select>
    <button class="btn btn-submit" type="submit">عرض</button>
    <a class="btn" href="javascript:window.print()">طباعة</a>
    <a class="btn btn-secondary" href="javascript:history.back()">رجوع</a>
  </form>
</div>

<?php foreach (($blocks ?? []) as $b): ?>
  <div class="a4-sheet">
    <!-- رأس -->
    <div class="header">
      <div class="brand">
        <?php if (($b['code'] ?? 1) == 2): ?>
          <img src="<?php echo base_url('assets/imeges/saleh.png'); ?>" alt="شعار">
        <?php else: ?>
          <img src="<?php echo base_url('assets/imeges/m1.png'); ?>" alt="شعار"style="height:40px; width:auto;">
        <?php endif; ?>
        <h1>مقارنة GOSI مع سجل الموظفين — <?php echo $b['label']; ?></h1>
      </div>
      <div class="meta"><div class="date">التاريخ: <?php echo date('Y/m/d'); ?></div></div>
    </div>

    <!-- ملخص سريع -->
    <?php $s = $b['summary']; ?>
    <div class="summary">
      <div class="card">
        <div class="label">عدد الموظفين في التأمينات GOSI</div>
        <div class="value"><?php echo number_format($s['cntGosi']); ?></div>
      </div>
      <div class="card">
        <div class="label">عدد الموظفين في سجل الموظفين</div>
        <div class="value"><?php echo number_format($s['cntEmp']); ?></div>
      </div>
      <div class="card">
        <div class="label">إجمالي الرواتب GOSI</div>
        <div class="value"><?php echo number_format($s['totalGosi'],2); ?> ر.س</div>
      </div>
      <div class="card">
        <div class="label">إجمالي الرواتب سجل الموظفين</div>
        <div class="value"><?php echo number_format($s['totalEmp'],2); ?> ر.س</div>
      </div>
    </div>

    <div class="summary">
      <div class="card">
        <div class="label">فارق العدد (سجل الموظفين − GOSI)</div>
        <div class="value">
          <?php
            $cls = ($s['deltaCount']>0)?'up':(($s['deltaCount']<0)?'down':'flat');
            $lbl = ($s['deltaCount']>0)?'↑':(($s['deltaCount']<0)?'↓':'=');
            echo '<span class="badge '.$cls.'">'.$lbl.' '.number_format($s['deltaCount']).' ('.number_format($s['pctCount'],2).'%)</span>';
          ?>
        </div>
      </div>
      <div class="card">
        <div class="label">فارق الإجمالي (سجل الموظفين − GOSI)</div>
        <div class="value">
          <?php
            $cls = ($s['deltaTotal']>0)?'up':(($s['deltaTotal']<0)?'down':'flat');
            $lbl = ($s['deltaTotal']>0)?'↑':(($s['deltaTotal']<0)?'↓':'=');
            echo '<span class="badge '.$cls.'">'.$lbl.' '.number_format($s['deltaTotal'],2).' ر.س ('.number_format($s['pctTotal'],2).'%)</span>';
          ?>
        </div>
      </div>
      <div class="card">
        <div class="label">إجمالي الفروقات في الرواتب (القيمة المطلقة)</div>
        <div class="value"><?php echo number_format($s['mismatchAbsDelta'],2); ?> ر.س</div>
      </div>
      <div class="card">
        <div class="label">ملاحظات</div>
        <div class="value">المقارنة تتم على رقم الهوية فقط.</div>
      </div>
    </div>

    <!-- 1) موظفون في GOSI وغير موجودين في EMP1 -->
    <div class="section-title">في التأمينات (GOSI) وغير موجودين في سجل الموظفين</div>
    <table class="print-table">
      <thead>
        <tr>
          <th>رقم الهوية</th>
          <th>الاسم (GOSI)</th>
          <th>أساسي</th>
          <th>بدل سكن</th>
          <th>بدل عمولة</th>
          <th>بدلات أخرى</th>
          <th>الإجمالي</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($b['onlyGosi'])): ?>
          <?php foreach ($b['onlyGosi'] as $r): ?>
            <tr>
              <td class="center"><?php echo htmlspecialchars($r['id_number']); ?></td>
              <td><?php echo htmlspecialchars($r['name']); ?></td>
              <td class="center"><?php echo number_format($r['basic'],2); ?></td>
              <td class="center"><?php echo number_format($r['housing'],2); ?></td>
              <td class="center"><?php echo number_format($r['commission'],2); ?></td>
              <td class="center"><?php echo number_format($r['other'],2); ?></td>
              <td class="center"><strong><?php echo number_format($r['total'],2); ?></strong></td>
            </tr>
          <?php endforeach; ?>
          <?php $t=$b['onlyGosiTotals']; ?>
          <tr>
            <th colspan="2" class="center">الإجمالي</th>
            <th class="center"><?php echo number_format($t['basic'],2); ?></th>
            <th class="center"><?php echo number_format($t['housing'],2); ?></th>
            <th class="center"><?php echo number_format($t['commission'],2); ?></th>
            <th class="center"><?php echo number_format($t['other'],2); ?></th>
            <th class="center"><?php echo number_format($t['total'],2); ?></th>
          </tr>
        <?php else: ?>
          <tr><td colspan="7" class="center">لا توجد فروقات في هذا القسم.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

    <!-- 2) موظفون في EMP1 وغير موجودين في GOSI -->
    <div class="section-title">في سجل الموظفين وغير موجودين في التأمينات (GOSI)</div>
    <table class="print-table">
      <thead>
        <tr>
          <th>رقم الهوية</th>
          <th>الاسم (EMP1)</th>
          <th>الرقم الوظيفي</th>
          <th>إجمالي الراتب</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($b['onlyEmp'])): ?>
          <?php
            $tot = 0; foreach ($b['onlyEmp'] as $r): $tot += floatval($r['total_salary']); ?>
            <tr>
              <td class="center"><?php echo htmlspecialchars($r['id_number']); ?></td>
              <td><?php echo htmlspecialchars($r['subscriber_name']); ?></td>
              <td class="center"><?php echo htmlspecialchars($r['employee_id']); ?></td>
              <td class="center"><strong><?php echo number_format($r['total_salary'],2); ?></strong></td>
            </tr>
          <?php endforeach; ?>
          <tr>
            <th colspan="3" class="center">إجمالي القسم</th>
            <th class="center"><?php echo number_format($tot,2); ?></th>
          </tr>
        <?php else: ?>
          <tr><td colspan="4" class="center">لا توجد فروقات في هذا القسم.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

    <!-- 3) فروقات الرواتب لمن هم موجودون في الجدولين -->
    <div class="section-title">فروقات الرواتب (موجود في الجدولين والقيم مختلفة)</div>
    <table class="print-table">
      <thead>
        <tr>
          <th>رقم الهوية</th>
          <th>الاسم (GOSI)</th>
          <th>الرقم الوظيفي</th>
          <th>إجمالي الراتب (GOSI)</th>
          <th>إجمالي الراتب (EMP1)</th>
          <th>الفرق (EMP1 − GOSI)</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($b['mismatch'])): ?>
          <?php $sumDiff = 0; foreach ($b['mismatch'] as $r): $sumDiff += $r['diff']; ?>
            <tr>
              <td class="center"><?php echo htmlspecialchars($r['id_number']); ?></td>
              <td><?php echo htmlspecialchars($r['name_gosi']); ?></td>
              <td class="center"><?php echo htmlspecialchars($r['employee_id']); ?></td>
              <td class="center"><?php echo number_format($r['total_gosi'],2); ?></td>
              <td class="center"><?php echo number_format($r['total_emp'],2); ?></td>
              <td class="center">
                <?php
                  $cls = ($r['diff']>0)?'up':(($r['diff']<0)?'down':'flat');
                  $lbl = ($r['diff']>0)?'↑':(($r['diff']<0)?'↓':'=');
                  echo '<span class="badge '.$cls.'">'.$lbl.' '.number_format($r['diff'],2).'</span>';
                ?>
              </td>
            </tr>
          <?php endforeach; ?>
          <tr>
            <th colspan="5" class="center">مجموع الفروقات</th>
            <th class="center"><?php echo number_format($sumDiff,2); ?></th>
          </tr>
        <?php else: ?>
          <tr><td colspan="6" class="center">لا توجد فروقات في الرواتب.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

  </div><!-- /.a4-sheet -->
<?php endforeach; ?>

<?php if (empty($blocks)): ?>
  <div class="a4-sheet">
    <div class="header">
      <div class="brand">
        <img src="<?php echo base_url('assets/imeges/m1.png'); ?>" alt="شعار">
        <h1>مقارنة GOSI مع سجل الموظفين</h1>
      </div>
      <div class="meta"><div class="date">التاريخ: <?php echo date('Y/m/d'); ?></div></div>
    </div>
    <div class="section-title">لا توجد بيانات</div>
    <div>اختر الشركة من الأعلى ثم اضغط «عرض» لبدء المقارنة.</div>
  </div>
<?php endif; ?>

</body>
</html>
