<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= html_escape($title ?? 'نظام الحوافز السنوية') ?></title>

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
      overflow-x:hidden;
    }

    /* خلفية متحركة خفيفة */
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

    .wrap{max-width:1400px;margin:26px auto;padding:0 16px; position:relative; z-index:1;}

    /* ===== Header (نفس اللي بالصورة) ===== */
    .header-nav{
      background: rgba(255,255,255,.03);
      border: 1px solid var(--glass-border);
      border-radius: var(--radius-xxl);
      box-shadow: 0 8px 32px rgba(0,0,0,.28);
      backdrop-filter: blur(18px);
      -webkit-backdrop-filter: blur(18px);
      padding: 18px 20px;
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:14px;
      margin-bottom: 18px;
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
      font-size: 1.9rem;
      font-weight: 900;
      margin:0;
      line-height: 1.15;
      color:#fff;
    }
    .title-box p{
      margin:6px 0 0;
      color: var(--text-muted);
      font-size: .98rem;
    }

    .btn-marsom{
      border: 1px solid rgba(255,255,255,.18);
      background: rgba(255,255,255,.06);
      color:#fff;
      border-radius: 14px;
      padding: 10px 16px;
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

    /* ===== Card table (أبيض مثل الصورة) ===== */
    .table-card{
      background:#fff;
      border-radius: var(--radius-xxl);
      box-shadow: var(--shadow-lg);
      overflow:hidden;
    }
    .table-card .card-pad{ padding: 14px; }

    /* مهم: فرض الأسود داخل الجدول */
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
    .table tbody tr:hover{
      background:#f7f7f7;
    }

    .badge-pillx{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      padding: 7px 12px;
      border-radius: 999px;
      font-weight: 900;
      font-size: .85rem;
      border: 1px solid transparent;
    }
    .badge-draft{
      background: rgba(255,193,7,.22);
      border-color: rgba(255,193,7,.45);
      color:#6a4b00 !important;
    }
    .badge-final{
      background: rgba(40,167,69,.18);
      border-color: rgba(40,167,69,.40);
      color:#0f5f22 !important;
    }

    .btn-action{
      border-radius: 12px;
      padding: 8px 12px;
      font-weight: 900;
      border: 1px solid rgba(0,0,0,.12);
      background: #fff;
      color:#000 !important;
      text-decoration:none;
      display:inline-flex;
      align-items:center;
      gap:8px;
      transition: .2s ease;
      font-size: .9rem;
    }
    .btn-action:hover{
      transform: translateY(-2px);
      border-color: rgba(255,140,0,.65);
      box-shadow: 0 10px 18px rgba(255,140,0,.12);
      color:#000 !important;
    }
    .btn-action.primary{
      background: linear-gradient(135deg, rgba(255,140,0,.18), rgba(255,140,0,.08));
      border-color: rgba(255,140,0,.35);
    }

    .alert-ok{
      background: rgba(40,167,69,.12);
      border: 1px solid rgba(40,167,69,.25);
      color:#0d3a18;
      border-radius: 16px;
      padding: 12px 14px;
      margin-bottom: 14px;
    }

    @media (max-width: 768px){
      .header-nav{flex-direction:column; align-items:stretch}
      .header-actions{justify-content:center}
      .title-box h1{font-size:1.6rem}
    }
  </style>
</head>

<body>
  <div class="bg-pattern"></div>

  <div class="wrap">

    <!-- Header -->
    <div class="header-nav">
      <div class="title-box">
        <h1>نظام الحوافز السنوية</h1>
        <p>إدارة دفعات الحوافز (إنشاء - تعديل - احتساب)</p>
      </div>

      <div class="header-actions d-flex gap-2 flex-wrap">
        <a class="btn-marsom primary" href="<?= site_url('AnnualIncentives/create') ?>">
          <i class="fas fa-plus"></i>
          إنشاء دفعة جديدة
        </a>
      </div>

        <button type="button" class="btn-marsom primary" onclick="window.location.href='<?php echo site_url('users2/mobile_dashboard'); ?>'">
  <i class="fa fa-arrow-right me-1"></i> رجوع
</button>

    </div>



    <?php if($this->session->flashdata('ok')): ?>
      <div class="alert-ok">
        <i class="fas fa-check-circle"></i>
        <?= $this->session->flashdata('ok') ?>
      </div>
    <?php endif; ?>

    <!-- Table Card -->
    <div class="table-card">
      <div class="card-pad">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th style="width:70px">#</th>
                <th>اسم الحافز</th>
                <th style="width:130px">السنة</th>
                <th style="width:160px">الميزانية</th>
             
                <th style="width:320px">الإجراءات</th>
              </tr>
            </thead>

            <tbody>
            <?php if(!empty($rows)): foreach($rows as $r): ?>
              <?php
                $st = ($r['status'] ?? 'draft');
                $is_final = ($st === 'final');
              ?>
              <tr>
                <td><?= (int)($r['id'] ?? 0) ?></td>

                <td class="fw-bold">
                  <?= html_escape($r['batch_name'] ?? '') ?>
                </td>

                <td><?= html_escape($r['batch_year'] ?? '') ?></td>

                <td><?= html_escape($r['budget_total'] ?? '') ?></td>

               

                <td class="d-flex gap-2 flex-wrap">
                  

                 

                  <a href="<?= site_url('AnnualIncentives/edit_settings/'.(int)$r['id']) ?>" class="btn-action">
          <i class="fas fa-sliders"></i> تعديل الميزانية/الفلاتر
        </a>

                  <a href="<?= site_url('AnnualIncentives/employees/'.(int)$r['id']) ?>" class="btn-action">
          <i class="fas fa-users"></i> إضافة/حذف موظفين
        </a>
       
        <a href="<?= site_url('AnnualIncentives/calc/'.(int)$r['id']) ?>" class="btn-action">
          <i class="fas fa-calculator"></i> تعديل الاحتساب
        </a>

         <a class="btn-action primary" href="<?= site_url('AnnualIncentives/view/'.(int)($r['id'] ?? 0)) ?>">
                    <i class="fas fa-eye"></i>  استعراض النتائج
                  </a>


                </td>
              </tr>
            <?php endforeach; else: ?>
              <tr>
                <td colspan="6" class="text-center py-4" style="color:#777 !important;font-weight:800">
                  لا توجد دفعات بعد.
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
