<?php 
@ob_start();
session_start();

if(isset($_POST['selSupplier'])){
	   
	$querytype =  isset($_POST['querytype']) ? $_POST['querytype'] : '';
	$dtype =  isset($_POST['dtype']) ? $_POST['dtype'] : '';
	if($querytype == ""){$querytype=4;}
	if($dtype == 'json'){
		header('Content-Type: application/json');
	}
	if($_POST['selSupplier'] != ""){
	$referencenumber = $_POST['referencenumber'];
	$statusTimeStamp = $_POST['statusTimeStamp'];
	$Supplier = $_POST['selSupplier'];
	
	
	
include('config.php');

				
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
//require_once('lib/nusoap.php');
            

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
	///print_r($paramSh);
$erromsg="";
$wsdl=$statusUrl;

//$wsdl  = 'http://services.starline.com/OrderStatusService/WSDL/1.0.0/OrderStatusService.wsdl';
//$client = new SoapClient($wsdl);
try {
$client = new SoapClient($wsdl);
$response = $client->__soapCall("getOrderStatusDetails", array($param)); 
} catch (SoapFault $e) {
   // echo "<pre>SoapFault: ".print_r($e, true)."</pre>\n";
	//echo "Failed to load";
	
   // echo "<pre>faultcode: '".$e->faultcode."'</pre>";
   // echo "<pre>faultstring: '".$e->getMessage()."'</pre>";
	$erromsg = "Failed to load";
	die();
}

/** api exception validation*/
if($erromsg != ""){
		
 if($dtype=='json'){
			echo json_encode(array('purchaseOrderNumber'=>array('SupplierError'=>$erromsg)));
		die();
	}	
	echo $erromsg; die();
}

//var_dump($client->__getFunctions()); 
//var_dump($client->__getTypes());


$resultr = json_decode(json_encode($response), true);
/*print_r('<pre>');
print_r($responseSh);
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

//$resultSh = json_decode(json_encode($responseSh), true);
/*print_r('<pre>');
//print_r($response);
print_r($orstatus);
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
		print "</pre>";*/
		$carrier ="";
		$clink="";
				
		$resultshvalreturn = "";
		
		
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
		print_r($ponumsearcharray);
		print "</pre>";*/
			if(is_array($ponumsearcharray)){
			foreach($ponumsearcharray as $ponumsearch){
				if($ponumber ==$ponumsearch['purchaseOrderNumber']){
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
					if(is_array($salesorderarray)){
					foreach($salesorderarray as $salesorder){
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
					if(is_array($ShipmentLocationarray)){
						foreach($ShipmentLocationarray as $ShipmentLocation){
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
							
							if(is_array($PackageArrayarray)){
							$i=0;
											foreach($PackageArrayarray as $Package){
												//if($i!=0){$resultshvalreturn.= ", ";}
													//if(isset($Package['trackingNumber'])){
														$ptrackingNumber = isset($Package['trackingNumber']) ? $Package['trackingNumber'] : '';
														$pcarrier = isset($Package['carrier']) ? $Package['carrier'] : '';
														$pshipmentMethod = isset($Package['shipmentMethod']) ? $Package['shipmentMethod'] : '';
														$trakingno = trackingno($pcarrier, $ptrackingNumber, $pshipmentMethod);
														$resultshvalreturn.= $trakingno;
														
														$i++;
													//}
						
						}
							}
					}
			
					}
		}
		
					}//die();
		//$resultshvalreturn.= "<br / >/*********Po Array loop*********/<br / ><br / >";
			}
			}
			}
}
			
			
			
		

			
			if($resultshvalreturn=="" && ($status == "80" || $status == "75")){ $resultshvalreturn="Shipped"; }
			elseif($status == "99"){ $resultshvalreturn="Canceled"; }
			elseif($resultshvalreturn=="" && ($status == "10" || $status == "11" || $status == "20" || $status == "30" || $status == "40" || $status == "41" || $status == "42" || $status == "43" || $status == "44" || $status == "60" || $status == "70")){$resultshvalreturn="In Process";}
			return $resultshvalreturn;
			set_time_limit(0);
	}


