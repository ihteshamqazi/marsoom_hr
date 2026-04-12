<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>مستحقات نهاية الخدمة</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<style>
    @page { size: A4; margin: 12mm 12mm 14mm 12mm; }
    html, body { font-family:'Tajawal',system-ui,sans-serif; background:#f4f6f9; color:#111; }
    .a4-sheet { width:210mm; min-height:297mm; margin:10mm auto; background:#fff; box-shadow:0 6px 24px rgba(0,0,0,.08); padding:14mm; }
    .no-print { display:flex; flex-wrap:wrap; gap:.5rem; justify-content:center; margin:12px auto; padding: 0 14mm; }
    .btn { border:1px solid #001f3f; color:#001f3f; background:#fff; padding:8px 14px; border-radius:8px; font-weight:700; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:8px; }
    .btn:hover { background:#001f3f; color:#fff; }
    .btn-success { border-color:#198754; color:#198754; background-color: #fff;}
    .btn-success:hover { background:#198754; color:#fff; }
    .btn-danger { border-color:#dc3545; color:#dc3545; background-color: #fff;}
    .btn-danger:hover { background:#dc3545; color:#fff; }
    .header { display:flex; align-items:center; justify-content:space-between; gap:16px; padding-bottom:10px; border-bottom:2px solid #001f3f; margin-bottom:14px; }
    .brand h1 { font-size:20px; margin:0; font-weight:800; color:#001f3f; }
    .section-title { margin:14px 0 8px; padding:8px 10px; background:#f7f9fc; border-right:4px solid #FF8C00; font-weight:800; }
    .print-table { width:100%; border-collapse:collapse; margin-top:6px; }
    .print-table th, .print-table td { border:1px solid #d9dee3; padding:6px 8px; font-size:13px; vertical-align:middle; }
    .print-table th { background:#001f3f; color:#fff; font-weight:700; text-align:center; }
    .center{text-align:center}
    .form-input { width: 95%; padding: 4px; border: 1px solid #ccc; border-radius: 4px; text-align: center; font-family: inherit; font-size: 13px; }
    .form-input:read-only { background-color: #f0f0f0; border-color: #ddd; cursor: not-allowed; }
    .total-field { font-weight: bold; background-color: #e9ecef; }
    .search-container { max-width: 600px; margin: 40px auto; padding: 30px; background: #fff; border-radius: 12px; box-shadow: 0 6px 24px rgba(0,0,0,.08); text-align: center; }
    .search-container h2 { font-weight:800; color:#001f3f; margin-bottom:20px; }
    .approval-actions { display: flex; gap: 1rem; justify-content: center; margin-top: 20px;}

    @media print {
        body { background:#fff; }
        .a4-sheet { margin:0; width:auto; min-height:auto; box-shadow:none; padding:0; }
        .no-print { display:none !important; }
        .form-input, .form-select { border:none; background:transparent; padding:4px; width:100%; text-align: center; -moz-appearance: none; -webkit-appearance: none; appearance: none;}
        .form-input:focus { outline: none; }
    }
</style>
</head>
<body>

<?php if (empty($row)): ?>
    <div class="search-container">
        <h2>البحث عن موظف</h2>
        <p>الرجاء اختيار الموظف لإنشاء مستحقات نهاية الخدمة الخاصة به.</p>
        <?php if (!empty($err)): ?>
            <div class="alert alert-danger mt-3"><?php echo html_escape($err); ?></div>
        <?php endif; ?>
        <form method="get" action="<?php echo site_url('users1/end_of_service'); ?>" class="mt-4">
            <div class="mb-3">
                <select name="emp" id="employee_search" class="form-select" required>
                    <option></option>
                    <?php if(!empty($employees)): ?>
                        <?php foreach($employees as $employee_item): ?>
                            <option value="<?php echo html_escape($employee_item['username']); ?>">
                                <?php echo html_escape($employee_item['name']) . ' (' . html_escape($employee_item['username']) . ')'; ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <button class="btn" type="submit">عرض المستحقات</button>
        </form>
    </div>

<?php else: ?>
    <div class="no-print">
        <?php if ($is_approval_mode): ?>
            <a class="btn" href="<?php echo site_url('users1/eos_approvals'); ?>">العودة لقائمة الاعتمادات</a>
        <?php else: ?>
            <a class="btn" href="<?php echo site_url('users1/end_of_service'); ?>">العودة للبحث</a>
        <?php endif; ?>
        <a class="btn" href="javascript:window.print()"><i class="fa-solid fa-print me-2"></i>طباعة</a>
    </div>

    <form id="settlementForm" method="post" action="<?php echo site_url('users1/end_of_service?emp='.$emp); ?>">
        <div class="a4-sheet">
            <div class="header">
                <div class="brand"><h1>مستحقات نهاية الخدمة</h1></div>
                <div class="meta"><div class="date">التاريخ: <?php echo date('Y-m-d'); ?></div></div>
            </div>

            <input type="hidden" name="resignation_order_id" value="<?php echo html_escape($row['resignation_order_id'] ?? ''); ?>">

            
            <table class="print-table">
                <thead><tr><th class="center" colspan="4">بيانات الموظف والراتب</th></tr></thead>
                <tbody>
                    <tr>
                        <td class="center">اسم الموظف</td><td><?php echo html_escape($row['subscriber_name']); ?></td>
                        <td class="center">المسمى الوظيفي</td><td><?php echo html_escape($row['profession']); ?></td>
                    </tr>
                    <tr>
                        <td class="center">الرقم الوظيفي</td><td><?php echo html_escape($row['employee_id']); ?></td>
                        <td class="center">تاريخ المباشرة</td><td><?php echo html_escape($row['joining_date']); ?></td>
                    </tr>
                    <tr>
                        <td class="center">إجمالي الراتب</td><td><?php echo number_format($row['total_salary'], 2); ?></td>
                        <td class="center">تاريخ آخر يوم عمل</td><td><?php echo html_escape($row['date_of_the_last_working'] ?? ''); ?></td>
                    </tr>
                </tbody>
            </table>
            <div class="section-title">ملخص بيانات الموظف</div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-title text-primary fw-bold">تفاصيل الاستقالة والسلوك الوظيفي</h6>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>سبب الاستقالة:</strong> <?php echo html_escape($row['reason_for_resignation'] ?? 'غير محدد'); ?></li>
                                <li class="list-group-item"><strong>رصيد الإجازات المتبقي:</strong> <span class="fw-bold"><?php echo html_escape($row['leave_balance'] ?? 0); ?></span> يوم</li>
                                <li class="list-group-item"><strong>إجمالي أيام الغياب (آخر مسير):</strong> <span class="fw-bold text-danger"><?php echo html_escape($row['total_absences'] ?? 0); ?></span> يوم</li>
                                <li class="list-group-item"><strong>إجمالي دقائق التأخير (آخر مسير):</strong> <span class="fw-bold text-danger"><?php echo html_escape($row['total_lateness'] ?? 0); ?></span> دقيقة</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                     <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-title text-primary fw-bold">مسار الاعتماد</h6>
                            <?php if(!empty($row['approvers'])): ?>
                                <ol class="list-group list-group-numbered">
                                    <?php foreach($row['approvers'] as $approver): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-start">
                                            <div class="ms-2 me-auto">
                                                <div class="fw-bold"><?php echo html_escape($approver['approver_name']); ?></div>
                                                <small class="text-muted">المستوى <?php echo html_escape($approver['approval_level']); ?></small>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ol>
                            <?php else: ?>
                                <p class="text-muted">لم يتم تحديد مسار اعتماد.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="section-title">التفاصيل المالية</div>
            <table class="print-table">
                <thead><tr><th>المدفوعات/ الحسميات</th><th>القيمة (SAR)</th><th>النوع</th></tr></thead>
                <tbody>
                    <?php
                        function create_settlement_row($label, $name, $type, $is_approval_mode, $settlement_details) {
                            $value = $is_approval_mode ? ($settlement_details[$name] ?? '0.00') : '0.00';
                            $readonly = $is_approval_mode ? 'readonly' : '';
                            $disabled = $is_approval_mode ? 'disabled' : '';
                            $initial_class = ($type === 'payment') ? 'payment' : 'deduction';
                            
                            $type_dropdown = "<select class='form-select form-select-sm type-selector' {$disabled}>
                                <option value='payment' ".($initial_class === 'payment' ? 'selected' : '').">مستحق</option>
                                <option value='deduction' ".($initial_class === 'deduction' ? 'selected' : '').">خصم</option>
                            </select>";
                            
                            echo "<tr>
                                <td>{$label}</td>
                                <td><input type='number' step='0.01' name='settlement[{$name}]' class='form-input {$initial_class}' value='{$value}' {$readonly}></td>
                                <td class='center'>{$type_dropdown}</td>
                            </tr>";
                        }
                    ?>
                    <?php create_settlement_row('مكافأة نهاية الخدمة', 'gratuity_amount', 'payment', $is_approval_mode, $settlement_details); ?>
                    <?php create_settlement_row('التعويضات', 'compensation', 'payment', $is_approval_mode, $settlement_details); ?>
                    <?php create_settlement_row('خصم التأمينات', 'insurance_deduction', 'deduction', $is_approval_mode, $settlement_details); ?>
                    <?php create_settlement_row('تعويض التأمينات', 'insurance_compensation', 'deduction', $is_approval_mode, $settlement_details); ?>
                    <?php create_settlement_row('قيمة رصيد الإجازة', 'leave_balance_deduction', 'deduction', $is_approval_mode, $settlement_details); ?>
                    <?php create_settlement_row('خصم الغيابات', 'absence_deduction', 'deduction', $is_approval_mode, $settlement_details); ?>
                    <?php create_settlement_row('خصم التأخير', 'lateness_deduction', 'deduction', $is_approval_mode, $settlement_details); ?>
                    <?php create_settlement_row('خصم الشرط الجزائي', 'penalty_clause_deduction', 'deduction', $is_approval_mode, $settlement_details); ?>
                    <?php create_settlement_row('خصم الإجراء الجزائي للغياب', 'absence_penalty_deduction', 'deduction', $is_approval_mode, $settlement_details); ?>
                    
                    <tr>
                        <td><strong>المبلغ النهائي المستحق للموظف</strong></td>
                        <td><input type="text" id="final_amount" name="settlement[final_amount]" class="form-input total-field" value="<?php echo html_escape($is_approval_mode ? ($settlement_details['final_amount'] ?? '0.00') : '0.00'); ?>" readonly></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>

            <div class="no-print">
            <?php if ($is_approval_mode && isset($settlement_details['approval_status']) && $settlement_details['approval_status'] === 'pending'): ?>
                <div class="approval-actions">
                    <button type="button" id="approveBtn" class="btn btn-success"><i class="fa fa-check me-2"></i>موافقة</button>
                    <button type="button" id="rejectBtn" class="btn btn-danger"><i class="fa fa-times me-2"></i>رفض</button>
                </div>
            <?php elseif (!$is_approval_mode): ?>
                <div style="margin-top: 20px; text-align:center;">
                    <button type="submit" class="btn btn-success">حفظ وتقديم للاعتماد</button>
                </div>
            <?php endif; ?>
            </div>
        </div>
    </form>
<?php endif; ?>

<div class="modal fade" id="rejectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">سبب الرفض</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="rejection_reason" class="form-label">الرجاء إدخال سبب واضح للرفض:</label>
                    <textarea class="form-control" id="rejection_reason" rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" id="submitRejectionBtn" class="btn btn-primary">تأكيد الرفض</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    const isApprovalMode = <?php echo json_encode($is_approval_mode); ?>;
    const form = document.getElementById('settlementForm');

    if ($('#employee_search').length) {
        $('#employee_search').select2({
            theme: 'bootstrap-5',
            placeholder: 'ابحث بالاسم أو الرقم الوظيفي',
            allowClear: true
        });
    }

    if (form && !isApprovalMode) {
        const finalAmountField = document.getElementById('final_amount');

        function calculateTotal() {
            let totalPayments = 0;
            form.querySelectorAll('.payment').forEach(input => { totalPayments += parseFloat(input.value) || 0; });
            
            let totalDeductions = 0;
            form.querySelectorAll('.deduction').forEach(input => { totalDeductions += parseFloat(input.value) || 0; });
            
            finalAmountField.value = (totalPayments - totalDeductions).toFixed(2);
        }

        form.querySelectorAll('.payment, .deduction').forEach(input => {
            input.addEventListener('input', calculateTotal);
        });

        form.querySelectorAll('.type-selector').forEach(selector => {
            selector.addEventListener('change', function() {
                const selectedType = this.value;
                const valueInput = this.closest('tr').querySelector('.form-input');
                valueInput.classList.remove('payment', 'deduction');
                valueInput.classList.add(selectedType);
                calculateTotal();
            });
        });
        calculateTotal();
    }

    if (isApprovalMode) {
        const approveBtn = $('#approveBtn');
        const rejectBtn = $('#rejectBtn');
        const rejectionModal = new bootstrap.Modal(document.getElementById('rejectionModal'));
        const submitRejectionBtn = $('#submitRejectionBtn');
        const rejectionReasonText = $('#rejection_reason');
        const approvalTaskId = <?php echo json_encode($approval_task_id); ?>;

        const handleAction = function(action, reason = '') {
            if (!confirm('هل أنت متأكد من ' + (action === 'approve' ? 'الموافقة' : 'الرفض') + '؟')) return;
            
            $.ajax({
                url: "<?php echo site_url('users1/update_settlement_status'); ?>",
                type: 'POST',
                data: {
                    task_id: approvalTaskId,
                    action: action,
                    rejection_reason: reason,
                },
                dataType: 'json',
                beforeSend: function() {
                    approveBtn.prop('disabled', true);
                    rejectBtn.prop('disabled', true);
                },
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        window.location.href = "<?php echo site_url('users1/eos_approvals'); ?>";
                    } else {
                        alert('Error: ' + response.message);
                        approveBtn.prop('disabled', false);
                        rejectBtn.prop('disabled', false);
                    }
                },
                error: function() {
                    alert('An unexpected error occurred. Please try again.');
                    approveBtn.prop('disabled', false);
                    rejectBtn.prop('disabled', false);
                }
            });
        };

        approveBtn.on('click', function() { handleAction('approve'); });
        rejectBtn.on('click', function() { rejectionModal.show(); });

        submitRejectionBtn.on('click', function() {
            const reason = rejectionReasonText.val();
            if (reason.trim().length < 5) {
                alert('الرجاء إدخال سبب واضح للرفض.');
                return;
            }
            rejectionModal.hide();
            handleAction('reject', reason);
        });
    }
});
</script>

</body>
</html>