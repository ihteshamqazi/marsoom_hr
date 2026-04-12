<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بيانات الموظفين - نظام مرسوم</title>

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
        /* ==========================================================================
        Marsoom UI — Calm Theme - Employee Data Page
        Version: 2.0.0 (Enhanced Professional)
        ========================================================================== */

        /* ---------------------- Theme Vars ---------------------- */
        :root {
            --marsom-blue: #001f3f; /* Deep blue from logo */
            --marsom-orange: #FF8C00; /* Warm orange from logo */
            --text-light: #ffffff;
            --text-muted-light: rgba(255, 255, 255, 0.7);
            --glass-bg: rgba(255, 255, 255, 0.08); /* More subtle transparency */
            --glass-border: rgba(255, 255, 255, 0.2);
            --glass-shadow: rgba(0, 0, 0, 0.5);

            --primary-color: #0d6efd; /* Fallback/Accent blue */
            --primary-light: #eef5ff;
            --secondary-color: #6c757d;
            --bg-light: #f8f9fa; /* Not used for body, but kept for consistency */
            --card-bg: #ffffff;
            --text-dark: #212529;
            --text-muted: #6c757d;
            --border-color: #e6e9f0;

            --shadow-sm: 0 2px 6px rgba(0,0,0,.05);
            --shadow-md: 0 6px 14px rgba(0,0,0,.06);
            --shadow-lg: 0 12px 24px rgba(0,0,0,.08);

            /* Motion (calm) */
            --ani-duration: 320ms;
            --ani-ease: cubic-bezier(.22,.61,.36,1);
            --hover-translate: -4px;
            --lift-shadow: var(--shadow-md);
        }

        /* ---------------------- Base ---------------------- */
        html, body {
            height: 100%;
            font-family: 'Tajawal', sans-serif; /* Changed to Tajawal as requested for main text */
            background: linear-gradient(135deg, var(--marsom-blue) 0%, #34495e 50%, var(--marsom-orange) 100%); /* Brand colors gradient */
            background-size: 400% 400%;
            animation: gradientAnimation 20s ease infinite;
            color: var(--text-dark); /* Default text color, overridden by main-screen-container */
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            overflow-x: hidden; /* لمنع ظهور شريط التمرير الأفقي */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative; /* For particle animation */
        }

        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Particle Background Animation with Hexagons */
        .particles {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            overflow: hidden;
            z-index: 0; /* Behind main content */
        }

        .particle {
            position: absolute;
            background: rgba(255, 140, 0, 0.1); /* Subtle orange tint for particles */
            clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%); /* Hexagon shape */
            animation: float 25s infinite ease-in-out;
            opacity: 0;
            filter: blur(2px);
        }
        .particle:nth-child(even) { background: rgba(0, 31, 63, 0.1); } /* Blue tint for even particles */

        /* Random sizes and positions for particles */
        .particle:nth-child(1) { width: 40px; height: 40px; left: 10%; top: 20%; animation-duration: 18s; animation-delay: 0s; }
        .particle:nth-child(2) { width: 70px; height: 70px; left: 25%; top: 50%; animation-duration: 22s; animation-delay: 2s; }
        .particle:nth-child(3) { width: 55px; height: 55px; left: 40%; top: 10%; animation-duration: 25s; animation-delay: 5s; }
        .particle:nth-child(4) { width: 80px; height: 80px; left: 60%; top: 70%; animation-duration: 20s; animation-delay: 8s; }
        .particle:nth-child(5) { width: 60px; height: 60px; left: 80%; top: 30%; animation-duration: 23s; animation-delay: 10s; }
        .particle:nth-child(6) { width: 45px; height: 45px; left: 5%; top: 85%; animation-duration: 19s; animation-delay: 3s; }
        .particle:nth-child(7) { width: 90px; height: 90px; left: 70%; top: 5%; animation-duration: 28s; animation-delay: 6s; }
        .particle:nth-child(8) { width: 35px; height: 35px; left: 90%; top: 40%; animation-duration: 17s; animation-delay: 12s; }
        .particle:nth-child(9) { width: 75px; height: 75px; left: 20%; top: 75%; animation-duration: 21s; animation-delay: 1s; }
        .particle:nth-child(10) { width: 65px; height: 65px; left: 50%; top: 90%; animation-duration: 24s; animation-delay: 4s; }

        @keyframes float {
            0% { transform: translateY(0) translateX(0) rotate(0deg); opacity: 0; }
            20% { opacity: 1; }
            80% { opacity: 1; }
            100% { transform: translateY(-100vh) translateX(50px) rotate(360deg); opacity: 0; } /* Added rotation */
        }

        /* ---------------------- Page Layout ---------------------- */
        .container-fluid {
            max-width: 1400px; /* جعل الجدول أوسع قليلاً */
            background: var(--glass-bg);
            backdrop-filter: blur(15px); /* Stronger blur for more depth */
            -webkit-backdrop-filter: blur(15px);
            border-radius: 25px; /* حواف أكثر استدارة */
            border: 1px solid var(--glass-border);
            box-shadow: 0 20px 50px 0 var(--glass-shadow); /* ظل أعمق وأكثر انتشاراً */
            padding: 30px; /* تقليل البادينغ الداخلي */
            animation: fadeInScale var(--ani-duration) var(--ani-ease) forwards; /* أنيميشن دخول للحاوية */
            z-index: 1; /* Above particles */
            color: var(--text-light); /* Text color for content inside glass container */
        }

        /* ---------------------- Top Page Navigation ---------------------- */
        .page-top-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--glass-border); /* Glass border */
        }
        .page-top-nav .btn {
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.2s ease;
            background-color: rgba(255, 255, 255, 0.1); /* Subtle glass button */
            border-color: var(--glass-border);
            color: var(--text-light);
        }
        .page-top-nav .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            background-color: rgba(255, 255, 255, 0.2);
            color: var(--marsom-orange);
        }
        .page-top-nav .btn i {
            color: var(--text-light); /* Default icon color */
            transition: color 0.2s ease;
        }
        .page-top-nav .btn:hover i {
            color: var(--marsom-orange); /* Orange icon on hover */
        }
        .page-top-nav h5 {
            font-family: 'El Messiri', sans-serif; /* Luxurious font for heading */
            font-weight: 700;
            color: var(--text-light);
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        /* ---------------------- Page Header ---------------------- */
        .page-header {
            font-family: 'El Messiri', sans-serif; /* Luxurious font for heading */
            font-weight: 800;
            color: var(--text-light); /* White text for contrast */
            text-align: center;
            margin-bottom: 40px;
            font-size: 2.8rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.4);
            animation: fadeInDown 0.8s var(--ani-ease) forwards;
            position: relative;
        }
        .page-header::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 70px;
            height: 4px;
            background: linear-gradient(90deg, var(--marsom-orange), #ffc107); /* Orange gradient underline */
            border-radius: 5px;
        }

        /* ---------------------- DataTable Styling ---------------------- */
        .data-table-container {
            padding: 25px;
            background-color: rgba(255, 255, 255, 0.05); /* Very subtle transparent background */
            border-radius: 20px;
            box-shadow: inset 0 3px 10px rgba(0, 0, 0, 0.07);
            border: 1px solid var(--glass-border);
        }

        table.dataTable thead th {
            background: linear-gradient(90deg, var(--marsom-blue), #34495e); /* Deep blue gradient for header */
            color: white;
            font-weight: 700;
            border-bottom: 4px solid #00152b; /* Darker blue bottom border */
            text-align: right;
            padding: 15px 20px;
            white-space: nowrap;
            font-size: 1.1em; /* Slightly larger header font */
        }

        table.dataTable tbody tr {
            transition: all 0.3s ease;
            cursor: pointer;
            color: var(--text-light); /* White text for table rows */
        }

        table.dataTable tbody tr:nth-child(even) {
            background-color: rgba(255, 255, 255, 0.03); /* More subtle alternating rows */
        }

        table.dataTable tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.15); /* Stronger hover effect */
            transform: translateY(-3px) scale(1.01); /* Lift and slight scale */
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
        }

        table.dataTable td {
            padding: 12px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1); /* Lighter border between rows */
            vertical-align: middle;
        }
        table.dataTable td img {
            border: 2px solid var(--marsom-orange); /* Orange border for avatars */
        }


        /* تخصيص أزرار DataTables (تصدير) */
        .dt-buttons .btn {
            background: linear-gradient(90deg, #28a745, #218838); /* Green gradient */
            color: white;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            margin: 0 8px 20px 0;
            font-weight: 600;
            transition: all 0.3s ease, transform 0.2s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.12);
        }

        .dt-buttons .btn:hover {
            background: linear-gradient(90deg, #218838, #1e7e34);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        /* تخصيص مربع البحث */
        .dataTables_filter label {
            font-weight: 600;
            color: var(--text-light); /* White text for search label */
            font-size: 1.1rem;
        }

        .dataTables_filter input {
            border-radius: 10px;
            border: 1px solid var(--glass-border); /* Glass border */
            background-color: rgba(255, 255, 255, 0.1); /* Subtle glass background */
            color: var(--text-light); /* White text for input */
            padding: 10px 15px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            width: 250px;
        }
        .dataTables_filter input::placeholder {
            color: var(--text-muted-light); /* Lighter placeholder */
        }

        .dataTables_filter input:focus {
            border-color: var(--marsom-orange); /* Orange focus border */
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(255, 140, 0, 0.25); /* Orange focus shadow */
            background-color: rgba(255, 255, 255, 0.15);
        }

        /* تخصيص التنقل (Pagination) */
        .dataTables_paginate .pagination .page-item .page-link {
            border-radius: 10px;
            margin: 0 4px;
            color: var(--text-light); /* White text for pagination */
            border: 1px solid var(--glass-border);
            background-color: rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            min-width: 40px;
            text-align: center;
        }

        .dataTables_paginate .pagination .page-item.active .page-link {
            background: linear-gradient(90deg, var(--marsom-orange), #ffc107); /* Orange gradient for active */
            color: white;
            border-color: var(--marsom-orange);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .dataTables_paginate .pagination .page-item .page-link:hover {
            background-color: rgba(255, 255, 255, 0.2);
            color: var(--marsom-orange);
            border-color: var(--marsom-orange);
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
            background: var(--glass-bg); /* Glass background */
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            padding: 20px;
            position: absolute;
            top: calc(100% + 15px);
            right: 0;
            z-index: 1000;
            min-width: 220px;
            max-height: 300px;
            overflow-y: auto;
            transform-origin: top right;
            animation: expandAndFadeIn 0.3s ease-out forwards;
            display: none;
            color: var(--text-light); /* White text */
        }

        .column-toggle-panel.active {
            display: block;
        }

        .column-toggle-panel label {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            font-size: 1rem;
            color: var(--text-light);
            cursor: pointer;
            transition: color 0.2s ease;
        }
        .column-toggle-panel label:hover {
            color: var(--marsom-orange);
        }

        .column-toggle-panel input[type="checkbox"] {
            margin-left: 10px;
            transform: scale(1.2);
            accent-color: var(--marsom-orange); /* Orange accent */
            cursor: pointer;
        }

        @keyframes expandAndFadeIn {
            from { opacity: 0; transform: scaleY(0.8) translateY(-10px); }
            to { opacity: 1; transform: scaleY(1) translateY(0); }
        }

        /* زر إظهار/إخفاء الأعمدة المخصص */
        .custom-colvis-btn {
            background: linear-gradient(90deg, #17a2b8, #138496); /* Cyan gradient */
            color: white;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease, transform 0.2s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.12);
        }

        .custom-colvis-btn:hover {
            background: linear-gradient(90deg, #138496, #117a8b);
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
            .dataTables_wrapper .row {
                flex-direction: column;
                align-items: center;
            }
            .dataTables_wrapper .col-md-6,
            .dataTables_wrapper .col-md-12 {
                width: 100%;
                text-align: center !important;
                margin-bottom: 15px;
            }
            .dataTables_filter input {
                width: 90%;
            }
            .dt-buttons .btn {
                margin-bottom: 10px;
            }
            .column-toggle-panel {
                left: 50%;
                right: auto;
                transform: translateX(-50%);
                transform-origin: top center;
            }
        }
    </style>
</head>
<body>

    <!-- Particle Background -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="container-fluid">
        <!-- Top Page Navigation -->
        <div class="page-top-nav">
            <button class="btn btn-outline-secondary" onclick="window.history.back()">
                <i class="fas fa-arrow-right me-2"></i> رجوع
            </button>
            <h5 class="d-none d-md-block">بيانات الموظفين</h5>
            <button class="btn btn-outline-primary" onclick="window.location.href='#'">
                <i class="fas fa-home me-2"></i> الرئيسية
            </button>
        </div>

        <h3 class="page-header">بيانات الموظفين</h3>

        <div class="data-table-container">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <!-- أزرار DataTables القياسية ستظهر هنا -->
                <div id="dt-buttons-container" class="order-2 order-md-1"></div>

                <!-- زر إظهار/إخفاء الأعمدة المخصص مع قائمة Checkbox -->
                <div class="position-relative order-1 order-md-2 mb-3 mb-md-0 me-md-3">
                    <button id="toggleColumnsBtn" class="btn custom-colvis-btn">
                        <i class="fas fa-columns"></i> إظهار/إخفاء الأعمدة
                    </button>
                    <div id="columnTogglePanel" class="column-toggle-panel">
                        <!-- Checkboxes ستضاف هنا ديناميكياً بواسطة JavaScript -->
                    </div>
                </div>

                <!-- مربع البحث وخيار عدد الصفوف سيظهر هنا تلقائياً بفضل Dom -->
            </div>

            <table id="employeeTable" class="table table-striped table-hover dt-responsive nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>الرقم</th>
                        <th>الرقم الوظيفي</th>
                        <th>رقم الهوية</th>
                        <th>اسم الموظف</th>
                        <th>الجنسية</th>
                        <th>الجنس</th>
                        <th>تاريخ الميلاد</th>
                        <th>اجمالي الراتب</th>
                        <th>المسمى الوظيفي</th>
                        <th>نوع الطلب</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $idno=0; ?>
                    <?php foreach($get_salary_vacations as $get_salary_vacations) : ?>
                        <?php $idno ++; ?>
                        <tr style="font-family: 'Tajawal', sans-serif; font-weight: bold; font-style: normal; font-size:12px;">
                            <td><?php echo $idno; ?></td>
                            <td><?php echo $get_salary_vacations['employee_id']; ?></td>
                            <td><?php echo $get_salary_vacations['id_number']; ?></td>
                            <td><?php echo $get_salary_vacations['subscriber_name']; ?></td>
                            <td><?php echo $get_salary_vacations['nationality']; ?></td>
                            <td><?php echo $get_salary_vacations['gender']; ?></td>
                            <td><?php echo $get_salary_vacations['birth_date']; ?></td>
                            <td><?php echo $get_salary_vacations['total_salary']; ?></td>
                            <td><?php echo $get_salary_vacations['profession']; ?></td>
                            <td><?php echo $get_salary_vacations['company_name']; ?></td>
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
        document.addEventListener('DOMContentLoaded', function() {
            // تهيئة DataTables
            var table = $('#employeeTable').DataTable({
                searching: true,
                paging: true,
                lengthChange: true,
                responsive: true,
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> تصدير Excel',
                        className: 'btn btn-success'
                    },
                ],
                // تعديل هيكلة الـ DOM لوضع الأزرار ومربع البحث والتنقل
                dom: '<"row"<"col-md-6"l><"col-md-6 text-md-end"f>>' +
                     '<"row"<"col-md-12 text-md-start"B>>' + // الأزرار على اليسار (start) في RTL
                     '<"row"<"col-md-12"tr>>' +
                     '<"row"<"col-md-5"i><"col-md-7"p>>',
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.3/i18n/ar.json' // ملف اللغة العربية
                }
            });

            // نقل أزرار DataTables إلى الحاوية المخصصة لها
            table.buttons().container().appendTo('#dt-buttons-container');

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

            // General Animations (from previous Marsoom UI)
            (function initCalmAnimations(){
                const items = document.querySelectorAll('.ani');
                if (!items.length) return;

                const io = new IntersectionObserver((entries)=>{
                    entries.forEach(entry=>{
                        if (entry.isIntersecting){
                            entry.target.classList.add('in');
                            io.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.06, rootMargin: '0px 0px -8% 0px' });

                items.forEach(el=> io.observe(el));
            })();
        });
    </script>

</body>
</html>