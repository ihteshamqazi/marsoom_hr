<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مسير الرواتب</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/colreorder/1.7.0/css/colReorder.dataTables.min.css">


    <style>
        :root {
            --marsom-blue: #001f3f;
            --marsom-orange: #FF8C00;
            --text-light: #ffffff;
            --text-dark: #343a40;
            --glass-bg: rgba(255, 255, 255, 0.08);
            --glass-border: rgba(255, 255, 255, 0.2);
            --glass-shadow: rgba(0, 0, 0, 0.5);
            --success-bg: #d1e7dd;
            --success-text: #0f5132;
            --warning-bg: #fff3cd;
            --warning-text: #664d03;
            --danger-bg: #f8d7da;
            --danger-text: #842029;
            --info-bg: #cff4fc;
            --info-text: #055160;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            overflow: hidden;
            background: linear-gradient(135deg, var(--marsom-blue) 0%, #34495e 50%, var(--marsom-orange) 100%);
            background-size: 400% 400%;
            animation: gradientAnimation 20s ease infinite;
            color: var(--text-dark);
            position: relative;
        }

        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .particles {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            overflow: hidden;
            z-index: -1;
        }

        .particle {
            position: absolute;
            background: rgba(255, 140, 0, 0.1);
            clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
            animation: float 25s infinite ease-in-out;
            opacity: 0;
            filter: blur(2px);
        }
        .particle:nth-child(even) { background: rgba(0, 31, 63, 0.1); }
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
            100% { transform: translateY(-100vh) translateX(50px) rotate(360deg); opacity: 0; }
        }

        #loading-screen {
            position: fixed;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--marsom-blue) 0%, #34495e 50%, var(--marsom-orange) 100%);
            background-size: 400% 400%;
            animation: gradientAnimation 20s ease infinite;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            transition: opacity 0.5s ease-out;
        }
        /* Special class to hide row from screen but keep it for Excel export */
.excel-only-row th {
    height: 0 !important;
    line-height: 0 !important;
    padding: 0 !important;
    border: none !important;
    color: transparent !important;
    overflow: hidden;
    white-space: nowrap;
}
/* Ensure the row itself takes no space */
.excel-only-row {
    visibility: collapse;
}
        .loader {
            width: 50px;
            height: 50px;
            border: 5px solid rgba(255, 255, 255, 0.3);
            border-top: 5px solid var(--marsom-orange);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }

        #loading-screen h3 {
            font-family: 'El Messiri', sans-serif;
            color: var(--text-light);
            font-weight: 700;
            font-size: 22px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .main-content-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 70px;
        }

        .main-container {
            max-width: 95%;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 50px 0 rgba(0, 0, 0, 0.5);
            padding: 40px;
            animation: fadeInScale 0.9s ease-out forwards;
            color: var(--text-light);
            width: 100%;
            margin-bottom: 20px;
        }

        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.97); }
            to { opacity: 1; transform: scale(1); }
        }

        .top-nav-buttons {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 10;
        }

        .top-nav-button {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            padding: 10px 15px;
            color: var(--text-light);
            text-decoration: none;
            font-family: 'El Messiri', sans-serif;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .top-nav-button:hover {
            background: rgba(255, 255, 255, 0.25);
            border-color: var(--marsom-orange);
            color: var(--marsom-orange);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        .top-nav-button i {
            font-size: 1.2em;
            color: var(--text-light);
            transition: color 0.3s ease;
        }

        .top-nav-button:hover i {
            color: var(--marsom-orange);
        }

        .page-title {
            font-family: 'El Messiri', sans-serif;
            font-weight: 700;
            color: var(--text-light);
            text-align: center;
            margin-bottom: 40px;
            font-size: 2.8rem;
            text-shadow: 0 3px 6px rgba(0, 0, 0, 0.4);
            position: relative;
            display: inline-block;
            padding-bottom: 10px;
        }
        .page-title::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, var(--marsom-blue), var(--marsom-orange));
            border-radius: 2px;
        }

        .filter-section {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 25px;
            animation: fadeInUp 0.8s ease-out 0.2s;
            animation-fill-mode: backwards;
        }

        .filter-label {
            font-weight: 700;
            color: var(--marsom-blue);
            margin-bottom: 10px;
            font-family: 'El Messiri', sans-serif;
            font-size: 1.1rem;
        }

        .column-filter {
            background: rgba(255, 255, 255, 0.95);
            border: 2px solid rgba(0, 31, 63, 0.2);
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 0.9rem;
            color: var(--text-dark);
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
        }

        .column-filter:focus {
            border-color: var(--marsom-orange);
            box-shadow: 0 0 0 0.25rem rgba(255, 140, 0, 0.25);
            outline: none;
        }

        .table-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            padding: 25px;
            animation: fadeInUp 1s ease-out 0.3s;
            animation-fill-mode: backwards;
        }

        .table-responsive { 
            overflow-x: auto;
            max-height: 70vh;
        }

        .dataTables-example thead th {
            background-color: var(--marsom-blue) !important;
            color: var(--text-light);
            font-weight: 500;
            text-align: center;
            vertical-align: middle;
            border-bottom: 2px solid #00152b;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .dataTables-example tbody td {
            text-align: center;
            vertical-align: middle;
            font-size: 14px;
            padding: 10px 8px;
            white-space: nowrap;
        }

        .dataTables-example tbody tr {
            opacity: 0;
            animation: fadeIn 0.5s ease-out forwards;
        }
        
        <?php for ($i = 0; $i < 20; $i++): ?>
        .dataTables-example tbody tr:nth-child(<?php echo $i + 1; ?>) {
            animation-delay: <?php echo $i * 0.05; ?>s;
        }
        <?php endfor; ?>

        .dataTables-example tbody tr:hover {
            background-color: rgba(0, 31, 63, 0.05);
            transform: scale(1.01);
            transition: transform 0.2s ease-in-out;
        }
        
        .dt-buttons .btn {
            background-color: var(--marsom-orange);
            border-color: var(--marsom-orange);
            color: var(--text-light);
            font-weight: 500;
            margin: 0 2px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .dt-buttons .btn:hover {
            background-color: #e0882f;
            border-color: #e0882f;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
        
        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 8px 12px;
            transition: all 0.3s ease;
        }
        .dataTables_wrapper .dataTables_filter input:focus,
        .dataTables_wrapper .dataTables_length select:focus {
            border-color: var(--marsom-orange);
            box-shadow: 0 0 0 0.25rem rgba(255, 140, 0, 0.25);
        }

        .filter-header {
            background-color: #e3f2fd !important;
            border-bottom: 2px solid #bbdefb;
        }

        .filter-header input,
        .filter-header select {
            font-size: 0.85rem;
            padding: 4px 8px;
            margin-bottom: 5px;
        }

        /* --- NEW MODAL STYLES --- */
        #detailsModal .modal-content {
            background-color: #f8f9fa;
            border-radius: 15px;
        }
        #detailsModal .modal-header {
            background-color: var(--marsom-blue);
            color: var(--text-light);
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }
        #detailsModal .modal-title {
            font-family: 'El Messiri', sans-serif;
        }
        #detailsModal .table-sm th {
            background-color: #e9ecef;
        }
        .status-badge {
            font-size: 0.9em;
            padding: 0.4em 0.7em;
            border-radius: 0.25rem;
        }
        .status-absent { background-color: var(--danger-bg); color: var(--danger-text); }
        .status-present { background-color: var(--success-bg); color: var(--success-text); }
        .status-vacation { background-color: var(--info-bg); color: var(--info-text); }
        .status-weekend { background-color: #f8f9fa; color: #6c757d; }
        .status-single { background-color: var(--warning-bg); color: var(--warning-text); }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .filter-badge {
            background-color: var(--marsom-orange);
            color: white;
            border-radius: 12px;
            padding: 2px 8px;
            font-size: 0.75rem;
            margin-left: 5px;
        }
     .salary-summary-item {
            border-bottom: 1px solid #e9ecef;
            padding: 12px 0;
            transition: background-color 0.2s ease;
        }

        .salary-summary-item:hover {
            background-color: #f8f9fa;
        }

        .salary-summary-item:last-child {
            border-bottom: none;
        }

        .calculation-details {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
            border-right: 4px solid var(--marsom-orange);
        }

        .calculation-formula {
            font-family: 'Courier New', monospace;
            background-color: #e9ecef;
            padding: 8px 12px;
            border-radius: 4px;
            margin: 5px 0;
            font-size: 0.9em;
        }

        .summary-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border: 1px solid #e9ecef;
        }

        .summary-header {
            color: var(--marsom-blue);
            border-bottom: 2px solid var(--marsom-orange);
            padding-bottom: 10px;
            margin-bottom: 15px;
            font-family: 'El Messiri', sans-serif;
        }

        .positive-amount {
            color: #198754;
            font-weight: bold;
        }
/* Summary Totals Styling */
.summary-card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 15px;
    border: 1px solid rgba(0, 31, 63, 0.1);
    transition: all 0.3s ease;
    height: 100%;
}
/* ============================================= */
/* COLUMN REORDER STYLES - ADD THESE */
/* ============================================= */
.column-reorder-container {
    background: rgba(255, 255, 255, 0.95);
    border: 1px solid rgba(0, 31, 63, 0.2);
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.reorder-header {
    background-color: #001f3f;
    color: white;
    padding: 10px 15px;
    border-radius: 8px;
    margin-bottom: 15px;
    font-family: 'El Messiri', sans-serif;
}

#columnList {
    min-height: 200px;
    border: 2px dashed #ccc;
    border-radius: 8px;
    padding: 10px;
    background-color: #f8f9fa;
}

.column-item {
    background-color: white;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 10px 15px;
    margin-bottom: 8px;
    cursor: move;
    transition: all 0.3s ease;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.column-item:hover {
    background-color: #e3f2fd;
    border-color: #FF8C00;
    transform: translateX(-5px);
}

.column-item.ui-sortable-helper {
    background-color: #fff3cd;
    border-color: #FF8C00;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.column-item.ui-state-highlight {
    height: 40px;
    background-color: #f8f9fa;
    border: 2px dashed #6c757d;
    border-radius: 6px;
}

.column-index {
    background-color: #001f3f;
    color: white;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.9em;
}

.column-name {
    flex-grow: 1;
    margin: 0 15px;
    font-weight: 500;
    color: #001f3f;
}

.column-visibility {
    display: flex;
    align-items: center;
    gap: 10px;
}

.visibility-toggle {
    cursor: pointer;
    font-size: 1.2em;
    color: #6c757d;
    transition: color 0.3s ease;
}

.visibility-toggle.active {
    color: #FF8C00;
}

.visibility-toggle:hover {
    color: #001f3f;
}

.reorder-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
    padding-top: 15px;
    border-top: 1px solid #dee2e6;
}

.btn-reorder {
    background-color: #FF8C00;
    border-color: #FF8C00;
    color: white;
    font-weight: 500;
    padding: 8px 20px;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.btn-reorder:hover {
    background-color: #e67e00;
    border-color: #e67e00;
    transform: translateY(-2px);
}

.btn-reset {
    background-color: #6c757d;
    border-color: #6c757d;
    color: white;
}

.column-hidden {
    opacity: 0.6;
    background-color: #f8f9fa;
    text-decoration: line-through;
}

.column-hidden:hover {
    background-color: #e9ecef;
}

.table-card {
    position: relative;
}

.table-actions {
    position: absolute;
    top: 15px;
    left: 15px;
    z-index: 10;
}
.summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.summary-card h6 {
    font-size: 0.9rem;
    color: var(--marsom-blue);
    margin-bottom: 8px;
    font-weight: 600;
}

.summary-card .amount {
    font-size: 1.2rem;
    font-weight: 700;
    direction: ltr;
    text-align: left;
    font-family: 'Courier New', monospace;
}

.summary-card .positive {
    color: #198754;
}

.summary-card .negative {
    color: #dc3545;
}

.summary-card .neutral {
    color: #6c757d;
}

#total-net-salary {
    background: linear-gradient(135deg, var(--marsom-blue), #004080);
    color: white;
    border: none;
}

#total-net-salary h6,
#total-net-salary .amount {
    color: white;
}

#total-employees-count {
    background-color: #f8f9fa;
    border-left: 4px solid var(--marsom-orange);
}
        .negative-amount {
            color: #dc3545;
            font-weight: bold;
        }

        .neutral-amount {
            color: #6c757d;
            font-weight: bold;
        }

        .breakdown-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
        }

        .breakdown-label {
            flex: 1;
            color: #495057;
        }

        .breakdown-value {
            flex: 0 0 120px;
            text-align: left;
            font-weight: 500;
        }

        .breakdown-details {
            flex: 0 0 200px;
            text-align: left;
            font-size: 0.85em;
            color: #6c757d;
        }

        .total-row {
            border-top: 2px solid #dee2e6;
            margin-top: 10px;
            padding-top: 10px;
            font-weight: bold;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }
.breakdown-value, .calculation-formula, .breakdown-details {
    font-family: 'Courier New', monospace;
    direction: ltr;
    text-align: left;
}

.calculation-formula {
    font-family: 'Courier New', monospace;
    direction: ltr;
    unicode-bidi: embed;
}
    </style>
</head>
<body>

<div class="particles">
  <div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div><div class="particle"></div>
</div>

<div id="loading-screen">
  <div class="loader"></div>
  <h3>جاري تجهيز مسير الرواتب...</h3>
</div>

<div class="main-content-wrapper" style="visibility: hidden; opacity: 0;">
    <div class="top-nav-buttons">
        <a href="#" onclick="history.back(); return false;" class="top-nav-button">
            <i class="fas fa-arrow-right"></i><span>رجوع</span>
        </a>
        <a href="<?php echo site_url('users1/main_hr1'); ?>" class="top-nav-button">
            <i class="fas fa-home"></i><span>الرئيسية</span>
        </a>
    </div>

    <div class="main-container container-fluid">
        <div class="text-center">
            <h1 class="page-title">مسير الرواتب</h1>
            <div class="text-start mb-3">
                <button id="process-salary-btn" class="btn btn-success fw-bold">
                    <i class="fas fa-check-circle me-2"></i>معالجة وحفظ الرواتب
                </button>
               <button id="reorderColumnsBtn" class="btn btn-info fw-bold ms-2">
    <i class="fas fa-columns me-2"></i>ترتيب الأعمدة
</button>
                <button id="check-exclusions-btn" class="btn btn-warning fw-bold ms-2">
    <i class="fas fa-user-slash me-2"></i>تقرير المستبعدين
</button>
                <button type="button" id="btnExportExcelJS" class="btn btn-primary fw-bold ms-2">
    <i class="fas fa-file-excel me-2"></i>تصدير إكسل (مطابق للشاشة)
</button>

<a href="#" id="btnExportBankExcel" class="btn btn-dark fw-bold ms-2">
    <i class="fas fa-university me-2"></i> تصدير ملف البنك
</a>
            </div>
        </div>
        <!-- ============================================= -->
<!-- COLUMN REORDER PANEL - ADD THIS SECTION -->
<!-- ============================================= -->
<!-- Column Reorder Panel -->
<div id="columnReorderPanel" class="row mt-3" style="display: none;">
    <div class="col-12">
        <div class="column-reorder-container">
            <div class="reorder-header">
                <h5 class="mb-0"><i class="fas fa-sort me-2"></i>ترتيب وإظهار/إخفاء أعمدة الجدول</h5>
                <small class="opacity-75">اسحب وأفلت الأعمدة لترتيبها | انقر على أيقونة العين لإظهار/إخفاء العمود</small>
            </div>
            
            <div id="columnList">
                <!-- Columns will be populated by JavaScript -->
            </div>
            
            <div class="reorder-actions">
                <button id="applyOrderBtn" class="btn btn-reorder">
                    <i class="fas fa-check me-2"></i>حفظ الترتيب
                </button>
                <button id="resetOrderBtn" class="btn btn-reorder btn-reset">
                    <i class="fas fa-redo me-2"></i>إعادة التعيين
                </button>
                <button id="closePanelBtn" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>إغلاق
                </button>
            </div>
        </div>
    </div>
</div>
<!-- ============================================= -->
<!-- END COLUMN REORDER PANEL -->
<!-- ============================================= -->
        <!-- Add this after the export button, before the filter section -->
<div class="row mt-4 mb-4">
    <div class="col-12">
        <div class="card table-card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Payroll Summary Totals</h5>
                <small class="opacity-75">Calculated based on current filters</small>
            </div>
            <div class="card-body">
                <div class="row" id="payroll-totals">
                    <!-- Will be populated by JavaScript -->
                    <div class="text-center py-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Calculating totals...</span>
                        </div>
                        <p class="mt-2">Calculating totals...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
// ==================================================================
// START: ROBUST PRE-CALCULATION (HEADERS & DATA)
// ==================================================================

// 1. Initialize Arrays
if (!isset($dynamic_deductions_headers)) $dynamic_deductions_headers = [];
if (!isset($dynamic_deductions_data)) $dynamic_deductions_data = [];
if (!isset($discounts_map)) $discounts_map = [];

