<html>

<head>

</head>
<body>
<?php

//include carbon API for date time in working space

require_once "vendor/autoload.php";
use Carbon\Carbon;

$servername = "localhost";
$database_name="dates";
$username = "pharmacy";
$password = "pharmacy";

try{
// Create connection
$conn = new PDO("mysql:host=$servername;dbname=$database_name",$username,$password);
//set the PDO error mode to exception
$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

echo "connected successfully";
}catch(PDOException $e)
{
	echo "connection failed ". $e->getMessage();
}
//get drug code and drug values from the get request
$drug_code=$_POST['drug_code']; 
$quantity=$_POST['quantity']; 

//make a prepared for insterting a row to damaged table
$insert_to_damaged=$conn->prepare("INSERT INTO  damaged (drug_code,quantity,expiry_date)VALUES(:drug_coded,:drug_quantityd,:drug_expiryd)");

  $insert_to_damaged->bindParam(':drug_coded', $drug_coded);
  $insert_to_damaged->bindParam(':drug_quantityd', $drug_quantityd);
  $insert_to_damaged->bindParam(':drug_expiryd', $drug_expiryd);

//sql statement for returning a row from store table
$sql='SELECT * FROM store WHERE drug_code=?';
/*make and execute a prepared for returning a row from store 
table*/
$store_row=$conn->prepare($sql);
$store_row->execute([$drug_code]);

//iterate through each row of the store table
while($row=$store_row->fetch(PDO::FETCH_ASSOC))
{
	$drug_quantity=$row['quantity'];
	$drug1_expiry=$row['expiry_date'];
	$drug_expiry=strtotime($drug1_expiry);
	echo "<br>". "n loop"."<br>";
	$today=strtotime(carbon::now()->format('Y-m-d'));
		/*echo "<br>" . $now . "<br>";
	echo "<br> boolean: " . $drug_expiry<carbon::now()->format('Y-m-d') . "<br>";
	echo $drug_quantity ."<br>";
	echo $drug_expiry . "<br>";*/
	
	/* if the drug has expired or the batch has 0 quantity transfer the details
	to damaged table and delete it from the store table*/
	if($drug_expiry<$today or $drug_quantity=== 0)
	{
		echo "true";
		$drug_coded=$drug_code;
$drug_quantityd=$drug_quantity;
$drug_expiryd=$drug1_expiry;
		$insert_to_damaged->execute();
		echo "<br>" . $drug1_expiry . "<br>";
		$conn->exec("DELETE FROM store WHERE expiry_date='" . $drug1_expiry ."'");
	}

}
//query for returning expiry dates for drug
$sqlmindate='SELECT MIN(expiry_date) AS minimum FROM store WHERE drug_code=?';

$get_row=$conn->prepare($sqlmindate);
$get_row->execute([$drug_code]);
$min1_date=$get_row->fetch(PDO::FETCH_ASSOC);
$min_date=$min1_date['minimum'];
 echo"<br>" . "min_date: " . $min_date ."<br>";


//query quantity from store nearest date from now
$sql1='SELECT quantity FROM store WHERE expiry_date=?';

$get_row=$conn->prepare($sql1);
$get_row->execute([$min_date]);
$store1_quantity=$get_row->fetch(PDO::FETCH_ASSOC);
$store_quantity=$store1_quantity['quantity'];
echo "store quantity: " . $store_quantity ."<br>";

/*query quantity from the specified date from pharmacy
and add requested quantity to it*/
$sql2='SELECT quantity FROM pharmacy WHERE expiry_date=?';

$get_row=$conn->prepare($sql2);
$get_row->execute([$min_date]);
$pharmacy1_quantity=$get_row->fetch(PDO::FETCH_ASSOC);
$pharmacy_quantity=$pharmacy1_quantity['quantity'];
$new_quantity=$quantity+$pharmacy_quantity;
echo   "new_quantity: " . $new_quantity ."<br>";
echo  "quantity: " .$quantity ."<br>";
echo "pharmacy quantity: " . $pharmacy_quantity ."<br>";



//transfer from store to pharmacy
$store_quantity=$store_quantity-$quantity;

//make a prepared for  updating a row in pharmacy table
$update_in_pharmacy=$conn->prepare("UPDATE pharmacy SET quantity=" . $new_quantity . " WHERE expiry_date='" .$min_date ."'");
$update_in_pharmacy->execute();

//update store
$update_in_store=$conn->prepare("UPDATE store SET quantity=" . $store_quantity . " WHERE expiry_date='" .$min_date ."'");
$update_in_store->execute();




/*
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";$servername = "localhost";
$username = "pharmacy";
$password = "pharmacy";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
$sql = "SELECT expiry_date FROM store"
$dates = $conn->query($sql);
$minddate=min($dates);
$today= Carbon::today();


while($mindate<$today and !(empty( $dates )))
{
if (($key = array_search('$mindate', $dates)) !== false) {
    unset($dates[$key]);
}
$selected_date=min($dates);
echo $selected_date;

}

//$_GET["drug_code"]; 
//$_GET["quantity"]; 

$result = mysql_query("SHOW COLUMNS FROM `table` LIKE 'fieldname'");
$exists = (mysql_num_rows($result))?TRUE:FALSE;

*/
?>
</body>
</html>


