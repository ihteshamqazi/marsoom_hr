<html>
	<head>
		<title>ciBlog</title>
        <link  href="https://bootswatch.com/4/flatly/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo base_url(); ?>/assets/css/style.css" >
        <script src="http://cdn.ckeditor.com/4.5.11/standard/ckeditor.js"></script>
	</head>
	<body>
<?php echo form_open('users/login'); ?>
 
	<div class="breadcrumbs">
 <div class="content">
 <div class="row">
     <div class="col-sm-4">
         <div class="page-header float-left">
             <div class="page-title">
               
             </div>
         </div>	
     </div>
 </div>
 <div class="row">
     <div class="container-fluid">
     <div class="form-content">
           <div class="row">
             <div class="col-4">
                 
             </div>
             <div class="col-4">
                 
             </div>
               <div class="col-4">
                  <h4>شاشة تسجيل الدخول</h4>  
             </div>
         </div>
      
         <div class="row">
 
             <div class="col-4">
                 
             </div>
             <div class="col-4">
                 <label>إسم المستخدم</label>
                 <input type="text" name="username" value=""  class="form-control" placeholder="username" required >
             </div>
               <div class="col-4">
                 
             </div>
         </div>
         <div class="row">
             <div class="col-4">
                 
             </div>
             <div class="col-4">
                 <label>كلمة السر</label>
                 <input type="password" name="password" value="" class="form-control" placeholder="password" required >
             </div>
              <div class="col-4">
                 
             </div>
         </div>
         <br/>
            <div class="row">
             <div class="col-4">
                 
             </div>
             <div class="col-4">
                  <div class="col-4">
                  </div>
                   <div class="col-4">

                    

                     

                  </div>
                   <div class="col-4">
                       <button type="submit" name="submitForm" class="btn btn-success" value="formSave">دخول</button>
                    <?php echo form_close(); ?> 
                  </div>
                  
             </div>
             <div class="col-4">
                 
             </div>
         </div>

     
 </div>
 </div>
 </div>
 </div>
 </div>

</div>
            <script>
                CKEDITOR.replace( 'editor1' );
            </script>
		
	</body>
</html>