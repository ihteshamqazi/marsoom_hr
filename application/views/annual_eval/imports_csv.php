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
    code{direction:ltr;display:inline-block}
  </style>
</head>
<body>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><?= html_escape($title) ?></h4>
    <div class="text-muted">السنة: <b><?= (int)$year ?></b></div>
  </div>

  <?php if (!empty($flash)): ?>
    <div class="alert alert-info"><?= html_escape($flash) ?></div>
  <?php endif; ?>

  <div class="card p-3 mb-3">
    <div class="fw-bold mb-2">أعمدة Master.csv</div>
    <div class="text-muted small">
      <code>emp_no, emp_name, department, job_title, hire_date, supervisor_emp_no, supervisor_name, form_type, role_type</code>
    </div>
    <form class="row g-2 mt-2" action="<?= site_url('AnnualEvaluationImports/upload_master') ?>" method="post" enctype="multipart/form-data">
      <input type="hidden" name="year" value="<?= (int)$year ?>">
      <div class="col-md-8"><input class="form-control" type="file" name="csv_file" accept=".csv" required></div>
      <div class="col-md-4"><button class="btn btn-primary w-100">رفع Master</button></div>
    </form>
  </div>

  <div class="card p-3 mb-3">
    <div class="fw-bold mb-2">أعمدة discipline.csv</div>
    <div class="text-muted small"><code>emp_no, emp_name, score</code></div>
    <form class="row g-2 mt-2" action="<?= site_url('AnnualEvaluationImports/upload_discipline') ?>" method="post" enctype="multipart/form-data">
      <input type="hidden" name="year" value="<?= (int)$year ?>">
      <div class="col-md-8"><input class="form-control" type="file" name="csv_file" accept=".csv" required></div>
      <div class="col-md-4"><button class="btn btn-primary w-100">رفع الانضباط</button></div>
    </form>
  </div>

  <div class="card p-3">
    <div class="fw-bold mb-2">أعمدة courses.csv</div>
    <div class="text-muted small"><code>emp_no, emp_name, base_score</code></div>
    <form class="row g-2 mt-2" action="<?= site_url('AnnualEvaluationImports/upload_courses') ?>" method="post" enctype="multipart/form-data">
      <input type="hidden" name="year" value="<?= (int)$year ?>">
      <div class="col-md-8"><input class="form-control" type="file" name="csv_file" accept=".csv" required></div>
      <div class="col-md-4"><button class="btn btn-primary w-100">رفع الدورات</button></div>
    </form>
  </div>

</div>
</body>
</html>
