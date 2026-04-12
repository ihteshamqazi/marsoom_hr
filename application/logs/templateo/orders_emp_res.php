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
    body{font-family:'Tajawal',sans-serif;overflow:hidden;background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);background-size:400% 400%;animation:grad 20s ease infinite;color:var(--text-dark);position:relative}
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
  <a href="<?php echo site_url('users1/main_emp'); ?>"><i class="fas fa-home"></i><span>الرئيسية</span></a>
</div>

<div class="main-container container-fluid">
  <div class="text-center">
    <h1 class="page-title">  طلباتي</h1>
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
                  <th>رقم الطلب</th>
                  <th>  تاريخ الطلب </th>
                  <th>   وقت الطلب</th>
                  <th>  نوع الطلب</th>
                  <th>  حالة الطلب</th> 
                  <th>     بانتظار موافقة </th>
                  <th>  مرفق </th>
                  <th>إجراءات المخالصة</th> 
                   
                </tr>
              </thead>
              <tbody>
    <?php foreach($get_salary_vacations as $employee) : ?>
        <tr>
             
            <td><?php echo $employee['id']; ?></td>
            <td><?php echo $employee['date']; ?></td>
            <td><?php echo $employee['time']; ?></td>
            <td><?php echo $employee['order_name']; ?></td>
            <td class="text-nowrap">
  <?php
    $statusVal = isset($employee['status']) ? (string)$employee['status'] : '';

    // خريطة الحالات → نص + نمط + أيقونة
    $statusMap = [
      '0'   => ['label' => 'بانتظار موافقة الموظف المسؤول',
                'class' => 'badge rounded-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                'icon'  => 'fa-hourglass-half'],
      '1'   => ['label' => 'بانتظار موافقة الموارد البشرية',
                'class' => 'badge rounded-pill bg-info-subtle text-info-emphasis border border-info-subtle',
                'icon'  => 'fa-user-tie'],
      '2'   => ['label' => 'تمت الموافقة',
                'class' => 'badge rounded-pill bg-success-subtle text-success-emphasis border border-success-subtle',
                'icon'  => 'fa-circle-check'],
      // حالات الرفض
      '3'   => ['label' => 'مرفوض',
                'class' => 'badge rounded-pill bg-danger-subtle text-danger-emphasis border border-danger-subtle',
                'icon'  => 'fa-circle-xmark'],
      '-1'  => ['label' => 'مرفوض',
                'class' => 'badge rounded-pill bg-danger-subtle text-danger-emphasis border border-danger-subtle',
                'icon'  => 'fa-circle-xmark'],
    ];

    // اختر الميتا المناسبة، أو حالة افتراضية لو غير معروف
    $meta = $statusMap[$statusVal] ?? [
      'label' => 'غير معروف',
      'class' => 'badge rounded-pill bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle',
      'icon'  => 'fa-question'
    ];
  ?>
  <span class="<?= $meta['class'] ?>" title="<?= htmlspecialchars($meta['label'], ENT_QUOTES) ?>">
    <i class="fa-solid <?= $meta['icon'] ?> ms-1"></i><?= $meta['label'] ?>
  </span>
</td>

           
           <?php
if (!function_exists('get_short_name_by_empid_cached')) {
  function get_short_name_by_empid_cached($empId) {
    static $cache = [];
    $empId = trim((string)$empId);
    if ($empId === '') return '';

    if (isset($cache[$empId])) return $cache[$empId];

    $CI =& get_instance();
    // عدّل اسم العمود إذا اختلف لديك
    $CI->db->select('subscriber_name AS full_name');
    $CI->db->from('emp1');
    $CI->db->where('employee_id', $empId);
    $row = $CI->db->get()->row_array();

    $full = isset($row['full_name']) ? trim($row['full_name']) : '';
    if ($full === '') return $cache[$empId] = '';

    // تقسيم الاسم وتصفية الأجزاء الأقصر من حرفين
    if (function_exists('mb_strlen')) {
      $parts = preg_split('/\s+/u', $full, -1, PREG_SPLIT_NO_EMPTY);
      $parts = array_values(array_filter($parts, fn($p) => mb_strlen($p,'UTF-8') >= 2));
    } else {
      $parts = preg_split('/\s+/', $full, -1, PREG_SPLIT_NO_EMPTY);
      $parts = array_values(array_filter($parts, fn($p) => strlen($p) >= 2));
    }

    $cnt = count($parts);
    if ($cnt === 0) return $cache[$empId] = '';
    if ($cnt === 1) return $cache[$empId] = $parts[0];
    if ($cnt === 2) return $cache[$empId] = $parts[0] . ' ' . $parts[1];

    // ثلاثة فأكثر: الأول + قبل الأخير + الأخير
    $first   = $parts[0];
    $before  = $parts[$cnt - 2];
    $last    = $parts[$cnt - 1];

    return $cache[$empId] = $first . ' ' . $before . ' ' . $last;
  }
}
?>


<td class="text-nowrap">
  <?php
    $rid  = isset($employee['responsible_employee']) ? (string)$employee['responsible_employee'] : '';
    $name = $rid ? get_short_name_by_empid_cached($rid) : '';
    echo $name !== ''
      ? '<span title="رقم وظيفي: '.htmlspecialchars($rid,ENT_QUOTES,'UTF-8').'">'.htmlspecialchars($name,ENT_QUOTES,'UTF-8').'</span>'
      : '<span class="text-muted">'.($rid !== '' ? htmlspecialchars($rid,ENT_QUOTES,'UTF-8') : '—').'</span>';
  ?>
</td>

            <td>
  <?php
    $file = isset($employee['file']) ? trim($employee['file']) : '';
    if ($file !== ''):
      $url = base_url($file);                           // مسار المرفق الكامل
      $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION)); // امتداد الملف
      $name = basename($file);                          // اسم الملف للعنوان
  ?>
    <a href="<?= htmlspecialchars($url, ENT_QUOTES) ?>"
       class="btn btn-sm btn-outline-primary file-preview"
       title="عرض المرفق"
       data-url="<?= htmlspecialchars($url, ENT_QUOTES) ?>"
       data-ext="<?= htmlspecialchars($ext, ENT_QUOTES) ?>"
       data-name="<?= htmlspecialchars($name, ENT_QUOTES) ?>">
      <i class="fa-solid fa-paperclip"></i>
    </a>
  <?php else: ?>
    —
  <?php endif; ?>
