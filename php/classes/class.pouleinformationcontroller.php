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
					ORDER BY matches_won DESC, matches_draw DESC, sets_won DESC, points_won DESC, points_balance DESC";

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
	
    private function getMatches($poule)
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
		$pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD, array(PDO::ATTR_PERSISTENT => true));
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		try
		{
			$pdo->beginTransaction();
			
			foreach($matches as $match)
			{			
				$this->updateTeamResults($pdo, $match['team1'], ToolBox::getTeamResults(1, $match));
				$this->updateTeamResults($pdo, $match['team2'], ToolBox::getTeamResults(2, $match));
			}
			
			$this->startNextRound($pdo, $poule);
			
			$pdo->commit();
			
			return array("type" => "alert-success", "text" => "New round started");
		}
		catch(PDOException $e)
		{
			$pdo->rollBack();
			Monolog::getInstance()->addAlert('Error ending round, PDOException: ' . var_export($e, true));
		}
		
		return array("type" => "", "text" => "Failed to start a new round");
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
					`points_balance` = `points_balance` + :points_won - :points_lost
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
		
		$this->generateMatches($pdo, $poule);
	}
	
	private function generateMatches($pdo, $poule)
	{
		//TODO: use the algoritm of https://github.com/FredFlitsPaal/SharpShuttle to generate these matches
		
		$teams = $this->getTeams($pdo, $poule);
		$matches = array();
		
		if(sizeof($teams) > 1)
		{
			foreach($teams as $team1)
			{
				foreach($teams as $team2)
				{
					if(!in_array($team1['id'] . "-" . $team2['id'], $matches) && !in_array($team2['id'] . "-" . $team1['id'], $matches) && $team1['id'] != $team2['id'])
					{
						$matches[] = ($team1['id'] . "-" . $team2['id']);
					}
				}
			}	
		}
		
		if(sizeof($matches > 0))
		{
			$this->addMatches($pdo, $matches, $poule);
		}
	}
	
	private function addMatches($pdo, $matches, $poule)
	{
		if(sizeof($matches) > 0)
		{
			Monolog::getInstance()->addDebug('Matches: ' . var_export($matches, true));
			
			foreach($matches as $match)
			{
				list($team1, $team2) = explode("-", $match);
				
				$sql = "INSERT INTO `match` (`team1`, `team2`, `round`, `status`)
						VALUES (:team1, :team2, :round, :status)";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(":team1", $team1);
				$stmt->bindParam(":team2", $team2);
				$stmt->bindValue(":round", $poule['round'] + 1);
				$stmt->bindValue(":status", MATCH_NOT_YET_STARTED);
				$stmt->execute();
			}
		}
	}
	
	private function getTeams($pdo, $poule)
	{
		$sql = "SELECT *
				FROM `team`
				WHERE `poule` = :poule";
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(":poule", $poule['id']);
		$stmt->execute();
		
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}