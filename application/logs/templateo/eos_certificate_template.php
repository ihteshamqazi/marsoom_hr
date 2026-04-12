<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إفادة تحويل مستحقات نهاية الخدمة</title>
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
            line-height: 2.2;
            font-size: 16px;
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
        .save-button { background-color: #198754; }
        .save-button:hover { background-color: #157347; }
        
        .letter-container {
            width: 21cm;
            min-height: 29.7cm;
            padding: 2.5cm;
            background-color: white;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
            color: #212529;
            display: flex;
            flex-direction: column;
        }

        /* NEW STYLES FOR EXACT LAYOUT */
        p { margin: 0; }
        .line { display: flex; justify-content: space-between; align-items: center; font-weight: 700; width: 100%; }
        .body-block { text-align: justify; margin-top: 1.5rem; }
        .closing-block { text-align: left; font-weight: 700; margin-top: auto; padding-top: 4rem; }

        /* Styles for ALWAYS fillable input fields */
        .fillable {
            border: none; border-bottom: 1px dotted #888; background-color: #f8f9fa;
            padding: 0 4px; font-family: 'Tajawal', sans-serif; font-size: 1em;
            font-weight: 700; text-align: right;
        }
        .fillable-date { width: 130px; }
        .fillable-amount { width: 140px; }
        .fillable-contract-end { width: 120px; }

        /* Styles for TOGGLEABLE edit fields */
        .editable-field .edit-view {
            display: none; /* Hide inputs by default */
            border: none; border-bottom: 1px dashed #0d6efd;
            font-family: 'Tajawal', sans-serif; font-size: 1em; font-weight: 700;
            padding: 0 2px;
            min-width: 150px; /* Give some base width */
        }
        
        /* When in editing mode... */
        .editing-mode .text-view { display: none; }
        .editing-mode .edit-view { display: inline-block; }
        .editing-mode .print-button { display: none; }

        @media print {
            body { background-color: white; padding: 0; margin: 0; }
            .print-controls { display: none; }
            .letter-container { box-shadow: none; padding: 0; }
            .fillable { border-bottom: 1px solid transparent; background-color: transparent; font-weight: 700; }
            .edit-view { display: none !important; }
            .text-view { display: inline !important; font-weight: 700; }
            strong, b { font-weight: 700 !important; }
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

    <div id="letterContainer" class="letter-container">
        <div class="header-block">
            <p>التاريخ: <input type="text" class="fillable fillable-date" value="04/09/2025"></p>
            <p>الموافق: <input type="text" class="fillable fillable-date" value="12/03/1447"></p>
        </div>
        
        <br><br><br>

        <div class="line">
            <span>السادة/ بنك الرياض</span>
            <span>المحترمين،،،</span>
        </div>
        
        <div class="line">
            <span>الموضوع:</span>
            <span>إفادة تحويل مستحقات نهاية الخدمة</span>
        </div>
        
        <div class="line">
            <span>السلام عليكم ورحمة الله وبركاته</span>
            <span>وبعد،،،</span>
        </div>
        
        <div class="body-block">
            <p>
                نفيدكم نحن شركة مرسوم (سداد سابقاَ) بأن الأستاذ 
                <strong class="editable-field">
                    <span class="text-view" data-field="subscriber_name"><?php echo $employee->subscriber_name; ?></span>
                    <input type="text" class="edit-view" data-field="subscriber_name" value="<?php echo htmlspecialchars($employee->subscriber_name); ?>">
                </strong>
                سعودي الجنسية بموجب الهوية رقم (
                <strong class="editable-field">
                    <span class="text-view" data-field="id_number"><?php echo $employee->id_number; ?></span>
                    <input type="text" class="edit-view" data-field="id_number" value="<?php echo htmlspecialchars($employee->id_number); ?>">
                </strong>
                ) كان يعمل لدينا سابقاَ بوظيفة 
                <strong class="editable-field">
                    <span class="text-view" data-field="profession"><?php echo $employee->profession; ?></span>
                    <input type="text" class="edit-view" data-field="profession" value="<?php echo htmlspecialchars($employee->profession); ?>">
                </strong>
                وتم انتهاء علاقته التعاقدية بتاريخ <input type="text" class="fillable fillable-contract-end" placeholder="dd/mm/yyyy">، وقد تم تحويل كافة الحقوق التعاقدية والنظامية مثل (الرواتب الشهرية، قيمة الاجازات، مكافأة نهاية الخدمة، وغيرها) بمبلغ ((<input type="text" class="fillable fillable-amount" placeholder="اكتب المبلغ هنا">)) على حسابه البنكي (
                <strong class="editable-field">
                    <span class="text-view" data-field="iban"><?php echo $employee->n2; ?></span>
                    <input type="text" class="edit-view" data-field="iban" value="<?php echo htmlspecialchars($employee->n2); ?>">
                </strong>
                )، وقد أعطي هذا الخطاب بناءَ على طلبه دون أدنى مسؤولية على الشركة.
            </p>
        </div>

        <div class="closing-block">
            <p>شركة مرسوم</p>
        </div>
    </div>

    <script>
        document.getElementById('editButton').addEventListener('click', function() {
            const letterContainer = document.getElementById('letterContainer');
            const isEditing = letterContainer.classList.contains('editing-mode');
            const buttonIcon = this.querySelector('i');
            const buttonText = this.querySelector('span');

            if (isEditing) {
                // --- SAVE CHANGES ---
                const inputs = letterContainer.querySelectorAll('.edit-view');
                inputs.forEach(input => {
                    const textView = letterContainer.querySelector(`.text-view[data-field="${input.dataset.field}"]`);
                    if (textView) {
                        textView.textContent = input.value;
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
                buttonText.textContent = 'حفظ';
            }
        });
    </script>

</body>
</html>