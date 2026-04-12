<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مسير الرواتب</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.css">

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

        .table-responsive { overflow-x: auto; }

        .dataTables-example thead th {
            background-color: var(--marsom-blue) !important;
            color: var(--text-light);
            font-weight: 500;
            text-align: center;
            vertical-align: middle;
            border-bottom: 2px solid #00152b;
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
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card table-card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover dataTables-example" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>الإجراءات</th>
                                        <th>الرقم الوظيفي</th>
                                        <th>اسم الموظف</th>
                                        <th>رقم الهوية</th>
                                        <th>اسم البنك</th>
                                        <th>الايبان</th>
                                        <th>المسمى الوظيفي</th>
                                        <th>الادارة</th>
                                        <th>الجنسية</th>
                                        <th>إجمالي الأجر</th>
                                        <th>اسم الشركة</th>
                                        <th>ايام الغياب</th>
                                        <th>إجمالي الدقائق المتأخرة</th>
                                        <th>إجمالي الدقائق المبكرة</th>
                                        <th>أيام بصمة منفردة</th>
                                        <th>خصم الغياب</th>
                                        <th>خصم بصمة منفردة</th>
                                        <th>خصم دقائق التأخير والخروج المبكر</th>
                                        <th>خصم الجزاءات</th>
                                        <th>التعويضات</th>
                                        <th> تعويض الموظفين الجدد</th>
                                        <th>إجمالي خصم الحضور والانصراف</th>
                                        <th>الاجمالي الكلي للخصومات</th>
                                        <th>اجمالي الراتب ما قبل خصم التأمينات</th>
                                        <th>اجمالي خصم التامينات</th>
                                        <th>صافي الراتب</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $sheet_start_ts = isset($get_salary_sheet['start_date']) ? strtotime(trim($get_salary_sheet['start_date'])) : false;
                                        $sheet_end_ts   = isset($get_salary_sheet['end_date']) ? strtotime(trim($get_salary_sheet['end_date'])) : false;
                                    ?>
                                    <?php foreach ($employees as $row): ?>
                                        <?php
                                            // All data is now pre-fetched from maps passed by the controller
                                            $summary = $attendance_map[$row->employee_id] ?? null;
                                            $absence = $summary ? (int)$summary->absence : 0;
                                            $minutes_late = $summary ? (int)$summary->minutes_late : 0;
                                            $minutes_early = $summary ? (int)$summary->minutes_early : 0;
                                            $single_thing = $summary ? (int)$summary->single_thing : 0;

                                            $discount_amount = isset($discounts_map[$row->employee_id]) ? (float)$discounts_map[$row->employee_id]->amount : 0;
                                            $reparations_amount = isset($reparations_map[$row->employee_id]) ? (float)$reparations_map[$row->employee_id]->amount : 0;

                                            $total_salary = (float)$row->total_salary;
                                            $daily_salary = $total_salary / 30;
                                            $minute_salary = $daily_salary / 8 / 60;

                                            $absence_deduct = $daily_salary * $absence;
                                            $single_thing_deduct = $daily_salary * $single_thing;
                                            $deduction = $minute_salary * ($minutes_late + $minutes_early);

                                            $new_emp_comp = 0.0;
                                            $new_emp_details = $new_employee_map[$row->employee_id] ?? null;
                                            if ($new_emp_details && !empty($new_emp_details->join_date) && $sheet_start_ts && $sheet_end_ts) {
                                                $join_ts = strtotime(str_replace('/', '-', trim($new_emp_details->join_date)));
                                                if ($join_ts) {
                                                    $join_year_month = date('Y-m', $join_ts);
                                                    $join_day = (int)date('j', $join_ts);
                                                    $days_to_30 = max(0, 30 - $join_day);

                                                    $days_join_to_end = (date('Y-m', $sheet_end_ts) === $join_year_month) ? max(0, min((int)(new DateTime(date('Y-m-d', $join_ts)))->diff(new DateTime(date('Y-m-d', $sheet_end_ts)))->days, $days_to_30)) : $days_to_30;
                                                    $days_join_to_start = (date('Y-m', $sheet_start_ts) === $join_year_month && $sheet_start_ts <= $join_ts) ? max(0, min(max(0, $join_day - (int)date('j', $sheet_start_ts)), $days_to_30)) : 0;
                                                    
                                                    $final_days = min(30, max(0, $days_to_30 - $days_join_to_end - $days_join_to_start));
                                                    $new_emp_comp = ($total_salary / 30.0) * $final_days;
                                                }
                                            }

                                            $insurance_deduction = 0.0;
                                            $discount_rate = $insurance_map[$row->employee_id] ?? 0.0;
                                            $base_plus_house = (float)$row->base_salary + (float)$row->housing_allowance;
                                            
                                            if ($new_emp_details && trim((string)$new_emp_details->nationality) === 'سعودي' && !empty($new_emp_details->join_date)) {
                                                $join_ts = strtotime(str_replace('/', '-', trim($new_emp_details->join_date)));
                                                if ($join_ts) {
                                                    $days_in_month = (int)date('t', $join_ts);
                                                    $join_day = (int)date('j', $join_ts);
                                                    $joined_days = max(0, $days_in_month - $join_day + 1);
                                                    $prorated = ($days_in_month > 0 ? ($base_plus_house / $days_in_month) : 0.0) * $joined_days;
                                                    $insurance_deduction = $prorated * $discount_rate;
                                                }
                                            } else if ($row->nationality === 'سعودي') {
                                                $insurance_deduction = $base_plus_house * $discount_rate;
                                            }

                                            $attendance_total_deduct = $deduction + $single_thing_deduct + $absence_deduct;
                                            $total_deductions = $attendance_total_deduct + $discount_amount;
                                            $salary_before_insurance = $total_salary - $total_deductions;

                                            $is_stopped = isset($stopped_salary_map[$row->employee_id]);
                                            $is_exempt = isset($exemption_map[$row->employee_id]);
                                            $net_salary = 0.0;
                                            if (!$is_stopped) {
                                                $net_salary = $is_exempt ? ($total_salary - $insurance_deduction) : ($total_salary - $total_deductions - $insurance_deduction);
                                            }
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-info view-details-btn" 
                                                        data-bs-toggle="modal" data-bs-target="#detailsModal"
                                                        data-empid="<?php echo $row->employee_id; ?>"
                                                        data-empname="<?php echo htmlspecialchars($row->subscriber_name); ?>"
                                                        data-sheetid="<?php echo $id; ?>">
                                                        <i class="fas fa-list"></i> تفاصيل
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-primary" onclick="openExemptionPopup('<?php echo $row->employee_id; ?>', '<?php echo $id; ?>')">
                                                        <i class="fas fa-gavel"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td><?php echo $row->employee_id; ?></td>
                                            <td><?php echo $row->subscriber_name; ?></td>
                                            <td><?php echo $row->id_number; ?></td>
                                            <td><?php echo $row->n3; ?></td>
                                            <td><?php echo $row->n2; ?></td>
                                            <td><?php echo $row->profession; ?></td>
                                            <td><?php echo $row->n1; ?></td>
                                            <td><?php echo $row->nationality; ?></td>
                                            <td><?php echo number_format($total_salary, 2); ?></td>
                                            <td><?php echo $row->company_name; ?></td>
                                            <td><strong><?php echo $absence; ?></strong></td>
                                            <td><strong><?php echo $minutes_late; ?></strong></td>
                                            <td><strong><?php echo $minutes_early; ?></strong></td>
                                            <td><strong><?php echo $single_thing; ?></strong></td>
                                            <td><strong><?php echo number_format($absence_deduct, 2); ?></strong></td>
                                            <td><strong><?php echo number_format($single_thing_deduct, 2); ?></strong></td>
                                            <td><strong><?php echo number_format($deduction, 2); ?></strong></td>
                                            <td><strong><?php echo number_format($discount_amount, 2); ?></strong></td>
                                            <td><strong><?php echo number_format($reparations_amount, 2); ?></strong></td>
                                            <td><strong><?php echo number_format($new_emp_comp, 2); ?></strong></td>
                                            <td><strong><?php echo number_format($attendance_total_deduct, 2); ?></strong></td>
                                            <td><strong><?php echo number_format($total_deductions, 2); ?></strong></td>
                                            <td><strong><?php echo number_format($salary_before_insurance, 2); ?></strong></td>
                                            <td><strong><?php echo number_format($insurance_deduction, 2); ?></strong></td>
                                            <td>
                                                <strong>
                                                    <?php
                                                        if ($is_stopped) {
                                                            echo '<span class="badge bg-danger">الراتب موقف</span>';
                                                        } else {
                                                            echo number_format($net_salary, 2);
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

<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailsModalLabel">تفاصيل الحضور للموظف: <span id="modalEmployeeName"></span></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="modal-loader" class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">جاري تحميل السجل اليومي...</p>
        </div>
        <div id="modal-content-area" class="d-none">
            <div class="table-responsive">
                <table class="table table-sm table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>اليوم</th>
                            <th>وقت الدخول</th>
                            <th>وقت الخروج</th>
                            <th>الحالة</th>
                            <th>تفاصيل المخالفة</th>
                        </tr>
                    </thead>
                    <tbody id="details-table-body">
                        </tbody>
                </table>
            </div>
        </div>
        <div id="modal-error-area" class="d-none alert alert-danger">
            حدث خطأ أثناء جلب البيانات. الرجاء المحاولة مرة أخرى.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
      </div>
    </div>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

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

<script>
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

    $(document).ready(function() {
        $('.dataTables-example').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "الكل"]],
            layout: {
                topStart: {
                    buttons: [
                        { extend: 'copy', text: '<i class="fa fa-copy"></i> نسخ' },
                        { extend: 'excel', text: '<i class="fa fa-file-excel"></i> إكسل' },
                        { extend: 'pdf', text: '<i class="fa fa-file-pdf"></i> PDF' },
                        { extend: 'print', text: '<i class="fa fa-print"></i> طباعة' },
                        { extend: 'colvis', text: '<i class="fa fa-eye"></i> إظهار/إخفاء الأعمدة' }
                    ]
                }
            },
            language: { "url": "https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json" }
        });
        
        // ============================================
        // START: NEW JAVASCRIPT FOR DETAILS MODAL
        // ============================================
        $('.view-details-btn').on('click', function() {
            var empId = $(this).data('empid');
            var empName = $(this).data('empname');
            var sheetId = $(this).data('sheetid');

            // Reset modal state
            $('#modalEmployeeName').text(empName);
            $('#details-table-body').empty();
            $('#modal-loader').removeClass('d-none');
            $('#modal-content-area').addClass('d-none');
            $('#modal-error-area').addClass('d-none');

            // AJAX call to get details
            $.ajax({
                url: "<?php echo site_url('users1/get_employee_violation_details'); ?>",
                type: 'POST',
                data: {
                    emp_id: empId,
                    sheet_id: sheetId
                },
                dataType: 'json',
                success: function(response) {
                    $('#modal-loader').addClass('d-none');
                    if (response.status === 'success' && response.data) {
                        populateDetailsTable(response.data);
                        $('#modal-content-area').removeClass('d-none');
                    } else {
                        $('#modal-error-area').text(response.message || 'فشل في جلب البيانات.').removeClass('d-none');
                    }
                },
                error: function() {
                    $('#modal-loader').addClass('d-none');
                    $('#modal-error-area').removeClass('d-none');
                }
            });
        });

        function populateDetailsTable(data) {
            var tableBody = $('#details-table-body');
            if (data.length === 0) {
                tableBody.append('<tr><td colspan="6" class="text-center">لا توجد سجلات حضور لهذه الفترة.</td></tr>');
                return;
            }
            $.each(data, function(index, row) {
                let statusBadge = '';
                if (row.status === 'غياب') {
                    statusBadge = '<span class="status-badge status-absent">غياب</span>';
                } else if (row.status === 'حاضر') {
                    statusBadge = '<span class="status-badge status-present">حاضر</span>';
                } else if (row.status === 'إجازة') {
                    statusBadge = '<span class="status-badge status-vacation">إجازة</span>';
                } else if (row.status === 'عطلة نهاية الأسبوع') {
                    statusBadge = '<span class="status-badge status-weekend">عطلة</span>';
                } else { // بصمة منفردة
                     statusBadge = '<span class="status-badge status-single">' + row.status + '</span>';
                }

                let violationHtml = row.violation_details.length > 0 ? row.violation_details.join('<br>') : '—';

                var newRow = '<tr>' +
                    '<td>' + row.date + '</td>' +
                    '<td>' + row.day_name + '</td>' +
                    '<td>' + row.check_in + '</td>' +
                    '<td>' + row.check_out + '</td>' +
                    '<td>' + statusBadge + '</td>' +
                    '<td>' + violationHtml + '</td>' +
                    '</tr>';
                tableBody.append(newRow);
            });
        }
        // ============================================
        // END: NEW JAVASCRIPT FOR DETAILS MODAL
        // ============================================
    });
</script>

</body>
</html>