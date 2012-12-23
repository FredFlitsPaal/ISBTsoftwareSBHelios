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
/*
	$(".matchresults").live('shown', function()
	{
		modal = this.id;
		//console.log($('input:text:visible:first', this));
		//console.log(this.id);
		//$('#' + modal + ' .focushere').focus();
		$('#' + modal + ' .focushere').css('background-color', 'red');
		//$('input:text:visible:first', this).css('background-color', 'red');
	});
*/
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