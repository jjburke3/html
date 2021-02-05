<?php
include('../credentials.php');


// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$position = $_GET['position'];

if($position == 'all')
	$posString = " ('QB','RB','WR','TE','D/ST','K') ";
else if($position == 'Off')
	$posString = " ('QB','RB','WR','TE') ";
else if($position == 'flex')
	$posString = " ('RB','WR','TE') ";
else
	$posString = " ('".$position."') ";



$sql = "
select @row := @row + 1 as Rank,
case when statPosition = 'QB' then @qb := @qb + 1
when statPosition = 'RB' then @rb := @rb + 1
when statPosition = 'WR' then @wr := @wr + 1
when statPosition = 'TE' then @te := @te + 1
when statPosition = 'D/ST' then @d := @d + 1
when statPosition = 'K' then @k := @k + 1
end as 'Position Rank',
uniqueTeams as 'Unique Teams',
statPlayer as playerId,
playerName as Player,
statPosition as Position,
teams as Teams,
years as Years,
points as Points,
wins as 'Wins',
round((wins+ties*.5)/(wins+losses+ties),2) as 'Win %',
losses,
ties,
champs as Championships,
teamPlayed as 'Most Used By'
from (
select 
statPlayer, 
statPosition, 
group_concat(distinct(teamAbbrv) order by statTeam asc) as teams,
concat(min(statYear),' to ',max(statYear)) as years,
round(sum(totalPoints),0) as points

from scrapped_data2.playerStats
left join refData.nflTeams on statTeam = teamId
where statYear >= 2010
	and statPosition in  ".$posString."
group by 1,2
order by sum(totalPoints) desc) b
left join (select @row := 0, @qb := 0, @rb := 0, @wr := 0, @te := 0, @d := 0, @k := 0) t on 1 = 1 
left join (
select player, playerPosition,
count(distinct(team)) as uniqueTeams,
sum(wins) as wins,
sum(losses) as losses,
sum(ties) as ties,
sum(champs) as champs,
substring_index(group_concat(concat(team,' Played: ',playCount,' Points: ',round(playedPoints,0)) order by playCount desc separator '|'),'|',1) as teamPlayed
from (
select playerId as player, playerPosition, playerTeam as team, 
count(case when winWin = 1 then 1 end) as wins,
count(case when winLoss = 1 then 1 end) as losses,
count(case when winTie = 1 then 1 end) as ties,
count(case when winWin = 1 and playoffs = 'championship' then 1 end) as champs,
sum(case when playerSlot not in ('Bench','IR') then playerPoints end) as playedPoints, 
count(distinct(case when playerSlot not in ('Bench','IR') then concat(playerSeason,'=',playerWeek) end)) as playCount
from la_liga_data.playerPoints
join la_liga_data.wins on winSeason = playerSeason and winWeek = playerWeek and playerTeam = winTeam
where playerPosition in  ".$posString."
group by player, playerPosition, playerTeam) b
group by player, playerPosition
	) play on player = statPlayer
join refData.playerIds on statPlayer = playerId
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