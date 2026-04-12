<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>سجل الحضور: <?= $employee_name ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; background: #f4f7fa; padding: 20px; }
        .card { border: none; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); border-radius: 10px; }
        .table-responsive { border-radius: 10px; background: white; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4 class="text-primary mb-0"><?= $employee_name ?></h4>
                <small class="text-muted">الرقم الوظيفي: <?= $employee_id ?> | الفترة: <?= $sheet_period ?></small>
            </div>
            <div class="text-end">
                <span class="badge bg-primary fs-6">قيمة اليوم: <?= number_format($salary_details['daily'], 2) ?></span>
                <span class="badge bg-info text-dark fs-6">قيمة الدقيقة: <?= number_format($salary_details['minute'], 2) ?></span>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover text-center align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>التاريخ</th><th>اليوم</th><th>دخول</th><th>خروج</th><th>الحالة</th><th>الخصم التقديري</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total_cost = 0;
                foreach($daily_log as $row): 
                    $cost = 0;
                    if($row['is_absent'] || $row['is_single']) $cost = $salary_details['daily'];
                    else $cost = ($row['late'] + $row['early']) * $salary_details['minute'];
                    $total_cost += $cost;
                ?>
                <tr class="<?= $row['class'] ?>">
                    <td><?= $row['date'] ?></td>
                    <td><?= $row['day'] ?></td>
                    <td dir="ltr"><?= $row['in'] ?></td>
                    <td dir="ltr"><?= $row['out'] ?></td>
                    <td>
                        <strong><?= $row['status'] ?></strong>
                        <?php if($row['late'] > 0) echo "<br><small class='text-danger'>تأخير {$row['late']} د</small>"; ?>
                        <?php if($row['early'] > 0) echo "<br><small class='text-danger'>مبكر {$row['early']} د</small>"; ?>
                    </td>
                    <td class="fw-bold <?= $cost > 0 ? 'text-danger' : '' ?>"><?= $cost > 0 ? number_format($cost, 2) : '-' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="table-light fw-bold">
                <tr><td colspan="5" class="text-end">إجمالي الخصم المحسوب من السجل:</td><td><?= number_format($total_cost, 2) ?> ر.س</td></tr>
            </tfoot>
        </table>
    </div>
</div>
</body>
</html>