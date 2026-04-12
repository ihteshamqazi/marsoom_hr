<?php
    $username = html_escape($this->session->userdata('username') ?? 'guest');
    $name = html_escape($this->session->userdata('name') ?? 'Guest User');
    $is_hr_user = $is_hr_user ?? false;
    $leave_types = $leave_types ?? [];
    $balances = $balances ?? [];
    $public_holidays = $public_holidays ?? [];
     $year = $year ?? (int)date('Y');
    $month = $month ?? (int)date('n');
    $target_name = $name;
    $target_username = $username;
    $daysMap = $daysMap ?? [];
    $eventsByDay = $eventsByDay ?? [];
    $violationsByDay = $violationsByDay ?? [];
    $holidaysMap = $holidaysMap ?? [];
    $prevY = $prevY ?? date('Y', strtotime('-1 month'));
    $prevM = $prevM ?? date('n', strtotime('-1 month'));
    $nextY = $nextY ?? date('Y', strtotime('+1 month'));
    $nextM = $nextM ?? date('n', strtotime('+1 month'));
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
     
   
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>تطبيق الموظف</title>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.7/main.min.css" rel="stylesheet">
     <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="manifest" href="<?php echo base_url('assets/manifest.json'); ?>">
    <meta name="theme-color" content="#001f3f">
