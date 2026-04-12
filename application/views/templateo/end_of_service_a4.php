<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// First, define $is_creator. This is needed for the "Edit and Resend" button.
$is_creator = isset($settlement_details) && isset($settlement_details['created_by_id']) && $settlement_details['created_by_id'] == $this->session->userdata('username');

// Next, use the simple logic to determine if MAIN fields (like adding payments) are editable.
if ($is_approval_mode) {
    $can_edit = false;
} else {
    $can_edit = true;
}

$readonly = $can_edit ? '' : 'readonly';
$disabled = $can_edit ? '' : 'disabled';

// --- NEW LOGIC FOR STATS FIELDS (Vacation, Late, Early) ---
$current_user_id = $this->session->userdata('username');
$allowed_stat_editors = ['2784', '2230','2901'];

// Check if current user is allowed to edit stats
$can_edit_stats = in_array($current_user_id, $allowed_stat_editors);

// Set readonly attribute specifically for these fields
$stats_readonly = $can_edit_stats ? '' : 'readonly';
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
    @page { 
        size: A4; 
        margin: 12mm 12mm 14mm 12mm; 
    }
    
    html, body { 
        font-family: 'Tajawal', system-ui, sans-serif; 
        background: #f4f6f9; 
        color: #111; 
        margin: 0;
        padding: 0;
    }
    
    .container {
        max-width: 210mm;
        margin: 0 auto;
        padding: 0 10px;
    }
    
    .a4-sheet { 
        width: 100%;
        min-height: 297mm; 
        margin: 10mm auto; 
        background: #fff; 
        box-shadow: 0 6px 24px rgba(0,0,0,.08); 
        padding: 14mm; 
        box-sizing: border-box;
    }
    
    .no-print { 
        display: flex; 
        flex-wrap: wrap; 
        gap: .5rem; 
        justify-content: center; 
        margin: 12px auto; 
        padding: 0 14mm; 
    }
    
    .btn { 
        border: 1px solid #001f3f; 
        color: #001f3f; 
        background: #fff; 
        padding: 8px 14px; 
        border-radius: 8px; 
        font-weight: 700; 
        cursor: pointer; 
        text-decoration: none; 
        display: inline-flex; 
        align-items: center; 
        gap: 8px; 
        transition: all 0.3s ease;
    }
    
    .btn:hover { 
        background: #001f3f; 
        color: #fff; 
        text-decoration: none;
    }
    
    .btn-success { 
        border-color: #198754; 
        color: #198754; 
        background-color: #fff;
    }
    
    .btn-success:hover { 
        background: #198754; 
        color: #fff; 
    }
    
    .btn-danger { 
        border-color: #dc3545; 
        color: #dc3545; 
        background-color: #fff;
    }
    
    .btn-danger:hover { 
        background: #dc3545; 
        color: #fff; 
    }
    
    .btn-warning { 
        border-color: #ffc107; 
        color: #856404; 
        background-color: #fff;
    }
    
    .btn-warning:hover { 
        background: #ffc107; 
        color: #856404; 
    }
    
    .btn-outline-primary {
        border-color: #0d6efd;
        color: #0d6efd;
        background-color: #fff;
    }
    
    .btn-outline-primary:hover {
        background-color: #0d6efd;
        color: #fff;
    }
    
    .btn-outline-secondary {
        border-color: #6c757d;
        color: #6c757d;
        background-color: #fff;
    }
    
    .btn-outline-secondary:hover {
        background-color: #6c757d;
        color: #fff;
    }
    
    .header { 
        display: flex; 
        align-items: center; 
        justify-content: space-between; 
        gap: 16px; 
        padding-bottom: 10px; 
        border-bottom: 2px solid #001f3f; 
        margin-bottom: 14px; 
    }
    
    .brand h1 { 
        font-size: 20px; 
        margin: 0; 
        font-weight: 800; 
        color: #001f3f; 
    }
    
    .section-title { 
        margin: 14px 0 8px; 
        padding: 8px 10px; 
        background: #f7f9fc; 
        border-right: 4px solid #FF8C00; 
        font-weight: 800; 
        border-radius: 4px;
    }
    
    .print-table { 
        width: 100%; 
        border-collapse: collapse; 
        margin-top: 6px; 
    }
    
    .print-table th, .print-table td { 
        border: 1px solid #d9dee3; 
        padding: 6px 8px; 
        font-size: 13px; 
        vertical-align: middle; 
    }
    
    .print-table th { 
        background: #001f3f; 
        color: #fff; 
        font-weight: 700; 
        text-align: center; 
    }
    
    .center {
        text-align: center;
    }
    
    .top-actions a {
        background: rgba(255,255,255,.12);
        border: 1px solid var(--glass-border);
        color: #000;
        text-decoration: none;
        border-radius: 10px;
        padding: 8px 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: .25s;
    }
    
    .form-input { 
        width: 100%; 
        padding: 4px; 
        border: 1px solid #ccc; 
        border-radius: 4px; 
        font-family: inherit; 
        font-size: 13px; 
        box-sizing: border-box;
    }
    
    .form-input.desc-input { 
        text-align: right; 
    }
    
    .form-input.amount-input { 
        text-align: center; 
    }
    
    .form-input:read-only { 
        background-color: #f0f0f0; 
        border-color: #ddd; 
        cursor: not-allowed; 
    }
    
    .total-field { 
        font-weight: bold; 
        background-color: #e9ecef; 
    }
    
    .search-container { 
        max-width: 600px; 
        margin: 40px auto; 
        padding: 30px; 
        background: #fff; 
        border-radius: 12px; 
        box-shadow: 0 6px 24px rgba(0,0,0,.08); 
        text-align: center; 
    }
    
    .search-container h2 { 
        font-weight: 800; 
        color: #001f3f; 
        margin-bottom: 20px; 
    }
    
    .approval-actions { 
        display: flex; 
        gap: 1rem; 
        justify-content: center; 
        margin-top: 20px;
    }
    
    .btn-remove-item { 
        cursor: pointer; 
        font-size: 1.2rem; 
        color: #dc3545;
    }
    
    .input-group-sm { 
        max-width: 200px; 
    }
    
    .card { 
        border: 1px solid #d9dee3; 
        border-radius: 8px; 
        margin-bottom: 1rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .card-body { 
        padding: 1rem; 
    }
    
    .list-group-item {
        border: none;
        padding: 0.75rem 0;
        background: transparent;
    }
    
    /* Stepper (Train) CSS */
    .stepper {
        position: relative;
        padding-right: 20px;
        margin-top: 20px;
    }
    
    .step-item {
        position: relative;
        padding-bottom: 25px;
        border-right: 2px solid #e9ecef;
        margin-right: 10px;
        padding-right: 25px;
    }
    
    .step-item:last-child {
        border-right: 2px solid transparent;
    }
    
    .step-icon {
        position: absolute;
        right: -11px;
        top: 0;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #fff;
        border: 2px solid #e9ecef;
        z-index: 2;
        text-align: center;
        line-height: 16px;
        font-size: 10px;
    }
    
    /* Status Colors */
    .step-item.approved .step-icon { 
        background: #198754; 
        border-color: #198754; 
        color: #fff; 
    }
    
    .step-item.approved { 
        border-right-color: #198754; 
    }
    
    .step-item.pending .step-icon { 
        background: #ffc107; 
        border-color: #ffc107; 
        color: #000; 
        animation: pulse 2s infinite;
    }
    
    .step-item.pending { 
        border-right-color: #e9ecef; 
    }
    
    .step-item.rejected .step-icon { 
        background: #dc3545; 
        border-color: #dc3545; 
        color: #fff; 
    }
    
    .step-item.waiting .step-icon { 
        background: #e9ecef; 
        color: #6c757d; 
    }
    
    .step-content { 
        margin-top: -5px; 
    }
    
    .step-title { 
        font-weight: bold; 
        font-size: 14px; 
        margin-bottom: 2px; 
    }
    
    .step-date { 
        font-size: 11px; 
        color: #6c757d; 
    }
    
    .step-comment { 
        font-size: 12px; 
        background: #f8f9fa; 
        padding: 5px; 
        margin-top: 5px; 
        border-radius: 4px; 
        border: 1px dashed #ccc; 
    }
    
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7); }
        70% { box-shadow: 0 0 0 6px rgba(255, 193, 7, 0); }
        100% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
    }
    
    .form-check-label {
        margin-right: 0.5rem;
    }
    
    .alert {
        border-radius: 8px;
        padding: 12px 16px;
    }
    
    .badge {
        font-size: 10px;
        padding: 4px 8px;
    }
    
    .text-primary {
        color: #0d6efd !important;
    }
    
    .text-muted {
        color: #6c757d !important;
    }
    
    .border-dashed {
        border-style: dashed !important;
    }
    
    @media print {
        body { 
            background: #fff; 
        }
        
        .a4-sheet { 
            margin: 0; 
            width: auto; 
            min-height: auto; 
            box-shadow: none; 
            padding: 0; 
        }
        
        .no-print { 
            display: none !important; 
        }
        
        .form-input, .form-select { 
            border: none; 
            background: transparent; 
            padding: 4px; 
            width: 100%; 
            text-align: center; 
            -moz-appearance: none; 
            -webkit-appearance: none; 
            appearance: none;
        }
        
        .form-input:focus { 
            outline: none; 
        }
        
        .btn-remove-item { 
            display: none !important; 
        }
        
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        
        .stepper {
            page-break-inside: avoid;
        }
    }
    
    @media (max-width: 768px) {
        .a4-sheet {
            padding: 10px;
            margin: 5px auto;
        }
        
        .header {
            flex-direction: column;
            text-align: center;
            gap: 10px;
        }
        
        .no-print {
            flex-direction: column;
            align-items: center;
        }
        
        .approval-actions {
            flex-direction: column;
            align-items: center;
        }
        
        .search-container {
            margin: 20px auto;
            padding: 20px;
        }
        
        .print-table {
            font-size: 12px;
        }
        
        .print-table th, .print-table td {
            padding: 4px 6px;
        }
    }
