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
select chartSeason,
chartWeek, 
case chartTeam
when 'Arizona Cardinals' then 'ARZ'
when 'Atlanta Falcons' then 'ATL'
when 'Baltimore Ravens' then 'BAL'
when 'Buffalo Bills' then 'BUF'
when 'Carolina Panthers' then 'CAR'
when 'Chicago Bears' then 'CHI'
when 'Cincinnati Bengals' then 'CIN'
when 'Cleveland Browns' then 'CLE'
when 'Dallas Cowboys' then 'DAL'
when 'Denver Broncos' then 'DEN'
when 'Detroit Lions' then 'DET'
when 'Green Bay Packers' then 'GB'
when 'Houston Texans' then 'HOU'
when 'Indianapolis Colts' then 'IND'
when 'Jacksonville Jaguars' then 'JAX'
when 'Kansas City Chiefs' then 'KC'
when 'Los Angeles Chargers' then 'LAC'
when 'Los Angeles Rams' then 'LAR'
when 'Miami Dolphins' then 'MIA'
when 'Minnesota Vikings' then 'MIN'
when 'New England Patriots' then 'NE'
when 'New Orleans Saints' then 'NO'
when 'New York Giants' then 'NYG'
when 'New York Jets' then 'NYJ'
when 'Oakland Raiders' then 'OAK'
when 'Philadelphia Eagles' then 'PHI'
when 'Pittsburgh Steelers' then 'PIT'
when 'San Francisco 49ers' then 'SF'
when 'Seattle Seahawks' then 'SEA'
when 'Tampa Bay Buccaneers' then 'TB'
when 'Tennessee Titans' then 'TEN'
when 'Washington Redskins' then 'WAS'
end as chartTeam,
chartPosition,
chartRank,
chartPlayer,
chartRole,
injuryStatus,
thirdDownBack,
goalLine,
kickReturner,
puntReturner



 from scrapped_data.depthCharts where chartSeason = 2019 and chartWeek = 0;



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