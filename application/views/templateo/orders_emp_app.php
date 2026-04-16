<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلبات الموظفين (موافقات)</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.css">

    <style>
        :root{--marsom-blue:#001f3f;--marsom-orange:#FF8C00;--text-light:#fff;--text-dark:#343a40;}
        body{font-family:'Tajawal',sans-serif;overflow-y:auto;background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);background-size:400% 400%;animation:grad 20s ease infinite;color:var(--text-dark);position:relative;min-height:100vh;}
        @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        .particles{position:fixed;inset:0;overflow:hidden;z-index:-1; pointer-events: none;}
        .particle{position:absolute;background:rgba(255,140,0,.1);clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);animation:float 25s infinite ease-in-out;opacity:0;filter:blur(2px)}
        .particle:nth-child(even){background:rgba(0,31,63,.1)}
        .particle:nth-child(1){width:40px;height:40px;left:10%;top:20%;animation-duration:18s}.particle:nth-child(2){width:70px;height:70px;left:25%;top:50%;animation-duration:22s;animation-delay:2s}.particle:nth-child(3){width:55px;height:55px;left:40%;top:10%;animation-duration:25s;animation-delay:5s}.particle:nth-child(4){width:80px;height:80px;left:60%;top:70%;animation-duration:20s;animation-delay:8s}.particle:nth-child(5){width:60px;height:60px;left:80%;top:30%;animation-duration:23s;animation-delay:10s}
        @keyframes float{0%{transform:translateY(0) translateX(0) rotate(0);opacity:0}20%{opacity:1}80%{opacity:1}100%{transform:translateY(-100vh) translateX(50px) rotate(360deg);opacity:0}}
        
        .main-container{padding:30px 15px;visibility:hidden;opacity:0;transition:opacity .5s;position:relative;z-index:1}
        .page-title{font-family:'El Messiri',sans-serif;font-weight:700;font-size:2.8rem;color:var(--text-light);margin-bottom:32px;text-align:center;position:relative;display:inline-block;padding-bottom:10px;text-shadow:0 3px 6px rgba(0,0,0,.4)}
        .page-title::after{content:'';position:absolute;width:100px;height:4px;background:linear-gradient(90deg,var(--marsom-blue),var(--marsom-orange));bottom:0;left:50%;transform:translateX(-50%);border-radius:2px}
        .table-card{background:rgba(255,255,255,.95);backdrop-filter:blur(10px);border-radius:15px;box-shadow:0 10px 40px rgba(0,0,0,.2);padding:25px}
        
        .dataTables-example thead th{background-color:#001f3f !important;color:#fff;text-align:center;vertical-align:middle;border-bottom:2px solid #00152b}
        .dataTables-example tbody td{text-align:center;vertical-align:middle;font-size:14px;white-space:nowrap}
        .dataTables-example tbody tr:hover{background-color:rgba(0,31,63,.08)}
        
        .top-actions{position:fixed;top:12px;right:12px;display:flex;gap:10px;z-index:5}
        .top-actions a{background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.3);color:#fff;text-decoration:none;border-radius:10px;padding:8px 14px;display:inline-flex;align-items:center;gap:8px;transition:.25s;backdrop-filter:blur(4px);}
        .top-actions a:hover{background:rgba(255,255,255,.25);color:var(--marsom-orange)}
        
        #loading-screen{position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;flex-direction:column;transition:opacity .5s}
        .loader{width:50px;height:50px;border:5px solid rgba(255,255,255,.3);border-top:5px solid var(--marsom-orange);border-radius:50%;animation:spin 1s linear infinite;margin-bottom:16px}
        @keyframes spin{to{transform:rotate(360deg)}}
        
        .badge { font-weight: 500; letter-spacing: 0.3px; }
        .btn-group .btn { margin: 0 2px; }
        
        .column-filter { width: 100%; padding: 4px 8px; font-size: 13px; border: 1px solid #dee2e6; border-radius: 4px; }
        .filter-row th { padding: 8px 4px !important; background-color: #e9ecef !important; }
        .filter-row input, .filter-row select { font-size: 13px; }
    </style>
</head>
<body>

<div class="particles">
    <div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div>
</div>

<div id="loading-screen">
    <div class="loader"></div>
    <h3 style="color:#fff">جاري تحميل الطلبات ...</h3>
</div>

<div class="top-actions">
    <a href="javascript:history.back()"><i class="fas fa-arrow-right"></i><span>رجوع</span></a>
    <a href="<?php echo site_url('users1/main_emp'); ?>"><i class="fas fa-home"></i><span>الرئيسية</span></a>
</div>

<div class="main-container container-fluid">
    <div class="text-center">
        <h1 class="page-title">طلبات الموظفين (موافقات)</h1>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card table-card">
                <div class="card-body">
                    
                    <div class="row mb-4 justify-content-center align-items-center g-3">
                        
                        <div class="col-md-4">
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-light border-secondary-subtle"><i class="fa-solid fa-calendar-check text-primary"></i></span>
                                <select id="sheetFilterApp" class="form-select border-secondary-subtle">
                                    <option value="">عرض الكل (جميع الفترات)</option>
                                    <?php if (!empty($salary_sheets)): ?>
                                        <?php foreach ($salary_sheets as $sheet): ?>
                                            <option value="<?= $sheet['id'] ?>" 
                                                    data-start="<?= $sheet['start_date'] ?>" 
                                                    data-end="<?= $sheet['end_date'] ?>">
                                                <?= $sheet['type'] ?> (<?= $sheet['start_date'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <?php if ($current_user_id == '2230'): ?>
                            <div class="col-md-auto">
                                <button id="btnMyTeam" class="btn btn-primary shadow-sm w-100">
                                    <i class="fa-solid fa-users-viewfinder me-2"></i> عرض طلبات فريقي (<?= htmlspecialchars($my_department) ?>)
                                </button>
                                <button id="btnResetTeam" class="btn btn-secondary shadow-sm w-100" style="display:none;">
                                    <i class="fa-solid fa-list me-2"></i> عرض طلبات الجميع
                                </button>
                            </div>
                        <?php endif; ?>
                        
                        <div class="col-md-auto">
                            <div class="btn-group shadow-sm" role="group">
                                <button type="button" class="btn btn-warning" id="filterPending">
                                    <i class="fa-solid fa-hourglass-half me-2"></i> بانتظار الموافقة
                                </button>
                                <button type="button" class="btn btn-success" id="filterApproved">
                                    <i class="fa-solid fa-check me-2"></i> تمت الموافقة
                                </button>
                                <button type="button" class="btn btn-danger" id="filterRejected">
                                    <i class="fa-solid fa-xmark me-2"></i> مرفوض
                                </button>
                                <button type="button" class="btn btn-secondary" id="filterAll">
                                    <i class="fa-solid fa-list me-2"></i> عرض الكل
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example" style="width:100%">
                            <thead>
                                <tr>
                                    <th>رقم الطلب</th>
                                    <th>رقم الموظف</th>
                                    <th>اسم الموظف</th>
                                    <th>اجراء</th>
                                    <th>حالة الطلب</th>
                                    <th>تاريخ الحدث</th>
                                    <th>وقت الطلب</th>
                                    <th>نوع الطلب / تفاصيل</th>
                                    <th>مرفق</th>
                                </tr>
                                <tr class="filter-row">
                                    <th><input type="text" class="column-filter" placeholder="بحث..." data-column="0"></th>
                                    <th><input type="text" class="column-filter" placeholder="بحث..." data-column="1"></th>
                                    <th><input type="text" class="column-filter" placeholder="بحث..." data-column="2"></th>
                                    <th>
                                        <select class="column-filter" data-column="3">
                                            <option value="">الكل</option>
                                            <option value="pending">قيد الانتظار</option>
                                            <option value="completed">تم الإجراء</option>
                                        </select>
                                    </th>
                                    <th>
                                        <select class="column-filter" data-column="4" id="defaultStatusFilter">
                                            <option value="">الكل</option>
                                            <option value="بانتظار موافقتك" selected>بانتظار موافقتك</option>
                                            <option value="تمت الموافقة">تمت الموافقة</option>
                                            <option value="مرفوض">مرفوض</option>
                                        </select>
                                    </th>
                                    <th><input type="text" class="column-filter" placeholder="بحث..." data-column="5"></th>
                                    <th><input type="text" class="column-filter" placeholder="بحث..." data-column="6"></th>
                                    <th>
                                       <select class="column-filter form-select form-select-sm" data-column="7" style="font-size: 11px;">
                                            <option value="">الكل</option>
                                            <option value="مهمة عمل">مهمة عمل</option>
                                            <option value="استقالة">استقالة</option>
                                            <option value="تصحيح بصمة">تصحيح بصمة</option>
                                            <option value="عمل إضافي">عمل إضافي</option>
                                            <option value="إجازة">إجازة</option>
                                            <option value="مصاريف">مصاريف مالية</option>
                                            <option value="عُهدة">طلب عُهدة</option>
                                            <option value="خطاب">طلب خطاب</option>
                                            <option value="الاستئذان">الاستئذان</option>
                                        </select>
                                    </th>
                                    <th>
                                        <select class="column-filter" data-column="8">
                                            <option value="">الكل</option>
                                            <option value="نعم">يوجد مرفق</option>
                                            <option value="لا">لا يوجد</option>
                                        </select>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($get_salary_vacations as $employee) : ?>
                                    <?php 
                                        $eventDate = $employee['event_date'] ?? $employee['date']; 
                                        $deptName = $employee['department'] ?? ''; 
                                    ?>
                                    <tr id="request-row-<?= (int)$employee['id']; ?>" 
                                        class="<?= ($employee['approval_status'] ?? 'pending') === 'pending' ? 'pending-row' : '' ?>"
                                        data-event-date="<?= $eventDate ?>" 
                                        data-department="<?= htmlspecialchars($deptName) ?>">
                                        
                                        <td>
                                            <a href="<?php echo site_url('users1/view_request/' . $employee['id']); ?>" 
                                               class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                                                <i class="fa-solid fa-eye"></i>
                                                <span><?php echo $employee['id']; ?></span>
                                            </a>
                                        </td>   
                                        <td><?php echo htmlspecialchars($employee['emp_id'] ?? 'غير معروف'); ?></td>              
                                        <td><?php echo htmlspecialchars($employee['emp_name'] ?? 'غير معروف'); ?></td> 
                                        <td class="text-nowrap">
                                            <?php
                                                $is_pending = ($employee['approval_status'] ?? 'pending') === 'pending';
                                            ?>
                                            <?php if ($is_pending): ?>
                                                <div class="btn-group" role="group" aria-label="actions">
                                                    <button type="button" class="btn btn-sm btn-success js-approve" data-id="<?= (int)$employee['id']; ?>" title="موافقة"><i class="fa-solid fa-check"></i></button>
                                                    <button type="button" class="btn btn-sm btn-danger js-reject" data-id="<?= (int)$employee['id']; ?>" title="رفض"><i class="fa-solid fa-xmark"></i></button>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted small"><i class="fa-solid fa-check-double"></i> تم الإجراء</span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <td id="status-badge-<?= (int)$employee['id']; ?>">
                                            <?php
                                                $st = (int)$employee['status']; 
                                                $approval_st = $employee['approval_status'] ?? 'unknown'; 
                                                
                                                if ($approval_st === 'approved' || $st === 2) { 
                                                    echo '<span class="badge rounded-pill bg-success-subtle text-success-emphasis border border-success-subtle"><i class="fa-solid fa-circle-check ms-1"></i> تمت الموافقة</span>'; 
                                                } 
                                                elseif ($approval_st === 'rejected' || $st === 3) { 
                                                    echo '<span class="badge rounded-pill bg-danger-subtle text-danger-emphasis border border-danger-subtle"><i class="fa-solid fa-circle-xmark ms-1"></i> مرفوض</span>'; 
                                                } 
                                                elseif ($approval_st === 'pending') { 
                                                    echo '<span class="badge rounded-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle"><i class="fa-solid fa-hourglass-half ms-1"></i> بانتظار موافقتك</span>'; 
                                                } 
                                                else { 
                                                    echo '<span class="badge rounded-pill bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle">' . htmlspecialchars($approval_st) . '</span>'; 
                                                }
                                            ?>
                                            <?php if (!empty($employee['reason_for_rejection'])): ?>
                                                <div id="reject-reason-<?= (int)$employee['id']; ?>" class="small text-muted mt-1">سبب: <?= htmlspecialchars($employee['reason_for_rejection']); ?></div>
                                            <?php else: ?>
                                                <div id="reject-reason-<?= (int)$employee['id']; ?>" class="small text-muted mt-1 d-none"></div>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <td><?= $eventDate ?></td>
                                        <td><?php echo $employee['time']; ?></td>
                                        <td>
                                            <?php echo $employee['order_name']; ?>
                                            
                                            <?php if ((isset($employee['type']) && $employee['type'] == 9) || (isset($employee['order_type']) && $employee['order_type'] == 9)): ?>
                                                <div class="mt-2 small text-muted border-top pt-1">
                                                    <?php if(!empty($employee['mission_type'])): ?>
                                                        <span class="badge bg-primary-subtle text-primary-emphasis mb-1"><?= $employee['mission_type'] ?></span><br>
                                                    <?php endif; ?>
                                                    
                                                    <?php if(!empty($employee['mission_date'])): ?>
                                                        <i class="fa-regular fa-calendar me-1"></i> <?= $employee['mission_date'] ?><br>
                                                    <?php endif; ?>
                                                    
                                                    <?php if(!empty($employee['mission_start_time'])): ?>
                                                        <i class="fa-regular fa-clock me-1"></i> <?= $employee['mission_start_time'] ?> - <?= $employee['mission_end_time'] ?>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ((isset($employee['type']) && $employee['type'] == 12) || (isset($employee['order_type']) && $employee['order_type'] == 12)): ?>
                                                <div class="mt-2 small text-muted border-top pt-1">
                                                    <?php if(!empty($employee['permission_start_time'])): ?>
                                                        <i class="fa-regular fa-clock me-1 text-primary"></i> 
                                                        <span dir="ltr" class="fw-bold">
                                                            <?= date('H:i', strtotime($employee['permission_start_time'])) ?> - <?= date('H:i', strtotime($employee['permission_end_time'])) ?>
                                                        </span>
                                                        <br>
                                                        <span class="badge bg-info-subtle text-info-emphasis mt-1 border border-info-subtle">
                                                            <i class="fa-solid fa-stopwatch me-1"></i> <?= $employee['permission_hours'] ?> ساعة
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                                $file = isset($employee['file']) ? trim($employee['file']) : '';
                                                if ($file !== ''):
                                                $url = base_url($file);
                                            ?>
                                            <a href="<?= htmlspecialchars($url, ENT_QUOTES) ?>" target="_blank" class="btn btn-sm btn-outline-primary" title="عرض المرفق"><i class="fa-solid fa-paperclip"></i></a>
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
                <h6 class="modal-title text-danger"><i class="fa-solid fa-circle-xmark ms-1"></i> رفض الطلب</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2 fw-bold">الرجاء كتابة سبب الرفض:</div>
                                <textarea id="rejectReason" class="form-control" rows="3" placeholder="اكتب السبب هنا..."></textarea>
                <div class="invalid-feedback d-block d-none" id="rejectReasonError">يجب كتابة سبب الرفض (3 أحرف على الأقل).</div>
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
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    
    // --- 1. Initialize Custom Filter Functions ---
    
    // A. Salary Sheet Filter (Date Range)
    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
            var selectedOption = $('#sheetFilterApp').find('option:selected');
            var start = selectedOption.data('start');
            var end = selectedOption.data('end');
            
            if (!start || !end) return true; // No filter selected

            var eventDateStr = data[5]; // Column index 5 is "Date"
            if (!eventDateStr) return false;

            if (eventDateStr >= start && eventDateStr <= end) {
                return true;
            }
            return false;
        }
    );

    // B. Department Filter (My Team)
    // This function will be pushed/popped dynamically
    var myDepartment = "<?= $my_department ?? '' ?>";
    var deptFilterFunction = function(settings, data, dataIndex) {
        var rowNode = dt.row(dataIndex).node();
        var rowDept = $(rowNode).attr('data-department');
        return rowDept === myDepartment;
    };

    // --- 2. Initialize DataTable ---
    var dt = $('.dataTables-example').DataTable({
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "الكل"]],
        order: [[ 0, "desc" ]], // Sort by ID descending
        layout: {
            topStart: {
                buttons: [
                    { extend:'copy', text:'<i class="fa fa-copy"></i> نسخ' },
                    { extend:'excel', text:'<i class="fa fa-file-excel"></i> إكسل' },
                    { extend:'print', text:'<i class="fa fa-print"></i> طباعة' },
                ]
            }
        },
        language: { url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json' },
        initComplete: function () {
            // Apply individual column filtering
            this.api().columns().every(function () {
                var column = this;
                
                // Skip if this is the actions column (index 3), status (4), type (7), or attachment (8) - they have select filters
                if (column.index() === 3 || column.index() === 4 || column.index() === 7 || column.index() === 8) {
                    return;
                }
                
                // Create text input for other columns
                $('input.column-filter[data-column="' + column.index() + '"]').on('keyup change', function () {
                    if (column.search() !== this.value) {
                        column.search(this.value).draw();
                    }
                });
            });
            
            // For select filters (actions, status, type, attachment)
            $('select.column-filter').on('change', function () {
                var columnIndex = $(this).data('column');
                var value = $(this).val();
                
                if (columnIndex === 3) { // Actions column
                    if (value === 'pending') {
                        dt.column(columnIndex).search('^تم الإجراء$', true, false).draw();
                    } else if (value === 'completed') {
                        dt.column(columnIndex).search('^تم الإجراء$', true, true).draw();
                    } else {
                        dt.column(columnIndex).search('').draw();
                    }
                } 
                else if (columnIndex === 4) { // Status column
                    dt.column(columnIndex).search(value).draw();
                }
                else if (columnIndex === 7) { // Type Filter (Client-side Text Search)
                     dt.column(columnIndex).search(value).draw();
                }
                else if (columnIndex === 8) { // Attachment column
                    if (value === 'نعم') {
                        dt.column(columnIndex).search('^—$', true, false).draw();
                    } else if (value === 'لا') {
                        dt.column(columnIndex).search('^—$', true, true).draw();
                    } else {
                        dt.column(columnIndex).search('').draw();
                    }
                }
            });
            
            // Set default filter for pending approvals
            $('#defaultStatusFilter').val('بانتظار موافقتك').trigger('change');
        }
    });

    // --- 3. Filter Event Listeners ---

    // Sheet Filter Change
    $('#sheetFilterApp').on('change', function() {
        dt.draw();
    });

    // My Team Button Click
    $('#btnMyTeam').on('click', function() {
        if (!myDepartment) {
            alert('لم يتم العثور على بيانات إدارتك في النظام.');
            return;
        }
        // Add the department filter function
        $.fn.dataTable.ext.search.push(deptFilterFunction);
        dt.draw();
        
        // Toggle Buttons
        $(this).hide();
        $('#btnResetTeam').show();
        // Reset sheet filter to show all team orders
        $('#sheetFilterApp').val('').trigger('change');
    });

    // Reset Team Button Click
    $('#btnResetTeam').on('click', function() {
        // Remove the department filter function
        var index = $.fn.dataTable.ext.search.indexOf(deptFilterFunction);
        if (index > -1) {
            $.fn.dataTable.ext.search.splice(index, 1);
        }
        
        dt.draw();
        
        // Toggle Buttons
        $(this).hide();
        $('#btnMyTeam').show();
    });

    // Quick Filter Buttons
    $('#filterPending').on('click', function() {
        $('#defaultStatusFilter').val('بانتظار موافقتك').trigger('change');
    });

    $('#filterApproved').on('click', function() {
        $('#defaultStatusFilter').val('تمت الموافقة').trigger('change');
    });

    $('#filterRejected').on('click', function() {
        $('#defaultStatusFilter').val('مرفوض').trigger('change');
    });

    $('#filterAll').on('click', function() {
        $('#defaultStatusFilter').val('').trigger('change');
        // Also clear all other filters
        $('.column-filter').val('');
        dt.columns().search('').draw();
    });

    // --- 4. AJAX Action Logic (Approve/Reject) ---
    var UPDATE_URL = '<?= site_url("users1/update_order_status"); ?>';
    var CSRF_NAME = '<?= $this->security->get_csrf_token_name(); ?>';
    var CSRF_HASH = '<?= $this->security->get_csrf_hash(); ?>';

    function sendUpdate(id, status, reason, $btn) {
        var payload = { id: id, status: status };
        if (typeof reason === 'string') payload.reason = reason;
        payload[CSRF_NAME] = CSRF_HASH;

        return $.ajax({
            url: UPDATE_URL,
            type: 'POST',
            dataType: 'json',
            data: payload,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        }).done(function(res) {
            if (res && res.csrfHash) CSRF_HASH = res.csrfHash;
            if (res && res.ok) {
                // Success: Update UI
                dt.row($btn.closest('tr')).remove().draw();
            } else {
                alert((res && res.error) ? res.error : 'تعذر تنفيذ العملية');
            }
        }).fail(function(xhr) {
            alert('خطأ في الاتصال بالسيرفر: ' + xhr.status);
        });
    }

    // Approve Click
    $(document).on('click', '.js-approve', function(e) {
        e.preventDefault();
        var $btn = $(this), id = parseInt($btn.data('id'), 10);
        if (!id || !confirm('هل أنت متأكد من الموافقة على هذا الطلب؟')) return;
        
        $btn.prop('disabled', true);
        sendUpdate(id, 2, null, $btn).always(function() { 
            // Only re-enable if it wasn't removed (i.e. error)
            if($btn.closest('tr').length > 0) $btn.prop('disabled', false); 
        });
    });

    // Reject Logic
    var rejectId = null;
    var $rejectBtn = null;
    const rejectionModal = new bootstrap.Modal(document.getElementById('rejectModal'));
    
    $(document).on('click', '.js-reject', function(e) {
        e.preventDefault();
        $rejectBtn = $(this);
        rejectId = parseInt($rejectBtn.data('id'), 10);
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
        });
    });

    // --- 5. Loading Screen ---
    const loading = document.getElementById('loading-screen');
    const main = document.querySelector('.main-container');
    
    setTimeout(function() {
        loading.style.opacity = '0';
        setTimeout(function() {
            loading.style.display = 'none';
            document.body.style.overflow = 'auto';
            main.style.visibility = 'visible';
            main.style.opacity = '1';
        }, 400);
    }, 500);
});
</script>

</body>
</html>