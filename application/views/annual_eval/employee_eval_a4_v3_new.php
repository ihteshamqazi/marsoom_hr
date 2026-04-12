<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title><?= html_escape($title ?? 'التقييم السنوي للموظف') ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

<style>
  /* =========================
     Theme
  ========================== */
  :root{
    --navy:#001f3f;
    --orange:#FF8C00;
    --bg:#f4f6f9;
    --card:#ffffff;
    --muted:#6b7280;
    --line:#e5e7eb;
    --soft:#f7f9fc;
    --ok:#137333;
    --bad:#b00020;
    --shadow: 0 10px 28px rgba(0,0,0,.08);
    --radius:16px;
  }

  *{ box-sizing:border-box; }
  html, body {
    width:100%;
    max-width:100%;
    overflow-x:hidden; /* ✅ يمنع أي شريط أفقي */
    font-family:'Tajawal',system-ui,-apple-system,"Segoe UI",Arial,sans-serif;
    background:var(--bg);
    color:#111;
    margin:0;
    padding:0;
  }

  /* =========================
     Layout
  ========================== */
  .page-wrap{
    max-width: 1100px;
    margin: 14px auto;
    padding: 0 12px 18px;
  }

  .sheet{
    background:var(--card);
    box-shadow:var(--shadow);
    border-radius: var(--radius);
    padding: 16px;
  }

  /* =========================
     Top Buttons
  ========================== */
  .no-print {
    display:flex;
    flex-wrap:wrap;
    gap:.5rem;
    justify-content:center;
    margin: 12px auto;
    padding: 0 12px;
  }
  .btn {
    border:1px solid var(--navy);
    color:var(--navy);
    background:#fff;
    padding:10px 14px;
    border-radius:12px;
    font-weight:900;
    cursor:pointer;
    text-decoration:none;
    display:inline-flex;
    align-items:center;
    gap:8px;
    transition:.15s ease;
    user-select:none;
  }
  .btn:hover { background:var(--navy); color:#fff; }
  .btn-submit { border-color:var(--orange); color:var(--orange); }
  .btn-submit:hover { background:var(--orange); color:#fff; }

  /* =========================
     Header
  ========================== */
  .header {
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:14px;
    padding-bottom:12px;
    border-bottom:2px solid var(--navy);
    margin-bottom:14px;
  }
  .brand {
    display:flex;
    align-items:center;
    gap:10px;
    min-width: 220px;
  }
  .brand img { height:46px; width:auto; }
  .brand h1 {
    font-size:18px;
    margin:0;
    font-weight:900;
    color:var(--navy);
    letter-spacing:.2px;
    line-height:1.2;
  }
  .meta {
    text-align:left;
    font-size:12px;
    color:#444;
    line-height:1.8;
  }
  .meta .date { font-weight:900; }

  /* =========================
     Summary
  ========================== */
  .summary {
    display:grid;
    grid-template-columns: repeat(4, 1fr);
    gap:10px;
    margin-top:10px;
    margin-bottom: 10px;
  }
  .kpi {
    border:1px solid var(--line);
    border-radius:14px;
    padding:10px 12px;
    background: #fbfcff;
  }
  .kpi .label { font-size:12px; color:var(--muted); font-weight:800; }
  .kpi .value { font-size:16px; font-weight:900; margin-top:4px; word-break: break-word; }

  .big-badge {
    display:inline-block;
    padding:.45rem .75rem;
    border-radius:12px;
    font-weight:900;
    font-size:14px;
    border:1px solid rgba(0,0,0,.08);
    background:#e6f4ea;
    color:var(--ok);
  }
  .muted{ color:var(--muted); font-size:12px; line-height:1.5; }

  /* =========================
     Section
  ========================== */
  .section-title {
    margin:14px 0 8px;
    padding:10px 12px;
    background:var(--soft);
    border-right:5px solid var(--orange);
    font-weight:900;
    border-radius: 12px;
  }

  /* =========================
     Tables (Desktop)
  ========================== */
  .table-wrap{
    width:100%;
    overflow:auto;
    border:1px solid var(--line);
    border-radius: 14px;
    background:#fff;
  }
  table.print-table {
    width:100%;
    border-collapse:collapse;
    min-width: 760px;
  }
  .print-table th, .print-table td {
    border-bottom:1px solid var(--line);
    padding:10px 10px;
    font-size:13px;
    vertical-align:top;
  }
  .print-table thead th {
    background:var(--navy);
    color:#fff;
    font-weight:900;
    text-align:center;
    white-space:nowrap;
  }
  .print-table tbody tr:hover{ background:#fbfdff; }
  .center { text-align:center; vertical-align:middle; }

  .desc-list{
    margin:0;
    padding:0 18px 0 0;
    line-height:1.6;
  }
  .desc-list li{ margin:2px 0; }

  .reason-box{
    font-size:12px;
    line-height:1.6;
    word-break: break-word;
    white-space: normal;
  }

  .diff-pos{color:var(--ok);font-weight:900}
  .diff-neg{color:var(--bad);font-weight:900}

  /* =========================
     Criteria Cards (Mobile/Tablet)
  ========================== */
  .criteria-cards{ display:none; } /* default hidden */
  .criteria-card{
    border:1px solid var(--line);
    border-radius:16px;
    background:#fff;
    padding:12px;
    margin:10px 0;
    box-shadow: 0 8px 18px rgba(0,0,0,.04);
  }
  .cc-head{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    margin-bottom:10px;
  }
  .cc-title{
    font-weight:900;
    color:var(--navy);
    font-size:14px;
    line-height:1.4;
  }
  .cc-max{
    font-size:12px;
    font-weight:900;
    color:#111;
    background:#f3f6ff;
    border:1px solid rgba(0,0,0,.07);
    padding:6px 10px;
    border-radius:999px;
    white-space:nowrap;
  }
  .cc-grid{
    display:grid;
    grid-template-columns: 1fr 1fr;
    gap:10px;
  }
  .cc-box{
    border:1px solid var(--line);
    border-radius:14px;
    padding:10px;
    background:#fbfcff;
    text-align:center;
  }
  .cc-box .t{ font-size:12px; color:var(--muted); font-weight:900; }
  .cc-box .n{ font-size:18px; font-weight:900; margin-top:4px; }
  .cc-diff{
    margin-top:10px;
    text-align:center;
    font-weight:900;
    padding:10px;
    border-radius:14px;
    border:1px solid var(--line);
    background:#fff;
  }

  details.cc-details{
    margin-top:10px;
    border:1px solid var(--line);
    border-radius:14px;
    background:#fff;
    padding:8px 10px;
  }
  details.cc-details > summary{
    cursor:pointer;
    list-style:none;
    font-weight:900;
    color:var(--navy);
    display:flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    user-select:none;
  }
  details.cc-details > summary::-webkit-details-marker{ display:none; }
  .cc-pill{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:8px 12px;
    border-radius:999px;
    border:1px solid rgba(0,0,0,.08);
    background:#f7f9fc;
    font-size:12px;
    font-weight:900;
  }
  .cc-body{
    margin-top:10px;
    border-top:1px dashed var(--line);
    padding-top:10px;
    font-size:13px;
    line-height:1.8;
  }
  .kv{
    display:grid;
    grid-template-columns: 120px 1fr;
    gap:8px 12px;
  }
  .kv .k{ color:var(--muted); font-weight:900; }
  .kv .v{ color:#111; font-weight:700; word-break:break-word; }

  /* =========================
     Notes
  ========================== */
  .glass {
    background: rgba(255,255,255,0.7);
    border:1px solid rgba(0,0,0,0.08);
    border-radius:14px;
    padding:12px;
    box-shadow: inset 0 1px 4px rgba(0,0,0,.06);
  }
  .notes-pre{
    font-family:'Tajawal',sans-serif;
    font-size:14px;
    direction:rtl;
    white-space:pre-wrap;
    margin:0;
  }

  /* =========================
     Responsive
  ========================== */
  @media (max-width: 992px){
    .summary{ grid-template-columns: repeat(2, 1fr); }
    .brand h1{ font-size:16px; }
    .meta{ text-align:right; }
    .header{ flex-direction: column; align-items:stretch; }
  }

  /* ✅ الجوال/الآيباد: نلغي جدول المعايير ونستخدم كروت */
  @media (max-width: 768px){
    .sheet{ padding:12px; }
    .summary{ grid-template-columns: 1fr; }

    /* إلغاء أي min-width تسبب سكرول */
    .table-wrap{ overflow: hidden; } /* ✅ يمنع شريط تمرير */
    table.print-table{ min-width: 0; }

    /* المعايير */
    .criteria-table{ display:none; }
    .criteria-cards{ display:block; }

    /* الجداول الأخرى نخليها مسموح تعرض بس بدون سكرول (تقدر تبقيها جدول، بس لا تجبر min-width) */
    .table-wrap.other-table{ overflow:auto; } /* الجداول الصغيرة ممكن سحبها لو اضطريت */
  }

  /* =========================
     Print (A4)
  ========================== */
  @media print {
    @page { size: A4; margin: 12mm 12mm 14mm 12mm; }
    body { background:#fff; }
    .page-wrap{ max-width:none; margin:0; padding:0; }
    .sheet { box-shadow:none; border-radius:0; padding:0; }
    .no-print { display:none !important; }
    .criteria-cards{ display:none !important; } /* الطباعة جدول فقط */
    .criteria-table{ display:block !important; }
    .table-wrap{ overflow: visible; border:0; }
  }

  .bd-box{
  margin-top:8px;
  border-top:1px dashed rgba(0,0,0,.12);
  padding-top:8px;
}
.bd-title{
  font-size:12px;
  font-weight:900;
  color:var(--muted);
  margin-bottom:6px;
}
.bd-grid{
  display:grid;
  grid-template-columns: 1fr auto;
  gap:6px 10px;
  font-size:12px;
  line-height:1.7;
}
.bd-name{ font-weight:800; color:#111; }
.bd-score{ text-align:left; font-weight:900; white-space:nowrap; }
.bd-low{ color:var(--bad); } /* الناقص */

</style>
</head>
<body>

<?php
$total_max = (float)($total_max ?? 120);
  $emp  = $emp ?? [];
  $self = $self ?? null;
  $sup  = $sup  ?? null;

  $get = function($arr, $k){
    return $arr ? (float)($arr[$k] ?? 0) : 0.0;
  };
  $fmt = function($v){ return number_format((float)$v, 2); };

  $self_total = $get($self, 'total_score');
  $sup_total  = $get($sup,  'total_score');

  $grade_self = $self ? (string)($self['grade_label'] ?? '-') : 'غير مُدخل';
  $grade_sup  = $sup  ? (string)($sup['grade_label']  ?? '-') : 'غير مُدخل';

  $final_total = $sup ? $sup_total : $self_total;
  $final_grade = $sup ? $grade_sup : $grade_self;

  $year = (int)($year ?? date('Y'));

 $self_reasons = [];
$sup_reasons  = [];
$self_breakdown = [];
$sup_breakdown  = [];

if ($self && !empty($self['reasons_json'])) {
  $tmp = json_decode($self['reasons_json'], true);
  if (is_array($tmp)) $self_reasons = $tmp;
}
if ($sup && !empty($sup['reasons_json'])) {
  $tmp = json_decode($sup['reasons_json'], true);
  if (is_array($tmp)) $sup_reasons = $tmp;
}

if ($self && !empty($self['breakdown_json'])) {
  $tmp = json_decode($self['breakdown_json'], true);
  if (is_array($tmp)) $self_breakdown = $tmp;
}
if ($sup && !empty($sup['breakdown_json'])) {
  $tmp = json_decode($sup['breakdown_json'], true);
  if (is_array($tmp)) $sup_breakdown = $tmp;
}

/**
 * ✅ يرجّع:
 * - سبب المعيار التراكمي ( _criterion )
 * - وأسباب البنود اللي فيها نص
 */
 $get_reason_block = function($reasons, $breakdown, $critKey, $parts){
  $arr = $reasons[$critKey] ?? [];
  if (!is_array($arr)) $arr = [];

  $bd = $breakdown[$critKey] ?? [];
  if (!is_array($bd)) $bd = [];

  $crit = trim((string)($arr['_criterion'] ?? ''));
  $lines = [];

  if (is_array($parts)) {
    foreach ($parts as $p){
      $pk   = (string)($p['k'] ?? '');
      $pmax = (float)($p['max'] ?? 0);
      if ($pk === '') continue;

      $t = trim((string)($arr[$pk] ?? ''));
      if ($t === '') continue;

      $v = (float)($bd[$pk] ?? 0);

      $lines[] = "• ".(string)($p['name'] ?? $pk)
        .": ".number_format($v,2,'.','')." / ".(int)$pmax
        ." — ".$t;
    }
  }

  if ($crit === '' && empty($lines)) return '-';

  $out = '';
  if ($crit !== '') $out .= $crit;
  if (!empty($lines)) {
    if ($out !== '') $out .= "\n";
    $out .= implode("\n", $lines);
  }
  return $out;
};
/**
 * ✅ جدول تفصيلي للبنود + مجموع
 */
 /**
 * ✅ عرض تفاصيل البنود: (الدرجة / الحد الأعلى)
 * - $only_low = true => يعرض فقط البنود الناقصة
 */
$render_breakdown = function($breakdown, $critKey, $parts, $only_low = false){
  $arr = $breakdown[$critKey] ?? [];
  if (!is_array($arr)) $arr = [];
  if (!is_array($parts) || empty($parts)) return '';

  $rows = [];
  foreach ($parts as $p){
    $pk = (string)($p['k'] ?? '');
    if ($pk === '') continue;

    $mx = (float)($p['max'] ?? 0);
    $v  = (float)($arr[$pk] ?? 0);

    if ($only_low && $v >= $mx) continue;

    $rows[] = [
      'name' => (string)($p['name'] ?? $pk),
      'v'    => $v,
      'mx'   => $mx,
      'low'  => ($v < $mx),
    ];
  }

  if (empty($rows)) return '';

  $html  = '<div class="bd-box">';
  $html .= '<div class="bd-title">تفاصيل الدرجات</div>';
  $html .= '<div class="bd-grid">';
  foreach ($rows as $r){
    $html .= '<div class="bd-name">'.html_escape($r['name']).'</div>';
    $cls = $r['low'] ? 'bd-score bd-low' : 'bd-score';
    $html .= '<div class="'.$cls.'">'.number_format($r['v'],2).' / '.(int)$r['mx'].'</div>';
  }
  $html .= '</div></div>';

  return $html;
};
  /* ✅ تحويل الشرح إلى نقاط مرتبة */
  $desc_to_list = function($text){
    $text = trim((string)$text);
    if ($text === '') return '<span class="muted">-</span>';

    if (strpos($text, "\n") !== false) {
      $parts = preg_split('/\r\n|\r|\n/', $text);
    } else {
      $parts = preg_split('/[،؛\.]+/u', $text);
    }

    $items = [];
    foreach ($parts as $p) {
      $p = trim($p);
      if ($p !== '') $items[] = $p;
    }

    if (count($items) <= 1) {
      return '<div style="line-height:1.8">'.nl2br(html_escape($text)).'</div>';
    }

    $html = '<ul class="desc-list">';
    foreach ($items as $it) $html .= '<li>'.html_escape($it).'</li>';
    $html .= '</ul>';
    return $html;
  };
?>

<!-- أزرار أعلى الصفحة -->
<div class="no-print">
  <a class="btn btn-submit" href="javascript:window.print()">طباعة</a>
  <a class="btn" href="javascript:history.back()">رجوع</a>
</div>

<div class="page-wrap">
  <div class="sheet">

    <!-- رأس -->
    <div class="header">
      <div class="brand">
        <img src="<?= base_url('assets/imeges/m1.PNG') ?>" alt="شعار">
        <h1>التقييم السنوي للموظف</h1>
      </div>
      <div class="meta">
        <div class="date">التاريخ: <?= date('Y/m/d') ?></div>
        <?php if(!empty($emp['emp_no'])): ?>
          <div>الرقم الوظيفي: <strong><?= html_escape($emp['emp_no']) ?></strong></div>
        <?php endif; ?>
        <div>سنة التقييم: <strong>2025</strong></div>
      </div>
    </div>

    <!-- ملخص -->
    <div class="summary">
      <div class="kpi">
        <div class="label">الاسم</div>
        <div class="value"><?= html_escape($emp['emp_name'] ?? '') ?></div>
      </div>
      <div class="kpi">
        <div class="label">المسمى الوظيفي</div>
        <div class="value"><?= html_escape($emp['job_title'] ?? '') ?></div>
      </div>
      <div class="kpi">
        <div class="label">المسؤول المباشر</div>
        <div class="value"><?= html_escape($emp['supervisor_name'] ?? '') ?></div>
      </div>
      <div class="kpi">
        <div class="label">النتيجة المعتمدة</div>
        <div class="value">
           <span class="big-badge"><?= $fmt($final_total) ?> / <?= $fmt($total_max) ?></span>
          <div class="muted" style="margin-top:6px;">التصنيف: <b><?= html_escape($final_grade) ?></b></div>
        </div>
      </div>
    </div>

    <!-- معلومات الموظف -->
    <div class="section-title">معلومات الموظف</div>
    <div class="table-wrap other-table" style="margin-bottom:10px;">
      <table class="print-table">
        <thead>
          <tr>
            <th>المسؤول المباشر</th>
            <th>المسمى الوظيفي</th>
            <th>الإدارة / القسم</th>
            <th>الاسم</th>
            <th>الرقم الوظيفي</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="center"><?= html_escape($emp['supervisor_name'] ?? '') ?></td>
            <td class="center"><?= html_escape($emp['job_title'] ?? '') ?></td>
            <td class="center"><?= html_escape($emp['department'] ?? '') ?></td>
            <td class="center"><?= html_escape($emp['emp_name'] ?? '') ?></td>
            <td class="center"><?= html_escape($emp['emp_no'] ?? '') ?></td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="table-wrap other-table">
      <table class="print-table">
        <thead>
          <tr>
            <th>نموذج التقييم</th>
            <th>سنة التقييم</th>
            <th>رقم المشرف</th>
            <th>اسم المشرف</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="center">نموذج <?= (int)($emp['form_type'] ?? 1) ?></td>
            <td class="center">2025</td>
            <td class="center"><?= html_escape($emp['supervisor_emp_no'] ?? '') ?></td>
            <td class="center"><?= html_escape($emp['supervisor_name'] ?? '') ?></td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- المعايير -->
    <div class="section-title">معايير التقييم</div>

    <!-- ✅ 1) عرض الجوال/الآيباد: كروت (بدون أي شريط تمرير) -->
    <div class="criteria-cards">
       <?php foreach(($criteria ?? []) as $c): ?>
  <?php
    $criterion_key = (string)($c['key'] ?? '');
    $score_column  = (string)($c['score_column'] ?? $criterion_key);

    $sv = $get($self, $score_column);
    $pv = $get($sup,  $score_column);
    $df = $pv - $sv;

    $desc_html = $desc_to_list($c['desc'] ?? '');
    $parts = $c['parts'] ?? [];

    $self_reason_txt = $self ? $get_reason_block($self_reasons, $self_breakdown, $criterion_key, $parts) : '-';
    $sup_reason_txt  = $sup  ? $get_reason_block($sup_reasons,  $sup_breakdown,  $criterion_key, $parts) : '-';

    $bd_self_html = '';
    $bd_sup_html  = '';
    if (!empty($parts)) {
      $bd_self_html = $render_breakdown($self_breakdown, $criterion_key, $parts, true);
      $bd_sup_html  = $render_breakdown($sup_breakdown,  $criterion_key, $parts, true);
    }
  ?>

        <div class="criteria-card">
          <div class="cc-head">
            <div class="cc-title"><?= html_escape($c['name'] ?? '') ?></div>
            <div class="cc-max">الحد الأعلى: <?= (int)($c['max'] ?? 0) ?></div>
          </div>

          <div class="cc-grid">
            <div class="cc-box">
              <div class="t">التقييم الذاتي</div>
              <div class="n"><?= $self ? $fmt($sv) : '-' ?></div>
            </div>
            <div class="cc-box">
              <div class="t">تقييم المسؤول المباشر</div>
              <div class="n"><?= $sup ? $fmt($pv) : '-' ?></div>
            </div>
          </div>

          <div class="cc-diff">
            الفرق:
            <?php if(!$self || !$sup): ?>
              <span>-</span>
            <?php else: ?>
              <span class="<?= $df_class ?>"><?= $df_text ?></span>
            <?php endif; ?>
          </div>

          <details class="cc-details">
            <summary><span class="cc-pill">تفاصيل المعيار</span></summary>
            <div class="cc-body">
              <div class="kv">
                <div class="k">الشرح</div>
                <div class="v"><?= $desc_html ?></div>
                <div class="k">تفاصيل الذاتي</div>
<div class="v"><?= $self ? ($bd_self_html ?: '-') : '-' ?></div>

<div class="k">تفاصيل المسؤول</div>
<div class="v"><?= $sup ? ($bd_sup_html  ?: '-') : '-' ?></div>


                <div class="k">أسباب الذاتي</div>
                <div class="v"><?= $self ? nl2br(html_escape($self_reason_txt)) : '-' ?></div>

                <div class="k">أسباب المسؤول</div>
                <div class="v"><?= $sup ? nl2br(html_escape($sup_reason_txt)) : '-' ?></div>
              </div>
            </div>
          </details>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- ✅ 2) عرض الكمبيوتر + الطباعة: جدول كامل -->
    <div class="criteria-table">
      <div class="table-wrap">
        <table class="print-table">
          <thead>
            <tr>
              <th>المعيار</th>
              <th>الشرح</th>
              <th>الحد الأعلى</th>
              <th>التقييم الذاتي</th>
              <th>أسباب التقييم الذاتي</th>
              <th>تقييم المسؤول</th>
              <th>أسباب تقييم المسؤول</th>
              <th>الفرق</th>
            </tr>
          </thead>

          <tbody>
          <?php foreach(($criteria ?? []) as $c): ?>
  <?php
    $criterion_key = (string)($c['key'] ?? '');
    $score_column  = (string)($c['score_column'] ?? $criterion_key);

    $sv = $get($self, $score_column);
    $pv = $get($sup,  $score_column);
    $df = $pv - $sv;

    $desc_html = $desc_to_list($c['desc'] ?? '');
    $parts = $c['parts'] ?? [];

    $self_reason_txt = $self ? $get_reason_block($self_reasons, $self_breakdown, $criterion_key, $parts) : '-';
    $sup_reason_txt  = $sup  ? $get_reason_block($sup_reasons,  $sup_breakdown,  $criterion_key, $parts) : '-';

    $bd_self_html = '';
    $bd_sup_html  = '';
    if (!empty($parts)) {
      $bd_self_html = $render_breakdown($self_breakdown, $criterion_key, $parts, true);
      $bd_sup_html  = $render_breakdown($sup_breakdown,  $criterion_key, $parts, true);
    }

    $df_text = (!$self || !$sup) ? '-' : (($df >= 0 ? '+' : '') . $fmt($df));
    $df_class = ($df >= 0) ? 'diff-pos' : 'diff-neg';
  ?>
            <tr>
              <td class="center"><b><?= html_escape($c['name']) ?></b></td>
              <td><?= $desc_html ?></td>
              <td class="center"><?= (int)($c['max'] ?? 0) ?></td>

              <td class="center"><?= $self ? $fmt($sv) : '-' ?></td>
              <td>
  <div class="reason-box"><?= $self ? nl2br(html_escape($self_reason_txt)) : '-' ?></div>
  <?php if($self && $bd_self_html): ?>
    <?= $bd_self_html ?>
  <?php endif; ?>
</td>

              <td class="center"><?= $sup ? $fmt($pv) : '-' ?></td>
              <td>
  <div class="reason-box"><?= $sup ? nl2br(html_escape($sup_reason_txt)) : '-' ?></div>
  <?php if($sup && $bd_sup_html): ?>
    <?= $bd_sup_html ?>
  <?php endif; ?>
</td>

              <td class="center">
                <?php if(!$self || !$sup): ?>
                  -
                <?php else: ?>
                  <span class="<?= $df >= 0 ? 'diff-pos':'diff-neg' ?>">
                    <?= ($df >= 0 ? '+' : '') . $fmt($df) ?>
                  </span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>

          <tfoot>
            <tr>
              <th colspan="3" class="center">الإجمالي</th>
              <th class="center"><?= $self ? $fmt($self_total) : '-' ?></th>
              <th class="center">—</th>
              <th class="center"><?= $sup  ? $fmt($sup_total)  : '-' ?></th>
              <th class="center">—</th>
              <th class="center">
                <?php if (!$self || !$sup): ?>
                  -
                <?php else: ?>
                  <?php $dft = $sup_total - $self_total; ?>
                  <span class="<?= $dft >= 0 ? 'diff-pos':'diff-neg' ?>">
                    <?= ($dft >= 0 ? '+' : '') . $fmt($dft) ?>
                  </span>
                <?php endif; ?>
              </th>
            </tr>

            <tr>
              <th colspan="3" class="center">التصنيف</th>
              <th class="center"><?= html_escape($grade_self) ?></th>
              <th class="center">—</th>
              <th class="center"><?= html_escape($grade_sup) ?></th>
              <th class="center">—</th>
              <th class="center">—</th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

    <!-- ملاحظات -->
    <div class="section-title">ملاحظات</div>
    <div class="glass">
      <div class="muted"><b>ملاحظات الموظف:</b></div>
      <pre class="notes-pre"><?= html_escape($self['notes'] ?? '') ?></pre>
      <hr style="border:none;border-top:1px solid rgba(0,0,0,.08);margin:10px 0;">
      <div class="muted"><b>ملاحظات المسؤول المباشر:</b></div>
      <pre class="notes-pre"><?= html_escape($sup['notes'] ?? '') ?></pre>
    </div>

    <!-- الاعتمادات -->
    <div class="section-title">الاعتمادات</div>
    <div class="table-wrap other-table">
      <table class="print-table">
        <thead>
          <tr>
            <th>اعتماد العضو المنتدب</th>
            <th>مدير إدارة الموارد البشرية</th>
            <th>المسؤول المباشر</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="center">سالم العجمي</td>
            <td class="center">منصور رجب</td>
            <td class="center"><?= html_escape($emp['supervisor_name'] ?? '') ?></td>
          </tr>
        </tbody>
      </table>
    </div>

    <div style="margin-top:10px; text-align:center; color:#555;">
      <div style="font-size:12px;">
        للاعتراض على التقييم نأمل تحميل التقييم وارسال الاعتراض عن طريق البريد الإلكتروني إلى الإدارة المعنية وشكراً
      </div>
    </div>

  </div>
</div>

</body>
</html>