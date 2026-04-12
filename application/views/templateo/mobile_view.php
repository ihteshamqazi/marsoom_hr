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
    $saturday_assignments = $saturday_assignments ?? [];
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --marsom-blue: #001f3f; 
            --marsom-orange: #FF8C00; 
            --text-light: #ffffff;
            --text-muted-light: #ffffff; 
            --glass-bg: rgba(255, 255, 255, 0.08);
            --glass-border: rgba(255, 255, 255, 0.2);
        }
        
        html, body { 
            font-family: 'Tajawal', sans-serif; 
            background: linear-gradient(135deg, var(--marsom-blue) 0%, #34495e 50%, var(--marsom-orange) 100%); 
            background-attachment: fixed; 
            color: var(--text-light); 
        }
        
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
        
        .day-box.day-saturday_work {
            background-color: rgba(255, 140, 0, 0.25);
            border: 1px solid rgba(255, 140, 0, 0.6);
            color: #ffffff;
        }
        /* Fix for Request Details Modal */
#requestDetailsModal .modal-content {
    background: linear-gradient(135deg, #1c2b3e 0%, #2c3e50 100%);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 1rem;
}

#requestDetailsModal .modal-header {
    background: rgba(0, 0, 0, 0.3);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    color: #ffffff;
}

#requestDetailsModal .modal-header .modal-title {
    color: #ffffff;
    font-weight: 700;
}

#requestDetailsModal .modal-body {
    background: rgba(0, 0, 0, 0.2);
    color: #ffffff;
}

#requestDetailsModal .modal-footer {
    background: rgba(0, 0, 0, 0.3);
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

#requestDetailsModal .card {
    background: rgba(255, 255, 255, 0.05) !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    color: #ffffff !important;
}

#requestDetailsModal .card-title {
    color: #ffffff !important;
}

#requestDetailsModal h1, 
#requestDetailsModal h2, 
#requestDetailsModal h3, 
#requestDetailsModal h4, 
#requestDetailsModal h5, 
#requestDetailsModal h6 {
    color: #ffffff !important;
}

#requestDetailsModal .text-muted {
    color: rgba(255, 255, 255, 0.6) !important;
}

#requestDetailsModal .alert {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #ffffff;
}

#requestDetailsModal .alert-success {
    background: rgba(40, 167, 69, 0.2);
    border-color: rgba(40, 167, 69, 0.5);
    color: #d4edda;
}

#requestDetailsModal .alert-danger {
    background: rgba(220, 53, 69, 0.2);
    border-color: rgba(220, 53, 69, 0.5);
    color: #f8d7da;
}

#requestDetailsModal .alert-info {
    background: rgba(23, 162, 184, 0.2);
    border-color: rgba(23, 162, 184, 0.5);
    color: #d1ecf1;
}

#requestDetailsModal .alert-warning {
    background: rgba(255, 193, 7, 0.2);
    border-color: rgba(255, 193, 7, 0.5);
    color: #fff3cd;
}

#requestDetailsModal .badge {
    color: #ffffff;
}

#requestDetailsModal .bg-dark {
    background-color: rgba(0, 0, 0, 0.3) !important;
    color: #ffffff !important;
}

#requestDetailsModal .bg-success {
    background-color: rgba(40, 167, 69, 0.8) !important;
}

#requestDetailsModal .bg-warning {
    background-color: rgba(255, 193, 7, 0.8) !important;
    color: #000000 !important;
}

#requestDetailsModal .bg-danger {
    background-color: rgba(220, 53, 69, 0.8) !important;
}

#requestDetailsModal .btn-outline-light {
    border-color: rgba(255, 255, 255, 0.3);
    color: #ffffff;
}

#requestDetailsModal .btn-outline-light:hover {
    background-color: rgba(255, 255, 255, 0.1);
    border-color: var(--marsom-orange);
}

#requestDetailsModal .btn-primary {
    background-color: var(--marsom-orange);
    border-color: var(--marsom-orange);
    color: #ffffff;
}

#requestDetailsModal .btn-success {
    background-color: #28a745;
    border-color: #28a745;
    color: #ffffff;
}

#requestDetailsModal .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

#requestDetailsModal hr {
    border-color: rgba(255, 255, 255, 0.1);
}

#requestDetailsModal .rounded {
    border-radius: 0.5rem !important;
}

