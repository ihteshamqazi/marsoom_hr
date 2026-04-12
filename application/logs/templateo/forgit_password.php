<!doctype html>
<html lang="en">

<head>
<title>:: Lucid RTL :: Login</title>
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
</head>

 

<body class="theme-cyan rtl">
    <?php echo form_open('users/login');?>
	<!-- WRAPPER -->
	<div id="wrapper">
		<div class="vertical-align-wrap">
			<div class="vertical-align-middle auth-main">
				<div class="auth-box">
                    <div class="top">
                        <img src="<?php echo base_url();?>/assets/images/logo.PNG" alt="Lucid">
                    </div>
					<div class="card">
                        <div class="header">
                            <p class="lead" style="font-family: 'Tajawal',  sans-serif; font-weight: bold;
    font-style: normal; font-size:30px;">        في حال فقدان كلمة المرور   </p>

                        </div>
                        <div class="body">
                            <form class="form-auth-small" action="index.html">
                                 <h2 style="font-family: 'Tajawal',  sans-serif; font-weight: bold;
    font-style: normal; font-size:30px;">الرجاء التواصل مع الدعم الفني  تحويلة  رقم 224 </h2>
     <span class="helper-text m-b-10"><i class="fa fa-user-md"></i> <a style="font-family: 'Tajawal', sans-serif; font-weight: bold;
    font-style: normal; font-size:12px;"> صالح السفياني</a></span>
                                 
                                 

                                <button type="submit" name="submitForm" value="formSave" style="  font-family: 'Tajawal', sans-serif; font-weight: bold;
    font-style: normal; font-size:20px; " class="btn btn-primary btn-lg btn-block"  >رجوع</button>
                                
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

