<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= html_escape($title ?? 'اختيار الموظفين') ?></title>

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

    /* Glass card */
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

    .muted{color:var(--text-muted) !important;}

    .chip{
      background: rgba(255,255,255,.08);
      border: 1px solid rgba(255,255,255,.16);
      border-radius:999px;
      padding:.25rem .7rem;
      font-size:12px;
      font-weight:900;
      color:#fff;
      display:inline-flex;
      align-items:center;
      gap:8px;
      white-space:nowrap;
    }

    /* White row cards like your screenshot style */
    .rowcard{
      border-radius:14px;
      background:#fff;
      border:1px solid #eef1f6;
      box-shadow: 0 10px 22px rgba(0,0,0,.08);
    }
    .rowcard .font-weight-bold{color:#000;}
    .rowcard .small{color:#6c757d !important;}

    /* Inputs */
    .form-control{
      border-radius:14px;
      font-weight:800;
    }

    /* Search status */
    .searchHint{
      background: rgba(255,255,255,.06);
      border:1px solid rgba(255,255,255,.14);
      border-radius: 16px;
      color:#fff;
      padding: 12px;
    }

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
        <h1>اختيار الموظفين</h1>
        <p>
          <?= html_escape($batch['batch_name'] ?? '') ?> — سنة <?= html_escape($batch['batch_year'] ?? '') ?>
          — الميزانية: <span class="chip"><i class="fas fa-wallet"></i> <?= html_escape($batch['budget_total'] ?? '') ?></span>
        </p>
      </div>

      <div class="header-actions d-flex gap-2 flex-wrap">
        <a href="<?= site_url('AnnualIncentives') ?>" class="btn-marsom gray">
          <i class="fas fa-arrow-right"></i> رجوع
        </a>
        <a href="<?= site_url('AnnualIncentives/calc/'.(int)$batch['id']) ?>" class="btn-marsom primary">
          <i class="fas fa-arrow-left"></i> التالي: شاشة الاحتساب
        </a>
      </div>
    </div>

    <!-- Search -->
    <div class="glass-card">
      <div class="font-weight-bold mb-2" style="color:#fff;">
        <i class="fas fa-magnifying-glass"></i>
        بحث من جدول emp1 (بالرقم الوظيفي أو الاسم)
      </div>

      <div class="row">
        <div class="col-md-8">
          <input id="q" class="form-control" placeholder="اكتب الرقم الوظيفي أو اسم الموظف...">
        </div>
        <div class="col-md-4 mt-2 mt-md-0">
          <button id="btnSearch" class="btn-marsom primary w-100" type="button">
            <i class="fas fa-search"></i> بحث
          </button>
        </div>
      </div>

      <div id="searchBox" class="mt-3"></div>
    </div>

    <!-- Selected -->
    <div class="glass-card">
      <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:10px;">
        <div class="font-weight-bold" style="color:#fff;">
          <i class="fas fa-user-check"></i> الموظفين المختارين
        </div>
        <div class="chip">
          <i class="fas fa-hashtag"></i> العدد: <span id="selCount"><?= count($selected) ?></span>
        </div>
      </div>

      <div id="selectedList" class="mt-3">
        <?php if(!empty($selected)): foreach($selected as $s): ?>
          <div class="rowcard p-3 mb-2 d-flex align-items-center justify-content-between flex-wrap">
            <div>
              <div class="font-weight-bold">
                <?= html_escape($s['subscriber_name'] ?? '') ?>
                <?php if(!empty($s['profession'])): ?>
                  (<?= html_escape($s['profession']) ?>)
                <?php endif; ?>
              </div>
              <div class="small">
                رقم وظيفي: <?= html_escape($s['employee_id'] ?? '') ?> — هوية: <?= html_escape($s['id_number'] ?? '') ?>
              </div>
            </div>
            <div class="mt-2 mt-md-0">
              <button class="btn btn-outline-danger btn-sm" onclick="removeRow(<?= (int)$s['id'] ?>)">
                <i class="fas fa-xmark"></i> إزالة
              </button>
            </div>
          </div>
        <?php endforeach; else: ?>
          <div class="searchHint text-center py-3">لم يتم اختيار أي موظف بعد.</div>
        <?php endif; ?>
      </div>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    const BATCH_ID = "<?= (int)$batch['id'] ?>";

    function escapeHtml(s){
      return String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
    }

    function renderSearch(rows){
      if(!rows || !rows.length){
        $('#searchBox').html('<div class="searchHint text-center py-3">لا توجد نتائج.</div>');
        return;
      }
      let html = '<div class="mt-2">';
      rows.forEach(r => {
        html += `
          <div class="rowcard p-3 mb-2">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
              <div>
                <div class="font-weight-bold">
                  ${escapeHtml(r.subscriber_name)} ${r.profession ? '('+escapeHtml(r.profession)+')' : ''}
                </div>
                <div class="small">رقم وظيفي: ${escapeHtml(r.employee_id)} — هوية: ${escapeHtml(r.id_number)}</div>
              </div>
              <div class="mt-2 mt-md-0">
                <button class="btn btn-primary btn-sm" onclick="addEmp('${escapeHtml(r.employee_id)}')">
                  <i class="fas fa-plus"></i> إضافة
                </button>
              </div>
            </div>
            <div class="small text-muted mt-2">
              إجمالي الراتب: ${escapeHtml(r.total_salary)} |
              أساسي: ${escapeHtml(r.base_salary)} |
              سكن: ${escapeHtml(r.housing_allowance)} |
              مواصلات(n4): ${escapeHtml(r.n4)} |
              بدلات أخرى: ${escapeHtml(r.other_allowances)}
            </div>
          </div>
        `;
      });
      html += '</div>';
      $('#searchBox').html(html);
    }

    function refreshSelected(selected){
      if(!selected || !selected.length){
        $('#selectedList').html('<div class="searchHint text-center py-3">لم يتم اختيار أي موظف بعد.</div>');
        $('#selCount').text('0');
        return;
      }
      $('#selCount').text(selected.length);
      let html = '';
      selected.forEach(s => {
        html += `
          <div class="rowcard p-3 mb-2 d-flex align-items-center justify-content-between flex-wrap">
            <div>
              <div class="font-weight-bold">
                ${escapeHtml(s.subscriber_name)} ${s.profession ? '('+escapeHtml(s.profession)+')' : ''}
              </div>
              <div class="small">رقم وظيفي: ${escapeHtml(s.employee_id)} — هوية: ${escapeHtml(s.id_number)}</div>
            </div>
            <div class="mt-2 mt-md-0">
              <button class="btn btn-outline-danger btn-sm" onclick="removeRow(${parseInt(s.id)})">
                <i class="fas fa-xmark"></i> إزالة
              </button>
            </div>
          </div>
        `;
      });
      $('#selectedList').html(html);
    }

    $('#btnSearch').on('click', function(){
      const q = $('#q').val().trim();
      if(!q){ $('#searchBox').html('<div class="searchHint text-center py-3">اكتب كلمة بحث.</div>'); return; }
      $('#searchBox').html('<div class="searchHint text-center py-3">... جاري البحث</div>');
      $.get("<?= site_url('AnnualIncentives/search_emp1') ?>", {q:q}, function(res){
        if(!res || !res.ok){ $('#searchBox').html('<div class="searchHint text-center py-3">حدث خطأ.</div>'); return; }
        renderSearch(res.rows);
      }, 'json');
    });

    // Enter key
    $('#q').on('keypress', function(e){
      if(e.which === 13){ $('#btnSearch').click(); }
    });

    function addEmp(employee_id){
      $.post("<?= site_url('AnnualIncentives/add_employee') ?>", {batch_id:BATCH_ID, employee_id:employee_id}, function(res){
        if(!res || !res.ok){ alert(res?.msg || 'خطأ'); return; }
        if(res.selected) refreshSelected(res.selected);
      }, 'json');
    }

    function removeRow(row_id){
      if(!confirm('إزالة الموظف من القائمة؟')) return;
      $.post("<?= site_url('AnnualIncentives/remove_employee') ?>", {row_id:row_id}, function(res){
        if(!res || !res.ok){ alert(res?.msg || 'خطأ'); return; }
        location.reload();
      }, 'json');
    }
  </script>
</body>
</html>
