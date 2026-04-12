<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>شاشة دخول شركة مرسوم للتحصيل الديون</title>  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <!-- Google Fonts - Inter for clean typography, and El Messiri for luxury -->
  <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <!-- Font Awesome for Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" xintegrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
      z-index: 0; /* Behind login form */
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

    .login-container {
      background: var(--glass-bg);
      backdrop-filter: blur(15px); /* Stronger blur for more depth */
      -webkit-backdrop-filter: blur(15px);
      border-radius: 20px;
      border: 1px solid var(--glass-border);
      box-shadow: 0 10px 50px 0 var(--glass-shadow); /* Deeper, more luxurious shadow */
      padding: 45px;
      max-width: 500px;
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

    .form-label {
      color: var(--text-muted-light);
      font-weight: 500;
    }

    .input-group-text {
        background-color: var(--glass-bg);
        border: 1px solid var(--glass-border);
        color: var(--text-muted-light);
        border-right: none;
        border-radius: 8px 0 0 8px; /* Match input border radius */
    }

    .form-control {
      background-color: var(--glass-bg);
      border: 1px solid var(--glass-border);
      border-left: none;
      color: var(--text-light);
      padding: 12px 18px;
      border-radius: 0 8px 8px 0; /* Match input group border radius */
      transition: all 0.3s ease;
    }
    .input-group .form-control {
        border-radius: 0 8px 8px 0 !important; /* Force border-radius */
    }
    .input-group .input-group-text {
        border-radius: 8px 0 0 8px !important; /* Force border-radius */
    }

    .form-control::placeholder {
      color: rgba(255, 255, 255, 0.5);
    }

    .form-control:focus {
      background-color: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.4);
      box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.25);
    }

    /* Override Bootstrap's focus style for input-group */
    .input-group > .form-control:focus,
    .input-group > .input-group-text:focus {
        z-index: 2;
    }
    .input-group .form-control:focus + .input-group-text,
    .input-group .input-group-text:focus + .form-control {
      border-color: rgba(255, 255, 255, 0.4);
    }

    .btn-primary {
      background: linear-gradient(90deg, var(--marsom-orange), #ffae42); /* Orange gradient for button */
      border: none;
      border-radius: 8px;
      padding: 16px 30px;
      font-weight: 600;
      letter-spacing: 1px;
      transition: all 0.3s ease;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4); /* Deeper shadow */
    }

    .btn-primary:hover {
      transform: translateY(-4px) scale(1.03);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.6);
      filter: brightness(1.15);
    }

    .form-check-input {
      background-color: var(--glass-bg);
      border: 1px solid var(--glass-border);
      cursor: pointer;
      width: 1.1em;
      height: 1.1em;
      margin-left: .5em;
    }
    .form-check-input:checked {
      background-color: var(--marsom-orange); /* Orange for checked state */
      border-color: var(--marsom-orange);
    }
    .form-check {
      padding-right: 1.5em;
    }

    .form-check-label {
      color: var(--text-muted-light);
      cursor: pointer;
    }

    .text-link {
        color: var(--marsom-orange); /* Orange for links */
        transition: color 0.3s ease;
    }

    .text-link:hover {
        color: #ffcf92; /* Lighter orange on hover */
        text-decoration: underline;
    }

    .text-white-50 {
        color: rgba(255, 255, 255, 0.75) !important;
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

  <div class="login-container position-relative">
    <div class="logo-container">
      <img src="<?php echo base_url();?>/assets/imeges/m2.PNG" alt="Marsom Logo">
    </div>
    <h2 class="text-center mb-4">   نظام الموارد البشرية</h2>
     <?php echo form_open('users1/login');?>
      <div class="mb-3">
        <label for="email" class="form-label">البريد الإلكتروني أو اسم المستخدم</label>
        <div class="input-group">
          <span class="input-group-text"><i class="fas fa-user"></i></span>
          <input type="text" name="username" class="form-control" id="email" placeholder="أدخل بريدك الإلكتروني أو اسم المستخدم">
        </div>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">كلمة المرور</label>
        <div class="input-group">
          <span class="input-group-text"><i class="fas fa-lock"></i></span>
          <input type="password" name="password" class="form-control" id="password" placeholder="أدخل كلمة المرور">
        </div>
      </div>
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
          <input type="checkbox"  class="form-check-input" id="rememberMe">
          <label class="form-check-label" for="rememberMe">تذكرني</label>
        </div>
        <a href="#" class="text-link text-decoration-none">نسيت كلمة المرور؟</a>
      </div>
      <div class="d-grid gap-2">
         <button type="submit" name="submitForm" value="formSave" style="font-family: 'Tajawal', sans-serif; font-weight: bold;
    font-style: normal; font-size:20px; " class="btn btn-primary"  >الدخول</button>

         
      </div>
    <?php echo form_close(); ?>
    <div class="text-center mt-4">
      <p class="text-white-50">ليس لديك حساب؟ <a href="#" class="text-link text-decoration-none">إنشاء حساب جديد</a></p>
    </div>
  </div>

  <!-- Bootstrap 5 JS (optional) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script>
    // Define CSS variables for use in JavaScript if needed, or just for consistency
    document.documentElement.style.setProperty('--marsom-blue-rgb', '0, 31, 63');
    document.documentElement.style.setProperty('--marsom-orange-rgb', '255, 140, 0');
  </script>
  
</body>
</html>