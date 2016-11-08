<?php 

error_reporting(0);

include('BS_process_sample_gift.php');
$status="New";
//if(isset($_POST['selStatus'])){
//$status = $_POST['selStatus'];
$username='AWSWS'; 
$password='AW$W$U$1';
$wsdl='https://www.co-store.com/admin/BrightStoresOrderData.asmx?WSDL';
$startDate = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-1 month" ) );
$param = array(
	"userName" =>$username,
	"password" => $password,
	//"status" => $status,
	"startDate" => $startDate,
	"endDate" => date('Y-m-d'),
	"PIN" => '1'
);
//print_r($param);
 
function search($array, $key, $value)
{
    $results = array();
    search_r($array, $key, $value, $results);
    return $results;
}

function search_r($array, $key, $value, &$results)
{
    if (!is_array($array)) {
        return;
    }

    if (isset($array[$key]) && $array[$key] == $value) {
        $results[] = $array;
    }

    foreach ($array as $subarray) {
        search_r($subarray, $key, $value, $results);
    }
}


$response="";
try {
$client = new SoapClient($wsdl);
$response = $client->GetDistributorOrdersByDateRange($param);
} catch (SoapFault $e) {
   // echo "<pre>SoapFault: ".print_r($e, true)."</pre>\n";
	//echo "Failed to load";
	
   // echo "<pre>faultcode: '".$e->faultcode."'</pre>";
    //echo "<pre>faultstring: '".$e->getMessage()."'</pre>";
	$erromsg = "Failed to load";
	die();
}

$resultr = json_decode(json_encode($response), true);
$xmldata = isset($resultr['GetDistributorOrdersByDateRangeResult']) ? $resultr['GetDistributorOrdersByDateRangeResult'] : "";
//$xmldata = htmlspecialchars_decode($xmldata);
$xmldata = str_replace("Â® "," ",$xmldata);
	
//print "<pre>";
//print_r($xmldata);
//print "</pre>";

set_time_limit(0);

$xml = simplexml_load_string($xmldata);

$stores="";
$storearray = isset($xml->Store) ? $xml->Store : "";
if(!empty($storearray)){
foreach($storearray as $store ){
	$zStoreID = json_decode(json_encode($store->zStoreID),true);
	$zStoreName = json_decode(json_encode($store->zStoreName),true);
	$zCompanyName = json_decode(json_encode($store->zCompanyName),true);
	$zDefaultUserGroupID = json_decode(json_encode($store->zDefaultUserGroupID),true);
	$zFromEmail = json_decode(json_encode($store->zFromEmail),true);
	$zExclusiveBillingState = json_decode(json_encode($store->zExclusiveBillingState),true);
	$zStoreLevel = json_decode(json_encode($store->zStoreLevel),true);
	$zAccessCSM = json_decode(json_encode($store->zAccessCSM),true);
	$zExtended2Features = json_decode(json_encode($store->zExtended2Features),true);
	$zDefaultShippingMethod = json_decode(json_encode($store->zDefaultShippingMethod),true);
	
	$stores[@$zStoreID[0]] = array(
			"zStoreID"=>@$zStoreID[0], 
			"zStoreName"=>@$zStoreName[0], 
			"zCompanyName"=>@$zCompanyName[0], 
			"zDefaultUserGroupID"=>@$zDefaultUserGroupID[0], 
			"zFromEmail"=>@$zFromEmail[0], 
			"zExclusiveBillingState"=>@$zExclusiveBillingState[0], 
			"zStoreLevel"=>@$zStoreLevel[0], 
			"zAccessCSM"=>@$zAccessCSM[0], 
			"zExtended2Features"=>@$zExtended2Features[0], 
			"zDefaultShippingMethod"=>@$zDefaultShippingMethod[0]
	);
}
}

$users="";
$usersarray = isset($xml->User) ? $xml->User : "";
if(!empty($usersarray)){
foreach($usersarray as $user ){
	$zUserID = json_decode(json_encode($user->zUserID),true);
	$zUserGroupID = json_decode(json_encode($user->zUserGroupID),true);
	$zUsername = json_decode(json_encode($user->zUsername),true);
	$zEmail = json_decode(json_encode($user->zEmail),true);
	$zFirstName = json_decode(json_encode($user->zFirstName),true);
	$zLastName = json_decode(json_encode($user->zLastName),true);
	$zBudgetEnabled = json_decode(json_encode($user->zBudgetEnabled),true);
		
	$users[$zUserID[0]] = array(
			"zUserID"=>@$zUserID[0], 
			"zUserGroupID"=>@$zUserGroupID[0], 
			"zUsername"=>@$zUsername[0], 
			"zEmail"=>@$zEmail[0], 
			"zFirstName"=>@$zFirstName[0], 
			"zLastName"=>@$zLastName[0], 
			"zBudgetEnabled"=>@$zBudgetEnabled[0]
	);
}
}