</style>
</head>
<body>

<div class="container">
    <?php if (empty($row)): ?>
        <div class="search-container">
            <div class="top-actions" style="color:black;">
                <a href="<?php echo site_url('users1/main_hr1'); ?>" style="text-decoration: none;"><i class="fas fa-home"></i> الرئيسية</a>
            </div>
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
                <div class="mb-3 text-start">
            <label for="deduction_start_date" class="form-label fw-bold text-dark">
                 احتساب الخصومات (الغياب، التأخير) بدءاً من تاريخ:
                 <br><small class="text-muted fw-normal">(اختياري: إذا تُرك فارغاً سيتم الحساب كالمعتاد)</small>
            </label>
            <input type="date" name="deduction_start_date" id="deduction_start_date" class="form-control" 
       value="<?= $this->input->get_post('deduction_start_date', true) ?>">
        </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="replace" value="true" id="replaceCheckbox">
                    <label class="form-check-label" for="replaceCheckbox">
                        <strong>استبدال وإنشاء جديد</strong> (حدد هذا الخيار إذا كان للموظف تسوية حالية وتريد حذفها وبدء تسوية جديدة)
                    </label>
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
            
            <?php if ($is_creator && isset($settlement_details) && $settlement_details['status'] === 'approved') : ?>
                <a class="btn btn-warning" href="<?php echo site_url('users1/end_of_service?emp=' . $row['employee_id']); ?>">
                    <i class="fas fa-edit me-2"></i> تعديل وإعادة إرسال
                </a>
            <?php endif; ?>
        </div>
        
        <form id="settlementForm" method="post" action="<?php echo site_url('users1/end_of_service?emp='.($row['employee_id'] ?? '')); ?>">
    <div class="a4-sheet">
        <div class="header">
            <div class="brand"><h1>مستحقات نهاية الخدمة</h1></div>
            <div class="meta"><div class="date">التاريخ: <?php echo date('Y-m-d'); ?></div></div>
        </div>

        <input type="hidden" name="resignation_order_id" value="<?php echo html_escape($row['resignation_order_id'] ?? ''); ?>">
        <input type="hidden" name="settlement_id" value="<?php echo $settlement_details['id'] ?? ''; ?>">
        <input type="hidden" name="employee_id" value="<?php echo $row['employee_id'] ?? ''; ?>">
        
        <?php 
            $active_deduction_date = $this->input->get_post('deduction_start_date', true);
            if (empty($active_deduction_date) && isset($settlement_details['deduction_start_date'])) {
                $active_deduction_date = $settlement_details['deduction_start_date'];
            }
        ?>
        <input type="hidden" id="hidden_deduction_start_date" name="deduction_start_date" value="<?php echo html_escape($active_deduction_date); ?>">
                
                <table class="print-table">
                    <thead><tr><th class="center" colspan="4">بيانات الموظف والراتب</th></tr></thead>
                    <tbody>
                        <tr>
                            <td class="center" style="width:20%;">اسم الموظف</td><td><?php echo html_escape($row['subscriber_name']); ?></td>
                            <td class="center" style="width:20%;">المسمى الوظيفي</td><td><?php echo html_escape($row['profession']); ?></td>
                        </tr>
                        <tr>
                            <td class="center">الرقم الوظيفي</td><td id="employeeIdCell"><?php echo html_escape($row['employee_id']); ?></td>
                            <td class="center">تاريخ المباشرة</td><td><?php echo html_escape($row['joining_date']); ?></td>
                        </tr>
                        <tr>
                            <td class="center">الراتب الأساسي</td>
                            <td><?php echo number_format($row['base_salary'] ?? 0, 2); ?></td>
                            <td class="center">بدل السكن</td>
                            <td><?php echo number_format($row['housing_allowance'] ?? 0, 2); ?></td>
                        </tr>
                        <tr>
                            <td class="center">بدل النقل (n4)</td>
                            <td><?php echo number_format($row['transportation_allowance'] ?? 0, 2); ?></td>
                            <td class="center">بدلات أخرى</td>
                            <td><?php echo number_format($row['other_allowances'] ?? 0, 2); ?></td>
                        </tr>
                        <tr style="background-color: #f7f9fc;">
                            <td class="center"><strong>إجمالي الراتب</strong></td>
                            <td><strong><?php echo number_format($row['total_salary'], 2); ?></strong></td>
                            <td class="center">تاريخ آخر يوم عمل</td>
                            <td><?php echo html_escape($row['date_of_the_last_working'] ?? ''); ?></td>
                        </tr>
                        <tr>
                            <td class="center">سبب الاستقالة</td>
                            <td colspan="3">
                                <input type="text" 
                                    name="reason_for_resignation" 
                                    class="form-input" 
                                    value="<?php echo html_escape($row['reason_for_resignation'] ?? ''); ?>" 
                                    <?php echo $readonly; ?>>
                            </td>
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
                                    <li class="list-group-item">
    <strong>إجمالي أيام الغياب:</strong>
    <div class="input-group input-group-sm mt-1">
        <input type="number" id="manualAbsenceDays" class="form-control" 
               value="<?php echo html_escape($row['total_absences'] ?? 0); ?>" 
               <?php echo $stats_readonly; ?>>
        <button class="btn btn-outline-secondary view-violation-details" 
                type="button" 
                data-type="absence" 
                title="عرض التفاصيل">
            <i class="fas fa-list"></i>
        </button>
    </div>
