<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= html_escape($title ?? 'تعديل إعدادات الدفعة') ?></title>

  <!-- Bootstrap 4.6.2 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@600;700&family=Tajawal:wght@400;600;800&display=swap" rel="stylesheet">
  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <style>
    :root{
      --primary-blue:#001f3f;
      --primary-orange:#FF8C00;
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
      color:var(--text-light);
      overflow-x:hidden;
      background-attachment: fixed;
    }

    /* Background pattern */
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

    .wrap{max-width:1100px;margin:24px auto;padding:0 14px;position:relative;z-index:1;}

    /* Header */
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
      line-height:1.2;
      color:#fff;
    }
    .title-box p{
      margin:6px 0 0;
      color:var(--text-muted);
      font-size:.95rem;
    }

    /* Buttons */
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
    .btn-marsom.primary{
      background: linear-gradient(135deg, rgba(255,140,0,.25), rgba(255,140,0,.10));
      border-color: rgba(255,140,0,.45);
    }
    .btn-marsom.gray{
      background: rgba(255,255,255,.05);
      border-color: rgba(255,255,255,.22);
    }

    /* Glass Card */
    .glass-card{
      background: var(--glass-bg);
      border: 1px solid var(--glass-border);
      border-radius: var(--radius-xxl);
      box-shadow: var(--shadow-sm);
      backdrop-filter: blur(18px);
      -webkit-backdrop-filter: blur(18px);
      padding: 18px;
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

    .muted{color:var(--text-muted) !important;}

    /* Form look */
    label{font-weight:900; color:#fff;}
    .form-control{
      border-radius: 14px;
      font-weight: 900;
      border: 1px solid rgba(255,255,255,.16);
      background: rgba(255,255,255,.06);
      color:#fff;
    }
    .form-control:focus{
      background: rgba(255,255,255,.08);
      color:#fff;
      border-color: rgba(255,140,0,.55);
      box-shadow: 0 0 0 .2rem rgba(255,140,0,.15);
    }

    /* Bootstrap 4 custom controls text */
    .custom-control-label{
      color:#fff;
      font-weight: 800;
      cursor:pointer;
    }
    .custom-control-label::before{
      border-color: rgba(255,255,255,.30) !important;
      background: rgba(255,255,255,.10) !important;
    }
    .custom-control-input:checked ~ .custom-control-label::before{
      border-color: rgba(255,140,0,.65) !important;
      background: rgba(255,140,0,.35) !important;
    }

    /* Parts box */
    #partsBox{
      background: rgba(255,255,255,.05) !important;
      border: 1px dashed rgba(255,255,255,.20) !important;
      border-radius: 18px !important;
    }

    hr{border-color: rgba(255,255,255,.12) !important;}

    @media(max-width: 992px){
      .header-nav{flex-direction:column; align-items:stretch}
      .header-actions{justify-content:center}
    }
  </style>
</head>

<body>
  <div class="bg-pattern"></div>

  <div class="wrap">

    <!-- Header -->
    <div class="header-nav">
      <div class="title-box">
        <h1>تعديل إعدادات الدفعة</h1>
        <p><?= html_escape($batch['batch_name'] ?? '') ?> — <?= html_escape($batch['batch_year'] ?? '') ?></p>
      </div>

      <div class="header-actions d-flex gap-2 flex-wrap">
        <a href="<?= site_url('AnnualIncentives') ?>" class="btn-marsom gray">
          <i class="fas fa-arrow-right"></i> رجوع
        </a>
      </div>
    </div>

    <!-- Form Card -->
    <div class="glass-card">
      <form method="post" action="<?= site_url('AnnualIncentives/update_settings') ?>">
        <input type="hidden" name="batch_id" value="<?= (int)$batch['id'] ?>">

        <div class="form-group">
          <label><i class="fas fa-wallet"></i> الميزانية المخصصة</label>
          <input type="text" name="budget_total" class="form-control" value="<?= html_escape($batch['budget_total'] ?? '') ?>" required>
          <small class="muted d-block mt-2">مثال: 100000 أو 100,000.00</small>
        </div>

        <hr>

        <div class="font-weight-bold mb-2" style="color:#fff;">
          <i class="fas fa-sliders"></i> طريقة احتساب الراتب
        </div>

        <?php $mode = ($batch['calc_mode'] ?? 'total'); ?>

        <div class="custom-control custom-radio mb-2">
          <input type="radio" id="mode_total" name="calc_mode" class="custom-control-input" value="total" <?= ($mode==='total')?'checked':'' ?>>
          <label class="custom-control-label" for="mode_total">إجمالي الراتب (total_salary)</label>
        </div>

        <div class="custom-control custom-radio mb-3">
          <input type="radio" id="mode_parts" name="calc_mode" class="custom-control-input" value="parts" <?= ($mode==='parts')?'checked':'' ?>>
          <label class="custom-control-label" for="mode_parts">أجزاء الراتب (اختيار)</label>
        </div>

        <div id="partsBox" class="p-3" style="display:none;">
          <div class="row">
            <div class="col-md-3 mb-2">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" id="use_base_salary" name="use_base_salary" class="custom-control-input" <?= (($batch['use_base_salary']??'0')=='1')?'checked':'' ?>>
                <label class="custom-control-label" for="use_base_salary">الراتب الأساسي</label>
              </div>
            </div>
            <div class="col-md-3 mb-2">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" id="use_housing_allowance" name="use_housing_allowance" class="custom-control-input" <?= (($batch['use_housing_allowance']??'0')=='1')?'checked':'' ?>>
                <label class="custom-control-label" for="use_housing_allowance">بدل السكن</label>
              </div>
            </div>
            <div class="col-md-3 mb-2">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" id="use_transport_allowance" name="use_transport_allowance" class="custom-control-input" <?= (($batch['use_transport_allowance']??'0')=='1')?'checked':'' ?>>
                <label class="custom-control-label" for="use_transport_allowance">بدل المواصلات</label>
              </div>
            </div>
            <div class="col-md-3 mb-2">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" id="use_other_allowances" name="use_other_allowances" class="custom-control-input" <?= (($batch['use_other_allowances']??'0')=='1')?'checked':'' ?>>
                <label class="custom-control-label" for="use_other_allowances">بدلات أخرى</label>
              </div>
            </div>
          </div>
          <small class="muted d-block mt-2">
            سيتم إعادة احتساب القاعدة لكل موظف مع الحفاظ على التزايد الحالي.
          </small>
        </div>

        <div class="d-flex justify-content-end mt-4">
          <button class="btn-marsom primary" type="submit" style="padding:12px 18px;">
            <i class="fas fa-save"></i> حفظ التعديلات وإعادة الاحتساب
          </button>
        </div>

      </form>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function toggleParts(){
      var mode = $('input[name="calc_mode"]:checked').val();
      $('#partsBox').toggle(mode === 'parts');
    }
    $(function(){
      toggleParts();
      $('input[name="calc_mode"]').on('change', toggleParts);
    });
  </script>
</body>
</html>
