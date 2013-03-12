<?php

class CourtInformationController 
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
            if(!empty($_POST['addScore']))
            {
    			$message = $this->endMatchAndAddScore($_POST['match-id']);
            }
            else
            {
                $message = $this->endMatch($_POST['match-id']);
            }
		}
				
		$matchesOnCourt = $this->getMatches('matches_on_court');
		$UpcomingMatches = $this->getMatches('upcoming_matches');
		$availableCourts = $this->getAvaiableCourts();
		
        include_once(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "pages" . DIRECTORY_SEPARATOR . "court-information" . DIRECTORY_SEPARATOR . "index.html");
		
		return ob_get_clean();
    }
	
	private function getMatches($option = null)
	{
		if($option == null)
		{
			$order_by = '';
			Monolog::getInstance()->addInfo('no option given, add option');
		}
		elseif($option == 'upcoming_matches')
		{
			$order_by = ' ORDER BY `match`.id ASC';
		}
		elseif($option == 'matches_on_court')
		{
			// = court information (default in the past)
			$order_by = ' AND `match`.court != \'\' ORDER BY `match`.court ASC';
		}

        try
        {
            $pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD, array(PDO::ATTR_PERSISTENT => true));
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
            $sql = "SELECT 
						`match`.*,
						`user1`.name as `team1_user1`,
						`user2`.name `team1_user2`,
						`user3`.name as `team2_user1`,
						`user4`.name as `team2_user2`,
						`category`.id as category,
						`court`.`number` as `court`,
						`user1`.postponed as `user1_postponed`,
						`user2`.postponed as `user2_postponed`,
						`user3`.postponed as `user3_postponed`,
						`user4`.postponed as `user4_postponed`
                    FROM `match` 
                    INNER JOIN `team` `team1` ON(team1.id = match.team1) 
                    INNER JOIN `team` `team2` ON(team2.id = match.team2) 
                    INNER JOIN `user` `user1` ON(team1.user1 = user1.id) 
                    LEFT JOIN `user` `user2` ON(team1.user2 = user2.id) 
                    INNER JOIN `user` `user3` ON(team2.user1 = user3.id) 
                    LEFT JOIN `user` `user4` ON(team2.user2 = user4.id) 
                    INNER JOIN `poule` ON(poule.id = team1.poule)
                    INNER JOIN `category` ON(poule.category = category.id)
					LEFT JOIN `court` ON(`match`.court = `court`.id)
                    WHERE poule.round = match.round";
            $sql .= $order_by;

            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            Monolog::getInstance()->addAlert('Error selecting matches, PDOException: ' . var_export($e, true));
        }

        return array();  
	}
	
	private function getAvaiableCourts()
	{
        try
        {
            $pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD, array(PDO::ATTR_PERSISTENT => true));
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
            $sql = "SELECT `court`.*
					FROM `court`
					WHERE `court`.id NOT IN(
						SELECT `match`.`court`
						FROM `match`
						WHERE `match`.`court` IS NOT NULL
					)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            Monolog::getInstance()->addAlert('Error selecting available courts, PDOException: ' . var_export($e, true));
        }

        return array();  
	}

	private function pauseMatch($match, $pause)
	{
        try
        {
            $pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD, array(PDO::ATTR_PERSISTENT => true));
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
            $sql = "UPDATE 
						`match`
					SET
						`status` = :status
					WHERE
						`id` = :id";

            $stmt = $pdo->prepare($sql);
			$stmt->bindParam(":id", $match);
			$stmt->bindValue(":status", $pause ? MATCH_PAUSED : MATCH_STARTED);
            $stmt->execute();
        }
        catch(PDOException $e)
        {
            Monolog::getInstance()->addAlert('Could not change match status, PDOException: ' . var_export($e, true));
        }
	}
	
	private function endMatch($match)
	{
        try
        {
            $pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD, array(PDO::ATTR_PERSISTENT => true));
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
            $sql = "UPDATE 
						`match`
					SET
						`status` = :status,
						`court` = NULL,
						`end_time` = NOW()
					WHERE
						`id` = :id";

            $stmt = $pdo->prepare($sql);
			$stmt->bindParam(":id", $match);
			$stmt->bindValue(":status", MATCH_ENDED);
            
            $stmt->execute();
			
			return array("type" => "alert-success", "text" => "Match ended");
        }
        catch(PDOException $e)
        {
            Monolog::getInstance()->addAlert('Error ending match, PDOException: ' . var_export($e, true));
        }
		
		return array("type" => "alert-error", "text" => "Match status could not be changed");
	}

	private function endMatchAndAddScore($match)
	{
		//at least two sets should be filled with scores, otherwise somebody did something invalid
		if($_POST['set-1-1'] != '' && $_POST['set-1-2'] != '' && $_POST['set-2-1'] != '' && $_POST['set-2-2'] != '')
		{
	        try
	        {
	            $pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD, array(PDO::ATTR_PERSISTENT => true));
				$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				//fixed values
				$set3Team1 = 0;
				$set3Team2 = 0;

	            $sql = "UPDATE 
	            			`match`
						SET 
							`team1_set1_score` = :team1_set1_score,
							`team1_set2_score` = :team1_set2_score,
							`team1_set3_score` = :team1_set3_score,
							`team2_set1_score` = :team2_set1_score,
							`team2_set2_score` = :team2_set2_score,
							`team2_set3_score` = :team2_set3_score,
							`status` = :status,
							`court` = NULL,
							`end_time` = NOW()
						WHERE
							`id` = :match_id";

	            $stmt = $pdo->prepare($sql);
				$stmt->bindParam(":team1_set1_score", $_POST['set-1-1'], PDO::PARAM_INT);
				$stmt->bindParam(":team1_set2_score", $_POST['set-2-1'], PDO::PARAM_INT);
				$stmt->bindParam(":team1_set3_score", $set3Team1);
				$stmt->bindParam(":team2_set1_score", $_POST['set-1-2'], PDO::PARAM_INT);
				$stmt->bindParam(":team2_set2_score", $_POST['set-2-2'], PDO::PARAM_INT);
				$stmt->bindParam(":team2_set3_score", $set3Team2);
				$stmt->bindValue(":status", 		  MATCH_FINISHED);
				$stmt->bindParam(":match_id", $match, PDO::PARAM_INT);
	            $stmt->execute();
				
				return array("type" => "alert-success", "text" => "Match ended and the match results were saved.");
	        }
	        catch(PDOException $e)
	        {
	            Monolog::getInstance()->addAlert('Error ending match and updating score, PDOException: ' . var_export($e, true));
	        }
			
			return array("type" => "alert-error", "text" => "Match status could not be changed and failed to save match results");
		}
		else
		{
			return array("type" => "alert-info", "text" => "Invalid match results, at least two sets must be submitted! Nothing changed, try again please...");
		}
	}
	
	private function startMatch($match)
	{
		try
        {
            $pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD, array(PDO::ATTR_PERSISTENT => true));
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
            $sql = "SELECT 
						`match`.status,
						`match`.court
					FROM
						`match`
					WHERE
						`id` = :id";

            $stmt = $pdo->prepare($sql);
			$stmt->bindParam(":id", $match);
            $stmt->execute();
        }
        catch(PDOException $e)
        {
            Monolog::getInstance()->addAlert('Error selecting match, PDOException: ' . var_export($e, true));
            return array("type" => "alert-error", "text" => "Could not assign match to court");
        }

        $result = $stmt->fetchAll();
        if($result[0]['status'] != 0 && $result[0]['court'] != NULL)
        {
        	return array("type" => "alert-warning", "text" => "The match is already assigned to court number ".$result[0]['court']);
        }
        elseif($result[0]['status'] != 0)
        {
        	return array("type" => "alert-warning", "text" => "The match is already finished!");
        }
        else
        {
			$courts = $this->getAvaiableCourts();
			
	        if(sizeof($courts) == 0)
			{
				return array("type" => "alert-warning", "text" => "No courts available!");
			}
			
			try
	        {
	            $pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD, array(PDO::ATTR_PERSISTENT => true));
				$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
	            $sql = "UPDATE 
							`match`
						SET
							`court` = :court,
							`status` = :status,
							`start_time` = NOW()
						WHERE
							`id` = :id";

	            $stmt = $pdo->prepare($sql);
				$stmt->bindParam(":id", $match);
				$stmt->bindValue(":court", $courts[0]['id']);
				$stmt->bindValue(":status", MATCH_STARTED);
	            $stmt->execute();
				
				return array("type" => "alert-success", "text" => "The match is assigned to court number ".$courts[0]['id']);
	        }
	        catch(PDOException $e)
	        {
	            Monolog::getInstance()->addAlert('Error starting match, PDOException: ' . var_export($e, true));
	        }
			
			return array("type" => "alert-error", "text" => "Could not assign match to court");
		}
	}
}