    $(document).ready(function() {
       // put all your jQuery goodness in here.
       
        $('li').click(function() {
            anchor = $(this).find('a:first').attr('href');
            servePage(anchor.substring(1, anchor.length));
        });
    });

    function servePage(anchor){    
        $.post('index.php', { page: anchor},
            function(data) {
                if(data.error == true){
                    $('#container').html(data.html);
                }else{
                    $('#container').html(data.html);
                }
        }, "json");
    }
    
    /*
    function display404(){
        //do cool stuff
        alert('dusss....');
    }
    */