</li>
<li class="list-group-item">
    <strong>رصيد الإجازات المتبقي (يوم):</strong>
    <div class="input-group input-group-sm mt-1">
        <input type="number" step="0.1" id="leaveBalanceInput" class="form-control" 
               value="<?php echo html_escape($row['leave_balance'] ?? 0); ?>" 
               <?php echo $stats_readonly; ?>>
        
        <?php if ($can_edit_stats): ?>
            <button class="btn btn-outline-success" type="button" id="saveLeaveBalanceBtn" title="حفظ الرصيد في ملف الموظف">
                <i class="fas fa-save"></i>
            </button>
        <?php endif; ?>
    </div>
</li>
<li class="list-group-item">
    <strong>إجمالي دقائق التأخير:</strong>
    <div class="input-group input-group-sm mt-1">
        <input type="number" id="manualLateMinutes" class="form-control" 
               value="<?php echo html_escape($row['total_lateness'] ?? 0); ?>" 
               <?php echo $stats_readonly; ?>>
        <button class="btn btn-outline-secondary view-violation-details" 
                type="button" 
                data-type="late" 
                title="عرض التفاصيل">
            <i class="fas fa-list"></i>
        </button>
    </div>
</li>

<li class="list-group-item">
    <strong>إجمالي دقائق الخروج المبكر:</strong>
    <div class="input-group input-group-sm mt-1">
        <input type="number" id="manualEarlyMinutes" class="form-control" 
               value="<?php echo html_escape($row['total_early'] ?? 0); ?>" 
               <?php echo $stats_readonly; ?>>
        <button class="btn btn-outline-secondary view-violation-details" 
                type="button" 
                data-type="early" 
                title="عرض التفاصيل">
            <i class="fas fa-list"></i>
        </button>
        <?php if ($can_edit_stats): ?>
            <button class="btn btn-outline-primary" type="button" id="recalculateBtn">إعادة الحساب</button>
        <?php endif; ?>
    </div>