#requestDetailsModal .small {
    color: rgba(255, 255, 255, 0.7) !important;
}
        .active-filters-container {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 0.75rem;
            padding: 1rem;
        }
        
        .filter-chip {
            background: rgba(255, 140, 0, 0.2);
            border: 1px solid var(--marsom-orange);
            border-radius: 1.5rem;
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-light);
        }
        
        .filter-chip .remove-filter {
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            padding: 0;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
        }
        
        .filter-chip .remove-filter:hover { background: rgba(255, 255, 255, 0.2); }
        
        .offcanvas {
            background: linear-gradient(135deg, var(--marsom-blue) 0%, #1a2a3a 100%);
        }
        
        .offcanvas-header { background: rgba(0, 0, 0, 0.3); }
        .offcanvas-body { background: rgba(255, 255, 255, 0.05); }
        
        .offcanvas .form-select, 
        .offcanvas .form-control {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--glass-border);
            color: var(--text-light);
            border-radius: 0.5rem;
        }
        
        .offcanvas .form-select:focus, 
        .offcanvas .form-control:focus {
            background-color: rgba(255, 255, 255, 0.15);
            border-color: var(--marsom-orange);
            color: var(--text-light);
            box-shadow: 0 0 0 0.2rem rgba(255, 140, 0, 0.25);
        }
        
        .form-check-input:checked {
            background-color: var(--marsom-orange);
            border-color: var(--marsom-orange);
        }
        
        .day-box.day-saturday_work::after {
            content: '📌';
            position: absolute;
            top: 5px;
            left: 5px;
            font-size: 0.7em;
        }
        
        .card-glass .card-header { background: rgba(255, 255, 255, 0.1); border-bottom-color: var(--glass-border); font-family: 'El Messiri', sans-serif; font-weight: 700; }
        .list-group-item { background: transparent; border-bottom: 1px solid var(--glass-border) !important; color: var(--text-light); }
        .list-group-item:last-child { border-bottom: none !important; }
        .list-group-item h6 { color: #fff; font-weight: 700; }
        .list-group-item .request-details { color: var(--text-muted-light); font-size: 0.85rem; }
        
        .modal-content { background: #1c2b3e; color: #fff; border: 1px solid var(--glass-border); border-radius: 1rem; }
        .modal-header, .modal-footer { border-color: var(--glass-border); }
        .form-control, .form-select { background-color: var(--glass-bg); border-color: var(--glass-border); color: #fff; }
        .form-control:focus, .form-select:focus { background-color: rgba(255,255,255,0.2); border-color: var(--marsom-orange); color: #000; box-shadow: none; }
        .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }
        .alert-info { background-color: var(--glass-bg); border-color: var(--glass-border); color: #ffffff; }
        
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
        .balance-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .balance-box { background-color: #f9fafb; text-align: center; padding: 1rem; border-radius: 0.75rem; }
        .balance-box .value { font-size: 2rem; font-weight: 800; color: var(--primary-brand); line-height: 1; }
        .balance-box .label { font-size: 0.9rem; color: #6B7280; margin-top: 0.25rem; }
        
        .offcanvas .form-label {
            font-weight: 600;
            color: var(--text-light);
            margin-bottom: 0.5rem;
        }
        
        .offcanvas .form-select, .offcanvas .form-control {
            background-color: rgba(255,255,255,0.1);
            border: 1px solid var(--glass-border);
            color: var(--text-light);
        }
        
        .offcanvas .form-select:focus, .offcanvas .form-control:focus {
            background-color: rgba(255,255,255,0.2);
            border-color: var(--marsom-orange);
            color: var(--text-light);
        }
        
        .filter-badge {
            background: var(--marsom-orange);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            margin-right: 5px;
        }
        
        .active-filters-container {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 0.75rem;
            padding: 1rem;
        }
        
        .attendance-box .time-counter {
            font-size: 2.8rem;
            font-weight: 700;
            color: var(--text-light);
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            margin-bottom: 0.5rem;
            min-height: 3.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .attendance-box .status-text {
            font-size: 1.1rem;
            color: var(--text-muted-light);
            margin-bottom: 1rem;
            min-height: 1.5rem;
        }
        
        .attendance-box .btn {
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
        }
        
        .attendance-box .btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        
        .offcanvas .btn-outline-light {
            border-color: var(--glass-border);
            color: var(--text-light);
        }
        
        .offcanvas .btn-outline-light:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: var(--marsom-orange);
        }
        
        .offcanvas .btn-outline-light.active {
            background-color: var(--marsom-orange);
            border-color: var(--marsom-orange);
            color: white;
        }
        
        .badge { transition: all 0.3s ease; }
        #filterToggleButton.active {
            background-color: var(--marsom-orange);
            border-color: var(--marsom-orange);
            color: black;
        }
        
        .logout-btn {
            background-color: transparent !important;
            color: #dc3545 !important;
            font-weight: 700;
        }
        
        .logout-btn:hover {
            background-color: rgba(220, 53, 69, 0.1) !important;
            color: #dc3545 !important;
        }
        
        .accordion-item:last-child { border-bottom: none; }
        
        .avatar-container {
            display: inline-block;
            position: relative;
            margin-bottom: 1rem;
        }
        
        .avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 4px solid var(--marsom-orange);
            object-fit: cover;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }
        
        #changePhotoBtn {
            background: var(--marsom-orange);
            border: 2px solid white;
            color: white;
            font-size: 0.8rem;
            transition: all 0.3s ease;
        }
        
        #changePhotoBtn:hover {
            background: #e67e00;
            transform: scale(1.1);
        }
        
        .profile-photo-loading { opacity: 0.7; }
        
        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 0.75rem;
            padding: 1rem;
            color: var(--text-light);
            animation: slideIn 0.3s ease;
        }
        
        .toast-success { border-left: 4px solid #28a745; }
        .toast-error { border-left: 4px solid #dc3545; }
        
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
</head>
<body>

<div class="particles"><div class="particle"></div><div class="particle"></div><div class="particle"></div></div>

<div class="container app-container">
    <ul class="nav nav-tabs nav-fill mb-3" id="myTab" role="tablist" style="font-size: 0.8rem;">
        <li class="nav-item" role="presentation">
            <button class="nav-link active p-1 px-2" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button">
                <i class="fas fa-home me-1"></i> الرئيسية
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link p-1 px-2" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance-tab-pane" type="button">
                <i class="fas fa-user-clock me-1"></i> الحضور
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link p-1 px-2" id="requests-tab" data-bs-toggle="tab" data-bs-target="#requests-tab-pane" type="button">
                <i class="fas fa-clipboard-list me-1"></i> الطلبات
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link p-1 px-2" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button">
                <i class="fas fa-user me-1"></i> ملفي
            </button>
        </li>
    </ul>
    
    <div class="tab-content" id="myTabContent">
        <!-- Home Tab -->
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
                
                <?php if($this->session->userdata('username') != '2824'): ?>
                <div class="row g-3 request-btn-grid my-4">
             
<?php if($this->session->userdata('username') == '1001'): ?>
     <div class="col-4">
                        <button class="btn w-100" data-bs-toggle="modal" data-bs-target="#vacationModal"><i class="fas fa-umbrella-beach"></i><span>إجازة</span></button>
</div> 
                         <?php endif; ?> 

                 <!--   <?php if($this->session->userdata('username') != '1001'): ?>
                    <div class="col-4">

                        <button class="btn w-100" data-bs-toggle="modal" data-bs-target="#overtimeModal"><i class="fas fa-clock-rotate-left"></i><span>عمل إضافي</span></button>

                    </div>
 <?php endif; ?>-->
                 <?php if($this->session->userdata('username') == '1835' or $this->session->userdata('username') == '1001'): ?>
                 
                   
                     <div class="col-4">
    <a href="<?= site_url('AnnualIncentives'); ?>" class="btn w-100">
        <i class="fas fa-user-tie"></i>
        <span>   الحوافز السنوية  </span>
    </a>
</div>

                 
                <?php endif; ?>

                 <?php if($this->session->userdata('username') == '1835' or $this->session->userdata('username') == '1001'): ?>
                 
                   
                     <div class="col-4">
    <a href="<?= site_url('AnnualEvaluationSupervisor'); ?>" class="btn w-100">
        <i class="fas fa-clipboard-check"></i>
        <span>   تقييم الموظفين     </span>
    </a>
</div>

                 
                <?php endif; ?>

                  <?php if($this->session->userdata('username') == '1835' or $this->session->userdata('username') == '1001'): ?>

<div class="col-4">
    <a href="https://services.marsoom.net/sla/SlaApprovals/inbox" class="btn w-100">
        <i class="fas fa-inbox"></i>
        <span> SLA الوارد </span>
    </a>
</div>

<?php endif; ?>


<?php if($this->session->userdata('username') == '1835' or $this->session->userdata('username') == '1001'): ?>

<div class="col-4">
    <a href="https://services.marsoom.net/sla/SlaEmployeePortal" class="btn w-100">
        <i class="fas fa-paper-plane"></i>
        <span> SLA الصادر </span>
    </a>
</div>

<?php endif; ?>

<?php if($this->session->userdata('username') == '1835' or $this->session->userdata('username') == '1001'): ?>

<div class="col-4">
    <a href="https://services.marsoom.net/sla/SlaEmployeeViolations" class="btn w-100">
        <i class="fas fa-triangle-exclamation"></i>
        <span>  مخالفات SLA   </span>
    </a>
</div>

<?php endif; ?>




<?php if($this->session->userdata('username') == '1835' or $this->session->userdata('username') == '1001'  or $this->session->userdata('username') == '2901'): ?>
<div class="col-4">
    <a href="<?= site_url('users1/delegation_report'); ?>" class="btn w-100">
        <i class="fas fa-id-badge"></i>
        <span>  تقرير التفويضات  </span>
    </a>
</div>
<?php endif; ?>
                 <?php if($this->session->userdata('username') == '1835' or $this->session->userdata('username') == '1001'): ?>
                 
                   
                      <div class="col-4">
    <a href="https://services.marsoom.net/bills11/Policy_pending_requests" class="btn w-100">
        <i class="fas fa-money-bill-wave"></i>
        <span>سياسات التحصيل</span>
    </a>
</div>

                 
                <?php endif; ?>





                 <?php if($this->session->userdata('username') == '1835' or $this->session->userdata('username') == '1001'): ?>
                 
                   
                     <div class="col-4">
    <a href="<?= site_url('users1/renewal_system'); ?>" class="btn w-100">
        <i class="fa-solid fa-id-card"></i>
        <span>      تجديد الهويات  </span>
    </a>
</div>

                 
                <?php endif; ?>


                <?php if($this->session->userdata('username') == '1835' or $this->session->userdata('username') == '1001'): ?>
                 
                   
                     <div class="col-4">
    <a href="https://services.marsoom.net/collection/meetings" class="btn w-100">
        <i class="fa-solid fa-calendar-days"></i>
        <span>       الاجتماعات    </span>
    </a>
</div>

                 
                <?php endif; ?>



                 <?php if($this->session->userdata('username') == '1835' or $this->session->userdata('username') == '1001'): ?>
<div class="col-4">
    <a href="https://services.marsoom.net/bills11/ExchangeIncentives" class="btn w-100">
        <i class="fas fa-hand-holding-usd"></i>
        <span> مكافئات ادارة التحصيل </span>
    </a>
</div>
<?php endif; ?>




                 <?php if($this->session->userdata('username') == '1835' or $this->session->userdata('username') == '1001'): ?>
                 
                   
                     <div class="col-4">
    <a href="https://services.marsoom.net/recruitment2/candidates/my_pending_evaluations" class="btn w-100">
        <i class="fas fa-user-check"></i>
        <span>     التوظيف   </span>
    </a>
</div>

                 
                <?php endif; ?>


                  <?php if($this->session->userdata('username') == '1835' or $this->session->userdata('username') == '1001'): ?>
                 
                   
                     <div class="col-4">
    <a href="https://services.marsoom.net/collection/users/ceo_report" class="btn w-100">
        <i class="fas fa-chart-line"></i>
        <span>      تقارير التحصيل   </span>
    </a>
</div>

                 
                <?php endif; ?>




                </div>
                <?php endif; ?>

              



                <div class="card card-glass requests-list">
                    <div class="card-header">آخر 3 طلبات</div>
                    <ul class="list-group list-group-flush" id="lastRequestsContainer"></ul>
                </div>
            </div>
        </div>
        
        <!-- Attendance Tab -->
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
                    <div class="chip"><span class="dot" style="background-color: #FF8C00;"></span> عمل السبت</div>
                </div>

                <div class="calendar-grid-header">
                    <div>ح</div><div>ن</div><div>ث</div><div>ر</div><div>خ</div><div>ج</div><div>س</div>
                </div>
                <div class="calendar-grid" id="calendarGrid">
                    <div class="text-center p-5"><div class="spinner-border"></div></div>
                </div>
                
                <div class="details-card-new mt-4" id="detailsCard" style="display:none;">
                    <h6 class="details-title" id="detailsDateTitle"></h6>
                    <div id="dayDetailsContent"></div>
                </div>
            </div>
        </div>
        
        <!-- Requests Tab - FIXED SECTION -->
        <div class="tab-pane fade" id="requests-tab-pane" role="tabpanel">
            <ul class="nav nav-pills nav-fill mb-3 bg-white bg-opacity-10 rounded p-1" id="requestsSubTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-pill small fw-bold" id="my-requests-subtab" data-bs-toggle="tab" data-bs-target="#myRequestsPane" type="button">
                        <i class="fas fa-list me-1"></i> طلباتي
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill small fw-bold" id="approvals-subtab" data-bs-toggle="tab" data-bs-target="#approvalsPane" type="button" onclick="loadApprovals()">
                        <i class="fas fa-check-circle me-1"></i> الموافقات
                        <span class="badge bg-danger ms-1" id="mainPendingBadge">0</span>
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="requestsSubTabsContent">
                <!-- My Requests Pane -->
                <div class="tab-pane fade show active" id="myRequestsPane" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0 text-light">سجل طلباتي</h6>
                        <button class="btn btn-outline-light position-relative" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasFilters" id="filterToggleButton">
                            <i class="fas fa-filter me-2"></i> الفلاتر
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="activeFilterCount" style="display: none;">0</span>
                        </button>
                    </div>

                    <div class="active-filters-container mb-3" id="activeFiltersContainer" style="display: none;">
                        <div class="d-flex flex-wrap gap-2" id="activeFiltersList"></div>
                    </div>

                    <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="offcanvasFilters">
                        <div class="offcanvas-header border-bottom border-secondary">
                            <h5 class="offcanvas-title"><i class="fas fa-filter me-2"></i>فرز الطلبات</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
                        </div>
                        <div class="offcanvas-body">
                            <div class="mb-4">
                                <label class="form-label fw-bold">نوع الطلب</label>
                                <select id="filterRequestType" class="form-select">
                                    <option value="">جميع الأنواع</option>
                                    <option value="5">إجازة</option>
                                    <option value="2">تصحيح بصمة</option>
                                    <option value="3">عمل إضافي</option>
                                    <option value="1">استقالة</option>
                                    <option value="7">طلب خطاب</option>
                                    <option value="6">طلب عهدة</option>
                                    <option value="4">مصاريف مالية</option>
                                    <option value="Mandate">انتداب</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold">حالة الطلب</label>
                                <select id="filterStatus" class="form-select">
                                    <option value="">جميع الحالات</option>
                                    <option value="0">بالانتظار</option>
                                    <option value="2">معتمد</option>
                                    <option value="3">مرفوض</option>
                                    <option value="-1">ملغي</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold">الفترة الزمنية</label>
                                <div class="row g-2">
                                    <div class="col-12"><label class="form-label small">من تاريخ</label><input type="date" id="filterStartDate" class="form-control"></div>
                                    <div class="col-12"><label class="form-label small">إلى تاريخ</label><input type="date" id="filterEndDate" class="form-control"></div>
                                </div>
                            </div>
                            <button class="btn btn-primary w-100 mt-3" id="applyFiltersBtn">تطبيق الفلاتر</button>
                            <button class="btn btn-outline-light w-100 mt-2" id="resetFiltersBtn">إعادة تعيين</button>
                        </div>
                    </div>

                    <div id="myRequestsContainer">
                        <div class="text-center p-5">
                            <div class="spinner-border text-light"></div>
                            <p class="text-light mt-2">جاري تحميل طلباتي...</p>
                        </div>
                    </div>
                </div>

                <!-- Approvals Pane -->
                <div class="tab-pane fade" id="approvalsPane" role="tabpanel">
                    <div class="d-flex gap-2 mb-3 overflow-auto pb-2" style="white-space: nowrap;">
                        <button class="btn btn-sm px-3 rounded-pill btn-primary fw-bold" id="btnModeMandate" onclick="switchApprovalMode('mandate')">
                            <i class="fas fa-briefcase me-1"></i> انتداب
                        </button>

                        <button class="btn btn-sm px-3 rounded-pill btn-outline-light fw-bold" id="btnModeLeaves" onclick="switchApprovalMode('leaves')">
                            <i class="fas fa-file-alt me-1"></i> إجازات/أخرى
                        </button>
                        <button class="btn btn-sm px-3 rounded-pill btn-outline-warning fw-bold text-dark" id="btnModeClearance" onclick="switchApprovalMode('clearance')">
                            <i class="fas fa-file-signature me-1"></i> إخلاء طرف
                        </button>
                        <button class="btn btn-sm px-3 rounded-pill btn-outline-danger fw-bold" id="btnModeEOS" onclick="switchApprovalMode('eos')">
                            <i class="fas fa-hand-holding-usd me-1"></i> نهاية الخدمة
                        </button>
                        <button class="btn btn-sm px-3 rounded-pill btn-outline-secondary fw-bold" id="btnModeWorkMission" onclick="switchApprovalMode('work_mission')">
    <i class="fas fa-tasks me-1"></i> مهمة عمل
</button>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2 px-2">
                        <span class="text-white-50 small" id="approvalCountDisplay">جاري التحميل...</span>
                    </div>

                    <div id="approvalsContainer">
                        <div class="text-center p-5">
                            <div class="spinner-border text-light"></div>
                            <p class="text-light mt-2">جاري تحميل طلبات الموافقة...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Profile Tab -->
        <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel">
            <div class="profile-header">
                <div class="avatar-container position-relative">
                    <img src="<?php echo $this->session->userdata('profile_photo') ? $this->session->userdata('profile_photo') : 'https://placehold.co/100x100/333/FFF?text=' . substr($name, 0, 2); ?>" 
                         class="avatar" id="profileAvatar" alt="Profile Photo">
                    <input type="file" id="profilePhotoInput" accept="image/*" style="display: none;">
                    <button class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle" 
                            style="width: 30px; height: 30px; padding: 0;" id="changePhotoBtn">
                        <i class="fas fa-camera"></i>
                    </button>
                </div>
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
                        <div class="accordion-body" id="personal-details-content"></div>
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
                
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <a href="<?php echo site_url('users1/login'); ?>" class="accordion-button collapsed logout-btn" id="logoutButton" onclick="return confirm('هل أنت متأكد أنك تريد تسجيل الخروج؟')">
                            <i class="fas fa-sign-out-alt fa-fw"></i> خروج
                        </a>
                    </h2>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<div class="modal fade" id="fingerprintModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="fingerprintForm" action="<?php echo site_url('users1/add_new_order'); ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="request_type" value="fingerprint">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="modal-header"><h5 class="modal-title">طلب تصحيح بصمة</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div id="fpErrorAlert" class="alert alert-danger d-none"></div>
                    <div class="row g-3">
                        <div class="col-12"><label class="form-label">تاريخ التصحيح</label><input type="date" name="fp[date]" class="form-control"></div>
                        <div class="col-12"><div class="border rounded p-3"><div class="fw-bold mb-2">الحضور</div><div class="row g-2"><div class="col-sm-5"><input type="time" class="form-control" name="fp[in_time]"></div><div class="col-sm-7"><input type="text" class="form-control" name="fp[in_note]" placeholder="ملاحظة"></div></div></div></div>
                        <div class="col-12"><div class="border rounded p-3"><div class="fw-bold mb-2">الانصراف</div><div class="row g-2"><div class="col-sm-5"><input type="time" class="form-control" name="fp[out_time]"></div><div class="col-sm-7"><input type="text" class="form-control" name="fp[out_note]" placeholder="ملاحظة"></div></div></div></div>
                        <div class="col-12"><label class="form-label">سبب التصحيح</label><select name="fp[reason]" class="form-select"><option value="">اختر السبب...</option><option>نسيان بصمة</option><option>مشكلة في التطبيق</option><option>اخرى</option></select></div>
                        <div class="col-12"><label class="form-label">تفاصيل السبب</label><input type="text" name="fp[details]" class="form-control"></div>
                        <div class="col-12"><label class="form-label">مرفق (اختياري)</label><input type="file" name="fp[file]" class="form-control"></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button><button type="submit" class="btn btn-primary">إرسال</button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="vacationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="vacationForm" action="<?php echo site_url('users1/add_new_order'); ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="request_type" value="vacation">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="modal-header"><h5 class="modal-title">طلب إجازة</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div id="vacErrorAlert" class="alert alert-danger d-none"></div>
                    <div class="row g-3">
                        <div class="col-12"><label class="form-label">نوع الإجازة</label><select name="vac[main_type]" class="form-select" id="vacMainType"><?php foreach ($leave_types as $type): ?><option value="<?php echo htmlspecialchars($type['slug']); ?>"><?php echo htmlspecialchars($type['name_ar']); ?></option><?php endforeach; ?></select></div>
                        <div class="col-12"><div class="alert alert-info p-2 mb-0">رصيدك: <b id="vacBalanceDisplay">--</b> يوم</div></div>
                        
                        <div class="col-12 d-none" id="vacationDurationContainer"> 
                            <label class="form-label">مدة الإجازة</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="vac[day_type]" id="vacDayTypeFull" value="full" checked>
                                    <label class="form-check-label" for="vacDayTypeFull">يوم كامل (فترة)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="vac[day_type]" id="vacDayTypeHalf" value="half">
                                    <label class="form-check-label" for="vacDayTypeHalf">نصف يوم</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3" id="vacFullDayRange">
                            <div class="col-6"><label class="form-label">من تاريخ</label><input type="date" name="vac[start]" class="form-control" id="vacFrom"></div>
                            <div class="col-6"><label class="form-label">إلى تاريخ</label><input type="date" name="vac[end]" class="form-control" id="vacTo"></div>
                            <div class="col-12"><div id="vacDaysMsg" class="fw-bold text-center text-info"></div><input type="hidden" name="vac[days_count]" id="vacDaysCount" value="0"></div>
                        </div>
                        <div class="row g-3 d-none" id="vacHalfDayFields">
                            <div class="col-sm-6"><label class="form-label">في تاريخ</label><input type="date" name="vac[half_date]" class="form-control" id="vacHalfDate"></div>
                            <div class="col-sm-6"><label class="form-label">الفترة</label><select name="vac[half_period]" class="form-select" id="vacHalfPeriod"><option value="am">صباحي</option><option value="pm">مسائي</option></select></div>
                        </div>
                        <div class="col-12">
                            <label for="delegation_employee_id" class="form-label">تفويض المهام (اختياري)</label>
                            <select name="vac[delegation_employee_id]" id="mobile_delegation_employee_id" class="form-select">
                                <option value="">-- اختر موظف للتفويض --</option>
                                <?php if (!empty($employees)): ?>
                                    <?php foreach ($employees as $emp): ?>
                                        <?php if ($emp['username'] == $this->session->userdata('username')) continue; ?>
                                        <option value="<?php echo htmlspecialchars($emp['username']); ?>">
                                            <?php echo htmlspecialchars($emp['name']) . ' (' . htmlspecialchars($emp['username']) . ')'; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <small class="form-text text-muted-light">سيتم منع الموظف المفوض من أخذ إجازة خلال هذه الفترة.</small>
                        </div>
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
                        <div class="col-12"><label class="form-label">السبب</label><input type="text" name="ot[amount]" class="form-control"></div>
                        <div class="col-12"><label class="form-label">السبب</label><input type="text" name="ot[reason]" class="form-control"></div>
                        <div class="col-12"><label class="form-label">مرفق (اختياري)</label><input type="file" name="ot[file]" class="form-control"></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button><button type="submit" class="btn btn-primary">إرسال</button></div>
            </form>
        </div>
    </div>
</div>

<!-- Request Details Modal - FIXED -->
<div class="modal fade" id="requestDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="background: #1c2b3e; color: #fff; border: 1px solid rgba(255,255,255,0.2);">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title">تفاصيل الطلب</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="requestDetailsBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-light"></div>
                    <p class="mt-2">جاري تحميل التفاصيل...</p>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

<!-- Mandate Details Modal -->
<div class="modal fade" id="mandateDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="background: #1c2b3e; color: #fff; border: 1px solid rgba(255,255,255,0.2);">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title">تفاصيل الانتداب</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="mandateDetailsBody">
                <div class="text-center py-5"><div class="spinner-border text-light"></div></div>
            </div>
            <div class="modal-footer border-top-0" id="mandateModalFooter"></div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Global variables
let isProcessingAttendance = false;
let workTimerInterval = null;
let mySalaryChart = null;
let allApprovals = [];
let currentMode = 'mandate';

// Toast function
function showToast(message, type = 'info') {
    console.log('💬 Showing toast:', message);
    
    document.querySelectorAll('.toast-notification').forEach(toast => toast.remove());
    
    const toast = document.createElement('div');
    toast.className = `toast-notification ${type === 'success' ? 'toast-success' : type === 'error' ? 'toast-error' : ''}`;
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas ${type === 'success' ? 'fa-check-circle text-success' : type === 'error' ? 'fa-exclamation-circle text-danger' : 'fa-info-circle text-info'} me-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 5000);
}

// Attendance functions
async function handleAttendanceClick() {
    if (isProcessingAttendance) {
        console.log('⏳ Attendance already processing, skipping...');
        return;
    }
    
    console.log('🚀 Starting attendance process...');
    isProcessingAttendance = true;
    
    const button = document.getElementById('attendanceToggleButton');
    const originalHTML = button.innerHTML;
    
    // Show loading state immediately
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري طلب الموقع...';
    button.disabled = true;
    button.style.pointerEvents = 'none';
    
    try {
        console.log('📍 Step 1: Testing geolocation availability...');
        
        // Check if geolocation is available
        if (!navigator.geolocation) {
            throw new Error('المتصفح لا يدعم خدمة الموقع الجغرافي');
        }
        
        console.log('📍 Step 2: Getting current position...');
        
        // Get location with better error handling
        const position = await getLocationWithTimeout();
        console.log('📍 Location obtained:', position.coords);
        
        // Send to server
        await sendAttendanceToServer(position.coords);
        
    } catch (error) {
        console.error('❌ Error in attendance process:', error);
        showToast('❌ ' + error.message, 'error');
    } 
    finally {
        // Always reset processing flag and button
        isProcessingAttendance = false;
        
        setTimeout(() => {
            button.innerHTML = originalHTML;
            button.disabled = false;
            button.style.pointerEvents = 'auto';
            console.log('✅ Button reset completed');
            
            // Update attendance box to reflect new state
            updateAttendanceBox();
        }, 2000);
    }
}

// Improved geolocation function with timeout
function getLocationWithTimeout() {
    return new Promise((resolve, reject) => {
        let timeoutReached = false;
        
        const timeoutId = setTimeout(() => {
            timeoutReached = true;
            reject(new Error('انتهت مهلة طلب الموقع. يرجى المحاولة مرة أخرى.'));
        }, 10000);
        
        navigator.geolocation.getCurrentPosition(
            (position) => {
                if (!timeoutReached) {
                    clearTimeout(timeoutId);
                    resolve(position);
                }
            },
            (error) => {
                if (!timeoutReached) {
                    clearTimeout(timeoutId);
                    let message = 'خطأ في الحصول على الموقع';
                    
                    switch(error.code) {
                        case 1: // PERMISSION_DENIED
                            message = 'تم رفض الوصول إلى الموقع. يرجى السماح بالوصول إلى الموقع في إعدادات المتصفح ثم المحاولة مرة أخرى.';
                            break;
                        case 2: // POSITION_UNAVAILABLE
                            message = 'معلومات الموقع غير متاحة. يرجى التأكد من تشغيل خدمة الموقع.';
                            break;
                        case 3: // TIMEOUT
                            message = 'انتهت مهلة طلب الموقع. يرجى المحاولة مرة أخرى.';
                            break;
                        default:
                            message = 'حدث خطأ غير متوقع في خدمة الموقع.';
                    }
                    reject(new Error(message));
                }
            },
            {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0
            }
        );
    });
}

// Send attendance with coordinates
async function sendAttendanceToServer(coords) {
    console.log('🌐 Sending attendance data to server...', coords);
    
    const response = await fetch("<?php echo site_url('users2/toggle_attendance'); ?>", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `latitude=${coords.latitude}&longitude=${coords.longitude}&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>`
    });
    
    const result = await response.json();
    console.log('📡 Server response:', result);
    
    if (result.status === 'success') {
        showToast('✅ ' + result.message, 'success');
    } else {
        throw new Error(result.message || 'حدث خطأ في الخادم');
    }
}

function updateAttendanceBox() {
    // Add this check at the very beginning
    <?php if($this->session->userdata('username') == '1018'): ?>
        console.log('User 1018 - hiding attendance button');
        const toggleButton = document.getElementById('attendanceToggleButton');
        if (toggleButton) {
            toggleButton.style.display = 'none';
        }
        // Optionally hide the entire attendance box or just disable it
        return; // Exit the function early for user 1018
    <?php endif; ?>

    fetch("<?php echo site_url('users2/get_today_attendance_summary'); ?>")
        .then(res => res.json())
        .then(data => {
            console.log('📊 Attendance data received:', data);
            if (data.status === 'success' && data.data) {
                const p = data.data;
                const statusText = document.getElementById('attendanceStatusText');
                const toggleButton = document.getElementById('attendanceToggleButton');
                const workTimeCounter = document.getElementById('workTimeCounter');

                // Clear any existing timer
                if (workTimerInterval) {
                    clearInterval(workTimerInterval);
                    workTimerInterval = null;
                }

                if (p.isCurrentlyCheckedIn) {
                    // USER IS CURRENTLY CHECKED IN
                    const checkInTime = new Date(p.lastCheckInTime).toLocaleTimeString('ar-SA', { 
                        hour: '2-digit', 
                        minute: '2-digit' 
                    });
                    
                    statusText.textContent = `في الدوام (الحضور: ${checkInTime})`;
                    toggleButton.innerHTML = '<i class="fas fa-sign-out-alt me-2"></i>تسجيل الانصراف';
                    
                    // Start the timer using the last check-in time from the server
                    startWorkTimer(p.lastCheckInTime);

                } else if (p.hasCheckInToday) {
                    // USER HAS WORKED TODAY BUT IS CURRENTLY CHECKED OUT
                    workTimeCounter.textContent = p.totalWorkDuration || '00:00:00';
                    statusText.textContent = "اكتمل الدوام لهذا اليوم";
                    toggleButton.innerHTML = '<i class="fas fa-map-marker-alt me-2"></i>تسجيل الحضور';
                } else {
                    // USER HAS NOT CHECKED IN AT ALL TODAY
                    workTimeCounter.textContent = '00:00:00';
                    statusText.textContent = "لم يتم تسجيل الحضور بعد";
                    toggleButton.innerHTML = '<i class="fas fa-map-marker-alt me-2"></i>تسجيل الحضور';
                }

                toggleButton.disabled = false;
                
            } else {
                console.error('❌ No data received from server');
                document.getElementById('workTimeCounter').textContent = '--:--:--';
                document.getElementById('attendanceStatusText').textContent = "خطأ في تحميل الحالة";
            }
        })
        .catch(error => {
            console.error('❌ Attendance fetch error:', error);
            document.getElementById('workTimeCounter').textContent = '--:--:--';
            document.getElementById('attendanceStatusText').textContent = "خطأ في تحميل البيانات";
        });
}

function startWorkTimer(startTime) {
    if (workTimerInterval) {
        clearInterval(workTimerInterval);
    }

    const startTimestamp = new Date(startTime).getTime();
    const workTimeCounter = document.getElementById('workTimeCounter');

    const updateTimer = () => {
        const now = Date.now();
        const elapsedMilliseconds = now - startTimestamp;
        
        if (elapsedMilliseconds < 0) {
            workTimeCounter.textContent = '00:00:00';
            return;
        }
        
        const totalSeconds = Math.floor(elapsedMilliseconds / 1000);
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;

        workTimeCounter.textContent = [
            String(hours).padStart(2, '0'),
            String(minutes).padStart(2, '0'),
            String(seconds).padStart(2, '0')
        ].join(':');
    };

    updateTimer(); // Update immediately
    workTimerInterval = setInterval(updateTimer, 1000); // Update every second
}

// Request approval functions
window.approveRequest = async function(requestId, type = '') {
    if (!confirm('هل أنت متأكد من الموافقة على هذا الطلب؟')) return;
    
    console.log('🟢 Approving request:', {requestId, type});
    
    // Visual Feedback
    const btn = document.querySelector(`button[onclick*="approveRequest(${requestId}"]`);
    if(btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    }
    
    showToast('⏳ جاري معالجة الموافقة...', 'info');

    try {
        // Prepare Form Data
        const formData = new FormData();
        formData.append('<?php echo $this->security->get_csrf_token_name(); ?>', '<?php echo $this->security->get_csrf_hash(); ?>');
        
        // Add type parameter if provided
        if (type) {
            formData.append('type', type);
        }

        // Build URL - check if it's a number or string
        let url = `<?php echo site_url('users2/approve_request'); ?>/${requestId}`;
        
        console.log('📤 Sending approval to:', url);
        console.log('📦 Sending data:', {type: type});

        // Send Request
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        console.log('📥 Server response:', result);
        
        if (result.status === 'success') {
            showToast('✅ ' + result.message, 'success');
            // Reload approvals
            setTimeout(() => {
                loadApprovals();
            }, 1000);
        } else {
            showToast('❌ ' + (result.message || 'خطأ في السيرفر'), 'error');
            if(btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check me-1"></i>موافقة';
            }
        }
    } catch (error) {
        console.error('❌ Approval error:', error);
        showToast('❌ خطأ في الاتصال بالشبكة', 'error');
        if(btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check me-1"></i>موافقة';
        }
    }
};

window.rejectRequest = async function(requestId, type = '') {
    const reason = prompt('يرجى إدخال سبب الرفض:');
    if (reason === null) return; 
    
    showToast('⏳ جاري المعالجة...', 'info');

    try {
        const formData = new FormData();
        formData.append('reason', reason);
        formData.append('<?php echo $this->security->get_csrf_token_name(); ?>', '<?php echo $this->security->get_csrf_hash(); ?>');
        if (type) formData.append('type', type);

        const response = await fetch(`<?php echo site_url('users2/reject_request'); ?>/${requestId}`, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            showToast('✅ ' + result.message, 'success');
            // Reload approvals
            setTimeout(() => {
                loadApprovals();
            }, 1000);
        } else {
            showToast('❌ ' + (result.message || 'خطأ في السيرفر'), 'error');
        }
    } catch (error) {
        console.error(error);
        showToast('❌ خطأ في الاتصال', 'error');
    }
};

// Request details function - FIXED
// Request details function - FIXED for your backend
window.showRequestDetails = async function(orderId, type = '') {
    console.log("📞 showRequestDetails called with:", {orderId, type});
    
    try {
        // 1. Get modal elements
        const modal = document.getElementById('requestDetailsModal');
        const modalBody = document.getElementById('requestDetailsBody');
        
        if (!modal || !modalBody) {
            console.error('❌ Modal elements not found');
            showToast('❌ عناصر النافذة غير موجودة', 'error');
            return;
        }
        
        // 2. Show loading
        modalBody.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary"></div>
                <p class="mt-2">جاري تحميل تفاصيل الطلب...</p>
            </div>
        `;
        
        // 3. Show modal
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
        
        // 4. Fetch data from server - USING CORRECT URL FOR YOUR BACKEND
        console.log("🌐 جاري جلب البيانات من السيرفر...");
        
        // Build URL exactly as your backend expects
        const baseUrl = "<?php echo site_url('users2/get_request_details'); ?>";
        let url = `${baseUrl}?id=${orderId}`;
        
        // Add type parameter if provided
        if (type && type !== '') {
            url += `&type=${type}`;
        }
        
        console.log("🌐 Request URL:", url);
        
        const response = await fetch(url);
        
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        
        const result = await response.json();
        console.log("📥 استجابة السيرفر:", result);
        
        if (result.status === 'success') {
            const data = result.data;
            
            // Format the data properly
            let detailsHtml = `
                <div class="request-details-container">
                    <div class="card bg-dark border-0 mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">الطلب #${data.id || orderId}</h5>
                                <span class="badge ${getStatusBadgeClass(data.status)}">${getStatusText(data.status)}</span>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <small class="text-muted d-block">نوع الطلب:</small>
                                        <h6>${data.order_name || getRequestTypeText(type) || 'غير محدد'}</h6>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <small class="text-muted d-block">تاريخ الطلب:</small>
                                        <h6>${data.date || data.created_at || 'غير محدد'}</h6>
                                    </div>
                                </div>
                            </div>`;
            
            // Employee info
            if (data.emp_name || data.subscriber_name) {
                detailsHtml += `
                    <div class="mb-3">
                        <small class="text-muted d-block">اسم الموظف:</small>
                        <h6>${data.emp_name || data.subscriber_name}</h6>
                    </div>`;
            }
            
            // Department info
            if (data.department) {
                detailsHtml += `
                    <div class="mb-3">
                        <small class="text-muted d-block">القسم:</small>
                        <h6>${data.department}</h6>
                    </div>`;
            }
            
            // Add specific details based on request type
            if (type === '5' || data.vac_start) {
                detailsHtml += `
                    <div class="mb-3">
                        <small class="text-muted d-block">فترة الإجازة:</small>
                        <h6>${data.vac_start || 'غير محدد'} إلى ${data.vac_end || 'غير محدد'}</h6>
                    </div>`;
                
                if (data.vac_days_count) {
                    detailsHtml += `
                        <div class="mb-3">
                            <small class="text-muted d-block">عدد الأيام:</small>
                            <h6>${data.vac_days_count} يوم</h6>
                        </div>`;
                }
                
                if (data.vac_reason) {
                    detailsHtml += `
                        <div class="mb-3">
                            <small class="text-muted d-block">سبب الإجازة:</small>
                            <p class="mb-0" style="white-space: pre-wrap;">${data.vac_reason}</p>
                        </div>`;
                }
                if (data.delegation_employee_id) {
                    // إذا كان الاسم قادماً من السيرفر سنعرضه، وإلا سنعرض الرقم كحل مؤقت
                    const delegateName = data.delegation_employee_name ? data.delegation_employee_name : data.delegation_employee_id;
                    
                    detailsHtml += `
                        <div class="mb-3">
                            <small class="text-muted d-block">الموظف المفوض (البديل):</small>
                            <h6 class="mb-0 text-info"><i class="fas fa-user-shield me-1"></i> ${delegateName}</h6>
                        </div>`;
                }
            }
            
            if (type === '2' || data.correction_date) {
                detailsHtml += `
                    <div class="mb-3">
                        <small class="text-muted d-block">تاريخ التصحيح:</small>
                        <h6>${data.correction_date || 'غير محدد'}</h6>
                    </div>`;
                
                if (data.in_time || data.out_time) {
                    detailsHtml += `
                        <div class="mb-3">
                            <small class="text-muted d-block">التوقيت المصحح:</small>
                            <h6>دخول: ${data.in_time || '--'} | خروج: ${data.out_time || '--'}</h6>
                        </div>`;
                }
                
                if (data.note) {
                    detailsHtml += `
                        <div class="mb-3">
                            <small class="text-muted d-block">ملاحظات التصحيح:</small>
                            <p class="mb-0" style="white-space: pre-wrap;">${data.note}</p>
                        </div>`;
                }
            }
            
            if (type === '3' || data.ot_date) {
                detailsHtml += `
                    <div class="mb-3">
                        <small class="text-muted d-block">تاريخ العمل الإضافي:</small>
                        <h6>${data.ot_date || 'غير محدد'}</h6>
                    </div>`;
                
                if (data.ot_hours) {
                    detailsHtml += `
                        <div class="mb-3">
                            <small class="text-muted d-block">عدد الساعات:</small>
                            <h6>${data.ot_hours} ساعة</h6>
                        </div>`;
                }
                if (data.ot_amount) {
                    detailsHtml += `
                        <div class="mb-3">
                            <small class="text-muted d-block"> المبلغ المتوقع:</small>
                            <h6>${data.ot_amount}ريال </h6>
                        </div>`;
                }
                
                if (data.paid !== undefined) {
                    detailsHtml += `
                        <div class="mb-3">
                            <small class="text-muted d-block">الحالة المالية:</small>
                            <h6>${data.paid === '1' || data.paid === 1 ? 'مدفوع' : 'غير مدفوع'}</h6>
                        </div>`;
                }
                
                if (data.reason) {
                    detailsHtml += `
                        <div class="mb-3">
                            <small class="text-muted d-block">السبب:</small>
                            <p class="mb-0" style="white-space: pre-wrap;">${data.reason}</p>
                        </div>`;
                }
            }
            
            // End of Service (EOS) details - type 8
            if (type === '8') {
                detailsHtml += `
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-hand-holding-usd me-2"></i>طلب نهاية خدمة</h6>
                    </div>`;
                
                if (data.effective_date) {
                    detailsHtml += `
                        <div class="mb-3">
                            <small class="text-muted d-block">تاريخ نهاية الخدمة:</small>
                            <h6>${data.effective_date}</h6>
                        </div>`;
                }
                
                if (data.total_amount) {
                    detailsHtml += `
                        <div class="mb-3">
                            <small class="text-muted d-block">إجمالي المبلغ:</small>
                            <h6 class="text-success">${data.total_amount} ريال</h6>
                        </div>`;
                }
            }
            
            // Clearance details - type 9
            if (type == 9) {
    // Header
    detailsHtml += `
        <div class="alert alert-secondary bg-opacity-25 border-secondary">
            <h6><i class="fas fa-tasks me-2"></i>تفاصيل مهمة العمل</h6>
        </div>`;

    // Mission Date
    if (data.mission_date) {
        detailsHtml += `
            <div class="mb-3">
                <small class="text-muted d-block">تاريخ المهمة:</small>
                <h6>${data.mission_date}</h6>
            </div>`;
    }

    // Mission Time
    if (data.mission_start_time) {
        detailsHtml += `
            <div class="mb-3">
                <small class="text-muted d-block">وقت المهمة:</small>
                <h6 dir="ltr" class="text-end">${data.mission_start_time} - ${data.mission_end_time || '...'}</h6>
            </div>`;
    }

    // Mission Type
    if (data.mission_type) {
        detailsHtml += `
            <div class="mb-3">
                <small class="text-muted d-block">نوع المهمة:</small>
                <h6>${data.mission_type}</h6>
            </div>`;
    }

    // Mission Note
    if (data.mission_note) {
        detailsHtml += `
            <div class="mb-3">
                <small class="text-muted d-block">ملاحظات المهمة:</small>
                <p class="mb-0 text-white">${data.mission_note}</p>
            </div>`;
    }
}

// 2. Logic for Clearance (String 'clearance')
else if (type == 'clearance') {
    detailsHtml += `
        <div class="alert alert-info">
            <h6><i class="fas fa-file-signature me-2"></i>طلب إخلاء طرف</h6>
        </div>
        <div class="mb-3">
            <small class="text-muted d-block">تفاصيل:</small>
            <p>${data.task_name || data.note || 'لا يوجد تفاصيل'}</p>
        </div>`;
}
        // ---------------- ADD THIS NEW BLOCK ----------------
// Permission (الاستئذان) details - type 12
if (type == 12 || type == '12' || data.order_name === 'الاستئذان') {
    detailsHtml += `
        <div class="alert alert-primary bg-opacity-25 border-primary">
            <h6><i class="fas fa-clock me-2"></i>تفاصيل الاستئذان</h6>
        </div>`;

    if (data.permission_date) {
        detailsHtml += `
            <div class="mb-3">
                <small class="text-muted d-block">تاريخ الاستئذان:</small>
                <h6>${data.permission_date}</h6>
            </div>`;
    }

    if (data.permission_start_time || data.permission_end_time) {
        detailsHtml += `
            <div class="mb-3">
                <small class="text-muted d-block">وقت الاستئذان:</small>
                <h6 dir="ltr" class="text-end fw-bold">
                    <span class="text-success">${data.permission_start_time || ''}</span> 
                    <i class="fas fa-arrow-right mx-2 text-muted" style="font-size:0.8rem"></i> 
                    <span class="text-danger">${data.permission_end_time || ''}</span>
                </h6>
            </div>`;
    }

    if (data.permission_hours) {
        detailsHtml += `
            <div class="mb-3">
                <small class="text-muted d-block">المدة الزمنية:</small>
                <h6 class="text-primary fw-bold">${data.permission_hours} ساعة</h6>
            </div>`;
    }
}
// ---------------------------------------------------    
            // General notes
            if (data.note && !(type === '2' && data.note)) {
                detailsHtml += `
                    <div class="mb-3">
                        <small class="text-muted d-block">ملاحظات:</small>
                        <p class="mb-0" style="white-space: pre-wrap;">${data.note}</p>
                    </div>`;
            }
            
            // Attachments
            if (data.attachment || data.attachment_url) {
                const attachmentUrl = data.attachment_url || (data.attachment ? '<?php echo base_url("uploads/"); ?>' + data.attachment : '');
                detailsHtml += `
                    <div class="mb-3">
                        <small class="text-muted d-block">المرفقات:</small>
                        <a href="${attachmentUrl}" class="btn btn-sm btn-outline-light" target="_blank">
                            <i class="fas fa-paperclip me-1"></i> عرض المرفق
                        </a>
                    </div>`;
            }
            
            detailsHtml += `
                            <hr>
                            <div class="text-center mt-3">
                                <small class="text-muted">معرف الطلب: ${orderId} | النوع: ${type}</small>
                            </div>
                        </div>
                    </div>
                </div>`;
            
            modalBody.innerHTML = detailsHtml;
        } else {
            // Show error
            modalBody.innerHTML = `
                <div class="alert alert-danger">
                    <h5>❌ خطأ في تحميل البيانات</h5>
                    <p>${result.message || 'حدث خطأ غير معروف'}</p>
                    <p class="small">طلب #${orderId} | النوع: ${type}</p>
                    <div class="mt-2">
                        <button class="btn btn-sm btn-primary" onclick="testBackendConnection('${orderId}', '${type}')">
                            <i class="fas fa-vial me-1"></i> اختبار الاتصال بالخادم
                        </button>
                    </div>
                </div>
            `;
        }
        
    } catch (error) {
        console.error("❌ خطأ:", error);
        
        const modalBody = document.getElementById('requestDetailsBody');
        modalBody.innerHTML = `
            <div class="alert alert-danger">
                <h5>❌ خطأ في الاتصال</h5>
                <p>${error.message}</p>
                <p class="small">طلب #${orderId} | النوع: ${type}</p>
                <div class="mt-2">
                    <button class="btn btn-sm btn-primary" onclick="window.showRequestDetails('${orderId}', '${type}')">
                        إعادة المحاولة
                    </button>
                    <button class="btn btn-sm btn-outline-light ms-2" onclick="testBackendConnection('${orderId}', '${type}')">
                        <i class="fas fa-vial me-1"></i> اختبار الاتصال
                    </button>
                </div>
            </div>
        `;
    }
};

// Helper function to test backend connection
function testBackendConnection(orderId, type) {
    const modalBody = document.getElementById('requestDetailsBody');
    modalBody.innerHTML = `
        <div class="alert alert-info">
            <h5>🔍 اختبار الاتصال بالخادم</h5>
            <p>جاري اختبار الوصول إلى الخادم...</p>
            <div id="testResults"></div>
        </div>
    `;
    
    const testResults = document.getElementById('testResults');
    
    // Test the exact URL your backend expects
    const testUrl = "<?php echo site_url('users2/get_request_details'); ?>?id=" + orderId + (type ? "&type=" + type : "");
    
    testResults.innerHTML = `
        <div class="mb-2">
            <small class="text-muted">الرابط المستخدم:</small>
            <div class="p-2 bg-dark rounded small">${testUrl}</div>
        </div>
        <div class="text-center">
            <div class="spinner-border spinner-border-sm"></div>
        </div>
    `;
    
    fetch(testUrl)
        .then(response => {
            testResults.innerHTML += `
                <div class="mb-2">
                    <small class="text-muted">حالة الاستجابة:</small>
                    <div class="p-2 bg-dark rounded small">HTTP ${response.status} - ${response.statusText}</div>
                </div>
            `;
            return response.json();
        })
        .then(result => {
            testResults.innerHTML += `
                <div class="mb-2">
                    <small class="text-muted">نتيجة الخادم:</small>
                    <div class="p-2 bg-dark rounded small">${JSON.stringify(result, null, 2)}</div>
                </div>
            `;
            
            if (result.status === 'success') {
                testResults.innerHTML += `
                    <div class="alert alert-success mt-2">
                        ✅ تم الاتصال بالخادم بنجاح!
                        <button class="btn btn-sm btn-success mt-2" onclick="window.showRequestDetails('${orderId}', '${type}')">
                            عرض التفاصيل الآن
                        </button>
                    </div>
                `;
            } else {
                testResults.innerHTML += `
                    <div class="alert alert-warning mt-2">
                        ⚠️ الخادم استجاب ولكن هناك خطأ
                        <p class="small mb-0">${result.message || 'لا توجد بيانات'}</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            testResults.innerHTML += `
                <div class="alert alert-danger mt-2">
                    ❌ فشل الاتصال بالخادم
                    <p class="small mb-0">${error.message}</p>
                </div>
            `;
        });
}

