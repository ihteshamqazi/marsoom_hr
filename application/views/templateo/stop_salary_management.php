<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة إيقاف الرواتب</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    
    <style>
        :root{--marsom-blue:#001f3f;--marsom-orange:#FF8C00;--text-light:#fff;--text-dark:#343a40;--glass-bg:rgba(255,255,255,.08);--glass-border:rgba(255,255,255,.2);--glass-shadow:rgba(0,0,0,.5)}
        body{font-family:'Tajawal',sans-serif;background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);background-size:400% 400%;animation:grad 20s ease infinite;color:var(--text-dark);position:relative; min-height: 100vh;}
        @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        .particles{position:fixed;inset:0;overflow:hidden;z-index:-1;pointer-events: none;}
        .particle{position:absolute;background:rgba(255,140,0,.1);clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);animation:float 25s infinite ease-in-out;opacity:0;filter:blur(2px)}
        .particle:nth-child(even){background:rgba(0,31,63,.1)}
        /* Particle animation setup */
        .particle:nth-child(1){width:40px;height:40px;left:10%;top:20%;animation-duration:18s}
        .particle:nth-child(2){width:70px;height:70px;left:25%;top:50%;animation-duration:22s;animation-delay:2s}
        .particle:nth-child(3){width:55px;height:55px;left:40%;top:10%;animation-duration:25s;animation-delay:5s}
        .particle:nth-child(4){width:80px;height:80px;left:60%;top:70%;animation-duration:20s;animation-delay:8s}
        @keyframes float{0%{transform:translateY(0) translateX(0) rotate(0);opacity:0}20%{opacity:1}80%{opacity:1}100%{transform:translateY(-100vh) translateX(50px) rotate(300deg);opacity:0}}
        
        #loading-screen{position:fixed;inset:0;background:var(--marsom-blue);z-index:9999;display:flex;align-items:center;justify-content:center;flex-direction:column;transition:opacity .5s}
        .loader{width:50px;height:50px;border:5px solid rgba(255,255,255,.3);border-top:5px solid var(--marsom-orange);border-radius:50%;animation:spin 1s linear infinite;margin-bottom:16px}
        @keyframes spin{to{transform:rotate(360deg)}}
        
        .main-container{padding:30px 15px;visibility:hidden;opacity:0;transition:opacity .5s;position:relative;z-index:1}
        .page-title{font-family:'El Messiri',sans-serif;font-weight:700;font-size:2.8rem;color:var(--text-light);margin-bottom:32px;text-align:center;position:relative;display:inline-block;padding-bottom:10px;text-shadow:0 3px 6px rgba(0,0,0,.4)}
        .page-title::after{content:'';position:absolute;width:100px;height:4px;background:linear-gradient(90deg,var(--marsom-blue),var(--marsom-orange));bottom:0;left:50%;transform:translateX(-50%);border-radius:2px}
        
        .table-card{background:rgba(255,255,255,.95);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.3);border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,.15);padding:25px}
        .dataTables-example thead th{background-color:#001f3f !important;color:#fff;text-align:center;vertical-align:middle;border-bottom:2px solid #00152b}
        .dataTables-example tbody td{text-align:center;vertical-align:middle;font-size:14px;}
        .dt-buttons .btn{background-color:var(--marsom-orange);border-color:var(--marsom-orange);color:#fff;font-weight:500;margin:0 2px;box-shadow:0 2px 8px rgba(0,0,0,.2)}
        .dt-buttons .btn:hover{background:#e0882f;border-color:#e0882f;transform:translateY(-1px)}
        
        .top-actions{position:fixed;top:12px;right:12px;display:flex;gap:10px;z-index:5}
        .top-actions a{background:rgba(255,255,255,.12);border:1px solid var(--glass-border);color:#fff;text-decoration:none;border-radius:10px;padding:8px 14px;display:inline-flex;align-items:center;gap:8px;transition:.25s}
        .top-actions a:hover{background:rgba(255,255,255,.2);color:var(--marsom-orange)}
        .action-btn { cursor: pointer; margin: 0 5px; font-size: 1.1rem; }
        
        /* Select2 Customization */
        .select2-container--bootstrap-5 .select2-selection { border-color: #dee2e6; }
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-search__field { width: 100% !important; }
    </style>
</head>
<body>

<div class="particles"><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div></div>
<div id="loading-screen"><div class="loader"></div><h3 style="color:#fff">جاري تحميل البيانات ...</h3></div>
<div class="top-actions"><a href="javascript:history.back()"><i class="fas fa-arrow-right"></i><span>رجوع</span></a><a href="<?php echo site_url('users1/main_hr1'); ?>"><i class="fas fa-home"></i><span>الرئيسية</span></a></div>

<div class="main-container container-fluid">
    <div class="text-center"><h1 class="page-title">إدارة إيقاف الرواتب</h1></div>
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card table-card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="stopTable" class="table table-striped table-bordered table-hover dataTables-example" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الموظف</th>
                                    <th>فترة المسير (Salary Sheet)</th>
                                    <th>السبب</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($stop_requests)): ?>
                                    <?php foreach ($stop_requests as $req): ?>
                                    <tr>
                                        <td><?= $req['id'] ?></td>
                                        <td><?= $req['emp_name'] ?> <span class="badge bg-secondary ms-1"><?= $req['emp_id'] ?></span></td>
                                        <td>
                                            <span class="badge bg-info text-dark" style="font-size:0.9rem;">
                                                <?= $req['sheet_name'] ?>
                                            </span>
                                            <br>
                                            <small class="text-muted" style="font-size:0.8rem;">
                                                <?= $req['start_date'] ?> <i class="fas fa-arrow-left mx-1"></i> <?= $req['end_date'] ?>
                                            </small>
                                        </td>
                                        <td><?= $req['reason'] ?></td>
                                        
                                        <td>
                                            <a class="action-btn text-primary" onclick="editRow(<?= $req['id'] ?>)" title="تعديل"><i class="fas fa-edit"></i></a>
                                            <a class="action-btn text-danger" onclick="deleteRow(<?= $req['id'] ?>)" title="حذف"><i class="fas fa-trash-alt"></i></a>
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

<div class="modal fade" id="stopModal" tabindex="-1" aria-labelledby="stopModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitle">إضافة إيقاف جديد</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="stopForm">
                    <input type="hidden" name="id" id="row_id">
                    <input type="hidden" name="<?= $csrf_name ?>" value="<?= $csrf_hash ?>" id="csrf_token">

                    <div class="mb-3">
                        <label class="form-label fw-bold">الموظف (يمكن اختيار أكثر من موظف)</label>
                        <select class="form-select select2" name="emp_ids[]" id="emp_ids" multiple="multiple" required style="width:100%">
                            <?php foreach($employees as $emp): ?>
                                <option value="<?= $emp['username'] ?>">
                                    <?= $emp['name'] ?> (<?= $emp['username'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">ابحث بالاسم أو الرقم الوظيفي.</div>
                    </div>  
                    <div class="mb-3">
        <label class="form-label fw-bold">تاريخ الإيقاف (اختياري)</label>
        <input type="date" class="form-control" name="stop_date" id="stop_date">
        <div class="form-text text-muted">اذا تم تحديد تاريخ، يجب أن يكون ضمن فترة مسير الرواتب المختار.</div>
    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">مسير الرواتب (الفترة)</label>
                        <select class="form-select" name="sheet_id" id="sheet_id" >
                            <option value="">-- اختر الفترة التي سيتم إيقاف الراتب فيها --</option>
                            <?php foreach($salary_sheets as $sheet): ?>
                                <option value="<?= $sheet['id'] ?>">
                                    <?= $sheet['type'] ?> (<?= $sheet['start_date'] ?> - <?= $sheet['end_date'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">السبب</label>
                        <textarea class="form-control" name="reason" id="reason" rows="3" required placeholder="سبب إيقاف الراتب..."></textarea>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="submit" form="stopForm" class="btn btn-primary">حفظ البيانات</button>
            </div>
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Remove Loading Screen
    window.addEventListener('load', function(){
        document.getElementById('loading-screen').style.opacity='0';
        setTimeout(() => { 
            document.getElementById('loading-screen').style.display='none'; 
            document.body.style.overflow='auto'; 
            document.querySelector('.main-container').style.visibility='visible';
            document.querySelector('.main-container').style.opacity='1';
        }, 400);
    });

    $(document).ready(function() {
        // Initialize DataTable
        $('#stopTable').DataTable({
            responsive: true,
            pageLength: 25,
            language: { url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json' },
            dom: 'Bfrtip',
            buttons: [
                { 
                    text: '<i class="fas fa-plus-circle"></i> إضافة إيقاف جديد', 
                    className: 'btn btn-success', 
                    action: function ( e, dt, node, config ) { openModal(); }
                },
                { extend: 'excel', text: '<i class="fas fa-file-excel"></i> إكسل', className: 'btn btn-success' },
                { extend: 'print', text: '<i class="fas fa-print"></i> طباعة', className: 'btn btn-secondary' }
            ]
        });

        // Initialize Select2
        $('.select2').select2({
            dropdownParent: $('#stopModal'),
            theme: 'bootstrap-5',
            placeholder: "اختر الموظف (يمكن اختيار أكثر من واحد)",
            allowClear: true,
            language: {
                noResults: function() { return "لا توجد نتائج"; }
            }
        });
    });

    const modal = new bootstrap.Modal(document.getElementById('stopModal'));

    function openModal() {
        $('#stopForm')[0].reset();
        $('#row_id').val('');
        $('#emp_ids').val(null).trigger('change'); 
        $('#modalTitle').text('إضافة إيقاف جديد');
        modal.show();
    }

    function editRow(id) {
    $.post('<?= site_url("users1/get_stop_salary_details") ?>', 
        { id: id, '<?= $csrf_name ?>': $('#csrf_token').val() }, 
        function(res) {
            if(res.status === 'success') {
                $('#csrf_token').val(res.csrf_hash);
                $('#row_id').val(res.data.id);
                
                // Set single employee
                $('#emp_ids').val([res.data.emp_id]).trigger('change');
                
                $('#sheet_id').val(res.data.sheet_id);
                
                // Set the Stop Date
                $('#stop_date').val(res.data.stop_date); // <--- ADDED THIS LINE
                
                $('#reason').val(res.data.reason);
                $('#modalTitle').text('تعديل إيقاف الراتب');
                modal.show();
            }
        }, 'json'
    );
}

    $('#stopForm').submit(function(e) {
        e.preventDefault();
        var formData = $(this).serialize();

        $.post('<?= site_url("users1/save_stop_salary") ?>', formData, function(res) {
            $('#csrf_token').val(res.csrf_hash);
            
            if(res.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'تم الحفظ!',
                    text: res.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => location.reload());
                modal.hide();
            } else {
                Swal.fire({icon: 'error', title: 'خطأ!', text: res.message});
            }
        }, 'json').fail(function() {
            Swal.fire({icon: 'error', title: 'خطأ!', text: 'حدث خطأ في الاتصال بالخادم.'});
        });
    });

    function deleteRow(id) {
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
                $.post('<?= site_url("users1/delete_stop_salary") ?>', 
                    { id: id, '<?= $csrf_name ?>': $('#csrf_token').val() }, 
                    function(res) {
                        $('#csrf_token').val(res.csrf_hash);
                        if(res.status === 'success') {
                            Swal.fire('تم الحذف!', res.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('خطأ!', res.message, 'error');
                        }
                    }, 'json'
                );
            }
        });
    }
</script>

</body>
</html>