</li>

                                   
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-title text-primary fw-bold mb-3">
                                    <i class="fas fa-road me-2"></i> مسار الاعتماد
                                </h6>

                                <?php 
                                // Use 'approval_log' which comes from the model, not 'approvers'
                                $logs = $row['approval_log'] ?? []; 

                                if (!empty($logs)): ?>
                                    <div class="stepper">
                                        <?php foreach($logs as $log): ?>
                                            <?php 
                                                // Determine class and icon based on status
                                                $statusClass = 'waiting';
                                                $icon = '<i class="fas fa-hourglass"></i>'; // Default waiting
                                                $statusText = 'بانتظار الدور';

                                                if ($log['status'] == 'approved') {
                                                    $statusClass = 'approved';
                                                    $icon = '<i class="fas fa-check"></i>';
                                                    $statusText = 'تم الاعتماد';
                                                } elseif ($log['status'] == 'rejected') {
                                                    $statusClass = 'rejected';
                                                    $icon = '<i class="fas fa-times"></i>';
                                                    $statusText = 'مرفوض';
                                                } elseif ($log['status'] == 'pending') {
                                                    $statusClass = 'pending';
                                                    $icon = '<i class="fas fa-spinner fa-spin"></i>';
                                                    $statusText = 'جاري المراجعة (الحالي)';
                                                } elseif ($log['status'] == 'skipped') {
                                                    $statusClass = 'waiting';
                                                    $icon = '<i class="fas fa-minus"></i>';
                                                    $statusText = 'تم التجاوز';
                                                }
                                            ?>

                                            <div class="step-item <?php echo $statusClass; ?>">
                                                <div class="step-icon"><?php echo $icon; ?></div>
                                                <div class="step-content">
                                                    <div class="step-title">
                                                        <?php echo html_escape($log['approver_name']); ?>
                                                        <span class="badge rounded-pill bg-light text-dark border ms-1" style="font-size: 10px;">
                                                            مستوى <?php echo $log['approval_level']; ?>
                                                        </span>
                                                    </div>
                                                    
                                                    <div class="text-muted small mb-1">
                                                        <?php echo $statusText; ?>
                                                    </div>

                                                    <?php if (!empty($log['action_date'])): ?>
                                                        <div class="step-date">
                                                            <i class="far fa-clock me-1"></i>
                                                            <?php echo date('Y-m-d H:i', strtotime($log['action_date'])); ?>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if (!empty($log['rejection_reason']) || !empty($log['comments'])): ?>
                                                        <div class="step-comment">
                                                            <strong>ملاحظات:</strong> 
                                                            <?php echo html_escape($log['rejection_reason'] . ($log['comments'] ?? '')); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-light text-center border border-dashed">
                                        <i class="fas fa-info-circle text-muted mb-2"></i><br>
                                        لم يبدأ مسار الاعتماد بعد أو لا توجد بيانات.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="section-title">التفاصيل المالية</div>
                <table class="print-table" id="financial-table">
                    <thead>
                        <tr>
                            <th style="width:50%;">المدفوعات/ الحسميات</th>
                            <th style="width:25%;">القيمة (SAR)</th>
                            <th style="width:20%;">النوع</th>
                            <th class="no-print" style="width:5%;"></th>
                        </tr>
                    </thead>
                    <tbody id="financial-items-body">
                        </tbody>
                    <tfoot>
                        <tr>
                            <td><strong>المبلغ النهائي المستحق للموظف</strong></td>
                            <td><input type="text" id="final_amount" name="settlement[final_amount]" class="form-input total-field amount-input" value="<?php echo html_escape($settlement_details['final_amount'] ?? '0.00'); ?>" readonly></td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
                
                <?php if ($can_edit): // Only show this section in Create Mode ?>
                    <div class="section-title">مسار الاعتماد (قابل للتعديل)</div>
                    <p class="text-muted small no-print">
                        الرجاء تحديد مسار الاعتماد بالترتيب. المعتمد الأول هو من سيبدأ المراجعة.
                    </p>

                    <table class="print-table mb-3">
                        <thead>
                            <tr>
                                <th style="width: 25%;">المستوى</th>
                                <th>المعتمد</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                // --- Your default approver order ---
                                $default_approvers = ['2774', '2230', '2833','2909', '1693','2784', '1001']; 
                            ?>

                            <?php for ($i = 1; $i <= 7; $i++): ?>
                                <tr>
                                    <td class="center">المعتمد <?php echo $i; ?></td>
                                    <td>
                                        <select name="approvers[]" class="form-select" <?php echo $disabled; ?>>
                                            
                                            <option value="">-- اختياري --</option> 
                                            
                                            <?php if(!empty($all_approvers)): ?>
                                                <?php foreach($all_approvers as $approver): ?>
                                                    <?php 
                                                        $employee_id = html_escape($approver['username']);
                                                        // Check if this employee is the default for this level
                                                        $is_selected = (isset($default_approvers[$i - 1]) && $default_approvers[$i - 1] == $employee_id) ? 'selected' : '';
                                                    ?>
                                                    <option value="<?php echo $employee_id; ?>" <?php echo $is_selected; ?>>
                                                        <?php echo html_escape($approver['name']) . ' (' . $employee_id . ')'; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>

                                        </select>
                                    </td>
                                </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
                
                <?php if ($can_edit): ?>
                    <div class="no-print mt-3">
                        <button type="button" id="add-payment-btn" class="btn btn-sm btn-outline-primary"><i class="fa fa-plus me-1"></i> إضافة مستحق</button>
                        <button type="button" id="add-deduction-btn" class="btn btn-sm btn-outline-secondary"><i class="fa fa-minus me-1"></i> إضافة خصم</button>
                    </div>
                <?php endif; ?>

                <?php 
                // Check if current user is the verifier (2784)
                $is_verifier_user = ($this->session->userdata('username') == '2784');
                $is_verifier_turn = (isset($settlement_details['current_approver']) && $settlement_details['current_approver'] == '2784');

                // Determine if fields should be enabled
                $enable_verification = ($is_verifier_user && $is_verifier_turn);

                // Retrieve existing values if available
                $items_arr = isset($settlement_details['items_json']) ? json_decode($settlement_details['items_json'], true) : [];
                $ver_flags = isset($items_arr['verification_flags']) ? $items_arr['verification_flags'] : [];

                $checked_signed = (isset($ver_flags['signed_by_employee']) && $ver_flags['signed_by_employee'] == 1) ? 'checked' : '';
                $checked_mol    = (isset($ver_flags['verified_mol']) && $ver_flags['verified_mol'] == 1) ? 'checked' : '';
                $checked_other  = (isset($ver_flags['verified_other']) && $ver_flags['verified_other'] == 1) ? 'checked' : '';

                // Render the section if it is the verifier's turn OR if the verification data exists (history)
                if ($is_verifier_turn || !empty($ver_flags)) : 
                ?>
                <div class="card mb-3 no-print" style="border: 1px solid #17a2b8;">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-check-double"></i> إجراءات التحقق (HR Verification)</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="ver_signed" id="ver_signed" value="1" <?php echo $checked_signed; ?> <?php echo $enable_verification ? '' : 'disabled'; ?>>
                                    <label class="form-check-label fw-bold" for="ver_signed">
                                        تم التوقيع من قبل الموظف (Signed by Employee)
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="ver_mol" id="ver_mol" value="1" <?php echo $checked_mol; ?> <?php echo $enable_verification ? '' : 'disabled'; ?>>
                                    <label class="form-check-label fw-bold" for="ver_mol">
                                         تم التحقق من وزارة العمل (Verified Ministry of Labour)
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="ver_other" id="ver_other" value="1" <?php echo $checked_other; ?> <?php echo $enable_verification ? '' : 'disabled'; ?>>
                                    <label class="form-check-label fw-bold" for="ver_other">
                                        تحقق آخر (Other Verification)
                                    </label>
                                </div>
                            </div>
                        </div>
                        <?php if ($enable_verification): ?>
                            <div class="alert alert-warning mt-2 mb-0 py-1">
                                <small><i class="fas fa-info-circle"></i> يرجى تحديد الخيارات أعلاه قبل اعتماد الطلب.</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                <div class="no-print">
                    <?php if ($is_approval_mode && isset($settlement_details['approval_status']) && $settlement_details['approval_status'] === 'pending'): ?>
                        <div class="approval-actions">
                            <button type="button" id="approveBtn" class="btn btn-success"><i class="fa fa-check me-2"></i>موافقة</button>
                            <button type="button" id="rejectBtn" class="btn btn-danger"><i class="fa fa-times me-2"></i>رفض</button>
                        </div>
                    <?php elseif ($can_edit): ?>
                        <div style="margin-top: 20px; text-align:center;">
                            <button type="submit" class="btn btn-success">حفظ وتقديم للاعتماد</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    <?php endif; ?>
    <div class="modal fade" id="violationDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="violationModalTitle">تفاصيل المخالفات</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>التاريخ</th>
                                <th>اليوم</th>
                                <th>النوع</th>
                                <th>القيمة (دقائق/أيام)</th>
                                <th>المبلغ المخصوم (SAR)</th>
                            </tr>
                        </thead>
                        <tbody id="violationModalBody">
                            </tbody>
                        <tfoot id="violationModalFooter">
                            </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
    <div class="modal fade" id="rejectionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">سبب الرفض</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
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
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // --- Variables passed from PHP ---
    const isApprovalMode = <?php echo json_encode($is_approval_mode); ?>;
    const canEdit = <?php echo json_encode($can_edit); ?>;
    const replaceExisting = <?php echo json_encode($replace_existing); ?>;
    const totalSalary = <?php echo (float)($row['total_salary'] ?? 0); ?>;
    const dailyRate = totalSalary > 0 ? totalSalary / 30.0 : 0;
    const minuteRate = dailyRate > 0 ? dailyRate / 8.0 / 60.0 : 0;

    // Initialize Select2 if it exists
    if ($('#employee_search').length) {
        $('#employee_search').select2({
            theme: 'bootstrap-5',
            placeholder: 'ابحث بالاسم أو الرقم الوظيفي',
            allowClear: true
        });
    }

 // Load existing items based on mode
