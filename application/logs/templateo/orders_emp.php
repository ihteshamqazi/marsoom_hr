<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلبات الموظفين</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.css">
    <style>
        :root{ --marsom-blue:#001f3f;--marsom-orange:#FF8C00; }
        body{font-family:'Tajawal',sans-serif;background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);background-size:400% 400%;animation:grad 20s ease infinite;color:#343a40;}
        @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        .main-container{padding:30px 15px;position:relative;z-index:1}
        .page-title{font-family:'El Messiri',sans-serif;font-weight:700;font-size:2.8rem;color:#fff;margin-bottom:32px;text-align:center;text-shadow:0 3px 6px rgba(0,0,0,.4)}
        .table-card{background:rgba(255,255,255,.92);backdrop-filter:blur(8px);border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,.15);padding:25px}
        .dataTables-example thead th{background-color:#001f3f !important;color:#fff;text-align:center;vertical-align:middle;}
        .dataTables-example tbody td{text-align:center;vertical-align:middle;font-size:14px;white-space:nowrap}
        .top-actions{position:fixed;top:12px;right:12px;display:flex;gap:10px;z-index:5}
        .top-actions a{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);color:#fff;text-decoration:none;border-radius:10px;padding:8px 14px;display:inline-flex;align-items:center;gap:8px;}
        thead .filter-row th { padding: 4px !important; background-color: #f8f9fa; }
        thead .filter-row select, thead .filter-row input { width: 100%; padding: 0.25rem 0.5rem; border: 1px solid #ccc; border-radius: 4px; font-size: 13px; }
    </style>
</head>
<body>

<div class="top-actions">
    <a href="javascript:history.back()"><i class="fas fa-arrow-right"></i><span>رجوع</span></a>
    <a href="<?php echo site_url('users1/main_emp'); ?>"><i class="fas fa-home"></i><span>الرئيسية</span></a>
</div>

<div class="main-container container-fluid">
    <div class="text-center"><h1 class="page-title">طلبات الموظفين</h1></div>
    <div class="row">
        <div class="col-12">
            <div class="card table-card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example" style="width:100%">
                            <thead>
                                <tr>
                                    <th>رقم الطلب</th><th>مقدم الطلب</th><th>اسم الموظف</th><th>رقم الموظف</th><th>تاريخ الطلب</th><th>وقت الطلب</th>
                                    <th>نوع الطلب</th><th>حالة الطلب</th><th>بانتظار موافقة</th><th>مرفق</th><th>إجراءات</th> 
                                </tr>
                                <tr class="filter-row">
                                    <th><input type="text" id="idFilter" placeholder="بحث..."></th>
                                    <th><input type="text" id="creatorFilter" placeholder="بحث..."></th>
                                    <th><input type="text" id="empNameFilter" placeholder="بحث..."></th>
                                    <th><input type="text" id="empIdFilter" placeholder="بحث..."></th>
                                    <th><input type="date" id="dateFilter"></th>
                                    <th></th>
                                    <th><select id="typeFilter"><option value="">الكل</option><option value="استقالة">استقالة</option><option value="تصحيح بصمة">تصحيح بصمة</option><option value="عمل إضافي">عمل إضافي</option><option value="إجازة">إجازة</option></select></th>
                                    <th><select id="statusFilter"><option value="">الكل</option><option value="0">بانتظار موافقة المسؤول</option><option value="1">بانتظار موافقة الموارد البشرية</option><option value="2">تمت الموافقة</option><option value="3">مرفوض</option><option value="-2">ملغي</option></select></th>
                                    <th></th><th></th><th></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="logModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"><div class="modal-header"><h5 class="modal-title">سجل إجراءات الطلب رقم: <span id="logOrderId"></span></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><div id="logContent"></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button></div></div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.bootstrap5.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>

<script>
$(document).ready(function () {
    const statusMap = { 'pending': {label: 'بانتظار الإجراء', class: 'bg-warning-subtle text-warning-emphasis', icon: 'fa-hourglass-half'}, 'approved': {label: 'تمت الموافقة', class: 'bg-success-subtle text-success-emphasis', icon: 'fa-circle-check'}, 'rejected': {label: 'مرفوض', class: 'bg-danger-subtle text-danger-emphasis', icon: 'fa-circle-xmark'}, 'skipped': {label: 'تم تخطيه', class: 'bg-secondary-subtle text-secondary-emphasis', icon: 'fa-forward'}, };
    const orderStatusMap = { '0': {label: 'بانتظار موافقة المسؤول', class: 'bg-warning-subtle text-warning-emphasis', icon: 'fa-hourglass-half'}, '1': {label: 'بانتظار موافقة الموارد البشرية', class: 'bg-info-subtle text-info-emphasis', icon: 'fa-user-tie'}, '2': {label: 'تمت الموافقة', class: 'bg-success-subtle text-success-emphasis', icon: 'fa-circle-check'}, '3': {label: 'مرفوض', class: 'bg-danger-subtle text-danger-emphasis', icon: 'fa-circle-xmark'}, '-1': {label: 'مرفوض', class: 'bg-danger-subtle text-danger-emphasis', icon: 'fa-circle-xmark'}, '-2': {label: 'ملغي', class: 'bg-dark-subtle text-dark-emphasis', icon: 'fa-ban'} };
    const canCancelRequests = <?php echo json_encode($can_cancel_requests ?? false); ?>;
    const logModal = new bootstrap.Modal(document.getElementById('logModal'));

    var table = $('.dataTables-example').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?php echo site_url('users1/fetch_orders'); ?>",
            "type": "POST",
            // ✅ **FIX:** Send custom filter data to the server
            "data": function ( d ) {
                d.filter_employee_id = $('#empIdFilter').val();
                d.filter_name = $('#empNameFilter').val();
                d.filter_creator = $('#creatorFilter').val();
                d.filter_request_id = $('#idFilter').val();
                d.filter_date = $('#dateFilter').val();
                d.filter_type = $('#typeFilter').val();
                d.filter_status = $('#statusFilter').val();
            }
        },
        "columns": [
            { "data": "id" }, { "data": "creator_name" }, { "data": "emp_name" },
            { "data": "emp_id" }, { "data": "date" }, { "data": "time" },
            { "data": "order_name" }, { "data": "status" }, { "data": "responsible_employee_name" },
            { "data": "file" }, { "data": "id", "orderable": false }
        ],
        "columnDefs": [
            {
                "targets": 7, // Status column
                "render": function(data, type, row) {
                    let meta = orderStatusMap[data] || {label: 'غير معروف', class: 'bg-secondary-subtle', icon: 'fa-question'};
                    return `<span class="badge rounded-pill ${meta.class}"><i class="fa-solid ${meta.icon} me-1"></i>${meta.label}</span>`;
                }
            },
            {
                "targets": 9, // Attachment column
                "orderable": false,
                "render": function(data, type, row) {
                    return data ? `<a href="<?php echo base_url(); ?>${data}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-paperclip"></i></a>` : '—';
                }
            },
            {
                "targets": 10, // Actions column
                "orderable": false,
                "render": function(data, type, row) {
                    let buttons = `
                        <div class="btn-group">
                            <a href="<?php echo site_url('users1/view_request/'); ?>${data}" class="btn btn-sm btn-outline-primary" title="عرض التفاصيل"><i class="fa-solid fa-eye"></i></a>
                            <button type="button" class="btn btn-sm btn-outline-info view-log-btn" data-order-id="${data}" title="سجل الإجراءات"><i class="fas fa-history"></i></button>
                        </div>`;
                    
                    const cancellable = ['0', '1', '3', '-1','2'].includes(row.status);
                    if (canCancelRequests && cancellable) {
                        buttons += ` <a href="<?php echo site_url('users1/cancel_request/'); ?>${data}" class="btn btn-sm btn-outline-danger" onclick="return confirm('هل أنت متأكد من إلغاء هذا الطلب؟');" title="إلغاء الطلب"><i class="fas fa-trash-alt"></i></a>`;
                    }
                    return buttons;
                }
            }
        ],
        orderCellsTop: true,
        layout: {
            topStart: {
                buttons: [
                    { extend:'excel', text:'<i class="fa fa-file-excel"></i> إكسل' },
                    { extend:'print', text:'<i class="fa fa-print"></i> طباعة' },
                    { text:'<i class="fa fa-plus-circle"></i> طلب جديد', className:'btn btn-success', action: function() { window.location.href = "<?php echo site_url('users1/add_new_order'); ?>"; } }
                ]
            }
        },
        language: { url:'https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json' },
    });

    // --- Filter Logic ---
    $('#typeFilter').on('change', function(){ table.column(6).search(this.value).draw(); });
    $('#statusFilter').on('change', function(){ table.column(7).search(this.value).draw(); });

    // --- NEW: Event Listener for Log Button ---
    $(document).on('click', '.view-log-btn', function() {
        var orderId = $(this).data('order-id');
        $('#logOrderId').text(orderId);
        $('#logContent').html('<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
        logModal.show();

        $.ajax({
            url: `<?php echo site_url('users1/get_order_log/'); ?>${orderId}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' && response.log.length > 0) {
                    let logHtml = '<table class="table table-sm table-bordered"><thead><tr><th>المستوى</th><th>اسم المسؤول</th><th>الإجراء</th><th>التاريخ والوقت</th><th>سبب الرفض</th></tr></thead><tbody>';
                    response.log.forEach(function(item) {
                        let meta = statusMap[item.status] || {label: item.status, class: 'bg-secondary-subtle', icon: 'fa-question'};
                        let statusBadge = `<span class="badge ${meta.class}"><i class="fa-solid ${meta.icon} me-1"></i>${meta.label}</span>`;
                        logHtml += `
                            <tr>
                                <td>${item.approval_level}</td>
                                <td>${item.approver_name || item.approver_id}</td>
                                <td>${statusBadge}</td>
                                <td>${item.action_date || '—'}</td>
                                <td>${item.rejection_reason || '—'}</td>
                            </tr>
                        `;
                    });
                    logHtml += '</tbody></table>';
                    $('#logContent').html(logHtml);
                } else {
                    $('#logContent').html('<p class="text-muted text-center">لا يوجد سجل إجراءات لهذا الطلب.</p>');
                }
            },
            error: function() {
                $('#logContent').html('<p class="text-danger text-center">فشل في تحميل سجل الإجراءات.</p>');
            }
        });
    });
});


// Loading Screen Logic
window.addEventListener('load', function(){
    const loading = document.getElementById('loading-screen');
    const main = document.querySelector('.main-container');
    if (loading && main) {
        loading.style.opacity='0';
        setTimeout(function(){ 
            loading.style.display='none'; 
            document.body.style.overflow='auto'; 
            main.style.visibility='visible'; 
            main.style.opacity='1'; 
        }, 400);
    }
});
</script>

</body>
</html>