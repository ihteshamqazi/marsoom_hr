<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
  $uri_path = trim(strtolower((string) $this->uri->uri_string()), '/');

  // Exact match or child path only (e.g. candidates/foo matches candidates/foo/bar, not candidates/foobar)
  $is_path = function (string $match) use ($uri_path): string {
      $m = trim(strtolower($match), '/');
      if ($m === '') {
          return '';
      }
      if ($m === 'dashboard') {
          return ($uri_path === '' || $uri_path === 'dashboard') ? 'active' : '';
      }
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
            <li><a class="<?= $is_path('dashboard'); ?>" href="<?= site_url('dashboard'); ?>"><i class="bi bi-speedometer2"></i> لوحة التحكم</a></li>

            <!-- <li><a class="<?= $is_path('onboarding/my_tasks'); ?>" href="<?= site_url('onboarding/my_tasks'); ?>"><i class="bi bi-list-task"></i> مهام التهيئة </a></li> -->


            <li><a href="#" class="nav-link-muted">                   
                   <i class="bi bi-list-task"></i> مهام التهيئة </a></li>

            <li><a class="<?= $is_path('jobs'); ?>" href="<?= site_url('jobs'); ?>"><i class="bi bi-megaphone"></i> إدارة الوظائف</a></li>
            <li><a class="<?= $is_path('offers'); ?>" href="<?= site_url('offers'); ?>"><i class="bi bi-award"></i> اعتماد العروض</a></li>
            <li><a class="<?= $is_path('job_description'); ?>" href="<?= site_url('job_description'); ?>"><i class="bi bi-journal-text"></i> الوصف الوظيفي</a></li>
            <li><a class="<?= $is_path('jobtitles'); ?>" href="<?= site_url('JobTitles'); ?>"><i class="bi bi-person-badge"></i> إدارة المسميات الوظيفية</a></li>
            <li><a class="<?= $is_path('candidates/my_pending_evaluations'); ?>" href="<?= site_url('candidates/my_pending_evaluations'); ?>"><i class="bi bi-clipboard-check"></i> التقييمات المطلوبة</a></li>
            <li><a class="<?= $is_path('candidates/add_manual'); ?>" href="<?= site_url('candidates/add_manual'); ?>"><i class="bi bi-person-plus"></i> إضافة مرشح يدوياً</a></li>
            <li><a class="<?= $is_path('candidateattachments'); ?>" href="<?= site_url('CandidateAttachments'); ?>"><i class="bi bi-paperclip"></i> مرفقات المرشح</a></li>
            <li><a class="<?= $is_path('candidateevaluations2'); ?>" href="<?= site_url('CandidateEvaluations2'); ?>"><i class="bi bi-clipboard-data"></i> إدارة تقييمات المرشح</a></li>
                        
            <!-- <li><a class="<?= $is_path('interviewreport'); ?>" href="<?= site_url('InterviewReport'); ?>"><i class="bi bi-mic"></i> تقرير المقابلات الوظيفية</a></li> -->
            
            <li><a href="#" class="nav-link-muted" ><i class="bi bi-mic"></i> تقرير المقابلات الوظيفية</a></li>

            <li><a class="<?= $is_path('reports'); ?>" href="<?= site_url('reports'); ?>"><i class="bi bi-bar-chart-line"></i> لوحة التقارير</a></li>
            <li><a class="<?= $is_path('candidates/regions'); ?>" href="<?= site_url('candidates/regions'); ?>"><i class="bi bi-globe"></i> تقرير المرشحين حسب المناطق</a></li>
            <li><a class="<?= $is_path('joboffersreport'); ?>" href="<?= site_url('JobOffersReport'); ?>"><i class="bi bi-file-earmark-bar-graph"></i> تقرير العروض الوظيفية</a></li>
            <li><a class="<?= $is_path('onboardingnotify'); ?>" href="<?= site_url('OnboardingNotify'); ?>"><i class="bi bi-bell"></i> إشعارات المباشرات</a></li>
            <li><a class="<?= $is_path('users/data_emp_part_new_jobs'); ?>" href="<?= site_url('users/data_emp_part_new_jobs'); ?>"><i class="bi bi-database"></i> بيانات المتقدمين</a></li>
            <li><a class="<?= $is_path('users/data_emp_part_new_jobs10_done'); ?>" href="<?= site_url('users/data_emp_part_new_jobs10_done'); ?>"><i class="bi bi-check-circle"></i> المرشحين المعتمدين</a></li>
            <li><a href="#" class="nav-link-muted" title="غير متوفر في هذا التطبيق (وحدة quality222)" onclick="return false;"><i class="bi bi-credit-card"></i> استلام البطاقة الوظيفية</a></li>
            <li><a href="#" class="nav-link-muted" title="غير متوفر في هذا التطبيق (وحدة quality222)" onclick="return false;"><i class="bi bi-card-list"></i> بيانات البطاقات الوظيفية</a></li>
            <li><a href="#" class="nav-link-muted" title="غير متوفر في هذا التطبيق (وحدة quality222)" onclick="return false;"><i class="bi bi-receipt"></i> تقرير استلام البطاقات</a></li>
            <li><a class="<?= $is_path('mdpendingevaluations'); ?>" href="<?= site_url('MdPendingEvaluations'); ?>"><i class="bi bi-hourglass-split"></i> طلبات العضو المنتدب المعلقة</a></li>
            <li><a class="<?= $is_path('smsplatform'); ?>" href="<?= site_url('SmsPlatform'); ?>"><i class="bi bi-chat-dots"></i> منصة إرسال الرسائل النصية</a></li>
            <li><a href="#" class="nav-link-muted" title="غير متوفر في هذا التطبيق (وحدة quality222)" onclick="return false;"><i class="bi bi-shield-check"></i> التحقق من رفع بيانات البطاقة</a></li>
            <li><a href="<?= site_url('users/logout'); ?>"><i class="bi bi-box-arrow-right"></i> تسجيل الخروج</a></li>
        </ul>
    </div>
</div>
