<?php /* application/views/templateo/employee_eval_a4_v5.php */ ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>التقييم السنوي للموظف</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- خطوط -->
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+128&display=swap" rel="stylesheet">

<style>
  @page { size: A4; margin: 12mm 12mm 14mm 12mm; }
  html, body { font-family:'Tajawal',system-ui,-apple-system,"Segoe UI",Arial,sans-serif; background:#f4f6f9; color:#111; }
  .a4-sheet { width:210mm; min-height:297mm; margin:10mm auto; background:#fff; box-shadow:0 6px 24px rgba(0,0,0,.08); padding:14mm; }

  /* أزرار أعلى الصفحة */
  .no-print { display:flex; flex-wrap:wrap; gap:.5rem; justify-content:center; margin:12px auto; }
  .btn { border:1px solid #001f3f; color:#001f3f; background:#fff; padding:8px 14px; border-radius:8px; font-weight:700; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:8px; }
  .btn:hover { background:#001f3f; color:#fff; }
  .btn-secondary { border-color:#999; color:#444; }
  .btn-secondary:hover { background:#444; color:#fff; }
  .btn-submit { border-color:#FF8C00; color:#FF8C00; }
  .btn-submit:hover { background:#FF8C00; color:#fff; }

  /* رأس */
  .header { display:flex; align-items:center; justify-content:space-between; gap:16px; padding-bottom:10px; border-bottom:2px solid #001f3f; margin-bottom:14px; }
  .brand { display:flex; align-items:center; gap:10px; }
  .brand img { height:46px; width:auto; }
  .brand h1 { font-size:18px; margin:0; font-weight:800; color:#001f3f; letter-spacing:.2px; }
  .meta { text-align:left; font-size:12px; color:#444; }
  .meta .date { font-weight:700; }

  /* عناوين أقسام */
  .section-title { margin:14px 0 8px; padding:8px 10px; background:#f7f9fc; border-right:4px solid #FF8C00; font-weight:800; }

  /* جداول مطبوعة */
  .print-table { width:100%; border-collapse:collapse; margin-top:6px; }
  .print-table th, .print-table td { border:1px solid #d9dee3; padding:6px 8px; font-size:13px; vertical-align:middle; }
  .print-table th { background:#001f3f; color:#fff; font-weight:700; text-align:center; }
  .center { text-align:center; }

  /* بطاقات الملخص */
  .summary { display:grid; grid-template-columns: repeat(4, 1fr); gap:10px; margin-top:10px; }
  .card { border:1px solid #e7ebf0; border-radius:10px; padding:10px; background:#fafbff; }
  .card .label { font-size:12px; color:#555; }
  .card .value { font-size:18px; font-weight:800; }

  .big-badge { display:inline-block; padding:.4rem .8rem; border-radius:10px; font-weight:800; font-size:14px; border:1px solid rgba(0,0,0,.08); background:#e6f4ea; color:#137333; }

  /* صندوق زجاجي */
  .glass { background: rgba(255,255,255,0.22); border:1px solid rgba(0,0,0,0.08); border-radius:12px; padding:10px; box-shadow: inset 0 1px 4px rgba(0,0,0,.06); }

  @media print {
    body { background:#fff; }
    .a4-sheet { margin:0; width:auto; min-height:auto; box-shadow:none; padding:0; }
    .no-print { display:none !important; }
    a { color:inherit; text-decoration:none; }
  }
</style>
</head>
<body>

<!-- أزرار -->
<div class="no-print">
  <a class="btn btn-submit" href="javascript:window.print()">طباعة</a>
  <a class="btn btn-secondary" href="<?php echo site_url('users/user_report'); ?>">الرئيسية</a>
  <a class="btn" href="javascript:history.back()">رجوع</a>
</div>

<div class="a4-sheet">
  <!-- رأس -->
  <div class="header">
    <div class="brand">
      <img src="<?php echo base_url('assets/imeges/m1.PNG'); ?>" alt="شعار">
      <h1>التقييم السنوي للموظف</h1>
    </div>
    <div class="meta">
      <div class="date">التاريخ: <?php echo date('Y/m/d'); ?></div>
      <?php if(!empty($ev_done['w1'])): ?>
        <div>الرقم الوظيفي: <strong><?php echo htmlspecialchars($ev_done['w1']); ?></strong></div>
      <?php endif; ?>
    </div>
  </div>

  <!-- بطاقات ملخص -->
  <?php
    $total  = (float)($ev_done['w15'] ?? 0);           // الإجمالي
    $totalX = (float)($ev_done['w16'] ?? $total);      // + المعيار الاستثنائي
  ?>
  <div class="summary">
    <div class="card">
      <div class="label">الاسم</div>
      <div class="value"><?php echo htmlspecialchars($ev_done['w2'] ?? ''); ?></div>
    </div>
    <div class="card">
      <div class="label">المسمى الوظيفي</div>
      <div class="value"><?php echo htmlspecialchars($ev_done['w4'] ?? ''); ?></div>
    </div>
    <div class="card">
      <div class="label">المسؤول المباشر</div>
      <div class="value"><?php echo htmlspecialchars($ev_done['w3'] ?? ''); ?></div>
    </div>
    <div class="card">
      <div class="label">التقييم الكلي</div>
      <div class="value"><span class="big-badge">% <?php echo number_format($totalX,2); ?></span></div>
    </div>
  </div>

  <!-- معلومات الموظف -->
  <div class="section-title">معلومات الموظف</div>
  <table class="print-table">
    <thead>
      <tr>
        <th>المسؤول المباشر</th>
        <th>المسمى الوظيفي</th>
        <th>الاسم</th>
        <th>الرقم الوظيفي</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="center"><?php echo htmlspecialchars($ev_done['w3'] ?? ''); ?></td>
        <td class="center"><?php echo htmlspecialchars($ev_done['w4'] ?? ''); ?></td>
        <td class="center"><?php echo htmlspecialchars($ev_done['w2'] ?? ''); ?></td>
        <td class="center"><?php echo htmlspecialchars($ev_done['w1'] ?? ''); ?></td>
      </tr>
    </tbody>
  </table>

  <table class="print-table">
    <thead>
      <tr>
        <th>سنة التقييم</th>
        <th>تاريخ المباشرة</th>
        <th>الإدارة / القسم</th>
        <th>الفئة</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="center"><?php echo htmlspecialchars($ev_done['year'] ?? '2025'); ?></td>
        <td class="center"><?php echo htmlspecialchars($ev_done['w6'] ?? ''); ?></td>
        <td class="center"><?php echo htmlspecialchars($ev_done['w5'] ?? ''); ?></td>
        <td class="center">
          <?php
            if (($ev_emp22['m11'] ?? '') == "1")      echo "محصل ديون";
            elseif (($ev_emp22['m11'] ?? '') == "2") echo "مشرف تحصيل ديون";
            elseif (($ev_emp22['m11'] ?? '') == "3") echo "مدير تحصيل";
            elseif (($ev_emp22['m11'] ?? '') == "4") echo "إداري";
            elseif (($ev_emp22['m11'] ?? '') == "5") echo "مشرف إدارة";
            elseif (($ev_emp22['m11'] ?? '') == "6") echo "مدير إدارة";
          ?>
        </td>
      </tr>
    </tbody>
  </table>

  <!-- معايير التقييم (هذه النسخة) -->
  <?php
    $p_self   = (float)($ev_done['w12'] ?? 0); // تطوير الذات 10%
    $p_admin  = (float)($ev_done['w23'] ?? 0); // المهارات الإدارية 15%
    $p_tasks  = (float)($ev_done['w21'] ?? 0); // المهام 50%
    $p_comm   = (float)($ev_done['w9']  ?? 0); // التواصل والتعاون 20%
    $p_attend = (float)($ev_done['w14'] ?? 0); // الحضور والانصراف 15%
  ?>
  <div class="section-title">معايير التقييم</div>
  <table class="print-table">
    <thead>
      <tr>
        <th>تطوير الذات (معيار استثنائي) 10%</th>
        <th>المهارات الإدارية 15%</th>
        <th>المهام الوظيفية 50%</th>
        <th>التواصل والتعاون 20%</th>
        <th>الحضور والانصراف 15%</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="center">% <?php echo number_format($p_self,2);   ?></td>
        <td class="center">% <?php echo number_format($p_admin,2);  ?></td>
        <td class="center">% <?php echo number_format($p_tasks,2);  ?></td>
        <td class="center">% <?php echo number_format($p_comm,2);   ?></td>
        <td class="center">% <?php echo number_format($p_attend,2); ?></td>
      </tr>
    </tbody>
    <tfoot>
      <tr>
        <th colspan="3" class="center">الإجمالي</th>
        <th colspan="2" class="center">% <?php echo number_format((float)($ev_done['w15'] ?? ($p_self+$p_admin+$p_tasks+$p_comm+$p_attend)),2); ?></th>
      </tr>
      <tr>
        <th colspan="3" class="center">الإجمالي + المعيار الاستثنائي</th>
        <th colspan="2" class="center">% <?php echo number_format($totalX,2); ?></th>
      </tr>
    </tfoot>
  </table>

  <!-- ملاحظات -->
  <div class="section-title">ملاحظات</div>
  <div class="glass">
    <pre style="font-family:'Tajawal',sans-serif; font-size:14px; direction:rtl; white-space:pre-wrap; margin:0;">
<?php echo htmlspecialchars($ev_done['w20'] ?? ''); ?>
    </pre>
  </div>

  <!-- الاعتمادات -->
  <div class="section-title">الاعتمادات</div>
  <table class="print-table">
    <thead>
      <tr>
        <th>اعتماد العضو المنتدب</th>
        <th>مدير إدارة الموارد البشرية</th>
        <th>اعتماد المسؤول المباشر</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="center">سالم العجمي</td>
        <td class="center">منصور رجب</td>
        <td class="center"><?php echo htmlspecialchars($ev_done['w3'] ?? ''); ?></td>
      </tr>
    </tbody>
  </table>

  <!-- باركود للرقم الوظيفي (اختياري) -->
  <?php if(!empty($ev_done['w1'])): ?>
  <div style="margin-top:8px; text-align:center; color:#555;">
    <div style="font-family:'Libre Barcode 128', cursive; font-size:48px; letter-spacing:2px;">
      *<?php echo preg_replace('/[^0-9A-Za-z]/','',$ev_done['w1']); ?>*
    </div>
    <div style="font-size:12px;">باركود داخلي للرقم الوظيفي</div>
  </div>
  <?php endif; ?>

</div><!-- /.a4-sheet -->

</body>
</html>