// Also update the getRequestTypeText function to handle type 8 and 9
function getRequestTypeText(type) {
    const requestTypeMap = {
        '5': 'إجازة',
        '2': 'تصحيح بصمة', 
        '3': 'عمل إضافي',
        '1': 'استقالة',
        '7': 'طلب خطاب',
        '6': 'طلب عهدة',
        '4': 'مصاريف مالية',
        'Mandate': 'انتداب',
        '8': 'مستحقات نهاية الخدمة',
'9': 'مهمة عمل',          // 9 is now Work Mission
'clearance': 'إخلاء طرف'  // String is Clearance
    };
    return requestTypeMap[type] || type;
}

// Helper functions for request details
function getStatusText(status) {
    const statusMap = {
        '0': 'قيد المراجعة',
        '2': 'معتمد', 
        '3': 'مرفوض',
        '-1': 'ملغي',
        '-2': 'ملغي بواسطة الموارد البشرية'
    };
    return statusMap[status] || 'غير معروف';
}

function getStatusBadgeClass(status) {
    const statusClassMap = {
        '0': 'bg-warning text-dark',
        '2': 'bg-success',
        '3': 'bg-danger',
        '-1': 'bg-secondary',
        '-2': 'bg-dark'
    };
    return statusClassMap[status] || 'bg-secondary';
}

