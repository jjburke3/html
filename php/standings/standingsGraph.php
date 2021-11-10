<?php
include('../credentials.php');


// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$sql = "
select
case standTeam when 'jj burke' then 'JJ Burke'
when 'chris hammitt' then 'Chris Hammitt'
when 'tom buckley' then 'Tom Buckley'
when 'billy beirne' then 'Billy Beirne'
when 'mike derusso' then 'Mike DeRusso'
when 'mark krizmanich' then 'Mark Krizmanich'
when 'chris curtin' then 'Chris Curtin'
when 'matthew singer' then 'Matthew Singer'
when 'jordan hiller' then 'Jordan Hiller'
when 'joe young' then 'Joe Young'
when 'mike guiltinan' then 'Mike Guiltinan'
when 'parker king' then 'Parker King'
when 'ricky garcia' then 'Ricky Garcia'
when 'andrew lamb' then 'Andrew Lamb'
else
	standTeam end as Team,
standWeek, 
round(standWins/standRunCount,2) as wins, 
round(standLosses/standRunCount,2) as losses, 
round(standPoints/standRunCount,1) as pointsScored, 
round(standPoints/(case when standYear > 2020 then 14 else 13 end*standRunCount),2) as pointAverage, 
round(standPoints/(case when standYear > 2020 then 14 else 13 end*standRunCount),2) as exPointAverage, 
round(standPointsRemain/(standRunCount*least(case when standYear > 2020 then 17 else 16 end,
	case when standYear > 2020 then 18 else 17 end-standWeek)),2) as exRemainPointAverage, 
round(standWins/standRunCount,2) as exWins, 
round(standPlayoffs/standRunCount,2) as playoffsOdds, 
round(standChamp/standRunCount,2) as champOdds, 
round(standHighPoints/standRunCount,2) as highpoints, 
round(standLowPoints/standRunCount,2) as lowPoints, 
round(standFirstPlace/standRunCount,2) as firstplace, 
round(standBye/standRunCount,2) as bye,
round((standChamp/standRunCount)*champPayout +
	(standRunnerUp/standRunCount)*runnerupPayout +
    (standThirdPlace/standRunCount)*thirdPlacePayout +
    (standFirstPlace/standRunCount)*regChampPayout +
    (standHighPoints/standRunCount)*highPointsPayout + 
    (standWeeklyHighPoints/standRunCount)*weeklyHighPointsPayout,2) as exMoney,
standWinsArray as exWinsArray,
standPointsArray as exPointAverageArray
from leagueSims.standings
join (select payYear, 
	avg(case when payRule = '1st Place Finish' then payAmount end) as champPayout, 
	avg(case when payRule = '2nd Place Finish' then payAmount end) as runnerupPayout, 
	avg(case when payRule = '3rd Place Finish' then payAmount end) as thirdPlacePayout, 
	avg(case when payRule = 'Regular Season Champ' then payAmount end) as regChampPayout, 
	avg(case when payRule = 'Regular Season High Points' then payAmount end) as highPointsPayout, 
	avg(case when payRule = 'Weekly High Points' then payAmount end) as weeklyHighPointsPayout
    from la_liga_data.payouts group by 1) b on payYear = standYear
where standYear = ".$_GET['year']."
	and standWeek <= ".$_GET['week']."
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