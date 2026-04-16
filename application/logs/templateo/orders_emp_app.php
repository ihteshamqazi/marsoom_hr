<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلبات الموظفين</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.css">

    <style>
        :root{--marsom-blue:#001f3f;--marsom-orange:#FF8C00;--text-light:#fff;--text-dark:#343a40;}
        body{font-family:'Tajawal',sans-serif;overflow:hidden;background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);background-size:400% 400%;animation:grad 20s ease infinite;color:var(--text-dark);position:relative}
        @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        .particles{position:fixed;inset:0;overflow:hidden;z-index:-1;pointer-events: none;}
        .particle{position:absolute;background:rgba(255,140,0,.1);clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);animation:float 25s infinite ease-in-out;opacity:0;filter:blur(2px)}
        .particle:nth-child(even){background:rgba(0,31,63,.1)}
        .particle:nth-child(1){width:40px;height:40px;left:10%;top:20%;animation-duration:18s}.particle:nth-child(2){width:70px;height:70px;left:25%;top:50%;animation-duration:22s;animation-delay:2s}.particle:nth-child(3){width:55px;height:55px;left:40%;top:10%;animation-duration:25s;animation-delay:5s}.particle:nth-child(4){width:80px;height:80px;left:60%;top:70%;animation-duration:20s;animation-delay:8s}.particle:nth-child(5){width:60px;height:60px;left:80%;top:30%;animation-duration:23s;animation-delay:10s}.particle:nth-child(6){width:45px;height:45px;left:5%;top:85%;animation-duration:19s;animation-delay:3s}.particle:nth-child(7){width:90px;height:90px;left:70%;top:5%;animation-duration:28s;animation-delay:6s}.particle:nth-child(8){width:35px;height:35px;left:90%;top:40%;animation-duration:17s;animation-delay:12s}.particle:nth-child(9){width:75px;height:75px;left:20%;top:75%;animation-duration:21s;animation-delay:1s}.particle:nth-child(10){width:65px;height:65px;left:50%;top:90%;animation-duration:24s;animation-delay:4s}
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
        .top-actions{position:fixed;top:12px;right:12px;display:flex;gap:10px;z-index:5}
        .top-actions a{background:rgba(255,255,255,.12);border:1px solid var(--glass-border);color:#fff;text-decoration:none;border-radius:10px;padding:8px 14px;display:inline-flex;align-items:center;gap:8px;transition:.25s}
        .top-actions a:hover{background:rgba(255,255,255,.2);color:var(--marsom-orange)}
        /* NEW: Styles for filter buttons */
        .status-filters .btn { font-weight: 500; }
        .status-filters .btn.active { background-color: var(--marsom-blue); color: white; }
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
    <a href="<?php echo site_url('users1/main_emp'); ?>"><i class="fas fa-home"></i><span>الرئيسية</span></a>
</div>

<div class="main-container container-fluid">
    <div class="text-center">
        <h1 class="page-title">طلبات الموظفين</h1>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card table-card">
                <div class="card-body">
                    
                    <div class="d-flex justify-content-center mb-4">
                        <div class="btn-group status-filters" role="group">
                            <button type="button" class="btn btn-outline-secondary active" data-status-filter="">الكل</button>
                            <button type="button" class="btn btn-outline-secondary" data-status-filter="بانتظار">بانتظار</button>
                            <button type="button" class="btn btn-outline-secondary" data-status-filter="الموافقة">تمت الموافقة</button>
                            <button type="button" class="btn btn-outline-secondary" data-status-filter="مرفوض">مرفوض</button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example" style="width:100%">
                            <thead>
    <tr>
        <th>رقم الطلب</th>
        <th>اسم الموظف</th> <th>اجراء</th>
        <th>حالة الطلب</th>
        <th>تاريخ الطلب</th>
        <th>وقت الطلب</th>
        <th>نوع الطلب</th>
        <th>الرصيد المتبقي</th>
        <th>مرفق</th>
    </tr>
</thead>
                            <tbody>
                                <?php foreach($get_salary_vacations as $employee) : ?>
                                    <tr id="request-row-<?= (int)$employee['id']; ?>">
                                       <td>
    <a href="<?php echo site_url('users1/view_request/' . $employee['id']); ?>" 
       class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
        <i class="fa-solid fa-eye"></i>
        <span><?php echo $employee['id']; ?></span>
    </a>
</td>                 
 <td>
                <?php echo htmlspecialchars($employee['requester_name'] ?? ($employee['emp_name'] ?? 'غير معروف')); ?>
            </td> 
            <td class="text-nowrap">
                                            <?php
                                                // UPDATED: Check if status is pending (0 or 1) to decide whether to show buttons
                                                $is_pending = in_array((int)($employee['status'] ?? -1), [0, 1]);
                                            ?>
                                            <?php if ($is_pending): ?>
                                                <div class="btn-group" role="group" aria-label="actions">
                                                    <button type="button" class="btn btn-sm btn-success js-approve" data-id="<?= (int)$employee['id']; ?>" title="موافقة"><i class="fa-solid fa-check"></i></button>
                                                    <button type="button" class="btn btn-sm btn-danger js-reject" data-id="<?= (int)$employee['id']; ?>" title="رفض"><i class="fa-solid fa-xmark"></i></button>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">تم اتخاذ إجراء</span>
                                            <?php endif; ?>
                                        </td>
                                        <td id="status-badge-<?= (int)$employee['id']; ?>">
                                            <?php
                                                $st = (int)$employee['status'];
                                                if ($st === 2) { echo '<span class="badge rounded-pill bg-success-subtle text-success-emphasis border border-success-subtle"><i class="fa-solid fa-circle-check ms-1"></i> تمت الموافقة</span>'; } 
                                                elseif ($st === 3) { echo '<span class="badge rounded-pill bg-danger-subtle text-danger-emphasis border border-danger-subtle"><i class="fa-solid fa-circle-xmark ms-1"></i> مرفوض</span>'; } 
                                                elseif ($st === 1) { echo '<span class="badge rounded-pill bg-info-subtle text-info-emphasis border border-info-subtle"><i class="fa-solid fa-user-tie ms-1"></i> بانتظار موافقة الموارد البشرية</span>'; } 
                                                else { echo '<span class="badge rounded-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle"><i class="fa-solid fa-hourglass-half ms-1"></i> بانتظار المعالجة</span>'; }
                                            ?>
                                            <?php if (!empty($employee['reason_for_rejection'])): ?>
                                                <div id="reject-reason-<?= (int)$employee['id']; ?>" class="small text-muted mt-1">سبب الرفض: <?= htmlspecialchars($employee['reason_for_rejection'], ENT_QUOTES, 'UTF-8'); ?></div>
                                            <?php else: ?>
                                                <div id="reject-reason-<?= (int)$employee['id']; ?>" class="small text-muted mt-1 d-none"></div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $employee['date']; ?></td>
                                        <td><?php echo $employee['time']; ?></td>
                                        <td><?php echo $employee['order_name']; ?></td>
                                        <td>
                                            <?php if (isset($employee['order_name']) && $employee['order_name'] === 'إجازة'): ?>
                                                <span class="badge bg-dark"><?php echo isset($employee['remaining_balance']) ? (int)$employee['remaining_balance'] . ' يوم' : 'غير محدد'; ?></span>
                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                                $file = isset($employee['file']) ? trim($employee['file']) : '';
                                                if ($file !== ''):
                                                $url = base_url($file);
                                                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                                $name = basename($file);
                                            ?>
                                            <a href="<?= htmlspecialchars($url, ENT_QUOTES) ?>" class="btn btn-sm btn-outline-primary file-preview" title="عرض المرفق" data-url="<?= htmlspecialchars($url, ENT_QUOTES) ?>" data-ext="<?= htmlspecialchars($ext, ENT_QUOTES) ?>" data-name="<?= htmlspecialchars($name, ENT_QUOTES) ?>"><i class="fa-solid fa-paperclip"></i></a>
                                            <?php else: ?>
                                            —
                                            <?php endif; ?>
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

<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true" dir="rtl">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fa-solid fa-circle-xmark ms-1"></i> سبب الرفض</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">رجاءً اكتب سبب الرفض (3 أحرف على الأقل):</div>
                <textarea id="rejectReason" class="form-control" rows="3" placeholder="مثال: البيانات غير مكتملة"></textarea>
                <div class="invalid-feedback d-block d-none" id="rejectReasonError">اكتب سببًا لا يقل عن 3 أحرف.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-danger" id="confirmRejectBtn">تأكيد الرفض</button>
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
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.colVis.min.js"></script>

<script>
$(document).ready(function() {
    
    // --- Initialize DataTables ---
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
                    { extend:'colvis',text:'<i class="fa fa-eye"></i> إظهار/إخفاء الأعمدة' },
                    { text:'<i class="fa fa-plus-circle"></i> طلب جديد', className:'btn btn-success', action:function(){ window.location.href = "<?php echo site_url('users1/add_new_order'); ?>"; } }
                ]
            }
        },
        language: { url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json' }
    });

    // --- NEW: Status Filter Logic ---
    $('.status-filters .btn').on('click', function() {
        $('.status-filters .btn').removeClass('active');
        $(this).addClass('active');
        var filterValue = $(this).data('status-filter');
        // Column index 2 is "حالة الطلب"
        dt.column(3).search(filterValue).draw();
    });

    // --- Re-integrated AJAX Approval/Rejection Logic ---
    var UPDATE_URL = '<?= site_url("users1/update_order_status"); ?>';
    var CSRF_NAME = '<?= $this->security->get_csrf_token_name(); ?>';
    var CSRF_HASH = '<?= $this->security->get_csrf_hash(); ?>';

    function sendUpdate(id, status, reason, $btn) {
        var payload = { id: id, status: status };
        if (typeof reason === 'string') payload.reason = reason;
        payload[CSRF_NAME] = CSRF_HASH; // Always send CSRF token

        return $.ajax({
            url: UPDATE_URL,
            type: 'POST',
            dataType: 'json',
            data: payload,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        }).done(function(res) {
            if (res && res.csrfHash) CSRF_HASH = res.csrfHash; // Update CSRF token for next request
            if (res && res.ok) {
                var statusCell = $('#status-badge-' + id);
                var reasonDiv = $('#reject-reason-' + id);
                if (status === 3) { // Rejected
                    statusCell.html('<span class="badge rounded-pill bg-danger-subtle text-danger-emphasis border border-danger-subtle"><i class="fa-solid fa-circle-xmark ms-1"></i> مرفوض</span>');
                    reasonDiv.html('سبب الرفض: ' + $('<div/>').text(reason).html()).removeClass('d-none');
                } else { // Approved
                    statusCell.html('<span class="badge rounded-pill bg-success-subtle text-success-emphasis border border-success-subtle"><i class="fa-solid fa-circle-check ms-1"></i> تمت الموافقة</span>');
                    reasonDiv.addClass('d-none').html('');
                }
                $btn.closest('.btn-group').html('<span class="text-muted">تم اتخاذ إجراء</span>');
            } else {
                alert((res && res.error) ? res.error : 'تعذر تنفيذ العملية');
            }
        }).fail(function(xhr) {
            var msg = 'خطأ في الاتصال بالسيرفر';
            if (xhr.status === 404) msg = 'المسار غير موجود (404)';
            if (xhr.status === 403) msg = 'CSRF Token Error. Please refresh the page.';
            alert(msg);
        });
    }

    $(document).on('click', '.js-approve', function(e) {
        e.preventDefault();
        var $btn = $(this), id = parseInt($btn.data('id'), 10);
        if (!id || !confirm('هل أنت متأكد من الموافقة على هذا الطلب؟')) return;
        $btn.prop('disabled', true);
        sendUpdate(id, 2, null, $btn).always(function() { $btn.prop('disabled', false); });
    });

    var rejectId = null;
    var $rejectBtn = null;
    const rejectionModal = new bootstrap.Modal(document.getElementById('rejectModal'));
    $(document).on('click', '.js-reject', function(e) {
        e.preventDefault();
        $rejectBtn = $(this);
        rejectId = parseInt($rejectBtn.data('id'), 10);
        if (!rejectId) return;
        $('#rejectReason').val('');
        $('#rejectReasonError').addClass('d-none');
        rejectionModal.show();
    });

    $('#confirmRejectBtn').on('click', function() {
        if (!rejectId) return;
        var reason = ($('#rejectReason').val() || '').trim();
        if (reason.length < 3) {
            $('#rejectReasonError').removeClass('d-none');
            return;
        }
        var $confirmBtn = $(this);
        $confirmBtn.prop('disabled', true);
        sendUpdate(rejectId, 3, reason, $rejectBtn).done(function(res) {
            if (res && res.ok) {
                rejectionModal.hide();
            }
        }).always(function() {
            $confirmBtn.prop('disabled', false);
            rejectId = null;
            $rejectBtn = null;
        });
    });

    // --- Loading Screen Logic ---
    const loading = document.getElementById('loading-screen');
    const main = document.querySelector('.main-container');
    loading.style.opacity = '0';
    setTimeout(function() {
        loading.style.display = 'none';
        document.body.style.overflow = 'auto';
        main.style.visibility = 'visible';
        main.style.opacity = '1';
    }, 400);
});
</script>

</body>
</html>