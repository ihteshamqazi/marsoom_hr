<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>إسناد قائمة عملاء لموظف جديد</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <!-- Tajawal Font -->
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Tajawal', sans-serif;
      background-color: #f8f9fa;
      padding: 40px;
    }
    .card {
      max-width: 650px;
      margin: auto;
      border-radius: 16px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .card-header {
      background-color: #0E1F3B;
      color: white;
      text-align: center;
      font-weight: bold;
      font-size: 22px;
      border-top-left-radius: 16px;
      border-top-right-radius: 16px;
    }
    .note {
      background-color: #fff3cd;
      color: #856404;
      border: 1px solid #ffeeba;
      padding: 15px;
      border-radius: 8px;
      font-size: 15px;
      margin-bottom: 20px;
    }
    .btn-orange {
      background-color: #F29840;
      border: none;
      font-weight: bold;
    }
    .btn-orange:hover {
      background-color: #e68a2f;
    }
  </style>
</head>
<body>

<div class="card">
  <div class="card-header">إسناد قائمة عملاء لموظف جديد</div>
  <div class="card-body p-4">

    <!-- تنبيه -->
    <div class="note">
      سيتم سحب العملاء من الموظفين الآخرين التابعين لنفس المشرف <strong>بشكل متساوٍ</strong> وفقًا لسياسة التوزيع المعتمدة.
    </div>

    <form method="post" action="#">

      <!-- الموظف الجديد (قراءة فقط) -->
      <div class="mb-3">
        <label class="form-label">الموظف الجديد</label>
        <input type="text" readonly class="form-control" value="عبدالرحمن الحربي" name="new_employee">
      </div>

      <div class="text-center mt-4">
        <button type="submit" class="btn btn-orange px-5 py-2">تنفيذ الإسناد</button>
      </div>
    </form>
  </div>
</div>

</body>
</html>
