<?php
include('credentials.php');


// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$sql = "
select 'best' as type, winTeam, winOpp, max(winStreak) as winStreak,
substring_index(group_concat(concat(winSeason,'-',winWeek) order by winStreak desc separator '|'),'|',1) as endWeek,
substring_index(group_concat(concat(winSeason,'-',winWeek) order by winStreak desc separator '|'),'|',-1) as startWeek

from (
select a.winSeason, a.winWeek, a.winTeam,a.winOpp,
ceil(length(substring_index(group_concat(b.winWin order by b.winSeason desc, b.winWeek desc),'0',1))/2)  as winStreak
from 
(select a.winSeason, a.winWeek, a.winTeam, a.winOpp, max(a.winWin) as winWin
from
la_liga_data.wins a
left join la_liga_data.wins c on c.winTeam = a.winTeam and a.winOpp = c.winOpp and
	(c.winSeason * 100 + c.winWeek) > (a.winSeason * 100 + a.winWeek)
    and (c.playoffs is not null or c.winWeek <= 13)
    where (a.playoffs is not null or a.winWeek <= 13) and a.winWin = 1
    group by 1,2,3,4
    having ifnull(group_concat(c.winWin order by c.winSeason asc, c.winWeek asc),'') not like '1%'
) a
join la_liga_data.wins b on b.winTeam = a.winTeam and 
	 b.winOpp = a.winOpp and
(b.winSeason * 100 + b.winWeek) <= (a.winSeason * 100 + a.winWeek)
	and (b.playoffs is not null or b.winWeek <= 13)


group by 1,2,3,4
order by 5 desc


) b

group by 2,3

union all


select 'active', winTeam, winOpp,
ceil(length(substring_index(group_concat(winWin order by winSeason desc, winWeek desc),'0',1))/2) as activeStreak,
null as endWeek,
null as startWeek
from la_liga_data.wins
where winWeek <= 13 or playoffs is not null
group by 2,3
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