<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Employment Certificate</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Tajawal', sans-serif; background-color: #EAECEF; display: flex; flex-direction: column; align-items: center; padding: 2rem 0; font-size: 14px; line-height: 1.8; }
        .print-controls { position: fixed; top: 1rem; left: 1rem; z-index: 100; background: #fff; padding: 0.5rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); display: flex; gap: 10px; }
        .control-button { padding: 10px 20px; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-family: 'Tajawal', sans-serif; }
        .print-button { background-color: #0d6efd; } .print-button:hover { background-color: #0b5ed7; }
        .edit-button { background-color: #6c757d; } .edit-button:hover { background-color: #5c636a; }
        .save-button { background-color: #198754; } .save-button:hover { background-color: #157347; }
        
        .letter-container {
            width: 21cm; min-height: 29.7cm; padding: 2cm;
            background-color: white; box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            box-sizing: border-box; color: #212529; display: flex; flex-direction: column;
        }
        
        /* NEW STYLES FOR BILINGUAL ROW ALIGNMENT */
        .bilingual-row {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }
        .bilingual-row .col-ar { width: 48%; text-align: right; }
        .bilingual-row .col-en { width: 48%; text-align: left; direction: ltr; }
        
        .title { font-size: 1.2em; font-weight: 700; margin: 1.5rem 0; }
        .recipient { font-weight: 700; margin-bottom: 2rem; }
        .body-text { text-align: justify; margin-bottom: 1.5rem; }
        
        .detail-row {
            border-bottom: 1px solid #eee;
            padding: 8px 0;
        }
        .detail-row .col-ar, .detail-row .col-en {
            display: flex;
            justify-content: space-between;
        }
        .data-label { font-weight: 700; }
        
        .closing-block { margin-top: auto; padding-top: 3rem; }
        .closing-block p { font-weight: 700; }
        
        .fillable, .edit-view { border: none; border-bottom: 1px dotted #888; background-color: #f8f9fa; font-family: inherit; font-size: inherit; font-weight: 500; }
        .edit-view { display: none; border-bottom: 1px dashed #0d6efd; width: 60%; }
        .editing-mode .text-view { display: none; }
        .editing-mode .edit-view { display: inline-block; }
        .editing-mode .print-button { display: none; }

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
        height:10mm; /* Adjust this value based on your company letterhead size */
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
        <button class="control-button edit-button" id="editButton"><i class="fas fa-pencil-alt"></i> <span>Edit</span></button>
        <button class="control-button print-button" onclick="window.print()"><i class="fas fa-print"></i> <span>Print</span></button>
    </div>

    <div id="letterContainer" class="letter-container">
        
        <div class="bilingual-row">
         
            <div class="col-en"><p><input type="text" class="fillable" style="width: 150px;" value="<?php echo date('F j, Y'); ?>"></p></div>
        </div>
        
        <div class="bilingual-row title">
            <div class="col-ar"><h3>خطاب تعريف</h3></div>
            <div class="col-en"><h3>Employment certificate</h3></div>
        </div>

        <div class="bilingual-row recipient">
            <div class="col-ar"><p>السادة: <input type="text" class="fillable" value="السفارة الإيطالية (الرياض)"></p></div>
            <div class="col-en"><p>To: <input type="text" class="fillable" value="Italian Embassy Riyadh"></p></div>
        </div>

        <div class="bilingual-row body-text">
            <div class="col-ar"><p>يشهد مكتب صالح بن منصور الجربوع محامون ومستشارون بأن المذكور ادناه يعمل لدينا ومازال على رأس العمل وقد أعطي هذا الخطاب، بناء على طلبه دون أدنى مسؤولية على الشركة.</p></div>
            <div class="col-en"><p>We, Saleh Mansour Aljarbou office law firm & consultants, confirm that below mentioned employee is still working with us & this letter has been issued for him per his request, without any responsibility on the company.</p></div>
        </div>

        <div class="bilingual-row detail-row">
            <div class="col-ar editable-field"><span class="data-label">الاسم:</span> <span class="text-view" data-field="name_ar"><?php echo $employee->subscriber_name; ?></span><input type="text" class="edit-view" data-field="name_ar" value="<?php echo htmlspecialchars($employee->subscriber_name); ?>"></div>
            <div class="col-en editable-field"><span class="data-label">Name:</span> <span class="text-view" data-field="name_en"><?php echo $employee->subscriber_name; ?></span><input type="text" class="edit-view" data-field="name_en" value="<?php echo htmlspecialchars($employee->subscriber_name); ?>"></div>
        </div>
        <div class="bilingual-row detail-row">
            <div class="col-ar editable-field"><span class="data-label">رقم الهوية:</span> <span class="text-view" data-field="id_ar"><?php echo $employee->id_number; ?></span><input type="text" class="edit-view" data-field="id_ar" value="<?php echo htmlspecialchars($employee->id_number); ?>"></div>
            <div class="col-en editable-field"><span class="data-label">National ID:</span> <span class="text-view" data-field="id_en"><?php echo $employee->id_number; ?></span><input type="text" class="edit-view" data-field="id_en" value="<?php echo htmlspecialchars($employee->id_number); ?>"></div>
        </div>
        <div class="bilingual-row detail-row">
            <div class="col-ar editable-field"><span class="data-label">الجنسية:</span> <span class="text-view" data-field="nat_ar"><?php echo $employee->nationality; ?></span><input type="text" class="edit-view" data-field="nat_ar" value="<?php echo htmlspecialchars($employee->nationality); ?>"></div>
            <div class="col-en editable-field"><span class="data-label">Nationality:</span> <span class="text-view" data-field="nat_en"><?php echo $employee->nationality; ?></span><input type="text" class="edit-view" data-field="nat_en" value="<?php echo htmlspecialchars($employee->nationality); ?>"></div>
        </div>
        <div class="bilingual-row detail-row">
            <div class="col-ar editable-field"><span class="data-label">المسمى الوظيفي:</span> <span class="text-view" data-field="prof_ar"><?php echo $employee->profession; ?></span><input type="text" class="edit-view" data-field="prof_ar" value="<?php echo htmlspecialchars($employee->profession); ?>"></div>
            <div class="col-en editable-field"><span class="data-label">Job title:</span> <span class="text-view" data-field="prof_en"><?php echo $employee->profession; ?></span><input type="text" class="edit-view" data-field="prof_en" value="<?php echo htmlspecialchars($employee->profession); ?>"></div>
        </div>
        <div class="bilingual-row detail-row">
            <div class="col-ar editable-field"><span class="data-label">تاريخ التعيين:</span> <span class="text-view" data-field="join_ar"><?php echo date('d-m-Y', strtotime($employee->joining_date)); ?></span><input type="text" class="edit-view" data-field="join_ar" value="<?php echo htmlspecialchars(date('d-m-Y', strtotime($employee->joining_date))); ?>"></div>
            <div class="col-en editable-field"><span class="data-label">Joining date:</span> <span class="text-view" data-field="join_en"><?php echo date('d-m-Y', strtotime($employee->joining_date)); ?></span><input type="text" class="edit-view" data-field="join_en" value="<?php echo htmlspecialchars(date('d-m-Y', strtotime($employee->joining_date))); ?>"></div>
        </div>
        <div class="bilingual-row detail-row">
            <div class="col-ar editable-field"><span class="data-label">إجمالي الراتب:</span> <span class="text-view" data-field="sal_ar"><?php echo number_format($employee->total_salary, 2); ?></span><input type="text" class="edit-view" data-field="sal_ar" value="<?php echo htmlspecialchars($employee->total_salary); ?>"></div>
            <div class="col-en editable-field"><span class="data-label">Total salary:</span> <span class="text-view" data-field="sal_en"><?php echo number_format($employee->total_salary, 2); ?></span><input type="text" class="edit-view" data-field="sal_en" value="<?php echo htmlspecialchars($employee->total_salary); ?>"></div>
        </div>

        <div style="flex-grow: 1;"></div>

        <div class="bilingual-row closing-block">
            <div class="col-ar">
                <p>مكتب صالح بن منصور الجربوع</p>
                <p>محامون ومستشارون</p>
            </div>
            <div class="col-en">
                <p>Sincerely,</p>
                <p>Saleh Mansour Aljarbou office</p>
                <p>law firm & consultants</p>
            </div>
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
                    if (textView) { textView.textContent = input.value; }
                });
                letterContainer.classList.remove('editing-mode');
                this.classList.remove('save-button');
                this.classList.add('edit-button');
                buttonIcon.className = 'fas fa-pencil-alt';
                buttonText.textContent = 'Edit';
            } else {
                // ENTER EDIT MODE
                letterContainer.classList.add('editing-mode');
                this.classList.remove('edit-button');
                this.classList.add('save-button');
                buttonIcon.className = 'fas fa-save';
                buttonText.textContent = 'Save';
            }
        });
    </script>

</body>
</html>