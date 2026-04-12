<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>نموذج التزام جهة العمل بتحويل راتب الموظف</title>
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
            line-height: 2.2; 
            font-size: 16px;
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
        .edit-button { background-color: #6c757d; }
        .save-button { background-color: #198754; }
        
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
            justify-content: flex-start; 
        }

        p, div { margin: 0; }
        .header-block { text-align: left; font-weight: 500; }
        .title-block { text-align: center; font-weight: 700; font-size: 1.1em; margin: 2rem 0; }
        .recipient-block, .greeting-block { font-weight: 700; margin-bottom: 1rem; }
        .employee-data-block { margin: 2rem 0; }
        .data-item { display: flex; margin-bottom: 0.5rem; }
        .data-label { font-weight: 700; width: 220px; flex-shrink: 0; }
        .data-value { font-weight: 500; }
        .body-block { text-align: justify; margin-top: 1.5rem; }
        
        /* Fixed Gap Logic */
        .closing-block { 
            font-weight: 700; 
            margin-top: 4rem; /* Fixed distance instead of 'auto' */
            padding-top: 1rem; 
        }

        .fillable {
            border: none; border-bottom: 1px dotted #888; background-color: #f8f9fa;
            padding: 0 4px; font-family: 'Tajawal', sans-serif; font-size: 1em;
            font-weight: 700; text-align: right; width: 150px;
        }
        .editable-field .edit-view {
            display: none; border: none; border-bottom: 1px dashed #0d6efd;
            font-family: 'Tajawal', sans-serif; font-size: 1em; font-weight: 700;
            padding: 0 2px;
        }
        .editing-mode .text-view { display: none; }
        .editing-mode .edit-view { display: inline-block; }
        .editing-mode .print-button { display: none; }

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
    }    }
    </style>
</head>
<body>

    <div class="print-controls">
        <button class="control-button edit-button" id="editButton"><i class="fas fa-pencil-alt"></i> <span>تعديل</span></button>
        <button class="control-button print-button" onclick="window.print()"><i class="fas fa-print"></i> <span>طباعة</span></button>
    </div>

    <div id="letterContainer" class="letter-container">
        <div class="header-block">
            <p>التاريخ: <input type="text" class="fillable" value="<?php echo date('Y-m-d'); ?>" readonly></p>
        </div>

        <div class="title-block">
            <p>نموذج التزام جهة العمل بتحويل راتب الموظف</p>
        </div>

        <div class="recipient-block">
            <span>المكرمون / <?php echo $employee->n3; ?></span>
            <span style="margin-right: auto;">المحترمين</span>
        </div>

        <div class="greeting-block">
            <span>السلام عليكم ورحمة الله وبركاته</span>
        </div>

        <div class="employee-data-block">
            <div class="data-item">
                <span class="data-label">اسم الموظف:</span>
                <div class="data-value editable-field">
                    <span class="text-view" data-field="subscriber_name"><?php echo $employee->subscriber_name; ?></span>
                    <input type="text" class="edit-view" data-field="subscriber_name" value="<?php echo htmlspecialchars($employee->subscriber_name); ?>" style="width: 250px;">
                </div>
            </div>
            <div class="data-item">
                <span class="data-label">رقم بطاقة العمل/ الرقم الوظيفي:</span>
                <div class="data-value editable-field">
                    <span class="text-view" data-field="employee_id"><?php echo $employee->employee_id; ?></span>
                    <input type="text" class="edit-view" data-field="employee_id" value="<?php echo htmlspecialchars($employee->employee_id); ?>">
                </div>
            </div>
            <div class="data-item">
                <span class="data-label">مسمى الوظيفة:</span>
                <div class="data-value editable-field">
                    <span class="text-view" data-field="profession"><?php echo $employee->profession; ?></span>
                    <input type="text" class="edit-view" data-field="profession" value="<?php echo htmlspecialchars($employee->profession); ?>">
                </div>
            </div>
            <div class="data-item">
                <span class="data-label">تاريخ الالتحاق بالعمل:</span>
                <div class="data-value editable-field">
                    <span class="text-view" data-field="joining_date"><?php echo date('d-m-Y', strtotime($employee->joining_date)); ?></span>
                    <input type="text" class="edit-view" data-field="joining_date" value="<?php echo htmlspecialchars(date('d-m-Y', strtotime($employee->joining_date))); ?>">
                </div>
            </div>
        </div>

        <div class="body-block">
            <p>
                تقدم موظفنا الموضحة بياناته أعلاه طالبا تحويل راتبه وبدلاته الشهرية وجميع مستحقاته الوظيفية الى حسابه الجاري رقم ايبان (
                <strong class="editable-field">
                    <span class="text-view" data-field="iban"><?php echo $employee->n2; ?></span>
                    <input type="text" class="edit-view" data-field="iban" value="<?php echo htmlspecialchars($employee->n2); ?>" style="width: 280px;">
                </strong>
                ) لأجل سداد الديون التي سوف تترتب عليه لصالح بنك <?php echo $employee->n3; ?>، لذا نود أن نؤكد لكم موافقتنا بهذا التحويل في مواعيده الشهرية واستمراره حتى نهاية علاقته الوظيفية معنا مع التزامنا عند انتهاء العلاقة التعاقدية بأشعاركم على البريد الالكتروني قبل تحويل مستحقات نهاية الخدمة وقد أعطي هذا الخطاب بناءً على طلبه دون ادنى مسؤولية على المكتب او تحملها أي مطالبات مالية او غير ذلك حالاً او مستقبلاً.
            </p>
        </div>

        <div class="closing-block">
            <p>مكتب / صالح بن منصور الجربوع محامون ومستشارون</p>
        </div>
    </div>

    <script>
        document.getElementById('editButton').addEventListener('click', function() {
            const letterContainer = document.getElementById('letterContainer');
            const isEditing = letterContainer.classList.contains('editing-mode');
            const buttonIcon = this.querySelector('i');
            const buttonText = this.querySelector('span');

            if (isEditing) {
                // SAVE
                const inputs = letterContainer.querySelectorAll('.edit-view');
                inputs.forEach(input => {
                    const textView = letterContainer.querySelector(`.text-view[data-field="${input.dataset.field}"]`);
                    if (textView) {
                        textView.textContent = input.value;
                    }
                });
                letterContainer.classList.remove('editing-mode');
                this.classList.remove('save-button');
                this.classList.add('edit-button');
                buttonIcon.className = 'fas fa-pencil-alt';
                buttonText.textContent = 'تعديل';
            } else {
                // ENTER EDIT MODE
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