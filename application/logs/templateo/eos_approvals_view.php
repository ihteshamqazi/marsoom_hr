<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>اعتمادات مستحقات نهاية الخدمة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Tajawal', sans-serif; }
        .container { max-width: 1200px; }
        .page-title { font-weight: 800; color: #0E1F3B; }
    </style>
</head>
<body>
    <div class="container my-5">
        <h1 class="page-title text-center mb-4">اعتمادات مستحقات نهاية الخدمة</h1>
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>رقم المهمة</th>
                                <th>اسم الموظف</th>
                                <th>تاريخ التقديم</th>
                                <th>الحالة الحالية</th>
                                <th>الإجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pending_eos_tasks)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">لا توجد مهام معلقة حاليًا.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($pending_eos_tasks as $task): ?>
                                    <tr>
                                        <td><?php echo html_escape($task['approval_id']); ?></td>
                                        <td><?php echo html_escape($task['emp_name']); ?></td>
                                        <td><?php echo date('Y-m-d', strtotime($task['date'])); ?></td>
                                        <td>
                                            <span class="badge bg-warning text-dark">
                                                بانتظار الموافقة
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?php echo site_url($task['url_prefix'] . $task['url_suffix']); ?>" class="btn btn-primary btn-sm">
                                                <i class="fa-solid fa-eye me-1"></i>
                                                عرض ومراجعة
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>