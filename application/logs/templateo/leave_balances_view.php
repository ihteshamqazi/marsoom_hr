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
    </style>
</head>
<body>

<div class="top-actions">
    <a href="javascript:history.back()"><i class="fas fa-arrow-right"></i><span>رجوع</span></a>
    <a href="<?php echo site_url('dashboard'); ?>"><i class="fas fa-home"></i><span>الرئيسية</span></a>
</div>

<div class="container-fluid p-4">
    <div class="text-center mb-4"><h1 class="page-title">إدارة أرصدة الإجازات</h1></div>
    <div class="card table-card">
        <div class="card-header bg-transparent pt-3 px-3 pb-0 border-bottom-0">
            <ul class="nav nav-tabs" id="balanceTabs" role="tablist">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#view-pane">عرض الأرصدة</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#manual-pane">إدخال/تحديث يدوي</button></li>
            </ul>
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
                                            <span class="view-mode"><?php echo (float)$balance['balance_allotted']; ?></span>
                                            <input type="number" class="form-control form-control-sm edit-input" name="balance_allotted" value="<?php echo (float)$balance['balance_allotted']; ?>">
                                        </td>
                                        <td>
                                            <span class="view-mode"><?php echo (float)$balance['balance_consumed']; ?></span>
                                            <input type="number" class="form-control form-control-sm edit-input" name="balance_consumed" value="<?php echo (float)$balance['balance_consumed']; ?>">
                                        </td>
                                        <td><span class="badge bg-primary fs-6 remaining-balance-badge"><?php echo (float)$balance['remaining_balance']; ?></span></td>
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
                     </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>

<script>
$(document).ready(function () {
    $('.dataTables-example').DataTable({ responsive: true, pageLength: 25, language: { url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json' }});

    var originalValues = {};

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
        
        // ✅ **FIX:** Build the payload with the composite key from data attributes
        var payload = {
            employee_id: row.data('employee-id'),
            leave_type_slug: row.data('slug'),
            year: row.data('year'),
            balance_allotted: allotted,
            balance_consumed: consumed,
            '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
        };

        $.ajax({
            url: "<?= site_url('users1/update_leave_balance'); ?>",
            type: 'POST', data: payload, dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var newRemaining = parseFloat(allotted) - parseFloat(consumed);
                    row.find('.view-mode').eq(0).text(allotted);
                    row.find('.view-mode').eq(1).text(consumed);
                    row.find('.remaining-balance-badge').text(newRemaining.toFixed(1));
                    row.find('.edit-input, .edit-mode-buttons').hide();
                    row.find('.view-mode, .view-mode-buttons').show();
                } else { alert('Error: ' + response.message); }
                $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val(response.csrf_hash);
            },
            error: function() { alert('An error occurred connecting to the server.'); }
        });
    });

    $('#manualBalanceForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var alertContainer = $('#manual-alert-container');

        $.ajax({
            url: "<?= site_url('users1/add_manual_balance'); ?>",
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alertContainer.html('<div class="alert alert-success">' + response.message + '</div>');
                    form[0].reset();
                } else {
                    alertContainer.html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function() {
                alertContainer.html('<div class="alert alert-danger">حدث خطأ في الاتصال بالخادم.</div>');
            }
        });
    });
});
</script>

</body>
</html>