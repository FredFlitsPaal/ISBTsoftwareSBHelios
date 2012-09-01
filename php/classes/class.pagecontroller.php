<?php

class pageController{
    //security, login, etc.. later in dev.
    
    public function page($l_sPage){
        
        switch($l_sPage){
            case "instellingen":
                $l_aNotFound["error"] = false;
                $l_aNotFound["html"] = file_get_contents('pages/instellingen.html');
            break;
            
            case "home":
                $l_aNotFound["error"] = false;
                $l_aNotFound["html"] = file_get_contents('pages/home.html');
            break;
            
            default:
                $l_aNotFound["error"] = true;
                $l_aNotFound["html"] = file_get_contents('pages/404.html');
            break;
        };
        
        echo json_encode($l_aNotFound);
    }
}
?>