function getRequestTypeText(type) {
    const requestTypeMap = {
        '5': 'إجازة',
        '2': 'تصحيح بصمة', 
        '3': 'عمل إضافي',
        '1': 'استقالة',
        '7': 'طلب خطاب',
        '6': 'طلب عهدة',
        '4': 'مصاريف مالية',
        'Mandate': 'انتداب'
    };
    return requestTypeMap[type] || type;
}

// Approval Mode functions
function switchApprovalMode(mode) {
    console.log('🔄 Switching to mode:', mode, 'All approvals:', allApprovals);
    
    currentMode = mode;
    
    // Update button states
    const btnModeMandate = document.getElementById('btnModeMandate');
    const btnModeLeaves = document.getElementById('btnModeLeaves');
    const btnModeClearance = document.getElementById('btnModeClearance');
    const btnModeEOS = document.getElementById('btnModeEOS');
    // ADDED: New button definition
    const btnModeWorkMission = document.getElementById('btnModeWorkMission');
    
    // Reset all buttons
    [btnModeMandate, btnModeLeaves, btnModeClearance, btnModeEOS, btnModeWorkMission].forEach(btn => {
        if (btn) {
            btn.classList.remove('btn-primary', 'btn-info', 'btn-warning', 'btn-danger', 'btn-secondary', 'text-dark', 'active');
            btn.classList.add('btn-outline-light');
        }
    });
    
    // Activate selected button
    if (mode === 'mandate' && btnModeMandate) {
        btnModeMandate.classList.remove('btn-outline-light');
        btnModeMandate.classList.add('btn-primary', 'active');
    } else if (mode === 'leaves' && btnModeLeaves) {
        btnModeLeaves.classList.remove('btn-outline-light');
        btnModeLeaves.classList.add('btn-info', 'active');
    } else if (mode === 'clearance' && btnModeClearance) {
        btnModeClearance.classList.remove('btn-outline-light');
        btnModeClearance.classList.add('btn-warning', 'text-dark', 'active');
    } else if (mode === 'eos' && btnModeEOS) {
        btnModeEOS.classList.remove('btn-outline-light');
        btnModeEOS.classList.add('btn-danger', 'active');
    } else if (mode === 'work_mission' && btnModeWorkMission) {
        // ADDED: Active state for Work Mission
        btnModeWorkMission.classList.remove('btn-outline-light');
        btnModeWorkMission.classList.add('btn-secondary', 'active');
    }

    if (!allApprovals || allApprovals.length === 0) {
        console.log('⚠️ No approvals data available');
        renderApprovalList([]);
        return;
    }

    // Filter logic
    let filtered = allApprovals.filter(req => {
        const rType = String(req.type);
        console.log('🔍 Checking request:', {id: req.id, type: rType, order_name: req.order_name});
        
        if (mode === 'mandate') return rType === 'Mandate';
        
        // UPDATED: Clearance now checks for string 'clearance'
        if (mode === 'clearance') return rType == 'clearance'; 
        
        // ADDED: Work Mission checks for '9'
        if (mode === 'work_mission') return rType == '9';
        
        if (mode === 'eos') return rType == '8';
        
        // UPDATED: Leaves excludes all the above
        if (mode === 'leaves') return (rType !== 'Mandate' && rType != '9' && rType != '8' && rType != 'clearance');
        
        return true;
    });

    console.log('📋 Filtered requests count:', filtered.length, 'Mode:', mode);
    renderApprovalList(filtered);
}
// Render approval list
function renderApprovalList(data) {
    console.log('🎨 Rendering approval list with', data.length, 'items');
    
    const approvalCountDisplay = document.getElementById('approvalCountDisplay');
    const approvalsContainer = document.getElementById('approvalsContainer');
    
    if (!approvalsContainer) {
        console.error('❌ approvalsContainer not found');
        return;
    }
    
    if (!data || data.length === 0) {
        approvalsContainer.innerHTML = `
            <div class="text-center p-5 text-white-50">
                <i class="fas fa-inbox fa-3x mb-3"></i>
                <p class="mb-2">لا توجد طلبات لعرضها</p>
                <small class="text-muted">جرب تبديل نوع الفلتر لعرض المزيد</small>
            </div>`;
        
        if (approvalCountDisplay) {
            approvalCountDisplay.textContent = 'العدد: 0';
        }
        return;
    }
    
    if (approvalCountDisplay) {
        approvalCountDisplay.textContent = 'العدد: ' + data.length;
    }

    let html = '';
    data.forEach(req => {
        console.log('📄 Rendering request:', {id: req.id, type: req.type, order_name: req.order_name});
        
        // Check if this is a Mandate request
        const isMandate = (req.type === 'Mandate' || req.order_name === 'Mandate' ||req.order_name === 'إنتداب' || req.order_name === 'انتداب عمل');
        
        let actionBtn = '';
        
        if (isMandate) {
            actionBtn = `<div class="d-flex gap-2 w-100">
                <button class="btn btn-sm btn-primary flex-grow-1" onclick="openMandateDetails('${req.order_id || req.id}')">
                    <i class="fas fa-eye me-1"></i> عرض التفاصيل
                </button>
                <button class="btn btn-sm btn-success flex-grow-1" onclick="approveRequest('${req.order_id || req.id}', 'Mandate')">
                    <i class="fas fa-check me-1"></i> موافقة
                </button>
                <button class="btn btn-sm btn-danger flex-grow-1" onclick="rejectRequest('${req.order_id || req.id}', 'Mandate')">
                    <i class="fas fa-times me-1"></i> رفض
                </button>
            </div>`;
        } else {
            actionBtn = `<div class="d-flex gap-2 w-100">
                <button class="btn btn-sm btn-success flex-grow-1" onclick="approveRequest('${req.id}', '${req.type}')">
                    <i class="fas fa-check me-1"></i> موافقة
                </button>
                <button class="btn btn-sm btn-danger flex-grow-1" onclick="rejectRequest('${req.id}', '${req.type}')">
                    <i class="fas fa-times me-1"></i> رفض
                </button>
                <button class="btn btn-sm btn-outline-light" onclick="window.showRequestDetails('${req.id}', '${req.type}')">
                    <i class="fas fa-eye me-1"></i> تفاصيل
                </button>
            </div>`;
        }

        html += `
        <div class="card mb-2 bg-white bg-opacity-10 border border-secondary text-light">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between mb-2">
                    <h6 class="mb-0 fw-bold text-warning">
                        <i class="fas fa-user-circle me-1"></i> ${req.emp_name || 'غير محدد'}
                    </h6>
                    <span class="badge bg-secondary">${req.date || 'غير محدد'}</span>
            <span class="badge bg-secondary">${req.id || 'غير محدد'}</span>
                </div>
                <div class="small text-white-50 mb-2">${req.order_name || 'طلب'}</div>
                ${req.note ? `<div class="p-2 rounded bg-black bg-opacity-25 small mb-2">${req.note}</div>` : ''}
                <div class="mt-2">${actionBtn}</div>
            </div>
        </div>`;
    });
    
    approvalsContainer.innerHTML = html;
    console.log('✅ Approval list rendered');
}

