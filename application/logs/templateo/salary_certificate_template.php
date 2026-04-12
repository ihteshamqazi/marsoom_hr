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
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #EAECEF;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2rem 0;
            line-height: 1.9;
        }
        .print-controls {
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 100;
            background: #fff;
            padding: 0.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            gap: 10px;
        }
        .control-button {
            padding: 10px 20px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-family: 'Tajawal', sans-serif;
        }
        .print-button { background-color: #0d6efd; }
        .print-button:hover { background-color: #0b5ed7; }
        .edit-button { background-color: #6c757d; }
        .edit-button:hover { background-color: #5c636a; }
        .save-button { background-color: #198754; } /* Green for Save */
        .save-button:hover { background-color: #157347; }
        
        .letter-container {
            width: 21cm;
            min-height: 29.7cm;
            padding: 2.5cm;
            background-color: white;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
            color: #212529;
        }
        
        /* New styles for edit mode */
        .detail-value .edit-view,
        .letter-body .edit-view {
            display: none; /* Hide input fields by default */
            width: 100%;
            border: none;
            border-bottom: 1px dashed #0d6efd;
            font-family: 'Tajawal', sans-serif;
            font-size: 16px;
            font-weight: 500;
            padding: 2px 5px;
        }
        .letter-body .edit-view {
            font-size: 17px;
            width: 100px; /* Specific width for salary input */
            text-align: center;
        }

        /* When in editing mode, hide text and show inputs */
        .editing-mode .text-view {
            display: none;
        }
        .editing-mode .edit-view {
            display: inline-block;
        }
        .editing-mode .print-button {
            display: none; /* Hide print button while editing */
        }
        
        /* Original styles from previous step */
        .letter-header { display: flex; justify-content: space-between; margin-bottom: 2.5rem; }
        .date-block { font-size: 14px; font-weight: 500; }
        .recipient { font-size: 18px; font-weight: 700; margin-bottom: 1rem; }
        .recipient-block, .greeting-block { font-weight: 700; margin-bottom: 1rem; }
        .subject { font-size: 18px; font-weight: 700; margin-bottom: 2rem; }
        .letter-body { font-size: 17px; margin-bottom: 2rem; text-align: justify; }
        .employee-details { margin: 2.5rem 0; }
        .detail-item { display: flex; align-items: baseline; margin-bottom: 1rem; font-size: 17px; }
        .detail-label { font-weight: 700; width: 160px; flex-shrink: 0; }
        .detail-value { font-weight: 500; flex-grow: 1; }
        .letter-closing, .company-name { font-size: 17px; font-weight: 700; margin-top: 3rem; }
        .data-item { display: flex; margin-bottom: 0.5rem; }
        .data-label { font-weight: 700; width: 220px; flex-shrink: 0; }
        .data-value { font-weight: 500; }
        .company-name { margin-top: 0.5rem; }

        @media print {
            body { background-color: white; padding: 0; margin: 0; }
            .print-controls { display: none; }
            .letter-container { box-shadow: none; padding: 0; }
            .edit-view { display: none !important; } /* Ensure inputs are not printed */
            .text-view { display: inline !important; } /* Ensure text is visible for printing */
        }
         .fillable {
            border: none; border-bottom: 1px dotted #888; background-color: #f8f9fa;
            padding: 0 4px; font-family: 'Tajawal', sans-serif; font-size: 1em;
            font-weight: 700; text-align: right; width: 150px;
        }
    </style>
</head>
<body>

    <div class="print-controls">
        <button class="control-button edit-button" id="editButton">
            <i class="fas fa-pencil-alt"></i> <span>تعديل</span>
        </button>
        <button class="control-button print-button" onclick="window.print()">
            <i class="fas fa-print"></i> <span>طباعة</span>
        </button>
    </div>

    <div class="letter-container" id="letterContainer">
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
            <span>السادة / <input type="text" class="fillable" value="مصرف الراجحي"></span>
            <span style="margin-right: auto;">المحترمين</span>
        </div>

        <div class="subject">
            <u>الموضوع: اثبات مزايا وظيفية</u>
        </div>

        <p class="letter-body">
    السلام عليكم ورحمة الله وبركاته، وبعد ،،،
    <br><br>
    نفيدكم علماً بأن الموظف الموضحة بياناته أدناه يعمل لدينا وما زال على رأس العمل، 
    
    <?php if (isset($show_salary) && $show_salary): // <-- ADD THIS PHP IF-CONDITION ?>
        وأجره الشهري "<b>
            <span class="text-view" data-field="total_salary"><?php echo number_format($employee->total_salary, 2); ?></span>
            <input type="text" class="edit-view" data-field="total_salary" value="<?php echo htmlspecialchars($employee->total_salary, ENT_QUOTES); ?>">
        </b>" ريال سعودي،
    <?php endif; // <-- END THE IF-CONDITION ?>

    وتصرف له بشكل غير دائم بعض الامتيازات الوظيفية من حوافز ومكافأة غير منتظمة الصرف وغير دائمة الاستحقاق، وتم إصدار هذا الخطاب بناءً على طلب الموظف الموضح بياناته أدناه دون أدنى مسؤولية على الشركة.
</p>

        <div class="employee-details">
            <div class="detail-item">
                <span class="detail-label">الاســـــــــــــــــــــــــــــــــــم:</span>
                <div class="detail-value">
                    <span class="text-view" data-field="subscriber_name"><?php echo $employee->subscriber_name; ?></span>
                    <input type="text" class="edit-view" data-field="subscriber_name" value="<?php echo $employee->subscriber_name; ?>">
                </div>
            </div>
            <div class="detail-item">
                <span class="detail-label">الـجـنسيـــــــــــة:</span>
                <div class="detail-value">
                    <span class="text-view" data-field="nationality"><?php echo $employee->nationality; ?></span>
                    <input type="text" class="edit-view" data-field="nationality" value="<?php echo $employee->nationality; ?>">
                </div>
            </div>
            <div class="detail-item">
                <span class="detail-label">رقــــــم الـهويـــة:</span>
                <div class="detail-value">
                    <span class="text-view" data-field="id_number"><?php echo $employee->id_number; ?></span>
                    <input type="text" class="edit-view" data-field="id_number" value="<?php echo $employee->id_number; ?>">
                </div>
            </div>
            <div class="detail-item">
                <span class="detail-label">المسمى الوظيفي:</span>
                <div class="detail-value">
                    <span class="text-view" data-field="profession"><?php echo $employee->profession; ?></span>
                    <input type="text" class="edit-view" data-field="profession" value="<?php echo $employee->profession; ?>">
                </div>
            </div>
        </div>

        <p class="letter-closing">وتقبلوا فائق التحية و التقدير،،،</p>
        <p class="company-name">شــركـة مرسوم</p>
    </div>

    <script>
        document.getElementById('editButton').addEventListener('click', function() {
            const letterContainer = document.getElementById('letterContainer');
            const isEditing = letterContainer.classList.contains('editing-mode');
            const buttonIcon = this.querySelector('i');
            const buttonText = this.querySelector('span');

            if (isEditing) {
                // --- SAVE CHANGES ---
                // Find all input fields
                const inputs = letterContainer.querySelectorAll('.edit-view');
                inputs.forEach(input => {
                    // Find the corresponding text span
                    const textView = letterContainer.querySelector(`.text-view[data-field="${input.dataset.field}"]`);
                    if (textView) {
                        // For salary, format it with commas
                        if (input.dataset.field === 'total_salary') {
                            const numericValue = parseFloat(input.value) || 0;
                            textView.textContent = numericValue.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        } else {
                            textView.textContent = input.value;
                        }
                    }
                });

                // Switch back to view mode
                letterContainer.classList.remove('editing-mode');
                this.classList.remove('save-button');
                this.classList.add('edit-button');
                buttonIcon.className = 'fas fa-pencil-alt';
                buttonText.textContent = 'تعديل';

            } else {
                // --- ENTER EDIT MODE ---
                letterContainer.classList.add('editing-mode');
                this.classList.remove('edit-button');
                this.classList.add('save-button');
                buttonIcon.className = 'fas fa-save';
                buttonText.textContent = 'حفظ التعديلات';
            }
        });
    </script>

</body>
</html>