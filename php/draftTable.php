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
case when k.id is null then 'N' else 'Y' end as keeper,
ifnull(redraft,'Undrafted') as redraft,
ifnull(round(points,0),0) as points,
ifnull(absRedraft,99999) as absRedraft,
netPoints from la_liga_data.draftData d
left join la_liga_data.keepers k on 
d.draftYear = k.draftYear
and d.selectingTeam = k.team
and d.draftRound = k.draftRound
and d.player = k.player
left join (select statYear, statPlayer, statPosition, netPoints, points,reDraft as absRedraft,
case when round(1+(redraft-mod(reDraft,14))/14,0) <= 15 or statYear <= 2013 and round(1+(redraft-mod(reDraft,14))/14,0) <= 16 then
concat('Round:',round(1+(redraft-mod(reDraft-1,14))/14,0),' Pick:',1+mod(reDraft-1,14))
else 'Undrafted' end
 as redraft
from (
select b.*,
@row := if(@label = statYear, @row + 1,1) as reDraft,
@label := statYear
from (
select statYear, statPlayer, statPosition, 
ifnull(points,0) - replacePoints as netPoints, ifnull(points,0) as points from
(select statYear, statPlayer, statPosition, sum(totalPoints) as points
from scrapped_data.playerStats
where statWeek < 17
group by 1,2,3) b
left join analysis.replacementValue on replaceYear = statYear and replacePosition = statPosition
where statYear between 2011 and 2018
order by statYear, points - replacePoints desc) b
join (select @row := 0, @label := 0) t) b) b on statYear = d.draftYear and statPlayer = d.player and statPosition = playerPosition


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