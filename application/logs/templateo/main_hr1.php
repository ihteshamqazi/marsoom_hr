<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>الصفحة الرئيسية لشركة مرسوم</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, var(--marsom-blue) 0%, #34495e 50%, var(--marsom-orange) 100%);
      background-attachment: fixed;
      background-size: 400% 400%;
      animation: gradientAnimation 20s ease infinite;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      min-height: 100vh;
      margin: 0;
      padding: 50px 15px;
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
      z-index: 0;
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
      100% { transform: translateY(-100vh) translateX(50px) rotate(360deg); opacity: 0; }
    }

    .main-screen-container {
      background: var(--glass-bg);
      backdrop-filter: blur(15px);
      -webkit-backdrop-filter: blur(15px);
      border-radius: 20px;
      border: 1px solid var(--glass-border);
      box-shadow: 0 10px 50px 0 var(--glass-shadow);
      padding: 45px;
      max-width: 1140px;
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
        max-width: 200px;
        height: auto;
        filter: drop-shadow(0 0 10px rgba(0,0,0,0.5));
    }

    h2 {
      font-family: 'El Messiri', sans-serif;
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
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid var(--glass-border);
      border-radius: 10px;
      padding: 20px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      color: var(--text-light);
      text-decoration: none;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
      height: 100%;
    }

    .quick-action-button:hover {
      background: rgba(255, 255, 255, 0.15);
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
      color: var(--marsom-orange);
      border-color: var(--marsom-orange);
    }

    .quick-action-button i {
      font-size: 2.5em;
      margin-bottom: 15px;
      color: var(--text-light);
      transition: color 0.3s ease;
    }
    .quick-action-button:hover i {
        color: var(--marsom-orange);
    }

    .quick-action-button span {
      font-size: 1.1em;
      font-weight: 500;
      text-align: center;
      font-family: 'El Messiri', sans-serif;
    }
    
    /* Modal custom styling */
    .modal-content {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 15px;
      border: 1px solid rgba(255, 255, 255, 0.3);
    }
    
    .modal-header {
      border-bottom: 1px solid var(--marsom-orange);
    }
    
    .modal-title {
      color: var(--marsom-blue);
      font-family: 'El Messiri', sans-serif;
    }
    
    .btn-primary {
      background-color: var(--marsom-blue);
      border-color: var(--marsom-blue);
    }
    
    .btn-primary:hover {
      background-color: #001a35;
      border-color: #001a35;
    }
  </style>
