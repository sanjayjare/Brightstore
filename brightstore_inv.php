<?php 
error_reporting(0);
function checkinventory($distributorName,$storeName,$categoryName,$productName,	$sizeName, $sizeSku,$colorName, $colorSku){
	$invvalue='';
	if($distributorName != ""){
		$key='5BF47727E25C4E4013632E9B80A9472DFA8D2C48A4910B5C3ED87B99D1803ADEF38298228BDC94FA07E2703ED06C240E42E75FC561B184C3B047D80BBE9A552B44639043BD537B2A58B3288141E0D228980B6F94350CB8ABDED3F48F236C0B2E';
		
		
		//echo $distributorName."/".$storeName."/".$categoryName."/".$productName."/".$sizeName."/".$sizeSku."/".$colorName."/".$colorSku;
		$inventoryQuantity=1;
		$inventoryTrigger=1;
		$wsdl='https://www.co-store.com/admin/BrightStoresAdministration.asmx?WSDL';
		$param = array(
		"key" =>$key,
		"distributorName" =>$distributorName,
		"storeName" =>$storeName,
		"categoryName" =>$categoryName,
		"productName" =>$productName,
		"sizeName" =>$sizeName,
		"sizeSku" =>$sizeSku,
		"colorName" =>$colorName,
		"colorSku" =>$colorSku,
		"inventoryQuantity" =>$inventoryQuantity,
		"inventoryTrigger" =>$inventoryTrigger
	);
	
	

	$client = new SoapClient($wsdl);

	$response = $client->CheckInventory($param); 

	//$resultr = new stdClass();
	$resultr = json_decode(json_encode($response), true);
	
	$invalarray = isset($resultr['CheckInventoryResult']) ? $resultr['CheckInventoryResult'] : "";
	/*print "<pre>";
	print_r($param);
	print "</pre>";
	echo $invalarray;*/
	if($invalarray != ""){
		$invaln = explode('Current/New:',$invalarray);
		$inval1 = explode('/',$invaln[1]);
		$invvalue = $inval1[0];
	}
	else{
		$invvalue='';
	}
}
	return $invvalue;
}
$cc=array();
$k=0;
//$keys = array_keys($orderline);
$old='';
 //print_r($keys);

	   $file = fopen("F554105.csv","r");
	   while(!feof($file))
         {
            
           $data=fgetcsv($file);
		   $id= $data[2];
		       
				$cc=$data;
				//print_r($cc);
				$invval = checkinventory(trim($cc[0]),trim($cc[1]),'',trim($cc[2]),trim($cc[3]),'',trim($cc[4]),'');
				 //echo $invval;
				 //die();
				$file1= fopen("newfile.csv","a");
			    fputcsv($file1,array($cc[0],$cc[1],$cc[2],$cc[3],$cc[4],$cc[5],$cc[6],$cc[7],$invval));
					 
				
						 
						
			}/*close while Loop */
 fclose($file);

$date= date('Y-m-d');




$file_name="Inventory_".$date.".csv";

rename ("newfile.csv", $file_name);


echo "File Updated Successfully<br>";
//die();

/* Code for send mail with file attachment*/

 $file = "F554105.csv";

 $files = array("F554105.csv",$file_name);
 //$mailto="alpa@powerweave.com";
 $mailto="Chrisl@vernoncompany.com,stuartz@vernoncompany.com";
 
 $from_name="no-reply@powerweave.com";
 $message="Please find Inventory Update for Vernon Demo store(".$date.").";
 $subject="Inventory Update for Vernon Demo store(".$date.").";
     $file_size = filesize($file);
    $handle = fopen($file, "r");
    $content = fread($handle, $file_size);
    fclose($handle);
    $content = chunk_split(base64_encode($content));
	
	 $file_size1 = filesize($file_name);
    $handle1 = fopen($file_name, "r");
    $content1 = fread($handle1, $file_size1);
    fclose($handle1);
    $content1 = chunk_split(base64_encode($content1));
	
	
	
	
    $uid = md5(uniqid(time()));
    $name = basename($file);
    $header = "From: ".$from_name."\r\n";
    $header .= "CC:alpa@powerweave.com\r\n";  
	$header .= "BCC:sanjay.jare@powerweave.com\r\n";     
    $header .= "MIME-Version: 1.0\r\n";
    $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";

    $nmessage = "--".$uid."\r\n";
    $nmessage .= "Content-type:text/plain; charset=iso-8859-1\r\n";
    $nmessage .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $nmessage .= $message."\r\n\r\n";
    $nmessage .= "--".$uid."\r\n";
    $nmessage .= "Content-Type: application/octet-stream; name=\"".$file."\"\r\n"; 
    $nmessage .= "Content-Transfer-Encoding: base64\r\n";
    $nmessage .= "Content-Disposition: attachment; filename=\"".$file."\"\r\n\r\n";
    $nmessage .= $content."\r\n\r\n";
   $nmessage .= "--".$uid."\r\n";
     $nmessage .= "Content-Type: application/octet-stream; name=\"".$file_name."\"\r\n"; 
    $nmessage .= "Content-Transfer-Encoding: base64\r\n";
    $nmessage .= "Content-Disposition: attachment; filename=\"".$file_name."\"\r\n\r\n";
    $nmessage .= $content1."\r\n\r\n";
    $nmessage .= "--".$uid."--";

if (mail($mailto, $subject, $nmessage, $header)) {
        echo "Mail Sent Successfully.<br/>"; // or use booleans here
    } else {
        echo "Mail NOT Sent. <br/>";
    }

die();
?>