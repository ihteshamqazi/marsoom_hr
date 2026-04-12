<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= html_escape($title ?? 'استعراض الدفعة') ?></title>

  <!-- Bootstrap 5 RTL -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
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

    .wrap{max-width:1400px;margin:24px auto;padding:0 14px;position:relative;z-index:1;}

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
      color:#fff;
    }
    .title-box p{
      margin:6px 0 0;
      color: var(--text-muted);
      font-size: .95rem;
    }

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
      border-color: rgba(255,140,0,.45);
    }
    .btn-marsom.warn{
      background: linear-gradient(135deg, rgba(255,193,7,.22), rgba(255,193,7,.08));
      border-color: rgba(255,193,7,.35);
    }
    .btn-marsom.success{
      background: linear-gradient(135deg, rgba(25,135,84,.22), rgba(25,135,84,.08));
      border-color: rgba(25,135,84,.35);
    }
    .btn-marsom.gray{
      background: rgba(255,255,255,.05);
      border-color: rgba(255,255,255,.22);
    }

    /* Alerts */
    .alert-glass{
      background: rgba(255,255,255,.06);
      border: 1px solid rgba(255,255,255,.14);
      border-radius: 18px;
      color:#fff;
      padding: 12px 14px;
      box-shadow: var(--shadow-sm);
      margin-bottom: 12px;
    }
    .alert-glass.ok{ border-color: rgba(25,135,84,.35); }
    .alert-glass.err{ border-color: rgba(220,53,69,.35); }

    /* Summary card (Glass) */
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

    /* Summary chips */
    .chip{
      background: rgba(255,255,255,.08);
      border: 1px solid rgba(255,255,255,.16);
      border-radius: 14px;
      padding: 10px 12px;
      font-weight: 900;
      color:#fff;
      display:inline-flex;
      align-items:center;
      gap:8px;
      white-space:nowrap;
    }
    .chip b{ font-weight: 900; }
    .chip.bad{
      background: rgba(220,53,69,.12);
      border-color: rgba(220,53,69,.28);
    }
    .chip.good{
      background: rgba(25,135,84,.12);
      border-color: rgba(25,135,84,.28);
    }

    /* Table card (أبيض + خط أسود) */
    .table-card{
      background:#fff;
      border-radius: var(--radius-xxl);
      box-shadow: var(--shadow-lg);
      overflow:hidden;
    }
    .table-card .pad{ padding: 14px 14px 6px; }

    /* فرض الأسود داخل الجدول */
    .table-card table,
    .table-card thead th,
    .table-card tbody td{
      color:#000 !important;
    }

    .table thead th{
      font-weight: 900;
      font-size: 1rem;
      padding-top: 14px;
      padding-bottom: 14px;
      border-bottom: 1px solid #e6e6e6 !important;
      white-space:nowrap;
    }

    .table tbody td{
      font-weight: 700;
      padding-top: 14px;
      padding-bottom: 14px;
      border-color:#f0f0f0 !important;
      vertical-align: middle;
    }
    .table tbody tr:hover{ background:#f7f7f7; }

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
        <h1>استعراض الدفعة</h1>
        <p>
          <?= html_escape($batch['batch_name'] ?? '') ?>
          — <?= html_escape($batch['batch_year'] ?? '') ?>
          — الحالة: <?= html_escape($batch['status'] ?? '') ?>
        </p>
      </div>

      <div class="header-actions d-flex gap-2 flex-wrap">
        <a href="<?= site_url('AnnualIncentives/employees/'.(int)$batch['id']) ?>" class="btn-marsom">
          <i class="fas fa-users"></i> إضافة/حذف موظفين
        </a>
        <a href="<?= site_url('AnnualIncentives/edit_settings/'.(int)$batch['id']) ?>" class="btn-marsom">
          <i class="fas fa-sliders"></i> تعديل الميزانية/الفلاتر
        </a>
        <a href="<?= site_url('AnnualIncentives/calc/'.(int)$batch['id']) ?>" class="btn-marsom warn">
          <i class="fas fa-calculator"></i> تعديل الاحتساب
        </a>
        <a href="<?= site_url('AnnualIncentives/export_excel/'.(int)$batch['id']) ?>" class="btn-marsom success">
          <i class="fas fa-file-excel"></i> تصدير Excel
        </a>
        <a href="<?= site_url('AnnualIncentives/print/'.(int)$batch['id']) ?>" target="_blank" class="btn-marsom gray">
          <i class="fas fa-print"></i> طباعة
        </a>
        <a href="<?= site_url('AnnualIncentives') ?>" class="btn-marsom gray">
          <i class="fas fa-arrow-right"></i> رجوع
        </a>
      </div>
    </div>

    <?php if($this->session->flashdata('ok')): ?>
      <div class="alert-glass ok">
        <i class="fas fa-check-circle"></i>
        <?= $this->session->flashdata('ok') ?>
      </div>
    <?php endif; ?>

    <?php if($this->session->flashdata('err')): ?>
      <div class="alert-glass err">
        <i class="fas fa-triangle-exclamation"></i>
        <?= $this->session->flashdata('err') ?>
      </div>
    <?php endif; ?>

    <!-- Summary -->
    <div class="glass-card">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
          <div style="color:var(--text-muted);font-weight:800">ملخص عام</div>
          <div class="h5 mb-0" style="font-weight:900">
            <?= html_escape($batch['batch_name'] ?? '') ?> — <?= html_escape($batch['batch_year'] ?? '') ?>
          </div>
        </div>

        <div class="d-flex flex-wrap" style="gap:10px;">
          <div class="chip">
            <i class="fas fa-wallet"></i>
            الميزانية: <b><?= number_format((float)$budget_f, 2, '.', ',') ?></b>
          </div>
          <div class="chip">
            <i class="fas fa-coins"></i>
            المصروف: <b><?= number_format((float)$spent_f, 2, '.', ',') ?></b>
          </div>
          <div class="chip <?= ($remaining_f < 0 ? 'bad' : 'good') ?>">
            <i class="fas fa-chart-pie"></i>
            المتبقي:
            <b style="color:<?= ($remaining_f < 0 ? '#ffb3bd' : '#b9ffd1') ?>;">
              <?= number_format((float)$remaining_f, 2, '.', ',') ?>
            </b>
          </div>
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="table-card">
      <div class="pad">
        <div class="table-responsive">
          <table class="table table-hover mb-0 align-middle">
            <thead>
              <tr>
                <th style="width:70px">#</th>
                <th>الموظف</th>
                
                <th>التزايد</th>
                <th>قاعدة الحساب</th>
                <th>قيمة الحافز</th>
              </tr>
            </thead>
            <tbody>
              <?php $i = 1; ?>
              <?php if(!empty($rows)): foreach($rows as $r): ?>
                <tr>
                  <td class="fw-bold"><?= $i++ ?></td>
                  <td class="fw-bold">
                    <?= html_escape($r['subscriber_name'] ?? '') ?>
                    <?php if(!empty($r['profession'])): ?>
                      (<?= html_escape($r['profession']) ?>)
                    <?php endif; ?>
                  </td>
                  
                  <td><?= html_escape($r['multiplier'] ?? '0') ?></td>
                  <td><?= html_escape($r['calc_base_amount'] ?? '0') ?></td>
                  <td class="fw-bold"><?= html_escape($r['incentive_amount'] ?? '0') ?></td>
                </tr>
              <?php endforeach; else: ?>
                <tr>
                  <td colspan="6" class="text-center py-4" style="color:#777 !important;font-weight:900">
                    لا توجد بيانات.
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
