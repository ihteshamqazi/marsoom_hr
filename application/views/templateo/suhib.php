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
                    <?php echo form_open_multipart('users1/attendance_view/'.$id); ?>

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

    // === VACATION CHECKING ===
    $is_on_vacation = false;
    $vacation_type = '';
    foreach ($leave_requests_data as $leave) {
        $vacation_start = !empty($leave['vac_start']) ? strtotime($leave['vac_start']) : null;
        $vacation_end = !empty($leave['vac_end']) ? strtotime($leave['vac_end']) : null;
        if ($vacation_start && $vacation_end) {
            $current_date_midnight = strtotime(date('Y-m-d', $date));
            if ($current_date_midnight >= strtotime(date('Y-m-d', $vacation_start)) && $current_date_midnight <= strtotime(date('Y-m-d', $vacation_end))) {
                $is_on_vacation = true;
                $vacation_type = $leave['vac_main_type'] ?? 'إجازة';
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
        // === ATTENDANCE LOGIC FOR NON-VACATION DAYS ===
        
        // ✨ --- DYNAMIC RULES ARE NOW SET *PER DAY* --- ✨
        if ($is_mandatory_saturday) {
            $working_hours_for_day = 6.0;
            $lateness_threshold_for_day = '13:00:00'; // Late if punch-in is AFTER 1:00 PM
            
            // Saturday punch windows
            $punch_in_cutoff = strtotime($date_str . ' 16:00:00'); // IN punches until 4:00 PM
            $punch_out_start = strtotime($date_str . ' 14:00:00'); // OUT punches from 2:00 PM
            $exit_cutoff_limit = strtotime($date_str . ' 21:00:00'); // Absolute latest punch out
            
            $daily_salary_divisor = $saturday_daily_salary_divisor;
        } else {
            // Normal weekday logic
            $working_hours_for_day = $default_working_hours;
            $lateness_threshold_for_day = $default_lateness_threshold;
            
            // Normal day punch windows
            $punch_in_cutoff = strtotime($date_str . ' 12:00:00');
            $punch_out_start = strtotime($date_str . ' 13:00:00');
            $exit_cutoff_limit = strtotime($date_str . ' 21:00:00');
            
            $daily_salary_divisor = $default_daily_salary_divisor;
        }
        // ✨ --- END DYNAMIC RULES --- ✨

        $earliest_in_before_noon = null;
        $latest_out_in_window    = null;
        $present_from_kept       = false;

        foreach ($attendance_data as $attendance) {
            if ($attendance->emp_code == $emp_code && $attendance->punch_date == $date_str) {
                $raw_first = !empty($attendance->first_punch) ? strtotime($attendance->first_punch) : null;
                $raw_last  = !empty($attendance->last_punch)  ? strtotime($attendance->last_punch)  : null;

                // دخول: يُحسب فقط لو قبل $punch_in_cutoff
                if ($raw_first && $raw_first < $punch_in_cutoff) {
                    $present_from_kept = true;
                    if ($earliest_in_before_noon === null || $raw_first < $earliest_in_before_noon) {
                        $earliest_in_before_noon = $raw_first;
                    }
                }
                // خروج: يُحسب فقط داخل نافذة [$punch_out_start .. $exit_cutoff_limit]
                if ($raw_last && $raw_last >= $punch_out_start && $raw_last <= $exit_cutoff_limit) {
                    $present_from_kept = true;
                    if ($latest_out_in_window === null || $raw_last > $latest_out_in_window) {
                        $latest_out_in_window = $raw_last;
                    }
                }
            }
        }

        $first_punch = $earliest_in_before_noon;
        $last_punch  = $latest_out_in_window;
        $is_present  = $present_from_kept;
        $display_first_punch = '';
        $display_last_punch  = '';

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
        } 
        elseif (empty($first_punch) || empty($last_punch) || (($last_punch - $first_punch) < 60)) {
            $violation_class = 'early-departure';
            $message = 'بصمة منفردة';
            if (!$is_on_correction) {
                $single_punch_days++;
                $total_single_punch_deduction += $daily_salary;
            } else {
                $violation_class = 'on-time';
                $message .= ' (مستثنى بتصحيح بصمة)';
            }
            if ($first_punch) { $display_first_punch = date('H:i', $first_punch); }
            if ($last_punch)  { $display_last_punch  = date('H:i', $last_punch);  }
        } 
        else {
            // Employee is present with valid punches
            $required_duration_seconds = (int) round($working_hours_for_day * 3600);
            
            // ✨ USE CORRECTED TIMES FOR ALL CALCULATIONS ✨
            $first_punch_for_calculation = $correction_applied && $corrected_first_punch ? $corrected_first_punch : $first_punch;
            $last_punch_for_calculation = $correction_applied && $corrected_last_punch ? $corrected_last_punch : $last_punch;
            
            // Normalize seconds
            $first_punch_normalized = strtotime(date('Y-m-d H:i:00', $first_punch_for_calculation));
            $last_punch_normalized  = strtotime(date('Y-m-d H:i:00', $last_punch_for_calculation));

            // Use the dynamic rule variables
            $max_entry = strtotime($date_str . ' ' . $lateness_threshold_for_day);

            // Late calculation
            $late_time_minutes_today = ($first_punch_normalized > $max_entry)
                ? max(0, floor(($first_punch_normalized - $max_entry) / 60))
                : 0;

            // ✨ FIXED: Early departure calculation - only count if they worked less than required hours ✨
            $actual_work_seconds = $last_punch_normalized - $first_punch_normalized;
            $actual_work_minutes = $actual_work_seconds / 60;
            $required_work_minutes = $working_hours_for_day * 60;

            // Only count early departure if they worked less than required hours
            if ($actual_work_minutes < $required_work_minutes) {
                $early_exit_minutes_today = $required_work_minutes - $actual_work_minutes;
            } else {
                $early_exit_minutes_today = 0;
            }

            // ✨ CRITICAL FIX: Always add to totals, regardless of correction status ✨
            // Only exclude from totals if it's a COMPLETE correction (both times corrected)
            $should_exclude_from_totals = $is_on_correction && $corrected_first_punch && $corrected_last_punch;

            // Messages and classes
            $messages_today = [];
            $class_tags     = [];
            
            if ($is_mandatory_saturday) {
                $class_tags[] = 'saturday-work';
                $messages_today[] = 'دوام سبت';
            }

            if ($late_time_minutes_today > 0) {
                $messages_today[] = 'دخول متأخر: ' . $late_time_minutes_today . ' دقيقة';
                $class_tags[] = 'late-arrival';
                // ✨ FIXED: Only exclude from totals if it's a COMPLETE correction ✨
                if (!$should_exclude_from_totals) {
                    $total_late_minutes += $late_time_minutes_today; 
                    $total_late_exit_deduction += ($late_time_minutes_today * $daily_salary / $daily_salary_divisor / 60);
                }
            }

            if ($early_exit_minutes_today > 0) {
                $messages_today[] = 'خروج مبكر: ' . $early_exit_minutes_today . ' دقيقة';
                $class_tags[] = 'early-departure';
                // ✨ FIXED: Only exclude from totals if it's a COMPLETE correction ✨
                if (!$should_exclude_from_totals) {
                    $total_early_exit_minutes += $early_exit_minutes_today;
                    $total_late_exit_deduction += ($early_exit_minutes_today * $daily_salary / $daily_salary_divisor / 60);
                }
            }

            if ($late_time_minutes_today === 0 && $early_exit_minutes_today === 0) {
                if (!$is_mandatory_saturday) {
                    $class_tags[] = 'on-time';
                }
                $messages_today[] = 'ملتزم بوقت العمل';
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
            
            // ✨ DISPLAY: Show corrected times when available ✨
            $display_first_punch = date('H:i', $first_punch_for_calculation);
            $display_last_punch  = date('H:i', $last_punch_for_calculation);
        }
    }

    echo "<td class='$violation_class' title='$message' data-bs-toggle='tooltip' data-bs-placement='top'>
            <strong>دخول:</strong> " . (isset($display_first_punch) ? $display_first_punch : '') . " <br><strong>خروج:</strong> " . (isset($display_last_punch) ? $display_last_punch : '') . "
          </td>";
}

// ##################################################################
// ### END OF CORRECTED PHP LOGIC BLOCK ###
// ##################################################################
?>



                                        
                                        <td><strong><?php echo $absence_days; ?></strong></td>
                                        <td><strong><?php echo $total_late_minutes; ?></strong></td>
                                        <td><strong><?php echo $total_early_exit_minutes; ?></strong></td>
                                        <td><strong><?php echo $single_punch_days; ?></strong></td>
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
      pageLength: 10,deferRender: true,
      layout: {
        topStart: {
          buttons: [
            { extend: 'copy',  text: '<i class="fa fa-copy"></i> نسخ' },
            { extend: 'excel', text: '<i class="fa fa-file-excel"></i> إكسل' },
            { extend: 'pdf',   text: '<i class="fa fa-file-pdf"></i> PDF' },
            { extend: 'print', text: '<i class="fa fa-print"></i> طباعة' },
            { extend: 'colvis',text: '<i class="fa fa-eye"></i> إظهار/إخفاء الأعمدة' },

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
                  // إذا عندك CSRF مفعّل، فك التعليق وأرسل التوكن:
                  // headers: { 'X-CSRF-TOKEN': '<?php echo $this->security->get_csrf_hash(); ?>' },
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
        }, 500); // Match CSS transition time
    });

    $(document).ready(function() {
        // Initialize DataTables
        // $('.dataTables-example').DataTable({
        //     responsive: true,
        //     pageLength: 10,
        //     layout: {
        //         topStart: {
        //             buttons: [
        //                 { extend: 'copy', text: '<i class="fa fa-copy"></i> نسخ' },
        //                 { extend: 'excel', text: '<i class="fa fa-file-excel"></i> إكسل' },
        //                 { extend: 'pdf', text: '<i class="fa fa-file-pdf"></i> PDF' },
        //                 { extend: 'print', text: '<i class="fa fa-print"></i> طباعة' },
        //                 { extend: 'colvis', text: '<i class="fa fa-eye"></i> إظهار/إخفاء الأعمدة' }
        //             ]
        //         }
        //     },
        //     language: {
        //         "url": "https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json"
        //     }
        // });

        // Initialize Bootstrap Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
         var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
             return new bootstrap.Tooltip(tooltipTriggerEl);
         });
     });
</script>

</body>
</html>