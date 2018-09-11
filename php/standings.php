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
round(exPointAverage/(13-(wins+losses+tie)),2) as 'Expected End of Year Point Average',
round(exWins,2) as 'Expected End of Year Wins',
round(playoffsOdds,2) as 'Playoffs Odds',
round(lowpoints,2) as 'Lowest Points Odds',
round(highpoints,2) as 'Highest Points Odds',
round(champOdds,2) as 'Champ Odds'
from analysis.standings
where standWeek = (select max(standWeek) from analysis.standings)
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