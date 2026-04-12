 <script type="text/javascript"> 
      $(document).ready( function() {
        //$('.alert').delay(2000).fadeOut();     
        $('#count').addClass('animated lightSpeedIn'); 
        $('.tap-title').addClass('animated slideInRight');
        $('#btnAdd').addClass('animated slideInLeft');
        $('.table').addClass('animated zoomInUp');   
        $('.table >thead>tr').addClass('animated slideInLeft');
            
      });
</script>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="/resources/demos/style.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script>
   

  $(function() {
      $('#datepicker').datepicker({dateFormat: 'yy/mm/dd'});
});

   $(function() {
      $('#datepicker1').datepicker({dateFormat: 'yy/mm/dd'});
});




  </script>

  <?php echo form_open_multipart('users/reportaccount'); ?>
       <div class="header-secondary row gray-bg">
          <div class="col-lg-12">
          <div class="page-heading clearfix">
           <h1 class="page-title pull-left">حركة  الأرشيف</h1> 
          </div>
          </div>
         </div>
</br>

      <div class="row">
         <div class="col-md-6">
            <label class="col-sm-6 control-label">من تاريخ  </label>     
             <div id="date-popup" class="input-group date"> 
                      <input type="text" id="datepicker1" name="search" data-format="D, dd MM yyyy" class="form-control"> 
                      <span class="input-group-addon"><i class="fa fa-calendar"></i></span> 
           </div>
         </div>
         <div class="col-md-6">
            <label class="col-sm-6 control-label">الى تاريخ</label>                         
           <div id="date-popup" class="input-group date"> 
                      <input type="text" id="datepicker" name="search1" data-format="D, dd MM yyyy" class="form-control"> 
                      <span class="input-group-addon"><i class="fa fa-calendar"></i></span> 
           </div>
         </div>                          
      </div>
                          </br>

      <div class="row">
          <div class="col-md-6">
              <button type="submit" id="submit" class="btn btn-primary">بحث </button>
          </div>
          <div class="col-md-6">                            
          </div>                                  
      </div>

      
     </br>


      <ol class="breadcrumb breadcrumb-2"> 
        <li><a href="<?php echo site_url('users/dashbord/'); ?>"><i class="fa fa-home"></i>لوحة التحكم</a></li> 
        <li class="active"><strong>الطلبات</strong></li> 
      </ol> 
      <div class="row">
        <div class="col-lg-12 animatedParent animateOnce z-index-49">
          <div class="panel panel-default animated fadeInUp">
            <div class="panel-heading clearfix">
              <h3 class="panel-title">الطلبات</h3>
              <ul class="panel-tool-options"> 
                <li><a data-rel="collapse" href="#"><i class="icon-down-open"></i></a></li>
               
              </ul>
            </div>
            <div class="panel-body">
              <form  id="rootwizard-2" class="form-wizard validate-form-wizard validate">    
                <div class="tab-content">
                  <div class="tab-pane  active" id="tab2-1">
                  
           <div class="row">
                <div class="col-md-12">
                    <div class="card"> 
                        <div>
                           
                        </div>
                    </div>
                </div>
            </div>
                    <div class="row"> 
                                          <table id="bootstrap-data-table" class="table table-striped table-bordered">
                    <thead>
                      <tr>
                         <th scope="col"> الرقم</th>  
                          <th scope="col">العميل</th>
                             <th scope="col"> الوصف  </th>
                               <th scope="col"> نوع العملية </th>
                              
                     
                        
                      <th scope="col">تاريخ  التحصيل</th>
                             
                      </tr>
                    </thead>
                    <tbody>
                      <?php $z=0; $z1=0; ?>
 
                    <?php foreach($customers as $customerss) : ?>
    <tr>
          <td><?php echo $customerss['id']; ?></td>
          <td><?php echo $customerss['userid']; ?></td>
           <td><?php echo $customerss['name']; ?></td>
            <td><?php echo $customerss['type']; ?></td>
 
          
          
          
           <td><?php echo $customerss['created_at']; ?></td>

         </td>
            

       <!--  <?php 
                         
                 if( $customerss['reder'] == 1) : ?>
                    <td><p><a class="btn btn-success" href="<?php echo site_url('users/receiptalrm1/'.$customerss['id']); ?>">
               تمت الموافقة على الطلب
           </a><p></td>
                    
               <?php 
                         
                 elseif ($customerss['reder'] == 0) :  ?>
                   <td><p><a class="btn btn-warning" href="<?php echo site_url('users/mview2/'.$customerss['id']); ?>">
         طلب جديد
           </a><p></td>
 <?php 
             elseif ($customerss['reder'] == 2) :  ?>
                   <td><p><a class="btn btn-blue" href="<?php echo site_url('users/mview2/'.$customerss['id']); ?>">
        تحت الدراسة
           </a><p></td>

            <?php 
             elseif ($customerss['reder'] == 3) :  ?>
                   <td><p><a class="btn btn-red" href="<?php //echo site_url('users/mview/'.$customerss['id']); ?>">
         تم رفض الطلب
           </a><p></td>  
                <?php endif; ?> -->  
    </tr>
    <?php endforeach; ?>
                    </tbody>
                  </table>

                    

                     

                     




                      </div> 
                       
                    </div>
                      <div class="row">
                        <div class="col-6">       
                                           </div>
                                          
 
                     </div>
                </div>
                                  
              </form>
            </div>
      <div class="content mt-3">
 
      </div><!-- .animated -->
        </div><!-- .content -->