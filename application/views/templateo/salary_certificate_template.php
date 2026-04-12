<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>خطاب إثبات مزايا وظيفية</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* --- SCREEN STYLES (Monitor View) --- */
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #EAECEF;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2rem 0;
            line-height: 1.9;
            margin: 0;
        }

        .print-controls {
            position: fixed; top: 1rem; left: 1rem; z-index: 100;
            background: #fff; padding: 0.5rem; border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15); display: flex; gap: 10px;
        }
        .control-button {
            padding: 10px 20px; color: white; border: none; border-radius: 5px; cursor: pointer;
            font-size: 16px; font-family: 'Tajawal', sans-serif;
        }
        .print-button { background-color: #0d6efd; }
        .download-button { background-color: #198754; }
        .back-button { background-color: #6c757d; }
        
        .letter-container {
            width: 21cm;
            min-height: 29.7cm; /* Fixed height for screen look */
            padding: 2.5cm;
            background-color: white;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
            color: #212529;
            display: flex;
            flex-direction: column;
        }

        .letter-header { display: flex; justify-content: space-between; margin-bottom: 2.5rem; }
        .date-block { font-size: 14px; font-weight: 500; }
        .recipient-block { font-weight: 700; margin-bottom: 1rem; display: flex; justify-content: space-between; }
        
        .subject { font-size: 18px; font-weight: 700; margin-bottom: 2rem; text-decoration: underline; }
        
        .letter-body { font-size: 17px; margin-bottom: 2rem; text-align: justify; }
        
        .employee-details { margin: 2rem 0; }
        .detail-item { display: flex; align-items: baseline; margin-bottom: 0.8rem; font-size: 17px; }
        .detail-label { font-weight: 700; width: 160px; flex-shrink: 0; }
        .detail-value { font-weight: 500; flex-grow: 1; }
        
        /* Fixed Gap Logic */
        .letter-closing { margin-top: 3rem; font-size: 17px; font-weight: 700; }
        .company-name { font-size: 17px; font-weight: 700; margin-top: 0.5rem; }

      
       @media print {
    @page {
        size: A4;
        margin: 20mm 15mm 10mm 15mm; /* Increased top margin for company letterhead */
    }

    html, body {
        height: auto !important;
        min-height: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        background-color: white !important;
        display: block !important; 
        line-height: 2.0 !important; /* Tighten text */
    }

    .print-controls { display: none; }

    .letter-container { 
        width: 100% !important;
        max-width: 100% !important;
        height: auto !important; 
        min-height: 0 !important; 
        margin: 0 !important;
        padding: 0 !important; 
        border: none !important;
        box-shadow: none !important;
        display: block !important; 
        position: relative;
    }

    /* Add space for company letterhead */
    .letter-container::before {
        content: "";
        display: block;
        height: 20mm; /* Adjust this value based on your company letterhead size */
        width: 100%;
        margin-bottom: 1rem;
    }

    /* Optional: If you want to actually show the company letterhead */
    /* Uncomment and customize this section if you have a letterhead image */
    /*
    .letter-container::before {
        content: "";
        display: block;
        height: 40mm;
        width: 100%;
        background-image: url('path/to/your/letterhead.jpg');
        background-size: contain;
        background-repeat: no-repeat;
        background-position: top center;
        margin-bottom: 1rem;
    }
    */

    /* Alternative: Add top padding to the container instead */
    /* Uncomment if you prefer this approach */
    /*
    .letter-container {
        padding-top: 40mm !important;
    }
    */

    /* Compact spacings for print */
    .letter-header { 
        margin-bottom: 1.5rem !important;
        margin-top: 0 !important;
    }
    .subject { margin-bottom: 1.5rem !important; }
    .letter-body { margin-bottom: 1.5rem !important; }
    .employee-details { margin: 1.5rem 0 !important; }
    .detail-item { margin-bottom: 0.5rem !important; }
    
    /* Footer Positioning Fix */
    .letter-closing { 
        margin-top: 2rem !important; /* Tight fixed gap */
        page-break-inside: avoid;
    }
        }
    </style>
</head>
<body>

    <div class="print-controls">
        <button class="control-button back-button" onclick="window.close()">
            <i class="fas fa-arrow-right"></i> <span>إغلاق</span>
        </button>
        <button class="control-button download-button" onclick="downloadLetter()">
            <i class="fas fa-download"></i> <span>تحميل الخطاب</span>
        </button>
        <button class="control-button print-button" onclick="window.print()">
            <i class="fas fa-print"></i> <span>طباعة</span>
        </button>
    </div>

    <div class="letter-container" id="letterContainer">
        <?php
            date_default_timezone_set('Asia/Riyadh');
            $formatterG = new IntlDateFormatter('en_US', IntlDateFormatter::LONG, IntlDateFormatter::NONE, 'Asia/Riyadh', IntlDateFormatter::GREGORIAN);
            $gregorian_date = $formatterG->format(time());
            $formatterH = new IntlDateFormatter('ar-SA-u-ca-islamic', IntlDateFormatter::LONG, IntlDateFormatter::NONE, 'Asia/Riyadh', 0, 'd MMMM yyyy');
            $hijri_date = $formatterH->format(time());
        ?>

        <div class="letter-header">
            <div class="date-block">التاريخ: <?php echo $hijri_date; ?></div>
        </div>

        <div class="recipient-block">
            <span>السادة / <?php echo $employee->n3; ?></span>
            <span>المحترمين</span>
        </div>

        <div class="subject">
            الموضوع: اثبات مزايا وظيفية
        </div>

        <p class="letter-body">
            السلام عليكم ورحمة الله وبركاته، وبعد ،،،
            <br><br>
            نفيدكم علماً بأن الموظف الموضحة بياناته أدناه يعمل لدينا وما زال على رأس العمل، 
            
            <?php if (isset($show_salary) && $show_salary): ?>
                وأجره الشهري "<b><?php echo number_format($employee->total_salary, 2); ?></b>" ريال سعودي،
            <?php endif; ?>

            وتصرف له بشكل غير دائم بعض الامتيازات الوظيفية من حوافز ومكافأة غير منتظمة الصرف وغير دائمة الاستحقاق، وتم إصدار هذا الخطاب بناءً على طلب الموظف الموضح بياناته أدناه دون أدنى مسؤولية على الشركة.
        </p>

        <div class="employee-details">
            <div class="detail-item">
                <span class="detail-label">الاســـــــــــــــــــــــــــــــــــم:</span>
                <span class="detail-value"><?php echo $employee->subscriber_name; ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">الـجـنسيـــــــــــة:</span>
                <span class="detail-value"><?php echo $employee->nationality; ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">رقــــــم الـهويـــة:</span>
                <span class="detail-value"><?php echo $employee->id_number; ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">المسمى الوظيفي:</span>
                <span class="detail-value"><?php echo $employee->profession; ?></span>
            </div>
        </div>

        <p class="letter-closing">وتقبلوا فائق التحية و التقدير،،،</p>
        <p class="company-name">شــركـة مرسوم</p>
    </div>

    <script>
        function downloadLetter() {
            // Show loading
            const downloadBtn = document.querySelector('.download-button');
            const originalText = downloadBtn.innerHTML;
            downloadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>جاري التحميل...</span>';
            downloadBtn.disabled = true;

            const letterSlug = '<?php echo $letter_slug ?? ""; ?>';
            const employeeId = '<?php echo $employee->employee_id ?? ""; ?>';
            const orderId = '<?php echo $order_id ?? ""; ?>';

            // Create a form and submit it to download the letter
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?php echo site_url("users1/download_letter"); ?>';
            
            const slugInput = document.createElement('input');
            slugInput.type = 'hidden';
            slugInput.name = 'letter_slug';
            slugInput.value = letterSlug;
            form.appendChild(slugInput);

            const empInput = document.createElement('input');
            empInput.type = 'hidden';
            empInput.name = 'employee_id';
            empInput.value = employeeId;
            form.appendChild(empInput);

            const orderInput = document.createElement('input');
            orderInput.type = 'hidden';
            orderInput.name = 'order_id';
            orderInput.value = orderId;
            form.appendChild(orderInput);

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);

            // Restore button after a delay
            setTimeout(() => {
                downloadBtn.innerHTML = originalText;
                downloadBtn.disabled = false;
            }, 3000);
        }

        // Auto-print if needed
        <?php if (isset($auto_print) && $auto_print): ?>
        window.onload = function() {
            window.print();
        };
        <?php endif; ?>
    </script>

</body>
</html>