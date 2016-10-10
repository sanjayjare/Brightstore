<?php 
@ob_start();
session_start();
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Order Status</title>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css">
<link href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css" rel="stylesheet" />
<link type="text/css"  href="css/style.css" type="text/css" rel="stylesheet" />
</head>

<body>
<div class="mainx">
<form name="orderdetails" id="orderdetailsfrm" method="post" action="">
<?php

/**** Suppliers list array ****/
include('config.php');

foreach($db->query("select row_id,userName, password, statusUrl, companyID, companyName, statusVersion, shippingUrl, shippingVersion from Companies where userName != '' and  password != '' and statusUrl != '' and statusVersion != '' order by companyName") as $rows) {
	$row_id = $rows['row_id'];
	$userName = $rows['userName'];
	$password = $rows['password'];
	$statusUrl = $rows['statusUrl'];
	$companyID = $rows['companyID'];
	$companyName = $rows['companyName'];
	$statusVersion = $rows['statusVersion'];
	$shippingUrl = $rows['shippingUrl'];
	$shippingVersion = $rows['shippingVersion'];

	$suppliers[$row_id] = array('row_id'=>$row_id, 'companyID'=>$companyID, 'companyName'=>$companyName,
                        'statusUrl'  =>$statusUrl, 'statusVersion'=>$statusVersion,
                        'userName'=>$userName, 'password'=>$password,'shippingUrl'=>$shippingUrl, 'shippingVersion'=>$shippingVersion );

}


//print_r($suppliers);
?>

            <table class="tablebox">
                <tbody><tr>
                    <td>Select A Supplier : 
                    </td>
                    <td>
                        <select id="selSupplier" name="selSupplier">
	<option value="">Select A Supplier</option>
	<?php 
	$i=0;
	foreach($suppliers as $sp){
		echo "<option value='".$sp['row_id']."'>".$sp['companyName']."</option>";
		$i++;
	}
	$_SESSION['suppliers']=$suppliers;
	?>
	

</select>
                    </td>
                </tr>
				<tr>
                    <td>Type : 
                    </td>
                    <td>
							<select name="dtype" class="dtype" id="dtype">
                            	<option value="normal">Normal</option>
								<option value="table ">Table</option>
								<option value="json">JSON</option>
							</select>
							
                    </td>
                </tr>
				<tr>
                    <td>Query Type : 
                    </td>
                    <td>
							<div class="qydiv">
                            <div class="qydiv1"><input type="radio" selected class="qtype" name="querytype" value="1" id="q1"> PO Search</div>
							<div class="qydiv3"><input type="radio" class="qtype" name="querytype" value="3" id="q3"> Last Update Search</div>
							<div class="qydiv4"><input type="radio" class="qtype" name="querytype" value="4" id="q4"> All Open Search </div>
                            </div>
							
                    </td>
                </tr>
                <tr>
                    <td> 
                    </td>
                    <td>
                        
                    </td>
                </tr>
				
				<tr>
				<td>
				<div class="ponodiv" style="display:none">
					Enter Reference Number (PO #) : 
					</div>
				<div class="datef" style="display:none">
					Status Date: 
					</div></td>
				<td>
					<div class="ponodiv" style="display:none">
					<input type="text" id="referencenumber" value="" name="referencenumber">
					</div>
					
					<div class="datef" style="display:none">
					<input type="text" readonly class="statusTimeStamp" name="statusTimeStamp" id="datepicker">
					</div>
					
				</td>
				</tr>
                <tr>
                    <td colspan="2" class="submitbtntd">
                        <input type="button" id="getorderstatus" value="Get Order Status" name="getorderstatus">
                    </td>
                </tr>
            </tbody></table>
        


</form>
<div id="loader"></div>
<div id="datacontent"></div>
<!------------ Including jQuery Date UI with CSS -------------->


<script src="//code.jquery.com/jquery-1.12.3.js"></script>
<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script src="http://code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
<script type="text/javascript">
$(document).ready(function() {
   
	
	
	$('.qtype').click(function(){
		var qval = $(this).val();
		//alert(qval);
		$("#referencenumber").val('');
		$(".statusTimeStamp").val('');
		if(qval == 3){
			$(".ponodiv").hide();
			$(".datef").show();
		}
		else{
			$(".datef").hide();
		}
		
		if(qval == 1){
			$(".datef").hide();
			$(".ponodiv").show();
		}
		else{
			$(".ponodiv").hide();
			
		}
		
	});
	
	$(function() {
		$("#datepicker").datepicker();
		$("#datepicker").datepicker("option", "dateFormat", 'yy-mm-dd');
		// Pass the user selected date format.
		/*$("#format").change(function() {
		$("#datepicker").datepicker("option", "dateFormat", 'yyyy-mm-dd');
		});*/
	});
	
	
	$(document).on('click','#getorderstatus', function(){
		var qval = $('input[name=querytype]:checked').val();
		var selSupplier = $('#selSupplier').val();
		var dtype = $('#dtype').val();
		var referencenumber = $('#referencenumber').val();
		var statusTimeStamp = $('.statusTimeStamp').val();
		
		var error = 0;
		if(selSupplier == "")
			{
				alert("Select Supplier");
				error = 1;
				return false
			}
			
		if($('input[name=querytype]:checked').length<=0)
			{
				alert("No Querytype checked");
				error = 1;
				return false
			}
			
		if(error == 0){
			
			if(dtype=="normal"){
				
				callapi(selSupplier, qval, dtype, referencenumber, statusTimeStamp);
				return false
			}else{
				this.form.action = "orderstatusdetail.php";
				$("#orderdetailsfrm").submit();
				return false
			}
			return false
		}	
		return false
	});
	
	function callapi(selSupplier, qval, dtype, referencenumber, statusTimeStamp){
		
		var newurl="orderstatusdetail_aj.php";
		var data={};
		data['selSupplier']=selSupplier;
		data['querytype']=qval;
		data['dtype']=dtype;
		data['referencenumber']=referencenumber;
		data['statusTimeStamp']=statusTimeStamp;
		$.ajax({ url: newurl,
			  data: data,
			  type: 'post',
			  beforeSend: function(output) {
			 $("#loader").html('Please wait...');
			 $("#datacontent").html('');
			  },
			  success: function(output) {
				//alert(output);
				
				$("#loader").html('');
				$("#datacontent").html(output);
				$('#gvOrdersloop').DataTable();
			}
		});	
	}
});
</script>
</div>
</div>
</body>
</html>