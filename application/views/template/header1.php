<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>مرسوم -  الموارد البشرية</title>
  <link rel="icon" href="<?php echo base_url();?>/images/fav.png" />

  <!-- Bootstrap CSS -->
  <link href="<?php echo base_url();?>/old-folders/assets/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer"/>
  
  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@100..900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;700&display=swap" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?php echo base_url();?>/old-folders/assets/css/style2.css">
  <link rel="stylesheet" href="<?php echo base_url();?>/old-folders/assets/css/phone.css">

  <!-- Calendar Specific Styles -->
  <style>
  :root{
      --marsom-blue:#001f3f; --marsom-orange:#FF8C00; --text-light:#fff;
      --glass-bg:rgba(255,255,255,.08); --glass-border:rgba(255,255,255,.2); --glass-shadow:rgba(0,0,0,.5);
      --vac:#1abc9c;    --half:#8e44ad;  --corr:#f39c12; --abs:#dc3545; --week:#6c757d;
      --holiday: #3498db;
  }
  
  .attendance-page body {
      font-family:'Tajawal',sans-serif;
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      min-height: 100vh;
      margin: 0;
      padding: 0;
      color: #333;
  }
  
  .attendance-container {
      margin-right: 20px; /* Space for sidebar */
      padding: 20px;
      margin-top: 50px; /* Space for navbar */
      min-height: calc(100vh - 70px);
  }
  
  @media (max-width: 768px) {
      .attendance-container {
          margin-right: 0;
          padding: 15px;
          margin-top: 60px;
      }
  }
  
  /* Rest of your calendar styles... */
  .gradient-bg {
      background: linear-gradient(135deg, var(--marsom-blue) 0%, #34495e 50%, var(--marsom-orange) 100%);
      background-size: 400% 400%;
      animation: gradientAnimation 20s ease infinite;
      position: relative;
      border-radius: 20px;
      padding: 20px;
  }
  
  @keyframes gradientAnimation {
      0% { background-position: 0% 50% }
      50% { background-position: 100% 50% }
      100% { background-position: 0% 50% }
  }
  /* Sidebar Section Headers */
.sidebar-section {
    padding: 15px 20px 8px;
    font-weight: 700;
    color: #fff;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background: rgba(255,255,255,0.1);
    margin-top: 10px;
    border-top: 1px solid rgba(255,255,255,0.2);
    cursor: default;
}

.sidebar-section:first-child {
    margin-top: 0;
    border-top: none;
}

/* Update sidebar styles for better organization */
.sidebar ul li {
    border-bottom: 1px solid rgba(255,255,255,0.05);
}

.sidebar ul li a {
    padding: 12px 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.sidebar ul li a i img {
    width: 18px;
    height: 18px;
    filter: brightness(0) invert(1);
    opacity: 0.8;
}

.sidebar ul li a.active i img {
    opacity: 1;
}
  /* Continue with all your calendar-specific styles from your original code */
  .main-screen-container {
      background: var(--glass-bg);
      backdrop-filter: blur(15px);
      -webkit-backdrop-filter: blur(15px);
      border: 1px solid var(--glass-border);
      box-shadow: 0 10px 50px 0 var(--glass-shadow);
      border-radius: 20px;
      padding: 20px;
      width: 100%;
      max-width: 1000px;
      margin: 0 auto;
  }
  
  /* Add all other calendar styles here... */
  </style>
</head>
<body class="attendance-page">

  <!-- Loading Screen -->
  <!-- <div class="loading">
    <div class="logo-center"><img src="<?php echo base_url();?>/images/loading.svg" alt=""></div>
  </div> -->
  
  <!-- Header Section -->
  <section class="home">
    <div class="container">
      <!-- Navbar -->
      <div class="navbar col-12">
        <div class="box">
          <div class="link"><a href="<?php echo site_url('users1/main_emp'); ?>"><img src="<?php echo base_url();?>/images/logo-dash.svg" alt=""></a></div>
          <div class="link link-menu">
            <ul>
              <li><a href="#"> ادارة المهام </a></li>
              <li><a href="#">  أمر صرف</a></li>
              <li><a href="#">  تقرير السدادات</a></li>
              <li><a href="#">  ادارة المهام </a></li>
              <li><a href="#">  طلبات الإقفال</a></li>
              <li><a href="#"> تقرير افضل اداء</a></li>
            </ul>
          </div>
        </div>
        <div class="box">
          <div class="link box-welcom">
            <div class="welcom">
              <img src="<?php echo base_url();?>/images/man.png" alt="">
              <span>مرحباً, <?= html_escape($this->session->userdata('name') ?: 'صالح') ?></span>
            </div>
          </div>
     
          <a class="links" href="#"><img src="<?php echo base_url();?>/images/gear.svg" alt=""></a>
          <a class="links" href="#"><img src="<?php echo base_url();?>/images/not.svg" alt=""></a>
          <a class="links" href="javascript:history.back()"><img src="<?php echo base_url();?>/images/nav_l.png" alt=""></a>
        </div>
      </div><!-- navbar -->

      <!-- Sidebar -->
      <!-- Sidebar -->
<!-- Sidebar -->
<div class="sidebar col-12 col-md-3">
    <ul>
        <li><a href="<?php echo site_url('users1/main_emp'); ?>"><i class="fas fa-home"></i> الصفحة الرئيسية</a></li>
        <li><a href="<?php echo site_url('users1/orders_emp'); ?>"><i class="fas fa-file-alt"></i> طلباتي الشخصية</a></li>
        <li><a href="<?php echo site_url('users1/orders_emp_app'); ?>"><i class="fas fa-users"></i> طلبات الموظفين</a></li>
        <li><a href="<?php echo site_url('users1/mandate_request'); ?>"><i class="fas fa-plane-departure"></i> طلب انتداب</a></li>
        <li><a href="<?php echo site_url('users1/mandate_approvals'); ?>"><i class="fas fa-check-double"></i> اعتماد الانتدابات</a></li>
        <li><a href="<?php echo site_url('users1/eos_approvals'); ?>"><i class="fas fa-check-to-slot"></i> نهاية الخدمة</a></li>
        <li><a href="<?php echo site_url('users1/insurance_approvals'); ?>"><i class="fas fa-heartbeat"></i> الموافقات التأمينية</a></li>
        <li><a href="<?php echo site_url('users1/new_insurance_request'); ?>"><i class="fas fa-file-medical"></i> طلبات التأمين</a></li>
        <li><a href="<?php echo site_url('users1/my_mandates'); ?>"><i class="fas fa-history"></i> سجل الانتدابات</a></li>
        <li><a href="<?php echo site_url('users1/my_insurance_requests'); ?>"><i class="fas fa-file-medical-alt"></i> سجل التأمين الطبي</a></li>
        
        <!-- الحضور والرواتب Section -->
        <li class="sidebar-section"><span>الحضور والرواتب</span></li>
        <li><a href="<?php echo site_url('users1/attendance'); ?>"><i class="fas fa-fingerprint"></i> الحضور والانصراف</a></li>
        <li><a href="<?php echo site_url('users1/my_salary_slips'); ?>"><i class="fas fa-file-invoice-dollar"></i> قسائم الراتب</a></li>
        
        <!-- إدارة المهام Section -->
        <li class="sidebar-section"><span>إدارة المهام</span></li>
      <!--  <li><a href="<?php echo site_url('users1/task_manager_dashboard'); ?>"><i class="fas fa-cogs"></i> إدارة المهام</a></li>
        <li><a href="<?php echo site_url('users1/my_tasks_dashboard'); ?>"><i class="fas fa-clipboard-check"></i> مهامي</a></li> -->
        <li><a href="<?php echo site_url('users1/my_clearance_tasks'); ?>"><i class="fas fa-check-circle"></i> مهام المخالصة</a></li>
        <li><a href="<?php echo site_url('users1/violations_list'); ?>"><i class="fas fa-clipboard-list"></i> سجل المخالفات</a></li>
        
        <!-- المعلومات الشخصية Section -->
        <li class="sidebar-section"><span>المعلومات الشخصية</span></li>
        <li><a href="<?php echo site_url('users1/profile'); ?>"><i class="fas fa-user-circle"></i> ملف الموظف</a></li>
        <li><a href="<?php echo site_url('users1/team_balances_dashboard'); ?>"><i class="fas fa-calendar-alt"></i> أرصدة الإجازات</a></li>
        <li><a href="<?php echo site_url('users1/leave_capacity_dashboard'); ?>"><i class="fas fa-users-cog"></i> أرصدة الفريق</a></li>
        
        <!-- Special Access for Specific Users -->
        <?php if(in_array($this->session->userdata('username') ?? '', array('1835', '1127', '2901'))): ?>
        <li><a href="<?php echo site_url('users1/letter_management'); ?>"><i class="fas fa-envelope"></i> رسائل الموظفين</a></li>
        <?php endif; ?>
        
        <!-- التقييم والأداء Section -->
        <li class="sidebar-section"><span>التقييم والأداء</span></li>
        <li><a href="<?php echo site_url('users1/employee_survey'); ?>"><i class="fas fa-poll-h"></i> استبيان الرضا</a></li>
        <li><a href="<?php echo site_url('users1/happiness_index'); ?>"><i class="fas fa-smile-beam"></i> مؤشر السعادة</a></li>
        <li><a href="<?php echo site_url('users/user_report101'); ?>" target="_blank"><i class="fas fa-chart-bar"></i> تقارير التقييم</a></li>
    </ul>
</div>
      <!-- Main Content Area -->
      <div class="main-content-area">