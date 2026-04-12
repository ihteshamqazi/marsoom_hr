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
  </style>
</head>
<body>
<div class="container py-4">

   <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
  <div class="d-flex align-items-center gap-2">
    <button onclick="history.back()" class="btn btn-light border">
      ← رجوع
    </button>
    <h4 class="mb-0"><?= html_escape($title) ?></h4>
  </div>

  <div class="text-muted">العام: <b>2025</b></div>
</div>

  <?php if (!empty($flash)): ?>
    <div class="alert alert-info"><?= html_escape($flash) ?></div>
  <?php endif; ?>

  <div class="card p-3">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>الموظف</th>
            <th>الرقم الوظيفي</th>
            <th>القسم</th>
            <th>النموذج</th>
            <th class="text-end">الإجراء</th>
          </tr>
        </thead>
        <tbody>
        <?php if (empty($team)): ?>
          <tr><td colspan="5" class="text-center text-muted py-4">لا يوجد موظفون تابعون لك في هذا العام.</td></tr>
        <?php else: foreach($team as $r): ?>
          <tr>
            <td class="fw-bold"><?= html_escape($r['emp_name']) ?></td>
            <td><?= html_escape($r['emp_no']) ?></td>
            <td><?= html_escape($r['department'] ?? '-') ?></td>
            <td><span class="badge bg-secondary"><?= (int)$r['form_type'] ?></span></td>
            <td class="text-end">
              <a class="btn btn-sm btn-primary"
                 href="<?= site_url('AnnualEvaluationSupervisor/form/'.rawurlencode($r['emp_no']).'?year='.(int)$year) ?>">
                 تقييم الموظف
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