try {
    $ci =& get_instance();
    if (!isset($ci->db)) $ci->load->database();

    // 2. Prepare Variables
    $curr_sheet_id = (isset($id) && !empty($id)) ? $ci->db->escape($id) : 0;
    
    // Parse Dates safely
    $s_str = '1970-01-01';
    $e_str = '1970-01-01';
    if (isset($get_salary_sheet['start_date']) && isset($get_salary_sheet['end_date'])) {
        $s_str = $ci->db->escape(trim($get_salary_sheet['start_date']));
        $e_str = $ci->db->escape(trim($get_salary_sheet['end_date']));
    }

    // 3. RAW SQL QUERY (Fixes the "Empty Sheet ID" confusion)
    // This query explicitly asks for:
    // A. Recurring items
    // B. Matching Sheet ID
    // C. Items with (NULL or 0 or Empty) Sheet ID that match the Date Range
    $sql = "SELECT * FROM orders.discounts 
            WHERE is_recurring = 1 
            OR sheet_id = $curr_sheet_id 
            OR ( 
                (sheet_id IS NULL OR sheet_id = '' OR sheet_id = '0') 
                AND discount_date >= $s_str 
                AND discount_date <= $e_str 
            )";

    $query = $ci->db->query($sql);

    // 4. PROCESS RESULTS
    if ($query) {
        foreach ($query->result() as $disc) {
            // Add to Total Map (For Math)
            if (!isset($discounts_map[$disc->emp_id])) $discounts_map[$disc->emp_id] = 0;
            $discounts_map[$disc->emp_id] += (float)$disc->amount;

            // Add to Dynamic Columns (For Headers)
            $d_type = trim($disc->type);
            if (!empty($d_type)) {
                // Add Header if new
                if (!in_array($d_type, $dynamic_deductions_headers)) {
                    $dynamic_deductions_headers[] = $d_type;
                }

                // Add Cell Data
                if (!isset($dynamic_deductions_data[$disc->emp_id])) {
                    $dynamic_deductions_data[$disc->emp_id] = [];
                }
                if (!isset($dynamic_deductions_data[$disc->emp_id][$d_type])) {
                    $dynamic_deductions_data[$disc->emp_id][$d_type] = 0;
                }
                $dynamic_deductions_data[$disc->emp_id][$d_type] += (float)$disc->amount;
            }
        }
    }

} catch (Exception $e) { }
// ==================================================================
// END: ROBUST PRE-CALCULATION
// ==================================================================
?>
        <div class="row">
            <div class="col-12">
                <div class="filter-section">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="filter-label">
                                <i class="fas fa-building me-2"></i>فلترة حسب الشركة:
                            </label>
                            <select id="companyFilter" class="form-select column-filter">
    <option value="">جميع الشركات</option>
    <?php
    $unique_companies = [];
    // We will map normalized names to the original first occurrence to keep the value consistent
    $normalized_map = []; 

    foreach ($employees as $row) {
        if (!empty($row->company_name)) {
            $original_name = $row->company_name;
            // Normalize: Trim spaces and remove hidden characters
            $normalized_name = trim(preg_replace('/\s+/', ' ', $original_name));
            
            // Only add if this normalized version hasn't been added yet
            if (!in_array($normalized_name, $unique_companies)) {
                $unique_companies[] = $normalized_name;
                // We use the normalized name for both value and display to ensure the filter works correctly
                echo '<option value="' . htmlspecialchars($normalized_name) . '">' . htmlspecialchars($normalized_name) . '</option>';
            }
        }
    }
    ?>
