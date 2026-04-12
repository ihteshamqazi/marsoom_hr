<?php echo validation_errors();  echo form_open_multipart('users/erorr/'.$id); ?>
<style>
#cal-1, #cal-2 {
  margin-left: 12px;
  margin-top: 10px;
}
.icon-button {
  width: 30px;
}
input {
  width: 243px;
  margin-bottom: 8px;
}
.checkbox-label{
	float:right;
	margin-right:230px;
}
</style>
<script>
$(function (){
	
	$('#Save').click(function(){	
		/*var disc = $("textarea#editor1").val();
		alert(disc);
		if(disc.val()==''){
			disc.parent().parent().addClass('has-error');
			return false;
		}else{
			disc.parent().parent().removeClass('has-error');
			result +='1';
		}
		if(result=='1'){
			
			alert('ook');
			
		}*/
	});

// Calender	
	var cal1 = new Calendar(),
    cal2 = new Calendar(true, 0, false, true),
    date1 = document.getElementById('date-1'),
    date2 = document.getElementById('date-2'),
    cal1Mode = cal1.isHijriMode(),
    cal2Mode = cal2.isHijriMode();

document.getElementById('cal-1').appendChild(cal1.getElement());
document.getElementById('cal-2').appendChild(cal2.getElement());
cal1.show();
cal2.show();
setDateFields();

cal1.callback = function() {
  if (cal1Mode !== cal1.isHijriMode()) {
    cal2.disableCallback(true);
    cal2.changeDateMode();
    cal2.disableCallback(false);
    cal1Mode = cal1.isHijriMode();
    cal2Mode = cal2.isHijriMode();
  }
  else
    cal2.setTime(cal1.getTime());
  setDateFields();
};

cal2.callback = function() {
  if (cal2Mode !== cal2.isHijriMode()) {
    cal1.disableCallback(true);
    cal1.changeDateMode();
    cal1.disableCallback(false);
    cal1Mode = cal1.isHijriMode();
    cal2Mode = cal2.isHijriMode();
  }
  else
    cal1.setTime(cal2.getTime());
  setDateFields();
};

function setDateFields() {
  date1.value = cal1.getDate().getDateString();
  date2.value = cal2.getDate().getDateString();
}

function showCal1() {
  if (cal1.isHidden()) cal1.show();
  else cal1.hide();
}

function showCal2() {
  if (cal2.isHidden()) cal2.show();
  else cal2.hide();
}

// checkbox 
$('.list').on('change', function() {
		$('.list').not(this).prop('checked', false);  
});
$('.list1').on('change', function() {
		$('.list1').not(this).prop('checked', false);  
});




//Add more	
		var i=1;
		$('#add').click(function(){
			i++;
			$('#dynamic_field').append('<tr id="row'+i+'"><td>'+
				'<input type="text" name="name[]" placeholder="Enter your Name" class="form-control name_list" />'+
				'<input type="text" name="phone[]" placeholder="Enter your phone" class="form-control name_list" />'+
				'<input type="text" name="mail[]" placeholder="Enter your mail" class="form-control name_list" />'+
				'<input type="text" name="job[]" placeholder="Enter your job" class="form-control name_list" />'+
				'<input type="text" name="address[]" placeholder="Enter your address" class="form-control name_list" />'+
				'<button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove">X</button></td></tr>');
		});
//Remove		
		$(document).on('click', '.btn_remove', function(){
			var button_id = $(this).attr("id"); 
			$('#row'+button_id+'').remove();
		});
		
		/*$('#submit').click(function(){		
			$.ajax({
				url:"name.php",
				method:"POST",
				data:$('#add_name').serialize(),
				success:function(data)
				{
					alert(data);
					$('#add_name')[0].reset();
				}
			});
		});*/
		

	
});
		
	
	
