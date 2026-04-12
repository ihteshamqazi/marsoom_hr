<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>شيت الرواتب</title>

<!-- Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

<!-- Bootstrap 5 RTL -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

<!-- DataTables (Bootstrap 5 skin) -->
<link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">

<!-- Icons (اختياري) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<!-- AOS Animations -->
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet"/>

<style>
  :root{
    --brand:#0E1F3B;
    --accent:#F29840;
    --bg:#f5f6fa;
  }
  body{
    font-family:'Tajawal',sans-serif;
    background:linear-gradient(135deg,#f5f6fa,#e9ecf3);
  }
  .page-title{
    color:var(--brand);
    font-weight:800;
    letter-spacing:.3px;
  }
  .card-elevated{
    border:0;
    border-radius:18px;
    box-shadow:0 12px 30px rgba(0,0,0,.08);
  }
  table.dataTable thead th{
    background:var(--accent);
    color:var(--brand);
    font-weight:700;
    text-align:center;
    border-bottom:0!important;
  }
  table.dataTable tbody td{
    vertical-align:middle;
    white-space:nowrap;
  }
  /* الألوان المرتبطة بالـPHP (لا تغيّر الأسماء) */
  .on-time{
    background-color:#d4edda!important; color:#155724!important; border:1px solid #c3e6cb!important;
  }
  .late-arrival{
    background-color:#fff3cd!important; color:#856404!important; border:1px solid #ffeeba!important;
  }
  .early-departure{
    background-color:#f8d7da!important; color:#721c24!important; border:1px solid #f5c6cb!important;
  }
  .on-vacation{
    background-color:#d1ecf1!important; color:#0c5460!important; border:1px solid #bee5eb!important;
  }
  /* صفوف متحركة عند الظهور */
  tr.animated-row{ opacity:0; transform:translateY(10px); transition:.4s ease; }
  tr.animated-row.aos-animate{ opacity:1; transform:none; }
  .btn-brand{ background:var(--brand); color:#fff; }
  .btn-brand:hover{ background:#0a1425; color:#fff; }
  .btn-accent{ background:var(--accent); color:#fff; }
  .btn-accent:hover{ background:#e0882f; color:#fff; }
</style>
</head>
<body>

<div class="container py-4">

  <h2 class="page-title text-center mb-4" data-aos="fade-down">شيت الرواتب</h2>

  <?php echo validation_errors(); ?>
  <?php echo form_open_multipart('users1/sadad_report_emp'); ?>

  <div class="card card-elevated" data-aos="fade-up" data-aos-delay="100">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle dataTable-salaries w-100">
          <thead>
            <tr>
              <th>الرقم الوظيفي</th>
              <th>اسم الموظف</th>
              <th>رقم الهوية</th>
              <th>الجنسية</th>
              <th>إجمالي الأجر</th>
              <th>اسم الشركة</th>
              <!-- توليد أيام العمل (ما عدا الجمعة/السبت) -->
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
              <th>إجمالي دقائق التأخير</th>
              <th>إجمالي دقائق الخروج المبكر</th>
              <th>أيام البصمة المنفردة</th>
              <th>خصم الغياب</th>
              <th>خصم بصمة منفردة</th>
              <th>خصم التأخير/الخروج</th>
              <th>خصم الجزاءات</th>
              <th>التعويضات</th>
              <th>إجمالي خصم الحضور</th>
              <th>إجمالي الخصومات</th>
              <th>الإجمالي قبل التأمينات</th>
              <th>خصم التأمينات</th>
              <th>صافي الراتب</th>
              <th>تأمينات سعودي (9.75%)</th>
              <th>الصافي بعد التأمينات</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($employees as $row): ?>
              <tr class="animated-row" data-aos="fade-up" data-aos-delay="150">
                <td><?php echo $row->employee_id; ?></td>
                <td><?php echo $row->subscriber_name; ?></td>
                <td><?php echo $row->id_number; ?></td>
                <td><?php echo $row->nationality; ?></td>
                <td><?php echo $row->total_salary; ?></td>
                <td><?php echo $row->company_name; ?></td>

                <?php
                  // ====== (كل بلوك من كودك PHP كما هو بلا تعديل منطقي) ======
                  $violations = isset($violations[$row->employee_id]) ? $violations[$row->employee_id] : [];

                  $start_date = strtotime($get_salary_sheet['start_date']);
                  $end_date = strtotime($get_salary_sheet['end_date']);
                  $emp_code = $row->employee_id;

                  // vacations
                  $this->db->select('start_date, end_date, type');
                  $this->db->from('vacations');
                  $this->db->where('username', $emp_code);
                  $query_vacations = $this->db->get();
                  $vacations_data = $query_vacations->result_array();

                  $absence_days = 0;
                  $total_late_minutes = 0;
                  $total_early_exit_minutes = 0;
                  $single_punch_days = 0;

                  $daily_salary = $row->total_salary / 30;

                  $total_absence_deduction = 0;
                  $total_single_punch_deduction = 0;
                  $total_late_exit_deduction = 0;
                  $total_deductions = 0;

                  for ($date = $start_date; $date <= $end_date; $date = strtotime("+1 day", $date)) {
                    if (date('N', $date) == 5 || date('N', $date) == 6) { continue; }

                    $date_str = date('Y-m-d', $date);
                    $first_punch = null; $last_punch = null;
                    $violation_class = ''; $message = ''; $is_present = false;

                    $is_on_vacation = false; $vacation_type = '';
                    foreach ($vacations_data as $vacation) {
                      $vacation_start = strtotime($vacation['start_date']);
                      $vacation_end = strtotime($vacation['end_date']);
                      if ($date >= $vacation_start && $date <= $vacation_end) {
                        $is_on_vacation = true; $vacation_type = $vacation['type']; break;
                      }
                    }

                    if ($is_on_vacation) {
                      $violation_class = 'on-vacation';
                      $message = 'إجازة: ' . $vacation_type;
                      $display_first_punch = ''; $display_last_punch = '';
                    } else {
                      foreach ($attendance_data as $attendance) {
                        if ($attendance->emp_code == $emp_code && $attendance->punch_date == $date_str) {
                          $first_punch = !empty($attendance->first_punch) ? strtotime($attendance->first_punch) : null;
                          $last_punch = !empty($attendance->last_punch) ? strtotime($attendance->last_punch) : null;
                          $is_present = true; break;
                        }
                      }

                      $display_first_punch = ''; $display_last_punch = '';

                      if (!$is_present) {
                        $violation_class = 'early-departure';
                        $message = 'غياب';
                        $absence_days++;
                        $total_absence_deduction += $daily_salary;
                      } elseif (empty($first_punch) || empty($last_punch) || (($last_punch - $first_punch) < 60)) {
                        $violation_class = 'early-departure';
                        $message = 'بصمة منفردة';
                        $single_punch_days++;
                        $total_single_punch_deduction += $daily_salary;

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
                        $shortfall_seconds = $required_duration_seconds - $work_duration_seconds;
                        $late_time_seconds = $first_punch - strtotime($date_str . ' 11:00:00');

                        $late_time_minutes = floor($late_time_seconds / 60);
                        $early_exit_minutes = floor($shortfall_seconds / 60);

                        if ($late_time_minutes > 0) {
                          $violation_class = 'late-arrival';
                          $message = 'دخول متأخر: ' . floor($late_time_minutes / 60) . ' ساعة و ' . ($late_time_minutes % 60) . ' دقيقة';
                          $total_late_minutes += $late_time_minutes;
                          $total_late_exit_deduction += (($early_exit_minutes + $late_time_minutes) * $daily_salary / 9 / 60);
                        } elseif ($early_exit_minutes > 0) {
                          $violation_class = 'early-departure';
                          $message = 'خروج مبكر: ' . floor($early_exit_minutes / 60) . ' ساعة و ' . ($early_exit_minutes % 60) . ' دقيقة';
                          $total_early_exit_minutes += $early_exit_minutes;
                          $total_late_exit_deduction += ($early_exit_minutes * $daily_salary / 9 / 60);
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
                ?>

                <!-- الأعمدة التجمّعية (كما في كودك) -->
                <td><strong><?php echo $absence_days; ?> أيام غياب</strong></td>
                <td><strong><?php echo $total_late_minutes; ?> دقائق تأخير</strong></td>
                <td><strong><?php echo $total_early_exit_minutes; ?> دقائق خروج مبكر</strong></td>
                <td><strong><?php echo $single_punch_days; ?> أيام بصمة منفردة</strong></td>
                <td><strong><?php echo number_format($total_absence_deduction, 2); ?> ريال</strong></td>
                <td><strong><?php echo number_format($total_single_punch_deduction, 2); ?> ريال</strong></td>
                <td><strong><?php echo number_format($total_late_exit_deduction, 2); ?> ريال</strong></td>

                <?php
                  $discount_data = $this->hr_model->get_discounts($this->uri->segment(3, 0), $row->employee_id);
                  $discount_amount = isset($discount_data['amount']) ? $discount_data['amount'] : 0;

                  $get_reparations = $this->hr_model->get_reparations($this->uri->segment(3, 0), $row->employee_id);
                  $reparations_amount = isset($get_reparations['amount']) ? $get_reparations['amount'] : 0;
                ?>

                <td><strong><?php echo number_format($discount_amount, 2); ?> ريال</strong></td>
                <td><strong><?php echo number_format($reparations_amount, 2); ?> ريال</strong></td>

                <td><strong><?php echo number_format($total_absence_deduction + $total_single_punch_deduction + $total_late_exit_deduction, 2); ?> ريال</strong></td>
                <td><strong><?php echo number_format($total_absence_deduction + $discount_amount + $total_single_punch_deduction + $total_late_exit_deduction, 2); ?> ريال</strong></td>

                <?php
                  $final_salary = $row->total_salary + $reparations_amount - $discount_amount - ($total_absence_deduction + $total_single_punch_deduction + $total_late_exit_deduction);
                ?>
                <td><strong><?php echo number_format($final_salary, 2); ?> ريال</strong></td>

                <?php
                  if ($row->nationality === 'سعودي') {
                    $final_salary111 = ($row->base_salary + $row->housing_allowance) * 0.0975;
                  } else { $final_salary111 = 0; }
                ?>
                <td><strong><?php echo number_format($final_salary111, 2); ?> ريال</strong></td>

                <?php $final_salary555 = $final_salary - $final_salary111; ?>
                <td><strong><?php echo number_format($final_salary555, 2); ?> ريال</strong></td>

                <!-- إضافة الأعمدة الأخيرة -->
<!-- اجمالي الراتب ما قبل خصم التأمينات -->
<td>
  <strong>
    <?php 
      // الراتب قبل التأمينات (بعد الخصومات والإضافات)
      echo number_format($final_salary, 2); 
    ?> ريال
  </strong>
</td>

<!-- اجمالي خصم التامينات -->
<td>
  <strong>
    <?php 
      // قيمة التأمينات للمواطن السعودي
      echo number_format($final_salary111, 2); 
    ?> ريال
  </strong>
</td>

<!-- صافي الراتب -->
<td>
  <strong>
    <?php 
      echo number_format($final_salary, 2); 
    ?> ريال
  </strong>
</td>

<!-- تأمينات سعودي (9.75%) -->
<td>
  <strong>
    <?php 
      echo number_format($final_salary111, 2); 
    ?> ريال
  </strong>
</td>

<!-- الصافي بعد التأمينات -->
<td>
  <strong>
    <?php 
      echo number_format($final_salary555, 2); 
    ?> ريال
  </strong>
</td>



              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <?php echo form_close(); ?>

</div>

<!-- JS ============================================= -->

<!-- Bootstrap 5 (Bundle يتضمن Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery (مطلوب لـ DataTables فقط) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables + Buttons (Bootstrap 5) -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>

<!-- AOS -->
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

<script>
  // DataTables
  $(function(){
    $('.dataTable-salaries').DataTable({
      dom: '<"d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3"Bf>rt<"d-flex justify-content-between align-items-center mt-3"lip>',
      buttons: [
        { extend: 'copyHtml5', className: 'btn btn-accent' },
        { extend: 'excelHtml5', className: 'btn btn-brand' },
        { extend: 'pdfHtml5', className: 'btn btn-danger' },
        { extend: 'colvis', className: 'btn btn-outline-secondary' }
      ],
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json"
      },
      pageLength: 25,
      responsive: true
    });
  });

  // Bootstrap 5 Tooltips
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el){
    new bootstrap.Tooltip(el);
  });

  // AOS
  AOS.init({ duration: 600, once: true, easing: 'ease-out' });
</script>

</body>
</html>
