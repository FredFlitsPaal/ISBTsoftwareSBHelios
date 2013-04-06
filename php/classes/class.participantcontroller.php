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

class ParticipantController 
{

    public function actionIndex()
    {
		ob_start();
		
		if(!empty($_POST['action']) && $_POST['action'] == "start-match")
		{
			$message = $this->startMatch($_POST['match-id']);
		}
		
		if(!empty($_POST['action']) && $_POST['action'] == "pause-match")
		{
			$this->pauseMatch($_POST['match-id'], true);
		}
		
		if(!empty($_POST['action']) && $_POST['action'] == "play-match")
		{
			$this->pauseMatch($_POST['match-id'], false);
		}
		
		if(!empty($_POST['action']) && $_POST['action'] == "end-match")
		{
			$message = $this->endMatch($_POST['match-id']);
		}
		
		if(isset($_POST['player-id']) == true && isset($_POST['postpone']) == true)
		{
			$message = $this->changeState();
		}

		$participants = $this->getParticipants();

        include_once(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "pages" . DIRECTORY_SEPARATOR . "participants" . DIRECTORY_SEPARATOR . "index.html");
		
		return ob_get_clean();
    }
	
	private function getParticipants()
	{
		try
        {
            $pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD, array(PDO::ATTR_PERSISTENT => true));
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
            $sql = "SELECT * FROM `user` ORDER BY `name`";

            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            Monolog::getInstance()->addAlert('Selecting participants went wrong, PDOException: ' . var_export($e, true));
        }

        return array();
	}

	private function changeState()
	{
		if(is_numeric($_POST['player-id']) == true && isset($_POST['postpone']) == true)
		{
			if($_POST['postpone'] == 'true'){
				$postponed = 1;
			}else{
				$postponed = 0;
			}

			try
	        {
	            $pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD, array(PDO::ATTR_PERSISTENT => true));
				$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
	            $sql = "UPDATE `user` SET `postponed` = :postponed WHERE `id` = :id LIMIT 1";

	            $stmt = $pdo->prepare($sql);
	            $stmt->bindParam(":id", $_POST['player-id'], PDO::PARAM_INT);
	            $stmt->bindParam(":postponed", $postponed);
	            $stmt->execute();
	        }
	        catch(PDOException $e)
	        {
	            Monolog::getInstance()->addAlert('updating postponed state went wrong, PDOException: ' . var_export($e, true));
	            return array("type" => "alert-danger", "text" => "Something went wrong, try again...");			
	        }
		}
		else
		{
			return array("type" => "alert-danger", "text" => "Something went wrong, try again...");			
		}

		if($_POST['postpone'] == 'true'){
			return array("type" => "alert-success", "text" => $_POST['name']." is now postponed!");
		}else{
			return array("type" => "alert-success", "text" => $_POST['name']." is now ready!");
		}
	}
}