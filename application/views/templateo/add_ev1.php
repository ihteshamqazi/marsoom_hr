<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>شاشة التقييم - مرسوم</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>

  <style>
    :root {
      --marsom-blue: #001f3f;
      --marsom-orange: #FF8C00;
      --marsom-light-blue: #2c5aa0;
      --glass-bg: rgba(255, 255, 255, 0.08);
      --glass-border: rgba(255, 255, 255, 0.2);
      --glass-shadow: rgba(0, 0, 0, 0.5);
      --card-bg: rgba(8, 34, 64, 0.96);
      --white: #ffffff;
      --danger: #F30000;
    }

    body {
      font-family: 'Tajawal', sans-serif;
      background: linear-gradient(135deg, var(--marsom-blue) 0%, #34495e 50%, var(--marsom-orange) 100%);
      background-attachment: fixed;
      background-size: 400% 400%;
      animation: gradientAnimation 20s ease infinite;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      min-height: 100vh;
      margin: 0;
      padding: 40px 15px;
      position: relative;
      color: var(--white);
    }

    @keyframes gradientAnimation {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    /* Hexagon particles */
    .particles {
      position: fixed;
      inset: 0;
      overflow: hidden;
      z-index: 0;
      pointer-events: none;
    }
    .particle {
      position: absolute;
      background: rgba(255, 140, 0, 0.1);
      clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
      animation: float 25s infinite ease-in-out;
      opacity: 0;
      filter: blur(2px);
    }
    .particle:nth-child(even) {
      background: rgba(0, 31, 63, 0.14);
    }
    .particle:nth-child(1) { width:40px; height:40px; left:10%; top:20%; animation-duration:18s; animation-delay:0s; }
    .particle:nth-child(2) { width:70px; height:70px; left:25%; top:60%; animation-duration:22s; animation-delay:2s; }
    .particle:nth-child(3) { width:55px; height:55px; left:40%; top:8%;  animation-duration:25s; animation-delay:5s; }
    .particle:nth-child(4) { width:80px; height:80px; left:60%; top:72%; animation-duration:20s; animation-delay:8s; }
    .particle:nth-child(5) { width:60px; height:60px; left:80%; top:30%; animation-duration:23s; animation-delay:10s; }
    .particle:nth-child(6) { width:45px; height:45px; left:5%;  top:85%; animation-duration:19s; animation-delay:3s; }
    .particle:nth-child(7) { width:90px; height:90px; left:70%; top:5%;  animation-duration:28s; animation-delay:6s; }
    .particle:nth-child(8) { width:35px; height:35px; left:90%; top:40%; animation-duration:17s; animation-delay:12s; }
    .particle:nth-child(9) { width:75px; height:75px; left:20%; top:75%; animation-duration:21s; animation-delay:1s; }
    .particle:nth-child(10){ width:65px; height:65px; left:50%; top:90%; animation-duration:24s; animation-delay:4s; }

    @keyframes float {
      0% { transform: translateY(0) translateX(0) rotate(0deg); opacity:0; }
      15% { opacity:1; }
      85% { opacity:1; }
      100% { transform: translateY(-100vh) translateX(40px) rotate(360deg); opacity:0; }
    }

    /* Main container */
    .main-screen-container {
      position: relative;
      z-index: 1;
      background: var(--glass-bg);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border-radius: 22px;
      border: 1px solid var(--glass-border);
      box-shadow: 0 14px 60px var(--glass-shadow);
      padding: 28px 24px 26px;
      max-width: 1200px;
      width: 100%;
      margin: auto;
      animation: fadeInScale 1s ease-out forwards;
    }

    @keyframes fadeInScale {
      from { opacity: 0; transform: translateY(30px) scale(0.96); }
      to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    .logo-container {
      text-align: center;
      margin-bottom: 6px;
    }
    .logo-container img {
      max-width: 150px;
      height: auto;
      filter: drop-shadow(0 0 8px rgba(0,0,0,0.6));
    }

    .page-header-title {
      font-family: 'El Messiri', sans-serif;
      font-size: 2rem;
      font-weight: 700;
      text-align: center;
      margin: 4px 0 6px;
      color: var(--white);
      text-shadow: 0 3px 8px rgba(0,0,0,0.45);
    }

    .page-subtitle {
      text-align: center;
      font-size: 0.95rem;
      color: rgba(255,255,255,0.8);
      margin-bottom: 18px;
    }

    /* Evaluation card */
    .eval-card {
      background: var(--card-bg);
      border-radius: 18px;
      padding: 20px 18px 18px;
      border: 1px solid rgba(255,255,255,0.16);
      box-shadow: 0 10px 30px rgba(0,0,0,0.55);
    }

    .eval-header {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 15px;
      padding-bottom: 10px;
      border-bottom: 1px solid rgba(255,255,255,0.15);
    }

    .eval-icon {
      width: 42px;
      height: 42px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, var(--marsom-orange), var(--marsom-light-blue));
      color: #fff;
      box-shadow: 0 4px 10px rgba(0,0,0,0.35);
      font-size: 1.2rem;
    }

    .eval-title {
      font-family: 'El Messiri', sans-serif;
      font-size: 1.25rem;
      font-weight: 700;
      color: var(--white);
    }

    .eval-subtitle {
      font-size: 0.8rem;
      color: rgba(255,255,255,0.75);
    }

    .info-label {
      font-weight: 600;
      font-size: 0.95rem;
      color: #f5f5f5;
      margin-bottom: 4px;
    }

    .info-value {
      font-weight: 700;
      color: var(--marsom-orange);
      margin-right: 4px;
    }

    .section-title {
      font-family: 'El Messiri', sans-serif;
      font-size: 1.1rem;
      font-weight: 700;
      color: var(--white);
      margin-top: 14px;
      margin-bottom: 8px;
    }

    .divider-soft {
      height: 1px;
      background: linear-gradient(to left,
        transparent,
        rgba(255,255,255,0.35),
        transparent);
      margin: 10px 0 14px;
    }

    .criterion-label {
      font-weight: 600;
      font-size: 0.95rem;
      color: #ffffff;
      margin-bottom: 6px;
      line-height: 1.7;
    }

    .criterion-label span.highlight {
      color: var(--marsom-orange);
      font-size: 0.9rem;
    }

    .required-star {
      color: var(--danger);
      font-weight: 700;
      margin-right: 4px;
    }

    .custom-select,
    select.form-select,
    .eval-input {
      background-color: #0f2238;
      border-radius: 10px !important;
      border: 1px solid rgba(255,255,255,0.18);
      color: #ffffff;
      padding: 7px 10px;
      font-size: 0.9rem;
      font-weight: 600;
      outline: none;
      width: 100%;
      transition: all 0.2s ease;
    }

    .custom-select:focus,
    select.form-select:focus,
    .eval-input:focus {
      border-color: var(--marsom-orange);
      box-shadow: 0 0 0 1px rgba(255,140,0,0.35);
      background-color: #132b45;
    }

    .custom-select option {
      color: #000;
      background-color: #fff;
      font-weight: 500;
    }

    .notes-label {
      font-weight: 600;
      font-size: 0.95rem;
      color: #ffffff;
      margin-top: 14px;
      margin-bottom: 6px;
    }

    .btn-submit-eval {
      margin-top: 16px;
      padding: 8px 28px;
      border-radius: 22px;
      border: none;
      background: linear-gradient(135deg, var(--marsom-orange), var(--marsom-light-blue));
      color: #fff;
      font-weight: 700;
      font-size: 0.95rem;
      box-shadow: 0 6px 16px rgba(0,0,0,0.5);
      display: inline-flex;
      align-items: center;
      gap: 8px;
      cursor: pointer;
      transition: all 0.25s ease;
    }

    .btn-submit-eval:hover {
      transform: translateY(-2px);
      box-shadow: 0 9px 20px rgba(0,0,0,0.65);
      color: #fff;
    }

    .btn-submit-eval i {
      font-size: 1rem;
    }

    .alert-eval {
      border-radius: 12px;
      padding: 8px 12px;
      font-size: 0.85rem;
      margin-bottom: 12px;
    }

    @media (max-width: 768px) {
      body {
        padding: 20px 10px;
      }
      .main-screen-container {
        padding: 20px 15px 18px;
      }
      .page-header-title {
        font-size: 1.6rem;
      }
      .eval-card {
        padding: 16px 12px 14px;
      }
    }
  </style>
</head>
<body>

<!-- خلفية الهكساقات -->
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

<div class="main-screen-container">
  <!-- الشعار -->
  <div class="logo-container">
    <!-- استبدل بالرابط الفعلي لشعار مرسوم -->
    <img src="https://via.placeholder.com/150x50/001f3f/ffffff?text=Marsom+Logo" alt="Marsom Logo">
  </div>

  <!-- عنوان الشاشة -->
  <h1 class="page-header-title">شاشة التقييم</h1>
  <div class="page-subtitle">
    نموذج تقييم أداء الموظف وفق معايير الحضور والإنتاجية والسلوك والمهام والقيادة وتطوير الذات
  </div>

  <!-- عرض أخطاء التحقق إن وجدت -->
  <?php
    $validation = validation_errors();
    if (!empty($validation)):
  ?>
    <div class="alert alert-danger alert-eval" role="alert">
      <?php echo $validation; ?>
    </div>
  <?php endif; ?>

  <!-- نموذج التقييم -->
  <?php echo form_open_multipart('users/add_ev1/'.$id, ['id' => 'evaluation-form', 'novalidate' => 'novalidate']); ?>

  <div class="eval-card">
    <div class="eval-header">
      <div class="eval-icon">
        <i class="fa fa-user-check"></i>
      </div>
      <div>
        <div class="eval-title">بيانات الموظف المراد تقييمه</div>
        <div class="eval-subtitle">يرجى مراجعة البيانات قبل اعتماد التقييم</div>
      </div>
    </div>

    <!-- بيانات أساسية -->
    <div class="row g-3 mb-2">
      <div class="col-md-4 col-12">
        <div class="info-label">اسم الموظف:</div>
        <div class="info-value"><?php echo $get_emp_data2022['m2']; ?></div>
      </div>
      <div class="col-md-4 col-12">
        <div class="info-label">الرقم الوظيفي:</div>
        <div class="info-value"><?php echo $get_emp_data2022['m1']; ?></div>
      </div>
      <div class="col-md-4 col-12">
        <div class="info-label">المسؤول المباشر:</div>
        <div class="info-value"><?php echo $get_emp_data2022['m3']; ?></div>
      </div>
    </div>

    <div class="divider-soft"></div>

    <div class="section-title">معايير التقييم</div>

    <!-- الحضور والانصراف -->
    <div class="mb-3">
      <div class="criterion-label">
        الحضور والانصراف (20%)<br>
        <span class="highlight">يشمل: الغياب، التأخير، الخروج المخالف</span>
      </div>
      <select name="w14" class="custom-select">
        <option value="<?php echo $get_emp_data_m2022['r3']; ?>">
          <?php echo $get_emp_data_m2022['r3']; ?>
        </option>
      </select>
    </div>

    <!-- الإنتاجية -->
    <div class="mb-3">
      <div class="criterion-label">
        الإنتاجية (50%)<br>
        <span class="highlight">يشمل: نسبة تحقيق المستهدف المطلوب من الموظف</span>
      </div>
      <select name="w13" class="custom-select">
        <option value="<?php echo $get_emp_data_m2022['r4']; ?>">
          <?php echo $get_emp_data_m2022['r4']; ?>
        </option>
      </select>
    </div>

    <!-- السلوك العام -->
    <div class="mb-3">
      <div class="criterion-label">
        السلوك العام (5%) <span class="required-star">*</span><br>
        <span class="highlight">
          المظهر الخارجي ونظافة مكان العمل، الزي الرسمي، الالتزام بالتعليمات وسياسات الشركة،
          الالتزام بتوجيهات المدير المباشر، التعامل مع الزملاء والرؤساء.
        </span>
      </div>
      <select name="w9" class="custom-select" required>
        <?php for ($i=1; $i<=5; $i++): ?>
          <option value="<?php echo $i; ?>" <?php echo $i==15 ? 'selected' : ''; ?>>
            <?php echo $i; ?>
          </option>
        <?php endfor; ?>
      </select>
    </div>

    <!-- المهام الوظيفية -->
    <div class="mb-3">
      <div class="criterion-label">
        المهام الوظيفية (15%)<br>
        <span class="highlight">
          التحديث الدوري للأرقام، متابعة وعود السداد، إنهاء إجراءات العملاء بعد السداد،
          جودة وصحة التقارير، الالتزام بمواعيدها، جودة معلومات الخصومات والتسويات،
          تنظيم المحفظة وتدوين الملاحظات.
        </span>
      </div>
      <select name="w21" class="custom-select">
        <?php
          // من 1 إلى 25، الافتراضي 25
          for ($i=1; $i<=15; $i++):
        ?>
          <option value="<?php echo $i; ?>" <?php echo $i==25 ? 'selected' : ''; ?>>
            <?php echo $i; ?>
          </option>
        <?php endfor; ?>
      </select>
    </div>

    <!-- المهام القيادية -->
    <div class="mb-3">
      <div class="criterion-label">
        المهام القيادية (10%)<br>
        <span class="highlight">
          تدريب وتطوير الموظفين، تعزيز روح الفريق، متابعة الأعمال، إدارة وتوجيه الفريق.
        </span>
      </div>
      <select name="w22" class="custom-select">
        <?php for ($i=1; $i<=10; $i++): ?>
          <option value="<?php echo $i; ?>" <?php echo $i==15 ? 'selected' : ''; ?>>
            <?php echo $i; ?>
          </option>
        <?php endfor; ?>
      </select>
    </div>

    <!-- تطوير الذات -->
     

    <div class="divider-soft"></div>

    <!-- ملاحظات -->
    <div class="mb-2">
      <div class="notes-label">ملاحظات على الموظف (اختياري)</div>
      <input type="text" name="w20" class="eval-input" autocomplete="off"
             placeholder="اكتب ملاحظاتك إن وجدت...">
    </div>

    <!-- زر الحفظ -->
    <div class="text-center">
      <button type="submit" name="submitForm" value="formSave" class="btn-submit-eval">
        <i class="fa fa-save"></i>
        حفظ التقييم
      </button>
    </div>

  </div><!-- /eval-card -->

  <?php echo form_close(); ?>
</div><!-- /main-screen-container -->

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- إن كانت هذه الملفات تُحمّل من الـ layout الرئيسي يمكنك حذفها هنا -->
<script src="<?php echo base_url();?>assets/bundles/libscripts.bundle.js"></script>
<script src="<?php echo base_url();?>assets/bundles/vendorscripts.bundle.js"></script>

</body>
</html>
