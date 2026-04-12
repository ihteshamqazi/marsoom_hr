<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تطبيق أنيميشن وبوتستراب 5</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.7/main.min.css" rel="stylesheet">
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar/index.global.min.js'></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="assets/js/index.global.min.js"></script>
    <script src="assets/js/index.global.js"></script>
  
     <style>
        
        /* الأنماط العامة للجسم والخط */
       body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa; /* لون خلفية فاتح */
            margin: 0;
            padding-bottom: 70px; /* مساحة للشريط السفلي الثابت */
        }
        /* --- New Styling for FullCalendar Header --- */

        /* Overall Toolbar Container */
        /* --- New Muted & Refined Calendar Header Style --- */

        /* Overall Toolbar Container */
        /* --- New 2-Line Calendar Header Style --- */

        /* Overall Toolbar Container - Made bigger and set to wrap */
        /* --- Bootstrap 5 Inspired Styles for Calendar & Details --- */

        /* --- Calendar Header --- */

        .fc .fc-toolbar.fc-header-toolbar {
            margin-bottom: 1.5rem;
            padding: 0;
            background-color: transparent;
            border: none;
            box-shadow: none;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
        }

        .fc .fc-toolbar-chunk:nth-child(2) {
            order: 1;
            flex-basis: 100%;
            text-align: center;
            margin-bottom: 1rem;
        }

        .fc .fc-toolbar-title {
            font-size: 1.5rem;
            font-weight: 500;
            color: #212529;
            /* Bootstrap's standard dark text color */
        }

        .fc .fc-toolbar-chunk:nth-child(1),
        .fc .fc-toolbar-chunk:nth-child(3) {
            order: 2;
        }

        /* General Button Styling (emulates .btn and .btn-light) */
        .fc .fc-button {
            display: inline-block;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
            user-select: none;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 0.375rem 0.75rem;
            font-size: 0.9rem;
            border-radius: 0.25rem;
            transition: all .15s ease-in-out;
        }

        .fc .fc-button:hover {
            background-color: #e2e6ea;
            border-color: #dae0e5;
        }

        /* Active/Primary Button (emulates .btn-primary) */
        .fc .fc-button-primary {
            color: #fff;
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .fc .fc-button-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
        }

        /* Button Group Styling */
        .fc .fc-button-group {
            padding: 0;
            background-color: transparent;
        }
        .fc .fc-button-group > .fc-button {
            margin: 0;
            position: relative;
            border-radius: 0;
        }
        .fc .fc-button-group > .fc-button:not(:last-child) {
            border-right: none;
        }
        .fc .fc-button-group > .fc-button:first-child {
            border-top-left-radius: 0.25rem;
            border-bottom-left-radius: 0.25rem;
        }
        .fc .fc-button-group > .fc-button:last-child {
            border-top-right-radius: 0.25rem;
            border-bottom-right-radius: 0.25rem;
        }


        /* --- Attendance Details Card (using .list-group style) --- */
        .attendance-details .card {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075);
        }

        .attendance-details .card-title {
            font-size: 1.25rem;
            font-weight: 500;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #dee2e6;
        }

        .attendance-details .list-group-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0; /* Adjusted padding */
            color: #212529;
            background-color: #fff;
            border-bottom: 1px solid #dee2e6;
        }
        .attendance-details .list-group-item:last-child {
            border-bottom: none;
        }

        .attendance-details .item-label {
            display: flex;
            align-items: center;
            color: #6c757d;
            /* Muted label color */
        }
        .attendance-details .item-label i {
            font-size: 1rem;
            margin-left: 0.75rem;
            /* For RTL */
            color: #6c757d;
            width: 20px;
            text-align: center;
        }

        .attendance-details .item-value {
            font-weight: 600;
            font-family: monospace;
            /* Gives numbers a clean, aligned look */
            font-size: 1rem;
        }
        /* أنماط الشاشات (المحتوى الخاص بكل تاب) */
        .screen {
            display: none; /* إخفاء الشاشات افتراضياً */
            padding: 20px;
            min-height: calc(100vh - 70px); /* ضبط الارتفاع ليتناسب مع شريط التنقل */
            background-color: #fff; /* خلفية بيضاء للشاشات */
            box-shadow: 0 0 10px rgba(0,0,0,0.05); /* ظل خفيف */
            border-radius: 15px; /* زوايا دائرية */
            margin: 10px; /* هامش حول الشاشة */
            opacity: 0; /* شفافية 0 للأنيميشن */
            transform: translateY(10px); /* إزاحة للأسفل للأنيميشن */
            transition: opacity 0.4s ease-out, transform 0.4s ease-out; /* انتقال سلس للشفافية والإزاحة */
        }
        .screen.active {
            display: block; /* إظهار الشاشة النشطة */
            opacity: 1; /* جعلها ظاهرة بالكامل */
            transform: translateY(0); /* إعادة الإزاحة لموقعها الأصلي */
        }
        /* أنماط شريط التنقل السفلي */
        .bottom-nav {
            background-color: #ffffff; /* خلفية بيضاء */
            border-top: 1px solid #e0e0e0; /* حد علوي خفيف */
            padding: 5px 0;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05); /* ظل علوي */
            border-top-left-radius: 20px; /* زاوية دائرية علوية يسار */
            border-top-right-radius: 20px; /* زاوية دائرية علوية يمين */
            overflow: hidden; /* للحفاظ على الزوايا الدائرية نظيفة */
        }
        .bottom-nav .nav-link {
            color: #6c757d; /* لون رمادي باهت للروابط */
            flex-grow: 1; /* لجعل الروابط تتمدد بالتساوي */
            text-align: center;
            padding: 10px 0;
            display: flex;
            flex-direction: column; /* ترتيب الأيقونة والنص عمودياً */
            align-items: center;
            justify-content: center;
            font-size: 0.8rem; /* حجم خط صغير */
            transition: color 0.3s ease, transform 0.3s ease; /* انتقال سلس للون والتحويل */
            border-radius: 10px; /* زوايا دائرية للأزرار */
            margin: 0 5px; /* مسافة بين التبويبات */
        }
        .bottom-nav .nav-link i {
            font-size: 1.2rem; /* حجم أكبر للأيقونات */
            margin-bottom: 5px;
            transition: transform 0.3s ease; /* انتقال سلس للأيقونة */
        }
        .bottom-nav .nav-link.active {
            color: #0d6efd; /* لون أزرق أساسي لـ Bootstrap للرابط النشط */
            transform: translateY(-3px); /* رفع خفيف للرابط النشط */
            background-color: #eaf3ff; /* خلفية زرقاء فاتحة للرابط النشط */
        }
        .bottom-nav .nav-link.active i {
            transform: scale(1.1); /* تكبير الأيقونة قليلاً للرابط النشط */
        }
        .bottom-nav .nav-link:hover {
            color: #0a58ca; /* لون أزرق أغمق عند التمرير */
        }

        /* حاوية أساسية لتوسيط المحتوى على الشاشات الأكبر */
        .container-fluid {
            max-width: 500px; /* أقصى عرض لمشاهدة الهاتف المحمول */
            padding: 0; /* إزالة الحشو الافتراضي لمحتوى العرض الكامل */
        }

        /* أنماط المودالات المتدفقة من اليسار (Offcanvas) */
        .offcanvas.offcanvas-start {
            width: 80vw; /* عرض المودال عند الظهور من اليسار */
            max-width: 300px; /* أقصى عرض للمودال */
            border-radius: 0 15px 15px 0; /* زوايا دائرية على اليمين */
        }
        /* أنماط المودالات المتدفقة من الأسفل */
        .modal.fade .modal-dialog.modal-bottom {
            transform: translateY(100%);
            transition: transform 0.3s ease-out;
        }
        .modal.show .modal-dialog.modal-bottom {
            transform: translateY(0);
        }
        .modal-bottom .modal-content {
            border-radius: 15px 15px 0 0; /* زوايا دائرية علوية فقط */
        }
        .modal-bottom .modal-header,
        .modal-bottom .modal-body,
        .modal-bottom .modal-footer {
            border: none; /* إزالة الحدود الافتراضية */
        }

        /* أنماط مربع الحضور المخصص */
        .attendance-box {
            background: linear-gradient(135deg, #4a90e2 0%, #0d6efd 100%); /* تدرج أزرق */
            color: #fff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            position: relative;
            overflow: hidden;
            margin-bottom: 20px;
        }
        .attendance-box::before {
            content: '';
            position: absolute;
            top: -50px;
            left: -50px;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: rotate(45deg);
        }
        .attendance-box .location-info {
            font-size: 1rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        .attendance-box .time-counter {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .attendance-box .remaining-time {
            font-size: 0.9rem;
            opacity: 0.8;
            margin-bottom: 15px;
        }
        .attendance-box .btn {
            background-color: #fff;
            color: #0d6efd;
            border: none;
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .attendance-box .btn:hover {
            background-color: #eaf3ff;
            color: #0a58ca;
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        
        /* أنماط الأزرار المربعة الصغيرة */
        .square-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 15px;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            text-decoration: none;
            color: #343a40;
            height: 120px; /* لضمان حجم موحد */
            width: 100%; /* لملء العمود */
        }
        .square-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            color: #0d6efd;
        }
        .square-btn i {
            font-size: 2rem;
            margin-bottom: 10px;
            color: #0d6efd; /* لون الأيقونة */
            transition: color 0.3s ease;
        }
        .square-btn:hover i {
            color: #0a58ca;
        }
        .square-btn span {
            font-size: 0.9rem;
            font-weight: 500;
            text-align: center;
        }

        /* أنماط جدول الطلبات */
        .requests-table .card-header {
            background-color: #f0f2f5;
            font-weight: 600;
            color: #333;
            border-bottom: 1px solid #dee2e6;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }
        .requests-table .list-group-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding: 12px 15px;
        }
        .requests-table .list-group-item:last-child {
            border-bottom: none;
        }
        .requests-table .list-group-item h6 {
            margin-bottom: 2px;
            font-size: 1rem;
            font-weight: 500;
        }
        .requests-table .list-group-item small {
            font-size: 0.8rem;
            color: #6c757d;
        }

        /* زر "طلب جديد" العائم */
        .fab-button {
            position: fixed;
            bottom: 85px; /* فوق شريط التنقل السفلي */
            left: 20px;
            background-color: #28a745; /* أخضر */
            color: white;
            border-radius: 50%;
            width: 55px;
            height: 55px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.25);
            transition: all 0.3s ease;
            z-index: 10;
            border: none; /* إزالة الحدود */
        }
        .fab-button:hover {
            background-color: #218838;
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.3);
        }

        /* أنماط شاشة الحضور الجديدة */
        .attendance-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        /* أنماط تاب السجلات والإجازات */
        .attendance-tabs .nav-link {
            flex-grow: 1;
            text-align: center;
            padding: 10px;
            border-radius: 10px;
            color: #6c757d;
            font-weight: 600;
            transition: all 0.3s ease;
            background-color: #f0f2f5;
        }
        .attendance-tabs .nav-link.active {
            background-color: #0d6efd;
            color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        /* Overall Calendar Container Styling */
        #calendar {
          max-width: 1000px;
          margin: 50px auto;
          height : 600px;
          padding: 20px;
          background: #ffffff;
          border-radius: 12px;
          box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
          transition: all 0.3s ease;
        }

        /* Toolbar */
        .fc .fc-toolbar {
          margin-bottom: 20px;
          font-size: 16px;
        }

        .fc .fc-toolbar-title {
          font-weight: 600;
          font-size: 20px;
          color: #2c3e50;
        }

        /* Buttons */
        .fc-button {
          background-color: #e9ecef;
          color: #333;
          border: 1px solid #ced4da;
          border-radius: 6px;
          padding: 5px 12px;
          font-size: 14px;
          transition: all 0.2s ease;
        }

        .fc-button:hover {
          background-color: #d6d8db;
          color: #000;
          border-color: #adb5bd;
        }

        .fc-button-primary {
          background-color: #0d6efd;
          color: #fff;
          border: none;
        }

        .fc-button-primary:hover {
          background-color: #0b5ed7;
        }

        /* Day Headers (Mon, Tue, etc.) */
        .fc-col-header-cell {
          background-color: #f1f3f5;
          font-weight: 500;
          font-size: 14px;
          padding: 10px 0;
          border: 1px solid #dee2e6;
        }

        /* Day Cells */
        .fc-daygrid-day {
          border: 1px solid #f1f3f5;
        }

        .fc-daygrid-day-number {
          padding: 6px;
          font-size: 13px;
          color: #495057;
        }

        /* Today's Date */
        .fc-day-today {
          background-color: #e9f5ff !important;
        }

        /* Events */
        .fc-event {
          background-color: #4dabf7;
          border: none;
          color: white;
          border-radius: 4px;
          padding: 2px 4px;
          font-size: 13px;
          box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .fc-event:hover {
          background-color: #339af0;
          cursor: pointer;
        }

        /* Scrollbar (optional) */
        .fc-scroller::-webkit-scrollbar {
          width: 6px;
        }

        .fc-scroller::-webkit-scrollbar-thumb {
          background-color: #adb5bd;
          border-radius: 3px;
        }

        /* أنماط بطاقات تفاصيل السجلات */
        .record-card {
            background-color: #eaf3ff; /* خلفية زرقاء فاتحة */
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 15px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border: 1px solid #d0e7ff;
        }
        .record-card h6 {
            color: #0d6efd;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .record-card p {
            font-size: 1.5rem;
            font-weight: bold;
            color: #343a40;
            margin-bottom: 0;
        }
        .record-card.green {
            background-color: #d4edda; /* أخضر فاتح */
            border-color: #c3e6cb;
        }
        .record-card.green h6 {
            color: #155724;
        }
        .record-card.green p {
            color: #155724;
        }
        .record-card.red {
            background-color: #f8d7da; /* أحمر فاتح */
            border-color: #f5c6cb;
        }
        .record-card.red h6 {
            color: #721c24;
        }
        .record-card.red p {
            color: #721c24;
        }

        .summary-box {
            background-color: #ffffff;
            border-radius: 15px;
            padding: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            display: flex;
            justify-content: space-around;
            align-items: center;
            text-align: center;
        }
        .summary-item {
            flex: 1;
            padding: 5px;
        }
        .summary-item h6 {
            font-size: 0.8rem;
            color: #6c757d;
            margin-bottom: 5px;
        }
        .summary-item p {
            font-size: 1.1rem;
            font-weight: bold;
            color: #343a40;
            margin-bottom: 0;
        }

        /* أنماط التقويم الأسبوعي */
        .weekly-calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
            text-align: center;
            margin-bottom: 15px;
        }
        .weekly-day {
            padding: 10px 5px;
            border-radius: 8px;
            background-color: #f0f2f5;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.2s ease;
            font-weight: 500;
            color: #343a40;
        }
        .weekly-day.selected {
            background-color: #0d6efd;
            color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .weekly-day.holiday {
            background-color: #f8d7da; /* أحمر فاتح للعطلات */
            color: #721c24;
            font-weight: 600;
        }

        /* أنماط ملخص الحضور */
        .summary-cards .col-6 {
            margin-bottom: 15px;
        }
        .summary-item-card {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 15px;
            text-align: center;
            height: 100%; /* لضمان نفس الارتفاع */
        }
        .summary-item-card h5 {
            font-size: 1rem;
            color: #6c757d;
            margin-bottom: 5px;
        }
        .summary-item-card p {
            font-size: 1.8rem;
            font-weight: bold;
            color: #343a40;
            margin-bottom: 0;
        }
        .summary-item-card i {
            font-size: 2rem;
            color: #0d6efd; /* لون الأيقونة */
            margin-bottom: 10px;
        }
        .summary-item-card.green-bg { background-color: #d4edda;
        }
        .summary-item-card.red-bg { background-color: #f8d7da;
        }
        .summary-item-card.yellow-bg { background-color: #fff3cd;
        }
        .summary-item-card.blue-bg { background-color: #eaf3ff;
        }
        .summary-item-card.gray-bg { background-color: #e9ecef;
        }


        /* أنماط عرض حالة الأيام في عرض الشهر */
        .monthly-day-status-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
        }
        .monthly-day-status-item:last-child {
            border-bottom: none;
        }
        .monthly-day-status-item .day-info {
            font-weight: 500;
            color: #343a40;
        }
        .monthly-day-status-item .status-badge {
            font-size: 0.8rem;
            padding: 5px 10px;
            border-radius: 8px;
        }
        .monthly-day-status-item.holiday {
            background-color: #f8d7da; /* خلفية خفيفة للعطلة */
            color: #721c24;
            font-weight: bold;
        }
        .monthly-day-status-item.holiday .day-info {
            color: #721c24;
        }
        .monthly-day-status-item.holiday .status-badge {
            background-color: #dc3545;
            color: #fff;
        }

        /* أنماط اجازات الشهر */
        .leave-period-card {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #e0e0e0;
        }
        .leave-period-card h6 {
            font-weight: 600;
            margin-bottom: 5px;
            color: #0d6efd;
        }
        .leave-period-card p {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 0;
        }
        .leave-period-card .badge {
            font-size: 0.7rem;
            padding: 5px 8px;
            border-radius: 5px;
            margin-top: 5px;
        }
        .leave-period-card.no-leaves {
            text-align: center;
            opacity: 0.7;
            padding: 20px;
        }

        /* أنماط شاشة التقرير */
        .report-summary-box {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }
        .report-summary-box h5 {
            font-weight: 600;
            color: #343a40;
            margin-bottom: 10px;
        }
        .report-summary-box p {
            font-size: 1.4rem;
            font-weight: bold;
            color: #0d6efd;
            margin-bottom: 0;
        }
        .report-summary-box small {
            color: #6c757d;
        }

        .attendance-summary-bar-container {
            background-color: #e9ecef;
            border-radius: 10px;
            height: 25px;
            overflow: hidden;
            margin-bottom: 15px;
            display: flex;
        }
        .attendance-summary-bar-segment {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 0.7rem;
            font-weight: bold;
            transition: width 0.5s ease-out; /* Smooth transition for width change */
        }
        .segment-present { background-color: #28a745;
        } /* أخضر */
        .segment-incomplete { background-color: #fd7e14;
        } /* برتقالي/وردي */
        .segment-absent { background-color: #dc3545;
        } /* أحمر */
        .segment-unrequired { background-color: #ffc107;
        } /* أصفر */
        .segment-rest { background-color: #6c757d;
        } /* رمادي */
        .segment-leave { background-color: #0d6efd;
        } /* أزرق */

        .attendance-legend {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            font-size: 0.8rem;
            color: #343a40;
        }
        .legend-item {
            display: flex;
            align-items: center;
        }
        .legend-color-box {
            width: 15px;
            height: 15px;
            border-radius: 3px;
            margin-left: 5px;
        }

        /* أنماط شاشة الطلبات الجديدة */
        .requests-tabs .nav-link {
            flex-grow: 1;
            text-align: center;
            padding: 10px;
            border-radius: 10px;
            color: #6c757d;
            font-weight: 600;
            transition: all 0.3s ease;
            background-color: #f0f2f5;
        }
        .requests-tabs .nav-link.active {
            background-color: #0d6efd;
            color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .request-box {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #e0e0e0;
        }
        .request-box .request-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 5px;
            color: #343a40;
        }
        .request-box .request-status {
            font-size: 0.85rem;
            padding: 5px 10px;
            border-radius: 8px;
            font-weight: bold;
        }
        .request-box .request-details p {
            margin-bottom: 5px;
            font-size: 0.9rem;
            color: #6c757d;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .request-box .request-details p i {
            color: #0d6efd;
        }
        .request-box .request-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            justify-content: flex-end;
        }
        .request-box .request-actions .btn {
            border-radius: 8px;
            font-size: 0.9rem;
            padding: 8px 15px;
        }

        /* أنماط القائمة المنسدلة للملف الشخصي */
        .profile-menu {
            transition: max-height 0.3s ease-out, opacity 0.3s ease-out;
            max-height: 0;
            overflow: hidden;
            opacity: 0;
        }
        .profile-menu.show {
            max-height: 500px; /* أو أي قيمة كبيرة لتستوعب المحتوى */
            opacity: 1;
        }
        .profile-menu .list-group-item {
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .profile-menu .list-group-item:hover {
            background-color: #f0f2f5;
        }
        .profile-menu .list-group-item.toggle-btn {
            font-weight: bold;
            background-color: #e9ecef;
        }
        .profile-menu .list-group-item.toggle-btn:hover {
            background-color: #e9ecef;
        }
        .profile-menu .list-group-item i {
            width: 20px;
        }
        .profile-menu .list-group-item small {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 2px;
        }
        .profile-menu .sub-menu {
            display: none;
            padding-right: 20px;
            background-color: #f8f9fa;
        }
        .profile-menu .sub-menu.show {
            display: block;
        }
        .profile-menu .sub-menu .list-group-item {
            padding-right: 30px;
        }

        /* أنماط شاشة عرض التفاصيل */
        .profile-details-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #f8f9fa;
            z-index: 1050;
            padding: 20px;
            display: none;
            overflow-y: auto;
        }
        .profile-details-screen.show {
            display: block;
        }
        .profile-details-screen .header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .profile-details-screen .header h4 {
            flex-grow: 1;
            text-align: center;
            margin: 0;
        }
        .info-card {
            background-color: #fff;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .info-card h6 {
            font-weight: bold;
            color: #0d6efd;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-item .label {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .info-item .value {
            font-weight: 500;
        }
        
        /* --- START: FINAL Profile Screen Styles (Corrected DIV Spacing) --- */

        /* This header now sits cleanly inside the screen's default padding */
        .profile-header-new {
            text-align: center;
            padding: 10px 0 20px 0;
            border-bottom: 1px solid #f0f2f5;
            margin-bottom: 15px;
        }
        .profile-header-new .avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 10px;
            border: 3px solid #0d6efd;
        }
        .profile-header-new h5 {
            margin: 0;
            font-weight: 600;
            font-size: 1.2rem;
        }
        .profile-header-new p {
            margin: 4px 0 0;
            color: #6c757d;
            font-size: 0.9rem;
        }

        /* This is the key fix: The sections have NO horizontal margin,
           allowing them to fill the parent screen's padded area correctly. */
        .profile-section-new {
            background-color: #fff;
            border-radius: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .profile-item-new {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px;
            cursor: pointer;
            transition: background-color 0.2s ease;
            border-bottom: 1px solid #f0f2f5;
            background-color: #fff;
        }
        .profile-section-new .profile-item-new:last-child {
            border-bottom: none;
        }
        .profile-item-new:hover {
            background-color: #f8f9fa;
        }
        .profile-item-new .item-content {
            display: flex;
            align-items: center;
            gap: 15px;
            font-weight: 500;
        }
        .profile-item-new .item-content i {
            color: #0d6efd;
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
        }
        .profile-item-new .chevron-icon {
            color: #adb5bd;
            transition: transform 0.3s ease-out;
        }
        .profile-item-new.toggle-btn.open .chevron-icon {
            transform: rotate(180deg);
        }

        .profile-submenu-new {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            background-color: #f8f9fa;
        }
        .profile-submenu-new.show {
            max-height: 500px; 
        }
        .profile-submenu-new .profile-item-new {
            padding-right: 30px;
            background-color: transparent;
        }
        .profile-submenu-new .item-content i {
            color: #6c757d;
        }

        .profile-item-new.danger-item .item-content,
        .profile-item-new.danger-item .item-content i {
            color: #dc3545;
            font-weight: 600;
        }
        /* --- Styles for Personal Details Panel --- */
/* --- START: Amazing Design for Details Panel --- */

/* This wrapper adds padding around the cards */
#profileDetailsContent {
    padding: 10px;
    background-color: var(--bg-color); /* Uses the soft gray from the theme */
}

