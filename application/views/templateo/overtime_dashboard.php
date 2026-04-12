<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة المستحقات المالية</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #c0392b;
            --light-bg: #ebedef;
            --text-color: #2c3e50;
            --border-radius: 12px;
            --card-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background-color: var(--light-bg);
            color: var(--text-color);
            overflow-x: hidden;
        }
        /* --- Soft Badges (Missing Styles) --- */
.status-badge {
    display: inline-block;
    padding: 0.35em 0.65em;
    font-size: 0.75em;
    font-weight: 700;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 0.25rem;
}

/* Green (Paid/Approved) */
.badge-soft-success {
    color: #27ae60;
    background-color: rgba(39, 174, 96, 0.1);
}

/* Yellow/Orange (Pending/Requested) */
.badge-soft-warning {
    color: #f39c12;
    background-color: rgba(243, 156, 18, 0.1);
}

/* Red (Rejected) */
.badge-soft-danger {
    color: #c0392b;
    background-color: rgba(192, 57, 43, 0.1);
}

/* Blue (Requested from Finance) */
.badge-soft-info {
    color: #2980b9;
    background-color: rgba(41, 128, 185, 0.1);
}

/* Grey (Unpaid) */
.badge-soft-secondary {
    color: #7f8c8d;
    background-color: rgba(127, 140, 141, 0.1);
}
        /* --- Header Section --- */
        .page-header {
            background: white;
            padding: 25px 0;
            margin-bottom: 30px;
            border-bottom: 1px solid #e0e0e0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        }
        .page-title {
            font-weight: 800;
            color: var(--primary-color);
            margin: 0;
            font-size: 1.8rem;
        }
        .page-subtitle {
            color: #7f8c8d;
            font-size: 0.95rem;
            margin-top: 5px;
        }

        /* --- Tabs Design --- */
        .custom-tabs .nav-link {
            border: none;
            color: #7f8c8d;
            font-weight: 700;
            padding: 12px 25px;
            border-radius: 50px;
            transition: all 0.3s ease;
            margin-left: 10px;
        }
        .custom-tabs .nav-link:hover {
            background-color: #ecf0f1;
            color: var(--primary-color);
        }
        .custom-tabs .nav-link.active {
            background-color: var(--primary-color);
            color: white;
            box-shadow: 0 4px 10px rgba(44, 62, 80, 0.2);
        }
        .custom-tabs {
            border-bottom: none;
            margin-bottom: 20px;
        }

        /* --- Card & Table --- */
        .dashboard-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            border: none;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .table thead th {
            background-color: #f8f9fa;
            color: #576574;
            font-weight: 700;
            border-bottom: 2px solid #ecf0f1;
            padding: 15px;
            white-space: nowrap;
        }
        .table tbody td {
            vertical-align: middle;
            padding: 15px;
            border-bottom: 1px solid #f1f2f6;
            color: #2c3e50;
            font-weight: 500;
        }
        .table-hover tbody tr:hover {
            background-color: #fcfcfc;
        }

        /* --- Action Buttons --- */
        .btn-action {
            width: 35px; height: 35px;
            padding: 0;
            display: inline-flex;
            align-items: center; justify-content: center;
            border-radius: 8px;
            margin: 0 3px;
            transition: all 0.2s;
            border: none;
            font-size: 0.9rem;
        }
        .btn-action:hover { transform: translateY(-2px); }
        .btn-view { background-color: #e3f2fd; color: #3498db; }
        .btn-pay { background-color: #e8f8f5; color: #2ecc71; }
        .btn-request { background-color: #fef9e7; color: #f39c12; }

        /* --- Modal --- */
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        }
        .modal-header {
            border-bottom: 1px solid #f1f1f1;
            padding: 20px 25px;
        }
    </style>
</head>
<body>

    <header class="page-header">
        <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title"><i class="fas fa-wallet me-2 text-success"></i> لوحة المستحقات المالية</h1>
                <p class="page-subtitle">إدارة العمل الإضافي، الانتدابات، وتصفيات نهاية الخدمة</p>
            </div>
            <div>
                <span class="badge bg-white text-dark border px-3 py-2 rounded-pill shadow-sm">
                    <i class="fas fa-calendar-alt me-1 text-muted"></i> <?php echo date('Y-m-d'); ?>
                </span>
            </div>
        </div>
    </header>

    <div class="container-fluid px-4">
        
        <div class="card dashboard-card mb-4">
            <div class="card-header bg-white border-bottom-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                <h6 class="text-primary fw-bold mb-0"><i class="fas fa-filter me-2"></i> تصفية متقدمة</h6>
            </div>
            <div class="card-body">
                <form id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold">الشركة (Company)</label>
                            <select class="form-select" id="filter_company" name="filter_company">
                                <option value="">الكل (All)</option>
                                <option value="شركة مرسوم">شركة مرسوم</option>
                                <option value="مكتب الدكتور">مكتب الدكتور</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold">اسم الموظف (Name)</label>
                            <input type="text" class="form-control" id="filter_emp_name_text" name="filter_emp_name_text">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted small fw-bold">الرقم الوظيفي (ID)</label>
                            <input type="text" class="form-control" id="filter_emp_id_text" name="filter_emp_id_text">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted small fw-bold">من تاريخ</label>
                            <input type="date" class="form-control" id="filter_date_from" name="filter_date_from">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted small fw-bold">إلى تاريخ</label>
                            <input type="date" class="form-control" id="filter_date_to" name="filter_date_to">
                        </div>
                        
                        <div class="col-12 d-flex justify-content-end align-items-center mt-4 border-top pt-3">
                            <div>
                                <button type="button" id="btn-reset" class="btn btn-light border px-4 me-2">
                                    <i class="fas fa-undo me-1"></i> إعادة تعيين
                                </button>
                                <button type="button" id="btn-filter" class="btn btn-primary px-5">
                                    <i class="fas fa-search me-1"></i> بحث
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <ul class="nav nav-tabs custom-tabs" id="financeTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="ot-tab" data-bs-toggle="tab" data-bs-target="#ot-pane" type="button">
                    <i class="fas fa-clock me-2"></i> العمل الإضافي
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="mandate-tab" data-bs-toggle="tab" data-bs-target="#mandate-pane" type="button">
                    <i class="fas fa-plane me-2"></i> الانتدابات
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="eos-tab" data-bs-toggle="tab" data-bs-target="#eos-pane" type="button">
                    <i class="fas fa-hand-holding-usd me-2"></i> نهاية الخدمة
                </button>
            </li>
            <li class="nav-item" role="presentation">
    <button class="nav-link" id="expenses-tab" data-bs-toggle="tab" data-bs-target="#expenses" type="button" role="tab">
        <i class="fas fa-file-invoice-dollar me-2"></i> المصاريف المالية
    </button>
</li>
        </ul>

        <div class="tab-content" id="financeTabContent">
            
            <div class="tab-pane fade show active" id="ot-pane" role="tabpanel">
                <div class="dashboard-card">
                    <div class="table-responsive">
                        <table id="overtimeTable" class="table table-hover align-middle w-100">
<thead>
    <tr>
        <th>#</th>
        <th>رقم الموظف</th>
        <th>الاسم</th>
        <th>الشركة</th>
        <th>سلسلة الاعتمادات</th>
        <th>التاريخ</th>
        <th>الساعات</th>
        <th>المبلغ</th>
        <th>الحالة</th>
        <th>الصرف</th>
        <th>الإيصال</th> <th>الإجراءات</th>
    </tr>
</thead>
                        </table>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="mandate-pane" role="tabpanel">
                <div class="dashboard-card">
                    <div class="table-responsive">
                        <table id="mandateTable" class="table table-hover align-middle w-100">
                            <thead>
    <tr>
        <th>#</th>
        <th>رقم الموظف</th>
        <th>الاسم</th>
        <th>الشركة</th>
        <th>سلسلة الاعتمادات</th>
        <th>التاريخ</th>
        <th>المدة</th>
        <th>المبلغ</th>
        <th>الحالة</th>
        <th>الصرف</th>
        <th>الإيصال</th> <th>الإجراءات</th>
    </tr>
</thead>
                        </table>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="eos-pane" role="tabpanel">
                <div class="dashboard-card">
                    <div class="table-responsive">
                        <table id="eosTable" class="table table-hover align-middle w-100">
                            <table id="eosTable" class="table table-hover align-middle w-100">
    <thead>
        <tr>
            <th>#</th>
            <th>رقم الموظف</th>
            <th>الاسم</th>
            <th>الشركة</th>
            <th>سلسلة الاعتمادات</th> <th>المبلغ النهائي</th>
            <th>الحالة</th>
            <th>الصرف</th>
            <th>الإيصال</th> <th>الإجراءات</th>
        </tr>
    </thead>
</table>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="tab-pane fade" id="expenses" role="tabpanel" aria-labelledby="expenses-tab">
    <div class="card p-4 border-0 shadow-sm rounded-3">
        <div class="table-responsive">
            <table id="expensesTable" class="table table-hover align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th>رقم الطلب</th>
                        <th>الموظف</th>
                        <th>البند</th>
                        <th>المبلغ</th>
                        <th>التاريخ</th>
                        <th>السبب</th>
                        <th>حالة الدفع</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="approverModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h6 class="modal-title fw-bold">سلسلة الاعتمادات</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-light" id="approverModalBody" style="min-height:100px;">
                    </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-success"><i class="fas fa-money-check-alt me-2"></i> تسجيل عملية صرف</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="paymentForm">
                    <div class="modal-body">
                        <input type="hidden" name="order_id" id="modal_order_id">
                        <input type="hidden" name="order_type" id="modal_order_type">
                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        
                        <div class="mb-4 text-center">
                             <div class="p-3 bg-light rounded-circle d-inline-block">
                                <i class="fas fa-file-invoice-dollar fa-3x text-success"></i>
                             </div>
                             <p class="text-muted mt-2">يرجى إرفاق إيصال التحويل البنكي لإتمام العملية</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">تاريخ التحويل</label>
                            <input type="date" name="payment_date" class="form-control form-control-lg" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">صورة الإيصال</label>
                            <input type="file" name="receipt_file" class="form-control form-control-lg" accept=".pdf,.jpg,.jpeg,.png" required>
                            <div class="form-text">الملفات المدعومة: PDF, JPG, PNG</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary px-4 rounded-pill" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success px-5 rounded-pill fw-bold">تأكيد الصرف</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
        var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
        var otTable, manTable, eosTable, expensesTable;

        $(document).ready(function() {
            
            // Common AJAX Data Filter
            function getFilterData(d) {
                d[csrfName] = csrfHash; 
                d.filter_company = $('#filter_company').val();
                d.filter_emp_name_text = $('#filter_emp_name_text').val();
                d.filter_emp_id_text = $('#filter_emp_id_text').val();
                d.filter_date_from = $('#filter_date_from').val();
                d.filter_date_to = $('#filter_date_to').val();
            }

            // 1. Overtime Table
            otTable = $('#overtimeTable').DataTable({
                "processing": true, "serverSide": true,
                "ajax": {
                    "url": "<?php echo base_url('users1/fetch_overtime_orders'); ?>",
                    "type": "POST", "data": getFilterData,
                    "dataSrc": function(json) { if(json.csrf_hash) csrfHash = json.csrf_hash; return json.data; }
                },
                "columns": [
                    { "data": "id" },
                    { "data": "emp_id" },
                    { "data": "emp_name" },
                    { "data": "company_name" },
                    { "data": "approvers" }, // Renders the button HTML from PHP
                    { "data": "ot_date" },
                    { "data": "ot_hours" },
                    { "data": "ot_amount" },
                    { "data": "status" },
                    { "data": "pay_status" },
                    { 
                        "data": "bank_receipt_file",
                        "render": function(data) {
                            if(data) return `<a href="<?php echo base_url(''); ?>${data}" target="_blank" class="btn btn-sm btn-outline-info"><i class="fas fa-file-pdf"></i></a>`;
                            return '-';
                        }
                    },
                    { "data": "actions" }
                ],
                "order": [[ 0, "desc" ]]
            });

            // 2. Mandate Table
            manTable = $('#mandateTable').DataTable({
                "processing": true, "serverSide": true,
                "ajax": {
                    "url": "<?php echo base_url('users1/fetch_mandate_data'); ?>",
                    "type": "POST", "data": getFilterData,
                    "dataSrc": function(json) { if(json.csrf_hash) csrfHash = json.csrf_hash; return json.data; }
                },
                "columns": [
                    { "data": "id" },
                    { "data": "emp_id" },
                    { "data": "emp_name" },
                    { "data": "company_name" },
                    { "data": "approvers" }, // Renders the button HTML from PHP
                    { "data": "start_date" },
                    { "data": "duration" },
                    { "data": "amount" },
                    { "data": "status" },
                    { "data": "pay_status" },
                    { 
                        "data": "bank_receipt_file",
                        "render": function(data) {
                            if(data) return `<a href="<?php echo base_url(''); ?>${data}" target="_blank" class="btn btn-sm btn-outline-info"><i class="fas fa-file-pdf"></i></a>`;
                            return '-';
                        }
                    },
                    { "data": "actions" }
                ],
                "order": [[ 0, "desc" ]]
            });

            // 3. End of Service Table (EOS)
            eosTable = $('#eosTable').DataTable({
                "processing": true, "serverSide": true,
                "ajax": {
                    "url": "<?php echo base_url('users1/fetch_eos_data'); ?>",
                    "type": "POST", "data": getFilterData,
                    "dataSrc": function(json) { if(json.csrf_hash) csrfHash = json.csrf_hash; return json.data; }
                },
                "columns": [
                    null, // 0. #
                    null, // 1. Emp ID
                    null, // 2. Name
                    null, // 3. Company
                    { "orderable": false }, // 4. Approvers Button
                    null, // 5. Amount
                    null, // 6. Status
                    null, // 7. Pay Status
                    { "orderable": false }, // 8. Receipt (HTML from PHP)
                    { "orderable": false }  // 9. Actions
                ],
                "order": [[ 0, "desc" ]]
            });

            // 4. Expenses Table (جدول المصاريف)
            expensesTable = $('#expensesTable').DataTable({
                "processing": true, 
                "serverSide": false, // False because we fetch all approved directly
                "ajax": {
                    "url": "<?php echo base_url('users1/fetch_approved_expenses'); ?>",
                    "type": "POST", // POST to pass CSRF token safely
                    "data": getFilterData,
                    "dataSrc": function(json) { if(json.csrf_hash) csrfHash = json.csrf_hash; return json.data; }
                },
                "columns": [
                    { "data": "id" },
                    { "data": "emp_name" },
                    { "data": "exp_item_name" },
                    { "data": "exp_amount" },
                    { "data": "exp_date" },
                    { "data": "exp_reason" },
                    { "data": "payment_status" },
                    { "data": "actions", "orderable": false }
                ],
                "order": [[ 0, "desc" ]],
                "language": { "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json" }
            });

            // --- Filters Events ---
            $('#btn-filter').click(function(){
                otTable.ajax.reload();
                manTable.ajax.reload();
                eosTable.ajax.reload();
                expensesTable.ajax.reload();
            });

            $('#btn-reset').click(function(){
                $('#filterForm')[0].reset();
                otTable.ajax.reload();
                manTable.ajax.reload();
                eosTable.ajax.reload();
                expensesTable.ajax.reload();
            });

            // --- Payment Form Submit ---
            $('#paymentForm').on('submit', function(e) {
                e.preventDefault();
                $(this).find('input[name="'+csrfName+'"]').val(csrfHash);
                var formData = new FormData(this);
                var type = $('#modal_order_type').val(); 
                
                var targetUrl = '';
                if(type === 'mandate') targetUrl = '<?php echo base_url("users1/confirm_mandate_payment"); ?>';
                else if(type === 'eos') targetUrl = '<?php echo base_url("users1/confirm_eos_payment"); ?>';
                else if(type === 'expense') targetUrl = '<?php echo base_url("users1/submit_expense_payment"); ?>';
                else targetUrl = '<?php echo base_url("users1/confirm_ot_payment"); ?>';

                var submitBtn = $(this).find('button[type="submit"]');
                var originalText = submitBtn.html();
                submitBtn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

                $.ajax({
                    url: targetUrl, type: 'POST', data: formData,
                    contentType: false, processData: false, dataType: 'json',
                    success: function(response) {
                        submitBtn.html(originalText).prop('disabled', false);
                        if(response.csrf_hash) csrfHash = response.csrf_hash;
                        
                        if(response.status === 'success') {
                            $('#paymentModal').modal('hide');
                            alert(response.message);
                            otTable.ajax.reload(null, false);
                            manTable.ajax.reload(null, false);
                            eosTable.ajax.reload(null, false);
                            expensesTable.ajax.reload(null, false);
                        } else { alert(response.message); }
                    },
                    error: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                        alert('حدث خطأ في الاتصال');
                    }
                });
            });
        });

        // --- Functions ---
        function showApprovers(id, type) {
            $('#approverModalBody').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i></div>');
            new bootstrap.Modal(document.getElementById('approverModal')).show();
            
            $.ajax({
                url: '<?php echo base_url("users1/get_approvers_ajax"); ?>',
                type: 'POST',
                data: {id: id, type: type, [csrfName]: csrfHash},
                success: function(response) {
                    $('#approverModalBody').html(response);
                }
            });
        }
        
        // --- Action Functions ---
        function openPaymentModal(id, type) {
            $('#modal_order_id').val(id);
            $('#modal_order_type').val(type); // 'expense', 'ot', 'eos', 'mandate'
            $('input[name="receipt_file"]').val('');
            new bootstrap.Modal(document.getElementById('paymentModal')).show();
        }

        function sendToFinance(id, btn) {
            if(!confirm('تأكيد إرسال طلب العمل الإضافي للمالية؟')) return;
            handleRequest('<?php echo base_url("users1/submit_payment_request"); ?>', id, btn, otTable);
        }

        function sendMandateToFinance(id, btn) {
            if(!confirm('تأكيد إرسال الانتداب للمالية؟')) return;
            handleRequest('<?php echo base_url("users1/submit_mandate_payment"); ?>', id, btn, manTable);
        }
        
        function sendEosToFinance(id, btn) {
            if(!confirm('تأكيد إرسال مخالصة نهاية الخدمة للمالية؟')) return;
            handleRequest('<?php echo base_url("users1/submit_eos_payment"); ?>', id, btn, eosTable);
        }

        // New action function specifically for Expenses if you ever need an extra step before paying
        function sendExpenseToFinance(id, btn) {
            if(!confirm('تأكيد إرسال المصروفات للمالية؟')) return;
            handleRequest('<?php echo base_url("users1/submit_expense_request"); ?>', id, btn, expensesTable);
        }

        function handleRequest(url, id, btn, tableObj) {
            var tooltip = bootstrap.Tooltip.getInstance(btn);
            if(tooltip) tooltip.hide();

            var originalContent = $(btn).html();
            $(btn).html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
            
            $.ajax({
                url: url, type: 'POST',
                data: { order_id: id, [csrfName]: csrfHash }, dataType: 'json',
                success: function(response) {
                    if(response.csrf_hash) csrfHash = response.csrf_hash;
                    if(response.status === 'success') {
                        tableObj.ajax.reload(null, false);
                        alert(response.message);
                    } else {
                        alert(response.message);
                        $(btn).html(originalContent).prop('disabled', false);
                    }
                },
                error: function() {
                    alert('حدث خطأ في الشبكة');
                    $(btn).html(originalContent).prop('disabled', false);
                }
            });
        }
    </script>
</body>
</html>