// Load approvals
async function loadApprovals() {
    console.log('📥 Loading approvals...');
    
    const approvalsContainer = document.getElementById('approvalsContainer');
    if (!approvalsContainer) {
        console.error('❌ approvalsContainer element not found');
        return;
    }
    
    approvalsContainer.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-light"></div>
            <p class="text-light mt-2">جاري تحميل طلبات الموافقة...</p>
        </div>`;

    try {
        const response = await fetch('<?php echo site_url("users2/get_my_approvals"); ?>');
        const result = await response.json();

        console.log('📥 Approvals API response:', result);

        if (result.status === 'success') {
            allApprovals = result.data || [];
            console.log('✅ Loaded', allApprovals.length, 'approvals');
            
            // Update badge
            const mainPendingBadge = document.getElementById('mainPendingBadge');
            if (mainPendingBadge) {
                mainPendingBadge.textContent = allApprovals.length;
            }
            
            // Apply the current mode filter
            switchApprovalMode(currentMode);
            
        } else {
            console.error('❌ API returned error:', result.message);
            approvalsContainer.innerHTML = '<div class="text-center text-danger p-4">فشل تحميل البيانات: ' + result.message + '</div>';
        }
    } catch (e) {
        console.error('❌ Error loading approvals:', e);
        approvalsContainer.innerHTML = '<div class="text-center text-muted p-4">خطأ في الاتصال</div>';
    }
}

// Mandate details function
window.openMandateDetails = function(id) {
    console.log('🔍 Opening mandate details for ID:', id);
    
    const modal = document.getElementById('mandateDetailsModal');
    const modalBody = document.getElementById('mandateDetailsBody');
    const modalFooter = document.getElementById('mandateModalFooter');
    
    if (modal && modalBody) {
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
        
        modalBody.innerHTML = '<div class="text-center"><div class="spinner-border text-light"></div><p class="mt-2">جاري تحميل التفاصيل...</p></div>';
        
        // Use fetch instead of jQuery
        fetch(`<?php echo site_url("users2/get_request_details"); ?>?id=${id}&type=Mandate&_=${new Date().getTime()}`)
            .then(response => response.json())
            .then(res => {
                console.log('✅ Mandate details response:', res);
                if(res.status == 'success') {
                    let d = res.data;
                    
                    // Build Legs HTML
                    let legs = '';
                    if(d.destinations) {
                        d.destinations.forEach(leg => {
                            let badge = leg.leg_mode == 'air' 
                                ? '<i class="fas fa-plane text-info"></i>' 
                                : '<i class="fas fa-car text-warning"></i>';
                            legs += `
                            <div class="p-2 mb-2 rounded bg-dark bg-opacity-25 d-flex justify-content-between align-items-center">
                                <span class="small">${leg.from_city} <i class="fas fa-arrow-left mx-1 text-secondary"></i> ${leg.to_city}</span>
                                <span>${badge} <small>${leg.distance_km}km</small></span>
                            </div>`;
                        });
                    }

                    // Body
                    modalBody.innerHTML = `
                        <div class="text-center mb-3">
                            <h5 class="fw-bold mb-1">${d.subscriber_name || 'غير محدد'}</h5>
                            <span class="badge bg-secondary">${d.department || 'غير محدد'}</span>
                        </div>
                        <div class="row g-2 text-center mb-3">
                            <div class="col-6"><div class="p-2 border rounded border-secondary"><small class="text-muted d-block">المدة</small>${d.duration_days || '0'} أيام</div></div>
                            <div class="col-6"><div class="p-2 border rounded border-secondary"><small class="text-muted d-block">البدء</small>${d.start_date || 'غير محدد'}</div></div>
                        </div>
                        <div class="mb-3 border p-2 rounded border-secondary">
                            <small class="text-muted d-block">الغرض:</small>
                            <p class="small mb-0">${d.reason || 'لا يوجد'}</p>
                        </div>
                        ${legs ? `<h6 class="text-warning small mb-2">خط السير:</h6>${legs}` : ''}
                        <div class="d-flex justify-content-between fw-bold text-success border-top border-secondary pt-2 mt-2">
                            <span>الإجمالي المستحق:</span>
                            <span>${d.total_amount || '0'} SAR</span>
                        </div>
                    `;

                    // Footer Actions
                    if (modalFooter) {
                        modalFooter.innerHTML = `
                            <button class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                            <button class="btn btn-danger" onclick="rejectRequest('${d.id}', 'Mandate')">رفض</button>
                            <button class="btn btn-success" onclick="approveRequest('${d.id}', 'Mandate')">اعتماد</button>
                        `;
                    }
                } else {
                    modalBody.innerHTML = '<p class="text-center text-danger">فشل تحميل التفاصيل: ' + (res.message || 'Unknown error') + '</p>';
                }
            })
            .catch(error => {
                console.error('❌ Mandate details error:', error);
                modalBody.innerHTML = '<p class="text-center text-danger">خطأ في الاتصال بالخادم</p>';
            });
    }
};

// Main application initialization
document.addEventListener('DOMContentLoaded', function() {
    console.log('📍 Application initializing...');

    // Initialize attendance system
    const attendanceButton = document.getElementById('attendanceToggleButton');
    if (attendanceButton) {
        console.log('✅ Attendance button found, attaching click handler...');
        attendanceButton.onclick = function(e) {
            console.log('🟡 Attendance button clicked!');
            e.preventDefault();
            e.stopPropagation();
            
            if (!isProcessingAttendance) {
                handleAttendanceClick();
            } else {
                console.log('🟡 Attendance already processing, ignoring click');
            }
        };
        
        attendanceButton.disabled = false;
        attendanceButton.style.pointerEvents = 'auto';
        attendanceButton.style.cursor = 'pointer';
    } else {
        console.error('❌ Attendance button not found!');
    }
    
    // Load initial attendance state
    updateAttendanceBox();

    // Application constants
    const PUBLIC_HOLIDAYS = <?php echo json_encode($public_holidays ?? []); ?>;
    const employeeBalances = <?php echo json_encode($balances ?? []); ?>;
    const isHrUser = <?php echo json_encode($is_hr_user ?? false); ?>;
    const leaveTypesData = <?php echo json_encode($leave_types ?? []); ?>;
    const SATURDAY_ASSIGNMENTS = <?php echo json_encode($saturday_assignments ?? []); ?>;

    const q = (selector) => document.querySelector(selector);

    // Load last three requests
    function loadLastThreeRequests() {
        const container = q('#lastRequestsContainer'); 
        if (!container) return;
        
        container.innerHTML = `<li class="list-group-item text-center p-3"><div class="spinner-border spinner-border-sm"></div></li>`;
        fetch("<?php echo site_url('users2/get_last_requests'); ?>")
            .then(res => res.json()).then(data => {
                container.innerHTML = '';
                if (data.status === 'success' && data.data.length > 0) {
                    data.data.forEach(req => {
                        let sClass = 'bg-secondary', sText = 'غير معروف', dtls = '';
                        switch (String(req.status)) { 
                            case '0': sClass = 'bg-warning text-dark'; sText = 'قيد المراجعة'; break; 
                            case '2': sClass = 'bg-success'; sText = 'معتمد'; break; 
                            case '3': sClass = 'bg-danger'; sText = 'مرفوض'; break; 
                        }
                        switch (String(req.type)) { 
                            case '2': dtls = `تصحيح ليوم: ${req.correction_date || ''}`; break; 
                            case '5': dtls = `إجازة من ${req.vac_start || ''} إلى ${req.vac_end || ''}`; break; 
                            case '3': dtls = `عمل إضافي - ${req.ot_hours || 'N/A'} ساعات`; break; 
                            default: dtls = req.note || ''; 
                        }
                        container.innerHTML += `<li class="list-group-item d-flex justify-content-between align-items-center"><div><h6 class="mb-1">${req.order_name || 'طلب'}</h6><small class="request-details text-muted">${dtls}</small></div><span class="badge ${sClass}">${sText}</span></li>`;
                    });
                } else { 
                    container.innerHTML = '<li class="list-group-item text-center text-muted">لا توجد طلبات حديثة.</li>'; 
                }
            }).catch(console.error);
    }

    // Form Validation Logic
    function setupFormValidation(formId, validationFunction, errorAlertId) {
        const form = document.getElementById(formId); 
        if (!form) return;
        
        const submitBtn = form.querySelector('button[type="submit"]');
        const errorAlertBox = document.getElementById(errorAlertId);
        
        function showErrors(errors) { 
            if (!errorAlertBox) return; 
            if (errors.length > 0) { 
                errorAlertBox.innerHTML = '<ul><li>' + errors.join('</li><li>') + '</li></ul>'; 
                errorAlertBox.classList.remove('d-none'); 
            } else { 
                errorAlertBox.innerHTML = ''; 
                errorAlertBox.classList.add('d-none'); 
            } 
        }
        
        function validate() { 
            const errors = []; 
            validationFunction(errors); 
            showErrors(errors); 
            if (submitBtn) submitBtn.disabled = errors.length > 0; 
            return errors.length === 0; 
        }
        
        form.addEventListener('input', validate); 
        form.addEventListener('change', validate);
        form.addEventListener('submit', (e) => { 
            if (!validate()) e.preventDefault(); 
        });
        validate();
    }

    function validateFingerprint(errors) { 
        if (!q('#fingerprintForm input[name="fp[date]"]').value) errors.push('تاريخ التصحيح مطلوب.'); 
        if (!q('#fingerprintForm input[name="fp[in_time]"]').value && !q('#fingerprintForm input[name="fp[out_time]"]').value) errors.push('يجب إدخال وقت الحضور أو الانصراف.'); 
        if (!q('#fingerprintForm select[name="fp[reason]"]').value) errors.push('سبب التصحيح مطلوب.'); 
        if (!q('#fingerprintForm input[name="fp[details]"]').value.trim()) errors.push('تفاصيل السبب مطلوبة.'); 
    }

    function validateVacation(errors) {
        const vacType = q('#vacationForm #vacMainType').value;
        if (!vacType) errors.push('نوع الإجازة مطلوب.');

        const dayType = q('input[name="vac[day_type]"]:checked')?.value;
        
        let requested = 0;
        if (dayType === 'full') {
            const from = q('#vacFrom').value,
                to = q('#vacTo').value;
            if (!from) errors.push('تاريخ بداية الإجازة مطلوب.');
            if (!to) errors.push('تاريخ نهاية الإجازة مطلوب.');
            if (from && to && to < from) errors.push('تاريخ النهاية يجب أن يكون بعد البداية.');
            
            requested = parseFloat(q('#vacDaysCount').value) || 0;
        } else if (dayType === 'half') {
            if (!q('#vacHalfDate').value) errors.push('تاريخ نصف الإجازة مطلوب.');
            requested = 0.5;
        }

        // Validation Logic
        if (vacType === 'annual') {
            if (!isHrUser) {
                const available = (employeeBalances[vacType] && typeof employeeBalances[vacType].remaining !== 'undefined') 
                                    ? parseFloat(employeeBalances[vacType].remaining) 
                                    : 0;
                
                if (requested > (available + 0.01)) {
                    errors.push(`رصيد الإجازات السنوية غير كافٍ. المطلوب: ${requested}, المتاح: ${available}`);
                }
            }
        } else if (vacType === 'sick') {
            const leaveTypeInfo = leaveTypesData.find(lt => lt.slug === vacType);
            const defaultBalance = (leaveTypeInfo && leaveTypeInfo.default_balance) ? parseFloat(leaveTypeInfo.default_balance) : 90;
            
            if (requested > defaultBalance) {
                 errors.push(`لا يمكن طلب أكثر من ${defaultBalance} يوم إجازة مرضية. المطلوب: ${requested}`);
            }

        } else if (vacType !== 'unpaid') {
            const leaveTypeInfo = leaveTypesData.find(lt => lt.slug === vacType);
            
            if (leaveTypeInfo && leaveTypeInfo.default_balance) {
                const defaultBalance = parseFloat(leaveTypeInfo.default_balance);
                
                if (requested > defaultBalance) {
                    errors.push(`لا يمكن طلب أكثر من ${defaultBalance} يوم لهذا النوع من الإجازات. المطلوب: ${requested}`);
                }
            }
        }

        if (!q('input[name="vac[reason]"]').value.trim()) errors.push('سبب الإجازة مطلوب.');
        
        if (vacType === 'sick' && (!q('#vacAttachment').files || q('#vacAttachment').files.length === 0)) {
            errors.push('التقرير الطبي إلزامي.');
        }

        const errorAlertBox = document.getElementById('vacErrorAlert');
        if (errorAlertBox) {
            if (errors.length > 0) {
                errorAlertBox.innerHTML = '<ul><li>' + errors.join('</li><li>') + '</li></ul>';
                errorAlertBox.classList.remove('d-none');
            } else {
                errorAlertBox.innerHTML = '';
                errorAlertBox.classList.add('d-none');
            }
        }
        
        const submitBtn = q('#vacationForm button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = errors.length > 0;
        }
    }

    function validateOvertime(errors) { 
        if (!q('#overtimeForm input[name="ot[date]"]').value) errors.push('تاريخ العمل الإضافي مطلوب.'); 
        const hours = q('#overtimeForm input[name="ot[hours]"]').value; 
        if (!hours || parseFloat(hours) <= 0) errors.push('عدد الساعات يجب أن يكون رقماً صحيحاً.'); 
        if (!q('#overtimeForm input[name="ot[reason]"]').value.trim()) errors.push('سبب العمل الإضافي مطلوب.'); 
    }

    setupFormValidation('fingerprintForm', validateFingerprint, 'fpErrorAlert');
    setupFormValidation('vacationForm', validateVacation, 'vacErrorAlert');
    setupFormValidation('overtimeForm', validateOvertime, 'otErrorAlert');

    // Vacation Form Specific Logic
    const vacMainTypeSelect = q('#vacMainType'), vacFromInput = q('#vacFrom'), vacToInput = q('#vacTo');

    function calculateLeaveDays() {
        const fromVal = q('#vacFrom').value;
        const toVal = q('#vacTo').value;
        const msgEl = q('#vacDaysMsg'),
            countEl = q('#vacDaysCount');

        if (!fromVal || !toVal || toVal < fromVal) {
            msgEl.innerHTML = '';
            countEl.value = 0;
            return;
        }

        const parseDateAsLocal = (dateStr) => {
            const parts = dateStr.split('-');
            return new Date(parts[0], parts[1] - 1, parts[2]);
        };
        
        const formatDateAsISO = (dateObj) => {
             const y = dateObj.getFullYear();
             const m = String(dateObj.getMonth() + 1).padStart(2, '0');
             const d = String(dateObj.getDate()).padStart(2, '0');
             return `${y}-${m}-${d}`;
        };

        let count = 0;
        let cur = parseDateAsLocal(fromVal);
        let end = parseDateAsLocal(toVal);
        
        let assignedSaturdayIncluded = false;

        while (cur <= end) {
            const day = cur.getDay();
            const dateStr = formatDateAsISO(cur);

            const isWeekend = (day === 5 || day === 6);
            const isPublicHoliday = PUBLIC_HOLIDAYS.includes(dateStr);
            
            const isAssignedSaturday = (
                day === 6 && 
                Array.isArray(SATURDAY_ASSIGNMENTS) && 
                SATURDAY_ASSIGNMENTS.includes(dateStr)
            );

            if (!isPublicHoliday && (!isWeekend || isAssignedSaturday)) {
                count++;
                if (isAssignedSaturday) {
                    assignedSaturdayIncluded = true;
                }
            }

            cur.setDate(cur.getDate() + 1);
        }

        let message = `مجموع أيام العمل: <b class="text-primary">${count}</b> يوم`;
        
        if (assignedSaturdayIncluded) {
            message += ' (شامل أيام السبت المكلفة)';
        }

        msgEl.innerHTML = message;
        countEl.value = count;

        if (typeof validateVacation === 'function') {
            validateVacation([]);
        }
    }

    function updateBalanceDisplay() {
        const type = vacMainTypeSelect.value;
        const balanceSpan = q('#vacBalanceDisplay');
        const durationContainer = q('#vacationDurationContainer');
        const fullDayRadio = q('#vacDayTypeFull');
        const attachmentLabel = q('#attachmentRequired');

        if (type === 'annual') {
            durationContainer.classList.remove('d-none');
        } else {
            durationContainer.classList.add('d-none');
            if (fullDayRadio && !fullDayRadio.checked) {
                fullDayRadio.checked = true;
            }
            toggleVacationFields();
        }

        const isSickLeave = (type === 'sick');
        attachmentLabel.classList.toggle('d-none', !isSickLeave);
        q('#vacAttachment').required = isSickLeave;
        
        balanceSpan.textContent = (type && employeeBalances[type] && typeof employeeBalances[type].remaining !== 'undefined') 
                                    ? employeeBalances[type].remaining 
                                    : '--';
        
        calculateLeaveDays();
        
        if (typeof validateVacation === 'function') {
            validateVacation([]);
        }
    }

    const dayTypeRadios = document.querySelectorAll('input[name="vac[day_type]"]');
    function toggleVacationFields() { 
        const selectedType = q('input[name="vac[day_type]"]:checked').value; 
        q('#vacFullDayRange').classList.toggle('d-none', selectedType !== 'full'); 
        q('#vacHalfDayFields').classList.toggle('d-none', selectedType !== 'half'); 
        if(selectedType === 'half') {
            q('#vacDaysCount').value = '0.5'; 
            q('#vacDaysMsg').innerHTML = `مجموع الأيام: <b class="text-primary">0.5</b> يوم`;
        } else {
            calculateLeaveDays();
        } 
        
        if (typeof validateVacation === 'function') {
            validateVacation([]);
        }
    }
    
    if (dayTypeRadios.length > 0) {
        dayTypeRadios.forEach(radio => radio.addEventListener('change', toggleVacationFields));
    }
    
    if(vacMainTypeSelect) { 
        vacMainTypeSelect.addEventListener('change', updateBalanceDisplay); 
        if (vacFromInput) vacFromInput.addEventListener('change', calculateLeaveDays); 
        if (vacToInput) vacToInput.addEventListener('change', calculateLeaveDays); 
        
        updateBalanceDisplay();
        toggleVacationFields();
    }

    // Calendar functionality
    const calendarGrid = document.getElementById('calendarGrid');
    const monthTitleEl = document.getElementById('calendarMonthTitle');
    const detailsCardEl = document.getElementById('detailsCard');
    const detailsDateTitleEl = document.getElementById('detailsDateTitle');
    const dayDetailsContentEl = document.getElementById('dayDetailsContent');
    
    let currentDate = new Date();

    async function fetchCalendarData(year, month) {
        if (!calendarGrid) return;
        
        calendarGrid.innerHTML = `<div class="text-center p-5 w-100" style="grid-column: 1 / 8;"><div class="spinner-border"></div></div>`;
        
        try {
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
        if (!calendarGrid) return;
        
        calendarGrid.innerHTML = '';
        if (monthTitleEl) {
            monthTitleEl.textContent = new Date(year, month - 1).toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
        }
    
        const daysInMonth = new Date(year, month, 0).getDate();
        const firstDayIndex = new Date(year, month - 1, 1).getDay();

        for (let i = 0; i < firstDayIndex; i++) {
            const emptyCell = document.createElement('div');
            emptyCell.className = 'day-box empty';
            calendarGrid.appendChild(emptyCell);
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const dateStr = `${year}-${String(month).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
            const dayData = data.find(d => d.date === dateStr) || { date: dateStr, status: 'unknown' };
            
            const cell = document.createElement('div');
            cell.className = 'day-box';
            cell.textContent = day;
            cell.dataset.date = dateStr;
            cell.dataset.details = JSON.stringify(dayData);
            
            cell.classList.add(`day-${dayData.status}`);
            if (new Date(dateStr).toDateString() === new Date().toDateString()) {
                cell.classList.add('day-today');
            }

            if (dayData.event_details && dayData.event_details.status == '2') {
                const dot = document.createElement('span');
                dot.className = 'event-dot';
                if (dayData.event_details.type === 'corr') {
                    dot.style.backgroundColor = 'var(--marsom-blue)';
                    dot.title = 'تصحيح بصمة معتمد';
                } else if (dayData.event_details.type === 'vac_half' || dayData.event_details.type === 'vac_full') {
                    dot.style.backgroundColor = '#1abc9c';
                    dot.title = dayData.status_text || 'إجازة معتمدة';
                }
                if (dot.style.backgroundColor) {
                    cell.appendChild(dot);
                }
            } else if (dayData.has_leave) {
                const dot = document.createElement('span');
                dot.className = 'event-dot';
                dot.style.backgroundColor = '#1abc9c';
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
        if (!detailsCardEl || !detailsDateTitleEl || !dayDetailsContentEl) return;
        
        detailsCardEl.style.display = 'block';
        detailsDateTitleEl.textContent = new Date(data.date + 'T00:00:00').toLocaleDateString('ar-SA', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

        let contentHtml = '';
        const eventDetails = data.event_details;

        if (data.status === 'present') {
            contentHtml = `
                <div class="detail-item">
                    <span class="label">وقت الدخول</span>
                    <span class="value">${data.check_in || '--:--'}</span>
                    ${(eventDetails && eventDetails.type === 'corr' && data.corrected_check_in) ? '<small class="text-white-50"> (تم التصحيح)</small>' : ''}
                </div>
                <div class="detail-item">
                    <span class="label">وقت الخروج</span>
                    <span class="value">${data.check_out || '--:--'}</span>
                     ${(eventDetails && eventDetails.type === 'corr' && data.corrected_check_out) ? '<small class="text-white-50"> (تم التصحيح)</small>' : ''}
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
            if (eventDetails && eventDetails.type === 'corr') {
                contentHtml += `<div class="mt-2 p-2" style="background: rgba(0,31,63,0.2); border-radius: 8px; border: 1px solid var(--glass-border);">`;
                contentHtml += `<small class="d-block text-info p-2"><b>تفاصيل التصحيح المعتمد:</b></small>`;
                contentHtml += `<small class="d-block px-2 pb-2">الوقت المصحح: ${data.corrected_check_in || 'لم يصحح'} - ${data.corrected_check_out || 'لم يصحح'}</small>`;
                contentHtml += `</div>`;
            }
        } else if (data.status === 'leave' && eventDetails) {
            contentHtml = `<div class="detail-item">
                              <span class="label">الحالة</span>
                              <span class="value" style="color: #1abc9c;">${data.status_text || 'إجازة معتمدة'}</span>
                          </div>`;
            if (eventDetails.vac_start && eventDetails.vac_end) {
                contentHtml += `<div class="detail-item">
                                   <span class="label">الفترة</span>
                                   <span class="value">${eventDetails.vac_start} إلى ${eventDetails.vac_end}</span>
                                </div>`;
            }
            if (eventDetails.vac_reason) {
                contentHtml += `<div class="detail-item">
                                   <span class="label">السبب</span>
                                   <span class="value">${eventDetails.vac_reason}</span>
                                </div>`;
            }
            if (eventDetails.type === 'vac_half' && eventDetails.vac_half_period) {
                contentHtml += `<div class="detail-item">
                                   <span class="label">فترة النصف يوم</span>
                                   <span class="value">${eventDetails.vac_half_period}</span>
                                </div>`;
            }
        } else if (data.status === 'absent') {
            contentHtml = `<div class="detail-item"><span class="label">الحالة</span><span class="value negative">${data.status_text || 'غياب'}</span></div>`;
        } else if (data.status === 'saturday_work') {
            contentHtml = `<div class="detail-item"><span class="label">الحالة</span><span class="value" style="color: var(--marsom-orange);">عمل يوم سبت</span></div>`;
            
            contentHtml += `<div class="detail-item">
                              <span class="label">وقت الدخول</span>
                              <span class="value">${data.check_in || '--:--'}</span>
                          </div>`;
            contentHtml += `<div class="detail-item">
                              <span class="label">وقت الخروج</span>
                              <span class="value">${data.check_out || '--:--'}</span>
                          </div>`;
            contentHtml += `<div class="detail-item">
                              <span class="label">مدة العمل</span>
                              <span class="value">${data.worked || '--:--'}</span>
                          </div>`;
        } else if (data.status === 'holiday' || data.status === 'weekend') {
            contentHtml = `<div class="detail-item"><span class="label">الحالة</span><span class="value text-muted">${data.status_text || data.status}</span></div>`;
        } else {
            detailsCardEl.style.display = 'none';
        }

        dayDetailsContentEl.innerHTML = contentHtml;
    }

    const btnPrevMonth = document.getElementById('btnPrevMonth');
    const btnNextMonth = document.getElementById('btnNextMonth');
    
    if (btnPrevMonth) {
        btnPrevMonth.addEventListener('click', (e) => {
            e.preventDefault();
            currentDate.setMonth(currentDate.getMonth() - 1);
            fetchCalendarData(currentDate.getFullYear(), currentDate.getMonth() + 1);
        });
    }

    if (btnNextMonth) {
        btnNextMonth.addEventListener('click', (e) => {
            e.preventDefault();
            currentDate.setMonth(currentDate.getMonth() + 1);
            fetchCalendarData(currentDate.getFullYear(), currentDate.getMonth() + 1);
        });
    }
    
    if (calendarGrid) {
        fetchCalendarData(currentDate.getFullYear(), currentDate.getMonth() + 1);
    }

    // Requests functionality
    const myRequestsTab = document.getElementById('my-requests-subtab');
    const approvalsTab = document.getElementById('approvals-subtab');
    const myRequestsContainer = document.getElementById('myRequestsContainer');

    const statusMap = {
        '0': { text: 'قيد المراجعة', class: 'bg-warning text-dark' },
        '2': { text: 'معتمد', class: 'bg-success' },
        '3': { text: 'مرفوض', class: 'bg-danger' },
        '-1': { text: 'ملغي', class: 'bg-secondary' },
        '-2': { text: 'ملغي بواسطة الموارد البشرية', class: 'bg-dark' }
    };

    // Filter elements
    const filterRequestType = document.getElementById('filterRequestType');
    const filterStatus = document.getElementById('filterStatus');
    const filterStartDate = document.getElementById('filterStartDate');
    const filterEndDate = document.getElementById('filterEndDate');
    const applyFiltersBtn = document.getElementById('applyFiltersBtn');
    const resetFiltersBtn = document.getElementById('resetFiltersBtn');

    // Current filter state
    let currentFilters = {
        type: '',
        status: '',
        startDate: '',
        endDate: ''
    };

    function initializeDateFilters() {
        const today = new Date();
        const oneMonthAgo = new Date();
        oneMonthAgo.setMonth(today.getMonth() - 1);
        
        if (filterStartDate && filterEndDate) {
            filterStartDate.valueAsDate = oneMonthAgo;
            filterEndDate.valueAsDate = today;
            
            currentFilters.startDate = formatDateForAPI(oneMonthAgo);
            currentFilters.endDate = formatDateForAPI(today);
        }
    }

    function formatDateForAPI(date) {
        return date.toISOString().split('T')[0];
    }

    function updateFilters() {
        currentFilters = {
            type: filterRequestType ? filterRequestType.value : '',
            status: filterStatus ? filterStatus.value : '',
            startDate: filterStartDate ? filterStartDate.value : '',
            endDate: filterEndDate ? filterEndDate.value : ''
        };
    }

    function resetFilters() {
        if (filterRequestType) filterRequestType.value = '';
        if (filterStatus) filterStatus.value = '';
        initializeDateFilters();
        updateFilters();
        loadMyRequests();
    }

    function applyFilters() {
        updateFilters();
        loadMyRequests();
        
        const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasFilters'));
        if (offcanvas) offcanvas.hide();
    }

    function getRequestDetails(req) {
        switch(String(req.type)) {
            case '2': 
                return `تصحيح بصمة - ${req.correction_date || 'غير محدد'}`;
            case '5': 
                const daysCount = req.vac_days_count ? ` (${req.vac_days_count} يوم)` : '';
                return `إجازة من ${req.vac_start || ''} إلى ${req.vac_end || ''}${daysCount}`;
            case '3': 
                return `عمل إضافي - ${req.ot_hours || 'N/A'} ساعة - ${req.ot_date || ''}`;

            case '1': 
                return `استقالة - ${req.resignation_date || ''}`;
            case '7': 
                return `طلب خطاب - ${req.letter_type || ''}`;
            case '6': 
                return `طلب عهدة - ${req.custody_item || ''}`;
            case '4': 
                return `مصاريف مالية - ${req.expense_amount || ''} ريال`;
            default: 
                return req.note || 'لا توجد تفاصيل إضافية';
        }
    }

    // Load my requests
    async function loadMyRequests() {
        if (!myRequestsContainer) return;
        
        myRequestsContainer.innerHTML = `
            <div class="card-glass p-4 text-center">
                <div class="spinner-border spinner-border-sm me-2"></div>
                جاري تحميل الطلبات...
            </div>`;
        
        try {
            const params = new URLSearchParams();
            
            if (currentFilters.type) params.append('type', currentFilters.type);
            if (currentFilters.status !== '') params.append('status', currentFilters.status);
            if (currentFilters.startDate) params.append('start_date', currentFilters.startDate);
            if (currentFilters.endDate) params.append('end_date', currentFilters.endDate);
            
            const url = `<?php echo site_url('users2/get_my_requests'); ?>?${params.toString()}`;
            
            console.log('Fetching URL:', url);
            
            const response = await fetch(url);
            
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            
            const result = await response.json();
            console.log('API Response:', result);

            if (result.status === 'success') {
                renderMyRequests(result.data);
            } else {
                myRequestsContainer.innerHTML = `
                    <div class="card-glass p-4 text-center text-danger">
                        <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                        <p class="mb-0">خطأ في تحميل الطلبات: ${result.message || 'حدث خطأ غير معروف'}</p>
                    </div>`;
            }
        } catch (error) {
            console.error("Failed to load requests:", error);
            myRequestsContainer.innerHTML = `
                <div class="card-glass p-4 text-center text-danger">
                    <i class="fas fa-wifi fa-2x mb-3"></i>
                    <p class="mb-0">فشل في تحميل الطلبات. يرجى التحقق من اتصالك بالشبكة.</p>
                    <button class="btn btn-sm btn-outline-light mt-2" onclick="loadMyRequests()">
                        <i class="fas fa-redo me-1"></i>إعادة المحاولة
                    </button>
                </div>`;
        }
    }

    // Render my requests
    function renderMyRequests(requests) {
        if (!myRequestsContainer) return;
        
        if (!requests || requests.length === 0) {
            myRequestsContainer.innerHTML = `
                <div class="card-glass p-4 text-center">
                    <i class="fas fa-inbox fa-2x text-muted mb-3"></i>
                    <p class="mb-0">لا توجد طلبات لعرضها.</p>
                    ${Object.values(currentFilters).some(filter => filter !== '') ? 
                        '<small class="text-muted">جرب تعديل الفلاتر للحصول على نتائج أكثر</small>' : ''}
                </div>`;
            return;
        }
        
        let html = '';
        requests.forEach(req => {
            const status = statusMap[req.status] || { text: `غير معروف (${req.status})`, class: 'bg-secondary' };
            const requestType = getRequestTypeText(req.type) || req.order_name || 'طلب';
            
            html += `
                <div class="request-card" data-order-id="${req.id}">
                    <div class="request-card-header">
                        <div>
                            <h6 class="mb-1">${requestType} #${req.id}</h6>
                            <small class="text-muted">${getRequestDetails(req)}</small>
                        </div>
                        <span class="badge ${status.class}">${status.text}</span>
                    </div>
                    <div class="request-card-body">
                        ${req.note ? `<div class="detail-row"><span class="detail-label">ملاحظات</span><span class="detail-value">${req.note}</span></div>` : ''}
                    </div>
                    <div class="request-card-footer">
                        <button class="btn btn-sm btn-outline-light" onclick="window.showRequestDetails('${req.id}', '${req.type}')">
                            <i class="fas fa-eye me-1"></i>عرض التفاصيل
                        </button>
                    </div>
                </div>`;
        });
        myRequestsContainer.innerHTML = html;
    }

    // Profile functionality
    const profileAccordion = document.getElementById('profileAccordion');
    
    if (profileAccordion) {
        const dataLoaders = {
            personal: "<?php echo site_url('users2/mobile_get_personal_details'); ?>",
            job: "<?php echo site_url('users2/mobile_get_job_details'); ?>",
            financial: "<?php echo site_url('users2/mobile_get_financial_details'); ?>",
            balances: "<?php echo site_url('users2/mobile_get_leave_balances'); ?>",
            contract: "<?php echo site_url('users2/mobile_get_contract_details'); ?>",
            payslip: "<?php echo site_url('users2/mobile_check_last_payslip_status'); ?>"
        };

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
                    html += `<div class="balance-box"><div class="value">${parseFloat(b.remaining_balance).toFixed(2)}</div><div class="label">${b.leave_type_name}</div></div>`;
                });
                html += '</div>';
                return html;
            },
            payslip: (data) => {
                if (!data || !data.month) {
                    return `
                        <div class="text-center py-3">
                            <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">لم يتم الإصدار بعد</h5>
                            <p class="text-muted small mb-0">لم يتم اعتماد مسير الراتب لهذا الشهر حتى الآن.</p>
                        </div>`;
                }
                return `
                    <div class="text-center py-2">
                        <i class="fas fa-file-invoice-dollar fa-4x text-success mb-3"></i>
                        <h5 class="mb-2 text-dark">مسير رواتب شهر: <span dir="ltr">${data.month}</span></h5>
                        <p class="text-muted small mb-4">تم إصدار واعتماد مسير الرواتب لهذا الشهر.</p>
                        
                        <div class="d-grid gap-2 col-10 mx-auto">
                            <a href="${data.download_url}" class="btn btn-primary fw-bold" target="_blank">
                                <i class="fas fa-print me-2"></i> عرض / طباعة التعريف
                            </a>
                        </div>
                    </div>`;
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
            if (mySalaryChart) mySalaryChart.destroy();
            mySalaryChart = new Chart(ctx, {
                type: 'doughnut', 
                data: { 
                    labels: ['الأساسي', 'السكن', 'النقل', 'أخرى'], 
                    datasets: [{ 
                        data: [base, housing, transport, others], 
                        backgroundColor: ['var(--marsom-orange)', 'var(--marsom-blue)', '#6c757d', '#34495e'], 
                        borderWidth: 0 
                    }] 
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false, 
                    plugins: { legend: { display: false } } 
                }
            });
        }

        profileAccordion.addEventListener('show.bs.collapse', async function (event) {
            const button = event.target.previousElementSibling.querySelector('button');
            const contentArea = event.target.querySelector('.accordion-body');
            const loaderKey = button.dataset.loader;

            if (contentArea && !contentArea.dataset.loaded) {
                contentArea.innerHTML = `<div class="text-center p-4"><div class="spinner-border spinner-border-sm"></div></div>`;
                try {
                    const response = await fetch(dataLoaders[loaderKey]);
                    const result = await response.json();
                    if (result.status === 'success') {
                        contentArea.innerHTML = contentRenderers[loaderKey](result.data);
                        contentArea.dataset.loaded = 'true';
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
        const firstSection = document.getElementById('collapsePersonal');
        if (firstSection) {
            firstSection.dispatchEvent(new Event('show.bs.collapse'));
        }
    }

    // Event Listeners
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', applyFilters);
    }
    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', resetFilters);
    }

    if (myRequestsTab) {
        myRequestsTab.addEventListener('click', function(e) {
            console.log('🟡 My Requests tab clicked');
            setTimeout(loadMyRequests, 100);
        });
    }

    // Initialize everything
    initializeDateFilters();
    loadLastThreeRequests();
    loadMyRequests();

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (workTimerInterval) {
            clearInterval(workTimerInterval);
            workTimerInterval = null;
        }
        if (mySalaryChart) {
            mySalaryChart.destroy();
            mySalaryChart = null;
        }
    });

    console.log('✅ Application initialized successfully');
});

// Service Worker Registration
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/hr/assets/sw.js')
            .then(reg => {
                console.log('Service Worker registered successfully:', reg);
            })
            .catch(err => {
                console.log('Service Worker registration failed: ', err);
            });
    });
}

// Logout functionality
document.addEventListener('DOMContentLoaded', function() {
    const logoutButton = document.getElementById('logoutButton');
    if (logoutButton) {
        logoutButton.addEventListener('click', function(e) {
            if (confirm('هل أنت متأكد أنك تريد تسجيل الخروج؟')) {
                // Show loading state
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> جاري تسجيل الخروج...';
                this.disabled = true;
                
                // Perform logout
                fetch('<?php echo site_url("users/logout"); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: '<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        window.location.href = '<?php echo site_url("users/login"); ?>';
                    } else {
                        alert('حدث خطأ أثناء تسجيل الخروج');
                        this.innerHTML = '<i class="fas fa-sign-out-alt fa-fw"></i> خروج';
                        this.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Logout error:', error);
                    alert('حدث خطأ في الاتصال');
                    this.innerHTML = '<i class="fas fa-sign-out-alt fa-fw"></i> خروج';
                    this.disabled = false;
                });
            }
        });
    }
});
</script>
</body>
</html>