.info-card-group {
    background-color: var(--card-color);
    border-radius: 16px;
    margin-bottom: 15px;
    border: 1px solid var(--border-color);
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    overflow: hidden;
}

.info-group-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--text-dark);
    padding: 12px 16px;
    background-color: #F9FAFB; /* Slightly different background for the title */
    border-bottom: 1px solid var(--border-color);
    text-align: center;
}

.details-info-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.details-info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 16px;
    border-bottom: 1px solid var(--border-color);
    font-size: 0.9rem;
}

.details-info-item:last-child {
    border-bottom: none;
}

.details-info-item .info-label {
    color: #0d6efd;
    display: flex;
    align-items: center;
    gap: 12px; /* Space between icon and text */
    font-weight: 600;
}

.details-info-item .info-label i {
    font-size: 1rem;
    color: #0d6efd;
    width: 20px; /* Ensures alignment */
    text-align: center;
    
}

.details-info-item .info-value {
    font-weight: 600;
    color: var(--text-dark);
    text-align: left;
    word-break: break-all; /* Prevents long IBANs from breaking layout */
}
/* --- Style for Salary Pie Chart Container --- */
/* --- START: CORRECTED Styles for Financial Details & Pie Chart --- */

/* This gives the chart a defined space to draw inside */
.pie-chart-container {
    position: relative;
    padding: 1rem;
    margin: 0 auto;
    height: 280px; /* Defines a height */
    width: 280px;  /* Defines a width */
}

/* This fixes the alignment of the details list for RTL */
.details-info-item {
    display: grid; /* Use Grid for better alignment */
    grid-template-columns: 1fr auto; /* Value on left, Label on right */
    align-items: center;
    padding: 14px 16px;
    border-bottom: 1px solid var(--border-color);
    font-size: 0.9rem;
}

.details-info-item:last-child {
    border-bottom: none;
}

/* --- Styles for Status Badges --- */
.status-badge {
    padding: 4px 12px;
    border-radius: 9999px; /* Pill shape */
    font-weight: 600;
    font-size: 0.8rem;
    text-align: center;
}

.status-active {
    background-color: var(--success-bg); /* Uses green from your theme */
    color: var(--success-text);
}

.status-inactive {
    background-color: #F3F4F6; /* Gray color */
    color: #4B5563;
}
/* --- END: CORRECTED Styles --- */
/* --- END: Amazing Design for Details Panel --- */
        /* --- END: FINAL Profile Screen Styles --- */
        /* Style for the main clickable item */
.profile-item-new {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    cursor: pointer;
    transition: background-color 0.3s;
}

.profile-item-new:hover {
    background-color: #e9ecef;
}

.profile-item-new .item-content {
    display: flex;
    align-items: center;
    gap: 15px; /* Space between icon and text */
    font-size: 1.1rem;
}

/* Style for the chevron icon to indicate open/close */
.chevron-icon {
    transition: transform 0.3s ease-in-out;
}

.chevron-icon.active {
    transform: rotate(-90deg);
}

/* Style for the sub-menu */
.profile-submenu {
    padding: 10px 10px 10px 45px; /* Indent the submenu */
    background-color: #ffffff;
    border-bottom: 1px solid #dee2e6;
}

.submenu-item {
    display: block;
    padding: 10px 5px;
    color: #333;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s;
    font-size: 0.9 rem;
    font-weight: 500;
}

.submenu-item:hover {
    background-color: #f1f1f1;
    color: #000;
}