$usersgroupa="";
$usergrouparray = isset($xml->UserGroup) ? $xml->UserGroup : "";
if(!empty($usergrouparray)){
foreach($usergrouparray as $usersgroup ){
	$zUserGroupID = json_decode(json_encode($usersgroup->zUserGroupID),true);
	$zStoreID = json_decode(json_encode($usersgroup->zStoreID),true);
	$zName = json_decode(json_encode($usersgroup->zName),true);
	$zEmployeeIDRequired = json_decode(json_encode($usersgroup->zEmployeeIDRequired),true);
		
	$usersgroupa[$zUserGroupID[0]] = array(
			"zUserGroupID"=>@$zUserGroupID[0],
			"zStoreID"=>@$zStoreID[0], 
			"zName"=>@$zName[0], 
			"zEmployeeIDRequired"=>@$zEmployeeIDRequired[0]
	);
}
}

$orderline="";
$orderlinearray = isset($xml->OrderLine) ? $xml->OrderLine : "";
if(!empty($orderlinearray)){
foreach($orderlinearray as $orderlin){
	$xml2xml = $orderlin->zDescription;
	
	//$xml2xml1 = htmlentities($xml2xml);
	//$xml2xml1 = htmlentities($xml2xml, ENT_QUOTES, "UTF-8");
	$xml2xml1 = htmlspecialchars_decode($xml2xml);
	$xml2xml2 = str_replace('<?xml version="1.0" encoding="utf-16"?>','',$xml2xml1);
	//$xml2xml2 = str_replace('&lt;?xml version=&quot;1.0&quot; encoding=&quot;utf-16&quot;?&gt;','',$xml2xml2);
	//$xml2xml3 = htmlspecialchars_decode($xml2xml2);
	//$xml2xml3 = htmlentities($xml2xml2, ENT_QUOTES, "UTF-8");
	
	//print_r($xml2xml3);
	$xml2 = json_decode(json_encode(simplexml_load_string($xml2xml2)),true);
	$zOrderLineID = json_decode(json_encode($orderlin->zOrderLineID),true);
	$zOrderID = json_decode(json_encode($orderlin->zOrderID),true);
	$zQuantity = json_decode(json_encode($orderlin->zQuantity),true);
	$zUnitPrice = json_decode(json_encode($orderlin->zUnitPrice),true);
	$zSize = json_decode(json_encode($orderlin->zSize),true);
	$zSubsidy = json_decode(json_encode($orderlin->zSubsidy),true);
	$zStartupCost = json_decode(json_encode($orderlin->zStartupCost),true);
	
	$COGS = json_decode(json_encode($orderlin->COGS),true);
	$SizeSku = json_decode(json_encode($orderlin->SizeSku),true);
	$ColorSku = json_decode(json_encode($orderlin->ColorSku),true);
	$InternalId = json_decode(json_encode($orderlin->InternalId),true);
	
	$orderline[]=array(
			"zOrderLineID"=>@$zOrderLineID[0], 
			"zOrderID"=>@$zOrderID[0], 
			"zQuantity"=>@$zQuantity[0], 
			"zUnitPrice"=>@$zUnitPrice[0], 
			"zSize"=>@$zSize[0], 
			"zSubsidy"=>@$zSubsidy[0], 
			"zStartupCost"=>@$zStartupCost[0], 
			"COGS"=>@$COGS[0],
			"SizeSku"=>@$SizeSku[0], 
			"ColorSku"=>@$ColorSku[0], 
			"InternalId"=>@$InternalId[0], 		
			"zDescription"=>$xml2
		);
	
}
}
else{
	echo "No new orders.";
}

$singleorderarray="";

