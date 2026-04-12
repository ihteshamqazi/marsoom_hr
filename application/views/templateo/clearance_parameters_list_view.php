<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة مهام المخالصة</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Tajawal', sans-serif; background-color: #f4f6f9; }
        .card { box-shadow: 0 0 15px rgba(0,0,0,.05); border:0; }
        .card-header { background-color: #001f3f; color: white; }
         .no-print { display:flex; flex-wrap:wrap; gap:.5rem; justify-content:center; margin:12px auto; padding: 0 14mm; }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="card">
            <a class="btn" href="<?php echo site_url('users1/main_hr1'); ?>"><i class="fas fa-home me-2"></i>الرئيسية </a>
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3><i class="fas fa-list-check me-2"></i>مهام إخلاء الطرف</h3>
                <a href="<?php echo site_url('users1/add_clearance_parameter'); ?>" class="btn btn-light">
                    <i class="fas fa-plus me-1"></i> إضافة مهمة جديدة
                </a>
            </div>
            <div class="card-body">
                <?php if($this->session->flashdata('success')): ?>
                    <div class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
                <?php endif; ?>
                <?php if($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>الإدارة</th>
                                <th>اسم المهمة</th>
                                <th>الموظف المسؤول</th>
                                <th>الحالة</th>
                                <th>إجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($parameters)): ?>
                                <tr><td colspan="5" class="text-center text-muted py-4">لا توجد مهام معرفة حالياً.</td></tr>
                            <?php else: ?>
                                <?php foreach ($parameters as $param): ?>
                                    <tr>
                                        <td><?php echo html_escape($param['department_name'] ?? 'N/A'); ?></td>
                                        <td><?php echo html_escape($param['parameter_name']); ?></td>
                                        <td><?php echo html_escape($param['approver_name'] ?? 'N/A'); ?> (<?php echo html_escape($param['approver_user_id']); ?>)</td>
                                        <td>
                                            <?php if ($param['is_active']): ?>
                                                <span class="badge bg-success">فعال</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">غير فعال</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo site_url('users1/edit_clearance_parameter/' . $param['id']); ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?php echo site_url('users1/delete_clearance_parameter/' . $param['id']); ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('هل أنت متأكد من حذف هذه المهمة؟')">
                                                <i class="fas fa-trash"></i>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>