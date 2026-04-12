<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>أرصدة إجازات الفريق</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        body { font-family: 'Tajawal', sans-serif; background-color: #f4f6f9; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .table thead th { background-color: #001f3f; color: white; border: none; vertical-align: middle; }
        .progress-thin { height: 6px; margin-top: 5px; background-color: #e9ecef; }
        .badge-bal { font-size: 0.85rem; padding: 5px 10px; border-radius: 6px; min-width: 40px; display: inline-block; text-align: center; }
        .bg-rem-high { background-color: #d1e7dd; color: #0f5132; }
        .bg-rem-low { background-color: #f8d7da; color: #842029; }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="fas fa-wallet text-success"></i> أرصدة إجازات الفريق</h3>
        <a href="<?= site_url('users1/main_hr1') ?>" class="btn btn-outline-secondary">الرئيسية</a>
    </div>

    <div class="card card-custom">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 text-center">
                    <thead>
                        <tr>
                            <th class="text-start pe-4">الموظف</th>
                            <th>سنوية (متبقي)</th>
                            <th>سنوية (مستهلك)</th>
                            <th>مرضية (متبقي)</th>
                            <th>طارئة (مستهلك)</th>
                            <th>غير مدفوعة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($team_balances)): ?>
                            <tr><td colspan="6" class="py-4 text-muted">لا توجد بيانات متاحة.</td></tr>
                        <?php else: ?>
                            <?php foreach($team_balances as $emp): 
                                $bal = $emp['balances'];
                                $annual_rem = $bal['annual']['remaining'] ?? 0;
                                $annual_tot = $bal['annual']['total'] ?? 0;
                                $annual_used = $bal['annual']['used'] ?? 0;
                                $sick_rem = $bal['sick']['remaining'] ?? 0;
                                $emerg_used = $bal['emergency']['used'] ?? 0; // Assuming emergency tracks usage
                                $unpaid_used = $bal['unpaid']['used'] ?? 0;

                                // Progress bar calc
                                $percent = ($annual_tot > 0) ? ($annual_rem / $annual_tot) * 100 : 0;
                                $color_class = ($annual_rem < 5) ? 'bg-danger' : 'bg-success';
                                $bg_class = ($annual_rem < 5) ? 'bg-rem-low' : 'bg-rem-high';
                            ?>
                            <tr>
                                <td class="text-start pe-4">
                                    <div class="fw-bold text-dark"><?= $emp['info']['name'] ?></div>
                                    <div class="small text-muted">#<?= $emp['info']['id'] ?> - <?= $emp['info']['dept'] ?></div>
                                </td>
                                <td>
                                    <span class="badge-bal <?= $bg_class ?> fw-bold"><?= $annual_rem ?></span>
                                    <div class="progress progress-thin">
                                        <div class="progress-bar <?= $color_class ?>" style="width: <?= $percent ?>%"></div>
                                    </div>
                                </td>
                                <td class="text-muted"><?= $annual_used ?></td>
                                <td class="text-info fw-bold"><?= $sick_rem ?></td>
                                <td><?= $emerg_used > 0 ? '<span class="text-warning fw-bold">'.$emerg_used.'</span>' : '-' ?></td>
                                <td><?= $unpaid_used > 0 ? '<span class="text-danger fw-bold">'.$unpaid_used.'</span>' : '-' ?></td>
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