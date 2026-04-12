<!doctype html>
<html lang="en">

<head>
<title> برنامج   خدمة العملاء    </title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<meta name="description" content="Lucid Bootstrap 4.1.1 Admin Template">
<meta name="author" content="WrapTheme, design by: ThemeMakker.com">

<link rel="icon" href="<?php echo base_url();?>favicon.ico" type="image/x-icon">
<!-- VENDOR CSS -->
<link rel="stylesheet" href="<?php echo base_url();?>/assets/vendor/bootstrap/css/bootstrap.min.rtl.css">
<link rel="stylesheet" href="<?php echo base_url();?>/assets/vendor/font-awesome/css/font-awesome.min.css">

<!-- MAIN CSS -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/main.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/rtl.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/color_skins.css">
 <link href="<?php echo base_url();?>css/style.css" rel="stylesheet" type="text/css" media="all" />
 <link href="https://fonts.googleapis.com/css?family=Tajawal&display=swap" rel="stylesheet">
 <meta http-equiv="refresh" content="60; URL=https://services.marsoom.net/collection/users/login">
</head>

 <style>
    body {
      font-family: Arial, sans-serif;
      text-align: center;
      margin-top: 100px;
    }
    #timer {
      font-size: 2rem;
      color: #333;
    }
  </style>



 


<body class="theme-cyan rtl">
    <?php echo form_open('users/check_otp');?>
    <!-- WRAPPER -->
    <div id="wrapper">
        <div class="vertical-align-wrap">
            <div class="vertical-align-middle auth-main">
                <div class="auth-box">
                    <div class="top">
                        
                    </div>
                    <div class="card">
                        <div class="header">
                            <p class="lead" style="font-family: 'Tajawal', sans-serif; font-weight: bold;
    font-style: normal; font-size:20px;">             الرجاء ادخال الرمز المرسل على  البريد الالكتروني   الخاص بكم    
 
            </p>
                        </div>
                        <div id="timer">00:00:00</div>

                         <script>
    // Countdown duration in seconds
    let countdownTime = 120; // 60 seconds

    // Function to format time as HH:MM:SS
    function formatTime(seconds) {
      const mins = String(Math.floor(seconds / 60)).padStart(2, '0');
      const secs = String(seconds % 60).padStart(2, '0');
      return `00:${mins}:${secs}`;
    }

    // Display the timer and update every second
    const timerElement = document.getElementById('timer');
    const interval = setInterval(() => {
      if (countdownTime <= 0) {
        clearInterval(interval);
        timerElement.textContent = "Time's up!";
      } else {
        timerElement.textContent = formatTime(countdownTime);
        countdownTime--;
      }
    }, 1000);
  </script>
  

                        <div class="body">
                            <form class="form-auth-small" action="index.html">
                                <div class="form-group">
                                    <label style="font-family: 'Tajawal', sans-serif; font-weight: bold;
    font-style: normal; font-size:20px;" for="signin-email" class="control-label sr-only">رمز التحقق</label>
                                    <input  style="font-family: 'Tajawal', sans-serif; font-weight: bold;
    font-style: normal; font-size:20px;" type="text" name="otp" class="form-control" id="signin-email" value="" placeholder=" رمز التحقق  ">
                                </div>
                               
                               

                                <button type="submit" name="submitForm" value="formSave" style="font-family: 'Tajawal', sans-serif; font-weight: bold;
    font-style: normal; font-size:20px; " class="btn btn-primary btn-lg btn-block"  >الدخول</button>
                               
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END WRAPPER -->
 <?php echo form_close(); ?>
</body>
</html>

