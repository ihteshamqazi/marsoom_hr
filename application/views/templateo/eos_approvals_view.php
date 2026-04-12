<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>اعتمادات مستحقات نهاية الخدمة</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
<style>
    body { font-family: 'Tajawal', sans-serif; background-color: #f4f6f9; }
    .page-title { font-weight: 800; color: #001f3f; border-bottom: 2px solid #001f3f; padding-bottom: 10px; }
    .card { border: none; border-radius: .75rem; box-shadow: 0 4px 25px rgba(0,0,0, .08); }
    .table thead th { background-color: #f8f9fa; font-weight: 600; text-align: center; }
    .table tbody td { vertical-align: middle; text-align: center; }
    .badge { font-size: 0.9em; font-weight: 500; padding: .5em .9em; }
</style>
</head>
<body>
    <div class="container my-5">
        <h1 class="page-title text-center mb-4">سجل اعتمادات نهاية الخدمة</h1>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>اسم الموظف</th>
                                <th>تاريخ التقديم</th>
                                <th>حالة المهمة</th>
                                <th>تاريخ الإجراء</th>
                                <th>الإجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($all_eos_tasks)): ?>
                                <tr><td colspan="5" class="text-center text-muted py-4">لا توجد أي مهام مسندة إليك.</td></tr>
                            <?php else: ?>
                                <?php foreach ($all_eos_tasks as $task): ?>
                                    <tr>
                                        <td><?php echo html_escape($task['emp_name']); ?></td>
                                        <td><?php echo date('Y-m-d', strtotime($task['submission_date'])); ?></td>
                                        <td>
                                            <?php if ($task['approval_status'] === 'pending'): ?>
                                                <span class="badge bg-warning text-dark">مهمة جديدة</span>
                                            <?php elseif ($task['approval_status'] === 'approved'): ?>
                                                <span class="badge bg-success">تمت الموافقة</span>
                                            <?php elseif ($task['approval_status'] === 'rejected'): ?>
                                                <span class="badge bg-danger">تم الرفض</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $task['action_date'] ? date('Y-m-d H:i', strtotime($task['action_date'])) : '—'; ?></td>
                                        <td>
                                            <?php if ($task['approval_status'] === 'pending'): ?>
                                                <a href="<?php echo site_url('users1/end_of_service?task_id=' . $task['approval_id']); ?>" class="btn btn-primary btn-sm">
                                                    <i class="fa-solid fa-eye me-1"></i> عرض ومراجعة
                                                </a>
                                            <?php else: ?>
                                                <a href="<?php echo site_url('users1/end_of_service?task_id=' . $task['approval_id']); ?>" class="btn btn-outline-secondary btn-sm">
                                                    <i class="fa-solid fa-search me-1"></i> عرض التفاصيل
                                                </a>
                                            <?php endif; ?>
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