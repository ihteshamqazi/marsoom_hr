<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <style>
        body { font-family: 'Tajawal', sans-serif; background-color: #f4f6f9; }
        .card { box-shadow: 0 0 15px rgba(0,0,0,.05); border:0; }
        .card-header { background-color: #001f3f; color: white; }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3><?php echo $page_title; ?></h3>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo site_url('users1/save_clearance_parameter'); ?>" method="post">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            
                            <?php if (isset($parameter['id'])): ?>
                                <input type="hidden" name="id" value="<?php echo $parameter['id']; ?>">
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="department_id" class="form-label">الإدارة (القسم)</label>
                                <select class="form-select" id="department_id" name="department_id" required>
                                    <option value="">-- اختر الإدارة --</option>
                                    <?php foreach ($departments as $dept): ?>
                                        <option value="<?php echo $dept['id']; ?>" <?php echo (isset($parameter) && $parameter['department_id'] == $dept['id']) ? 'selected' : ''; ?>>
                                            <?php echo html_escape($dept['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="parameter_name" class="form-label">اسم المهمة</label>
                                <input type="text" class="form-control" id="parameter_name" name="parameter_name" 
                                       value="<?php echo isset($parameter) ? html_escape($parameter['parameter_name']) : ''; ?>" required>
                                <div class="form-text">مثال: "تسليم العهدة" أو "إغلاق الحسابات"</div>
                            </div>

                           <select class="form-select" id="approver_user_id" name="approver_user_id" required>
<option value="">-- اختر الموظف --</option>
 <?php foreach ($employees as $emp): ?>
 <option value="<?php echo $emp['employee_id']; ?>" <?php echo (isset($parameter) && $parameter['approver_user_id'] == $emp['employee_id']) ? 'selected' : ''; ?>>
<?php echo html_escape($emp['subscriber_name']) . ' (' . $emp['employee_id'] . ')'; ?>
 </option>
<?php endforeach; ?>
</select>
                            
                            <div class="mb-3">
                                <label for="is_active" class="form-label">الحالة</label>
                                <select class="form-select" id="is_active" name="is_active">
                                    <option value="1" <?php echo (isset($parameter) && $parameter['is_active'] == 1) ? 'selected' : ''; ?>>فعال</option>
                                    <option value="0" <?php echo (isset($parameter) && $parameter['is_active'] == 0) ? 'selected' : ''; ?>>غير فعال</option>
                                </select>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>حفظ</button>
                                <a href="<?php echo site_url('users1/clearance_parameters_list'); ?>" class="btn btn-secondary"><i class="fas fa-times me-2"></i>إلغاء</a>
                            </div>
                        </form>
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
            // Initialize Select2 for dropdowns
            $('#department_id').select2({ theme: 'bootstrap-5' });
            $('#approver_user_id').select2({ theme: 'bootstrap-5' });
        });
    </script>
</body>
</html>