<script src='https://cdn.jsdelivr.net/npm/fullcalendar/index.global.min.js'></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --marsom-blue: #001f3f; --marsom-orange: #FF8C00; --text-light: #ffffff;
            --text-muted-light: rgba(255, 255, 255, 0.75); --glass-bg: rgba(255, 255, 255, 0.08);
            --glass-border: rgba(255, 255, 255, 0.2);
        }
        html, body { font-family: 'Tajawal', sans-serif; background: linear-gradient(135deg, var(--marsom-blue) 0%, #34495e 50%, var(--marsom-orange) 100%); background-attachment: fixed; color: var(--text-light); }
        .particles{position:fixed;inset:0;z-index:-1;overflow:hidden}
        .particle{position:absolute;background:rgba(255,140,0,.1);clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);animation:float 25s ease-in-out infinite;opacity:0;filter:blur(2px)}
        .particle:nth-child(even){background:rgba(0,31,63,.1)}
        .particle:nth-child(1){width:40px;height:40px;left:8%;top:20%;animation-duration:18s}
        .particle:nth-child(2){width:70px;height:70px;left:25%;top:50%;animation-duration:22s;animation-delay:2s}
        @keyframes grad{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        @keyframes float{0%{transform:translate(0,0) rotate(0);opacity:0}20%{opacity:1}80%{opacity:1}100%{transform:translate(50px,-110vh) rotate(360deg);opacity:0}}
        .app-container { padding-top: 1rem; }
        .nav-tabs { border-bottom: 1px solid var(--glass-border); }
        .nav-tabs .nav-link { color: var(--text-muted-light); font-family: 'El Messiri', sans-serif; font-weight: 700; border: none; border-bottom: 3px solid transparent; transition: all 0.3s ease; background: none; }
        .nav-tabs .nav-link.active { color: var(--marsom-orange); border-bottom-color: var(--marsom-orange); }
        .tab-pane { padding-top: 1rem; }
        .attendance-box { background: var(--glass-bg); backdrop-filter: blur(10px); border: 1px solid var(--glass-border); padding: 20px; border-radius: 1rem; text-align: center; }
        .attendance-box .time-counter { font-size: 2.8rem; font-weight: 700; }
        .request-btn-grid .btn { background: var(--glass-bg); backdrop-filter: blur(10px); border: 1px solid var(--glass-border); color:var(--text-light); display: flex; flex-direction: column; align-items: center; justify-content: center; height: 110px; font-weight: 600; transition: all .3s ease; }
        .request-btn-grid .btn:hover { background: rgba(255, 255, 255, 0.15); border-color: var(--marsom-orange); }
        .request-btn-grid .btn i { font-size: 1.8rem; margin-bottom: 8px; color: var(--marsom-orange); }
        .card-glass { background: var(--glass-bg); backdrop-filter: blur(10px); border: 1px solid var(--glass-border); border-radius: 1rem; color: var(--text-light); }
        .card-glass .card-header { background: rgba(255, 255, 255, 0.1); border-bottom-color: var(--glass-border); font-family: 'El Messiri', sans-serif; font-weight: 700; }
        .list-group-item { background: transparent; border-bottom: 1px solid var(--glass-border) !important; color: var(--text-light); }
        .list-group-item:last-child { border-bottom: none !important; }
        .list-group-item h6 { color: #fff; font-weight: 700; }
        .list-group-item .request-details { color: var(--text-muted-light); font-size: 0.85rem; }
        .modal-content { background: #1c2b3e; color: #fff; border: 1px solid var(--glass-border); border-radius: 1rem; }
        .modal-header, .modal-footer { border-color: var(--glass-border); }
        .form-control, .form-select { background-color: var(--glass-bg); border-color: var(--glass-border); color: #fff; }
        .form-control:focus, .form-select:focus { background-color: rgba(255,255,255,0.2); border-color: var(--marsom-orange); color: #fff; box-shadow: none; }
        .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }
        .alert-info { background-color: var(--glass-bg); border-color: var(--glass-border); color: var(--text-light); }
         .month-nav{display:flex;justify-content:space-between;align-items:center;gap:8px;margin-bottom:12px}
        .month-title{font-size:1.05rem;font-weight:700}
        .btn-nav{background:rgba(255,255,255,.12);border:1px solid var(--glass-border);color:#fff;border-radius:10px;padding:6px 10px}
        .legend{display:flex;flex-wrap:wrap;justify-content:center;gap:12px;margin-bottom:12px;font-size:.8rem}
        .legend .chip{display:inline-flex;align-items:center;gap:6px}
        .legend .dot{width:10px;height:10px;border-radius:50%}
        .calendar{width:100%;border-collapse:separate;border-spacing:5px}
        .calendar th{text-align:center;color:var(--text-muted-light);font-size:.85rem;padding-bottom:5px;}
        .day-cell{height:75px;border:1px solid var(--glass-border);border-radius:14px;background:rgba(255,255,255,.06);position:relative;padding:5px;cursor:pointer;transition:.2s}
        .day-cell:hover{transform:translateY(-2px);background:rgba(255,255,255,.12)}
        .day-num{position:absolute;top:5px;right:8px;font-weight:700;font-size:.9rem}
        .day-cell.today{outline:2px solid var(--marsom-orange)}
        .day-cell.selected{box-shadow:0 0 0 2px #fff inset}
        .event-dots{position:absolute;bottom:5px;left:5px;display:flex;gap:4px;align-items:center}
        .event-dots .e{width:7px;height:7px;border-radius:50%}
        .e.vac{background:var(--vac)} .e.half{background:var(--half)} .e.corr{background:var(--corr)}
        .day-cell.absent{background: rgba(220,53,69,.18) !important; border-color: rgba(220,53,69,.55) !important;}
        .day-cell.weekend{background: rgba(108,117,125,.15); border-color: rgba(108,117,125,.45);}
        .day-cell.public-holiday{background: rgba(52, 152, 219, 0.2) !important; border-color: rgba(52, 152, 219, 0.6) !important;}
        .details-card{margin-top:10px;background:rgba(0,0,0,.2);border-radius:14px;padding:12px;}
        /* Add these new styles inside your main <style> tag */
.month-nav .btn-nav { background: transparent; border: none; font-size: 1.2rem; color: var(--text-light); }
.calendar-grid-header { display: grid; grid-template-columns: repeat(7, 1fr); text-align: center; font-weight: 700; color: var(--text-muted-light); margin-bottom: 10px; }
.calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px; }
.day-box { height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 12px; font-weight: 700; font-size: 1.1rem; cursor: pointer; position: relative; transition: all .2s ease; }
.day-box.empty { background: none; cursor: default; }
.day-box.day-present { background-color: rgba(40, 167, 69, 0.2); border: 1px solid rgba(40, 167, 69, 0.5); }
.day-box.day-absent { background-color: rgba(220, 53, 69, 0.2); border: 1px solid rgba(220, 53, 69, 0.5); }
.day-box.day-weekend { background-color: rgba(108, 117, 125, 0.2); border: 1px solid rgba(108, 117, 125, 0.4); }
.day-box.day-leave { background-color: rgba(26, 188, 156, 0.2); border: 1px solid rgba(26, 188, 156, 0.5); }
.day-box.day-today { box-shadow: 0 0 0 2px var(--marsom-orange); }
.day-box.selected { transform: scale(1.05); box-shadow: 0 0 0 2px #fff; }
.day-box .event-dot { position: absolute; bottom: 8px; width: 6px; height: 6px; border-radius: 50%; }
.details-card-new { background: rgba(0,0,0,0.2); padding: 15px; border-radius: 1rem; }
.details-title { font-weight: 700; margin-bottom: 15px; }
.detail-item { display: flex; justify-content: space-between; align-items: center; padding: 10px; background-color: var(--glass-bg); border-radius: 8px; margin-bottom: 8px; }
.detail-item .label { font-weight: 500; color: var(--text-muted-light); }
.detail-item .value { font-weight: 700; font-family: monospace; font-size: 1.1rem; }
.value.positive { color: #28a745; }
.value.negative { color: #dc3545; }
/* Add these styles to your main <style> tag */
#requests-tab-pane .nav-pills .nav-link { background-color: rgba(255,255,255,0.1); color: var(--text-muted-light); }
#requests-tab-pane .nav-pills .nav-link.active { background-color: var(--marsom-orange); color: #fff; }
.request-card { background: var(--glass-bg); backdrop-filter: blur(10px); border: 1px solid var(--glass-border); border-radius: 1rem; color: var(--text-light); margin-bottom: 1rem; }
.request-card-header { background: rgba(255,255,255,0.1); padding: 0.75rem 1.25rem; display: flex; justify-content: space-between; align-items: center; }
.request-card-header h6 { margin: 0; font-family: 'El Messiri', sans-serif; }
.request-card-body { padding: 1.25rem; }
.request-card .detail-row { display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid var(--glass-border); }
.request-card .detail-row:last-child { border-bottom: none; }
.request-card .detail-label { color: var(--text-muted-light); }
.request-card .detail-value { font-weight: 700; }
.request-card-footer { padding: 0.75rem 1.25rem; background: rgba(0,0,0,0.2); display: flex; gap: 0.5rem; justify-content: flex-end; }
/* Add these new styles for the Profile Tab */
.content-card{background:var(--glass-bg);backdrop-filter:blur(10px);border:1px solid var(--glass-border);border-radius:15px;padding:25px;height:100%}
.profile-menu .menu-header{text-align:center;padding-bottom:20px;border-bottom:1px solid var(--glass-border);margin-bottom:15px}
.profile-menu .avatar{width:90px;height:90px;border-radius:50%;border:3px solid var(--marsom-orange);margin-bottom:10px}
.profile-menu h5{font-family:'El Messiri',sans-serif;font-weight:700;margin-bottom:2px;font-size:1.1rem; color: var(--text-light);}
.profile-menu p{font-size:.9rem;color:var(--text-muted-light)}
.profile-menu .list-group-item{background-color:transparent;border:none;color:var(--text-light);font-weight:700;cursor:pointer;transition:all .2s ease;padding:15px 10px;border-radius:8px;margin-bottom:5px}
.profile-menu .list-group-item:hover,.profile-menu .list-group-item.active{background-color:rgba(255,140,0,.15);color:var(--marsom-orange)}
.profile-menu .list-group-item i{width:25px;margin-left:10px}
.profile-menu .list-group-item.danger-item:hover{color:#dc3545;background-color:rgba(220,53,69,.1)}
#details-container{min-height:500px;padding:20px}
.content-title{font-family:'El Messiri',sans-serif;text-align:center;font-weight:700;color:var(--text-light);position:relative;padding-bottom:15px;margin-bottom:30px;font-size:1.8rem}
.content-title::after{content:'';position:absolute;width:80px;height:3px;background-color:var(--marsom-orange);bottom:0;left:50%;transform:translateX(-50%)}
.info-box{text-align:center;padding:1rem;margin-bottom:1rem; background: rgba(0,0,0,0.1); border-radius: .75rem;}
.info-box .info-value{font-size:1.1rem;font-weight:700;color:var(--text-light);margin-bottom:5px}
.info-box .info-label{font-size:.9rem;color:var(--text-muted-light);margin-bottom:0}
.pie-chart-container{position:relative;margin:1rem auto;height:250px;max-width:250px}
.status-badge{padding:4px 14px;border-radius:9999px;font-weight:600;font-size:.9rem}
.status-active{background-color:#d1fae5;color:#065f46}
.balance-card{text-align:center;padding:20px;border-radius:1rem;border-top:4px solid var(--marsom-orange); background-color: rgba(0,0,0,0.1);}
.balance-card .balance-title{font-family:'El Messiri',sans-serif;font-weight:700;font-size:1.1rem;color:var(--text-light); margin-bottom: 10px;}
.balance-card .balance-value{font-size:2rem;font-weight:800;color:var(--marsom-orange);line-height:1}
/* Add these new styles for the Profile Tab */
#profile-tab-pane { background-color: #f4f7f61f; border-radius: 20px; color: #111; padding: 1rem !important; }
.profile-header { text-align: center; margin-bottom: 1.5rem; }
.profile-header .avatar { width: 90px; height: 90px; border-radius: 50%; border: 4px solid #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 0.75rem; }
.profile-header h5 { font-weight: 700; color: #111; }
.profile-header p { color: #6B7280; font-size: 0.9rem; }
.profile-accordion .accordion-item { background-color: #ffffff7a; border: 1px solid #e5e7eb; border-radius: 0.75rem !important; margin-bottom: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
.profile-accordion .accordion-button { font-weight: 700; color: #111; background-color: #fff; border-radius: 0.75rem !important; box-shadow: none !important; }
.profile-accordion .accordion-header { background-color: #ffffff7a; }
.profile-accordion .accordion-button:not(.collapsed) { background-color: #f9fafb; }
.profile-accordion .accordion-button i { color: var(--primary-brand); margin-left: 1rem; }
.profile-accordion .accordion-body { padding: 1.25rem; }
.detail-list-item { display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #f3f4f6; }
.detail-list-item:last-child { border-bottom: none; }
.detail-list-item .label { color: #6B7280; }
.detail-list-item .value { font-weight: 600; color: #111; }
.pie-chart-container { position: relative; margin: 1rem auto; height: 250px; max-width: 250px; }
.balance-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.balance-box { background-color: #f9fafb; text-align: center; padding: 1rem; border-radius: 0.75rem; }
.balance-box .value { font-size: 2rem; font-weight: 800; color: var(--primary-brand); line-height: 1; }
.balance-box .label { font-size: 0.9rem; color: #6B7280; margin-top: 0.25rem; }
    </style>
</head>
<body>

<div class="particles"><div class="particle"></div><div class="particle"></div><div class="particle"></div></div>

<div class="container app-container">
    <ul class="nav nav-tabs nav-fill mb-3" id="myTab" role="tablist">
        <li class="nav-item" role="presentation"><button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button"><i class="fas fa-home me-1"></i> الرئيسية</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance-tab-pane" type="button"><i class="fas fa-user-clock me-1"></i> الحضور</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link" id="requests-tab" data-bs-toggle="tab" data-bs-target="#requests-tab-pane" type="button"><i class="fas fa-clipboard-list me-1"></i> الطلبات</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button"><i class="fas fa-user me-1"></i> ملفي</button></li>
    </ul>

    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel">
            <div class="pt-3">
                <h4 class="fw-bold"><?php echo $name; ?>، مرحباً بك!</h4>
                <p class="text-muted-light mb-4"><?php $formatter = new IntlDateFormatter('ar-SA@calendar=gregorian', IntlDateFormatter::FULL, IntlDateFormatter::NONE, 'Asia/Riyadh'); echo $formatter->format(time()); ?></p>

                <div class="attendance-box mb-4">
                    <div class="time-counter" id="workTimeCounter">--:--:--</div>
                    <div class="status-text mb-3" id="attendanceStatusText">جاري تحميل الحالة...</div>
                    <button class="btn bg-white text-primary fw-bold" id="attendanceToggleButton">
                        <i class="fas fa-map-marker-alt me-2"></i>تسجيل الحضور
                    </button>
                </div>

                <div class="row g-3 request-btn-grid my-4">
                    <div class="col-4"><button class="btn w-100" data-bs-toggle="modal" data-bs-target="#fingerprintModal"><i class="fas fa-fingerprint"></i><span>بصمة</span></button></div>
                    <div class="col-4"><button class="btn w-100" data-bs-toggle="modal" data-bs-target="#vacationModal"><i class="fas fa-umbrella-beach"></i><span>إجازة</span></button></div>
                    <div class="col-4"><button class="btn w-100" data-bs-toggle="modal" data-bs-target="#overtimeModal"><i class="fas fa-clock-rotate-left"></i><span>عمل إضافي</span></button></div>
                </div>

                <div class="card card-glass requests-list">
                    <div class="card-header">آخر 3 طلبات</div>
                    <ul class="list-group list-group-flush" id="lastRequestsContainer"></ul>
                </div>
            </div>
        </div>
       <div class="tab-pane fade" id="attendance-tab-pane" role="tabpanel">
    <div class="pt-2">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <button class="btn btn-nav" id="btnPrevMonth">السابق<i class="fas fa-chevron-left"></i></button>
            <h5 class="month-title mb-0" id="calendarMonthTitle"></h5>
            <button class="btn btn-nav" id="btnNextMonth">التالي<i class="fas fa-chevron-right"></i></button>
        </div>

        <div class="legend mb-4">
            <div class="chip"><span class="dot" style="background-color: #28a745;"></span> حاضر</div>
            <div class="chip"><span class="dot" style="background-color: #dc3545;"></span> غياب</div>
            <div class="chip"><span class="dot" style="background-color: #6c757d;"></span> ويكند</div>
            <div class="chip"><span class="dot" style="background-color: #1abc9c;"></span> إجازة</div>
        </div>

        <div class="calendar-grid-header">
            <div>ح</div><div>ن</div><div>ث</div><div>ر</div><div>خ</div><div>ج</div><div>س</div>
        </div>
        <div class="calendar-grid" id="calendarGrid">
            <div class="text-center p-5"><div class="spinner-border"></div></div>
        </div>
        
        <div class="details-card-new mt-4" id="detailsCard" style="display:none;">
            <h6 class="details-title" id="detailsDateTitle"></h6>
            <div id="dayDetailsContent">
                </div>
        </div>
    </div>
</div>
        <<div class="tab-pane fade" id="requests-tab-pane" role="tabpanel">
    <ul class="nav nav-pills nav-fill mb-4" id="requestsSubTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="my-requests-subtab" data-bs-toggle="tab" data-bs-target="#myRequestsPane" type="button">طلباتي</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="approvals-subtab" data-bs-toggle="tab" data-bs-target="#approvalsPane" type="button">الموافقات</button>
        </li>
    </ul>

    <div class="tab-content" id="requestsSubTabsContent">
        <div class="tab-pane fade show active" id="myRequestsPane" role="tabpanel">
            <div id="myRequestsContainer">
                <div class="text-center p-5"><div class="spinner-border"></div></div>
            </div>
        </div>
        <div class="tab-pane fade" id="approvalsPane" role="tabpanel">
            <div id="approvalsContainer">
                </div>
        </div>
    </div>
</div>
<div class="tab-pane fade" id="profile-tab-pane" role="tabpanel">
    <div class="profile-header">
        <img src="https://placehold.co/100x100/333/FFF?text=<?php echo substr($name, 0, 2); ?>" class="avatar" alt="Avatar">
        <h5><?php echo $name; ?></h5>
        <p>ID: <?php echo $username; ?></p>
    </div>

    <div class="accordion profile-accordion" id="profileAccordion">
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePersonal" aria-expanded="true" data-loader="personal">
                    <i class="fas fa-user-check fa-fw"></i> المعلومات الشخصية
                </button>
            </h2>
            <div id="collapsePersonal" class="accordion-collapse collapse show" data-bs-parent="#profileAccordion">
                <div class="accordion-body" id="personal-details-content">
                    </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseJob" aria-expanded="false" data-loader="job">
                    <i class="fas fa-id-badge fa-fw"></i> البيانات الوظيفية
                </button>
            </h2>
            <div id="collapseJob" class="accordion-collapse collapse" data-bs-parent="#profileAccordion">
                <div class="accordion-body" id="job-details-content"></div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFinancial" aria-expanded="false" data-loader="financial">
                    <i class="fas fa-money-check-alt fa-fw"></i> الراتب والتفاصيل المالية
                </button>
            </h2>
            <div id="collapseFinancial" class="accordion-collapse collapse" data-bs-parent="#profileAccordion">
                <div class="accordion-body" id="financial-details-content"></div>
            </div>
        </div>
        
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBalances" aria-expanded="false" data-loader="balances">
                    <i class="fas fa-calendar-alt fa-fw"></i> أرصدة الإجازات
                </button>
            </h2>
            <div id="collapseBalances" class="accordion-collapse collapse" data-bs-parent="#profileAccordion">
                <div class="accordion-body" id="balances-details-content"></div>
            </div>
        </div>
        
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseContract" aria-expanded="false" data-loader="contract">
                    <i class="fas fa-file-signature fa-fw"></i> العقود
                </button>
            </h2>
            <div id="collapseContract" class="accordion-collapse collapse" data-bs-parent="#profileAccordion">
                <div class="accordion-body" id="contract-details-content"></div>
            </div>
        </div>
    </div>
</div>
    </div>
</div>

<div class="modal fade" id="fingerprintModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="fingerprintForm" action="<?php echo site_url('users1/add_new_order'); ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="request_type" value="fingerprint"><input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="modal-header"><h5 class="modal-title">طلب تصحيح بصمة</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body"><div id="fpErrorAlert" class="alert alert-danger d-none"></div><div class="row g-3"><div class="col-12"><label class="form-label">تاريخ التصحيح</label><input type="date" name="fp[date]" class="form-control"></div><div class="col-12"><div class="border rounded p-3"><div class="fw-bold mb-2">الحضور</div><div class="row g-2"><div class="col-sm-5"><input type="time" class="form-control" name="fp[in_time]"></div><div class="col-sm-7"><input type="text" class="form-control" name="fp[in_note]" placeholder="ملاحظة"></div></div></div></div><div class="col-12"><div class="border rounded p-3"><div class="fw-bold mb-2">الانصراف</div><div class="row g-2"><div class="col-sm-5"><input type="time" class="form-control" name="fp[out_time]"></div><div class="col-sm-7"><input type="text" class="form-control" name="fp[out_note]" placeholder="ملاحظة"></div></div></div></div><div class="col-12"><label class="form-label">سبب التصحيح</label><select name="fp[reason]" class="form-select"><option value="">اختر السبب...</option><option>نسيان بصمة</option><option>مشكلة في التطبيق</option><option>اخرى</option></select></div><div class="col-12"><label class="form-label">تفاصيل السبب</label><input type="text" name="fp[details]" class="form-control"></div><div class="col-12"><label class="form-label">مرفق (اختياري)</label><input type="file" name="fp[file]" class="form-control"></div></div></div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button><button type="submit" class="btn btn-primary">إرسال</button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="vacationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="vacationForm" action="<?php echo site_url('users1/add_new_order'); ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="request_type" value="vacation"><input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="modal-header"><h5 class="modal-title">طلب إجازة</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div id="vacErrorAlert" class="alert alert-danger d-none"></div>
                    <div class="row g-3">
                        <div class="col-12"><label class="form-label">نوع الإجازة</label><select name="vac[main_type]" class="form-select" id="vacMainType"><?php foreach ($leave_types as $type): ?><option value="<?php echo htmlspecialchars($type['slug']); ?>"><?php echo htmlspecialchars($type['name_ar']); ?></option><?php endforeach; ?></select></div>
                        <div class="col-12"><div class="alert alert-info p-2 mb-0">رصيدك: <b id="vacBalanceDisplay">--</b> يوم</div></div>
                        <div class="col-12 d-none" id="vacationDurationContainer"><label class="form-label">مدة الإجازة</label><div class="d-flex gap-3"><div class="form-check"><input class="form-check-input" type="radio" name="vac[day_type]" id="vacDayTypeFull" value="full" checked><label class="form-check-label" for="vacDayTypeFull">يوم كامل (فترة)</label></div><div class="form-check"><input class="form-check-input" type="radio" name="vac[day_type]" id="vacDayTypeHalf" value="half"><label class="form-check-label" for="vacDayTypeHalf">نصف يوم</label></div></div></div>
                        <div class="row g-3" id="vacFullDayRange"><div class="col-6"><label class="form-label">من تاريخ</label><input type="date" name="vac[start]" class="form-control" id="vacFrom"></div><div class="col-6"><label class="form-label">إلى تاريخ</label><input type="date" name="vac[end]" class="form-control" id="vacTo"></div><div class="col-12"><div id="vacDaysMsg" class="fw-bold text-center text-info"></div><input type="hidden" name="vac[days_count]" id="vacDaysCount" value="0"></div></div>
                        <div class="row g-3 d-none" id="vacHalfDayFields"><div class="col-sm-6"><label class="form-label">في تاريخ</label><input type="date" name="vac[half_date]" class="form-control" id="vacHalfDate"></div><div class="col-sm-6"><label class="form-label">الفترة</label><select name="vac[half_period]" class="form-select" id="vacHalfPeriod"><option value="am">صباحي</option><option value="pm">مسائي</option></select></div></div>
                        <div class="col-12"><label class="form-label">السبب</label><input type="text" name="vac[reason]" class="form-control"></div>
                        <div class="col-12"><label class="form-label">مرفق <span id="attachmentRequired" class="text-danger d-none">*</span></label><input type="file" name="vac[file]" id="vacAttachment" class="form-control"></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button><button type="submit" class="btn btn-primary">إرسال</button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="overtimeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="overtimeForm" action="<?php echo site_url('users1/add_new_order'); ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="request_type" value="overtime">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="modal-header"><h5 class="modal-title">طلب عمل إضافي</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div id="otErrorAlert" class="alert alert-danger d-none"></div>
                    <div class="row g-3">
                        <div class="col-12"><label class="form-label">التاريخ</label><input type="date" name="ot[date]" class="form-control"></div>
                        <div class="col-12"><label class="form-label">عدد الساعات</label><input type="number" name="ot[hours]" class="form-control" min="0.25" step="0.25" placeholder="مثال: 1.5"></div>
                        <div class="col-12"><label class="form-label">هل مدفوع؟</label><select name="ot[paid]" class="form-select"><option value="1">نعم</option><option value="0">لا</option></select></div>
                        <div class="col-12"><label class="form-label">السبب</label><input type="text" name="ot[reason]" class="form-control"></div>
                        <div class="col-12"><label class="form-label">مرفق (اختياري)</label><input type="file" name="ot[file]" class="form-control"></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button><button type="submit" class="btn btn-primary">إرسال</button></div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="requestDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="requestDetailsModalLabel">تفاصيل الطلب</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="requestDetailsBody">
                <div class="text-center p-5"><div class="spinner-border"></div></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {

    const PUBLIC_HOLIDAYS = <?php echo json_encode($public_holidays ?? []); ?>;
    const employeeBalances = <?php echo json_encode($balances ?? []); ?>;
    const isHrUser = <?php echo json_encode($is_hr_user ?? false); ?>;
    const q = (selector) => document.querySelector(selector);
    let workTimerInterval = null;
    function startWorkTimer(startTime) {
        if (workTimerInterval) clearInterval(workTimerInterval); // Clear any existing timer

        const startTimestamp = new Date(startTime).getTime();
        const workTimeCounter = q('#workTimeCounter');

        workTimerInterval = setInterval(() => {
            const now = new Date().getTime();
            const elapsedMilliseconds = now - startTimestamp;
            const elapsedSeconds = Math.floor(elapsedMilliseconds / 1000);
            
            const hours = Math.floor(elapsedSeconds / 3600);
            const minutes = Math.floor((elapsedSeconds % 3600) / 60);
            const seconds = elapsedSeconds % 60;

            workTimeCounter.textContent = [
                String(hours).padStart(2, '0'),
                String(minutes).padStart(2, '0'),
                String(seconds).padStart(2, '0')
            ].join(':');
        }, 1000);
    }

    function updateAttendanceBox() {
        fetch("<?php echo site_url('users2/get_today_attendance_summary'); ?>")
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success' && data.data) {
                    const p = data.data;
                    const statusText = q('#attendanceStatusText');
                    const toggleButton = q('#attendanceToggleButton');

                    if (p.firstCheckIn && p.lastCheckOut && p.firstCheckIn !== p.lastCheckOut) {
                        // Day is complete
                        if (workTimerInterval) clearInterval(workTimerInterval);
                        q('#workTimeCounter').textContent = p.workDuration || '00:00:00';
                        statusText.textContent = "اكتمل الدوام لهذا اليوم";
                        toggleButton.textContent = "تم تسجيل الدوام";
                        toggleButton.disabled = true;
                    } else if (p.firstCheckIn) {
                        // Currently on duty, start the live timer
                        statusText.textContent = `في الدوام (الحضور: ${p.firstCheckIn.split(' ')[1].substring(0, 5)})`;
                        toggleButton.textContent = "تسجيل الانصراف";
                        startWorkTimer(p.firstCheckIn);
                    } else {
                        // Not checked in yet
                        q('#workTimeCounter').textContent = '00:00:00';
                        statusText.textContent = "لم يتم تسجيل الحضور بعد";
                        toggleButton.textContent = "تسجيل الحضور";
                    }
                }
            }).catch(console.error);
    }

        q('#attendanceToggleButton').addEventListener('click', async function() {
        const button = this;
        button.disabled = true;
        button.innerHTML = `<span class="spinner-border spinner-border-sm"></span> جار التحقق...`;

        try {
            const branchesResponse = await fetch("<?php echo site_url('users2/get_branch_locations'); ?>");
            if (!branchesResponse.ok) throw new Error('لا يمكن تحميل مواقع الفروع.');
            const branches = await branchesResponse.json();

            if (!branches || branches.length === 0) {
                throw new Error('لا توجد مواقع فروع معرفة بالنظام.');
            }

            const position = await new Promise((resolve, reject) => {
                if (!navigator.geolocation) {
                    reject(new Error('خدمة تحديد المواقع غير مدعومة.'));
                }
                navigator.geolocation.getCurrentPosition(resolve, reject, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
            });
            
            const userLat = position.coords.latitude;
            const userLon = position.coords.longitude;
            let closestBranch = null;
            let closestDistance = Infinity;

            for (const branch of branches) {
                const distance = getDistance(userLat, userLon, branch.latitude, branch.longitude);
                if (distance < closestDistance) {
                    closestDistance = distance;
                    closestBranch = branch;
                }
            }
            
            if (closestDistance <= 100) { // Set radius to 100 meters
                const now = new Date();
                const pad = (num) => String(num).padStart(2, '0');
                const punch_time = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())} ${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
                const punch_state = now.getHours() < 14 ? 'Check In' : 'Check Out'; // Logic assumes afternoon is checkout

                const response = await fetch("<?php echo site_url('users2/attendance'); ?>", {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `punch_time=${punch_time}&punch_state=${punch_state}&area_alias=${closestBranch.branch_name}`
                });
                const result = await response.json();

                if (result.status === 'success') {
                    alert(`تم تسجيل البصمة بنجاح في: ${closestBranch.branch_name}`);
                    updateAttendanceBox(); // Refresh the box to start the timer or show completion
                } else {
                    throw new Error(result.message || 'فشل الخادم في تسجيل البصمة.');
                }

            } else {
                throw new Error(`أنت خارج النطاق المسموح. أقرب فرع (${closestBranch.branch_name}) يبعد ${Math.round(closestDistance)} متر.`);
            }

        } catch (error) {
            alert('خطأ: ' + error.message);
            console.error('Attendance Error:', error);
        } finally {
            button.disabled = false;
            button.innerHTML = `<i class="fas fa-map-marker-alt me-2"></i>تسجيل الحضور`;
        }
    });

    function getDistance(lat1, lon1, lat2, lon2) {
        const R = 6371e3; // metres
        const φ1 = lat1 * Math.PI/180;
        const φ2 = lat2 * Math.PI/180;
        const Δφ = (lat2-lat1) * Math.PI/180;
        const Δλ = (lon2-lon1) * Math.PI/180;
        const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) + Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ/2) * Math.sin(Δλ/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c; // in metres
    }
    function loadLastThreeRequests() {
        const container = q('#lastRequestsContainer'); if (!container) return;
        container.innerHTML = `<li class="list-group-item text-center p-3"><div class="spinner-border spinner-border-sm"></div></li>`;
        fetch("<?php echo site_url('users2/get_last_requests'); ?>")
            .then(res => res.json()).then(data => {
                container.innerHTML = '';
                if (data.status === 'success' && data.data.length > 0) {
                    data.data.forEach(req => {
                        let sClass = 'bg-secondary', sText = 'غير معروف', dtls = '';
                        switch (String(req.status)) { case '0': sClass = 'bg-warning text-dark'; sText = 'قيد المراجعة'; break; case '2': sClass = 'bg-success'; sText = 'معتمد'; break; case '3': sClass = 'bg-danger'; sText = 'مرفوض'; break; }
                        switch (String(req.type)) { case '2': dtls = `تصحيح ليوم: ${req.correction_date || ''}`; break; case '5': dtls = `إجازة من ${req.vac_start || ''} إلى ${req.vac_end || ''}`; break; case '3': dtls = `عمل إضافي - ${req.ot_hours || 'N/A'} ساعات`; break; default: dtls = req.note || ''; }
                        container.innerHTML += `<li class="list-group-item d-flex justify-content-between align-items-center"><div><h6 class="mb-1">${req.order_name || 'طلب'}</h6><small class="request-details text-muted">${dtls}</small></div><span class="badge ${sClass}">${sText}</span></li>`;
                    });
                } else { container.innerHTML = '<li class="list-group-item text-center text-muted">لا توجد طلبات حديثة.</li>'; }
            }).catch(console.error);
    }

    // --- Form Validation Logic ---
    function setupFormValidation(formId, validationFunction, errorAlertId) {
        const form = document.getElementById(formId); if (!form) return;
        const submitBtn = form.querySelector('button[type="submit"]');
        const errorAlertBox = document.getElementById(errorAlertId);
        function showErrors(errors) { if (!errorAlertBox) return; if (errors.length > 0) { errorAlertBox.innerHTML = '<ul><li>' + errors.join('</li><li>') + '</li></ul>'; errorAlertBox.classList.remove('d-none'); } else { errorAlertBox.innerHTML = ''; errorAlertBox.classList.add('d-none'); } }
        function validate() { const errors = []; validationFunction(errors); showErrors(errors); if (submitBtn) submitBtn.disabled = errors.length > 0; return errors.length === 0; }
        form.addEventListener('input', validate); form.addEventListener('change', validate);
        form.addEventListener('submit', (e) => { if (!validate()) e.preventDefault(); });
        validate();
    }
    function validateFingerprint(errors) { if (!q('#fingerprintForm input[name="fp[date]"]').value) errors.push('تاريخ التصحيح مطلوب.'); if (!q('#fingerprintForm input[name="fp[in_time]"]').value && !q('#fingerprintForm input[name="fp[out_time]"]').value) errors.push('يجب إدخال وقت الحضور أو الانصراف.'); if (!q('#fingerprintForm select[name="fp[reason]"]').value) errors.push('سبب التصحيح مطلوب.'); if (!q('#fingerprintForm input[name="fp[details]"]').value.trim()) errors.push('تفاصيل السبب مطلوبة.'); }
    function validateVacation(errors) { const vacType = q('#vacationForm #vacMainType').value; if (!vacType) errors.push('نوع الإجازة مطلوب.'); const dayType = q('input[name="vac[day_type]"]:checked')?.value; if(dayType === 'full'){ const from = q('#vacFrom').value, to = q('#vacTo').value; if (!from) errors.push('تاريخ بداية الإجازة مطلوب.'); if (!to) errors.push('تاريخ نهاية الإجازة مطلوب.'); if (from && to && to < from) errors.push('تاريخ النهاية يجب أن يكون بعد البداية.');} else if(dayType === 'half') { if(!q('#vacHalfDate').value) errors.push('تاريخ نصف الإجازة مطلوب.'); } if (!isHrUser) { let requested = (dayType === 'half') ? 0.5 : (parseInt(q('#vacDaysCount').value, 10) || 0); const available = parseFloat(q('#vacBalanceDisplay').textContent) || 0; if (requested > available) errors.push('رصيد الإجازات غير كافٍ.'); } if (!q('input[name="vac[reason]"]').value.trim()) errors.push('سبب الإجازة مطلوب.'); if (vacType === 'sick' && (!q('#vacAttachment').files || q('#vacAttachment').files.length === 0)) errors.push('التقرير الطبي إلزامي.'); }
    function validateOvertime(errors) { if (!q('#overtimeForm input[name="ot[date]"]').value) errors.push('تاريخ العمل الإضافي مطلوب.'); const hours = q('#overtimeForm input[name="ot[hours]"]').value; if (!hours || parseFloat(hours) <= 0) errors.push('عدد الساعات يجب أن يكون رقماً صحيحاً.'); if (!q('#overtimeForm input[name="ot[reason]"]').value.trim()) errors.push('سبب العمل الإضافي مطلوب.'); }
    
    setupFormValidation('fingerprintForm', validateFingerprint, 'fpErrorAlert');
    setupFormValidation('vacationForm', validateVacation, 'vacErrorAlert');
    setupFormValidation('overtimeForm', validateOvertime, 'otErrorAlert');

    // --- Vacation Form Specific Logic ---
    const vacMainTypeSelect = q('#vacMainType'), vacFromInput = q('#vacFrom'), vacToInput = q('#vacTo');
    function calculateLeaveDays() { const from = vacFromInput.value, to = vacToInput.value; const msgEl = q('#vacDaysMsg'), countEl = q('#vacDaysCount'); if (!from || !to || to < from) { msgEl.textContent = ''; countEl.value = 0; return; } let count = 0, cur = new Date(from); let end = new Date(to); while(cur <= end){ const day = cur.getDay(), dateStr = cur.toISOString().slice(0, 10); if(day !== 5 && day !== 6 && !PUBLIC_HOLIDAYS.includes(dateStr)) count++; cur.setDate(cur.getDate() + 1); } msgEl.innerHTML = `مجموع أيام العمل: <b class="text-primary">${count}</b> يوم`; countEl.value = count; }
    function updateBalanceDisplay() { const type = vacMainTypeSelect.value; q('#attachmentRequired').classList.toggle('d-none', type !== 'sick'); q('#vacAttachment').required = type === 'sick'; q('#vacBalanceDisplay').textContent = (type && employeeBalances[type]) ? employeeBalances[type].remaining : '--'; calculateLeaveDays(); }
    const dayTypeRadios = document.querySelectorAll('input[name="vac[day_type]"]');
    function toggleVacationFields() { const selectedType = q('input[name="vac[day_type]"]:checked').value; q('#vacFullDayRange').classList.toggle('d-none', selectedType !== 'full'); q('#vacHalfDayFields').classList.toggle('d-none', selectedType !== 'half'); if(selectedType === 'half') {q('#vacDaysCount').value = '0.5'; q('#vacDaysMsg').innerHTML = `مجموع الأيام: <b class="text-primary">0.5</b> يوم`;} else {calculateLeaveDays();} }
    q('#vacationDurationContainer').classList.remove('d-none'); // Always show the toggle
    dayTypeRadios.forEach(radio => radio.addEventListener('change', toggleVacationFields));
    if(vacMainTypeSelect) { vacMainTypeSelect.addEventListener('change', updateBalanceDisplay); vacFromInput.addEventListener('change', calculateLeaveDays); vacToInput.addEventListener('change', calculateLeaveDays); updateBalanceDisplay(); toggleVacationFields();}

    // Initial calls
    updateAttendanceBox();
    loadLastThreeRequests();
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarGrid = document.getElementById('calendarGrid');
    const monthTitleEl = document.getElementById('calendarMonthTitle');
    const detailsCardEl = document.getElementById('detailsCard');
    const detailsDateTitleEl = document.getElementById('detailsDateTitle');
    const dayDetailsContentEl = document.getElementById('dayDetailsContent');
    
    let currentDate = new Date();

    async function fetchCalendarData(year, month) {
        calendarGrid.innerHTML = `<div class="text-center p-5 w-100" style="grid-column: 1 / 8;"><div class="spinner-border"></div></div>`;
        
        try {
            // This endpoint needs to exist in your Users2.php controller
            const response = await fetch(`<?php echo site_url('users2/get_calendar_data'); ?>?y=${year}&m=${month}`);
            const data = await response.json();
            
            if (data.status === 'success') {
                renderCalendar(year, month, data.data);
            } else {
                calendarGrid.innerHTML = `<p class="text-danger">Error loading data.</p>`;
            }
        } catch (error) {
            console.error("Fetch Error:", error);
            calendarGrid.innerHTML = `<p class="text-danger">Failed to connect to server.</p>`;
        }
    }

    function renderCalendar(year, month, data) {
        calendarGrid.innerHTML = '';
        monthTitleEl.textContent = new Date(year, month - 1).toLocaleDateString('ar-SA', { month: 'long', year: 'numeric' });

        const daysInMonth = new Date(year, month, 0).getDate();
        const firstDayIndex = new Date(year, month - 1, 1).getDay(); // 0=Sun, 1=Mon...

        // Add empty cells for the first week
        for (let i = 0; i < firstDayIndex; i++) {
            const emptyCell = document.createElement('div');
            emptyCell.className = 'day-box empty';
            calendarGrid.appendChild(emptyCell);
        }

        // Add day cells
        for (let day = 1; day <= daysInMonth; day++) {
            const dateStr = `${year}-${String(month).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
            const dayData = data.find(d => d.date === dateStr) || { date: dateStr, status: 'unknown' };
            
            const cell = document.createElement('div');
            cell.className = 'day-box';
            cell.textContent = day;
            cell.dataset.date = dateStr;
            cell.dataset.details = JSON.stringify(dayData); // Store all data
            
            // Apply class based on status
            cell.classList.add(`day-${dayData.status}`);
            if (new Date(dateStr).toDateString() === new Date().toDateString()) {
                cell.classList.add('day-today');
            }

            // Add event dot if there's a leave request
            if (dayData.has_leave) {
                const dot = document.createElement('span');
                dot.className = 'event-dot';
                dot.style.backgroundColor = '#1abc9c'; // Leave color
                cell.appendChild(dot);
            }

            cell.addEventListener('click', () => {
                document.querySelectorAll('.day-box.selected').forEach(c => c.classList.remove('selected'));
                cell.classList.add('selected');
                renderDayDetails(dayData);
            });
            calendarGrid.appendChild(cell);
        }
    }

    function renderDayDetails(data) {
        detailsCardEl.style.display = 'block';
        detailsDateTitleEl.textContent = new Date(data.date + 'T00:00:00').toLocaleDateString('ar-SA', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

        let contentHtml = '';
        if (data.status === 'present') {
            contentHtml = `
                <div class="detail-item">
                    <span class="label">وقت الدخول</span>
                    <span class="value">${data.check_in || '--:--'}</span>
                </div>
                <div class="detail-item">
                    <span class="label">وقت الخروج</span>
                    <span class="value">${data.check_out || '--:--'}</span>
                </div>
                <div class="detail-item">
                    <span class="label">مدة العمل</span>
                    <span class="value">${data.worked || '--:--'}</span>
                </div>
                <div class="detail-item">
                    <span class="label">الفرق</span>
                    <span class="value ${data.difference && data.difference.startsWith('+') ? 'positive' : 'negative'}">${data.difference || '--:--'}</span>
                </div>
            `;
        } else if (data.status === 'leave') {
            contentHtml = `<div class="detail-item"><span class="label">الحالة</span><span class="value">إجازة معتمدة</span></div>`;
        } else if (data.status === 'absent') {
            contentHtml = `<div class="detail-item"><span class="label">الحالة</span><span class="value">غياب</span></div>`;
        } else {
             detailsCardEl.style.display = 'none';
        }
        dayDetailsContentEl.innerHTML = contentHtml;
    }

    document.getElementById('btnPrevMonth').addEventListener('click', (e) => {
        e.preventDefault();
        currentDate.setMonth(currentDate.getMonth() - 1);
        fetchCalendarData(currentDate.getFullYear(), currentDate.getMonth() + 1);
    });

    document.getElementById('btnNextMonth').addEventListener('click', (e) => {
        e.preventDefault();
        currentDate.setMonth(currentDate.getMonth() + 1);
        fetchCalendarData(currentDate.getFullYear(), currentDate.getMonth() + 1);
    });
    
    // Initial Load
    fetchCalendarData(currentDate.getFullYear(), currentDate.getMonth() + 1);
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const myRequestsTab = document.getElementById('my-requests-subtab');
    const approvalsTab = document.getElementById('approvals-subtab');
    const myRequestsContainer = document.getElementById('myRequestsContainer');
    const approvalsContainer = document.getElementById('approvalsContainer');
    const detailsModal = new bootstrap.Modal(document.getElementById('requestDetailsModal'));
    const detailsModalBody = document.getElementById('requestDetailsBody');

    const statusMap = {
        '0': { text: 'قيد المراجعة', class: 'bg-warning text-dark' },
        '2': { text: 'معتمد', class: 'bg-success' },
        '3': { text: 'مرفوض', class: 'bg-danger' },
        '-1': { text: 'ملغي', class: 'bg-secondary' },
        '-2': { text: 'ملغي بواسطة الموارد البشرية', class: 'bg-dark' }
    };

    function renderRequests(container, requests, isApproval) {
        if (!requests || requests.length === 0) {
            container.innerHTML = `<div class="card-glass p-4 text-center"><p class="mb-0">لا توجد طلبات لعرضها.</p></div>`;
            return;
        }
        
        let html = '';
        requests.forEach(req => {
            const status = statusMap[req.status] || { text: `غير معروف (${req.status})`, class: 'bg-secondary' };
            // ✅ Added data-order-id attribute to the card
            html += `
                <div class="request-card" data-order-id="${req.id}">
                    <div class="request-card-header">
                        <h6>${req.order_name} #${req.id}</h6>
                        <span class="badge ${status.class}">${status.text}</span>
                    </div>
                    <div class="request-card-body">
                        ${isApproval ? `<div class="detail-row"><span class="detail-label">اسم الموظف</span><span class="detail-value">${req.emp_name}</span></div>` : ''}
                        <div class="detail-row"><span class="detail-label">تاريخ الطلب</span><span class="detail-value">${new Date(req.date + ' ' + req.time).toLocaleDateString('ar-SA')}</span></div>
                        <div class="detail-row"><span class="detail-label">تفاصيل</span><span class="detail-value">${getRequestDetails(req)}</span></div>
                    </div>
                    ${isApproval ? `<div class="request-card-footer"><button class="btn btn-sm btn-success">موافقة</button><button class="btn btn-sm btn-danger">رفض</button></div>` : ''}
                </div>`;
        });
        container.innerHTML = html;
    }

    function getRequestDetails(req) {
        switch(String(req.type)) {
            case '2': return `تصحيح ليوم: ${req.correction_date}`;
            case '5': return `إجازة من ${req.vac_start} إلى ${req.vac_end}`;
            case '3': return `عمل إضافي - ${req.ot_hours} ساعات`;
            default: return req.note || 'لا توجد تفاصيل';
        }
    }

    async function loadMyRequests() {
        myRequestsContainer.innerHTML = `<div class="text-center p-5"><div class="spinner-border"></div></div>`;
        const response = await fetch("<?php echo site_url('users2/get_my_requests'); ?>");
        const result = await response.json();
        if (result.status === 'success') renderRequests(myRequestsContainer, result.data, false);
    }

    async function loadApprovals() {
        approvalsContainer.innerHTML = `<div class="text-center p-5"><div class="spinner-border"></div></div>`;
        const response = await fetch("<?php echo site_url('users2/get_my_approvals'); ?>");
        const result = await response.json();
        if (result.status === 'success') renderRequests(approvalsContainer, result.data, true);
    }
    
    // ✅ NEW: Function to show request details
    async function showRequestDetails(orderId) {
        detailsModal.show();
        detailsModalBody.innerHTML = `<div class="text-center p-5"><div class="spinner-border"></div></div>`;

        const response = await fetch(`<?php echo site_url('users2/get_request_details'); ?>/${orderId}`);
        const result = await response.json();

        if (result.status === 'success') {
            const req = result.data;
            let detailsHtml = '<div class="d-flex flex-column gap-2">';
            const allDetails = {
                "رقم الطلب": req.id, "نوع الطلب": req.order_name, "مقدم الطلب": req.emp_name,
                "تاريخ الطلب": new Date(req.date + ' ' + req.time).toLocaleString('ar-SA'),
                "الحالة": (statusMap[req.status] || {}).text || 'غير معروف',
                "فترة الإجازة": (req.vac_start && req.vac_end) ? `من ${req.vac_start} إلى ${req.vac_end}` : null,
                "عدد أيام الإجازة": req.vac_days_count, "سبب الإجازة": req.vac_reason,
                "تاريخ التصحيح": req.correction_date, "وقت الدخول": req.attendance_correction, "وقت الخروج": req.correction_of_departure,
                "سبب التصحيح": req.reason_for_correction, "تفاصيل السبب": req.details_of_the_reason,
                "تاريخ العمل الإضافي": req.ot_date, "عدد الساعات": req.ot_hours, "سبب العمل الإضافي": req.ot_reason,
                "سبب الرفض": req.reason_for_rejection
            };
            for(const [label, value] of Object.entries(allDetails)) {
                if (value) { // Only show details that have a value
                    detailsHtml += `<div class="detail-row"><span class="detail-label">${label}</span><span class="detail-value">${value}</span></div>`;
                }
            }
            detailsHtml += '</div>';
            detailsModalBody.innerHTML = detailsHtml;
        } else {
            detailsModalBody.innerHTML = `<p class="text-danger p-3">لا يمكن تحميل تفاصيل الطلب.</p>`;
        }
    }

    // ✅ NEW: Event Listener for clicking on a request card
    document.getElementById('requestsSubTabsContent').addEventListener('click', function(e) {
        const card = e.target.closest('.request-card');
        if (card) {
            const orderId = card.dataset.orderId;
            if (orderId) {
                showRequestDetails(orderId);
            }
        }
    });

    // Event Listeners for sub-tabs
    myRequestsTab.addEventListener('shown.bs.tab', loadMyRequests);
    approvalsTab.addEventListener('shown.bs.tab', loadApprovals);
    
    // Initial Load
    loadMyRequests();
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const profileAccordion = document.getElementById('profileAccordion');
    if (!profileAccordion) return;

    // A map of which function to call for each section
    const dataLoaders = {
        personal: "<?php echo site_url('users2/mobile_get_personal_details'); ?>",
        job: "<?php echo site_url('users2/mobile_get_job_details'); ?>",
        financial: "<?php echo site_url('users2/mobile_get_financial_details'); ?>",
        balances: "<?php echo site_url('users2/mobile_get_leave_balances'); ?>",
        contract: "<?php echo site_url('users2/mobile_get_contract_details'); ?>"
    };

    // A map of functions to build the HTML for each section
    const contentRenderers = {
        personal: (data) => `
            <div class="detail-list-item"><span class="label">الاسم الكامل</span><span class="value">${data.subscriber_name || '-'}</span></div>
            <div class="detail-list-item"><span class="label">الجنسية</span><span class="value">${data.nationality || '-'}</span></div>
            <div class="detail-list-item"><span class="label">تاريخ الميلاد</span><span class="value">${data.birth_date || '-'}</span></div>
            <div class="detail-list-item"><span class="label">البريد الإلكتروني</span><span class="value">${data.email || '-'}</span></div>
            <div class="detail-list-item"><span class="label">رقم الهاتف</span><span class="value">${data.phone || '-'}</span></div>`,
        job: (data) => `
            <div class="detail-list-item"><span class="label">المسمى الوظيفي</span><span class="value">${data.profession || '-'}</span></div>
            <div class="detail-list-item"><span class="label">القسم</span><span class="value">${data.n1 || '-'}</span></div>
            <div class="detail-list-item"><span class="label">تاريخ التعيين</span><span class="value">${data.joining_date || '-'}</span></div>
            <div class="detail-list-item"><span class="label">المدير المباشر</span><span class="value">${data.manager || '-'}</span></div>`,
        financial: (data) => {
            const base = parseFloat(data.base_salary || 0);
            const housing = parseFloat(data.housing_allowance || 0);
            const transport = parseFloat(data.n4 || 0);
            const others = parseFloat(data.total_salary || 0) - base - housing - transport;
            setTimeout(() => renderPieChart(base, housing, transport, others), 100);
            return `
                <div class="pie-chart-container"><canvas id="salaryPieChart"></canvas></div>
                <div class="detail-list-item"><span class="label">الراتب الأساسي</span><span class="value">${base.toLocaleString()} SAR</span></div>
                <div class="detail-list-item"><span class="label">بدل السكن</span><span class="value">${housing.toLocaleString()} SAR</span></div>
                <div class="detail-list-item"><span class="label">بدل النقل</span><span class="value">${transport.toLocaleString()} SAR</span></div>
                <div class="detail-list-item"><span class="label">بدلات أخرى</span><span class="value">${others.toLocaleString()} SAR</span></div>
                <div class="detail-list-item"><strong><span class="label">الإجمالي</span></strong><strong><span class="value">${parseFloat(data.total_salary || 0).toLocaleString()} SAR</span></strong></div>`;
        },
        balances: (balances) => {
            if (!balances || balances.length === 0) return `<p class="text-center text-muted">لا توجد أرصدة إجازات مسجلة.</p>`;
            let html = '<div class="balance-grid">';
            balances.forEach(b => {
                html += `<div class="balance-box"><div class="value">${parseFloat(b.remaining_balance).toFixed(1)}</div><div class="label">${b.leave_type_name}</div></div>`;
            });
            html += '</div>';
            return html;
        },
        contract: (data) => `
            <div class="detail-list-item"><span class="label">حالة العقد</span><span class="value">${data.contract_status || '-'}</span></div>
            <div class="detail-list-item"><span class="label">تاريخ البداية</span><span class="value">${data.contract_start || '-'}</span></div>
            <div class="detail-list-item"><span class="label">تاريخ النهاية</span><span class="value">${data.contract_end || '-'}</span></div>
            <div class="detail-list-item"><span class="label">المدة المتبقية</span><span class="value">${data.remaining_renewal_period || '-'}</span></div>`
    };
    
    function renderPieChart(base, housing, transport, others) {
        const ctx = document.getElementById('salaryPieChart')?.getContext('2d');
        if (!ctx) return;
        if (window.mySalaryChart) window.mySalaryChart.destroy();
        window.mySalaryChart = new Chart(ctx, {
            type: 'doughnut', data: { labels: ['الأساسي', 'السكن', 'النقل', 'أخرى'], datasets: [{ data: [base, housing, transport, others], backgroundColor: ['var(--marsom-orange)', 'var(--marsom-blue)', '#6c757d', '#34495e'], borderWidth: 0 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    }

    // Main logic to handle accordion clicks
    profileAccordion.addEventListener('show.bs.collapse', async function (event) {
        const button = event.target.previousElementSibling.querySelector('button');
        const contentArea = event.target.querySelector('.accordion-body');
        const loaderKey = button.dataset.loader;

        // Load data only if it hasn't been loaded before
        if (contentArea && !contentArea.dataset.loaded) {
            contentArea.innerHTML = `<div class="text-center p-4"><div class="spinner-border spinner-border-sm"></div></div>`;
            try {
                const response = await fetch(dataLoaders[loaderKey]);
                const result = await response.json();
                if (result.status === 'success') {
                    contentArea.innerHTML = contentRenderers[loaderKey](result.data);
                    contentArea.dataset.loaded = 'true'; // Mark as loaded
                } else {
                    contentArea.innerHTML = `<p class="text-danger text-center">فشل تحميل البيانات.</p>`;
                }
            } catch (error) {
                console.error(`Error loading ${loaderKey} details:`, error);
                contentArea.innerHTML = `<p class="text-danger text-center">خطأ في الاتصال.</p>`;
            }
        }
    });

    // Manually trigger the first section to load its content
    document.getElementById('collapsePersonal').dispatchEvent(new Event('show.bs.collapse'));
});
</script>

<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('<?php echo base_url('assets/sw.js'); ?>')
                .then(reg => console.log('Service Worker registered.'))
                .catch(err => console.error('Service Worker registration failed: ', err));
        });
    }
</script>
</body>
</html>