<?php

class pageController{
    //security, login, etc.. later in dev.
    
    public function __construct($p_sPage){
        $this->page($p_sPage);
    }

    private function page($p_sPage){
        $p_sPage = explode("/", $p_sPage);

        //Deafult subpage
        if(!isset($p_sPage[1])){
	        $p_sPage[1] = 'index';
         }
        switch($p_sPage[0]){
            case "match-scores":
                $l_aPageCall["error"] = false;
                //Subpage
                if(!file_exists(PAGE_DIR.'match-scores/' . $p_sPage[1] . '.html')){
	                echo 'De stukkels die dit gemaakt hebben hebben keihard gefaald, sla ze op hun bek dat ze het fixen!';
                }else{
         	       //$l_aPageCall["html"] = file_get_contents(PAGE_DIR.'match-scores/' . $p_sPage[1] . '.html');                
                    $controller = new matchScoreController();
                    $l_aPageCall["html"] = $controller->actionIndex();
                }
                
            break;
            case "poule-information":
                $l_aPageCall["error"] = false;
                //Subpage
                if(!file_exists(PAGE_DIR.'poule-information/' . $p_sPage[1] . '.html')){
	                echo 'De stukkels die dit gemaakt hebben hebben keihard gefaald, sla ze op hun bek dat ze het fixen!';
                }else{

         	       $l_aPageCall["html"] = file_get_contents(PAGE_DIR.'poule-information/' . $p_sPage[1] . '.html');
                }
                
            break;
            
            default:
                $l_aPageCall["error"] = true;
                $l_aPageCall["html"] = file_get_contents(PAGE_DIR.'404.html');
            break;
        };
        echo json_encode($l_aPageCall);
    }
}