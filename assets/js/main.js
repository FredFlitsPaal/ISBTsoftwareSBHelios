$(document).ready(function() {
   // put all your jQuery goodness in here.
   
    //load main pages
    $('div.navigation li').click(function() {
    	$('div.navigation li').removeClass('active');
    	$(this).addClass('active');
    	
        anchor = $(this).find('a:first').attr('href');
        servePage(anchor.substring(1, anchor.length));
    });
    
    //if no page defined, load tournament page
    if(!window.location.hash){
        servePage('match-scores');
    } else {
    	$('div.navigation li').removeClass('active');
    	$('div.navigation li a[href=' + window.location.hash + ']').parent().addClass('active');
        servePage(window.location.hash.substring(1));
    }
    
    //Make table wider and remove menu's in carrousels
	$('div.carrousels').ajaxComplete(function() {
		//Make table wide and remove side-menu
	    $('div.carrousels .span2').hide();
	    $('div.carrousels .span10').addClass('span12');
	    $('div.carrousels .span12').removeClass('span10');
	    
	    //Remove alert
	    $('div.content .alert').hide();
	    //Remove carrousels button
	    $('div.header .carrousels-button').hide();
	    //Remove comments in table
	    $('.carrousels-hide').hide();
	});
    
	function getAnchor()
	{
		if(!window.location.hash)
			anchor = "match-scores";
		else
			anchor = window.location.hash.substring(1);
		
		
		return anchor;
	}
	
	$(".pause-button").live('click', function()
	{
		var anchor = getAnchor();
		var data = [
			{"name" : "match-id", "value" : $(this).data("match")},
			{"name" : "action", "value" : "pause-match"}
		];
		
		servePageWithData(anchor, data);
	});
	
	$(".play-button").live('click', function()
	{
		var anchor = getAnchor();
		var data = [
			{"name" : "match-id", "value" : $(this).data("match")},
			{"name" : "action", "value" : "play-match"}
		];
		
		servePageWithData(anchor, data);
	});
	
	$("form").live('submit', function()
	{
		var anchor = getAnchor();

		servePageWithData(anchor, $(this).serializeArray());
		
		// Fix to hide the grey overlay
		$(".modal-backdrop").hide();
		
		return false;
	});
	
	// Focus first input & submit matchscores modal form with enter key
	$(document).on("shown", ".matchresults", function()
	{
		var modalId = $(this).attr("id");

		// Focus on first input in matchscores modal
		$("#" + modalId).find(".focushere").focus();

		// Submit matchscores modal form with enter key
		$(this).find("input").keypress(function (e) {
			if (e.which == 13) {
				$("#" + modalId + " form").submit();
				return false;
			}
		});
		
		// Input fields max 2 integers, if 1 int add 0
		$(this).find("input").attr("maxlength", "2");
		$(this).find("input").change(function() {
			var data = $(this).val();
			
			if($.isNumeric(data)) {
				if(data.length == 1) {
					data = 0 + data;
				}
				$(this).val(data);
			} else {
				$(this).val('');
			}
		});
	});

	// Show over modal to addShow
	$(document).on("click", "button[data-toggle=over-modal]", function(e) {
		e.preventDefault();
		
		var newModalId = $(this).attr("data-target");
		var oldModalId = $(this).parents(".modal").attr("id");
		
		// Show over-modal
		$(newModalId).modal("show");
		$("#" + oldModalId).modal("hide");
	});
});

//call for page
function servePageWithData(anchor, data)
{
	if(typeof data == "undefined")
	{
		data = [];
	}
	data.push({"name" : "page", "value" : anchor});
	
	$.post('pages/servePage.php', data, function(data) {
		if(data.error == true){
			//define some error specific stuff here later...
			$('.viewport').html(data.html);
		}else{
			$('.viewport').html(data.html);
		}
	}, "json");
}

function servePage(anchor){
	servePageWithData(anchor, []);
}