</head>
<body>

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

  <div class="main-screen-container position-relative">
    <div class="logo-container">
      <img src="https://via.placeholder.com/200x80/001f3f/ffffff?text=Marsom+Logo" alt="Marsom Logo">
    </div>
    <h2 class="text-center mb-5">الصفحة الرئيسية</h2>

    <div class="quick-actions-grid">
     <div class="row row-cols-2 row-cols-sm-3 row-cols-md-5 g-4">
        
        <div class="col">
            <a href="<?php echo site_url('users1/emp_data101'); ?>" class="quick-action-button">
                <i class="fas fa-users"></i>
                <span>الموظفين</span>
            </a>
        </div>

        <div class="col">
            <a href="<?php echo site_url('users1/new_employees_list'); ?>" class="quick-action-button">
                <i class="fas fa-user-plus"></i>
                <span>قائمة الموظفين الجدد</span>
            </a>
        </div>
        
        <div class="col">
            <a href="<?php echo site_url('users1/main_salary'); ?>" class="quick-action-button">
                <i class="fas fa-hand-holding-dollar"></i>
                <span>الرواتب</span>
            </a>
        </div>

        <div class="col">
            <a href="#" class="quick-action-button" data-bs-toggle="modal" data-bs-target="#payrollModal">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>مسير الرواتب</span>
            </a>
        </div>

        <div class="col">
            <a href="<?php echo site_url('users1/orders_emp'); ?>" class="quick-action-button">
                <i class="fas fa-clipboard-list"></i>
                <span>طلباتي</span>
            </a>
        </div>

        <div class="col">
            <a href="<?php echo site_url('users1/orders_emp_app'); ?>" class="quick-action-button">
                <i class="fas fa-inbox"></i>
                <span>صندوق الموافقات</span>
            </a>
        </div>
        <div class="col">
            <a href="<?php echo site_url('users1/employee_requests_report'); ?>" class="quick-action-button">
                <i class="fas fa-inbox"></i>
                <span>تقرير طلبات الموظفين </span>
            </a>
        </div>
        <div class="col">
            <a href="<?php echo site_url('users1/employee_balances_report'); ?>" class="quick-action-button">
                <i class="fas fa-umbrella-beach"></i>
                <span>الإجازات</span>
            </a>
        </div>

        <div class="col">
            <a href="<?php echo site_url('users1/leave_balances_up'); ?>" class="quick-action-button">
                <i class="fas fa-calendar-day"></i>
                <span>أرصدة الإجازات</span>
            </a>
        </div>

        <div class="col">
            <a href="<?php echo site_url('users1/public_holidays'); ?>" class="quick-action-button">
                <i class="fas fa-umbrella-beach"></i>
                <span>العطلات الرسمية</span>
            </a>
        </div>

        <div class="col">
            <a href="https://services.marsoom.net/ev_inv2025/users/employee_evaluation" class="quick-action-button" target="_blank" rel="noopener noreferrer">
                <i class="fas fa-star-half-alt"></i>
                <span>التقييم</span>
            </a>
        </div>

        <div class="col">
            <a href="<?php echo site_url('users1/attendance/'); ?>" class="quick-action-button">
                <i class="fas fa-fingerprint"></i>
                <span>الحضور والانصراف</span>
            </a>
        </div>

        <div class="col">
            <a href="<?php echo site_url('users1/models_emp'); ?>" class="quick-action-button">
                <i class="fas fa-file-signature"></i>
                <span>النماذج</span>
            </a>
        </div>

        <div class="col">
            <a href="<?php echo site_url('users2/main1'); ?>" class="quick-action-button">
                <i class="fas fa-mobile-alt"></i>
                <span>تطبيق مرسوم</span>
            </a>
        </div>

        <div class="col">
            <a href="<?php echo site_url('users1/residents'); ?>" class="quick-action-button">
                <i class="fas fa-passport"></i>
                <span>المقيمين</span>
            </a>
        </div>

        <div class="col">
            <a href="https://services.marsoom.net/recruitment/users/rec_data" class="quick-action-button">
                <i class="fas fa-user-tie"></i>
                <span>التوظيف</span>
            </a>
        </div>

        <div class="col">
            <a href="https://services.marsoom.net/collection/users/productivity_report" class="quick-action-button">
                <i class="fas fa-funnel-dollar"></i>
                <span>التحصيل</span>
            </a>
        </div>

        <div class="col">
            <a href="<?php echo site_url('users1/payroll_compare'); ?>" class="quick-action-button" title="مقارنات الرواتب">
                <i class="fas fa-scale-balanced"></i>
                <span>مقارنات الرواتب</span>
            </a>
        </div>

        <div class="col">
            <a href="<?php echo site_url('users1/gosi_emp_compare'); ?>" class="quick-action-button" title="مقارنات التأمينات الاجتماعية">
                <i class="fas fa-id-card"></i>
                <span>مقارنات التأمينات</span>
            </a>
        </div>

        <div class="col">
            <a href="<?php echo site_url('users1/org_pyramid'); ?>" class="quick-action-button" title="عرض الهيكل التنظيمي">
                <i class="fas fa-sitemap"></i>
                <span>الهيكل التنظيمي</span>
            </a>
        </div>

        <div class="col">
            <a href="<?php echo site_url('users1/org_structure_management'); ?>" class="quick-action-button" title="تحديث الهيكل التنظيمي">
                <i class="fas fa-project-diagram"></i>
                <span>تحديث الهيكل</span>
            </a>
        </div>

        <div class="col">
            <a href="<?php echo site_url('users1/end_of_service'); ?>" class="quick-action-button" title="مستحقات نهاية الخدمة">
                <i class="fas fa-handshake-slash"></i>
                <span>نهاية الخدمة</span>
            </a>
        </div>

        <div class="col">
            <a href="<?php echo site_url('users1/eos_approvals'); ?>" class="quick-action-button" title="موافقات نهاية الخدمة">
                <i class="fas fa-check-to-slot"></i>
                <span>موافقات ن. الخدمة</span>
            </a>
        </div>

        <div class="col">
            <a href="<?php echo site_url('users1/clearance_form'); ?>" class="quick-action-button" title="إخلاء الطرف">
                <i class="fas fa-user-check"></i>
                <span>إخلاء الطرف</span>
            </a>
        </div>

        <div class="col">
            <a href="<?php echo site_url('users1/my_clearance_tasks'); ?>" class="quick-action-button" title="مهام إخلاء الطرف">
                <i class="fas fa-tasks"></i>
                <span>مهام إخلاء الطرف</span>
            </a>
        </div>

        <div class="col">
            <a href="<?php echo site_url('users1/resignation_process_report'); ?>" class="quick-action-button" title="طلبات الاستقالة">
                <i class="fas fa-person-walking-arrow-right"></i>
                <span>طلبات الاستقالة</span>
            </a>
        </div>

        <div class="col">
            <a href="<?php echo site_url('users1/series_of_approvals'); ?>" class="quick-action-button">
                <i class="fas fa-cogs"></i>
                <span>الإعدادات</span>
            </a>
        </div>

        <div class="col">
            <a href="<?php echo site_url('users1/insurance_discounts'); ?>" class="quick-action-button">
                <i class="fas fa-shield-halved"></i>
                <span>خصومات التأمين</span>
            </a>
        </div>

    </div>
    </div>
  </div>

  <!-- Modal placed outside the main container -->
  <div class="modal fade" id="payrollModal" tabindex="-1" aria-labelledby="payrollModalLabel" aria-hidden="true" dir="rtl">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="payrollModalLabel">
            <i class="fas fa-money-check-alt me-2"></i>تحديث فترة مسير الرواتب
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
        </div>
        <div class="modal-body">
          <p class="text-muted">أدخل اسم وتواريخ المسير الجديد. سيتم تحديث الفترة الحالية في النظام.</p>
          
          <div id="payroll-alert-container"></div>
          
          <form id="payrollForm">
            <div class="mb-3">
              <label for="type" class="form-label fw-bold">اسم المسير</label>
              <input type="text" class="form-control" id="type" name="type" placeholder="مثال: مسير رواتب سبتمبر 2025" required>
            </div>
            <div class="mb-3">
              <label for="start_date" class="form-label fw-bold">تاريخ البداية</label>
              <input type="date" class="form-control" id="start_date" name="start_date" required>
            </div>
            <div class="mb-3">
              <label for="end_date" class="form-label fw-bold">تاريخ النهاية</label>
              <input type="date" class="form-control" id="end_date" name="end_date" required>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
          <button type="button" class="btn btn-primary" id="submitPayrollBtn">
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            تحديث وحفظ
          </button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    $(document).ready(function() {
      // Set default dates for the modal
      const today = new Date();
      const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
      const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
      
      $('#start_date').val(formatDate(firstDay));
      $('#end_date').val(formatDate(lastDay));
      
      // Handle form submission
      $('#submitPayrollBtn').on('click', function() {
        const saveBtn = $(this);
        const form = $('#payrollForm');
        const alertContainer = $('#payroll-alert-container');
        
        // Reset previous alerts
        alertContainer.html('');
        
        // Basic validation
        if ($('#type').val().trim() === '' || $('#start_date').val() === '' || $('#end_date').val() === '') {
          alertContainer.html('<div class="alert alert-danger" role="alert">الرجاء ملء جميع الحقول.</div>');
          return;
        }
        
        // Show loading state
        saveBtn.prop('disabled', true).find('.spinner-border').removeClass('d-none');
        
        // Simulate API call (replace with actual AJAX call)
        setTimeout(function() {
          // Hide modal after success
          const modal = bootstrap.Modal.getInstance(document.getElementById('payrollModal'));
          modal.hide();
          
          // Show success message
          alert('تم تحديث مسير الرواتب بنجاح!');
          
          // Reset form and button state
          saveBtn.prop('disabled', false).find('.spinner-border').addClass('d-none');
          form[0].reset();
        }, 1500);
      });
      
      // Helper function to format date as YYYY-MM-DD
      function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
      }
    });
  </script>
</body>
</html>