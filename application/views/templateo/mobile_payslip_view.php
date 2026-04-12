<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>مسير الرواتب - <?= $month ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #001f3f;
            --secondary: #FF8C00;
            --bg-gray: #f8f9fa;
            --border-color: #dee2e6;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 10px;
            color: #333;
            direction: rtl;
        }

        /* --- Main Container --- */
        .payslip-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            max-width: 800px;
            margin: 0 auto 80px auto; /* Bottom margin for fixed button */
            overflow: hidden;
        }

        /* --- Header --- */
        .header {
            background: var(--primary);
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
        }
        .header img {
            height: 50px;
            background: white;
            padding: 5px;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .header h2 { margin: 0; font-size: 18px; font-weight: 700; }
        .header p { margin: 5px 0 0; opacity: 0.9; font-size: 13px; }
        .month-badge {
            background: var(--secondary);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            display: inline-block;
            margin-top: 10px;
            font-family: sans-serif; 
            direction: ltr;
        }

        /* --- Info Section --- */
        .info-section {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            background: #fff;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .info-item label {
            display: block;
            color: #6c757d;
            font-size: 11px;
            margin-bottom: 2px;
        }
        .info-item span {
            font-weight: 700;
            font-size: 13px;
            display: block;
        }
        .number-font { font-family: sans-serif; direction: ltr; display: inline-block; }

        /* --- Financial Section --- */
        .financial-section {
            padding: 20px;
        }
        
        .finance-group {
            margin-bottom: 20px;
            background: var(--bg-gray);
            border-radius: 12px;
            padding: 15px;
        }
        
        .group-title {
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 12px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 5px;
            display: flex;
            justify-content: space-between;
        }
        .group-title.earnings { color: #198754; border-color: #198754; }
        .group-title.deductions { color: #dc3545; border-color: #dc3545; }

        .finance-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 13px;
        }
        .finance-row:last-child { margin-bottom: 0; }
        .finance-row .label { color: #555; }
        .finance-row .value { font-weight: 700; font-family: sans-serif; direction: ltr; }

        /* --- Net Salary --- */
        .net-salary-card {
            background: var(--primary);
            color: white;
            margin: 20px;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,31,63,0.2);
        }
        .net-salary-card .label { opacity: 0.8; font-size: 13px; display: block; margin-bottom: 5px; }
        .net-salary-card .amount { font-size: 28px; font-weight: 800; font-family: sans-serif; direction: ltr; }
        .net-salary-card .currency { font-size: 14px; margin-right: 5px; }

        /* --- Print Button --- */
        .print-btn-container {
            position: fixed;
            bottom: 20px;
            left: 0;
            right: 0;
            padding: 0 20px;
            z-index: 100;
        }
        .btn-print {
            background: var(--secondary);
            color: white;
            border: none;
            width: 100%;
            padding: 15px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(255, 140, 0, 0.3);
            font-family: 'Tajawal', sans-serif;
            cursor: pointer;
        }

        /* ==============================
           PRINT MEDIA QUERY (A4 Layout)
           ============================== */
        @media print {
            body { background: white; padding: 0; margin: 0; }
            .payslip-card { box-shadow: none; border: none; margin: 0; width: 100%; max-width: 100%; border-radius: 0; }
            .print-btn-container { display: none; }
            
            /* Change Grid to Table-like layout for print */
            .header { background: white; color: black; border-bottom: 2px solid #000; padding: 10px 0; text-align: center; }
            .header h2 { font-size: 24px; color: #000; }
            .header img { display: none; } /* Optional: Hide logo image if base64 causes issues, or keep it */
            .month-badge { background: #eee; color: #000; border: 1px solid #000; }

            .info-grid { grid-template-columns: 1fr 1fr 1fr; border: 1px solid #000; padding: 10px; margin-bottom: 20px; }
            .info-item { border-bottom: none; }

            /* Force side-by-side for financial sections on A4 */
            .financial-section { display: flex; gap: 20px; padding: 0; margin-top: 20px; }
            .finance-group { flex: 1; background: white; border: 1px solid #000; padding: 0; border-radius: 0; margin: 0; }
            .group-title { background: #eee; padding: 8px; margin: 0; border-bottom: 1px solid #000; color: #000 !important; text-align: center; -webkit-print-color-adjust: exact; }
            .finance-row { padding: 8px; border-bottom: 1px solid #eee; }

            .net-salary-card { 
                background: white; 
                color: black; 
                border: 2px solid #000; 
                margin-top: 30px; 
                box-shadow: none;
            }
            .net-salary-card .amount { color: #000; }
        }

        /* Mobile specific Tweaks */
        @media screen and (max-width: 576px) {
            .info-grid { grid-template-columns: 1fr; gap: 12px; }
            .info-item { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #eee; padding-bottom: 8px; }
            .info-item label { margin-bottom: 0; }
        }
    </style>
</head>
<body>

    <div class="payslip-card">
        <div class="header">
            <?php if(!empty($logo_base64)): ?>
                <img src="<?php echo $logo_base64; ?>" alt="Logo">
            <?php endif; ?>
            <h2>شركة مرسوم لتحصيل الديون</h2>
            <p>مسير الرواتب الشهري (Payslip)</p>
            <div class="month-badge"><?= $month ?></div>
        </div>

        <div class="info-section">
            <div class="info-grid">
                <div class="info-item">
                    <label>الموظف</label>
                    <span><?= $emp_name ?></span>
                </div>
                <div class="info-item">
                    <label>الرقم الوظيفي</label>
                    <span class="number-font"><?= $emp_id ?></span>
                </div>
                <div class="info-item">
                    <label>المسمى الوظيفي</label>
                    <span><?= $designation ?></span>
                </div>
                <div class="info-item">
                    <label>البنك</label>
                    <span><?= $bank_name ?></span>
                </div>
                <div class="info-item" style="grid-column: 1 / -1;">
                    <label>الآيبان</label>
                    <span class="number-font" style="font-size: 11px; letter-spacing: 1px;"><?= $iban ?></span>
                </div>
            </div>
        </div>

        <div class="financial-section">
            
            <div class="finance-group">
                <div class="group-title earnings">
                    <span>الاستحقاقات</span>
                    <span>(+)</span>
                </div>
                
                <div class="finance-row">
                    <span class="label">الراتب الأساسي</span>
                    <span class="value"><?= number_format($basic_salary, 2) ?></span>
                </div>
                <div class="finance-row">
                    <span class="label">بدل السكن</span>
                    <span class="value"><?= number_format($housing, 2) ?></span>
                </div>
                <div class="finance-row">
                    <span class="label">بدل النقل</span>
                    <span class="value"><?= number_format($transport, 2) ?></span>
                </div>
                <div class="finance-row">
                    <span class="label">بدلات أخرى / إضافي</span>
                    <span class="value"><?= number_format($other_earnings, 2) ?></span>
                </div>
                
                <div class="finance-row" style="margin-top: 10px; padding-top: 10px; border-top: 1px dashed #ccc;">
                    <span class="label" style="font-weight: bold; color: #198754;">إجمالي الاستحقاقات</span>
                    <span class="value" style="color: #198754;"><?= number_format($total_earnings, 2) ?></span>
                </div>
            </div>

            <div class="finance-group">
                <div class="group-title deductions">
                    <span>الاستقطاعات</span>
                    <span>(-)</span>
                </div>
                
                <div class="finance-row">
                    <span class="label">التأمينات (GOSI)</span>
                    <span class="value"><?= number_format($gosi_amount, 2) ?></span>
                </div>
                <div class="finance-row">
                    <span class="label">غياب</span>
                    <span class="value"><?= number_format($absence_amount, 2) ?></span>
                </div>
                <div class="finance-row">
                    <span class="label">تأخير / خروج مبكر</span>
                    <span class="value"><?= number_format($late_amount + $early_amount, 2) ?></span>
                </div>
                
                <div class="finance-row" style="margin-top: 10px; padding-top: 10px; border-top: 1px dashed #ccc;">
                    <span class="label" style="font-weight: bold; color: #dc3545;">إجمالي الاستقطاعات</span>
                    <span class="value" style="color: #dc3545;"><?= number_format($total_deductions, 2) ?></span>
                </div>
            </div>

        </div>

        <div class="net-salary-card">
            <span class="label">صافي الراتب المستحق</span>
            <span class="amount"><?= number_format($net_salary, 2) ?></span>
            <span class="currency">ريال</span>
        </div>

        <div style="text-align: center; color: #999; font-size: 11px; margin-bottom: 20px;">
            تم إصدار هذا المستند إلكترونياً بتاريخ <span class="number-font"><?= date('Y-m-d') ?></span>
        </div>

    </div>

    <div class="print-btn-container">
        <button onclick="window.print()" class="btn-print">
            <i class="fa-solid fa-print"></i> طباعة / حفظ PDF
        </button>
    </div>

</body>
</html>