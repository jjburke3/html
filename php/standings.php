<?php
include('credentials.php');


// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$sql = "
select
standTeam as Team,
wins as Wins,
losses as Losses,
tie as Tie,
pointsScored as 'Points Scored',
round(pointsScored/(wins+losses+tie),2) as 'Average Points',
round(exPointAverage/(13-(wins+losses+tie)),2) as 'Expected Remaining Point Average',
round(exWins,2) as 'Expected Remaining Wins',
round(playoffsOdds,2) as 'Playoffs Odds',
round(lowpoints,2) as 'Lowest Points Odds',
round(highpoints,2) as 'Highest Points Odds',
round(champOdds,2) as 'Champ Odds'
from analysis.standings
order by playoffsOdds desc,
champOdds desc
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