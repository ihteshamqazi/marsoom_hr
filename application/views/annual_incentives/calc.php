<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= html_escape($title ?? 'احتساب الحوافز') ?></title>

  <!-- Bootstrap 4.6.2 (كما تريد عشان المودال) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@600;700&family=Tajawal:wght@400;600;800&display=swap" rel="stylesheet">
  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <style>
    :root{
      --primary-blue:#001f3f;
      --primary-orange:#FF8C00;
      --secondary-blue:#0a3d62;
      --dark-bg:#0d1b2a;
      --darker-bg:#0a1929;

      --glass-bg: rgba(255,255,255,.05);
      --glass-border: rgba(255,255,255,.14);

      --text-light:#fff;
      --text-muted: rgba(255,255,255,.65);

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
      color: var(--text-light);
      overflow-x:hidden;
      background-attachment: fixed;
    }

    /* خلفية Pattern */
    .bg-pattern{
      position:fixed; inset:0;
      background-image:
        radial-gradient(circle at 10% 20%, rgba(255,140,0,.06) 0%, transparent 22%),
        radial-gradient(circle at 90% 80%, rgba(0,31,63,.06) 0%, transparent 22%),
        linear-gradient(45deg, transparent 48%, rgba(255,140,0,.03) 50%, transparent 52%),
        linear-gradient(-45deg, transparent 48%, rgba(0,31,63,.03) 50%, transparent 52%);
      background-size: 420px 420px, 420px 420px, 110px 110px, 110px 110px;
      z-index:-2;
      animation: patternMove 18s linear infinite;
    }
    @keyframes patternMove{
      0%{background-position:0 0,0 0,0 0,0 0}
      100%{background-position:420px 420px,420px 420px,110px 110px,110px 110px}
    }

    .wrap{
      max-width: 1400px;
      margin: 24px auto;
      padding: 0 14px;
      position:relative;
      z-index:1;
    }

    /* Header Glass */
    .header-nav{
      background: rgba(255,255,255,.03);
      border: 1px solid var(--glass-border);
      border-radius: var(--radius-xxl);
      box-shadow: 0 8px 32px rgba(0,0,0,.28);
      backdrop-filter: blur(18px);
      -webkit-backdrop-filter: blur(18px);
      padding: 16px 18px;
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:12px;
      margin-bottom: 14px;
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
      color:#fff;
    }
    .title-box p{
      margin:6px 0 0;
      color: var(--text-muted);
      font-size: .95rem;
    }

    /* Buttons (Marsom style) */
    .btn-marsom{
      border: 1px solid rgba(255,255,255,.18);
      background: rgba(255,255,255,.06);
      color:#fff !important;
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
      color:#fff !important;
      text-decoration:none;
    }
    .btn-marsom.warn{
      background: linear-gradient(135deg, rgba(255,193,7,.20), rgba(255,193,7,.08));
      border-color: rgba(255,193,7,.35);
    }
    .btn-marsom.gray{
      background: rgba(255,255,255,.05);
      border-color: rgba(255,255,255,.22);
    }

    /* Glass cards */
    .glass-card{
      background: var(--glass-bg);
      border: 1px solid var(--glass-border);
      border-radius: var(--radius-xxl);
      box-shadow: var(--shadow-sm);
      backdrop-filter: blur(18px);
      -webkit-backdrop-filter: blur(18px);
      padding: 16px;
      margin-bottom: 14px;
      position:relative;
      overflow:hidden;
    }
    .glass-card::before{
      content:'';
      position:absolute; top:0; left:0; right:0;
      height:3px;
      background: linear-gradient(90deg, var(--primary-orange), var(--primary-blue));
      opacity:.9;
    }

    /* Muted text in dark UI */
    .muted{ color: var(--text-muted) !important; }

    /* Chips */
    .chip{
      background: rgba(255,255,255,.08);
      border: 1px solid rgba(255,255,255,.16);
      border-radius: 999px;
      padding: .35rem .8rem;
      font-size: 12px;
      font-weight: 900;
      color:#fff;
      display:inline-flex;
      align-items:center;
      gap:8px;
      white-space:nowrap;
    }

    /* Remaining money */
    .money{
      font-weight: 900;
      font-size: 18px;
      letter-spacing:.3px;
      color:#fff;
    }

    /* Status box */
    .dangerBox{
      background: rgba(220,53,69,.12);
      border: 1px solid rgba(220,53,69,.25);
      border-radius: 16px;
      padding: 10px;
      color:#fff;
    }
    .okBox{
      background: rgba(25,135,84,.12);
      border: 1px solid rgba(25,135,84,.25);
      border-radius: 16px;
      padding: 10px;
      color:#fff;
    }

    /* Employee cards -> أبيض (نفس مفهوم الجدول) */
    .empCard{
      background:#fff;
      border:1px solid #eef1f6;
      border-radius:16px;
      box-shadow: 0 12px 26px rgba(0,0,0,.08);
    }
    .empTop{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
    .empName{font-weight:900; color:#000;}
    .empCard .small{ color:#6c757d !important; }
    .empCard .money{ color:#000; } /* قيمة الحافز داخل الكرت أسود */
    .toggleDetails{cursor:pointer; border-radius:12px; font-weight:900;}

    .selectSmall{
      border-radius:12px;
      font-weight:900;
    }

    /* Card title inside glass */
    .cardTitle{
      font-weight: 900;
      color:#fff;
    }

    /* Divider */
    hr{ border-color: rgba(255,255,255,.12) !important; }

    @media (max-width: 576px){
      .money{font-size:16px}
    }

    /* ✅ Fix: Modal text colors (Bootstrap 4) */
#budgetModal .modal-content{
  background:#ffffff !important;
  color:#111 !important;
}

#budgetModal .modal-header{
  background:#fff5f5 !important;
  border-bottom:1px solid #ffd6d6 !important;
}

#budgetModal .modal-title{
  color:#111 !important;
  font-weight:900;
}

#budgetModal .modal-body{
  color:#111 !important;
  background:#fff !important;
  font-weight:700;
  line-height:1.7;
}

