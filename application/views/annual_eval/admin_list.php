<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= html_escape($title ?? '') ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
  <style>
    body{font-family:'Tajawal',sans-serif;background:#f6f8fc}
    .card{border:0;border-radius:18px;box-shadow:0 10px 25px rgba(0,0,0,.07)}
    .table td,.table th{vertical-align:middle}
    .pill{border-radius:999px}
  </style>
</head>
<body>
<div class="container py-4">
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><?= html_escape($title) ?></h4>
    <div class="text-muted">العام: <b>2025</b></div>
  </div>

  <?php if (!empty($flash)): ?>
    <div class="alert alert-info"><?= html_escape($flash) ?></div>
  <?php endif; ?>

  <div class="card p-3 mb-3">
    <form class="row g-2 align-items-end" method="get" action="<?= site_url('AnnualEvaluationAdmin') ?>">
      <div class="col-md-3">
        <label class="form-label">السنة</label>
        <input type="number" class="form-control" name="year" value="<?= (int)$year ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">القسم (اختياري)</label>
        <input type="text" class="form-control" name="department" value="<?= html_escape($department ?? '') ?>">
      </div>
      <div class="col-md-5 d-flex gap-2">
        <button class="btn btn-primary px-4" type="submit">تطبيق</button>
      </div>
    </form>
  </div>

  <div class="card p-3">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-light">
          <tr>
            <th>الموظف</th>
            <th>القسم</th>
            <th>المشرف</th>
            <th>Self</th>
            <th>Supervisor</th>
            <th class="text-end">تفاصيل</th>
          </tr>
        </thead>
        <tbody>
        <?php if (empty($rows)): ?>
          <tr><td colspan="6" class="text-center text-muted py-4">لا توجد بيانات.</td></tr>
        <?php else: foreach($rows as $r): ?>
          <tr>
            <td class="fw-bold"><?= html_escape($r['emp_name']) ?><div class="text-muted small"><?= html_escape($r['emp_no']) ?> | نموذج <?= (int)$r['form_type'] ?></div></td>
            <td><?= html_escape($r['department'] ?? '-') ?></td>
            <td><?= html_escape($r['supervisor_name'] ?? '-') ?><div class="text-muted small"><?= html_escape($r['supervisor_emp_no'] ?? '') ?></div></td>

            <td>
              <?php if (!empty($r['self_total'])): ?>
                <span class="badge bg-success pill"><?= number_format((float)$r['self_total'],2,'.','') ?></span>
                <div class="text-muted small"><?= html_escape($r['self_grade'] ?? '-') ?></div>
              <?php else: ?>
                <span class="badge bg-secondary pill">غير مُدخل</span>
              <?php endif; ?>
            </td>

            <td>
              <?php if (!empty($r['sup_total'])): ?>
                <span class="badge bg-primary pill"><?= number_format((float)$r['sup_total'],2,'.','') ?></span>
                <div class="text-muted small"><?= html_escape($r['sup_grade'] ?? '-') ?></div>
              <?php else: ?>
                <span class="badge bg-secondary pill">غير مُدخل</span>
              <?php endif; ?>
            </td>

             <td class="text-end">
              <a class="btn btn-sm btn-outline-primary"
                 target="_blank"
                 href="<?= site_url('AnnualEvaluation/print_a4/'.rawurlencode($r['emp_no']).'?year='.(int)$year) ?>">
                 عرض
              </a>
            </td>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>
</body>
</html>
