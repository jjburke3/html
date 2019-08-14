<?php
include('credentials.php');
$dbname = "draft";


// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$sql = "

select averageRank, player, position, team, lastYearTeams,
lastYearPoints, age, experience, bye,
chartRanking,
case when keepPlayer is not null then 'K' else '' end as selected,
averageADP, averagePosRank,
espnRank, calcADP, ffDataRank,
ffDataADP, ffDataPosRank,
ffaRank, ffaPosRank, ffaECR, ffaPosECR, projProjected
 from draft.availPlayers a
left join draft.allRankings b on rankPlayer = player
	and rankPosition = position
left join draft.keepers on keepPlayer = player and keepPosition = position
left join draft.depthChart on chartSeason = 2018
	and replace(chartPlayer,'.','') = replace(player,'.','') and chartPosition = position
left join draft.projections on projPlayer = player
	and projPosition = position
order by ifnull(averageRank,999999) asc;



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