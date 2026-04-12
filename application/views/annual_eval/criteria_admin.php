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
    :root{
      --marsom-blue:#001f3f;
      --marsom-orange:#FF8C00;
      --bg:#f4f6f9;
    }
    body{font-family:'Tajawal',sans-serif;background:var(--bg)}
    .shell{max-width:1400px;margin:0 auto;padding:20px 12px}
    .cardx{
      background:#fff;border:1px solid rgba(0,0,0,.06);
      border-radius:18px;box-shadow:0 10px 25px rgba(0,0,0,.06);
      padding:16px;margin-top:14px
    }
    .topbar{
      background:#fff;border:1px solid rgba(0,0,0,.06);
      border-radius:18px;box-shadow:0 10px 25px rgba(0,0,0,.06);
      padding:14px 16px;display:flex;gap:10px;align-items:center;justify-content:space-between;flex-wrap:wrap
    }
    .title{font-size:20px;font-weight:800;color:var(--marsom-blue);margin:0}
    .crit-box{
      border:1px solid #e9edf3;border-radius:16px;padding:14px;margin-bottom:16px;background:#fcfdff;
    }
    .section-title{
      margin:0 0 12px;padding:8px 10px;background:#f7f9fc;border-right:4px solid var(--marsom-orange);
      border-radius:10px;font-weight:800
    }
    .part-box{
      border:1px dashed #d7dde6;border-radius:14px;padding:12px;background:#fff;margin-top:10px;
    }
    .muted{color:#666;font-size:12px}
    .actions{display:flex;gap:8px;flex-wrap:wrap}
    .btn-marsom{
      background:#001f3f;border-color:#001f3f;color:#fff;font-weight:800;
    }
    .btn-marsom:hover{background:#001733;border-color:#001733;color:#fff}
  </style>
</head>
<body>
<div class="shell">

  <div class="topbar">
    <div>
      <h1 class="title"><?= html_escape($title) ?></h1>
      <div class="muted">هذه الشاشة خاصة بالأدمن لتعديل المعايير والبنود والشرح والأمثلة والدرجات.</div>
    </div>

    <div class="actions">
      <a href="<?= site_url('AnnualEvaluationAdmin') ?>" class="btn btn-outline-secondary">رجوع</a>
    </div>
  </div>

  <?php if (!empty($flash)): ?>
    <div class="cardx">
      <div class="alert alert-info mb-0"><?= html_escape($flash) ?></div>
    </div>
  <?php endif; ?>

  <div class="cardx">
    <form method="get" action="<?= site_url('AnnualEvaluationCriteriaAdmin') ?>" class="row g-2 align-items-end">
      <div class="col-md-4">
        <label class="form-label fw-bold">اختر النموذج</label>
        <select name="form_type" class="form-select">
          <?php foreach(($forms ?? []) as $f): ?>
            <option value="<?= (int)$f['form_type'] ?>" <?= ((int)$form_type === (int)$f['form_type']) ? 'selected' : '' ?>>
              <?= html_escape($f['form_name']) ?> (<?= (int)$f['form_type'] ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-8">
        <button class="btn btn-primary">عرض</button>
        <a class="btn btn-success" href="<?= site_url('AnnualEvaluationCriteriaAdmin/add_criterion?form_type='.(int)$form_type) ?>">إضافة معيار جديد</a>
      </div>
    </form>
  </div>

  <div class="cardx">
    <form method="post" action="<?= site_url('AnnualEvaluationCriteriaAdmin/save') ?>">
      <input type="hidden" name="form_type" value="<?= (int)$form_type ?>">

      <?php if (empty($criteria)): ?>
        <div class="alert alert-warning mb-0">لا توجد معايير لهذا النموذج.</div>
      <?php else: ?>

        <?php foreach(($criteria ?? []) as $c): ?>
          <div class="crit-box">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
              <div class="section-title mb-0">
                المعيار #<?= (int)$c['id'] ?>
              </div>
              <div class="actions">
                <a class="btn btn-sm btn-outline-success" href="<?= site_url('AnnualEvaluationCriteriaAdmin/add_part/'.(int)$c['id'].'?form_type='.(int)$form_type) ?>">إضافة بند</a>
                <a class="btn btn-sm btn-outline-danger"
                   onclick="return confirm('هل أنت متأكد من حذف هذا المعيار؟');"
                   href="<?= site_url('AnnualEvaluationCriteriaAdmin/delete_criterion/'.(int)$c['id'].'?form_type='.(int)$form_type) ?>">
                   حذف المعيار
                </a>
              </div>
            </div>

            <div class="row g-3">
              <div class="col-md-3">
                <label class="form-label fw-bold">اسم المعيار</label>
                <input type="text" class="form-control" name="criteria[<?= (int)$c['id'] ?>][criterion_name]" value="<?= html_escape($c['criterion_name']) ?>">
              </div>

              <div class="col-md-2">
                <label class="form-label fw-bold">مفتاح المعيار</label>
                <input type="text" class="form-control" name="criteria[<?= (int)$c['id'] ?>][criterion_key]" value="<?= html_escape($c['criterion_key']) ?>">
              </div>

              <div class="col-md-2">
                <label class="form-label fw-bold">عمود الدرجة</label>
                <input type="text" class="form-control" name="criteria[<?= (int)$c['id'] ?>][score_column]" value="<?= html_escape($c['score_column']) ?>">
              </div>

              <div class="col-md-2">
                <label class="form-label fw-bold">الدرجة العليا</label>
                <input type="number" step="0.01" class="form-control" name="criteria[<?= (int)$c['id'] ?>][max_score]" value="<?= (float)$c['max_score'] ?>">
              </div>

              <div class="col-md-1">
                <label class="form-label fw-bold">الترتيب</label>
                <input type="number" class="form-control" name="criteria[<?= (int)$c['id'] ?>][sort_order]" value="<?= (int)$c['sort_order'] ?>">
              </div>

              <div class="col-md-2">
                <label class="form-label fw-bold d-block">خيارات</label>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="criteria[<?= (int)$c['id'] ?>][is_readonly]" value="1" <?= !empty($c['is_readonly']) ? 'checked' : '' ?>>
                  <label class="form-check-label">قراءة فقط</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="criteria[<?= (int)$c['id'] ?>][min_from_courses]" value="1" <?= !empty($c['min_from_courses']) ? 'checked' : '' ?>>
                  <label class="form-check-label">حد أدنى من الدورات</label>
                </div>
              </div>

              <div class="col-12">
                <label class="form-label fw-bold">الوصف / الملاحظات / الأمثلة</label>
                <textarea class="form-control" rows="6" name="criteria[<?= (int)$c['id'] ?>][criterion_desc]"><?= html_escape($c['criterion_desc']) ?></textarea>
              </div>
            </div>

            <hr>

            <div class="section-title">البنود الفرعية</div>

            <?php if (empty($c['parts'])): ?>
              <div class="text-muted">لا توجد بنود فرعية لهذا المعيار.</div>
            <?php else: ?>
              <?php foreach(($c['parts'] ?? []) as $p): ?>
                <div class="part-box">
                  <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                      <label class="form-label fw-bold">اسم البند</label>
                      <input type="text" class="form-control" name="criteria[<?= (int)$c['id'] ?>][parts][<?= (int)$p['id'] ?>][part_name]" value="<?= html_escape($p['part_name']) ?>">
                    </div>

                    <div class="col-md-3">
                      <label class="form-label fw-bold">مفتاح البند</label>
                      <input type="text" class="form-control" name="criteria[<?= (int)$c['id'] ?>][parts][<?= (int)$p['id'] ?>][part_key]" value="<?= html_escape($p['part_key']) ?>">
                    </div>

                     <div class="col-md-2">
  <label class="form-label fw-bold">الدرجة</label>
  <input type="number" step="0.01" class="form-control"
         name="criteria[<?= (int)$c['id'] ?>][parts][<?= (int)$p['id'] ?>][max_score]"
         value="<?= (float)$p['max_score'] ?>">
</div>

<div class="col-md-3">
  <label class="form-label fw-bold">إلزام الملاحظة</label>
  <select class="form-select"
          name="criteria[<?= (int)$c['id'] ?>][parts][<?= (int)$p['id'] ?>][note_rule]">
    <option value="optional" <?= (($p['note_rule'] ?? '') === 'optional') ? 'selected' : '' ?>>
      اختيارية
    </option>
    <option value="required_on_less" <?= (($p['note_rule'] ?? 'required_on_less') === 'required_on_less') ? 'selected' : '' ?>>
      إجباري عند تخفيض الدرجة
    </option>
    <option value="required_on_positive" <?= (($p['note_rule'] ?? '') === 'required_on_positive') ? 'selected' : '' ?>>
      إجباري عند إضافة أي درجة
    </option>
  </select>
</div>

<div class="col-md-1">
  <label class="form-label fw-bold">الترتيب</label>
  <input type="number" class="form-control"
         name="criteria[<?= (int)$c['id'] ?>][parts][<?= (int)$p['id'] ?>][sort_order]"
         value="<?= (int)$p['sort_order'] ?>">
</div>

                    <div class="col-md-2">
                      <a class="btn btn-outline-danger w-100"
                         onclick="return confirm('هل أنت متأكد من حذف هذا البند؟');"
                         href="<?= site_url('AnnualEvaluationCriteriaAdmin/delete_part/'.(int)$p['id'].'?form_type='.(int)$form_type) ?>">
                         حذف البند
                      </a>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>

          </div>
        <?php endforeach; ?>

        <div class="d-flex justify-content-end gap-2 mt-3">
          <button type="submit" class="btn btn-marsom px-4">حفظ جميع التعديلات</button>
        </div>

      <?php endif; ?>
    </form>
  </div>

</div>
</body>
</html>