.submenu-item i {
    margin-right: 10px;
    
}
.attendance-box {
    background: linear-gradient(135deg, #4a90e2 0%, #0d6efd 100%);
    color: #fff;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    text-align: center;
    position: relative;
    overflow: hidden;
    margin-bottom: 20px;
}
    </style>
</head>
<body class="d-flex flex-column h-100">
    <div class="container-fluid flex-grow-1">
        
        <div id="homeScreen" class="screen active rounded-lg shadow-md mt-3">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <button class="btn btn-light rounded-full p-2 me-2" data-bs-toggle="offcanvas" data-bs-target="#announcementsOffcanvas" aria-controls="announcementsOffcanvas">
                        <i class="fas fa-bullhorn text-gray-700"></i>
                    </button>
                    <button class="btn btn-light rounded-full p-2 me-2" data-bs-toggle="offcanvas" data-bs-target="#notificationsOffcanvas" aria-controls="notificationsOffcanvas">
                        <i class="fas fa-bell text-gray-700"></i>
                    </button>
                    <button class="btn btn-light rounded-full p-2" data-bs-toggle="offcanvas" data-bs-target="#profileOffcanvas" aria-controls="profileOffcanvas">
                        <i class="fas fa-user-circle text-gray-700"></i>
                    </button>
                </div>
                <div>
                    <button class="btn btn-outline-primary rounded-pill px-3 py-2" onclick="showAttendanceScreen()">
    <i class="fas fa-calendar-alt me-2"></i>
    <span>
        <?php
            // PHP code to display the date in Arabic
            $formatter = new IntlDateFormatter('ar-SA', IntlDateFormatter::FULL, IntlDateFormatter::NONE, 'Asia/Riyadh', IntlDateFormatter::GREGORIAN);
            echo $formatter->format(time());
        ?>
    </span>
</button>
</div>
</div><h2 class="text-2xl font-semibold text-gray-800 mb-4">
    مرحباً، <?php echo isset($employee_name) ? html_escape($employee_name) : 'زائر'; ?>!
</h2>
            <p class="text-gray-600">
                نتمنى لك يوماً موفقاً في عملك.
            </p>

            <div class="attendance-box">
    <div class="location-info">
        <i class="fas fa-map-marker-alt"></i>
        <span>الرياض</span>
    </div>
    <div class="time-counter" id="workTimeCounter">00:00</div>
    <div class="remaining-time">من أصل 9 ساعات</div>
    <button class="btn" id="attendanceToggleButton">تسجيل الحضور / تسجيل الانصراف</button>
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

            <div class="card shadow-sm rounded-lg requests-table">
                <div class="card-header">طلباتك الحالية/المعلقة</div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <div>
                                <h6 class="mb-1">طلب إجازة سنوية</h6>
                                <small>حالة: <span class="badge bg-warning">معلق</span></small>
                            </div>
                            <span>2025/9/1 - 2025/9/5</span>
                        </li>
                        <li class="list-group-item">
                            <div>
                                <h6 class="mb-1">تصحيح حضور</h6>
                                <small>حالة: <span class="badge bg-info">قيد المراجعة</span></small>
                            </div>
                            <span>2025/8/18</span>
                        </li>
                        <li class="list-group-item">
                            <div>
                                <h6 class="mb-1">طلب عمل إضافي</h6>
                                <small>حالة: <span class="badge bg-success">موافق عليه</span></small>
                            </div>
                            <span>+3 ساعات (2025/8/15)</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="attendanceScreen" class="screen rounded-lg shadow-md mt-3">
            <div class="attendance-header">
                <h2 class="text-2xl font-semibold text-gray-800">الحضور</h2>
                <div>
                    <button class="btn btn-primary rounded-md" onclick="showAttendanceReport()">
                        <i class="fas fa-chart-pie me-2"></i>التقرير
                    </button>
                </div>
            </div>
            <div id="calendar"></div>
            <div class="attendance-details mt-4">
                <div class="card shadow-sm rounded-lg">
                    <div class="card-body">
                        <h5 class="card-title">تفاصيل الحضور</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <div class="item-label"><i class="fas fa-calendar-alt"></i> التاريخ:</div>
                                <span id="attendanceDate" class="item-value"></span>
                            </li>
                            <li class="list-group-item">
                                <div class="item-label"><i class="fas fa-user-clock"></i> وقت الحضور:</div>
                                <span id="attendanceIn" class="item-value"></span>
                            </li>
                            <li class="list-group-item">
                                <div class="item-label"><i class="fas fa-user-clock"></i> وقت الانصراف:</div>
                                <span id="attendanceOut" class="item-value"></span>
                            </li>
                            <li class="list-group-item">
                                <div class="item-label"><i class="fas fa-stopwatch"></i> مدة العمل:</div>
                                <span id="attendanceDuration" class="item-value"></span>
                            </li>
                            <li class="list-group-item">
                                <div class="item-label"><i class="fas fa-hourglass-half"></i> الفرق:</div>
                                <span id="attendanceDiff" class="item-value"></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="ordersScreen" class="screen rounded-lg shadow-md mt-3">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">الطلبات</h2>
            <ul class="nav nav-pills nav-justified requests-tabs mb-4" id="requestsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="my-requests-tab" data-bs-toggle="tab" data-bs-target="#myRequestsContent" type="button" role="tab" aria-controls="myRequestsContent" aria-selected="true">طلباتي</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="employee-requests-tab" data-bs-toggle="tab" data-bs-target="#employeeRequestsContent" type="button" role="tab" aria-controls="employeeRequestsContent" aria-selected="false">طلبات الموظفين</button>
                </li>
            </ul>
            <div class="tab-content" id="requestsTabContent">
                <div class="tab-pane fade show active" id="myRequestsContent" role="tabpanel" aria-labelledby="my-requests-tab">
                    <div id="requestsLoading" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">جاري التحميل...</span>
                        </div>
                        <p class="mt-2 text-muted">جاري تحميل طلباتك...</p>
                    </div>
                    <div id="requestsError" class="alert alert-danger d-none" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span id="requestsErrorMessage">حدث خطأ في تحميل الطلبات</span>
                    </div>
                    <div id="noRequests" class="text-center py-5 d-none">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد طلبات</h5>
                        <p class="text-muted">لم تقم بإرسال أي طلبات حتى الآن</p>
                        <div class="d-flex gap-2 justify-content-center">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#correctAttendanceModal">
                                <i class="fas fa-plus me-2"></i>إرسال طلب تصحيح
                            </button>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#leaveRequestModal">
                                <i class="fas fa-calendar-plus me-2"></i>طلب إجازة
                            </button>
                        </div>
                    </div>
                    <div id="requestsContainer"></div>
                </div>
                <div class="tab-pane fade" id="employeeRequestsContent" role="tabpanel" aria-labelledby="employee-requests-tab">
                    <div class="request-box" data-request-id="14846">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="request-title">طلب تصحيح بصمة - أحمد محمد</span>
                            <span class="badge bg-warning request-status">بالانتظار التعميد</span>
                        </div>
                        <div class="request-details">
                            <p><i class="fas fa-calendar-alt"></i> تاريخ الطلب: الأربعاء، 13 أغسطس، 2025 11:30 ص</p>
                            <p><i class="fas fa-user"></i> الموظف: أحمد محمد</p>
                            <p><i class="fas fa-file-alt"></i> نوع الطلب: تصحيح حضور</p>
                            <p><i class="fas fa-calendar-day"></i> يوم التصحيح: الثلاثاء، 12 أغسطس</p>
                            <p><i class="fas fa-hashtag"></i> رقم الطلب: 14846</p>
                        </div>
                        <div class="request-actions">
                            <button class="btn btn-success rounded-md">موافق</button>
                            <button class="btn btn-danger rounded-md" data-bs-toggle="modal" data-bs-target="#rejectReasonModal" data-request-id="14846">مرفوض</button>
                        </div>
                    </div>
                    <div class="request-box" data-request-id="14847">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="request-title">طلب إجازة - فاطمة علي</span>
                            <span class="badge bg-warning request-status">بالانتظار التعميد</span>
                        </div>
                        <div class="request-details">
                            <p><i class="fas fa-calendar-alt"></i> تاريخ الطلب: الخميس، 14 أغسطس، 2025 02:00 م</p>
                            <p><i class="fas fa-user"></i> الموظف: فاطمة علي</p>
                            <p><i class="fas fa-file-alt"></i> نوع الطلب: إجازة طارئة</p>
                            <p><i class="fas fa-calendar-day"></i> يوم الإجازة: 2025/8/20</p>
                            <p><i class="fas fa-hashtag"></i> رقم الطلب: 14847</p>
                        </div>
                        <div class="request-actions">
                            <button class="btn btn-success rounded-md">موافق</button>
                            <button class="btn btn-danger rounded-md" data-bs-toggle="modal" data-bs-target="#rejectReasonModal" data-request-id="14847">مرفوض</button>
                        </div>
                    </div>
                    <div class="text-center text-muted mt-4">
                        <p>لا توجد طلبات أخرى معلقة للموظفين حالياً.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="profileScreen" class="screen rounded-lg shadow-md mt-3">
            <div class="profile-details-screen" id="profileDetailsScreen">
                <div class="header">
                    <button class="btn btn-light rounded-full" onclick="hideProfileDetails()"><i class="fas fa-arrow-right"></i></button>
                    <h4 id="profileDetailsTitle"></h4>
                </div>
                <div id="profileDetailsContent"></div>
            </div>

            <div class="profile-header-new">
                <img src="https://placehold.co/100x100/A0A0A0/FFFFFF?text=صورة" class="avatar" alt="صورة الملف الشخصي">
                <h5>صالح سعيد عبدالسلام</h5>
                <p>ID: 12345</p>
            </div>

            <div class="profile-section-new">
                                
                <div class="profile-item-new" onclick="loadPersonalDetails()">
                    <div class="item-content"><i class="fas fa-user-check"></i><span>شخصي</span></div>
                    <i class="fas fa-chevron-left chevron-icon"></i>
                </div>
                <div class="profile-item-new" onclick="loadJobDetails()">
                    <div class="item-content"><i class="fas fa-id-badge"></i><span>البيانات الوظيفية</span></div>
                    <i class="fas fa-chevron-left chevron-icon"></i>
                </div>
                <div class="profile-item-new" onclick="loadFinancialDetails()">
                    <div class="item-content"><i class="fas fa-money-check-alt"></i><span>الراتب والتفاصيل المالية</span></div>
                    <i class="fas fa-chevron-left chevron-icon"></i>
                </div>
                <div class="profile-item-new" onclick="loadContractDetails()">
                    <div class="item-content"><i class="fas fa-file-signature"></i><span>العقود</span></div>
                    <i class="fas fa-chevron-left chevron-icon"></i>
                </div>
                 <div class="profile-item-new" onclick="showProfileDetails('الإجازات')">
                    <div class="item-content"><i class="fas fa-plane-departure"></i><span>الإجازات</span></div>
                    <i class="fas fa-chevron-left chevron-icon"></i>
                </div>
                <div class="profile-item-new" id="documents-section">
    <div class="item-content">
        <i class="fas fa-folder-open"></i>
        <span>مستندات</span>
    </div>
    <i class="fas fa-chevron-left chevron-icon"></i>
</div>

<div class="profile-submenu" id="documents-submenu" style="display: none;">
    <a href="#" class="submenu-item" id="add-file-btn" data-bs-toggle="modal" data-bs-target="#addFileModal">
        <i class="fas fa-plus-circle"></i> إضافة ملف
    </a>
    <a href="#" class="submenu-item" id="view-files-btn" data-bs-toggle="modal" data-bs-target="#viewFilesModal">
        <i class="fas fa-list-alt"></i> عرض الملفات
    </a>
</div>



                 <div class="profile-item-new" onclick="showProfileDetails('العهد')">
                    <div class="item-content"><i class="fas fa-box"></i><span>العهد</span></div>
                    <i class="fas fa-chevron-left chevron-icon"></i>
                </div>
                 <div class="profile-item-new" onclick="showAttendanceScreen('الحضور')">
                    <div class="item-content"><i class="fas fa-calendar-alt"></i><span>الحضور</span></div>
                    <i class="fas fa-chevron-left chevron-icon"></i>
                </div>
                <div class="profile-item-new" onclick="showProfileDetails('دعم مرسوم')">
                    <div class="item-content"><i class="fas fa-feather-alt"></i><span>دعم مرسوم</span></div>
                    <i class="fas fa-chevron-left chevron-icon"></i>
                </div>
            </div>
            
            <div class="profile-section-new">
                 <div class="profile-item-new">
                    <div class="item-content">
                        <i class="fas fa-bell"></i>
                        <span>الإشعارات</span>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="notificationSwitch" checked>
                        <label class="form-check-label" for="notificationSwitch"></label>
                    </div>
                </div>
                <div class="profile-item-new" onclick="showProfileDetails('تغيير كلمة المرور')">
                    <div class="item-content">
                        <i class="fas fa-key"></i>
                        <span>تغيير كلمة المرور</span>
                    </div>
                    <i class="fas fa-chevron-left chevron-icon"></i>
                </div>
            </div>

            <div class="profile-section-new">
                <div class="profile-item-new danger-item">
                    <div class="item-content">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>تسجيل الخروج</span>
                    </div>
                </div>
            </div>
        </div>

        <div id="reportScreen" class="screen rounded-lg shadow-md mt-3">
            <div class="attendance-header">
                <button class="btn btn-light rounded-full p-2" onclick="showAttendanceScreen()">
                    <i class="fas fa-arrow-right text-gray-700"></i> </button>
                <h2 class="text-xl font-semibold text-gray-800 m-0">التقرير الشهري</h2>
                <div></div> 
            </div>
            <div class="calendar-container">
                <div class="calendar-header">
                    <button class="btn btn-link p-0" id="prevReportMonthBtn"><i class="fas fa-chevron-right"></i></button>
                    <span id="currentReportMonthYear">أغسطس 2025</span>
                    <button class="btn btn-link p-0" id="nextReportMonthBtn"><i class="fas fa-chevron-left"></i></button>
                </div>
            </div>
            <div class="report-summary-box mb-3">
                <h5>مجموع ساعات العمل خلال الشهر</h5>
                <p id="totalWorkHours">106 ساعة 48 دقيقة</p>
                <small>من أصل 180 ساعة / 12 ساعة عمل إضافي</small>
            </div>
            <div class="report-summary-box mb-3">
                <h5>ساعات العمل الإضافي المؤكدة</h5>
                <p id="totalOvertimeHours">12 ساعة 0 دقيقة</p>
                <small>تم احتسابها بناءً على تسجيلاتك</small>
            </div>
            <h5 class="text-xl font-semibold text-gray-800 mb-3">ملخص الحضور الشهري</h5>
            <div class="attendance-summary-bar-container">
                <div class="attendance-summary-bar-segment segment-present" style="width: 40%;" data-bs-toggle="tooltip" data-bs-placement="top" title="الحضور: 11 يوم">11</div>
                <div class="attendance-summary-bar-segment segment-incomplete" style="width: 10%;" data-bs-toggle="tooltip" data-bs-placement="top" title="غير مكتمل: 2 يوم">2</div>
                <div class="attendance-summary-bar-segment segment-absent" style="width: 5%;" data-bs-toggle="tooltip" data-bs-placement="top" title="الغياب: 1 يوم">1</div>
                <div class="attendance-summary-bar-segment segment-unrequired" style="width: 10%;" data-bs-toggle="tooltip" data-bs-placement="top" title="غير مطلوب: 3 يوم">3</div>
                <div class="attendance-summary-bar-segment segment-rest" style="width: 15%;" data-bs-toggle="tooltip" data-bs-placement="top" title="أيام راحة: 4 يوم">4</div>
                <div class="attendance-summary-bar-segment segment-leave" style="width: 20%;" data-bs-toggle="tooltip" data-bs-placement="top" title="إجازة: 5 أيام">5</div>
            </div>
            <div class="attendance-legend mb-4">
                <div class="legend-item"><div class="legend-color-box segment-present"></div> حضور</div>
                <div class="legend-item"><div class="legend-color-box segment-incomplete"></div> غير مكتمل</div>
                <div class="legend-item"><div class="legend-color-box segment-absent"></div> غياب</div>
                <div class="legend-item"><div class="legend-color-box segment-unrequired"></div> غير مطلوب</div>
                <div class="legend-item"><div class="legend-color-box segment-rest"></div> راحة</div>
                <div class="legend-item"><div class="legend-color-box segment-leave"></div> إجازة</div>
            </div>
            <div class="alert alert-warning text-center rounded-lg" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                السجلات غير المكتملة قد تؤدي إلى حدوث خصومات بناءً على السياسات المطبقة بشركتكم.
            </div>
        </div>
<br/>
    </div> <nav class="navbar fixed-bottom bottom-nav d-flex justify-content-around">
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

    <div class="offcanvas offcanvas-start" tabindex="-1" id="announcementsOffcanvas" aria-labelledby="announcementsOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="announcementsOffcanvasLabel">الإعلانات</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <p>لا توجد إعلانات جديدة حالياً.</p>
            <div class="card mb-2">
                <div class="card-body">
                    <h6 class="card-title">إعلان هام 1</h6>
                    <p class="card-text"><small class="text-muted">هذا إعلان تجريبي.</small></p>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-body">
                    <h6 class="card-title">تذكير بالاجتماع</h6>
                    <p class="card-text"><small class="text-muted">اجتماع قسم التسويق غداً.</small></p>
                </div>
            </div>
        </div>
    </div>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="notificationsOffcanvas" aria-labelledby="notificationsOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="notificationsOffcanvasLabel">الإشعارات</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <p>لا توجد إشعارات غير مقروءة.</p>
            <div class="card mb-2">
                <div class="card-body">
                    <h6 class="card-title">طلب إجازة جديد</h6>
                    <p class="card-text"><small class="text-muted">تم استلام طلب إجازة من أحمد.</small></p>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-body">
                    <h6 class="card-title">تحديث حالة طلب</h6>
                    <p class="card-text"><small class="text-muted">تمت الموافقة على طلبك رقم #1000.</small></p>
                </div>
            </div>
        </div>
    </div>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="profileOffcanvas" aria-labelledby="profileOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="profileOffcanvasLabel">ملفك الشخصي</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="text-center mb-4">
                <img src="https://placehold.co/100x100/A0A0A0/FFFFFF?text=صورة" class="rounded-circle mb-3 border border-3 border-primary" alt="صورة الملف الشخصي">
                <h5 class="card-title">صالح سعيد عبدالسلام</h5>
                <p class="card-text text-muted">صالح@مثال.كوم</p>
                <p class="card-text text-muted">ID: 12345</p>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    القسم:
                    <span class="fw-bold">الموارد البشرية</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    تاريخ البدء:
                    <span class="fw-bold">2023/1/1</span>
                </li>
                <li class="list-group-item">
                    <button class="btn btn-info w-100 rounded-md">تعديل المعلومات</button>
                </li>
                <li class="list-group-item">
                    <button class="btn btn-danger w-100 rounded-md">تسجيل الخروج</button>
                </li>
            </ul>
        </div>
    </div>

    <div class="modal fade" id="correctAttendanceModal" tabindex="-1" aria-labelledby="correctAttendanceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="correctAttendanceModalLabel">تصحيح الحضور</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="correctionForm">
                    <div class="modal-body">
                        <div id="responseMessage" class="alert mb-3 d-none"></div>
                        <div class="mb-3">
                            <label for="correctionDate" class="form-label">التاريخ</label>
                            <input type="date" class="form-control rounded-md" id="correctionDate" required>
                        </div>
                        <div class="mb-3">
                            <label for="checkInTime" class="form-label">وقت الدخول</label>
                            <input type="time" class="form-control rounded-md" id="checkInTime" required>
                        </div>
                        <div class="mb-3">
                            <label for="checkOutTime" class="form-label">وقت الخروج</label>
                            <input type="time" class="form-control rounded-md" id="checkOutTime" required>
                        </div>
                        <div class="mb-3">
                            <label for="correctionReason" class="form-label">السبب</label>
                            <select class="form-select rounded-md" id="correctionReason" required>
                                <option value="">-- اختر السبب --</option>
                                <option value="نسيان البصمة">نسيان البصمة</option>
                                <option value="مشكلة في تطبيق الجوال">مشكلة في تطبيق الجوال</option>
                                <option value="مشكلة في جهاز البصمة">مشكلة في جهاز البصمة</option>
                                <option value="مشكلة في الإتصال بالإنترنت">مشكلة في الإتصال بالإنترنت</option>
                                <option value="العمل عن بعد">العمل عن بعد</option>
                                <option value="تبديل الدوام مع موظف أخر">تبديل الدوام مع موظف أخر</option>
                                <option value="عمل إضافي بعد فترة بصمة الخروج">عمل إضافي بعد فترة بصمة الخروج</option>
                                <option value="زيارة موقع خارجي">زيارة موقع خارجي</option>
                                <option value="اخرى">اخرى</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-md" data-bs-dismiss="modal">إغلاق</button>
                        <button type="submit" class="btn btn-primary rounded-md">إرسال طلب</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="leaveRequestModal" tabindex="-1" aria-labelledby="leaveRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-bottom">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="leaveRequestModalLabel">طلب إجازة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="leaveRequestForm">
                <div class="modal-body">
                    <div id="leaveResponseMessage" class="alert mb-3 d-none"></div>
                    <div class="mb-3">
                        <label for="leaveType" class="form-label">نوع الإجازة</label>
                        <select class="form-select rounded-md" id="leaveType" required>
                            <option value="">اختر...</option>
                            <option value="سنوية">سنوية</option>
                            <option value="مرضية">مرضية</option>
                            <option value="زواج">زواج</option>
                            <option value="وفاة زوج، اصول او فروع">وفاة زوج، اصول او فروع</option>
                            <option value="مولود جديد">مولود جديد (يجوز اتخاذ الاجازة في خلال ٧ ايام من تاريخ الميلاد فقط)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="startDate" class="form-label">تاريخ البدء</label>
                        <input type="date" class="form-control rounded-md" id="startDate" required>
                    </div>
                    <div class="mb-3">
                        <label for="endDate" class="form-label">تاريخ الانتهاء</label>
                        <input type="date" class="form-control rounded-md" id="endDate" required>
                    </div>

                    <div class="mb-3">
                        <label for="totalDays" class="form-label">عدد أيام الإجازة الفعلية (بدون الجمعة والسبت)</label>
                        <input type="number" class="form-control rounded-md" id="totalDays" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="reentryVisa" class="form-label">تأشيرة خروج وعودة</label>
                        <select class="form-select rounded-md" id="reentryVisa" required>
                            <option value="">اختر...</option>
                            <option value="نعم">نعم</option>
                            <option value="لا">لا</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="visaDays" class="form-label">فترة التأشيرة</label>
                        <select class="form-select rounded-md" id="visaDays" required>
                            <option value="">اختر...</option>
                            <option value="2 شهر/شهور">2 شهر/شهور</option>
                            <option value="3 شهر/شهور">3 شهر/شهور</option>
                            <option value="4 شهر/شهور">4 شهر/شهور</option>
                            <option value="5 شهر/شهور">5 شهر/شهور</option>
                            <option value="6 شهر/شهور">6 شهر/شهور</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="leaveReason" class="form-label">السبب</label>
                        <textarea class="form-control rounded-md" id="leaveReason" rows="3" maxlength="500" placeholder="اكتب سبب طلب الإجازة..." required></textarea>
                        <div class="form-text">الحد الأقصى 500 حرف</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-md" data-bs-dismiss="modal">إغلاق</button>
                    <button type="submit" class="btn btn-primary rounded-md">إرسال طلب</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <div class="modal fade" id="overtimeRequestModal" tabindex="-1" aria-labelledby="overtimeRequestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-bottom">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="overtimeRequestModalLabel">طلب عمل إضافي</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="overtimeRequestLoading" class="text-center py-3 d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">جاري الإرسال...</span>
                        </div>
                        <p class="mt-2 text-muted">جاري إرسال طلب العمل الإضافي...</p>
                    </div>
                    <div id="overtimeRequestError" class="alert alert-danger d-none" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span id="overtimeRequestErrorMessage">حدث خطأ</span>
                    </div>
                    <div id="overtimeRequestSuccess" class="alert alert-success d-none" role="alert">
                        <i class="fas fa-check-circle"></i>
                        <span id="overtimeRequestSuccessMessage">تم إرسال الطلب بنجاح</span>
                    </div>
                    <form id="overtimeRequestForm">
                        <div class="mb-3">
                            <label for="overtimeDate" class="form-label">التاريخ <span class="text-danger">*</span></label>
                            <input type="date" class="form-control rounded-md" id="overtimeDate" required>
                            <div class="invalid-feedback">يرجى اختيار التاريخ</div>
                        </div>
                        <div class="mb-3">
                            <label for="overtimeHours" class="form-label">عدد الساعات الإضافية <span class="text-danger">*</span></label>
                            <input type="number" class="form-control rounded-md" id="overtimeHours" min="0.5" max="12" step="0.5" required>
                            <div class="form-text">أدخل عدد الساعات (من 0.5 إلى 12 ساعة)</div>
                            <div class="invalid-feedback">يرجى إدخال عدد الساعات الإضافية</div>
                        </div>
                        <div class="mb-3">
                            <label for="overtimeReason" class="form-label">السبب <span class="text-danger">*</span></label>
                            <textarea class="form-control rounded-md" id="overtimeReason" rows="3" required placeholder="اكتب سبب طلب العمل الإضافي..."></textarea>
                            <div class="invalid-feedback">يرجى كتابة سبب طلب العمل الإضافي</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-md" data-bs-dismiss="modal">إغلاق</button>
                    <button type="button" class="btn btn-primary rounded-md" id="submitOvertimeRequest">
                        <i class="fas fa-paper-plane me-2"></i>إرسال طلب
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="newRequestModal" tabindex="-1" aria-labelledby="newRequestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-bottom">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newRequestModalLabel">طلب جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>هنا يمكنك إضافة نموذج لإنشاء أنواع مختلفة من الطلبات الجديدة.</p>
                    <form>
                        <div class="mb-3">
                            <label for="requestType" class="form-label">نوع الطلب</label>
                            <select class="form-select rounded-md" id="requestType">
                                <option selected>اختر نوع الطلب...</option>
                                <option>طلب صيانة</option>
                                <option>طلب دعم فني</option>
                                <option>طلب مواد مكتبية</option>
                                <option>أخرى</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="requestDetails" class="form-label">التفاصيل</label>
                            <textarea class="form-control rounded-md" id="requestDetails" rows="4"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-md" data-bs-dismiss="modal">إغلاق</button>
                    <button type="button" class="btn btn-success rounded-md">إرسال الطلب</button>
                </div>
            </div>
        </div>
    </div>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="attendanceOptionsOffcanvas" aria-labelledby="attendanceOptionsOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="attendanceOptionsOffcanvasLabel">عرض التقويم</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="list-group list-group-flush">
                <li class="list-group-item list-group-item-action" onclick="setAttendanceView('day')" data-bs-dismiss="offcanvas">
                    <i class="fas fa-calendar-day me-2"></i> يوم
                </li>
                <li class="list-group-item list-group-item-action" onclick="setAttendanceView('week')" data-bs-dismiss="offcanvas">
                    <i class="fas fa-calendar-week me-2"></i> أسبوع
                </li>
                <li class="list-group-item list-group-item-action" onclick="setAttendanceView('month')" data-bs-dismiss="offcanvas">
                    <i class="fas fa-calendar-alt me-2"></i> شهر
                </li>
            </ul>
        </div>
    </div>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="reportOffcanvas" aria-labelledby="reportOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="reportOffcanvasLabel">التقرير</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="list-group list-group-flush">
                <li class="list-group-item list-group-item-action" onclick="showAttendanceScreen(); setAttendanceView('day')" data-bs-dismiss="offcanvas">
                    <i class="fas fa-user-clock me-2"></i> الحضور
                </li>
                <li class="list-group-item list-group-item-action" onclick="showReportScreen()" data-bs-dismiss="offcanvas">
                    <i class="fas fa-chart-pie me-2"></i> التقرير الشهري
                </li>
            </ul>
        </div>
    </div>

    <div class="modal fade" id="rejectReasonModal" tabindex="-1" aria-labelledby="rejectReasonModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-bottom">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectReasonModalLabel">سبب الرفض</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label for="rejectionReasonTextarea" class="form-label">الرجاء إدخال سبب الرفض:</label>
                            <textarea class="form-control rounded-md" id="rejectionReasonTextarea" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-md" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-danger rounded-md" id="saveRejectReasonBtn">حفظ</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addFileModal" tabindex="-1" aria-labelledby="addFileModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="addFileModalLabel">إضافة مستند جديد</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="add-document-form" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="doc_type" class="form-label">نوع المستند</label>
                        <select class="form-select" id="doc_type" name="doc_type" required>
                            <option value="">-- اختر النوع --</option>
                            <option value="Passport">جواز سفر</option>
                            <option value="ID">بطاقة الهوية / إقامة</option>
                            <option value="Contract">عقد عمل</option>
                            <option value="Certificate">شهادة</option>
                            <option value="Other">أخرى</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">الوصف</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="document_file" class="form-label">الملف (مسموح بـ: pdf, docx, jpg, png)</label>
                        <input type="file" class="form-control" id="document_file" name="document_file" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                        <button type="submit" class="btn btn-primary">حفظ المستند</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="viewFilesModal" tabindex="-1" aria-labelledby="viewFilesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewFilesModalLabel">المستندات المرفوعة</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="document-list-container">
                    <p>جاري تحميل المستندات...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // الحصول على جميع روابط شريط التنقل
            const navLinks = document.querySelectorAll('.bottom-nav .nav-link');
            // الحصول على جميع div الشاشات
            const screens = document.querySelectorAll('.screen');
            const currentDateSpan = document.getElementById('currentDate');
            const attendanceToggleButton = document.getElementById('attendanceToggleButton');
            const workTimeCounter = document.getElementById('workTimeCounter');

            let startTime = null;
            let timerInterval = null;

            /**
             * دالة لعرض شاشة معينة وإخفاء البقية.
             * @param {string} screenId - معرف الشاشة المراد عرضها.
             */
            function showScreen(screenId) {
    // إزالة فئة 'active' من جميع الشاشات وإخفائها
    screens.forEach(screen => {
        screen.classList.remove('active');
    });

    // إضافة فئة 'active' للشاشة المستهدفة لعرضها وتطبيق الأنيميشن
    const targetScreen = document.getElementById(screenId);
    if (targetScreen) {
        targetScreen.classList.add('active');
    }
    
    // *** ADD THIS CODE ***
    // If we are showing the attendance screen, tell the calendar to resize itself.
    // The timeout ensures the screen is fully visible before resizing.
    if (screenId === 'attendanceScreen' && calendar) {
        setTimeout(function() {
            calendar.updateSize();
        }, 300); // Delay matches the CSS transition time
    }
    }   

            /**
             * دالة لتحديث حالة 'active' لروابط شريط التنقل.
             * @param {string} activeLinkId - معرف الشاشة النشطة لتحديد الرابط المقابل.
             */
            function updateNavActiveState(activeLinkId) {
                // إزالة فئة 'active' من جميع روابط التنقل
                navLinks.forEach(link => {
                    link.classList.remove('active');
                });
                // إضافة فئة 'active' للرابط الذي يتوافق مع الشاشة النشطة
                const activeLink = document.querySelector(`[data-screen-id="${activeLinkId}"]`);
                if (activeLink) {
                    activeLink.classList.add('active');
                }
            }

            // إضافة مستمعي الأحداث للنقر على روابط شريط التنقل
            navLinks.forEach(link => {
                link.addEventListener('click', (event) => {
                    event.preventDefault(); // منع السلوك الافتراضي للرابط (مثل الانتقال لصفحة جديدة)
                    const screenId = event.currentTarget.dataset.screenId; // الحصول على معرف الشاشة من سمة data-screen-id
                    showScreen(screenId); // عرض الشاشة المحددة
                    updateNavActiveState(screenId); // تحديث حالة الرابط النشط

                    // إضافة منطق خاص لبعض الشاشات عند الانتقال إليها
                    if (screenId === 'attendanceScreen') {
                        setAttendanceView('day');
                    }
                });
            });

            // دالة لعرض شاشة الحضور عند النقر على زر التاريخ
            window.showAttendanceScreen = function() {
                showScreen('attendanceScreen');
                updateNavActiveState('attendanceScreen');
                setAttendanceView('day'); // عرض "يوم" افتراضياً عند الانتقال لشاشة الحضور
            };

            // دالة لعرض شاشة التقرير
            window.showReportScreen = function() {
                showScreen('reportScreen');
                // لا يوجد تاب في الشريط السفلي للتقرير، لذا لا نحدّث حالة التنقل السفلي
                renderReportSummary(currentReportMonth); // تحديث ملخص التقرير الشهري
            };

            // تحديث التاريخ الحالي على الزر
            function updateCurrentDate() {
                const today = new Date();
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', calendar: 'gregory' };
                currentDateSpan.textContent = today.toLocaleDateString('en-SA', options);
            }

            // وظيفة تحديث عداد الوقت
            function updateTimer() {
                if (startTime) {
                    const now = new Date();
                    const elapsedMilliseconds = now - startTime;
                    const elapsedSeconds = Math.floor(elapsedMilliseconds / 1000);
                    const hours = Math.floor(elapsedSeconds / 3600);
                    const minutes = Math.floor((elapsedSeconds % 3600) / 60);
                    const seconds = elapsedSeconds % 60;

                    const formattedTime = [
                        hours.toString().padStart(2, '0'),
                        minutes.toString().padStart(2, '0')
                    ].join(':');
                    // يمكن إضافة الثواني إذا أردت: + ':' + seconds.toString().padStart(2, '0')

                    workTimeCounter.textContent = formattedTime;
                } else {
                    workTimeCounter.textContent = '00:00';
                }
            }

            // وظيفة لتسجيل الحضور/الانصراف
            attendanceToggleButton.addEventListener('click', () => {
                if (startTime) {
                    // تسجيل الانصراف
                    clearInterval(timerInterval);
                    startTime = null;
                    attendanceToggleButton.textContent = 'تسجيل الحضور';
                    // هنا يمكنك إضافة منطق إرسال بيانات الانصراف إلى الخادم
                } else {
                    // تسجيل الحضور
                    startTime = new Date();
                    attendanceToggleButton.textContent = 'تسجيل الانصراف';
                    timerInterval = setInterval(updateTimer, 1000); // تحديث العداد كل ثانية
                    // هنا يمكنك إضافة منطق إرسال بيانات الحضور إلى الخادم
                }
            });
            showScreen('homeScreen');
            updateNavActiveState('homeScreen');
            updateCurrentDate(); // تحديث التاريخ عند التحميل الأولي
            updateTimer(); // بدء تحديث العداد
            updateAttendanceBox();
            loadLastThreeRequests();
            // Initialize tooltips for dynamically added elements (e.g., summary bar)
            const enableTooltips = () => {
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                const tooltipList = tooltipTriggerList.map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
            };
            enableTooltips(); // Enable tooltips on initial load

            // --------------------------------------------------------------------
            // منطق شاشة الطلبات الجديدة
            // --------------------------------------------------------------------
            const ordersScreen = document.getElementById('ordersScreen');
            const rejectReasonModal = new bootstrap.Modal(document.getElementById('rejectReasonModal'));
            const rejectReasonTextarea = document.getElementById('rejectionReasonTextarea');
            const saveRejectReasonBtn = document.getElementById('saveRejectReasonBtn');
            let currentRequestIdToReject = null;

            // عند فتح مودال الرفض، احفظ معرف الطلب
            document.getElementById('rejectReasonModal').addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget; // الزر الذي أطلق المودال
                currentRequestIdToReject = button.dataset.requestId; // استخراج معرف الطلب
                rejectReasonTextarea.value = ''; // مسح أي نص سابق
            });

            // عند النقر على زر "حفظ" في مودال الرفض
            saveRejectReasonBtn.addEventListener('click', () => {
                const reason = rejectReasonTextarea.value;
                if (currentRequestIdToReject && reason.trim() !== '') {
                    console.log(`Rejecting request ID: ${currentRequestIdToReject} with reason: ${reason}`);
                    // هنا يمكنك إضافة منطق لإرسال طلب الرفض إلى الخادم
                    // بعد الإرسال بنجاح، يمكنك إغلاق المودال وتحديث قائمة الطلبات
                    rejectReasonModal.hide();
                    // مثال: تحديث حالة الطلب في الواجهة (لأغراض العرض فقط)
                    const requestBox = ordersScreen.querySelector(`.request-box[data-request-id="${currentRequestIdToReject}"]`);
                    if (requestBox) {
                        const statusBadge = requestBox.querySelector('.request-status');
                        if (statusBadge) {
                            statusBadge.textContent = 'مرفوض';
                            statusBadge.classList.remove('bg-warning', 'bg-success');
                            statusBadge.classList.add('bg-danger');
                        }
                        const actionsDiv = requestBox.querySelector('.request-actions');
                        if (actionsDiv) {
                            actionsDiv.innerHTML = `<p class="text-danger m-0"><i class="fas fa-info-circle me-1"></i> تم الرفض: ${reason}</p>`;
                        }
                    }
                } else {
                    alert('الرجاء إدخال سبب الرفض.'); // استخدام alert مؤقت لأغراض العرض، يفضل استخدام مودال مخصص
                }
            });

            // عند النقر على زر "موافق" (لأغراض العرض فقط)
            ordersScreen.addEventListener('click', (event) => {
                if (event.target.classList.contains('btn-success') && event.target.textContent === 'موافق') {
                    const requestBox = event.target.closest('.request-box');
                    if (requestBox) {
                        const requestId = requestBox.dataset.requestId;
                        console.log(`Approving request ID: ${requestId}`);
                        // هنا يمكنك إضافة منطق لإرسال طلب الموافقة إلى الخادم
                        // بعد الإرسال بنجاح، يمكنك تحديث حالة الطلب في الواجهة
                        const statusBadge = requestBox.querySelector('.request-status');
                        if (statusBadge) {
                            statusBadge.textContent = 'موافق عليه';
                            statusBadge.classList.remove('bg-warning', 'bg-danger');
                            statusBadge.classList.add('bg-success');
                        }
                        const actionsDiv = requestBox.querySelector('.request-actions');
                        if (actionsDiv) {
                            actionsDiv.innerHTML = `<p class="text-success m-0"><i class="fas fa-check-circle me-1"></i> تم الموافقة</p>`;
                        }
                    }
                }
            });
            
            // --------------------------------------------------------------------
            // منطق شاشة الملف الشخصي الجديدة
            // --------------------------------------------------------------------
            
            const profileDetailsScreen = document.getElementById('profileDetailsScreen');
            const profileDetailsTitle = document.getElementById('profileDetailsTitle');
            const profileDetailsContent = document.getElementById('profileDetailsContent');
            
            // بيانات وهمية لتفاصيل الملف الشخصي
            
            
            window.toggleSubMenu = function(menuId) {
                const menu = document.getElementById(menuId);
                const icon = menu.previousElementSibling.querySelector('i:last-child');
                
                // إغلاق أي قائمة فرعية مفتوحة أخرى
                document.querySelectorAll('.profile-menu').forEach(m => {
                    if (m.id !== menuId && m.classList.contains('show')) {
                        m.classList.remove('show');
                        m.previousElementSibling.querySelector('i:last-child').classList.replace('fa-chevron-down', 'fa-chevron-left');
                    }
                });
                
                menu.classList.toggle('show');
                if (menu.classList.contains('show')) {
                    icon.classList.replace('fa-chevron-left', 'fa-chevron-down');
                } else {
                    icon.classList.replace('fa-chevron-down', 'fa-chevron-left');
                }
            }
            
            window.showProfileDetails = function(title) {
                const content = profileData[title];
                if (content) {
                    profileDetailsTitle.textContent = title;
                    profileDetailsContent.innerHTML = content;
                    profileDetailsScreen.classList.add('show');
                    document.body.style.overflow = 'hidden'; // منع التمرير في الخلفية
                }
            }

            window.hideProfileDetails = function() {
                profileDetailsScreen.classList.remove('show');
                document.body.style.overflow = ''; // إعادة التمرير
            }
        });
    </script>
   <script>
 document.addEventListener('DOMContentLoaded', function() {
    // Get the form and modal elements
    const correctionForm = document.getElementById('correctionForm');
    const correctionModal = document.getElementById('correctAttendanceModal');
    const responseMessage = document.getElementById('responseMessage');
    
    // Check if elements exist
    if (!correctionForm || !correctionModal || !responseMessage) {
        console.error('Required form elements not found');
        return;
    }
    
    // Set default date to today
    const today = new Date().toISOString().split('T')[0];
    const dateInput = document.getElementById('correctionDate');
    const inTimeInput = document.getElementById('checkInTime');
    const outTimeInput = document.getElementById('checkOutTime');
    const reasonSelect = document.getElementById('correctionReason'); // Changed to reasonSelect
    
    if (dateInput) dateInput.value = today;
    if (inTimeInput) inTimeInput.value = '08:00';
    if (outTimeInput) outTimeInput.value = '16:00';
    
    // Handle form submission
    correctionForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Hide previous messages
        responseMessage.classList.add('d-none');
        
        // Get form values
        const date = dateInput?.value;
        const inTime = inTimeInput?.value;
        const outTime = outTimeInput?.value;
        const reason = reasonSelect?.value; // Get selected value from select element
        
        // Validation
        if (!date || !inTime || !outTime || !reason) {
            showMessage('يرجى ملء جميع الحقول المطلوبة', 'error');
            return;
        }
        
        if (inTime >= outTime) {
            showMessage('وقت الدخول يجب أن يكون قبل وقت الخروج', 'error');
            return;
        }
        
        // Validate date (not in future)
        const selectedDate = new Date(date);
        const currentDate = new Date();
        currentDate.setHours(0, 0, 0, 0);
        
        if (selectedDate > currentDate) {
            showMessage('لا يمكن اختيار تاريخ في المستقبل', 'error');
            return;
        }
        
        // Check if date is too old (more than 30 days ago)
        const thirtyDaysAgo = new Date();
        thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
        thirtyDaysAgo.setHours(0, 0, 0, 0);
        
        if (selectedDate < thirtyDaysAgo) {
            showMessage('لا يمكن تصحيح البيانات للتواريخ الأقدم من 30 يوماً', 'error');
            return;
        }
        
        // Validate working hours (reasonable range)
        const inHour = parseInt(inTime.split(':')[0]);
        const outHour = parseInt(outTime.split(':')[0]);
        
        if (inHour < 6 || inHour > 12) {
            showMessage('وقت الدخول يجب أن يكون بين 6:00 و 12:00', 'error');
            return;
        }
        
        if (outHour < 12 || outHour > 23) {
            showMessage('وقت الخروج يجب أن يكون بين 12:00 و 23:00', 'error');
            return;
        }
        
        // Calculate work duration
        const workDuration = calculateWorkDuration(inTime, outTime);
        if (workDuration < 4) {
            if (!confirm('مدة العمل أقل من 4 ساعات. هل تريد المتابعة؟')) {
                return;
            }
        }
        
        // Prepare form data for submission
        const formData = {
            correctionDate: date,
            checkInTime: inTime,
            checkOutTime: outTime,
            correctionReason: reason
        };
        
        // Submit to backend
        submitToBackend(formData);
    });
    
    // Function to calculate work duration in hours
    function calculateWorkDuration(inTime, outTime) {
        const [inHour, inMin] = inTime.split(':').map(Number);
        const [outHour, outMin] = outTime.split(':').map(Number);
        
        const inMinutes = inHour * 60 + inMin;
        const outMinutes = outHour * 60 + outMin;
        
        return (outMinutes - inMinutes) / 60;
    }
    
    // Function to show message
    function showMessage(message, type) {
        if (!responseMessage) return;
        
        responseMessage.textContent = message;
        responseMessage.classList.remove('d-none', 'alert-success', 'alert-danger');
        
        if (type === 'success') {
            responseMessage.classList.add('alert-success');
        } else {
            responseMessage.classList.add('alert-danger');
        }
        
        // Scroll to message
        responseMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Auto-hide success message after 5 seconds
        if (type === 'success') {
            setTimeout(() => {
                responseMessage.classList.add('d-none');
            }, 5000);
        }
    }
    // Add this new function to your main script tag
