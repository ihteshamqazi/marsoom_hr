<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير مخالفات الموظفين</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.css">

    <style>
        :root { --marsom-blue: #001f3f; --marsom-orange: #FF8C00; --text-light: #ffffff; --text-dark: #343a40; }
        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, var(--marsom-blue) 0%, #34495e 50%, var(--marsom-orange) 100%);
            background-size: 400% 400%;
            animation: gradientAnimation 20s ease infinite;
        }
        @keyframes gradientAnimation { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
        .main-container { padding: 30px 15px; }
        .top-actions a{background:rgba(255,255,255,.12);border:1px solid var(--glass-border);color:#fff;text-decoration:none;border-radius:10px;padding:8px 14px;display:inline-flex;align-items:center;gap:8px;transition:.25s}
        .page-title { font-family: 'El Messiri', sans-serif; font-weight: 700; font-size: 2.8rem; color: var(--text-light); margin-bottom: 40px; text-align: center; text-shadow: 0 3px 6px rgba(0, 0, 0, 0.4); }
        .search-card, .table-card { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(8px); border-radius: 15px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15); padding: 25px; margin-bottom: 30px; }
        .dataTables-example thead th { background-color: var(--marsom-blue) !important; color: var(--text-light); text-align: center; vertical-align: middle; }
        .dataTables-example tbody td { text-align: center; vertical-align: middle; }
        .modal-header { background-color: var(--marsom-blue); color: var(--text-light); }
        .modal-header .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }
        .modal-title { font-family: 'El Messiri', sans-serif; }
        .clickable-row { cursor: pointer; color: var(--marsom-blue); font-weight: 500; text-decoration: none; }
        .clickable-row:hover { text-decoration: underline; }
        .dt-button-spacer {
    margin-left: 15px !important;
}
    </style>
</head>
<body>

<div class="main-container container-fluid">
    <div class="text-center">
        <div class="top-actions" style="color:black;">
        <a href="<?php echo site_url('users1/main_hr1'); ?>" style="text-decoration: none;"><i class="fas fa-home"></i> الرئيسية</a>
        
    </div>
        <h1 class="page-title">تقرير مخالفات الموظفين</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card search-card">
                <div class="card-body">
                    <h5 class="card-title text-center mb-4">البحث في التقرير</h5>
                    <form method="GET" action="<?php echo site_url('users1/violations'); ?>">
                        <div class="alert alert-info d-flex justify-content-between align-items-center">
    <span>لتصحيح دقائق التأخير بسبب إجازات نصف اليوم، اضغط على زر المعالجة.</span>
    <button id="recalculate-btn" class="btn btn-primary fw-bold" data-sheet-id="<?php echo html_escape($selected_sheet_id); ?>">
        <i class="fas fa-sync-alt me-2"></i>معالجة وتصحيح دقائق التأخير
    </button>
