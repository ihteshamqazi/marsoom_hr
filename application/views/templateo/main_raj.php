<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>تقرير تحليل محفظة الراجحي</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- خط العنوان -->
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@700&display=swap" rel="stylesheet">
  <!-- خط الأزرار -->
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@600&display=swap" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    * {
      box-sizing: border-box;
      transition: all 0.3s ease-in-out;
    }

    body {
      margin: 0;
      padding: 0;
      background: #EEF3F8;
      font-family: 'Cairo', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .report-box {
      background: #fff;
      padding: 40px 30px;
      border-radius: 20px;
      width: 90%;
      max-width: 460px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
      text-align: center;
      opacity: 0;
      transform: translateY(30px);
      animation: fadeSlideIn 1s ease forwards;
    }

    @keyframes fadeSlideIn {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .report-title {
      font-size: 24px;
      font-weight: 700;
      color: #0E1F3B;
      margin-bottom: 35px;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 10px;
    }

    .btn {
      font-family: 'Tajawal', sans-serif !important;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      border: none;
      border-radius: 12px;
      padding: 15px;
      width: 100%;
      font-size: 16px;
      font-weight: 600;
      color: white;
      margin-bottom: 15px;
      cursor: pointer;
      transition: 0.3s ease;
      box-shadow: 0 6px 12px rgba(0,0,0,0.1);
    }

    .btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }

    .btn-full {
      background-color: #0E1F3B;
    }

    .btn-over-year {
      background-color: #F4A835;
    }

    .btn-under-year {
      background-color: #28a745;
    }

    .btn i {
      font-size: 18px;
    }
  </style>
</head>
<body>

  <div class="report-box">
    <div class="report-title">
      <i class="fas fa-chart-pie"></i>
      تقرير تحليل محفظة الراجحي
    </div>

<a href="<?php echo base_url();?>/users/marsoom_all" class="btn btn-full">
  <i class="fas fa-layer-group"></i>
  كامل المحفظة
</a>

<a href="<?php echo base_url();?>/users/marsoom_1" class="btn btn-full">
  <i class="fas fa-layer-group"></i>
        الراجحي أكبر من سنة
</a>


<a href="<?php echo base_url();?>/users/marsoom_0" class="btn btn-full">
  <i class="fas fa-layer-group"></i>
           الراجحي إلى سنة
</a>



    
  </div>

</body>
</html>
