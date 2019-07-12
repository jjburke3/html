<?php
include('credentials.php');


// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$team = $_GET['team'];

$year = $_GET['year'];
$week = $_GET['week'];

if ($team == 'all') {
	$team = '%';
}
$sql = "
select 
playerPosition,
ifnull(sum(case when team like '".$team."' then points end)
/count(distinct(case when team like '".$team."' then concat(season,week,team) end)),0)
-
ifnull(sum(points)
/count(distinct(concat(season,week,team))),0) as points


from la_liga_data.pointsScored
join la_liga_data.wins on winSeason = season and winWeek = week
	and winTeam = team
    and (winWeek < 14 or playoffs is not null)
where season between ".$year."
    and week between ".$week."
    and vsTeam != ''
	and playerSlot != 'Bench'
    and concat(season,'-',week) in
    (select distinct concat(winSeason,'-',winWeek)
		from la_liga_data.wins 
		where winTeam like '".$team."'
		and (winWeek < 14 or playoffs is not null) ) 
group by playerPosition
order by 1
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