function loadLastThreeRequests() {
    const container = document.querySelector('.requests-table .list-group');
    if (!container) return;

    container.innerHTML = '<li>جاري تحميل الطلبات...</li>'; // Loading message

    fetch("<?php echo site_url('users2/get_last_requests'); ?>")
        .then(response => response.json())
        .then(data => {
            container.innerHTML = ''; // Clear loading message
            if (data.status === 'success' && data.data.length > 0) {
                data.data.forEach(req => {
                    let statusClass = 'bg-secondary';
                    let statusText = 'غير معروف';

                    switch (req.status) {
                        case '0': statusClass = 'bg-warning'; statusText = 'معلق'; break;
                        case '1': statusClass = 'bg-info'; statusText = 'قيد المراجعة'; break;
                        case '2': statusClass = 'bg-success'; statusText = 'موافق عليه'; break;
                        case '3': statusClass = 'bg-danger'; statusText = 'مرفوض'; break;
                    }

                    const requestHtml = `
                        <li class="list-group-item">
                            <div>
                                <h6 class="mb-1">${req.order_name || 'طلب'}</h6>
                                <small>حالة: <span class="badge ${statusClass}">${statusText}</span></small>
                            </div>
                            <span>${req.date}</span>
                        </li>
                    `;
                    container.innerHTML += requestHtml;
                });
            } else {
                container.innerHTML = '<li class="list-group-item text-center text-muted">لا توجد طلبات حديثة.</li>';
            }
        })
        .catch(error => {
            console.error('Error fetching last requests:', error);
            container.innerHTML = '<li class="list-group-item text-center text-danger">فشل في تحميل الطلبات.</li>';
        });
}
    // Add this new function to your main script tag
