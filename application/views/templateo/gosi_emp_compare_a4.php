<?php /* application/views/templateo/gosi_emp_compare_a4.php */ ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>مقارنة التأمينات الاجتماعية مع سجل الموظفين</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
:root{
  --marsom-blue:#001f3f;
  --marsom-orange:#FF8C00;
  --text-dark:#001f3f;
  --text-muted:#667085;

  --glass-bg:rgba(255,255,255,.88);
  --glass-border:rgba(0,0,0,.06);
  --glass-shadow:rgba(0,0,0,.10);

  --line:#e7ebf0;
  --soft:#f6f8fb;
}

/* ===== Print A4 ===== */
@page{ size:A4; margin:12mm 12mm 14mm 12mm; }

html, body{
  margin:0;
  padding:0;
  min-height:100vh;
  font-family:'El Messiri','Tajawal',system-ui,-apple-system,"Segoe UI",Arial,sans-serif;
  background:#fff;
  color:#111;
}

/* خلفية الشاشة */
.screen-bg{
  min-height:100vh;
  width:100%;
  background:
    radial-gradient(1200px 600px at 20% 10%, rgba(255,140,0,.10), transparent 55%),
    radial-gradient(1200px 600px at 80% 0%, rgba(0,31,63,.10), transparent 55%),
    linear-gradient(180deg,#fff 0%, #f7f9fc 100%);
}

/* الحاوية الرئيسية (ملء الشاشة) */
.main-screen-container{
  width:100%;
  min-height:100vh;
  padding:22px 26px 26px;
  backdrop-filter:blur(18px);
  -webkit-backdrop-filter:blur(18px);
  background:var(--glass-bg);
  border:1px solid var(--glass-border);
  box-shadow:0 0 40px var(--glass-shadow);
}

/* ===== Toolbar (Screen only) ===== */
.no-print{
  display:flex;
  flex-wrap:wrap;
  gap:10px;
  align-items:center;
  justify-content:space-between;
  background:#fff;
  border:1px solid var(--glass-border);
  border-radius:18px;
  padding:12px 14px;
  margin-bottom:14px;
}

.no-print form{
  display:flex;
  flex-wrap:wrap;
  gap:10px;
  align-items:center;
  margin:0;
}

.no-print label{
  font-size:13px;
  color:var(--text-muted);
  font-weight:800;
  display:inline-flex;
  align-items:center;
  gap:8px;
}

.control, .no-print select{
  padding:10px 12px;
  border-radius:14px;
  border:1px solid #d7dde6;
  background:#fff;
  font-family:'El Messiri','Tajawal',sans-serif;
  font-weight:800;
  min-width:220px;
}

#branchFilterContainer select{
  min-width:150px;
}

