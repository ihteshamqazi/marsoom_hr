<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير فروقات الرواتب</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Tajawal', sans-serif; background-color: #f4f6f9; }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .diff-pos { color: #198754; font-weight: bold; }
        .diff-neg { color: #dc3545; font-weight: bold; }
        .bg-gradient-header { background: linear-gradient(135deg, #001f3f 0%, #1e3c72 100%); color: white; }
    </style>
</head>
<body>

<div class="container mt-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="fas fa-balance-scale text-primary"></i> تقرير فروقات الرواتب</h3>
        <a href="<?= site_url('users1/main_hr1') ?>" class="btn btn-outline-secondary">الرئيسية</a>
    </div>

    <div class="card card-custom mb-4">
        <div class="card-body">
            <form method="GET" action="<?= site_url('users1/salary_variance_report') ?>" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-bold">المسير الحالي (الجديد)</label>
                    <select name="current_sheet" class="form-select" required>
                        <option value="">-- اختر المسير --</option>
                        <?php foreach($salary_sheets as $sheet): ?>
                            <option value="<?= $sheet['type'] ?>" <?= $selected_current == $sheet['type'] ? 'selected' : '' ?>>
                                <?= $sheet['type'] ?> (<?= $sheet['start_date'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-bold">المسير السابق (للمقارنة)</label>
                    <select name="previous_sheet" class="form-select" required>
                        <option value="">-- اختر المسير --</option>
                        <?php foreach($salary_sheets as $sheet): ?>
                            <option value="<?= $sheet['type'] ?>" <?= $selected_previous == $sheet['type'] ? 'selected' : '' ?>>
                                <?= $sheet['type'] ?> (<?= $sheet['start_date'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">
                        <i class="fas fa-search"></i> مقارنة
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php if(isset($variance_data) && !empty($variance_data)): ?>
    <div class="card card-custom">
        <div class="card-header bg-gradient-header">
            <h5 class="mb-0 text-white">نتائج المقارنة</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>الموظف</th>
                            <th>الحالة</th>
                            <th>الصافي السابق</th>
                            <th>الصافي الحالي</th>
                            <th>الفرق (Variance)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($variance_data as $row): 
                            $badge = '';
                            if($row['status'] == 'new') $badge = '<span class="badge bg-success">موظف جديد / عائد</span>';
                            elseif($row['status'] == 'left') $badge = '<span class="badge bg-danger">راتب موقف / مستبعد</span>';
                            else $badge = '<span class="badge bg-warning text-dark">تغير في الراتب</span>';
                            
                            $diff_class = $row['diff'] > 0 ? 'diff-pos' : 'diff-neg';
                            $icon = $row['diff'] > 0 ? '<i class="fas fa-arrow-up"></i>' : '<i class="fas fa-arrow-down"></i>';
                        ?>
                        <tr>
                            <td>
                                <div class="fw-bold"><?= $row['name'] ?></div>
                                <small class="text-muted">#<?= $row['emp_id'] ?></small>
                            </td>
                            <td><?= $badge ?></td>
                            <td><?= number_format($row['prev_net'], 2) ?></td>
                            <td><?= number_format($row['curr_net'], 2) ?></td>
                            <td class="<?= $diff_class ?>" dir="ltr">
                                <?= number_format($row['diff'], 2) ?> <?= $icon ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php elseif($this->input->get('current_sheet')): ?>
        <div class="alert alert-success text-center">
            <i class="fas fa-check-circle fa-2x mb-2"></i><br>
            لا توجد فروقات! الرواتب متطابقة تماماً بين الفترتين.
        </div>
    <?php endif; ?>

</div>
</body>
</html>