function updateAttendanceBox() {
    const workTimeCounter = document.getElementById('workTimeCounter');
    const attendanceToggleButton = document.getElementById('attendanceToggleButton');

    fetch("<?php echo site_url('users2/get_today_attendance_summary'); ?>")
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' && data.data) {
                const punches = data.data;
                workTimeCounter.textContent = punches.workDuration || '00:00:00';

                if (punches.firstCheckIn && !punches.lastCheckOut) {
                    attendanceToggleButton.textContent = 'تسجيل الانصراف';
                } else {
                    attendanceToggleButton.textContent = 'تسجيل الحضور';
                }
            } else {
                 workTimeCounter.textContent = '00:00:00';
                 attendanceToggleButton.textContent = 'تسجيل الحضور';
            }
        })
        .catch(error => {
            console.error('Error fetching attendance summary:', error);
            workTimeCounter.textContent = 'خطأ';
        });
}
    
    // Function to submit data to backend
    function submitToBackend(formData) {
        const submitBtn = correctionForm.querySelector('button[type="submit"]');
        if (!submitBtn) return;
        
        const originalText = submitBtn.textContent;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>جاري الإرسال...';
        submitBtn.disabled = true;
        
        // Construct the API URL
        let apiUrl;
        const currentPath = window.location.pathname;
        
        if (currentPath.includes('users2')) {
            // If we're already in users2 controller
            apiUrl = window.location.origin + currentPath.split('/').slice(0, -1).join('/') + '/submit_correction_request';
        } else {
            // If we're in a different controller
            apiUrl = window.location.origin + '/users2/submit_correction_request';
        }
        
        // Get CSRF token if exists
        const csrfInput = document.querySelector('input[name*="csrf"]');
        if (csrfInput) {
            formData[csrfInput.name] = csrfInput.value;
        }
        
        console.log('Submitting to:', apiUrl);
        console.log('Data:', formData);
        
        // Using fetch API to submit data
        fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('Error response:', text);
                    throw new Error(`HTTP error! status: ${response.status}`);
                });
            }
            
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                return response.text().then(text => {
                    console.error('Non-JSON response:', text);
                    throw new Error('Response is not JSON');
                });
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            
            if (data.status === 'success') {
                showMessage(data.message, 'success');
                
                // Reset form after success
                setTimeout(function() {
                    resetForm();
                    closeModal();
                }, 2500);
            } else {
                showMessage(data.message || 'حدث خطأ غير معروف', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            
            let errorMessage = 'حدث خطأ في الإتصال. يرجى المحاولة مرة أخرى.';
            
            if (error.message.includes('HTTP error! status: 404')) {
                errorMessage = 'الرابط غير موجود. تأكد من إعدادات النظام.';
            } else if (error.message.includes('HTTP error! status: 500')) {
                errorMessage = 'خطأ في الخادم. يرجى التحقق من سجلات النظام.';
            } else if (error.message.includes('Response is not JSON')) {
                errorMessage = 'استجابة غير صحيحة من الخادم. تحقق من كود المتحكم.';
            }
            
            showMessage(errorMessage, 'error');
        })
        .finally(() => {
            // Restore button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    }
    
    // Function to reset form
    function resetForm() {
        correctionForm.reset();
        const today = new Date().toISOString().split('T')[0];
        
        if (dateInput) dateInput.value = today;
        if (inTimeInput) inTimeInput.value = '08:00';
        if (outTimeInput) outTimeInput.value = '16:00';
        if (reasonSelect) reasonSelect.selectedIndex = 0; // Reset select to first option
        
        responseMessage.classList.add('d-none');
    }
    
    // Function to close modal
    function closeModal() {
        try {
            const modal = bootstrap.Modal.getInstance(correctionModal);
            if (modal) {
                modal.hide();
            } else {
                const newModal = new bootstrap.Modal(correctionModal);
                newModal.hide();
            }
        } catch (e) {
            console.log('Could not close modal via Bootstrap JS:', e);
            const closeBtn = correctionModal.querySelector('[data-bs-dismiss="modal"]');
            if (closeBtn) {
                closeBtn.click();
            }
        }
    }
    
    // Reset form when modal is opened
    correctionModal.addEventListener('shown.bs.modal', function() {
        resetForm();
    });
    
    // Reset form when modal is closed
    correctionModal.addEventListener('hidden.bs.modal', function() {
        resetForm();
    });
    
    // Add validation for time inputs
    function validateTimeFormat(timeString) {
        const timeRegex = /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/;
        return timeRegex.test(timeString);
    }
    
    // Add event listeners for time inputs
    [inTimeInput, outTimeInput].forEach(input => {
        if (input) {
            input.addEventListener('blur', function() {
                if (this.value && !validateTimeFormat(this.value)) {
                    showMessage('تنسيق الوقت غير صحيح. استخدم تنسيق HH:MM', 'error');
                    this.focus();
                }
            });
        }
    });
    
    // Handle "اخرى" option - show custom input
    if (reasonSelect) {
        reasonSelect.addEventListener('change', function() {
            const customReasonDiv = document.getElementById('customReasonDiv');
            
            if (this.value === 'اخرى') {
                if (!customReasonDiv) {
                    // Create custom reason input
                    const div = document.createElement('div');
                    div.id = 'customReasonDiv';
                    div.className = 'mb-3';
                    div.innerHTML = `
                        <label for="customReason" class="form-label">اكتب السبب</label>
                        <textarea class="form-control rounded-md" id="customReason" rows="3" maxlength="500" placeholder="اكتب السبب بالتفصيل..."></textarea>
                        <div class="form-text">الحد الأقصى 500 حرف</div>
                    `;
                    this.parentElement.insertAdjacentElement('afterend', div);
                    
                    // Focus on the textarea
                    setTimeout(() => {
                        document.getElementById('customReason').focus();
                    }, 100);
                }
            } else {
                // Remove custom reason input if exists
                if (customReasonDiv) {
                    customReasonDiv.remove();
                }
            }
        });
    }
    
    // Update form submission to handle custom reason
    const originalFormSubmit = correctionForm.onsubmit;
    correctionForm.addEventListener('submit', function(e) {
        const customReasonTextarea = document.getElementById('customReason');
        if (reasonSelect.value === 'اخرى' && customReasonTextarea) {
            const customReason = customReasonTextarea.value.trim();
            if (!customReason) {
                e.preventDefault();
                showMessage('يرجى كتابة السبب في حقل النص', 'error');
                return false;
            }
            // Update the reason value to include custom text
            reasonSelect.setAttribute('data-custom-reason', customReason);
        }
    });
  });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get form elements
    const leaveRequestForm = document.getElementById('leaveRequestForm');
    const leaveRequestModal = document.getElementById('leaveRequestModal');
    const leaveResponseMessage = document.getElementById('leaveResponseMessage');
    
    // Check if elements exist
    if (!leaveRequestForm || !leaveRequestModal || !leaveResponseMessage) {
        console.error('Leave request form elements not found');
        return;
    }
    
    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    
    if (startDateInput) startDateInput.min = today;
    if (endDateInput) endDateInput.min = today;
    
    // Handle visa days visibility based on reentry visa selection
    const reentryVisaSelect = document.getElementById('reentryVisa');
    const visaDaysDiv = document.getElementById('visaDays').closest('.mb-3');
    
    if (reentryVisaSelect && visaDaysDiv) {
        reentryVisaSelect.addEventListener('change', function() {
            if (this.value === 'لا') {
                visaDaysDiv.style.display = 'none';
                document.getElementById('visaDays').value = '';
                document.getElementById('visaDays').removeAttribute('required');
            } else {
                visaDaysDiv.style.display = 'block';
                document.getElementById('visaDays').setAttribute('required', 'required');
            }
        });
    }
    
    // Handle start date change to update end date minimum
    if (startDateInput && endDateInput) {
        startDateInput.addEventListener('change', function() {
            endDateInput.min = this.value;
            if (endDateInput.value && endDateInput.value < this.value) {
                endDateInput.value = this.value;
            }
        });
    }
    
    // Handle form submission
    leaveRequestForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Hide previous messages
        leaveResponseMessage.classList.add('d-none');
        
        // Get form values
        const leaveType = document.getElementById('leaveType')?.value;
        const startDate = document.getElementById('startDate')?.value;
        const endDate = document.getElementById('endDate')?.value;
        const reentryVisa = document.getElementById('reentryVisa')?.value;
        const visaDays = document.getElementById('visaDays')?.value;
        const reason = document.getElementById('leaveReason')?.value;
        
        // Validation
        if (!leaveType || !startDate || !endDate || !reentryVisa || !reason) {
            showLeaveMessage('يرجى ملء جميع الحقول المطلوبة', 'error');
            return;
        }
        
        // Validate dates
        const startDateObj = new Date(startDate);
        const endDateObj = new Date(endDate);
        const todayObj = new Date();
        todayObj.setHours(0, 0, 0, 0);
        
        if (startDateObj < todayObj) {
            showLeaveMessage('تاريخ البدء لا يمكن أن يكون في الماضي', 'error');
            return;
        }
        
        if (endDateObj < startDateObj) {
            showLeaveMessage('تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء', 'error');
            return;
        }
        
        // Calculate leave duration
        const diffTime = Math.abs(endDateObj - startDateObj);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        
        if (diffDays > 365) {
            showLeaveMessage('لا يمكن طلب إجازة لأكثر من 365 يوم', 'error');
            return;
        }
        
        // Validate visa days if reentry visa is required
        if (reentryVisa === 'نعم' && !visaDays) {
            showLeaveMessage('يرجى تحديد فترة التأشيرة', 'error');
            return;
        }
        
        // Prepare form data
        const formData = {
            leaveType: leaveType,
            startDate: startDate,
            endDate: endDate,
            reentryVisa: reentryVisa,
            visaDays: reentryVisa === 'نعم' ? visaDays : '',
            reason: reason.trim()
        };
        
        // Submit to backend
        submitLeaveRequest(formData);
    });
    
    // Function to show message
    function showLeaveMessage(message, type) {
        if (!leaveResponseMessage) return;
        
        leaveResponseMessage.textContent = message;
        leaveResponseMessage.classList.remove('d-none', 'alert-success', 'alert-danger');
        
        if (type === 'success') {
            leaveResponseMessage.classList.add('alert-success');
        } else {
            leaveResponseMessage.classList.add('alert-danger');
        }
        
        // Scroll to message
        leaveResponseMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Auto-hide success message after 5 seconds
        if (type === 'success') {
            setTimeout(() => {
                leaveResponseMessage.classList.add('d-none');
            }, 5000);
        }
    }
    
    // Function to submit leave request
    function submitLeaveRequest(formData) {
    const submitBtn = leaveRequestForm.querySelector('button[type="submit"]');
    if (!submitBtn) return;
    
    const originalText = submitBtn.textContent;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>جاري الإرسال...';
    submitBtn.disabled = true;
    
    // Use the same URL that worked in your direct test
    const baseUrl = window.location.origin;
    const currentPath = window.location.pathname;
    
    let apiUrl;
    
    // Check if your site uses index.php in URLs
    if (currentPath.includes('index.php')) {
        // For sites with index.php in URL
        const basePath = currentPath.substring(0, currentPath.indexOf('index.php'));
        apiUrl = baseUrl + basePath + 'index.php/users2/submit_vacation_request';
    } else {
        // For sites with URL rewriting (no index.php)
        apiUrl = baseUrl + '/hr/users2/submit_vacation_request';
    }
    
    console.log('Submitting leave request to:', apiUrl);
    console.log('Data:', formData);
    
    fetch(apiUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => {
        console.log('Response status:', response.status);
        
        if (!response.ok) {
            return response.text().then(text => {
                console.error('Error response:', text);
                throw new Error(`HTTP error! status: ${response.status}`);
            });
        }
        
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            return response.text().then(text => {
                console.error('Non-JSON response:', text);
                throw new Error('Response is not JSON');
            });
        }
        
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        
        if (data.status === 'success') {
            showLeaveMessage(data.message, 'success');
            
            // Reset form after success
            setTimeout(function() {
                resetLeaveForm();
                closeLeaveModal();
                
                // Refresh requests list if the function exists
                if (typeof refreshRequestsAfterSubmit === 'function') {
                    refreshRequestsAfterSubmit();
                }
            }, 2500);
        } else {
            showLeaveMessage(data.message || 'حدث خطأ غير معروف', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        let errorMessage = 'حدث خطأ في الإتصال. يرجى المحاولة مرة أخرى.';
        
        if (error.message.includes('HTTP error! status: 404')) {
            errorMessage = 'الرابط غير موجود. تأكد من إعدادات النظام.';
        } else if (error.message.includes('HTTP error! status: 500')) {
            errorMessage = 'خطأ في الخادم. يرجى التحقق من سجلات النظام.';
        } else if (error.message.includes('Response is not JSON')) {
            errorMessage = 'استجابة غير صحيحة من الخادم.';
        }
        
        showLeaveMessage(errorMessage, 'error');
    })
    .finally(() => {
        // Restore button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
  }

    
    // Function to reset form
    function resetLeaveForm() {
        leaveRequestForm.reset();
        const today = new Date().toISOString().split('T')[0];
        
        if (startDateInput) startDateInput.min = today;
        if (endDateInput) endDateInput.min = today;
        
        // Reset visa days visibility
        const visaDaysDiv = document.getElementById('visaDays').closest('.mb-3');
        if (visaDaysDiv) {
            visaDaysDiv.style.display = 'block';
            document.getElementById('visaDays').setAttribute('required', 'required');
        }
        
        leaveResponseMessage.classList.add('d-none');
    }
    
    // Function to close modal
    function closeLeaveModal() {
        try {
            const modal = bootstrap.Modal.getInstance(leaveRequestModal);
            if (modal) {
                modal.hide();
            } else {
                const newModal = new bootstrap.Modal(leaveRequestModal);
                newModal.hide();
            }
        } catch (e) {
            console.log('Could not close modal via Bootstrap JS:', e);
            const closeBtn = leaveRequestModal.querySelector('[data-bs-dismiss="modal"]');
            if (closeBtn) {
                closeBtn.click();
            }
        }
    }
    
    // Reset form when modal is opened/closed
    leaveRequestModal.addEventListener('shown.bs.modal', function() {
        resetLeaveForm();
    });
    
    leaveRequestModal.addEventListener('hidden.bs.modal', function() {
        resetLeaveForm();
    });
    
    // Character count for reason textarea
    const reasonTextarea = document.getElementById('leaveReason');
    if (reasonTextarea) {
        reasonTextarea.addEventListener('input', function() {
            if (this.value.length > 500) {
                this.value = this.value.substring(0, 500);
                showLeaveMessage('السبب لا يجب أن يتجاوز 500 حرف', 'error');
            }
        });
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const submitBtn = document.getElementById('submitOvertimeRequest');
    const form = document.getElementById('overtimeRequestForm');
    const modal = document.getElementById('overtimeRequestModal');
    const bootstrapModal = new bootstrap.Modal(modal);
    
    // Set default date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('overtimeDate').value = today;
    
    submitBtn.addEventListener('click', function() {
        submitOvertimeRequest();
    });
    
    // Reset form when modal is closed
    modal.addEventListener('hidden.bs.modal', function() {
        resetOvertimeForm();
    });
});

function submitOvertimeRequest() {
    const form = document.getElementById('overtimeRequestForm');
    const submitBtn = document.getElementById('submitOvertimeRequest');
    const loadingElement = document.getElementById('overtimeRequestLoading');
    const errorElement = document.getElementById('overtimeRequestError');
    const successElement = document.getElementById('overtimeRequestSuccess');
    
    // Validate form
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }
    
    // Get form data
    const date = document.getElementById('overtimeDate').value;
    const hours = document.getElementById('overtimeHours').value;
    const reason = document.getElementById('overtimeReason').value.trim();
    
    // Additional validation
    if (!date || !hours || !reason) {
        showOvertimeError('جميع الحقول مطلوبة');
        return;
    }
    
    if (parseFloat(hours) <= 0 || parseFloat(hours) > 12) {
        showOvertimeError('عدد الساعات يجب أن يكون بين 0.5 و 12 ساعة');
        return;
    }
    
    // Check if date is not in the future (more than today)
    const selectedDate = new Date(date);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    if (selectedDate > today) {
        showOvertimeError('لا يمكن طلب عمل إضافي لتاريخ في المستقبل');
        return;
    }
    
    // Check if date is too old (more than 30 days ago)
    const thirtyDaysAgo = new Date();
    thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
    
    if (selectedDate < thirtyDaysAgo) {
        showOvertimeError('لا يمكن طلب عمل إضافي لتاريخ أقدم من 30 يوم');
        return;
    }
    
    // Prepare data
    const requestData = {
        date: date,
        hours: hours,
        reason: reason
    };
    
    console.log('Submitting overtime request:', requestData);
    
    // Show loading
    loadingElement.classList.remove('d-none');
    errorElement.classList.add('d-none');
    successElement.classList.add('d-none');
    submitBtn.disabled = true;
    form.style.display = 'none';
    
    // Submit request
    fetch('https://services.marsoom.net/hr/users2/submit_overtime_request', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(requestData)
    })
    .then(response => {
        console.log('Overtime request response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Overtime request response:', data);
        
        loadingElement.classList.add('d-none');
        submitBtn.disabled = false;
        form.style.display = 'block';
        
        if (data.status === 'success') {
            showOvertimeSuccess(data.message || 'تم إرسال طلب العمل الإضافي بنجاح');
            
            // Reset form after success
            setTimeout(() => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('overtimeRequestModal'));
                modal.hide();
                
                // Refresh requests list if the function exists
                if (typeof loadAllUserRequests === 'function') {
                    loadAllUserRequests();
                }
            }, 2000);
            
        } else {
            showOvertimeError(data.message || 'فشل في إرسال طلب العمل الإضافي');
        }
    })
    .catch(error => {
        console.error('Error submitting overtime request:', error);
        
        loadingElement.classList.add('d-none');
        submitBtn.disabled = false;
        form.style.display = 'block';
        
        showOvertimeError('حدث خطأ في الاتصال بالخادم');
    });
}

