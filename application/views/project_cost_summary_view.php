<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= html_escape($title ?? 'تقرير تكلفة المشاريع') ?></title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
  <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <style>
    :root{
      --primary-blue:#001f3f;
      --primary-orange:#FF8C00;
      --glass-bg: rgba(255,255,255,.06);
      --glass-border: rgba(255,255,255,.12);
      --card-bg: rgba(255,255,255,.10);
      --text:#0b1220;
      --muted:#667085;
      --radius: 22px;
      --shadow: 0 14px 34px rgba(0,0,0,.14);
    }
    body{
      font-family:'Tajawal',sans-serif;
      background: linear-gradient(135deg, #0a1929 0%, #001f3f 40%, #1a1a2e 100%);
      min-height:100vh;
    }
    .wrap{max-width:1400px;margin:22px auto;padding:0 14px;}
    .header{
      background: rgba(255,255,255,.06);
      border:1px solid rgba(255,255,255,.12);
      border-radius: 26px;
      backdrop-filter: blur(18px);
      box-shadow: 0 10px 26px rgba(0,0,0,.20);
      padding: 16px 18px;
      color:#fff;
      display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;
    }
    .header h1{
      font-family:'El Messiri',serif;
      margin:0;
      font-size:1.5rem;
      font-weight:900;
      background: linear-gradient(135deg,#fff,#ffd166);
      -webkit-background-clip:text;-webkit-text-fill-color:transparent;
    }
    .header .sub{color:rgba(255,255,255,.72);font-weight:700}
    .btn-mr{
      border:1px solid rgba(255,255,255,.20);
      background: rgba(255,255,255,.10);
      color:#fff;
      border-radius: 14px;
      padding: 10px 12px;
      font-weight:900;
      text-decoration:none;
      display:inline-flex;align-items:center;gap:10px;
      transition:.2s;
    }
    .btn-mr:hover{transform:translateY(-2px);border-color:rgba(255,140,0,.55);box-shadow:0 10px 22px rgba(255,140,0,.14);color:#fff;}
    .btn-mr.primary{background: linear-gradient(135deg, rgba(255,140,0,.30), rgba(255,140,0,.12)); border-color: rgba(255,140,0,.35);}

    /* ✅ Desktop table (واضح) */
    .panel{
      margin-top:14px;
      background: rgba(255,255,255,.08);
      border:1px solid rgba(255,255,255,.14);
      border-radius: 26px;
      backdrop-filter: blur(18px);
      box-shadow: 0 12px 30px rgba(0,0,0,.18);
      padding: 14px;
    }
    .table{
      background: rgba(255,255,255,.92);
      border-radius: 18px;
      overflow:hidden;
      margin:0;
    }
    .table thead th{
      background: #f6f7fb;
      color: #0b1220;
      font-weight: 900;
      border-bottom: 1px solid #e5e7eb;
      white-space:nowrap;
    }
    .table td{
      color:#0b1220;
      border-top: 1px solid #eef0f5;
      vertical-align:middle;
    }
    .badge-soft{
      background:#f2f4f7;border:1px solid #e5e7eb;color:#0b1220;
      padding:.35rem .65rem;border-radius:999px;font-weight:900;
    }

    /* ✅ Mobile cards */
    .mobile-list{display:none;}
    .proj-card{
      background: rgba(255,255,255,.92);
      border-radius: 18px;
      border:1px solid #e5e7eb;
      box-shadow: 0 10px 22px rgba(0,0,0,.10);
      overflow:hidden;
      margin-bottom:10px;
    }
    .proj-head{
      padding:12px 14px;
      display:flex;align-items:center;justify-content:space-between;gap:10px;
      cursor:pointer;
    }
    .proj-name{
      font-weight:1000;color:#0b1220;font-size:1.05rem;
      display:flex;align-items:center;gap:10px;flex-wrap:wrap;
    }
    .proj-body{padding:0 14px 14px}
    .kv{display:flex;justify-content:space-between;gap:12px;padding:8px 0;border-bottom:1px dashed #e5e7eb;}
    .kv:last-child{border-bottom:0}
    .k{color:#475467;font-weight:900}
    .v{color:#0b1220;font-weight:1000}
    .smallmuted{color:#667085;font-weight:800;font-size:.9rem}

    /* ✅ Mobile behavior */
    @media (max-width: 768px){
      .desktop-table{display:none;}
      .mobile-list{display:block;}
      .header .actions{display:none;} /* ✅ اخفاء كل الأزرار بالجوال */
    }

    @media print{
      .btn-mr, form, .actions{display:none !important;}
      body{background:#fff !important;}
      .header{background:#fff !important;color:#000 !important;border:1px solid #ddd !important;box-shadow:none !important;}
      .panel{background:#fff !important;border:1px solid #ddd !important;box-shadow:none !important;}
    }
  </style>
</head>
<body>
<div class="wrap">

  <div class="header">
    <div>
      <h1>تقرير تكلفة المشاريع</h1>
      <div class="sub">الشهر: <span class="badge-soft"><?= html_escape($month) ?></span></div>
      <div class="smallmuted">ترتيب المشاريع: الأعلى عدد موظفين أولاً</div>
    </div>

    <!-- ✅ أخفاءها بالجوال -->
    <div class="actions d-flex gap-2 flex-wrap">
      
      <a class="btn-mr primary" href="<?= site_url('ProjectCostReport/export_summary_csv?month='.urlencode($month)); ?>">
        <i class="fas fa-file-excel"></i> تصدير Excel
      </a>
      <button class="btn-mr" onclick="window.print()"><i class="fas fa-print"></i> طباعة</button>
    </div>

    <form class="d-flex gap-2 flex-wrap" method="get" action="<?= site_url('ProjectCostReport'); ?>">
      <input type="text" class="form-control" name="month" value="<?= html_escape($month) ?>" placeholder="yyyy/mm" style="max-width:160px;">
      <button class="btn btn-light fw-bold" type="submit"><i class="fas fa-filter"></i> تطبيق</button>
    </form>
  </div>

  <div class="panel mt-3">

    <!-- ✅ Desktop Table -->
    <div class="desktop-table">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th>#</th>
              <th>المشروع</th>
              <th class="text-center">عدد الأشخاص</th>
              <th class="text-center">الرواتب</th>
              <th class="text-center">الإنتاجية</th>
              <th class="text-center">نسبة العمولة</th>
              <th class="text-center">عمولة الشركة</th>
              <th class="text-center">تكلفة التشغيل (ثابت)</th>
              <th class="text-center">إجمالي التشغيل</th>
              <th class="text-center">هامش تقريبي</th>
              <th class="text-center">الإجراء</th>
            </tr>
          </thead>
          <tbody>
          <?php if (!empty($projects)): $i=1; foreach ($projects as $p): ?>
            <tr>
              <td class="fw-bold"><?= (int)$i++ ?></td>
              <td>
                <div class="fw-bold"><?= html_escape($p['project_name'] ?: '—') ?></div>
                <div class="smallmuted">رقم المشروع: <span class="badge-soft"><?= html_escape($p['project_id']) ?></span></div>
              </td>
              <td class="text-center fw-bold"><?= (int)$p['people_count'] ?></td>
              <td class="text-center fw-bold"><?= number_format((float)$p['total_salary'],2) ?></td>
              <td class="text-center fw-bold"><?= number_format((float)$p['total_productivity'],2) ?></td>
              <td class="text-center fw-bold"><?= number_format((float)$p['commission_rate'],2) ?>%</td>
              <td class="text-center fw-bold"><?= number_format((float)$p['company_commission'],2) ?></td>
              <td class="text-center fw-bold"><?= number_format((float)$p['fixed_operating_person'],2) ?></td>
              <td class="text-center fw-bold"><?= number_format((float)$p['total_operating_cost'],2) ?></td>
              <td class="text-center fw-bold"><?= number_format((float)$p['approx_margin'],2) ?></td>
              <td class="text-center">
                <a class="btn btn-warning fw-bold"
                   href="<?= site_url('ProjectCostReport/project/'.(int)$p['project_id'].'?month='.urlencode($month)); ?>">
                  <i class="fas fa-eye"></i> التفاصيل
                </a>
              </td>
            </tr>
          <?php endforeach; else: ?>
            <tr><td colspan="11" class="text-center text-muted py-4">لا توجد بيانات.</td></tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ✅ Mobile Cards (اسم المشروع فقط + توسعة) -->
    <div class="mobile-list">
      <?php if (!empty($projects)): $i=1; foreach ($projects as $p): ?>
        <?php $cid = 'p_'.$p['project_id']; ?>
        <div class="proj-card">
          <div class="proj-head" data-bs-toggle="collapse" data-bs-target="#<?= $cid ?>" aria-expanded="false">
            <div class="proj-name">
              <i class="fas fa-folder-open text-warning"></i>
              <?= html_escape($p['project_name'] ?: '—') ?>
              <span class="badge-soft">#<?= html_escape($p['project_id']) ?></span>
            </div>
            <div class="text-muted fw-bold"><i class="fas fa-chevron-down"></i></div>
          </div>

          <div id="<?= $cid ?>" class="collapse">
            <div class="proj-body">
              <div class="kv"><div class="k">عدد الأشخاص</div><div class="v"><?= (int)$p['people_count'] ?></div></div>
              <div class="kv"><div class="k">الرواتب</div><div class="v"><?= number_format((float)$p['total_salary'],2) ?></div></div>
              <div class="kv"><div class="k">الإنتاجية</div><div class="v"><?= number_format((float)$p['total_productivity'],2) ?></div></div>
              <div class="kv"><div class="k">نسبة العمولة</div><div class="v"><?= number_format((float)$p['commission_rate'],2) ?>%</div></div>
              <div class="kv"><div class="k">عمولة الشركة</div><div class="v"><?= number_format((float)$p['company_commission'],2) ?></div></div>
              <div class="kv"><div class="k">تكلفة التشغيل (للفرد)</div><div class="v"><?= number_format((float)$p['fixed_operating_person'],2) ?></div></div>
              <div class="kv"><div class="k">إجمالي التشغيل</div><div class="v"><?= number_format((float)$p['total_operating_cost'],2) ?></div></div>
              <div class="kv"><div class="k">هامش تقريبي</div><div class="v"><?= number_format((float)$p['approx_margin'],2) ?></div></div>

              <a class="btn btn-warning w-100 fw-bold mt-2"
                 href="<?= site_url('ProjectCostReport/project/'.(int)$p['project_id'].'?month='.urlencode($month)); ?>">
                <i class="fas fa-eye"></i> عرض تفاصيل الموظفين
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; else: ?>
        <div class="text-center text-white-50 py-4">لا توجد بيانات.</div>
      <?php endif; ?>
    </div>

  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
