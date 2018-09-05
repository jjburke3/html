<?php
include('credentials.php');


// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$sql = "
select season,
week,
team,
vsTeam,
player,
playerTeam,
concat(playerPosition,if(playerPosition2 is not null and playerPosition2 <> 'NULL',concat('/',playerPosition2),'')) as playerPosition,
playerSlot,
points


from la_liga_data.pointsScored
";


$result = mysqli_query($conn,$sql);
$data = array();
$column_names = array();

for ($x = 0; $x < mysqli_num_fields($result); $x++) {
	$column_names[] = mysqli_fetch_field($result)->name;
}

for ($x = 0; $x < mysqli_num_rows($result); $x++) {
	$data[] = mysqli_fetch_assoc($result);
}

function outputCSV($data,$column_names,$file_name = 'file.csv') {
       # output headers so that the file is downloaded rather than displayed
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=$file_name");
        # Disable caching - HTTP 1.1
        header("Cache-Control: no-cache, no-store, must-revalidate");
        # Disable caching - HTTP 1.0
        header("Pragma: no-cache");
        # Disable caching - Proxies
        header("Expires: 0");
    
        # Start the ouput
        $output = fopen("php://output", "w");
		
		fputcsv($output,$column_names);
        
         # Then loop through the rows
        foreach ($data as $row) {
            # Add the rows to the body
            fputcsv($output, $row); // here you can change delimiter/enclosure
        }
        # Close the stream off
        fclose($output);
    }
	
outputCSV($data,$column_names,'pointsScored.csv');

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