let existingItems = [];

try {
    if (isApprovalMode) {
        // In approval mode, load items from the saved JSON or items array
        if (<?php echo isset($settlement_details['items_json']) ? 'true' : 'false'; ?>) {
            // Try to parse JSON if it exists
            const itemsJson = '<?php echo addslashes($settlement_details['items_json'] ?? '[]'); ?>';
            if (itemsJson && itemsJson !== '[]') {
                existingItems = JSON.parse(itemsJson);
            } else {
                existingItems = [];
            }
        } else {
            // Fallback: use the items array if available
            const itemsArray = <?php echo json_encode($settlement_details['items'] ?? []); ?>;
            if (Array.isArray(itemsArray)) {
                existingItems = itemsArray;
            } else {
                existingItems = [];
            }
        }
    } else if (!isApprovalMode && <?php echo isset($row) ? 'true' : 'false'; ?>) {
        // In creation mode, populate with auto-calculated values from the controller
        existingItems = [
            { 
                description: 'مكافأة نهاية الخدمة', 
                amount: '<?php echo isset($row['calculated_gratuity']) ? number_format($row['calculated_gratuity'], 2, '.', '') : "0.00"; ?>', 
                type: 'payment', 
                key: 'gratuity_amount' 
            },
            { 
                description: 'تعويض رصيد الإجازات', 
                amount: '<?php echo isset($row['calculated_leave_compensation']) ? number_format($row['calculated_leave_compensation'], 2, '.', '') : "0.00"; ?>', 
                type: 'payment', 
                key: 'compensation' 
            },
            { 
                description: 'راتب حتى آخر يوم عمل', 
                amount: '<?php echo isset($row['calculated_prorated_salary']) ? number_format($row['calculated_prorated_salary'], 2, '.', '') : "0.00"; ?>', 
                type: 'payment', 
                key: 'prorated_salary' 
            },
            { 
                description: 'تعويضات أخرى', 
                amount: '0.00', 
                type: 'payment', 
                key: 'insurance_compensation' 
            },
            { 
                description: 'خصم التأمينات', 
                amount: '<?php echo isset($row['calculated_insurance_deduction']) ? number_format($row['calculated_insurance_deduction'], 2, '.', '') : "0.00"; ?>', 
                type: 'deduction', 
                key: 'insurance_deduction' 
            },
            { 
                description: 'خصم رصيد الإجازة بالسالب', 
                amount: '<?php echo isset($row['calculated_negative_leave_deduction']) ? number_format($row['calculated_negative_leave_deduction'], 2, '.', '') : "0.00"; ?>', 
                type: 'deduction', 
                key: 'leave_balance_deduction' 
            },
            { 
                description: 'خصم الغيابات', 
                amount: '<?php echo isset($row['calculated_absence_deduction']) ? number_format($row['calculated_absence_deduction'], 2, '.', '') : "0.00"; ?>', 
                type: 'deduction', 
                key: 'absence_deduction' 
            },
            { 
                description: 'خصم التأخير والخروج المبكر', 
                amount: '<?php echo isset($row['calculated_lateness_deduction']) ? number_format($row['calculated_lateness_deduction'], 2, '.', '') : "0.00"; ?>', 
                type: 'deduction', 
                key: 'lateness_deduction' 
            },
            { 
                description: 'خصم الشرط الجزائي', 
                amount: '0.00', 
                type: 'deduction', 
                key: 'penalty_clause_deduction' 
            },
            { 
                description: 'خصم بصمة منفردة', 
                amount: '<?php echo isset($row['calculated_single_punch_deduction']) ? number_format($row['calculated_single_punch_deduction'], 2, '.', '') : "0.00"; ?>', 
                type: 'deduction', 
                key: 'absence_penalty_deduction' 
            }
        ];
    }
} catch (error) {
    console.error('Error loading existing items:', error);
    existingItems = []; // Fallback to empty array
}