</select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="filter-label">
                                <i class="fas fa-user-tie me-2"></i>المسمى الوظيفي:
                            </label>
                            <select id="professionFilter" class="form-select column-filter">
                                <option value="">جميع المسميات</option>
                                <?php
                                $unique_professions = [];
                                foreach ($employees as $row) {
                                    if (!empty($row->profession) && !in_array($row->profession, $unique_professions)) {
                                        $unique_professions[] = $row->profession;
                                        echo '<option value="' . htmlspecialchars($row->profession) . '">' . htmlspecialchars($row->profession) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="filter-label">
                                <i class="fas fa-sitemap me-2"></i>الإدارة:
                            </label>
                            <select id="departmentFilter" class="form-select column-filter">
                                <option value="">جميع الإدارات</option>
                                <?php
                                $unique_departments = [];
                                foreach ($employees as $row) {
                                    if (!empty($row->n1) && !in_array($row->n1, $unique_departments)) {
                                        $unique_departments[] = $row->n1;
                                        echo '<option value="' . htmlspecialchars($row->n1) . '">' . htmlspecialchars($row->n1) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="filter-label">
                                <i class="fas fa-flag me-2"></i>الجنسية:
                            </label>
                            <select id="nationalityFilter" class="form-select column-filter">
                                <option value="">جميع الجنسيات</option>
                                <?php
                                $unique_nationalities = [];
                                foreach ($employees as $row) {
                                    if (!empty($row->nationality) && !in_array($row->nationality, $unique_nationalities)) {
                                        $unique_nationalities[] = $row->nationality;
                                        echo '<option value="' . htmlspecialchars($row->nationality) . '">' . htmlspecialchars($row->nationality) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12 text-center">
                            <button id="resetAllFilters" class="btn btn-outline-danger">
                                <i class="fas fa-redo me-2"></i>إعادة تعيين جميع الفلاتر
                            </button>
                            <span id="activeFiltersCount" class="filter-badge" style="display: none;">0 فلتر نشط</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card table-card">
                    <div class="table-actions">
    <button id="quickToggleColumns" class="btn btn-sm btn-outline-primary" title="تبديل عرض/إخفاء الأعمدة">
        <i class="fas fa-eye-slash"></i>
    </button>
</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover dataTables-example" style="width:100%">
    <thead>
    <tr class="excel-only-row">
        <!-- Employee Information Group (12 columns) -->
        <th></th> <!-- Actions -->
        <th></th> <!-- Employee ID -->
        <th>Totals:</th> <!-- Name -->
        <th></th> <!-- ID Number -->
        <th></th> <!-- IBAN -->
        <th></th> <!-- Bank Name -->
        <th></th> <!-- Nationality -->
        <th></th> <!-- Employee Status -->
        <th></th> <!-- Job Title -->
        <th></th> <!-- Department -->
        <th></th> <!-- Company Name -->
        <th id="h_total_salary">0.00</th> <!-- Total Salary -->
        
        <!-- Basic Allowances Group (5 columns) -->
        <th id="h_base">0.00</th> <!-- Base Salary -->
        <th id="h_house">0.00</th> <!-- Housing -->
        <th id="h_trans">0.00</th> <!-- Transport -->
        <th id="h_other">0.00</th> <!-- Other Allowances -->
        <th id="h_comm">0.00</th> <!-- Communication -->
        
        <!-- Dynamic Additions (variable columns) -->
        <?php foreach($dynamic_additions_headers as $header): ?>
            <th></th>
        <?php endforeach; ?>
        
        <!-- Attendance & Previous Salary (9 columns) -->
        <th></th> <!-- Previous Salary -->
        <th id="h_abs_days">0</th> <!-- Absence Days -->
        <th id="h_unpaid_days">0</th> <!-- Unpaid Days -->
        <th id="h_late_mins">0</th> <!-- Late Minutes -->
        <th id="h_early_mins">0</th> <!-- Early Minutes -->
        <th id="h_single_punch">0</th> <!-- Single Punch -->
        <th id="h_diff_pos">0.00</th> <!-- Positive Differences -->
        <th id="h_diff_neg">0.00</th> <!-- Negative Differences -->
        
        <!-- Basic Deductions (5 columns) -->
        <th id="h_ded_abs">0.00</th> <!-- Absence Deduction -->
        <th id="h_ded_unpaid">0.00</th> <!-- Unpaid Deduction -->
        <th id="h_ded_single">0.00</th> <!-- Single Punch Deduction -->
        <th id="h_ded_late">0.00</th> <!-- Late Deduction -->
        <th id="h_ded_early">0.00</th> <!-- Early Deduction -->
        
        <!-- Dynamic Deductions (variable columns) -->
        <?php foreach($dynamic_deductions_headers as $header): ?>
            <th></th>
        <?php endforeach; ?>
        
        <!-- Final Calculation (4 columns) -->
        <th id="h_ded_total_att">0.00</th> <!-- Total Attendance Deductions -->
        <th id="h_salary_before">0.00</th> <!-- Salary Before Insurance -->
        <th id="h_ded_gosi">0.00</th> <!-- Insurance Deduction -->
        <th id="h_net_salary">0.00</th> <!-- Net Salary -->
    </tr>

    <tr>
        <!-- ============ EMPLOYEE INFORMATION GROUP (12 columns) ============ -->
        <th>الإجراءات</th>
        <th>الرقم الوظيفي</th>
        <th>اسم الموظف</th>
        <th>رقم الهوية</th>
        <th>رقم الايبان</th>
        <th>اسم البنك</th>
        <th>الجنسية</th>
        <th>حالة الموظف</th>
        <th>المسمى الوظيفي</th>
        <th>الادارة</th>
        <th>اسم الشركة</th>
        <th>إجمالي الأجر</th>
        
        <!-- ============ BASIC ALLOWANCES GROUP (5 columns) ============ -->
        <th>الراتب الأساسي</th>
        <th>بدل السكن</th>
        <th>بدل النقل</th>
        <th>بدلات أخرى</th>
        <th>بدل الاتصال</th>
        <th>ايام الغياب</th>
        <th>أيام إجازة غير مدفوعة</th>
        <th>إجمالي الدقائق المتأخرة</th>
        <th>إجمالي الدقائق المبكرة</th>
        <th>أيام بصمة منفردة</th>
        
        <!-- ============ DYNAMIC ADDITIONAL ALLOWANCES GROUP ============ -->
        <?php foreach($dynamic_additions_headers as $header): ?>
            <th class="bg-success text-white"><?php echo html_escape($header); ?> (إضافة)</th>
        <?php endforeach; ?>
        
        <!-- ============ ATTENDANCE & TIME DIFFERENCES GROUP (9 columns) ============ -->
        <th>راتب الشهر السابق</th>
        
        <th class="bg-light text-success">فروقات (موجب)</th>
        <th class="bg-light text-danger">فروقات (سالب)</th>
        
        <!-- ============ BASIC DEDUCTIONS GROUP (5 columns) ============ -->
        <th>خصم الغياب</th>
        <th>خصم إجازة غير مدفوعة</th>
        <th>خصم بصمة منفردة</th>
        <th>خصم التأخير</th>
        <th>خصم الخروج المبكر</th>
        
        <!-- ============ DYNAMIC DEDUCTIONS GROUP ============ -->
        <?php foreach($dynamic_deductions_headers as $header): ?>
            <th class="bg-danger text-white"><?php echo html_escape($header); ?> (خصم)</th>
        <?php endforeach; ?>
        
        <!-- ============ FINAL SALARY CALCULATION GROUP (4 columns) ============ -->
        <th>إجمالي خصم الحضور والانصراف</th>
        <th>اجمالي الراتب ما قبل خصم التأمينات</th>
        <th>اجمالي خصم التامينات</th>
        <th>صافي الراتب</th>
    </tr>
</thead>

<thead class="filter-header">
    <tr>
        <!-- Employee Information Filters (12 columns) -->
        <th></th> <!-- Actions -->
        <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
        <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
        <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
        <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
        <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
        <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
        <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
        <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
        <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
        <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
        <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
        
        <!-- Basic Allowances Filters (5 columns) -->
        <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
        <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
        <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
        <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
        <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
        <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
        <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
        <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
        <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
        <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
        <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
        
        <!-- Dynamic Allowances Filters -->
        <?php if(!empty($dynamic_additions_headers)): ?>
            <?php foreach($dynamic_additions_headers as $header): ?>
                <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <!-- Attendance & Differences Filters (9 columns) -->
        <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
        
        <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
        
        <!-- Basic Deductions Filters (5 columns) -->
        <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
        <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
        <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
        <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
        <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
        
        <!-- Dynamic Deductions Filters -->
        <?php if(!empty($dynamic_deductions_headers)): ?>
            <?php foreach($dynamic_deductions_headers as $header): ?>
                <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <!-- Final Calculation Filters (4 columns) -->
        <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
        <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
        <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
        <th><input type="text" class="form-control form-control-sm column-search" placeholder="بحث..." ></th>
    </tr>
</thead>
                             <tbody>
   <?php 
    // ==================================================================
    // START: PAYROLL PERIOD DEFINITIONS
    // ==================================================================
    $sheet_start_dt = null;
    $sheet_end_dt = null;
    $prev_month_year = '0000-00'; // Calendar month *before* payroll start
    $payroll_start_month_year = '0000-00'; // Calendar month *of* payroll start
    $payroll_end_month_year = '0000-00'; // Calendar month *of* payroll end
    $payroll_start_day = 16; // Default payroll start day

    if (isset($get_salary_sheet['start_date']) && isset($get_salary_sheet['end_date'])) {
        try {
            $sheet_start_dt = new DateTime(trim($get_salary_sheet['start_date']));
            $sheet_end_dt   = new DateTime(trim($get_salary_sheet['end_date']));

            $payroll_start_month_year = $sheet_start_dt->format('Y-m'); 
            $payroll_end_month_year   = $sheet_end_dt->format('Y-m');
            
            // This is the calendar month *before* the payroll period *starts*
            // e.g., For Sep 16 payroll, $prev_month_year is "2025-08"
            $prev_month_dt = (clone $sheet_start_dt)->modify('-1 month');
            $prev_month_year = $prev_month_dt->format('Y-m');
            
            // ***MODIFICATION: Get the day of the month payroll starts***
            $payroll_start_day = (int)$sheet_start_dt->format('j'); // e.g., 16

        } catch (Exception $e) {
            echo "";
        }
    }
    // ==================================================================
    // END: PAYROLL PERIOD DEFINITIONS
    // ==================================================================

    ?>
    <?php
// ==================================================================
// START: DISCOUNT FIX (CORRECT TABLE: orders.discounts)
// ==================================================================
$discounts_map = []; 

try {
    // 1. Get CodeIgniter Instance
    $ci =& get_instance();
    if (!isset($ci->db)) { $ci->load->database(); }

    // 2. Prepare Variables
    $curr_sheet_id = (isset($id) && !empty($id)) ? $id : 0;
    
    // Dates (Fallbacks if missing)
    $s_str = (isset($sheet_start_dt) && $sheet_start_dt) ? $sheet_start_dt->format('Y-m-d') : '1970-01-01';
    $e_str = (isset($sheet_end_dt) && $sheet_end_dt)     ? $sheet_end_dt->format('Y-m-d')   : '1970-01-01';

    // 3. BROAD SQL QUERY (Using Correct Table Name)
    // We fetch: Recurring OR Matching Sheet OR Matching Dates
    $ci->db->group_start();
        $ci->db->where('is_recurring', 1);
        $ci->db->or_where('sheet_id', $curr_sheet_id);
        
        // If we have valid dates, fetch anything in range to filter later
        if ($s_str != '1970-01-01') {
            $ci->db->or_group_start();
                $ci->db->where('discount_date >=', $s_str);
                $ci->db->where('discount_date <=', $e_str);
            $ci->db->group_end();
        }
    $ci->db->group_end();
    
    // *** FIX: USING CORRECT TABLE NAME ***
    $query = $ci->db->get('orders.discounts');

    // 4. STRICT PHP FILTERING
    if ($query) {
        foreach ($query->result() as $disc) {
            $include_this = false;

            // CHECK 1: Is it Recurring? -> YES
            if (isset($disc->is_recurring) && $disc->is_recurring == 1) {
                $include_this = true;
            }
            // CHECK 2: Does Sheet ID match exactly? -> YES
            elseif (!empty($disc->sheet_id) && $disc->sheet_id == $curr_sheet_id) {
                $include_this = true;
            }
            // CHECK 3: Is Sheet ID Empty AND Date is in range? -> YES
            // (Only checks date if Sheet ID is effectively unassigned)
            elseif (empty($disc->sheet_id) || $disc->sheet_id == 0 || $disc->sheet_id == '0') {
                if ($disc->discount_date >= $s_str && $disc->discount_date <= $e_str) {
                    $include_this = true;
                }
            }
            
            // CHECK 4 (Safety): If Sheet ID is set BUT matches a DIFFERENT sheet -> NO
            // (Prevents including a discount meant for another sheet, even if date matches)
            if (!empty($disc->sheet_id) && $disc->sheet_id != 0 && $disc->sheet_id != $curr_sheet_id) {
                $include_this = false; 
            }

            // 5. Add to Map
            if ($include_this) {
                if (!isset($discounts_map[$disc->emp_id])) {
                    $discounts_map[$disc->emp_id] = 0;
                }
                $discounts_map[$disc->emp_id] += (float)$disc->amount;
            }
        }
    }

} catch (Exception $e) {
    // Prevent crash
}
// ==================================================================
// END: DISCOUNT FIX
// ==================================================================
?>
    <?php foreach ($employees as $row): ?>
        <?php
        // ==================================================================
        // START: 1. INITIALIZE VARIABLES & FLAGS
        // ==================================================================
        $emp_id = $row->employee_id;
        $full_time_salary = (float)($row->total_salary ?? 0); // e.g., 5500 (Always the full salary)
        $salary_for_this_period = $full_time_salary; // Default to full. Will be prorated to 4400 if new.
        
        $employee_note = $notes_map[$emp_id] ?? '—';
        $reparations_amount = $reparations_map[$emp_id] ?? 0.0; // Manual additions
        
        $new_emp_details = $new_employee_map[$emp_id] ?? null;
        $is_new_employee = false;
        $skip_this_month = false;
        $previous_month_comp = 0.0; // Back-pay
        $hiring_day_difference = 0.0; // For new column (e.g., 1100)
        $join_date_display = '';
        $join_dt = null;
        $join_month_year = '0000-00';
        $join_day = 0;
        $actual_worked_days = 30;
        
        $is_stopped = isset($stopped_salary_map[$emp_id]);
        $is_exempt = isset($exemption_map[$emp_id]);

        $prev_absence_deduct = 0.0;
        $prev_late_deduct = 0.0;
        $prev_early_deduct = 0.0;
        $prev_single_thing_deduct = 0.0;
        $total_previous_attendance_deductions = 0.0;
        // ==================================================================
        // END: 1. INITIALIZE VARIABLES & FLAGS
        // ==================================================================


       // ==================================================================
        // START: 2. NEW EMPLOYEE PRORATION LOGIC (RULES 1 & 2) - **FIXED**
        // ==================================================================
        if ($new_emp_details && !empty($new_emp_details->join_date) && $sheet_start_dt) {
            $is_new_employee = true;
            try {
                $join_dt = new DateTime(str_replace('/', '-', trim($new_emp_details->join_date)));
                $join_date_display = $join_dt->format('Y-m-d');
                $join_month_year = $join_dt->format('Y-m'); // Calendar month of join
                $join_day = (int)$join_dt->format('j'); // Calendar day of join

                // --- Main Logic Block ---
                
                // CASE 1: Employee joined *within* this payroll period
                if ($join_dt >= $sheet_start_dt && $join_dt <= $sheet_end_dt) {
                    
                    // Rule 1: Joined AFTER the 17th (e.g., 18th onwards) -> Defer to 1.5 months package
                    if ($join_day > 17) { 
                        // 1. Calculate days from JOIN DATE to end of current month
                        $days_in_current_month = max(1, 30 - $join_day + 1); 
                        
                        // 2. Add 30 days for the full next month
                        $actual_worked_days = $days_in_current_month + 30; 
                        
                        // 3. Calculate salary 
                        $salary_for_this_period = ($full_time_salary / 30) * $actual_worked_days;
                        
                        // 4. Set note
                        $employee_note = "موظف جديد (سياسة التأجيل) - راتب $actual_worked_days يوم";
                        
                        // 5. Hiring difference 
                        $hiring_day_difference = $full_time_salary - $salary_for_this_period;

                    } 
                    // Rule 2: Joined ON OR BEFORE the 17th (e.g., 17th -> 14 days paid this month)
                    else {
                        $actual_worked_days = max(1, 30 - $join_day + 1);
                        $salary_for_this_period = ($full_time_salary / 30) * $actual_worked_days; // Prorated salary
                        $hiring_day_difference = $full_time_salary - $salary_for_this_period;
                    }
                }
                
                // CASE 2: Employee joined *before* this payroll period started
                else if ($join_dt < $sheet_start_dt) {
                    // Back-pay logic handled naturally
                }
                
                // CASE 3: Employee joined *after* this payroll period ended (future hire)
                else if ($join_dt > $sheet_end_dt) {
                    $skip_this_month = true;
                    $employee_note = 'موظف لم يبدأ العمل بعد';
                    $salary_for_this_period = 0;
                    $hiring_day_difference = $full_time_salary;
                }

            } catch (Exception $e) {
                $employee_note = 'خطأ في قراءة تاريخ الانضمام';
                $is_new_employee = false;
            }
        }
        $new_emp_comp = 0.0; // This is the old column, now set to 0
        // ==================================================================
        // END: 2. NEW EMPLOYEE PRORATION LOGIC
        // ==================================================================
        

        // ==================================================================
        // START: 3. CALCULATE WORKING HOURS & MINUTE SALARY (FULL & CURRENT)
        // ==================================================================
        $rule_row = $data_map['rules'][$emp_id] ?? null;
        $default_working_hours = 8.0;
        if ($rule_row && isset($rule_row->working_hours) && $rule_row->working_hours !== '') {
             $wh_raw = trim((string)$rule_row->working_hours);
             if (strpos($wh_raw, ':') !== false) {
                 $parts = array_map('intval', explode(':', $wh_raw));
                 $h = $parts[0] ?? 0; $m = $parts[1] ?? 0; $s = $parts[2] ?? 0;
                 $default_working_hours = $h + ($m/60) + ($s/3600);
             } else { $default_working_hours = floatval($wh_raw); }
             if ($default_working_hours <= 0) { $default_working_hours = 8.0; }
        }

        // **DEDUCTION RATES (CURRENT & PREVIOUS) ARE ALWAYS BASED ON FULL-TIME SALARY**
        $full_time_daily_salary = $full_time_salary > 0 ? $full_time_salary / 30 : 0;
        $full_time_minute_salary = ($default_working_hours > 0 && $full_time_daily_salary > 0) ? ($full_time_daily_salary / $default_working_hours / 60) : 0;
        
        // Use the full-time rates for BOTH current and previous deductions.
        // This fixes the bug where back-pay (فروقات التوظيف) was inflating the deduction rate.
        $current_daily_salary = $full_time_daily_salary;
        $current_minute_salary = $full_time_minute_salary;
        // ==================================================================
        // END: 3. CALCULATE WORKING HOURS & MINUTE SALARY
        // ==================================================================


        // ==================================================================
        // START: 4. CALCULATE *PREVIOUS* PERIOD DEDUCTIONS (IF BACK-PAY EXISTS)
        // ==================================================================
        if ($previous_month_comp > 0 && isset($prev_attendance_map[$emp_id])) {
            $prev_summary = $prev_attendance_map[$emp_id];
            
            $prev_absence_days = $prev_summary ? (int)$prev_summary->absence : 0;
            $prev_minutes_late_val = $prev_summary ? (int)$prev_summary->minutes_late : 0;
            $prev_minutes_early_val = $prev_summary ? (int)$prev_summary->minutes_early : 0;
            $prev_single_thing_val = $prev_summary ? (int)$prev_summary->single_thing : 0;

            // Calculate previous deductions using FULL-TIME rates
            $prev_absence_deduct = $full_time_daily_salary * $prev_absence_days;
            $prev_late_deduct = $full_time_minute_salary * $prev_minutes_late_val;
            $prev_early_deduct = $full_time_minute_salary * $prev_minutes_early_val;
            $prev_single_thing_deduct = $full_time_daily_salary * $prev_single_thing_val;
            
            $total_previous_attendance_deductions = $prev_absence_deduct + $prev_late_deduct + $prev_early_deduct + $prev_single_thing_deduct;
        }
        // ==================================================================
        // END: 4. CALCULATE *PREVIOUS* PERIOD DEDUCTIONS
        // ==================================================================


        // ==================================================================
// START: 5. CALCULATE *CURRENT* PERIOD DEDUCTIONS
// ==================================================================
$summary = $attendance_map[$emp_id] ?? null;
$absence_from_summary = $summary ? (int)$summary->absence : 0;
$unpaid_leave_days = $unpaid_leave_map[$emp_id] ?? 0;
$half_day_vacations = $half_day_vacations_map[$emp_id] ?? 0;

// --- NEW LOGIC: Separate Unpaid Leave from Regular Absence ---
$pure_unpaid_days = $unpaid_leave_days;
$pure_absence_days = max(0, $absence_from_summary - $pure_unpaid_days); // REMOVED: - $half_day_vacations

$minutes_late = $summary ? (int)$summary->minutes_late : 0;
$minutes_early = $summary ? (int)$summary->minutes_early : 0;
$single_thing = $summary ? (int)$summary->single_thing : 0;

// Fix for new employees (ignore pre-joining absence)
if ($is_new_employee && !$skip_this_month && $previous_month_comp == 0 && $hiring_day_difference > 0) {
    $pure_absence_days = 0; 
    $single_thing = 0;
}

$absence_deduct = $current_daily_salary * $pure_absence_days;
$unpaid_leave_deduction = $current_daily_salary * $pure_unpaid_days;

$single_thing_deduct = $current_daily_salary * $single_thing;
$late_deduct = $current_minute_salary * $minutes_late;
$early_deduct = $current_minute_salary * $minutes_early;

// === ADD THIS: Half-day vacation deduction ===
$half_day_vacation_deduct = 0;

// === UPDATE THIS LINE: Add half_day_vacation_deduct ===
$attendance_total_deduct = $absence_deduct + $unpaid_leave_deduction + $single_thing_deduct + $late_deduct + $early_deduct + $half_day_vacation_deduct;

$discount_amount = $discounts_map[$emp_id] ?? 0.0;

$total_deductions = $attendance_total_deduct + $discount_amount + $total_previous_attendance_deductions;
// ==================================================================
// END: 5. CALCULATE *CURRENT* PERIOD DEDUCTIONS
// ==================================================================
        
        
        // ==================================================================
        // START: 6. INSURANCE DEDUCTION (GOSI)
        // ==================================================================
        $insurance_deduction = 0.0;
        $gosi_base_for_calc = 0.0;
        $gosi_calc_note = '';
        $discount_rate = $insurance_map[$emp_id] ?? 0.0;
        
        $base_plus_house = (float)($row->base_salary ?? 0) + (float)($row->housing_allowance ?? 0);
        $base_plus_house_capped = min($base_plus_house, 45000); 

        if (!$skip_this_month) { 
            $is_saudi = ($new_emp_details && trim((string)$new_emp_details->nationality) === 'سعودي') || $row->nationality === 'سعودي';

            if ($is_saudi && $discount_rate > 0) {
                
                // CASE 1: Joined THIS calendar month (and not skipped)
               // CASE 1: Joined THIS calendar month (and not skipped)
                if ($is_new_employee && ($join_month_year === $payroll_start_month_year || $join_month_year === $payroll_end_month_year) && !$skip_this_month) {
                    
                    // Policy 1: Joined ON OR BEFORE the 17th (Normal partial GOSI for this month)
                    if ($join_day <= 17) {
                        // FIX: Use actual days of the month (e.g., 31) instead of fixed 30
                        $days_in_join_month = (int)$join_dt->format('t'); // Get total days in that specific month (28, 29, 30, or 31)
                        
                        // Calculate days worked based on the month's actual length
                        $joined_calendar_days = max(1, $days_in_join_month - $join_day + 1); 
                        
                        // Calculate prorated base: (Salary / DaysInMonth) * WorkedDays
                        $prorated_gosi_base = ($base_plus_house_capped / $days_in_join_month) * $joined_calendar_days; 
                        
                        $insurance_deduction = $prorated_gosi_base * $discount_rate;
                        $gosi_calc_note = "راتب جزئي ($joined_calendar_days يوم من $days_in_join_month)";
                    }
                    // Policy 2: Joined AFTER the 17th (1.5 Months GOSI)
                    else {
                        // We are paying for 1.5 months. We must deduct GOSI for 1.5 months.
                        
                        // 1. GOSI for the partial *current* month
                        $days_in_join_month = (int)$join_dt->format('t'); 
                        $calendar_days_paid_this_month = max(1, $days_in_join_month - $join_day + 1); 

                        $prorated_base_this_month = ($base_plus_house_capped / $days_in_join_month) * $calendar_days_paid_this_month; 
                        $gosi_this_month = $prorated_base_this_month * $discount_rate;

                        // 2. GOSI for the full *next* month
                        $gosi_next_month = $base_plus_house_capped * $discount_rate;

                        // 3. Total GOSI
                        $insurance_deduction = $gosi_this_month + $gosi_next_month;
                        $prorated_gosi_base = $prorated_base_this_month + $base_plus_house_capped;
                        $gosi_calc_note = "راتب 1.5 شهر (كامل + جزئي $calendar_days_paid_this_month يوم)";
                    }
                }
                // CASE 2: Joined *last* calendar month (and were skipped)
                // THIS CASE IS NOW OBSOLETE for GOSI calculation, as it's handled in CASE 1.
                /* --- REMOVING THIS BLOCK ---
                else if ($is_new_employee && $join_month_year === $prev_month_year && $previous_month_comp > 0) {
                    $days_in_join_month = (int)$join_dt->format('t');
                    $join_day_last_month = (int)$join_dt->format('j');
                    
                    // ***MODIFICATION START: This is Policy 2***
                    // Base GOSI on the number of days being paid for (e.g., 15 days), not from join date
                    $joined_calendar_days_last_month = max(1, 30 - $payroll_start_day + 1);
                    // ***MODIFICATION END***

                    
                    $prorated_base_last_month = ($days_in_join_month > 0 ? ($base_plus_house_capped / $days_in_join_month) : 0.0) * $joined_calendar_days_last_month;
                    $gosi_last_month = $prorated_base_last_month * $discount_rate;

                    $gosi_this_month = $base_plus_house_capped * $discount_rate;
                    
                    $insurance_deduction = $gosi_last_month + $gosi_this_month;
                    $prorated_gosi_base = $prorated_base_last_month + $base_plus_house_capped;
                    $gosi_calc_note = "راتب كامل + راتب جزئي ($joined_calendar_days_last_month يوم)";
                }
                */ // --- END OF REMOVED BLOCK ---

                // CASE 3: Normal Saudi Employee
                else if ($is_saudi) {
                    $insurance_deduction = $base_plus_house_capped * $discount_rate;
                    $prorated_gosi_base = $base_plus_house_capped;
                    $gosi_calc_note = "راتب كامل";
                }
            }
        } 
        // ==================================================================
        // END: 6. INSURANCE DEDUCTION (GOSI)
        // ==================================================================


     // ==================================================================
        // START: 7. FINAL NET SALARY & SUSPENSION (VIRTUAL 30-DAY MONTH)
        // ==================================================================
        $salary_before_insurance = 0.0;
        $net_salary = 0.0;
        $suspension_deduction = 0.0;
        $is_fully_stopped_this_month = false;

        // 1. Calculate Suspension based on Standard 30-Day Calendar Month
        if (isset($sheet_end_dt) && $sheet_end_dt && !$skip_this_month) {
            
            // A. Determine the "Target Salary Month"
            // Since payroll ends Jan 15, the salary is for "January".
            $target_year = $sheet_end_dt->format('Y');
            $target_month = $sheet_end_dt->format('m');

            // B. Construct Standard 30-Day Range (e.g., Jan 1 to Jan 30)
            // This ensures Jan 11 stop results in 20 days deduction (11 to 30)
            $virtual_start = "$target_year-$target_month-01";
            
            // Handle Feb: If Feb, use Feb 28/29. For others, clamp to 30.
            $days_in_actual_month = (int)$sheet_end_dt->format('t');
            $end_day = ($days_in_actual_month < 30) ? $days_in_actual_month : 30;
            $virtual_end = "$target_year-$target_month-$end_day";

            // C. Get Stopped Days within this "Virtual Month"
            $days_stopped = $this->hr_model->get_suspensions_in_range($emp_id, $virtual_start, $virtual_end);

            if ($days_stopped > 0) {
                // RULE 1: Full Month Stop
                // If stopped days >= 30 (or full Feb), set salary to 0
                if ($days_stopped >= 30 || ($days_in_actual_month < 30 && $days_stopped >= $days_in_actual_month)) {
                    $is_fully_stopped_this_month = true;
                    $suspension_deduction = $full_time_salary;
                } 
                else {
                    // RULE 2: Partial Stop (Standard 30 Days Math)
                    // Example: Stop Jan 11 to Jan 30 = 20 Days
                    $daily_rate_30 = $full_time_salary / 30; 
                    $suspension_deduction = $daily_rate_30 * $days_stopped;
                }

                $sus_note = "(خصم إيقاف راتب: " . $days_stopped . " يوم)";
                $employee_note = ($employee_note === '—') ? $sus_note : $employee_note . ' ' . $sus_note;
            }
        }

        // 2. Final Math
        if ($is_fully_stopped_this_month) {
            // SCENARIO A: Fully Stopped
            $net_salary = 0.0; 
            $salary_before_insurance = 0.0; 
            $total_deductions = 0.0;
            $reparations_amount = 0.0; 
            $previous_month_comp = 0.0; 
            $insurance_deduction = 0.0;
            $employee_note = 'الراتب موقف (كامل الشهر)';
        }
        else if ($skip_this_month) {
            // SCENARIO B: Skipped
            $net_salary = 0.0; $salary_before_insurance = 0.0; $total_deductions = 0.0;
            $reparations_amount = 0.0; $previous_month_comp = 0.0; $insurance_deduction = 0.0;
        }
        else {
            // SCENARIO C: Active / Partial Pay
            $total_deductions = $attendance_total_deduct + $total_previous_attendance_deductions + $discount_amount + $suspension_deduction;
            
            if ($is_exempt) {
                $unpaid_leave_deduction = $current_daily_salary * $unpaid_leave_days;
                $total_deductions = $discount_amount + $unpaid_leave_deduction + $suspension_deduction;
                
                $base_note = ($employee_note === '—' || $employee_note === '') ? 'معفى من خصومات الحضور' : $employee_note;
                if ($unpaid_leave_days > 0) {
                    $employee_note = $base_note . " - خصم إجازة ($unpaid_leave_days يوم)";
                }
            }

            $salary_before_insurance = ($salary_for_this_period + $reparations_amount + $previous_month_comp) - $total_deductions;
            $net_salary = $salary_before_insurance - $insurance_deduction;
            
            if ($net_salary < 0) $net_salary = 0;
        }
        // ==================================================================
        // END: 7. FINAL NET SALARY & SUSPENSION
        // ==================================================================
        ?>

                                       <tr
    data-n1="<?= html_escape($row->employee_id) ?>"
    data-n2="<?= html_escape($row->subscriber_name) ?>"
    data-note="<?= html_escape($employee_note) ?>"
    data-n3="<?= html_escape($row->id_number) ?>"
    data-n4="<?= html_escape($row->n2) ?>"
    data-n5="<?= html_escape($row->n3) ?>"
    
    data-n6="<?= $full_time_salary ?>" data-n7="<?= $late_deduct ?>"
    data-n8="<?= $early_deduct ?>"
    data-n9="<?= $absence_deduct ?>"
    data-n10="<?= $insurance_deduction ?>"
    data-n11="<?= $total_deductions ?>"
    data-n12="<?= $net_salary ?>"
    data-n13="<?= html_escape($get_salary_sheet['type'] ?? '') ?>"
    data-n14="<?= html_escape($row->n13 ?? '') ?>"
    data-company-code="<?= html_escape($row->n13 ?? '') ?>"
    
    data-salary-details='<?= json_encode([
        'total_salary' => $salary_for_this_period,
        'full_time_salary' => $full_time_salary,
        'daily_salary' => $current_daily_salary,
        'minute_salary' => $current_minute_salary,
        
        'absence_days' => $total_absence_days,
        'minutes_late' => $minutes_late,
        'minutes_early' => $minutes_early,
        'single_punch_days' => $single_thing,
        'absence_deduct' => $absence_deduct,
        'late_deduct' => $late_deduct,
        'early_deduct' => $early_deduct,
        'single_punch_deduct' => $single_thing_deduct,
        'half_day_vacation_deduct' => $half_day_vacation_deduct,
        'total_current_attendance_deductions' => $attendance_total_deduct,
        
        'prev_absence_deduct' => $prev_absence_deduct,
        'prev_late_deduct' => $prev_late_deduct,
        'prev_early_deduct' => $prev_early_deduct,
        'prev_single_punch_deduct' => $prev_single_thing_deduct,
        'total_previous_attendance_deductions' => $total_previous_attendance_deductions,
        
        'unpaid_leave_days' => $unpaid_leave_days,
        'unpaid_leave_deduction' => isset($unpaid_leave_deduction) ? $unpaid_leave_deduction : ($current_daily_salary * $unpaid_leave_days),
        
        'discount_amount' => $discount_amount,
        'reparations_amount' => $reparations_amount,
        'hiring_day_difference' => $hiring_day_difference,
        'insurance_deduction' => $insurance_deduction,
        'total_deductions' => $total_deductions,
        'salary_before_insurance' => $salary_before_insurance,
        'net_salary' => $net_salary,
        
        'is_stopped' => $is_stopped,
        'is_exempt' => $is_exempt,
        'base_salary' => $row->base_salary,
        'housing_allowance' => $row->housing_allowance,
        'base_plus_house' => $base_plus_house_capped,
        'insurance_rate' => $discount_rate,
        'n4' => $row->n4,
        'other_allowances' => $row->other_allowances,
        'n7' => $row->n7,
        'is_new_employee' => $is_new_employee,
        'actual_worked_days' => $actual_worked_days,
        'join_date' => $join_date_display,
        'join_day' => $join_day ?? 0,
        'is_skipped_this_month' => $skip_this_month,
        'previous_month_comp' => $previous_month_comp,
        'prorated_gosi_base' => $prorated_gosi_base,
        'gosi_calc_note' => $gosi_calc_note,
        'half_day_vacations' => $half_day_vacations
    ]) ?>'
>
    <!-- ============ EMPLOYEE INFORMATION GROUP (12 columns) ============ -->
    <td>
        <div class="btn-group" role="group">
           <a href="javascript:void(0);" 
   onclick="openAttendancePopup('<?php echo site_url('users1/employee_daily_log/' . $row->employee_id . '/' . $id); ?>')"
   class="btn btn-sm btn-info" title="تفاصيل الحضور">
    <i class="fas fa-list"></i>
</a>
<a href="javascript:void(0);" 
   onclick="openPopup('<?php echo site_url('users1/employee_discounts_log/' . $row->employee_id . '/' . $id); ?>')"
   class="btn btn-sm btn-danger" title="تفاصيل الخصومات">
    <i class="fas fa-minus-circle"></i>
</a>

<a href="javascript:void(0);" 
   onclick="openPopup('<?php echo site_url('users1/employee_reparations_log/' . $row->employee_id . '/' . $id); ?>')"
   class="btn btn-sm btn-success" title="تفاصيل الاستحقاقات">
    <i class="fas fa-plus-circle"></i>
</a>
            <button type="button" class="btn btn-sm btn-success" 
                onclick="openReparationModal('<?php echo $row->employee_id; ?>', '<?php echo htmlspecialchars($row->subscriber_name); ?>')">
                <i class="fas fa-plus"></i>
            </button>

            <button type="button" class="btn btn-sm btn-danger" 
                onclick="openDiscountModal('<?php echo $row->employee_id; ?>', '<?php echo htmlspecialchars($row->subscriber_name); ?>')">
                <i class="fas fa-minus"></i>
            </button>

            <button type="button" class="btn btn-sm btn-warning view-salary-summary-btn" 
                data-bs-toggle="modal" data-bs-target="#salarySummaryModal"
                data-empid="<?php echo $row->employee_id; ?>"
                data-empname="<?php echo htmlspecialchars($row->subscriber_name); ?>">
                <i class="fas fa-calculator"></i>
            </button>
        </div>
    </td>
    <td><?php echo $row->employee_id; ?></td>
    <td><?php echo $row->subscriber_name; ?></td>
    <td><?php echo $row->id_number; ?></td>
    <td><?php echo $row->n3; ?></td> <!-- IBAN -->
    <td><?php echo $row->n2; ?></td> <!-- Bank Name -->
    <td><?php echo $row->nationality; ?></td> <!-- Nationality -->
    <td>
        <?php 
        if (!empty($employee_note)): 
            $badge_class = 'bg-secondary';
            if (strpos($employee_note, 'موقف') !== false) { $badge_class = 'bg-danger'; }
            elseif (strpos($employee_note, 'سيتم صرف') !== false) { $badge_class = 'bg-warning text-dark'; }
            elseif (strpos($employee_note, 'معفى') !== false) { $badge_class = 'bg-warning text-dark'; }
            elseif (strpos($employee_note, 'متبقي') !== false) { $badge_class = 'bg-success'; }
            elseif (strpos($employee_note, 'جديد') !== false) { $badge_class = 'bg-info text-dark'; }
            elseif (strpos($employee_note, 'معتمد') !== false) { $badge_class = 'bg-success'; }
            elseif (strpos($employee_note, 'قيد') !== false) { $badge_class = 'bg-warning text-dark'; }
        ?>
            <span class="badge <?php echo $badge_class; ?>"><?php echo htmlspecialchars($employee_note); ?></span>
        <?php else: ?>
            <span class="text-muted">—</span>
        <?php endif; ?>
    </td>
    <td><?php echo $row->profession; ?></td> <!-- Job Title -->
    <td><?php echo $row->n1; ?></td> <!-- Department -->
    <td><?php echo $row->company_name; ?></td> <!-- Company Name -->
    <td>
        <?php 
        if ($is_new_employee && !$skip_this_month && $previous_month_comp == 0) {
            echo '<span title="موظف جديد - الراتب الكامل (الراتب الفعلي للحساب: ' . number_format($salary_for_this_period, 2) . ')">' . number_format($full_time_salary, 2) . '</span>';
        } else {
            echo number_format($full_time_salary, 2);
        }
        ?>
    </td>
    
    <!-- ============ BASIC ALLOWANCES GROUP (5 columns) ============ -->
    <td><?php echo number_format((float)($row->base_salary ?? 0), 2); ?></td>
    <td><?php echo number_format((float)($row->housing_allowance ?? 0), 2); ?></td>
    <td><?php echo number_format((float)($row->n4 ?? 0), 2); ?></td>
    <td><?php echo number_format((float)($row->other_allowances ?? 0), 2); ?></td>
    <td><?php echo number_format((float)($row->n7 ?? 0), 2); ?></td>
     <td><strong><?php echo $pure_absence_days; ?></strong></td>
    <td><strong><?php echo $pure_unpaid_days; ?></strong></td>
    <td><strong><?php echo $minutes_late; ?></strong></td>
    <td><strong><?php echo $minutes_early; ?></strong></td>
    <td><strong><?php echo $single_thing; ?></strong></td>
    
    <!-- ============ DYNAMIC ADDITIONAL ALLOWANCES GROUP ============ -->
    <?php 
    $emp_additions = $dynamic_additions_data[$emp_id] ?? []; 
    if(!empty($dynamic_additions_headers)):
        foreach($dynamic_additions_headers as $header): 
            $val = $emp_additions[$header] ?? 0; 
    ?>
        <td><span class="text-success"><?php echo number_format($val, 2); ?></span></td>
    <?php 
        endforeach; 
    endif;
    ?>
    
    <!-- ============ ATTENDANCE & TIME DIFFERENCES GROUP (9 columns) ============ -->
    <td><strong class="text-success"><?php echo number_format($previous_month_comp, 2); ?></strong></td>
   
    <td>
        <strong class="text-success">
            <?php echo ($hiring_day_difference > 0) ? number_format($hiring_day_difference, 2) : '0.00'; ?>
        </strong>
    </td>
    <td>
        <strong class="text-danger" dir="ltr">
            <?php echo ($hiring_day_difference < 0) ? number_format($hiring_day_difference, 2) : '0.00'; ?>
        </strong>
    </td>
    
    <!-- ============ BASIC DEDUCTIONS GROUP (5 columns) ============ -->
    <td><strong class="text-danger"><?php echo number_format($absence_deduct, 2); ?></strong></td>
    <td><strong class="text-danger"><?php echo number_format($unpaid_leave_deduction, 2); ?></strong></td>
    <td><strong class="text-danger"><?php echo number_format($single_thing_deduct, 2); ?></strong></td>
    <td><strong class="text-danger"><?php echo number_format($late_deduct, 2); ?></strong></td>
    <td><strong class="text-danger"><?php echo number_format($early_deduct, 2); ?></strong></td>
    
    <!-- ============ DYNAMIC DEDUCTIONS GROUP ============ -->
    <?php 
    $emp_deductions = $dynamic_deductions_data[$emp_id] ?? []; 
    if(!empty($dynamic_deductions_headers)):
        foreach($dynamic_deductions_headers as $header): 
            $val = $emp_deductions[$header] ?? 0; 
    ?>
        <td><span class="text-danger"><?php echo number_format($val, 2); ?></span></td>
    <?php 
        endforeach;
    endif;
    ?>
    
    <!-- ============ FINAL SALARY CALCULATION GROUP (4 columns) ============ -->
    <td><strong class="text-danger"><?php echo number_format($attendance_total_deduct + $total_previous_attendance_deductions, 2); ?></strong></td>
    <td><strong><?php echo number_format($salary_before_insurance, 2); ?></strong></td>
    <td><strong class="text-danger"><?php echo number_format($insurance_deduction, 2); ?></strong></td>
    <td>
    
        <strong>
            <?php
            // FIX: If Net Salary is calculated (Partial Stop), show it. 
            // Only show 0.00 if explicitly 0 or Skipped.
            if ($net_salary > 0) {
                echo number_format($net_salary, 2);
            } else {
                echo '<span class="badge bg-danger">0.00</span>';
            }
            ?>
        </strong>
    
    </td>
</tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Company Selection Modal -->
<div class="modal fade" id="companySelectionModal" tabindex="-1" aria-labelledby="companySelectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="companySelectionModalLabel">
                    <i class="fas fa-building me-2"></i>اختر الشركات للمعالجة
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    يرجى اختيار الشركات التي تريد معالجة رواتب موظفيها. سيتم معالجة البيانات للشركات المحددة فقط.
                </div>
                
                <div id="companySelectionError" class="alert alert-danger d-none">
                    <i class="fas fa-exclamation-triangle me-2"></i>يجب اختيار شركة واحدة على الأقل للمعالجة.
                </div>
                
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="selectAllCompanies">
                    <label class="form-check-label fw-bold" for="selectAllCompanies">
                        اختيار/إلغاء جميع الشركات
                    </label>
                </div>
                
                <div class="row" id="companiesList">
    <?php
    $unique_companies_modal = [];
    foreach ($employees as $row) {
        if (!empty($row->company_name)) {
            // FIX: Normalize the name (Remove extra spaces)
            $raw_name = $row->company_name;
            $normalized_name = trim(preg_replace('/\s+/', ' ', $raw_name));
            
            // Only show if we haven't shown this name before
            if (!in_array($normalized_name, $unique_companies_modal)) {
                $unique_companies_modal[] = $normalized_name;
                $company_code = $row->n13 ?? '';
                
                // Use the normalized name for display
                echo '
                <div class="col-md-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input company-checkbox" type="checkbox" value="' . htmlspecialchars($company_code) . '" id="company_' . htmlspecialchars($company_code) . '" data-company-name="' . htmlspecialchars($normalized_name) . '">
                        <label class="form-check-label" for="company_' . htmlspecialchars($company_code) . '">
                            ' . htmlspecialchars($normalized_name) . '
                        </label>
                    </div>
                </div>';
            }
        }
    }
    ?>
</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>إلغاء
                </button>
                <button type="button" class="btn btn-success" id="confirmCompanyProcessBtn">
                    <i class="fas fa-check-circle me-2"></i>تأكيد والمعالجة
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Salary Summary Modal -->
<div class="modal fade" id="salarySummaryModal" tabindex="-1" aria-labelledby="salarySummaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="salarySummaryModalLabel">
                    <i class="fas fa-calculator me-2"></i>تفاصيل حساب الراتب - <span id="salaryEmployeeName"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="salary-summary-content">
                    <!-- Salary breakdown will be populated here by JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>إغلاق
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="reportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">تفاصيل التقرير</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="height: 80vh;">
                <iframe id="reportFrame" src="" style="width:100%; height:100%; border:none;"></iframe>
            </div>
        </div>
    </div>
</div>
<!-- Daily Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center" id="detailsModalLabel">
                    <i class="fas fa-list me-2"></i>
                    <span>تفاصيل الحضور والانصراف - </span>
                    <span id="modalEmployeeName" class="fw-bold ms-1"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-0"> 
                <div id="modal-loader" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">جاري التحميل...</span>
                    </div>
                    <p class="mt-3 text-muted fw-bold">جاري تحميل البيانات...</p>
                </div>

                <div id="modal-error-area" class="alert alert-danger d-none m-3 text-center" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <span id="modal-error-message">حدث خطأ أثناء تحميل البيانات.</span>
                </div>

                <div id="modal-content-area" class="d-none">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered mb-0 align-middle text-center">
                            <thead class="table-light sticky-top" style="z-index: 1;">
                                <tr>
                                    <th scope="col" style="min-width: 100px;">التاريخ</th>
                                    <th scope="col" style="min-width: 80px;">اليوم</th>
                                    <th scope="col" style="min-width: 100px;">وقت الدخول</th>
                                    <th scope="col" style="min-width: 100px;">وقت الخروج</th>
                                    <th scope="col" style="min-width: 100px;">الحالة</th>
                                    <th scope="col" style="min-width: 150px;">المخالفات</th>
                                    <th scope="col" style="min-width: 100px;" class="bg-danger text-white">قيمة الخصم</th>
                                </tr>
                            </thead>
                            <tbody id="details-table-body">
                                </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>إغلاق
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="quickDiscountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-minus-circle me-2"></i>إضافة خصم جديد</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="quickDiscountForm">
                    <input type="hidden" name="<?php echo $csrf_token_name; ?>" value="<?php echo $csrf_hash; ?>">
                    <input type="hidden" name="sheet_id" value="<?php echo $id; ?>"> <input type="hidden" name="emp_id" id="disc_emp_id">
                    <input type="hidden" name="emp_name" id="disc_emp_name">
                    <input type="hidden" name="is_recurring" value="0"> 

                    <div class="mb-3">
                        <label class="form-label fw-bold">الموظف:</label>
                        <input type="text" class="form-control" id="disc_emp_display" readonly disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">سبب الخصم:</label>
                        <input type="text" class="form-control" name="type" required placeholder="مثال: سلفة، جزاء...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">المبلغ:</label>
                        <input type="number" step="0.01" class="form-control" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تاريخ الخصم:</label>
                        <input type="date" class="form-control" name="discount_date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملاحظات:</label>
                        <textarea class="form-control" name="notes" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" form="quickDiscountForm" class="btn btn-danger">حفظ الخصم</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="attendanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-alt me-2"></i> تفاصيل سجل الحضور
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="height: 80vh;">
                <iframe id="attendanceFrame" src="" style="width:100%; height:100%; border:none;"></iframe>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-primary" onclick="printIframe()">
                    <i class="fas fa-print me-1"></i> طباعة التقرير
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="quickReparationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>إضافة تعويض/مكافأة</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="quickReparationForm">
                    <input type="hidden" name="<?php echo $csrf_token_name; ?>" value="<?php echo $csrf_hash; ?>">
                    <input type="hidden" name="sheet_id" value="<?php echo $id; ?>">
                    <input type="hidden" name="emp_id" id="rep_emp_id">
                    <input type="hidden" name="emp_name" id="rep_emp_name">

                    <div class="mb-3">
                        <label class="form-label fw-bold">الموظف:</label>
                        <input type="text" class="form-control" id="rep_emp_display" readonly disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">النوع (السبب):</label>
                        <input type="text" class="form-control" name="type" required placeholder="مثال: مكافأة، تعويض...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">المبلغ:</label>
                        <input type="number" step="0.01" class="form-control" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تاريخ التعويض:</label>
                        <input type="date" class="form-control" name="reparation_date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملاحظات:</label>
                        <textarea class="form-control" name="notes" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" form="quickReparationForm" class="btn btn-success">حفظ التعويض</button>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.bootstrap5.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.colVis.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdn.datatables.net/colreorder/1.7.0/js/dataTables.colReorder.min.js"></script>

<script>
    // ==================================================================
    // START: UTILITY FUNCTIONS (POPUPS, LOADERS)
    // ==================================================================
    function openExemptionPopup(empId, sheetId) {
        var url = "<?php echo site_url('users1/exemption'); ?>/" + empId + "/" + sheetId;
        var width  = 800;
        var height = 600;
        var left = (screen.width/2) - (width/2);
        var top  = (screen.height/2) - (height/2);
        window.open(url, 'ExemptionPopup', 'width='+width+',height='+height+',top='+top+',left='+left+',resizable=yes,scrollbars=yes,status=no');
    }

    window.addEventListener('load', function() {
        const loadingScreen = document.getElementById('loading-screen');
        const mainContentWrapper = document.querySelector('.main-content-wrapper');
        loadingScreen.style.opacity = '0';
        setTimeout(() => {
            loadingScreen.style.display = 'none';
            document.body.style.overflow = 'auto';
            mainContentWrapper.style.visibility = 'visible';
            mainContentWrapper.style.opacity = '1';
        }, 500);
    });

    function openDailyLogPopup(empId, sheetId) {
        var url = "<?php echo site_url('users1/employee_daily_log'); ?>/" + empId + "/" + sheetId;
        var width  = 980;
        var height = 700;
        var left = (screen.width / 2) - (width / 2);
        var top  = (screen.height / 2) - (height / 2);
        window.open(url, 'DailyLogPopup', 'width=' + width + ',height=' + height + ',top=' + top + ',left=' + left + ',resizable=yes,scrollbars=yes,status=no');
    }

    function formatCurrency(amount, decimals = 2) {
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        }).format(amount);
    }

    function formatNumber(number) {
        return new Intl.NumberFormat('en-US').format(number);
    }
    // ==================================================================
    // END: UTILITY FUNCTIONS
    // ==================================================================


    $(document).ready(function() {
        // ==================================================================
        // START: DATATABLE INITIALIZATION AND FILTERS
        // ==================================================================
        var table = $('.dataTables-example').DataTable({
            responsive: true,
            pageLength: 10,
            colReorder: true, 
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "الكل"]],
            layout: {
                topStart: {
                    buttons: [
                        { extend: 'copy', text: '<i class="fa fa-copy"></i> نسخ' },
                        { extend: 'excel', text: '<i class="fa fa-file-excel"></i> إكسل' },
                        { extend: 'pdf', text: '<i class="fa fa-file-pdf"></i> PDF' },
                        { extend: 'print', text: '<i class="fa fa-print"></i> طباعة' },
                        { extend: 'colvis', text: '<i class="fa fa-eye"></i> إظهار/إخفاء الأعمدة' },
                        {
                    extend: 'excelHtml5',
                    text: 'ScreenMatchExcel',
                    className: 'd-none btn-screen-export', // Hidden class
                    title: 'مسير الرواتب - ' + new Date().toLocaleDateString('en-GB'),
                    charset: 'UTF-8',
                    bom: true, // Fix Arabic characters
                    exportOptions: {
                        columns: ':visible', // Match screen columns
                        search: 'applied',   // Match screen filters
                        order: 'current',    // Match screen sort
                        format: {
                            body: function(data, row, column, node) {
                                // Strip HTML tags to get clean text
                                var tmp = document.createElement("div");
                                tmp.innerHTML = data;
                                return tmp.textContent || tmp.innerText || "";
                            }
                        }
                    }
                }
                    ]
                }
            },
            language: { "url": "https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json" },
            columnDefs: [
                { targets: 0, orderable: false, searchable: false }, // Actions column
                { targets: '_all', orderable: true, searchable: true }
            ],
            
            initComplete: function () {
    var api = this.api();

    // Loop through every column in the table
    api.columns().every(function () {
        var column = this;
        var colIdx = column.index();

        // 1. Find the input inside the specific <th> of the filter row
        // We select the filter header row, find the TH at the current index, then find the input inside it
        var input = $('.filter-header th').eq(colIdx).find('input');

        // 2. If an input exists for this column, bind the search event
        if (input.length > 0) {
            // Remove old events to prevent duplicates
            input.off('keyup change clear');
            
            input.on('keyup change clear', function () {
                if (column.search() !== this.value) {
                    column.search(this.value).draw();
                    updateActiveFiltersCount(); // Update the "Active Filters" badge
                }
            });
        }
    });

    // SET DEFAULT COMPANY FILTER
    setTimeout(function() {
        $('#companyFilter').val('مكتب الدكتور').trigger('change');
    }, 100);
    // ==================================================================
// START: MODAL FUNCTIONS (GLOBAL SCOPE FIX)
// ==================================================================

// 1. Define Open Functions Globally
window.openDiscountModal = function(empId, empName) {
    // Set values
    $('#disc_emp_id').val(empId);
    $('#disc_emp_name').val(empName);
    $('#disc_emp_display').val(empName);
    
    // Reset form
    $('#quickDiscountForm')[0].reset(); 
    
    // Restore hidden values after reset
    $('#disc_emp_id').val(empId);
    $('#disc_emp_name').val(empName);
    $('input[name="discount_date"]').val(new Date().toISOString().split('T')[0]);
    
    // Show Modal
    var myModal = new bootstrap.Modal(document.getElementById('quickDiscountModal'));
    myModal.show();
};

window.openReparationModal = function(empId, empName) {
    // Set values
    $('#rep_emp_id').val(empId);
    $('#rep_emp_name').val(empName);
    $('#rep_emp_display').val(empName);
    
    // Reset form
    $('#quickReparationForm')[0].reset();
    
    // Restore hidden values
    $('#rep_emp_id').val(empId);
    $('#rep_emp_name').val(empName);
    $('input[name="reparation_date"]').val(new Date().toISOString().split('T')[0]);

    // Show Modal
    var myModal = new bootstrap.Modal(document.getElementById('quickReparationModal'));
    myModal.show();
};

// 2. Handle Discount Submission
$(document).on('submit', '#quickDiscountForm', function(e) {
    e.preventDefault();
    var formData = $(this).serialize();
    
    $.ajax({
        url: "<?php echo site_url('users1/save_discount'); ?>",
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            // Update CSRF token on page
            if(response.csrf_hash) {
                $('input[name="<?php echo $csrf_token_name; ?>"]').val(response.csrf_hash);
            }

            if (response.status === 'success') {
                // Close modal manually
                var modalEl = document.getElementById('quickDiscountModal');
                var modal = bootstrap.Modal.getInstance(modalEl);
                modal.hide();

                Swal.fire({
                    icon: 'success',
                    title: 'تم الحفظ',
                    text: 'تم إضافة الخصم بنجاح. سيتم تحديث الصفحة.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload(); 
                });
            } else {
                Swal.fire('خطأ', response.message, 'error');
            }
        },
        error: function() {
            Swal.fire('خطأ', 'فشل الاتصال بالخادم', 'error');
        }
    });
});

// 3. Handle Reparation Submission
$(document).on('submit', '#quickReparationForm', function(e) {
    e.preventDefault();
    var formData = $(this).serialize();
    
    $.ajax({
        url: "<?php echo site_url('users1/save_reparation'); ?>",
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            // Update CSRF
            if(response.csrf_hash) {
                $('input[name="<?php echo $csrf_token_name; ?>"]').val(response.csrf_hash);
            }

            if (response.status === 'success') {
                // Close modal manually
                var modalEl = document.getElementById('quickReparationModal');
                var modal = bootstrap.Modal.getInstance(modalEl);
                modal.hide();

                Swal.fire({
                    icon: 'success',
                    title: 'تم الحفظ',
                    text: 'تم إضافة التعويض بنجاح. سيتم تحديث الصفحة.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('خطأ', response.message, 'error');
            }
        },
        error: function() {
            Swal.fire('خطأ', 'فشل الاتصال بالخادم', 'error');
        }
    });
});
  // ==================================================================
    // START: EXCEL EXPORT (SCREEN MATCH) LOGIC - FIXED
    // ==================================================================
    $('#btnExportExcelJS').on('click', function() {
        var table = $('.dataTables-example').DataTable();
        
        // Trigger the hidden button we defined in Step 1
        table.button('.btn-screen-export').trigger();
    });
    // ==================================================================
    // END: EXCEL EXPORT LOGIC
    // ==================================================================
}
        });
// Bank Export Button Logic
$('#companyFilter').on('change', function() {
    updateBankExportLink();
});

// Initial call
updateBankExportLink();

function updateBankExportLink() {
    var selectedCompany = $('#companyFilter').val();
    var sheetId = "<?php echo $id; ?>"; // Get Sheet ID from PHP
    var baseUrl = "<?php echo site_url('users1/export_bank_payroll_csv/'); ?>" + sheetId;
    
    if (selectedCompany) {
        $('#btnExportBankExcel').attr('href', baseUrl + '?company=' + encodeURIComponent(selectedCompany));
    } else {
        $('#btnExportBankExcel').attr('href', baseUrl);
    }
}
        // Quick Filters Functionality
      // Quick Filters Functionality
$('#companyFilter').on('change', function() {
    var selectedCompany = $(this).val();
    var baseExportUrl = "<?php echo site_url('users1/export_payroll_sheet_csv/' . $id); ?>";
    
    if (selectedCompany) {
        // Append the company parameter to the URL
        $('#btnExportExcel').attr('href', baseExportUrl + '?company=' + encodeURIComponent(selectedCompany));
    } else {
        // Reset to default URL if no company selected
        $('#btnExportExcel').attr('href', baseExportUrl);
    }
    
    // FIXED: Company column is now at index 10 (0-based)
    table.columns(10).search(selectedCompany).draw();
    updateActiveFiltersCount();
});

$('#professionFilter').on('change', function() {
    // Job Title is now at index 8 (0-based)
    table.columns(8).search($(this).val()).draw();
    updateActiveFiltersCount();
});

$('#departmentFilter').on('change', function() {
    // Department is now at index 9 (0-based)
    table.columns(9).search($(this).val()).draw();
    updateActiveFiltersCount();
});

$('#nationalityFilter').on('change', function() {
    // Nationality is now at index 6 (0-based)
    table.columns(6).search($(this).val()).draw();
    updateActiveFiltersCount();
});

// Reset All Filters
$('#resetAllFilters').on('click', function() {
    $('#companyFilter, #professionFilter, #departmentFilter, #nationalityFilter').val('');
    $('.column-search').val('');
    table.columns().search('').draw();
    updateActiveFiltersCount();
    
    // RE-APPLY DEFAULT
    setTimeout(function() {
        $('#companyFilter').val('مكتب الدكتور').trigger('change');
    }, 100);
});

// Update totals when filters change
$('#companyFilter, #professionFilter, #departmentFilter, #nationalityFilter').on('change', function() {
    setTimeout(calculateAndDisplayTotals, 100);
});
// ==================================================================
// START: EXCLUSION & VARIANCE REPORT LOGIC (UPDATED)
// ==================================================================
$('#check-exclusions-btn').on('click', function() {
    var table = $('.dataTables-example').DataTable();
    var visibleRows = table.rows({ search: 'applied' }).nodes();
    var reportList = [];

    // Loop through all visible rows
    $(visibleRows).each(function() {
        var row = $(this);
        var empName = row.attr('data-n2');
        var empId = row.attr('data-n1');
        var empNote = row.attr('data-note') || ''; // Get the text note (e.g. Resignation status)
        
        // Get the calculated details JSON
        var details = JSON.parse(row.attr('data-salary-details'));
        
        var reasons = [];
        var isFlagged = false;

        // 1. Salary Stopped
        if (details.is_stopped) {
            reasons.push('<span class="badge bg-danger">الراتب موقف إدارياً</span>');
            isFlagged = true;
        } 
        
        // 2. New Employee - Skipped (Joined too late)
        if (details.is_skipped_this_month) {
            reasons.push('<span class="badge bg-warning text-dark">موظف جديد (لم يبدأ الاستحقاق)</span>');
            isFlagged = true;
        }
        
        // 3. New Employee - Partial Pay (Mid-month join)
        if (details.is_new_employee && !details.is_skipped_this_month) {
             reasons.push('<span class="badge bg-info text-dark">موظف جديد - راتب جزئي (تاريخ المباشرة: ' + details.join_date + ')</span>');
             isFlagged = true;
        }

        // 4. Hiring Difference / Salary Adjustment
        // (Checking if n20 column has a value)
        if (parseFloat(details.hiring_day_difference) !== 0 && !details.is_new_employee) {
             reasons.push('<span class="badge bg-secondary">تعديل راتب / فروقات</span>');
             isFlagged = true;
        }

        // 5. Partial Salary (Resigned or Unpaid Leave impacting base)
        // Logic: If Calculated Salary < Contract Salary AND not a new employee
        if (parseFloat(details.total_salary) < parseFloat(details.full_time_salary) && !details.is_new_employee) {
             reasons.push('<span class="badge bg-warning text-dark">راتب غير كامل (استقالة/أخرى)</span>');
             isFlagged = true;
        }
        
        // 6. Explicit Resignation Note
        if (empNote.includes('استقالة') || empNote.includes('resignation')) {
             // Only add badge if not already covered by #5
             if (!reasons.some(r => r.includes('راتب غير كامل'))) {
                 reasons.push('<span class="badge bg-dark">يوجد طلب استقالة</span>');
             }
             isFlagged = true;
        }

        // 7. Net Salary is Zero (and not caught by above)
        if (parseFloat(details.net_salary) <= 0 && !isFlagged) {
            reasons.push('<span class="badge bg-dark">صافي الراتب صفر</span>');
            isFlagged = true;
        }

        // Add to list if any condition met
        if (isFlagged) {
            reportList.push({
                id: empId,
                name: empName,
                reason: reasons.join(' ')
            });
        }
    });

    // Build HTML for the Popup
    var htmlContent = '';
    if (reportList.length > 0) {
        htmlContent += '<div class="alert alert-secondary">عدد الحالات: <b>' + reportList.length + '</b></div>';
        htmlContent += '<div class="table-responsive" style="max-height: 400px; overflow-y: auto;">';
        htmlContent += '<table class="table table-bordered table-sm text-start" dir="rtl">';
        htmlContent += '<thead class="table-light"><tr><th width="15%">الرقم</th><th width="35%">الموظف</th><th>الحالة / الملاحظة</th></tr></thead>';
        htmlContent += '<tbody>';
        
        $.each(reportList, function(index, item) {
            htmlContent += `<tr>
                <td>${item.id}</td>
                <td>${item.name}</td>
                <td>${item.reason}</td>
            </tr>`;
        });
        
        htmlContent += '</tbody></table></div>';
        
        Swal.fire({
            title: 'تقرير الاستثناءات والفروقات',
            html: htmlContent,
            icon: 'info',
            width: '800px',
            confirmButtonText: 'إغلاق',
            confirmButtonColor: '#001f3f'
        });
    } else {
        Swal.fire({
            title: 'لا توجد استثناءات',
            text: 'جميع الموظفين في القائمة الحالية يتقاضون رواتبهم الكاملة الاعتيادية.',
            icon: 'success',
            confirmButtonText: 'موافق',
            confirmButtonColor: '#198754'
        });
    }
});
// ==================================================================
// END: EXCLUSION & VARIANCE REPORT LOGIC
// ==================================================================

// ==================================================================
// START: CALCULATE AND DISPLAY PAYROLL TOTALS
// ==================================================================
function calculateAndDisplayTotals() {
    var table = $('.dataTables-example').DataTable();
    var visibleRows = table.rows({ search: 'applied' }).nodes();
    
    var totals = {
        total_salary: 0,
        base_salary: 0,
        housing_allowance: 0,
        transport_allowance: 0,
        other_allowances: 0,
        communication_allowance: 0,
        hiring_diff_pos: 0,
        hiring_diff_neg: 0,
        previous_month_comp: 0,
        reparations: 0,
        absence_deduct: 0,
        unpaid_leave_deduct: 0,
        single_punch_deduct: 0,
        late_early_deduct: 0,
        penalties_deduct: 0,
        total_attendance_deduct: 0,
        salary_before_insurance: 0,
        insurance_deduct: 0,
        net_salary: 0,
        employee_count: 0
    };
    
    // Calculate totals from visible rows
    $(visibleRows).each(function() {
        var row = $(this);
        var details = JSON.parse(row.attr('data-salary-details'));
        
        // Skip stopped employees
        if (parseFloat(details.net_salary) > 0 || (!details.is_stopped && !details.is_skipped_this_month)) {
            totals.employee_count++;
            
            totals.total_salary += parseFloat(details.full_time_salary) || 0;
            totals.base_salary += parseFloat(details.base_salary) || 0;
            totals.housing_allowance += parseFloat(details.housing_allowance) || 0;
            totals.transport_allowance += parseFloat(details.n4) || 0;
            totals.other_allowances += parseFloat(details.other_allowances) || 0;
            totals.communication_allowance += parseFloat(details.n7) || 0;
            var hDiff = parseFloat(details.hiring_day_difference) || 0;
            if(hDiff > 0) totals.hiring_diff_pos += hDiff;
            if(hDiff < 0) totals.hiring_diff_neg += hDiff;
            totals.previous_month_comp += parseFloat(details.previous_month_comp) || 0;
            totals.reparations += parseFloat(details.reparations_amount) || 0;
            totals.absence_deduct += parseFloat(details.absence_deduct) || 0;
            totals.unpaid_leave_deduct += parseFloat(details.unpaid_leave_deduction) || 0;
            totals.single_punch_deduct += parseFloat(details.single_punch_deduct) || 0;
            
            var lateEarly = (parseFloat(details.late_deduct) || 0) + (parseFloat(details.early_deduct) || 0);
            totals.late_early_deduct += lateEarly;
            
            totals.penalties_deduct += parseFloat(details.discount_amount) || 0;
            
            // Total attendance deductions
            var attendanceTotal = 
                (parseFloat(details.absence_deduct) || 0) +
                (parseFloat(details.unpaid_leave_deduction) || 0) +
                (parseFloat(details.single_punch_deduct) || 0) +
                (parseFloat(details.late_deduct) || 0) +
                (parseFloat(details.early_deduct) || 0) +
                (parseFloat(details.half_day_vacation_deduct) || 0);
            
            totals.total_attendance_deduct += attendanceTotal;
            
            totals.salary_before_insurance += parseFloat(details.salary_before_insurance) || 0;
            totals.insurance_deduct += parseFloat(details.insurance_deduction) || 0;
            totals.net_salary += parseFloat(details.net_salary) || 0;
        }
    });
    
    // Create HTML for totals display
    var totalsHtml = `
        <div class="row">
            <div class="col-md-2 col-sm-4 mb-3">
                <div class="summary-card" id="total-employees-count">
                    <h6>عدد الموظفين</h6>
                    <div class="amount neutral">${totals.employee_count}</div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 mb-3">
                <div class="summary-card">
                    <h6>إجمالي الأجر</h6>
                    <div class="amount positive">${formatCurrency(totals.total_salary)}</div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 mb-3">
                <div class="summary-card">
                    <h6>الراتب الأساسي</h6>
                    <div class="amount positive">${formatCurrency(totals.base_salary)}</div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 mb-3">
                <div class="summary-card">
                    <h6>بدل السكن</h6>
                    <div class="amount positive">${formatCurrency(totals.housing_allowance)}</div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 mb-3">
                <div class="summary-card">
                    <h6>بدل النقل</h6>
                    <div class="amount positive">${formatCurrency(totals.transport_allowance)}</div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 mb-3">
                <div class="summary-card">
                    <h6>بدلات أخرى</h6>
                    <div class="amount positive">${formatCurrency(totals.other_allowances)}</div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-2 col-sm-4 mb-3">
                <div class="summary-card">
                    <h6>بدل الاتصال</h6>
                    <div class="amount positive">${formatCurrency(totals.communication_allowance)}</div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 mb-3">
                <div class="summary-card">
                    <h6>فروقات التوظيف</h6>
                    <div class="amount ${totals.hiring_difference >= 0 ? 'positive' : 'negative'}">
                        ${formatCurrency(totals.hiring_difference)}
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 mb-3">
                <div class="summary-card">
                    <h6>راتب الشهر السابق</h6>
                    <div class="amount positive">${formatCurrency(totals.previous_month_comp)}</div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 mb-3">
                <div class="summary-card">
                    <h6>التعويضات</h6>
                    <div class="amount positive">${formatCurrency(totals.reparations)}</div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 mb-3">
                <div class="summary-card">
                    <h6>خصم الغياب</h6>
                    <div class="amount negative">-${formatCurrency(totals.absence_deduct)}</div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 mb-3">
                <div class="summary-card">
                    <h6>خصم إجازة غير مدفوعة</h6>
                    <div class="amount negative">-${formatCurrency(totals.unpaid_leave_deduct)}</div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-2 col-sm-4 mb-3">
                <div class="summary-card">
                    <h6>خصم بصمة منفردة</h6>
                    <div class="amount negative">-${formatCurrency(totals.single_punch_deduct)}</div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 mb-3">
                <div class="summary-card">
                    <h6>خصم التأخير والخروج المبكر</h6>
                    <div class="amount negative">-${formatCurrency(totals.late_early_deduct)}</div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 mb-3">
                <div class="summary-card">
                    <h6>خصم الجزاءات</h6>
                    <div class="amount negative">-${formatCurrency(totals.penalties_deduct)}</div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 mb-3">
                <div class="summary-card">
                    <h6>إجمالي خصم الحضور</h6>
                    <div class="amount negative">-${formatCurrency(totals.total_attendance_deduct)}</div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 mb-3">
                <div class="summary-card">
                    <h6>الراتب قبل التأمينات</h6>
                    <div class="amount neutral">${formatCurrency(totals.salary_before_insurance)}</div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 mb-3">
                <div class="summary-card">
                    <h6>خصم التأمينات</h6>
                    <div class="amount negative">-${formatCurrency(totals.insurance_deduct)}</div>
                </div>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-4 offset-md-4">
                <div class="summary-card" id="total-net-salary">
                    <h6>صافي الراتب الإجمالي</h6>
                    <div class="amount" style="font-size: 1.5rem;">${formatCurrency(totals.net_salary)}</div>
                    <small>صافي ما سيتم صرفه</small>
                </div>
            </div>
        </div>
        
        <div class="row mt-2">
            <div class="col-12">
                <div class="alert alert-info p-2">
                    <small>
                        <i class="fas fa-info-circle me-1"></i>
                        إجمالي الإضافات: ${formatCurrency(totals.previous_month_comp + totals.reparations)} | 
                        إجمالي الخصومات: ${formatCurrency(totals.total_attendance_deduct + totals.penalties_deduct + totals.insurance_deduct)} | 
                        عدد الموظفين النشطين: ${totals.employee_count}
                    </small>
                </div>
            </div>
        </div>
    `;
    
    $('#payroll-totals').html(totalsHtml);
    // === PASTE THE NEW CODE HERE ===
    // INJECT TOTALS INTO HEADER ROW (For Excel)
    $('#h_total_salary').text(formatCurrency(totals.total_salary));
    $('#h_base').text(formatCurrency(totals.base_salary));
    $('#h_house').text(formatCurrency(totals.housing_allowance));
    $('#h_trans').text(formatCurrency(totals.transport_allowance));
    $('#h_other').text(formatCurrency(totals.other_allowances));
    $('#h_comm').text(formatCurrency(totals.communication_allowance));

    $('#h_abs_days').text(formatNumber(totals.employee_count > 0 ? totals.absence_deduct / (totals.total_salary/30/totals.employee_count) : 0)); 
    $('#h_unpaid_days').text(''); 

    $('#h_diff_pos').text(formatCurrency(totals.hiring_diff_pos));
    $('#h_diff_neg').text(formatCurrency(totals.hiring_diff_neg));

    $('#h_prev_month').text(formatCurrency(totals.previous_month_comp));
    $('#h_ded_abs').text(formatCurrency(totals.absence_deduct));
    $('#h_ded_unpaid').text(formatCurrency(totals.unpaid_leave_deduct));
    $('#h_ded_single').text(formatCurrency(totals.single_punch_deduct));
    $('#h_ded_late').text(formatCurrency(totals.late_early_deduct)); 
    $('#h_ded_total_att').text(formatCurrency(totals.total_attendance_deduct));
    $('#h_salary_before').text(formatCurrency(totals.salary_before_insurance));
    $('#h_ded_gosi').text(formatCurrency(totals.insurance_deduct));
    $('#h_net_salary').text(formatCurrency(totals.net_salary));
    // === END PASTE ===
}

// Initial calculation
calculateAndDisplayTotals();

// Update totals when table changes (filtering, searching, etc.)
table.on('draw.dt', function() {
    calculateAndDisplayTotals();
});

// Update totals when filters change
$('#companyFilter, #professionFilter, #departmentFilter, #nationalityFilter').on('change', function() {
    setTimeout(calculateAndDisplayTotals, 100);
});

// Update totals when column search changes
$('.column-search').on('keyup change', function() {
    setTimeout(calculateAndDisplayTotals, 200);
});

// Update totals when reset filters
$('#resetAllFilters').on('click', function() {
    setTimeout(calculateAndDisplayTotals, 300);
});
// ==================================================================
// END: CALCULATE AND DISPLAY PAYROLL TOTALS
// ==================================================================
        function updateActiveFiltersCount() {
            var activeFilters = 0;
            if ($('#companyFilter').val()) activeFilters++;
            if ($('#professionFilter').val()) activeFilters++;
            if ($('#departmentFilter').val()) activeFilters++;
            if ($('#nationalityFilter').val()) activeFilters++;
            $('.column-search').each(function() { if ($(this).val()) activeFilters++; });
            $('#activeFiltersCount').text(activeFilters + ' فلتر نشط').toggle(activeFilters > 0);
        }

        updateActiveFiltersCount();
        // ==================================================================
        // END: DATATABLE INITIALIZATION AND FILTERS
        // ==================================================================


        // ==================================================================
        // START: MODAL JAVASCRIPT
        // ==================================================================

        // --- Salary Summary Modal ---
        $('.dataTables-example tbody').on('click', '.view-salary-summary-btn', function() {
            var empId = $(this).data('empid');
            var empName = $(this).data('empname');
            var row = $(this).closest('tr');
            var salaryDetails = JSON.parse(row.attr('data-salary-details'));
            
            $('#salaryEmployeeName').text(empName);
            populateSalarySummary(salaryDetails, empName, empId);
        });

        // --- Daily Details Modal ---
        $('.dataTables-example tbody').on('click', '.view-details-link', function(e) {
            e.preventDefault(); 
            
            var empId = $(this).closest('tr').data('n1');
            var empName = $(this).closest('tr').data('n2');
            var sheetId = "<?php echo $id; ?>";
            var modal = $('#detailsModal');

            modal.find('#modalEmployeeName').text(empName);
            modal.find('#details-table-body').empty();
            modal.find('#modal-loader').removeClass('d-none');
            modal.find('#modal-content-area').addClass('d-none');
            modal.find('#modal-error-area').addClass('d-none');
            
            modal.modal('show');

            $.ajax({
                url: "<?php echo site_url('users1/get_employee_violation_details'); ?>",
                type: 'POST',
                data: {
                    emp_id: empId,
                    sheet_id: sheetId,
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
                },
                dataType: 'json',
                success: function(response) {
                    modal.find('#modal-loader').addClass('d-none');
                    if (response.status === 'success' && response.data) {
                        populateDetailsTable(response.data);
                        modal.find('#modal-content-area').removeClass('d-none');
                    } else {
                        modal.find('#modal-error-area').text(response.message || 'فشل في جلب البيانات.').removeClass('d-none');
                    }
                },
                error: function(xhr, status, error) {
                    modal.find('#modal-loader').addClass('d-none');
                    modal.find('#modal-error-area').text('حدث خطأ في الاتصال بالخادم: ' + error).removeClass('d-none');
                }
            });
        });

        function populateDetailsTable(data) {
    var tableBody = $('#details-table-body');
    tableBody.empty(); // Clear old data first

    if (data.length === 0) {
        tableBody.append('<tr><td colspan="7" class="text-center p-3 text-muted">لا توجد سجلات حضور لهذه الفترة.</td></tr>');
        return;
    }

    var totalDeduction = 0;

    $.each(data, function(index, row) {
        // Status Badge Logic
        let statusBadge = '';
        if (row.status === 'غياب') { statusBadge = '<span class="badge bg-danger">غياب</span>'; }
        else if (row.status === 'حاضر') { statusBadge = '<span class="badge bg-success">حاضر</span>'; }
        else if (row.status === 'إجازة') { statusBadge = '<span class="badge bg-info text-dark">إجازة</span>'; }
        else if (row.status === 'عطلة نهاية الأسبوع') { statusBadge = '<span class="badge bg-light text-dark border">عطلة</span>'; }
        else { statusBadge = '<span class="badge bg-warning text-dark">' + row.status + '</span>'; }

        // Violation Logic
        let violationHtml = row.violation_details.length > 0 
            ? '<div class="text-danger small"><i class="fas fa-exclamation-circle"></i> ' + row.violation_details.join('<br><i class="fas fa-exclamation-circle"></i> ') + '</div>' 
            : '<span class="text-muted">—</span>';

        // Deduction Amount Logic
        let amount = parseFloat(row.deduction_amount || 0);
        totalDeduction += amount;
        
        let amountHtml = amount > 0 
            ? `<span class="fw-bold text-danger">${amount.toFixed(2)}</span>` 
            : `<span class="text-muted">0.00</span>`;

        var newRow = `
            <tr>
                <td>${row.date}</td>
                <td>${row.day_name}</td>
                <td dir="ltr">${row.check_in}</td>
                <td dir="ltr">${row.check_out}</td>
                <td>${statusBadge}</td>
                <td class="text-start">${violationHtml}</td>
                <td>${amountHtml}</td>
            </tr>
        `;
        tableBody.append(newRow);
    });

    // Add Total Row at the bottom
    var totalRow = `
        <tr class="table-dark fw-bold">
            <td colspan="6" class="text-end">إجمالي الخصومات:</td>
            <td class="text-warning">${totalDeduction.toFixed(2)}</td>
        </tr>
    `;
    tableBody.append(totalRow);
}
        // ==================================================================
        // START: **CORRECTED** populateSalarySummary FUNCTION (v6)
        // ==================================================================
        function populateSalarySummary(details, empName, empId) {
            var content = $('#salary-summary-content');
            content.empty();
            
            // --- 1. Header Section ---
            content.append(`
                <div class="summary-section">
                    <h4 class="summary-header">
                        <i class="fas fa-user me-2"></i>تفاصيل حساب الراتب - ${empName}
                    </h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>الرقم الوظيفي:</strong> ${empId}</p>
                            <p><strong>الحالة:</strong> ${details.is_stopped ? '<span class="badge bg-danger">الراتب موقف</span>' : '<span class="badge bg-success">نشط</span>'}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>الإعفاء من الخصومات:</strong> ${details.is_exempt ? '<span class="badge bg-warning">معفى</span>' : '<span class="badge bg-secondary">غير معفى</span>'}</p>
                            <p><strong>موظف جديد:</strong> ${details.is_new_employee ? `<span class="badge bg-info">نعم (تاريخ الانضمام: ${details.join_date})</span>` : 'لا'}</p>
                        </div>
                    </div>
                </div>
            `);

            // --- 2. Skipped Employee Message ---
            if (details.is_skipped_this_month) {
                content.append(`
                    <div class="summary-section">
                        <h4 class="summary-header text-warning">
                            <i class="fas fa-info-circle me-2"></i>حالة الراتب
                        </h4>
                        <div class="alert alert-warning" role="alert">
                            <strong>موظف جديد (انضمام يوم ${details.join_day} >= 16).</strong><br>
                            سيتم إيقاف حساب الراتب لهذا الشهر. سيتم صرف الراتب المستحق عن هذا الشهر (من تاريخ الانضمام) مع مسير الرواتب للشهر القادم.
                        </div>
                    </div>
                `);
                return; // Stop rendering
            }

            // --- 3. Basic Salary Information ---
            let salaryLabel = "إجمالي الأجر الشهري (n6):";
            // This is the salary used for *calculation* (either 5500 or 4400)
            let calculationSalary = details.total_salary; 

            if (details.is_new_employee && details.actual_worked_days < 30) {
                 salaryLabel = "إجمالي الأجر (الراتب الكامل):";
            }

            let basicsSection = `
                <div class="summary-section">
                    <h5 class="summary-header">
                        <i class="fas fa-money-bill-wave me-2"></i>الأساسيات
                    </h5>
                    <div class="breakdown-row">
                        <div class="breakdown-label">${salaryLabel}</div>
                        <div class="breakdown-value positive-amount">${formatCurrency(details.full_time_salary)} ر.س</div>
                        <div class="breakdown-details">(n6)</div>
                    </div>
            `;

            // Conditionally show the Prorated Salary row
            if (details.is_new_employee && details.actual_worked_days < 30) {
                basicsSection += `
                    <div class="breakdown-row" style="background-color: #fff3cd;">
                        <div class="breakdown-label">الأجر الجزئي (لـ ${details.actual_worked_days} يوم):</div>
                        <div class="breakdown-value positive-amount">${formatCurrency(details.total_salary)} ر.س</div>
                        <div class="breakdown-details">(المبلغ المستخدم لحساب الخصومات)</div>
                    </div>
                `;
            }
            
            basicsSection += `
                <div class="breakdown-row">
                    <div class="breakdown-label">الأجر اليومي (المستخدم للخصم):</div>
                    <div class="breakdown-value">${formatCurrency(details.daily_salary)} ر.س</div>
                    <div class="breakdown-details calculation-formula">${formatCurrency(calculationSalary)} ÷ 30 يوم</div>
                </div>
                <div class="breakdown-row">
                    <div class="breakdown-label">الأجر لكل دقيقة (للخصم):</div>
                    <div class="breakdown-value">${formatCurrency(details.minute_salary, 4)} ر.س</div>
                    <div class="breakdown-details calculation-formula">${formatCurrency(details.daily_salary)} ÷ 8 س ÷ 60 د</div>
                </div>
            </div>
            `;
            content.append(basicsSection);


            // --- 4. Attendance Deductions (Current Period) ---
            let attendanceSection = `
                <div class="summary-section">
                    <h5 class="summary-header">
                        <i class="fas fa-user-clock me-2"></i>خصومات الحضور (الفترة الحالية)
                    </h5>
                    <div class="breakdown-row">
                        <div class="breakdown-label">أيام الغياب:</div>
                        <div class="breakdown-value">${formatNumber(details.absence_days)} يوم</div>
                        <div class="breakdown-details">${(details.is_new_employee && details.actual_worked_days < 30) ? '(تم تجاهل ما قبل الانضمام)' : ''}</div>
                    </div>`;
            
           if (details.half_day_vacations > 0) {
    attendanceSection += `
        <div class="breakdown-row">
            <div class="breakdown-label">إجازات نصف يوم:</div>
            <div class="breakdown-value">${formatNumber(details.half_day_vacations)} يوم</div>
            <div class="breakdown-details"></div>
        </div>
        <div class="breakdown-row">
            <div class="breakdown-label">خصم إجازات نصف يوم:</div>
            <div class="breakdown-value negative-amount">-${formatCurrency(details.half_day_vacation_deduct)} ر.س</div>
            <div class="breakdown-details calculation-formula">${formatNumber(details.half_day_vacations)} يوم × ${formatCurrency(details.daily_salary / 2)}</div>
        </div>
    `;
}
            attendanceSection += `
                    <div class="breakdown-row">
                        <div class="breakdown-label">خصم الغياب (n9):</div>
                        <div class="breakdown-value negative-amount">-${formatCurrency(details.absence_deduct)} ر.س</div>
                        <div class="breakdown-details calculation-formula">${formatNumber(details.absence_days)} يوم × ${formatCurrency(details.daily_salary)}</div>
                    </div>
                    <div class="breakdown-row">
                        <div class="breakdown-label">دقائق التأخير:</div>
                        <div class="breakdown-value">${formatNumber(details.minutes_late)} دقيقة</div>
                        <div class="breakdown-details"></div>
                    </div>
                    <div class="breakdown-row">
                        <div class="breakdown-label">خصم التأخير (n7):</div>
                        <div class="breakdown-value negative-amount">-${formatCurrency(details.late_deduct)} ر.س</div>
                        <div class="breakdown-details calculation-formula">${formatNumber(details.minutes_late)} دقيقة × ${formatCurrency(details.minute_salary, 4)}</div>
                    </div>
                    <div class="breakdown-row">
                        <div class="breakdown-label">دقائق الخروج المبكر:</div>
                        <div class="breakdown-value">${formatNumber(details.minutes_early)} دقيقة</div>
                        <div class="breakdown-details"></div>
                    </div>
                    <div class="breakdown-row">
                        <div class="breakdown-label">خصم الخروج المبكر (n8):</div>
                        <div class="breakdown-value negative-amount">-${formatCurrency(details.early_deduct)} ر.س</div>
                        <div class="breakdown-details calculation-formula">${formatNumber(details.minutes_early)} دقيقة × ${formatCurrency(details.minute_salary, 4)}</div>
                    </div>
                    <div class="breakdown-row">
                        <div class="breakdown-label">أيام البصمة المنفردة:</div>
                        <div class="breakdown-value">${formatNumber(details.single_punch_days)} يوم</div>
                        <div class="breakdown-details">${(details.is_new_employee && details.actual_worked_days < 30) ? '(تم تجاهل ما قبل الانضمام)' : ''}</div>
                    </div>
                    <div class="breakdown-row">
                        <div class="breakdown-label">خصم البصمة المنفردة:</div>
                        <div class="breakdown-value negative-amount">-${formatCurrency(details.single_punch_deduct)} ر.س</div>
                        <div class="breakdown-details calculation-formula">${formatNumber(details.single_punch_days)} يوم × ${formatCurrency(details.daily_salary)}</div>
                    </div>
                    <div class="breakdown-row total-row">
                        <div class="breakdown-label">إجمالي خصومات الحضور (الحالية) (n18):</div>
                        <div class="breakdown-value negative-amount">-${formatCurrency(details.total_current_attendance_deductions)} ر.س</div>
                        <div class="breakdown-details"></div>
                    </div>
                </div>
            `;
            content.append(attendanceSection);
            
            // --- 4.5. Attendance Deductions (PREVIOUS Period) ---
            if (details.total_previous_attendance_deductions > 0) {
                let prevAttendanceSection = `
                    <div class="summary-section" style="border-color: var(--marsom-orange);">
                        <h5 class="summary-header">
                            <i class="fas fa-history me-2"></i>خصومات الحضور (الفترة السابقة) (n19)
                        </h5>
                        <div class="breakdown-row">
                            <div class="breakdown-label">خصم الغياب (السابق):</div>
                            <div class="breakdown-value negative-amount">-${formatCurrency(details.prev_absence_deduct)} ر.س</div>
                            <div class="breakdown-details">(بناءً على الراتب الكامل)</div>
                        </div>
                        <div class="breakdown-row">
                            <div class="breakdown-label">خصم التأخير (السابق):</div>
                            <div class="breakdown-value negative-amount">-${formatCurrency(details.prev_late_deduct)} ر.س</div>
                            <div class="breakdown-details"></div>
                        </div>
                        <div class="breakdown-row">
                            <div class="breakdown-label">خصم الخروج المبكر (السابق):</div>
                            <div class="breakdown-value negative-amount">-${formatCurrency(details.prev_early_deduct)} ر.س</div>
                            <div class="breakdown-details"></div>
                        </div>
                        <div class="breakdown-row">
                            <div class="breakdown-label">خصم البصمة المنفردة (السابق):</div>
                            <div class="breakdown-value negative-amount">-${formatCurrency(details.prev_single_punch_deduct)} ر.س</div>
                            <div class="breakdown-details"></div>
                        </div>
                        <div class="breakdown-row total-row">
                            <div class="breakdown-label">إجمالي خصومات الحضور (السابقة):</div>
                            <div class="breakdown-value negative-amount">-${formatCurrency(details.total_previous_attendance_deductions)} ر.س</div>
                            <div class="breakdown-details"></div>
                        </div>
                    </div>
                `;
                content.append(prevAttendanceSection);
            }

            // --- 5. Other Deductions and Additions ---
            let additionsSection = `
                <div class="summary-section">
                    <h5 class="summary-header">
                        <i class="fas fa-calculator me-2"></i>إضافات وخصومات أخرى
                    </h5>
                    <div class="breakdown-row">
                        <div class="breakdown-label">خصم الجزاءات (n17):</div>
                        <div class="breakdown-value negative-amount">-${formatCurrency(details.discount_amount)} ر.س</div>
                        <div class="breakdown-details">قيم مالية محددة</div>
                    </div>
                    <div class="breakdown-row">
                        <div class="breakdown-label">التعويضات (يدوي) (n16):</div>
                        <div class="breakdown-value positive-amount">+${formatCurrency(details.reparations_amount)} ر.س</div>
                        <div class="breakdown-details">مبالغ إضافية</div>
                    </div>
                    `;
            
            if (details.previous_month_comp > 0) {
                additionsSection += `
                    <div class="breakdown-row" style="background-color: #d1e7dd;">
                        <div class="breakdown-label">راتب متبقي (الشهر السابق) (n15):</div>
                        <div class="breakdown-value positive-amount">+${formatCurrency(details.previous_month_comp)} ر.س</div>
                        <div class="breakdown-details">تعويض عن شهر الانضمام</div>
                    </div>
                `;
            }

            // **MODIFIED**: Show "Hiring Difference" (e.g., 1100) here, but it's not part of the sum
            if (details.hiring_day_difference > 0 && !details.is_skipped_this_month) {
                additionsSection += `
                    <div class="breakdown-row" style="background-color: #fff3cd;">
                        <div class="breakdown-label">فروقات التوظيف (للعرض فقط):</div>
                        <div class="breakdown-value negative-amount">-${formatCurrency(details.hiring_day_difference)} ر.س</div>
                        <div class="breakdown-details">( ${formatCurrency(details.full_time_salary)} - ${formatCurrency(details.total_salary)} )</div>
                    </div>
                `;
            }

            additionsSection += `
                </div>
            `;
            content.append(additionsSection);

            // --- 6. Insurance Deductions ---
            if (details.insurance_deduction > 0 || (details.insurance_rate > 0 && details.base_plus_house > 0)) {
                content.append(`
                    <div class="summary-section">
                        <h5 class="summary-header">
                            <i class="fas fa-shield-alt me-2"></i>خصم التأمينات (n10)
                        </h5>
                        <div class="breakdown-row">
                            <div class="breakdown-label">أساس احتساب التأمينات:</div>
                            <div class="breakdown-value">${formatCurrency(details.prorated_gosi_base)} ر.س</div>
                            <div class="breakdown-details">${details.gosi_calc_note}</div>
                        </div>
                        <div class="breakdown-row">
                            <div class="breakdown-label">نسبة الخصم:</div>
                            <div class="breakdown-value">${(details.insurance_rate * 100).toFixed(2)}%</div>
                            <div class="breakdown-details"></div>
                        </div>
                        <div class="breakdown-row">
                            <div class="breakdown-label">خصم التأمينات:</div>
                            <div class="breakdown-value negative-amount">-${formatCurrency(details.insurance_deduction)} ر.س</div>
                            <div class="breakdown-details calculation-formula">${formatCurrency(details.prorated_gosi_base)} × ${(details.insurance_rate * 100).toFixed(2)}%</div>
                        </div>
                    </div>
                `);
            }

            // --- 7. Final Summary ---
            const total_additions = details.reparations_amount + details.previous_month_comp;
            const total_additions_details = `(تعويضات: ${formatCurrency(details.reparations_amount)}, متبقي: ${formatCurrency(details.previous_month_comp)})`;
            let finalSummaryHtml = `
                <div class="summary-section bg-light">
                    <h5 class="summary-header text-dark">
                        <i class="fas fa-file-invoice-dollar me-2"></i>الملخص النهائي
                    </h5>
                    <div class="breakdown-row">
                        <div class="breakdown-label">إجمالي الأجر (المستخدم بالحساب):</div>
                        <div class="breakdown-value positive-amount">${formatCurrency(details.total_salary)} ر.س</div>
                        <div class="breakdown-details">${(details.is_new_employee && details.actual_worked_days < 30) ? '(راتب جزئي)' : '(راتب كامل)'}</div>
                    </div>
                    <div class="breakdown-row">
                        <div class="breakdown-label">+ الإضافات (n16 + n15):</div>
                        <div class="breakdown-value positive-amount">+${formatCurrency(total_additions)} ر.س</div>
                        <div class="breakdown-details">${total_additions_details}</div>
                    </div>
                    `;
            
            if (!details.is_exempt) {
                finalSummaryHtml += `
                    <div class="breakdown-row">
                        <div class="breakdown-label">- خصومات الحضور (الحالية) (n18):</div>
                        <div class="breakdown-value negative-amount">-${formatCurrency(details.total_current_attendance_deductions)} ر.س</div>
                        <div class="breakdown-details"></div>
                    </div>`;
                if (details.total_previous_attendance_deductions > 0) {
                     finalSummaryHtml += `
                    <div class="breakdown-row">
                        <div class="breakdown-label">- خصومات الحضور (السابقة) (n19):</div>
                        <div class="breakdown-value negative-amount">-${formatCurrency(details.total_previous_attendance_deductions)} ر.س</div>
                        <div class="breakdown-details"></div>
                    </div>`;
                }
                finalSummaryHtml += `
                    <div class="breakdown-row">
                        <div class="breakdown-label">- خصم الجزاءات (n17):</div>
                        <div class="breakdown-value negative-amount">-${formatCurrency(details.discount_amount)} ر.س</div>
                        <div class="breakdown-details"></div>
                    </div>
                `;
            } else {
                 // --- FIX: Show Unpaid Leave for Exempt Employees ---
                 if (details.unpaid_leave_deduction > 0) {
                    finalSummaryHtml += `
                        <div class="breakdown-row">
                            <div class="breakdown-label" style="color:#dc3545">- خصم إجازة غير مدفوعة:</div>
                            <div class="breakdown-value negative-amount">-${formatCurrency(details.unpaid_leave_deduction)} ر.س</div>
                            <div class="breakdown-details">(${details.unpaid_leave_days} يوم) - (الإجازة غير المدفوعة لا تشملها الإعفاء)</div>
                        </div>
                    `;
                 }
                 
                 finalSummaryHtml += `
                    <div class="breakdown-row">
                        <div class="breakdown-label">- خصومات الحضور (تأخير/غياب):</div>
                        <div class="breakdown-value negative-amount">0.00 ر.س</div>
                        <div class="breakdown-details">(معفى)</div>
                    </div>
                     <div class="breakdown-row">
                        <div class="breakdown-label">- خصم الجزاءات (n17):</div>
                        <div class="breakdown-value negative-amount">-${formatCurrency(details.discount_amount)} ر.س</div>
                        <div class="breakdown-details"></div>
                    </div>
                `;
            }

            finalSummaryHtml += `
                <div class="breakdown-row" style="border-top: 1px dashed #ccc; padding-top: 10px;">
                    <div class="breakdown-label">إجمالي الخصومات (n11):</div>
                    <div class="breakdown-value negative-amount">-${formatCurrency(details.total_deductions)} ر.س</div>
                    <div class="breakdown-details"></div>
                </div>
                <div class="breakdown-row">
                    <div class="breakdown-label">= الراتب قبل التأمينات:</div>
                    <div class="breakdown-value neutral-amount">${formatCurrency(details.salary_before_insurance)} ر.س</div>
                    <div class="breakdown-details"></div>
                </div>
                ${(details.insurance_deduction > 0 || (details.insurance_rate > 0 && details.base_plus_house > 0)) ? `
                <div class="breakdown-row">
                    <div class="breakdown-label">- خصم التأمينات (n10):</div>
                    <div class="breakdown-value negative-amount">-${formatCurrency(details.insurance_deduction)} ر.س</div>
                    <div class="breakdown-details"></div>
                </div>
                ` : ''}
                <div class="breakdown-row total-row bg-primary text-white rounded">
                    <div class="breakdown-label" style="color: white;">صافي الراتب المستحق (n12):</div>
                    <div class="breakdown-value" style="color: white; font-size: 1.2em;">${formatCurrency(details.net_salary)} ر.س</div>
                    <div class="breakdown-details" style="color: white;">المبلغ النهائي</div>
                </div>
            </div>
            `;
            content.append(finalSummaryHtml);
        }
        // ==================================================================
        // END: CORRECTED populateSalarySummary FUNCTION
        // ==================================================================


        // ==================================================================
        // START: PAYROLL PROCESSING JAVASCRIPT (MODIFIED)
        // ==================================================================
        const companyModal = new bootstrap.Modal(document.getElementById('companySelectionModal'));
        const csrfTokenName = '<?php echo $this->security->get_csrf_token_name(); ?>';
        let csrfTokenHash = '<?php echo $this->security->get_csrf_hash(); ?>'; // Initial Hash

        $('#process-salary-btn').on('click', function() {
            $('#companySelectionError').addClass('d-none');
            $('.company-checkbox').prop('checked', false);
            $('#selectAllCompanies').prop('checked', false);
            companyModal.show();
        });

        $('#selectAllCompanies').on('change', function() {
            $('.company-checkbox').prop('checked', this.checked);
        });
        $('.company-checkbox').on('change', function() {
            if ($('.company-checkbox:checked').length === $('.company-checkbox').length) {
                $('#selectAllCompanies').prop('checked', true);
            } else {
                $('#selectAllCompanies').prop('checked', false);
            }
        });

        $('#confirmCompanyProcessBtn').on('click', function() {
            const selectedCompanyCodes = [];
            const selectedCompanyNames = [];
            $('.company-checkbox:checked').each(function() {
                selectedCompanyCodes.push($(this).val());
                selectedCompanyNames.push($(this).data('company-name'));
            });

            if (selectedCompanyCodes.length === 0) {
                $('#companySelectionError').removeClass('d-none');
                return;
            } else {
                $('#companySelectionError').addClass('d-none');
            }

            companyModal.hide();

            Swal.fire({
                title: 'تأكيد المعالجة؟',
                html: `سيتم معالجة مسير الرواتب للشركات التالية:<br><strong>${selectedCompanyNames.join('<br>')}</strong><br><br>سيتم حذف أي بيانات سابقة لهذه الفترة وللشركات المحددة فقط، وحفظ هذه البيانات بشكل نهائي.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم، قم بالمعالجة!',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    const fullPayrollBatch = [];
                    const sheetId = "<?php echo html_escape($get_salary_sheet['type'] ?? ''); ?>";

                    // In payroll_view101.php -> inside the process button click event

table.rows({ search: 'applied' }).nodes().each(function(node) {
    const row = $(node);
    const details = JSON.parse(row.attr('data-salary-details'));
    
    // Calculate combined "Other Allowances" to simplify columns if needed
    // Or stick to specific columns. Here we map exactly to the Model.
    
    fullPayrollBatch.push({
        // --- Identity ---
        n1: row.data('n1'), // Employee ID
        n2: row.data('n2'), // Name
        n3: row.data('n3'), // National ID
        n4: row.data('n4'), // IBAN
        n5: row.data('n5'), // Bank
        n14: row.data('company-code'),

        // --- Earnings Breakdown ---
        n21: details.base_salary || 0,
        n22: details.housing_allowance || 0,
        n23: details.n4 || 0, // Transport
        n24: details.other_allowances || 0,
        n6:  details.full_time_salary, // Total Gross Contract

        // --- Adjustments ---
        // Hiring Diff: If new employee worked partial month, this is the diff
        n20: details.hiring_day_difference || 0, 
        n15: details.previous_month_comp || 0,   // Backpay
        n16: details.reparations_amount || 0,    // Manual Additions

        // --- Deductions ---
        n9:  details.absence_deduct || 0,
        n29: details.unpaid_leave_deduction || 0,
        n7:  details.late_deduct || 0,
        n8:  details.early_deduct || 0,
        // ---------- FIX IS HERE ----------
        n31: details.single_punch_deduct || 0, // Maps Single Punch Deduct to n31 column
        // ---------------------------------
        n17: details.discount_amount || 0, // Penalties
        n10: details.insurance_deduction || 0,
        n11: details.total_deductions || 0, // Total Deductions
        n18: details.other_deductions || 0,

        // --- Attendance Counters (Text from table cells) ---
        n26: row.find('td:eq(17)').text(), // Absence Days
        n27: row.find('td:eq(18)').text(), // Unpaid Days
        n28: row.find('td:eq(19)').text(), // Late Minutes

        // --- Net ---
        n12: details.net_salary
    });
});
                   // In payroll_view101.php

const filteredPayrollBatch = fullPayrollBatch.filter(empData => {
    // FIX: Always include employees with NO company code (Stopped/New)
    if (!empData.n14 || empData.n14 === '' || empData.n14 === '0') {
        return true; 
    }
    // Otherwise, check if their company matches the selection
    return selectedCompanyCodes.includes(String(empData.n14));
});

                    if (filteredPayrollBatch.length === 0) {
                        Swal.fire('خطأ!', 'لم يتم العثور على موظفين تابعين للشركات المحددة ضمن الفلاتر الحالية (أو تم تخطيهم).', 'error');
                        return;
                    }

                    $.ajax({
                        url: "<?php echo site_url('users1/process_payroll'); ?>",
                        type: 'POST',
                        data: {
                            payroll_data: filteredPayrollBatch,
                            sheet_id: sheetId,
                            [csrfTokenName]: csrfTokenHash
                        },
                        dataType: 'json',
                        beforeSend: function() {
                            Swal.fire({
                                title: 'جاري المعالجة...', text: 'الرجاء الانتظار.',
                                allowOutsideClick: false, didOpen: () => { Swal.showLoading(); }
                            });
                        },
                        success: function(response, textStatus, jqXHR) {
                            csrfTokenHash = response.csrf_hash || csrfTokenHash;
                            $('input[name="'+ csrfTokenName +'"]').val(csrfTokenHash);

                            if (response.status === 'success') {
                                Swal.fire('نجاح!', response.message, 'success');
                            } else {
                                Swal.fire('خطأ!', response.message || 'فشل غير متوقع.', 'error');
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error("AJAX Error:", textStatus, errorThrown, jqXHR.responseText);
                            const newCsrfHash = jqXHR.responseJSON?.csrf_hash;
                            if (newCsrfHash) { csrfTokenHash = newCsrfHash; }
                            $('input[name="'+ csrfTokenName +'"]').val(csrfTokenHash);
                            Swal.fire('خطأ فادح!', 'حدث خطأ غير متوقع أثناء الاتصال بالخادم. راجع الكونسول لمزيد من التفاصيل.', 'error');
                        }
                    });
                }
            });
        });
        // ==================================================================
        // END: PAYROLL PROCESSING JAVASCRIPT
        // ==================================================================

// =============================================
// COLUMN REORDER SYSTEM - IMMEDIATE REORDERING
// =============================================
$(document).ready(function() {
    let dataTable;
    let isPanelOpen = false;
    
    // Initialize when page loads
    setTimeout(initializeSystem, 1000);
    
    function initializeSystem() {
        dataTable = $('.dataTables-example').DataTable();
        console.log('Column reorder system ready');
        
        // Load saved column order if exists
        loadSavedOrder();
        
        // Setup all event handlers
        setupEventHandlers();
        
        // Add quick toggle button
        addQuickToggleButton();
    }
    
    // Load saved column order
    function loadSavedOrder() {
        const savedOrder = localStorage.getItem('payrollColumnsOrder');
        if (savedOrder) {
            try {
                const order = JSON.parse(savedOrder);
                console.log('Loading saved column order:', order);
                
                // Apply saved order using colReorder
                if (dataTable.colReorder) {
                    dataTable.colReorder.order(order);
                    dataTable.draw(false);
                }
            } catch(e) {
                console.error('Error loading saved order:', e);
            }
        }
    }
    
    // Save column order
    function saveColumnOrder(order) {
        localStorage.setItem('payrollColumnsOrder', JSON.stringify(order));
        console.log('Column order saved:', order);
    }
    
    // Setup event handlers
    function setupEventHandlers() {
        // Open/Close reorder panel
        $('#reorderColumnsBtn').click(function() {
            toggleReorderPanel();
        });
        
        // Close panel
        $('#closePanelBtn').click(function() {
            closeReorderPanel();
        });
        
        // Apply/Save new order
        $('#applyOrderBtn').click(function() {
            applyNewColumnOrder();
        });
        
        // Reset to defaults
        $('#resetOrderBtn').click(function() {
            resetColumnOrder();
        });
    }
    
    // Toggle reorder panel
    function toggleReorderPanel() {
        const $panel = $('#columnReorderPanel');
        
        if (isPanelOpen) {
            closeReorderPanel();
        } else {
            openReorderPanel();
        }
    }
    
    // Open reorder panel
    function openReorderPanel() {
        isPanelOpen = true;
        $('#columnReorderPanel').slideDown(300, function() {
            populateColumnList();
            // Scroll to panel
            $('html, body').animate({
                scrollTop: $(this).offset().top - 20
            }, 300);
        });
    }
    
    // Close reorder panel
    function closeReorderPanel() {
        isPanelOpen = false;
        $('#columnReorderPanel').slideUp(300);
    }
    
    // Populate column list in panel
    function populateColumnList() {
        const $list = $('#columnList');
        $list.empty();
        
        // Get current column order
        let currentOrder;
        if (dataTable.colReorder) {
            currentOrder = dataTable.colReorder.order();
        } else {
            currentOrder = dataTable.columns().indexes().toArray();
        }
        
        console.log('Current column order:', currentOrder);
        
        // Create column items
        currentOrder.forEach((colIndex, displayIndex) => {
            const column = dataTable.column(colIndex);
            const header = column.header();
            const columnName = $(header).text().trim() || `العمود ${colIndex + 1}`;
            const isVisible = column.visible();
            
            const $item = $(`
                <div class="column-item" data-col-index="${colIndex}">
                    <div class="column-index">${displayIndex + 1}</div>
                    <div class="column-name">${columnName}</div>
                    <div class="column-visibility">
                        <i class="fas ${isVisible ? 'fa-eye' : 'fa-eye-slash'} visibility-toggle ${isVisible ? 'active' : ''}"
                           data-col-index="${colIndex}" title="${isVisible ? 'إخفاء' : 'إظهار'}"></i>
                        <i class="fas fa-grip-vertical text-muted" title="اسحب لإعادة الترتيب"></i>
                    </div>
                </div>
            `);
            
            if (!isVisible) {
                $item.addClass('column-hidden');
            }
            
            $list.append($item);
        });
        
        // Make sortable
        $list.sortable({
            placeholder: "column-item ui-state-highlight",
            handle: ".fa-grip-vertical",
            start: function(e, ui) {
                ui.item.addClass('dragging');
            },
            stop: function(e, ui) {
                ui.item.removeClass('dragging');
                updateColumnNumbers();
            }
        });
        
        // Add click handlers for visibility toggles
        $('.visibility-toggle').off('click').on('click', function(e) {
            e.stopPropagation();
            toggleColumnVisibility($(this));
        });
    }
    
    // Update column numbers in panel
    function updateColumnNumbers() {
        $('#columnList .column-item').each(function(index) {
            $(this).find('.column-index').text(index + 1);
        });
    }
    
    // Toggle column visibility
    function toggleColumnVisibility($icon) {
        const colIndex = parseInt($icon.data('col-index'));
        const isCurrentlyVisible = $icon.hasClass('active');
        const newVisibility = !isCurrentlyVisible;
        
        // Update icon
        $icon.toggleClass('active', newVisibility);
        $icon.toggleClass('fa-eye fa-eye-slash');
        $icon.attr('title', newVisibility ? 'إخفاء' : 'إظهار');
        
        // Update item style
        $icon.closest('.column-item').toggleClass('column-hidden', !newVisibility);
        
        // Update in DataTable
        dataTable.column(colIndex).visible(newVisibility, false);
        
        // Redraw table
        setTimeout(() => {
            dataTable.columns.adjust().draw(false);
        }, 100);
    }
    
    // Apply new column order - THIS IS THE KEY FUNCTION
    function applyNewColumnOrder() {
        // Get new order from panel
        const newOrder = [];
        $('#columnList .column-item').each(function() {
            const colIndex = parseInt($(this).data('col-index'));
            newOrder.push(colIndex);
        });
        
        console.log('Applying new column order:', newOrder);
        
        // Save the order
        saveColumnOrder(newOrder);
        
        // Apply the order to the table
        if (dataTable.colReorder) {
            // Use colReorder plugin
            dataTable.colReorder.order(newOrder);
            dataTable.draw(false);
            console.log('Applied via colReorder');
        } else {
            // Alternative method: reinitialize table
            reorderTableManually(newOrder);
        }
        
        // Show success message
        Swal.fire({
            title: 'تم!',
            text: 'تم تطبيق ترتيب الأعمدة الجديد.',
            icon: 'success',
            confirmButtonText: 'حسناً',
            confirmButtonColor: '#198754',
            timer: 1500,
            showConfirmButton: false
        });
        
        // Close panel
        closeReorderPanel();
    }
    
    // Alternative method to reorder columns
    function reorderTableManually(newOrder) {
        // Get all table data
        const tableData = dataTable.data().toArray();
        const headers = [];
        
        // Collect headers in new order
        newOrder.forEach(colIndex => {
            const column = dataTable.column(colIndex);
            const header = $(column.header()).text().trim();
            headers.push(header);
        });
        
        // Create new table data in new order
        const newData = tableData.map(row => {
            const newRow = [];
            newOrder.forEach(colIndex => {
                newRow.push(row[colIndex]);
            });
            return newRow;
        });
        
        // Destroy current table
        dataTable.destroy();
        
        // Rebuild table header in new order
        const $table = $('.dataTables-example');
        const $thead = $table.find('thead');
        const $headerRow = $thead.find('tr:first');
        
        // Clear and rebuild header
        $headerRow.empty();
        headers.forEach(header => {
            $headerRow.append(`<th>${header}</th>`);
        });
        
        // Rebuild table body in new order
        const $tbody = $table.find('tbody');
        $tbody.empty();
        
        newData.forEach(rowData => {
            const $row = $('<tr></tr>');
            rowData.forEach(cellData => {
                $row.append(`<td>${cellData}</td>`);
            });
            $tbody.append($row);
        });
        
        // Reinitialize DataTable
        dataTable = $table.DataTable({
            responsive: true,
            pageLength: 10,
            colReorder: true,
            // Copy all your original DataTable options here
            language: { "url": "https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json" },
            // ... add all other options from your original initialization
        });
        
        console.log('Table reordered manually');
    }
    
    // Reset column order to default
    function resetColumnOrder() {
        Swal.fire({
            title: 'تأكيد الإعادة',
            text: 'هل تريد إعادة جميع الأعمدة إلى الترتيب الافتراضي؟',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'نعم، إعادة',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                // Clear saved order
                localStorage.removeItem('payrollColumnsOrder');
                
                // Get default order (original column indices)
                const defaultOrder = dataTable.columns().indexes().toArray();
                
                // Apply default order
                if (dataTable.colReorder) {
                    dataTable.colReorder.order(defaultOrder);
                    dataTable.draw(false);
                }
                
                // Show all columns
                dataTable.columns().every(function() {
                    this.visible(true, false);
                });
                dataTable.columns.adjust().draw(false);
                
                // Show success
                Swal.fire({
                    title: 'تمت الإعادة!',
                    text: 'تمت إعادة الأعمدة إلى الترتيب الافتراضي.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
                
                // Close panel
                closeReorderPanel();
                
                // Reload panel if open
                if (isPanelOpen) {
                    setTimeout(populateColumnList, 300);
                }
            }
        });
    }
    
    // Add quick toggle button
    function addQuickToggleButton() {
        if ($('#quickToggleColumns').length === 0) {
            $('.table-card').prepend(`
                <div class="table-actions">
                    <button id="quickToggleColumns" class="btn btn-sm btn-outline-primary" title="تبديل عرض الأعمدة">
                        <i class="fas fa-eye-slash"></i>
                    </button>
                </div>
            `);
            
            $('#quickToggleColumns').click(function() {
                toggleImportantColumns();
            });
        }
    }
    
    // Toggle important columns
    function toggleImportantColumns() {
        // Define columns to always keep visible
        const importantColumns = [0, 1, 2, 3, 33]; // Actions, ID, Name, ID Number, Net Salary
        
        // Check if any non-important columns are hidden
        let anyHidden = false;
        dataTable.columns().every(function(i) {
            if (!importantColumns.includes(i) && !this.visible()) {
                anyHidden = true;
                return false;
            }
        });
        
        // Toggle non-important columns
        dataTable.columns().every(function(i) {
            if (!importantColumns.includes(i)) {
                const newVisibility = anyHidden; // Show if any are hidden, hide if all are shown
                this.visible(newVisibility, false);
            }
        });
        
        // Redraw
        dataTable.columns.adjust().draw(false);
        
        // Update button icon
        const $icon = $('#quickToggleColumns i');
        if (anyHidden) {
            $icon.removeClass('fa-eye-slash').addClass('fa-eye');
        } else {
            $icon.removeClass('fa-eye').addClass('fa-eye-slash');
        }
        
        // Show notification
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: anyHidden ? 'تم إظهار جميع الأعمدة' : 'تم إخفاء الأعمدة غير المهمة',
            showConfirmButton: false,
            timer: 1500
        });
    }
});
    }); // End $(document).ready
</script>
<script>
    // Function to open the modal and load the URL
    function openAttendancePopup(url) {
        // 1. Set the iframe src
        var frame = document.getElementById('attendanceFrame');
        frame.src = url;

        // 2. Show the modal
        var myModal = new bootstrap.Modal(document.getElementById('attendanceModal'));
        myModal.show();
    }

    // Function to print the iframe content from the parent modal button
    function printIframe() {
        var frame = document.getElementById('attendanceFrame');
        if (frame.contentWindow) {
            frame.contentWindow.focus();
            frame.contentWindow.print();
        }
    }

    // Optional: Clear iframe when modal is closed to stop video/memory usage
    document.getElementById('attendanceModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('attendanceFrame').src = "";
    });
</script>
<script>
    function openPopup(url) {
        var frame = document.getElementById('reportFrame');
        frame.src = url;
        var myModal = new bootstrap.Modal(document.getElementById('reportModal'));
        myModal.show();
    }
    
    // Clear iframe on close to stop memory leaks
    document.getElementById('reportModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('reportFrame').src = "about:blank";
    });
</script>
</body>
</html>