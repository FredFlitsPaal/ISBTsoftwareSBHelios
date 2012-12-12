<?php

class pouleInformationController {

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
		
		$pouleResults = $this->getPouleResults($poule['id']);
		$matches = $this->getMatches($poule['id']);
		$poules = $this->getPoules();
		
        include_once(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "pages" . DIRECTORY_SEPARATOR . "poule-information" . DIRECTORY_SEPARATOR . "index.html");

		return ob_get_clean();
    }
	
	private function getPoule($poule)
	{
        try
        {
            $pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD);
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
            $pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD);
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
            Monolog::getInstance()->addAlert('Error selecting poule, PDOException: ' . var_export($e, true));
        }

        return array(); 
	}
	
	private function getPoules()
	{
        try
        {
            $pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD);
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
            Monolog::getInstance()->addAlert('Error selecting poule, PDOException: ' . var_export($e, true));
        }

        return array();
	}
	
    private function getPouleResults($poule)
    {
        try
        {
            $pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
            $sql = "SELECT `team`.*, `user1`.name `user1`, `user2`.name `user2`
                    FROM `team` 
                    INNER JOIN `user` `user1` ON(user1 = user1.id) 
                    LEFT JOIN `user` `user2` ON(user2 = user2.id) 
					INNER JOIN `poule` ON(`team`.poule = `poule`.id)
					WHERE `poule`.id = :poule
					ORDER BY matches_won DESC, matches_draw DESC, points_balance DESC";

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
            $pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
            $sql = "SELECT `match`.*, `user1`.name as `team1_user1`, `user2`.name `team1_user2`, `user3`.name as `team2_user1`, `user4`.name as `team2_user2`, `category`.id as category
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
}