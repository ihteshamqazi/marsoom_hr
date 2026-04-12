<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم الموظفين - نظام الموارد البشرية</title>

    <!-- تضمين خط Tajawal من Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <!-- تضمين Font Awesome لإيقونات رائعة -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" rel="stylesheet">

    <style>
        /* الخط الأساسي للجسم وتأثير الخلفية */
        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%); /* تدرج لوني رمادي-أزرق فاتح وناعم */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            /*justify-content: center; /* تم إزالة هذا لتجنب المشاكل مع التمرير العام للصفحة */*/
            align-items: center;
            padding: 30px;
            overflow-x: hidden; /* لمنع ظهور شريط التمرير الأفقي */
        }

        /* حاوية الصفحة الرئيسية */
        .container-fluid {
            max-width: 1500px; /* جعل الجدول أوسع قليلاً */
            background-color: #ffffff;
            border-radius: 25px; /* حواف أكثر استدارة */
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.18); /* ظل أعمق وأكثر انتشاراً */
            padding: 40px;
            animation: fadeInScale 0.9s ease-out forwards; /* أنيميشن دخول للحاوية */
            margin-bottom: 40px; /* مسافة إضافية في الأسفل */
        }

        /* عنوان الصفحة */
        .page-header {
            font-family: 'Tajawal', sans-serif;
            font-weight: 800;
            color: #2c3e50;
            text-align: center;
            margin-bottom: 50px;
            font-size: 3.2rem; /* حجم أكبر للعنوان */
            text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.15);
            animation: fadeInDown 0.8s ease-out forwards; /* أنيميشن دخول للعنوان */
            position: relative;
        }
        .page-header::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 5px;
            background: linear-gradient(90deg, #6a93cb, #88b0eb); /* شريط زخرفي تحت العنوان */
            border-radius: 5px;
        }

        /* تصميم DataTable */
        .data-table-container {
            padding: 25px;
            background-color: #fcfdfe; /* خلفية بيضاء ناعمة للجدول */
            border-radius: 20px;
            box-shadow: inset 0 3px 10px rgba(0, 0, 0, 0.07); /* ظل داخلي أكثر وضوحاً */
            border: 1px solid #e0e6ed; /* حد خفيف حول الحاوية */
        }

        /* تصحيح رأس الجدول مع التمرير */
        table.dataTable thead {
            background: linear-gradient(90deg, #537bbd, #6a93cb); /* تدرج لوني لرأس الجدول */
            color: white;
            font-weight: 700;
            border-bottom: 4px solid #4168a2; /* حد سفلي سميك ومظلم */
        }

        table.dataTable thead th {
            text-align: right;
            padding: 15px 20px;
            white-space: nowrap; /* يمنع التفاف النص في الرأس */
        }
        
        /* هذا هام جداً لعمل ScrollY مع DataTables */
        div.dataTables_wrapper div.dataTables_scrollBody {
            border-bottom: 1px solid #e9f2fb; /* خط أسفل جسم الجدول */
            border-left: 1px solid #e9f2fb; /* خط يسار جسم الجدول */
            border-right: 1px solid #e9f2fb; /* خط يمين جسم الجدول */
            border-radius: 0 0 20px 20px; /* حواف مستديرة سفلية */
        }

        div.dataTables_wrapper div.dataTables_scrollHead table.dataTable,
        div.dataTables_wrapper div.dataTables_scrollFoot table.dataTable {
            margin-bottom: 0 !important;
        }
        
        /* إخفاء شريط التمرير الأفقي الزائد في رأس الجدول إذا ظهر */
        div.dataTables_scrollHeadInner {
            padding-right: 0 !important;
        }

        table.dataTable tbody tr {
            transition: all 0.3s ease;
            cursor: pointer; /* مؤشر اليد عند التحليق */
        }

        table.dataTable tbody tr:nth-child(even) {
            background-color: #f8fbfd; /* صفوف متناوبة للحصول على مظهر نظيف */
        }

        table.dataTable tbody tr:hover {
            background-color: #eef7ff; /* تأثير التحليق على الصفوف */
            transform: translateY(-3px); /* رفع خفيف للصف */
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1); /* ظل أكثر بروزاً عند التحليق */
        }

        table.dataTable td {
            padding: 12px 20px;
            vertical-align: middle; /* محاذاة رأسية لوسط الخلية */
        }

        /* تخصيص أزرار DataTables (تصدير) */
        .dt-buttons .btn {
            background-color: #28a745; /* لون أخضر جذاب للتصدير */
            color: white;
            border: none;
            border-radius: 10px; /* حواف أكثر استدارة */
            padding: 10px 20px;
            margin-right: 8px; /* مسافة بين الأزرار */
            font-weight: 600;
            transition: all 0.3s ease, transform 0.2s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.12);
        }

        .dt-buttons .btn:hover {
            background-color: #218838;
            transform: translateY(-3px); /* رفع خفيف عند التحليق */
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        /* تخصيص مربع البحث */
        .dataTables_filter label {
            font-weight: 600;
            color: #555;
            font-size: 1.1rem;
            white-space: nowrap; /* منع التفاف النص */
            margin-left: 8px; /* مسافة بين النص ومربع البحث */
        }

        .dataTables_filter input {
            border-radius: 10px; /* حواف مستديرة */
            border: 1px solid #cce0ee; /* حد أزرق فاتح */
            padding: 10px 15px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            width: 250px; /* عرض ثابت لمربع البحث */
        }

        .dataTables_filter input:focus {
            border-color: #6a93cb;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(106, 147, 203, 0.25); /* ظل تركيز أزرق فاتح */
        }

        /* تخصيص التنقل (Pagination) */
        .dataTables_paginate .pagination .page-item .page-link {
            border-radius: 10px; /* حواف مستديرة */
            margin: 0 4px;
            color: #537bbd;
            border: 1px solid #e0e6ed;
            transition: all 0.3s ease;
            min-width: 40px; /* عرض أدنى للأزرار */
            text-align: center;
        }

        .dataTables_paginate .pagination .page-item.active .page-link {
            background: linear-gradient(90deg, #537bbd, #6a93cb); /* تدرج لوني للزر النشط */
            color: white;
            border-color: #537bbd;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .dataTables_paginate .pagination .page-item .page-link:hover {
            background-color: #eef7ff;
            color: #4168a2;
            border-color: #cce0ee;
        }

        /* رسوم متحركة عامة */
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.97); }
            to { opacity: 1; transform: scale(1); }
        }

        /* تخصيص لوحة إظهار/إخفاء الأعمدة (الموجودة كـ checkboxes) */
        .column-toggle-panel {
            background-color: #ffffff;
            border: 1px solid #e0e6ed;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 20px;
            position: absolute; /* وضعها كقائمة منسدلة */
            top: calc(100% + 15px); /* أسفل الزر قليلاً */
            right: 0; /* محاذاة لليمين */
            z-index: 1000;
            min-width: 220px;
            max-height: 300px;
            overflow-y: auto;
            transform-origin: top right;
            animation: expandAndFadeIn 0.3s ease-out forwards;
            display: none; /* مخفي افتراضياً */
        }

        .column-toggle-panel.active {
            display: block; /* يظهر عند التفعيل */
        }

        .column-toggle-panel label {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            font-size: 1rem;
            color: #333;
            cursor: pointer;
            transition: color 0.2s ease;
        }
        .column-toggle-panel label:hover {
            color: #537bbd;
        }

        .column-toggle-panel input[type="checkbox"] {
            margin-left: 10px; /* مسافة بين النص والمربع */
            transform: scale(1.2); /* تكبير مربع التحديد قليلاً */
            accent-color: #537bbd; /* لون مربع التحديد */
            cursor: pointer;
        }

        @keyframes expandAndFadeIn {
            from { opacity: 0; transform: scaleY(0.8) translateY(-10px); }
            to { opacity: 1; transform: scaleY(1) translateY(0); }
        }

        /* زر إظهار/إخفاء الأعمدة المخصص */
        .custom-colvis-btn {
            background-color: #17a2b8; /* لون أزرق سماوي جذاب */
            color: white;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease, transform 0.2s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.12);
        }

        .custom-colvis-btn:hover {
            background-color: #138496;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        /* تحسينات للوضوح على الشاشات الصغيرة (Responsive) */
        @media (max-width: 991px) {
            .page-header {
                font-size: 2.2rem;
                margin-bottom: 30px;
            }
            .container-fluid {
                padding: 25px;
            }
            /* ترتيب العناصر العلوية على الشاشات الصغيرة */
            .d-flex.flex-wrap.justify-content-start {
                justify-content: center !important; /* توسيط العناصر على الشاشات الصغيرة */
            }
            /* لجعل حاويات العناصر تأخذ عرض 100% على الجوال */
            #filter-container,
            #toggleColumnsBtn,
            #buttons-container,
            #length-container {
                width: 100%;
                margin-left: 0 !important; /* إزالة المسافات الجانبية */
                margin-right: 0 !important; /* إزالة المسافات الجانبية */
                margin-bottom: 10px; /* مسافة بين العناصر المكدسة */
            }
            .column-toggle-panel {
                left: 50%; /* توسيط القائمة المنسدلة على الجوال */
                right: auto;
                transform: translateX(-50%);
                transform-origin: top center;
                min-width: 90%; /* عرض أكبر للوحة على الجوال */
            }
            .dataTables_filter input {
                width: calc(100% - 70px); /* Adjust width considering label width */
            }
            .dataTables_filter label {
                display: inline-block; /* Keep label inline */
                width: 60px; /* Give label a fixed width */
                text-align: end;
            }
        }
    </style>
