<?php /* application/views/templateo/payroll_compare_a4.php */ ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>مقارنة الرواتب</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Fonts + Icons -->
  <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <style>
    :root{
      --marsom-blue:#001f3f;
      --marsom-orange:#FF8C00;
      --text-dark:#001f3f;
      --text-muted:#667085;

      --glass-bg: rgba(255,255,255,.86);
      --glass-border: rgba(0,0,0,.06);
      --glass-shadow: rgba(0,0,0,.10);

      --card-bg: rgba(255,255,255,.92);
      --soft:#f6f8fb;
      --line:#e7ebf0;
    }

    /* ====== Print A4 ====== */
    @page { size: A4; margin: 12mm 12mm 14mm 12mm; }

    html,body{
      margin:0;
      padding:0;
      background:#ffffff;
      color:var(--text-dark);
      font-family:'El Messiri','Tajawal',system-ui,-apple-system,"Segoe UI",Arial,sans-serif;
    }

    /* خلفية لطيفة للشاشة */
    .screen-bg{
      min-height:100vh;
      background:
        radial-gradient(1200px 600px at 20% 10%, rgba(255,140,0,.10), transparent 55%),
        radial-gradient(1200px 600px at 80% 0%, rgba(0,31,63,.10), transparent 55%),
        linear-gradient(180deg,#fff 0%, #f7f9fc 100%);
      padding:18px 0 30px;
    }

    /* الحاوية الزجاجية الكبيرة */
    .main-screen-container{
      width:min(1060px, 96vw);
      margin:0 auto;
      backdrop-filter: blur(18px);
      -webkit-backdrop-filter: blur(18px);
      background: var(--glass-bg);
      border:1px solid var(--glass-border);
      box-shadow: 0 0 40px var(--glass-shadow);
      border-radius:22px;
      padding:18px 18px 22px;
    }

    /* شريط أدوات الشاشة */
    .toolbar{
      display:flex;
      flex-wrap:wrap;
      gap:10px;
      align-items:center;
      justify-content:space-between;
      background: rgba(255,255,255,.92);
      border:1px solid var(--glass-border);
      border-radius:16px;
      padding:12px 12px;
      margin-bottom:14px;
      box-shadow: 0 6px 18px rgba(0,0,0,.06);
    }

    .toolbar form{
      display:flex;
      flex-wrap:wrap;
      gap:10px;
      align-items:center;
      margin:0;
    }

    .toolbar label{
      font-size:13px;
      color:var(--text-muted);
      font-weight:700;
      display:inline-flex;
      align-items:center;
      gap:8px;
    }

    .control{
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding:10px 12px;
      border-radius:14px;
      border:1px solid #d7dde6;
      background:#fff;
      font-family:'El Messiri','Tajawal',sans-serif;
      font-weight:700;
      color:var(--text-dark);
      outline:none;
    }

    input[type="month"].control{
      padding:9px 12px;
      min-width:160px;
    }

    .btn{
      border:1px solid var(--marsom-blue);
      color:var(--marsom-blue);
      background:#fff;
      padding:10px 14px;
      border-radius:14px;
      font-weight:800;
      cursor:pointer;
      text-decoration:none;
      display:inline-flex;
      align-items:center;
      gap:10px;
      transition:.22s ease;
      box-shadow: 0 3px 10px rgba(0,0,0,.05);
    }
    .btn:hover{ background:var(--marsom-blue); color:#fff; transform:translateY(-1px); }
    .btn-secondary{ border-color:#98a2b3; color:#344054; }
    .btn-secondary:hover{ background:#344054; color:#fff; }
    .btn-submit{ border-color:var(--marsom-orange); color:var(--marsom-orange); }
    .btn-submit:hover{ background:var(--marsom-orange); color:#fff; }

    /* ====== A4 Paper Card (داخل الشاشة + للطباعة) ====== */
    .a4-paper{
      width:210mm;
      min-height:297mm;
      margin:0 auto;
      background:#fff;
      border-radius:18px;
      border:1px solid var(--line);
      box-shadow: 0 10px 30px rgba(0,0,0,.08);
      padding:14mm;
      position:relative;
      overflow:hidden;
    }

    /* watermark خفيف */
    .wm{
      position:absolute;
      inset:auto -40px 28px auto;
      transform: rotate(-10deg);
      font-weight:800;
      color: rgba(0,31,63,.06);
      font-size:42px;
      pointer-events:none;
      user-select:none;
      white-space:nowrap;
    }

    /* Header */
    .top-header{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:14px;
      padding-bottom:12px;
      border-bottom:2px solid var(--marsom-blue);
      margin-bottom:14px;
    }
    .brand{
      display:flex;
      align-items:center;
      gap:14px;
    }
    .brand .logo{
      width:64px;
      height:64px;
      border-radius:16px;
      display:flex;
      align-items:center;
      justify-content:center;
      background: rgba(255,140,0,.14);
      border:1px solid rgba(255,140,0,.20);
      overflow:hidden;
    }
    .brand .logo img{
      height:44px;
      width:auto;
      filter: drop-shadow(0 0 4px rgba(0,0,0,.25));
    }
    .brand .titles h1{
      margin:0;
      font-size:22px;
      font-weight:800;
      color:var(--marsom-blue);
      letter-spacing:.2px;
    }
    .brand .titles .sub{
      margin-top:4px;
      font-size:13px;
      color:var(--text-muted);
      font-weight:700;
      display:flex;
      gap:10px;
      flex-wrap:wrap;
    }

    .meta{
      text-align:left;
      font-size:12px;
      color:#475467;
      font-weight:800;
    }
    .meta .pill{
      display:inline-flex;
      align-items:center;
      gap:8px;
      background: var(--soft);
      border:1px solid var(--line);
      border-radius:999px;
      padding:8px 12px;
      margin-bottom:8px;
    }

    /* Section Blocks */
    .company-block{
      border:1px solid var(--line);
      border-radius:18px;
      padding:12px 12px 14px;
      margin-top:14px;
      background: linear-gradient(180deg, rgba(0,31,63,.03), transparent 55%);
    }

    .company-title{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      margin-bottom:10px;
    }

    .company-title .left{
      display:flex;
      align-items:center;
      gap:10px;
    }
    .company-badge{
      width:46px;
      height:46px;
      border-radius:14px;
      background: var(--marsom-orange);
      color:#fff;
      display:flex;
      align-items:center;
      justify-content:center;
      font-size:18px;
      box-shadow: 0 10px 18px rgba(255,140,0,.18);
    }
    .company-title h2{
      margin:0;
      font-size:16px;
      font-weight:900;
      color:var(--marsom-blue);
    }
    .company-title .small{
      font-size:12px;
      color:var(--text-muted);
      font-weight:800;
    }

    /* Summary cards */
    .summary{
      display:grid;
      grid-template-columns: repeat(4, 1fr);
      gap:10px;
      margin:10px 0 12px;
    }
    .card{
      background: var(--card-bg);
      border:1px solid var(--line);
      border-radius:16px;
      padding:10px 10px;
      box-shadow: 0 6px 14px rgba(0,0,0,.05);
    }
    .card .label{
      font-size:12px;
      color:var(--text-muted);
      font-weight:800;
      display:flex;
      align-items:center;
      gap:8px;
    }
    .card .value{
      margin-top:6px;
      font-size:16px;
      font-weight:900;
      color:var(--marsom-blue);
    }

    .delta{
      font-weight:900;
    }
    .delta.up{ color:#137333; }
    .delta.down{ color:#b42318; }
    .delta.flat{ color:#475467; }

    /* Tables */
    .section-title{
      margin:10px 0 8px;
      padding:10px 12px;
      border-radius:14px;
      background: rgba(255,255,255,.92);
      border:1px solid var(--line);
      font-weight:900;
      color:var(--marsom-blue);
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:10px;
    }
    .section-title .tag{
      font-size:12px;
      color:#fff;
      background: var(--marsom-blue);
      border-radius:999px;
      padding:6px 10px;
      display:inline-flex;
      align-items:center;
      gap:8px;
      white-space:nowrap;
    }
    .section-title.blue .tag{ background:#1e40af; }
    .section-title.green .tag{ background:#059669; }

    .table-wrap{
      border:1px solid var(--line);
      border-radius:16px;
      overflow:hidden;
      background:#fff;
      box-shadow: 0 6px 14px rgba(0,0,0,.04);
    }
    table{
      width:100%;
      border-collapse:collapse;
    }
    th, td{
      padding:10px 10px;
      font-size:13px;
      border-bottom:1px solid #eef2f6;
      vertical-align:middle;
    }
    th{
      background: var(--marsom-blue);
      color:#fff;
      font-weight:900;
      text-align:center;
    }
    tr:nth-child(even) td{ background:#fbfcfe; }
    td.center{ text-align:center; }

    .tfoot td{
      background:#f6f8fb !important;
      font-weight:900;
      border-top:1px solid var(--line);
    }

    .empty{
      border:1px dashed var(--line);
      border-radius:16px;
      padding:12px;
      background:#fff;
      color:#475467;
      font-weight:800;
    }

    hr.sep{
      margin:14px 0;
      border:0;
      border-top:1px dashed var(--line);
    }

    .footer-note{
      margin-top:10px;
      display:flex;
      justify-content:space-between;
      gap:10px;
      flex-wrap:wrap;
      color:#667085;
      font-size:12px;
      font-weight:800;
    }

    /* ===== Print ===== */
    @media print{
      .screen-bg{ padding:0; background:#fff; }
      .main-screen-container{
        width:auto;
        margin:0;
        background:#fff;
        border:0;
        box-shadow:none;
        padding:0;
        border-radius:0;
      }
      .toolbar, .no-print{ display:none !important; }
      .a4-paper{
        margin:0;
        width:auto;
        min-height:auto;
        border:0;
        box-shadow:none;
        border-radius:0;
        padding:0;
      }
      a{ color:inherit; text-decoration:none; }
      .wm{ display:none; }
    }

    /* Responsive on screen */
    @media (max-width: 900px){
      .a4-paper{ width:auto; }
      .summary{ grid-template-columns: repeat(2, 1fr); }
      .meta{ text-align:right; }
      .top-header{ flex-direction:column; align-items:flex-start; }
    }
  </style>
</head>

<body>
<div class="screen-bg">
  <div class="main-screen-container">

    <!-- Toolbar (Screen Only) -->
    <div class="toolbar no-print">
      <form method="get" action="<?php echo site_url('users1/payroll_compare'); ?>">
        <label><i class="fa-regular fa-calendar"></i> الشهر الأول</label>
        <input type="month" name="m1" class="control" value="<?php echo html_escape($m1 ?? ''); ?>" required>

        <label><i class="fa-regular fa-calendar-check"></i> الشهر الثاني</label>
        <input type="month" name="m2" class="control" value="<?php echo html_escape($m2 ?? ''); ?>" required>

        <button class="btn btn-submit" type="submit">
          <i class="fa-solid fa-rotate"></i> عرض المقارنة
        </button>

        <a class="btn" href="javascript:window.print()">
          <i class="fa-solid fa-print"></i> طباعة
        </a>

        <a class="btn btn-secondary" href="javascript:history.back()">
          <i class="fa-solid fa-arrow-right"></i> رجوع
        </a>
      </form>

      <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <span class="control" style="border-style:dashed; background:transparent;">
          <i class="fa-solid fa-circle-info" style="color:var(--marsom-orange)"></i>
          اختر شهرين للمقارنة ثم اطبع A4
        </span>
      </div>
    </div>

    <!-- A4 Paper -->
    <div class="a4-paper">
      <div class="wm">MARSOOM • PAYROLL</div>

      <!-- Header -->
      <div class="top-header">
        <div class="brand">
          <div class="logo">
            <img src="<?php echo base_url('assets/imeges/m1.png'); ?>" alt="مرسوم">
          </div>
          <div class="titles">
            <h1>تقرير مقارنة الرواتب</h1>
            <div class="sub">
              <span><i class="fa-solid fa-arrow-right-arrow-left" style="color:var(--marsom-orange)"></i> الفترة: <?php echo html_escape($m1); ?> ↔ <?php echo html_escape($m2); ?></span>
              <span><i class="fa-regular fa-clock" style="color:var(--marsom-orange)"></i> إصدار: <?php echo date('Y/m/d'); ?></span>
            </div>
          </div>
        </div>

        <div class="meta">
          <div class="pill"><i class="fa-solid fa-shield-halved" style="color:var(--marsom-blue)"></i> مرسوم — نظام الموارد البشرية</div><br>
          <div class="pill"><i class="fa-solid fa-file-lines" style="color:var(--marsom-orange)"></i> نسخة طباعة A4</div>
        </div>
      </div>

      <?php foreach ($companies as $code => $comp): ?>
        <?php
          $name       = $comp['name'];
          $leftOnly   = $comp['leftOnly'];
          $rightOnly  = $comp['rightOnly'];
          $sum1       = $comp['sum1'];
          $sum2       = $comp['sum2'];
          $leftTotals = $comp['leftTotals'];
          $rightTotals= $comp['rightTotals'];

          // محاولة احتساب فروقات مختصرة من الملخص إن وجدت
          $emp1 = (float)($sum1['emp_count'] ?? 0);
          $emp2 = (float)($sum2['emp_count'] ?? 0);
          $empDelta = $emp2 - $emp1;
          $empTrend = ($empDelta > 0) ? 'up' : (($empDelta < 0) ? 'down' : 'flat');
        ?>

        <div class="company-block">
          <div class="company-title">
            <div class="left">
              <div class="company-badge"><i class="fa-solid fa-building"></i></div>
              <div>
                <h2><?php echo html_escape($name); ?></h2>
                <div class="small">كود: <?php echo html_escape((string)$code); ?> • مقارنة تفصيلية حسب الموظفين والملخصات</div>
              </div>
            </div>

            <div class="small">
              <span class="delta <?php echo $empTrend; ?>">
                <?php
                  $s = ($empDelta > 0) ? '+' : (($empDelta < 0) ? '−' : '');
                  echo 'تغير العدد: ' . $s . number_format(abs($empDelta), 0);
                ?>
              </span>
            </div>
          </div>

          <!-- Summary Cards -->
          <div class="summary">
            <div class="card">
              <div class="label"><i class="fa-solid fa-users" style="color:var(--marsom-orange)"></i> عدد الموظفين — <?php echo html_escape($m1); ?></div>
              <div class="value"><?php echo number_format($sum1['emp_count'] ?? 0); ?></div>
            </div>
            <div class="card">
              <div class="label"><i class="fa-solid fa-users" style="color:var(--marsom-orange)"></i> عدد الموظفين — <?php echo html_escape($m2); ?></div>
              <div class="value"><?php echo number_format($sum2['emp_count'] ?? 0); ?></div>
            </div>
            <div class="card">
              <div class="label"><i class="fa-solid fa-right-from-bracket" style="color:#1e40af"></i> موجودون في <?php echo html_escape($m1); ?> فقط</div>
              <div class="value"><?php echo number_format((int)($leftTotals['count'] ?? 0)); ?></div>
            </div>
            <div class="card">
              <div class="label"><i class="fa-solid fa-right-to-bracket" style="color:#059669"></i> موجودون في <?php echo html_escape($m2); ?> فقط</div>
              <div class="value"><?php echo number_format((int)($rightTotals['count'] ?? 0)); ?></div>
            </div>
          </div>

          <!-- LEFT ONLY -->
          <div class="section-title blue">
            <div>
              <i class="fa-solid fa-circle-minus" style="color:#1e40af"></i>
              الموجودون في <?php echo html_escape($m1); ?> وغير موجودين في <?php echo html_escape($m2); ?>
            </div>
            <span class="tag"><i class="fa-solid fa-filter"></i> اختلافات الشهر الأول</span>
          </div>

          <?php if (!empty($leftOnly)): ?>
            <div class="table-wrap">
              <table>
                <thead>
                  <tr>
                    <th>الرقم الوظيفي</th>
                    <th>اسم الموظف</th>
                    <th>إجمالي الراتب</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($leftOnly as $r): ?>
                    <tr>
                      <td class="center"><?php echo html_escape($r['emp_no']); ?></td>
                      <td><?php echo html_escape($r['emp_name']); ?></td>
                      <td class="center"><?php echo number_format((float)$r['total_salary'], 2); ?></td>
                    </tr>
                  <?php endforeach; ?>
                  <tr class="tfoot">
                    <td class="center">الإجمالي</td>
                    <td class="center"><?php echo (int)($leftTotals['count'] ?? 0); ?> موظف</td>
                    <td class="center"><?php echo number_format((float)($leftTotals['salary'] ?? 0), 2); ?></td>
                  </tr>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <div class="empty"><i class="fa-regular fa-circle-check" style="color:#137333"></i> لا يوجد فرق: جميع موظفي الشهر الأول موجودون في الشهر الثاني.</div>
          <?php endif; ?>

          <!-- RIGHT ONLY -->
          <div class="section-title green" style="margin-top:12px;">
            <div>
              <i class="fa-solid fa-circle-plus" style="color:#059669"></i>
              الموجودون في <?php echo html_escape($m2); ?> وغير موجودين في <?php echo html_escape($m1); ?>
            </div>
            <span class="tag"><i class="fa-solid fa-filter"></i> اختلافات الشهر الثاني</span>
          </div>

          <?php if (!empty($rightOnly)): ?>
            <div class="table-wrap">
              <table>
                <thead>
                  <tr>
                    <th>الرقم الوظيفي</th>
                    <th>اسم الموظف</th>
                    <th>إجمالي الراتب</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($rightOnly as $r): ?>
                    <tr>
                      <td class="center"><?php echo html_escape($r['emp_no']); ?></td>
                      <td><?php echo html_escape($r['emp_name']); ?></td>
                      <td class="center"><?php echo number_format((float)$r['total_salary'], 2); ?></td>
                    </tr>
                  <?php endforeach; ?>
                  <tr class="tfoot">
                    <td class="center">الإجمالي</td>
                    <td class="center"><?php echo (int)($rightTotals['count'] ?? 0); ?> موظف</td>
                    <td class="center"><?php echo number_format((float)($rightTotals['salary'] ?? 0), 2); ?></td>
                  </tr>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <div class="empty"><i class="fa-regular fa-circle-check" style="color:#137333"></i> لا يوجد فرق: جميع موظفي الشهر الثاني كانوا موجودين في الشهر الأول.</div>
          <?php endif; ?>

          <!-- CHANGES TABLE -->
          <div class="section-title" style="margin-top:12px;">
            <div><i class="fa-solid fa-chart-line" style="color:var(--marsom-orange)"></i> ملخصات شهرية ومؤشرات التغيير</div>
            <span class="tag"><i class="fa-solid fa-signal"></i> مقارنة أداء الرواتب</span>
          </div>

          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>البند</th>
                  <th><?php echo html_escape($m1); ?></th>
                  <th><?php echo html_escape($m2); ?></th>
                  <th>التغير</th>
                  <th>% النسبة</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($comp['changes'] as $ch): ?>
                  <tr>
                    <td><?php echo html_escape($ch['label']); ?></td>
                    <td class="center"><?php echo number_format((float)$ch['v1'], 2); ?></td>
                    <td class="center"><?php echo number_format((float)$ch['v2'], 2); ?></td>
                    <td class="center delta <?php echo $ch['trend']; ?>">
                      <?php
                        $sign = ($ch['delta'] > 0) ? '+' : (($ch['delta'] < 0) ? '−' : '');
                        echo $sign . number_format(abs((float)$ch['delta']), 2);
                      ?>
                    </td>
                    <td class="center delta <?php echo $ch['trend']; ?>">
                      <?php echo number_format((float)$ch['pct'], 2); ?>%
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <div class="footer-note">
            <div><i class="fa-solid fa-lock" style="color:var(--marsom-blue)"></i> هذا التقرير للاستخدام الداخلي</div>
            <div><i class="fa-solid fa-print" style="color:var(--marsom-orange)"></i> يفضل الطباعة بوضعية: Portrait + Margins Default</div>
          </div>

          <hr class="sep">
        </div>
      <?php endforeach; ?>

      <div class="footer-note" style="margin-top:0;">
        <div>© <?php echo date('Y'); ?> مرسوم</div>
        <div>Payroll Compare • v2</div>
      </div>

    </div><!-- /.a4-paper -->
  </div><!-- /.main-screen-container -->
</div><!-- /.screen-bg -->
</body>
</html>