</td>
<td>
            <?php 
            // We assume resignation requests are named 'استقالة'
            // Change this value if your resignation request type has a different name
            if (isset($employee['order_name']) && $employee['order_name'] === 'استقالة'): 
            ?>
                <button type="button" 
                        class="btn btn-sm btn-warning btn-clearance" 
                        data-bs-toggle="modal" 
                        data-bs-target="#clearanceModal"
                        data-request-id="<?php echo $employee['id']; ?>"
                        title="تحديد إدارات المخالصة">
                    <i class="fas fa-tasks"></i>
                </button>
            <?php else: ?>
                —
            <?php endif; ?>
        </td>
            
             
             
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
<div class="modal fade" id="clearanceModal" tabindex="-1" aria-labelledby="clearanceModalLabel" aria-hidden="true" dir="rtl">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="clearanceModalLabel"><i class="fas fa-tasks me-2"></i>تحديد إدارات المخالصة</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted">الرجاء تحديد الإدارات التي يجب على الموظف الحصول على مخالصة منها لإتمام إجراءات الاستقالة.</p>
        
        <input type="hidden" id="resignationRequestId">

        <div id="clearance-departments-list" class="mt-3" style="min-height: 200px;">
          <div class="d-flex justify-content-center align-items-center h-100">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
        <button type="button" class="btn btn-primary" id="saveClearanceBtn">
          <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
          حفظ التحديدات
        </button>
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

 function openAddReparationPopup(){
  window.location.href = "<?php echo site_url('users1/add_new_order'); ?>";
}



  // فتح بوب-أب صفحة التصدير العامة
  

  // بوب-أب الموظف (الموجود سابقًا)
   

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
    text:'<i class="fa fa-plus-circle"></i>     طلب جديد   ',
    className:'btn btn-success', // زر أخضر
    action:function(){ openAddReparationPopup(); }
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

