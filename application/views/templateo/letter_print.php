<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>خطاب <?php echo get_letter_type_name($letter_slug); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: white;
            margin: 0;
            padding: 2.5cm;
            line-height: 1.9;
        }
        .letter-container {
            width: 21cm;
            min-height: 29.7cm;
        }
        .letter-header { display: flex; justify-content: space-between; margin-bottom: 2.5rem; }
        .date-block { font-size: 14px; font-weight: 500; }
        .recipient-block { font-weight: 700; margin-bottom: 1rem; font-size: 18px; }
        .subject { font-size: 18px; font-weight: 700; margin-bottom: 2rem; text-decoration: underline; }
        .letter-body { font-size: 17px; margin-bottom: 2rem; text-align: justify; }
        .employee-details { margin: 2.5rem 0; }
        .detail-item { display: flex; align-items: baseline; margin-bottom: 1rem; font-size: 17px; }
        .detail-label { font-weight: 700; width: 160px; flex-shrink: 0; }
        .detail-value { font-weight: 500; flex-grow: 1; }
        .letter-closing, .company-name { font-size: 17px; font-weight: 700; margin-top: 3rem; }
        
        @media print {
            body { padding: 0; margin: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="letter-container">
        <?php
        date_default_timezone_set('Asia/Riyadh');
        $formatterG = new IntlDateFormatter('ar-SA', IntlDateFormatter::LONG, IntlDateFormatter::NONE, 'Asia/Riyadh', 0, 'd MMMM yyyy');
        $gregorian_date = $formatterG->format(time());
        $formatterH = new IntlDateFormatter('ar-SA-u-ca-islamic', IntlDateFormatter::LONG, IntlDateFormatter::NONE, 'Asia/Riyadh', 0, 'd MMMM yyyy');
        $hijri_date = $formatterH->format(time());
        ?>

        <div class="letter-header">
            <div class="date-block">التاريخ: <?php echo $hijri_date; ?></div>
            <div class="date-block">الموافق: <?php echo $gregorian_date; ?></div>
        </div>

        <div class="recipient-block">
            <span>السادة / <?php echo $letter_data['recipient_name']; ?></span>
            <span style="margin-right: auto;">المحترمين</span>
        </div>

        <div class="subject">
            الموضوع: <?php echo get_letter_subject($letter_slug); ?>
        </div>

        <div class="letter-body">
            <?php echo get_letter_content($letter_slug, $employee, $letter_data); ?>
        </div>

        <p class="letter-closing">وتقبلوا فائق التحية و التقدير،،،</p>
        <p class="company-name">شــركـة مرسوم</p>
    </div>

    <script>
        // Auto-close after printing
        window.onafterprint = function() {
            setTimeout(function() {
                window.close();
            }, 1000);
        };
    </script>
</body>
</html>

<?php
function get_letter_type_name($slug) {
    $types = [
        'salary-certificate' => 'إثبات مزايا وظيفية',
        'salary-commitment' => 'التزام تحويل راتب',
        'salary-commitment-marsoom' => 'التزام تحويل راتب (مرسوم)',
        'embassy-letter' => 'خطاب للسفارة',
        'eos-certificate' => 'إفادة نهاية الخدمة'
    ];
    return $types[$slug] ?? $slug;
}

function get_letter_subject($slug) {
    $subjects = [
        'salary-certificate' => 'اثبات مزايا وظيفية',
        'salary-commitment' => 'التزام تحويل راتب',
        'salary-commitment-marsoom' => 'التزام تحويل راتب',
        'embassy-letter' => 'خطاب تعريف',
        'eos-certificate' => 'إفادة نهاية الخدمة'
    ];
    return $subjects[$slug] ?? 'خطاب';
}

function get_letter_content($slug, $employee, $letter_data) {
    switch($slug) {
        case 'salary-certificate':
            return "
                السلام عليكم ورحمة الله وبركاته، وبعد ،،،
                <br><br>
                نفيدكم علماً بأن الموظف الموضحة بياناته أدناه يعمل لدينا وما زال على رأس العمل، 
                وأجره الشهري \"<b>" . number_format($letter_data['salary_amount'], 2) . "</b>\" ريال سعودي،
                وتصرف له بشكل غير دائم بعض الامتيازات الوظيفية من حوافز ومكافأة غير منتظمة الصرف وغير دائمة الاستحقاق، 
                وتم إصدار هذا الخطاب بناءً على طلب الموظف الموضح بياناته أدناه دون أدنى مسؤولية على الشركة.
                " . get_employee_details($employee) . "
            ";
            
        case 'salary-commitment':
            return "
                السلام عليكم ورحمة الله وبركاته، وبعد ،،،
                <br><br>
                نلتزم نحن شركة مرسوم بتحويل راتب الموظف الموضحة بياناته أدناه إلى حسابكم المذكور، 
                وذلك بدءاً من الشهر القادم ولمدة بقاء الموظف بالشركة.
                " . get_employee_details($employee) . "
            ";
            
        // Add other letter types here...
        
        default:
            return "محتوى الخطاب...";
    }
}

function get_employee_details($employee) {
    return "
        <div class='employee-details'>
            <div class='detail-item'>
                <span class='detail-label'>الاســـــــــــــــــــــــــــــــــــم:</span>
                <span class='detail-value'>{$employee->subscriber_name}</span>
            </div>
            <div class='detail-item'>
                <span class='detail-label'>الـجـنسيـــــــــــة:</span>
                <span class='detail-value'>{$employee->nationality}</span>
            </div>
            <div class='detail-item'>
                <span class='detail-label'>رقــــــم الـهويـــة:</span>
                <span class='detail-value'>{$employee->id_number}</span>
            </div>
            <div class='detail-item'>
                <span class='detail-label'>المسمى الوظيفي:</span>
                <span class='detail-value'>{$employee->profession}</span>
            </div>
        </div>
    ";
}
?>