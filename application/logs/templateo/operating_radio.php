

        <?php echo validation_errors(); ?>
             <?php echo form_open_multipart('users/operating_radio'); ?>
    <div id="main-content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-5 col-md-8 col-sm-12">                        
                        <h2 style="font-family: 'Tajawal', sans-serif; font-weight: bold;
    font-style: normal; font-size:30px;"><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a>  تقرير نسب الأشغال  </h2>
                       <ul class="breadcrumb">
                           <li class="breadcrumb-item"><a href="<?php echo base_url();?>users/dashbord_analyses"><i class="icon-home"></i></a></li>                            
                            <li class="breadcrumb-item"><a href="<?php echo base_url();?>users/users_index"><i class="icon-users"></i></a></li>

                             <li class="breadcrumb-item"><a href="<?php echo base_url();?>users/user_report"><i class="icon-notebook"></i></a></li>

                            <li class="breadcrumb-item active"><a href="<?php echo base_url();?>users/register"><i class="fa fa-plus"></i></a></li>
                            
                        </ul>
                    </div>            
                   
                </div>
            </div>

            <div class="row clearfix">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">
                            <h2 style="font-family: 'Tajawal', sans-serif; font-weight: bold;
    font-style: normal; font-size:20px;">الرجاء       تعبئة بيانات الطلب</h2>
                        </div>
                        <div class="body">
                            <form id="basic-form" method="post" novalidate>
                             
                                  
                                  
                                 
                                  
                                  

                                     
                                    

                                 <!--  <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <label style="font-family: 'Tajawal', sans-serif; font-weight: bold;
    font-style: normal; font-size:12px;" class="input-group-text" for="inputGroupSelect01">الفرع</label>
                                </div>
                                 <select name="hotel_id" style="font-family: 'Tajawal', sans-serif; font-weight: bold;
                     font-style: normal; font-size:12px;" class="custom-select" id="inputGroupSelect01">
                      <option tyle="font-family: 'Tajawal', sans-serif; font-weight: bold;
                              font-style: normal; font-size:12px;" value="1">شهد الطائف</option>
                                <option tyle="font-family: 'Tajawal', sans-serif; font-weight: bold;
                              font-style: normal; font-size:12px;" value="2">بوابة الطائف</option>
              </select>
                            </div> -->

                              <div class="form-group">
                                    <label style="font-family: 'Tajawal', sans-serif; font-weight: bold;
    font-style: normal; font-size:15px;">عدد الغرف  المؤجرة</label>
                                    <input style="font-family: 'Tajawal', sans-serif; font-weight: bold;
    font-style: normal; font-size:12px;" type="text" name="room" class="form-control" required>
                                </div>

                                  <div class="form-group">
                                    <label style="font-family: 'Tajawal', sans-serif; font-weight: bold;
    font-style: normal; font-size:15px;">عدد حجوزات  بوكينج</label>
                                    <input style="font-family: 'Tajawal', sans-serif; font-weight: bold;
    font-style: normal; font-size:12px;" type="text" name="booking" class="form-control" required>
                                </div>



                            
 

 
                                
                                <button style="font-family: 'Tajawal', sans-serif; font-weight: bold;
    font-style: normal; font-size:12px;" type="submit" name="submitForm" value="formSave" class="btn btn-primary">حفظ</button>
                            </form>
                        </div>
                    </div>
                </div>
                
            </div>
            
        </div>
    </div>


      <?php echo form_close(); ?>
    

