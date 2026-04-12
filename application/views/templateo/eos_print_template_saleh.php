<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>مخالصة نهائية - <?php echo $subscriber_name; ?></title>
    <style>
        body {
            font-family: 'Arial', sans-serif; 
            background: #ccc;
            margin: 0;
            padding: 20px;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .page {
            background: white;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 15mm 15mm;
            position: relative;
            box-sizing: border-box;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
        }

        .header {
            width: 100%;
            height: 100px;
            position: relative;
            margin-bottom: 20px;
        }

        .logo-section {
            position: absolute;
            right: 0;
            top: 0;
        }
        .logo-section img {
            width: 180px; /* Made slightly larger for office logo if needed */
            max-height: 90px;
            object-fit: contain;
        }

        .date-section {
            position: absolute;
            left: 0;
            top: 20px;
            font-family: 'Arial', sans-serif;
            font-weight: bold;
            font-size: 11px;
            color: #000;
            direction: ltr;
        }

        .main-title {
            text-align: center;
            color: #1a4f76; /* Dark blue color for law office professional look */
            font-weight: bold;
            font-size: 24px;
            margin-top:40px;
            margin-bottom: 10px;
        }

        .custom-line {
            border-top: 2px solid #1a4f76;
            margin-bottom: 40px;
            margin-top: 10px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            direction: rtl;
        }
        .info-table th, .info-table td {
            border: 2px solid #000;
            text-align: center;
            padding: 5px;
            font-size: 16px;
            font-weight: bold;
            font-family: 'Arial', sans-serif;
        }
        .info-table th {
            background-color: #f2f2f2;
            color: #000;
            width: 33.33%;
        }
        .info-table td {
            color: #000; /* Standard black for legal docs */
        }

        .content-text {
            text-align: justify;
            font-size: 18px;
            line-height: 2;
            font-weight: bold;
            color: #000;
            margin-bottom: 40px;
            font-family: 'Arial', sans-serif;
        }

        .signatures {
            margin-top: 50px;
            font-weight: bold;
            font-size: 18px;
            color: #000;
            padding-right: 20px;
        }
        .sig-row {
            margin-bottom: 25px;
        }
        .sig-label {
            display: inline-block;
            width: 60px;
        }

        .no-print {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn {
            padding: 10px 20px;
            background: #333;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
        }

        @media print {
            body { background: none; padding: 0; margin: 0; }
            .page { box-shadow: none; margin: 0; width: 100%; height: auto; padding: 0; }
            .no-print { display: none; }
            @page { size: A4; margin: 10mm 15mm; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" class="btn">طباعة / Print</button>
    </div>

    <div class="page">
        
        <div class="header">
            <div class="date-section">
                <?php echo $print_date; ?>
            </div>
            
            <div class="logo-section">
                <?php if(!empty($logo_base64)): ?>
                    <img src="<?php echo $logo_base64; ?>" alt="Logo">
                <?php else: ?>
                    <h2><?php echo $company_name; ?></h2>
                <?php endif; ?>
            </div>
        </div>

        <div class="main-title">مخالصة نهائية</div>

        <div class="custom-line"></div>

        <table class="info-table">
            <thead>
                <tr>
                    <th>الرقم الوظيفي</th>
                    <th>الاسم</th>
                    <th>رقم الهوية</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo $emp_code; ?></td>
                    <td><?php echo $subscriber_name; ?></td>
                    <td><?php echo $id_number; ?></td>
                </tr>
            </tbody>
        </table>

       <div class="content-text">
    إشارة إلى انتهاء العلاقة التعاقدية بيني أنا الموظف الموضح بياناته في الجدول أعلاه و<?php echo $company_name; ?>؛ أقر وأعلن بأن إجمالي جميع مستحقاتي المالية والعينية والتي تشمل كافة حقوقي التعاقدية والنظامية مثل (الرواتب الشهرية، قيمة الاجازات، مكافأة نهاية الخدمة، وغيرها) عن كامل مدة عملي مع المكتب بقيمة (<span id="arabic-amount"><?php echo $final_amount; ?></span>) فقط <span id="arabic-words"></span> لا غير. وبالتوقيع على هذا الاقرار أبرئ ذمة المكتب، وعملائه والعاملين فيه إبراء شاملاً ومطلقاً لا رجعة فيه من أي حق أو مطالبة أو ادعاء من أي نوع كان حالياً ومستقبلاً، وأقر بأنني وقعت على هذا الاقرار وأنا بكامل الاهلية المعتبرة شرعاً ونظاماً وبمحض إرادتي واختياري دون ضغط أو إكراه أو إلجاء من أحد.
    <br><br>
    <center>والله ولي التوفيق.</center>
</div>

        <div class="signatures">
            <div class="sig-row" style="margin-bottom: 30px;">المقر والمتعهد بما فيه:</div>
            <div class="sig-row">
                <span class="sig-label">الإسم:</span> <span><?php echo $subscriber_name; ?></span>
            </div>
            <div class="sig-row">
                <span class="sig-label">التوقيع:</span> ___________________________
            </div>
            <div class="sig-row">
                <span class="sig-label">التاريخ:</span> ___________________________
            </div>
        </div>

    </div>

    <script>
        function convertToArabicNumbers(number) {
            const arabicNumbers = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
            return number.toString().replace(/\d/g, digit => arabicNumbers[digit]);
        }

        function convertToArabicWords(number) {
            const units = ['', 'واحد', 'اثنان', 'ثلاثة', 'أربعة', 'خمسة', 'ستة', 'سبعة', 'ثمانية', 'تسعة'];
            const teens = ['عشرة', 'أحد عشر', 'اثنا عشر', 'ثلاثة عشر', 'أربعة عشر', 'خمسة عشر', 'ستة عشر', 'سبعة عشر', 'ثمانية عشر', 'تسعة عشر'];
            const tens = ['', 'عشرة', 'عشرون', 'ثلاثون', 'أربعون', 'خمسون', 'ستون', 'سبعون', 'ثمانون', 'تسعون'];
            const hundreds = ['', 'مائة', 'مئتان', 'ثلاثمائة', 'أربعمائة', 'خمسمائة', 'ستمائة', 'سبعمائة', 'ثمانمائة', 'تسعمائة'];
            
            function convertPart(num, addCurrency = false) {
                if (num === 0) return '';
                
                let result = '';
                
                if (num < 10) {
                    result = units[num];
                } else if (num < 20) {
                    result = teens[num - 10];
                } else if (num < 100) {
                    let ten = Math.floor(num / 10);
                    let unit = num % 10;
                    if (unit === 0) {
                        result = tens[ten];
                    } else {
                        result = units[unit] + ' و' + tens[ten];
                    }
                } else if (num < 1000) {
                    let hundred = Math.floor(num / 100);
                    let remainder = num % 100;
                    if (remainder === 0) {
                        result = hundreds[hundred];
                    } else {
                        result = hundreds[hundred] + ' و' + convertPart(remainder);
                    }
                } else if (num < 1000000) {
                    let thousand = Math.floor(num / 1000);
                    let remainder = num % 1000;
                    
                    let thousandText = '';
                    if (thousand === 1) {
                        thousandText = 'ألف';
                    } else if (thousand === 2) {
                        thousandText = 'ألفان';
                    } else if (thousand < 10) {
                        thousandText = units[thousand] + ' آلاف';
                    } else {
                        thousandText = convertPart(thousand) + ' ألف';
                    }
                    
                    if (remainder === 0) {
                        result = thousandText;
                    } else {
                        result = thousandText + ' و' + convertPart(remainder);
                    }
                } else {
                    let million = Math.floor(num / 1000000);
                    let remainder = num % 1000000;
                    
                    let millionText = '';
                    if (million === 1) {
                        millionText = 'مليون';
                    } else if (million === 2) {
                        millionText = 'مليونان';
                    } else if (million < 10) {
                        millionText = units[million] + ' ملايين';
                    } else {
                        millionText = convertPart(million) + ' مليون';
                    }
                    
                    if (remainder === 0) {
                        result = millionText;
                    } else {
                        result = millionText + ' و' + convertPart(remainder);
                    }
                }
                
                return result;
            }

            let integerPart = Math.floor(number);
            let decimalPart = Math.round((number - integerPart) * 100);
            
            let result = '';
            
            if (integerPart === 0) {
                result = 'صفر';
            } else {
                result = convertPart(integerPart);
            }
            
            result += ' ريال';
            
            if (decimalPart > 0) {
                result += ' و' + convertPart(decimalPart) + ' هللة';
            }
            
            return result;
        }

        const amountElement = document.getElementById('arabic-amount');
        const wordsElement = document.getElementById('arabic-words');
        
        if (amountElement && wordsElement) {
            const originalAmount = parseFloat(amountElement.textContent.replace(/,/g, ''));
            const arabicAmount = convertToArabicNumbers(originalAmount.toFixed(2));
            amountElement.textContent = arabicAmount;
            const arabicWords = convertToArabicWords(originalAmount);
            wordsElement.textContent = arabicWords;
        }
    </script>
</body>
</html>