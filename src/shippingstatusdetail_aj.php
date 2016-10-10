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

<!------------ Including jQuery Date UI with CSS -------------->



<?php

if(isset($_POST['selSupplier'])){
	$querytype =  isset($_POST['querytype']) ? $_POST['querytype'] : '';
	$dtype =  isset($_POST['dtype']) ? $_POST['dtype'] : '';
	if($querytype == ""){$querytype=4;}
	
	if($_POST['selSupplier'] != ""){
	$referencenumber = $_POST['referencenumber'];
	$statusTimeStamp = $_POST['statusTimeStamp'];
	$Supplier = $_POST['selSupplier'];
	
	include('config.php');	
	//$res = mysql_query("select row_id,userName, password, statusUrl, companyID, companyName, statusVersion, shippingUrl, shippingVersion from Companies where userName != '' and  password != '' and statusUrl != '' and statusVersion != '' and row_id='".$Supplier."'  order by companyName");

//while($rows = mysql_fetch_assoc($res)){
foreach($db->query("select row_id,userName, password, statusUrl, companyID, companyName, statusVersion, shippingUrl, shippingVersion from Companies where userName != '' and  password != '' and statusUrl != '' and statusVersion != '' and row_id='".$Supplier."'  order by companyName") as $rows) {

	$row_id = $rows['row_id'];
	$userName = $rows['userName'];
	$password = $rows['password'];
	$statusUrl = $rows['statusUrl'];
	$companyID = $rows['companyID'];
	$companyName = $rows['companyName'];
	$statusVersion = $rows['statusVersion'];
	$shippingUrl = $rows['shippingUrl'];
	$shippingVersion = $rows['shippingVersion'];

	$suppliers[$Supplier] = array('row_id'=>$row_id, 'companyID'=>$companyID, 'companyName'=>$companyName,
                        'statusUrl'  =>$statusUrl, 'statusVersion'=>$statusVersion,
                        'userName'=>$userName, 'password'=>$password,'shippingUrl'=>$shippingUrl, 'shippingVersion'=>$shippingVersion );

}

	//echo $Supplier;
	$username = '';
	$password = '';
	$statusUrl = '';
	$statusVersion =  '';
	//echo $statusTimeStamp;
	//if($statusTimeStamp!=""){$statusTimeStamp}
	if($Supplier != null){
		
		$username = $suppliers[$Supplier]['userName'];
		$password = $suppliers[$Supplier]['password'];
		$statusUrl = $suppliers[$Supplier]['statusUrl'];
		$statusVersion = $suppliers[$Supplier]['statusVersion'];
		$shippingUrl = $suppliers[$Supplier]['shippingUrl'];
		$shippingVersion = $suppliers[$Supplier]['shippingVersion'];
	}
	
	$p_error=array();
	$p_errortext="";
	if($username == ""){
		$p_error['username']="Username required.";
		$p_errortext.="Username required.<br />";
	}
	if($password == ""){
		$p_error['password ']="Password required.";
		$p_errortext.="Password required.<br />";
	}
	if($username == ""){
		$p_error['statusUrl']="statusUrl required.";
		$p_errortext.="statusUrl required.<br />";
	}
	if($username == ""){
		$p_error['statusVersion']="statusVersion required.";
		$p_errortext.="statusVersion required.<br />";
	}
	
	if(!empty($p_error)){
		echo $p_errortext;
		//print_r($p_error);
		die();
	}
	
/*
 *	$Id: wsdlclient2.php,v 1.3 2007/11/06 14:48:49 snichol Exp $
 *
 *	WSDL client sample.
 *
 *	Service: WSDL proxy
 *	Payload: rpc/encoded
 *	Transport: http
 *	Authentication: none
 */
               

$param = array(
	"wsVersion" => $statusVersion,
	"id" =>$username,
	"password" => $password,
	"queryType" => $querytype,
);

$paramSh = array(
	"wsVersion" => $shippingVersion,
	"id" =>$username,
	"password" => $password,
	"queryType" => $querytype,
);

	if($referencenumber!=""){
		$param['referenceNumber'] = $referencenumber;
		$paramSh['referenceNumber'] = $referencenumber;
	}
	if($statusTimeStamp!=""){
		$param['statusTimeStamp'] = $statusTimeStamp;
		$paramSh['shipmentDateTimeStamp'] = $statusTimeStamp;
		//array_push($param = 'statusTimeStamp' => $statusTimeStamp));
	}
	//print_r($param);

$wsdl=$statusUrl;
$erromsg="";
//$wsdl  = 'http://services.starline.com/OrderStatusService/WSDL/1.0.0/OrderStatusService.wsdl';
try {
$client = new SoapClient($wsdl);
} catch (SoapFault $e) {
   // echo "<pre>SoapFault: ".print_r($e, true)."</pre>\n";
	//echo "Failed to load";
	
   // echo "<pre>faultcode: '".$e->faultcode."'</pre>";
    //echo "<pre>faultstring: '".$e->getMessage()."'</pre>";
	$erromsg = "Failed to load";
	//die();
}
//var_dump($client->__getFunctions()); 
//var_dump($client->__getTypes());

/** api exception validation*/
if($erromsg != ""){
		
 if($dtype=='json'){
			echo json_encode(array('purchaseOrderNumber'=>array('SupplierError'=>$erromsg)));
		die();
	}	
	echo $erromsg; die();
}


    $response = $client->__soapCall("getOrderStatusDetails", array($param));

	/*if($querytype == '4' ){$responseSh='';}
elseif($shippingUrl == ""){$responseSh='';}
else{
$wsdlSh = $shippingUrl;

try {
	$clientSh = new SoapClient($wsdlSh);
	$responseSh = $clientSh->__soapCall("getOrderShipmentNotification", array($paramSh));
} catch (SoapFault $e) {
   // echo "<pre>SoapFault: ".print_r($e, true)."</pre>\n";
	//echo "Failed to load";
	
   // echo "<pre>faultcode: '".$e->faultcode."'</pre>";
   // echo "<pre>faultstring: '".$e->getMessage()."'</pre>";
	$erromsgSh = "Failed to load";
	$responseSh="SoapFault";
}


}*/


$resultr = json_decode(json_encode($response), true);
/*print_r('<pre>');
print_r(json_decode(json_encode($responseSh), true));
print_r('</pre>');*/
//print_r($response->OrderStatusArray);

if(isset($resultr['errorMessage'])){ 
		if($dtype=='json'){
			echo json_encode(array('purchaseOrderNumber'=>array('SupplierError'=>$resultr['errorMessage'])));
		die();
	}	
	echo $resultr['errorMessage']; die();
}
if(!isset($resultr['OrderStatusArray']['OrderStatus'])){
	if($dtype=='json'){
			echo json_encode(array('purchaseOrderNumber'=>array('SupplierError'=>'No details found')));
		die();
	}	
	echo " No details found."; die();
	}
$orstatus=$resultr['OrderStatusArray']['OrderStatus'];

/*$resultSh = json_decode(json_encode($responseSh), true);
print_r('<pre>');
print_r($resultSh);
print_r('</pre>');*/

function trackingno($carrier, $trno, $shipmentMethod){
	$carrier1=$carrier;	
	$carrier=strtolower($carrier);
	
	//$trno=strtolower($trno);
	$shipmentMethod1=$shipmentMethod; 
	$shipmentMethod=strtolower($shipmentMethod);
	
	$carrierdata=" (Not Specified)";
	$carrierRG="Not Specified";
	
		if($carrier !="" ){$carrierdata=" (".$carrier1.")";}
	if($carrier=='ups (usa)' || $carrier=='ups' || $carrier=='ups (us)'){
		
		$clink="http://wwwapps.ups.com/etracking/tracking.cgi?AcceptUPSLicenseAgreement=yes&TypeOfInquiryNumber=T&InquiryNumber1=";
		}
		elseif($carrier=="" && substr($shipmentMethod, 0, 3) === 'ups'){
			$clink="http://wwwapps.ups.com/etracking/tracking.cgi?AcceptUPSLicenseAgreement=yes&TypeOfInquiryNumber=T&InquiryNumber1=";
			$carrierdata =" (".$shipmentMethod1.")";
		}
		elseif($carrier=='fedex (usa)' || $carrier=='fedex' || $carrier=='fedex (us)'){$clink="https://www.fedex.com/apps/fedextrack/?action=track&cntry_code=us_english&tracknumbers=";
		}
		elseif($carrier=='' && substr($shipmentMethod, 0, 3) =='fedex'){$clink="https://www.fedex.com/apps/fedextrack/?action=track&cntry_code=us_english&tracknumbers=";
		$carrierdata =" (".$shipmentMethod1.")";
		}
		elseif($carrier=='us postal service'){
			$clink="https://tools.usps.com/go/TrackConfirmAction?qtc_tLabels1=";
		}
		
		elseif($carrier=='miscellaneous' || $carrier=='miscellaneous (us)'){$clink="";}
		else{$clink="";}
		
		if($clink == ""){
			if($carrier1!=""){$carrierRG=$carrier1;}
			if (preg_match("/\b(1Z ?[0-9A-Z]{3} ?[0-9A-Z]{3} ?[0-9A-Z]{2} ?[0-9A-Z]{4} ?[0-9A-Z]{3} ?[0-9A-Z]|[\dT]\d\d\d ?\d\d\d\d ?\d\d\d)\b/", $trno)) {
				$clink="http://wwwapps.ups.com/etracking/tracking.cgi?AcceptUPSLicenseAgreement=yes&TypeOfInquiryNumber=T&InquiryNumber1=";
				$carrierdata =" (".$carrierRG.")";
			}
			
			elseif (preg_match("/(\b96\d{20}\b)|(\b\d{15}\b)|(\b\d{12}\b)/", $trno)) {
				$clink="https://www.fedex.com/apps/fedextrack/?action=track&cntry_code=us_english&tracknumbers=";
				$carrierdata =" (".$carrierRG.")";
			}
			elseif (preg_match("/\b((98\d\d\d\d\d?\d\d\d\d|98\d\d) ?\d\d\d\d ?\d\d\d\d( ?\d\d\d)?)\b/", $trno)) {
				$clink="https://www.fedex.com/apps/fedextrack/?action=track&cntry_code=us_english&tracknumbers=";
				$carrierdata =" (".$carrierRG.")";
			}
			elseif (preg_match("/^[0-9]{15}$/", $trno)) {
				$clink="https://www.fedex.com/apps/fedextrack/?action=track&cntry_code=us_english&tracknumbers=";
				$carrierdata =" (".$carrierRG.")";
			}
		}
		
		if($clink != ""){
			$trdata="<a href='".$clink."".$trno."' target='_blank'>".$trno."</a>".$carrierdata.", ";
		}
		else{
			$trdata=$trno."".$carrierdata.", ";
			}
		if($trno==""){$trdata="";}
		return $trdata;
	}

function searcharraySh($ponumber, $status, $paramSh, $shippingUrl, $querytype){
		/*print "<pre>";	
		print_r($arraysh);
		print "</pre>";	*/
		$carrier ="";
		$clink="";
				
		$resultshvalreturn = "";
		$shippingdetailsarray = "";
		
		/*$errormcode = isset($arraysh['errorMessage']['code']) ? $arraysh['errorMessage']['code'] : "";
		
		if($errormcode == "0" || $errormcode == ""){
			//$shippingdetailsarray .= "";
		}else{
			$shippingdetailsarray="Not Available";
			return $shippingdetailsarray;
			die();
		}*/
		//$poarray=isset($arraysh['OrderShipmentNotificationArray']) ? $arraysh['OrderShipmentNotificationArray'] : "";
	//$shippingdetailsarray = "Not Available"; 
	if($status=="80" || $status=="75"){
		
		$responseSh='';
		//if($querytype == '4' ){$responseSh='';}
		if($shippingUrl == ""){$responseSh='';}
		else{
		$wsdlSh = $shippingUrl;
		$paramSh['referenceNumber'] = $ponumber;
		try {
			$clientSh = new SoapClient($wsdlSh);
			$responseSh = $clientSh->__soapCall("getOrderShipmentNotification", array($paramSh));
		} catch (SoapFault $e) {
		   // echo "<pre>SoapFault: ".print_r($e, true)."</pre>\n";
			//echo "Failed to load";
			
		   // echo "<pre>faultcode: '".$e->faultcode."'</pre>";
		   // echo "<pre>faultstring: '".$e->getMessage()."'</pre>";
			$erromsgSh = "Failed to load";
			$responseSh="SoapFault";
		}
		
		}
		$arraysh = json_decode(json_encode($responseSh), true);
		
		if($arraysh=="SoapFault"){
				$resultshvalreturn="Fail to load";
				return $resultshvalreturn;
				die();
			}
	
			$narray=isset($arraysh['OrderShipmentNotificationArray']['OrderShipmentNotification']) ? $arraysh['OrderShipmentNotificationArray']['OrderShipmentNotification'] : "";
		if(isset($arraysh['OrderShipmentNotificationArray']['OrderShipmentNotification']['purchaseOrderNumber'])){
			$ponumsearcharray=array($narray);
		}
		else{
			$ponumsearcharray=$narray;	
			}
		/*print "<pre>";	
		print_r(count($ponumsearcharray));
		print "</pre>";*/
		//echo "test";
		
		
		if(($ponumsearcharray == "")){$shippingdetailsarray= "Not Available"; }
			if(is_array($ponumsearcharray)){
			
			foreach($ponumsearcharray as $ponumsearch){
				if($ponumber ==$ponumsearch['purchaseOrderNumber']){
				//$resultshvalreturn="";
				$ponumber = isset($ponumsearch['purchaseOrderNumber']) ? $ponumsearch['purchaseOrderNumber'] : "";
				$pocomplete = isset($ponumsearch['complete']) ? $ponumsearch['complete'] : "";
				//$shippingdetailsarray.= "<div class='div1'>Po:".$ponumber."<br />complete:".$pocomplete;
				$shippingdetailsarray.= "<div class='div1'>";
				$parray = isset($ponumsearch['SalesOrderArray']['SalesOrder']) ? $ponumsearch['SalesOrderArray']['SalesOrder'] : "";
				if(isset($ponumsearch['SalesOrderArray']['SalesOrder']['ShipmentLocationArray'])){
						$salesorderarray=array($parray);
				}
				else{
					$salesorderarray=$parray;
				}
				/*print "<pre>";	
				print_r($salesorderarray);
				print "</pre>";*/
					$shippingdetailsarray.= "<div class='div2'><h2>Salesorders</h2>";
					if(($salesorderarray == "")){$shippingdetailsarray.= "Salesorders: Not Available"; }
					if(is_array($salesorderarray)){
						
					foreach($salesorderarray as $salesorder){
						$salesOrderNumber = isset($salesorder['salesOrderNumber']) ? $salesorder['salesOrderNumber'] : "";
				$socomplete = isset($salesorder['complete']) ? $salesorder['complete'] : "";
						$shippingdetailsarray.= "<div class='div2_1'>salesOrderNumber:".$salesOrderNumber."<br />complete:".$socomplete;
					$sarray = isset($salesorder['ShipmentLocationArray']['ShipmentLocation']) ? $salesorder['ShipmentLocationArray']['ShipmentLocation'] : "";
					if(isset($salesorder['ShipmentLocationArray']['ShipmentLocation']['PackageArray'])){
							$ShipmentLocationarray=array($sarray);
					}
					else{
						$ShipmentLocationarray=$sarray;
					}
					/*print "<pre>";	
					print_r($ShipmentLocationarray);
					print "</pre>";*/
					$shippingdetailsarray.= "<div class='div3'><h2>ShipmentLocations</h2>";
					if(($ShipmentLocationarray == "")){$shippingdetailsarray.= "ShipmentLocations: Not Available"; }
					if(is_array($ShipmentLocationarray)){
						
						foreach($ShipmentLocationarray as $ShipmentLocation){
							
							$shlid = isset($ShipmentLocation['id']) ? $ShipmentLocation['id'] : "";
				$shlcomplete = isset($ShipmentLocation['complete']) ? $ShipmentLocation['complete'] : "";
				$shlshipmentDestinationType = isset($ShipmentLocation['shipmentDestinationType']) ? $ShipmentLocation['shipmentDestinationType'] : "";
				
				/** ShipFromAddress variable **/
				$shlfaddress1 = isset($ShipmentLocation['ShipFromAddress']['address1']) ? $ShipmentLocation['ShipFromAddress']['address1'] : "";
				$shlfaddress2 = isset($ShipmentLocation['ShipFromAddress']['address2']) ? $ShipmentLocation['ShipFromAddress']['address2'] : "";
				$shlfaddress3 = isset($ShipmentLocation['ShipFromAddress']['address3']) ? $ShipmentLocation['ShipFromAddress']['address3'] : "";
				$shlfaddress4 = isset($ShipmentLocation['ShipFromAddress']['address4']) ? $ShipmentLocation['ShipFromAddress']['address4'] : "";
				$shlfcity = isset($ShipmentLocation['ShipFromAddress']['city']) ? $ShipmentLocation['ShipFromAddress']['city'] : "";
				$shlfregion = isset($ShipmentLocation['ShipFromAddress']['region']) ? $ShipmentLocation['ShipFromAddress']['region'] : "";
				$shlfpostalCode = isset($ShipmentLocation['ShipFromAddress']['postalCode']) ? $ShipmentLocation['ShipFromAddress']['postalCode'] : "";
				$shlfcountry = isset($ShipmentLocation['ShipFromAddress']['country']) ? $ShipmentLocation['ShipFromAddress']['country'] : "";
				
				/** ShipToAddress variable **/
				$shltaddress1 = isset($ShipmentLocation['ShipToAddress']['address1']) ? $ShipmentLocation['ShipToAddress']['address1'] : "";
				$shltaddress2 = isset($ShipmentLocation['ShipToAddress']['address2']) ? $ShipmentLocation['ShipToAddress']['address2'] : "";
				$shltaddress3 = isset($ShipmentLocation['ShipToAddress']['address3']) ? $ShipmentLocation['ShipToAddress']['address3'] : "";
				$shltaddress4 = isset($ShipmentLocation['ShipToAddress']['address4']) ? $ShipmentLocation['ShipToAddress']['address4'] : "";
				$shltcity = isset($ShipmentLocation['ShipToAddress']['city']) ? $ShipmentLocation['ShipToAddress']['city'] : "";
				$shltregion = isset($ShipmentLocation['ShipToAddress']['region']) ? $ShipmentLocation['ShipToAddress']['region'] : "";
				$shltpostalCode = isset($ShipmentLocation['ShipToAddress']['postalCode']) ? $ShipmentLocation['ShipToAddress']['postalCode'] : "";
				$shltcountry = isset($ShipmentLocation['ShipToAddress']['country']) ? $ShipmentLocation['ShipToAddress']['country'] : "";
				
						$shippingdetailsarray.= "<div class='div3_1'>id:".$shlid."<br />
						complete:".$shlcomplete."<br />
						
						shipmentDestinationType:".$shlshipmentDestinationType."<br /><div class='div3_2'><h2>Shipping From Address</h2>
						Address1: ".$shlfaddress1."<br />
                        Address2: ".$shlfaddress2."<br />
                        Address3: ".$shlfaddress3."<br />
                        Address4: ".$shlfaddress4."<br />
                        City: ".$shlfcity."<br />
                        Region: ".$shlfregion."<br />
                        PostalCode: ".$shlfpostalCode."<br />
                        Country: ".$shlfcountry."<br />
						
						</div>
						<div class='div3_2'><h2>Shipping To Address</h2>
						Address1: ".$shltaddress1."<br />
                        Address2: ".$shltaddress2."<br />
                        Address3: ".$shltaddress3."<br />
                        Address4: ".$shltaddress4."<br />
                        City: ".$shltcity."<br />
                        Region: ".$shltregion."<br />
                        PostalCode: ".$shltpostalCode."<br />
                        Country: ".$shltcountry."<br />
						</div>";
							
							
						$sharray = isset($ShipmentLocation['PackageArray']['Package']) ? $ShipmentLocation['PackageArray']['Package'] : "";
						if(isset($ShipmentLocation['PackageArray']['Package']['trackingNumber'])){
								$PackageArrayarray=array($sharray);
						}
						else{
							$PackageArrayarray=$sharray;
						}
						/*print "<pre>";	
						print_r($PackageArrayarray);
						print "</pre>";*/
							$shippingdetailsarray.= "<div class='div4'><h2>Package</h2>";
							if(($PackageArrayarray == "")){$shippingdetailsarray.= "Packages: Not Available"; }
							if(is_array($PackageArrayarray)){
									

											foreach($PackageArrayarray as $Package){
												$pid = isset($Package['id']) ? $Package['id'] : '';
									$ptrackingNumber = isset($Package['trackingNumber']) ? $Package['trackingNumber'] : '';
									$pshipmentDate = isset($Package['shipmentDate']) ? $Package['shipmentDate'] : '';
									$pdimUOM = isset($Package['dimUOM']) ? $Package['dimUOM'] : '';
									$plength = isset($Package['length']) ? $Package['length'] : '';
									$pwidth = isset($Package['width']) ? $Package['width'] : '';
									$pheight = isset($Package['height']) ? $Package['height'] : '';
									$pweightUOM = isset($Package['weightUOM']) ? $Package['weightUOM'] : '';
									$pweight = isset($Package['weight']) ? $Package['weight'] : '';
									$pcarrier = isset($Package['carrier']) ? $Package['carrier'] : '';
									$pshipmentMethod = isset($Package['shipmentMethod']) ? $Package['shipmentMethod'] : '';
									$pshippingAccount = isset($Package['shippingAccount']) ? $Package['shippingAccount'] : '';
									$pshipmentTerms = isset($Package['shipmentTerms']) ? $Package['shipmentTerms'] : '';
												
								$shippingdetailsarray.= "<div class='div4_1'>
						id: ".$pid." <br />
						trackingNumber: ".$ptrackingNumber." <br />
						shipmentDate: ".$pshipmentDate." <br />
						dimUOM: ".$pdimUOM." <br />
						length: ".$plength." <br />
						width: ".$pwidth." <br />
						height: ".$pheight." <br />
						weightUOM: ".$pweightUOM." <br />
						weight: ".$pweight." <br />
						carrier: ".$pcarrier." <br />
						shipmentMethod: ".$pshipmentMethod." <br />
						shippingAccount: ".$pshippingAccount." <br />
						shipmentTerms: ".$pshipmentTerms." <br />


						
						
						
						";
														$trakingno = trackingno($pcarrier, $ptrackingNumber, $pshipmentMethod);
														$resultshvalreturn.= $trakingno;
													
								$itemarray = isset($Package['ItemArray']['Item']) ? $Package['ItemArray']['Item'] : "";	
						
						if(isset($Package['ItemArray']['Item']['supplierProductId'])){
								$ItemArrayarray=array($itemarray);
								//$itemcount = count($Package['ItemArray']['Item']);
						}
						else{
							$ItemArrayarray=$itemarray;
							//$itemcount = count($ItemArrayarray);
						}
						
						$shippingdetailsarray.= "<div class='div5'><h2>Items</h2>";
							
							//echo "---".$itemcount."---";
							if(($itemarray == "")){$shippingdetailsarray.= "Items: Not Available"; }
							if(is_array($ItemArrayarray)){
									


											foreach($ItemArrayarray as $item){
		$isupplierProductId = isset($item['supplierProductId']) ? $item['supplierProductId'] : '';
$isupplierPartId = isset($item['supplierPartId']) ? $item['supplierPartId'] : '';
$idistributorProductId = isset($item['distributorProductId']) ? $item['distributorProductId'] : '';
$idistributorPartId = isset($item['distributorPartId']) ? $item['distributorPartId'] : '';
$ipurchaseOrderLineNumber = isset($item['purchaseOrderLineNumber']) ? $item['purchaseOrderLineNumber'] : '';
$iquantity = isset($item['quantity']) ? $item['quantity'] : '';

$shippingdetailsarray.= "<div class='div5_1'>
						supplierProductId: ".$isupplierProductId." <br />
supplierPartId: ".$isupplierPartId." <br />
distributorProductId: ".$idistributorProductId." <br />
distributorPartId: ".$idistributorPartId." <br />
purchaseOrderLineNumber: ".$ipurchaseOrderLineNumber." <br />
quantity: ".$iquantity." <br />



						
						</div>
						
						";

											}
											
							}					
													
							$shippingdetailsarray.= "</div>";						
						$shippingdetailsarray.= "</div>";							
						
						}
						
							}
							$shippingdetailsarray.= "</div>";
						$shippingdetailsarray.= "</div>";	
					}
						
					}
						$shippingdetailsarray.= "</div>";
					$shippingdetailsarray.= "</div>";
		}
						
					}//die();
					$shippingdetailsarray.= "</div>";
				$shippingdetailsarray.="</div>";
			}
			}
			}
			
}
			
			
			
		

			
			if($resultshvalreturn=="" && ($status == "80" || $status == "75")){ $resultshvalreturn="Shipped"; $shippingdetailsarray= "Not Available";}
			elseif($status == "99"){ $resultshvalreturn="Cancelled"; $shippingdetailsarray= "Not Available";}
			elseif($resultshvalreturn=="" && ($status == "10" || $status == "11" || $status == "20" || $status == "30" || $status == "40" || $status == "41" || $status == "42" || $status == "43" || $status == "44" || $status == "60" || $status == "70")){$resultshvalreturn="In Process";$shippingdetailsarray= "Not Available";}
			return array($resultshvalreturn, $shippingdetailsarray);
			set_time_limit(0); 
	}

/**** json view ****/

/*** Table View***/
if(isset($resultr['OrderStatusArray']['OrderStatus']['purchaseOrderNumber'])){	
	$orstatus=array($resultr['OrderStatusArray']['OrderStatus']);	
}
else{
	$orstatus=$resultr['OrderStatusArray']['OrderStatus'];
}
?>
<div class="gridline">
            
	<table id="gvOrdersloop" class="display" data-page-length="25" data-order="[[ 0, &quot;asc&quot; ]]" width="100%">
		<thead><tr>
			<th scope="col">Purchase Order Number</th>
			<th scope="col">Status ID</th><th scope="col">Status Name</th><th scope="col">Expected Ship Date</th><th scope="col">Expected Delivery Date</th><th scope="col">Email Address</th><th scope="col">Phone Number</th><th scope="col">Valid Timestamp</th><th>Shipping Status </th>
		</tr>
		</thead>
		<tbody>
		<?php foreach($orstatus as $resulloop){
			$orderstatussetailhtml='';
			if(isset($resulloop['OrderStatusDetailArray']['OrderStatusDetail']['statusName'])){
				$orstatusdetailarray=array($resulloop['OrderStatusDetailArray']['OrderStatusDetail']);
			}
			else{
				$orstatusdetailarray=$resulloop['OrderStatusDetailArray']['OrderStatusDetail'];
				}
			//print_r($orstatus);
			$purchaseOrderNumber = isset($resulloop['purchaseOrderNumber']) ? $resulloop['purchaseOrderNumber'] : '';				
			$statusName="";
			$statusID =	'';
			$expectedDeliveryDate = '';
			$additionalExplanation = '';
			$responseRequired = '';
			$validTimestamp = '';
			$validTimestamp = '';
			
			$name =	'';
			$emailAddress =	 '';
			$phoneNumber =	'';
				
			
			foreach($orstatusdetailarray as $orstatus1){
				
				$factoryOrderNumber = isset($orstatus1['factoryOrderNumber']) ? $orstatus1['factoryOrderNumber']	 : '';
				$statusName =	isset($orstatus1['statusName']) ? $orstatus1['statusName']	 : '';
				$statusID =	isset($orstatus1['statusID']) ? $orstatus1['statusID']	 : '';
				$expectedShipDate =	isset($orstatus1['expectedShipDate']) ? $orstatus1['expectedShipDate']	 : '';
				$expectedDeliveryDate =	isset($orstatus1['expectedDeliveryDate']) ? $orstatus1['expectedDeliveryDate']	 : '';
				$additionalExplanation = isset($orstatus1['additionalExplanation']) ? $orstatus1['additionalExplanation']	 : '';
				$responseRequired =	isset($orstatus1['responseRequired']) ? $orstatus1['responseRequired']	 : '';
				$validTimestamp = isset($orstatus1['validTimestamp']) ? $orstatus1['validTimestamp'] : '';
				$validTimestamp = isset($orstatus1['validTimestamp']) ? $orstatus1['validTimestamp'] : '';
				$statusName =  str_replace(chr(194).chr(160), ' ', $statusName);  //removes \u00a0
				$statusName = str_replace('â', ' ', $statusName);  //removes \u00e2
				$statusName = str_replace(chr(194).chr(128).chr(194).chr(153), ' ', $statusName);
				
				//if($querytype=='4' && ($statusID=='80' || $statusID=='99')){$statusID="";}
				 
				if($statusID != ""){
				$orderstatussetailhtml.='<tr>';
				$orderstatussetailhtml.='<td>'.$purchaseOrderNumber.'</td>';
			
				
				if($expectedShipDate != ""){$expectedShipDate = date("Y-m-d H:i:s",strtotime($expectedShipDate));}
				if($expectedDeliveryDate != ""){$expectedDeliveryDate = date("Y-m-d H:i:s",strtotime($expectedDeliveryDate));}
				if($validTimestamp != ""){$validTimestamp = date("Y-m-d H:i:s",strtotime($validTimestamp));}
				
				$orderstatussetailhtml.='<td>'.$statusID.'</td>';
            $orderstatussetailhtml.='<td>'.$statusName.'</td>';
            $orderstatussetailhtml.='<td>'.$expectedShipDate.'</td>';
           $orderstatussetailhtml.= '<td>'.$expectedDeliveryDate.'</td>';
		   
            
			
           
			
				if(isset($orstatus1['ResponseToArray']['RespondTo']['name'])){
					$ResponseToArray1=array($orstatus1['ResponseToArray']['RespondTo']);
				}
				else{
					if(empty($orstatus1['ResponseToArray']['RespondTo'])){
						$ResponseToArray1="";
					}
					else{
						$ResponseToArray1=$orstatus1['ResponseToArray']['RespondTo'];
					}
					
					}
				
					$name =	'';
					$emailAddress =	'';
					$phoneNumber = '';
					$emailAddress1 =	'';
					$phoneNumber1 = '';
				if(is_array($ResponseToArray1)){
					//$i=0;
					$e=0;
					$ph=0;
					foreach($ResponseToArray1 as $RespondTo1){
						
					$name=	isset($RespondTo1['name']) ? $RespondTo1['name'] : '';
					$emailAddress=	isset($RespondTo1['emailAddress']) ? $RespondTo1['emailAddress'] : '';
					$phoneNumber=	isset($RespondTo1['phoneNumber']) ? $RespondTo1['phoneNumber'] : '';
					
					if($emailAddress != ""){
						if($e > 0){
							$emailAddress.=", ";
						}
						$emailAddress1.=$emailAddress;
						$e++;
					}
					if($phoneNumber != ""){
						if($ph > 0){
							$phoneNumber.=", ";
						}
						$phoneNumber1.=$phoneNumber;
						$ph++;
					}
					
					//$i++;				
					}
				}
				$orderstatussetailhtml.='<td>'.$emailAddress1.'</td>';
				$orderstatussetailhtml.='<td>'.$phoneNumber1.'</td>';
				
				$shstatus = searcharraySh($purchaseOrderNumber, $statusID, $paramSh, $shippingUrl, $querytype);
				 $orderstatussetailhtml.='<td>'.$validTimestamp.'</td>';
			$orderstatussetailhtml.='<td>'.$shstatus[0].'</td>';
				$orderstatussetailhtml.='</tr>';
				$orderstatussetailhtml.='<tr><td colspan="9">'.$shstatus[1].'<td></tr>';	
				}
			}
			
			
				
				 echo $orderstatussetailhtml; 
			?>
		
		<?php
			set_time_limit(0);  
		}
		?>

	</tbody></table>
	<?php //} ?>
	</div>



<?php } ?>
<?php } ?>
</div>
</div>
</body>
</html>