</head>
<body>

    <div class="container-fluid">
        <h5 class="page-header" style="font-size:30px;">         الخصومات     </h5>

        <div class="data-table-container">
            <!-- حاوية العناصر العلوية للتحكم في الجدول -->
             <div class="d-flex flex-wrap justify-content-start align-items-center mb-4 gap-3">
    <!-- مربع البحث (Search Input) -->
    <div id="filter-container" class="order-0"></div>

    <!-- زر إظهار/إخفاء الأعمدة المخصص مع قائمة Checkbox -->
    <div class="position-relative order-1">
        <button id="toggleColumnsBtn" class="btn custom-colvis-btn">
            <i class="fas fa-columns"></i> إظهار/إخفاء الأعمدة
        </button>
        <div id="columnTogglePanel" class="column-toggle-panel">
            <!-- Checkboxes ستضاف هنا ديناميكياً بواسطة JavaScript -->
        </div>
    </div>

    <!-- أزرار DataTables القياسية (مثل تصدير Excel) -->
    <div id="buttons-container" class="order-2"></div>

    <!-- خيار تغيير عدد الصفوف المعروضة (Length Change) -->
    <div id="length-container" class="order-3"></div>

    <!-- زر لفتح نافذة منبثقة لصفحة إضافة الإجازات -->
   <div class="order-4">
    <button class="btn custom-colvis-btn" onclick="openVacationForm()">
        <i class="fas fa-plus-circle"style="color: white; font-weight: bold;"></i> 
        <span style="color: white; font-weight: bold;">إضافة  خصم</span>
    </button>
