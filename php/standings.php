<?php
include('credentials.php');


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
when 'matthew singer' then 'Matthew singer'
when 'jordan hiller' then 'Jordan Hiller'
when 'joe young' then 'Joe Young'
when 'mike guiltinan' then 'Mike Guiltinan'
when 'parker king' then 'Parker King'
when 'ricky garcia' then 'Ricky Garcia'
when 'andrew lamb' then 'Andrew Lamb'
else
	standTeam end as Team,
wins as Wins,
losses as Losses,
tie as Tie,
pointsScored as 'Points Scored',
round(pointsScored/(wins+losses+tie),2) as 'Average Points',
concat(round(exPointAverage/13,2),' (',round(exPointAverage/13-exPointAverage2/13,2),')') as 'Expected End of Year Point Average',
concat(round(exWins,2),' (',round(exWins-exWins2,2),')') as 'Expected End of Year Wins',
concat(round(playoffsOdds,2),' (',round(playoffsOdds-playoffsOdds2,2),')') as 'Playoffs Odds',
concat(round(lowpoints,2),' (',round(lowpoints-lowpoints2,2),')') as 'Lowest Points Odds',
concat(round(highpoints,2),' (',round(highpoints-highpoints2,2),')') as 'Highest Points Odds',
concat(round(champodds,2),' (',round(champodds-champodds2,2),')') as 'Champ Odds'
from (select standTeam,
substring_index(group_concat(wins order by standWeek desc separator '|'),'|',1) as wins,
substring_index(group_concat(losses order by standWeek desc separator '|'),'|',1) as losses,
substring_index(group_concat(tie order by standWeek desc separator '|'),'|',1) as tie,
substring_index(group_concat(pointsScored order by standWeek desc separator '|'),'|',1) as pointsScored,
substring_index(group_concat(exPointAverage order by standWeek desc separator '|'),'|',1) as exPointAverage,
substring_index(substring_index(group_concat(exPointAverage order by standWeek desc separator '|'),'|',2),'|',-1) as exPointAverage2,
substring_index(group_concat(exWins order by standWeek desc separator '|'),'|',1) as exWins,
substring_index(substring_index(group_concat(exWins order by standWeek desc separator '|'),'|',2),'|',-1) as exWins2,
substring_index(group_concat(playoffsOdds order by standWeek desc separator '|'),'|',1) as playoffsOdds,
substring_index(substring_index(group_concat(playoffsOdds order by standWeek desc separator '|'),'|',2),'|',-1) as playoffsOdds2,
substring_index(group_concat(lowPoints order by standWeek desc separator '|'),'|',1) as lowPoints,
substring_index(substring_index(group_concat(lowPoints order by standWeek desc separator '|'),'|',2),'|',-1) as lowPoints2,
substring_index(group_concat(highPoints order by standWeek desc separator '|'),'|',1) as highPoints,
substring_index(substring_index(group_concat(highPoints order by standWeek desc separator '|'),'|',2),'|',-1) as highPoints2,
substring_index(group_concat(champOdds order by standWeek desc separator '|'),'|',1) as champOdds,
substring_index(substring_index(group_concat(champOdds order by standWeek desc separator '|'),'|',2),'|',-1) as champOdds2
from analysis.standings
where standType = '".$_GET['type']."'
group by 1) b
order by playoffsOdds desc,
champOdds desc
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