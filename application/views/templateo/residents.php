<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>بيانات الموظفين</title>

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.css">

  <style>
    :root{
      --marsom-blue:#001f3f;--marsom-orange:#FF8C00;--text-light:#fff;--text-dark:#343a40;
      --glass-bg:rgba(255,255,255,.08);--glass-border:rgba(255,255,255,.2);--glass-shadow:rgba(0,0,0,.5)
    }
    body{font-family:'El+Messiri',sans-serif;overflow:hidden;background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);background-size:400% 400%;animation:grad 20s ease infinite;color:var(--text-dark);position:relative}
    @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
    .particles{position:fixed;inset:0;overflow:hidden;z-index:-1}
    .particle{position:absolute;background:rgba(255,140,0,.1);clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);animation:float 25s infinite ease-in-out;opacity:0;filter:blur(2px)}
    .particle:nth-child(even){background:rgba(0,31,63,.1)}
    .particle:nth-child(1){width:40px;height:40px;left:10%;top:20%;animation-duration:18s}
    .particle:nth-child(2){width:70px;height:70px;left:25%;top:50%;animation-duration:22s;animation-delay:2s}
    .particle:nth-child(3){width:55px;height:55px;left:40%;top:10%;animation-duration:25s;animation-delay:5s}
    .particle:nth-child(4){width:80px;height:80px;left:60%;top:70%;animation-duration:20s;animation-delay:8s}
    .particle:nth-child(5){width:60px;height:60px;left:80%;top:30%;animation-duration:23s;animation-delay:10s}
    .particle:nth-child(6){width:45px;height:45px;left:5%;top:85%;animation-duration:19s;animation-delay:3s}
    .particle:nth-child(7){width:90px;height:90px;left:70%;top:5%;animation-duration:28s;animation-delay:6s}
    .particle:nth-child(8){width:35px;height:35px;left:90%;top:40%;animation-duration:17s;animation-delay:12s}
    .particle:nth-child(9){width:75px;height:75px;left:20%;top:75%;animation-duration:21s;animation-delay:1s}
    .particle:nth-child(10){width:65px;height:65px;left:50%;top:90%;animation-duration:24s;animation-delay:4s}
    @keyframes float{0%{transform:translateY(0) translateX(0) rotate(0);opacity:0}20%{opacity:1}80%{opacity:1}100%{transform:translateY(-100vh) translateX(50px) rotate(360deg);opacity:0}}

    #loading-screen{position:fixed;inset:0;background:transparent;z-index:9999;display:flex;align-items:center;justify-content:center;flex-direction:column;transition:opacity .5s}
    .loader{width:50px;height:50px;border:5px solid rgba(255,255,255,.3);border-top:5px solid var(--marsom-orange);border-radius:50%;animation:spin 1s linear infinite;margin-bottom:16px}
    @keyframes spin{to{transform:rotate(360deg)}}

    .main-container{padding:30px 15px;visibility:hidden;opacity:0;transition:opacity .5s;position:relative;z-index:1}
    .page-title{font-family:'El Messiri',sans-serif;font-weight:700;font-size:2.8rem;color:var(--text-light);margin-bottom:32px;text-align:center;position:relative;display:inline-block;padding-bottom:10px;text-shadow:0 3px 6px rgba(0,0,0,.4)}
    .page-title::after{content:'';position:absolute;width:100px;height:4px;background:linear-gradient(90deg,var(--marsom-blue),var(--marsom-orange));bottom:0;left:50%;transform:translateX(-50%);border-radius:2px}
    .table-card{background:rgba(255,255,255,.9);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.3);border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,.15);padding:25px}
    .dataTables-example thead th{background-color:#001f3f !important;color:#fff;text-align:center;vertical-align:middle;border-bottom:2px solid #00152b}
    .dataTables-example tbody td{text-align:center;vertical-align:middle;font-size:14px;white-space:nowrap}
    .dataTables-example tbody tr:hover{background-color:rgba(0,31,63,.05)}
    .dt-buttons .btn{background-color:var(--marsom-orange);border-color:var(--marsom-orange);color:#fff;font-weight:500;margin:0 2px;box-shadow:0 2px 8px rgba(0,0,0,.2)}
    .dt-buttons .btn:hover{background:#e0882f;border-color:#e0882f;transform:translateY(-1px)}
    /* أزرار أعلى يمين */
    .top-actions{position:fixed;top:12px;right:12px;display:flex;gap:10px;z-index:5}
    .top-actions a{background:rgba(255,255,255,.12);border:1px solid var(--glass-border);color:#fff;text-decoration:none;border-radius:10px;padding:8px 14px;display:inline-flex;align-items:center;gap:8px;transition:.25s}
    .top-actions a:hover{background:rgba(255,255,255,.2);color:var(--marsom-orange)}
  </style>
</head>
<body>

<!-- الخلفية المتحركة -->
<div class="particles">
  <div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div>
  <div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div>
</div>

<!-- شاشة التحميل -->
<div id="loading-screen">
  <div class="loader"></div>
  <h3 style="color:#fff">جاري تحميل التقرير ...</h3>
</div>

<!-- أزرار رجوع/الرئيسية -->
<div class="top-actions">
  <a href="javascript:history.back()"><i class="fas fa-arrow-right"></i><span>رجوع</span></a>
  <a href="<?php echo site_url('dashboard'); ?>"><i class="fas fa-home"></i><span>الرئيسية</span></a>
</div>

<div class="main-container container-fluid">
  <div class="text-center">
    <h1 class="page-title">   المقيمين</h1>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card table-card">
        <div class="card-body">
          <?php echo validation_errors(); ?>
          <?php echo form_open_multipart('users2/sadad_report_emp'); ?>

          <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover dataTables-example" style="width:100%">
              <thead>
                <tr>
                  <th>الرقم الوظيفي</th>
                  <th>رقم الهوية</th>
                  <th>اسم الموظف</th>
                  <th>الجنسية</th>
                  <th> تاريخ انتهاء الاقامة</th>
                  <th>المسمى الوظيفي</th>
                  <th>   الشركة</th>
                   <th>   عدد الايام المتبقية حتى انتاهء الاقامة</th>
                   
                </tr>
              </thead>
              <tbody>
    <?php foreach($get_salary_vacations as $employee) : ?>
        <tr>
            <td>
                <button type="button" class="btn btn-sm btn-primary"
                        onclick="openExemptionPopup('<?php echo $employee['employee_id']; ?>')">
                    <?php echo $employee['employee_id']; ?>
                </button>
            </td>
            <td><?php echo $employee['id_number']; ?></td>
            <td><?php echo $employee['subscriber_name']; ?></td>
            <td><?php echo $employee['nationality']; ?></td>
           
            <td><?php echo $employee['Iqama_expiry_date']; ?></td>
            
            <td><?php echo $employee['profession']; ?></td>
            <td>
  <?php 
    if ($employee['n13'] == 1) {
        echo "مرسوم";
    } elseif ($employee['n13'] == 2) {
        echo "مكتب الدكتور صالح الجربوع";
    } else {
        echo $employee['n13']; // في حال كانت القيمة غير 1 أو 2
    }
  ?>
</td>

 <!-- ====== داخل الصف/الخانة في الجدول ====== -->
<td class="p-0">
  <div class="joining-glass-counter m-2" 
       dir="rtl"
       data-joining-date="<?php echo htmlspecialchars($employee['Iqama_expiry_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    <div class="jg-title">
      <i class="fa-regular fa-hourglass-half"></i>
      المدة منذ الانضمام
    </div>
    <div class="jg-grid">
      <div class="jg-item">
        <div class="jg-num" data-gc="days">00</div>
        <div class="jg-label">يوم</div>
      </div>
      <div class="jg-item">
        <div class="jg-num" data-gc="hours">00</div>
        <div class="jg-label">ساعة</div>
      </div>
      <div class="jg-item">
        <div class="jg-num" data-gc="minutes">00</div>
        <div class="jg-label">دقيقة</div>
      </div>
      <div class="jg-item">
        <div class="jg-num" data-gc="seconds">00</div>
        <div class="jg-label">ثانية</div>
      </div>
    </div>
    <div class="jg-badge" data-gc="mode">منذ الانضمام</div>
  </div>
</td>

<!-- ====== (يمكن وضعها مرة واحدة أعلى/أسفل الصفحة) Bootstrap 5 + FA ====== -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- ====== نمط زجاجي لطيف ====== -->
<style>
  .joining-glass-counter{
    --glass-bg: rgba(255,255,255,0.12);
    --glass-brd: rgba(255,255,255,0.25);
    --glass-shadow: 0 10px 30px rgba(0,0,0,.12);
    --num-color: #0d6efd; /* أزرق Bootstrap */
    background: var(--glass-bg);
    border: 1px solid var(--glass-brd);
    border-radius: 16px;
    padding: 12px 14px;
    box-shadow: var(--glass-shadow);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
  }
  .joining-glass-counter .jg-title{
    font-weight: 700;
    font-size: .95rem;
    color: #0e1f3b;
    display: flex;
    align-items: center;
    gap: .5rem;
    margin-bottom: .5rem;
  }
  .joining-glass-counter .jg-grid{
    display: grid;
    grid-template-columns: repeat(4, minmax(0,1fr));
    gap: 8px;
  }
  .joining-glass-counter .jg-item{
    text-align: center;
    background: rgba(255,255,255,.22);
    border: 1px solid rgba(255,255,255,.35);
    border-radius: 12px;
    padding: 8px 6px;
    box-shadow: inset 0 1px 4px rgba(0,0,0,.06);
  }
  .joining-glass-counter .jg-num{
    font-weight: 800;
    font-size: 1.15rem;
    line-height: 1;
    color: var(--num-color);
  }
  .joining-glass-counter .jg-label{
    font-size: .75rem;
    color: #334155;
    margin-top: 4px;
  }
  .joining-glass-counter .jg-badge{
    margin-top: 8px;
    font-size: .75rem;
    color: #0f5132;
    background: rgba(25,135,84,.12); /* أخضر لطيف */
    border: 1px solid rgba(25,135,84,.35);
    display: inline-block;
    padding: 4px 8px;
    border-radius: 999px;
  }

  /* تحسينات صغيرة للموبايل */
  @media (max-width: 576px){
    .joining-glass-counter .jg-num{ font-size: 1rem; }
    .joining-glass-counter .jg-label{ font-size: .7rem; }
  }
</style>

<!-- ====== جافاسكربت: حساب المدة في Asia/Riyadh من dd-mm-yyyy وتحديث كل ثانية ====== -->
<script>
(function(){
  function parseDDMMYYYY(str){
    if(!str) return null;
    const m = String(str).trim().match(/^(\d{1,2})-(\d{1,2})-(\d{4})$/);
    if(!m) return null;
    const dd = +m[1], mm = +m[2], yyyy = +m[3];
    // نبني تاريخ 00:00 في توقيت الرياض
    const iso = `${String(yyyy).padStart(4,'0')}-${String(mm).padStart(2,'0')}-${String(dd).padStart(2,'0')}T00:00:00`;
    // حيلة للحصول على كائن Date مضبوط على Asia/Riyadh
    return new Date(new Date(iso).toLocaleString('en-US', { timeZone: 'Asia/Riyadh' }));
  }

  function nowInRiyadh(){
    return new Date(new Date().toLocaleString('en-US', { timeZone: 'Asia/Riyadh' }));
  }

  function tick(el){
    const dateStr = el.getAttribute('data-joining-date');
    const join = parseDDMMYYYY(dateStr);
    const modeEl = el.querySelector('[data-gc="mode"]');
    const dEl = el.querySelector('[data-gc="days"]');
    const hEl = el.querySelector('[data-gc="hours"]');
    const mEl = el.querySelector('[data-gc="minutes"]');
    const sEl = el.querySelector('[data-gc="seconds"]');

    if(!join || !dEl || !hEl || !mEl || !sEl){
      // تاريخ غير صالح
      if(modeEl) modeEl.textContent = 'تاريخ غير صالح';
      return;
    }

    const now = nowInRiyadh();
    let diff = now - join; // موجب = منذ الانضمام، سالب = متبقي للانضمام
    const future = diff < 0;
    if(future) diff = -diff;

    const dayMs = 24*60*60*1000, hourMs = 60*60*1000, minMs = 60*1000, secMs = 1000;
    const days = Math.floor(diff / dayMs); diff -= days*dayMs;
    const hours = Math.floor(diff / hourMs); diff -= hours*hourMs;
    const minutes = Math.floor(diff / minMs); diff -= minutes*minMs;
    const seconds = Math.floor(diff / secMs);

    dEl.textContent = String(days).padStart(2,'0');
    hEl.textContent = String(hours).padStart(2,'0');
    mEl.textContent = String(minutes).padStart(2,'0');
    sEl.textContent = String(seconds).padStart(2,'0');

    if(modeEl){
      modeEl.textContent = future ? 'متبقي على الانتهاء' : 'منذ الانضمام';
      // تغيير لون الشارة حسب الحالة
      modeEl.style.color = future ? '#842029' : '#0f5132';
      modeEl.style.borderColor = future ? 'rgba(220,53,69,.35)' : 'rgba(25,135,84,.35)';
      modeEl.style.backgroundColor = future ? 'rgba(220,53,69,.10)' : 'rgba(25,135,84,.12)';
    }
  }

  function init(){
    const cards = document.querySelectorAll('.joining-glass-counter');
    if(!cards.length) return;
    // تحديث فوري ثم كل ثانية
    cards.forEach(tick);
    setInterval(()=> cards.forEach(tick), 1000);
  }

  if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', init);
  }else{
    init();
  }
})();
</script>



            
        </tr>
    <?php endforeach; ?>
</tbody>
            </table>
          </div>
          <?php echo form_close(); ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- jQuery + Bootstrap -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables + Buttons -->
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.bootstrap5.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.colVis.min.js"></script>

<script>

   

  // فتح بوب-أب صفحة التصدير العامة
  function openExportPopup(){
    var url = "<?php echo site_url('users1/export_emp_data'); ?>";
    var width = 1000, height = 700;
    var left = (screen.width/2) - (width/2);
    var top  = (screen.height/2) - (height/2);
    window.open(url,'ExportEmployees','width='+width+',height='+height+',top='+top+',left='+left+',resizable=yes,scrollbars=yes,status=no');
  }

  // بوب-أب الموظف (الموجود سابقًا)
  function openExemptionPopup(empId) {
  const url = "<?php echo site_url('users1/view_emp'); ?>/" + encodeURIComponent(empId);
  const w = window.open(url, '_blank'); // يفتح في تبويب جديد
  if (w) w.opener = null;               // أمان إضافي (no opener)
}


  // شاشة التحميل
  window.addEventListener('load', function(){
    const loading = document.getElementById('loading-screen');
    const main = document.querySelector('.main-container');
    loading.style.opacity='0';
    setTimeout(function(){ loading.style.display='none'; document.body.style.overflow='auto'; main.style.visibility='visible'; main.style.opacity='1'; }, 400);
  });

$(document).ready(function () {
  var dt = $('.dataTables-example').DataTable({
    responsive: true,
    pageLength: 25,
    lengthChange: true, // ✅ تفعيل خيار تغيير عدد الصفوف
    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "الكل"]],
    layout: {
      topStart: {
        buttons: [
          { extend:'copy',  text:'<i class="fa fa-copy"></i> نسخ' },
          { extend:'excel', text:'<i class="fa fa-file-excel"></i> إكسل' },
          { extend:'pdf',   text:'<i class="fa fa-file-pdf"></i> PDF' },
          { extend:'print', text:'<i class="fa fa-print"></i> طباعة' },
          { extend:'colvis',text:'<i class="fa fa-eye"></i> إظهار/إخفاء الأعمدة' },
          {
            text:'<i class="fa fa-file-export"></i> تصدير بيانات الموظفين',
            className:'btn btn-outline-light',
            action:function(){ openExportPopup(); }
          }
        ]
      },
      bottomStart: { // ✅ مكان القائمة وعدد الصفوف
        pageLength: {
          menu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "الكل"] ]
        }
      }
    },
    language:{ url:'https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json' }
  });
});

</script>
</body>
</html>
