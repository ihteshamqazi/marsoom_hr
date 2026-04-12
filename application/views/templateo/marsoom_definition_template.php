<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>خطاب تعريف - مرسوم</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* --- SCREEN STYLES --- */
        body { 
            font-family: 'Tajawal', sans-serif; 
            background-color: #EAECEF; 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            padding: 2rem 0; 
            line-height: 1.8;
            color: #000; 
            margin: 0;
        }

        .print-controls { 
            position: fixed; top: 1rem; left: 1rem; z-index: 100; 
            background: #fff; padding: 0.5rem; border-radius: 8px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.15); display: flex; gap: 10px; 
        }
        .control-button { 
            padding: 10px 20px; color: white; border: none; border-radius: 5px; 
            cursor: pointer; font-size: 16px; font-family: 'Tajawal', sans-serif; 
        }
        .print-button { background-color: #0d6efd; }
        .back-button { background-color: #6c757d; }
        
        .letter-container { 
            width: 21cm; 
            min-height: 29.7cm; 
            padding: 2.5cm 2cm; 
            background-color: white; 
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); 
            box-sizing: border-box; 
            position: relative; 
            display: flex;
            flex-direction: column;
        }
        
        .header-date { display: flex; flex-direction: column; align-items: flex-start; margin-bottom: 2rem; font-weight: 700; font-size: 16px; }
        .recipient-section { font-weight: 700; font-size: 18px; margin-bottom: 1.5rem; }
        .fillable { border-bottom: 1px dotted #000; padding: 0 5px; min-width: 150px; display: inline-block; }
        
        .subject { text-align: right; font-weight: 700; font-size: 18px; text-decoration: underline; margin-bottom: 1.5rem; }
        
        .letter-body { text-align: justify; font-size: 17px; margin-bottom: 1.5rem; }
        
        /* Grid for Employee Info */
        .info-grid { 
            display: grid; 
            grid-template-columns: 180px 1fr; 
            gap: 12px 0; 
            margin-bottom: 1.5rem; 
            font-size: 17px; 
        }
        .label { font-weight: 700; }
        .value { font-weight: 500; }

        /* Salary Table Styles */
        .salary-title { font-weight: 700; text-decoration: underline; margin-bottom: 10px; margin-top: 10px; }
        .salary-table { width: 100%; border-collapse: collapse; margin-top: 5px; font-size: 14px; text-align: center; }
        .salary-table th { border: 1px solid #000; padding: 10px 5px; background-color: #f8f9fa; font-weight: 700; }
        .salary-table td { border: 1px solid #000; padding: 10px 5px; font-weight: 500; }

        /* FIXED FOOTER STYLE */
        .footer { 
            margin-top: 4rem;
            font-weight: 700; 
            font-size: 18px; 
            text-align: left; 
            padding-left: 2cm; 
        }

        /* --- PRINT STYLES (THE FIX) --- */
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
        line-height: 1.5 !important; /* Tighten text */
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
        height: 18mm; /* Adjust this value based on your company letterhead size */
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
        <button class="control-button back-button" onclick="window.close()"><i class="fas fa-arrow-right"></i> <span>إغلاق</span></button>
        <button class="control-button print-button" onclick="window.print()"><i class="fas fa-print"></i> <span>طباعة</span></button>
    </div>

    <div class="letter-container">
        <!-- Optional: Visual guide for letterhead space (only shows on screen) -->
        <div class="print-guide"></div>
        
        <?php
        date_default_timezone_set('Asia/Riyadh');
        
        // 1. Fetch Order Details to get the bank name directly from the request
        $this->load->model('hr_model');
        $order_details = $this->hr_model->get_request_details($order_id);

        // 2. Determine Recipient Name (Bank Name)
        $recipient_name = '';

        if (!empty($order_details['letter_to_ar'])) {
            // Priority 1: Use the Arabic name provided in the request
            $recipient_name = $order_details['letter_to_ar'];
        } elseif (!empty($order_details['letter_to_en'])) {
            // Priority 2: Use English name (and translate if possible)
            $english_name = $order_details['letter_to_en'];
            
            // Translation Dictionary
            $bankTranslations = [
                'Al Rajhi Bank' => 'مصرف الراجحي',
                'Al Ahli Bank' => 'البنك الأهلي',
                'Riyad Bank' => 'بنك الرياض',
                'National Commercial Bank' => 'البنك الأهلي السعودي',
                'Saudi British Bank' => 'البنك السعودي البريطاني',
                'SABB' => 'البنك السعودي البريطاني',
                'Arab National Bank' => 'البنك العربي الوطني',
                'Saudi Investment Bank' => 'البنك السعودي للاستثمار',
                'Bank AlBilad' => 'بنك البلاد',
                'Bank AlJazira' => 'بنك الجزيرة',
                'Samba' => 'مجموعة سامبا المالية',
                'Samba Financial Group' => 'مجموعة سامبا المالية',
                'Alinma Bank' => 'مصرف الإنماء',
                'SNB' => 'البنك الأهلي السعودي',
                'Saudi National Bank' => 'البنك الأهلي السعودي',
            ];

            $recipient_name = $bankTranslations[$english_name] ?? $english_name;
        } else {
            // Fallback: Use employee profile bank or placeholder
            $recipient_name = $employee->n3 ?? '......................';
        }
        
        // 3. Date Formatting
        $gregorian_date = date('Y-m-d');
        
        // Try to use IntlDateFormatter for Arabic Dates
        if (class_exists('IntlDateFormatter')) {
            try {
                $formatterG = new IntlDateFormatter('ar_SA', IntlDateFormatter::LONG, IntlDateFormatter::NONE, 'Asia/Riyadh');
                $gregorian_date = $formatterG->format(time());
            } catch (Exception $e) {
                // Keep default Y-m-d if error
            }
        }
        ?>

        <div class="header-date">
            <div>التاريخ : <?php echo $gregorian_date; ?> </div>
        </div>

        <div class="recipient-section">
            <div>المكرمون /  <?php echo htmlspecialchars($recipient_name); ?> المحترمين</div>
        </div>

        <div class="subject">الموضوع : خطاب تعريف</div>

        <div class="letter-body">
            السلام عليكم ورحمة الله وبركاته ،،، وبعد
            <br>
            تشهد شركة مرسوم بأن المذكور ادناه يعمل لدينا ومازال على رأس العمل وقد أعطي هذا الخطاب بناء على طلبه دون أدنى مسؤولية على الشركة.
        </div>

        <div class="info-grid">
            <div class="label">الاســــــــــــــــــــــــــــــــم :</div>
            <div class="value"><?php echo $employee->subscriber_name; ?></div>

            <div class="label">رقــــــم الهويــــــــة :</div>
            <div class="value"><?php echo $employee->id_number; ?></div>

            <div class="label">الجنسيــــــــــــــــــــــــــة :</div>
            <div class="value"><?php echo $employee->nationality; ?></div>

            <div class="label">الـمسـمـى الــوظــيـفـــي :</div>
            <div class="value"><?php echo $employee->profession; ?></div>

            <div class="label">تــــــــاريــخ التعـــيــيـن :</div>
            <div class="value"><?php echo $employee->joining_date; ?> م</div>

            <div class="label">الـــراتــب الاجـــمــالــي :</div>
            <div class="value"><?php echo number_format($employee->total_salary, 0); ?> ريال سعودي</div>
        </div>

        <div class="salary-details">
            <div class="salary-title">تفاصيل الراتب الشهري :-</div>
            <table class="salary-table">
                <thead>
                    <tr>
                        <th>الراتب الاساسي</th>
                        <th>بدل السكن</th>
                        <th>بدل مواصلات</th>
                        <th>بدل اتصالات وانترنت</th>
                        <th>بدلات أخرى</th>
                        <th>الراتب الاجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo number_format($employee->base_salary, 0); ?> ريال</td>
                        <td><?php echo number_format($employee->housing_allowance, 0); ?> ريال</td>
                        <td><?php echo number_format($employee->n4, 0); ?> ريال</td>
                        <td>0 ريال</td> 
                        <td><?php echo number_format($employee->other_allowances, 0); ?> ريال</td>
                        <td><?php echo number_format($employee->total_salary, 0); ?> ريال</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="letter-body" style="margin-top: 2rem;">
            والله يحفظكم ويرعاكم ،،،
        </div>

        <div class="footer">
            شــركـة مرسوم
        </div>
    </div>
    
    <script>
        // Optional: Test print preview
        document.addEventListener('DOMContentLoaded', function() {
            // Add keyboard shortcut for print (Ctrl+P)
            document.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                    e.preventDefault();
                    window.print();
                }
            });
            
            // Adjust letterhead space based on actual letterhead size
            function adjustLetterheadSpace() {
                // If you know the exact height of your letterhead in mm, convert to rem
                // Example: 100mm letterhead = 100/4.23 ≈ 23.64rem
                // You can set it here or in the CSS
                const letterheadHeightMM = 100; // Change this to your actual letterhead height
                const letterheadHeightREM = letterheadHeightMM / 4.23;
                
                // Uncomment the line below if you want to set it dynamically
                // document.querySelector('.letter-container').style.paddingTop = letterheadHeightREM + 'rem';
            }
            
            adjustLetterheadSpace();
        });
    </script>
</body>
</html>