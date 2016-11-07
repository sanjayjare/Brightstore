<?php
/**
 * urls for wsdl access to Bright Stores
 * https://www.co-store.com/admin/BrightStoresOrderData.asmx
 * https://www.co-store.com/admin/BrightStoresAdministration.asmx
 * https://storedemo.mybrightsites.com/apipie/1.0/orders/index.html
 * https://storedemo.mybrightsites.com/apipie/1.0/orders/show.html
 * http://www.promostandards.org/service/view/1/
 */


/**
 * Process  order example
 *
 * Simple wrapper to place order on AP and in OQ
 *
 * @category    Class
 * @author      Stuart Zurcher
 */
class ProcessOrder {
	
	/**
	 * Variables for AP order
	 */
	private $order = array();				// order array
	private $transaction_no;                // unique time + rand
	private $order_array = array(); 					// Array of information for sql insert
	private $customer_PO;						// titos' order incremment id also used as the PO#
	private $SoldTo;						// customer who bought the product from BS
	private $SoldBy;						// $stores['store id']
	private $stores = array();				// obtained from /home/AWS/bssrvc/stores_id.txt
	public $order_id;						//AP order id
	public $queue_id;
	public $error=0;						// error handling
	public $error_message;
	
	
	
	 /* 
	  * param array $order   wsdl order
	  * @return boolean -- successful process or not
	 */
	 
	 
		
