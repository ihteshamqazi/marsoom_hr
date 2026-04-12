<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= html_escape(($title ?? 'تفاصيل المشروع').' - '.($project['project_name'] ?? '')) ?></title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
  <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <style>
    :root{
      --primary-blue:#001f3f;
      --primary-orange:#FF8C00;
      --badge:#f2f4f7;
      --text:#0b1220;
      --muted:#667085;
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
      font-size:1.35rem;
      font-weight:900;
      background: linear-gradient(135deg,#fff,#ffd166);
      -webkit-background-clip:text;-webkit-text-fill-color:transparent;
    }
    .sub{color:rgba(255,255,255,.72);font-weight:800}
    .badge-soft{
      background: var(--badge); border:1px solid #e5e7eb; color:var(--text);
      padding:.35rem .65rem;border-radius:999px;font-weight:900;
    }
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

    .panel{
      margin-top:14px;
      background: rgba(255,255,255,.08);
      border:1px solid rgba(255,255,255,.14);
      border-radius: 26px;
      backdrop-filter: blur(18px);
      box-shadow: 0 12px 30px rgba(0,0,0,.18);
      padding: 14px;
    }

    .kpis{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin-bottom:12px;}
    .kpi{
      background: rgba(255,255,255,.92);
      border:1px solid #e5e7eb;
      border-radius: 18px;
      padding: 12px;
      box-shadow: 0 10px 22px rgba(0,0,0,.10);
    }
    .kpi .l{color:#475467;font-weight:900}
    .kpi .v{color:#0b1220;font-weight:1000;font-size:1.15rem;margin-top:2px}
    .kpi .s{color:#667085;font-weight:800;font-size:.9rem;margin-top:2px}

    .table{
      background: rgba(255,255,255,.92);
      border-radius: 18px;
      overflow:hidden;
      margin:0;
    }
    .table thead th{
      background:#f6f7fb;color:#0b1220;font-weight:900;border-bottom:1px solid #e5e7eb;white-space:nowrap;
    }
    .table td{color:#0b1220;border-top:1px solid #eef0f5;vertical-align:middle;}

    /* Mobile */
    .desktop-block{display:block;}
    .mobile-list{display:none;}
    .emp-card{
      background: rgba(255,255,255,.92);
      border:1px solid #e5e7eb;
      border-radius: 18px;
      box-shadow: 0 10px 22px rgba(0,0,0,.10);
      overflow:hidden;
      margin-bottom:10px;
    }
    .emp-head{padding:12px 14px;display:flex;justify-content:space-between;gap:10px;cursor:pointer;}
    .emp-name{color:#0b1220;font-weight:1000}
    .emp-meta{color:#667085;font-weight:800;font-size:.9rem}
    .emp-body{padding:0 14px 14px}
    .kv{display:flex;justify-content:space-between;gap:12px;padding:8px 0;border-bottom:1px dashed #e5e7eb;}
    .kv:last-child{border-bottom:0}
    .k{color:#475467;font-weight:900}
    .v{color:#0b1220;font-weight:1000}

    @media (max-width: 992px){
      .kpis{grid-template-columns:repeat(2,minmax(0,1fr));}
    }
    @media (max-width: 768px){
      .header .actions{display:none;} /* ✅ اخفاء الأزرار بالجوال */
      .desktop-block{display:none;}
      .mobile-list{display:block;}
    }

    @media print{
      .btn-mr, .actions, #q{display:none !important;}
      body{background:#fff !important;}
      .header,.panel{background:#fff !important;border:1px solid #ddd !important;box-shadow:none !important;}
    }
  </style>
</head>
<body>
<div class="wrap">

  <div class="header">
    <div>
      <h1>تفاصيل تكلفة المشروع</h1>
      <div class="sub">
        المشروع: <span class="badge-soft"><?= html_escape($project['project_name'] ?? '—') ?></span>
        — رقم: <span class="badge-soft"><?= html_escape($project_id) ?></span>
        — الشهر: <span class="badge-soft"><?= html_escape($month) ?></span>
      </div>
    </div>

    <!-- ✅ تُخفى بالجوال -->
    <div class="actions d-flex gap-2 flex-wrap">
      <a class="btn-mr" href="<?= site_url('ProjectCostReport?month='.urlencode($month)); ?>"><i class="fas fa-arrow-right"></i> رجوع</a>
      <a class="btn-mr primary" href="<?= site_url('ProjectCostReport/export_project_csv/'.(int)$project_id.'?month='.urlencode($month)); ?>">
        <i class="fas fa-file-excel"></i> تصدير Excel
      </a>
      <button class="btn-mr" onclick="window.print()"><i class="fas fa-print"></i> طباعة</button>
    </div>
  </div>

  <div class="panel">

    <div class="kpis">
      <div class="kpi">
        <div class="l">إجمالي الرواتب</div>
        <div class="v"><?= number_format((float)$totals['total_salary'],2) ?></div>
        <div class="s">يشمل الموظف + المشرف + المدير</div>
      </div>
      <div class="kpi">
        <div class="l">إجمالي الإنتاجية</div>
        <div class="v"><?= number_format((float)$totals['total_productivity'],2) ?></div>
        <div class="s">داخل الشهر (n8)</div>
      </div>
      <div class="kpi">
        <div class="l">عمولة الشركة</div>
        <div class="v"><?= number_format((float)$totals['company_commission'],2) ?></div>
        <div class="s">نسبة العمولة: <?= number_format((float)$rate,2) ?>%</div>
      </div>
      <div class="kpi">
        <div class="l">هامش تقريبي</div>
        <div class="v"><?= number_format((float)$totals['approx_margin'],2) ?></div>
        <div class="s">عمولة - رواتب - تشغيل</div>
      </div>
    </div>

    <!-- Desktop Table -->
    <div class="desktop-block">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
        <div class="text-white-50 fw-bold">الترتيب: غير محصل ديون أولاً ثم محصل ديون.</div>
        <input id="q" class="form-control" placeholder="بحث بالاسم أو الرقم الوظيفي..." style="min-width:280px;">
      </div>

      <div class="table-responsive">
        <table id="tbl" class="table table-hover align-middle">
          <thead>
            <tr>
              <th>#</th>
              <th>الموظف</th>
              <th class="text-center">النوع</th>
              <th>المشرف</th>
              <th>المدير</th>
              <th class="text-center">الراتب</th>
              <th class="text-center">إنتاجية الشهر</th>
              <th class="text-center">عمولة الشركة</th>
              <th class="text-center">تشغيل (ثابت)</th>
              <th class="text-center">هامش تقريبي</th>
            </tr>
          </thead>
          <tbody>
          <?php if (!empty($rows)): $i=1; foreach ($rows as $r): ?>
            <tr>
              <td class="fw-bold"><?= (int)$i++ ?></td>
              <td>
                <div class="fw-bold"><?= html_escape($r['name']) ?></div>
                <div class="emp-meta">الرقم الوظيفي: <span class="badge-soft"><?= html_escape($r['emp_no']) ?></span></div>
              </td>
              <td class="text-center"><span class="badge-soft"><?= html_escape($r['type_label']) ?></span></td>
              <td><?= html_escape($r['supervisor_name']) ?></td>
              <td><?= html_escape($r['manager_name']) ?></td>
              <td class="text-center fw-bold"><?= number_format((float)$r['salary'],2) ?></td>
              <td class="text-center fw-bold"><?= number_format((float)$r['productivity'],2) ?></td>
              <td class="text-center fw-bold"><?= number_format((float)$r['company_commission'],2) ?></td>
              <td class="text-center fw-bold"><?= number_format((float)$r['operating_cost'],2) ?></td>
              <td class="text-center fw-bold"><?= number_format((float)$r['approx_margin'],2) ?></td>
            </tr>
          <?php endforeach; else: ?>
            <tr><td colspan="10" class="text-center text-muted py-4">لا توجد بيانات.</td></tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Mobile Accordion -->
    <div class="mobile-list">
      <?php if (!empty($rows)): $i=1; foreach ($rows as $r): ?>
        <?php $cid = 'e_'.$r['emp_no']; ?>
        <div class="emp-card">
          <div class="emp-head" data-bs-toggle="collapse" data-bs-target="#<?= $cid ?>">
            <div>
              <div class="emp-name">
                <?= html_escape($r['name']) ?>
                <span class="badge-soft"><?= html_escape($r['type_label']) ?></span>
              </div>
              <div class="emp-meta">#<?= html_escape($r['emp_no']) ?></div>
            </div>
            <div class="text-muted fw-bold"><i class="fas fa-chevron-down"></i></div>
          </div>

          <div id="<?= $cid ?>" class="collapse">
            <div class="emp-body">
              <div class="kv"><div class="k">المشرف</div><div class="v"><?= html_escape($r['supervisor_name']) ?></div></div>
              <div class="kv"><div class="k">المدير</div><div class="v"><?= html_escape($r['manager_name']) ?></div></div>
              <div class="kv"><div class="k">الراتب</div><div class="v"><?= number_format((float)$r['salary'],2) ?></div></div>
              <div class="kv"><div class="k">إنتاجية الشهر</div><div class="v"><?= number_format((float)$r['productivity'],2) ?></div></div>
              <div class="kv"><div class="k">عمولة الشركة</div><div class="v"><?= number_format((float)$r['company_commission'],2) ?></div></div>
              <div class="kv"><div class="k">تشغيل (ثابت)</div><div class="v"><?= number_format((float)$r['operating_cost'],2) ?></div></div>
              <div class="kv"><div class="k">هامش تقريبي</div><div class="v"><?= number_format((float)$r['approx_margin'],2) ?></div></div>
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
<script>
  // بحث سريع (Desktop فقط)
  const q = document.getElementById('q');
  const tbl = document.getElementById('tbl');
  if (q && tbl) {
    q.addEventListener('input', () => {
      const v = (q.value || '').trim().toLowerCase();
      tbl.querySelectorAll('tbody tr').forEach(tr => {
        tr.style.display = tr.innerText.toLowerCase().includes(v) ? '' : 'none';
      });
    });
  }
</script>
</body>
</html>
