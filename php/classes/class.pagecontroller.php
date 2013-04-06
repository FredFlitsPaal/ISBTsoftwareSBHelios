<?php
/* ===========================================================
 * Copyright 2012 SB Helios, Netherlands.
 *
 * Licensed under the Apache License, Version 2.0 (the "License")
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */

class pageController{
    //security, login, etc.. later in dev.
    
    public function __construct($p_sPage){
        $this->page($p_sPage);
    }

    private function page($p_sPage){
        $p_aPage = explode("/", $p_sPage);
		
        //dit is puur voor het feit dat de default in de switch niet breekt denk ik?
        if(empty($p_aPage[1])) {
	        $p_aPage[1] = $p_aPage[0];
        }
		
        switch($p_aPage[0])
		{
            case "match-scores":
				$controller = new matchScoreController();
                $l_aPageCall["error"] = false;
                $l_aPageCall["html"] = $controller->actionIndex();
            break;

            case "court-information":
				$controller = new CourtInformationController();
                $l_aPageCall["error"] = false;
                $l_aPageCall["html"] = $controller->actionIndex();
            break;

            case "poule-information":
				$controller = new pouleInformationController();
                $l_aPageCall["error"] = false;
                $l_aPageCall["html"] = $controller->actionIndex();
            break;

            case "participants":
                $participants = new ParticipantController();
                $l_aPageCall["error"] = false;
                $l_aPageCall["html"] = $participants->actionIndex();
            break;
            
        	default:
        		$p_aPage = $this->checkPageExists($p_aPage);
        		$l_aPageCall["html"] = file_get_contents(PAGE_DIR . $p_aPage[0] . '/' . $p_aPage[1] . '.html');                
        	break;
        };
		
		// Send the result
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