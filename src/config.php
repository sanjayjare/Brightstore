<?php 
include('PDO class.php');
$username = "oq_dev_user";
$password = "sajdflj97welr@Sad";
$hostname = "54.70.10.27"; 
$Database = "invReq_dev";
$SSL = false;
$CA_PATH = '/etc/mysql/ssl/';
$Host2='54.70.10.27';
        /**
         * Mysql connection
         */


/*$link = mysql_connect($hostname,$username, $password,false,MYSQL_CLIENT_SSL) 
        or die(mysql_error());
$selected = mysql_select_db("invReq_dev",$link) 
  or die("Could not select invReq_dev");*/
  


try {
   // $dbh = new PDO('mysql:host=$hostname;dbname=invReq_dev', $username, $password);
	$db1 = new PDO_db($Database, $username, $password, $hostname, $Utf8='utf8mb4', $SSL, $CA_PATH, $Host2);
    $db = $db1->PDO($Database, $username, $password, $hostname, $Utf8='utf8mb4', $SSL, $CA_PATH, $Host2);
	/*foreach($db->query('SELECT * from Companies') as $row) {
        print_r($row);
    }
    $dbh = null;*/
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
?>