</div>

</div>

<script>
    // دالة لفتح شاشة إضافة الإجازات في نافذة منبثقة في المنتصف
    function openVacationForm() {
        var width = 800; // عرض النافذة
        var height = 600; // ارتفاع النافذة
        var left = (window.innerWidth / 2) - (width / 2); // حساب المسافة من اليسار لفتح النافذة في المنتصف
        var top = (window.innerHeight / 2) - (height / 2); // حساب المسافة من الأعلى لفتح النافذة في المنتصف

        window.open("<?php echo site_url('users/add_vacations'); ?>", 
                    "Add Vacation", 
                    "width=" + width + ",height=" + height + ",left=" + left + ",top=" + top + ",scrollbars=yes,resizable=yes");
    }
</script>


            <table id="employeeTable" class="table table-striped table-hover dt-responsive nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>   الرقم </th>
                        <th>    سبب الخصم</th>
                        <th>    الرقم الوظيفي </th>
                        <th>      اسم الموظف</th>
                        <th>      مبلغ الخصم</th>
                        <th>         الرقم الوظيفي (منشئ الطلب) </th>
                        <th>      اسم الموظف (منشئ الطلب)</th>
                        <th>  تاريخ الطلب   </th>
                         <th>       وقت الطلب </th>
                         <th>     مسير الرواتب المراد الخصم منه </th>
                    </tr>
                </thead>
                <tbody>

                    <?php $idno=0; ?>
                                    <?php foreach($get_salary_vacations as $get_salary_vacations) : ?>
                                        <?php $idno ++; ?>
                             <tr  style="font-family: 'Tajawal', sans-serif; font-weight: bold; font-style: normal; font-size:12px;">
                                       <td ><?php    echo $idno; ?></td> 
                                         
                                       <td ><?php      echo $get_salary_vacations['type']; ?></td> 
                                       <td ><?php     echo $get_salary_vacations['emp_id']; ?></td>
                                       <td ><?php     echo $get_salary_vacations['emp_name']; ?></td> 
                                       <td ><?php     echo $get_salary_vacations['amount']; ?></td> 
                                        <td ><?php     echo $get_salary_vacations['username']; ?></td> 
                                       <td ><?php      echo $get_salary_vacations['name']; ?></td>
                                          <td ><?php      echo $get_salary_vacations['date']; ?></td>
                                        <td ><?php   
                                       echo $get_salary_vacations['time']; ?></td>
                                       <td ><?php   
                                       echo $get_salary_vacations['sheet_id']; ?></td> 
                                        

                                   </tr>
                                   <?php endforeach; ?>  
                   
                </tbody>
            </table>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>

    <!-- DataTables Buttons Extension -->
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.5.0/jszip.min.js"></script> <!-- Required for Excel export -->
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script> <!-- HTML5 export (Excel) -->
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script> <!-- Optional: Print button -->
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // تهيئة DataTables
            var table = $('#employeeTable').DataTable({
                searching: true,
                paging: true,
                lengthChange: true,
                responsive: true,
                scrollY: '60vh', // تفعيل التمرير العمودي بارتفاع 60% من ارتفاع الشاشة
                scrollCollapse: true, // يقلص الجدول إذا كانت الصفوف أقل من الارتفاع المحدد
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> تصدير Excel',
                        className: 'btn btn-success'
                    },
                ],
                // جعل DataTables لا يقوم بوضع عناصر التحكم (l, f, B) تلقائياً،
                // سنقوم بوضعها يدوياً للتحكم الكامل في الترتيب.
                dom: 'rtip', // r: Responsive, t: Table, i: Info, p: Pagination
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.3/i18n/ar.json' // ملف اللغة العربية
                }
            });

            // نقل عناصر DataTables التي تم إنشاؤها تلقائياً إلى المواقع الجديدة المحددة في HTML
            // (f) مربع البحث
            $('.dataTables_filter').appendTo('#filter-container');
            // (B) أزرار DataTables (مثل تصدير Excel)
            table.buttons().container().appendTo('#buttons-container');
            // (l) خيار تغيير عدد الصفوف المعروضة
            $('.dataTables_length').appendTo('#length-container');


            // ----------------------------------------------------------------------
            // منطق إظهار/إخفاء الأعمدة باستخدام Checkboxes
            // ----------------------------------------------------------------------
            var columnTogglePanel = $('#columnTogglePanel');
            var toggleColumnsBtn = $('#toggleColumnsBtn');

            // توليد صناديق التحديد للأعمدة
            table.columns().every(function() {
                var column = this;
                var title = $(column.header()).text(); // اسم العمود
                var isVisible = column.visible(); // هل العمود مرئي حالياً

                var checkboxId = 'toggle-col-' + column.index();
                var checkboxHtml = `
                    <label for="${checkboxId}">
                        <input type="checkbox" id="${checkboxId}" data-column="${column.index()}" ${isVisible ? 'checked' : ''}>
                        ${title}
                    </label>
                `;
                columnTogglePanel.append(checkboxHtml);
            });

            // معالجة تغيير حالة صناديق التحديد
            columnTogglePanel.on('change', 'input[type="checkbox"]', function() {
                var columnIdx = $(this).data('column');
                var column = table.column(columnIdx);
                column.visible($(this).is(':checked')); // إظهار أو إخفاء العمود
            });

            // تبديل ظهور لوحة التحكم بالأعمدة عند النقر على الزر
            toggleColumnsBtn.on('click', function(e) {
                e.stopPropagation(); // منع إغلاق اللوحة فوراً بسبب click خارجها
                columnTogglePanel.toggleClass('active');
            });

            // إخفاء لوحة التحكم بالأعمدة عند النقر خارجها
            $(document).on('click', function(e) {
                if (!columnTogglePanel.is(e.target) && columnTogglePanel.has(e.target).length === 0 && !toggleColumnsBtn.is(e.target)) {
                    columnTogglePanel.removeClass('active');
                }
            });
            // ----------------------------------------------------------------------
        });
    </script>

</body>
</html>
