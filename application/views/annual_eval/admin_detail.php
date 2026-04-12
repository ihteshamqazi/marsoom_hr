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
    .diff-pos{color:#0a7a0a;font-weight:700}
    .diff-neg{color:#b00020;font-weight:700}
  </style>
</head>
<body>
<div class="container py-4">
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-1"><?= html_escape($title) ?></h4>
      <div class="text-muted">العام: <b><?= (int)$year ?></b></div>
    </div>
    <a class="btn btn-outline-secondary" href="<?= site_url('AnnualEvaluationAdmin?year='.(int)$year) ?>">رجوع</a>
  </div>

  <div class="card p-3 p-md-4 mb-3">
    <div class="row g-2">
      <div class="col-md-6">
        <div class="fw-bold"><?= html_escape($emp['emp_name']) ?> <span class="text-muted">(<?= html_escape($emp['emp_no']) ?>)</span></div>
        <div class="text-muted">القسم: <?= html_escape($emp['department'] ?? '-') ?> | النموذج: <?= (int)$emp['form_type'] ?></div>
      </div>
      <div class="col-md-6 text-end">
        <div class="fw-bold">المشرف: <?= html_escape($emp['supervisor_name'] ?? '-') ?></div>
        <div class="text-muted"><?= html_escape($emp['supervisor_emp_no'] ?? '') ?></div>
      </div>
    </div>
  </div>

  <?php
  $total_max = (float)($total_max ?? 120);
    $self = $self ?? null;
    $sup  = $sup  ?? null;
    $form_type = (int)($emp['form_type'] ?? 1);

    $get = function($arr, $k){ return $arr ? (float)($arr[$k] ?? 0) : 0.0; };
    $diff = function($k) use ($get, $self, $sup){
      return $get($sup,$k) - $get($self,$k);
    };
    $fmt = function($v){ return number_format((float)$v,2,'.',''); };
  ?>

  <div class="row g-3">
    <div class="col-lg-6">
      <div class="card p-3">
        <div class="fw-bold mb-2">تقييم الموظف (Self)</div>
        <?php if (!$self): ?>
          <div class="text-muted">لم يتم إدخال تقييم الموظف.</div>
        <?php else: ?>
          <div class="mb-2">
            <span class="badge bg-success"><?= $fmt($self['total_score']) ?></span>
            <span class="text-muted">/ <?= number_format($total_max,2,'.','') ?></span>
            <span class="ms-2 fw-bold"><?= html_escape($self['grade_label'] ?? '-') ?></span>
          </div>
          <div class="text-muted small">آخر تحديث: <?= html_escape($self['updated_at'] ?? $self['submitted_at'] ?? '-') ?></div>
        <?php endif; ?>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card p-3">
        <div class="fw-bold mb-2">تقييم المشرف</div>
        <?php if (!$sup): ?>
          <div class="text-muted">لم يتم إدخال تقييم المشرف.</div>
        <?php else: ?>
          <div class="mb-2">
            <span class="badge bg-primary"><?= $fmt($sup['total_score']) ?></span>
            <span class="text-muted">/ 120</span>
            <span class="ms-2 fw-bold"><?= html_escape($sup['grade_label'] ?? '-') ?></span>
          </div>
          <div class="text-muted small">آخر تحديث: <?= html_escape($sup['updated_at'] ?? $sup['submitted_at'] ?? '-') ?></div>
        <?php endif; ?>
      </div>
    </div>

    <div class="col-12">
      <div class="card p-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div class="fw-bold">تفاصيل المعايير + الفروقات (المشرف - الموظف)</div>
          <div class="text-muted small">
            الانضباط: <?= $fmt($discipline) ?> | قاعدة الدورات: <?= $fmt($courses_base) ?>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>المعيار</th>
                <th>Self</th>
                <th>Supervisor</th>
                <th>الفرق</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="fw-bold">الانضباط (20)</td>
                <td><?= $fmt($get($self,'discipline_score')) ?></td>
                <td><?= $fmt($get($sup,'discipline_score')) ?></td>
                <td class="text-muted">ثابت</td>
              </tr>

              <tr>
                <td class="fw-bold">السلوك العام</td>
                <td><?= $fmt($get($self,'behavior_score')) ?></td>
                <td><?= $fmt($get($sup,'behavior_score')) ?></td>
                <?php $d=$diff('behavior_score'); ?>
                <td class="<?= $d>=0?'diff-pos':'diff-neg' ?>"><?= ($d>=0?'+':'').$fmt($d) ?></td>
              </tr>

              <tr>
                <td class="fw-bold">تحقيق الأهداف</td>
                <td><?= $fmt($get($self,'goals_score')) ?></td>
                <td><?= $fmt($get($sup,'goals_score')) ?></td>
                <?php $d=$diff('goals_score'); ?>
                <td class="<?= $d>=0?'diff-pos':'diff-neg' ?>"><?= ($d>=0?'+':'').$fmt($d) ?></td>
              </tr>

              <?php if ($form_type === 1): ?>
                <tr>
                  <td class="fw-bold">التواصل والعمل الجماعي</td>
                  <td><?= $fmt($get($self,'teamwork_score')) ?></td>
                  <td><?= $fmt($get($sup,'teamwork_score')) ?></td>
                  <?php $d=$diff('teamwork_score'); ?>
                  <td class="<?= $d>=0?'diff-pos':'diff-neg' ?>"><?= ($d>=0?'+':'').$fmt($d) ?></td>
                </tr>

                <tr>
                  <td class="fw-bold">الحوكمة والالتزام</td>
                  <td><?= $fmt($get($self,'governance_score')) ?></td>
                  <td><?= $fmt($get($sup,'governance_score')) ?></td>
                  <?php $d=$diff('governance_score'); ?>
                  <td class="<?= $d>=0?'diff-pos':'diff-neg' ?>"><?= ($d>=0?'+':'').$fmt($d) ?></td>
                </tr>

                <tr>
                  <td class="fw-bold">الإنجاز وجودة العمل</td>
                  <td><?= $fmt($get($self,'quality_score')) ?></td>
                  <td><?= $fmt($get($sup,'quality_score')) ?></td>
                  <?php $d=$diff('quality_score'); ?>
                  <td class="<?= $d>=0?'diff-pos':'diff-neg' ?>"><?= ($d>=0?'+':'').$fmt($d) ?></td>
                </tr>

                <tr>
                  <td class="fw-bold">تطوير الذات</td>
                  <td><?= $fmt($get($self,'self_dev_score')) ?></td>
                  <td><?= $fmt($get($sup,'self_dev_score')) ?></td>
                  <?php $d=$diff('self_dev_score'); ?>
                  <td class="<?= $d>=0?'diff-pos':'diff-neg' ?>"><?= ($d>=0?'+':'').$fmt($d) ?></td>
                </tr>

              <?php else: ?>
                <tr>
                  <td class="fw-bold">تطوير القدرات والكوادر</td>
                  <td><?= $fmt($get($self,'people_dev_score')) ?></td>
                  <td><?= $fmt($get($sup,'people_dev_score')) ?></td>
                  <?php $d=$diff('people_dev_score'); ?>
                  <td class="<?= $d>=0?'diff-pos':'diff-neg' ?>"><?= ($d>=0?'+':'').$fmt($d) ?></td>
                </tr>

                <tr>
                  <td class="fw-bold">القيادة</td>
                  <td><?= $fmt($get($self,'leadership_score')) ?></td>
                  <td><?= $fmt($get($sup,'leadership_score')) ?></td>
                  <?php $d=$diff('leadership_score'); ?>
                  <td class="<?= $d>=0?'diff-pos':'diff-neg' ?>"><?= ($d>=0?'+':'').$fmt($d) ?></td>
                </tr>

                <tr>
                  <td class="fw-bold">الحوكمة والالتزام</td>
                  <td><?= $fmt($get($self,'governance_score')) ?></td>
                  <td><?= $fmt($get($sup,'governance_score')) ?></td>
                  <?php $d=$diff('governance_score'); ?>
                  <td class="<?= $d>=0?'diff-pos':'diff-neg' ?>"><?= ($d>=0?'+':'').$fmt($d) ?></td>
                </tr>

                <tr>
                  <td class="fw-bold">المعيار الاستثنائي</td>
                  <td><?= $fmt($get($self,'exceptional_score')) ?></td>
                  <td><?= $fmt($get($sup,'exceptional_score')) ?></td>
                  <?php $d=$diff('exceptional_score'); ?>
                  <td class="<?= $d>=0?'diff-pos':'diff-neg' ?>"><?= ($d>=0?'+':'').$fmt($d) ?></td>
                </tr>
              <?php endif; ?>

              <tr class="table-light">
                <td class="fw-bold">الإجمالي</td>
                <td><?= $fmt($get($self,'total_score')) ?></td>
                <td><?= $fmt($get($sup,'total_score')) ?></td>
                <?php $d=($get($sup,'total_score') - $get($self,'total_score')); ?>
                <td class="<?= $d>=0?'diff-pos':'diff-neg' ?>"><?= ($d>=0?'+':'').$fmt($d) ?></td>
              </tr>

            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>

</div>
</body>
</html>
