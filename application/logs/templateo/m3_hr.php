<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>شيت الرواتب</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&family=Cairo:wght@700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.css">

    <style>
        :root {
            --primary-color: #2C3E50;
            --secondary-color: #E67E22;
            --background-color: #f4f7f9;
            --card-bg-color: #ffffff;
            --text-color: #34495e;
            --header-text-color: #ffffff;
            --highlight-row-color: #eaf1f7;
            
            /* Status Colors */
            --success-bg: #e8f5e9;
            --success-text: #2e7d32;
            --warning-bg: #fffde7;
            --warning-text: #fbc02d;
            --danger-bg: #ffebee;
            --danger-text: #c62828;
            --info-bg: #e3f2fd;
            --info-text: #1565c0;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            overflow: hidden;
        }
        
        /* --- Loading Screen --- */
        #loading-screen {
            position: fixed;
            width: 100%;
            height: 100%;
            background-color: var(--background-color);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            transition: opacity 0.5s ease-out;
        }

        .loader {
            width: 60px;
            height: 60px;
            border: 6px solid rgba(0, 0, 0, 0.1);
            border-top: 6px solid var(--secondary-color);
            border-radius: 50%;
            animation: spin 1s cubic-bezier(0.68, -0.55, 0.27, 1.55) infinite;
            margin-bottom: 25px;
        }

        #loading-screen h3 {
            color: var(--primary-color);
            font-family: 'Cairo', sans-serif;
            font-weight: 800;
            font-size: 24px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .main-container {
            padding: 30px 15px;
            visibility: hidden;
            opacity: 0;
            transition: opacity 0.7s ease-in;
        }

        .page-title {
            font-family: 'Cairo', sans-serif;
            font-weight: 800;
            font-size: 2.8rem;
            color: var(--primary-color);
            margin-bottom: 40px;
            text-align: center;
            animation: fadeInDown 1s ease-out;
            position: relative;
            display: inline-block;
            padding-bottom: 12px;
        }

        .page-title::after {
            content: '';
            position: absolute;
            width: 100px;
            height: 5px;
            background: linear-gradient(90deg, var(--secondary-color), var(--primary-color));
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 5px;
        }

        .table-card {
            background-color: var(--card-bg-color);
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
            padding: 30px;
            animation: fadeInUp 1s ease-out 0.3s;
            animation-fill-mode: backwards;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .dataTables-example thead th {
            background: var(--primary-color) !important;
            color: var(--header-text-color);
            font-weight: 700;
            text-align: center;
            vertical-align: middle;
            border-bottom: 3px solid var(--secondary-color);
            padding: 15px 10px;
        }

        .dataTables-example tbody td {
            text-align: center;
            vertical-align: middle;
            font-size: 14px;
            padding: 12px 10px;
            white-space: nowrap;
            transition: background-color 0.3s ease;
        }
        
        .dataTables-example tbody tr:nth-child(even) {
            background-color: var(--highlight-row-color);
        }

        .dataTables-example tbody tr {
            opacity: 0;
            animation: fadeIn 0.5s ease-out forwards;
        }
        
        /* Staggered animation for table rows */
        <?php for ($i = 0; $i < 20; $i++): ?>
        .dataTables-example tbody tr:nth-child(<?php echo $i + 1; ?>) {
            animation-delay: <?php echo $i * 0.07; ?>s;
        }
        <?php endfor; ?>

        .dataTables-example tbody tr:hover {
            background-color: #dbe4ef;
            transform: scale(1.005);
            transition: transform 0.2s ease-in-out, background-color 0.2s ease-in-out;
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
        
        td strong {
            font-weight: 600;
        }
        
        /* --- Total Row Style --- */
        .dataTables-example tfoot {
            background-color: var(--primary-color) !important;
            color: var(--header-text-color);
            font-weight: bold;
        }
        .dataTables-example tfoot td {
            font-weight: bold;
            text-align: center;
        }
        .dataTables-example tfoot .total-label {
            text-align: right;
            border-left: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* DataTables Buttons Styling */
        .dt-buttons .btn {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: var(--header-text-color);
            font-weight: 600;
            margin: 0 4px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .dt-buttons .btn:hover {
            background-color: #d66b1a;
            border-color: #d66b1a;
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }
        
        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select {
            border-radius: 10px;
            border: 1px solid #c9d2d9;
            padding: 8px 12px;
            transition: border-color 0.3s ease;
        }

        .dataTables_wrapper .dataTables_filter input:focus,
        .dataTables_wrapper .dataTables_length select:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(230, 126, 34, 0.25);
            outline: none;
        }

        /* --- Animations --- */
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
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
    <h3>جاري تجهيز شيت الرواتب...</h3>
</div>

<div class="main-container container-fluid">
    <div class="text-center">
        <h1 class="page-title">شيت الرواتب 💸</h1>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card table-card">
                <div class="card-body">
                    <?php echo validation_errors(); ?>
                    <?php echo form_open_multipart('users1/sadad_report_emp'); ?>
                    
                    <?php
                    // PHP Code to calculate totals
                    $total_employees = 0;
                    $total_salary_sum = 0;
                    $total_absence_days_sum = 0;
                    $total_late_minutes_sum = 0;
                    $total_early_exit_minutes_sum = 0;
                    $total_single_punch_days_sum = 0;
                    $total_absence_deduction_sum = 0;
                    $total_single_punch_deduction_sum = 0;
                    $total_late_exit_deduction_sum = 0;
                    $total_discount_amount_sum = 0;
                    $total_reparations_amount_sum = 0;
                    $total_attendance_deduction_sum = 0;
                    $total_all_deductions_sum = 0;
                    $total_pre_insurance_salary_sum = 0;
                    $total_insurance_deduction_sum = 0;
                    $total_net_salary_sum = 0;

                    foreach ($employees as $row) {
                        $total_employees++;
                        $total_salary_sum += $row->total_salary;

                        $emp_code = $row->employee_id;
                        $daily_salary = $row->total_salary / 30;
                        $absence_days = 0;
                        $total_late_minutes = 0;
                        $total_early_exit_minutes = 0;
                        $single_punch_days = 0;
                        $total_absence_deduction = 0;
                        $total_single_punch_deduction = 0;
                        $total_late_exit_deduction = 0;
                        
                        $start_date = strtotime($get_salary_sheet['start_date']);
                        $end_date = strtotime($get_salary_sheet['end_date']);

                        // Fetch vacations data
                        $this->db->select('start_date, end_date, type');
                        $this->db->from('vacations');
                        $this->db->where('username', $emp_code);
                        $query_vacations = $this->db->get();
                        $vacations_data = $query_vacations->result_array();

                        // Loop through dates to calculate attendance metrics
                        for ($date = $start_date; $date <= $end_date; $date = strtotime("+1 day", $date)) {
                            if (date('N', $date) == 5 || date('N', $date) == 6) {
                                continue;
                            }
                            $date_str = date('Y-m-d', $date);
                            $is_present = false;
                            $first_punch = null;
                            $last_punch = null;

                            foreach ($attendance_data as $attendance) {
                                if ($attendance->emp_code == $emp_code && $attendance->punch_date == $date_str) {
                                    $is_present = true;
                                    $first_punch = !empty($attendance->first_punch) ? strtotime($attendance->first_punch) : null;
                                    $last_punch = !empty($attendance->last_punch) ? strtotime($attendance->last_punch) : null;
                                    break;
                                }
                            }
                            
                            $is_on_vacation = false;
                            foreach ($vacations_data as $vacation) {
                                $vacation_start = strtotime($vacation['start_date']);
                                $vacation_end = strtotime($vacation['end_date']);
                                if ($date >= $vacation_start && $date <= $vacation_end) {
                                    $is_on_vacation = true;
                                    break;
                                }
                            }

                            if (!$is_present && !$is_on_vacation) {
                                $absence_days++;
                                $total_absence_deduction += $daily_salary;
                            } elseif (($is_present && empty($first_punch)) || ($is_present && empty($last_punch)) || ($is_present && ($last_punch - $first_punch) < 60)) {
                                $single_punch_days++;
                                $total_single_punch_deduction += $daily_salary;
                            } else {
                                $required_duration_seconds = 9 * 3600;
                                $work_duration_seconds = $last_punch - $first_punch;
                                $late_time_seconds = max(0, $first_punch - strtotime($date_str . ' 11:00:00'));
                                $shortfall_seconds = max(0, $required_duration_seconds - $work_duration_seconds);
                                
                                $late_time_minutes = floor($late_time_seconds / 60);
                                $early_exit_minutes = floor($shortfall_seconds / 60);

                                $total_late_minutes += $late_time_minutes;
                                $total_early_exit_minutes += $early_exit_minutes;
                                $total_late_exit_deduction += (($early_exit_minutes + $late_time_minutes) * $daily_salary / 9 / 60);
                            }
                        }

                        // Get deductions and reparations for the employee
                        $discount_data = $this->hr_model->get_discounts($this->uri->segment(3, 0), $row->employee_id);
                        $discount_amount = isset($discount_data['amount']) ? $discount_data['amount'] : 0;
                        $total_discount_amount_sum += $discount_amount;
                        
                        $get_reparations = $this->hr_model->get_reparations($this->uri->segment(3, 0), $row->employee_id);
                        $reparations_amount = isset($get_reparations['amount']) ? $get_reparations['amount'] : 0;
                        $total_reparations_amount_sum += $reparations_amount;

                        // Calculate totals for summary row
                        $total_absence_days_sum += $absence_days;
                        $total_late_minutes_sum += $total_late_minutes;
                        $total_early_exit_minutes_sum += $total_early_exit_minutes;
                        $total_single_punch_days_sum += $single_punch_days;
                        $total_absence_deduction_sum += $total_absence_deduction;
                        $total_single_punch_deduction_sum += $total_single_punch_deduction;
                        $total_late_exit_deduction_sum += $total_late_exit_deduction;
                        $total_attendance_deduction_sum += ($total_absence_deduction + $total_single_punch_deduction + $total_late_exit_deduction);
                        $total_all_deductions_sum += ($total_absence_deduction + $discount_amount + $total_single_punch_deduction + $total_late_exit_deduction);
                        
                        $final_salary = $row->total_salary + $reparations_amount - ($total_absence_deduction + $discount_amount + $total_single_punch_deduction + $total_late_exit_deduction);
                        $total_pre_insurance_salary_sum += $final_salary;
                        
                        if ($row->nationality === 'سعودي') {
                            $insurance_deduction = ($row->base_salary + $row->housing_allowance) * 0.0975;
                        } else {
                            $insurance_deduction = 0;
                        }
                        $total_insurance_deduction_sum += $insurance_deduction;
                        
                        $net_salary = $final_salary - $insurance_deduction;
                        $total_net_salary_sum += $net_salary;
                    }
                    ?>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example" style="width:100%">
                            <thead>
                                <tr>
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
                                    <?php
                                    $start_date = strtotime($get_salary_sheet['start_date']);
                                    $end_date = strtotime($get_salary_sheet['end_date']);
                                    for ($date = $start_date; $date <= $end_date; $date = strtotime("+1 day", $date)) {
                                        if (date('N', $date) != 5 && date('N', $date) != 6) {
                                            echo "<th>" . date('d/m/Y', $date) . "</th>";
                                        }
                                    }
                                    ?>
                                    <th>أيام الغياب</th>
                                    <th>إجمالي الدقائق المتأخرة ⏳</th>
                                    <th>إجمالي الدقائق المبكرة</th>
                                    <th>أيام بصمة منفردة</th>
                                    <th>خصم الغياب</th>
                                    <th>خصم بصمة منفردة</th>
                                    <th>خصم دقائق التأخير والخروج المبكر</th>
                                    <th>خصم الجزاءات</th>
                                    <th>التعويضات</th>
                                    <th>إجمالي خصم الحضور والانصراف</th>
                                    <th>الاجمالي الكلي للخصومات</th>
                                    <th>اجمالي الراتب ما قبل خصم التأمينات</th>
                                    <th>اجمالي خصم التامينات</th>
                                    <th>صافي الراتب</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($employees as $row): ?>
                                    <tr>
                                        <td><?php echo $row->employee_id; ?></td>
                                        <td><?php echo $row->subscriber_name; ?></td>
                                        <td><?php echo $row->id_number; ?></td>
                                        <td><?php echo $row->n3; ?></td>
                                        <td><?php echo $row->n2; ?></td>
                                        <td><?php echo $row->profession; ?></td>
                                        <td><?php echo $row->n1; ?></td>
                                        <td><?php echo $row->nationality; ?></td>
                                        <td><?php echo $row->total_salary; ?></td>
                                        <td><?php echo $row->company_name; ?></td>
                                        <?php
                                        // Recalculate daily metrics for display in table cells
                                        $emp_code = $row->employee_id;
                                        $daily_salary = $row->total_salary / 30;
                                        $vacations_data = $this->db->select('start_date, end_date, type')->from('vacations')->where('username', $emp_code)->get()->result_array();

                                        $start_date = strtotime($get_salary_sheet['start_date']);
                                        $end_date = strtotime($get_salary_sheet['end_date']);

                                        $absence_days = 0;
                                        $total_late_minutes = 0;
                                        $total_early_exit_minutes = 0;
                                        $single_punch_days = 0;
                                        $total_absence_deduction = 0;
                                        $total_single_punch_deduction = 0;
                                        $total_late_exit_deduction = 0;

                                        for ($date = $start_date; $date <= $end_date; $date = strtotime("+1 day", $date)) {
                                            if (date('N', $date) == 5 || date('N', $date) == 6) {
                                                continue;
                                            }

                                            $date_str = date('Y-m-d', $date);
                                            $first_punch = null;
                                            $last_punch = null;
                                            $violation_class = '';
                                            $message = '';
                                            $is_present = false;

                                            $is_on_vacation = false;
                                            $vacation_type = '';
                                            foreach ($vacations_data as $vacation) {
                                                $vacation_start = strtotime($vacation['start_date']);
                                                $vacation_end = strtotime($vacation['end_date']);
                                                if ($date >= $vacation_start && $date <= $vacation_end) {
                                                    $is_on_vacation = true;
                                                    $vacation_type = $vacation['type'];
                                                    break;
                                                }
                                            }

                                            if ($is_on_vacation) {
                                                $violation_class = 'on-vacation';
                                                $message = 'إجازة: ' . $vacation_type;
                                                $display_first_punch = '';
                                                $display_last_punch = '';
                                            } else {
                                                foreach ($attendance_data as $attendance) {
                                                    if ($attendance->emp_code == $emp_code && $attendance->punch_date == $date_str) {
                                                        $first_punch = !empty($attendance->first_punch) ? strtotime($attendance->first_punch) : null;
                                                        $last_punch = !empty($attendance->last_punch) ? strtotime($attendance->last_punch) : null;
                                                        $is_present = true;
                                                        break;
                                                    }
                                                }
                                                $display_first_punch = '';
                                                $display_last_punch = '';

                                                if (!$is_present) {
                                                    $violation_class = 'early-departure';
                                                    $message = 'غياب';
                                                    $absence_days++;
                                                } elseif (empty($first_punch) || empty($last_punch) || (($last_punch - $first_punch) < 60)) {
                                                    $violation_class = 'early-departure';
                                                    $message = 'بصمة منفردة';
                                                    $single_punch_days++;
                                                    $single_punch_time = !empty($first_punch) ? $first_punch : $last_punch;
                                                    if ($single_punch_time) {
                                                        $noon_time_limit = strtotime($date_str . ' 12:00:00');
                                                        if ($single_punch_time < $noon_time_limit) {
                                                            $display_first_punch = date('H:i', $single_punch_time);
                                                        } else {
                                                            $display_last_punch = date('H:i', $single_punch_time);
                                                        }
                                                    }
                                                } else {
                                                    $required_duration_seconds = 9 * 3600;
                                                    $work_duration_seconds = $last_punch - $first_punch;
                                                    $shortfall_seconds = max(0, $required_duration_seconds - $work_duration_seconds);
                                                    $late_time_seconds = max(0, $first_punch - strtotime($date_str . ' 11:00:00'));
                                                    
                                                    $late_time_minutes = floor($late_time_seconds / 60);
                                                    $early_exit_minutes = floor($shortfall_seconds / 60);

                                                    if ($late_time_minutes > 0) {
                                                        $violation_class = 'late-arrival';
                                                        $message = 'دخول متأخر';
                                                        $total_late_minutes += $late_time_minutes;
                                                    } elseif ($early_exit_minutes > 0) {
                                                        $violation_class = 'early-departure';
                                                        $message = 'خروج مبكر';
                                                        $total_early_exit_minutes += $early_exit_minutes;
                                                    } else {
                                                        $violation_class = 'on-time';
                                                        $message = 'ملتزم بوقت العمل';
                                                    }
                                                    $display_first_punch = date('H:i', $first_punch);
                                                    $display_last_punch = date('H:i', $last_punch);
                                                }
                                            }
                                            echo "<td class='$violation_class' title='$message' data-bs-toggle='tooltip' data-bs-placement='top'>
                                                    <strong>دخول:</strong> $display_first_punch <br><strong>خروج:</strong> $display_last_punch
                                                  </td>";
                                        }

                                        // Get deductions and reparations for the employee
                                        $discount_data = $this->hr_model->get_discounts($this->uri->segment(3, 0), $row->employee_id);
                                        $discount_amount = isset($discount_data['amount']) ? $discount_data['amount'] : 0;
                                        $get_reparations = $this->hr_model->get_reparations($this->uri->segment(3, 0), $row->employee_id);
                                        $reparations_amount = isset($get_reparations['amount']) ? $get_reparations['amount'] : 0;

                                        $total_absence_deduction = $absence_days * $daily_salary;
                                        $total_single_punch_deduction = $single_punch_days * $daily_salary;
                                        $total_late_exit_deduction = (($total_early_exit_minutes + $total_late_minutes) * $daily_salary / 9 / 60);

                                        $total_attendance_deduction = $total_absence_deduction + $total_single_punch_deduction + $total_late_exit_deduction;
                                        $total_all_deductions = $total_attendance_deduction + $discount_amount;
                                        $final_salary = $row->total_salary + $reparations_amount - $total_all_deductions;
                                        
                                        if ($row->nationality === 'سعودي') {
                                            $insurance_deduction = ($row->base_salary + $row->housing_allowance) * 0.0975;
                                        } else {
                                            $insurance_deduction = 0;
                                        }
                                        $net_salary = $final_salary - $insurance_deduction;
                                        ?>
                                        <td><strong><?php echo $absence_days; ?></strong></td>
                                        <td><strong><?php echo $total_late_minutes; ?></strong></td>
                                        <td><strong><?php echo $total_early_exit_minutes; ?></strong></td>
                                        <td><strong><?php echo $single_punch_days; ?></strong></td>
                                        <td><strong><?php echo number_format($total_absence_deduction, 2); ?></strong></td>
                                        <td><strong><?php echo number_format($total_single_punch_deduction, 2); ?></strong></td>
                                        <td><strong><?php echo number_format($total_late_exit_deduction, 2); ?></strong></td>
                                        <td><strong><?php echo number_format($discount_amount, 2); ?></strong></td>
                                        <td><strong><?php echo number_format($reparations_amount, 2); ?></strong></td>
                                        <td><strong><?php echo number_format($total_attendance_deduction, 2); ?></strong></td>
                                        <td><strong><?php echo number_format($total_all_deductions, 2); ?></strong></td>
                                        <td><strong><?php echo number_format($final_salary, 2); ?></strong></td>
                                        <td><strong><?php echo number_format($insurance_deduction, 2); ?></strong></td>
                                        <td><strong><?php echo number_format($net_salary, 2); ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="8" class="total-label">الإجمالي:</td>
                                    <td><?php echo number_format($total_salary_sum, 2); ?></td>
                                    <td><?php echo $total_employees; ?> موظف</td>
                                    <?php
                                    $num_work_days = 0;
                                    $start_date = strtotime($get_salary_sheet['start_date']);
                                    $end_date = strtotime($get_salary_sheet['end_date']);
                                    for ($date = $start_date; $date <= $end_date; $date = strtotime("+1 day", $date)) {
                                        if (date('N', $date) != 5 && date('N', $date) != 6) {
                                            echo "<td></td>";
                                        }
                                    }
                                    ?>
                                    <td><?php echo $total_absence_days_sum; ?></td>
                                    <td><?php echo $total_late_minutes_sum; ?></td>
                                    <td><?php echo $total_early_exit_minutes_sum; ?></td>
                                    <td><?php echo $total_single_punch_days_sum; ?></td>
                                    <td><?php echo number_format($total_absence_deduction_sum, 2); ?></td>
                                    <td><?php echo number_format($total_single_punch_deduction_sum, 2); ?></td>
                                    <td><?php echo number_format($total_late_exit_deduction_sum, 2); ?></td>
                                    <td><?php echo number_format($total_discount_amount_sum, 2); ?></td>
                                    <td><?php echo number_format($total_reparations_amount_sum, 2); ?></td>
                                    <td><?php echo number_format($total_attendance_deduction_sum, 2); ?></td>
                                    <td><?php echo number_format($total_all_deductions_sum, 2); ?></td>
                                    <td><?php echo number_format($total_pre_insurance_salary_sum, 2); ?></td>
                                    <td><?php echo number_format($total_insurance_deduction_sum, 2); ?></td>
                                    <td><?php echo number_format($total_net_salary_sum, 2); ?></td>
                                </tr>
                            </tfoot>
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
        }, 500);
    });

    $(document).ready(function() {
        // Initialize DataTables
        $('.dataTables-example').DataTable({
            responsive: true,
            pageLength: 10,
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
            language: {
                "url": "https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json"
            }
        });

        // Initialize Bootstrap Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>

</body>
</html>