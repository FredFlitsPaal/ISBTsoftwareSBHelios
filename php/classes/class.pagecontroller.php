<?php

class pageController{
    //security, login, etc.. later in dev.
    
    public function __construct($p_sPage){
        $this->page($p_sPage);
    }

    private function page($p_sPage){
        $p_sPage = explode("/", $p_sPage);
        
        switch($p_sPage[0]){
            case "settings":
                $l_aPageCall["error"] = false;
                $l_aPageCall["html"] = file_get_contents(PAGE_DIR.'settings/settings.html');
            break;
            
            case "tournament":
                $l_aPageCall["error"] = false;
                $l_aPageCall["html"] = file_get_contents(PAGE_DIR.'tournament/header.html');
                
                //default page for tournament
                if(!isset($p_sPage[1])){
                    $l_aPageCall["html"] .= file_get_contents(PAGE_DIR.'tournament/currentmatches.html');
                }else{
                    $l_aPageCall["html"] .= file_get_contents(PAGE_DIR.'tournament/'.$p_sPage[1].'.html');
                }
                
                $l_aPageCall["html"] .= file_get_contents(PAGE_DIR.'tournament/footer.html');
            break;
            
            default:
                $l_aPageCall["error"] = true;
                $l_aPageCall["html"] = file_get_contents(PAGE_DIR.'404.html');
            break;
        };
        
        echo json_encode($l_aPageCall);
    }
}