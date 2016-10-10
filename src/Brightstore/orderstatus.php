<?php 
@ob_start();
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Order Status</title>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css">
<link type="text/css"  href="../css/style.css" type="text/css" rel="stylesheet" />
</head>

<body>
<div class="mainx">
<form name="orderdetails" id="orderdetailsfrm" method="post" action="">


            <table class="tablebox">
                <tbody><tr>
                    <td>Select Status : 
                    </td>
                    <td>
                        <select id="selStatus" name="selStatus">
	<option value="New">New</option>
    <option value="Billed">Billed</option>
		<option value="Paid">Paid</option>
		<option value="Fulfilled">Fulfilled</option>
		<option value="Cancelled">Cancelled</option>
		<option value="BackOrdered">Back-Ordered</option>
		<option value="InProgress">In Progress</option>
		<option value="PartialBackOrder">Partial Back Order</option>
		<option value="Shipped">Shipped</option>
		<option value="BundleBuyPending">Bundle Buy Pending</option>
	

</select>
                    </td>
                </tr>
				
                <tr>
                    <td colspan="2" class="submitbtntd">
                        <input type="button" id="getshippingstatus" value="Get Orders" name="getshippingstatus">
                    </td>
                </tr>
            </tbody></table>
        


</form>
<div id="loader"></div>
<div id="datacontent"></div>
<!------------ Including jQuery Date UI with CSS -------------->


<script src="//code.jquery.com/jquery-1.12.3.js"></script>
<script src="http://code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
<script type="text/javascript">
$(document).ready(function() {
   
		
	$(document).on('click','#getshippingstatus', function(){
		
		var selStatus = $('#selStatus').val();
		
		
		var error = 0;
		if(selStatus == "")
			{
				alert("Select Supplier");
				error = 1;
				return false
			}
			
		
			
		if(error == 0){
			
			/*if(dtype=="normal"){
				
				callapi(selStatus);
				return false
			}else{
				this.form.action = "orderstatusdetail.php";
				$("#orderdetailsfrm").submit();
				return false
			}*/
			callapi(selStatus);
			return false
		}	
		return false
	});
	
	function callapi(selStatus){
		
		var newurl="orders.php";
		var data={};
		data['selStatus']=selStatus;
		
		$.ajax({ url: newurl,
			  data: data,
			  type: 'post',
			  beforeSend: function(output) {
			 $("#loader").html('Please wait...');
			 $("#datacontent").html("");
			  },
			  success: function(output) {
				//alert(output);
				
				$("#loader").html('');
				$("#datacontent").html(output);
				//$('#gvOrdersloop').DataTable();
			}
		});	
	}
});
</script>
</div>
</div>
</body>
</html>