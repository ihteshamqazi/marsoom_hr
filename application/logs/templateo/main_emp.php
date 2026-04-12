<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام  الموارد البشرية    </title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <!-- Google Fonts - Inter for clean typography, and El Messiri for luxury -->
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        :root {
            --marsom-blue: #001f3f; /* Deep blue from logo */
            --marsom-orange: #FF8C00; /* Warm orange from logo */
            --text-light: #ffffff;
            --text-muted-light: rgba(255, 255, 255, 0.7);
            --glass-bg: rgba(255, 255, 255, 0.08); /* More subtle transparency */
            --glass-border: rgba(255, 255, 255, 0.2);
            --glass-shadow: rgba(0, 0, 0, 0.5);
        }

        body {
            font-family: 'El Messiri', sans-serif; /* Default body font */
            overflow: hidden; /* Prevent scrollbar from particle animation */
            background: linear-gradient(135deg, var(--marsom-blue) 0%, #34495e 50%, var(--marsom-orange) 100%); /* Brand colors gradient */
            background-size: 400% 400%;
            animation: gradientAnimation 20s ease infinite;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 15px;
            position: relative;
        }

        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Particle Background Animation with Hexagons */
        .particles {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            overflow: hidden;
            z-index: 0; /* Behind main content */
        }

        .particle {
            position: absolute;
            background: rgba(255, 140, 0, 0.1); /* Subtle orange tint for particles */
            clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%); /* Hexagon shape */
            animation: float 25s infinite ease-in-out;
            opacity: 0;
            filter: blur(2px);
        }
        .particle:nth-child(even) { background: rgba(0, 31, 63, 0.1); } /* Blue tint for even particles */


        /* Random sizes and positions for particles */
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
            100% { transform: translateY(-100vh) translateX(50px) rotate(360deg); opacity: 0; } /* Added rotation */
        }

        .main-screen-container {
            background: var(--glass-bg);
            backdrop-filter: blur(15px); /* Stronger blur for more depth */
            -webkit-backdrop-filter: blur(15px);
            border-radius: 20px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 10px 50px 0 var(--glass-shadow); /* Deeper, more luxurious shadow */
            padding: 45px;
            max-width: 900px; /* Wider container for buttons */
            width: 100%;
            animation: fadeInScale 1.2s ease-out forwards;
            z-index: 1;
            color: var(--text-light);
        }

        @keyframes fadeInScale {
            from { opacity: 0; transform: translateY(-50px) scale(0.9); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .logo-container {
            margin-bottom: 30px;
            text-align: center;
        }
        .logo-container img {
            max-width: 200px; /* Adjust logo size */
            height: auto;
            filter: drop-shadow(0 0 10px rgba(0,0,0,0.5)); /* Subtle shadow for logo */
        }

        h2 {
            font-family: 'El Messiri', sans-serif; /* Luxurious font for heading */
            color: var(--text-light);
            font-weight: 700;
            margin-bottom: 35px;
            text-shadow: 0 3px 6px rgba(0, 0, 0, 0.4);
        }

        /* Styles for quick action buttons */
        .quick-actions-grid {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid var(--glass-border);
        }

        .quick-action-button {
            background: rgba(255, 255, 255, 0.1); /* Subtle background */
            border: 1px solid var(--glass-border);
            border-radius: 10px;
            padding: 20px; /* More padding for larger buttons */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            height: 100%; /* Ensure buttons in grid have same height */
        }

        .quick-action-button:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            color: var(--marsom-orange); /* Highlight icon/text on hover */
            border-color: var(--marsom-orange);
        }

        .quick-action-button i {
            font-size: 2.5em; /* Larger icons */
            margin-bottom: 15px; /* More space below icon */
            color: var(--text-light); /* Default icon color */
            transition: color 0.3s ease;
        }
        .quick-action-button:hover i {
            color: var(--marsom-orange); /* Orange icon on hover */
        }

        .quick-action-button span {
            font-size: 1.1em; /* Larger text */
            font-weight: 500;
            text-align: center;
            font-family: 'El Messiri', sans-serif; /* Apply luxurious font to button text */
        }
        
        /* Top Fixed Navigation for Back and Home buttons */
        .top-fixed-nav {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 100;
            display: flex;
            gap: 10px;
        }
        .top-fixed-nav .btn {
            background-color: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: var(--text-light);
            border-radius: 10px;
            padding: 8px 15px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        .top-fixed-nav .btn:hover {
            background-color: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.3);
        }
        .top-fixed-nav .btn i {
            color: var(--text-light);
        }
        .top-fixed-nav .btn:hover i {
            color: var(--marsom-orange);
        }
    </style>
</head>
<body>

    <!-- Particle Background -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <!-- Top Fixed Navigation for Back and Home buttons -->

    <div class="top-fixed-nav">

        <button class="btn btn-secondary">
     مرحباً  <?php echo $this->session->userdata('name') ?>
</button>


          <button class="btn btn-secondary" onclick="location.href='<?= site_url('users/logout'); ?>'"><i class="fas fa-right-from-bracket me-2"></i> خروج</button>

<button class="btn btn-secondary" >
  <i class="fas fa-bullhorn me-2"></i> الإعلانات
</button>

<button class="btn btn-secondary" >
  <i class="fas fa-bell me-2"></i> الإشعارات
</button>

<?php 
  $username = $this->session->userdata('username'); 
  $allowed_users = array('1835', '2230', '2515', '2774', '2784','2901');
?>
<?php 
  
  $allowed_users_letter = array('1835','1127','2901');
?>
 

<!-- زر الموارد البشرية (يظهر فقط للمصرح لهم) -->
<?php if(in_array($username, $allowed_users)): ?>
  <a href="<?php echo site_url('users1/main_hr1'); ?>" class="btn btn-primary">
    <i class="fas fa-users-cog me-2"></i> الموارد البشرية
  </a>
<?php endif; ?>





 



       
        
            <img style="height:40px; width:auto;"    src="<?php echo base_url();?>/assets/imeges/m2.PNG" alt="Marsom Logo">
         

    </div>

    <div class="main-screen-container position-relative">
         
        <h2 class="text-center mb-5"> الموارد البشرية </h2>

        <!-- Quick Actions / Main Buttons Section -->
        <div class="quick-actions-grid">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
               <div class="col">
  <a href="<?php echo site_url('users1/orders_emp'); ?>" class="quick-action-button">
    <i class="fas fa-clipboard-list"></i> <!-- الطلبات -->
    <span>الطلبات</span>
  </a>
</div>



<div class="col">
  <a href="<?php echo site_url('users1/attendance/'); ?>" class="quick-action-button">
    <i class="fas fa-fingerprint"></i> 
    <span>الحضور والانصراف</span>
  </a>
</div>
<div class="col">
  <a href="<?php echo site_url('users1/orders_emp_app'); ?>" class="quick-action-button">
    <i class="fas fa-clipboard-list"></i> <!-- الطلبات -->
    <span>طلبات الموظفين</span>
  </a>
</div>
<!-- <div class="col">
  <a href="<?php echo site_url('users1/payroll_view101/1'); ?>" class="quick-action-button">
    <i class="fas fa-file-invoice-dollar"></i> 
    <span>الرواتب</span>
  </a>
</div>

<div class="col">
  <a href="<?php echo site_url('users1/violations/1'); ?>" class="quick-action-button">
    <i class="fas fa-users"></i> 
    <span>الموظفين</span>
  </a>
</div>

<div class="col">
  <a href="<?php echo site_url('users1/reparations101'); ?>" class="quick-action-button">
    <i class="fas fa-calendar-days"></i> 
    <span>الإجازات</span>
  </a>
</div>

<div class="col">
  <a href="<?php echo site_url('users1/discounts101'); ?>" class="quick-action-button">
    <i class="fas fa-chart-line"></i> 
    <span>التقارير</span>
  </a>
</div>

<div class="col">
  <a href="<?php  echo site_url('users1/profile');  ?>" class="quick-action-button">
    <i class="fas fa-id-card-clip"></i>  
    <span>الملف الشخصي</span>
  </a>
</div> 

<div class="col">
    <a href="<?php echo site_url('users2/main1'); ?>" class="quick-action-button">
        <i class="fas fa-mobile-alt"></i> 
        <span>تطبيق مرسوم</span>
    </a>
</div> -->
<div class="col">
    <a href="<?php echo site_url('users1/profile'); ?>" class="quick-action-button">
        <i class="fas fa-id-card"></i> <span>ملف الموظف</span>
    </a>
</div>
<div class="col">
    <a href="<?php echo site_url('users1/my_clearance_tasks'); ?>" class="quick-action-button">
        <i class="fas fa-tasks"></i> <span>مهام المخالصة</span>
    </a>
</div>
<?php if(in_array($username, $allowed_users_letter)): ?>
 <div class="col">
    <a href="<?php echo site_url('users1/letter_management'); ?>" class="quick-action-button">
        <i class="fas fa-tasks"></i> <span>رسائل الموظفين </span>
    </a>
</div>

 <?php endif; ?>







            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        // Define CSS variables for use in JavaScript if needed, or just for consistency
        document.documentElement.style.setProperty('--marsom-blue-rgb', '0, 31, 63');
        document.documentElement.style.setProperty('--marsom-orange-rgb', '255, 140, 0');
    </script>

    <?php
  $username = html_escape($this->session->userdata('username') ?? '');
  $name     = html_escape($this->session->userdata('name') ?? '');
?>
<div class="toast-container position-fixed bottom-0 start-0 p-3" style="z-index:1080">
  <div id="welcomeToast" class="toast align-items-center text-bg-primary border-0" role="alert"
       aria-live="assertive" aria-atomic="true" data-bs-delay="4000">
    <div class="d-flex">
      <div class="toast-body">
        مرحباً <?= $name ?: 'ضيف' ?> (<?= $username ?>) 👋 — يسعدنا وجودك!
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>


<script>
  document.getElementById('btnNotifications').addEventListener('click', function () {
    var toastEl = document.getElementById('welcomeToast');
    var toast   = new bootstrap.Toast(toastEl);
    toast.show();
  });
</script>

<script>
  window.addEventListener('load', function () {
    var toastEl = document.getElementById('welcomeToast');
    var toast   = new bootstrap.Toast(toastEl);
    toast.show();
  });
</script>

</body>
</html>