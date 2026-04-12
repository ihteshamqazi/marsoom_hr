<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= html_escape($title ?? 'تقييم الأداء السنوي - تقييم المسؤول المباشر') ?></title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

  <style>
    :root{
      --marsom-blue:#001f3f;
      --marsom-orange:#FF8C00;
      --bg:#f4f6f9;
    }
    body{font-family:'Tajawal',sans-serif;background:var(--bg);color:#111}
    .shell{max-width:1200px;margin:0 auto;padding:22px 14px}
    .topbar{
      background:#fff;border:1px solid rgba(0,0,0,.06);
      border-radius:18px;box-shadow:0 10px 25px rgba(0,0,0,.06);
      padding:14px 16px;display:flex;gap:14px;align-items:center;justify-content:space-between;flex-wrap:wrap
    }
    .brand{display:flex;gap:10px;align-items:center}
    .brand img{height:54px;width:auto}
    .brand .ttl{margin:0;font-weight:800;color:var(--marsom-blue);font-size:18px}
    .meta{color:#555;font-size:13px;line-height:1.65}
    .pill{display:inline-block;padding:.35rem .7rem;border-radius:999px;font-weight:800;font-size:12px}
    .pill-year{background:#eef2ff;color:#3949ab}

    .cardx{
      background:#fff;border:1px solid rgba(0,0,0,.06);
      border-radius:18px;box-shadow:0 10px 25px rgba(0,0,0,.06);
      padding:16px;margin-top:14px
    }

    .kpi{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin-top:12px}
    @media(max-width:1000px){.kpi{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media(max-width:520px){.kpi{grid-template-columns:1fr}}

    .kpi .box{background:#fafbff;border:1px solid #e7ebf0;border-radius:14px;padding:10px 12px;display:flex;justify-content:space-between;gap:12px;align-items:center}
    .kpi .label{font-size:12px;color:#666}
    .kpi .value{font-size:18px;font-weight:800}

    .section-title{margin:14px 0 8px;padding:8px 10px;background:#f7f9fc;border-right:4px solid var(--marsom-orange);font-weight:800;border-radius:10px}

    .grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin-top:10px}
    @media(max-width:900px){.grid{grid-template-columns:1fr}}

    .crit{
      border:1px solid rgba(0,0,0,.07);border-radius:16px;padding:12px;
      background:linear-gradient(180deg,#ffffff 0%, #fbfcff 100%);
    }

    .crit-head{
      display:flex;
      justify-content:space-between;
      gap:12px;
      align-items:flex-start;
    }
    .crit-head > div:first-child{
      flex: 1 1 auto;
      min-width: 0;
    }

    .crit-title{font-weight:800;color:#111}
    .badge-max{
      background:rgba(255,140,0,.12);color:#7a3c00;border:1px solid rgba(255,140,0,.25);
      border-radius:999px;padding:.2rem .55rem;font-weight:800;font-size:12px;white-space:nowrap
    }

    /* ✅ مهم: عرض النص الطويل كسطور */
    .desc{
      color:#666;
      font-size:12.5px;
      line-height:1.85;
      margin-top:6px;
      white-space:pre-wrap;
      word-break:break-word;
    }

    .mini{color:#777;font-size:12px;margin-top:6px}
    .input-row{display:flex;gap:10px;align-items:center;justify-content:space-between;margin-top:10px;flex-wrap:wrap}
    .input-row input{max-width:240px}
    .readonly{background:#f1f3f6 !important;cursor:not-allowed}
    .err{display:none;color:#b00020;font-size:12px;margin-top:6px;font-weight:800}

    .actions{display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap;margin-top:14px}

    .btn-marsom{
      background:linear-gradient(135deg,#001f3f 0%, #003366 100%);
      border:none;
      color:#fff !important;
      font-weight:800;
      padding:10px 26px;
      border-radius:10px;
      box-shadow:0 6px 18px rgba(0,31,63,.25);
      transition:all .2s ease;
    }
    .btn-marsom:hover{
      transform:translateY(-1px);
      box-shadow:0 8px 22px rgba(0,31,63,.35);
      background:linear-gradient(135deg,#001733 0%, #00264d 100%);
      color:#fff !important;
    }

    .btn-outline-marsom{border-color:var(--marsom-blue);color:var(--marsom-blue);font-weight:800}
    .btn-outline-marsom:hover{background:var(--marsom-blue);color:#fff}

    .hint-ico{
      width:22px;height:22px;border-radius:50%;
      border:1px solid rgba(0,0,0,.12);
      display:inline-flex;align-items:center;justify-content:center;
      font-weight:900;color:#555;cursor:pointer;user-select:none;flex:0 0 auto
    }
    textarea{resize:vertical}
    .btn-smx{padding:.35rem .7rem;border-radius:10px;font-weight:800}

    .is-invalid{border-color:#dc3545 !important}
  </style>
</head>
<body>
<?php
$total_max = (float)($total_max ?? 120);
  $year = (int)($year ?? date('Y'));
  $criteria  = (isset($criteria) && is_array($criteria)) ? $criteria : [];
  $sup = (isset($sup) && is_array($sup)) ? $sup : [];
  $discipline   = (float)($discipline ?? 0);
  $courses_base = (float)($courses_base ?? 0);

  $form_type = (int)($emp['form_type'] ?? 1);
  $emp_no = (string)($emp['emp_no'] ?? '');

  $has_self    = !empty($has_self);
$has_sup     = !empty($has_sup);
$show_result = !empty($show_result);



  $reasons = (isset($reasons) && is_array($reasons)) ? $reasons : [];


  $breakdown = (isset($breakdown) && is_array($breakdown)) ? $breakdown : [];


  $get = function($k, $default='') use ($sup){
    return isset($sup[$k]) ? $sup[$k] : $default;
  };

  $save_url   = rtrim(base_url(),'/') . '/AnnualEvaluationSupervisor/save';
  $list_url   = rtrim(base_url(),'/') . '/AnnualEvaluationSupervisor?year=' . $year;
   $print_url  = rtrim(base_url(),'/') . '/AnnualEvaluation/print_a4/' . rawurlencode($emp_no) . '?year=' . $year;
?>
<div class="shell">

  <div class="topbar">
    <div class="brand">
      <img src="<?= base_url('assets/imeges/m1.PNG') ?>" alt="شعار مرسوم">
      <div>
        <p class="ttl mb-1">تقييم الأداء السنوي - تقييم المسؤول المباشر</p>
        <div class="meta">
          الموظف: <b><?= html_escape($emp['emp_name'] ?? '') ?></b>
          <span class="text-muted">|</span>
          رقم: <?= html_escape($emp_no) ?>
          <span class="text-muted">|</span>
          القسم: <?= html_escape($emp['department'] ?? '-') ?>
          <span class="text-muted">|</span>
          نموذج <?= $form_type ?>
        </div>
      </div>
    </div>

    <div class="meta text-start">
      <span class="pill pill-year">العام: <?= $year ?></span>
      <?php if (!empty($sup['total_score'])): ?>
        <div class="mt-2">آخر إجمالي: <b><?= number_format((float)$sup['total_score'],2,'.','') ?></b> / <?= number_format($total_max,2,'.','') ?></div>
        <div class="text-muted">التصنيف: <b><?= html_escape($sup['grade_label'] ?? '-') ?></b></div>
      <?php else: ?>
        <div class="mt-2 text-muted">لم يتم حفظ تقييم المسؤول المباشر لهذه السنة.</div>
      <?php endif; ?>
    </div>
  </div>

  <?php if (!empty($flash)): ?>
    <div class="cardx">
      <div class="alert alert-info mb-0"><?= html_escape($flash) ?></div>
    </div>
  <?php endif; ?>

  <div class="cardx">

    <div class="d-flex justify-content-between flex-wrap gap-2">
      <a class="btn btn-outline-secondary btn-smx" href="<?= $list_url ?>">رجوع لقائمة الموظفين</a>
      <div class="text-muted">قاعدة الدورات: <b><?= number_format($courses_base,2,'.','') ?></b> / 20</div>
    </div>

    <div class="kpi">
      <div class="box">
        <div>
          <div class="label">النموذج</div>
          <div class="value">نموذج <?= $form_type ?></div>
        </div>
      </div>

      <div class="box">
        <div>
          <div class="label">الانضباط (CSV)</div>
          <div class="value"><?= number_format($discipline,2,'.','') ?> / 20</div>
        </div>
      </div>

      <div class="box">
        <div>
          <div class="label">الإجمالي المباشر</div>
          <div class="value" id="liveTotal">0.00 / <?= number_format($total_max,2,'.','') ?></div>
        </div>
      </div>

      <div class="box">
       <!--  <div>
          <div class="label">معاينة الطباعة</div>
          <div class="value">
            <a class="btn btn-outline-marsom btn-smx" href="<?= $print_url ?>" target="_blank">فتح</a>
          </div>
        </div> -->
      </div>
    </div>

    <div class="section-title">معايير التقييم وإدخال الدرجات (مع سبب إلزامي لكل معيار)</div>

    <?php if (empty($criteria)): ?>
      <div class="alert alert-danger">
        <b>خطأ:</b> لم يتم تحميل معايير التقييم.<br>
        تأكد من تمرير <code>$data['criteria']</code> من الكنترولر إلى الفيو.
      </div>
    <?php else: ?>
<?php if ($has_sup): ?>
  <div class="cardx">
    <div class="alert alert-success mb-2">
      شكراً لك، تم تقييم الموظف مسبقاً ولا يمكن إدخال التقييم مرة أخرى.
    </div>

    <?php if ($show_result): ?>
      <div class="alert alert-info mb-0">
        اكتمل التقييم الذاتي وتقييم المسؤول المباشر، ويمكنك الآن الاطلاع على التقرير النهائي.
        <div class="mt-2">
          <a class="btn btn-outline-marsom"
             target="_blank"
             href="<?= site_url('AnnualEvaluation/print_a4/' . rawurlencode($emp_no) . '?year=' . (int)$year) ?>">
             عرض التقرير النهائي
          </a>
        </div>
      </div>
    <?php else: ?>
      <div class="alert alert-warning mb-0">
        تم حفظ تقييم المسؤول المباشر بنجاح، وسيتم إظهار النتيجة بعد اكتمال تقييم الموظف الذاتي.
      </div>
    <?php endif; ?>
  </div>
<?php endif; ?>
      <?php if (!$has_sup): ?>
<form method="post" action="<?= $save_url ?>" id="evalForm">
        <input type="hidden" name="year" value="<?= $year ?>">
        <input type="hidden" name="emp_no" value="<?= html_escape($emp_no) ?>">

        <div class="grid">
          <?php foreach ($criteria as $c): ?>
           <?php
  $k   = (string)$c['key'];
  $max = (float)$c['max'];
  $readonly = !empty($c['readonly']);

  $parts = $c['parts'] ?? [];
  if (!is_array($parts)) $parts = [];

  // احسب إجمالي المعيار الحالي من breakdown
  $crit_total = 0.0;

  if ($k === 'discipline_score') {
    $crit_total = $discipline;
  } else {
    $vals = $breakdown[$k] ?? [];
    if (!is_array($vals)) $vals = [];
    foreach ($parts as $p){
      $pk = (string)$p['k'];
      $crit_total += (float)($vals[$pk] ?? 0);
    }

    if (!empty($c['min_from_courses'])) {
      if ($crit_total < $courses_base) $crit_total = $courses_base;
      if ($crit_total > $max) $crit_total = $max;
    } else {
      if ($crit_total > $max) $crit_total = $max;
      if ($crit_total < 0) $crit_total = 0;
    }
  }

  $crit_reason_val = (string)($reasons[$k]['_criterion'] ?? '');
?>

<?php
  $reason_mode = !empty($c['min_from_courses']) ? 'positive_proof' : 'default';
?>
<div class="crit"
     data-crit="<?= html_escape($k) ?>"
     data-crit-max="<?= (float)$max ?>"
     data-min-base="<?= (float)$courses_base ?>"
     data-reason-mode="<?= html_escape($reason_mode) ?>">
  <div class="crit-head">
    <div>
      <div class="crit-title">
        <?= html_escape($c['name']) ?>
        <span class="badge-max">الحد الأعلى: <?= (int)$max ?></span>
      </div>
      <div class="desc"><?= html_escape($c['desc']) ?></div>
    </div>
    <div class="hint-ico" data-bs-toggle="tooltip" data-bs-title="<?= html_escape($c['desc']) ?>">?</div>
  </div>

  <!-- إجمالي المعيار -->
  <div class="input-row">
    <div class="w-100">
      <label class="mini fw-bold d-block mb-1">إجمالي المعيار</label>
      <input type="text" class="form-control readonly crit-total"
             value="<?= number_format($crit_total,2,'.','') ?> / <?= (int)$max ?>" readonly>
      <?php if (!empty($c['min_from_courses']) && !$readonly): ?>
        <div class="mini">الحد الأدنى = <?= number_format($courses_base,2,'.','') ?> (من الدورات) — يسمح بالزيادة فقط حتى <?= (int)$max ?>.</div>
      <?php endif; ?>
    </div>
  </div>

  <?php if ($readonly): ?>
    <div class="mini">هذه الدرجة تُسحب تلقائيًا من CSV الانضباط ولا يمكن تعديلها.</div>
  <?php else: ?>

    <!-- البنود التفصيلية -->
    <div class="mt-2">
      <?php foreach ($parts as $p): ?>
  <?php
    $pk        = (string)$p['k'];
    $pmax      = (float)$p['max'];
    $note_rule = (string)($p['note_rule'] ?? 'required_on_less');

    $val = $breakdown[$k][$pk] ?? 0;
    $val = (float)$val;

    $p_reason = (string)($reasons[$k][$pk] ?? '');
  ?>
        <div class="border rounded-3 p-2 mb-2 part-row" data-part-max="<?= (float)$pmax ?>">
          <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
            <div class="fw-bold"><?= html_escape($p['name']) ?> <span class="text-muted">(<?= (int)$pmax ?>)</span></div>

            <input
  type="number"
  class="form-control part-field"
  style="max-width:180px"
  name="breakdown[<?= html_escape($k) ?>][<?= html_escape($pk) ?>]"
  step="0.01"
  min="0"
  max="<?= number_format($pmax,2,'.','') ?>"
  data-crit="<?= html_escape($k) ?>"
  data-part="<?= html_escape($pk) ?>"
  data-max="<?= number_format($pmax,2,'.','') ?>"
  data-note-rule="<?= html_escape($note_rule) ?>"
  value="<?= number_format($val,2,'.','') ?>"
  required
>
          </div>

          <div class="mt-2 part-reason-wrap" style="display:none;">
            <label class="mini fw-bold d-block mb-1">الملاحظة / الإثبات</label>
            <textarea
              class="form-control part-reason"
              name="reasons[<?= html_escape($k) ?>][<?= html_escape($pk) ?>]"
              rows="2"
              placeholder="اكتب الملاحظة أو الإثبات المطلوب لهذا البند..."
            ><?= html_escape($p_reason) ?></textarea>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- سبب المعيار (تراكمي) -->
    <div class="mt-2">
      <label class="mini fw-bold d-block mb-1">سبب المعيار (تراكمي)</label>
      <textarea
        class="form-control crit-reason"
        name="reasons[<?= html_escape($k) ?>][_criterion]"
        rows="3"
        readonly
        required
        placeholder="سيتم تعبئته تلقائيًا من أسباب البنود عند نقصان الدرجة..."
      ><?= html_escape($crit_reason_val) ?></textarea>
      <div class="mini text-muted">يتم تعبئة هذا الحقل تلقائيًا إذا تم إدخال أي بند أقل من درجته القصوى.</div>
    </div>

  <?php endif; ?>
</div>

          <?php endforeach; ?>
        </div>

        <div class="section-title">ملاحظات عامة (اختياري)</div>
        <textarea class="form-control" name="notes" rows="5"><?= html_escape($get('notes','')) ?></textarea>

        <div class="actions">
          <button class="btn btn-marsom" type="submit">حفظ تقييم المسؤول المباشر</button>
          <a class="btn btn-outline-secondary" href="<?= current_url().'?year='.$year ?>">تحديث</a>
        </div>
     </form>
<?php endif; ?>

    <?php endif; ?>

  </div>

</div>
<!-- Modal: إقرار نهائي قبل الحفظ -->
<div class="modal fade" id="finalConfirmModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border-radius:22px; border:none; overflow:hidden;">
      <div class="modal-header" style="background:linear-gradient(135deg,#001f3f 0%,#003366 100%); color:#fff;">
        <h5 class="modal-title fw-bold">تأكيد واعتماد التقييم النهائي</h5>
        <button type="button" class="btn-close btn-close-white m-0" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body p-4">
        <div class="d-flex align-items-start gap-3">
          <div style="width:56px;height:56px;border-radius:50%;background:rgba(255,140,0,.12);display:flex;align-items:center;justify-content:center;font-size:26px;flex:0 0 auto;">
            ✅
          </div>

          <div>
            <div class="fw-bold mb-2" style="font-size:18px;color:#001f3f;">
              يرجى تأكيد الإقرار التالي قبل الحفظ
            </div>

            <div style="line-height:2;color:#333;font-size:15px;">
              أقر وأتعهد بأن جميع بيانات التقييم المدخلة صحيحة، وأن لدي ما يثبت البنود التي تتطلب إثباتًا، وأوافق على اعتماد هذا التقييم بشكل نهائي، مع علمي بأنه لن يكون بالإمكان إعادة تقييم الموظف مرة أخرى بعد الحفظ.
            </div>
          </div>
        </div>

        <div class="form-check mt-4 p-3" style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:14px;">
          <input class="form-check-input" type="checkbox" id="finalConfirmCheck">
          <label class="form-check-label fw-bold" for="finalConfirmCheck">
            أوافق على الإقرار أعلاه وأرغب في اعتماد التقييم نهائيًا
          </label>
        </div>

        <div id="finalConfirmError" class="text-danger fw-bold mt-2" style="display:none;font-size:13px;">
          يلزم الموافقة على الإقرار قبل إتمام الحفظ.
        </div>
      </div>

      <div class="modal-footer justify-content-between px-4 pb-4" style="border-top:none;">
        <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">رجوع</button>
        <button type="button" id="confirmFinalSubmitBtn" class="btn btn-marsom px-4">
          اعتماد نهائي وحفظ التقييم
        </button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));

function num(v){
  v = Number(v);
  return Number.isFinite(v) ? v : 0;
}

function clamp(v, min, max){
  v = num(v);
  if (v < min) v = min;
  if (v > max) v = max;
  return v;
}

function normalizeSpaces(str){
  return (str || '').replace(/\s+/g, ' ').trim();
}

function lettersOnlyText(str){
  str = normalizeSpaces(str);
  return /^[A-Za-z\u0600-\u06FF\s]+$/.test(str);
}

function validateReasonText(text, minLen){
  const t = normalizeSpaces(text);
  if (t.length < minLen) {
    return 'يجب ألا يقل النص عن ' + minLen + ' حرفًا.';
  }
  if (!lettersOnlyText(t)) {
    return 'يسمح بالحروف والمسافات فقط، ولا يسمح بالأرقام أو الرموز.';
  }
  return '';
}

function setFieldError(textarea, message){
  let box = textarea.closest('.part-reason-wrap');
  if (!box) return;

  let err = box.querySelector('.reason-error');
  if (!err) {
    err = document.createElement('div');
    err.className = 'reason-error text-danger fw-bold mt-1';
    err.style.fontSize = '12px';
    box.appendChild(err);
  }

  if (message) {
    textarea.classList.add('is-invalid');
    err.textContent = message;
    err.style.display = 'block';
  } else {
    textarea.classList.remove('is-invalid');
    err.textContent = '';
    err.style.display = 'none';
  }
}

function recalcCriterion(critKey){
  const critBox = document.querySelector('.crit[data-crit="'+critKey+'"]');
  if (!critBox) return;

  const critMax = num(critBox.dataset.critMax);
  const minBase = num(critBox.dataset.minBase);
  const reasonMode = critBox.dataset.reasonMode || 'default';

  let total = 0;
  let reasonsLines = [];
  let hasAnyRequiredReason = false;

  critBox.querySelectorAll('.part-row').forEach(row => {
    const inp = row.querySelector('.part-field');
    if (!inp) return;

    const pmax = num(inp.dataset.max);
    let v = clamp(inp.value, 0, pmax);
    if (num(inp.value) !== v) inp.value = v.toFixed(2);

    total += v;

    const wrap = row.querySelector('.part-reason-wrap');
    const txt  = row.querySelector('.part-reason');
    const label = row.querySelector('.fw-bold')
      ? row.querySelector('.fw-bold').innerText.replace(/\s*\(\d+\)\s*/,'')
      : '';

     let reasonRequired = false;
let minLen = 15;
const partRule = inp.dataset.noteRule || 'required_on_less';

if (partRule === 'optional') {
  reasonRequired = false;
  minLen = 0;
} else if (partRule === 'required_on_positive') {
  reasonRequired = (v > 0);
  minLen = 20;
} else {
  reasonRequired = (v < pmax);
  minLen = 15;
}

    if (reasonRequired) {
      hasAnyRequiredReason = true;

      if (wrap) wrap.style.display = 'block';
      if (txt){
        txt.required = true;

        const msg = validateReasonText(txt.value || '', minLen);
        setFieldError(txt, msg);

        if (!msg) {
          reasonsLines.push(label + ' : ' + normalizeSpaces(txt.value));
        }
      }
    } else {
      if (wrap) wrap.style.display = 'none';
      if (txt){
        txt.required = false;
        setFieldError(txt, '');
      }
    }
  });

  if (reasonMode === 'positive_proof' && total < minBase) total = minBase;
  if (total > critMax) total = critMax;

  const totalEl = critBox.querySelector('.crit-total');
  if (totalEl) totalEl.value = total.toFixed(2) + ' / ' + critMax;

  const critReason = critBox.querySelector('.crit-reason');
  if (critReason){
    if (reasonsLines.length){
      critReason.value = reasonsLines.join("\n");
      critReason.required = true;
    } else {
      critReason.value = '';
      critReason.required = false;
      critReason.classList.remove('is-invalid');
    }
  }

  critBox.dataset.hasRequiredReason = hasAnyRequiredReason ? '1' : '0';
}

function recalcTotal(){
  let total = 0;

  document.querySelectorAll('.crit-total').forEach(el => {
    total += num(String(el.value).split('/')[0]);
  });

  const live = document.getElementById('liveTotal');
  if (live) live.textContent = total.toFixed(2) + ' / <?= number_format($total_max,2,'.','') ?>';
}

// init
document.querySelectorAll('.crit').forEach(box => {
  const critKey = box.dataset.crit;
  if (critKey && critKey !== 'discipline_score'){
    recalcCriterion(critKey);
  }
});
recalcTotal();

// events
document.querySelectorAll('.part-field').forEach(inp => {
  inp.addEventListener('input', () => {
    recalcCriterion(inp.dataset.crit);
    recalcTotal();
  });
  inp.addEventListener('blur', () => {
    recalcCriterion(inp.dataset.crit);
    recalcTotal();
  });
});

document.querySelectorAll('.part-reason').forEach(txt => {
  txt.addEventListener('input', () => {
    const box = txt.closest('.crit');
    if (!box) return;
    recalcCriterion(box.dataset.crit);
    recalcTotal();
  });
});

// submit validation
 const f = document.getElementById('evalForm');
const finalConfirmModalEl = document.getElementById('finalConfirmModal');
const finalConfirmCheck = document.getElementById('finalConfirmCheck');
const finalConfirmError = document.getElementById('finalConfirmError');
const confirmFinalSubmitBtn = document.getElementById('confirmFinalSubmitBtn');

let finalModalInstance = null;
let allowRealSubmit = false;

if (finalConfirmModalEl) {
  finalModalInstance = new bootstrap.Modal(finalConfirmModalEl);
}

function validateEvalFormBeforeSubmit(){
  let ok = true;

  document.querySelectorAll('.crit').forEach(box => {
    const critKey = box.dataset.crit;
    if (critKey && critKey !== 'discipline_score'){
      recalcCriterion(critKey);
    }
  });

  document.querySelectorAll('.part-reason').forEach(t => {
    if (t.required) {
      const critBox = t.closest('.crit');
      const row = t.closest('.part-row');
const inp = row ? row.querySelector('.part-field') : null;
const partRule = inp?.dataset?.noteRule || 'required_on_less';

let minLen = 15;
if (partRule === 'required_on_positive') {
  minLen = 20;
} else if (partRule === 'required_on_less') {
  minLen = 15;
} else {
  minLen = 0;
}

const msg = (minLen > 0) ? validateReasonText(t.value || '', minLen) : '';
      setFieldError(t, msg);
      if (msg) ok = false;
    }
  });

  document.querySelectorAll('.crit').forEach(box => {
    const critReason = box.querySelector('.crit-reason');
    const mustHave = box.dataset.hasRequiredReason === '1';

    if (critReason) {
      const visibleReasons = normalizeSpaces(critReason.value || '');

      if (mustHave && !visibleReasons) {
        ok = false;
        critReason.classList.add('is-invalid');
      } else {
        critReason.classList.remove('is-invalid');
      }
    }
  });

  recalcTotal();
  return ok;
}

if (f){
  f.addEventListener('submit', function(e){
    if (allowRealSubmit) return true;

    e.preventDefault();

    const ok = validateEvalFormBeforeSubmit();
    if (!ok){
      alert('فضلاً تأكد من كتابة الأسباب أو الإثباتات المطلوبة بالشروط المحددة قبل الحفظ.');
      return false;
    }

    if (finalConfirmCheck) finalConfirmCheck.checked = false;
    if (finalConfirmError) finalConfirmError.style.display = 'none';

    if (finalModalInstance) {
      finalModalInstance.show();
    }
    return false;
  });
}

if (confirmFinalSubmitBtn){
  confirmFinalSubmitBtn.addEventListener('click', function(){
    if (!finalConfirmCheck || !finalConfirmCheck.checked) {
      if (finalConfirmError) finalConfirmError.style.display = 'block';
      return;
    }

    if (finalConfirmError) finalConfirmError.style.display = 'none';

    allowRealSubmit = true;
    if (finalModalInstance) finalModalInstance.hide();
    f.submit();
  });
}
</script>
</body>
</html>