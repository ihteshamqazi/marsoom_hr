<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>سحب قائمة موظف مستقيل</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <!-- Tajawal Font -->
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Tajawal', sans-serif;
      background: #f9f9f9;
      padding: 40px;
    }
    .card {
      max-width: 600px;
      margin: auto;
      border-radius: 16px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }
    .card-header {
      background-color: #F29840;
      color: #fff;
      font-weight: bold;
      font-size: 20px;
      border-top-left-radius: 16px;
      border-top-right-radius: 16px;
      text-align: center;
    }
    .card-body label {
      font-weight: bold;
      color: #333;
    }
    .note {
      background-color: #fff3cd;
      border: 1px solid #ffeeba;
      padding: 15px;
      border-radius: 8px;
      color: #856404;
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
  <div class="card-header">
    سحب قائمة موظف مستقيل
  </div>
  <div class="card-body">
    
    <div class="note">
      سيتم سحب قائمة الموظف المستقيل، وتوزيعها تلقائيًا على باقي الموظفين التابعين لنفس المشرف.
    </div>

    <form method="post" action="#">
      <div class="mb-3">
        <label for="resigned_employee" class="form-label">اسم الموظف المستقيل</label>
        <input type="text" readonly class="form-control" id="resigned_employee" value="مثال: أحمد بن محمد" name="resigned_employee">
      </div>

      <div class="text-center mt-4">
        <button type="submit" class="btn btn-orange px-4 py-2">تأكيد السحب والتوزيع</button>
      </div>
    </form>

  </div>
</div>

</body>
</html>