	public function __construct($order) {
		//creating order array
	/*	echo "<pre>";
		print_r($order);
		echo "</pre>";*/
		
		$stores=$order[2];
		$userdetail=$order[3];
		$order_id = $order[0]['zOrderID'];
		$order_PaymentData = $order[0]['zPaymentData'];		
		
		/*echo "<pre>";
		print_r($order_PaymentData);
		echo "</pre>";*/
		
		$oitem = "";
		foreach($order[1] as $orderline){
			$oitem[] = array(
					"sku" => @$orderline['zDescription']['@attributes']['itemNumber'],
					"name" => @$orderline['zDescription']['@attributes']['productName'],
					"qty_ordered" => @$orderline['zQuantity'],
					"price" => @$orderline['zUnitPrice'],
					"Size" => @$orderline['zSize'],
					"Subsidy" => @$orderline['zSubsidy'],
					"StartupCost" => @$orderline['zStartupCost'],
					"COGS" => @$orderline['COGS'],
					"SizeSku" => @$orderline['SizeSku'],
					"ColorSku" => @$orderline['ColorSku'],
					"InternalId" => @$orderline['InternalId'],
					"productId" => @$orderline['zDescription']['@attributes']['productId'],
					"quantityMin" => @$orderline['zDescription']['@attributes']['quantityMin'],
					"colorName" => @$orderline['zDescription']['@attributes']['colorName'],
					"colorId" => @$orderline['zDescription']['@attributes']['colorId'],
					"sizeId" => @$orderline['zDescription']['@attributes']['sizeId'],
					"personalization" => @$orderline['zDescription']['@attributes']['personalization'],
					"Logo" => @$orderline['zDescription']['logo']['@attributes']
				);
		}
		//print_r($oitem);
		$ogift_message = "";
		//print_r($order[0]['zPaymentData']['GiftCertificateData']);
		if(isset($order[0]['zPaymentData']['GiftCertificateData'])){
			if(isset($order[0]['zPaymentData']['GiftCertificateData']['@attributes'])){
				$gs = array($order[0]['zPaymentData']['GiftCertificateData']);
			}
			else{
				$gs = $order[0]['zPaymentData']['GiftCertificateData'];
			}
			
		foreach($gs as $GiftCertificateData){
			//$ogift_message.= $GiftCertificateData['@attributes']['Notes'].", ";
			//print_r($GiftCertificateData);
			$ogift_message[] = array(
					"Id" => $GiftCertificateData['@attributes']['Id'],
					"Amount" => $GiftCertificateData['@attributes']['Amount'],
					"Notes" => $GiftCertificateData['@attributes']['Notes']
				);
			}
		}
		
		$PurchaseOData="";
		if(isset($order[0]['zPaymentData']['PurchaseOrderData'])){
			if(isset($order[0]['zPaymentData']['PurchaseOrderData']['@attributes'])){
				$podata = array($order[0]['zPaymentData']['PurchaseOrderData']);
			}
			else{
				$podata = $order[0]['zPaymentData']['PurchaseOrderData'];
			}
			
		foreach($podata as $PurchaseOrderData){
			//$ogift_message.= $GiftCertificateData['@attributes']['Notes'].", ";
			//print_r($GiftCertificateData);
			$PurchaseOData[] = array(
					"po_number" => $PurchaseOrderData['@attributes']['Code'],
					"Amount" => $PurchaseOrderData['@attributes']['Amount']
				);
			}
		}
		/*echo "<pre>";
		print_r($order_PaymentData);
		echo "</pre>";*/
		
		/**Payment array**/
		$paymentdataarray="";
		foreach($order[0]['zPaymentData'] as $key=>$pamentarray){
			if($key == 'PurchaseOrderData'){
				$paymentdataarray[]=array("Paymentdetail"=>array("PurchaseOrderData","Purchase Order",$pamentarray['@attributes']));
			}
			if($key == 'GiftCertificateData'){
				$paymentdataarray[]=array("Paymentdetail"=>array("GiftCertificateData","Gift Certificate",$pamentarray['@attributes']));
			}
			if($key == 'CreditCardData'){
				$paymentdataarray[]=array("Paymentdetail"=>array("CreditCardData","Credit Card",$pamentarray['@attributes']));
			}
			if($key == 'CreditCardResult'){
				$paymentdataarray[]=array("Paymentdetail"=>array("CreditCardResult","Credit Card",$pamentarray['@attributes']));
			}
			if($key == 'GLCodeData'){
				$paymentdataarray[]=array("Paymentdetail"=>array("GLCodeData","GL Code",$pamentarray['@attributes']));
			}
			if($key == 'InternalCostCenterData'){
				$paymentdataarray[]=array("Paymentdetail"=>array("InternalCostCenterData","Internal Cost Center",$pamentarray['@attributes']));
			}
			if($key == 'PaymentMethod4Data'){
				$paymentdataarray[]=array("Paymentdetail"=>array("PaymentMethod4Data","Pay by Check",$pamentarray['@attributes']));
			}
			if($key == 'PaymentMethod5Data'){
				$paymentdataarray[]=array("Paymentdetail"=>array("PaymentMethod5Data","Department",$pamentarray['@attributes']));
			}
			if($key == 'PaymentMethod6Data'){
				$paymentdataarray[]=array("Paymentdetail"=>array("PaymentMethod6Data","Payroll Deduction",$pamentarray['@attributes']));
			}
			if($key == 'PaymentMethod7Data'){
				$paymentdataarray[]=array("Paymentdetail"=>array("PaymentMethod7Data","Activity / Event",$pamentarray['@attributes']));
			}
			if($key == 'PaymentMethod8Data'){
				$paymentdataarray[]=array("Paymentdetail"=>array("PaymentMethod8Data","Other Payment Method",$pamentarray['@attributes']));
			}
			if($key == 'PaymentMethod9Data'){
				$paymentdataarray[]=array("Paymentdetail"=>array("PaymentMethod9Data","No Payment Required",$pamentarray['@attributes']));
			}
			if($key == 'PaymentAllocationData'){
				$paymentdataarray[]=array("Paymentdetail"=>array("PaymentAllocationData","Payment Allocation",$pamentarray['@attributes']));
			}
			if($key == 'CouponData'){
				$paymentdataarray[]=array("Paymentdetail"=>array("CouponData","Coupon",$pamentarray['@attributes']));
			}
		}
		
		$customer_PO = isset($PurchaseOData[0]['po_number']) ? $PurchaseOData[0]['po_number'] : "";
		
		$zShippingName = explode(" ", $order[0]['zShippingName']);
		$zShippingfirstname = isset($zShippingName[0]) ? $zShippingName[0]:"";
		$zShippinglastname = isset($zShippingName[1]) ? $zShippingName[1]:"";
		
		$zBillingName = explode(" ", $order[0]['zBillingName']);
		$zBillingfirstname = isset($zBillingName[0]) ? $zBillingName[0]:"";
		$zBillinglastname = isset($zBillingName[1]) ? $zBillingName[1]:"";
		
		$order = array(
			"increment_id" => $customer_PO,
			"status_history" => array(
				"status" => @$order[0]['zOrderStatus'],
				"created_at" => @$order[0]['zOrderDate'],
				"comment" => @$order[0]['zNotes']
			),
			"payment" => array(
				"po_number" => $customer_PO
			),
			"shipping_address" => array(
				"firstname" => @$zShippingfirstname,
				"lastname" => @$zShippinglastname,
				"street" => @$order[0]['zShippingStreet1'],
				"city" => @$order[0]['zShippingCity'],
				"region" => @$order[0]['zShippingState'],
				"postcode" => @$order[0]['zShippingZip'],
				"country_id" => @$order[0]['zShippingCountry'],
				"email" => @$order[0]['zShippingEmail'],
				"telephone" => @$order[0]['zShippingPhone'],
				"shipping_description" => ''
			),
			"billing_address" => array(
				"firstname" => @$zBillingfirstname,
				"lastname" => @$zBillinglastname,
				"street" => @$order[0]['zBillingStreet1'],
				"city" => @$order[0]['zBillingCity'],
				"region" => @$order[0]['zBillingState'],
				"postcode" => @$order[0]['zBillingZip'],
				"country_id" => @$order[0]['zBillingCountry'],
				"email" => @$order[0]['zBillingEmail'],
				"telephone" => @$order[0]['zBillingPhone']
			),
			"items" => $oitem,
			"ShippingFee" => @$order[0]['zShippingFee'],
			"HandlingFee" => @$order[0]['zHandlingFee'],
			"SalesTax" => @$order[0]['zSalesTax'],
			"OrderTotal" => @$order[0]['zOrderTotal'],
			"gift_message" => @$ogift_message
		);
		

		
		//print_r(get_stores());
		//$this->stores = get_stores();    // need to create array from /home/AWS/bssrvc/stores_id.txt
		$this->stores = @$stores;
		$this->order = @$order;
		$this->SoldTo = @$userdetail;
		//print_r($stores);
		//$this->SoldTo = getSoldTo($order['increment_id']); //user id      //  $stores[$order[''store_id']']['sold_to']  **may change
		$this->SoldBy = @$stores[0]['zStoreID']; //store id		//  $stores[$order[''store_id']']['sold_by']  **may change
		//print_r($this->SoldBy);
		$this->queue_id = 102;
		$this->customer_PO =  @$order ['increment_id'];  //use store order number as PO#
		$this->transaction_no = date ( 'Ymd-His' ) . rand ( 1, 9 );
		$payment = @$order["status_history"];
		$dt = @$payment["created_at"];
		if(isset($dt)){
			$dt = date('Y-m-d H:i:s', strtotime($dt));
		}
		$cust_order_date = $dt;
		// remove extra comments
		$auth_trans_id = empty($payment['comment'])?'':str_replace('authorize - successful.', '', substr($payment['comment'],0,-30))."\n";
		$payment_notes = "{$auth_trans_id}Cust Order date: {$cust_order_date}";
		$cust_po = empty($order["payment"]["po_number"])? false:  $order["payment"]["po_number"];	//  Customer PO from titos put in confidential notes
		if ($cust_po){$payment_notes .= " Customer PO: {$cust_po}";}
		$shipping_description = isset($order["shipping_description"]) ? $order["shipping_description"] : "";
		$shipping_address = "{$order['shipping_address']['firstname']} {$order['shipping_address']['lastname']}\n{$order['shipping_address']['street']}\n{$order['shipping_address']['city']}, {$order['shipping_address']['region']} {$order['shipping_address']['postcode']} {$order['shipping_address']['country_id']}\n\n{$order['shipping_address']['email']}\n{$order['shipping_address']['telephone']}\n{$shipping_description}
    ";
		/*if ($this->order['ShippingFee'] > 0) {
			$shipping_address .= 'Amount: '.$this->order['ShippingFee'];
		}*/
		$billing_address = "{$order['billing_address']['firstname']} {$order['billing_address']['lastname']}\n{$order['billing_address']['street']}\n{$order['billing_address']['city']}, {$order['billing_address']['region']} {$order['billing_address']['postcode']} {$order['billing_address']['country_id']}\n\n{$order['billing_address']['email']}\n{$order['billing_address']['telephone']}
    ";
	
	$billing_address .= 'Sales Tax: '.$this->order['SalesTax'];
	 
		
		//$gift_message = isset ( $order ['gift_message'] ) ? mysql_real_escape_string ( $order ['gift_message'] ) : '';
		$gift_message = isset ( $order ['gift_message'] ) ?  $order ['gift_message']  : '';
			
		$this->order_array = array (
				'GoingDirect' => 'No',
				'OrderType' => 'Other',
				'OrderClassification' => 'regular',
				'Trans_No' => $this->transaction_no,
				'SoldTo' => $this->SoldTo, 
				'SoldBy' => $this->SoldBy,
				'Order_Date' => date ( 'Y-m-d H:i:s' ), //date('Y-m-d H:i:s', strtotime($order['status_history']['created_at'])),
				'NoShipments' => 1,
				'order_status' => 'faxed',
				'SpecialShipInst' =>$shipping_address,
				'SpecialBillInst' => $billing_address,
				'confidential_notes' => $payment_notes,
				'MiscInstructions' => $order['gift_message'],
				'CheckSpecialBilling' => 'Yes',
				'CheckSpecialShipping' => 'Yes',
				'PONumber' => $cust_po, //customer po
				'PurchaseOrderNo' => $this->customer_PO,  // store order#
				/*'ShippingFee' => $order['ShippingFee'],
				'HandlingFee' => $order['HandlingFee'],
				'SalesTax' => $order['SalesTax'],*/
				'OrderTotal' => $order['OrderTotal'],
				'Order_Payment' => $paymentdataarray
		);

		// process line items
		foreach ( $this->order ['items'] as $item ) {
			$item_number = $item ['sku'];
			$item_description = $item ['name'];
			$qty = intval ( $item ['qty_ordered'] );
			$price = $item ['price'];
				
			$line_item_array = array (
					//'order_id' => $this->order_id,
					'order_id' => $order_id,					
					'Trans_No' => $this->transaction_no,
					'supplier_id' => 2326, // vernon
					'decorator_id' => 0,
					'Qty' => $qty,
					'ItemNumber' => $item_number,
					'ItemDescription' => $item_description,
					'Product_Price' => $price,
					
					/*'ItemNumber' => mysql_real_escape_string ( $item_number ),
					'ItemDescription' => mysql_real_escape_string ( $item_description ),
					'Product_Price' => mysql_real_escape_string ( $price )*/
					"Size" => $item['Size'],
					"Subsidy" => $item['Subsidy'],
					"StartupCost" => $item['StartupCost'],
					"COGS" => $item['COGS'],
					"SizeSku" => $item['SizeSku'],
					"ColorSku" => $item['ColorSku'],
					"InternalId" => $item['InternalId'],
					"productId" => $item['productId'],
					"quantityMin" => $item['quantityMin'],
					"ItemColor" => $item['colorName'],
					"colorId" => $item['colorId'],
					"sizeId" => $item['sizeId'],
					"personalization" => $item['personalization'],
					"Logo" => $item['Logo']
			);
			$this->order_array['items'][] = $line_item_array;
		}
		// shipping
		//if ($this->order ['ShippingFee'] > 0) {
			$line_item_array = array (
					'order_id' => $order_id,
					'Trans_No' => $this->transaction_no,
					'supplier_id' => 2326,
					'decorator_id' => 0,
					'Qty' => 1,
					'ItemNumber' => 'FREIGHT',
					'ItemDescription' => 'Shipping Amount',
					'Product_Price' => $this->order['ShippingFee']
				);
			$this->order_array['items'][] = $line_item_array;
		//}
		
		// handling
		//if ($this->order ['HandlingFee'] > 0) {
			$line_item_array = array (
					'order_id' => $order_id,
					'Trans_No' => $this->transaction_no,
					'supplier_id' => 2326,
					'decorator_id' => 0,
					'Qty' => 1,
					'ItemNumber' => 'Handling',
					'ItemDescription' => 'Handling Amount',
					'Product_Price' => $this->order['HandlingFee']
				);
			$this->order_array['items'][] = $line_item_array;
		//}
		
		// sales tax
		//if ($this->order ['SalesTax'] > 0) {
			/*$line_item_array = array (
					'order_id' => $order_id,
					'Trans_No' => $this->transaction_no,
					'supplier_id' => 2326,
					'decorator_id' => 0,
					'Qty' => 1,
					'ItemNumber' => 'Sales Tax',
					'ItemDescription' => 'Sales Tax Amount',
					'Product_Price' => $this->order['SalesTax']
				);
			$this->order_array['items'][] = $line_item_array;*/
		//}
		
		echo "<br><br>Order array:<br>";
		echo "<pre>";
		//print_r($order);
		print_r($this->order_array);
		echo "</pre>";
		// save $this->order_array as jsonencoded string in "/home/AWS/bssrvc/orders/".$this->customer_PO.".txt"
		//acknowledge($this->customer_PO);   // set order status id 60 'In Production'.  See http://www.promostandards.org/service/view/1/
	}
	
	
}