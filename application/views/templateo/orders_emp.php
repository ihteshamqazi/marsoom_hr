<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلباتي (الموظفين)</title>
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
        .badge { font-size: 0.75rem; }

        .my-requests-btn {
            background: linear-gradient(135deg, #0069d9 0%, #0056b3 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0, 105, 217, 0.3);
            transition: all 0.3s ease;
        }
        .my-requests-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 105, 217, 0.4);
        }
        .my-requests-btn.active {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }
        .my-requests-btn .badge {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
        }
    </style>
</head>
<body>

<div class="top-actions">
    <a href="javascript:history.back()"><i class="fas fa-arrow-right"></i><span>رجوع</span></a>
    <a href="<?php echo site_url('users1/main_emp'); ?>"><i class="fas fa-home"></i><span>الرئيسية</span></a>
</div>

<div class="main-container container-fluid">
    <div class="text-center">
        <h1 class="page-title">طلبات الموظفين</h1>
    </div>
    
    <div class="d-flex justify-content-center mb-4">
        <div class="d-flex flex-wrap gap-2 align-items-center">
            
            <?php if (isset($current_user_id) && $current_user_id == '2230'): ?>
                <button id="btnMyTeam" class="btn btn-primary shadow-sm">
                    <i class="fa-solid fa-users-viewfinder me-2"></i> عرض طلبات فريقي فقط (<?= htmlspecialchars($my_department ?? '') ?>)
                </button>
                <button id="btnResetTeam" class="btn btn-secondary shadow-sm" style="display:none;">
                    <i class="fa-solid fa-list me-2"></i> عرض الجميع
                </button>
            <?php endif; ?>
            
            <button id="btnMyRequests" class="my-requests-btn">
                <i class="fa-solid fa-user me-2"></i> طلباتي فقط
                <span class="badge ms-2" id="myRequestsCount">0</span>
            </button>
            <button id="btnResetMyRequests" class="btn btn-outline-success" style="display:none;">
                <i class="fa-solid fa-eye me-2"></i> عرض جميع الطلبات
            </button>

            <?php 
            $hr_ids_check = ['2230', '2515', '2774', '2784', '1835', '2901', '1859'];
            $curr_user = $this->session->userdata('username');
            if (in_array($curr_user, $hr_ids_check)): 
            ?>
                <button id="btnCeoPending" class="btn btn-warning shadow-sm text-dark fw-bold ms-2">
                    <i class="fa-solid fa-user-tie me-2"></i> بانتظار CEO (1001)
                </button>
                <button id="btnResetCeo" class="btn btn-outline-dark ms-2" style="display:none;">
                    <i class="fa-solid fa-rotate-left me-2"></i> عرض الكل
                </button>
            <?php endif; ?>
            </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card table-card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example" style="width:100%">
                            <thead>
                                <tr>
                                    <th>رقم الطلب</th>
                                    <th>مقدم الطلب</th>
                                    <th>اسم الموظف</th>
                                    <th>اسم الشركة</th>
                                    <th>رقم الموظف</th>
                                    <th>تاريخ الإجراء</th>
                                    <th>وقت الإنشاء</th>
                                    <th>نوع الطلب / التفاصيل</th>
                                    <th>حالة الصرف</th> <th>حالة الطلب</th>
                                    <th>بانتظار موافقة</th>
                                    <th>مرفق</th>
                                    <th>إجراءات</th>
                                </tr>
                                <tr class="filter-row">
                                    <th><input type="text" id="idFilter" placeholder="بحث..."></th>
                                    <th><input type="text" id="creatorFilter" placeholder="بحث..."></th>
                                    <th><input type="text" id="empNameFilter" placeholder="بحث..."></th>
                                    <th>
                                        <select id="companyFilter">
                                            <option value="">الكل</option>
                                            <option value="شركة مرسوم">شركة مرسوم</option>
                                            <option value="مكتب الدكتور">مكتب الدكتور</option>
                                        </select>
                                    </th>
                                    <th><input type="text" id="empIdFilter" placeholder="بحث..."></th>
                                    <th>
                                        <div class="d-flex flex-column gap-1">
                                            <select id="sheetFilter" class="form-select form-select-sm mb-1" style="font-size: 11px; font-weight: bold; color: #001f3f;">
                                                <option value="">-- كل الفترات --</option>
                                                <?php if (!empty($salary_sheets)): ?>
                                                    <?php foreach ($salary_sheets as $sheet): ?>
                                                        <option value="<?= $sheet['id'] ?>">
                                                            <?= $sheet['type'] ?> (<?= $sheet['start_date'] ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                            <input type="date" id="dateFilter" class="form-control form-control-sm">
                                        </div>
                                    </th>
                                    <th></th>
                                    
                                    <th>
                                       <select id="typeFilter" class="form-select form-select-sm" style="font-size: 11px; font-weight: bold;">
    <option value="">-- نوع الطلب --</option>
    <option value="مهمة عمل">مهمة عمل</option> 
    <option value="استقالة">استقالة</option>
    <option value="تصحيح بصمة">تصحيح بصمة</option>
    <option value="عمل إضافي">عمل إضافي</option>
    <option value="إجازة">إجازة</option>
    <option value="مصاريف مالية">مصاريف مالية</option>
    <option value="طلب عُهدة">طلب عُهدة</option>
    <option value="طلب خطاب">طلب خطاب</option>
    
    <option value="الاستئذان">الاستئذان</option>
</select>

                                        <select id="leaveTypeFilter" class="form-select form-select-sm mt-1" style="display:none; font-size: 11px; color: #d63384;">
                                            <option value="">-- نوع الإجازة --</option>
                                            <option value="annual">سنوية</option>
                                            <option value="sick">مرضية</option>
                                            <option value="maternity">أمومة</option>
                                            <option value="newborn">مولود</option>
                                            <option value="hajj">حج</option>
                                            <option value="marriage">زواج</option>
                                            <option value="death">وفاة</option>
                                            <option value="death_brother">وفاة أخ/أخت</option>
                                            <option value="unpaid">غير مدفوعة</option>
                                            <option value="exam">اختبارات</option>
                                        </select>
                                    </th>
                                    <th></th> <th>
                                        <select id="statusFilter" class="form-select form-select-sm" style="font-size: 11px; font-weight: bold; color: #001f3f;">
                                            <option value="">-- حالة الطلب (الكل) --</option>
                                            <option value="0">بانتظار الاعتماد الأول</option>
                                            <option value="1">قيد المعالجة (HR/مدير)</option>
                                            <option value="2">معتمد نهائي</option>
                                            <option value="3">مرفوض</option>
                                            <option value="-1">ملغي</option>
                                        </select>
                                    </th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
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
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">سجل إجراءات الطلب رقم: <span id="logOrderId"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="logContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
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
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>

<script>
$(document).ready(function () {
    const statusMap = {
        'pending': {label: 'بانتظار الإجراء', class: 'bg-warning-subtle text-warning-emphasis', icon: 'fa-hourglass-half'},
        'approved': {label: 'تمت الموافقة', class: 'bg-success-subtle text-success-emphasis', icon: 'fa-circle-check'},
        'rejected': {label: 'مرفوض', class: 'bg-danger-subtle text-danger-emphasis', icon: 'fa-circle-xmark'},
        'skipped': {label: 'تم تخطيه', class: 'bg-secondary-subtle text-secondary-emphasis', icon: 'fa-forward'},
    };
    
    const currentUserId = '<?php echo $this->session->userdata('username') ?? ''; ?>';
    
    <?php 
    $hr_ids = ['2230', '2515', '2774', '2784', '1835', '2901', '1859'];
    $is_hr_js = in_array($this->session->userdata('username'), $hr_ids) ? 'true' : 'false';
    ?>
    const isHrUser = <?php echo $is_hr_js; ?>;
    const canCancelRequests = <?php echo json_encode($can_cancel_requests ?? false); ?>;
    const logModal = new bootstrap.Modal(document.getElementById('logModal'));
    
    var isMyTeamFilterActive = false;
    var isMyRequestsFilterActive = false;
    var isCeoPendingFilterActive = false;

    var table = $('.dataTables-example').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?php echo site_url('users1/fetch_orders'); ?>",
            "type": "POST",
            "data": function (d) {
                d.filter_request_id = $('#idFilter').val();
                d.filter_creator = $('#creatorFilter').val();
                d.filter_name = $('#empNameFilter').val();
                d.filter_company = $('#companyFilter').val();
                d.filter_employee_id = $('#empIdFilter').val();
                d.filter_date = $('#dateFilter').val();
                d.filter_type = $('#typeFilter').val();
                d.filter_status = $('#statusFilter').val();
                d.filter_sheet_id = $('#sheetFilter').val();
                d.filter_my_department = isMyTeamFilterActive;
                d.filter_leave_type = $('#leaveTypeFilter').val();
                d.filter_my_requests = isMyRequestsFilterActive;
                d.filter_pending_ceo = isCeoPendingFilterActive;
                d['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
            },
            "dataSrc": function (json) {
                if (json && json.myRequestsCount !== undefined) {
                    $('#myRequestsCount').text(json.myRequestsCount);
                }
                return json.data || json;
            }
        },
        "columns": [
            { "data": "id" }, 
            { "data": "creator_name" }, 
            { "data": "emp_name" },
            { "data": "company_name" },
            { "data": "emp_id" }, 
            { "data": "event_date"}, 
            { "data": "time" },
            { 
                "data": "order_name",
                "render": function(data, type, row) {
                    if (row.type == 9) {
                        var html = '<div class="fw-bold text-primary">' + data + '</div>';
                        if (row.mission_type) {
                            html += '<div class="badge bg-secondary-subtle text-secondary-emphasis mt-1">' + row.mission_type + '</div>';
                        }
                        return html;
                    }
                    return data;
                }
            }, 
            { "data": "payment_status" }, // NEW COLUMN: Payment Status
            { "data": "status" }, 
            { "data": "responsible_employee_name" },
            { "data": "file" }, 
            { "data": "id", "orderable": false }
        ],
        "columnDefs": [
            {
                "targets": 9, // Status column (Shifted from 8 to 9)
                "render": function(data, type, row) {
                    let responsiblePerson = row.responsible_employee_name ? row.responsible_employee_name : 'المسؤول';
                    let html = '';

                    if (data == '0' || data == '1') {
                        let badgeClass = (data == '0') ? 'bg-warning-subtle text-warning-emphasis' : 'bg-info-subtle text-info-emphasis';
                        let icon = (data == '0') ? 'fa-hourglass-half' : 'fa-user-tie';
                        
                        html = `<span class="badge rounded-pill ${badgeClass}">
                                    <i class="fa-solid ${icon} me-1"></i> بانتظار: ${responsiblePerson}
                                </span>`;
                        
                    } else if (data == '2') {
                        html = `<span class="badge rounded-pill bg-success-subtle text-success-emphasis">
                                    <i class="fa-solid fa-circle-check me-1"></i> معتمد
                                </span>`;
                    } else if (data == '3') {
                        html = `<span class="badge rounded-pill bg-danger-subtle text-danger-emphasis">
                                    <i class="fa-solid fa-circle-xmark me-1"></i> مرفوض
                                </span>`;
                    } else {
                        html = `<span class="badge rounded-pill bg-dark-subtle text-dark-emphasis">
                                    <i class="fa-solid fa-ban me-1"></i> ملغي
                                </span>`;
                    }
                    return html;
                }
            },
            {
                "targets": 11, // File (Shifted from 10 to 11)
                "render": function(data, type, row) {
                    return data ? `<a href="<?php echo base_url(); ?>${data}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-paperclip"></i></a>` : '—';
                }
            },
            {
                "targets": 12, // Actions (Shifted from 11 to 12)
                "render": function(data, type, row) {
                    let orderType = row.type;
                    let buttons = `
                        <div class="btn-group">
                            <a href="<?php echo site_url('users1/view_request/'); ?>${data}" class="btn btn-sm btn-outline-primary" title="عرض التفاصيل"><i class="fa-solid fa-eye"></i></a>
                            <button type="button" class="btn btn-sm btn-outline-info view-log-btn" data-order-id="${data}" data-order-type="${orderType}" title="سجل الإجراءات"><i class="fas fa-history"></i></button>`;

                    if (isHrUser || row.status == '0') {
                         buttons += `<a href="<?php echo site_url('users1/edit_request/'); ?>${data}" class="btn btn-sm btn-outline-warning" title="تعديل"><i class="fas fa-edit"></i></a>`;
                    }
                    buttons += `</div>`;
                    
                    const isPending = ['0', '1'].includes(row.status);
                    if (canCancelRequests && isPending) {
                        buttons += ` <button type="button" class="btn btn-sm btn-success ms-1" onclick="hrOverride(${data})" title="موافقة مباشرة (تجاوز)"><i class="fas fa-check-double"></i></button>`;
                    }
                    
                    const cancellable = ['0', '1', '2', '3', '-1','11'].includes(row.status);
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
                    {
                        text:'<i class="fa fa-file-excel"></i> إكسل',
                        action: function ( e, dt, node, config ) {
                            var url = '<?php echo site_url('users1/export_all_orders'); ?>';
                            var params = {
                                id: $('#idFilter').val(),
                                creator: $('#creatorFilter').val(),
                                emp_name: $('#empNameFilter').val(),
                                company_name: $('#companyFilter').val(),
                                emp_id: $('#empIdFilter').val(),
                                date: $('#dateFilter').val(),
                                type: $('#typeFilter').val(),
                                status: $('#statusFilter').val(),
                                my_requests: isMyRequestsFilterActive,
                                pending_ceo: isCeoPendingFilterActive
                            };
                            window.location.href = url + '?' + $.param(params);
                        }
                    },
                    { extend:'print', text:'<i class="fa fa-print"></i> طباعة' }, 
                    { text:'<i class="fa fa-plus-circle"></i> طلب جديد', className:'btn btn-success', action: function() { window.location.href = "<?php echo site_url('users1/add_new_order'); ?>"; } } 
                ] 
            } 
        },
        language: { url:'https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json' },
    });

    // --- EVENT LISTENERS ---

    $('#sheetFilter').on('change', function() { table.draw(); });
    
    $('#btnMyTeam').on('click', function() { 
        isMyTeamFilterActive = true; 
        isMyRequestsFilterActive = false;
        isCeoPendingFilterActive = false; 

        $('#btnMyRequests').removeClass('active');
        $('#btnResetMyRequests').hide();
        $('#btnMyRequests').show();
        
        $('#btnCeoPending').show();
        $('#btnResetCeo').hide();

        $(this).hide(); 
        $('#btnResetTeam').show(); 
        table.draw(); 
    });

    $('#btnMyRequests').on('click', function() { 
        isMyRequestsFilterActive = true;
        isMyTeamFilterActive = false;
        isCeoPendingFilterActive = false; 

        $(this).addClass('active');
        $(this).hide();
        $('#btnResetMyRequests').show();
        $('#btnMyTeam').hide();
        $('#btnResetTeam').hide();

        $('#btnCeoPending').show();
        $('#btnResetCeo').hide();

        table.draw();
    });
    
    $('#btnResetMyRequests').on('click', function() { 
        isMyRequestsFilterActive = false;
        $('#btnMyRequests').removeClass('active');
        $(this).hide();
        $('#btnMyRequests').show();
        <?php if (isset($current_user_id) && $current_user_id == '2230'): ?>
            $('#btnMyTeam').show();
        <?php endif; ?>
        table.draw();
    });

    $('#btnCeoPending').on('click', function() { 
        isCeoPendingFilterActive = true;
        isMyRequestsFilterActive = false;
        isMyTeamFilterActive = false;
        
        $('#btnMyRequests').removeClass('active');
        $('#btnMyRequests').show();
        $('#btnResetMyRequests').hide();
        
        <?php if (isset($current_user_id) && $current_user_id == '2230'): ?>
            $('#btnMyTeam').show();
            $('#btnResetTeam').hide();
        <?php endif; ?>

        $(this).hide();
        $('#btnResetCeo').show();
        table.draw();
    });

    $('#btnResetCeo').on('click', function() { 
        isCeoPendingFilterActive = false;
        $(this).hide();
        $('#btnCeoPending').show();
        table.draw();
    });
    
    $('.filter-row input, .filter-row select').on('keyup change', function() { 
        if(this.id !== 'sheetFilter' && this.id !== 'typeFilter' && this.id !== 'leaveTypeFilter') { 
            table.draw(); 
        } 
    });
    
    $('#typeFilter').on('change', function() { 
        var selected = $(this).val(); 
        (selected === 'إجازة') ? $('#leaveTypeFilter').show() : $('#leaveTypeFilter').hide().val('');
        table.draw();
    });
    
    $('#leaveTypeFilter').on('change', function() { table.draw(); });

    $('.dataTables-example tbody').on('click', '.view-log-btn', function() {
        var orderId = $(this).data('order-id');
        var orderType = $(this).data('order-type');
        $('#logOrderId').text(orderId);
        $('#logContent').html('<div class="text-center"><div class="spinner-border text-primary" role="status"></div></div>');
        logModal.show();
        $.ajax({
            url: "<?php echo site_url('users1/get_order_log'); ?>",
            type: 'POST',
            data: { order_id: orderId, order_type: orderType, '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' && response.log.length > 0) {
                    let logHtml = '<table class="table table-sm table-bordered"><thead><tr><th>المستوى</th><th>اسم المسؤول</th><th>الإجراء</th><th>التاريخ</th><th>سبب الرفض</th></tr></thead><tbody>';
                    response.log.forEach(function(item) {
                        let meta = statusMap[item.status] || {label: item.status, class: 'bg-secondary-subtle', icon: 'fa-question'};
                        let statusBadge = `<span class="badge ${meta.class}"><i class="fa-solid ${meta.icon} me-1"></i>${meta.label}</span>`;
                        logHtml += `<tr><td>${item.approval_level}</td><td>${item.approver_name || item.approver_id}</td><td>${statusBadge}</td><td>${item.action_date || '—'}</td><td>${item.rejection_reason || '—'}</td></tr>`;
                    });
                    logHtml += '</tbody></table>';
                    $('#logContent').html(logHtml);
                } else {
                    $('#logContent').html('<p class="text-muted text-center">لا يوجد سجل إجراءات.</p>');
                }
            },
            error: function() { $('#logContent').html('<p class="text-danger text-center">فشل التحميل.</p>'); }
        });
    });

    setTimeout(() => {
        table.ajax.reload();
    }, 500);
});

function hrOverride(orderId) {
    if (!confirm('هل أنت متأكد من الموافقة المباشرة؟')) return;
    const csrfToken = $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val() || '<?php echo $this->security->get_csrf_hash(); ?>';
    $.ajax({
        url: '<?php echo base_url("users1/hr_override_approve"); ?>',
        type: 'POST',
        data: { id: orderId, '<?php echo $this->security->get_csrf_token_name(); ?>': csrfToken },
        dataType: 'json',
        success: function(response) {
             $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val(response.csrfHash);
            if (response.ok) {
                alert('تمت الموافقة بنجاح.');
                $('.dataTables-example').DataTable().ajax.reload(null, false); 
            } else {
                alert('فشل الإجراء: ' + (response.error || 'حدث خطأ.'));
            }
        },
        error: function(xhr) { alert('خطأ في الاتصال: ' + xhr.status); }
    });
}
</script>

</body>
</html>