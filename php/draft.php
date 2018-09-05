<?php
include('credentials.php');


// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$sql = "
select draftYear, draftRound, draftPick, 
selectingTeam, player, playerPosition, ifnull(round(points,1),0) as points, 
ifnull(round(points,1),0) - replacePoints as pointsOverReplace,  keeper
 from (
 select a.draftYear as draftYear, 
      a.draftRound as draftRound, 
a.draftPick as draftPick,
a.selectingTeam as selectingTeam,
a.player as player,
a.playerPosition as playerPosition,
a.playerTeam,
if(b.draftYear is null,'N','Y') as keeper,
preRank,
least(ifnull(preRank,999),a.draftPick +
(select count(*)
from (
select draftYear, draftPick, player,
keeperPick, preRank,
@row := if(@label = concat(draftYear,'-',draftPick),
@row + 1,draftPick) as addPick,
@label := concat(draftYear,'-',draftPick)
from (
select d.draftYear, d.draftPick, d.player,
e.draftPick as keeperPick, preRank
from la_liga_data.draftData d
join 
(select e.*, preRank from la_liga_data.keepers e
join scrapped_data.preRanks f on preYear = e.draftYear and prePlayer = e.player and prePosition = position) e
on e.draftYear = d.draftyear and e.draftPick > d.draftPick
order by d.draftYear, d.draftPick, preRank) d
join (select @row := 0, @label := cast('' as char)) t) d
where addPick >= preRank and d.draftYear = a.draftYear and d.draftPick = a.draftPick
)) as actualPick


from la_liga_data.draftData a

left join la_liga_data.keepers b on a.draftYear = b.draftYear and a.player = b.player and a.playerPosition = b.position
left join scrapped_data.preRanks c on preYear = b.draftYear and prePlayer = b.player and prePosition = position
where a.draftYear between 2010 and 2017
 
 ) draftData
left join (select statYear, statPlayer, statPosition, 
sum(totalPoints) as points 
from scrapped_data.playerStats 
where statWeek < 17
group by 1,2,3) b on draftYear = statYear and player = statPlayer  
	and playerPosition = statPosition 
join analysis.replacementValue on 
replaceYear = draftYear and replacePosition = 
playerPosition 
left join analysis.expectedAdvanced on 
adYear = draftYear and adPLayer = player and adPosition = playerPosition 
order by draftYear, draftRound, draftPick
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