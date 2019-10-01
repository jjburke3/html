<?php
include('credentials.php');


// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$player = str_replace('-',' ',$_GET['player']);
$position = $_GET['pos'];


$sql = "
select b.*,
winWin, playoffs,
winOpp,
coalesce(concat(
case when k.id is not null then 'Keeper ' else '' end,
'Round ',d.draftRound,', Pick ',d.draftPick,' by ',selectingTeam),'') as draftPlace

from (
select
player, substring_index(group_concat(distinct(playerId)),',',1) as playerId,
case when count(case when team != 'FA' then 1 end) > 0 then
group_concat(distinct(case when team != 'FA' then team end))
else group_concat(distinct(team)) end as team,
season, week, 
case when count(case when team = 'FA' then 1 end) > 0 then
group_concat(distinct(case when team = 'FA' then playerTeam end))
else 
group_concat(distinct(playerTeam)) end as playerTeam, 
playerPosition, 
case when count(case when team != 'FA' then 1 end) > 0 then
group_concat(distinct(case when team != 'FA' then slot end))
else group_concat(distinct(slot)) end as bench,
sum(points) as scoredPoints,
sum(otherPoints) as faPoints
from (
select player, playerId, team, season, week, playerTeam, playerPosition, 
case when playerSlot in ('Bench','IR') then 'Bench' else '' end as slot,
points, null as otherPoints

from la_liga_data.pointsScored
where player = '".$player."'
	and playerPosition = '".$position."'
union all
select statPlayer,null as playerId, 'FA' as team, statYear, statWeek, statTeam, statPosition,
'' as slot, null as points, totalPoints as otherPoints
from scrapped_data.playerStats
where statYear >= 2010 and statWeek <= 16
and statPlayer = '".$player."'
	and statPosition = '".$position."') b
group by player, season, week, playerPosition
order by player, playerPosition, season, week) b
left join la_liga_data.wins on winSeason = season and winWeek = week and winTeam = team
left join la_liga_data.draftData d on draftYear = season and d.player = b.player and d.playerPosition = b.playerPosition
left join la_liga_data.keepers k on k.draftYear = season and k.player = b.player
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