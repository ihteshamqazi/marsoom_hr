<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إعدادات سياسات الانتداب</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        /* Marsom Theme */
        body { font-family: 'Tajawal', sans-serif; background: linear-gradient(135deg, #001f3f, #34495e, #FF8C00); min-height: 100vh; }
        .glass-card { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); border-radius: 16px; padding: 30px; margin: 30px auto; max-width: 1200px; }
        .table thead th { background-color: #001f3f; color: white; }
    </style>
</head>
<body>
<div class="glass-card">
    <div class="d-flex justify-content-between mb-4">
        <h3><i class="fas fa-cogs"></i> إعدادات الانتداب (Limits)</h3>
        <button class="btn btn-warning fw-bold" onclick="$('#policyModal').modal('show')">+ سياسة جديدة</button>
    </div>
    <table class="table table-hover text-center align-middle">
        <thead>
            <tr>
                <th>المستوى</th>
                <th>المسافة (KM)</th>
                <th>النقل</th>
                <th>الحد الأدنى (يومي)</th>
                <th>الحد الأعلى (يومي)</th>
                <th>حذف</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($policies as $p): ?>
            <tr>
                <td><?= $p['employee_tag'] ?></td>
                <td dir="ltr"><?= $p['min_km'] ?> - <?= $p['max_km'] ?></td>
                <td><?= $p['transport_type'] == 'air' ? 'طيران' : 'سيارة' ?></td>
                <td class="text-success fw-bold"><?= $p['min_daily_amount'] ?> SAR</td>
                <td class="text-danger fw-bold"><?= $p['max_daily_amount'] ?> SAR</td>
                <td><a href="<?= site_url('users1/delete_policy/'.$p['id']) ?>" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="policyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white"><h5 class="modal-title">إضافة سياسة</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="<?= site_url('users1/save_policy_ajax') ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="mb-2"><label>المستوى</label><select name="employee_tag" class="form-select"><option value="Employee">Employee</option><option value="Manager">Manager</option></select></div>
                    <div class="row g-2 mb-2">
                        <div class="col-6"><label>من كم</label><input type="number" name="min_km" class="form-control" required></div>
                        <div class="col-6"><label>إلى كم</label><input type="number" name="max_km" class="form-control" required></div>
                    </div>
                    <div class="mb-2"><label>النقل</label><select name="transport_type" class="form-select"><option value="road">سيارة</option><option value="air">طيران</option></select></div>
                    <div class="row g-2">
                        <div class="col-6"><label>Min (Min Allow)</label><input type="number" name="min_daily_amount" class="form-control" value="275"></div>
                        <div class="col-6"><label>Max (Max Allow)</label><input type="number" name="max_daily_amount" class="form-control" value="550"></div>
                    </div>
                    <input type="hidden" name="salary_multiplier" value="1"> 
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary w-100">حفظ</button></div>
            </form>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>