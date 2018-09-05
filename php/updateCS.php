<?php
include('credentials.php');


// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$newValue = $_POST['newValue'];
$player = $_POST['player'];
$position = $_POST['position'];

$sql = "
update draft.availplayers 
 set selected = '".$newValue."'
 where player = '".$player."'
 and position = '".$position."';


";
$result = mysqli_query($conn,$sql);

$data = array();

echo '[]';

/*
if(mysqli_num_rows($result) > 0) {
		// output data of each row
		while($row = mysqli_fetch_assoc($result)) {
			echo "PROGRAM: " . $row["PROGRAM"]. " - QUOTES: " . $row["QUOTES"]. "<br>";
		}
	} else {
	echo "0 results";
}


*/

mysqli_close($conn);
?>