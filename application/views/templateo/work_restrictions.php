<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title> قيود العمل </title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />

  <link rel="preconnect" href="https://fonts.googleapis.com">
 <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
 <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

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

    /* Style for Select2 in modal */
    .select2-container--bootstrap-5 .select2-dropdown {
        z-index: 1060; /* Ensure it appears above the modal */
    }
    .select2-container--bootstrap-5 .select2-selection {
        text-align: right !important;
    }
 </style>
</head>
<body>

<div class="particles">
 <div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div>
 <div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div>
</div>

<div id="loading-screen">
 <div class="loader"></div>
 <h3 style="color:#fff">جاري تحميل التقرير ...</h3>
</div>

<div class="top-actions">
 <a href="javascript:history.back()"><i class="fas fa-arrow-right"></i><span>رجوع</span></a>
 <a href="<?php echo site_url('dashboard'); ?>"><i class="fas fa-home"></i><span>الرئيسية</span></a>
</div>

<div class="main-container container-fluid">
 <div class="text-center">
  <h1 class="page-title"> قيود العمل</h1>
 </div>

 <div class="row">
  <div class="col-12">
   <div class="card table-card">
    <div class="card-body">
     <div class="table-responsive">
      <table class="table table-striped table-bordered table-hover dataTables-example" style="width:100%">
       <thead>
        <tr>
         <th>الرقم الوظيفي</th>
         <th>اسم الموظف</th>
         <th> الادارة</th>
         <th> الشركة</th>
         <th> وقت الدخول</th>
         <th> وقت الخروج</th>
         <th> اقصى وقت للدخول</th>
                  <th> ساعات العمل</th>
                  <th> مسير رواتب</th>
         <th>إجراءات</th>
        </tr>
       </thead>
       <tbody>
        <?php foreach($get_salary_vacations as $restriction) : ?>
         <tr>
                              <td>
           <button type="button" class="btn btn-sm btn-primary"
               onclick="openExemptionPopup('<?php echo $restriction['emp_id']; ?>', '<?php echo $restriction['sheet_id']; ?>')">
            <?php echo $restriction['emp_id']; ?>
           </button>
          </td>
                              <td><?php echo $restriction['emp_name']; ?></td>
          <td><?php echo $restriction['management']; ?></td>
          <td><?php echo $restriction['company']; ?></td>
          <td><?php echo $restriction['first_punch']; ?></td>
          <td><?php echo $restriction['last_punch']; ?></td>
          <td><?php echo $restriction['maximum_departure_date']; ?></td>
                    <td><?php echo $restriction['working_hours']; ?></td>
                    <td><?php echo $restriction['sheet_id']; ?></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-success btn-edit" data-id="<?php echo $restriction['id']; ?>">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete" data-id="<?php echo $restriction['id']; ?>">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
         </tr>
        <?php endforeach; ?>
       </tbody>
      </table>
     </div>
    </div>
   </div>
  </div>
 </div>
</div>

