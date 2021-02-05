<?php
include('credentials.php');


// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$sql = "
select winTeam, winOpp, 
sum(winWin) as wins,
avg(winPoints) as points,
substring_index(group_concat(concat(round(winPoints),' - ',winSeason,', Week ',winWeek) order by winPoints desc separator '|'),'|',1) as maxPoints, 
sum(winLoss) as oppWins,
avg(winPointsAgs) as oppPoints,
substring_index(group_concat(concat(round(winPointsAgs),' - ',winSeason,', Week ',winWeek) order by winPointsAgs desc separator '|'),'|',1) as oppMaxPoints
 from la_liga_data.wins
where playoffs is not null or winWeek <= 13
group by 1,2
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