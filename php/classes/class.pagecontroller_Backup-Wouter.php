<?php

class BpageController{
    //security, login, etc.. later in dev.
    
    public function __construct($p_sPage){
        $this->page($p_sPage);
    }

    private function page($p_sPage){
        $p_aPage = explode("/", $p_sPage);
        
        if(!$p_aPage[1]) {
	        $p_aPage[1] = $p_aPage[0];
        }


       	switch($p_aPage[0]) {
/*
        	case 'poule-information':
        		if($p_aPage[0] != $p_aPage[1]) {
        			//Blablabla
        		} else {
	        		$l_aPageCall["html"] = file_get_contents(PAGE_DIR . $p_aPage[0] . '/' . $p_aPage[1] . '.html');
	        	}
        		break;
*/
        	default:
        		$p_aPage = $this->checkPageExists($p_aPage);
        		$l_aPageCall["html"] = file_get_contents(PAGE_DIR . $p_aPage[0] . '/' . $p_aPage[1] . '.html');                
        		break;
	    }
        echo json_encode($l_aPageCall);
    }
    
    private function checkPageExists($p_aPage)
    {
	    if(!file_exists(PAGE_DIR . $p_aPage[0] . '/' . $p_aPage[1] . '.html')){
            $l_aPageCall["error"] = true;
            $l_aPageCall["html"] = file_get_contents(PAGE_DIR . '404.html');
            echo json_encode($l_aPageCall);
            exit();
        } else {
        	return $p_aPage;
        }
    }
}