#budgetModal .modal-footer{
  background:#fff !important;
  border-top:1px solid #eee !important;
}

#budgetModal .close,
#budgetModal .close span{
  color:#111 !important;
  opacity:1 !important;
  text-shadow:none !important;
}
#budgetModal .close:hover,
#budgetModal .close:focus{
  opacity:1 !important;
  outline:none !important;
}

.evalRow{display:flex;gap:8px;flex-wrap:wrap;margin-top:10px}
.evalChip{
  background:#f8f9fa;
  border:1px solid #e9ecef;
  color:#000;
  border-radius:999px;
  padding:.35rem .7rem;
  font-size:12px;
  font-weight:900;
}
.evalChip b{font-weight:900}
.evalDiffPos{color:#137333}
.evalDiffNeg{color:#b00020}

/* إصلاح لون عنوان المودال */
.modal-header {
  background: #ffffff !important;
}

.modal-title {
  color: #111 !important;
  font-weight: 900;
}

/* زر الإغلاق */
.modal-header .close,
.modal-header .close span {
  color: #111 !important;
  opacity: 1 !important;
}

/* لو فيه عنوان داخل header */
.modal-header h5 {
  color: #111 !important;
}










  </style>
</head>

<body>
  <div class="bg-pattern"></div>

  <div class="wrap">

    <!-- Header -->
    <div class="header-nav">
      <div class="title-box">
        <h1>احتساب الحوافز</h1>
        <p>
          <?= html_escape($batch['batch_name'] ?? '') ?> — سنة <?= html_escape($batch['batch_year'] ?? '') ?>
        </p>
      </div>

      <div class="d-flex gap-2 flex-wrap">
        <a href="<?= site_url('AnnualIncentives/employees/'.(int)$batch['id']) ?>" class="btn-marsom gray">
          <i class="fas fa-arrow-right"></i> رجوع للموظفين
        </a>

         
      </div>

       <div class="d-flex gap-2 flex-wrap">
       

        <a href="<?= site_url('AnnualIncentives')?>" class="btn-marsom gray">
          <i class="fas fa-arrow-right"></i>   الرئيسية 
        </a>
      </div>
    </div>

    <!-- Budget / Base -->
    <div class="glass-card">
      <div class="d-flex flex-wrap align-items-center justify-content-between">
        <div>
          <div class="font-weight-bold mb-1 cardTitle"><i class="fas fa-wallet"></i> الميزانية</div>
          <div class="muted">
            الإجمالي:
            <span class="chip" id="budgetTotal">
              <?= html_escape($batch['budget_total'] ?? '') ?>
            </span>
          </div>
        </div>
        <div class="mt-2 mt-md-0 text-md-left">
          <div class="font-weight-bold mb-1 cardTitle"><i class="fas fa-chart-pie"></i> المتبقي</div>
          <div class="money" id="budgetRemaining">—</div>
        </div>
      </div>

      <hr>

      <div class="muted">
        <div class="font-weight-bold mb-2 cardTitle"><i class="fas fa-sliders"></i> قاعدة الاحتساب:</div>

        <div class="d-flex flex-wrap align-items-center" style="gap:10px;">
          <button id="btnResetAll" class="btn-marsom warn" type="button">
            <i class="fas fa-trash"></i> حذف كل الاحتساب
          </button>

          <?php if(($batch['calc_mode'] ?? 'total') === 'total'): ?>
            <span class="chip">إجمالي الراتب (total_salary)</span>
          <?php else: ?>
            <?php if((($batch['use_base_salary']??'0')=='1')): ?><span class="chip">الراتب الأساسي</span><?php endif; ?>
            <?php if((($batch['use_housing_allowance']??'0')=='1')): ?><span class="chip">بدل السكن</span><?php endif; ?>
            <?php if((($batch['use_transport_allowance']??'0')=='1')): ?><span class="chip">بدل المواصلات</span><?php endif; ?>
            <?php if((($batch['use_other_allowances']??'0')=='1')): ?><span class="chip">بدلات أخرى</span><?php endif; ?>
          <?php endif; ?>
        </div>
      </div>

      <div class="mt-3">
        <div id="statusBox" class="dangerBox d-none"></div>
      </div>
    </div>

    <!-- Employees list -->
    <div class="glass-card">
      <div class="d-flex align-items-center justify-content-between">
        <div class="cardTitle"><i class="fas fa-users"></i> قائمة الموظفين</div>
        <div class="chip">العدد: <?= count($rows) ?></div>
      </div>

      <div class="mt-3" id="empList">
        <?php if(!empty($rows)): foreach($rows as $r): ?>
          <?php
  $row_id = (int)$r['id'];
  $calc_base = (float)($r['_calc_base_amount'] ?? 0);

  $emp_no = trim((string)($r['employee_id'] ?? ''));
  $eval_year = (int)($r['_eval_year'] ?? ($batch['batch_year'] ?? date('Y')));

  $self_total = isset($r['_self_total']) ? $r['_self_total'] : null;
  $sup_total  = isset($r['_sup_total'])  ? $r['_sup_total']  : null;
  $diff_total = isset($r['_eval_diff'])  ? $r['_eval_diff']  : null;

  $fmt2 = function($v){
    return number_format((float)$v, 2, '.', ',');
  };
?>
          <div class="empCard p-3 mb-2"
               data-row-id="<?= $row_id ?>"
               data-base="<?= $calc_base ?>"
               data-current-incentive="<?= (float)($r['incentive_amount'] ?? 0) ?>">

            <div class="empTop">
              <div>
                <div class="empName">
                  <?= html_escape($r['subscriber_name'] ?? '') ?>
                  <?php if(!empty($r['profession'])): ?>
                    (<?= html_escape($r['profession']) ?>)
                  <?php endif; ?>
                </div>

                <div class="small">
                  رقم وظيفي: <?= html_escape($r['employee_id'] ?? '') ?>
                  — هوية: <?= html_escape($r['id_number'] ?? '') ?>
                </div>

                <div class="evalRow">
  <span class="evalChip">
       التقييم الذاتي: <b><?= ($self_total === null ? '—' : $fmt2($self_total)) ?></b>
  </span>

  <span class="evalChip">
     تقييم المسؤول المباشر: <b><?= ($sup_total === null ? '—' : $fmt2($sup_total)) ?></b>
  </span>

  <span class="evalChip">
    الفرق:
    <?php if ($diff_total === null): ?>
      <b>—</b>
    <?php else: ?>
      <?php $cls = ($diff_total >= 0) ? 'evalDiffPos' : 'evalDiffNeg'; ?>
      <b class="<?= $cls ?>"><?= ($diff_total >= 0 ? '+' : '') . $fmt2($diff_total) ?></b>
    <?php endif; ?>
  </span>

  <button
    type="button"
    class="btn btn-outline-primary btn-sm toggleEval"
    data-empno="<?= html_escape($emp_no) ?>"
    data-year="<?= (int)$eval_year ?>"
    style="border-radius:12px;font-weight:900;">
    <i class="fas fa-print"></i>  عرض تفاصيل التقييم  
  </button>
</div>

              </div>

              <div class="text-left">
                <div class="money" data-money>0.00</div>
                <div class="small">قيمة الحافز</div>
              </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mt-3 flex-wrap">
              <div class="d-flex align-items-center flex-wrap" style="gap:10px;">
                <div class="text-muted" style="font-weight:900;">التزايد:</div>
                <select class="form-control selectSmall" style="width:170px;" data-mult data-current="<?= (float)($r['multiplier'] ?? 0) ?>">
                  <?php
                    $options = [];
                    for($x=0; $x<=28; $x++){
                      $m = $x * 0.25;
                      $label = number_format($m, 2). ' راتب';
                      $options[] = ['v'=>number_format($m,2,'.',''),'l'=>$label];
                    }
                    foreach($options as $op):
                  ?>
                    <option value="<?= $op['v'] ?>"><?= $op['l'] ?></option>
                  <?php endforeach; ?>
                </select>

                <span class="chip" style="background:rgba(0,0,0,.06);border-color:#e9ecef;color:#000;">
                  قاعدة الحساب: <b data-base-label><?= number_format($calc_base,2,'.',',') ?></b>
                </span>
              </div>

              <div class="mt-2 mt-md-0">
                <button class="btn btn-outline-dark btn-sm toggleDetails" type="button">
                  <i class="fas fa-circle-info"></i> تفاصيل
                </button>
              </div>
            </div>

            <div class="details mt-3" style="display:none">
              <div class="row">
                <div class="col-md-3 col-6 mb-2"><div class="chip w-100" style="background:#f8f9fa;border-color:#e9ecef;color:#000;">إجمالي الراتب: <?= html_escape($r['total_salary'] ?? '') ?></div></div>
                <div class="col-md-3 col-6 mb-2"><div class="chip w-100" style="background:#f8f9fa;border-color:#e9ecef;color:#000;">الأساسي: <?= html_escape($r['base_salary'] ?? '') ?></div></div>
                <div class="col-md-3 col-6 mb-2"><div class="chip w-100" style="background:#f8f9fa;border-color:#e9ecef;color:#000;">السكن: <?= html_escape($r['housing_allowance'] ?? '') ?></div></div>
                <div class="col-md-3 col-6 mb-2"><div class="chip w-100" style="background:#f8f9fa;border-color:#e9ecef;color:#000;">مواصلات(n4): <?= html_escape($r['transport_allowance'] ?? '') ?></div></div>
                <div class="col-md-3 col-6 mb-2"><div class="chip w-100" style="background:#f8f9fa;border-color:#e9ecef;color:#000;">بدلات أخرى: <?= html_escape($r['other_allowances'] ?? '') ?></div></div>
              </div>
            </div>

          </div>
        <?php endforeach; else: ?>
          <div class="text-center text-muted py-4">لا يوجد موظفون في هذه الدفعة.</div>
        <?php endif; ?>
      </div>

      <hr>

      <form method="post" action="<?= site_url('AnnualIncentives/save_batch') ?>">
        <input type="hidden" name="batch_id" value="<?= (int)$batch['id'] ?>">
        <div class="d-flex justify-content-end">
          <button class="btn-marsom" type="submit" style="padding:12px 18px;">
            <i class="fas fa-check"></i> حفظ نهائي
          </button>
        </div>
        <small class="muted d-block mt-2">ملاحظة: يمكنك تركها مسودة وعدم حفظ نهائي إذا رغبت.</small>
      </form>

    </div>

  </div>

  <!-- Budget Exceeded Modal (داخل body بشكل صحيح) -->
  <div class="modal fade" id="budgetModal" tabindex="-1" role="dialog" aria-labelledby="budgetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content" style="border-radius:16px;overflow:hidden;">
        <div class="modal-header" style="background:#fff5f5;border-bottom:1px solid #ffd6d6;">
          <h5 class="modal-title font-weight-bold" id="budgetModalLabel">تنبيه: تجاوز الميزانية</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="outline:none;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="budgetModalBody" style="font-size:14px;">
          تجاوزت الميزانية، التعديل مرفوض.
        </div>
        <div class="modal-footer" style="border-top:1px solid #eee;">
          <button type="button" class="btn btn-dark" data-dismiss="modal" style="border-radius:12px;">حسنًا</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    function showBudgetPopup(message){
      $('#budgetModalBody').html(message || 'تجاوزت الميزانية، التعديل مرفوض.');
      $('#budgetModal').modal('show');
    }

    const BATCH_ID = "<?= (int)$batch['id'] ?>";
    const BUDGET_TOTAL = parseFloat(String("<?= html_escape($batch['budget_total'] ?? '0') ?>").replace(/,/g,'').trim()) || 0;

    function fmt(n){
      n = Number(n||0);
      return n.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2});
    }

    function showErr(msg){
      $('#statusBox').removeClass('d-none').removeClass('okBox').addClass('dangerBox').html('<b>تنبيه:</b> '+msg);
    }
    function showOk(msg){
      $('#statusBox').removeClass('d-none').removeClass('dangerBox').addClass('okBox').html('<b>تم:</b> '+msg);
    }

    function recalcTotals(){
      let sum = 0;
      $('[data-money]').each(function(){
        sum += parseFloat($(this).attr('data-val') || '0') || 0;
      });
      const rem = BUDGET_TOTAL - sum;
      $('#budgetRemaining').text(fmt(rem));
      return {sum, rem};
    }

    function applyCardMoney($card, base, mult){
      const inc = (base * mult);
      const $money = $card.find('[data-money]');
      $money.text(fmt(inc));
      $money.attr('data-val', String(inc));
      return inc;
    }

    function init(){
      $('.empCard').each(function(){
        const $card = $(this);
        const base  = parseFloat($card.data('base')) || 0;

        const $sel = $card.find('[data-mult]');
        const currentMult = parseFloat($sel.attr('data-current')) || 0;
        $sel.val(currentMult.toFixed(2));

        const currentIncentive = parseFloat($card.attr('data-current-incentive') || '0') || (base * currentMult);

        const $money = $card.find('[data-money]');
        $money.text(fmt(currentIncentive));
        $money.attr('data-val', String(currentIncentive));
      });

      recalcTotals();
    }

    $(document).on('click', '.toggleDetails', function(){
      const $card = $(this).closest('.empCard');
      $card.find('.details').slideToggle(150);
    });

    $(document).on('change', '[data-mult]', function(){
      const $sel = $(this);
      const $card = $sel.closest('.empCard');
      const rowId = parseInt($card.data('row-id'));
      const base  = parseFloat($card.data('base')) || 0;
      const newMult = parseFloat($sel.val()) || 0;

      const oldVal = parseFloat($card.find('[data-money]').attr('data-val') || '0') || 0;
      const newVal = base * newMult;

      let sumOther = 0;
      $('.empCard').each(function(){
        const $c = $(this);
        if(parseInt($c.data('row-id')) === rowId) return;
        sumOther += parseFloat($c.find('[data-money]').attr('data-val') || '0') || 0;
      });

      if(sumOther + newVal > BUDGET_TOTAL + 0.00001){
        showBudgetPopup(
          'لا يمكن تنفيذ التعديل لأن قيمة الحوافز بعد التعديل ستتجاوز الميزانية.<br><br>' +
          'الحل: قم بتقليل تزايد موظف آخر أو ارفع الميزانية ثم أعد المحاولة.'
        );

        const prevMult = (base > 0) ? (oldVal / base) : 0;
        $sel.val(prevMult.toFixed(2));
        return;
      }

      applyCardMoney($card, base, newMult);
      recalcTotals();

      $.post("<?= site_url('AnnualIncentives/update_multiplier') ?>", {
        row_id: rowId,
        batch_id: BATCH_ID,
        multiplier: newMult.toFixed(2)
      }, function(res){
        if(!res || !res.ok){
          showBudgetPopup((res && res.msg) ? res.msg : 'التعديل مرفوض.');

          const prevMult = (base > 0) ? (oldVal / base) : 0;
          $sel.val(prevMult.toFixed(2));
          applyCardMoney($card, base, prevMult);
          recalcTotals();
          return;
        }

        applyCardMoney($card, res.base, newMult);
        $('#budgetRemaining').text(fmt(res.remaining));
        if(res.remaining <= 0.00001){
          showOk('تم استهلاك الميزانية بالكامل (المتبقي = 0).');
        }else{
          $('#statusBox').addClass('d-none');
        }
      }, 'json');
    });

    $(function(){ init(); });

    $('#btnResetAll').on('click', function(){
      if(!confirm('هل أنت متأكد؟ سيتم تصفير التزايد والحوافز لجميع الموظفين في هذه الدفعة.')) return;

      $.post("<?= site_url('AnnualIncentives/reset_calculations') ?>", {batch_id: BATCH_ID}, function(res){
        if(!res || !res.ok){
          showBudgetPopup((res && res.msg) ? res.msg : 'تعذر تنفيذ العملية.');
          return;
        }

        $('.empCard').each(function(){
          const $card = $(this);
          const base  = parseFloat($card.data('base')) || 0;
          const $sel  = $card.find('[data-mult]');
          $sel.val('0.00');
          applyCardMoney($card, base, 0);
        });

        recalcTotals();
        showBudgetPopup('تم حذف كل الاحتساب بنجاح ✅');
      }, 'json');
    });

    $(document).on('click', '.toggleEval', function(){
  const empNo = String($(this).data('empno') || '').trim();
  const year  = parseInt($(this).data('year') || 0);

  if(!empNo){
    showBudgetPopup('لا يوجد رقم وظيفي للموظف لعرض التقييم.');
    return;
  }

  const url =
  "<?= rtrim(site_url('AnnualEvaluation/print_a4/'), '/') ?>/" +
  encodeURIComponent(empNo) +
  "?year=" + (year || "<?= (int)($batch['batch_year'] ?? date('Y')) ?>");

  $('#evalFrame').attr('src', url);
  $('#evalOpenNew').attr('href', url);

  $('#evalModal').modal('show');
});

$('#evalModal').on('hidden.bs.modal', function(){
  $('#evalFrame').attr('src', 'about:blank');
});


  </script>

  <!-- ✅ Evaluation Print Modal -->
<div class="modal fade" id="evalModal" tabindex="-1" role="dialog" aria-labelledby="evalModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="max-width:1100px;">
    <div class="modal-content" style="border-radius:16px;overflow:hidden;">
      <div class="modal-header" style="background:#f7f9fc;border-bottom:1px solid #e9ecef;">
        <h5 class="modal-title font-weight-bold" id="evalModalLabel">معاينة تقييم الموظف</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="outline:none;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body p-0" style="background:#fff;">
        <iframe id="evalFrame" src="about:blank"
                style="width:100%;height:80vh;border:0;"></iframe>
      </div>

      <div class="modal-footer" style="border-top:1px solid #eee;">
        <a id="evalOpenNew" class="btn btn-outline-dark" target="_blank" href="#" style="border-radius:12px;font-weight:900;">
          فتح في صفحة جديدة
        </a>
        <button type="button" class="btn btn-dark" data-dismiss="modal" style="border-radius:12px;font-weight:900;">
          إغلاق
        </button>
      </div>
    </div>
  </div>
</div>


</body>
</html>
