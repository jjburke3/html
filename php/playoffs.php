<?php
include('credentials.php');


// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$sql = "
select winTeam as Team, 
ifnull(sum(case when playoffs = 'championship' then winWin end),0) as 'Champs',
ifnull(sum(case when playoffs = 'championship' then winLoss end),0) as '2nd Place',
ifnull(sum(case when playoffs = '3rd place' then winWin end),0) as '3rd Place',
ifnull(count(distinct case when playoffs is not null then winSeason end),0) as 'Playoff Apps.',
ifnull(count(case when playoffs is not null and winWin = 1 then winSeason end),0) as 'Playoff Wins',
ifnull(sum(case when winWeek <= 13 then winWin end),0) as 'Regular Season Wins',
round(ifnull(sum(case when playoffs is not null then winPoints end),0),1) as 'Playoff Points',
round(ifnull(sum(case when winWeek <= 13 then winPoints end),0),1) as 'Regular Season Points',
round(ifnull(sum(case when winWeek <= 13 then winPoints end),0)/count(case when winWeek <= 13 then winSeason end),1) as 'Regular Season Points Avg',
count(distinct(highSeason)) as 'Season High Points',
count(distinct(lowSeason)) as 'Season Low Points',
count(distinct(highWeek)) as 'Weekly High Points',
count(distinct(lowWeek)) as 'Weekly Low Points',
substring_index(group_concat(case when winWeek <= 13 or playoffs is not null then
	concat(winPoints,' (',winSeason,' ',
		case when winWeek = 14 then '1st Playoff Round'
        when winWeek = 15 then '2nd Playoff Round'
        when playoffs = '3rd place' then '3rd Place Game'
        when playoffs = 'championship' then 'Championship' else concat('Week ',winWeek) end,')') end order by winPoints desc separator '|'),'|',1) as 'Personal High Score',
substring_index(group_concat(case when winWeek <= 13 or playoffs is not null then
	concat(winPoints,' (',winSeason,' ',
		case when winWeek = 14 then '1st Playoff Round'
        when winWeek = 15 then '2nd Playoff Round'
        when playoffs = '3rd place' then '3rd Place Game'
        when playoffs = 'championship' then 'Championship' else concat('Week ',winWeek) end,')') end order by winPoints asc separator '|'),'|',1) as 'Personal Low Score'

from la_liga_data.wins
left join (
select highSeason, highTeam,
highPoints,
@row := if(@priorP = highPoints,0,1) +
if(@label = highSeason,@row,0) as highestTotal,
@priorP := if(@label = highSeason, highPoints,null),
@label := highSeason
from (
select winSeason as highSeason, winTeam as highTeam,
sum(winPoints) as highPoints
from la_liga_data.wins
where winWeek <= 13
group by 1,2
order by 1, 3 desc) b, (select @row := 0, @label := cast('' as char), @priorP := 0) t) high
on highSeason = winSeason and highTeam = winTeam and highestTotal = 1

left join (
select lowSeason, lowTeam,
lowPoints,
@row := if(@priorP = lowPoints,0,1) +
if(@label = lowSeason,@row,0) as lowestTotal,
@priorP := if(@label = lowSeason, lowPoints,null),
@label := lowSeason
from (
select winSeason as lowSeason, winTeam as lowTeam,
sum(winPoints) as lowPoints
from la_liga_data.wins
where winWeek <= 13
group by 1,2
order by 1, 3 asc) b, (select @row := 0, @label := cast('' as char), @priorP := 0) t) low
on lowSeason = winSeason and lowTeam = winTeam and lowestTotal = 1

left join (
select highWeek, highWeekTeam,
highWeekPoints,
@row := if(@priorP = highWeekPoints,0,1) +
if(@label = highWeek,@row,0) as highestWeekTotal,
@priorP := if(@label = highWeek, highWeekPoints,null),
@label := highWeek
from (
select concat(winSeason,'-',winWeek) as highWeek, winTeam as highWeekTeam,
sum(winPoints) as highWeekPoints
from la_liga_data.wins
where winWeek <= 13
group by 1,2
order by 1, 3 desc) b, (select @row := 0, @label := cast('' as char), @priorP := 0) t) highWeek
on highWeek = concat(winSeason,'-',winWeek) and highWeekTeam = winTeam and highestWeekTotal = 1


left join (
select lowWeek, lowWeekTeam,
lowWeekPoints,
@row := if(@priorP = lowWeekPoints,0,1) +
if(@label = lowWeek,@row,0) as lowestWeekTotal,
@priorP := if(@label = lowWeek, lowWeekPoints,null),
@label := lowWeek
from (
select concat(winSeason,'-',winWeek) as lowWeek, winTeam as lowWeekTeam,
sum(winPoints) as lowWeekPoints
from la_liga_data.wins
where winWeek <= 13
group by 1,2
order by 1, 3 asc) b, (select @row := 0, @label := cast('' as char), @priorP := 0) t) lowWeek
on lowWeek = concat(winSeason,'-',winWeek) and lowWeekTeam = winTeam and lowestWeekTotal = 1

group by 1
order by 2 desc,3 desc,4 desc, 5 desc

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