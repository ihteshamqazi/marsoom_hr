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
    /* Using styles similar to your other pages for consistency */
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
    .search-container { max-width: 600px; margin: 40px auto; padding: 30px; background: #fff; border-radius: 12px; box-shadow: 0 6px 24px rgba(0,0,0,.08); text-align: center; }
    .search-container h2 { font-weight:800; color:#001f3f; margin-bottom:20px; }
    @media print {
        body { background:#fff; } .a4-sheet { margin:0; width:auto; min-height:auto; box-shadow:none; padding:0; } .no-print { display:none !important; }
    }
</style>
</head>
<body>

<?php if (empty($report_data)): ?>
    <div class="search-container">
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
        <a class="btn" href="<?php echo site_url('users1/export_resignation_report_excel/' . $res['id']); ?>"><i class="fas fa-file-excel me-2"></i>تصدير Excel</a>
        <a class="btn" href="<?php echo site_url('users1/export_resignation_report_pdf/' . $res['id']); ?>"><i class="fas fa-file-pdf me-2"></i>تصدير PDF</a>
    </div>

    <div class="a4-sheet">
        <div class="header"><h1>تقرير إنهاء الخدمة الشامل</h1></div>

        <div class="section-title">1. تفاصيل طلب الاستقالة</div>
        <table class="report-table">
            <tbody>
                <tr><th>اسم الموظف</th><td><?php echo html_escape($res['emp_name']); ?></td></tr>
                <tr><th>الرقم الوظيفي</th><td><?php echo html_escape($res['emp_id']); ?></td></tr>
                <tr><th>تاريخ آخر يوم عمل</th><td><?php echo html_escape($res['date_of_the_last_working']); ?></td></tr>
                <tr><th>سبب الإنهاء</th><td><?php echo html_escape($res['reason_for_resignation']); ?></td></tr>
                <tr><th>مقدم الطلب</th><td><?php echo html_escape($res['creator_name']); ?></td></tr>
                <tr><th>تاريخ تقديم الطلب</th><td><?php echo html_escape($res['date']) . ' ' . html_escape($res['time']); ?></td></tr>
            </tbody>
        </table>

       <!-- <div class="section-title">2. سجل اعتماد طلب الاستقالة</div>
        <table class="report-table" style="text-align:center;">
            <thead><tr style="background:#f1f3f5;"><th>المستوى</th><th>المسؤول</th><th>الحالة</th><th>التاريخ</th></tr></thead>
            <tbody>
                <?php foreach($report_data['approval_log'] as $log): ?>
                <tr>
                    <td><?php echo $log['approval_level']; ?></td>
                    <td><?php echo $log['approver_name']; ?></td>
                    <td><span class="status-badge status-<?php echo $log['status']; ?>"><?php echo $log['status']; ?></span></td>
                    <td><?php echo $log['action_date']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
-->
        <?php if ($report_data['settlement']): $set = $report_data['settlement']; ?>
        <div class="section-title">3. التسوية المالية النهائية</div>
        <table class="report-table">
            <tbody>
                <tr><th>مكافأة نهاية الخدمة</th><td><?php echo number_format($set['gratuity_amount'], 2); ?> ريال</td></tr>
                <tr><th>تعويض رصيد الإجازات</th><td><?php echo number_format($set['compensation'], 2); ?> ريال</td></tr>
                <tr><th>إجمالي المستحقات</th><td><b><?php echo number_format($set['gratuity_amount'] + $set['compensation'], 2); ?> ريال</b></td></tr>
                <tr><th>خصم التأمينات الاجتماعية</th><td><?php echo number_format($set['insurance_deduction'], 2); ?> ريال</td></tr>
                <tr><th>خصومات أخرى (غياب/سلف/جزاءات)</th><td><?php echo number_format($set['absence_deduction'] + $set['lateness_deduction'] + $set['penalty_clause_deduction'], 2); ?> ريال</td></tr>
                <tr style="background-color:#f0f8ff;"><th>المبلغ النهائي المستحق للموظف</th><td><b style="font-size: 1.1em;"><?php echo number_format($set['final_amount'], 2); ?> ريال</b></td></tr>
            </tbody>
        </table>
        <?php else: ?>
        <div class="section-title">3. التسوية المالية النهائية</div>
        <div class="alert alert-warning">لم تتم معالجة التسوية المالية لهذا الموظف بعد.</div>
        <?php endif; ?>

        <div class="section-title">4. حالة إخلاء الطرف</div>
        <?php if (!empty($report_data['clearances'])): ?>
        <table class="report-table" style="text-align:center;">
            <thead><tr style="background:#f1f3f5;"><th>الإدارة</th><th>المهمة المطلوب إنجازها</th><th>المسؤول</th><th>الحالة</th></tr></thead>
            <tbody>
                <?php foreach($report_data['clearances'] as $task): ?>
                <tr>
                    <td><?php echo html_escape($task['department_name']); ?></td>
                    <td style="text-align:right;"><?php echo html_escape($task['parameter_name']); ?></td>
                    <td><?php echo html_escape($task['approver_name']); ?></td>
                    <td><span class="status-badge status-<?php echo $task['status']; ?>"><?php echo html_escape($task['status']); ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="alert alert-warning">لم يتم بدء إجراءات إخلاء الطرف بعد.</div>
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