function showOvertimeError(message) {
    const errorElement = document.getElementById('overtimeRequestError');
    const errorMessageElement = document.getElementById('overtimeRequestErrorMessage');
    const successElement = document.getElementById('overtimeRequestSuccess');
    
    errorMessageElement.textContent = message;
    errorElement.classList.remove('d-none');
    successElement.classList.add('d-none');
}

function showOvertimeSuccess(message) {
    const successElement = document.getElementById('overtimeRequestSuccess');
    const successMessageElement = document.getElementById('overtimeRequestSuccessMessage');
    const errorElement = document.getElementById('overtimeRequestError');
    
    successMessageElement.textContent = message;
    successElement.classList.remove('d-none');
    errorElement.classList.add('d-none');
}
// Add this new function to your main script tag
// --- UPDATED JAVASCRIPT TO BUILD THE NEW DESIGN ---
async function loadPersonalDetails() {
    const detailsScreen = document.getElementById('profileDetailsScreen');
    const detailsTitle = document.getElementById('profileDetailsTitle');
    const detailsContent = document.getElementById('profileDetailsContent');

    // Set title and show a loading spinner
    detailsTitle.textContent = 'المعلومات الشخصية';
    detailsContent.innerHTML = `
        <div class="text-center p-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">جاري التحميل...</span>
            </div>
        </div>
    `;
    detailsScreen.classList.add('show');

    try {
        // Fetch data from the controller endpoint
        const response = await fetch('https://services.marsoom.net/hr/users2/get_personal_details'); // Using the reliable BASE_URL
        const result = await response.json();

        if (result.status === 'success') {
            const data = result.data;
            
            // --- NEW: Data is organized into groups ---
            const groups = {
                "المعلومات الأساسية": [
                    { label: 'رقم الموظف', value: data.employee_id, icon: 'fa-id-card' },
                    { label: 'اسم الموظف', value: data.subscriber_name, icon: 'fa-user' },
                    { label: 'الجنس', value: data.gender, icon: 'fa-venus-mars' },
                    { label: 'تاريخ الميلاد', value: data.birth_date, icon: 'fa-calendar-day' },
                    { label: 'الحالة الاجتماعية', value: data.marital_status, icon: 'fa-heart' }
                ],
                "معلومات الهوية": [
                    { label: 'الجنسية', value: data.nationality, icon: 'fa-flag' },
                    { label: 'الديانة', value: data.religion, icon: 'fa-mosque' },
                    { label: 'رقم الهوية/الإقامة', value: data.id_number, icon: 'fa-id-badge' },
                    { label: 'تاريخ انتهاء الهوية', value: data.id_expiry, icon: 'fa-calendar-times' }
                ],
                "معلومات الاتصال": [
                    { label: 'البريد الإلكتروني', value: data.email, icon: 'fa-envelope' },
                    { label: 'رقم الهاتف', value: data.telephone, icon: 'fa-phone' },
                    { label: 'العنوان', value: data.address, icon: 'fa-map-marker-alt' }
                ],
                "المعلومات المالية": [
                    { label: 'اسم البنك', value: data.n3, icon: 'fa-university' },
                    { label: 'رقم الآيبان', value: data.n2, icon: 'fa-credit-card' }
                ]
            };

            // Build the HTML with cards and groups
            let contentHtml = '';
            for (const title in groups) {
                contentHtml += `
                    <div class="info-card-group">
                        <h6 class="info-group-title">${title}</h6>
                        <ul class="details-info-list">
                `;
                groups[title].forEach(item => {
                    contentHtml += `
                        <li class="details-info-item">
                            <span class="info-label">
                                <i class="fas ${item.icon}"></i>
                                <span>${item.label}</span>
                            </span>
                            <span class="info-value">${item.value || 'غير محدد'}</span>
                        </li>
                    `;
                });
                contentHtml += '</ul></div>';
            }

            detailsContent.innerHTML = contentHtml;

        } else {
            detailsContent.innerHTML = `<div class="alert alert-danger m-3">${result.message}</div>`;
        }
    } catch (error) {
        console.error('Error fetching personal details:', error);
        detailsContent.innerHTML = `<div class="alert alert-danger m-3">حدث خطأ في الاتصال بالخادم.</div>`;
    }
}
// Add this new function to your main script tag
async function loadJobDetails() {
    const detailsScreen = document.getElementById('profileDetailsScreen');
    const detailsTitle = document.getElementById('profileDetailsTitle');
    const detailsContent = document.getElementById('profileDetailsContent');

    // Set title and show a loading spinner
    detailsTitle.textContent = 'البيانات الوظيفية';
    detailsContent.innerHTML = `
        <div class="text-center p-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">جاري التحميل...</span>
            </div>
        </div>
    `;
    detailsScreen.classList.add('show');

    try {
        // Fetch data from the new controller endpoint
        const response = await fetch('https://services.marsoom.net/hr/users2/get_job_details');
        const result = await response.json();

        if (result.status === 'success') {
            const data = result.data;
            
            // Map the data to Arabic labels and icons
            const detailsMap = [
                { label: 'تاريخ التعيين', value: data.joining_date, icon: 'fa-calendar-check' },
                { label: 'المهنة (المسمى الوظيفي)', value: data.profession, icon: 'fa-user-tie' },
                { label: 'النوع', value: data.type, icon: 'fa-file-contract' },
                { label: 'القسم', value: data.n1, icon: 'fa-sitemap' },
                { label: 'الشركة', value: data.company_name, icon: 'fa-building' },
                { label: 'الموقع', value: data.location, icon: 'fa-map-marked-alt' },
                { label: 'المدير المباشر', value: data.manager, icon: 'fa-user-shield' }
            ];

            // Build the HTML using the same card design
            let contentHtml = `
                <div class="info-card-group">
                    <h6 class="info-group-title">تفاصيل الوظيفة</h6>
                    <ul class="details-info-list">
            `;
            detailsMap.forEach(item => {
                contentHtml += `
                    <li class="details-info-item">
                        <span class="info-label">
                            <i class="fas ${item.icon}"></i>
                            <span>${item.label}</span>
                        </span>
                        <span class="info-value">${item.value || 'غير محدد'}</span>
                    </li>
                `;
            });
            contentHtml += '</ul></div>';

            detailsContent.innerHTML = contentHtml;

        } else {
            detailsContent.innerHTML = `<div class="alert alert-danger m-3">${result.message}</div>`;
        }
    } catch (error) {
        console.error('Error fetching job details:', error);
        detailsContent.innerHTML = `<div class="alert alert-danger m-3">حدث خطأ في الاتصال بالخادم.</div>`;
    }
}
// Add this new function to your main script tag
// --- UPDATED JAVASCRIPT TO FIX THE PIE CHART ---
async function loadFinancialDetails() {
    const detailsScreen = document.getElementById('profileDetailsScreen');
    const detailsTitle = document.getElementById('profileDetailsTitle');
    const detailsContent = document.getElementById('profileDetailsContent');

    detailsTitle.textContent = 'الراتب والتفاصيل المالية';
    detailsContent.innerHTML = `<div class="text-center p-5"><div class="spinner-border text-primary" role="status"></div></div>`;
    detailsScreen.classList.add('show');

    try {
        const response = await fetch('https://services.marsoom.net/hr/users2/get_financial_details');
        const result = await response.json();

        if (result.status === 'success') {
            const data = result.data;
            
            const detailsMap = [
                { label: 'الراتب الأساسي', value: data.base_salary, icon: 'fa-hand-holding-usd' },
                { label: 'بدل السكن', value: data.housing_allowance, icon: 'fa-home' },
                { label: 'بدل النقل', value: data.n4, icon: 'fa-car' },
                { label: 'بدلات أخرى', value: data.n5, icon: 'fa-plus-circle' },
                { label: 'إجمالي الراتب', value: data.total_salary, icon: 'fa-wallet' }
            ];

            let contentHtml = `
                <div class="info-card-group">
                    <h6 class="info-group-title">توزيع الراتب</h6>
                    <div class="pie-chart-container">
                        <canvas id="salaryPieChart"></canvas>
                    </div>
                </div>
                <div class="info-card-group">
                    <h6 class="info-group-title">التفاصيل المالية</h6>
                    <ul class="details-info-list">
            `;
            detailsMap.forEach(item => {
                // The layout is now controlled by CSS Grid, no HTML change needed here.
                contentHtml += `
                    <li class="details-info-item">
                        <span class="info-label"><i class="fas ${item.icon}"></i><span>${item.label}</span></span>
                        <span class="info-value">${parseFloat(item.value || 0).toLocaleString()}</span>
                    </li>
                `;
            });
            contentHtml += '</ul></div>';
            detailsContent.innerHTML = contentHtml;

            // --- Initialize the Pie Chart ---
            const ctx = document.getElementById('salaryPieChart').getContext('2d');
            
            if (window.mySalaryChart instanceof Chart) {
                window.mySalaryChart.destroy();
            }

            window.mySalaryChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['الراتب الأساسي', 'بدل السكن', 'بدل النقل', 'بدلات أخرى'],
                    datasets: [{
                        data: [
                            parseFloat(data.base_salary || 0), 
                            parseFloat(data.housing_allowance || 0), 
                            parseFloat(data.n4 || 0), 
                            parseFloat(data.n5 || 0)
                        ],
                        backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#8B5CF6'],
                        borderColor: '#ffffff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false, // <-- THIS IS THE CRITICAL FIX FOR THE CHART
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    family: "'Inter', sans-serif"
                                }
                            }
                        }
                    }
                }
            });

        } else {
            detailsContent.innerHTML = `<div class="alert alert-danger m-3">${result.message}</div>`;
        }
    } catch (error) {
        console.error('Error fetching financial details:', error);
        detailsContent.innerHTML = `<div class="alert alert-danger m-3">حدث خطأ في الاتصال بالخادم.</div>`;
    }
}
// Add this new function to your main script tag
async function loadContractDetails() {
    const detailsScreen = document.getElementById('profileDetailsScreen');
    const detailsTitle = document.getElementById('profileDetailsTitle');
    const detailsContent = document.getElementById('profileDetailsContent');

    detailsTitle.textContent = 'تفاصيل العقد';
    detailsContent.innerHTML = `<div class="text-center p-5"><div class="spinner-border text-primary" role="status"></div></div>`;
    detailsScreen.classList.add('show');

    try {
        const response = await fetch('https://services.marsoom.net/hr/users2/get_contract_details');
        const result = await response.json();

        if (result.status === 'success') {
            const data = result.data;
            
            // --- Logic for status color and text ---
            let statusClass = '';
            let statusText = '';
            if (data.contract_status && data.contract_status.toLowerCase() === 'active') {
                statusClass = 'status-active';
                statusText = 'ساري';
            } else {
                statusClass = 'status-inactive';
                statusText = 'غير ساري';
            }

            const detailsMap = [
                { label: 'حالة العقد', value: `<span class="status-badge ${statusClass}">${statusText}</span>`, icon: 'fa-check-circle' },
                { label: 'مدة العقد', value: data.contract_period, icon: 'fa-hourglass-half' },
                { label: 'تاريخ بداية العقد', value: data.contract_start, icon: 'fa-calendar-plus' },
                { label: 'تاريخ نهاية العقد', value: data.contract_end, icon: 'fa-calendar-times' },
                { label: 'المدة المتبقية للتجديد', value: data.remaining_renewal_period, icon: 'fa-history' }
            ];

            // Build the HTML using the card design
            let contentHtml = `
                <div class="info-card-group">
                    <h6 class="info-group-title">بيانات العقد الحالية</h6>
                    <ul class="details-info-list">
            `;
            detailsMap.forEach(item => {
                // Use a different structure for the status badge to allow HTML
                 if (item.label === 'حالة العقد') {
                    contentHtml += `
                    <li class="details-info-item">
                        <span class="info-label"><i class="fas ${item.icon}"></i><span>${item.label}</span></span>
                        ${item.value} 
                    </li>`;
                } else {
                    contentHtml += `
                    <li class="details-info-item">
                        <span class="info-label"><i class="fas ${item.icon}"></i><span>${item.label}</span></span>
                        <span class="info-value">${item.value || 'غير محدد'}</span>
                    </li>`;
                }
            });
            contentHtml += '</ul></div>';
            detailsContent.innerHTML = contentHtml;

        } else {
            detailsContent.innerHTML = `<div class="alert alert-danger m-3">${result.message}</div>`;
        }
    } catch (error) {
        console.error('Error fetching contract details:', error);
        detailsContent.innerHTML = `<div class="alert alert-danger m-3">حدث خطأ في الاتصال بالخادم.</div>`;
    }
}
function resetOvertimeForm() {
    const form = document.getElementById('overtimeRequestForm');
    const errorElement = document.getElementById('overtimeRequestError');
    const successElement = document.getElementById('overtimeRequestSuccess');
    const loadingElement = document.getElementById('overtimeRequestLoading');
    const submitBtn = document.getElementById('submitOvertimeRequest');
    
    // Reset form
    form.reset();
    form.classList.remove('was-validated');
    form.style.display = 'block';
    
    // Set default date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('overtimeDate').value = today;
    
    // Hide messages
    errorElement.classList.add('d-none');
    successElement.classList.add('d-none');
    loadingElement.classList.add('d-none');
    
    // Enable button
    submitBtn.disabled = false;
}
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadAllUserRequests();
});