<div class="modal fade" id="workRestrictionModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true" dir="rtl">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">إضافة/تعديل قيد عمل</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="restrictionForm">
                    <input type="hidden" name="id" id="form_id">
                    <input type="hidden" name="<?php echo $csrf_name; ?>" id="csrf_token" value="<?php echo $csrf_hash; ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="form_emp_id" class="form-label">الموظف <span class="text-danger">*</span></label>
                            <select class="form-control" id="form_emp_id" name="emp_id" required>
                                <option value="" selected disabled>اختر موظف...</option>
                                <?php foreach($all_employees as $employee): ?>
                                    <option value="<?php echo $employee['username']; ?>" data-name="<?php echo htmlspecialchars($employee['name'], ENT_QUOTES); ?>">
                                        <?php echo $employee['name']; ?> (<?php echo $employee['username']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="emp_name" id="form_emp_name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="form_sheet_id" class="form-label">معرف مسير الرواتب (Sheet ID)</label>
                            <input type="text" class="form-control" id="form_sheet_id" name="sheet_id" placeholder="مثال: 2025/11">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="form_management" class="form-label">الإدارة</label>
                            <input type="text" class="form-control" id="form_management" name="management">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="form_company" class="form-label">الشركة</label>
                            <input type="text" class="form-control" id="form_company" name="company">
                        </div>
                    </div>
                    
                    <hr class="my-3">

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="form_first_punch" class="form-label">وقت الدخول (HH:MM) <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="form_first_punch" name="first_punch" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="form_last_punch" class="form-label">وقت الخروج (HH:MM) <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="form_last_punch" name="last_punch" required>
                        </div>
                         <div class="col-md-4 mb-3">
                            <label for="form_working_hours" class="form-label">ساعات العمل (رقم)</label>
                            <input type="number" step="0.1" class="form-control" id="form_working_hours" name="working_hours" placeholder="مثال: 8.5">
                        </div>
                    </div>

                    <div class="row">
                       <div class="col-md-6 mb-3">
                            <label for="form_maximum_departure_date" class="form-label">أقصى وقت للدخول (HH:MM)</label>
                            <input type="time" class="form-control" id="form_maximum_departure_date" name="maximum_departure_date">
                        </div>
                    </div>
                    
                    <div id="form-message" class="mt-3"></div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-primary" id="btnSave"> <i class="fa fa-save"></i> حفظ </button>
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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
// Global variables for URLs and CSRF
var SAVE_URL   = "<?php echo site_url('users1/ajax_save_work_restriction'); ?>";
var GET_URL    = "<?php echo site_url('users1/ajax_get_work_restriction'); ?>";
var DELETE_URL = "<?php echo site_url('users1/ajax_delete_work_restriction'); ?>";
var CSRF_NAME  = "<?php echo $csrf_name; ?>";
var CSRF_HASH  = "<?php echo $csrf_hash; ?>";

 function openExportPopup(){
    var url = "<?php echo site_url('users1/exemption'); ?>";
    var width = 1000, height = 700;
    var left = (screen.width/2) - (width/2);
    var top  = (screen.height/2) - (height/2);
    window.open(url,'ExportEmployees','width='+width+',height='+height+',top='+top+',left='+left+',resizable=yes,scrollbars=yes,status=no');
  }

  // بوب-أب الموظف (الموجود سابقًا)
  function openExemptionPopup(empId, sheetId) {
    var url = "<?php echo site_url('users1/exemption'); ?>/" + empId + "/" + sheetId;
    var width = 800, height = 600;
    var left = (screen.width/2) - (width/2);
    var top  = (screen.height/2) - (height/2);
    window.open(url,'ExemptionPopup','width='+width+',height='+height+',top='+top+',left='+left+',resizable=yes,scrollbars=yes,status=no');
  }
// ========================================================


// Function to show messages in the modal
function showFormMessage(message, isError = false) {
    var $msg = $('#form-message');
    $msg.html(message).removeClass('alert alert-success alert-danger');
    if (isError) {
        $msg.addClass('alert alert-danger');
    } else {
        $msg.addClass('alert alert-success');
    }
}

// Function to update CSRF token
function updateCsrf(newHash) {
    CSRF_HASH = newHash;
    $('#csrf_token').val(newHash);
}

$(document).ready(function () {
    
    // Initialize Select2 dropdown
    $('#form_emp_id').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#workRestrictionModal') // Attach it to the modal
    });

    // Auto-fill employee name when selected
    $('#form_emp_id').on('change', function() {
        var selectedName = $(this).find('option:selected').data('name');
        $('#form_emp_name').val(selectedName);
    });

    // Initialize the Bootstrap Modal
    const restrictionModal = new bootstrap.Modal(document.getElementById('workRestrictionModal'));

   var dt = $('.dataTables-example').DataTable({
      responsive: true,
      pageLength: 25,
      lengthChange: true,
      lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "الكل"]],
      layout: {
         topStart: {
            buttons: [
                {
              text:'<i class="fa fa-plus"></i> إضافة قيد جديد',
              className:'btn btn-success',
              action: function(e, dt, node, config) {
                        // Open the "Add" modal
                        $('#restrictionForm')[0].reset(); // Reset the form
                        $('#form_id').val(''); // Clear the ID
                        $('#form_emp_id').val(null).trigger('change'); // Reset Select2
                        $('#modalLabel').text('إضافة قيد جديد');
                        showFormMessage('');
                        restrictionModal.show();
                    }
           },
           { extend:'copy', text:'<i class="fa fa-copy"></i> نسخ' },
           { extend:'excel', text:'<i class="fa fa-file-excel"></i> إكسل' },
           { extend:'pdf', text:'<i class="fa fa-file-pdf"></i> PDF' },
           { extend:'print', text:'<i class="fa fa-print"></i> طباعة' },
           { extend:'colvis',text:'<i class="fa fa-eye"></i> إظهار/إخفاء الأعمدة' },
           {
              text:'<i class="fa fa-file-export"></i>  إعفاء للجميع',
              className:'btn btn-outline-light',
              action:function(){ openExportPopup(); } // Corrected action
           }
        ]
       },
       bottomStart: { 
        pageLength: {
         menu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "الكل"] ]
        }
       }
      },
      language:{ url:'https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json' }
   });

    // --- CRUD JavaScript ---

    // 2. EDIT: Open modal and fetch data
    $('.dataTables-example tbody').on('click', '.btn-edit', function() {
        var recordId = $(this).data('id');
        $('#restrictionForm')[0].reset();
        showFormMessage('');
        
        var payload = { id: recordId };
        payload[CSRF_NAME] = CSRF_HASH;

        $.ajax({
            url: GET_URL,
            type: 'POST',
            data: payload,
            dataType: 'json',
            success: function(response) {
                if(response.status === 'success') {
                    var data = response.data;
                    $('#modalLabel').text('تعديل قيد: ' + data.emp_name);
                    $('#form_id').val(data.id);
                    $('#form_emp_id').val(data.emp_id).trigger('change');
                    $('#form_emp_name').val(data.emp_name);
                    $('#form_management').val(data.management);
                    $('#form_company').val(data.company);
                    $('#form_first_punch').val(data.first_punch);
                    $('#form_last_punch').val(data.last_punch);
                    $('#form_maximum_departure_date').val(data.maximum_departure_date);
                    $('#form_sheet_id').val(data.sheet_id);
                    $('#form_working_hours').val(data.working_hours);
                    
                    updateCsrf(response.csrf_hash);
                    restrictionModal.show();
                } else {
                    alert('Error: ' + response.message);
                    updateCsrf(response.csrf_hash);
                }
            },
            error: function(xhr) {
                alert('An error occurred while fetching data.');
            }
        });
    });

    // 3. SAVE (Submit Add/Edit Form)
    $('#btnSave').on('click', function() {
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> ...جاري الحفظ');
        showFormMessage('');

        var formData = $('#restrictionForm').serialize();

        $.ajax({
            url: SAVE_URL,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if(response.status === 'success') {
                    showFormMessage(response.message, false);
                    setTimeout(function() {
                        restrictionModal.hide();
                        location.reload(); // Easiest way to refresh the table
                    }, 1500);
                } else {
                    showFormMessage(response.message, true);
                    updateCsrf(response.csrf_hash);
                }
            },
            error: function(xhr) {
                showFormMessage('An error occurred: ' + xhr.responseText, true);
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fa fa-save"></i> حفظ');
            }
        });
    });

    // 4. DELETE: Show confirmation and delete
    $('.dataTables-example tbody').on('click', '.btn-delete', function() {
        var recordId = $(this).data('id');
        if (confirm('هل أنت متأكد من حذف هذا السجل؟ لا يمكن التراجع عن هذا الإجراء.')) {
            
            var payload = { id: recordId };
            payload[CSRF_NAME] = CSRF_HASH;

            $.ajax({
                url: DELETE_URL,
                type: 'POST',
                data: payload,
                dataType: 'json',
                success: function(response) {
                    if(response.status === 'success') {
                        alert(response.message);
                        location.reload(); // Refresh the page
                    } else {
                        alert('Error: ' + response.message);
                        updateCsrf(response.csrf_hash);
                    }
                },
                error: function(xhr) {
                    alert('An error occurred while deleting.');
                }
            });
        }
    });


   // شاشة التحميل
   const loading = document.getElementById('loading-screen');
   const main = document.querySelector('.main-container');
   loading.style.opacity='0';
   setTimeout(function(){ loading.style.display='none'; document.body.style.overflow='auto'; main.style.visibility='visible'; main.style.opacity='1'; }, 400);
 });

</script>
</body>
</html>