<?php

class pouleInformationController 
{

    public function actionIndex()
    {
		ob_start();

		if(!empty($_POST['poule']))
		{
			$poule = $this->getPoule($_POST['poule']);
		}
		else
		{
			$poule = $this->getFirstPoule();
		}
		
		$matches = $this->getMatches($poule['id']);
		
		if(!empty($_POST['startNextRound']))
		{
			$message = $this->endRound($poule, $matches);
			$matches = $this->getMatches($poule['id']);
		}

		if(!empty($_POST['finishPoule']))
		{
			$message = $this->finishPoule($poule, $matches);
			$matches = $this->getMatches($poule['id']);
		}
		
		$pouleResults = $this->getPouleResults($poule['id']);
		$poules = $this->getPoules();
		
        include_once(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "pages" . DIRECTORY_SEPARATOR . "poule-information" . DIRECTORY_SEPARATOR . "index.html");

		return ob_get_clean();
    }
	
	private function getPoule($poule)
	{
        try
        {
            $pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD, array(PDO::ATTR_PERSISTENT => true));
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
            $sql = "SELECT `poule`.*, `category`.name as `category_name`, `category`.level as `category_level` 
                    FROM `poule`
					INNER JOIN `category` ON(`category`.id = `poule`.category)
                    WHERE `poule`.`id` = :poule";

            $stmt = $pdo->prepare($sql);
			$stmt->bindParam(":poule", $poule);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            Monolog::getInstance()->addAlert('Error selecting poule, PDOException: ' . var_export($e, true));
        }