function loadAllUserRequests() {
    const loadingElement = document.getElementById('requestsLoading');
    const errorElement = document.getElementById('requestsError');
    const noRequestsElement = document.getElementById('noRequests');
    const containerElement = document.getElementById('requestsContainer');
    
    console.log('Loading all user requests...');
    
    // Show loading
    loadingElement.classList.remove('d-none');
    errorElement.classList.add('d-none');
    noRequestsElement.classList.add('d-none');
    containerElement.innerHTML = '';
    
    // Fetch all three types of requests
    Promise.all([
        fetchCorrectionRequests(),
        fetchVacationRequests(),
        fetchOvertimeRequests()
    ])
    .then(([correctionData, vacationData, overtimeData]) => {
        console.log('All requests fetched:');
        console.log('Correction data:', correctionData);
        console.log('Vacation data:', vacationData);
        console.log('Overtime data:', overtimeData);
        
        loadingElement.classList.add('d-none');
        
        let allRequests = [];
        
        // Process correction requests
        if (correctionData && correctionData.status === 'success' && correctionData.data && Array.isArray(correctionData.data)) {
            console.log('Processing', correctionData.data.length, 'correction requests');
            correctionData.data.forEach(request => {
                allRequests.push({
                    ...request,
                    request_type: 'correction',
                    sort_date: new Date(request.created_at || request.date || new Date())
                });
            });
        } else {
            console.log('No correction requests or error:', correctionData ? correctionData.message : 'No data');
        }
        
        // Process vacation requests
        if (vacationData && vacationData.status === 'success' && vacationData.data && Array.isArray(vacationData.data)) {
            console.log('Processing', vacationData.data.length, 'vacation requests');
            vacationData.data.forEach(request => {
                // Handle different date field formats for sorting
                let sortDate;
                if (request.created_at) {
                    sortDate = new Date(request.created_at);
                } else if (request.date && request.time) {
                    sortDate = new Date(request.date + ' ' + request.time);
                } else if (request.date) {
                    sortDate = new Date(request.date);
                } else {
                    sortDate = new Date();
                }
                
                allRequests.push({
                    ...request,
                    request_type: 'vacation',
                    sort_date: sortDate
                });
            });
        } else {
            console.log('No vacation requests or error:', vacationData ? vacationData.message : 'No data');
        }
        
        // Process overtime requests
        if (overtimeData && overtimeData.status === 'success' && overtimeData.data && Array.isArray(overtimeData.data)) {
            console.log('Processing', overtimeData.data.length, 'overtime requests');
            overtimeData.data.forEach(request => {
                // Use create_date for sorting, fallback to date
                let sortDate;
                if (request.create_date) {
                    sortDate = new Date(request.create_date);
                } else if (request.date) {
                    sortDate = new Date(request.date);
                } else {
                    sortDate = new Date();
                }
                
                allRequests.push({
                    ...request,
                    request_type: 'overtime',
                    sort_date: sortDate
                });
            });
        } else {
            console.log('No overtime requests or error:', overtimeData ? overtimeData.message : 'No data');
        }
        
        console.log('Total requests to display:', allRequests.length);
        
        if (allRequests.length > 0) {
            // Sort all requests by date (newest first)
            allRequests.sort((a, b) => b.sort_date - a.sort_date);
            console.log('Displaying requests:', allRequests);
            displayAllRequests(allRequests);
        } else {
            console.log('No requests found, showing no requests message');
            noRequestsElement.classList.remove('d-none');
        }
    })
    .catch(error => {
        console.error('Error loading requests:', error);
        loadingElement.classList.add('d-none');
        showError('حدث خطأ في تحميل الطلبات: ' + error.message);
    });
}

