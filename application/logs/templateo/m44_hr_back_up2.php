<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> الحضور والانصراف  </title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- DataTables CSS for Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.css">

    <!-- Custom Styles & Animations -->
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
    <h3>جاري تجهيز  تقرير الحضور والانصراف  ...</h3>
</div>

<div class="main-container container-fluid">
    <div class="text-center">
        <h1 class="page-title">   الحضور والانصراف</h1>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card table-card">
                <div class="card-body">
                    <?php echo validation_errors(); ?>
                    <?php echo form_open_multipart('users1/attendance_view/'.$id); ?>

                    <div class="table-responsive">
                     <table id="attendanceTable" class="table table-striped table-bordered table-hover" style="width:100%">


                            <thead>
                                <tr>
                                    <th>الرقم الوظيفي</th>
                                    <th>اسم الموظف</th>
                                    <th>رقم الهوية</th>
                                    
                                      <th>       المسمى الوظيفي</th>
                                       <th>      الادارة</th>
                                    <th>الجنسية</th>
                                    
                                    <th>اسم الشركة</th>
                                    <!-- PHP code to generate date columns -->
                                    <?php
                                    $start_date = strtotime($get_salary_sheet['start_date']);
                                    $end_date = strtotime($get_salary_sheet['end_date']);
                                    for ($date = $start_date; $date <= $end_date; $date = strtotime("+1 day", $date)) {
                                        if (date('N', $date) != 5 && date('N', $date) != 6) {
                                            echo "<th>" . date('d/m/Y', $date) . "</th>";
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
$violations = isset($violations[$row->employee_id]) ? $violations[$row->employee_id] : [];

$start_date = strtotime($get_salary_sheet['start_date']);
$end_date   = strtotime($get_salary_sheet['end_date']);
$emp_code   = $row->employee_id;

/* الإجازات */
$this->db->select('start_date, end_date, type');
$this->db->from('vacations');
$this->db->where('username', $emp_code);
$query_vacations = $this->db->get();
$vacations_data = $query_vacations->result_array(); 

/* NEW: تصحيح البصمة */
$this->db->select('start_date, end_date, type');
$this->db->from('fingerprint_correction');
$this->db->where('username', $emp_code);
$query_corrections = $this->db->get();
$fingerprint_corrections_data = $query_corrections->result_array();

$absence_days = 0; 
$total_late_minutes = 0; 
$total_early_exit_minutes = 0; 
$single_punch_days = 0; 

$daily_salary = $row->total_salary / 30;

$total_absence_deduction      = 0; 
$total_single_punch_deduction = 0; 
$total_late_exit_deduction    = 0; 
$total_deductions             = 0; 

for ($date = $start_date; $date <= $end_date; $date = strtotime("+1 day", $date)) {

  // تخطي نهاية الأسبوع (الجمعة 5، السبت 6)
  if (date('N', $date) == 5 || date('N', $date) == 6) { 
    continue;
  }

  $date_str = date('Y-m-d', $date);
  $first_punch = null;
  $last_punch  = null;
  $violation_class = '';
  $message = '';
  $is_present = false;

  // التحقق من الإجازة
  $is_on_vacation = false;
  $vacation_type = '';
  foreach ($vacations_data as $vacation) {
    $vacation_start = strtotime($vacation['start_date']);
    $vacation_end   = strtotime($vacation['end_date']);
    if ($date >= $vacation_start && $date <= $vacation_end) {
      $is_on_vacation = true;
      $vacation_type = $vacation['type'];
      break;
    }
  }

  // NEW: التحقق من وجود تصحيح بصمة لليوم (أولوية بعد الإجازة)
  $is_on_correction = false;
  $correction_type = '';
  if (!$is_on_vacation && !empty($fingerprint_corrections_data)) {
    foreach ($fingerprint_corrections_data as $corr) {
      if (empty($corr['start_date']) || empty($corr['end_date'])) {
        continue; // تجاهل سجلات غير مكتملة
      }
      // طابق على مستوى اليوم فقط (تجاهل الوقت)
      $corr_start = strtotime(date('Y-m-d', strtotime($corr['start_date'])));
      $corr_end   = strtotime(date('Y-m-d', strtotime($corr['end_date'])));
      if ($date >= $corr_start && $date <= $corr_end) {
        $is_on_correction = true;
        $correction_type = $corr['type'];
        break;
      }
    }
  }

  if ($is_on_vacation) {
    // إجازة: نوقف الحساب ونُظهر الحالة فقط
    $violation_class = 'on-vacation'; 
    $message = 'إجازة: ' . $vacation_type; 
    $display_first_punch = '';
    $display_last_punch  = '';

  } else {
    /* =================== استخراج بصمات اليوم مع حد 21:00 =================== */
    $noon_time_limit   = strtotime($date_str . ' 12:00:00');
    $exit_cutoff_limit = strtotime($date_str . ' 21:00:00'); // أي خروج بعده يُتجاهل

    $earliest_in_before_noon = null;  // أبكر دخول قبل 12
    $latest_out_in_window    = null;  // أحدث خروج داخل [12:00..21:00]
    $present_from_kept       = false; // حضور محسوب فقط من البصمات المقبولة

    foreach ($attendance_data as $attendance) {
      if ($attendance->emp_code == $emp_code && $attendance->punch_date == $date_str) {
        $raw_first = !empty($attendance->first_punch) ? strtotime($attendance->first_punch) : null;
        $raw_last  = !empty($attendance->last_punch)  ? strtotime($attendance->last_punch)  : null;

        // دخول: يُحسب فقط لو قبل 12:00
        if ($raw_first && $raw_first < $noon_time_limit) {
          $present_from_kept = true;
          if ($earliest_in_before_noon === null || $raw_first < $earliest_in_before_noon) {
            $earliest_in_before_noon = $raw_first;
          }
        }

        // خروج: يُحسب فقط داخل نافذة [12:00..21:00]
        if ($raw_last && $raw_last >= $noon_time_limit && $raw_last <= $exit_cutoff_limit) {
          $present_from_kept = true;
          if ($latest_out_in_window === null || $raw_last > $latest_out_in_window) {
            $latest_out_in_window = $raw_last;
          }
        }
      }
    }

    // البصمات المعتمدة
    $first_punch = $earliest_in_before_noon;   // قد تبقى null إذا لا دخول قبل 12
    $last_punch  = $latest_out_in_window;      // تُهمل أي خروجات بعد 21:00
    $is_present  = $present_from_kept;         // حضور يُحسب فقط من البصمات المقبولة
    /* ====================================================================== */

    $display_first_punch = '';
    $display_last_punch  = '';

    if (!$is_present) {
      // غياب
      $violation_class = 'early-departure';
      $message = 'غياب';
      if (!$is_on_correction) {
        $absence_days++; 
        $total_absence_deduction += $daily_salary; 
      } else {
        $violation_class = 'fingerprint-correction';
        $message .= ' (مستثنى بتصحيح بصمة: ' . $correction_type . ')';
      }

    } 
    elseif (empty($first_punch) || empty($last_punch) || (($last_punch - $first_punch) < 60)) {
      // بصمة منفردة (طبقًا لشرط 12:00)
      $violation_class = 'early-departure';
      $message = 'بصمة منفردة';
      if (!$is_on_correction) {
        $single_punch_days++;
        $total_single_punch_deduction += $daily_salary;
      } else {
        $violation_class = 'fingerprint-correction';
        $message .= ' (مستثنى بتصحيح بصمة: ' . $correction_type . ')';
      }

      if ($first_punch) { $display_first_punch = date('H:i', $first_punch); }
      if ($last_punch)  { $display_last_punch  = date('H:i', $last_punch);  }

    } 
    else {
      // ========= منطق الحساب =========

      $sheet_id = $this->uri->segment(3,0);
      // نجلب first_punch, last_punch, working_hours
      $rule_row = $this->db->select('first_punch, last_punch, working_hours')
                           ->from('work_restrictions')
                           ->where('emp_id', $row->employee_id)
                           ->where('sheet_id', $sheet_id)
                           ->get()
                           ->row();

      // نحول working_hours إلى ساعات (تدعم 8.5 أو 08:30:00)
      $working_hours = 9.0; // افتراضي
      if ($rule_row && isset($rule_row->working_hours) && $rule_row->working_hours !== '') {
        $wh_raw = trim((string)$rule_row->working_hours);
        if (strpos($wh_raw, ':') !== false) {
          // صيغة HH:MM:SS
          $parts = array_map('intval', explode(':', $wh_raw));
          $h = $parts[0] ?? 0; $m = $parts[1] ?? 0; $s = $parts[2] ?? 0;
          $working_hours = $h + ($m/60) + ($s/3600);
        } else {
          $working_hours = floatval($wh_raw);
        }
        if ($working_hours <= 0) { $working_hours = 9.0; }
      }
      $required_duration_seconds = (int) round($working_hours * 3600);
      $hours_divisor             = max(0.01, $working_hours); // منع القسمة على صفر

      // تسوية الثواني
      $first_punch_normalized = strtotime(date('Y-m-d H:i:00', $first_punch));
      $last_punch_normalized  = strtotime(date('Y-m-d H:i:00', $last_punch));

      // البداية الرسمية من القيود
      if ($rule_row && !empty($rule_row->first_punch)) {
        $official_start_base = strtotime(date('Y-m-d H:i:00', strtotime($date_str . ' ' . $rule_row->first_punch)));
      } elseif ($rule_row && !empty($rule_row->last_punch)) {
        $official_start_base = strtotime(date('Y-m-d H:i:00', strtotime($date_str . ' ' . $rule_row->last_punch)));
      } else {
        $official_start_base = strtotime($date_str . ' 08:00:00');
      }

      // حد الدخول الأقصى (الساعات المرنة)
      $max_entry = ($rule_row && !empty($rule_row->last_punch))
        ? strtotime(date('Y-m-d H:i:00', strtotime($date_str . ' ' . $rule_row->last_punch)))
        : $official_start_base;

      // 1) التأخير: فقط إذا actual_in > max_entry
      $late_time_minutes  = ($first_punch_normalized > $max_entry)
        ? max(0, floor(($first_punch_normalized - $max_entry) / 60))
        : 0;

      // 2) نهاية الدوام الرسمية (شرطية حسب التأخير)
      if ($late_time_minutes > 0) {
        $official_end_normalized = $max_entry + $required_duration_seconds;          // (حد المرونة + ساعات العمل)
      } else {
        $official_end_normalized = $official_start_base + $required_duration_seconds; // (بداية الدوام + ساعات العمل)
      }

      // 3) نقص الساعات الإجمالي بالنسبة لـ working_hours
      $work_duration_seconds    = $last_punch_normalized - $first_punch_normalized;
      $duration_deficit_minutes = max(0, floor(($required_duration_seconds - $work_duration_seconds) / 60));

      // 4) فرق النهاية الرسمية
      $end_deficit_minutes = ($last_punch_normalized < $official_end_normalized)
        ? max(0, floor(($official_end_normalized - $last_punch_normalized) / 60))
        : 0;

      // 5) الخروج المبكر = الأكبر من النقصين
      $early_exit_minutes = max($duration_deficit_minutes, $end_deficit_minutes);

      // ===== رسائل وكلاسات =====
      $messages_today = [];
      $class_tags     = [];

      if ($late_time_minutes > 0) {
        if ($is_on_correction) {
          $messages_today[] = 'دخول متأخر: ' . floor($late_time_minutes / 60) . ' ساعة و ' . ($late_time_minutes % 60) . ' دقيقة (مستثنى بتصحيح بصمة: ' . $correction_type . ')';
          $class_tags[] = 'fingerprint-correction';
        } else {
          $messages_today[] = 'دخول متأخر: ' . floor($late_time_minutes / 60) . ' ساعة و ' . ($late_time_minutes % 60) . ' دقيقة';
          $class_tags[] = 'late-arrival';
          // الخصم يُقسم على ساعات العمل من القيود
          $total_late_minutes += $late_time_minutes; 
          $total_late_exit_deduction += ($late_time_minutes * $daily_salary / $hours_divisor / 60);
        }
      }

      if ($early_exit_minutes > 0) {
        if ($is_on_correction) {
          $messages_today[] = 'خروج مبكر: ' . floor($early_exit_minutes / 60) . ' ساعة و ' . ($early_exit_minutes % 60) . ' دقيقة (مستثنى بتصحيح بصمة: ' . $correction_type . ')';
          $class_tags[] = 'fingerprint-correction';
        } else {
          $messages_today[] = 'خروج مبكر: ' . floor($early_exit_minutes / 60) . ' ساعة و ' . ($early_exit_minutes % 60) . ' دقيقة';
          $class_tags[] = 'early-departure';
          // الخصم يُقسم على ساعات العمل من القيود
          $total_early_exit_minutes += $early_exit_minutes;
          $total_late_exit_deduction += ($early_exit_minutes * $daily_salary / $hours_divisor / 60);
        }
      }

      if ($late_time_minutes === 0 && $early_exit_minutes === 0) {
        $violation_class = 'on-time';
        $message = 'ملتزم بوقت العمل';
      } else {
        $violation_class = implode(' ', array_unique($class_tags));
        $message = implode(' • ', $messages_today);
      }

      $display_first_punch = date('H:i', $first_punch);
      $display_last_punch  = date('H:i', $last_punch);
    }
  }

  echo "<td class='$violation_class' title='$message' data-bs-toggle='tooltip' data-bs-placement='top'>
          <strong>دخول:</strong> " . (isset($display_first_punch) ? $display_first_punch : '') . " <br><strong>خروج:</strong> " . (isset($display_last_punch) ? $display_last_punch : '') . "
        </td>";
}
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

<!-- JQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
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
      pageLength: 10,
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



<!-- Custom Script -->
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