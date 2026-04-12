<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلبات العمل عن بعد (المحصلين)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.css">
    
    <style>
        :root{--marsom-blue:#001f3f;--marsom-orange:#FF8C00;--text-light:#fff;--text-dark:#343a40;}
        body{font-family:'Tajawal',sans-serif;background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);background-size:400% 400%;animation:grad 20s ease infinite;color:var(--text-dark);position:relative; min-height: 100vh;}
        @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        .particles{position:fixed;inset:0;overflow:hidden;z-index:-1}
        .particle{position:absolute;background:rgba(255,140,0,.1);clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);animation:float 25s infinite ease-in-out;opacity:0;filter:blur(2px)}
        .particle:nth-child(even){background:rgba(0,31,63,.1)}
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
        .dt-buttons .btn{background-color:var(--marsom-orange);border-color:var(--marsom-orange);color:#fff;font-weight:500;margin:0 2px;}
        .dt-buttons .btn:hover{background:#e0882f;border-color:#e0882f;transform:translateY(-1px)}
        
        .top-actions{position:fixed;top:12px;right:12px;display:flex;gap:10px;z-index:5}
        .top-actions a{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);color:#fff;text-decoration:none;border-radius:10px;padding:8px 14px;display:inline-flex;align-items:center;gap:8px;transition:.25s}
        .top-actions a:hover{background:rgba(255,255,255,.2);color:var(--marsom-orange)}
        .action-btn { cursor: pointer; margin: 0 5px; font-size: 1.1rem; }
    </style>
</head>
<body>

<div class="particles"><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div></div>
<div id="loading-screen"><div class="loader"></div><h3 style="color:#fff">جاري تحميل البيانات ...</h3></div>
<div class="top-actions"><a href="javascript:history.back()"><i class="fas fa-arrow-right"></i><span>رجوع</span></a><a href="<?php echo site_url('users1/main_hr1'); ?>"><i class="fas fa-home"></i><span>الرئيسية</span></a></div>

<div class="main-container container-fluid">
    <div class="text-center"><h1 class="page-title">طلبات العمل عن بعد (المحصلين)</h1></div>
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card table-card">
                <div class="card-body">
                    
                    <div class="row mb-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-bold"><i class="fas fa-filter me-1"></i> تصفية حسب الحالة:</label>
                            <select id="statusFilter" class="form-select shadow-sm border-primary">
                                <option value="">جميع الحالات</option>
                                <option value="قيد الانتظار">قيد الانتظار</option>
                                <option value="معتمد">معتمد</option>
                                <option value="مرفوض">مرفوض</option>
                            </select>
                        </div>
                    </div>
                    <hr>

                    <div class="bulk-actions mb-3 p-2 bg-light border rounded" style="display: none;">
                        <span class="fw-bold me-3">إجراء جماعي:</span>
                        <button class="btn btn-success btn-sm me-2" onclick="processBulk('approve')"><i class="fas fa-check-double"></i> اعتماد المحدد</button>
                        <button class="btn btn-danger btn-sm" onclick="processBulk('reject')"><i class="fas fa-times-circle"></i> رفض المحدد</button>
                    </div>

                    <div class="table-responsive">
                        <table id="requestsTable" class="table table-striped table-bordered table-hover dataTables-example" style="width:100%">
                            <thead>
                                <tr>
                                    <th style="width: 40px; text-align: center;">
                                        <?php if($is_hr): ?>
                                            <input type="checkbox" id="selectAll" class="form-check-input shadow-sm" style="transform: scale(1.2); cursor: pointer;">
                                        <?php endif; ?>
                                    </th>
                                    <th>#</th>
                                    <th>الموظف</th>
                                    <th>التاريخ</th>
                                    <th>وقت الدخول (عن بعد)</th>
                                    <th>وقت الخروج (عن بعد)</th>
                                    <th>المدير المباشر</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($requests)): ?>
                                    <?php foreach ($requests as $req): ?>
                                    <tr>
                                        <td class="text-center align-middle">
                                            <?php if($is_hr): ?>
                                                <input type="checkbox" class="form-check-input req-checkbox" value="<?= $req['id'] ?>" style="transform: scale(1.2); cursor: pointer;">
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $req['id'] ?></td>
                                        <td><?= $req['emp_name'] ?> <br><small class="text-muted"><?= $req['emp_id'] ?></small></td>
                                        <td><span class="badge bg-primary"><?= $req['request_date'] ?></span></td>
                                        <td dir="ltr" class="text-success fw-bold"><?= date('H:i', strtotime($req['start_time'])) ?></td>
                                        <td dir="ltr" class="text-danger fw-bold"><?= date('H:i', strtotime($req['end_time'])) ?></td>
                                        <td><?= $req['manager_name'] ?? 'إدارة عليا' ?></td>
                                        <td>
                                            <?php 
                                                if($req['status'] == 0) echo '<span class="badge bg-warning text-dark">قيد الانتظار</span>';
                                                elseif($req['status'] == 1) echo '<span class="badge bg-success">معتمد</span>';
                                                else echo '<span class="badge bg-danger">مرفوض</span>';
                                            ?>
                                        </td>
                                        <td>
                                            <?php if($req['status'] == 0): ?>
                                                <?php if($is_hr): ?>
                                                    <a class="action-btn text-success" onclick="processRequest(<?= $req['id'] ?>, 'approve')" title="اعتماد"><i class="fas fa-check-circle"></i></a>
                                                    <a class="action-btn text-danger" onclick="processRequest(<?= $req['id'] ?>, 'reject')" title="رفض"><i class="fas fa-times-circle"></i></a>
                                                <?php else: ?>
                                                    <span class="text-muted small"><i class="fas fa-hourglass-half"></i> بانتظار قسم الموارد البشرية</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted"><i class="fas fa-lock"></i> مقفل</span>
                                                <?php if($req['status'] == 1 && $is_hr): ?>
                                                    <a class="action-btn text-danger ms-3" onclick="processRequest(<?= $req['id'] ?>, 'reject')" title="إلغاء الاعتماد (صلاحية HR)"><i class="fas fa-undo"></i> تراجع ورفض</a>
                                                <?php endif; ?>
                                            <?php endif; ?>
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

<div class="modal fade" id="addRequestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-laptop-house me-2"></i> تقديم طلب عمل عن بعد</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> يمكنك اختيار أي وقت للعمل عن بعد بحد أقصى <strong>ساعتين</strong>. إذا كان وقت الخروج بعد منتصف الليل (مثال: 1 ص)، سيقوم النظام بحسابه تلقائياً لليوم التالي.
                </div>
                <form id="requestForm">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" id="csrf_token">

                    <div class="mb-3">
                        <label class="form-label fw-bold">تاريخ العمل</label>
                        <input type="date" name="request_date" class="form-control" required min="2026-02-18" max="2026-03-17" value="2026-02-18">
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">وقت الدخول</label>
                            <input type="time" name="start_time" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">وقت الخروج</label>
                            <input type="time" name="end_time" class="form-control" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" form="requestForm" class="btn btn-success">إرسال الطلب</button>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
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
        
        // مصفوفة الأزرار: تظل فارغة للموظف العادي، ويضاف لها زر "طلب جديد" إذا كان المستخدم HR
        var dtButtons = [];
        
        <?php if($is_hr): ?>
        dtButtons.push({ 
            text: '<i class="fas fa-plus-circle"></i> تقديم طلب جديد', 
            className: 'btn btn-success', 
            action: function ( e, dt, node, config ) { 
                var myModal = new bootstrap.Modal(document.getElementById('addRequestModal'));
                myModal.show();
            }
        });
        <?php endif; ?>

        var table = $('#requestsTable').DataTable({
            responsive: true,
            pageLength: 25,
            language: { url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json' },
            dom: 'Bfrtip',
            columnDefs: [
                { orderable: false, targets: 0 } // منع الترتيب من عمود المربعات
            ],
            order: [[1, 'desc']], // ترتيب افتراضي تنازلي حسب رقم الطلب
            buttons: dtButtons // الأزرار الديناميكية
        });

        // --- تفعيل فلتر الحالة ---
        $('#statusFilter').on('change', function() {
            table.column(7).search(this.value).draw();
        });

        // --- Bulk Selection Logic ---
        $('#selectAll').on('change', function() {
            var isChecked = $(this).prop('checked');
            $('.req-checkbox').prop('checked', isChecked);
            toggleBulkActions();
        });

        $(document).on('change', '.req-checkbox', function() {
            toggleBulkActions();
            if (!$(this).prop('checked')) {
                $('#selectAll').prop('checked', false);
            }
        });

        function toggleBulkActions() {
            if ($('.req-checkbox:checked').length > 0) {
                $('.bulk-actions').slideDown(200);
            } else {
                $('.bulk-actions').slideUp(200);
            }
        }
    });

    // --- Bulk Processing Logic ---
    function processBulk(action) {
        var selectedIds = [];
        $('.req-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) return;

        var actionText = action === 'approve' ? 'اعتماد' : 'رفض (وحذف البصمات إن وجدت)';
        var actionColor = action === 'approve' ? '#198754' : '#d33';

        Swal.fire({
            title: 'تأكيد إجراء جماعي',
            text: "هل أنت متأكد من " + actionText + " عدد " + selectedIds.length + " طلبات محددة؟",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: actionColor,
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'نعم، تأكيد الإجراء',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('<?= site_url("ramadan/bulk_action_remote_request") ?>', { 
                    request_ids: selectedIds, 
                    action: action,
                    '<?= $this->security->get_csrf_token_name(); ?>': $('#csrf_token').val()
                }, function(res) {
                    $('#csrf_token').val(res.csrf_hash);
                    if(res.status === 'success') {
                        Swal.fire('تم التنفيذ!', res.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('خطأ!', res.message, 'error');
                    }
                }, 'json');
            }
        });
    }

    // --- Handle Single Form Submission ---
    $('#requestForm').submit(function(e) {
        e.preventDefault();
        
        var start = $('input[name="start_time"]').val();
        var end = $('input[name="end_time"]').val();
        
        var startMins = parseInt(start.split(':')[0]) * 60 + parseInt(start.split(':')[1]);
        var endMins = parseInt(end.split(':')[0]) * 60 + parseInt(end.split(':')[1]);
        
        // حساب ما بعد منتصف الليل
        if (endMins < startMins) {
            endMins += (24 * 60);
        }

        if ((endMins - startMins) > 120) {
            Swal.fire('خطأ', 'غير مسموح. أقصى مدة للعمل عن بعد هي ساعتين (120 دقيقة).', 'error');
            return;
        }

        var formData = $(this).serialize();

        $.post('<?= site_url("ramadan/submit_remote_request") ?>', formData, function(res) {
            $('#csrf_token').val(res.csrf_hash);
            if(res.status === 'success') {
                Swal.fire({icon: 'success', title: 'تم الحفظ!', text: res.message, showConfirmButton: false, timer: 1500}).then(() => location.reload());
            } else {
                Swal.fire({icon: 'error', title: 'خطأ!', text: res.message});
            }
        }, 'json').fail(function() {
            Swal.fire({icon: 'error', title: 'خطأ!', text: 'حدث خطأ في الاتصال بالخادم.'});
        });
    });

    // --- Handle Single Approve/Reject ---
    function processRequest(id, action) {
        var actionText = action === 'approve' ? 'اعتماد' : 'رفض';
        var actionColor = action === 'approve' ? '#198754' : '#d33';
        var warningText = action === 'approve' ? "سيتم تسجيل البصمات في النظام كعمل عن بعد. هل أنت متأكد؟" : "هل أنت متأكد من رفض الطلب؟ (إذا كان معتمداً مسبقاً، سيتم حذف البصمات من النظام)";

        Swal.fire({
            title: 'تأكيد الـ' + actionText,
            text: warningText,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: actionColor,
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'نعم، ' + actionText,
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('<?= site_url("ramadan/action_remote_request") ?>', { 
                    request_id: id, 
                    action: action,
                    '<?= $this->security->get_csrf_token_name(); ?>': $('#csrf_token').val()
                }, function(res) {
                    $('#csrf_token').val(res.csrf_hash);
                    if(res.status === 'success') {
                        Swal.fire('نجاح!', res.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('خطأ!', res.message, 'error');
                    }
                }, 'json');
            }
        });
    }
</script>
</body>
</html>