function searcharrayJson($status){
		$resultshvalreturn='';
		if($status == "80" || $status == "75"){ $resultshvalreturn="Shipped"; }
			elseif($status == "99"){ $resultshvalreturn="Canceled"; }
			elseif($status == "10" || $status == "11" || $status == "20" || $status == "30" || $status == "40" || $status == "41" || $status == "42" || $status == "43" || $status == "44" || $status == "60" || $status == "70"){$resultshvalreturn="In Process";}
			return $resultshvalreturn;
			set_time_limit(0);
	}
	
/**** json view ****/
if($dtype=='json'){
	//echo json_encode($orstatus);
		$orstatusJ=array();
		
if(isset($resultr['OrderStatusArray']['OrderStatus']['purchaseOrderNumber'])){	
	$orstatus=array($resultr['OrderStatusArray']['OrderStatus']);	
}
else{
	$orstatus=$resultr['OrderStatusArray']['OrderStatus'];
}		
		
		
		
		foreach($orstatus as $resulloopJ){
			
			if(isset($resulloopJ['OrderStatusDetailArray']['OrderStatusDetail']['statusName'])){
				$orstatus1array=array($resulloopJ['OrderStatusDetailArray']['OrderStatusDetail']);
			}
			else{
				$orstatus1array=$resulloopJ['OrderStatusDetailArray']['OrderStatusDetail'];
				}
				
			//$orstatus1array=$resulloopJ['OrderStatusDetailArray']['OrderStatusDetail'];
			$orstatusJarray='';
			$purchaseOrderNumber = isset($resulloopJ['purchaseOrderNumber']) ? $resulloopJ['purchaseOrderNumber'] : '';
			foreach($orstatus1array as $orstatus1){
				/*print "</pre>";	
				print_r($orstatus1);
				print "</pre>";	*/
				
			//$orstatus1=isset($orstatus['OrderStatusDetail']) ? $orstatus['OrderStatusDetail'] : "";
			$factoryOrderNumber = isset($orstatus1['factoryOrderNumber']) ? $orstatus1['factoryOrderNumber']	 : '';
				$statusName =	isset($orstatus1['statusName']) ? $orstatus1['statusName']	 : '';
				$statusID =	isset($orstatus1['statusID']) ? $orstatus1['statusID']	 : '';
				$expectedShipDate =	isset($orstatus1['expectedShipDate']) ? $orstatus1['expectedShipDate']	 : '';
				$expectedDeliveryDate =	isset($orstatus1['expectedDeliveryDate']) ? $orstatus1['expectedDeliveryDate']	 : '';
				$additionalExplanation = isset($orstatus1['additionalExplanation']) ? $orstatus1['additionalExplanation']	 : '';
				$responseRequired =	isset($orstatus1['responseRequired']) ? $orstatus1['responseRequired']	 : '';
				$validTimestamp = isset($orstatus1['validTimestamp']) ? $orstatus1['validTimestamp'] : '';


				$validTimestamp = isset($orstatus1['validTimestamp']) ? $orstatus1['validTimestamp']	 : '';
				$name =	isset($orstatus1['ResponseToArray']['RespondTo']['name']) ? $orstatus1['ResponseToArray']['RespondTo']['name']	 : '';
				$emailAddress =	isset($orstatus1['ResponseToArray']['RespondTo']['emailAddress']) ? $orstatus1['ResponseToArray']['RespondTo']['emailAddress']	 : '';
				$phoneNumber =	isset($orstatus1['ResponseToArray']['RespondTo']['phoneNumber']) ? $orstatus1['ResponseToArray']['RespondTo']['phoneNumber'] : '';
				
				$statusName =  str_replace(chr(194).chr(160), ' ', $statusName);  //removes \u00a0
				$statusName = str_replace('â', ' ', $statusName);  //removes \u00e2
				$statusName = str_replace(chr(194).chr(128).chr(194).chr(153), ' ', $statusName);
	
		/*if($statusID == ""){	
			echo json_encode(array('purchaseOrderNumber'=>array('SupplierError'=>'No details found.')));
			die();	
		}*/
				if($expectedShipDate!=""){$expectedShipDate=date("Y-m-d H:i:s", strtotime($expectedShipDate));}
				if($expectedDeliveryDate!=""){$expectedDeliveryDate=date("Y-m-d H:i:s", strtotime($expectedDeliveryDate));}
				if($validTimestamp!=""){
					$validTimestampD=date("Y-m-d", strtotime($validTimestamp));
					$validTimestampT=date("H:i:s", strtotime($validTimestamp));
				}else{
					$validTimestampD="";
					$validTimestampT="";
					}
					//$shstatus = searcharraySh($purchaseOrderNumber, $statusID, $paramSh, $shippingUrl, $querytype);
					$shstatus = searcharrayJson($statusID);
				if($querytype=='4' && ($statusID=='80' || $statusID=='99')){}
				else{
					
				$orstatusJarray[] = array(
					'AcknowledgeID' => $factoryOrderNumber,
					'ElectronicStatusId' => $statusID,
					'ElectronicStatusCode' => $statusName,
					'ScheduledPickDate' => $expectedShipDate,
					'EstDeliveryDate' => $expectedDeliveryDate,
					'SupplierComment' => $additionalExplanation,
					'SupplierNeesResponse' => $responseRequired,
					'SupplierResponseDate' => $validTimestampD,
					'SupplierResponseTime' => $validTimestampT,
					'SupplierContact' => array($name,$emailAddress,$phoneNumber),
					'VernonInquiryDate' => gmdate("Y-m-d"),
					'VernonInquiryTime' => gmdate('H:i:s'),
					'ShippingStatus' => $shstatus,
				);
		
				}
				}
				
				$orstatusJ[] = array('purchaseOrderNumber' => array($orstatusJarray));
			set_time_limit(0);
		}
//}
		//print_r($orstatusJ);
		//return $orstatusJ;

	echo json_encode($orstatusJ);	
	die();
}

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Order Status</title>
<link type="text/css"  href="css/style.css" type="text/css" rel="stylesheet" />

