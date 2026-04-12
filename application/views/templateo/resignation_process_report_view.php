<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>تقرير إنهاء الخدمة</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>
    @page { size: A4; margin: 12mm; }
    html, body { font-family:'Tajawal',system-ui,sans-serif; background:#f4f6f9; color:#111; }
    .a4-sheet { max-width:210mm; min-height:297mm; margin:10mm auto; background:#fff; box-shadow:0 6px 24px rgba(0,0,0,.08); padding:14mm; }
    .no-print { display:flex; gap:.5rem; justify-content:center; max-width: 210mm; margin: 12px auto; }
    .btn { border:1px solid #001f3f; color:#001f3f; background:#fff; padding:8px 14px; border-radius:8px; font-weight:700; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:8px; }
    .btn:hover { background:#001f3f; color:#fff; }
    .header { text-align:center; padding-bottom:10px; border-bottom:2px solid #001f3f; margin-bottom:14px; }
    .header h1 { font-size:20px; margin:0; font-weight:800; color:#001f3f; }
    .section-title { margin:18px 0 10px; padding:8px 10px; background:#f7f9fc; border-right:4px solid #FF8C00; font-weight:800; }
    .report-table { width:100%; border-collapse:collapse; margin-top:6px; }
    .report-table th, .report-table td { border:1px solid #d9dee3; padding:6px 8px; font-size:13px; vertical-align:middle; }
    .report-table th { background:#f1f3f5; font-weight:700; text-align:right; width: 25%;}
    .status-badge { padding: .25em .6em; font-size: .75em; font-weight: 700; border-radius: .375rem; }
    .status-pending { background-color: #fff3cd; color: #664d03; }
    .status-approved { background-color: #d1e7dd; color: #0a3622; }
    .status-rejected { background-color: #f8d7da; color: #58151c; }
    .status-created { background-color: #e2e3e5; color: #383d41; }
    .top-actions a{background:rgba(255,255,255,.12);border:1px solid var(--glass-border);color:#000;text-decoration:none;border-radius:10px;padding:8px 14px;display:inline-flex;align-items:center;gap:8px;transition:.25s}
    .search-container { max-width: 600px; margin: 40px auto; padding: 30px; background: #fff; border-radius: 12px; box-shadow: 0 6px 24px rgba(0,0,0,.08); text-align: center; }
    .search-container h2 { font-weight:800; color:#001f3f; margin-bottom:20px; }
    .alert { padding: 1rem; margin-bottom: 1rem; border: 1px solid transparent; border-radius: .25rem; }
    .alert-info { color: #0c5460; background-color: #d1ecf1; border-color: #bee5eb; font-size: 0.85rem; }
    .alert-warning { color: #856404; background-color: #fff3cd; border-color: #ffeeba; }
    @media print {
        body { background:#fff; } .a4-sheet { margin:0; width:auto; min-height:auto; box-shadow:none; padding:0; } .no-print { display:none !important; }
    }
</style>
</head>
<body>

<?php if (empty($report_data)): ?>
    <div class="search-container">
         <div class="top-actions" style="color:black;">
        <a href="<?php echo site_url('users1/main_hr1'); ?>" style="text-decoration: none;"><i class="fas fa-home"></i> الرئيسية</a>
    </div>
        <h2>تقرير عملية إنهاء الخدمة</h2>
        <p>الرجاء اختيار الموظف لعرض تقرير إنهاء الخدمة الخاص به.</p>
        <form method="get" action="<?php echo site_url('users1/resignation_process_report'); ?>" class="mt-4">
            <div class="mb-3">
                <select name="resignation_id" id="resignation_search" class="form-select" required>
                    <option></option>
                    <?php foreach($resignations_list as $res): ?>
                        <option value="<?php echo html_escape($res['id']); ?>">
                            <?php echo html_escape($res['emp_name']) . ' (' . html_escape($res['emp_id']) . ')'; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button class="btn btn-success" type="submit">عرض التقرير</button>
        </form>
    </div>
<?php else: $res = $report_data['resignation']; ?>
    <div class="no-print">
        <a class="btn" href="<?php echo site_url('users1/resignation_process_report'); ?>"><i class="fas fa-search me-2"></i>بحث جديد</a>
        <a class="btn" href="javascript:window.print()"><i class="fas fa-print me-2"></i>طباعة</a>
        
        <?php 
            // Generate the URL for the specific employee's form
            $form_url = site_url('users1/end_of_service?emp=' . $res['emp_id']);
        ?>
        <a class="btn" 
           href="<?php echo $form_url; ?>" 
           onclick="window.open(this.href, 'EOS_Form_Popup', 'width=1200,height=900,scrollbars=yes,resizable=yes'); return false;"
           style="border-color: var(--marsom-orange); color: var(--marsom-orange);">
            <i class="fas fa-external-link-alt me-2"></i> عرض النموذج الأصلي
        </a>
        <a class="btn" href="<?php echo site_url('users1/export_resignation_report_excel/' . $res['id']); ?>"><i class="fas fa-file-excel me-2"></i>تصدير Excel</a>
        <a class="btn" href="<?php echo site_url('users1/export_resignation_report_pdf/' . $res['id']); ?>"><i class="fas fa-file-pdf me-2"></i>تصدير PDF</a>
        <a class="btn" href="<?php echo site_url('users1/print_clearance_form/' . $res['id']); ?>" target="_blank" style="border-color: #28a745; color: #28a745;">
    <i class="fas fa-file-signature me-2"></i> طباعة نموذج إخلاء الطرف
</a>
    </div>

    <div class="a4-sheet">
        <div class="header"><h1>تقرير إنهاء الخدمة الشامل</h1></div>

        <div class="section-title">1. تفاصيل طلب الاستقالة</div>
        <table class="report-table">
            <tbody>
                <tr><th>اسم الموظف</th><td><?php echo html_escape($res['emp_name']); ?></td></tr>
                <tr><th>الرقم الوظيفي</th><td><?php echo html_escape($res['emp_id']); ?></td></tr>
                <tr><th>تاريخ آخر يوم عمل</th><td><?php echo html_escape($res['date_of_the_last_working']); ?></td></tr>
                
                <tr><th>مدة الخدمة</th><td><?php echo html_escape($report_data['service_period'] ?? 'غير متوفر'); ?></td></tr>
                
                <tr>
                    <th>رصيد الإجازات (عند التسوية)</th>
                    <td><?php echo number_format($report_data['annual_leave_balance'] ?? 0, 2); ?> يوم</td>
                </tr>
                
                <tr><th>سبب الإنهاء</th><td><?php echo html_escape($res['reason_for_resignation']); ?></td></tr>
                <tr><th>تاريخ تقديم الطلب</th><td><?php echo html_escape($res['date']) . ' ' . html_escape($res['time']); ?></td></tr>
            </tbody>
        </table>

        <div class="section-title">2. سجل اعتماد التسوية المالية</div>
        <?php if (!empty($report_data['approval_log'])): ?>
            <table class="report-table" style="text-align:center;">
                <thead><tr style="background:#f1f3f5;"><th style="width:20%">المستوى</th><th style="width:20%">المسؤول</th><th style="width:20%">الحالة</th><th style="width:20%">سبب الرفض</th><th style="width:20%">التاريخ</th></tr></thead>
                <tbody>
                    <?php foreach($report_data['approval_log'] as $log): ?>
                    <tr>
                        <td><?php echo $log['approval_level']; ?></td>
                        <td><?php echo html_escape($log['approver_name'] ?? $log['approver_id']); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $log['status']; ?>">
                                <?php 
                                    if ($log['status'] === 'created') {
                                        echo 'تم الإنشاء';
                                    } else {
                                        echo $log['status'];
                                    }
                                ?>
                            </span>
                        </td>
                         <td><?php echo $log['rejection_reason']; ?></td>
                        <td><?php echo $log['action_date']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info mt-2">لم يتم بدء إجراءات اعتماد التسوية المالية بعد.</div>
        <?php endif; ?>

<div class="section-title">3. التسوية المالية النهائية</div>
<?php 
// Use $report_data['settlement'] for the final amount and $report_data['settlement_items'] for the list
$settlement_data = $report_data['settlement'] ?? null;
$settlement_items = $report_data['settlement_items'] ?? []; // This comes from items_json

if (!empty($settlement_data) && !empty($settlement_items)): 
    $total_payments = 0;
    $total_deductions = 0;
?>
<table class="report-table">
    <thead>
        <tr style="background:#f1f3f5;">
            <th style="width: 60%;">البند</th>
            <th style="width: 20%;">النوع</th>
            <th style="width: 20%; text-align: center;">المبلغ (ريال)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($settlement_items as $item): ?>
            <?php
                $amount = (float)($item['amount'] ?? 0);
                $is_payment = (isset($item['type']) && $item['type'] === 'payment');
                
                if ($is_payment) {
                    $total_payments += $amount;
                    $type_label = 'مستحق';
                    $style = '';
                } else {
                    $total_deductions += $amount;
                    $type_label = 'خصم';
                    $style = 'color: #dc3545;'; // Red color for deductions
                }
            ?>
            <tr style="<?php echo $style; ?>">
                <td><?php echo html_escape($item['description'] ?? 'بند غير مسمى'); ?></td>
                <td><?php echo $type_label; ?></td>
                <td style="text-align: center;"><?php echo number_format($amount, 2); ?></td>
            </tr>
        <?php endforeach; ?>
        
        <tr style="background-color:#f8f9fa;">
            <th colspan="2">إجمالي المستحقات</th>
            <td style="text-align: center;"><b><?php echo number_format($total_payments, 2); ?> ريال</b></td>
        </tr>
        <tr style="background-color:#fff3cd;">
            <th colspan="2">إجمالي الخصومات</th>
            <td style="text-align: center;"><b><?php echo number_format($total_deductions, 2); ?> ريال</b></td>
        </tr>
        <tr style="background-color:#d1e7dd;">
            <th colspan="2">المبلغ النهائي المستحق للموظف</th>
            <td style="text-align: center;"><b style="font-size: 1.1em;"><?php echo number_format((float)($settlement_data['final_amount'] ?? 0), 2); ?> ريال</b></td>
        </tr>
    </tbody>
</table>
<?php else: ?>
<div class="alert alert-warning mt-2">لم تتم معالجة التسوية المالية لهذا الموظف بعد أو لا تحتوي على بنود.</div>
<?php endif; ?>

        <div class="section-title">4. حالة إخلاء الطرف</div>
<?php if (!empty($report_data['grouped_clearances'])): ?>
    <?php foreach($report_data['grouped_clearances'] as $dept_name => $tasks_in_dept): ?>
        <h5 class="mt-3 mb-2 ps-2" style="font-weight: bold; border-right: 3px solid #dee2e6;"><?php echo html_escape($dept_name); ?></h5>
        <table class="report-table mb-3" style="text-align:center;">
            <thead>
                <tr style="background:#f1f3f5;">
                    <th style="width:35%">المهمة المطلوب إنجازها</th>
                    <th style="width:25%">المسؤول</th>
                    <th style="width:15%">الحالة</th>
                    <th style="width:25%">تاريخ الإجراء/آخر تحديث</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($tasks_in_dept as $task): ?>
                <tr>
                    <td style="text-align:right;"><?php echo html_escape($task['parameter_name'] ?: ($task['task_description'] ?? 'مهمة خاصة')); ?></td>
                    <td><?php echo html_escape($task['approver_name'] ?? $task['approver_user_id'] ?? '—'); ?></td>
                    <td>
    <span class="status-badge status-<?php echo html_escape($task['status']); ?>">
        <?php 
        if ($task['status'] == 'approved') {
            echo 'نعم';
        } elseif ($task['status'] == 'pending') {
            echo 'لا';
        } else {
            echo html_escape($task['status']); // fallback for other statuses
        }
        ?>
    </span>
</td>
                    <td>
    <?php
        $display_date = (!empty($task['updated_at'])) ? $task['updated_at'] : ($task['created_at'] ?? 'N/A');
        echo html_escape($display_date);
    ?>
</td>
                </tr>
                   <?php if (!empty($task['rejection_reason'])): ?>
                   <tr class="table-danger">
                       <td colspan="4" style="text-align:right; font-size: 0.9em; padding-top: 2px; padding-bottom: 2px;">
                           <i class="fas fa-exclamation-circle me-1"></i> <strong>سبب الرفض:</strong> <?php echo html_escape($task['rejection_reason']); ?>
                       </td>
                   </tr>
                   <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>
<?php else: ?>
    <div class="alert alert-warning mt-2">لم يتم بدء إجراءات إخلاء الطرف بعد أو لا توجد مهام محددة.</div>
<?php endif; ?>
    </div>
<?php endif; ?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    if ($('#resignation_search').length) {
        $('#resignation_search').select2({
            theme: 'bootstrap-5',
            placeholder: 'ابحث بالاسم أو الرقم الوظيفي...',
            allowClear: true
        });
    }
});
</script>

</body>
</html>