</div>
                        <div class="row g-3 align-items-end">
                            <div class="col-md-5">
                                <label for="sheet_id" class="form-label">مسير الرواتب</label>
                                <select name="sheet_id" id="sheet_id" class="form-select form-select-lg" required>
                                    <option value="" disabled <?php echo empty($selected_sheet_id) ? 'selected' : ''; ?>>-- يرجى اختيار فترة المسير --</option>
                                    <?php if (!empty($all_salary_sheets)): ?>
                                        <?php foreach($all_salary_sheets as $sheet): ?>
                                            <option value="<?php echo $sheet['id']; ?>" <?php echo ($selected_sheet_id == $sheet['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($sheet['type'] . ' (' . $sheet['start_date'] . ' to ' . $sheet['end_date'] . ')', ENT_QUOTES); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="emp_id" class="form-label">الرقم الوظيفي</label>
                                <input type="text" name="emp_id" id="emp_id" class="form-control form-control-lg" placeholder="اختياري" value="<?php echo htmlspecialchars($selected_emp_id ?? '', ENT_QUOTES); ?>">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary btn-lg w-100"><i class="fa fa-search"></i> عرض التقرير</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($selected_sheet_id)): ?>
    <div class="row">
        <div class="col-12">
            <div class="card table-card">
                <div class="card-body">
                    <div class="table-responsive">
                        <input type="hidden" id="csrf-token-name" value="<?= $csrf_token_name ?>">
                        <input type="hidden" id="csrf-token-hash" value="<?= $csrf_hash ?>">

                        <table class="table table-striped table-bordered table-hover dataTables-example" style="width:100%">
                            <thead>
                                <tr>
                                    <th>الإجراء</th>
                                    <th>اسم الموظف</th>
                                    <th>ايام الغياب</th>
                                    <th>دقائق التأخير</th>
                                    <th>دقائق الخروج المبكر</th>
                                    <th>أيام بصمة منفردة</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($get_violations_summary as $row): ?>
                                <tr>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info" onclick="openExemptionPopup('<?php echo $row->emp_id; ?>', '<?php echo $selected_sheet_id; ?>')" title="إعفاء الموظف من مخالفة">
                                            <i class="fa fa-shield-halved"></i> إعفاء
                                        </button>
                                    </td>
                                    <td>
                                        <a href="#" class="clickable-row" data-bs-toggle="modal" data-bs-target="#detailsModal" data-empid="<?php echo $row->emp_id; ?>" data-empname="<?php echo htmlspecialchars($row->emp_name, ENT_QUOTES); ?>">
                                            <?php echo $row->emp_name; ?> (<?php echo $row->emp_id; ?>)
                                        </a>
                                    </td>
                                    <td><?php echo $row->absence; ?></td>
                                    <td><?php echo $row->minutes_late; ?></td>
                                    <td><?php echo $row->minutes_early; ?></td>
                                    <td><?php echo $row->single_thing; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel">تفاصيل الحضور للموظف: <span id="modal-emp-name"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modal-loader" class="text-center p-5"><div class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></div></div>
                <div class="table-responsive" id="modal-table-container" style="display:none;">
                    <table class="table table-bordered table-hover" id="details-table">
                        <thead><tr><th>التاريخ</th><th>اليوم</th><th>الدخول</th><th>الخروج</th><th>الحالة</th><th>تفاصيل المخالفة</th></tr></thead>
                        <tbody id="details-table-body"></tbody>
                    </table>
                </div>
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
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.colVis.min.js"></script>

<script>
// ✅ CONSOLIDATED AND CORRECTED SCRIPT
$(document).ready(function () {
    // Correctly get the sheet_id from the PHP variable passed by the controller
    var id_sheet = <?php echo json_encode($selected_sheet_id); ?>;

    $('.dataTables-example').DataTable({
        responsive: true,
        pageLength: 25,
        language: { "url": "https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json" },
        layout: {
            topStart: {
                buttons: [
    // Standard Export Buttons (Primary Colors)
    { 
        extend: 'excel', 
        text: '<i class="fa fa-file-excel me-1"></i> إكسل',
        className: 'btn-success' // Green for Excel
    },
    { 
        extend: 'print', 
        text: '<i class="fa fa-print me-1"></i> طباعة',
        className: 'btn-info' // Light blue for Print
    },
    { 
        extend: 'colvis', 
        text: '<i class="fa fa-eye me-1"></i> إظهار/إخفاء الأعمدة',
        className: 'btn-secondary' // Grey for Column Visibility
    },

    // Spacer between button groups
    {
        text: '',
        className: 'dt-button-spacer' 
    },

    // Bulk Action Buttons (Specific Colors)
   <?php if(in_array($this->session->userdata('username') ?? '', array('1835', '1001','2901','2774'))): ?>
{
    text: '<i class="fa fa-fingerprint me-1"></i> إعفاء من بصمة منفردة',
    className: 'btn-primary',
    action: () => sendBulkExemption(1, 'إعفاء جميع الموظفين من مخالفة البصمة المنفردة؟')
},
<?php endif; ?>
    { 
        text: '<i class="fa fa-clock me-1"></i> إعفاء من التأخير/المبكر', 
        className: 'btn-primary', // Standard blue
        action: () => sendBulkExemption(2, 'إعفاء جميع الموظفين من مخالفات التأخير أو الخروج المبكر؟') 
    },

    { 
        text: '<i class="fa fa-user-xmark me-1"></i> إعفاء من الغياب', 
        className: 'btn-primary', // Standard blue
        action: () => sendBulkExemption(3, 'إعفاء جميع الموظفين من الغياب؟') 
    },
     <?php if(in_array($this->session->userdata('username') ?? '', array('1835', '1001','2901','2774'))): ?>
    { 
        text: '<i class="fa fa-broom me-1"></i> إعفاء من الكل', 
        className: 'btn-danger', // Red for the "all" action
        action: () => sendBulkExemption(4, 'إعفاء جميع الموظفين من جميع المخالفات؟') 
    }
    <?php endif; ?>

]
            }
        }
    });

    function sendBulkExemption(type, confirmMsg) {
        if (!confirm(confirmMsg)) return;

        var $btns = $('.dt-buttons .btn').prop('disabled', true);
        
        // Prepare data with CSRF token
        var postData = { type: type };
        postData[$('#csrf-token-name').val()] = $('#csrf-token-hash').val();

        $.ajax({
            url: '<?php echo site_url("users1/bulk_exempt_attendance_summary/"); ?>' + id_sheet,
            type: 'POST',
            data: postData,
            dataType: 'json',
            success: function (res) {
                if (res && res.status === 'ok') {
                    alert('تم التنفيذ بنجاح. عدد السجلات المتأثرة: ' + (res.affected || 0));
                    location.reload(); // Reload the page to see changes
                } else {
                    alert('تعذر التنفيذ: ' + (res && res.msg ? JSON.stringify(res.msg) : 'خطأ غير معروف'));
                }
            },
            error: function () {
                alert('تعذر الاتصال بالخادم.');
            },
            complete: function () {
                $btns.prop('disabled', false);
            }
        });
    }

    // --- Modal Logic (as before) ---
    $('.clickable-row').on('click', function(e) {
        e.preventDefault();
        const empId = $(this).data('empid');
        const empName = $(this).data('empname');
        $('#modal-emp-name').text(empName);
        $('#details-table-body').empty();
        $('#modal-table-container').hide();
        $('#modal-loader').show();
        
        $.ajax({
            url: "<?php echo site_url('users1/get_employee_violation_details'); ?>",
            type: 'POST',
            data: { emp_id: empId, sheet_id: id_sheet },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' && Array.isArray(response.data) && response.data.length > 0) {
                    response.data.forEach(day => {
                        const violationText = day.violation_details.length > 0 ? day.violation_details.join('<br>') : '—';
                        const rowHtml = `<tr><td>${day.date}</td><td>${day.day_name}</td><td>${day.check_in}</td><td>${day.check_out}</td><td>${day.status}</td><td>${violationText}</td></tr>`;
                        $('#details-table-body').append(rowHtml);
                    });
                } else {
                    $('#details-table-body').append('<tr><td colspan="6" class="text-center">لا توجد بيانات تفصيلية.</td></tr>');
                }
            },
            error: function() {
                $('#details-table-body').append('<tr><td colspan="6" class="text-center">فشل في الاتصال بالخادم.</td></tr>');
            },
            complete: function() {
                $('#modal-loader').hide();
                $('#modal-table-container').show();
            }
        });
    });
});
// In templateo/violations.php, inside <script> tag
$(document).ready(function() {
    $('#recalculate-btn').on('click', function() {
        var sheetId = $(this).data('sheet-id');
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
        var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "سيقوم النظام بإعادة حساب دقائق التأخير لجميع الموظفين في هذا المسير.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم، قم بالمعالجة!',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?php echo site_url('users1/recalculate_payroll_violations/'); ?>" + sheetId,
                    type: 'POST',
                    data: {[csrfName]: csrfHash},
                    dataType: 'json',
                    beforeSend: function() {
                        Swal.fire({
                            title: 'جاري المعالجة...',
                            text: 'الرجاء الانتظار.',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'نجاح!',
                                text: response.message,
                                icon: 'success'
                            }).then(() => {
                                location.reload(); // Reload the page to show corrected data
                            });
                        } else {
                            Swal.fire('خطأ!', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('خطأ فادح!', 'حدث خطأ غير متوقع أثناء الاتصال بالخادم.', 'error');
                    }
                });
            }
        });
    });
});
function openExemptionPopup(empId, sheetId) {
    const url = `<?php echo site_url('users1/exemption'); ?>/${empId}/${sheetId}`;
    window.open(url, 'ExemptionPopup', `width=600,height=400,left=${(screen.width/2)-300},top=${(screen.height/2)-200}`);
}
</script>
</body>
</html>