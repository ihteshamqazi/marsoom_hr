<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>تطبيق الموظفين - تصميم فاخر</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@700&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --marsom-blue: #001f3f;
            --marsom-orange: #FF8C00;
            --text-light: #f0f0f0;
            --text-dark: #333;
            --glass-bg: rgba(2, 21, 46, 0.4); /* خلفية زجاجية أغمق */
            --glass-border: rgba(255, 255, 255, 0.15);
            --glow-orange: 0 0 15px rgba(255, 140, 0, 0.6);
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, var(--marsom-blue) 0%, #34495e 50%, var(--marsom-orange) 100%);
            background-size: 400% 400%;
            animation: gradientAnimation 20s ease infinite;
            color: var(--text-light);
            margin: 0;
            padding-bottom: 75px; /* مساحة للشريط السفلي */
            overflow-x: hidden;
        }

        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Particle Background Animation */
        .particles {
            position: fixed; width: 100%; height: 100%; top: 0; left: 0;
            overflow: hidden; z-index: -1;
        }
        .particle {
            position: absolute; background: rgba(255, 140, 0, 0.1);
            clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
            animation: float 25s infinite ease-in-out; opacity: 0; filter: blur(2px);
        }
        .particle:nth-child(even) { background: rgba(0, 31, 63, 0.1); }
        .particle:nth-child(1) { width: 40px; height: 40px; left: 10%; top: 20%; animation-duration: 18s; animation-delay: 0s; }
        .particle:nth-child(2) { width: 70px; height: 70px; left: 25%; top: 50%; animation-duration: 22s; animation-delay: 2s; }
        .particle:nth-child(3) { width: 55px; height: 55px; left: 40%; top: 10%; animation-duration: 25s; animation-delay: 5s; }
        .particle:nth-child(4) { width: 80px; height: 80px; left: 60%; top: 70%; animation-duration: 20s; animation-delay: 8s; }
        .particle:nth-child(5) { width: 60px; height: 60px; left: 80%; top: 30%; animation-duration: 23s; animation-delay: 10s; }
        @keyframes float {
            0% { transform: translateY(0) rotate(0deg); opacity: 0; } 20% { opacity: 1; } 80% { opacity: 1; }
            100% { transform: translateY(-100vh) rotate(360deg); opacity: 0; }
        }

        .container-fluid { max-width: 500px; padding: 0; }

        h1, h2, h3, h4, h5, h6 { font-family: 'El Messiri', sans-serif; text-shadow: 0 2px 4px rgba(0,0,0,0.5); }

        /* Screen Styling */
        .screen {
            display: none; padding: 15px; min-height: calc(100vh - 75px);
            opacity: 0; transform: translateY(15px);
            transition: opacity 0.5s ease-out, transform 0.5s ease-out;
        }
        .screen.active { display: block; opacity: 1; transform: translateY(0); }

        /* Glassmorphism Effect for Cards & Modals */
        .glass-card, .modal-content, .offcanvas, .request-box, .record-card, .summary-box, .leave-period-card, .summary-item-card, .monthly-day-status-item {
            background: var(--glass-bg);
            backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }
        .request-box, .record-card, .leave-period-card { padding: 15px; margin-bottom: 15px; }

        /* Bottom Navigation Bar */
        .bottom-nav {
            background: rgba(0, 16, 38, 0.5); backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px); border-top: 1px solid var(--glass-border);
            padding: 8px 0; border-top-left-radius: 25px; border-top-right-radius: 25px;
        }
        .bottom-nav .nav-link {
            color: var(--text-light); opacity: 0.7; flex-grow: 1; text-align: center;
            padding: 5px 0; display: flex; flex-direction: column; align-items: center; justify-content: center;
            font-size: 0.8rem; transition: all 0.3s ease;
        }
        .bottom-nav .nav-link i { font-size: 1.3rem; margin-bottom: 5px; }
        .bottom-nav .nav-link.active {
            color: var(--marsom-orange); opacity: 1; transform: translateY(-5px);
            text-shadow: var(--glow-orange);
        }
        .bottom-nav .nav-link:hover { color: var(--marsom-orange); }

        /* Home Screen: Attendance Box */
        .attendance-box {
            background: linear-gradient(135deg, var(--marsom-blue), #1f3a5a);
            color: #fff; padding: 20px; border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3); text-align: center;
            position: relative; overflow: hidden; margin-bottom: 20px;
        }
        .attendance-box .time-counter { font-size: 2.8rem; font-weight: 700; }
        .attendance-box .btn {
            background: var(--marsom-orange); color: #fff; border: none; padding: 10px 30px;
            border-radius: 50px; font-weight: 700; transition: all 0.3s ease;
            box-shadow: var(--glow-orange);
        }
        .attendance-box .btn:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(255, 140, 0, 0.7); }

        /* Home Screen: Square Buttons */
        .square-btn {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            padding: 15px; border-radius: 20px; text-decoration: none;
            color: var(--text-light); height: 120px; width: 100%;
            background: var(--glass-bg); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border); transition: all 0.3s ease;
        }
        .square-btn:hover { background: rgba(2, 21, 46, 0.7); transform: translateY(-5px); border-color: var(--marsom-orange); }
        .square-btn i { font-size: 2rem; margin-bottom: 10px; color: var(--marsom-orange); }
        
        /* General buttons */
        .btn-primary-themed {
             background: var(--marsom-orange); color: white; font-weight: bold; border:0;
             box-shadow: var(--glow-orange); border-radius: 10px;
        }
        .btn-primary-themed:hover {
            background-color: #e0882f;
            box-shadow: 0 5px 15px rgba(255, 140, 0, 0.7);
        }

        /* Floating Action Button (FAB) */
        .fab-button {
            position: fixed; bottom: 95px; left: 20px; background: var(--marsom-orange);
            color: white; border-radius: 50%; width: 55px; height: 55px;
            display: flex; align-items: center; justify-content: center; font-size: 1.5rem;
            box-shadow: var(--glow-orange); transition: all 0.3s ease; z-index: 10; border: none;
        }
        .fab-button:hover { transform: scale(1.1) rotate(90deg); }

        /* Modals & Offcanvas Styling */
        .modal-header, .modal-footer, .offcanvas-header { border: none; }
        .modal-content, .offcanvas { color: var(--text-light); }
        .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }
        .form-control, .form-select {
            background-color: rgba(0,0,0,0.2); border: 1px solid var(--glass-border);
            color: var(--text-light); border-radius: 10px;
        }
        .form-control::placeholder { color: rgba(255,255,255,0.5); }
        .form-control:focus, .form-select:focus {
            background-color: rgba(0,0,0,0.3); color: var(--text-light);
            border-color: var(--marsom-orange); box-shadow: 0 0 0 0.25rem rgba(255, 140, 0, 0.5);
        }
        option { background-color: var(--marsom-blue); }

        /* Attendance Screen */
        .calendar-container {
            background: none; border-radius: 20px; box-shadow: none; padding: 0; margin-bottom: 20px;
        }
        .calendar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; font-weight: 700; font-size: 1.2rem; }
        .calendar-grid, .weekly-calendar { display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px; text-align: center; }
        .calendar-day-name { font-size: 0.8rem; opacity: 0.7; margin-bottom: 5px; }
        .calendar-day, .weekly-day {
            padding: 10px 5px; border-radius: 10px; cursor: pointer;
            background: rgba(0,0,0,0.2); transition: all 0.2s ease;
        }
        .calendar-day.selected, .weekly-day.selected {
            background: var(--marsom-orange); color: #fff; transform: scale(1.1); box-shadow: var(--glow-orange);
        }
        .calendar-day.current-day { border: 2px solid var(--marsom-orange); }
        .weekly-day.holiday { background: rgba(255, 140, 0, 0.2); }
        .attendance-tabs .nav-link, .requests-tabs .nav-link {
            background-color: var(--glass-bg); border-radius: 10px; color: var(--text-light); margin: 0 5px;
            font-weight: 700; border: 1px solid transparent;
        }
        .attendance-tabs .nav-link.active, .requests-tabs .nav-link.active {
            background-color: var(--marsom-orange); box-shadow: var(--glow-orange); color: #fff;
            border-color: transparent;
        }
        .attendance-tabs .nav-link:hover, .requests-tabs .nav-link:hover {
            border-color: var(--marsom-orange);
        }
        .nav-pills { gap: 10px; }


        /* Profile Screen */
        #profileScreen .list-group-item {
            background-color: transparent; border-color: var(--glass-border); color: var(--text-light);
            padding: 1rem; border-radius: 15px !important; margin-bottom: 10px;
            cursor: pointer;
        }
        #profileScreen .list-group-item.toggle-btn {
            background-color: rgba(0,0,0,0.2); font-weight: bold;
        }
        .profile-menu { max-height: 0; overflow: hidden; opacity: 0; transition: all 0.4s ease-out; }
        .profile-menu.show { max-height: 500px; opacity: 1; }

        .profile-details-screen {
            position: fixed; top: 0; right: 0; width: 100%; height: 100%;
            background: linear-gradient(135deg, var(--marsom-blue) 0%, #34495e 100%);
            z-index: 1050; padding: 15px; display: none; overflow-y: auto;
        }
        
        .info-card {
            background: var(--glass-bg);
            backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 15px;
            color: var(--text-light);
        }
        .info-item {
            display: flex; justify-content: space-between; align-items: center;
            padding: 10px 0; border-bottom: 1px solid var(--glass-border);
        }
        .info-item:last-child { border-bottom: none; }
        .info-item .label { opacity: 0.7; }

        /* Report Screen */
        .report-summary-box {
            background: var(--glass-bg);
            padding: 20px;
            border-radius: 20px;
            border: 1px solid var(--glass-border);
        }
        .attendance-summary-bar-container {
            background-color: rgba(0,0,0,0.3); border-radius: 10px; height: 25px;
            overflow: hidden; display: flex;
        }
        .attendance-summary-bar-segment {
            height: 100%; display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 0.7rem; font-weight: bold;
            transition: width 0.5s ease-out;
        }
        .segment-present { background-color: #28a745; }
        .segment-incomplete { background-color: #fd7e14; }
        .segment-absent { background-color: #dc3545; }
        .segment-unrequired { background-color: #ffc107; }
        .segment-rest { background-color: #6c757d; }
        .segment-leave { background-color: #0d6efd; }
    </style>
</head>
<body class="d-flex flex-column h-100">
    <div class="particles">
        <div class="particle"></div><div class="particle"></div><div class="particle"></div>
        <div class="particle"></div><div class="particle"></div>
    </div>
    
    <div class="container-fluid flex-grow-1">
        <div id="homeScreen" class="screen active">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                     <h2 class="m-0" style="font-size: 2.2rem;">أهلاً، صالح!</h2>
                </div>
                 <div>
                    <button class="btn btn-outline-light rounded-pill px-3 py-2" onclick="showAttendanceScreen()">
                        <i class="fas fa-calendar-alt me-2"></i>
                        <span id="currentDate"></span>
                    </button>
                </div>
            </div>

            <div class="attendance-box">
                <div class="location-info">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>الرياض</span>
                </div>
                <div class="time-counter" id="workTimeCounter">00:00</div>
                <div class="remaining-time">من أصل 9 ساعات</div>
                <button class="btn" id="attendanceToggleButton">تسجيل الحضور</button>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-4">
                    <a href="#" class="square-btn" data-bs-toggle="modal" data-bs-target="#correctAttendanceModal">
                        <i class="fas fa-edit"></i>
                        <span>تصحيح حضور</span>
                    </a>
                </div>
                <div class="col-4">
                    <a href="#" class="square-btn" data-bs-toggle="modal" data-bs-target="#leaveRequestModal">
                        <i class="fas fa-plane-departure"></i>
                        <span>طلب إجازة</span>
                    </a>
                </div>
                <div class="col-4">
                    <a href="#" class="square-btn" data-bs-toggle="modal" data-bs-target="#overtimeRequestModal">
                        <i class="fas fa-hourglass-half"></i>
                        <span>طلب عمل إضافي</span>
                    </a>
                </div>
            </div>

            <div class="glass-card p-3">
                <h5 class="mb-3">طلباتك الحالية/المعلقة</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item bg-transparent text-light d-flex justify-content-between align-items-center border-secondary px-0">
                        <div>
                            <h6 class="mb-1">طلب إجازة سنوية</h6>
                            <small>حالة: <span class="badge bg-warning">معلق</span></small>
                        </div>
                        <span>2025/9/1 - 2025/9/5</span>
                    </li>
                    <li class="list-group-item bg-transparent text-light d-flex justify-content-between align-items-center border-0 px-0">
                        <div>
                            <h6 class="mb-1">طلب عمل إضافي</h6>
                            <small>حالة: <span class="badge bg-success">موافق عليه</span></small>
                        </div>
                        <span>+3 ساعات (2025/8/15)</span>
                    </li>
                </ul>
            </div>
        </div>

        <div id="attendanceScreen" class="screen">
            <div class="attendance-header d-flex justify-content-between align-items-center mb-4">
                <button class="btn btn-link text-light" data-bs-toggle="offcanvas" data-bs-target="#attendanceOptionsOffcanvas" aria-controls="attendanceOptionsOffcanvas">
                    <i class="fas fa-bars fa-lg"></i>
                </button>
                <h2 class="m-0">الحضور</h2>
                <button class="btn btn-link text-light" onclick="showReportScreen()">
                    <i class="fas fa-chart-line fa-lg"></i>
                </button>
            </div>

            <div id="dayViewContent" class="view-content active">
                <div class="calendar-container">
                    <div class="calendar-header">
                        <button class="btn btn-link p-0 text-light" id="prevMonth"><i class="fas fa-chevron-right"></i></button>
                        <span id="currentMonthYear"></span>
                        <button class="btn btn-link p-0 text-light" id="nextMonth"><i class="fas fa-chevron-left"></i></button>
                    </div>
                    <div class="calendar-grid"></div>
                </div>
                <ul class="nav nav-pills nav-justified attendance-tabs mb-4" id="attendanceTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="records-tab" data-bs-toggle="tab" data-bs-target="#recordsContent" type="button" role="tab">السجلات</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="leaves-tab" data-bs-toggle="tab" data-bs-target="#leavesContent" type="button" role="tab">إجازاتي</button>
                    </li>
                </ul>
                <div class="tab-content" id="attendanceTabContent">
                    <div class="tab-pane fade show active" id="recordsContent" role="tabpanel">
                        <div class="glass-card p-3 mb-3 text-center">
                             <h5 class="mb-3">حالة الحضور ليوم <span id="selectedDateRecord"></span></h5>
                             <p class="fw-bold fs-4 m-0" id="todayAttendanceStatus">--</p>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="leavesContent" role="tabpanel">
                         <h5 class="mb-3">طلبات الإجازات لشهر <span id="leavesMonthDisplay"></span></h5>
                         <div class="list-group glass-card p-2" id="leavesList"></div>
                    </div>
                </div>
            </div>
            
            <div id="weekViewContent" class="view-content" style="display: none;">
                </div>
            
            <div id="monthViewContent" class="view-content" style="display: none;">
                </div>
        </div>

        <div id="ordersScreen" class="screen">
            <h2 class="text-center mb-4">الطلبات</h2>
            <ul class="nav nav-pills nav-justified requests-tabs mb-4" id="requestsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="my-requests-tab" data-bs-toggle="tab" data-bs-target="#myRequestsContent" type="button" role="tab">طلباتي</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="employee-requests-tab" data-bs-toggle="tab" data-bs-target="#employeeRequestsContent" type="button" role="tab">طلبات الموظفين</button>
                </li>
            </ul>
            <div class="tab-content" id="requestsTabContent">
                <div class="tab-pane fade show active" id="myRequestsContent" role="tabpanel">
                    <div class="request-box">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold">طلب تصحيح بصمة</span>
                            <span class="badge bg-success">مؤكد</span>
                        </div>
                        <p class="small opacity-75 mb-1"><i class="fas fa-calendar-alt me-2"></i> تاريخ الطلب: الأربعاء، 13 أغسطس، 2025</p>
                        <p class="small opacity-75 m-0"><i class="fas fa-hashtag me-2"></i> رقم الطلب: 14843</p>
                    </div>
                </div>
                <div class="tab-pane fade" id="employeeRequestsContent" role="tabpanel">
                     <div class="request-box">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold">طلب تصحيح بصمة - أحمد محمد</span>
                            <span class="badge bg-warning">بالانتظار</span>
                        </div>
                        <p class="small opacity-75 m-0"><i class="fas fa-user me-2"></i> الموظف: أحمد محمد</p>
                        <div class="d-flex gap-2 mt-3">
                            <button class="btn btn-success flex-grow-1">موافق</button>
                            <button class="btn btn-danger flex-grow-1">رفض</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="profileScreen" class="screen">
            <div class="profile-details-screen" id="profileDetailsScreen">
                <div class="d-flex align-items-center mb-4">
                    <button class="btn btn-link text-light" onclick="hideProfileDetails()"><i class="fas fa-arrow-right fa-lg"></i></button>
                    <h4 id="profileDetailsTitle" class="flex-grow-1 text-center m-0"></h4>
                </div>
                <div id="profileDetailsContent"></div>
            </div>
            
            <h2 class="text-center mb-4">الملف الشخصي</h2>
            <ul class="list-group list-group-flush border-0">
                <li class="list-group-item toggle-btn d-flex justify-content-between align-items-center" onclick="toggleSubMenu('hrInfoMenu')">
                    <span><i class="fas fa-briefcase me-2"></i> معلومات الموارد البشرية</span>
                    <i class="fas fa-chevron-left"></i>
                </li>
                <div class="profile-menu list-group list-group-flush" id="hrInfoMenu">
                    <a class="list-group-item d-flex justify-content-between align-items-center" onclick="showProfileDetails('شخصي')">
                        <span><i class="fas fa-user-check me-2"></i> شخصي</span>
                        <i class="fas fa-chevron-left"></i>
                    </a>
                    <a class="list-group-item d-flex justify-content-between align-items-center" onclick="showProfileDetails('البيانات الوظيفية')">
                        <span><i class="fas fa-id-badge me-2"></i> البيانات الوظيفية</span>
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </div>
                <li class="list-group-item d-flex justify-content-between align-items-center" onclick="showProfileDetails('الإجازات')">
                    <span><i class="fas fa-plane-departure me-2"></i> الإجازات</span>
                    <i class="fas fa-chevron-left"></i>
                </li>
                 <li class="list-group-item text-danger d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-sign-out-alt me-2"></i> تسجيل الخروج</span>
                </li>
            </ul>
        </div>
        
        <div id="reportScreen" class="screen">
             <div class="attendance-header d-flex justify-content-between align-items-center mb-4">
                <button class="btn btn-link text-light" onclick="showAttendanceScreen()">
                    <i class="fas fa-arrow-right fa-lg"></i>
                </button>
                <h2 class="m-0">التقرير الشهري</h2>
                <div></div>
            </div>
            <div class="calendar-header mb-4">
                <button class="btn btn-link p-0 text-light" id="prevReportMonthBtn"><i class="fas fa-chevron-right"></i></button>
                <span id="currentReportMonthYear"></span>
                <button class="btn btn-link p-0 text-light" id="nextReportMonthBtn"><i class="fas fa-chevron-left"></i></button>
            </div>
            <div class="report-summary-box mb-3 text-center">
                <h5>مجموع ساعات العمل</h5>
                <p id="totalWorkHours" class="fs-3 fw-bold m-0" style="color:var(--marsom-orange)">--</p>
            </div>
            <h5 class="text-center mb-3">ملخص الحضور</h5>
            <div class="attendance-summary-bar-container mb-3"></div>
            <div class="attendance-legend d-flex flex-wrap justify-content-center gap-3 small"></div>
        </div>

    </div>

    <nav class="navbar fixed-bottom bottom-nav d-flex justify-content-around">
        <a class="nav-link active" href="#" data-screen-id="homeScreen">
            <i class="fas fa-home"></i>
            <span>الرئيسية</span>
        </a>
        <a class="nav-link" href="#" data-screen-id="attendanceScreen">
            <i class="fas fa-user-clock"></i>
            <span>الحضور</span>
        </a>
        <a class="nav-link" href="#" data-screen-id="ordersScreen">
            <i class="fas fa-clipboard-list"></i>
            <span>الطلبات</span>
        </a>
        <a class="nav-link" href="#" data-screen-id="profileScreen">
            <i class="fas fa-user"></i>
            <span>الملف الشخصي</span>
        </a>
    </nav>
    
    <button class="fab-button" data-bs-toggle="modal" data-bs-target="#newRequestModal">
        <i class="fas fa-plus"></i>
    </button>
    
    <div class="offcanvas offcanvas-start" tabindex="-1" id="attendanceOptionsOffcanvas">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">خيارات العرض</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="list-group list-group-flush">
                <li class="list-group-item list-group-item-action bg-transparent text-light border-secondary" onclick="setAttendanceView('day')" data-bs-dismiss="offcanvas">
                    <i class="fas fa-calendar-day me-2"></i> يوم
                </li>
                <li class="list-group-item list-group-item-action bg-transparent text-light border-secondary" onclick="setAttendanceView('week')" data-bs-dismiss="offcanvas">
                    <i class="fas fa-calendar-week me-2"></i> أسبوع
                </li>
                 <li class="list-group-item list-group-item-action bg-transparent text-light border-secondary" onclick="setAttendanceView('month')" data-bs-dismiss="offcanvas">
                    <i class="fas fa-calendar-alt me-2"></i> شهر
                </li>
            </ul>
        </div>
    </div>

    <div class="modal fade" id="newRequestModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">طلب جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>اختر نوع الطلب الذي تريد إنشاءه.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-primary-themed">إنشاء</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="correctAttendanceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تصحيح الحضور</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                     <form>
                        <div class="mb-3">
                            <label class="form-label">التاريخ</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">السبب</label>
                            <textarea class="form-control" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <button type="button" class="btn btn-primary-themed">إرسال</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="leaveRequestModal" tabindex="-1" aria-hidden="true">
         <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">طلب إجازة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                     <form>
                        <div class="mb-3">
                             <label class="form-label">نوع الإجازة</label>
                             <select class="form-select">
                                 <option>إجازة سنوية</option>
                                 <option>إجازة مرضية</option>
                                 <option>إجازة طارئة</option>
                             </select>
                        </div>
                         <div class="mb-3">
                            <label class="form-label">تاريخ البدء</label>
                            <input type="date" class="form-control">
                        </div>
                         <div class="mb-3">
                            <label class="form-label">تاريخ الانتهاء</label>
                            <input type="date" class="form-control">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <button type="button" class="btn btn-primary-themed">إرسال</button>
                </div>
            </div>
        </div>
    </div>

     <div class="modal fade" id="overtimeRequestModal" tabindex="-1" aria-hidden="true">
         <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">طلب عمل إضافي</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                     <form>
                         <div class="mb-3">
                            <label class="form-label">التاريخ</label>
                            <input type="date" class="form-control">
                        </div>
                         <div class="mb-3">
                            <label class="form-label">عدد الساعات</label>
                            <input type="number" class="form-control" min="1">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <button type="button" class="btn btn-primary-themed">إرسال</button>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const navLinks = document.querySelectorAll('.bottom-nav .nav-link');
            const screens = document.querySelectorAll('.screen');
            const currentDateSpan = document.getElementById('currentDate');
            const attendanceToggleButton = document.getElementById('attendanceToggleButton');
            const workTimeCounter = document.getElementById('workTimeCounter');

            let startTime = null;
            let timerInterval = null;

            function showScreen(screenId) {
                screens.forEach(screen => {
                    screen.classList.remove('active');
                });
                const targetScreen = document.getElementById(screenId);
                if (targetScreen) {
                    targetScreen.classList.add('active');
                }
            }

            function updateNavActiveState(activeLinkId) {
                navLinks.forEach(link => {
                    link.classList.remove('active');
                });
                const activeLink = document.querySelector(`[data-screen-id="${activeLinkId}"]`);
                if (activeLink) {
                    activeLink.classList.add('active');
                }
            }

            navLinks.forEach(link => {
                link.addEventListener('click', (event) => {
                    event.preventDefault();
                    const screenId = event.currentTarget.dataset.screenId;
                    showScreen(screenId);
                    updateNavActiveState(screenId);
                    if (screenId === 'attendanceScreen') {
                        setAttendanceView('day');
                    }
                });
            });

            window.showAttendanceScreen = function() {
                showScreen('attendanceScreen');
                updateNavActiveState('attendanceScreen');
                setAttendanceView('day');
            };

            window.showReportScreen = function() {
                showScreen('reportScreen');
                renderReportSummary(currentReportMonth);
            };

            function updateCurrentDate() {
                const today = new Date();
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', calendar: 'gregory' };
                currentDateSpan.textContent = today.toLocaleDateString('ar-SA', options);
            }

            function updateTimer() {
                if (startTime) {
                    const now = new Date();
                    const elapsedMilliseconds = now - startTime;
                    const elapsedSeconds = Math.floor(elapsedMilliseconds / 1000);
                    const hours = Math.floor(elapsedSeconds / 3600);
                    const minutes = Math.floor((elapsedSeconds % 3600) / 60);
                    const formattedTime = [
                        hours.toString().padStart(2, '0'),
                        minutes.toString().padStart(2, '0')
                    ].join(':');
                    workTimeCounter.textContent = formattedTime;
                } else {
                    workTimeCounter.textContent = '00:00';
                }
            }

            attendanceToggleButton.addEventListener('click', () => {
                if (startTime) {
                    clearInterval(timerInterval);
                    startTime = null;
                    attendanceToggleButton.textContent = 'تسجيل الحضور';
                } else {
                    startTime = new Date();
                    attendanceToggleButton.textContent = 'تسجيل الانصراف';
                    timerInterval = setInterval(updateTimer, 1000);
                }
            });

            // --- Attendance Screen Logic ---
            const dayViewContent = document.getElementById('dayViewContent');
            const calendarGrid = document.querySelector('#dayViewContent .calendar-grid');
            const currentMonthYearSpan = document.getElementById('currentMonthYear');
            const prevMonthBtn = document.getElementById('prevMonth');
            const nextMonthBtn = document.getElementById('nextMonth');
            const selectedDateRecordSpan = document.getElementById('selectedDateRecord');
            const todayAttendanceStatus = document.getElementById('todayAttendanceStatus');
            const leavesMonthDisplay = document.getElementById('leavesMonthDisplay');

            let currentDisplayDate = new Date();
            let selectedCalendarDay = null;

            const weekViewContent = document.getElementById('weekViewContent');
            const monthViewContent = document.getElementById('monthViewContent');
            
            const reportScreen = document.getElementById('reportScreen');
            const currentReportMonthYear = document.getElementById('currentReportMonthYear');
            const prevReportMonthBtn = document.getElementById('prevReportMonthBtn');
            const nextReportMonthBtn = document.getElementById('nextReportMonthBtn');
            const totalWorkHours = document.getElementById('totalWorkHours');
            const attendanceSummaryBarContainer = document.querySelector('.attendance-summary-bar-container');
            const attendanceLegend = document.querySelector('.attendance-legend');
            let currentReportMonth = new Date(2025, 7, 1);

            const attendanceData = {};
            const yearForDummyData = 2025;
            const monthForDummyData = 7; 
            for (let day = 1; day <= 31; day++) {
                const date = new Date(yearForDummyData, monthForDummyData, day);
                const dateString = `${yearForDummyData}-${(monthForDummyData + 1).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
                const dayOfWeek = date.getDay();
                if (dayOfWeek === 5 || dayOfWeek === 6) {
                    attendanceData[dateString] = { status: 'عطلة', isHoliday: true, holidayName: 'يوم عطلة', elapsed: '00:00', overtime: '00:00' };
                } else {
                    if (day >= 10 && day <= 12) {
                        attendanceData[dateString] = { status: 'اجازة', isLeave: true, leaveType: 'مرضية', elapsed: '00:00', overtime: '00:00' };
                    } else if (day === 7 || day === 17) {
                        attendanceData[dateString] = { status: 'غياب' };
                    } else if (day === 14) {
                        attendanceData[dateString] = { status: 'غير مكتمل' };
                    } else {
                        attendanceData[dateString] = { status: 'انصرف', elapsed: '09:00', overtime: '00:00'};
                    }
                }
            }

            const leavesData = {
                '8': [{ type: 'إجازة مرضية', from: '2025-08-10', to: '2025-08-12', status: 'معلق' }],
                '9': [{ type: 'إجازة سنوية', from: '2025-09-01', to: '2025-09-05', status: 'موافق عليه' }]
            };
            
            function renderMonthDayCalendar(month, year) {
                calendarGrid.innerHTML = ''; 
                const dayNames = ["س", "ح", "ن", "ث", "ر", "خ", "ج"];
                dayNames.forEach(name => {
                    const dayNameDiv = document.createElement('div');
                    dayNameDiv.className = 'calendar-day-name';
                    dayNameDiv.textContent = name;
                    calendarGrid.appendChild(dayNameDiv);
                });

                const firstDayOfMonth = new Date(year, month, 1);
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                const startDayOfWeek = firstDayOfMonth.getDay();
                
                const monthNames = ["يناير", "فبراير", "مارس", "أبريل", "مايو", "يونيو", "يوليو", "أغسطس", "سبتمبر", "أكتوبر", "نوفمبر", "ديسمبر"];
                currentMonthYearSpan.textContent = `${monthNames[month]} ${year}`;
                leavesMonthDisplay.textContent = `${monthNames[month]} ${year}`;

                for (let i = 0; i < startDayOfWeek; i++) {
                    const emptyDiv = document.createElement('div');
                    emptyDiv.classList.add('calendar-day', 'empty');
                    calendarGrid.appendChild(emptyDiv);
                }

                for (let day = 1; day <= daysInMonth; day++) {
                    const dayDiv = document.createElement('div');
                    dayDiv.classList.add('calendar-day');
                    dayDiv.textContent = day;
                    const dateString = `${year}-${(month + 1).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
                    
                    if (new Date().toDateString() === new Date(year, month, day).toDateString()) {
                       dayDiv.classList.add('current-day');
                       if(!selectedCalendarDay) {
                           dayDiv.classList.add('selected');
                           selectedCalendarDay = dayDiv;
                           displayAttendanceRecord(dateString);
                       }
                    }

                    dayDiv.addEventListener('click', () => {
                        if (selectedCalendarDay) {
                            selectedCalendarDay.classList.remove('selected');
                        }
                        dayDiv.classList.add('selected');
                        selectedCalendarDay = dayDiv;
                        displayAttendanceRecord(dateString);
                    });
                    calendarGrid.appendChild(dayDiv);
                }
                renderDayViewLeaves(month);
            }

            function displayAttendanceRecord(dateString) {
                selectedDateRecordSpan.textContent = new Date(dateString).toLocaleDateString('ar-SA', { day: 'numeric', month: 'long' });
                const record = attendanceData[dateString] || {};
                todayAttendanceStatus.textContent = record.status || 'لا يوجد بيانات';
            }

            function renderDayViewLeaves(month) {
                const leavesList = document.getElementById('leavesList');
                leavesList.innerHTML = '';
                const monthLeaves = leavesData[month + 1] || [];

                if (monthLeaves.length === 0) {
                    leavesList.innerHTML = `<p class="text-center opacity-75">لا توجد إجازات لهذا الشهر.</p>`;
                    return;
                }
                monthLeaves.forEach(leave => {
                     const leaveItem = document.createElement('div');
                     leaveItem.className = 'list-group-item bg-transparent text-light border-secondary';
                     leaveItem.innerHTML = `<h6>${leave.type}</h6><small>${leave.from} - ${leave.to}</small>`;
                     leavesList.appendChild(leaveItem);
                });
            }

            prevMonthBtn.addEventListener('click', () => {
                currentDisplayDate.setMonth(currentDisplayDate.getMonth() - 1);
                renderMonthDayCalendar(currentDisplayDate.getMonth(), currentDisplayDate.getFullYear());
            });

            nextMonthBtn.addEventListener('click', () => {
                currentDisplayDate.setMonth(currentDisplayDate.getMonth() + 1);
                renderMonthDayCalendar(currentDisplayDate.getMonth(), currentDisplayDate.getFullYear());
            });

            window.setAttendanceView = function(viewType) {
                dayViewContent.style.display = 'none';
                weekViewContent.style.display = 'none';
                monthViewContent.style.display = 'none';
                if (viewType === 'day') {
                    dayViewContent.style.display = 'block';
                    renderMonthDayCalendar(currentDisplayDate.getMonth(), currentDisplayDate.getFullYear());
                } else if (viewType === 'week') {
                    weekViewContent.style.display = 'block';
                } else if (viewType === 'month') {
                    monthViewContent.style.display = 'block';
                }
            };

            // --- Report Screen Logic ---
            function renderReportSummary(date) {
                const year = date.getFullYear();
                const month = date.getMonth();
                const monthNames = ["يناير", "فبراير", "مارس", "أبريل", "مايو", "يونيو", "يوليو", "أغسطس", "سبتمبر", "أكتوبر", "نوفمبر", "ديسمبر"];
                currentReportMonthYear.textContent = `${monthNames[month]} ${year}`;

                let totalMinutes = 0, daysPresent = 0, daysIncomplete = 0, daysAbsent = 0, daysRest = 0, daysLeave = 0;
                const daysInMonth = new Date(year, month + 1, 0).getDate();

                for (let day = 1; day <= daysInMonth; day++) {
                    const dateString = `${year}-${(month + 1).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
                    const record = attendanceData[dateString];
                    if (record) {
                        if (record.status === 'انصرف') {
                            daysPresent++;
                            totalMinutes += 9 * 60; // 9 hours
                        } else if (record.status === 'غير مكتمل') daysIncomplete++;
                        else if (record.status === 'غياب') daysAbsent++;
                        else if (record.isHoliday) daysRest++;
                        else if (record.isLeave) daysLeave++;
                    }
                }
                
                totalWorkHours.textContent = `${Math.floor(totalMinutes / 60)} ساعة و ${totalMinutes % 60} دقيقة`;
                
                const legendData = [
                    {label: 'حضور', count: daysPresent, class: 'segment-present'},
                    {label: 'غير مكتمل', count: daysIncomplete, class: 'segment-incomplete'},
                    {label: 'غياب', count: daysAbsent, class: 'segment-absent'},
                    {label: 'راحة', count: daysRest, class: 'segment-rest'},
                    {label: 'إجازة', count: daysLeave, class: 'segment-leave'}
                ];
                
                attendanceSummaryBarContainer.innerHTML = '';
                attendanceLegend.innerHTML = '';
                const totalDaysForBar = daysPresent + daysIncomplete + daysAbsent + daysRest + daysLeave;
                if(totalDaysForBar === 0) return;

                legendData.forEach(item => {
                    if (item.count > 0) {
                        const width = (item.count / totalDaysForBar) * 100;
                        attendanceSummaryBarContainer.innerHTML += `<div class="attendance-summary-bar-segment ${item.class}" style="width: ${width}%;" title="${item.label}: ${item.count} يوم">${item.count}</div>`;
                        attendanceLegend.innerHTML += `<div class="legend-item d-flex align-items-center"><div class="rounded-sm me-2 ${item.class}" style="width:15px; height:15px;"></div>${item.label}</div>`;
                    }
                });
            }

            prevReportMonthBtn.addEventListener('click', () => {
                currentReportMonth.setMonth(currentReportMonth.getMonth() - 1);
                renderReportSummary(currentReportMonth);
            });
            nextReportMonthBtn.addEventListener('click', () => {
                currentReportMonth.setMonth(currentReportMonth.getMonth() + 1);
                renderReportSummary(currentReportMonth);
            });

            // --- Profile Screen Logic ---
            const profileDetailsScreen = document.getElementById('profileDetailsScreen');
            const profileDetailsTitle = document.getElementById('profileDetailsTitle');
            const profileDetailsContent = document.getElementById('profileDetailsContent');
            const profileData = {
                'شخصي': `<div class="info-card"><h6>المعلومات الشخصية</h6><div class="info-item"><span class="label">الاسم</span><span class="value">صالح سعيد</span></div></div>`,
                'البيانات الوظيفية': `<div class="info-card"><h6>البيانات الوظيفية</h6><div class="info-item"><span class="label">القسم</span><span class="value">الموارد البشرية</span></div></div>`,
                'الإجازات': `<div class="info-card"><h6>رصيد الإجازات</h6><div class="info-item"><span class="label">سنوية</span><span class="value">21 يوم</span></div></div>`
            };
            
            window.toggleSubMenu = function(menuId) {
                const menu = document.getElementById(menuId);
                menu.classList.toggle('show');
                const icon = menu.previousElementSibling.querySelector('i:last-child');
                icon.classList.toggle('fa-chevron-left');
                icon.classList.toggle('fa-chevron-down');
            }

            window.showProfileDetails = function(title) {
                if (profileData[title]) {
                    profileDetailsTitle.textContent = title;
                    profileDetailsContent.innerHTML = profileData[title];
                    profileDetailsScreen.style.display = 'block';
                }
            }

            window.hideProfileDetails = function() {
                profileDetailsScreen.style.display = 'none';
            }
            
            // --- Initial Load ---
            showScreen('homeScreen');
            updateNavActiveState('homeScreen');
            updateCurrentDate();
            updateTimer();
        });
    </script>
</body>
</html>