<div class="modal fade" id="filePreviewModal" tabindex="-1" aria-hidden="true" dir="rtl">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fa-solid fa-paperclip ms-1"></i>
          <span id="filePreviewTitle">المرفق</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
      </div>
      <div class="modal-body p-0">
        <div id="filePreviewBody" class="w-100"></div>
      </div>
      <div class="modal-footer">
        <a id="downloadLink" class="btn btn-primary" href="#" target="_blank" rel="noopener">
          <i class="fa fa-download ms-1"></i> تنزيل
        </a>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('click', function(e){
  const btn = e.target.closest('.file-preview');
  if(!btn) return;

  e.preventDefault();

  const url  = btn.dataset.url;
  const ext  = (btn.dataset.ext || '').toLowerCase();
  const name = btn.dataset.name || 'المرفق';

  const body  = document.getElementById('filePreviewBody');
  const title = document.getElementById('filePreviewTitle');
  const dl    = document.getElementById('downloadLink');
  const modalEl = document.getElementById('filePreviewModal');

  // تحديث العناوين والروابط
  title.textContent = name;
  dl.href = url;

  // تنظيف محتوى المعاينة
  body.innerHTML = '';

  // معاينة حسب النوع
  if (['jpg','jpeg','png','gif','webp','bmp'].includes(ext)) {
    body.innerHTML = `<img src="${url}" class="img-fluid w-100" alt="${name}">`;
  } else if (ext === 'pdf') {
    // يمكن استخدام <embed> أيضًا
    body.innerHTML = `<iframe src="${url}" style="width:100%;height:75vh;border:0"></iframe>`;
  } else {
    // ملفات لا يدعمها المتصفح كمعاينة: افتحها في تبويب جديد
    window.open(url, '_blank');
    return;
  }

  // إظهار المودال
  const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
  modal.show();
});
</script>
<script>
$(document).ready(function() {
    var clearanceModal = new bootstrap.Modal(document.getElementById('clearanceModal'));
    var listContainer = $('#clearance-departments-list');
    var saveBtn = $('#saveClearanceBtn');

    // 1. When a clearance button on the table is clicked
    $(document).on('click', '.btn-clearance', function() {
        var requestId = $(this).data('request-id');
        $('#resignationRequestId').val(requestId);
        
        // Show loading spinner
        listContainer.html('<div class="d-flex justify-content-center align-items-center h-100"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
        
        // Fetch data via AJAX
        $.ajax({
            url: "<?php echo site_url('users1/get_clearance_data/'); ?>" + requestId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    populateModalCheckboxes(response.all_departments, response.selected_departments);
                } else {
                    listContainer.html('<div class="alert alert-danger">فشل في تحميل بيانات الإدارات.</div>');
                }
            },
            error: function() {
                listContainer.html('<div class="alert alert-danger">حدث خطأ في الاتصال بالخادم.</div>');
            }
        });
    });

    // 2. Function to build and display checkboxes in the modal
    function populateModalCheckboxes(allDepts, selectedDepts) {
        listContainer.empty();
        if (allDepts.length === 0) {
            listContainer.html('<p>لم يتم العثور على أي إدارات.</p>');
            return;
        }

        var html = '<div class="row">';
        $.each(allDepts, function(index, dept) {
            var isChecked = selectedDepts.includes(dept.id) ? 'checked' : '';
            html += `
                <div class="col-md-6">
                    <div class="form-check form-check-lg mb-2">
                        <input class="form-check-input" type="checkbox" value="${dept.id}" id="dept-${dept.id}" ${isChecked}>
                        <label class="form-check-label" for="dept-${dept.id}">
                            ${dept.name}
                        </label>
                    </div>
                </div>`;
        });
        html += '</div>';
        listContainer.html(html);
    }

    // 3. When the "Save" button in the modal is clicked
    saveBtn.on('click', function() {
        var button = $(this);
        var requestId = $('#resignationRequestId').val();
        var selectedIds = [];

        // Collect all checked department IDs
        listContainer.find('input[type="checkbox"]:checked').each(function() {
            selectedIds.push($(this).val());
        });

        // Show loading state on button
        button.prop('disabled', true).find('.spinner-border').removeClass('d-none');

        // Send data via AJAX to save
        $.ajax({
            url: "<?php echo site_url('users1/save_clearance_data'); ?>",
            type: 'POST',
            data: {
                request_id: requestId,
                department_ids: selectedIds
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alert(response.message); // Show success message
                    clearanceModal.hide(); // Hide the modal
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('حدث خطأ في الاتصال بالخادم.');
            },
            complete: function() {
                // Restore button to normal state
                button.prop('disabled', false).find('.spinner-border').addClass('d-none');
            }
        });
    });
});
</script>
</body>
</html>
