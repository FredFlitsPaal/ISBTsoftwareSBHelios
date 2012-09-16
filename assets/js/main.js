    $(document).ready(function() {
       // put all your jQuery goodness in here.
       
        $('div.sidebar-nav li').click(function() {
            anchor = $(this).find('a:first').attr('href');
            servePage(anchor.substring(1, anchor.length));
        });
        
        if(!window.location.hash){
            servePage('tournament');
        }
        
        $("div#content-tab a").live("click", function(){
            anchor = $(this).attr('href');
            servePage(anchor.substring(1, anchor.length));
        });
    });

    function servePage(anchor){    
        $.post('index.php', { page: anchor},
            function(data) {
                if(data.error == true){
                    $('.content').html(data.html);
                }else{
                    $('.content').html(data.html);
                }
        }, "json");
    }
    
    /*
    function display404(){
        //do cool stuff
        alert('dusss....');
    }
    */