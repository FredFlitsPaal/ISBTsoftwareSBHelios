$(document).ready(function() {
	// Reload columns
	$(".isbt-logo").click(function() {
		startRefresh();
	});
	
	$(".current-matches").click(function() {
		reloadCurrentMatches();
	});

	$(".upcoming-matches").click(function() {
		reloadUpcomingMatches();
/* 		servePage("upcoming-matches"); */
	});

	$(".match-scores").click(function() {
		reloadMatchScores();
	});

/*
	$("div").click(function () {
		$(".slide-down").slideDown("slow");
		$(".slide-down").addClass("highlight");
		setTimeout(function() {
			$(".slide-down").removeClass("highlight");
		}, 3000)
	});
*/
});

//Call for page
function servePageWithData(anchor, data)
{
	if(typeof data == "undefined")
	{
		data = [];
	}
	data.push({"name" : "page", "value" : "caroussel_" + anchor});

	$.post('pages/servePage.php', data, function(data) {
		if(data.error == true){
			//define some error specific stuff here later...
			$('header .messages').html("<div class=\"alert alert-error\" style=\"display: none;\"><strong>Oh snap! </strong> Something went wrong with fetching new results.</div>");
			$("header .messages .alert").fadeIn();
		}else{
			$("." + data.column + " .match-data").prepend(data.html);
				
			$(".slide-down").slideDown("slow");
			$("." + data.column + " .match-data > div:gt(" + ( maxAllowed-1 ) + ")" ).slideUp("slow", function() {
				$(this).remove();
			});

			$(".slide-down").addClass("highlight").removeClass("slide-down");
			setTimeout(function() {
				$(".highlight").removeClass("highlight");
			}, 30000)
			
			$("header .messages .alert").fadeOut(function() {
				$(this).remove();
			});
		}
	}, "json");
}

function servePage(anchor){
	servePageWithData(anchor, []);
}

var maxAllowed = 10;

function startRefresh(set) {
	refreshTime = 10000;
	setTimeout(startRefresh, refreshTime);
	
	reloadCurrentMatches();
	reloadUpcomingMatches();
	reloadMatchScores();
}

function reloadCurrentMatches() {
	latestStartTime = '';
	if($(".current-matches .match-data div")[0]) {
		latestStartTime = $(".current-matches .match-data div:first-child").attr("id").substr(10);
	}

	data = [];
	data.push({"name" : "latestStartTime", "value" : latestStartTime});
	data.push({"name" : "maxAllowed", "value" : maxAllowed});

	servePageWithData("current-matches", data);
}

function reloadUpcomingMatches() {
	lastUpcomingMatchId = '';
	if($(".upcoming-matches .match-data div")[0]) {
		lastUpcomingMatchId = $(".upcoming-matches .match-data div:first-child").attr("id").substr(8);
	}

	data = [];
	data.push({"name" : "lastUpcomingMatchId", "value" : lastUpcomingMatchId});
	data.push({"name" : "maxAllowed", "value" : maxAllowed});

	servePageWithData("upcoming-matches", data);
}

function reloadMatchScores() {
	latestEndTime = '';
	if($(".match-scores .match-data div")[0]) {
		latestEndTime = $(".match-scores .match-data div:first-child").attr("id").substr(8);
	}

	data = [];
	data.push({"name" : "latestEndTime", "value" : latestEndTime});
	data.push({"name" : "maxAllowed", "value" : maxAllowed});

	servePageWithData("match-scores", data);
}