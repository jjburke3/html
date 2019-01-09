<?php
include('credentials.php');


// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$sql = "
select @row := @row + 1 as rank,
case when statPosition = 'QB' then @qb := @qb + 1
when statPosition = 'RB' then @rb := @rb + 1
when statPosition = 'WR' then @wr := @wr + 1
when statPosition = 'TE' then @te := @te + 1
when statPosition = 'D/ST' then @d := @d + 1
when statPosition = 'K' then @k := @k + 1
end as positionRank,
b.*,
teamPlayed as mostUsedBy
from (
select statPlayer, statPosition, 
group_concat(distinct(statTeam) order by statTeam asc) as teams,
concat(min(statYear),' to ',max(statYear)) as years,
round(sum(totalPoints),0) as points

from scrapped_data.playerStats
where statYear >= 2010
group by 1,2
order by sum(totalPoints) desc) b
left join (select @row := 0, @qb := 0, @rb := 0, @wr := 0, @te := 0, @d := 0, @k := 0) t on 1 = 1 
left join (
select player, playerPosition,
substring_index(group_concat(concat(team,' Played: ',playCount,' Points: ',round(playedPoints,0)) order by playCount desc separator '|'),'|',1) as teamPlayed
from (
select player, playerPosition, team, sum(points) as playedPoints, count(distinct(concat(season,'=',week))) as playCount
from la_liga_data.pointsScored
where playerSlot not in ('Bench','IR')
group by player, playerPosition, team) b
group by player, playerPosition
	) play on player = statPlayer and playerPosition = statPosition ;
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