</head>

<body>

<div class="mainx">

<!------------ Including jQuery Date UI with CSS -------------->



<?php





/*** Table View***/
if(isset($resultr['OrderStatusArray']['OrderStatus']['purchaseOrderNumber'])){	
	$orstatus=array($resultr['OrderStatusArray']['OrderStatus']);	
}
else{
	$orstatus=$resultr['OrderStatusArray']['OrderStatus'];
}

?>
    
    
<div class="gridline">
            
	<table id="gvOrdersloop" class="display"  width="100%">
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
				
				if($querytype=='4' && ($statusID=='80' || $statusID=='99')){$statusID="";}
				
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
					}
				}
				$orderstatussetailhtml.='<td>'.$emailAddress1.'</td>';
				$orderstatussetailhtml.='<td>'.$phoneNumber1.'</td>';
				
				$shstatus = searcharraySh($purchaseOrderNumber, $statusID, $paramSh, $shippingUrl, $querytype);
				 $orderstatussetailhtml.='<td>'.$validTimestamp.'</td>';
			$orderstatussetailhtml.='<td>'.$shstatus.'</td>';
				$orderstatussetailhtml.='</tr>';
				}
			}
			
			//$orderstatussetailhtml.='</table>';
				
			//if($statusName != ""){
				
				 echo $orderstatussetailhtml; 
			?>
		
		<?php
			//} 
			
			set_time_limit(0);
		}
		?>

	</tbody></table>
	<?php //} ?>
	</div>    
</div>    
<?php } ?>
<?php } ?>



</body>
</html>