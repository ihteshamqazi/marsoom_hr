<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>تقييماتي - مرسوم</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>

  <style>
    :root {
      --marsom-blue: #001f3f;
      --marsom-orange: #FF8C00;
      --marsom-light-blue: #2c5aa0;
      --glass-bg: rgba(255, 255, 255, 0.08);
      --glass-border: rgba(255, 255, 255, 0.2);
      --glass-shadow: rgba(0, 0, 0, 0.5);
      --card-bg: rgba(8, 34, 64, 0.92);
    }
    /* body {
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
      color: #ffffff;
    } */
    @keyframes gradientAnimation {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
    /* ... (All your other CSS styles: particles, main-screen-container, logo, etc.) ... */
    /* ... (Copy all styles from your provided view here) ... */
    
    /* Ensure these styles from your original view are present */
    .particles { position: fixed; inset: 0; overflow: hidden; z-index: 0; pointer-events: none; }
    .particle { position: absolute; background: rgba(255, 140, 0, 0.1); clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%); animation: float 25s infinite ease-in-out; opacity: 0; filter: blur(2px); }
    .particle:nth-child(even) { background: rgba(0, 31, 63, 0.14); }
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
    @keyframes float { 0% { transform: translateY(0) translateX(0) rotate(0deg); opacity:0; } 15% { opacity:1; } 85% { opacity:1; } 100% { transform: translateY(-100vh) translateX(40px) rotate(360deg); opacity:0; } }
    .main-screen-container { position: relative; z-index: 1; background: var(--glass-bg); backdrop-filter: blur(18px); -webkit-backdrop-filter: blur(18px); border-radius: 22px; border: 1px solid var(--glass-border); box-shadow: 0 14px 60px var(--glass-shadow); padding: 30px 25px 25px; max-width: 1300px; width: 100%; margin: auto; animation: fadeInScale 1.1s ease-out forwards; }
    @keyframes fadeInScale { from { opacity: 0; transform: translateY(30px) scale(0.96); } to { opacity: 1; transform: translateY(0) scale(1); } }
    .logo-container { text-align: center; margin-bottom: 5px; }
    .logo-container img { max-width: 150px; height: auto; filter: drop-shadow(0 0 8px rgba(0,0,0,0.6)); }
    .page-header-title { font-family: 'El Messiri', sans-serif; font-size: 2rem; font-weight: 700; text-align: center; margin: 5px 0 8px; color: #ffffff; text-shadow: 0 3px 8px rgba(0,0,0,0.45); }
    .page-subtitle { text-align: center; font-size: 0.95rem; color: rgba(255,255,255,0.78); margin-bottom: 20px; }
    .users-card { background: var(--card-bg); border-radius: 18px; padding: 18px 16px 14px; border: 1px solid rgba(255,255,255,0.16); box-shadow: 0 10px 30px rgba(0,0,0,0.55); margin-top: 5px; }
    .users-card-header { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.12); }
    .users-title-icon { width: 42px; height: 42px; border-radius: 14px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, var(--marsom-orange), var(--marsom-light-blue)); box-shadow: 0 4px 10px rgba(0,0,0,0.35); color: #fff; font-size: 1.2rem; }
    .users-title-text { font-family: 'El Messiri', sans-serif; font-size: 1.25rem; font-weight: 700; color: #ffffff; }
    .users-subtitle { font-size: 0.8rem; color: rgba(255,255,255,0.75); }
    .table { margin-bottom: 0; font-size: 0.9rem; background-color: transparent; }
    .table thead tr { background-color: #ffffff; }
    .table thead th { color: #000000; font-weight: 700; font-size: 0.9rem; border-bottom: none; white-space: nowrap; text-align: center; }
    .table tbody tr { background-color: #ffffff; border-color: #e0e4ea; transition: all 0.2s ease; }
    .table tbody tr:hover { background-color: #ffe8cc; transform: translateY(-1px); box-shadow: 0 4px 10px rgba(0,0,0,0.15); }
    .table tbody td { vertical-align: middle; color: #000000; font-weight: 500; text-align: center; }
    .btn-eval { padding: 4px 14px; border-radius: 18px; font-size: 0.8rem; border: none; background: linear-gradient(135deg, var(--marsom-orange), var(--marsom-light-blue)); color: #fff; font-weight: 600; box-shadow: 0 3px 8px rgba(0,0,0,0.35); transition: all 0.2s ease; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; }
    .btn-eval:hover { transform: translateY(-1px); box-shadow: 0 5px 12px rgba(0,0,0,0.45); color: #fff; }
    /* End of copied styles */
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

<div class="main-screen-container">
  <div class="logo-container">
    <img src="https://via.placeholder.com/150x50/001f3f/ffffff?text=Marsom+Logo" alt="Marsom Logo">
  </div>

  <h1 class="page-header-title">سجل تقييماتي</h1>
  <div class="page-subtitle">
      عرض سجل التقييمات السنوية الخاصة بك
  </div>

  <?php if (!empty($evaluations)): ?>
    <div class="users-card">
      <div class="users-card-header">
        <div class="users-title-icon">
          <i class="fa fa-star-half-alt"></i>
        </div>
        <div>
          <div class="users-title-text">التقييمات المكتملة</div>
          <div class="users-subtitle">
            يعرض هذا الجدول تقييماتك السابقة مع النتيجة
          </div>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover" id="usersTable">
         <thead>
            <tr>
              <th>تاريخ التقييم</th>
              <th>المُقيّم (المسؤول المباشر)</th>
              <th>النتيجة الإجمالية</th>
              <th>عرض / طباعة</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($evaluations as $eval): ?>
            <tr>
              <td>
                <?php echo htmlspecialchars($eval['w18']); ?>
              </td>
              <td>
                <?php echo htmlspecialchars($eval['w3']); ?>
              </td>
              <td>
                <span class="badge" style="font-size: 0.9rem; background-color: var(--marsom-blue); color: white;">
                  % <?php echo number_format((float)$eval['w15'], 2); ?>
                </span>
              </td>
       <!--       
              <td>
                <?php if ($eval['view_status'] == 1): ?>
                  <span class="badge" style="background-color: #28a745; color: white; font-size: 0.8rem;">
                    <i class="fa fa-check"></i> تم الاطلاع
                  </span>
                <?php else: ?>
                  <span class="badge" style="background-color: #6c757d; color: white; font-size: 0.8rem;">
                    لم يتم الاطلاع
                  </span>
                <?php endif; ?>
              </td> -->
              <td>
                <a href="<?php echo site_url('users/inv22/'.$eval['w1']); ?>" class="btn-eval" target="_blank">
                  <i class="fa fa-eye"></i>
                  عرض
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php else: ?>
    <div class="alert alert-info text-center" style="background-color: var(--card-bg); border-color: var(--marsom-light-blue); color: white;">
      <h4 class="alert-heading">لا يوجد تقييمات</h4>
      <p>لم يتم العثور على أي تقييمات مكتملة في سجلك حتى الآن.</p>
    </div>
  <?php endif; ?>

</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  $(document).ready(function () {
    // You can initialize DataTables here if needed
    // if ($.fn.DataTable && $('#usersTable').length) {
    //   $('#usersTable').DataTable({ ... });
    // }
  });
</script>

</body>
</html>