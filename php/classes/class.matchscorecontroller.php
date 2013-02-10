<?php

class matchScoreController {

    public function actionIndex()
    {
		ob_start();
		
		if(!empty($_POST['match-id']))
		{
			$message = $this->updateScore();
		}
		
		$matches = $this->getMatches();

        include_once(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "pages" . DIRECTORY_SEPARATOR . "match-scores" . DIRECTORY_SEPARATOR . "index.html");
		
		return ob_get_clean();
    }

    private function getMatches()
    {
        try
        {
            $pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD, array(PDO::ATTR_PERSISTENT => true));
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
                    ORDER BY `status` = '4', `status` = '1', `status` = '2', `status` = '0', `status` = '3', `id` ASC";

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
	
	private function updateScore()
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

	            $sql = "UPDATE `match`
						SET 
							`team1_set1_score` = :team1_set1_score,
							`team1_set2_score` = :team1_set2_score,
							`team1_set3_score` = :team1_set3_score,
							`team2_set1_score` = :team2_set1_score,
							`team2_set2_score` = :team2_set2_score,
							`team2_set3_score` = :team2_set3_score,
							`end_time` = NOW(),
							`status` = '4'
						WHERE
							`id` = :match_id";

	            $stmt = $pdo->prepare($sql);
				$stmt->bindParam(":team1_set1_score", $_POST['set-1-1'], PDO::PARAM_INT);
				$stmt->bindParam(":team1_set2_score", $_POST['set-2-1'], PDO::PARAM_INT);
				$stmt->bindParam(":team1_set3_score", $set3Team1);
				$stmt->bindParam(":team2_set1_score", $_POST['set-1-2'], PDO::PARAM_INT);
				$stmt->bindParam(":team2_set2_score", $_POST['set-2-2'], PDO::PARAM_INT);
				$stmt->bindParam(":team2_set3_score", $set3Team2);
				$stmt->bindParam(":match_id", $_POST['match-id'], PDO::PARAM_INT);
	            $stmt->execute();
				
				return array("type" => "alert-success", "text" => "Match results saved.");
	        }
	        catch(PDOException $e)
	        {
	            Monolog::getInstance()->addAlert('Error updating score, PDOException: ' . var_export($e, true));
	        }
			
			return array("type" => "alert-error", "text" => "Failed to save match results");
		}
		else
		{
			return array("type" => "alert-info", "text" => "Invalid match results, at least two sets must be submitted");
		}
	}
	
	static public function remoteUpdateScore()
	{
		$oMatchScores = new matchScoreController();
		$oMatchScores->updateScore();
	}
}