<?php
include('../credentials.php');


// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$sql = "
select a.draftYear, a.draftRound, a.draftPick, 
selectingTeam, playerName as player, playerPosition, ifnull(round(points,1),0) as points, 
ifnull(round(points,1),0) - replacePoints as pointsOverReplace,  case when b.player is not null then 'Y' else 'N' end as keeper,
(select max(week) from la_liga_data.pointsScored where season = a.draftYear) as maxWeek
from la_liga_data.draftedPlayerData a
left join la_liga_data.keepers b on a.draftYear = b.draftYear and a.player = b.player
left join (select statYear, statPlayer, sum(totalPoints) as points
from scrapped_data2.playerStats
where statWeek <= 16
group by 1,2) b on statYear = a.draftYear and statPlayer = a.player
join analysis.replacementValue on 
replaceYear = a.draftYear and replacePosition = 
playerPosition 
join refData.playerIds on a.player = playerId
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