$orders="";
$ordersarray = isset($xml->Order) ? $xml->Order : "";
if(!empty($ordersarray)){
foreach($ordersarray as $order){
	$xml2xml = $order->zPaymentData;
	
	$xml2xml1 = htmlspecialchars_decode($xml2xml);
	$xml2xml2 = str_replace('<?xml version="1.0" encoding="utf-16"?>','',$xml2xml1);
	$xml2 = json_decode(json_encode(simplexml_load_string($xml2xml2)),true);
	
	
	$zOrderID = json_decode(json_encode($order->zOrderID),true);
	$zUserID = json_decode(json_encode($order->zUserID),true);
	$zOrderStatus = json_decode(json_encode($order->zOrderStatus),true);
	$zShippingMethodType = json_decode(json_encode($order->zShippingMethodType),true);
	$zShippingFee = json_decode(json_encode($order->zShippingFee),true);
	$zHandlingFee = json_decode(json_encode($order->zHandlingFee),true);
	$zSalesTax = json_decode(json_encode($order->zSalesTax),true);
	$zOrderTotal = json_decode(json_encode($order->zOrderTotal),true);
	$zBillingName = json_decode(json_encode($order->zBillingName),true);
	$zBillingCompany = json_decode(json_encode($order->zBillingCompany),true);
	$zBillingStreet1 = json_decode(json_encode($order->zBillingStreet1),true);
	$zBillingCity = json_decode(json_encode($order->zBillingCity),true);
	$zBillingState = json_decode(json_encode($order->zBillingState),true);
	$zBillingZip = json_decode(json_encode($order->zBillingZip),true);
	$zBillingCountry = json_decode(json_encode($order->zBillingCountry),true);
	$zBillingPhone = json_decode(json_encode($order->zBillingPhone),true);
	$zBillingEmail = json_decode(json_encode($order->zBillingEmail),true);
	$zShippingName = json_decode(json_encode($order->zShippingName),true);
	$zShippingCompany = json_decode(json_encode($order->zShippingCompany),true);
	$zShippingStreet1 = json_decode(json_encode($order->zShippingStreet1),true);
	$zShippingCity = json_decode(json_encode($order->zShippingCity),true);
	$zShippingState = json_decode(json_encode($order->zShippingState),true);
	$zShippingZip = json_decode(json_encode($order->zShippingZip),true);
	$zShippingCountry = json_decode(json_encode($order->zShippingCountry),true);
	$zShippingPhone = json_decode(json_encode($order->zShippingPhone),true);
	$zShippingEmail = json_decode(json_encode($order->zShippingEmail),true);
	$zLastTouched = json_decode(json_encode($order->zLastTouched),true);
	$zLastUpdated = json_decode(json_encode($order->zLastUpdated),true);
	$zOrderDate = json_decode(json_encode($order->zOrderDate),true);
	$zNotes  = json_decode(json_encode($order->zNotes),true);
	$zName = json_decode(json_encode($order->zName),true);
	$zDiscountOverride = json_decode(json_encode($order->zDiscountOverride),true);
	$zDonation = json_decode(json_encode($order->zDonation),true);
	//$zCouponCode  = json_decode(json_encode($order->zCouponCode),true);
	$zPurchaseIntent  = json_decode(json_encode($order->zNotes->zCouponCode->zPurchaseIntent),true);

	/*$orders[]=array(
			'zOrderID'=>$zOrderID
			);*/
	if(@$zOrderStatus[0] == "2"){
	$orders[$zOrderID[0]]=array(
			'zOrderID'=>@$zOrderID[0],
			'zUserID'=>@$zUserID[0],
			'zOrderStatus'=>@$zOrderStatus[0],
			'zShippingMethodType'=>@$zShippingMethodType[0],
			'zShippingFee'=>@$zShippingFee[0],
			'zHandlingFee'=>@$zHandlingFee[0],
			'zSalesTax'=>@$zSalesTax[0],
			'zOrderTotal'=>@$zOrderTotal[0],
			'zBillingName'=>@$zBillingName[0],
			'zBillingCompany'=>@$zBillingCompany[0],
			'zBillingStreet1'=>@$zBillingStreet1[0],
			'zBillingCity'=>@$zBillingCity[0],
			'zBillingState'=>@$zBillingState[0],
			'zBillingZip'=>@$zBillingZip[0],
			'zBillingCountry'=>@$zBillingCountry[0],
			'zBillingPhone'=>@$zBillingPhone[0],
			'zBillingEmail'=>@$zBillingEmail[0],
			'zShippingName'=>@$zShippingName[0],
			'zShippingCompany'=>@$zShippingCompany[0],
			'zShippingStreet1'=>@$zShippingStreet1[0],
			'zShippingCity'=>@$zShippingCity[0],
			'zShippingState'=>@$zShippingState[0],
			'zShippingZip'=>@$zShippingZip[0],
			'zShippingCountry'=>@$zShippingCountry[0],
			'zShippingPhone'=>@$zShippingPhone[0],
			'zShippingEmail'=>@$zShippingEmail[0],
			'zLastTouched'=>@$zLastTouched[0],
			'zLastUpdated'=>@$zLastUpdated[0],
			'zOrderDate'=>@$zOrderDate[0],
			'zNotes'=>@$zNotes[0],
			'zName'=>@$zName,
			'zDiscountOverride'=>@$zDiscountOverride,
			'zDonation'=>@$zDonation,
			'zCouponCode '=>@$zCouponCode,
			'zPurchaseIntent '=>@$zPurchaseIntent,
			'zPaymentData' => $xml2
		);
		
		$orderlinearray = search($orderline, 'zOrderID', $zOrderID[0]);
		//$orderusearray = search($users, 'zUserID', $zUserID[0]);
		//print_r($users[$zUserID[0]]['zUserGroupID']);
		//print_r($usersgroupa[$users[$zUserID[0]]['zUserGroupID']]['zStoreID']);
		//$orderusegrouparray = search($usersgroupa, 'zUserGroupID', $users[$zUserID[0]]['zUserGroupID']);
		//print_r($stores[@$orderusegrouparray[0]['zStoreID']]['zStoreID']);
		$singleorderarray=array($orders[$zOrderID[0]], $orderlinearray, $stores[@$usersgroupa[$users[$zUserID[0]]['zUserGroupID']]['zStoreID']], $users[$zUserID[0]]);
		$singleorder = new ProcessOrder($singleorderarray);
		//$singleorder($singleorderarray);
		//print_r($singleorder);
	}
	set_time_limit(0);
}
}



//print "<pre>";
//print_r($orders);
//print_r(search($orderline, 'zOrderID', '171680'));
//print "</pre>";



//}
die();
?>