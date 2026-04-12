<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= html_escape($title ?? 'تقرير رواتب القياديين') ?></title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
  <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;500;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

  <style>
    :root{
      --primary-blue:#001f3f;
      --primary-orange:#FF8C00;
      --dark-bg:#0d1b2a;
      --darker-bg:#0a1929;

      --glass-bg: rgba(255,255,255,.05);
      --glass-border: rgba(255,255,255,.10);
      --card-bg: rgba(255,255,255,.07);

      --text-light:#fff;
      --text-muted: rgba(255,255,255,.70);

      --shadow-lg: 0 20px 40px rgba(0,0,0,.40);
      --shadow-sm: 0 10px 25px rgba(0,0,0,.20);

      --radius-xxl: 24px;
      --radius-xl: 18px;
      --radius-lg: 14px;
    }

    *{box-sizing:border-box}
    body{
      font-family:'Tajawal', sans-serif;
      background: linear-gradient(135deg, var(--darker-bg) 0%, var(--primary-blue) 30%, #1a1a2e 70%, var(--dark-bg) 100%);
      min-height:100vh;
      color:var(--text-light);
      overflow-x:hidden;
      background-attachment: fixed;
    }

    .bg-pattern{
      position:fixed; inset:0;
      background-image:
        radial-gradient(circle at 10% 20%, rgba(255,140,0,.05) 0%, transparent 20%),
        radial-gradient(circle at 90% 80%, rgba(0,31,63,.05) 0%, transparent 20%),
        linear-gradient(45deg, transparent 48%, rgba(255,140,0,.03) 50%, transparent 52%),
        linear-gradient(-45deg, transparent 48%, rgba(0,31,63,.03) 50%, transparent 52%);
      background-size: 400px 400px, 400px 400px, 100px 100px, 100px 100px;
      z-index:-2;
      animation: patternMove 20s linear infinite;
    }
    @keyframes patternMove{
      0%{background-position:0 0,0 0,0 0,0 0}
      100%{background-position:400px 400px,400px 400px,100px 100px,100px 100px}
    }

    .floating-orb{position:fixed;border-radius:50%;filter: blur(40px);opacity:.15;animation: orbFloat 30s ease-in-out infinite;z-index:-1;}
    .orb-1{width:320px;height:320px;background:var(--primary-orange);top:10%;right:6%;}
    .orb-2{width:440px;height:440px;background:var(--primary-blue);bottom:8%;left:6%;animation-duration:40s;animation-delay:-10s;}
    .orb-3{width:220px;height:220px;background:#ff9f43;top:55%;left:22%;animation-duration:26s;animation-delay:-5s;}
    @keyframes orbFloat{
      0%,100%{transform:translate(0,0) scale(1)}
      25%{transform:translate(100px,-50px) scale(1.08)}
      50%{transform:translate(-50px,100px) scale(.92)}
      75%{transform:translate(-100px,-50px) scale(1.05)}
    }

    .wrap{max-width:1400px;margin:24px auto;padding:0 14px;position:relative;z-index:1;}

    .header-nav{
      background: rgba(255,255,255,.03);
      border: 1px solid var(--glass-border);
      border-radius: var(--radius-xxl);
      box-shadow: 0 8px 32px rgba(0,0,0,.28);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      padding: 16px 18px;
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:12px;
      margin-bottom: 16px;
      position:relative;
      overflow:hidden;
    }
    .header-nav::before{
      content:'';
      position:absolute; top:0; left:0; right:0;
      height:2px;
      background: linear-gradient(90deg, transparent, var(--primary-orange), transparent);
    }
    .title-box h1{
      font-family:'El Messiri', serif;
      font-size: 1.7rem;
      font-weight: 900;
      margin:0;
      line-height: 1.2;
      background: linear-gradient(135deg, #ffffff, #ffd166);
      -webkit-background-clip:text;
      -webkit-text-fill-color:transparent;
    }
    .title-box p{margin:6px 0 0;color: var(--text-muted);font-size: .95rem;}

    .btn-marsom{
      border: 1px solid rgba(255,255,255,.18);
      background: rgba(255,255,255,.06);
      color:#fff;
      border-radius: 14px;
      padding: 10px 14px;
      font-weight: 900;
      text-decoration:none;
      display:inline-flex;
      align-items:center;
      gap:10px;
      transition: all .25s ease;
      white-space:nowrap;
    }
    .btn-marsom:hover{
      transform: translateY(-2px);
      border-color: rgba(255,140,0,.55);
      box-shadow: 0 10px 22px rgba(255,140,0,.14);
      color:#fff;
    }
    .btn-marsom.primary{
      background: linear-gradient(135deg, rgba(255,140,0,.25), rgba(255,140,0,.10));
      border-color: rgba(255,140,0,.35);
    }
    .btn-marsom.report{
      background: linear-gradient(135deg, rgba(74,105,189,.22), rgba(0,31,63,.10));
      border-color: rgba(74,105,189,.25);
    }

    /* ✅ زر التفاصيل أسود دائمًا */
    .btn-details-black{
      background: rgba(255,255,255,.92) !important;
      border: 1px solid rgba(0,0,0,.15) !important;
      color:#000 !important;
    }
    .btn-details-black:hover{
      color:#000 !important;
      border-color: rgba(0,0,0,.35) !important;
      box-shadow: 0 10px 22px rgba(0,0,0,.12) !important;
    }
    .btn-details-black i{color:#000 !important}

    .section{
      background: var(--glass-bg);
      border: 1px solid var(--glass-border);
      border-radius: var(--radius-xxl);
      backdrop-filter: blur(18px);
      -webkit-backdrop-filter: blur(18px);
      box-shadow: var(--shadow-sm);
      padding: 18px;
      position: relative;
      overflow:hidden;
      margin-bottom: 16px;
    }
    .section::before{
      content:'';
      position:absolute; top:0; left:0; right:0;
      height:3px;
      background: linear-gradient(90deg, var(--primary-orange), var(--primary-blue));
      opacity:.9;
    }

    .company-badge{
      display:inline-flex; align-items:center; gap:10px;
      background: rgba(255,255,255,.06);
      border: 1px solid rgba(255,255,255,.12);
      padding: 10px 12px;
      border-radius: 999px;
      font-weight: 900;
    }

    .stat-grid{
      display:grid;
      grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));
      gap:12px;
      margin-top: 10px;
      margin-bottom: 14px;
    }
    .stat{
      background: rgba(255,255,255,.06);
      border: 1px solid rgba(255,255,255,.12);
      border-radius: var(--radius-xl);
      padding: 14px;
      box-shadow: 0 10px 25px rgba(0,0,0,.18);
    }
    .stat .k{color:var(--text-muted);font-weight:800;font-size:.92rem}
    .stat .v{font-weight:900;font-size:1.25rem;margin-top:6px}

    .table-wrap{
      background: rgba(255,255,255,.04);
      border: 1px solid rgba(255,255,255,.10);
      border-radius: var(--radius-xl);
      overflow:hidden;
    }
    .table{margin:0;color:#fff;}
    .table thead th{
      background: rgba(0,0,0,.22);
      border-bottom: 1px solid rgba(255,255,255,.12);
      color: rgba(255,255,255,.92);
      font-weight: 900;
      white-space:nowrap;
    }
    .table tbody td{
      border-top: 1px solid rgba(255,255,255,.08);
      vertical-align: middle;
    }

    /* ✅ الراتب يظهر بخط أسود */
    .badge-salary{
      background: rgba(255,140,0,.18);
      border: 1px solid rgba(255,140,0,.35);
      color:#000;
      font-weight: 900;
      padding: 8px 12px;
      border-radius: 999px;
      white-space:nowrap;
      display:inline-flex;
      align-items:center;
      gap:8px;
    }

    .empty{
      padding:22px;
      text-align:center;
      color: rgba(255,255,255,.78);
      font-weight:800;
    }

    /* ✅ Mobile cards */
    .m-card{
      background: var(--card-bg);
      border: 1px solid rgba(255,255,255,.12);
      border-radius: var(--radius-xl);
      box-shadow: 0 10px 25px rgba(0,0,0,.18);
      overflow:hidden;
      margin-bottom: 12px;
    }
    .m-card .m-head{
      padding: 14px 14px 10px;
      display:flex;
      justify-content:space-between;
      align-items:flex-start;
      gap:12px;
    }
    .m-name{
      font-weight: 900;
      font-size: 1.05rem;
      line-height: 1.35;
    }
    .m-sub{
      color: var(--text-muted);
      margin-top:6px;
      font-weight: 800;
      font-size: .9rem;
    }
    .m-actions{
      padding: 0 14px 14px;
      display:flex;
      gap:10px;
      flex-wrap:wrap;
      align-items:center;
      justify-content:flex-start;
    }
    .m-details{
      padding: 12px 14px 14px;
      border-top: 1px solid rgba(255,255,255,.10);
    }
    .m-grid{
      display:grid;
      grid-template-columns: 1fr;
      gap:10px;
    }
    .m-item{
      background: rgba(0,0,0,.20);
      border: 1px solid rgba(255,255,255,.10);
      border-radius: 14px;
      padding: 10px 12px;
    }
    .m-item .k{color: rgba(255,255,255,.70); font-weight: 900; font-size:.9rem}
    .m-item .v{font-weight: 900; margin-top:4px; word-break:break-word;}

    @media print{
      body{background:#fff !important;color:#000 !important;}
      .no-print{display:none !important;}
      .section{border:1px solid #ddd !important;box-shadow:none !important;background:#fff !important;color:#000 !important;}
      .section::before{display:none !important;}
      .table{color:#000 !important;}
      .table thead th{background:#f2f2f2 !important;color:#000 !important;}
      .badge-salary{border:1px solid #000 !important;background:#fff !important;color:#000 !important;}
    }
  </style>
</head>

<body>
  <div class="bg-pattern"></div>
  <div class="floating-orb orb-1"></div>
  <div class="floating-orb orb-2"></div>
  <div class="floating-orb orb-3"></div>

  <div class="wrap">

    <div class="header-nav" data-aos="fade-down" data-aos-duration="800">
      <div class="title-box">
        <h1>تقرير رواتب القياديين</h1>
        
      </div>

      <div class="d-flex gap-2 flex-wrap no-print">
         
        <button class="btn-marsom primary" onclick="window.print()">
          <i class="fas fa-print"></i> طباعة
        </button>
      </div>
    </div>

    <!-- (اختياري) فلتر مختصر -->
    <div class="section no-print" data-aos="fade-up" data-aos-delay="60">
      <form method="get" action="<?= site_url('LeaderSalariesReport'); ?>" class="row g-2 align-items-end">
        <div class="col-12 col-md-4">
          <label class="form-label fw-bold">بحث</label>
          <input type="text" name="q" value="<?= html_escape($filters['q'] ?? '') ?>" class="form-control" placeholder="اسم / رقم وظيفي">
        </div>
        <div class="col-12 col-md-3">
          <label class="form-label fw-bold">المسمى الوظيفي</label>
          <select name="profession" class="form-select">
            <option value="">الكل</option>
            <?php foreach(($professions ?? []) as $p): ?>
              <option value="<?= html_escape($p) ?>" <?= (($filters['profession'] ?? '') === $p) ? 'selected' : '' ?>>
                <?= html_escape($p) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12 col-md-3">
          <label class="form-label fw-bold">ترتيب</label>
          <select name="sort" class="form-select">
            <option value="salary_desc" <?= (($filters['sort'] ?? '')==='salary_desc')?'selected':'' ?>>من الأعلى إلى الأقل (راتب)</option>
            <option value="salary_asc"  <?= (($filters['sort'] ?? '')==='salary_asc')?'selected':'' ?>>من الأقل إلى الأعلى (راتب)</option>
            <option value="name_asc"    <?= (($filters['sort'] ?? '')==='name_asc')?'selected':'' ?>>الاسم (تصاعدي)</option>
            <option value="name_desc"   <?= (($filters['sort'] ?? '')==='name_desc')?'selected':'' ?>>الاسم (تنازلي)</option>
          </select>
        </div>
        <div class="col-12 col-md-2 d-grid">
          <button class="btn btn-warning fw-bold"><i class="fa-solid fa-magnifying-glass"></i> تطبيق</button>
        </div>
      </form>
    </div>

    <?php $qs = $_GET ? ('?'.http_build_query($_GET)) : ''; ?>

    <?php foreach (($groups ?? []) as $gi => $g): ?>
      <?php
        $stats = $g['stats'] ?? [];
        $rows  = $g['rows'] ?? [];
        $cnt   = (int)($stats['cnt'] ?? 0);
        $total = (float)($stats['total'] ?? 0);
        $avg   = (float)($stats['avg_salary'] ?? 0);
        $max   = (float)($stats['max_salary'] ?? 0);
        $min   = (float)($stats['min_salary'] ?? 0);
      ?>

      <div class="section" data-aos="fade-up" data-aos-delay="<?= 120 + ($gi*60) ?>">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
          <div class="company-badge">
            <i class="fa-solid fa-building"></i>
            <?= html_escape($g['label'] ?? '') ?>
          </div>
          <div class="no-print">
            <span style="color:rgba(255,255,255,.70);font-weight:900">n13:</span>
            <span class="badge bg-dark"><?= (int)($g['n13'] ?? 0) ?></span>
          </div>
        </div>

        <div class="stat-grid">
          <div class="stat"><div class="k">العدد</div><div class="v"><?= $cnt ?></div></div>
          <div class="stat"><div class="k">الإجمالي</div><div class="v"><?= number_format($total, 2) ?></div></div>
          <div class="stat"><div class="k">المتوسط</div><div class="v"><?= number_format($avg, 2) ?></div></div>
          <div class="stat"><div class="k">أعلى راتب</div><div class="v"><?= number_format($max, 2) ?></div></div>
          <div class="stat"><div class="k">أقل راتب</div><div class="v"><?= number_format($min, 2) ?></div></div>
        </div>

        <?php if (empty($rows)): ?>
          <div class="empty">لا توجد بيانات ضمن هذا القسم</div>
        <?php else: ?>

          <!-- ✅ Desktop Table (مرتب) -->
          <div class="table-wrap d-none d-md-block">
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead>
                  <tr>
                    <th style="width:70px">#</th>
                    <th>الرقم الوظيفي</th>
                    <th>اسم الموظف</th>
                    <th>المسمى الوظيفي</th>
                    <th>إجمالي الراتب</th>
                    <th style="width:220px" class="no-print">التفاصيل</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i=1; foreach($rows as $r): ?>
                    <tr>
                      <td class="fw-bold"><?= $i++ ?></td>
                      <td class="fw-bold"><?= html_escape($r['employee_id'] ?? '') ?></td>
                      <td><?= html_escape($r['subscriber_name'] ?? '') ?></td>
                      <td><?= html_escape($r['profession'] ?? '') ?></td>
                      <td><span class="badge-salary"><i class="fa-solid fa-sack-dollar"></i><?= number_format((float)($r['total_salary_num'] ?? 0), 2) ?></span></td>
                      <td class="no-print">
                        <a class="btn-marsom btn-details-black"
                           href="<?= site_url('LeaderSalariesReport/details/'.rawurlencode($r['employee_id'] ?? '')).$qs ?>">
                          <i class="fa-solid fa-diagram-project"></i> تفاصيل
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>

          <!-- ✅ Mobile Cards (بدون أي تشوه) -->
          <div class="d-md-none">
            <?php $i=1; foreach($rows as $r): ?>
              <?php $cid = 'm_'.$g['n13'].'_'.$i.'_'.$r['employee_id']; ?>
              <div class="m-card">
                <div class="m-head">
                  <div>
                    <div class="m-name"><?= html_escape($r['subscriber_name'] ?? '') ?></div>
                    <div class="m-sub">
                      <i class="fa-solid fa-id-badge"></i>
                      <?= html_escape($r['employee_id'] ?? '') ?>
                    </div>
                  </div>
                  <div class="text-start">
                    <span class="badge-salary"><i class="fa-solid fa-sack-dollar"></i><?= number_format((float)($r['total_salary_num'] ?? 0), 2) ?></span>
                  </div>
                </div>

                <div class="m-actions">
                  <button class="btn-marsom btn-details-black"
                          type="button"
                          data-bs-toggle="collapse"
                          data-bs-target="#<?= $cid ?>">
                    <i class="fa-solid fa-circle-info"></i> تفاصيل
                  </button>

                  <a class="btn-marsom primary"
                     href="<?= site_url('LeaderSalariesReport/details/'.rawurlencode($r['employee_id'] ?? '')).$qs ?>">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>   التابعين 
                  </a>
                </div>

                <div class="collapse" id="<?= $cid ?>">
                  <div class="m-details">
                    <div class="m-grid">
                      <div class="m-item">
                        <div class="k">المسمى الوظيفي</div>
                        <div class="v"><?= html_escape($r['profession'] ?? '-') ?></div>
                      </div>
                      <div class="m-item">
                        <div class="k">القسم</div>
                        <div class="v"><?= html_escape($g['label'] ?? '-') ?></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

        <?php endif; ?>

      </div>
    <?php endforeach; ?>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script> AOS.init({ duration: 800, once: true, offset: 50, easing: 'ease-out-cubic' }); </script>
</body>
</html>
