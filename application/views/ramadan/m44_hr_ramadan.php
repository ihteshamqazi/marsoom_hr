<?php
// This line is added to ensure $id is defined, as it's used in the form_open
$id = $this->uri->segment(3, 0); 
?>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> الحضور والانصراف </title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.css">

    <style>
        :root {
            --primary-color: #4a69bd;
            --secondary-color: #F29840;
            --background-color: #f8f9fa;
            --card-bg-color: #ffffff;
            --text-color: #343a40;
            --header-text-color: #ffffff;
            
            /* Status Colors */
            --success-bg: #d1e7dd;
            --success-text: #0f5132;
            --warning-bg: #fff3cd;
            --warning-text: #664d03;
            --danger-bg: #f8d7da;
            --danger-text: #842029;
            --info-bg: #cff4fc;
            --info-text: #055160;
        }
        .company-filter {
    margin: 15px 0;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

.company-filter label {
    font-weight: bold;
    margin-left: 10px;
    color: #495057;
}

.filter-checkbox {
    margin: 0 8px;
}
/* Special Style for New Year Holiday */
.new-year-holiday {
    background-color: #fff3cd !important; /* Yellowish */
    color: #856404;
    font-weight: bold;
    border: 1px solid #ffeeba;
}
.filter-checkbox input {
    margin-left: 5px;
}
        .top-actions a{background:rgba(255,255,255,.12);border:1px solid var(--glass-border);color:#000;text-decoration:none;border-radius:10px;padding:8px 14px;display:inline-flex;align-items:center;gap:8px;transition:.25s}
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            overflow: hidden; /* Hide scrollbars during loading */
        }
        
        /* --- Loading Screen --- */
        #loading-screen {
            position: fixed;
            width: 100%;
            height: 100%;
            background-color: #fff;
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
            border: 5px solid #f3f3f3;
            border-top: 5px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }

        #loading-screen h3 {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 22px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .public-holiday {
            background-color: #e8f4fd !important;
            color: #0c63e4;
            font-weight: 500;
        }
        /* Style for Mandate/Business Trip */
        .on-mandate {
            background-color: #e3f2fd !important; /* Light blue */
            color: #1565c0;
            font-weight: 500;
            border: 1px solid #bbdefb;
        }
        .main-container {
            padding: 30px 15px;
            visibility: hidden; /* Hide content initially */
            opacity: 0;
            transition: opacity 0.5s ease-in;
        }

        .page-title {
            font-weight: 800;
            font-size: 2.5rem;
            color: #0E1F3B;
            margin-bottom: 40px;
            text-align: center;
            animation: fadeInDown 1s ease-out;
            position: relative;
            display: inline-block;
            padding-bottom: 10px;
        }

        .page-title::after {
            content: '';
            position: absolute;
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 2px;
        }

        .table-card {
            background-color: var(--card-bg-color);
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 25px;
            animation: fadeInUp 1s ease-out 0.3s;
            animation-fill-mode: backwards;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .dataTables-example thead th {
            background-color: var(--primary-color) !important;
            color: var(--header-text-color);
            font-weight: 500;
            text-align: center;
            vertical-align: middle;
            border-bottom: 2px solid #3b5699;
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
        
        /* Staggered animation for table rows */
        <?php for ($i = 0; $i < 20; $i++): ?>
        .dataTables-example tbody tr:nth-child(<?php echo $i + 1; ?>) {
            animation-delay: <?php echo $i * 0.05; ?>s;
        }
        <?php endfor; ?>

        .dataTables-example tbody tr:hover {
            background-color: #f1f5f9;
            transform: scale(1.01);
            transition: transform 0.2s ease-in-out;
        }
        
        /* --- Cell Status Styles --- */
        .on-time {
            background-color: var(--success-bg) !important;
            color: var(--success-text);
            font-weight: 500;
        }
        .late-arrival {
            background-color: var(--warning-bg) !important;
            color: var(--warning-text);
            font-weight: 500;
        }
        .early-departure {
            background-color: var(--danger-bg) !important;
            color: var(--danger-text);
            font-weight: 500;
        }
        .on-vacation {
            background-color: var(--info-bg) !important;
            color: var(--info-text);
            font-weight: 500;
        }
        
        /* ✨ NEW: Style for Saturday work */
        .saturday-work {
            background-color: #fff8e1 !important; /* A light orange/yellow */
            color: #856404;
            font-weight: 500;
        }
        
        td strong {
            font-weight: 500;
        }

        /* DataTables Buttons Styling */
        .dt-buttons .btn {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: var(--header-text-color);
            font-weight: 500;
            margin: 0 2px;
            transition: all 0.3s ease;
        }
        /* Debug tooltip styles */
.tooltip {
    position: absolute;
    z-index: 1070;
    display: block;
    margin: 0;
    font-family: 'Tajawal', sans-serif;
    font-style: normal;
    font-weight: 400;
    line-height: 1.5;
    text-align: right;
    text-decoration: none;
    text-shadow: none;
    text-transform: none;
    letter-spacing: normal;
    word-break: normal;
    white-space: normal;
    word-spacing: normal;
    line-break: auto;
    font-size: 0.875rem;
    word-wrap: break-word;
    opacity: 0;
}

.tooltip.show {
    opacity: 1;
}

.tooltip .tooltip-arrow {
    position: absolute;
    display: block;
    width: 0.8rem;
    height: 0.4rem;
}

.tooltip .tooltip-inner {
    max-width: 200px;
    padding: 0.25rem 0.5rem;
    color: #fff;
    text-align: center;
    background-color: #000;
    border-radius: 0.375rem;
}
        .dt-buttons .btn:hover {
            background-color: #e0882f;
            border-color: #e0882f;
            transform: translateY(-2px);
        }
        
        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select {
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        /* --- Animations --- */
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

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

<div id="loading-screen">
    <div class="loader"></div>
    <h3>جاري تجهيز تقرير الحضور والانصراف ...</h3>
</div>

<div class="main-container container-fluid">
    <div class="text-center">
        <h1 class="page-title"> الحضور والانصراف</h1>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card table-card">
                <div class="card-body">
                    <?php echo validation_errors(); ?>
                    <?php echo form_open_multipart('ramadan/m44_hr_ramadan/'.$id); ?>
                    <div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label fw-bold text-primary"><i class="fas fa-building me-2"></i>اختر الشركة للعرض:</label>
        <select id="attendanceCompanyFilter" class="form-select shadow-sm border-primary">
            <option value="">عرض الكل (جميع الموظفين)</option>
            <option value="مكتب الدكتور">مكتب الدكتور صالح الجربوع</option>
            <option value="شركة مرسوم">شركة مرسوم لتحصيل الديون</option> </select>
    </div>
</div>

                    <div class="table-responsive">
                        <div class="top-actions" style="color:black;">
                            <a href="<?php echo site_url('users1/main_hr1'); ?>" style="text-decoration: none;"><i class="fas fa-home"></i> الرئيسية</a>
                        </div>

                       <table id="attendanceTable" class="table table-striped table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>الرقم الوظيفي</th>
                                    <th>اسم الموظف</th>
                                    <th>رقم الهوية</th>
                                    
                                    <th> المسمى الوظيفي</th>
                                    <th> الادارة</th>
                                    <th>الجنسية</th>
                                    
                                    <th>اسم الشركة</th>
                                    <?php
$start_date_dt = new DateTime($get_salary_sheet['start_date']);
$end_date_dt   = new DateTime($get_salary_sheet['end_date']);
$date_range    = new DatePeriod($start_date_dt, new DateInterval('P1D'), $end_date_dt->modify('+1 day'));

// Create a map of mandatory Saturdays (which dates are work days)
$saturday_work_days = [];
if(isset($data_map['saturday_assignments']) && is_array($data_map['saturday_assignments'])) {
    foreach ($data_map['saturday_assignments'] as $assignment) {
        $saturday_work_days[$assignment['saturday_date']] = true;
    }
}

// Also create a quick lookup for employee-specific Saturday assignments
$employee_saturday_assignments = [];
if(isset($data_map['saturday_assignments']) && is_array($data_map['saturday_assignments'])) {
    foreach ($data_map['saturday_assignments'] as $assignment) {
        $employee_saturday_assignments[$assignment['employee_id']][$assignment['saturday_date']] = true;
    }
}

foreach ($date_range as $date) {
    $day_of_week = $date->format('N');
    $date_str = $date->format('Y-m-d');
    
    // Display the column if:
    // - It's not Friday (5) AND
    // - It's either not a Saturday (not 6) OR it IS a Saturday that is mandatory for at least one person
    if ($day_of_week != 5 && ($day_of_week != 6 || isset($saturday_work_days[$date_str]))) {
        echo "<th>" . $date->format('d/m/Y') . "</th>";
    }
}
?>
                                    <th>ايام الغياب</th>
                                    <th>إجمالي الدقائق المتأخرة</th>
                                    <th>إجمالي الدقائق المبكرة</th>
                                    <th>أيام بصمة منفردة</th>
                                    
                                </tr>
                            </thead>
                           <tbody> 
                                <?php foreach ($employees as $row): ?>
                                    
                                    <tr>
                                        <td><?php echo $row->employee_id; ?></td>
                                        <td><?php echo $row->subscriber_name; ?></td>
                                        <td><?php echo $row->id_number; ?></td>
                                        
                                         <td><?php echo $row->profession; ?></td>
                                          <td><?php echo $row->n1; ?></td>
                                        <td><?php echo $row->nationality; ?></td>
                                        
                                        <td><?php echo $row->company_name; ?></td>

<?php
// ##################################################################
// ### START OF CORRECTED PHP LOGIC BLOCK FOR TBODY ###
// ##################################################################

$start_date = strtotime($get_salary_sheet['start_date']);
$end_date   = strtotime($get_salary_sheet['end_date']);
$emp_code   = $row->employee_id;
$joining_date_timestamp = null;
if (!empty($row->joining_date)) { //
    $joining_date_timestamp = strtotime($row->joining_date);
}
$is_exempt = isset($data_map['exemptions'][$emp_code]);
// Load data from the corrected data_map structure
$leave_requests_data = $data_map['leave_requests'][$emp_code] ?? [];
$fingerprint_corrections_data = $data_map['fp_corrections'][$emp_code] ?? [];
$emp_sat_assign = $employee_saturday_assignments[$emp_code] ?? [];

// Get the employee's default rules ONCE
$rule_row = $data_map['rules'][$row->employee_id] ?? null;
$default_working_hours = 9.0;
if ($rule_row && isset($rule_row->working_hours) && $rule_row->working_hours !== '') {
    $wh_raw = trim((string)$rule_row->working_hours);
    if (strpos($wh_raw, ':') !== false) {
        $parts = array_map('intval', explode(':', $wh_raw));
        $h = $parts[0] ?? 0; $m = $parts[1] ?? 0; $s = $parts[2] ?? 0;
        $default_working_hours = $h + ($m/60) + ($s/3600);
    } else {
        $default_working_hours = floatval($wh_raw);
    }
    if ($default_working_hours <= 0) { $default_working_hours = 9.0; }
}
$default_start_time = '08:00:00';
if ($rule_row && !empty($rule_row->first_punch)) {
    $default_start_time = $rule_row->first_punch;
}
$default_lateness_threshold = '08:30:00';
if ($rule_row && !empty($rule_row->last_punch)) {
    $default_lateness_threshold = $rule_row->last_punch;
}
$default_daily_salary_divisor = max(0.01, $default_working_hours); // Divisor for normal days
$saturday_daily_salary_divisor = max(0.01, 6.0); // Divisor for Saturdays

// Initialize totals for the employee row
$absence_days = 0; 
$total_late_minutes = 0; 
$total_early_exit_minutes = 0; 
$single_punch_days = 0; 

$daily_salary = 0;
if (is_numeric($row->total_salary) && $row->total_salary > 0) {
    $daily_salary = (float)$row->total_salary / 30;
}
$total_absence_deduction      = 0; 
$total_single_punch_deduction = 0; 
$total_late_exit_deduction    = 0; 

// Loop through all dates in the payroll period
for ($date = $start_date; $date <= $end_date; $date = strtotime("+1 day", $date)) {

    $date_str = date('Y-m-d', $date);
    $day_of_week = date('N', $date); // 1=Mon, 5=Fri, 6=Sat

    // Check if this is a mandatory Saturday for THIS specific employee
    $is_mandatory_saturday = ($day_of_week == 6) && isset($emp_sat_assign[$date_str]);

    // Check if this day should be displayed in the report
    $is_report_day = ($day_of_week != 5 && ($day_of_week != 6 || isset($saturday_work_days[$date_str])));

    if (!$is_report_day) {
        continue;
    }
    
    // FIX: If it's a Saturday header, but NOT mandatory for THIS employee, show it as a weekend
    if ($day_of_week == 6 && !$is_mandatory_saturday) {
        echo "<td class='on-vacation' title='إجازة نهاية أسبوع' data-bs-toggle='tooltip' data-bs-placement='top'><strong>نهاية أسبوع</strong></td>";
        continue; // Skip to next date
    }

    // Reset day variables
    $first_punch = null;
    $last_punch  = null;
    $violation_class = '';
    $message = '';
    $is_present = false;
    $late_time_minutes_today = 0;
    $early_exit_minutes_today = 0;
    $is_on_correction = false;
    $correction_applied = false;
    $corrected_first_punch = null;
    $corrected_last_punch = null;

    // === CHECK FOR PUBLIC HOLIDAY FIRST ===
    $is_public_holiday = isset($data_map['public_holidays']) && in_array($date_str, $data_map['public_holidays']);
    
    if ($is_public_holiday) {
        echo "<td class='on-vacation' title='عطلة رسمية' data-bs-toggle='tooltip' data-bs-placement='top'><strong>عطلة رسمية</strong></td>";
        continue; // Skip to next date
    }

    // ### NEW CODE BLOCK (Check Joining Date) ###
    if ($joining_date_timestamp && $date < $joining_date_timestamp) {
        // This date is BEFORE the employee's joining date.
        // Mark as 'N/A' and skip all violation calculations.
        echo "<td style='background-color: #f0f0f0; color: #aaa;' title='قبل تاريخ التعيين' data-bs-toggle='tooltip' data-bs-placement='top'>-</td>";
        continue; // Go to the next date
    }

    if ($is_exempt) {
        // This employee is exempt from attendance deductions.
        // Mark as 'Exempt' and skip all violation calculations.
        echo "<td class='on-time' title='معفى من البصمة' data-bs-toggle='tooltip' data-bs-placement='top'><strong>معفى</strong></td>";
        continue; // Go to the next date
    }

    // =================================================================
    // [START] NEW YEAR HOLIDAY CHECK
    // =================================================================
    // Check if date is Jan 1st
    if (date('m-d', $date) == '01-01') {
        // Check if this employee has New Year holiday status in the database
        if (isset($new_year_holiday_data[$emp_code])) {
            if ($new_year_holiday_data[$emp_code] == 1) {
                // Employee has holiday on New Year
                echo "<td class='new-year-holiday' title='إجازة رأس السنة (New Year)' data-bs-toggle='tooltip' data-bs-placement='top'>
                        <i class='fas fa-glass-cheers'></i> إجازة سنة
                       </td>";
                continue; // Skip calculating absence for this day
            } else {
                // Employee works on New Year - show as normal work day
                // Let the normal logic continue (no echo, no continue)
            }
        } else {
            // No record found in new_year_holiday table - treat as normal day
            // Let the normal logic continue
        }
    }
    // =================================================================
    // [END] NEW YEAR HOLIDAY CHECK
    // =================================================================

    // === MANDATE REQUEST CHECKING ===
    $is_on_mandate = false;
    $mandate_info = '';
    if (isset($data_map['mandate_requests'][$emp_code])) {
        foreach ($data_map['mandate_requests'][$emp_code] as $mandate) {
            $mandate_start = !empty($mandate['start_date']) ? strtotime($mandate['start_date']) : null;
            $mandate_end = !empty($mandate['end_date']) ? strtotime($mandate['end_date']) : null;
            
            if ($mandate_start && $mandate_end) {
                $current_date_midnight = strtotime(date('Y-m-d', $date));
                if ($current_date_midnight >= strtotime(date('Y-m-d', $mandate_start)) && 
                    $current_date_midnight <= strtotime(date('Y-m-d', $mandate_end))) {
                    $is_on_mandate = true;
                    $mandate_info = date('d/m/Y', $mandate_start) . ' - ' . date('d/m/Y', $mandate_end);
                    break;
                }
            }
        }
    }

    // === APPLY MANDATE STATUS ===
    if ($is_on_mandate) {
        echo "<td class='on-mandate' title='إنتداب' data-bs-toggle='tooltip' data-bs-placement='top'>
            <strong> إنتداب</strong>
        </td>";
        continue; // Skip all other calculations for this date
    }

    // === VACATION CHECKING ===
    // === VACATION CHECKING ===
    $is_on_vacation = false;
    $is_half_day_vacation = false;
    $vacation_type = '';
    
    foreach ($leave_requests_data as $leave) {
        $vacation_start = !empty($leave['vac_start']) ? strtotime($leave['vac_start']) : null;
        $vacation_end = !empty($leave['vac_end']) ? strtotime($leave['vac_end']) : null;
        if ($vacation_start && $vacation_end) {
            $current_date_midnight = strtotime(date('Y-m-d', $date));
            if ($current_date_midnight >= strtotime(date('Y-m-d', $vacation_start)) && $current_date_midnight <= strtotime(date('Y-m-d', $vacation_end))) {
                
                $v_type = $leave['vac_main_type'] ?? 'إجازة';
                
                // Detect if it's a Half-Day Vacation (نصف يوم)
                if (strpos($v_type, 'نصف') !== false || (isset($leave['vac_type']) && strpos($leave['vac_type'], 'نصف') !== false)) {
                    $is_half_day_vacation = true;
                    $vacation_type = 'إجازة نصف يوم';
                } else {
                    $is_on_vacation = true;
                    $vacation_type = $v_type;
                }
                break;
            }
        }
    }

    // === FINGERPRINT CORRECTION CHECKING ===
    if (!$is_on_vacation && !empty($fingerprint_corrections_data)) {
        foreach ($fingerprint_corrections_data as $corr) {
            if (empty($corr['correction_date'])) continue;
            if (strtotime($corr['correction_date']) == strtotime(date('Y-m-d', $date))) {
                $is_on_correction = true;
                
                // ✨ GET CORRECTED TIMES FROM DATABASE (USING CORRECT COLUMN NAMES) ✨
                if (!empty($corr['attendance_correction'])) {
                    $corrected_first_punch = strtotime($date_str . ' ' . $corr['attendance_correction']);
                }
                if (!empty($corr['correction_of_departure'])) {
                    $corrected_last_punch = strtotime($date_str . ' ' . $corr['correction_of_departure']);
                }
                $correction_applied = true;
                break;
            }
        }
    }

    // === APPLY VACATION/CORRECTION STATUS ===
    if ($is_on_vacation) {
        $violation_class = 'on-vacation'; 
        $message = 'إجازة: ' . $vacation_type; 
        $display_first_punch = '';
        $display_last_punch  = '';
    } else {
        // ✨ SIMPLIFIED SATURDAY LOGIC: Only check for presence, not duration ✨
        if ($is_mandatory_saturday) {
            // For Saturday: Just check if employee has any valid fingerprint
            $has_saturday_attendance = false;
            $saturday_first_punch = null;
            $saturday_last_punch = null;

            foreach ($attendance_data as $attendance) {
                if ($attendance->emp_code == $emp_code && $attendance->punch_date == $date_str) {
                    $raw_first = !empty($attendance->first_punch) ? strtotime($attendance->first_punch) : null;
                    $raw_last  = !empty($attendance->last_punch)  ? strtotime($attendance->last_punch)  : null;

                    // For Saturday: Any valid punch before cutoff counts as attendance
                    if ($raw_first && $raw_first < strtotime($date_str . ' 16:00:00')) {
                        $has_saturday_attendance = true;
                        if ($saturday_first_punch === null || $raw_first < $saturday_first_punch) {
                            $saturday_first_punch = $raw_first;
                        }
                    }
                    // Any valid punch in the afternoon window counts
                    if ($raw_last && $raw_last >= strtotime($date_str . ' 14:00:00') && $raw_last <= strtotime($date_str . ' 21:00:00')) {
                        $has_saturday_attendance = true;
                        if ($saturday_last_punch === null || $raw_last > $saturday_last_punch) {
                            $saturday_last_punch = $raw_last;
                        }
                    }
                }
            }

            $first_punch = $saturday_first_punch;
            $last_punch  = $saturday_last_punch;
            $is_present  = $has_saturday_attendance;
            $display_first_punch = '';
            $display_last_punch  = '';

            if (!$is_present) {
                // No fingerprint on mandatory Saturday = ABSENCE
                $violation_class = 'early-departure';
                $message = 'غياب (سبت عمل إلزامي)';
                if (!$is_on_correction) {
                    $absence_days++; 
                    $total_absence_deduction += $daily_salary; 
                } else {
                    $violation_class = 'on-time';
                    $message .= ' (مستثنى بتصحيح بصمة)';
                }
            } else {
                // Check for single punch on Saturday
                if (empty($first_punch) || empty($last_punch) || (($last_punch - $first_punch) < 60)) {
                    $violation_class = 'early-departure';
                    $message = 'بصمة منفردة (سبت عمل)';
                    if (!$is_on_correction) {
                        $single_punch_days++;
                        $total_single_punch_deduction += $daily_salary;
                    } else {
                        $violation_class = 'on-time';
                        $message .= ' (مستثنى بتصحيح بصمة)';
                    }
                    if ($first_punch) { $display_first_punch = date('H:i', $first_punch); }
                    if ($last_punch)  { $display_last_punch  = date('H:i', $last_punch);  }
                } else {
                    // Has valid fingerprint on Saturday = PRESENT (no duration checks)
                    $violation_class = 'saturday-work';
                    $message = 'حاضر - سبت عمل';
                    
                    // Show actual punch times
                    $display_first_punch = $first_punch ? date('H:i', $first_punch) : '--';
                    $display_last_punch  = $last_punch ? date('H:i', $last_punch) : '--';
                    
                    // No late/early calculations for Saturday
                    $late_time_minutes_today = 0;
                    $early_exit_minutes_today = 0;
                }
            }
        } else {
           // ==========================================================
            // RAMADAN WEEKDAY LOGIC (Flexible & Split Shifts)
            // ==========================================================
            // Check if they have the 8-hour breastfeeding shift in the database
$is_breastfeeding_shift = ($rule_row && isset($rule_row->working_hours) && (float)$rule_row->working_hours == 8.0);
            $base_ramadan_hours = $is_breastfeeding_shift ? 5.0 : 6.0;
            
            // If they have a half-day vacation, requirement drops to half!
            $working_hours_for_day = $is_half_day_vacation ? ($base_ramadan_hours / 2) : $base_ramadan_hours;
            $daily_salary_divisor = $base_ramadan_hours;

            // Because shifts go past midnight, we need tomorrow's date
            $next_day_str = date('Y-m-d', strtotime("+1 day", $date));

            // 1. Gather all potential raw punches from today AND tomorrow morning
            $possible_punches = [];
            foreach ($attendance_data as $attendance) {
                if ($attendance->emp_code == $emp_code) {
                    if ($attendance->punch_date == $date_str || $attendance->punch_date == $next_day_str) {
                        if (!empty($attendance->first_punch)) $possible_punches[] = strtotime($attendance->first_punch);
                        if (!empty($attendance->last_punch)) $possible_punches[] = strtotime($attendance->last_punch);
                    }
                }
            }
            
            // Add any HR manual corrections
            if ($correction_applied) {
                if ($corrected_first_punch) $possible_punches[] = $corrected_first_punch;
                if ($corrected_last_punch) $possible_punches[] = $corrected_last_punch;
            }

            $possible_punches = array_unique($possible_punches);
            sort($possible_punches);

            $worked_hours = 0;
            $first_punch_display = null;
            $last_punch_display = null;
            $is_present = false;

            // ==========================================================
            // SHIFT LOGIC: COLLECTORS vs REGULAR
            // ==========================================================
            if ($is_collector) {
                // Shift 1: 1 PM to 5 PM
                $s1_start = strtotime($date_str . ' 13:00:00');
                $s1_end   = strtotime($date_str . ' 17:00:00');
                // Shift 2: 8 PM to 1 AM
                $s2_start = strtotime($date_str . ' 20:00:00');
                $s2_end   = strtotime($next_day_str . ' 01:00:00');

                $s1_punches = []; $s2_punches = [];
                foreach ($possible_punches as $p) {
                    // Wide net to catch punches slightly outside the exact window
                    if ($p >= strtotime($date_str . ' 10:00:00') && $p <= strtotime($date_str . ' 18:00:00')) $s1_punches[] = $p;
                    if ($p >= strtotime($date_str . ' 18:00:01') && $p <= strtotime($next_day_str . ' 05:00:00')) $s2_punches[] = $p;
                }

                $worked_s1 = 0;
                if (count($s1_punches) >= 2) {
                    $eff_start1 = max(min($s1_punches), $s1_start);
                    $eff_end1   = min(max($s1_punches), $s1_end);
                    $worked_s1  = max(0, $eff_end1 - $eff_start1) / 3600;
                    $is_present = true;
                    $first_punch_display = min($s1_punches);
                    $last_punch_display = max($s1_punches);
                }

                $worked_s2 = 0;
                if (count($s2_punches) >= 2) {
                    $eff_start2 = max(min($s2_punches), $s2_start);
                    $eff_end2   = min(max($s2_punches), $s2_end);
                    $worked_s2  = max(0, $eff_end2 - $eff_start2) / 3600;
                    $is_present = true;
                    if (!$first_punch_display) $first_punch_display = min($s2_punches);
                    $last_punch_display = max($s2_punches);
                }

                // Capping shifts (Max 2 hrs afternoon, Max 4 hrs night)
                $worked_hours = min($worked_s1, 2.0) + min($worked_s2, 4.0);

            } else {
                // Standard Employees: 9 AM to 2 AM
                $shift_start = strtotime($date_str . ' 09:00:00');
                $shift_end   = strtotime($next_day_str . ' 02:00:00');

                $shift_punches = [];
                foreach ($possible_punches as $p) {
                    // Only count punches belonging to this physical shift timeframe
                    if ($p >= strtotime($date_str . ' 06:00:00') && $p <= strtotime($next_day_str . ' 05:00:00')) {
                        $shift_punches[] = $p;
                    }
                }

                if (count($shift_punches) >= 2) {
                    $is_present = true;
                    $first_punch_display = min($shift_punches);
                    $last_punch_display  = max($shift_punches);

                    // Cap hours exactly within the 9 AM - 2 AM legal window
                    $eff_start = max($first_punch_display, $shift_start);
                    $eff_end   = min($last_punch_display, $shift_end);

                    $worked_hours = max(0, $eff_end - $eff_start) / 3600;
                } elseif (count($shift_punches) == 1) {
                    $is_present = true;
                    $first_punch_display = min($shift_punches);
                    $last_punch_display = null; 
                }
            }

            // ==========================================================
            // PENALTY CALCULATION
            // ==========================================================
            $display_first_punch = $first_punch_display ? date('H:i', $first_punch_display) : '--';
            $display_last_punch  = $last_punch_display ? date('H:i', $last_punch_display) : '--';

            $messages_today = [];
            $class_tags     = [];
            $should_exclude_from_totals = $is_on_correction && $corrected_first_punch && $corrected_last_punch;

            if (!$is_present) {
                $violation_class = 'early-departure';
                $message = 'غياب';
                if (!$is_on_correction) {
                    $absence_days++; 
                    $total_absence_deduction += $daily_salary; 
                } else {
                    $violation_class = 'on-time';
                    $message .= ' (مستثنى بتصحيح بصمة)';
                }
            } elseif ($is_present && (!$first_punch_display || !$last_punch_display || ($last_punch_display - $first_punch_display) < 60)) {
                $violation_class = 'early-departure';
                $message = 'بصمة منفردة';
                if (!$is_on_correction) {
                    $single_punch_days++;
                    $total_single_punch_deduction += $daily_salary;
                } else {
                    $violation_class = 'on-time';
                    $message .= ' (مستثنى بتصحيح بصمة)';
                }
           } else {
                // Subtract worked hours from their specific requirement (6 or 3)
                $shortage_hours = $working_hours_for_day - $worked_hours;
                
                if ($shortage_hours > 0.033) { // 0.033 hours = 2 minutes tolerance
                    $shortage_minutes = round($shortage_hours * 60);
                    $late_time_minutes_today = $shortage_minutes; 

                    if (!$should_exclude_from_totals) {
                        $total_late_minutes += $shortage_minutes;
                        // Use their specific divisor (5.0 or 6.0) to calculate the minute penalty cost
                        $total_late_exit_deduction += ($shortage_minutes * $daily_salary / $daily_salary_divisor / 60);
                    }
                    
                    $messages_today[] = "نقص في ساعات العمل: $shortage_minutes دقيقة";
                    $class_tags[] = 'late-arrival';
                } else {
                    $class_tags[] = 'on-time';
                    $messages_today[] = 'ملتزم بالوقت المطلوب (' . $working_hours_for_day . ' ساعات)';
                }

                // Add visual indicator for the Half-Day
                if ($is_half_day_vacation) {
                    $messages_today[] = '(إجازة نصف يوم مخصومة من المطلوب)';
                    $class_tags[] = 'on-vacation'; // Gives the cell a blue tint to highlight it for HR
                }

                if ($is_on_correction) {
                    if ($should_exclude_from_totals) {
                        $messages_today[] = '(تم التصحيح الكامل - مستثنى)';
                    } else {
                        $messages_today[] = '(تم التصحيح الجزئي)';
                    }
                }

               $violation_class = implode(' ', array_unique($class_tags));
                $message = implode(' • ', $messages_today);
            }
        } // Close Saturday Logic Else
    } // Close Vacation Logic Else

    // 👉 PRINT THE TABLE CELL FOR THIS DAY
    // 👉 PRINT THE TABLE CELL FOR THIS DAY
    if ($is_collector) {
        // Find specific Start/End for Shift 1 (Remote) and Shift 2 (Onsite) for display
        $s1_in = (!empty($s1_punches)) ? date('H:i', min($s1_punches)) : '--';
        $s1_out = (count($s1_punches) >= 2) ? date('H:i', max($s1_punches)) : '--';
        
        $s2_in = (!empty($s2_punches)) ? date('H:i', min($s2_punches)) : '--';
        $s2_out = (count($s2_punches) >= 2) ? date('H:i', max($s2_punches)) : '--';

        echo "<td class='$violation_class' title=\"$message\" data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" style='min-width: 140px; font-size: 12px; padding: 5px;'>
                <div style='background:rgba(13, 110, 253, 0.1); border-radius:4px; padding:2px; margin-bottom:4px; border:1px solid rgba(13, 110, 253, 0.2);'>
                    <span class='badge bg-primary w-100 mb-1' style='font-size:10px;'>عن بعد (1م - 5م)</span><br>
                    <span class='text-dark'><strong>د:</strong> $s1_in | <strong>خ:</strong> $s1_out</span>
                </div>
                <div style='background:rgba(25, 135, 84, 0.1); border-radius:4px; padding:2px; border:1px solid rgba(25, 135, 84, 0.2);'>
                    <span class='badge bg-success w-100 mb-1' style='font-size:10px;'>حضوري (8م - 1ص)</span><br>
                    <span class='text-dark'><strong>د:</strong> $s2_in | <strong>خ:</strong> $s2_out</span>
                </div>
              </td>";
    } else {
        // Standard Employee Display
        echo "<td class='$violation_class' title=\"$message\" data-bs-toggle=\"tooltip\" data-bs-placement=\"top\">
            <strong>دخول:</strong> " . (!empty($display_first_punch) ? $display_first_punch : '--') . " <br>
            <strong>خروج:</strong> " . (!empty($display_last_punch) ? $display_last_punch : '--') . "
          </td>";
    }


} // 👉 END OF THE MAIN FOR LOOP

// ##################################################################
// ### END OF CORRECTED PHP LOGIC BLOCK ###
// ##################################################################
?>


                                        
                                        <td><strong><?php echo $absence_days; ?></strong></td>
                                        <td><strong><?php echo $total_late_minutes; ?></strong></td>
                                        <td><strong><?php echo $total_early_exit_minutes; ?></strong></td>
                                        <td><strong><?php echo $single_punch_days; ?></strong></td>
                                        <!-- Temporary test button - remove after testing -->

                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="pendingRequestsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered"> 
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-circle"></i> طلبات معلقة (غير معتمدة)
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="pending-loading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">جاري البحث عن طلبات معلقة...</p>
                </div>
                
                <div id="pending-content" style="display:none;">
                    <div class="alert alert-warning">
                        <strong>تنبيه:</strong> هذه الطلبات تقع ضمن فترة المسير الحالي (<span id="modal-period-span"></span>) ولم يتم اعتمادها (Status 0 or 1).
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="pendingTable">
                            <thead class="table-light">
                                <tr>
                                    <th>رقم الطلب</th>
                                    <th>الموظف</th>
                                    <th>النوع</th>
                                    <th>التاريخ المعني</th>
                                    <th>عند من (الموافق الحالي)</th> <th>الحالة</th>
                                    <th>اجراء</th>
                                </tr>
                            </thead>
                            <tbody id="pending-table-body">
                                </tbody>
                        </table>
                    </div>
                </div>
                
                <div id="pending-empty" class="text-center py-4" style="display:none;">
                    <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                    <h5>لا توجد طلبات معلقة لهذه الفترة</h5>
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
<!-- Replace these CDN links in your head section -->
<script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pdfmake@0.2.7/build/pdfmake.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pdfmake@0.2.7/build/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.colVis.min.js"></script>
<script>
$(document).ready(function () {
  // خذ رقم المسير من الرابط
  var id_sheet = <?php echo json_encode($this->uri->segment(3,0)); ?>;

  // منع إعادة تهيئة الداتا تيبل
  var dt;
  if ($.fn.DataTable.isDataTable('#attendanceTable')) {
    dt = $('#attendanceTable').DataTable(); // استخدم الموجود إن كان مُهيأ
  } else {
    dt = $('#attendanceTable').DataTable({
      responsive: true,
      pageLength: 10,
      deferRender: true,
      layout: {
        topStart: {
          buttons: [
            { extend: 'copy',  text: '<i class="fa fa-copy"></i> نسخ' },
            { extend: 'excel', text: '<i class="fa fa-file-excel"></i> إكسل' },
            { extend: 'pdf',   text: '<i class="fa fa-file-pdf"></i> PDF' },
            { extend: 'print', text: '<i class="fa fa-print"></i> طباعة' },
            { extend: 'colvis',text: '<i class="fa fa-eye"></i> إظهار/إخفاء الأعمدة' },
{
                text: '<i class="fas fa-bell"></i> طلبات معلقة',
                className: 'btn btn-danger', // Red button to catch attention
                action: function (e, dt, node, config) {
                    // 1. Show the Modal created in Part A
                    var myModal = new bootstrap.Modal(document.getElementById('pendingRequestsModal'));
                    myModal.show();
                    
                    // 2. Trigger the AJAX fetch function created in Part C
                    fetchPendingRequests();
                }
            },
            // ============================================================
            // END: NEW PENDING REQUESTS BUTTON
            // ============================================================
            // زر الترحيل
            {
              text: '<i class="fa fa-upload"></i> ترحيل المخالفات',
              className: 'btn btn-warning',
              action: function () {
                if (!confirm('هل أنت متأكد من ترحيل مخالفات الحضور والانصراف الخاص بالمسير؟')) return;

                // تجميع البيانات من الصفوف الظاهرة بعد الفلترة
                var payload = [];
                dt.rows({ search: 'applied' }).every(function () {
                  var $row   = $(this.node());
                  var $cells = $row.find('td');
                  if (!$cells.length) return;

                  // ترتيب الأعمدة الثابتة في البداية:
                  // 0: الرقم الوظيفي, 1: اسم الموظف
                  var emp_id   = ($cells.eq(0).text() || '').trim();
                  var emp_name = ($cells.eq(1).text() || '').trim();

                  // آخر 4 أعمدة في الصف هي ملخصاتنا:
                  // [ايام الغياب, إجمالي الدقائق المتأخرة, إجمالي الدقائق المبكرة, أيام بصمة منفردة]
                  var lastIdx           = $cells.length - 1;
                  var single_thing_txt  = ($cells.eq(lastIdx).text() || '').replace(/[^\d]/g,'');
                  var minutes_early_txt = ($cells.eq(lastIdx - 1).text() || '').replace(/[^\d]/g,'');
                  var minutes_late_txt  = ($cells.eq(lastIdx - 2).text() || '').replace(/[^\d]/g,'');
                  var absence_txt       = ($cells.eq(lastIdx - 3).text() || '').replace(/[^\d]/g,'');

                  if (emp_id) {
                    payload.push({
                      emp_id: emp_id,
                      emp_name: emp_name,
                      absence:       parseInt(absence_txt       || '0', 10),
                      minutes_late:  parseInt(minutes_late_txt  || '0', 10),
                      minutes_early: parseInt(minutes_early_txt || '0', 10),
                      single_thing:  parseInt(single_thing_txt  || '0', 10)
                    });
                  }
                });

                if (!payload.length) { alert('لا توجد صفوف صالحة للترحيل.'); return; }

                // استدعاء الحفظ: يحذف أولاً ثم يُدخل الدفعة الجديدة لنفس id_sheet
                $.ajax({
                  url: '<?php echo site_url("users1/save_attendance_summary/"); ?>' + id_sheet,
                  type: 'POST',
                  data: JSON.stringify({ rows: payload }),
                  contentType: 'application/json; charset=utf-8',
                  dataType: 'json',
                  beforeSend: function () {
                    // تعطيل الزر مؤقتاً
                    $('.dt-buttons .btn:contains("ترحيل المخالفات")')
                      .prop('disabled', true)
                      .append(' <span class="spinner-border spinner-border-sm"></span>');
                  },
                  success: function (res) {
                    if (res && res.status === 'ok') {
                      alert(
                        'تم الترحيل بنجاح.\n' +
                        'المحذوف: ' + (res.deleted_rows || 0) + '\n' +
                        'المضاف: '  + (res.inserted_rows || 0)
                      );
                    } else {
                     alert('حدث خطأ أثناء الترحيل: ' + (res && res.msg ? JSON.stringify(res.msg) : 'غير معروف'));
                    }
                  },
                  error: function () {
                     alert('تعذر الاتصال بالخادم. تأكد من الرابط والصلاحيات.');
                  },
                  complete: function () {
                     $('.dt-buttons .btn:contains("ترحيل المخالفات")')
                       .prop('disabled', false)
                       .find('.spinner-border').remove();
                  }
                });
              }
            }
          ]
        }
      },
      language: {
        url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json'
      }
    });
  }
function fetchPendingRequests() {
    var id_sheet = <?php echo json_encode($id); ?>;
    
    $('#pending-loading').show();
    $('#pending-content').hide();
    $('#pending-empty').hide();
    $('#pending-table-body').empty();

    $.ajax({
        url: '<?php echo site_url("users1/ajax_check_pending_requests"); ?>',
        type: 'POST',
        data: {
            '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
            'sheet_id': id_sheet
        },
        dataType: 'json',
        success: function(response) {
            $('#pending-loading').hide();

            if (response.status === 'success') {
                $('#modal-period-span').text(response.period);

                if (response.count > 0) {
                    var rows = '';
                    $.each(response.data, function(index, item) {
                        
                        // Date Logic
                        var displayDate = item.submission_date;
                        if(item.vac_start) displayDate = item.vac_start + ' <br>إلى<br> ' + item.vac_end;
                        else if(item.correction_date) displayDate = item.correction_date;
                        else if(item.ot_date) displayDate = item.ot_date;
                        else if(item.date_of_the_last_working) displayDate = item.date_of_the_last_working;

                        // Approver Name (Handle null)
                        var approverName = item.current_approver ? item.current_approver : '<span class="text-muted">غير محدد</span>';

                        var statusBadge = item.status == '0' 
                            ? '<span class="badge bg-warning text-dark">انتظار</span>' 
                            : '<span class="badge bg-info">تحت الاجراء</span>';

                        var link = '<?php echo site_url("users1/view_request/"); ?>' + item.id;

                        rows += '<tr>';
                        rows += '<td>' + item.id + '</td>';
                        rows += '<td>' + item.emp_name + '</td>';
                        rows += '<td>' + item.order_name + '</td>';
                        rows += '<td><span dir="ltr">' + displayDate + '</span></td>';
                        rows += '<td class="fw-bold text-danger">' + approverName + '</td>'; // New Column Data
                        rows += '<td>' + statusBadge + '</td>';
                        rows += '<td><a href="' + link + '" target="_blank" class="btn btn-sm btn-primary">عرض</a></td>';
                        rows += '</tr>';
                    });

                    $('#pending-table-body').html(rows);
                    $('#pending-content').show();
                } else {
                    $('#pending-empty').show();
                }
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            $('#pending-loading').hide();
            alert('حدث خطأ في الاتصال بالخادم');
        }
    });
}
  // 🔥 COMPANY FILTER FUNCTIONALITY
  var table = $('#attendanceTable').DataTable();

    $('#attendanceCompanyFilter').on('change', function() {
        var selectedValue = $(this).val();
        
        // Column index 6 is "اسم الشركة" based on your <thead>
        // We use Regex for exact matching
        if (selectedValue) {
            // Filter specific company
            table.column(6).search(selectedValue).draw(); // Using smart search (contains)
        } else {
            // Reset filter (Show All)
            table.column(6).search('').draw();
        }
    });

  // تفعيل تلميحات البوتستراب
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });
});
</script>



<script>
    // --- Loading Screen Logic ---
    window.addEventListener('load', function() {
        const loadingScreen = document.getElementById('loading-screen');
        const mainContainer = document.querySelector('.main-container');
        
        loadingScreen.style.opacity = '0';
        setTimeout(() => {
            loadingScreen.style.display = 'none';
            document.body.style.overflow = 'auto';
            mainContainer.style.visibility = 'visible';
            mainContainer.style.opacity = '1';
            
            // Initialize DataTable after loading
            initializeDataTable();
        }, 500);
    });

    function initializeDataTable() {
        var id_sheet = <?php echo json_encode($this->uri->segment(3,0)); ?>;

        // Check if DataTable is already initialized
        if ($.fn.DataTable.isDataTable('#attendanceTable')) {
            return $('#attendanceTable').DataTable();
        }

        var dt = $('#attendanceTable').DataTable({
            responsive: true,
            pageLength: 10,
            deferRender: true,
            layout: {
                topStart: {
                    buttons: [
                        { extend: 'copy',  text: '<i class="fa fa-copy"></i> نسخ' },
                        { extend: 'excel', text: '<i class="fa fa-file-excel"></i> إكسل' },
                        { extend: 'pdf',   text: '<i class="fa fa-file-pdf"></i> PDF' },
                        { extend: 'print', text: '<i class="fa fa-print"></i> طباعة' },
                        { extend: 'colvis',text: '<i class="fa fa-eye"></i> إظهار/إخفاء الأعمدة' },
                        
                        // --- NEW PENDING REQUESTS BUTTON (Added Correctly) ---
                        {
                            text: '<i class="fas fa-bell"></i> طلبات معلقة',
                            className: 'btn btn-danger position-relative',
                            action: function (e, dt, node, config) {
                                var myModal = new bootstrap.Modal(document.getElementById('pendingRequestsModal'));
                                myModal.show();
                                fetchPendingRequests();
                            }
                        },
                        // -----------------------------------------------------

                        {
                            text: '<i class="fa fa-upload"></i> ترحيل المخالفات',
                            className: 'btn btn-warning',
                            action: function () {
                                if (!confirm('هل أنت متأكد من ترحيل مخالفات الحضور والانصراف الخاص بالمسير؟')) return;

                                // Collect data
                                var payload = [];
                                dt.rows({ search: 'applied' }).every(function () {
                                    var $row   = $(this.node());
                                    var $cells = $row.find('td');
                                    if (!$cells.length) return;

                                    var emp_id   = ($cells.eq(0).text() || '').trim();
                                    var emp_name = ($cells.eq(1).text() || '').trim();

                                    var lastIdx           = $cells.length - 1;
                                    var single_thing_txt  = ($cells.eq(lastIdx).text() || '').replace(/[^\d]/g,'');
                                    var minutes_early_txt = ($cells.eq(lastIdx - 1).text() || '').replace(/[^\d]/g,'');
                                    var minutes_late_txt  = ($cells.eq(lastIdx - 2).text() || '').replace(/[^\d]/g,'');
                                    var absence_txt       = ($cells.eq(lastIdx - 3).text() || '').replace(/[^\d]/g,'');

                                    if (emp_id) {
                                        payload.push({
                                            emp_id: emp_id,
                                            emp_name: emp_name,
                                            absence:       parseInt(absence_txt       || '0', 10),
                                            minutes_late:  parseInt(minutes_late_txt  || '0', 10),
                                            minutes_early: parseInt(minutes_early_txt || '0', 10),
                                            single_thing:  parseInt(single_thing_txt  || '0', 10)
                                        });
                                    }
                                });

                                if (!payload.length) { alert('لا توجد صفوف صالحة للترحيل.'); return; }

                                $.ajax({
                                    url: '<?php echo site_url("users1/save_attendance_summary/"); ?>' + id_sheet,
                                    type: 'POST',
                                    data: JSON.stringify({ rows: payload }),
                                    contentType: 'application/json; charset=utf-8',
                                    dataType: 'json',
                                    beforeSend: function () {
                                        $('.dt-buttons .btn:contains("ترحيل المخالفات")')
                                            .prop('disabled', true)
                                            .append(' <span class="spinner-border spinner-border-sm"></span>');
                                    },
                                    success: function (res) {
                                        if (res && res.status === 'ok') {
                                            alert('تم الترحيل بنجاح.\n' + 'المحذوف: ' + (res.deleted_rows || 0) + '\n' + 'المضاف: '  + (res.inserted_rows || 0));
                                        } else {
                                            alert('حدث خطأ أثناء الترحيل: ' + (res && res.msg ? JSON.stringify(res.msg) : 'غير معروف'));
                                        }
                                    },
                                    error: function () {
                                        alert('تعذر الاتصال بالخادم. تأكد من الرابط والصلاحيات.');
                                    },
                                    complete: function () {
                                        $('.dt-buttons .btn:contains("ترحيل المخالفات")')
                                            .prop('disabled', false)
                                            .find('.spinner-border').remove();
                                    }
                                });
                            }
                        }
                    ]
                }
            },
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json'
            },
            initComplete: function() {
                initializeTooltips();
            },
            drawCallback: function() {
                initializeTooltips();
            }
        });

        function filterByCompany() {
            var selectedCompanies = [];
            $('.company-filter-checkbox:checked').each(function() {
                selectedCompanies.push($(this).val());
            });
            
            if (selectedCompanies.length > 0) {
                var pattern = selectedCompanies.map(function(company) {
                    return '^' + company + '$';
                }).join('|');
                
                dt.column(6).search(pattern, true, false).draw();
            } else {
                dt.column(6).search('').draw();
            }
        }

        filterByCompany();

        $('.company-filter-checkbox').on('change', function() {
            filterByCompany();
        });

        return dt;
    }

    // --- NEW FUNCTION TO FETCH PENDING REQUESTS ---
    function fetchPendingRequests() {
        var id_sheet = <?php echo json_encode($id); ?>; // Get ID from PHP variable
        
        // UI Reset
        $('#pending-loading').show();
        $('#pending-content').hide();
        $('#pending-empty').hide();
        $('#pending-table-body').empty();

        $.ajax({
            url: '<?php echo site_url("users1/ajax_check_pending_requests"); ?>',
            type: 'POST',
            data: {
                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                'sheet_id': id_sheet
            },
            dataType: 'json',
            success: function(response) {
                $('#pending-loading').hide();

                if (response.status === 'success') {
                    $('#modal-period-span').text(response.period);

                    if (response.count > 0) {
                        var rows = '';
                        $.each(response.data, function(index, item) {
                            
                            // Determine Specific Date based on type
                            var displayDate = item.submission_date;
                            if(item.vac_start) displayDate = item.vac_start + ' إلى ' + item.vac_end;
                            else if(item.correction_date) displayDate = item.correction_date;
                            else if(item.ot_date) displayDate = item.ot_date;
                            else if(item.date_of_the_last_working) displayDate = item.date_of_the_last_working;

                            // Status Badge
                            var statusBadge = item.status == '0' 
                                ? '<span class="badge bg-warning text-dark">انتظار</span>' 
                                : '<span class="badge bg-info">تحت الاجراء</span>';

                            // Request link
                            var link = '<?php echo site_url("users1/view_request/"); ?>' + item.id;

                            rows += '<tr>';
                            rows += '<td>' + item.id + '</td>';
                            rows += '<td>' + item.emp_name + '</td>';
                            rows += '<td>' + item.order_name + '</td>';
                            rows += '<td><span dir="ltr">' + displayDate + '</span></td>';
                            rows += '<td>' + statusBadge + '</td>';
                            rows += '<td><a href="' + link + '" target="_blank" class="btn btn-sm btn-primary">عرض</a></td>';
                            rows += '</tr>';
                        });

                        $('#pending-table-body').html(rows);
                        $('#pending-content').show();
                    } else {
                        $('#pending-empty').show();
                    }
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                $('#pending-loading').hide();
                alert('حدث خطأ في الاتصال بالخادم');
            }
        });
    }

    function initializeTooltips() {
        var tooltipElements = document.querySelectorAll('#attendanceTable td[data-bs-toggle="tooltip"]');
        tooltipElements.forEach(function(element) {
            var existingTooltip = bootstrap.Tooltip.getInstance(element);
            if (existingTooltip) { existingTooltip.dispose(); }
            var title = element.getAttribute('title');
            if (title && title.trim() !== '') {
                new bootstrap.Tooltip(element, {
                    trigger: 'hover',
                    placement: 'auto',
                    container: element,
                    boundary: 'viewport',
                    sanitize: false,
                    title: title
                });
            }
        });
    }

    $(document).ready(function() {
        setTimeout(initializeTooltips, 1000);
    });
</script>

</body>
</html>