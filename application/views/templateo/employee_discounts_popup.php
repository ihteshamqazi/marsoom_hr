<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تفاصيل الخصومات</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Tajawal', sans-serif; background: #fff; padding: 20px; }
        .header-box { border-right: 5px solid #dc3545; padding: 15px; background: #fff5f5; border-radius: 8px; margin-bottom: 20px; }
        .table thead th { background: #dc3545; color: white; border: none; }
        .amount-cell { font-weight: bold; color: #dc3545; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="d-flex justify-content-between mb-3 no-print">
        <button onclick="window.close()" class="btn btn-secondary btn-sm">إغلاق</button>
        <button onclick="window.print()" class="btn btn-danger btn-sm">طباعة</button>
    </div>

    <div class="header-box">
        <h4 class="fw-bold m-0 text-danger"><i class="fas fa-minus-circle me-2"></i>تفاصيل الخصومات والجزاءات</h4>
        <div class="text-muted mt-2">
            الموظف: <strong><?php echo html_escape($employee_name); ?></strong> | الفترة: <?php echo $sheet_period; ?>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <table class="table table-striped table-hover mb-0 text-center">
            <thead>
                <tr>
                    <th>#</th>
                    <th>نوع الخصم</th>
                    <th>التاريخ</th>
                    <th>المبلغ</th>
                    <th>السبب / الملاحظات</th>
                    <th>بواسطة</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total = 0;
                if(!empty($discounts)):
                    foreach($discounts as $row): 
                        $total += $row['amount'];
                ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><span class="badge bg-secondary"><?php echo $row['type']; ?></span></td>
                    <td dir="ltr" class="fw-bold text-muted"><?php echo $row['discount_date']; ?></td>
                    <td class="amount-cell"><?php echo number_format($row['amount'], 2); ?></td>
                    <td class="text-start small"><?php echo $row['notes']; ?></td>
                    <td class="small text-muted"><?php echo $row['username']; ?></td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="6" class="py-4 text-muted">لا توجد خصومات مسجلة في هذه الفترة</td></tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr class="table-danger fw-bold">
                    <td colspan="3" class="text-end">الإجمالي:</td>
                    <td class="text-danger fs-5"><?php echo number_format($total, 2); ?></td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>