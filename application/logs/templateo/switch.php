<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>تبديل القوائم بين موظفين</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <!-- Tajawal Font -->
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Tajawal', sans-serif;
      background-color: #f5f5f5;
      padding: 40px;
    }
    .card {
      max-width: 600px;
      margin: auto;
      border-radius: 16px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }
    .card-header {
      background-color: #0E1F3B;
      color: #fff;
      font-weight: bold;
      font-size: 20px;
      text-align: center;
      border-top-left-radius: 16px;
      border-top-right-radius: 16px;
    }
    .form-label {
      font-weight: bold;
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
  <div class="card-header">
    تبديل القوائم بين موظفين
  </div>
  <div class="card-body p-4">
    <form method="post" action="#">

      <!-- الموظف الأول -->
      <div class="mb-3">
        <label for="employee_from" class="form-label">الموظف الأول (المرسل)</label>
        <select class="form-select" id="employee_from" name="employee_from" required>
          <option selected disabled>اختر الموظف الأول</option>
          <option value="101">أحمد بن خالد</option>
          <option value="102">سارة العتيبي</option>
          <option value="103">فهد السبيعي</option>
          <!-- ديناميكي لاحقًا -->
        </select>
      </div>

      <!-- الموظف الثاني -->
      <div class="mb-3">
        <label for="employee_to" class="form-label">الموظف الثاني (المستلم)</label>
        <select class="form-select" id="employee_to" name="employee_to" required>
          <option selected disabled>اختر الموظف الثاني</option>
          <option value="201">عبدالله الدوسري</option>
          <option value="202">ريم العبدالله</option>
          <option value="203">ناصر الحربي</option>
          <!-- ديناميكي لاحقًا -->
        </select>
      </div>

      <!-- زر التنفيذ -->
      <div class="text-center mt-4">
        <button type="submit" class="btn btn-orange px-4 py-2">
          تنفيذ تبديل القوائم
        </button>
      </div>

    </form>
  </div>
</div>

</body>
</html>
