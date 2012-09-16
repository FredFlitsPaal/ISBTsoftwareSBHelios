<?php

class pageController{
    //security, login, etc.. later in dev.
    
    public function page($l_sPage){
        $l_sPage = explode("/", $l_sPage);
        
        switch($l_sPage[0]){
            case "settings":
                $l_aNotFound["error"] = false;
                $l_aNotFound["html"] = file_get_contents('pages/settings/settings.html');
            break;
            
            case "tournament":
                $l_aNotFound["error"] = false;
                $l_aNotFound["html"] = file_get_contents('pages/tournament/header.html');
                
                //default page for tournament
                if(!isset($l_sPage[1])){
                    $l_aNotFound["html"] .= file_get_contents('pages/tournament/currentmatches.html');
                }else{
                    $l_aNotFound["html"] .= file_get_contents('pages/tournament/'.$l_sPage[1].'.html');
                }
                
                $l_aNotFound["html"] .= file_get_contents('pages/tournament/footer.html');
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