$('body').append( "<div class='topnav' id = 'navBar'></div ")
$('#navBar').append( "<a class='active' href='./'>La Liga Port Lodge</a>" )
$('#navBar').append( "<a href='points.html'>Points and Wins</a>" )
$('#navBar').append( "<a href='playoffs.html'>Regular Season and Playoff Results</a>" )
$('#navBar').append( "<div class = 'dropdown2'>" +
					 "<button class='dropbtn'>Draft Info" +
					 "<i class='fa fa-caret-down'></i>" +
					 "</button> "+
					 "<div class='dropdown-content' " +
					 "id = 'draftDiv'</div>" +
					 "</div>")
	$('#draftDiv').append( "<a href='keepers.html'>Keepers</a>")
	$('#draftDiv').append( "<a href='draft.html'>Draft Chart</a>")
$('#navBar').append( "<div class = 'dropdown2'>" +
					 "<button class='dropbtn'>Other Projects" +
					 "<i class='fa fa-caret-down'></i>" +
					 "</button> "+
					 "<div class='dropdown-content' " +
					 "id = 'otherDiv'</div>" +
					 "</div>")
	$('#otherDiv').append( "<a href='node_link.html'>Link-Node Graph</a>")
	$('#otherDiv').append( "<a href='point_totals.html'>Points/Wins Graph</a>")

$('#navBar').append( "<div class = 'dropdown2'>" +
					 "<button class='dropbtn'>Download Data" +
					 "<i class='fa fa-caret-down'></i>" +
					 "</button> "+
					 "<div class='dropdown-content' " +
					 "id = 'dataDiv'</div>" +
					 "</div>")
	$('#dataDiv').append( "<a href='php/pointsScoredCSV.php'>Points Data</a>")
	$('#dataDiv').append( "<a href='php/winsCSV.php'>Matchup Data</a>")
	$('#dataDiv').append( "<a href='php/draftCSV.php'>Draft Data</a>")
	$('#dataDiv').append( "<a href='php/keeperCSV.php'>Keeper Data</a>")
$('#navBar').append( "<a href='http://games.espn.com/ffl/leagueoffice?leagueId=729310&seasonId=2018' target='_blank'>League Page</a>" )
/*  <a class='active' href='#home'>Home</a>
  <a href='#news'>News</a>
  <a href='#contact'>Contact</a>
  <a href='#about'>About</a>
</div>" )*/
