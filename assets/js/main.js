$(document).ready(function() {
   // put all your jQuery goodness in here.
   
    //load main pages
    $('div.sidebar-nav li').click(function() {
        anchor = $(this).find('a:first').attr('href');
        servePage(anchor.substring(1, anchor.length));
    });
    
    //if no page defined, load tournament page
    if(!window.location.hash){
        servePage('tournament');
    }
    
    //load subpage
    $("div#content-tab a").live("click", function(){
        anchor = $(this).attr('href');
        servePage(anchor.substring(1, anchor.length));
    });
});

//call for page
function servePage(anchor){    
    $.post('pages/servePage.php', { page: anchor},
        function(data) {
            if(data.error == true){
                //define some error specific stuff here later...
                $('.content').html(data.html);
            }else{
                $('.content').html(data.html);
            }
    }, "json");
}