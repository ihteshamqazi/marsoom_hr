<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= html_escape($title ?? 'طباعة الحوافز السنوية') ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;600;800&display=swap" rel="stylesheet">
  <style>
    :root{
      --text:#111;
      --muted:#555;
      --line:#e6e6e6;
      --head:#f6f7fb;
      --brand:#001f3f;  /* مرسوم */
      --brand2:#FF8C00; /* مرسوم */
    }
    body{font-family:Tajawal,sans-serif;margin:0;background:#fff;color:var(--text)}
    .page{padding:22px 26px}
    .topbar{
      display:flex;align-items:center;justify-content:space-between;gap:14px;
      border-bottom:2px solid #f0f0f0;padding-bottom:10px;margin-bottom:14px
    }
    .brandBox{display:flex;align-items:center;gap:12px}
    .logo{height:52px}
    .title{font-weight:800;font-size:18px;color:var(--brand)}
    .subtitle{color:var(--muted);font-size:13px;margin-top:4px}
    .meta{
      display:flex;flex-wrap:wrap;gap:14px;
      font-size:13px;color:var(--muted);margin:8px 0 12px
    }
    .meta b{color:var(--text)}
    .badge{
      display:inline-block;padding:3px 10px;border-radius:999px;
      border:1px solid var(--line);background:#fff;font-size:12px
    }

    table{width:100%;border-collapse:collapse;margin-top:10px}
    th,td{border:1px solid var(--line);padding:8px 8px;font-size:12.5px;vertical-align:middle}
    th{background:var(--head);font-weight:800}
    td strong{font-weight:800}
    .right{ text-align:right; }
    .center{ text-align:center; }
    .amount{ font-weight:800; }
    .footer{
      margin-top:14px;border-top:1px dashed var(--line);padding-top:10px;
      font-size:12px;color:var(--muted);display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap
    }
    .btn{
      font-family:Tajawal;cursor:pointer;border:0;border-radius:10px;
      padding:10px 14px;background:var(--brand);color:#fff
    }
    .btn2{background:var(--brand2);color:#111}
    .no-print{margin-bottom:12px;display:flex;gap:10px;flex-wrap:wrap}
    @media print{
      .no-print{display:none}
      .page{padding:0}
    }
  </style>
</head>
<body>
<div class="page">

  <div class="no-print">
    <button class="btn" onclick="window.print()">طباعة</button>
    <button class="btn btn2" onclick="window.close()">إغلاق</button>
  </div>

  <div class="topbar">
    <div>
      <div class="title">تقرير الحوافز السنوية — شركة مرسوم</div>
      <div class="subtitle">
        <?= html_escape($batch['batch_name'] ?? '') ?> — سنة <?= html_escape($batch['batch_year'] ?? '') ?>
        <span class="badge">الحالة: <?= html_escape($batch['status'] ?? '') ?></span>
      </div>
    </div>

    <div class="brandBox">
      <img class="logo" src="<?= base_url('assets/images/marsoom-logo.png') ?>" alt="Marsoom" onerror="this.style.display='none'">
    </div>
  </div>

  <div class="meta">
    <div>الميزانية: <b><?= html_escape($batch['budget_total'] ?? '') ?></b></div>
    <div>عدد الموظفين: <b><?= isset($rows) ? count($rows) : 0 ?></b></div>
    <div>تاريخ الطباعة: <b><?= date('Y-m-d H:i') ?></b></div>
  </div>

  <table>
    <thead>
      <tr>
        <th class="center" style="width:50px">#</th>
        <th>الموظف</th>
        <th class="center" style="width:120px">رقم وظيفي</th>
        <th class="center" style="width:140px">قاعدة الحساب</th>
        <th class="center" style="width:120px">التزايد</th>
        <th class="center" style="width:150px">قيمة الحافز</th>
      </tr>
    </thead>
    <tbody>
      <?php
        $i = 1;
        $sum = 0.0;
        foreach(($rows ?? []) as $r):
          $emp_name = (string)($r['subscriber_name'] ?? '');
          $prof = (string)($r['profession'] ?? '');
          $full = $emp_name . ($prof !== '' ? ' ('.$prof.')' : '');

          $inc = (string)($r['incentive_amount'] ?? '0');
          $inc_num = (float)str_replace([',',' '], '', $inc);
          $sum += $inc_num;
      ?>
        <tr>
          <td class="center"><?= $i++ ?></td>
          <td>
            <strong><?= html_escape($full) ?></strong>
            <div style="color:#666;font-size:11.5px;margin-top:2px;">
              هوية: <?= html_escape($r['id_number'] ?? '') ?>
            </div>
          </td>
          <td class="center"><?= html_escape($r['employee_id'] ?? '') ?></td>
          <td class="center"><?= html_escape($r['calc_base_amount'] ?? '') ?></td>
          <td class="center"><?= html_escape($r['multiplier'] ?? '') ?></td>
          <td class="center amount"><?= html_escape($r['incentive_amount'] ?? '') ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="footer">
    <div>إجمالي الحوافز المصروفة (حسب القائمة): <b><?= number_format($sum, 2, '.', ',') ?></b></div>
    <div>ملاحظة: التقرير مرتب من الأعلى حافز إلى الأقل.</div>
  </div>

</div>
</body>
</html>
