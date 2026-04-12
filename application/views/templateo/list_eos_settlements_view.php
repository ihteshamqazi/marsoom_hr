<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>قائمة مستحقات نهاية الخدمة</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
         body{font-family:'Tajawal',sans-serif;overflow:hidden;background:linear-gradient(135deg,var(--marsom-blue) 0%,#34495e 50%,var(--marsom-orange) 100%);background-size:400% 400%;animation:grad 20s ease infinite;color:var(--text-dark);position:relative}
    @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}

        .table-card { background-color: #fff; border-radius: 0.75rem; box-shadow: 0 0.5rem 1rem rgba(0,0,0,.1); padding: 1.5rem; margin-top: 2rem; }
        .table th { background-color: #001f3f; color: #fff; font-weight: bold; }
        .table td, .table th { vertical-align: middle; text-align: center; }
        .badge { font-size: 0.9em; padding: 0.4em 0.7em; }
        .action-links a { margin-left: 0.5rem; }
         .filter-bar { background-color: #e9ecef; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; }
    </style>
</head>
<body>
    <div class="container my-4">
       <div class="d-flex justify-content-between align-items-center mb-3">
             <h2 class="mb-0 fw-bold"><i class="fas fa-list-check me-2"></i>مستحقات نهاية الخدمة</h2>
             
             <div>
                 <a href="<?php echo site_url('users1/end_of_service'); ?>" 
                    onclick="window.open(this.href, 'EOS_Create_Popup', 'width=1200,height=900,scrollbars=yes,resizable=yes'); return false;"
                    class="btn btn-success">
                     <i class="fas fa-plus me-1"></i> إنشاء تسوية جديدة
                 </a>

                 <a href="<?php echo site_url('users1/main_hr1'); ?>" class="btn btn-outline-secondary">
                     <i class="fas fa-arrow-left me-1"></i> العودة للرئيسية
                 </a>
             </div>
        </div>

       <div class="filter-bar">
    <form method="get" action="<?php echo site_url('users1/list_eos_settlements'); ?>" class="row g-3 align-items-end">
        
        <div class="col-md-4">
            <label for="employeeIdSearch" class="form-label fw-bold">بحث برقم الموظف:</label>
            <div class="input-group">
                <span class="input-group-text bg-white text-secondary"><i class="fas fa-id-card"></i></span>
                <input type="text" 
                       id="employeeIdSearch" 
                       name="employee_id" 
                       class="form-control" 
                       placeholder="أدخل الرقم الوظيفي..." 
                       value="<?php echo isset($_GET['employee_id']) ? html_escape($_GET['employee_id']) : ''; ?>">
            </div>
        </div>

        <div class="col-md-4">
            <label for="statusFilter" class="form-label fw-bold">تصفية حسب الحالة:</label>
            <select id="statusFilter" name="status" class="form-select">
                <option value="">الكل</option>
                <?php foreach ($status_labels as $status_key => $status_label): ?>
                    <option value="<?php echo $status_key; ?>" <?php echo ($current_filter === $status_key) ? 'selected' : ''; ?>>
                        <?php echo html_escape($status_label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-auto">
            <button type="submit" class="btn btn-primary px-4"><i class="fas fa-search me-1"></i> بحث</button>
            <a href="<?php echo site_url('users1/list_eos_settlements'); ?>" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>
       <div class="table-card">
            <?php if (empty($settlements)): ?>
                <div class="alert alert-info text-center">لا توجد مستحقات مسجلة حالياً تطابق الفلتر المحدد.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>رقم الموظف</th>
                                <th>اسم الموظف</th>
                                <th>الحالة</th>
                                <th>المسؤول الحالي</th>
                                <th>المبلغ النهائي</th>
                                <th>تاريخ الإنشاء</th>
                                <th>أنشئ بواسطة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                       <tbody>
    <?php foreach ($settlements as $index => $item): ?>
    <tr>
        <td><?php echo $index + 1; ?></td>
        <td><?php echo html_escape($item['employee_id']); ?></td>
        <td><?php echo html_escape($item['employee_name']); ?></td>
        
        <td>
            <span class="badge <?php
                switch ($item['status']) {
                    case 'approved': echo 'bg-success'; break;
                    case 'rejected': echo 'bg-danger'; break;
                    default: echo 'bg-warning'; break;
                }
            ?>">
                <?php echo html_escape($status_labels[$item['status']] ?? $item['status']); ?>
            </span>
        </td>
        
        <td><?php echo html_escape($item['current_approver_name'] ?? '—'); ?></td>
        <td><?php echo number_format((float)$item['final_amount'], 2); ?> SAR</td>
        <td><?php echo date('Y-m-d H:i', strtotime($item['created_at'])); ?></td>
        <td><?php echo html_escape($item['creator_name'] ?? $item['created_by_id'] ?? '—'); ?></td>
        
        <td class="action-links text-nowrap">
    
    <a href="<?php echo site_url('users1/resignation_process_report?resignation_id=' . $item['resignation_order_id']); ?>" 
       class="btn btn-sm btn-outline-info" 
       title="عرض التقرير">
        <i class="fas fa-eye"></i>
    </a>

    <a href="<?php echo site_url('users1/print_eos_settlement_view/' . $item['resignation_order_id']); ?>" 
       class="btn btn-sm btn-outline-danger" 
       target="_blank"
       title="طباعة المخالصة">
        <i class="fas fa-print"></i>
    </a>

</td>
    </tr>
    <?php endforeach; ?>
</tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>