        return array(); 
	}
	
	private function getFirstPoule()
	{
        try
        {
            $pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD, array(PDO::ATTR_PERSISTENT => true));
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
            $sql = "SELECT `poule`.*, `category`.name as `category_name`, `category`.level as `category_level` 
                    FROM `poule`
					INNER JOIN `category` ON(`category`.id = `poule`.category)
					ORDER BY id
					LIMIT 0,1";

            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            Monolog::getInstance()->addAlert('Error selecting first poule, PDOException: ' . var_export($e, true));
        }

        return array(); 
	}
	
	private function getPoules()
	{
        try
        {
            $pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD, array(PDO::ATTR_PERSISTENT => true));
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
            $sql = "SELECT `poule`.id, `category`.name as `category_name`, `category`.level as `category_level` 
                    FROM `poule`
					INNER JOIN `category` ON(`category`.id = `poule`.category)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            Monolog::getInstance()->addAlert('Error selecting poules, PDOException: ' . var_export($e, true));
        }

        return array();
	}
	
    private function getPouleResults($poule)
    {
        try
        {
            $pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD, array(PDO::ATTR_PERSISTENT => true));
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
            $sql = "SELECT `team`.*, `user1`.name `user1`, `user2`.name `user2`
                    FROM `team` 
                    INNER JOIN `user` `user1` ON(user1 = user1.id) 
                    LEFT JOIN `user` `user2` ON(user2 = user2.id) 
					INNER JOIN `poule` ON(`team`.poule = `poule`.id)
					WHERE `poule`.id = :poule
					ORDER BY `average_sets_won` DESC, `points_balance` DESC";

            $stmt = $pdo->prepare($sql);
			$stmt->bindParam(":poule", $poule);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            Monolog::getInstance()->addAlert('Error selecting poule results, PDOException: ' . var_export($e, true));
        }

        return array();      
    }
	
    public static function getMatches($poule)
    {
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
                    INNER JOIN `category` ON (poule.category = category.id)
                    WHERE poule.round = match.round
					AND poule.id = :poule";

            $stmt = $pdo->prepare($sql);
			$stmt->bindParam(":poule", $poule);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            Monolog::getInstance()->addAlert('Error selecting matches, PDOException: ' . var_export($e, true));
        }

        return array();      
    }
	
	private function endRound($poule, $matches)
	{
        if(Toolbox::hasFinishedRound($poule['id']))
        {
        	if(count($matches) > 0)
        	{
	            try
	            {
	            	$pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD, array(PDO::ATTR_PERSISTENT => true));
	            	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	                $pdo->beginTransaction();

	                foreach($matches as $match)
	                {			
	                    $this->updateTeamResults($pdo, $match['team1'], ToolBox::getTeamResults(1, $match));
	                    $this->updateTeamResults($pdo, $match['team2'], ToolBox::getTeamResults(2, $match));
	                }

	                $pdo->commit();
	            }
	            catch(PDOException $e)
	            {
	                Monolog::getInstance()->addAlert('Error while updating team results, PDOException: ' . var_export($e, true));
	                $pdo->rollBack();
	                return array("type" => "alert-danger", "text" => "Failed to start a new round");
	            }
	            
	            try
	            {
	            	$pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD, array(PDO::ATTR_PERSISTENT => true));
	            	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	                $pdo->beginTransaction();
	                
	                $success = $this->startNextRound($pdo, $poule);

	                $pdo->commit();
	                
	                if($success == true)
	                {
	                    return array("type" => "alert-success", "text" => "New round started");
	                }
	                else
	                {
	                    return array("type" => "alert-danger", "text" => "New round started, but no new matches were generated because all different combinations of matches are already played...");
	                }
	            }
	            catch(PDOException $e)
	            {
	                Monolog::getInstance()->addAlert('Error ending round, PDOException: ' . var_export($e, true));
	                $pdo->rollBack();
	                return array("type" => "alert-danger", "text" => "Failed to start a new round");
	            }
	        }
        	else
        	{
        		return array("type" => "alert-warning", "text" => "Could not start a new round, because the poule was already finished before!");
        	}
        }
        return array("type" => "alert-warning", "text" => "Could not start a new round, as a new round was already started by someone else.");
    }

    private function finishPoule($poule, $matches)
	{
        if(Toolbox::hasFinishedRound($poule['id']))
        {
        	if(count($matches) > 0)
        	{
	            try
	            {
	            	$pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD, array(PDO::ATTR_PERSISTENT => true));
	            	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	                $pdo->beginTransaction();

	                foreach($matches as $match)
	                {			
	                    $this->updateTeamResults($pdo, $match['team1'], ToolBox::getTeamResults(1, $match));
	                    $this->updateTeamResults($pdo, $match['team2'], ToolBox::getTeamResults(2, $match));
	                }

	                $sql = "UPDATE 
								`poule`
							SET
								`round` = `round` + 1
							WHERE
								`id` = :poule";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(":poule", $poule['id']);
					$stmt->execute();

	                $pdo->commit();

	                return array("type" => "alert-success", "text" => "Finished poule!");
	            }
	            catch(PDOException $e)
	            {
	                Monolog::getInstance()->addAlert('Error while updating team results, PDOException: ' . var_export($e, true));
	                $pdo->rollBack();
	                return array("type" => "alert-danger", "text" => "Failed to finish poule");
	            }
        	}
        	else
        	{
        		return array("type" => "alert-warning", "text" => "Could not finish the poule, as someone else already finished the poule.");
        	}
        }
        
        return array("type" => "alert-warning", "text" => "Could not finish the poule, as a new round was already started by someone else.");
    }

	private function updateTeamResults($pdo, $team, $results)
	{
		$sql = "UPDATE 
					team
				SET
					`matches_played` = `matches_played` + 1,
					`matches_won` = `matches_won` + :matches_won,
					`matches_draw` = `matches_draw` + :matches_draw,
					`matches_lost` = `matches_lost` + :matches_lost,
					`sets_won` = `sets_won` + :sets_won,
					`sets_lost` = `sets_lost` + :sets_lost,
					`points_won` = `points_won` + :points_won,
					`points_lost` = `points_lost` + :points_lost,
					`average_sets_won` = `sets_won` / `matches_played`,
					`points_balance` = (`points_balance` + :points_won - :points_lost) / `matches_played`
				WHERE id = :team";
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(":matches_won", $results['matches_won']);
		$stmt->bindParam(":matches_draw", $results['matches_draw']);
		$stmt->bindParam(":matches_lost", $results['matches_lost']);
		$stmt->bindParam(":sets_won", $results['sets_won']);
		$stmt->bindParam(":sets_lost", $results['sets_lost']);
		$stmt->bindParam(":points_won", $results['points_won']);
		$stmt->bindParam(":points_lost", $results['points_lost']);
		$stmt->bindParam(":team", $team);
		$stmt->execute();
	}
	
	private function startNextRound($pdo, $poule)
	{
		$sql = "UPDATE 
					`poule`
				SET
					`round` = `round` + 1
				WHERE
					`id` = :poule";
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(":poule", $poule['id']);
		$stmt->execute();
		
		return $this->generateMatches($pdo, $poule);
	}
	
	private function generateMatches($pdo, $poule)
	{
		$teams = $this->getTeams($pdo, $poule);
		$matches = array();
     
        $alreadyPlayedMatches = $this->getAlreadyPlayedMatches($pdo, $poule);
		
        if(sizeof($teams) > 1)
		{
            Monolog::getInstance()->addDebug('New round : ' . ($poule['round'] + 1));
            $generator = new LadderGenerator($teams, $poule['round'] + 1, $alreadyPlayedMatches);
            $matches = $generator->generate();
		}
		
		if(count($matches) > 0)
		{
			$this->addMatches($pdo, $matches, $poule);
			return true;
		}else{
			Monolog::getInstance()->addWarning('New round started, but no new matches were generated because all different combinations of matches are already played...');
			return false;
		}
	}
	
	private function addMatches($pdo, $matches, $poule)
	{
		if(sizeof($matches) > 0)
		{
			//Monolog::getInstance()->addDebug('Matches: ' . var_export($matches, true));
			
			foreach($matches as $match)
			{
				$sql = "INSERT INTO `match` (`team1`, `team2`, `round`, `status`)
						VALUES (:team1, :team2, :round, :status)";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(":team1", $match['team1']['id']);
				$stmt->bindParam(":team2", $match['team2']['id']);
				$stmt->bindValue(":round", $poule['round'] + 1);
                if($match['team1']['id'] == -1 || $match['team2']['id'] == -1)
                {
                    $stmt->bindValue(":status", MATCH_FINISHED);
                }
                else
                {
                    $stmt->bindValue(":status", MATCH_NOT_YET_STARTED);
                }
				$stmt->execute();
			}
			Monolog::getInstance()->addDebug('Matches added for poule: '.$poule['id'].', round: '.$poule['round'] + 1);
		}
	}
	
	private function getTeams($pdo, $poule)
	{
		$sql = "SELECT *
				FROM `team`
				WHERE `poule` = :poule
				ORDER BY `average_sets_won` DESC, `points_balance` DESC";
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(":poule", $poule['id']);
		$stmt->execute();
		
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	private function getAlreadyPlayedMatches($pdo, $poule){
		try
        {
            $sql = "SELECT `team1`, `team2`
                    FROM `match` 
                    INNER JOIN `team` `team1` ON(team1.id = match.team1) 
                    INNER JOIN `poule` ON(poule.id = team1.poule)
                    WHERE poule.id = :poule";

            $stmt = $pdo->prepare($sql);
			$stmt->bindParam(":poule", $poule['id']);
            $stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            Monolog::getInstance()->addAlert('Error selecting already played matches, PDOException: ' . var_export($e, true));
        }
	}
}