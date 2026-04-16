<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
  $uri_path = trim(strtolower((string) $this->uri->uri_string()), '/');

  // Exact match or child path only
  $is_path = function (string $match) use ($uri_path): string {
      $m = trim(strtolower($match), '/');
      if ($m === '') {
          return '';
      }  
    //   if ($m === 'dashboard') {
    //       return ($uri_path === '' || $uri_path === 'dashboard') ? 'active' : '';
    //   }
      if ($uri_path === $m) {
          return 'active';
      }
      if (strpos($uri_path, $m . '/') === 0) {
          return 'active';
      }
      return '';
  };
?>

<div class="sidebar-overlay" id="sidebarOverlay"></div>
<div class="sidebar col-12 col-md-3" id="mainSidebar">
    <button type="button" class="sidebar-close d-md-none" id="sidebarClose" aria-label="إغلاق"><i class="bi bi-x-lg"></i></button>
    <img src="<?= base_url('newassets/images/logo.svg'); ?>" alt="">
    <div class="body">
        <ul>
            <!-- Stripped "hr/" from paths that start with it -->
            <li><a class="<?= $is_path('users1/orders_emp'); ?>" href="<?= site_url('users1/orders_emp'); ?>"><i class="bi bi-inbox"></i> طلباتي</a></li>
            <li><a class="<?= $is_path('users1/orders_emp_app'); ?>" href="<?= site_url('users1/orders_emp_app'); ?>"><i class="bi bi-people"></i> طلبات الموظفين</a></li>
            <li><a class="<?= $is_path('users1/mandate_request'); ?>" href="<?= site_url('users1/mandate_request'); ?>"><i class="bi bi-send"></i> طلب انتداب</a></li>
            <li><a class="<?= $is_path('users1/mandate_approvals'); ?>" href="<?= site_url('users1/mandate_approvals'); ?>"><i class="bi bi-check2-circle"></i> اعتماد الانتدابات</a></li>
            <li><a class="<?= $is_path('ramadan/remote_work'); ?>" href="<?= site_url('ramadan/remote_work'); ?>"><i class="bi bi-house"></i> العمل عن بعد</a></li>
            <li><a class="<?= $is_path('users1/manage_employees_list'); ?>" href="<?= site_url('users1/manage_employees_list'); ?>"><i class="bi bi-pencil-square"></i> تعديل بيانات الموظف</a></li>
            <li><a class="<?= $is_path('users1/manage_documents'); ?>" href="<?= site_url('users1/manage_documents'); ?>"><i class="bi bi-files"></i> إدارة المستندات (Documents)</a></li>
            <li><a class="<?= $is_path('users1/renewal_system'); ?>" href="<?= site_url('users1/renewal_system'); ?>"><i class="bi bi-arrow-repeat"></i> نظام تجديد الهويات</a></li>
            <li><a class="<?= $is_path('users1/overtime_dashboard'); ?>" href="<?= site_url('users1/overtime_dashboard'); ?>"><i class="bi bi-clock-history"></i> متابعة العمل الإضافي</a></li>
            <li><a class="<?= $is_path('users1/eos_approvals'); ?>" href="<?= site_url('users1/eos_approvals'); ?>"><i class="bi bi-flag"></i> نهاية الخدمة</a></li>
            <li><a class="<?= $is_path('users1/insurance_approvals'); ?>" href="<?= site_url('users1/insurance_approvals'); ?>"><i class="bi bi-shield"></i> الموافقات التأمينية</a></li>
            <li><a class="<?= $is_path('users1/new_insurance_request'); ?>" href="<?= site_url('users1/new_insurance_request'); ?>"><i class="bi bi-heart"></i> طلبات التأمين</a></li>
            <li><a class="<?= $is_path('users1/my_mandates'); ?>" href="<?= site_url('users1/my_mandates'); ?>"><i class="bi bi-journal"></i> سجل الانتدابات</a></li>
            <li><a class="<?= $is_path('users1/my_insurance_requests'); ?>" href="<?= site_url('users1/my_insurance_requests'); ?>"><i class="bi bi-heart-half"></i> سجل التأمين الطبي</a></li>
            <li><a class="<?= $is_path('users1/attendance'); ?>" href="<?= site_url('users1/attendance/'); ?>"><i class="bi bi-calendar-check"></i> الحضور والانصراف</a></li>
            <li><a class="<?= $is_path('users1/my_salary_slips'); ?>" href="<?= site_url('users1/my_salary_slips'); ?>"><i class="bi bi-cash-stack"></i> قسائم الراتب</a></li>
            <li><a class="<?= $is_path('sla/SlaEmployeePortal'); ?>" href="<?= site_url('sla/SlaEmployeePortal'); ?>"><i class="bi bi-ticket"></i> منصة التذاكر (SLA)</a></li>
            <li><a class="<?= $is_path('users1/task_manager_dashboard'); ?>" href="<?= site_url('users1/task_manager_dashboard'); ?>"><i class="bi bi-tasks"></i> إدارة المهام</a></li>
            <li><a class="<?= $is_path('users1/my_tasks_dashboard'); ?>" href="<?= site_url('users1/my_tasks_dashboard'); ?>"><i class="bi bi-list-check"></i> مهامي</a></li>
            <li><a class="<?= $is_path('users1/my_clearance_tasks'); ?>" href="<?= site_url('users1/my_clearance_tasks'); ?>"><i class="bi bi-x-circle"></i> مهام المخالصة</a></li>
            <li><a class="<?= $is_path('users1/violations_list'); ?>" href="<?= site_url('users1/violations_list'); ?>"><i class="bi bi-exclamation-triangle"></i> سجل المخالفات</a></li>
            <li><a class="<?= $is_path('collection/meetings'); ?>" href="<?= site_url('collection/meetings'); ?>"><i class="bi bi-people"></i> الاجتماعات</a></li>
            <li><a class="<?= $is_path('collection/ai/inbox'); ?>" href="<?= site_url('collection/ai/inbox'); ?>"><i class="bi bi-robot"></i> مستشار الذكاء الاصطناعي</a></li>
            <li><a class="<?= $is_path('AnnualIncentives'); ?>" href="<?= site_url('AnnualIncentives'); ?>"><i class="bi bi-gift"></i> الحوافز السنوية</a></li>
            <li><a class="<?= $is_path('poll/TrainingControlPanel'); ?>" href="<?= site_url('poll/TrainingControlPanel'); ?>"><i class="bi bi-bar-chart-steps"></i> لوحة تحكم الدورات والاستبيانات</a></li>
            <li><a class="<?= $is_path('recruitment2/MdPendingEvaluations'); ?>" href="<?= site_url('recruitment2/MdPendingEvaluations'); ?>"><i class="bi bi-hourglass"></i> طلبات العضو المنتدب المعلقة (التوظيف)</a></li>
            <li><a class="<?= $is_path('users1/profile'); ?>" href="<?= site_url('users1/profile'); ?>"><i class="bi bi-sun"></i> أرصدة الإجازات</a></li>
            <li><a class="<?= $is_path('users1/team_balances_dashboard'); ?>" href="<?= site_url('users1/team_balances_dashboard'); ?>"><i class="bi bi-emoji-smile"></i> مؤشر السعادة</a></li>
            <li><a class="<?= $is_path('users1/leave_capacity_dashboard'); ?>" href="<?= site_url('users1/leave_capacity_dashboard'); ?>"><i class="bi bi-people"></i> أرصدة الفريق</a></li>
            <li><a class="<?= $is_path('users1/letter_management'); ?>" href="<?= site_url('users1/letter_management'); ?>"><i class="bi bi-envelope"></i> رسائل الموظفين</a></li>
            <li><a class="<?= $is_path('users1/employee_survey'); ?>" href="<?= site_url('users1/employee_survey'); ?>"><i class="bi bi-star"></i> استبيان الرضا</a></li>
            <li><a class="<?= $is_path('users1/happiness_index'); ?>" href="<?= site_url('users1/happiness_index'); ?>"><i class="bi bi-emoji-smile"></i> مؤشر السعادة</a></li>
            <li><a class="<?= $is_path('users/user_report101'); ?>" href="<?= site_url('users/user_report101'); ?>"><i class="bi bi-graph-up"></i> تقارير التقييم</a></li>
            <li><a class="<?= $is_path('AnnualEvaluation'); ?>" href="<?= site_url('AnnualEvaluation'); ?>"><i class="bi bi-person-check"></i> التقييم الذاتي</a></li>
            <li><a class="<?= $is_path('emp_management/index'); ?>" href="<?= site_url('emp_management/index'); ?>"><i class="bi bi-person-badge"></i> إدارة الموظفين (EMP1)</a></li>
            <li><a class="<?= $is_path('emp_management/v_orders_emp'); ?>" href="<?= site_url('emp_management/v_orders_emp'); ?>"><i class="bi bi-card-list"></i> إدارة الطلبات (ORDERS_EMP)</a></li>
            <li><a class="<?= $is_path('emp_management/v_approval_workflow'); ?>" href="<?= site_url('emp_management/v_approval_workflow'); ?>"><i class="bi bi-diagram-3"></i> إدارة سير عمل الموافقة (APPROVAL_WORKFLOW)</a></li>
            <li><a class="<?= $is_path('emp_management/attendance_logs'); ?>" href="<?= site_url('emp_management/attendance_logs'); ?>"><i class="bi bi-clock"></i> إدارة سجلات الحضور (ATTENDANCE_LOGS)</a></li>
            <li><a class="<?= $is_path('emp_management/mandate_requests'); ?>" href="<?= site_url('emp_management/mandate_requests'); ?>"><i class="bi bi-share"></i> إدارة طلبات التفويض (MANDATE_REQUESTS)</a></li>
            <li><a class="<?= $is_path('emp_management/v_attendance_summary'); ?>" href="<?= site_url('emp_management/v_attendance_summary'); ?>"><i class="bi bi-table"></i> ملخص الحضور والانصراف (ATTENDANCE_SUMMARY)</a></li>
            <li><a class="<?= $is_path('emp_management/v_payroll_process'); ?>" href="<?= site_url('emp_management/v_payroll_process'); ?>"><i class="bi bi-calculator"></i> مسير الرواتب (PAYROLL_PROCESS)</a></li>
            <li><a class="<?= $is_path('emp_management/v_discounts'); ?>" href="<?= site_url('emp_management/v_discounts'); ?>"><i class="bi bi-dash-circle"></i> الخصومات (DISCOUNTS)</a></li>
            <li><a class="<?= $is_path('emp_management/v_reparations'); ?>" href="<?= site_url('emp_management/v_reparations'); ?>"><i class="bi bi-plus-circle"></i> التعويضات (REPARATIONS)</a></li>
            <li><a class="<?= $is_path('emp_management/v_employee_violations'); ?>" href="<?= site_url('emp_management/v_employee_violations'); ?>"><i class="bi bi-alarm"></i> مخالفات الموظفين (EMPLOYEE_VIOLATIONS)</a></li>
            <li><a class="<?= $is_path('emp_management/v_employee_leave_balances'); ?>" href="<?= site_url('emp_management/v_employee_leave_balances'); ?>"><i class="bi bi-calendar-week"></i> أرصدة إجازات الموظفين (LEAVE_BALANCES)</a></li>
            <li><a class="<?= $is_path('emp_management/v_end_of_service_settlements'); ?>" href="<?= site_url('emp_management/v_end_of_service_settlements'); ?>"><i class="bi bi-file-check"></i> مخالصات نهاية الخدمة (EOS_SETTLEMENTS)</a></li>
            <li><a class="<?= $is_path('emp_management/v_resignation_clearances'); ?>" href="<?= site_url('emp_management/v_resignation_clearances'); ?>"><i class="bi bi-door-closed"></i> إخلاء طرف الاستقالات (RESIGNATION_CLEARANCES)</a></li>
            <li><a class="<?= $is_path('emp_management/v_insurance_discount'); ?>" href="<?= site_url('emp_management/v_insurance_discount'); ?>"><i class="bi bi-percent"></i> خصم التأمين (INSURANCE_DISCOUNT)</a></li>
            <li><a class="<?= $is_path('emp_management/v_new_employees'); ?>" href="<?= site_url('emp_management/v_new_employees'); ?>"><i class="bi bi-person-plus"></i> الموظفين الجدد (NEW_EMPLOYEES)</a></li>
            <li><a class="<?= $is_path('emp_management/v_stop_salary'); ?>" href="<?= site_url('emp_management/v_stop_salary'); ?>"><i class="bi bi-stop-circle"></i> إيقاف الراتب (STOP_SALARY)</a></li>
            <li><a class="<?= $is_path('emp_management/v_work_restrictions'); ?>" href="<?= site_url('emp_management/v_work_restrictions'); ?>"><i class="bi bi-lock"></i> قيود العمل (WORK_RESTRICTIONS)</a></li>

            <!-- Logout link -->
            <li><a href="<?= site_url('users/logout'); ?>"><i class="bi bi-box-arrow-right"></i> تسجيل الخروج</a></li>
        </ul>
    </div>
</div>