<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - <?= $month ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Tajawal', sans-serif; /* Or any font you prefer */
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .payslip-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #001f3f;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #001f3f;
            margin: 0;
            font-size: 24px;
        }
        .header h3 {
            color: #FF8C00;
            margin: 5px 0 0;
            font-size: 16px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            color: #555;
        }
        .info-value {
            font-weight: 500;
            font-family: sans-serif; /* For English numbers */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: right;
        }
        th {
            background-color: #f8f9fa;
            color: #001f3f;
            font-weight: bold;
            text-align: center;
        }
        .amount {
            text-align: left;
            direction: ltr;
            font-family: sans-serif;
            font-weight: bold;
        }
        .net-salary {
            background-color: #001f3f;
            color: #fff;
            padding: 15px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        .print-btn-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-print {
            background-color: #001f3f;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            font-family: 'Tajawal', sans-serif;
            text-decoration: none;
            display: inline-block;
        }
        .btn-print:hover {
            background-color: #003366;
        }

        /* Print Styles */
        @media print {
            body {
                background-color: #fff;
                padding: 0;
            }
            .payslip-container {
                box-shadow: none;
                border: none;
                width: 100%;
                max-width: 100%;
                padding: 0;
            }
            .print-btn-container {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="print-btn-container">
        <button onclick="window.print()" class="btn-print">🖨️ طباعة التعريف / حفظ كـ PDF</button>
    </div>

    <div class="payslip-container">
        <div class="header">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <div style="text-align: right;">
                    <div style="font-weight: bold; color: #001f3f;">شركة مرسوم لتحصيل الديون</div>
                    <div style="font-size: 12px;">المملكة العربية السعودية - الرياض</div>
                </div>
                <?php if(!empty($logo_base64)): ?>
                    <img src="<?php echo $logo_base64; ?>" style="height: 60px;">
                <?php endif; ?>
            </div>
            
            <h1>مسير الرواتب (Payslip)</h1>
            <h3>شهر: <span style="font-family: sans-serif;">فبراير</span></h3>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">اسم الموظف:</span>
                <span class="info-value"><?= $emp_name ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">الرقم الوظيفي:</span>
                <span class="info-value"><?= $emp_id ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">المسمى الوظيفي:</span>
                <span class="info-value"><?= $designation ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">تاريخ الإصدار:</span>
                <span class="info-value"><?= $generated_date ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">البنك:</span>
                <span class="info-value"><?= $bank_name ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">IBAN:</span>
                <span class="info-value" style="font-size: 12px;"><?= $iban ?></span>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th colspan="2">الاستحقاقات (Earnings)</th>
                    <th colspan="2">الاستقطاعات (Deductions)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>الراتب الأساسي</td>
                    <td class="amount"><?= number_format($basic_salary, 2) ?></td>
                    <td>التأمينات الاجتماعية (GOSI)</td>
                    <td class="amount"><?= number_format($gosi_amount, 2) ?></td>
                </tr>
                <tr>
                    <td>بدل السكن</td>
                    <td class="amount"><?= number_format($housing, 2) ?></td>
                    <td>خصم الغياب</td>
                    <td class="amount"><?= number_format($absence_amount, 2) ?></td>
                </tr>
                <tr>
                    <td>بدل النقل</td>
                    <td class="amount"><?= number_format($transport, 2) ?></td>
                    <td>خصم التأخير / الخروج المبكر</td>
                    <td class="amount"><?= number_format($late_amount + $early_amount, 2) ?></td>
                </tr>
                <tr>
                    <td>بدلات أخرى / إضافي</td>
                    <td class="amount"><?= number_format($other_earnings, 2) ?></td>
                    <td>خصومات أخرى</td>
                    <td class="amount"><?= number_format($total_deductions - ($gosi_amount + $absence_amount + $late_amount + $early_amount), 2) ?></td>
                </tr>
                <tr style="background-color: #f9f9f9; font-weight: bold;">
                    <td>إجمالي الاستحقاقات</td>
                    <td class="amount"><?= number_format($total_earnings, 2) ?></td>
                    <td>إجمالي الاستقطاعات</td>
                    <td class="amount"><?= number_format($total_deductions, 2) ?></td>
                </tr>
            </tbody>
        </table>

        <div class="net-salary">
            صافي الراتب: <span style="font-family: sans-serif;"><?= number_format($net_salary, 2) ?></span> ريال سعودي
        </div>

        <div class="footer">
            <p>هذه الوثيقة صادرة إلكترونياً من نظام الموارد البشرية ولا تحتاج إلى توقيع.</p>
            <p>Generated by HR System on <?= date('Y-m-d H:i:s') ?></p>
        </div>
    </div>

</body>
</html>