function fetchCorrectionRequests() {
    const apiUrl = 'https://services.marsoom.net/hr/users2/get_user_correction_requests';
    
    console.log('Fetching correction requests from:', apiUrl);
    
    return fetch(apiUrl, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Correction requests response status:', response.status);
        if (!response.ok) {
            return response.text().then(text => {
                console.error('Correction requests error response:', text);
                throw new Error(`HTTP error! status: ${response.status}`);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Correction requests data:', data);
        return data;
    })
    .catch(error => {
        console.error('Error fetching correction requests:', error);
        return { status: 'error', data: [], message: error.message };
    });
}

function fetchVacationRequests() {
    const apiUrl = 'https://services.marsoom.net/hr/users2/get_user_vacation_requests';
    
    console.log('Fetching vacation requests from:', apiUrl);
    
    return fetch(apiUrl, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Vacation requests response status:', response.status);
        if (!response.ok) {
            return response.text().then(text => {
                console.error('Vacation requests error response:', text);
                throw new Error(`HTTP error! status: ${response.status}`);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Vacation requests data:', data);
        return data;
    })
    .catch(error => {
        console.error('Error fetching vacation requests:', error);
        return { status: 'error', data: [], message: error.message };
    });
}
function fetchOvertimeRequests() {
    const apiUrl = 'https://services.marsoom.net/hr/users2/get_user_overtime_requests';
    
    console.log('Fetching overtime requests from:', apiUrl);
    
    return fetch(apiUrl, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Overtime requests response status:', response.status);
        if (!response.ok) {
            return response.text().then(text => {
                console.error('Overtime requests error response:', text);
                throw new Error(`HTTP error! status: ${response.status}`);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Overtime requests data:', data);
        return data;
    })
    .catch(error => {
        console.error('Error fetching overtime requests:', error);
        return { status: 'error', data: [], message: error.message };
    });
}
function showError(message) {
    const errorElement = document.getElementById('requestsError');
    const errorMessageElement = document.getElementById('requestsErrorMessage');
    
    if (errorMessageElement) {
        errorMessageElement.textContent = message;
    }
    if (errorElement) {
        errorElement.classList.remove('d-none');
    }
}

function displayAllRequests(requests) {
    const container = document.getElementById('requestsContainer');
    if (!container) return;
    
    container.innerHTML = '';
    
    requests.forEach(request => {
        let requestHtml = '';
        if (request.request_type === 'correction') {
            requestHtml = createCorrectionRequestHtml(request);
        } else if (request.request_type === 'vacation') {
            requestHtml = createVacationRequestHtml(request);
        } else if (request.request_type === 'overtime') {
            requestHtml = createOvertimeRequestHtml(request);
        }
        if (requestHtml) {
            container.innerHTML += requestHtml;
        }
    });
}
function createOvertimeRequestHtml(request) {
    try {
        // Format dates safely
        let requestDateTime;
        if (request.create_date) {
            requestDateTime = new Date(request.create_date);
        } else if (request.date) {
            requestDateTime = new Date(request.date);
        } else {
            requestDateTime = new Date();
        }
        
        const workDate = new Date(request.date);
        
        const requestDateFormatted = requestDateTime.toLocaleDateString('en-SA', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        const workDateFormatted = workDate.toLocaleDateString('en-SA', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        // Determine status info
        const statusInfo = getStatusInfo(request.status || 'pending');
        
        // Create admin remarks section if exists
        let adminRemarksHtml = '';
        if (request.admin_remarks) {
            const remarkClass = request.status === 'rejected' ? 'text-danger' : 'text-info';
            adminRemarksHtml = `<p class="${remarkClass}"><i class="fas fa-comment"></i> ملاحظات الإدارة: ${request.admin_remarks}</p>`;
        }
        
        return `
            <div class="card mb-3 request-box">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="request-title fw-bold">طلب عمل إضافي</span>
                    <span class="badge ${statusInfo.class} request-status">${statusInfo.text}</span>
                </div>
                <div class="card-body request-details">
                    <p><i class="fas fa-calendar-alt text-primary"></i> تاريخ الطلب: ${requestDateFormatted}</p>
                    <p><i class="fas fa-file-alt text-info"></i> نوع الطلب: عمل إضافي</p>
                    <p><i class="fas fa-calendar-day text-success"></i> تاريخ العمل الإضافي: ${workDateFormatted}</p>
                    <p><i class="fas fa-clock text-warning"></i> عدد الساعات: ${request.hours || 'غير محدد'} ساعة</p>
                    <p><i class="fas fa-user text-secondary"></i> الموظف: ${request.name || request.username}</p>
                    <p><i class="fas fa-hashtag text-secondary"></i> رقم الطلب: ${request.id}</p>
                    <p><i class="${statusInfo.icon} text-muted"></i> حالة الطلب: ${statusInfo.description}</p>
                    <p><i class="fas fa-comment-dots text-dark"></i> السبب: ${request.reason || 'غير محدد'}</p>
                    ${adminRemarksHtml}
                    ${createActionButtons(request, 'overtime')}
                </div>
            </div>
        `;
    } catch (error) {
        console.error('Error creating overtime request HTML:', error, request);
        return `
            <div class="card mb-3 request-box">
                <div class="card-body">
                    <p class="text-danger">خطأ في عرض طلب العمل الإضافي رقم ${request.id}</p>
                    <p class="text-muted">البيانات: ${JSON.stringify(request)}</p>
                </div>
            </div>
        `;
    }
}
function createCorrectionRequestHtml(request) {
    // Format dates
    const createdAt = new Date(request.created_at || request.date);
    const requestDate = new Date(request.date);
    
    const createdAtFormatted = createdAt.toLocaleDateString('en-SA', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    const requestDateFormatted = requestDate.toLocaleDateString('en-SA', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    // Determine status info
    const statusInfo = getStatusInfo(request.status || 'pending');
    
    // Create admin remarks section if exists
    let adminRemarksHtml = '';
    if (request.admin_remarks) {
        const remarkClass = request.status === 'rejected' ? 'text-danger' : 'text-info';
        adminRemarksHtml = `<p class="${remarkClass}"><i class="fas fa-comment"></i> ملاحظات الإدارة: ${request.admin_remarks}</p>`;
    }
    
    return `
        <div class="card mb-3 request-box">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="request-title fw-bold">طلب تصحيح بصمة</span>
                <span class="badge ${statusInfo.class} request-status">${statusInfo.text}</span>
            </div>
            <div class="card-body request-details">
                <p><i class="fas fa-calendar-alt text-primary"></i> تاريخ الطلب: ${createdAtFormatted}</p>
                <p><i class="fas fa-file-alt text-info"></i> نوع الطلب: تصحيح حضور</p>
                <p><i class="fas fa-calendar-day text-success"></i> يوم التصحيح: ${requestDateFormatted}</p>
                <p><i class="fas fa-clock text-warning"></i> أوقات العمل: ${request.in_time || 'غير محدد'} - ${request.out_time || 'غير محدد'}</p>
                <p><i class="fas fa-hashtag text-secondary"></i> رقم الطلب: ${request.id}</p>
                <p><i class="${statusInfo.icon} text-muted"></i> حالة الطلب: ${statusInfo.description}</p>
                <p><i class="fas fa-comment-dots text-dark"></i> السبب: ${request.reason || 'غير محدد'}</p>
                ${adminRemarksHtml}
                ${createActionButtons(request, 'correction')}
            </div>
        </div>
    `;
}

function createVacationRequestHtml(request) {
    try {
        // Handle different date field formats
        let requestDateTime;
        if (request.created_at) {
            requestDateTime = new Date(request.created_at);
        } else if (request.date && request.time) {
            requestDateTime = new Date(request.date + ' ' + request.time);
        } else if (request.date) {
            requestDateTime = new Date(request.date);
        } else {
            requestDateTime = new Date();
        }
        
        const startDate = new Date(request.start_date);
        const endDate = new Date(request.end_date);
        
        const requestDateFormatted = requestDateTime.toLocaleDateString('en-SA', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        const startDateFormatted = startDate.toLocaleDateString('en-SA', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        const endDateFormatted = endDate.toLocaleDateString('en-SA', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        // Calculate vacation days
        const diffTime = Math.abs(endDate - startDate);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        
        // Determine status info
        const statusInfo = getStatusInfo(request.status || 'pending');
        
        // Create visa information
        let visaInfo = '';
        if (request.reentryvisa === 'نعم') {
            visaInfo = `<p><i class="fas fa-passport text-primary"></i> تأشيرة خروج وعودة: نعم${request.visadays ? ' (' + request.visadays + ')' : ''}</p>`;
        } else {
            visaInfo = `<p><i class="fas fa-passport text-secondary"></i> تأشيرة خروج وعودة: لا</p>`;
        }
        
        // Create admin remarks section if exists
        let adminRemarksHtml = '';
        if (request.admin_remarks) {
            const remarkClass = request.status === 'rejected' ? 'text-danger' : 'text-info';
            adminRemarksHtml = `<p class="${remarkClass}"><i class="fas fa-comment"></i> ملاحظات الإدارة: ${request.admin_remarks}</p>`;
        }
        
        return `
            <div class="card mb-3 request-box">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="request-title fw-bold">طلب إجازة</span>
                    <span class="badge ${statusInfo.class} request-status">${statusInfo.text}</span>
                </div>
                <div class="card-body request-details">
                    <p><i class="fas fa-calendar-alt text-primary"></i> تاريخ الطلب: ${requestDateFormatted}</p>
                    <p><i class="fas fa-file-alt text-info"></i> نوع الإجازة: ${request.type || 'غير محدد'}</p>
                    <p><i class="fas fa-calendar-day text-success"></i> من: ${startDateFormatted}</p>
                    <p><i class="fas fa-calendar-day text-success"></i> إلى: ${endDateFormatted}</p>
                    <p><i class="fas fa-clock text-warning"></i> عدد الأيام: ${diffDays} يوم</p>
                    ${visaInfo}
                    <p><i class="fas fa-hashtag text-secondary"></i> رقم الطلب: ${request.id}</p>
                    <p><i class="${statusInfo.icon} text-muted"></i> حالة الطلب: ${statusInfo.description}</p>
                    <p><i class="fas fa-comment-dots text-dark"></i> السبب: ${request.reason || 'غير محدد'}</p>
                    ${adminRemarksHtml}
                    ${createActionButtons(request, 'vacation')}
                </div>
            </div>
        `;
    } catch (error) {
        console.error('Error creating vacation request HTML:', error, request);
        return `
            <div class="card mb-3 request-box">
                <div class="card-body">
                    <p class="text-danger">خطأ في عرض طلب الإجازة رقم ${request.id}</p>
                    <p class="text-muted">البيانات: ${JSON.stringify(request)}</p>
                </div>
            </div>
        `;
    }
}

function getStatusInfo(status) {
    const statusMap = {
        'pending': {
            class: 'bg-warning text-dark',
            text: 'قيد المراجعة',
            description: 'بانتظار الموافقة',
            icon: 'fas fa-hourglass-half'
        },
        'approved': {
            class: 'bg-success text-white',
            text: 'معتمد',
            description: 'تم الموافقة على الطلب',
            icon: 'fas fa-check-circle'
        },
        'rejected': {
            class: 'bg-danger text-white',
            text: 'مرفوض',
            description: 'تم رفض الطلب',
            icon: 'fas fa-times-circle'
        },
        'cancelled': {
            class: 'bg-secondary text-white',
            text: 'ملغي',
            description: 'تم إلغاء الطلب',
            icon: 'fas fa-ban'
        }
    };
    
    return statusMap[status] || statusMap['pending'];
}

function createActionButtons(request, requestType) {
    if (request.status === 'pending') {
        if (requestType === 'correction') {
            return `
                <div class="mt-3">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="cancelCorrectionRequest(${request.id})">
                        <i class="fas fa-times me-1"></i>إلغاء الطلب
                    </button>
                </div>
            `;
        } else if (requestType === 'vacation') {
            return `
                <div class="mt-3">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="cancelVacationRequest(${request.id})">
                        <i class="fas fa-times me-1"></i>إلغاء الطلب
                    </button>
                </div>
            `;
        } else if (requestType === 'overtime') {
            return `
                <div class="mt-3">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="cancelOvertimeRequest(${request.id})">
                        <i class="fas fa-times me-1"></i>إلغاء الطلب
                    </button>
                </div>
            `;
        }
    }
    return '';
}


function cancelCorrectionRequest(requestId) {
    if (!confirm('هل أنت متأكد من إلغاء هذا الطلب؟')) {
        return;
    }
    
    const data = { request_id: requestId };
    
    fetch('https://services.marsoom.net/hr/users2/cancel_correction_request', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('تم إلغاء الطلب بنجاح');
            loadAllUserRequests();
        } else {
            alert(data.message || 'فشل في إلغاء الطلب');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ في الاتصال');
    });
}

function cancelVacationRequest(requestId) {
    if (!confirm('هل أنت متأكد من إلغاء طلب الإجازة؟')) {
        return;
    }
    
    const data = { request_id: requestId };
    
    fetch('https://services.marsoom.net/hr/users2/cancel_vacation_request', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('تم إلغاء طلب الإجازة بنجاح');
            loadAllUserRequests();
        } else {
            alert(data.message || 'فشل في إلغاء الطلب');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ في الاتصال');
    });
}
function cancelOvertimeRequest(requestId) {
    if (!confirm('هل أنت متأكد من إلغاء طلب العمل الإضافي؟')) {
        return;
    }
    
    const data = { request_id: requestId };
    
    fetch('https://services.marsoom.net/hr/users2/cancel_overtime_request', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('تم إلغاء طلب العمل الإضافي بنجاح');
            loadAllUserRequests();
        } else {
            alert(data.message || 'فشل في إلغاء الطلب');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ في الاتصال');
    });
}

function refreshRequestsAfterSubmit() {
    loadAllUserRequests();
}

// Test function - you can call this in console for debugging
function testAllRequests() {
    console.log('=== Testing All Request URLs ===');
    
    // Test correction requests
    fetch('https://services.marsoom.net/hr/users2/get_user_correction_requests')
        .then(response => response.json())
        .then(data => console.log('Correction test:', data))
        .catch(error => console.error('Correction error:', error));
    
    // Test vacation requests
    fetch('https://services.marsoom.net/hr/users2/get_user_vacation_requests')
        .then(response => response.json())
        .then(data => console.log('Vacation test:', data))
        .catch(error => console.error('Vacation error:', error));
}
window.toggleSubMenu = function(menuId, element) {
                const menu = document.getElementById(menuId);
                
                // Close any other open submenus to act like a proper accordion
                document.querySelectorAll('.profile-submenu-new.show').forEach(openMenu => {
                    if (openMenu.id !== menuId) {
                        openMenu.classList.remove('show');
                        openMenu.previousElementSibling.classList.remove('open');
                    }
                });

                // Toggle the clicked menu
                menu.classList.toggle('show');
                element.classList.toggle('open');
            }
</script>
<!-- JavaScript Code for the Attendance Screen -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.7/main.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.7/locales-all.min.js"></script> <!-- For Arabic -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const totalDaysInput = document.getElementById('totalDays');

    function calculateWorkingDays() {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);

        // Ensure both dates are valid and start date is not after end date
        if (isNaN(startDate) || isNaN(endDate) || startDate > endDate) {
            totalDaysInput.value = '';
            return;
        }

        let count = 0;
        const currentDate = new Date(startDate);

        while (currentDate <= endDate) {
            // getDay() returns 0 for Sunday, 1 for Monday, ..., 5 for Friday, 6 for Saturday
            const dayOfWeek = currentDate.getDay();
            if (dayOfWeek !== 5 && dayOfWeek !== 6) { // Not Friday and not Saturday
                count++;
            }
            // Move to the next day
            currentDate.setDate(currentDate.getDate() + 1);
        }

        totalDaysInput.value = count;
    }

    // Add event listeners to both date inputs
    startDateInput.addEventListener('change', calculateWorkingDays);
    endDateInput.addEventListener('change', calculateWorkingDays);

    // Also make sure to include 'totalDays' when you submit the form via AJAX
    // Example modification to your form submission logic:
    /*
    document.getElementById('leaveRequestForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            leaveType: document.getElementById('leaveType').value,
            startDate: document.getElementById('startDate').value,
            endDate: document.getElementById('endDate').value,
            totalDays: document.getElementById('totalDays').value, // <-- INCLUDE THIS
            reentryVisa: document.getElementById('reentryVisa').value,
            visaDays: document.getElementById('visaDays').value,
            reason: document.getElementById('leaveReason').value
        };
        
        // Your existing AJAX call here...
        // fetch('/users2/submit_vacation_request', {
        //     method: 'POST',
        //     headers: { 'Content-Type': 'application/json' },
        //     body: JSON.stringify(formData)
        // })...
    });
    */
});
</script>
<script>
    let calendar;
document.addEventListener('DOMContentLoaded', function() {
    const baseUrl = 'https://services.marsoom.net/hr'; // Adjust if your base URL is different

    // Get DOM elements
    var calendarEl = document.getElementById('calendar');
    var attendanceDateEl = document.getElementById('attendanceDate');
    var attendanceInEl = document.getElementById('attendanceIn');
    var attendanceOutEl = document.getElementById('attendanceOut');
    var attendanceDurationEl = document.getElementById('attendanceDuration');
    var attendanceDiffEl = document.getElementById('attendanceDiff');

    // Initialize FullCalendar
    var calendar = new FullCalendar.Calendar(calendarEl, {
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        locale: 'ar',
        direction: 'rtl',
        initialView: 'dayGridMonth',
        timeZone: 'local', // Explicitly use the browser's local timezone

        // Fetch events for the calendar view (days with attendance)
// Replace the 'events' object in your FullCalendar script

events: {
    url: `${baseUrl}/users2/getAttendanceEventsForRange`,
    method: 'GET',
    failure: function(arg) {
        console.error('Error fetching calendar events:', arg);
    },
    success: function(data) {
        if (!Array.isArray(data)) {
            console.error('Expected an array of events, but received:', data);
            return [];
        }

        const events = data.map(item => {
            let eventColor = '';
            let borderColor = '';
            let displayType = 'auto'; // Default event block

            switch (item.status) {
                case 'complete':
                    eventColor = '#28a745'; // Green
                    borderColor = '#218838';
                    break;
                case 'incomplete':
                    eventColor = '#218838'; // Light Orange
                    borderColor = '#e85d04';
                    break;
                case 'absent':
                    eventColor = '#dc3545'; // Red
                    borderColor = '#b02a37';
                    displayType = 'background'; // This colors the whole day cell
                    break;
            }

            return {
                start: item.date,
                allDay: true,
                backgroundColor: eventColor,
                borderColor: borderColor,
                display: displayType // Use 'background' for absences
            };
        });
        return events;
    }
},
        // Handler for clicking on a date
        dateClick: function(info) {
            // info.dateStr is the clean 'YYYY-MM-DD' string we need
            const dateString = info.dateStr;
            console.log('Date clicked:', dateString);
            loadAttendanceData(dateString);
        },
        
        // Handler for clicking on an existing event (a day with attendance)
        eventClick: function(info) {
            // Extract the clean date part from the event's start time
            const dateString = info.event.startStr.split('T')[0];
            console.log('Event clicked on date:', dateString);
            loadAttendanceData(dateString);
        }
    });

    // Render the calendar
    calendar.render();
    console.log('FullCalendar initialized.');

    function loadAttendanceData(dateToLoad) { // dateToLoad is 'YYYY-MM-DD'
        console.log('Attempting to load details for:', dateToLoad);
        
        // Clear previous details and show loading indicator
        attendanceDateEl.textContent = dateToLoad;
        attendanceInEl.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        attendanceOutEl.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        attendanceDurationEl.textContent = '...';
        attendanceDiffEl.textContent = '...';

        // Fetch data from the controller
        fetch(`${baseUrl}/users2/getAttendanceData`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            // Send the clean date string with the key your PHP controller expects
            body: JSON.stringify({ selectedDate: dateToLoad }) 
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Received attendance data:', data);
            
            if (data.error) {
                throw new Error(data.error);
            }

            // Update UI elements with the fetched data
            attendanceInEl.textContent = data.firstCheckIn || 'غير محدد';
            attendanceOutEl.textContent = data.lastCheckOut || 'غير محدد';
            attendanceDurationEl.textContent = data.workDuration || '00:00:00';
            attendanceDiffEl.textContent = data.timeDifference || '00:00:00';
        })
        .catch(error => {
            console.error('Error fetching attendance details:', error);
            attendanceInEl.textContent = 'فشل';
            attendanceOutEl.textContent = 'فشل';
            attendanceDurationEl.textContent = '00:00:00';
            attendanceDiffEl.textContent = '00:00:00';
            alert('حدث خطأ أثناء جلب تفاصيل الحضور: ' + error.message);
        });
    }

    // Load initial data for today
    const today = new Date();
    // Format today's date to YYYY-MM-DD correctly without timezone issues
    const todayYYYYMMDD = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');
    console.log('Loading initial attendance details for today:', todayYYYYMMDD);
    loadAttendanceData(todayYYYYMMDD); 
});
</script>
<script>
$(document).ready(function() {
    // --- START: Fixed Documents Script ---

    // Define your base URL here. This is much better than using PHP tags.
    // I am using the URL from your other fetch requests.
    const baseUrl = 'https://services.marsoom.net/hr'; 

    // 1. Handle clicking the main "Documents" section to show/hide the sub-menu
    $('#documents-section').on('click', function() {
        $('#documents-submenu').slideToggle('fast');
        $(this).find('.chevron-icon').toggleClass('active');
    });

    // 2. Handle the submission of the "Add Document" form
    $('#add-document-form').on('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission

        var formData = new FormData(this);
        var submitButton = $(this).find('button[type="submit"]');
        submitButton.prop('disabled', true).text('جاري الحفظ...');

        $.ajax({
            url: baseUrl + '/users2/add_document', // CORRECTED URL
            type: 'POST',
            data: formData,
            contentType: false, // Important for file uploads
            processData: false, // Important for file uploads
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alert(response.message);
                    // Use Bootstrap 5's instance method to hide the modal
                    var addModal = bootstrap.Modal.getInstance(document.getElementById('addFileModal'));
                    addModal.hide();
                    $('#add-document-form')[0].reset(); // Reset the form
                } else {
                    alert('خطأ: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                alert('حدث خطأ في الاتصال بالخادم. يرجى المحاولة مرة أخرى.');
            },
            complete: function() {
                 submitButton.prop('disabled', false).text('حفظ المستند');
            }
        });
    });

    // 3. Handle clicking "View Files" to fetch and display documents
    $('#view-files-btn').on('click', function() {
        var listContainer = $('#document-list-container');
        listContainer.html('<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">جاري تحميل المستندات...</p></div>');

        $.ajax({
            url: baseUrl + '/users2/get_user_documents', // CORRECTED URL
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' && Array.isArray(response.documents) && response.documents.length > 0) {
                    var table = '<div class="table-responsive"><table class="table table-striped table-bordered">';
                    table += '<thead class="table-light"><tr><th>نوع المستند</th><th>الوصف</th><th>اسم الملف</th><th>تاريخ الرفع</th><th>إجراء</th></tr></thead>';
                    table += '<tbody>';
                    
                    $.each(response.documents, function(index, doc) {
                        table += '<tr>';
                        table += '<td>' + (doc.doc_type || '-') + '</td>';
                        table += '<td>' + (doc.description || '-') + '</td>';
                        table += '<td>' + (doc.file_name || '-') + '</td>';
                        table += '<td>' + (doc.upload_date ? new Date(doc.upload_date).toLocaleDateString('ar-SA') : '-') + '</td>';
                        // Use the full base URL for the link
                        table += '<td><a href="' + baseUrl + '/users2/view_document/' + doc.id + '" target="_blank" class="btn btn-sm btn-info">عرض</a></td>';
                        table += '</tr>';
                    });

                    table += '</tbody></table></div>';
                    listContainer.html(table);
                } else if (response.status === 'success') {
                    listContainer.html('<p class="text-center text-muted mt-3">لم يتم العثور على مستندات.</p>');
                } else {
                     listContainer.html('<p class="text-center text-danger mt-3">خطأ: ' + response.message + '</p>');
                }
            },
            error: function() {
                listContainer.html('<p class="text-center text-danger mt-3">حدث خطأ في جلب البيانات.</p>');
            }
        });
    });
    // --- END: Fixed Documents Script ---
});



// --- START: CORRECTED ATTENDANCE SCRIPT WITH LOCAL TIME ---
// --- START: NEW MULTI-BRANCH ATTENDANCE SCRIPT ---

document.getElementById('attendanceToggleButton').addEventListener('click', async function() {
    const button = this;
    button.disabled = true;
    button.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> جار التحقق...`;

    try {
        // Step 1: Fetch all branch locations from the server
        const branchesResponse = await fetch('https://services.marsoom.net/hr/users2/get_branch_locations');
        if (!branchesResponse.ok) throw new Error('Could not fetch branch locations.');
        const branches = await branchesResponse.json();

        if (!branches || branches.length === 0) {
            alert('Error: No branch locations are configured in the system.');
            return;
        }

        // Step 2: Get the user's current GPS location
        const position = await new Promise((resolve, reject) => {
            if (!navigator.geolocation) {
                reject(new Error('Geolocation is not supported by this browser.'));
            }
            navigator.geolocation.getCurrentPosition(resolve, reject);
        });
        
        const userLat = position.coords.latitude;
        const userLon = position.coords.longitude;

        // Step 3: Loop through branches to find the closest one
        let closestBranch = null;
        let closestDistance = Infinity;

        for (const branch of branches) {
            const distance = getDistance(userLat, userLon, branch.latitude, branch.longitude);
            if (distance < closestDistance) {
                closestDistance = distance;
                closestBranch = branch;
            }
        }
        
        // Step 4: Check if the closest branch is within the allowed radius
        if (closestDistance <= 50) { // Allowed radius in meters
            // User is within range of a branch, proceed with attendance
            var now = new Date();
            const pad = (num) => String(num).padStart(2, '0');
            var punch_time = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())} ${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
            var punch_state = now.getHours() < 12 ? 'Check In' : 'Check Out';
            
            var data = {
                punch_time: punch_time,
                punch_state: punch_state,
                area_alias: closestBranch.branch_name // Send the name of the branch user is at
            };

            // Step 5: Send the attendance record to the server
            $.ajax({
                url: 'https://services.marsoom.net/hr/users2/attendance',
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert(`Attendance recorded successfully at: ${closestBranch.branch_name}`);
                    } else {
                        alert('Server Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error communicating with the server.');
                }
            });

        } else {
            alert(`You are outside the allowed area. Your closest branch is ${closestBranch.branch_name}, which is ${Math.round(closestDistance)} meters away.`);
        }

    } catch (error) {
        alert('Error: ' + error.message);
        console.error('Attendance Error:', error);
    } finally {
        // Restore button state
        button.disabled = false;
        button.innerHTML = `تسجيل الحضور / تسجيل الانصراف`;
    }
});

// Helper function for distance calculation (no changes needed here)
function getDistance(lat1, lon1, lat2, lon2) {
    var R = 6371; // Radius of the earth in km
    var dLat = deg2rad(lat2 - lat1);
    var dLon = deg2rad(lon2 - lon1);
    var a =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
        Math.sin(dLon / 2) * Math.sin(dLon / 2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    var d = R * c; // Distance in km
    return d * 1000; // Distance in meters
}

function deg2rad(deg) {
    return deg * (Math.PI / 180)
}
// --- END: NEW MULTI-BRANCH ATTENDANCE SCRIPT ---
// --- END: CORRECTED ATTENDANCE SCRIPT ---
</script>

</body>
</html>