/**************************    Choose Radieo          ****************************/
function refree() {
    var x = document.getElementById("myDIV");
    if (x.style.display === "none") {
        x.style.display = "block";
    } else {
        x.style.display = "none";
    }
    var y = document.getElementById("ifno");
    if (y.style.display != "none") {
        y.style.display = "none";
    }

}
function ifchooseno() {
    var x = document.getElementById("ifno");
    if (x.style.display === "none") {
        x.style.display = "block";
    } else {
        x.style.display = "none";
    }
    var y = document.getElementById("myDIV");
    if (y.style.display != "none") {
        y.style.display = "none";
    }
}
function hide() {
    var x = document.getElementById("ifno");
    if (x.style.display != "none") {
        x.style.display = "none";
    }
    var y = document.getElementById("myDIV");
    if (y.style.display != "none") {
        y.style.display = "none";
    }
}

/********************************************/

	
</script>	
		
	
		   
</br>
		 
			<div class="row">
				<div class="col-lg-12 animatedParent animateOnce z-index-49">
					<div class="panel panel-default animated fadeInUp">
						<div class="panel-heading clearfix">
							<h3 class="panel-title">تنويه</h3>
							<ul class="panel-tool-options"> 
								<li><a data-rel="collapse" href="#"><i class="icon-down-open"></i></a></li>
							 
							</ul>
						</div>
						  
						<div class="panel-body">
							<form  id="rootwizard-2" class="form-wizard validate-form-wizard validate">		 
								<div class="tab-content">
									<div class="tab-pane  active" id="tab2-1">
										 
 
 

									  
<div class="row">
	<div class="col-md-4">
	<label class="col-sm-6 control-label">   لم يتم رفع المرفق </label>
	
		<div class="form-group"> 
			<label>        الرجاء التأكد من شروط رفع المرفق  :</br>
			1- ان لا يتجاوز حجم المرفق اكبر من 5 ميجا بايت</br>
		2- ان يكون اسم المرفق بدون فراغات
	    </label>
		</div>

		 <div class="form-group" style="display:none;"> 
            
            <textarea id="editor1"  name="disc" placeholder=" <?php  echo $this->lang->line("dec"); ?>  "  class="form-control">hgigi</textarea> 
        </div>


	 


	</br>


	  <a  href="javascript:window.history.go(-1);" id="printInvoice" class="btn btn-info">     رجوع</a>
	</div> 
</div>
  
 
								</div>
                                  
							</form>
						</div>
					    
          <?php echo form_close(); ?>
<script>
// multiselect AutoComplete
    $("#multiselect").kendoMultiSelect();
	var multiselect = $("#multiselect").data("kendoMultiSelect");
    $("#autocomplete").kendoAutoComplete({
         dataSource: [ "Apples", "Oranges","Apples1", "Oranges1","Apples2", "Oranges2","Apples3", "Oranges3","Apples4", "Oranges4" ],
          select: function(e) {
            var item = e.item;
            var text = item.text();
            
            var data = multiselect.dataSource;
            data.add({text: text})
            //alert(data);
          }
    });
	
	
/*
// Upload Multi Files
$("#d").click(function() {

	alert('ok');
	$('#files').change(function(){
		
		var files = $('#files')[0].files;
		var error = '';
		var form_data = new FormData();
		for(var count = 0; count < files.length; count++){
			var name = files[count].name;
			var extt = name.split('.').pop()toLowerCase();
			if(jquery.inArray(extt,['doc','docx','pdf','png','jpg','jpeg']) == -1){
				error += "Invalid" + count + " Image"
				//alert('error');
			}else{
				form_data.append("files[]",files[count]);
			}
		}
		if(error == ''){
			$.ajax({
				url:"<?php echo base_url();?>users/deal_add",
				method:POST,
				data:form_data,
				contentType:false,
				cache:false,
				processData:false,
				beforsend:functio()
				{
					$('#upload').html("<label class'text-sucess'>uploading...</label>")
				},
				sucess:function(data)
				{
					$('#upload').html(data);
					$('#files').val('');
				}
			});
		}else{
			alert(error);
		}
	});
});*/
</script>