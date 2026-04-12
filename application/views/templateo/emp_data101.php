<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>بيانات الموظفين</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        :root{--marsom-blue:#001f3f;--marsom-orange:#FF8C00;--text-light:#fff;--text-dark:#343a40;}
        body{font-family:'Tajawal',sans-serif;overflow-y:auto !important;background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);background-size:400% 400%;animation:grad 20s ease infinite;color:var(--text-dark);position:relative}
        @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        .page-title{font-family:'El Messiri',sans-serif;font-weight:700;font-size:2.8rem;color:var(--text-light);margin-bottom:32px;text-align:center;position:relative;display:inline-block;padding-bottom:10px;text-shadow:0 3px 6px rgba(0,0,0,.4)}
        .page-title::after{content:'';position:absolute;width:100px;height:4px;background:linear-gradient(90deg,var(--marsom-blue),var(--marsom-orange));bottom:0;left:50%;transform:translateX(-50%);border-radius:2px}
        .table-card{background:rgba(255,255,255,.95);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.3);border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,.15);padding:25px}
        .dataTables-example thead th{background-color:#001f3f !important;color:#fff;text-align:center;vertical-align:middle;border-bottom:2px solid #00152b}
        .dataTables-example tbody td{text-align:center;vertical-align:middle;font-size:14px;white-space:nowrap}
        
        .filter-label { font-size: 0.9rem; font-weight: 700; color: #001f3f; margin-bottom: 5px; }
        .form-control, .form-select { border-radius: 8px; font-size: 0.9rem; }
        
        /* Status badge styles */
        .status-badge { padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; display: inline-block; min-width: 70px; text-align: center; }
        .status-active { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .status-resigned { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .status-deleted { background-color: #f0f0f0; color: #6c757d; border: 1px solid #dee2e6; }
        .status-other { background-color: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
    </style>
</head>
<body>

<div class="container-fluid my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="text-center flex-grow-1">
            <h1 class="page-title">بيانات الموظفين</h1>
        </div>
        <div>
             <a href="<?php echo site_url('users1/main_hr1'); ?>" class="btn btn-light"><i class="fas fa-arrow-right"></i> رجوع للرئيسية</a>
        </div>
    </div>

    <?php if($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
    <?php endif; ?>
    <?php if($this->session->flashdata('error')): ?>
        <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
    <?php endif; ?>

    <div class="card table-card mb-4">
        <div class="card-header bg-transparent border-0 pb-0">
            <h5 class="text-primary fw-bold"><i class="fas fa-filter me-2"></i>بحث وفلترة</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="filter-label">الرقم الوظيفي</label>
                    <input type="text" id="filter_employee_id" class="form-control" placeholder="بحث...">
                </div>
                <div class="col-md-3">
                    <label class="filter-label">اسم الموظف</label>
                    <input type="text" id="filter_name" class="form-control" placeholder="بحث...">
                </div>
                 <div class="col-md-3">
                    <label class="filter-label">رقم الهوية</label>
                    <input type="text" id="filter_id_number" class="form-control" placeholder="بحث...">
                </div>

                <div class="col-md-3">
                    <label class="filter-label">الحالة</label>
                    <select id="filter_status" class="form-select select2">
                        <option value="">الكل</option>
                        <option value="active">نشط</option>
                        <option value="resigned">منتهي الخدمة</option>
                        <option value="deleted">محذوف</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="filter-label">الشركة</label>
                    <select id="filter_company" class="form-select select2">
                        <option value="">الكل</option>
                        <?php foreach($companies as $c): ?>
                            <option value="<?= html_escape($c) ?>"><?= html_escape($c) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="filter-label">الإدارة / القسم</label>
                    <select id="filter_department" class="form-select select2">
                        <option value="">الكل</option>
                        <?php foreach($departments as $d): ?>
                            <option value="<?= html_escape($d) ?>"><?= html_escape($d) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="filter-label">المسمى الوظيفي</label>
                    <select id="filter_position" class="form-select select2">
                        <option value="">الكل</option>
                        <?php foreach($positions as $p): ?>
                            <option value="<?= html_escape($p) ?>"><?= html_escape($p) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                 <div class="col-md-3">
                    <label class="filter-label">المدير المباشر</label>
                    <select id="filter_manager" class="form-select select2">
                        <option value="">الكل</option>
                        <?php foreach($managers as $m): ?>
                            <option value="<?= html_escape($m) ?>"><?= html_escape($m) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="filter-label">الموقع</label>
                    <select id="filter_location" class="form-select select2">
                        <option value="">الكل</option>
                        <?php foreach($locations as $l): ?>
                            <option value="<?= html_escape($l) ?>"><?= html_escape($l) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="filter-label">الجنسية</label>
                    <select id="filter_nationality" class="form-select select2">
                        <option value="">الكل</option>
                        <?php foreach($nationalities as $n): ?>
                            <option value="<?= html_escape($n) ?>"><?= html_escape($n) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                 <div class="col-md-2">
                    <label class="filter-label">الجنس</label>
                    <select id="filter_gender" class="form-select">
                        <option value="">الكل</option>
                        <option value="ذكر">ذكر</option>
                        <option value="أنثى">أنثى</option>
                    </select>
                </div>

                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button id="btn-search" class="btn btn-primary flex-grow-1 fw-bold"><i class="fa fa-search"></i> تطبيق الفلتر</button>
                    <button id="btn-clear" class="btn btn-outline-danger flex-grow-1 fw-bold"><i class="fa fa-times"></i> مسح</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card">
        <div class="card-header d-flex flex-wrap justify-content-end gap-2 bg-transparent border-0 mb-2">
            <a href="#" id="btn-export-excel" class="btn btn-outline-success"><i class="fa fa-file-download"></i> تصدير إكسل</a>
            <button type="button" class="btn btn-outline-primary fw-bold" data-bs-toggle="modal" data-bs-target="#exportModal">
                <i class="fas fa-table me-2"></i> تقرير مخصص
            </button>
            <a href="<?= site_url('users1/add_employee') ?>" class="btn btn-primary"><i class="fa fa-plus"></i> إضافة موظف</a>
            <a href="<?= site_url('users1/upload_employees_page') ?>" class="btn btn-success"><i class="fa fa-file-excel"></i> رفع ملف Excel</a>
            <button type="button" id="btn-check-missing" class="btn btn-warning fw-bold"><i class="fas fa-exclamation-triangle"></i> بيانات ناقصة</button>
            <button type="button" id="btn-check-resigned" class="btn btn-danger fw-bold"><i class="fas fa-user-times"></i> منتهية خدماتهم</button>
        </div>
        <div class="card-body">
            <input type="hidden" id="csrf-token" name="<?= $csrf_token_name ?>" value="<?= $csrf_hash ?>">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover dataTables-example" style="width:100%">
                    <thead>
                        <tr>
                            <th>الرقم الوظيفي</th>
                            <th>رقم الهوية</th>
                            <th>اسم الموظف</th>
                            <th>الجنسية</th>
                            <th>الجنس</th>
                            <th>الحالة</th>
                            <th>مدة الخدمة</th> <th>إجمالي الراتب</th>
                            <th>المسمى الوظيفي</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">تصدير تقرير مخصص</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= site_url('users1/export_dynamic_employees') ?>" method="POST" id="exportForm">
                <input type="hidden" name="<?= $csrf_token_name ?>" value="<?= $csrf_hash ?>">
                
                <input type="hidden" name="filter_employee_id" id="exp_employee_id">
                <input type="hidden" name="filter_name" id="exp_name">
                <input type="hidden" name="filter_id_number" id="exp_id_number">
                <input type="hidden" name="filter_status" id="exp_status">
                <input type="hidden" name="filter_company" id="exp_company">
                <input type="hidden" name="filter_department" id="exp_department">
                <input type="hidden" name="filter_position" id="exp_position">
                <input type="hidden" name="filter_manager" id="exp_manager">
                <input type="hidden" name="filter_location" id="exp_location">
                <input type="hidden" name="filter_nationality" id="exp_nationality">
                <input type="hidden" name="filter_gender" id="exp_gender">
                
                <div class="modal-body">
                    <p class="text-muted mb-3">اختر الأعمدة التي تريد إضافتها في ملف الإكسل:</p>
                    <div class="row">
                        <div class="col-12 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAllCols">
                                <label class="form-check-label fw-bold" for="selectAllCols">تحديد الكل</label>
                            </div>
                            <hr>
                        </div>
                        
                        <div class="col-md-4 mb-2"><div class="form-check"><input class="form-check-input col-chk" type="checkbox" name="columns[]" value="employee_id" checked><label class="form-check-label">الرقم الوظيفي</label></div></div>
                        <div class="col-md-4 mb-2"><div class="form-check"><input class="form-check-input col-chk" type="checkbox" name="columns[]" value="subscriber_name" checked><label class="form-check-label">اسم الموظف</label></div></div>
                        <div class="col-md-4 mb-2"><div class="form-check"><input class="form-check-input col-chk" type="checkbox" name="columns[]" value="id_number"><label class="form-check-label">رقم الهوية</label></div></div>
                        
                        <div class="col-md-4 mb-2"><div class="form-check"><input class="form-check-input col-chk" type="checkbox" name="columns[]" value="joining_date"><label class="form-check-label">تاريخ الانضمام</label></div></div>
                        <div class="col-md-4 mb-2"><div class="form-check"><input class="form-check-input col-chk" type="checkbox" name="columns[]" value="birth_date"><label class="form-check-label">تاريخ الميلاد</label></div></div>
                        <div class="col-md-4 mb-2"><div class="form-check"><input class="form-check-input col-chk" type="checkbox" name="columns[]" value="profession"><label class="form-check-label">المسمى الوظيفي</label></div></div>
                        
                        <div class="col-md-4 mb-2"><div class="form-check"><input class="form-check-input col-chk" type="checkbox" name="columns[]" value="department"><label class="form-check-label">القسم / الإدارة</label></div></div>
                        <div class="col-md-4 mb-2"><div class="form-check"><input class="form-check-input col-chk" type="checkbox" name="columns[]" value="location"><label class="form-check-label">الفرع / الموقع</label></div></div>
                        <div class="col-md-4 mb-2"><div class="form-check"><input class="form-check-input col-chk" type="checkbox" name="columns[]" value="manager"><label class="form-check-label">المدير المباشر</label></div></div>
                        
                        <div class="col-md-4 mb-2"><div class="form-check"><input class="form-check-input col-chk" type="checkbox" name="columns[]" value="email"><label class="form-check-label">البريد الشخصي</label></div></div>
                        <div class="col-md-4 mb-2"><div class="form-check"><input class="form-check-input col-chk" type="checkbox" name="columns[]" value="company_email"><label class="form-check-label">البريد الرسمي</label></div></div>
                        <div class="col-md-4 mb-2"><div class="form-check"><input class="form-check-input col-chk" type="checkbox" name="columns[]" value="mobile"><label class="form-check-label">رقم الجوال</label></div></div>
                        
                        <div class="col-12"><hr></div>
                        
                        <div class="col-md-4 mb-2"><div class="form-check"><input class="form-check-input col-chk" type="checkbox" name="columns[]" value="total_salary"><label class="form-check-label">إجمالي الراتب</label></div></div>
                        <div class="col-md-4 mb-2"><div class="form-check"><input class="form-check-input col-chk" type="checkbox" name="columns[]" value="base_salary"><label class="form-check-label">الراتب الأساسي</label></div></div>
                        <div class="col-md-4 mb-2"><div class="form-check"><input class="form-check-input col-chk" type="checkbox" name="columns[]" value="housing"><label class="form-check-label">بدل السكن</label></div></div>
                        
                        <div class="col-md-4 mb-2"><div class="form-check"><input class="form-check-input col-chk" type="checkbox" name="columns[]" value="bank_name"><label class="form-check-label">اسم البنك</label></div></div>
                        <div class="col-md-8 mb-2"><div class="form-check"><input class="form-check-input col-chk" type="checkbox" name="columns[]" value="iban"><label class="form-check-label">رقم الآيبان (IBAN)</label></div></div>
                        
                        <div class="col-md-4 mb-2"><div class="form-check"><input class="form-check-input col-chk" type="checkbox" name="columns[]" value="status"><label class="form-check-label">الحالة</label></div></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <button type="submit" class="btn btn-success">تصدير</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // Helper function to get status badge HTML
    function getStatusBadge(status) {
        let badgeClass = 'status-other';
        let displayText = status;
        
        if (status === 'active') {
            badgeClass = 'status-active';
            displayText = 'نشط';
        } else if (status === 'resigned') {
            badgeClass = 'status-resigned';
            displayText = 'منتهي الخدمة';
        } else if (status === 'deleted') {
            badgeClass = 'status-deleted';
            displayText = 'محذوف';
        }
        
        return `<span class="status-badge ${badgeClass}">${displayText}</span>`;
    }
    
    // --- SERVICE PERIOD CALCULATOR ---
    function calculateServicePeriod(joinDate) {
        if (!joinDate) return '—';
        
        // Handle different date formats (YYYY-MM-DD or YYYY/MM/DD)
        const dateStr = joinDate.replace(/\//g, '-');
        const start = new Date(dateStr);
        const end = new Date(); // Today
        
        if (isNaN(start.getTime())) return 'تاريخ غير صالح';
        
        // Difference logic
        let years = end.getFullYear() - start.getFullYear();
        let months = end.getMonth() - start.getMonth();
        let days = end.getDate() - start.getDate();
        
        if (days < 0) {
            months--;
            // Get days in previous month
            const prevMonth = new Date(end.getFullYear(), end.getMonth(), 0);
            days += prevMonth.getDate();
        }
        
        if (months < 0) {
            years--;
            months += 12;
        }
        
        const parts = [];
        if (years > 0) parts.push(years + (years > 10 ? ' سنة' : ' سنوات')); // Simple pluralization
        if (months > 0) parts.push(months + ' شهر');
        if (days > 0) parts.push(days + ' يوم');
        
        return parts.length > 0 ? parts.join(' و ') : 'أقل من يوم';
    }

    // 1. Initialize Select2
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
            dir: 'rtl',
            placeholder: 'اختر...',
            allowClear: true,
            width: '100%'
        });
    });

    // 2. Helper Functions
    function openExemptionPopup(empId) {
        const url = "<?php echo site_url('users1/view_emp'); ?>/" + encodeURIComponent(empId);
        window.open(url, '_blank');
    }
    
    function deleteEmployee(id) {
        if (confirm('هل أنت متأكد من حذف (أرشفة) هذا الموظف؟')) {
            const csrfName = $('#csrf-token').attr('name');
            const csrfHash = $('#csrf-token').val();
            var postData = { id: id };
            postData[csrfName] = csrfHash;

            $.ajax({
                url: "<?php echo site_url('users1/delete_employee'); ?>",
                type: 'POST',
                data: postData,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#csrf-token').val(response.csrf_hash);
                        alert(response.message);
                        $('.dataTables-example').DataTable().draw(false);
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) { alert('حدث خطأ في الاتصال بالخادم.'); }
            });
        }
    }

    function activateEmployee(id) {
        if (confirm('هل أنت متأكد من استعادة هذا الموظف؟')) {
            const csrfName = $('#csrf-token').attr('name');
            const csrfHash = $('#csrf-token').val();
            var postData = { id: id };
            postData[csrfName] = csrfHash;

            $.ajax({
                url: "<?php echo site_url('users1/activate_employee'); ?>",
                type: 'POST',
                data: postData,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#csrf-token').val(response.csrf_hash);
                        alert(response.message);
                        $('.dataTables-example').DataTable().draw(false);
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) { alert('حدث خطأ في الاتصال بالخادم.'); }
            });
        }
    }

    // Function to update export form with current filters
    function updateExportForm() {
        $('#exp_employee_id').val($('#filter_employee_id').val() || '');
        $('#exp_name').val($('#filter_name').val() || '');
        $('#exp_id_number').val($('#filter_id_number').val() || '');
        $('#exp_status').val($('#filter_status').val() || '');
        $('#exp_company').val($('#filter_company').val() || '');
        $('#exp_department').val($('#filter_department').val() || '');
        $('#exp_position').val($('#filter_position').val() || '');
        $('#exp_manager').val($('#filter_manager').val() || '');
        $('#exp_location').val($('#filter_location').val() || '');
        $('#exp_nationality').val($('#filter_nationality').val() || '');
        $('#exp_gender').val($('#filter_gender').val() || '');
    }

    function updateExportLink() {
        const baseUrl = "<?php echo site_url('users1/export_employees_excel'); ?>";
        const params = new URLSearchParams();
        const fields = [
            'filter_employee_id', 'filter_id_number', 'filter_name', 
            'filter_status', 'filter_company', 'filter_department', 'filter_position', 
            'filter_manager', 'filter_location', 'filter_nationality', 'filter_gender'
        ];

        fields.forEach(field => {
            const val = $('#' + field).val();
            if(val && val !== 'all') params.append(field, val);
        });

        $('#btn-export-excel').attr('href', baseUrl + '?' + params.toString());
    }

    // 3. DataTable & Main Logic
    $(document).ready(function () {
        var dt = $('.dataTables-example').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "pageLength": 25,
            "searching": false, 
            "ajax": {
                "url": "<?php echo site_url('users1/fetch_employees'); ?>",
                "type": "POST",
                "data": function (d) {
                    d.filter_employee_id = $('#filter_employee_id').val();
                    d.filter_id_number = $('#filter_id_number').val();
                    d.filter_name = $('#filter_name').val();
                    d.filter_status = $('#filter_status').val();
                    d.filter_company = $('#filter_company').val();
                    d.filter_department = $('#filter_department').val();
                    d.filter_position = $('#filter_position').val();
                    d.filter_manager = $('#filter_manager').val();
                    d.filter_location = $('#filter_location').val();
                    d.filter_nationality = $('#filter_nationality').val();
                    d.filter_gender = $('#filter_gender').val();
                    d['<?php echo $csrf_token_name; ?>'] = $('#csrf-token').val();
                },
                "dataSrc": function(json) {
                    if (json.csrf_hash) { 
                        $('#csrf-token').val(json.csrf_hash); 
                    }
                    updateExportLink();
                    return json.data;
                }
            },
            "columns": [
                { "data": "employee_id" },
                { "data": "id_number" },
                { "data": "subscriber_name" },
                { "data": "nationality" },
                { "data": "gender" },
                { "data": "status" },
                { "data": "joining_date" }, // Used for Service Period Calculation
                { "data": "total_salary" },
                { "data": "profession" },
                { "data": "actions", "orderable": false },
                { "data": "status", "visible": false }
            ],
            "columnDefs": [
                {
                    "targets": 0,
                    "render": function (data) { 
                        return `<a href="#" onclick="openExemptionPopup('${data}')" class="fw-bold">${data}</a>`; 
                    }
                },
                {
                    "targets": 5, // Status column
                    "render": function (data, type, row) {
                        return getStatusBadge(data || 'active');
                    }
                },
                {
                    "targets": 6, // Service Period Column
                    "render": function (data, type, row) {
                        // 'data' here is joining_date
                        if (row.status === 'active') {
                            return `<small class="text-primary fw-bold">${calculateServicePeriod(data)}</small>`;
                        } else {
                            return `<span class="text-muted">—</span>`;
                        }
                    }
                },
                {
                    "targets": 9, // Actions column
                    "render": function (data, type, row) {
                        var editUrl = "<?php echo site_url('users1/edit_employee/'); ?>" + row.id;
                        if (row.status === 'deleted') {
                            return `<div class="btn-group">
                                <a href="${editUrl}" class="btn btn-sm btn-info" title="تعديل"><i class="fa fa-pen"></i></a>
                                <button class="btn btn-sm btn-success" onclick="activateEmployee(${row.id})" title="استعادة">
                                    <i class="fa fa-user-check"></i>
                                </button>
                            </div>`;
                        } else {
                            return `<div class="btn-group">
                                <a href="${editUrl}" class="btn btn-sm btn-info" title="تعديل"><i class="fa fa-pen"></i></a>
                                <button class="btn btn-sm btn-danger" onclick="deleteEmployee(${row.id})" title="حذف">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>`;
                        }
                    }
                }
            ],
            "language": { 
                url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json' 
            }
        });

        // Search button
        $('#btn-search').on('click', function() { 
            dt.draw(); 
            updateExportLink();
        });
        
        // Clear button
        $('#btn-clear').on('click', function() {
            $('input[type="text"]').val('');
            $('select').val('').trigger('change');
            dt.draw();
            updateExportLink();
        });

        // Select all checkboxes in export modal
        $('#selectAllCols').on('change', function() {
            $('.col-chk').prop('checked', $(this).is(':checked'));
        });
        
        // Update export form when modal opens
        $('#exportModal').on('show.bs.modal', function() {
            updateExportForm();
        });

        // Update export link on filter changes
        $('input, select').on('change', updateExportLink);
        updateExportLink();
    });

    // 4. MISSING DATA BUTTON LOGIC
    $(document).on('click', '#btn-check-missing', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'جاري الفحص...',
            text: 'يتم الآن فحص ملفات الموظفين بحثاً عن بيانات ناقصة.',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        const csrfName = $('#csrf-token').attr('name');
        const csrfHash = $('#csrf-token').val();
        var postData = {};
        postData[csrfName] = csrfHash;

        $.ajax({
            url: "<?php echo site_url('users1/check_missing_data'); ?>",
            type: 'POST',
            data: postData,
            dataType: 'json',
            success: function(response) {
                if (response.csrf_hash) { 
                    $('#csrf-token').val(response.csrf_hash); 
                }

                if (response.status === 'success') {
                    if (response.data.length === 0) {
                        Swal.fire('ممتاز!', 'جميع ملفات الموظفين النشطين مكتملة.', 'success');
                    } else {
                        let htmlContent = '<div class="table-responsive" style="max-height: 400px; overflow-y: auto; text-align: right;">';
                        htmlContent += '<table class="table table-bordered table-sm text-start" dir="rtl">';
                        htmlContent += '<thead class="table-dark"><tr><th>الرقم</th><th>الموظف</th><th>الحقول الناقصة</th><th>تعديل</th></tr></thead><tbody>';
                        
                        response.data.forEach(function(item) {
                            let missingTags = item.missing.map(f => `<span class="badge bg-danger me-1">${f}</span>`).join(' ');
                            htmlContent += `<tr>
                                <td>${item.emp_id}</td>
                                <td>${item.name}</td>
                                <td>${missingTags}</td>
                                <td>
                                    <a href="javascript:void(0);" onclick="openExemptionPopup('${item.emp_id}')" class="btn btn-sm btn-primary">
                                        <i class="fa fa-pen"></i>
                                    </a>
                                </td>
                            </tr>`;
                        });
                        htmlContent += '</tbody></table></div>';

                        Swal.fire({
                            title: `تم العثور على ${response.data.length} ملفات ناقصة`,
                            html: htmlContent,
                            icon: 'warning',
                            width: '800px',
                            confirmButtonText: 'إغلاق'
                        });
                    }
                } else {
                    Swal.fire('خطأ', 'حدث خطأ أثناء جلب البيانات.', 'error');
                }
            },
            error: function() { 
                Swal.fire('خطأ', 'فشل الاتصال بالخادم.', 'error'); 
            }
        });
    });

    // 5. RESIGNED EMPLOYEES BUTTON LOGIC
    $(document).on('click', '#btn-check-resigned', function(e) {
        e.preventDefault(); 

        if (typeof Swal === 'undefined') {
            alert('Error: SweetAlert2 library is missing.');
            return;
        }

        // 1. Define the Modal Content Structure
        const modalContent = `
            <div class="row g-2 mb-3 align-items-end text-end" dir="rtl">
                <div class="col-md-5">
                    <label class="form-label small fw-bold">من تاريخ (آخر يوم عمل)</label>
                    <input type="date" id="res_start_date" class="form-control form-control-sm">
                </div>
                <div class="col-md-5">
                    <label class="form-label small fw-bold">إلى تاريخ</label>
                    <input type="date" id="res_end_date" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <button type="button" id="btn-filter-resigned-action" class="btn btn-primary btn-sm w-100">
                        <i class="fa fa-search"></i> بحث
                    </button>
                </div>
            </div>
            <div id="resigned-results-container">
                <div class="text-center py-3"><div class="spinner-border text-primary"></div><p>جاري التحميل...</p></div>
            </div>
        `;

        // 2. Open Swal with HTML content
        Swal.fire({
            title: 'الموظفين المنتهية خدماتهم',
            html: modalContent,
            width: '95%',
            maxWidth: '1200px',
            showCloseButton: true,
            showConfirmButton: false, // We use custom close
            didOpen: () => {
                // Load default data (All past due)
                fetchResignedData();

                // Bind Filter Button Click
                $('#btn-filter-resigned-action').on('click', function() {
                    fetchResignedData();
                });
            }
        });

        // 3. Helper Function to Fetch Data
        function fetchResignedData() {
            const start = $('#res_start_date').val();
            const end = $('#res_end_date').val();
            
            const csrfName = $('#csrf-token').attr('name');
            const csrfHash = $('#csrf-token').val();
            
            $('#resigned-results-container').html('<div class="text-center py-3"><div class="spinner-border text-primary"></div></div>');

            $.ajax({
                url: "<?php echo site_url('users1/check_resigned_employees'); ?>",
                type: 'POST',
                data: {
                    start_date: start,
                    end_date: end,
                    [csrfName]: csrfHash
                },
                dataType: 'json',
                success: function(response) {
                    if (response.csrf_hash) { 
                        $('#csrf-token').val(response.csrf_hash); 
                    }

                    if (response.status === 'success') {
                        if (response.count === 0) {
                            $('#resigned-results-container').html('<div class="alert alert-info">لا توجد نتائج في هذه الفترة.</div>');
                        } else {
                            // Build Table
                            let htmlTable = '<div class="table-responsive" style="max-height: 50vh; overflow-y: auto;">';
                            htmlTable += '<table class="table table-bordered table-striped table-hover text-end" dir="rtl" style="font-size: 0.9rem;">';
                            htmlTable += '<thead class="table-dark" style="position: sticky; top: 0;"><tr><th>الرقم</th><th>الموظف</th><th>آخر يوم عمل</th><th>الحالة الحالية</th><th>اجراء</th></tr></thead><tbody>';
                            
                            response.data.forEach(function(item) {
                                let statusBadge = getStatusBadge(item.current_status || 'active');
                                
                                htmlTable += `<tr>
                                    <td><strong>${item.emp_id}</strong></td>
                                    <td>${item.emp_name}</td>
                                    <td dir="ltr">${item.date_of_the_last_working}</td>
                                    <td>${statusBadge}</td>
                                    <td class="text-center">
                                        <a href="javascript:void(0);" onclick="openExemptionPopup('${item.emp_id}')" class="btn btn-sm btn-primary">
                                            <i class="fa fa-eye"></i> الملف
                                        </a>
                                    </td>
                                </tr>`;
                            });
                            htmlTable += '</tbody></table></div>';
                            
                            $('#resigned-results-container').html(htmlTable);
                        }
                    } else {
                        $('#resigned-results-container').html('<div class="alert alert-danger">حدث خطأ: ' + response.message + '</div>');
                    }
                },
                error: function() {
                    $('#resigned-results-container').html('<div class="alert alert-danger">فشل الاتصال بالخادم.</div>');
                }
            });
        }
    });
</script>
</body>
</html>