// Validate that existingItems is an array
if (!Array.isArray(existingItems)) {
    console.warn('existingItems is not an array, converting to array:', existingItems);
    // Try to convert to array if it's an object
    if (existingItems && typeof existingItems === 'object') {
        existingItems = Object.values(existingItems);
    } else {
        existingItems = [];
    }
}

console.log('Loaded existingItems:', existingItems);
console.log('Is array?', Array.isArray(existingItems));

let itemIndex = existingItems.length; // Start index from current length
const tableBody = $('#financial-items-body');
    function calculateTotal() {
        let totalPayments = 0;
        let totalDeductions = 0;
        
        tableBody.find('tr').each(function() {
            const amount = parseFloat($(this).find('.amount-input').val()) || 0;
            const type = $(this).find('.type-input').val();
            
            if (type === 'payment') {
                totalPayments += amount;
            } else {
                totalDeductions += amount;
            }
        });
        
        const finalAmount = (totalPayments - totalDeductions).toFixed(2);
        $('#final_amount').val(finalAmount);
    }

    function createRow(item = {}) {
        const desc = item.description || '';
        const amount = item.amount || '0.00';
        const type = item.type || 'payment';
        const key = item.key || `custom_${itemIndex}`;
        const readonly = canEdit ? '' : 'readonly';
        const disabled = canEdit ? '' : 'disabled';

        const rowHtml = `
            <tr>
                <td>
                    <input type="text" name="items[${itemIndex}][description]" 
                           value="${desc}" class="form-input desc-input" ${readonly}>
                    <input type="hidden" name="items[${itemIndex}][key]" value="${key}">
                </td>
                <td>
                    <input type="number" step="0.01" name="items[${itemIndex}][amount]" 
                           class="form-input amount-input" value="${amount}" ${readonly}>
                </td>
                <td>
                    <select name="items[${itemIndex}][type]" 
                            class="form-select form-select-sm type-input" ${disabled}>
                        <option value="payment" ${type === 'payment' ? 'selected' : ''}>مستحق</option>
                        <option value="deduction" ${type === 'deduction' ? 'selected' : ''}>خصم</option>
                    </select>
                </td>
                <td class="no-print center">
                    ${canEdit ? '<i class="fa fa-times-circle text-danger btn-remove-item" title="حذف"></i>' : ''}
                </td>
            </tr>`;
        
        itemIndex++;
        return rowHtml;
    }
    // --- Handle Violation Details Button Click ---
    $('.view-violation-details').on('click', function() {
        const type = $(this).data('type'); // absence, late, or early
        const employeeId = $('#employeeIdCell').text().trim();
        // We need the last working day. It's in the table above.
        // Finding it via a unique selector or traversing might be tricky if not ID'd.
        // Ideally add id="lastWorkingDay" to the cell displaying the date in the PHP table.
        // Assuming you add id="lastWorkingDay" to that <td>:
        // <td id="lastWorkingDay"><?php echo html_escape($row['date_of_the_last_working'] ?? ''); ?></td>
        // If you can't change PHP easily, grab it from context or ensure it's available.
        // Let's assume we pass it in a hidden input for safety:
        const lastWorkingDay = $('input[name="resignation_order_id"]').closest('form').find('td:contains("تاريخ آخر يوم عمل")').next().text().trim();
        
        if(!lastWorkingDay) { alert('تاريخ آخر يوم عمل غير موجود'); return; }

        // Show loading
        $('#violationModalBody').html('<tr><td colspan="5">جاري التحميل...</td></tr>');
        $('#violationModalFooter').empty();
        const modal = new bootstrap.Modal(document.getElementById('violationDetailsModal'));
        modal.show();

        let titleMap = { 'absence': 'تفاصيل الغياب', 'late': 'تفاصيل التأخير', 'early': 'تفاصيل الخروج المبكر' };
        $('#violationModalTitle').text(titleMap[type]);

        $.ajax({
            url: "<?php echo site_url('users1/ajax_get_eos_violation_details'); ?>",
            type: 'POST',
           data: { 
    employee_id: employeeId, 
    last_working_day: lastWorkingDay,
    
    // Grab the date safely from the hidden input we just added
    deduction_start_date: $('#hidden_deduction_start_date').val(),
    
    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
},
            dataType: 'json',
            success: function(response) {
                let rows = '';
                let totalValue = 0;
                let totalCost = 0;
                let unit = (type === 'absence') ? ' يوم' : ' دقيقة';

                if (response.data && response.data.length > 0) {
                    // Filter data based on button clicked (Client-side filtering for simplicity)
                    const filteredData = response.data.filter(item => item.type === type);
                    
                    if (filteredData.length === 0) {
                        rows = '<tr><td colspan="5">لا توجد سجلات لهذا النوع</td></tr>';
                    } else {
                        filteredData.forEach(item => {
                            rows += `<tr>
                                <td>${item.date}</td>
                                <td>${item.day}</td>
                                <td>${item.label}</td>
                                <td dir="ltr">${item.value}${unit}</td>
                                <td>${parseFloat(item.cost).toFixed(2)}</td>
                            </tr>`;
                            totalValue += parseFloat(item.value);
                            totalCost += parseFloat(item.cost);
                        });
                    }
                } else {
                    rows = '<tr><td colspan="5">لا توجد بيانات</td></tr>';
                }

                $('#violationModalBody').html(rows);
                if (totalValue > 0) {
                     $('#violationModalFooter').html(`
                        <tr class="fw-bold bg-light">
                            <td colspan="3">الإجمالي</td>
                            <td dir="ltr">${totalValue}${unit}</td>
                            <td>${totalCost.toFixed(2)}</td>
                        </tr>
                    `);
                }
            },
            error: function() {
                $('#violationModalBody').html('<tr><td colspan="5" class="text-danger">خطأ في جلب البيانات</td></tr>');
            }
        });
    });
    function initializeTable() {
        tableBody.empty();
        existingItems.forEach(item => {
            tableBody.append(createRow(item));
        });
        calculateTotal();
    }

    // Event handlers for dynamic items
    if (canEdit) {
        $('#add-payment-btn').on('click', function() {
            tableBody.append(createRow({ type: 'payment' }));
        });
        
        $('#add-deduction-btn').on('click', function() {
            tableBody.append(createRow({ type: 'deduction' }));
        });
    }

    tableBody.on('click', '.btn-remove-item', function() {
        $(this).closest('tr').remove();
        calculateTotal();
    });

    tableBody.on('input change', '.amount-input, .type-input', function() {
        calculateTotal();
    });

    // Add the replace flag to the form on submission
    $('#settlementForm').on('submit', function() {
        if (replaceExisting) {
            $(this).append('<input type="hidden" name="replace_flag" value="true" />');
        }
    });

    // Initialize the table when the document is ready
    initializeTable();

    // --- Recalculate button logic ---
    $('#recalculateBtn').on('click', function() {
        const absenceDays = parseFloat($('#manualAbsenceDays').val()) || 0;
        const lateMinutes = parseFloat($('#manualLateMinutes').val()) || 0;
        const earlyMinutes = parseFloat($('#manualEarlyMinutes').val()) || 0;
        const leaveBalance = parseFloat($('#leaveBalanceInput').val()) || 0;

        const absenceDeduction = (absenceDays * dailyRate).toFixed(2);
        const latenessDeduction = ((lateMinutes + earlyMinutes) * minuteRate).toFixed(2);
        const leaveCompensation = (leaveBalance > 0) ? (leaveBalance * dailyRate).toFixed(2) : '0.00';
        const negativeLeaveDeduction = (leaveBalance < 0) ? (Math.abs(leaveBalance) * dailyRate).toFixed(2) : '0.00';

        // Update the values in the financial table
        $('input[name="settlement[absence_deduction]"]').val(absenceDeduction);
        $('input[name="settlement[lateness_deduction]"]').val(latenessDeduction);
        $('input[name="settlement[compensation]"]').val(leaveCompensation);
        $('input[name="settlement[leave_balance_deduction]"]').val(negativeLeaveDeduction);

        // Also update the corresponding items in the financial table
        tableBody.find('tr').each(function() {
            const descInput = $(this).find('.desc-input');
            const amountInput = $(this).find('.amount-input');
            const description = descInput.val();
            
            if (description === 'خصم الغيابات') {
                amountInput.val(absenceDeduction);
            } else if (description === 'خصم التأخير والخروج المبكر') {
                amountInput.val(latenessDeduction);
            } else if (description === 'تعويض رصيد الإجازات') {
                amountInput.val(leaveCompensation);
            } else if (description === 'خصم رصيد الإجازة بالسالب') {
                amountInput.val(negativeLeaveDeduction);
            }
        });

        calculateTotal();
        alert('تمت إعادة حساب المستحقات والخصومات.');
    });

    // --- JAVASCRIPT FOR UPDATING LEAVE BALANCE ---
    $('#saveLeaveBalanceBtn').on('click', function() {
        // Safety check: prevent action if field is readonly
        if ($('#leaveBalanceInput').prop('readonly')) {
            alert('ليس لديك صلاحية لتعديل هذا الحقل.');
            return;
        }
        const employeeId = $('#employeeIdCell').text().trim();
        const newBalance = $('#leaveBalanceInput').val();

        if (!employeeId || newBalance === '') {
            alert('بيانات الموظف أو الرقم غير مكتملة.'); 
            return;
        }
        if (!confirm('هل أنت متأكد من تحديث رصيد الإجازات إلى ' + newBalance + '؟')) {
            return;
        }
        
        $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url: "<?php echo site_url('users1/ajax_update_leave_balance'); ?>",
            type: 'POST',
            data: { employee_id: employeeId, new_balance: newBalance },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alert(response.message);
                    $('#leaveBalanceInput').css('border-color', 'green');
                } else {
                    alert('خطأ: ' + response.message);
                    $('#leaveBalanceInput').css('border-color', 'red');
                }
            },
            error: function() {
                alert('حدث خطأ في الاتصال بالخادم.');
                $('#leaveBalanceInput').css('border-color', 'red');
            },
            complete: function() {
                $('#saveLeaveBalanceBtn').prop('disabled', false).html('حفظ');
            }
        });
    });

    // Approval Logic
  if (isApprovalMode) {
    // First, let's make sure the buttons exist
    const approveBtn = $('#approveBtn');
    const rejectBtn = $('#rejectBtn');
    
    // Debug: Check if buttons are found
    console.log('Approve button found:', approveBtn.length > 0);
    console.log('Reject button found:', rejectBtn.length > 0);
    
    if (approveBtn.length === 0 || rejectBtn.length === 0) {
        console.error('Approve/Reject buttons not found!');
        return;
    }
    
    // Initialize rejection modal
    const rejectionModalElement = document.getElementById('rejectionModal');
    let rejectionModal = null;
    if (rejectionModalElement) {
        rejectionModal = new bootstrap.Modal(rejectionModalElement);
    }
    
    const submitRejectionBtn = $('#submitRejectionBtn');
    const rejectionReasonText = $('#rejection_reason');
    
    // Get approval_task_id from PHP - make sure this is defined in your PHP
    const approvalTaskId = <?php echo isset($approval_task_id) ? json_encode($approval_task_id) : 'null'; ?>;
    
    // Debug: Check if task ID exists
    console.log('Approval Task ID:', approvalTaskId);
    
    if (!approvalTaskId) {
        console.error('Approval Task ID is not defined!');
        alert('خطأ: رقم المهمة غير محدد');
        approveBtn.prop('disabled', true);
        rejectBtn.prop('disabled', true);
        return;
    }
    
    // Define handleAction function
    const handleAction = function(action, reason = '') {
        console.log('handleAction called with:', action, 'reason:', reason);
        
        if (!confirm('هل أنت متأكد من ' + (action === 'approve' ? 'الموافقة' : 'الرفض') + '؟')) {
            return;
        }
        
        // Prepare the data
        const requestData = {
            task_id: approvalTaskId,
            action: action,
            rejection_reason: reason,
            // --- ADDED VERIFICATION DATA ---
            ver_signed: $('#ver_signed').is(':checked') ? 1 : 0,
            ver_mol:    $('#ver_mol').is(':checked') ? 1 : 0,
            ver_other:  $('#ver_other').is(':checked') ? 1 : 0,
            // -------------------------------
            '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
        };
        
        console.log('Sending data:', requestData);
        
        // Disable buttons to prevent double clicks
        approveBtn.prop('disabled', true);
        rejectBtn.prop('disabled', true);
        
        // Show loading state
        const originalApproveText = approveBtn.html();
        const originalRejectText = rejectBtn.html();
        
        if (action === 'approve') {
            approveBtn.html('<span class="spinner-border spinner-border-sm"></span> جاري التنفيذ...');
        } else {
            rejectBtn.html('<span class="spinner-border spinner-border-sm"></span> جاري التنفيذ...');
        }
        
        // Send AJAX request
        $.ajax({
            url: "<?php echo site_url('users1/update_settlement_status'); ?>",
            type: 'POST',
            data: requestData,
            dataType: 'json',
            success: function(response) {
                console.log('Response received:', response);
                
                if (response.status === 'success') {
                    alert(response.message);
                    // Redirect to approvals page
                    window.location.href = "<?php echo site_url('users1/eos_approvals'); ?>";
                } else {
                    alert('خطأ: ' + (response.message || 'حدث خطأ غير معروف'));
                    // Re-enable buttons
                    approveBtn.prop('disabled', false).html(originalApproveText);
                    rejectBtn.prop('disabled', false).html(originalRejectText);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error);
                alert('حدث خطأ في الاتصال بالخادم. الرجاء المحاولة مرة أخرى.');
                // Re-enable buttons
                approveBtn.prop('disabled', false).html(originalApproveText);
                rejectBtn.prop('disabled', false).html(originalRejectText);
            }
        });
    };
    
    // Attach click handlers
    approveBtn.on('click', function() { 
        console.log('Approve button clicked');
        handleAction('approve'); 
    });
    
    rejectBtn.on('click', function() { 
        console.log('Reject button clicked');
        if (rejectionModal) {
            rejectionModal.show();
        } else {
            alert('لم يتم العثور على نافذة الرفض');
        }
    });
    
    // Handle rejection submission
    submitRejectionBtn.on('click', function() {
        const reason = rejectionReasonText.val().trim();
        if (reason.length < 5) {
            alert('الرجاء إدخال سبب واضح للرفض (5 أحرف على الأقل).');
            return;
        }
        
        if (rejectionModal) {
            rejectionModal.hide();
        }
        handleAction('reject', reason);
    });
}
});
</script>

</body>
</html>