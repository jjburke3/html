<?php
include('../credentials.php');


// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$sql = "
select b.Team,
b.Wins,
b.Losses,
concat(b.Points,case when a.Points is not null then concat(' (',b.Points-a.Points,')') else '' end) as Points,
concat(b.pointsAvg,case when a.pointsAvg is not null then concat(' (',b.pointsAvg-a.pointsAvg,')') else '' end) as 'Points Average',
concat(b.exPointsAvg,case when a.exPointsAvg is not null then concat(' (',b.exPointsAvg-a.exPointsAvg,')') else '' end) as 'Ex. Points Average',
concat(b.exWins,case when a.exWins is not null then concat(' (',b.exWins-a.exWins,')') else '' end) as 'Ex. Wins',
concat(b.standPointsRemain,case when a.standPointsRemain is not null then concat(' (',b.standPointsRemain-a.standPointsRemain,')') else '' end) as 'Remaining Exp. Points',
concat(b.playoffsOdds,case when a.playoffsOdds is not null then concat(' (',b.playoffsOdds-a.playoffsOdds,')') else '' end) as 'Playoffs Odds',
concat(b.champOdds,case when a.champOdds is not null then concat(' (',b.champOdds-a.champOdds,')') else '' end) as 'Champ Odds',
concat(b.firstPlaceOdds,case when a.firstPlaceOdds is not null then concat(' (',b.firstPlaceOdds-a.firstPlaceOdds,')') else '' end) as 'First Place Odds',
concat(b.highPointsOdds,case when a.highPointsOdds is not null then concat(' (',b.highPointsOdds-a.highPointsOdds,')') else '' end) as 'High Points Odds',
concat(b.byeOdds,case when a.byeOdds is not null then concat(' (',b.byeOdds-a.byeOdds,')') else '' end) as 'Bye Odds',
concat(b.lowPointsOdds,case when a.lowPointsOdds is not null then concat(' (',b.lowPointsOdds-a.lowPointsOdds,')') else '' end) as 'Low Points Odds',
concat(b.exWeeklyHighPoints,case when a.exWeeklyHighPoints is not null then concat(' (',b.exWeeklyHighPoints-a.exWeeklyHighPoints,')') else '' end) as 'Ex. Weekly High Points',
concat(b.exPayouts,case when a.exPayouts is not null then concat(' (',b.exPayouts-a.exPayouts,')') else '' end) as 'Ex. Payouts'
from (
select standTeam as Team,
winWin as Wins, 
winLoss as Losses, 
winPoints as Points, 
pointsAvg,
round(standPoints/(standRunCount*13),1) as exPointsAvg,
round(standPointsRemain/(standRunCount*(least(16,17-".$_GET['week']."))),1) as standPointsRemain,
round(standWins/standRunCount,2) as exWins,
round(standPlayoffs/standRunCount,2) as playoffsOdds,
round(standChamp/standRunCount,2) as champOdds,
round(standFirstPlace/standRunCount,2) as firstPlaceOdds,
round(standHighPoints/standRunCount,2) as highPointsOdds,
round(standBye/standRunCount,2) as byeOdds,
round(standLowPoints/standRunCount,2) as lowPointsOdds,
round(standWeeklyHighPoints/standRunCount,2) as exWeeklyHighPoints,
round((standChamp/standRunCount)*champPayout +
	(standRunnerUp/standRunCount)*runnerupPayout +
    (standThirdPlace/standRunCount)*thirdPlacePayout +
    (standFirstPlace/standRunCount)*regChampPayout +
    (standHighPoints/standRunCount)*highPointsPayout + 
    (standWeeklyHighPoints/standRunCount)*weeklyHighPointsPayout,2)
    as exPayouts
from leagueSims.standings
join (select payYear, 
	avg(case when payRule = '1st Place Finish' then payAmount end) as champPayout, 
	avg(case when payRule = '2nd Place Finish' then payAmount end) as runnerupPayout, 
	avg(case when payRule = '3rd Place Finish' then payAmount end) as thirdPlacePayout, 
	avg(case when payRule = 'Regular Season Champ' then payAmount end) as regChampPayout, 
	avg(case when payRule = 'Regular Season High Points' then payAmount end) as highPointsPayout, 
	avg(case when payRule = 'Weekly High Points' then payAmount end) as weeklyHighPointsPayout
    from la_liga_data.payouts group by 1) b on payYear = standYear
left join (select winTeam, round(sum(winPoints),1) as winPoints, sum(winWin) as winWin, sum(winLoss) as winLoss,
	round(sum(winPoints)/count(*),1) as pointsAvg
	from la_liga_data.wins 
	where winSeason = ".$_GET['year']." and winWeek < ".$_GET['week']." and winWeek <= 13
	group by 1) a on winTeam = standTeam
where standYear = ".$_GET['year']." and standWeek = ".$_GET['week']."
) b
left join (
select standTeam as Team,
winWin as Wins, 
winLoss as Losses, 
winPoints as Points, 
pointsAvg,
round(standPoints/(13*standRunCount),1) as exPointsAvg,
round(standPointsRemain/(standRunCount*(least(16,18-".$_GET['week']."))),1) as standPointsRemain,
round(standWins/standRunCount,2) as exWins,
round(standPlayoffs/standRunCount,2) as playoffsOdds,
round(standChamp/standRunCount,2) as champOdds,
round(standFirstPlace/standRunCount,2) as firstPlaceOdds,
round(standHighPoints/standRunCount,2) as highPointsOdds,
round(standBye/standRunCount,2) as byeOdds,
round(standLowPoints/standRunCount,2) as lowPointsOdds,
round(standWeeklyHighPoints/standRunCount,2) as exWeeklyHighPoints,
round((standChamp/standRunCount)*champPayout +
	(standRunnerUp/standRunCount)*runnerupPayout +
    (standThirdPlace/standRunCount)*thirdPlacePayout +
    (standFirstPlace/standRunCount)*regChampPayout +
    (standHighPoints/standRunCount)*highPointsPayout + 
    (standWeeklyHighPoints/standRunCount)*weeklyHighPointsPayout,2)
    as exPayouts
from leagueSims.standings
join (select payYear, 
	avg(case when payRule = '1st Place Finish' then payAmount end) as champPayout, 
	avg(case when payRule = '2nd Place Finish' then payAmount end) as runnerupPayout, 
	avg(case when payRule = '3rd Place Finish' then payAmount end) as thirdPlacePayout, 
	avg(case when payRule = 'Regular Season Champ' then payAmount end) as regChampPayout, 
	avg(case when payRule = 'Regular Season High Points' then payAmount end) as highPointsPayout, 
	avg(case when payRule = 'Weekly High Points' then payAmount end) as weeklyHighPointsPayout
    from la_liga_data.payouts group by 1) b on payYear = standYear
left join (select winTeam, round(sum(winPoints),1) as winPoints, sum(winWin) as winWin, sum(winLoss) as winLoss,
	round(sum(winPoints)/count(*),1) as pointsAvg
	from la_liga_data.wins 
	where winSeason = ".$_GET['year']." and winWeek < ".$_GET['week']." - 1 and winWeek <= 13
	group by 1) a on winTeam = standTeam
where standYear = ".$_GET['year']." and standWeek = ".$_GET['week']." - 1
) a on a.Team = b.Team

order by b.playoffsOdds desc, b.champOdds desc;
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