<?php
include('credentials.php');


// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$sql = "
select d.draftYear,
d.draftRound,
d.draftPick,
d.selectingTeam,
ifnull(nullif(d.player,''),k.player) as player,
ifnull(nullif(d.playerPosition,''),k.position) as playerPosition,
case when k.id is null then 'N' else 'Y' end as keeper from la_liga_data.draftData d
left join la_liga_data.keepers k on 
d.draftYear = k.draftYear
and d.selectingTeam = k.team
and d.draftRound = k.draftRound
and d.draftPick = k.draftPick

where d.draftYear > 2010


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