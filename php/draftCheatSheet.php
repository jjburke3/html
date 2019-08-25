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

select averageRank, a.player, a.position, a.team, teams as lastYearTeams,
priorYear.lastYearPoints, age, experience, bye,
concat(chartRank + 1,'-',chartRole) as chartRanking,
case when k.player is not null then 
concat(k.team,' K-',k.draftRound,'-',k.draftPick)
 else '' end as selected,
a.id,
concat_ws('-',startDate,injInjury,injGameStatus,endDate) as injury
 from draft.availPlayers a
left join draft.allRankings b on replace(replace(replace(replace(replace(replace(rankPlayer,'.',''),' Jr',''),' III',''),' II',''),'Fuller V','Fuller'),'Snead IV','Snead') = replace(replace(replace(replace(replace(replace(replace(a.player,'.',''),\"'\",'_'),' Jr',''),' III',''),' II',''),'Fuller V','Fuller'),'Snead IV','Snead')
	and rankPosition = a.position and rankYear = 2019 and rankWeek = 0
left join la_liga_data.keepers k on k.player = a.player and k.position = a.position and k.draftYear = 2019
left join scrapped_data.depthCharts on chartSeason = 2019 and chartWeek = 0
	and replace(replace(replace(replace(replace(replace(chartPlayer,'.',''),' Jr',''),' III',''),' II',''),'Fuller V','Fuller'),'Snead IV','Snead') = replace(replace(replace(replace(replace(replace(replace(a.player,'.',''),\"'\",'_'),' Jr',''),' III',''),' II',''),'Fuller V','Fuller'),'Snead IV','Snead') and chartPosition = a.position
left join (select statPlayer, statPosition, group_concat(distinct(statTeam)) as teams,
round(sum(totalPoints),0) as lastYearPoints
from scrapped_data.playerstats
where statYear = 2018 and statWeek <= 16
group by statPlayer, statPosition
) priorYear on replace(replace(replace(replace(replace(replace(statPlayer,'.',''),' Jr',''),' III',''),' II',''),'Fuller V','Fuller'),'Snead IV','Snead') = replace(replace(replace(replace(replace(replace(replace(a.player,'.',''),\"'\",'_'),' Jr',''),' III',''),' II',''),'Fuller V','Fuller'),'Snead IV','Snead') and statPosition = a.position
left join scrapped_data.injuries on injSeason = 2019
	and injWeek = 0 
	and replace(replace(replace(replace(replace(replace(injPlayer,'.',''),' Jr',''),' III',''),' II',''),'Fuller V','Fuller'),'Snead IV','Snead') = replace(replace(replace(replace(replace(replace(replace(a.player,'.',''),\"'\",'_'),' Jr',''),' III',''),' II',''),'Fuller V','Fuller'),'Snead IV','Snead') and injPosition = a.position
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