.btn{
  border:1px solid var(--marsom-blue);
  color:var(--marsom-blue);
  background:#fff;
  padding:10px 14px;
  border-radius:14px;
  font-weight:900;
  cursor:pointer;
  text-decoration:none;
  display:inline-flex;
  align-items:center;
  gap:10px;
  transition:.22s ease;
  box-shadow:0 3px 10px rgba(0,0,0,.05);
}
.btn:hover{ background:var(--marsom-blue); color:#fff; transform:translateY(-1px); }
.btn-secondary{ border-color:#98a2b3; color:#344054; }
.btn-secondary:hover{ background:#344054; color:#fff; }
.btn-submit{ border-color:var(--marsom-orange); color:var(--marsom-orange); }
.btn-submit:hover{ background:var(--marsom-orange); color:#fff; }

.hint-pill{
  display:inline-flex;
  align-items:center;
  gap:10px;
  padding:10px 12px;
  border-radius:999px;
  border:1px dashed #d7dde6;
  color:#344054;
  font-weight:900;
  background:transparent;
}

/* ===== A4 Sheet (content) ===== */
.a4-sheet{
  width:min(1400px, 98vw);
  margin:0 auto 16px;
  background:#fff;
  border:1px solid var(--line);
  border-radius:22px;
  box-shadow:0 10px 30px rgba(0,0,0,.08);
  padding:14mm;
  position:relative;
  overflow:hidden;
}

/* Watermark */
.wm{
  position:absolute;
  inset:auto -40px 26px auto;
  transform:rotate(-10deg);
  font-weight:900;
  color:rgba(0,31,63,.06);
  font-size:42px;
  pointer-events:none;
  user-select:none;
  white-space:nowrap;
}

/* Header */
.header{
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:16px;
  padding-bottom:12px;
  border-bottom:2px solid var(--marsom-blue);
  margin-bottom:14px;
}
.brand{
  display:flex;
  align-items:center;
  gap:12px;
}
.brand img{
  height:46px;
  width:auto;
  filter: drop-shadow(0 0 4px rgba(0,0,0,.20));
}
.brand h1{
  font-size:18px;
  margin:0;
  font-weight:900;
  color:var(--marsom-blue);
  letter-spacing:.2px;
}
.meta{
  text-align:left;
  font-size:12px;
  color:#475467;
  font-weight:900;
}

/* Sections */
.section-title{
  margin:14px 0 8px;
  padding:10px 12px;
  background:#fff;
  border:1px solid var(--line);
  border-right:4px solid var(--marsom-orange);
  border-radius:14px;
  font-weight:900;
  color:var(--marsom-blue);
  display:flex;
  align-items:center;
  gap:10px;
}
.section-title i{
  color:var(--marsom-orange);
}

/* Summary cards */
.summary{
  display:grid;
  grid-template-columns:repeat(4, 1fr);
  gap:10px;
  margin-top:10px;
}
.card{
  border:1px solid var(--line);
  border-radius:16px;
  padding:10px;
  background:#fff;
  box-shadow:0 6px 14px rgba(0,0,0,.04);
}
.card .label{
  font-size:12px;
  color:var(--text-muted);
  font-weight:900;
  display:flex;
  align-items:center;
  gap:8px;
}
.card .value{
  font-size:16px;
  font-weight:900;
  margin-top:6px;
  color:var(--marsom-blue);
}

/* Badges */
.badge{
  display:inline-block;
  padding:.35rem .6rem;
  border-radius:999px;
  font-size:12px;
  font-weight:900;
  border:1px solid transparent;
}
.up   { background:#e6f4ea; color:#137333; border-color:rgba(19,115,51,.25); }
.down { background:#fde8e8; color:#b42318; border-color:rgba(180,35,24,.25); }
.flat { background:#eef2ff; color:#1e40af; border-color:rgba(30,64,175,.25); }

.center{ text-align:center; }

/* Tables */
.print-table{
  width:100%;
  border-collapse:collapse;
  margin-top:6px;
  border:1px solid var(--line);
  border-radius:16px;
  overflow:hidden;
  box-shadow:0 6px 14px rgba(0,0,0,.03);
}
.print-table th, .print-table td{
  border-bottom:1px solid #eef2f6;
  padding:10px 10px;
  font-size:13px;
  vertical-align:middle;
}
.print-table th{
  background:var(--marsom-blue);
  color:#fff;
  font-weight:900;
  text-align:center;
}
.print-table tr:nth-child(even) td{
  background:#fbfcfe;
}

/* Print rules */
@media print{
  body{ background:#fff; }
  .screen-bg{ background:#fff; }
  .main-screen-container{
    padding:0;
    background:#fff;
    border:none;
    box-shadow:none;
  }
  .a4-sheet{
    margin:0;
    width:auto;
    min-height:auto;
    box-shadow:none;
    border:none;
    border-radius:0;
    padding:0;
  }
  .no-print{ display:none !important; }
  a{ color:inherit; text-decoration:none; }
  .wm{ display:none; }
}

/* Responsive */
@media (max-width: 900px){
  .summary{ grid-template-columns:repeat(2, 1fr); }
  .no-print{ align-items:flex-start; }
  .meta{ text-align:right; }
  .control, .no-print select{ min-width: 180px; }
}
</style>
</head>
<body>

<div class="screen-bg">
<div class="main-screen-container">

  <!-- ===== Toolbar ===== -->
  <div class="no-print">
    <form method="get" action="<?php echo site_url('users1/gosi_emp_compare'); ?>">
      <label><i class="fa-solid fa-building"></i> الشركة</label>
      <select name="c" id="companySelect">
        <option value="">الكل</option>
        <option value="1" <?php echo (isset($company) && $company === '1') ? 'selected' : ''; ?>>شركة مرسوم</option>
        <option value="2" <?php echo (isset($company) && $company === '2') ? 'selected' : ''; ?>>مكتب الدكتور صالح الجربوع</option>
      </select>

      <span id="branchFilterContainer" style="display:none;">
        <label><i class="fa-solid fa-code-branch"></i> الفرع</label>
        <select name="branch">
          <option value="">الكل</option>
          <option value="الرياض" <?php echo (isset($branch) && $branch === 'الرياض') ? 'selected' : ''; ?>>الرياض</option>
          <option value="أبها" <?php echo (isset($branch) && $branch === 'أبها') ? 'selected' : ''; ?>>أبها</option>
          <option value="الخبر" <?php echo (isset($branch) && $branch === 'الخبر') ? 'selected' : ''; ?>>الخبر</option>
        </select>
      </span>

      <button class="btn btn-submit" type="submit"><i class="fa-solid fa-rotate"></i> عرض</button>
      <a class="btn" href="javascript:window.print()"><i class="fa-solid fa-print"></i> طباعة</a>
      <a class="btn btn-secondary" href="javascript:history.back()"><i class="fa-solid fa-arrow-right"></i> رجوع</a>
    </form>

    <div class="hint-pill">
      <i class="fa-solid fa-circle-info" style="color:var(--marsom-orange)"></i>
      المقارنة تتم على رقم الهوية فقط
    </div>
  </div>

  <?php foreach (($blocks ?? []) as $b): ?>
    <div class="a4-sheet">
      <div class="wm">MARSOOM • GOSI</div>

      <div class="header">
        <div class="brand">
          <?php if (($b['code'] ?? 1) == 2): ?>
            <img src="<?php echo base_url('assets/imeges/saleh.png'); ?>" alt="شعار">
          <?php else: ?>
            <img src="<?php echo base_url('assets/imeges/m1.png'); ?>" alt="شعار" style="height:40px; width:auto;">
          <?php endif; ?>
          <div>
            <h1>مقارنة GOSI مع سجل الموظفين — <?php echo $b['label']; ?></h1>
            <div style="margin-top:6px;color:var(--text-muted);font-weight:900;font-size:12px;">
              <i class="fa-solid fa-shield-halved" style="color:var(--marsom-orange)"></i>
              تقرير تدقيق بيانات الموظفين والتأمينات
            </div>
          </div>
        </div>
        <div class="meta">
          <div class="date"><i class="fa-regular fa-calendar"></i> التاريخ: <?php echo date('Y/m/d'); ?></div>
        </div>
      </div>

      <?php $s = $b['summary']; ?>
      <div class="summary">
        <div class="card">
          <div class="label"><i class="fa-solid fa-users" style="color:var(--marsom-orange)"></i> عدد الموظفين في التأمينات GOSI</div>
          <div class="value"><?php echo number_format($s['cntGosi']); ?></div>
        </div>
        <div class="card">
          <div class="label"><i class="fa-solid fa-id-card" style="color:var(--marsom-orange)"></i> عدد الموظفين في سجل الموظفين</div>
          <div class="value"><?php echo number_format($s['cntEmp']); ?></div>
        </div>
        <div class="card">
          <div class="label"><i class="fa-solid fa-sack-dollar" style="color:var(--marsom-orange)"></i> إجمالي الرواتب GOSI</div>
          <div class="value"><?php echo number_format($s['totalGosi'],2); ?> ر.س</div>
        </div>
        <div class="card">
          <div class="label"><i class="fa-solid fa-sack-dollar" style="color:var(--marsom-orange)"></i> إجمالي الرواتب سجل الموظفين</div>
          <div class="value"><?php echo number_format($s['totalEmp'],2); ?> ر.س</div>
        </div>
      </div>

      <div class="summary">
        <div class="card">
          <div class="label"><i class="fa-solid fa-arrow-right-arrow-left" style="color:var(--marsom-orange)"></i> فارق العدد (سجل الموظفين − GOSI)</div>
          <div class="value">
            <?php
              $cls = ($s['deltaCount']>0)?'up':(($s['deltaCount']<0)?'down':'flat');
              $lbl = ($s['deltaCount']>0)?'↑':(($s['deltaCount']<0)?'↓':'=');
              echo '<span class="badge '.$cls.'">'.$lbl.' '.number_format($s['deltaCount']).' ('.number_format($s['pctCount'],2).'%)</span>';
            ?>
          </div>
        </div>
        <div class="card">
          <div class="label"><i class="fa-solid fa-scale-balanced" style="color:var(--marsom-orange)"></i> فارق الإجمالي (سجل الموظفين − GOSI)</div>
          <div class="value">
            <?php
              $cls = ($s['deltaTotal']>0)?'up':(($s['deltaTotal']<0)?'down':'flat');
              $lbl = ($s['deltaTotal']>0)?'↑':(($s['deltaTotal']<0)?'↓':'=');
              echo '<span class="badge '.$cls.'">'.$lbl.' '.number_format($s['deltaTotal'],2).' ر.س ('.number_format($s['pctTotal'],2).'%)</span>';
            ?>
          </div>
        </div>
        <div class="card">
          <div class="label"><i class="fa-solid fa-circle-exclamation" style="color:var(--marsom-orange)"></i> إجمالي الفروقات في الرواتب (القيمة المطلقة)</div>
          <div class="value"><?php echo number_format($s['mismatchAbsDelta'],2); ?> ر.س</div>
        </div>
        <div class="card">
          <div class="label"><i class="fa-solid fa-note-sticky" style="color:var(--marsom-orange)"></i> ملاحظات</div>
          <div class="value">المقارنة تتم على رقم الهوية فقط.</div>
        </div>
      </div>

      <div class="section-title"><i class="fa-solid fa-user-xmark"></i> في التأمينات (GOSI) وغير موجودين في سجل الموظفين</div>
      <table class="print-table">
        <thead>
          <tr>
            <th>#</th> <th>رقم الهوية</th>
            <th>الاسم (GOSI)</th>
            <th>أساسي</th>
            <th>بدل سكن</th>
            <th>بدل عمولة</th>
            <th>بدلات أخرى</th>
            <th>الإجمالي</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($b['onlyGosi'])): ?>
            <?php $counter = 1; foreach ($b['onlyGosi'] as $r): ?>
              <tr>
                <td class="center"><?php echo $counter++; ?></td> <td class="center"><?php echo htmlspecialchars($r['id_number']); ?></td>
                <td><?php echo htmlspecialchars($r['name']); ?></td>
                <td class="center"><?php echo number_format($r['basic'],2); ?></td>
                <td class="center"><?php echo number_format($r['housing'],2); ?></td>
                <td class="center"><?php echo number_format($r['commission'],2); ?></td>
                <td class="center"><?php echo number_format($r['other'],2); ?></td>
                <td class="center"><strong><?php echo number_format($r['total'],2); ?></strong></td>
              </tr>
            <?php endforeach; ?>
            <?php $t=$b['onlyGosiTotals']; ?>
            <tr>
              <th colspan="3" class="center">الإجمالي</th>
              <th class="center"><?php echo number_format($t['basic'],2); ?></th>
              <th class="center"><?php echo number_format($t['housing'],2); ?></th>
              <th class="center"><?php echo number_format($t['commission'],2); ?></th>
              <th class="center"><?php echo number_format($t['other'],2); ?></th>
              <th class="center"><?php echo number_format($t['total'],2); ?></th>
            </tr>
          <?php else: ?>
            <tr><td colspan="8" class="center">لا توجد فروقات في هذا القسم.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>

      <div class="section-title"><i class="fa-solid fa-user-plus"></i> في سجل الموظفين وغير موجودين في التأمينات (GOSI)</div>
      <table class="print-table">
        <thead>
          <tr>
            <th>#</th> <th>رقم الهوية</th>
            <th>الاسم (EMP1)</th>
            <th>الرقم الوظيفي</th>
            <th>إجمالي الراتب</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($b['onlyEmp'])): ?>
            <?php
              $tot = 0; $counter = 1;
              foreach ($b['onlyEmp'] as $r):
              $tot += floatval($r['total_salary']);
            ?>
              <tr>
                <td class="center"><?php echo $counter++; ?></td> <td class="center"><?php echo htmlspecialchars($r['id_number']); ?></td>
                <td><?php echo htmlspecialchars($r['subscriber_name']); ?></td>
                <td class="center"><?php echo htmlspecialchars($r['employee_id']); ?></td>
                <td class="center"><strong><?php echo number_format($r['total_salary'],2); ?></strong></td>
              </tr>
            <?php endforeach; ?>
            <tr>
              <th colspan="4" class="center">إجمالي القسم</th>
              <th class="center"><?php echo number_format($tot,2); ?></th>
            </tr>
          <?php else: ?>
            <tr><td colspan="5" class="center">لا توجد فروقات في هذا القسم.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>

      <div class="section-title"><i class="fa-solid fa-triangle-exclamation"></i> فروقات الرواتب (موجود في الجدولين والقيم مختلفة)</div>
      <table class="print-table">
        <thead>
          <tr>
            <th>#</th> <th>رقم الهوية</th>
            <th>الاسم (GOSI)</th>
            <th>الرقم الوظيفي</th>
            <th>إجمالي الراتب (GOSI)</th>
            <th>إجمالي الراتب (EMP1)</th>
            <th>الفرق (EMP1 − GOSI)</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($b['mismatch'])): ?>
            <?php $sumDiff = 0; $counter = 1; foreach ($b['mismatch'] as $r): $sumDiff += $r['diff']; ?>
              <tr>
                <td class="center"><?php echo $counter++; ?></td> <td class="center"><?php echo htmlspecialchars($r['id_number']); ?></td>
                <td><?php echo htmlspecialchars($r['name_gosi']); ?></td>
                <td class="center"><?php echo htmlspecialchars($r['employee_id']); ?></td>
                <td class="center"><?php echo number_format($r['total_gosi'],2); ?></td>
                <td class="center"><?php echo number_format($r['total_emp'],2); ?></td>
                <td class="center">
                  <?php
                    $cls = ($r['diff']>0)?'up':(($r['diff']<0)?'down':'flat');
                    $lbl = ($r['diff']>0)?'↑':(($r['diff']<0)?'↓':'=');
                    echo '<span class="badge '.$cls.'">'.$lbl.' '.number_format($r['diff'],2).'</span>';
                  ?>
                </td>
              </tr>
            <?php endforeach; ?>
            <tr>
              <th colspan="6" class="center">مجموع الفروقات</th>
              <th class="center"><?php echo number_format($sumDiff,2); ?></th>
            </tr>
          <?php else: ?>
            <tr><td colspan="7" class="center">لا توجد فروقات في الرواتب.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>

    </div>
  <?php endforeach; ?>

  <?php if (empty($blocks)): ?>
    <div class="a4-sheet">
      <div class="wm">MARSOOM • GOSI</div>

      <div class="header">
        <div class="brand">
          <img src="<?php echo base_url('assets/imeges/m1.png'); ?>" alt="شعار">
          <h1>مقارنة GOSI مع سجل الموظفين</h1>
        </div>
        <div class="meta"><div class="date">التاريخ: <?php echo date('Y/m/d'); ?></div></div>
      </div>

      <div class="section-title"><i class="fa-solid fa-circle-info"></i> لا توجد بيانات</div>
      <div style="font-weight:900;color:#475467;">اختر الشركة من الأعلى ثم اضغط «عرض» لبدء المقارنة.</div>
    </div>
  <?php endif; ?>

</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var companySelect = document.getElementById('companySelect');
  var branchContainer = document.getElementById('branchFilterContainer');

  function toggleBranchFilter() {
    if (companySelect.value === '1') {
      branchContainer.style.display = 'inline-block';
    } else {
      branchContainer.style.display = 'none';
    }
  }

  companySelect.addEventListener('change', toggleBranchFilter);
  toggleBranchFilter();
});
</script>

</body>
</html>
