<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة أرصدة الإجازات</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <style>
        :root{--marsom-blue:#001f3f;--marsom-orange:#FF8C00;}
        body{font-family:'Tajawal',sans-serif;background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);background-size:400% 400%;animation:grad 20s ease infinite;color:#343a40;}
        @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        .page-title{font-family:'El Messiri',sans-serif;font-weight:700;color:#fff;text-align:center;margin-bottom:2rem;font-size:2.8rem;text-shadow:0 3px 6px rgba(0,0,0,.4)}
        .table-card{background:rgba(255,255,255,.95);backdrop-filter:blur(8px);border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,.15);padding:25px}
        .dataTables-example thead th{background-color:#001f3f !important;color:#fff;}
        .top-actions{position:fixed;top:12px;right:12px;display:flex;gap:10px;z-index:5}
        .top-actions a{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);color:#fff;text-decoration:none;border-radius:10px;padding:8px 14px;display:inline-flex;align-items:center;gap:8px;}
        .nav-tabs .nav-link { color: var(--marsom-blue); font-weight: 700; }
        .nav-tabs .nav-link.active { color: var(--marsom-orange); border-color: var(--marsom-orange) var(--marsom-orange) #fff; }
        .edit-input { display: none; width: 80px; text-align: center; }
        .view-mode-buttons .btn, .edit-mode-buttons .btn { width: 40px; }
        #employee_id {
            max-height: 200px; 
            overflow-y: auto; 
        }
    </style>
</head>
<body>

<div class="top-actions">
    <a href="javascript:history.back()"><i class="fas fa-arrow-right"></i><span>رجوع</span></a>
    <a href="<?php echo site_url('users1/main_hr1'); ?>"><i class="fas fa-home"></i><span>الرئيسية</span></a>
</div>

<div class="container-fluid p-4">
    <div class="text-center mb-4"><h1 class="page-title">إدارة أرصدة الإجازات</h1></div>
    <div class="card table-card">
        <div class="card-header bg-transparent pt-3 px-3 pb-0 border-bottom-0">
            <div class="d-flex justify-content-between align-items-center">
                <ul class="nav nav-tabs" id="balanceTabs" role="tablist">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#view-pane">عرض الأرصدة</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#manual-pane">إدخال/تحديث يدوي</button></li>
                </ul>
                <a href="<?= site_url('users1/export_balances_excel') ?>" class="btn btn-success"><i class="fa fa-file-excel me-2"></i>تصدير إكسل</a>
            </div>
        </div>
        <div class="card-body">
            <div class="tab-content" id="balanceTabsContent">
                <div class="tab-pane fade show active" id="view-pane">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example" style="width:100%">
                            <thead>
                                <tr>
                                    <th>الرقم الوظيفي</th><th>اسم الموظف</th><th>نوع الإجازة</th><th>المخصص</th>
                                    <th>المستهلك</th><th>المتبقي</th><th>السنة</th><th>إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($balances as $balance) : ?>
                                    <tr data-employee-id="<?= htmlspecialchars($balance['employee_id']); ?>" 
                                        data-slug="<?= htmlspecialchars($balance['leave_type_slug']); ?>" 
                                        data-year="<?= htmlspecialchars($balance['year']); ?>">
                                        <td><?php echo htmlspecialchars($balance['employee_id']); ?></td>
                                        <td><?php echo htmlspecialchars($balance['subscriber_name']); ?></td>
                                        <td><?php echo htmlspecialchars($balance['leave_type_name']); ?></td>
                                        <td>
                                            <span class="view-mode"><?php echo number_format((float)$balance['balance_allotted'], 2); ?></span>
                                            <input type="number" step="any" class="form-control form-control-sm edit-input" name="balance_allotted" value="<?php echo (float)$balance['balance_allotted']; ?>">
                                        </td>
                                        <td>
                                            <span class="view-mode"><?php echo number_format((float)$balance['balance_consumed'], 2); ?></span>
                                            <input type="number" step="any" class="form-control form-control-sm edit-input" name="balance_consumed" value="<?php echo (float)$balance['balance_consumed']; ?>">
                                        </td>
                                        <td><span class="badge bg-primary fs-6 remaining-balance-badge"><?php echo number_format((float)$balance['remaining_balance'], 2); ?></span></td>
                                        <td><?php echo htmlspecialchars($balance['year']); ?></td>
                                        <td>
                                            <div class="view-mode-buttons"><button class="btn btn-sm btn-info btn-edit" title="تعديل"><i class="fa fa-pen"></i></button></div>
                                            <div class="edit-mode-buttons" style="display: none;"><button class="btn btn-sm btn-success btn-save" title="حفظ"><i class="fa fa-check"></i></button><button class="btn btn-sm btn-secondary btn-cancel" title="إلغاء"><i class="fa fa-times"></i></button></div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="tab-pane fade" id="manual-pane">
                    <div id="manual-alert-container"></div>
                    <div class="row">
                        <div class="col-md-5">
                            <h5><i class="fa fa-user-plus me-2"></i>إدخال/تحديث رصيد فردي</h5>
                            <form id="manualBalanceForm">
                                <input type="hidden" name="<?= $csrf_name; ?>" value="<?= $csrf_hash; ?>">
                                
                                <div class="mb-2">
                                    <label for="employee_search_input" class="form-label">بحث عن موظف</label>
                                    <input type="text" class="form-control" id="employee_search_input" placeholder="اكتب اسم الموظف أو رقمه للبحث...">
                                </div>

                                <div class="mb-3">
                                    <label for="employee_id" class="form-label">الموظف</label>
                                    <select class="form-select" id="employee_id" name="employee_id" required>
                                        <option value="">اختر موظف...</option>
                                        <?php if (!empty($all_employees)): ?>
                                            <?php foreach($all_employees as $emp): ?>
                                                <option value="<?= htmlspecialchars($emp['username']); ?>"><?= htmlspecialchars($emp['name']); ?> (<?= htmlspecialchars($emp['username']); ?>)</option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="leave_type_slug" class="form-label">نوع الإجازة</label>
                                    <select class="form-select" id="leave_type_slug" name="leave_type_slug" required>
                                        <option value="">اختر نوع الإجازة...</option>
                                        <?php if (!empty($leave_types)): ?>
                                            <?php foreach($leave_types as $type): ?>
                                                <option value="<?= htmlspecialchars($type['slug']); ?>"><?= htmlspecialchars($type['name_ar']); ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="balance_allotted" class="form-label">الرصيد المخصص</label>
                                            <input type="number" step="any" class="form-control" id="balance_allotted" name="balance_allotted" required>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="year" class="form-label">السنة</label>
                                            <input type="number" class="form-control" id="year" name="year" value="<?= date('Y'); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save me-2"></i>حفظ الرصيد</button>
                            </form>
                        </div>
                        <div class="col-md-7 border-end">
                            <h5><i class="fa fa-file-upload me-2"></i>رفع ملف أرصدة (Excel)</h5>
                            <p>قم برفع ملف Excel. سيتم تحديث الأرصدة الحالية أو إضافة أرصدة جديدة بناءً على الرقم الوظيفي ونوع الإجازة والسنة.</p>
                            <form id="uploadBalanceForm" enctype="multipart/form-data">
                                <input type="hidden" name="<?= $csrf_name; ?>" value="<?= $csrf_hash; ?>">
                                <div class="mb-3">
                                    <label for="balance_file" class="form-label">اختر ملف (.xlsx)</label>
                                    <input class="form-control" type="file" id="balance_file" name="balance_file" accept=".xlsx" required>
                                </div>
                                <div class="mb-3">
                                    <small>يجب أن يحتوي الملف على الأعمدة التالية: <strong>employee_id</strong>, <strong>leave_type_slug</strong>, <strong>balance_allotted</strong>, <strong>year</strong></small>
                                </div>
                                <button type="submit" class="btn btn-success"><i class="fa fa-upload me-2"></i>رفع ومعالجة الملف</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="csrf-token-main" name="<?= $csrf_name; ?>" value="<?= $csrf_hash; ?>">

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>

<script>
$(document).ready(function () {
    $('.dataTables-example').DataTable({ 
        responsive: true, 
        pageLength: 25, 
        language: { url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json' }
    });

    var originalValues = {};
    var csrfName = $('#csrf-token-main').attr('name');
    var csrfHash = $('#csrf-token-main').val();

    $('.dataTables-example').on('click', '.btn-edit', function() {
        var row = $(this).closest('tr');
        var key = `${row.data('employee-id')}-${row.data('slug')}-${row.data('year')}`;
        originalValues[key] = {
            allotted: row.find('input[name="balance_allotted"]').val(),
            consumed: row.find('input[name="balance_consumed"]').val()
        };
        row.find('.view-mode, .view-mode-buttons').hide();
        row.find('.edit-input, .edit-mode-buttons').show();
    });

    $('.dataTables-example').on('click', '.btn-cancel', function() {
        var row = $(this).closest('tr');
        var key = `${row.data('employee-id')}-${row.data('slug')}-${row.data('year')}`;
        var original = originalValues[key];
        if (original) {
            row.find('input[name="balance_allotted"]').val(original.allotted);
            row.find('input[name="balance_consumed"]').val(original.consumed);
        }
        row.find('.edit-input, .edit-mode-buttons').hide();
        row.find('.view-mode, .view-mode-buttons').show();
    });

    $('.dataTables-example').on('click', '.btn-save', function() {
        var row = $(this).closest('tr');
        var allotted = row.find('input[name="balance_allotted"]').val();
        var consumed = row.find('input[name="balance_consumed"]').val();
        
        var payload = {
            employee_id: row.data('employee-id'),
            leave_type_slug: row.data('slug'),
            year: row.data('year'),
            balance_allotted: allotted,
            balance_consumed: consumed
        };
        payload[csrfName] = csrfHash; 

        $.ajax({
            url: "<?= site_url('users1/update_leave_balance'); ?>",
            type: 'POST', data: payload, dataType: 'json',
            success: function(response) {
                csrfHash = response.csrf_hash;
                $('#csrf-token-main').val(csrfHash);
                $('input[name="'+csrfName+'"]').val(csrfHash); 
                
                if (response.status === 'success') {
                    var newRemaining = parseFloat(allotted) - parseFloat(consumed);
                    // row.find('.view-mode').eq(0).text(parseFloat(allotted).toFixed(2));
                    row.find('.view-mode').eq(1).text(parseFloat(consumed).toFixed(2));
                    row.find('.remaining-balance-badge').text(newRemaining.toFixed(2)); 
                    
                    row.find('.edit-input, .edit-mode-buttons').hide();
                    row.find('.view-mode, .view-mode-buttons').show();
                } else { 
                    alert('Error: ' + response.message); 
                }
            },
            error: function() { alert('An error occurred connecting to the server.'); }
        });
    });

    $('#manualBalanceForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var alertContainer = $('#manual-alert-container');
        
        var formData = form.serializeArray();
        var csrfFound = false;
        for (var i = 0; i < formData.length; i++) {
            if (formData[i].name === csrfName) {
                formData[i].value = csrfHash;
                csrfFound = true;
                break;
            }
        }
        if (!csrfFound) {
            formData.push({name: csrfName, value: csrfHash});
        }

        $.ajax({
            url: "<?= site_url('users1/add_manual_balance'); ?>",
            type: 'POST',
            data: $.param(formData),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alertContainer.html('<div class="alert alert-success">' + response.message + ' (يرجى تحديث الصفحة لرؤية التغيير في الجدول)</div>');
                    form[0].reset();
                    $('#employee_search_input').val(''); 
                    $('#employee_id option').show(); 
                } else {
                    alertContainer.html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function() {
                alertContainer.html('<div class="alert alert-danger">حدث خطأ في الاتصال بالخادم.</div>');
            }
        });
    });

    $('#uploadBalanceForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        var alertContainer = $('#manual-alert-container');
        
        formData.set(csrfName, csrfHash);

        $.ajax({
            url: "<?= site_url('users1/upload_balance_sheet'); ?>",
            type: 'POST',
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.status === 'success') {
                    alertContainer.html('<div class="alert alert-success">' + response.message + ' (يرجى تحديث الصفحة لرؤية التغييرات)</div>');
                } else {
                    alertContainer.html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function() {
                alertContainer.html('<div class="alert alert-danger">حدث خطأ في الاتصال بالخادم أو فشل رفع الملف.</div>');
            }
        });
    });

    $('#employee_search_input').on('input', function() {
        var searchTerm = $(this).val().toLowerCase().trim();
        $('#employee_id option').each(function() {
            var option = $(this);
            var optionText = option.text().toLowerCase();
            
            if (option.val() === "") {
                option.show();
                return; 
            }
            
            if (optionText.includes(searchTerm)) {
                option.show();
            } else {
                option.hide();
            }
        });
        
        if ($('#employee_id option:selected').is(':hidden')) {
             $('#employee_id').val('');
        }
    });
});
</script>

</body>
</html>