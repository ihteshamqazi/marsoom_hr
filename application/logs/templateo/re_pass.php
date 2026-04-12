<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>تغيير كلمة المرور</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@500;700&display=swap" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #0E1F3B, #1B263B);
      font-family: 'Tajawal', sans-serif;
      color: #fff;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .password-box {
      background-color: #ffffff;
      color: #000000;
      border-radius: 20px;
      padding: 40px 30px;
      max-width: 500px;
      width: 100%;
      box-shadow: 0 12px 30px rgba(0,0,0,0.3);
      transition: all 0.3s ease-in-out;
    }

    .password-box h2 {
      font-weight: 700;
      font-size: 26px;
      color: #0E1F3B;
      text-align: center;
      margin-bottom: 30px;
    }

    .form-label {
      font-size: 18px;
      font-weight: bold;
      color: #0E1F3B;
    }

    .form-control {
      font-size: 18px;
      padding: 12px;
      border-radius: 12px;
      border: 2px solid #ced4da;
    }

    #message {
      background-color: #f8f9fa;
      border-radius: 12px;
      padding: 15px;
      margin-top: 20px;
      font-size: 16px;
    }

    #message p {
      margin: 0 0 10px;
      font-weight: bold;
    }

    .valid {
      color: green;
    }

    .invalid {
      color: red;
    }

    .btn-primary {
      background-color: #F29840;
      border-color: #F29840;
      font-size: 18px;
      font-weight: bold;
      padding: 12px;
      border-radius: 12px;
    }

    .btn-primary:hover {
      background-color: #e08935;
      border-color: #e08935;
    }

    .alert-custom {
      display: none;
      margin-top: 15px;
      padding: 12px;
      background-color: #f8d7da;
      color: #721c24;
      border-radius: 10px;
      font-weight: bold;
    }
  </style>
</head>
<body>

  <div class="password-box">
    <h2>تغيير كلمة المرور</h2>

    <div id="alert-duplicate" class="alert-custom">
      لا يمكن استخدام كلمة المرور الحالية. الرجاء إدخال كلمة مرور مختلفة.
    </div>

     <?php echo validation_errors(); ?>
             <?php echo form_open_multipart('users/re_pass'); ?>
      <div class="mb-3">
        <label for="psw" class="form-label">كلمة المرور الجديدة</label>
        <input type="password" class="form-control" id="psw" name="password"
               required
               pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*]).{8,}"
               title="يجب أن تحتوي على رقم، حرف صغير، حرف كبير، رمز خاص، و8 خانات على الأقل">
      </div>

      <div id="message">
        <p id="letter" class="invalid">حرف صغير بالإنجليزية</p>
        <p id="capital" class="invalid">حرف كبير بالإنجليزية</p>
        <p id="number" class="invalid">رقم</p>
        <p id="symbol" class="invalid">رمز خاص (مثل @ أو #)</p>
        <p id="length" class="invalid">8 خانات أو أكثر</p>
      </div>

      <button type="submit" class="btn btn-primary w-100 mt-3">حفظ</button>
     <?php echo form_close(); ?>
  </div>

  <script>
    const myInput = document.getElementById("psw");
    const letter = document.getElementById("letter");
    const capital = document.getElementById("capital");
    const number = document.getElementById("number");
    const symbol = document.getElementById("symbol");
    const length = document.getElementById("length");
    const alertDuplicate = document.getElementById("alert-duplicate");

    myInput.onfocus = function() {
      document.getElementById("message").style.display = "block";
      alertDuplicate.style.display = "none";
    }

    myInput.onblur = function() {
      document.getElementById("message").style.display = "none";
    }

    myInput.onkeyup = function() {
      const value = myInput.value;

      letter.className = /[a-z]/.test(value) ? "valid" : "invalid";
      capital.className = /[A-Z]/.test(value) ? "valid" : "invalid";
      number.className = /[0-9]/.test(value) ? "valid" : "invalid";
      symbol.className = /[!@#$%^&*]/.test(value) ? "valid" : "invalid";
      length.className = value.length >= 8 ? "valid" : "invalid";

      // إخفاء التنبيه عند الكتابة من جديد
      if (value !== "admin@123") {
        alertDuplicate.style.display = "none";
      }
    }

    function validatePassword() {
      const value = myInput.value;

      if (value === "admin@123") {
        alertDuplicate.style.display = "block";
        myInput.focus();
        return false;
      }

      const conditions = [letter, capital, number, symbol, length];
      const allValid = conditions.every(el => el.className === "valid");

      if (!allValid) {
        alert("يرجى إدخال كلمة مرور تستوفي جميع الشروط.");
        return false;
      }

      return true;
    }
  </script>

</body>
</html>
