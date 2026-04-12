 
 
                <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>Users Data</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        
                    </div>
                </div>
            </div>
        </div>
         <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title">Users Data</strong>
                        </div>
                        <div>
                            <p><a class="btn btn-lg btn-info" href="<?php echo site_url('users/register/'); ?>">Add New Users</a><p>
                                           
                                      </div>
                       
                   <table id="bootstrap-data-table" class="table table-striped table-bordered">
                    <thead>
                      <tr>
                             <th scope="col">user name</th>
                         <th scope="col">Validity</th>
       
                      <th scope="col"> </th>      
    
            <th scope="col"> </th>
        
      
             
           
             
                      </tr>
                    </thead>
                    <tbody>
 
                    <?php foreach($customers as $customerss) : ?>
    <tr>
  
          <td><?php echo $customerss['name']; ?></td>
      <td><?php 
       if($customerss['type'] == 1){
                echo "sales";
            }elseif($customerss['type'] == 2){
                  echo "sales suport";

        }elseif($customerss['type'] == 3){
             echo " Admin";

        }
        else{

    } ?>
     </td>
     
    <td><p><a class="btn btn-outline-primary btn-sm" href="<?php echo site_url('customers/index/'.$customerss['id']); ?>">Customers added by him</a><p></td>

       <td><p><a class="btn btn-outline-primary btn-sm" href="<?php echo site_url('orders/index/'.$customerss['id']); ?>">All Requests added by him</a><p></td>
         
     
      
 

      
    
       
    </tr>
    <?php endforeach; ?>
                    </tbody>
                  </table>
                        </div>
                    </div>
                </div>


                </div>
            </div><!-- .animated -->
        </div><!-- .content -->




 
