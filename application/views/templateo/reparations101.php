<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التعويضات</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.css">
    <style>
        :root{--marsom-blue:#001f3f;--marsom-orange:#FF8C00;--text-light:#fff;--text-dark:#343a40;--glass-bg:rgba(255,255,255,.08);--glass-border:rgba(255,255,255,.2);--glass-shadow:rgba(0,0,0,.5)}
        body{font-family:'Tajawal',sans-serif;overflow:hidden;background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);background-size:400% 400%;animation:grad 20s ease infinite;color:var(--text-dark);position:relative}
        @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        .particles{position:fixed;inset:0;overflow:hidden;z-index:-1;pointer-events: none;}
        .particle{position:absolute;background:rgba(255,140,0,.1);clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);animation:float 25s infinite ease-in-out;opacity:0;filter:blur(2px)}
        .particle:nth-child(even){background:rgba(0,31,63,.1)}
        .particle:nth-child(1){width:40px;height:40px;left:10%;top:20%;animation-duration:18s}
        .particle:nth-child(2){width:70px;height:70px;left:25%;top:50%;animation-duration:22s;animation-delay:2s}
        .particle:nth-child(3){width:55px;height:55px;left:40%;top:10%;animation-duration:25s;animation-delay:5s}
        .particle:nth-child(4){width:80px;height:80px;left:60%;top:70%;animation-duration:20s;animation-delay:8s}
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
        .top-actions{position:fixed;top:12px;right:12px;display:flex;gap:10px;z-index:5}
        .top-actions a{background:rgba(255,255,255,.12);border:1px solid var(--glass-border);color:#fff;text-decoration:none;border-radius:10px;padding:8px 14px;display:inline-flex;align-items:center;gap:8px;transition:.25s}
        .top-actions a:hover{background:rgba(255,255,255,.2);color:var(--marsom-orange)}
        .action-btn { cursor: pointer; margin: 0 5px; }
    </style>
</head>
<body>

<div class="particles"><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div></div>
<div id="loading-screen"><div class="loader"></div><h3 style="color:#fff">جاري تحميل التقرير ...</h3></div>
<div class="top-actions"><a href="javascript:history.back()"><i class="fas fa-arrow-right"></i><span>رجوع</span></a><a href="<?php echo site_url('users1/main_hr1'); ?>"><i class="fas fa-home"></i><span>الرئيسية</span></a></div>

<div class="main-container container-fluid">
    <div class="text-center"><h1 class="page-title">التعويضات</h1></div>
    <div class="row">
        <div class="col-12">
            <div class="card table-card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>سبب التعويض</th>
                                    <th>الرقم الوظيفي</th>
                                    <th>اسم الموظف</th>
                                    <th>المبلغ</th>
                                    <th>مسير الرواتب</th>
                                    <th>منشئ الطلب</th>
                                    <th>تاريخ الطلب</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $idno = 0; ?>
                                <?php if (isset($get_salary_reparations) && is_array($get_salary_reparations)): ?>
                                    <?php foreach($get_salary_reparations as $reparation) : ?>
                                        <?php $idno++; ?>
                                        <tr data-reparation-id="<?php echo $reparation['id']; ?>">
                                            <td><?php echo $idno; ?></td>
                                            <td><?php echo html_escape($reparation['type']); ?></td>
                                            <td><?php echo html_escape($reparation['emp_id']); ?></td>
                                            <td><?php echo html_escape($reparation['emp_name']); ?></td>
                                            <td><?php echo number_format($reparation['amount'], 2); ?></td>
                                            <td><?php echo html_escape($reparation['sheet_id']); ?></td>
                                            <td><?php echo html_escape($reparation['name']); ?></td>
                                            <td><?php echo html_escape($reparation['reparation_date']); ?></td>
                                            <td>
                                                <a class="action-btn text-primary edit-btn" data-id="<?php echo $reparation['id']; ?>" title="تعديل"><i class="fas fa-edit"></i></a>
                                                <a class="action-btn text-danger delete-btn" data-id="<?php echo $reparation['id']; ?>" title="حذف"><i class="fas fa-trash-alt"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reparationModal" tabindex="-1" aria-labelledby="reparationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="reparationModalLabel">إضافة / تعديل تعويض</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reparationForm">
                    <input type="hidden" name="id" id="reparation_id">
                    <div class="row">
    <div class="col-md-6 mb-3"><label for="emp_id" class="form-label">الرقم الوظيفي</label><input type="text" class="form-control" id="emp_id" name="emp_id" required></div>
    <div class="col-md-6 mb-3"><label for="emp_name" class="form-label">اسم الموظف</label><input type="text" class="form-control" id="emp_name" name="emp_name" readonly></div>
    <div class="col-md-12 mb-3"><label for="type" class="form-label">سبب التعويض</label><input type="text" class="form-control" id="type" name="type" required></div>
    <div class="col-md-6 mb-3"><label for="amount" class="form-label">المبلغ</label><input type="number" step="0.01" class="form-control" id="amount" name="amount" required></div>
<div class="col-md-6 mb-3" id="sheet_id_container">
    <label for="sheet_id" class="form-label">مسير الرواتب</label>
    <select class="form-select" id="sheet_id" name="sheet_id">
        <option value="">-- اختر المسير --</option>
        <?php if(!empty($salary_sheets)): ?>
            <?php foreach($salary_sheets as $sheet): ?>
                <option value="<?php echo $sheet['id']; ?>">
                    <?php echo $sheet['type']; ?> (<?php echo $sheet['start_date']; ?>)
                </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
</div>

    <div class="col-md-6 mb-3"><label for="reparation_date" class="form-label">تاريخ التعويض</label><input type="date" class="form-control" id="reparation_date" name="reparation_date" required></div>
    <div class="col-md-12 mb-3"><label for="notes" class="form-label">ملاحظات</label><textarea class="form-control" id="notes" name="notes" rows="2"></textarea></div>
</div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="submit" form="reparationForm" class="btn btn-primary">حفظ البيانات</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="uploadModalLabel">رفع ملف إكسل</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo site_url('users1/upload_reparations_sheet'); ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <p>يرجى رفع ملف Excel (xlsx) يحتوي على الأعمدة التالية بالترتيب:</p>
                    <p><code>emp_id, emp_name, type, amount, sheet_id</code></p>
                    <hr>
                    <div class="mb-3">
                        <label for="reparation_file" class="form-label">اختر الملف</label>
                        <input class="form-control" type="file" id="reparation_file" name="reparation_file" required accept=".xlsx">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <button type="submit" class="btn btn-success">رفع ومعالجة</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Loading Screen
    window.addEventListener('load', function(){
        const loading = document.getElementById('loading-screen');
        const main = document.querySelector('.main-container');
        loading.style.opacity='0';
        setTimeout(function(){ loading.style.display='none'; document.body.style.overflow='auto'; main.style.visibility='visible'; main.style.opacity='1'; }, 400);
    });

    $(document).ready(function () {
        // --- CSRF TOKEN SETUP FOR AJAX ---
        // This is important for security in CodeIgniter
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
        var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

        // Initialize DataTable
        var dt = $('.dataTables-example').DataTable({
            responsive: true,
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "الكل"]],
            layout: {
                topStart: {
                    buttons: [
    { extend:'copy', text:'<i class="fa fa-copy"></i> نسخ' },
    { extend:'excel', text:'<i class="fa fa-file-excel"></i> إكسل' },
    { extend:'pdf', text:'<i class="fa fa-file-pdf"></i> PDF' },
    { extend:'print', text:'<i class="fa fa-print"></i> طباعة' },
    {
        text:'<i class="fa fa-plus-circle"></i> إضافة جديد',
        className:'btn btn-success',
        action: function () {
            $('#reparationForm')[0].reset();
            $('#reparation_id').val('');
            $('#reparationModalLabel').text('إضافة تعويض جديد');
            $('#reparationModal').modal('show');
        }
    },
    {
        text:'<i class="fa fa-upload"></i> رفع ملف',
        className:'btn btn-info',
        action: function () {
            $('#uploadModal').modal('show');
        }
    },
    // --- NEW BUTTON ---
    {
        text:'<i class="fa fa-trash"></i> حذف السجلات السابقة',
        className:'btn btn-danger',
        action: function () {
            Swal.fire({
                title: 'هل أنت متأكد تماماً؟',
                html: "سيتم حذف <b>كافة</b> سجلات التعويضات بشكل نهائي. <br>هذا الإجراء لا يمكن التراجع عنه!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، قم بالحذف!',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    var ajaxData = {};
                    ajaxData[csrfName] = csrfHash;
                    $.ajax({
                        url: "<?php echo site_url('users1/clear_all_reparations'); ?>",
                        type: 'POST',
                        data: ajaxData,
                        dataType: 'json',
                        success: function(response){
                            csrfHash = response.csrf_hash;
                            $('input[name="'+csrfName+'"]').val(csrfHash);
                            if(response.status === 'success'){
                                Swal.fire('تم الحذف!', response.message, 'success').then(() => location.reload());
                            } else {
                                Swal.fire('خطأ!', response.message, 'error');
                            }
                        },
                        error: function() { Swal.fire('خطأ!', 'فشل الاتصال بالخادم.', 'error'); }
                    });
                }
            });
        }
    }
    // --- END NEW BUTTON ---
]
                }
            },
            language: { url:'https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json' }
        });

        // AJAX to get employee name when emp_id is entered
        $('#emp_id').on('blur', function() {
            var empId = $(this).val();
            if (empId) {
                var ajaxData = { employee_id: empId };
                ajaxData[csrfName] = csrfHash; // Add CSRF token

                $.ajax({
                    url: "<?php echo site_url('users1/get_employee_data'); ?>",
                    type: "POST",
                    data: ajaxData,
                    dataType: "json",
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#emp_name').val(response.employee_name);
                        } else {
                            $('#emp_name').val('الموظف غير موجود');
                        }
                    }
                });
            }
        });

        // Handle Form Submission for Add/Edit
        $('#reparationForm').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serializeArray();
            formData.push({name: csrfName, value: csrfHash}); // Add CSRF token

            $.ajax({
                url: "<?php echo site_url('users1/save_reparation'); ?>",
                type: "POST",
                data: $.param(formData),
                dataType: "json",
                success: function(response) {
                    if (response.status === 'success') {
                        $('#reparationModal').modal('hide');
                        Swal.fire({icon: 'success', title: 'تم!', text: response.message}).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({icon: 'error', title: 'خطأ!', html: response.message});
                    }
                },
                error: function() {
                     Swal.fire({icon: 'error', title: 'خطأ!', text: 'حدث خطأ في الاتصال بالخادم.'});
                }
            });
        });

        // *** THE FIX IS HERE: EVENT DELEGATION ***
        // Handle Edit Button Click
        $('.dataTables-example tbody').on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            var ajaxData = { id: id };
            ajaxData[csrfName] = csrfHash; // Add CSRF token

            $.ajax({
                url: "<?php echo site_url('users1/get_reparation_data'); ?>",
                type: 'POST',
                data: ajaxData,
                dataType: 'json',
                success: function(response){
                    if(response.status === 'success'){
    var data = response.data;
    $('#reparation_id').val(data.id);
    $('#emp_id').val(data.emp_id);
    $('#emp_name').val(data.emp_name);
    $('#type').val(data.type);
    $('#amount').val(data.amount);
    $('#sheet_id').val(data.sheet_id);
     $('#reparation_date').val(data.reparation_date); // <-- ADD THIS LINE
    $('#notes').val(data.notes);
    $('#reparationModalLabel').text('تعديل التعويض');
    $('#reparationModal').modal('show');
}else {
                        Swal.fire('خطأ!', response.message, 'error');
                    }
                }
            });
        });

        // Handle Delete Button Click
        $('.dataTables-example tbody').on('click', '.delete-btn', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "لن تتمكن من التراجع عن هذا الإجراء!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، احذفه!',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    var ajaxData = { id: id };
                    ajaxData[csrfName] = csrfHash; // Add CSRF token

                    $.ajax({
                        url: "<?php echo site_url('users1/delete_reparation'); ?>",
                        type: 'POST',
                        data: ajaxData,
                        dataType: 'json',
                        success: function(response){
                            if(response.status === 'success'){
                                Swal.fire('تم الحذف!', response.message, 'success').then(() => location.reload());
                            } else {
                                Swal.fire('خطأ!', response.message, 'error');
                            }
                        }
                    });
                }
            });
        });
    });
</script>

</body>
</html>