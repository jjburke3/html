<?php
include('credentials.php');


// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$sql = "
select team, round(sum((ifnull(points,0) - replacePoints) - pickValue*
	((select max(week) from la_liga_data.pointsScored where draftYear = season and week < 17)/16)
),0) as keeperValue,
ifnull(round(sum(case when draftYear = (select max(draftYear) from la_liga_data.keepers) then
	(ifnull(points,0) - replacePoints) - pickValue*
	((select max(week) from la_liga_data.pointsScored where draftYear = season and week < 17)/16) END),0),0) as currentYearValue,
substring_index(group_concat(concat(draftYear,'_','Round: ',draftRound,', Pick: ',draftPick,'_',replace(player,'_',\"'\"),'_',round(ifnull(points,0),0))
	order by (ifnull(points,0) - replacePoints) - pickValue desc separator '|'),'|',1) as bestKeeper, 
substring_index(group_concat(concat(draftYear,'_','Round: ',draftRound,', Pick: ',draftPick,'_',replace(player,'_',\"'\"),'_',round(ifnull(points,0),0))
	 order by (ifnull(points,0) - replacePoints) - pickValue asc separator '|'),'|',1) as worstKeeper
from la_liga_data.keepers
left join refData.pickValue pick1 on draftPick = pick1.pickNumber
left join (select statYear, statPlayer, statPosition, sum(totalPoints) as points
from scrapped_data.playerStats
where statWeek < 17
group by 1,2,3) b on statYear = draftYear and statPlayer = player and statPosition = position
left join analysis.replacementValue on replaceYear = draftYear and replacePosition = position
where draftYear <= 2018
group by 1
order by 2 desc;


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