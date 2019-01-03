<?php
include('credentials.php');


// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$sql = "
select team, 
sum(latitude*case when points > 0 then points else 0 end)/sum(case when points > 0 then points else 0 end) as lat,
sum(longitude*case when points > 0 then points else 0 end)/sum(case when points > 0 then points else 0 end) as longitude
from (
select team, playerTeam,
latitude, longitude,
sum(points) as points from la_liga_data.pointsScored
left join refData.venuelocations on (homeTeam = playerTeam or homeTeam2 = playerTeam)
and season >= yearStart and season <= yearEnd
where season = 2018 and week <= 13
and playerSlot not in ('Bench','IR')
group by team, playerTeam, latitude, longitude) b
group by team

";


$result = mysqli_query($conn,$sql);
$data = array();



for ($x = 0; $x < mysqli_num_rows($result); $x++) {
	$data[] = mysqli_fetch_assoc($result);
}

echo json_encode($data);

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