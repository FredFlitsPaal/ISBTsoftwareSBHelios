<?php

class carousselController
{
	public function __construct($sRequest)
	{
		$aResult = array();
		$aResult["error"] = false;
		
		if($sRequest == "current-matches")
		{
			$aResult = $this->reloadCurrentMatches($_POST["latestStartTime"], $_POST["maxAllowed"]);
		}
		elseif($sRequest == "upcoming-matches")
		{
			$aResult = $this->reloadUpcomingMatches($_POST["lastUpcomingMatchId"], $_POST["maxAllowed"]);
		}
		elseif($sRequest == "match-scores")
		{
			$aResult = $this->reloadMatchScores($_POST["latestEndTime"], $_POST["maxAllowed"]);
		}
		else
		{
			$aResult = $this->reloadAll();
		}
		
		echo json_encode($aResult);
	}
	
	private function reloadAll()
	{
			$aResult["allColumns"] = true;
			$aResult["currentMatches"] = $this->reloadCurrentMatches();
			$aResult["upcomingMatches"] = $this->reloadUpcomingMatches();
			$aResult["matchScores"] = $this->reloadMatchScores();
			
			return $aResult;
	}
	
	private function reloadCurrentMatches($latestStartTime = null, $maxAllowed = 10)
	{
		$aResult["column"] = "current-matches";
		
		$newCurrentMatches = $this->getCurrentMatches($latestStartTime, $maxAllowed);

		if(!isset($newCurrentMatches)) {
			$aResult["error"] = true;
		} else {
			ob_start();
			include_once(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "pages" . DIRECTORY_SEPARATOR . "caroussel" . DIRECTORY_SEPARATOR . "current-matches.html");

			$aResult["html"] = ob_get_clean();
		}

		return $aResult;
	}
	
	private function reloadUpcomingMatches($lastUpcomingMatchId = null, $maxAllowed = 10)
	{
		$aResult["column"] = "upcoming-matches";
		
		$newUpcomingMatches = $this->getUpcomingMatches($lastUpcomingMatchId, $maxAllowed);
		if(!isset($newUpcomingMatches)) {
			$aResult["error"] = true;
		} else {
			ob_start();
			include_once(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "pages" . DIRECTORY_SEPARATOR . "caroussel" . DIRECTORY_SEPARATOR . "upcoming-matches.html");

			$aResult["html"] = ob_get_clean();
		}
		
		return $aResult;
	}
	
	private function reloadMatchScores($latestEndTime = null, $maxAllowed = 10)
	{
		$aResult["column"] = "match-scores";
		
		$newMatchScores = $this->getMatchScores($latestEndTime, $maxAllowed);
		if(!isset($newMatchScores)) {
			$aResult["error"] = true;
		} else {
			ob_start();
			include_once(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "pages" . DIRECTORY_SEPARATOR . "caroussel" . DIRECTORY_SEPARATOR . "match-scores.html");

			$aResult["html"] = ob_get_clean();
		}

		return $aResult;
	}
	
	private function getCurrentMatches($latestStartTime = null, $maxAllowed = 10)
	{
        try
        {
            $pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD, array(PDO::ATTR_PERSISTENT => true));
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			if($latestStartTime != "") {
				$sStarttime = str_replace("|", " ", $latestStartTime);
				$addStarttime = " AND `match`.start_time > :starttime";
			} else {
				$addStarttime = "";
			}
			if($maxAllowed > 0) $addLimit = " LIMIT 0," . $maxAllowed;
			
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
                    WHERE poule.round = match.round
                    AND `match`.court != ''";
//            $sql .= $addStarttime;
            $sql .= " ORDER BY `match`.start_time DESC";
            $sql .= $addLimit;

            $stmt = $pdo->prepare($sql);
			$stmt->bindParam(":starttime", $sStarttime);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            Monolog::getInstance()->addAlert('Error selecting matches, PDOException: ' . var_export($e, true));
        }
	}
	
	private function getUpcomingMatches($lastUpcomingMatchId = null, $maxAllowed = 10)
	{
        try
        {
            $pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD, array(PDO::ATTR_PERSISTENT => true));
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			if($maxAllowed > 0) $addLimit = " LIMIT 0," . $maxAllowed;

			
            $sql = "SELECT
            			`match`.*,
						`user1`.name as `team1_user1`,
						`user2`.name `team1_user2`,
						`user3`.name as `team2_user1`,
						`user4`.name as `team2_user2`,
						`category`.name as category_name,
						`category`.level as category_level,
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
                    WHERE `status` = '0'";
            $sql .= " ORDER BY `match`.id ASC";

            $stmt = $pdo->prepare($sql);
			$stmt->bindParam(":upcomingid", $lastUpcomingId);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            Monolog::getInstance()->addAlert('Error selecting matches, PDOException: ' . var_export($e, true));
        }
	}
	
	private function getMatchScores($latestEndTime = null, $maxAllowed = 10)
	{
        try
        {
            $pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD, array(PDO::ATTR_PERSISTENT => true));
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			if($latestEndTime != "") {
				$endTime = str_replace("|", " ", $latestEndTime);
				$addEndtime = " AND `match`.end_time > :endtime";
			} else {
				$addEndtime = "";
				if($maxAllowed > 0) $addLimit = " LIMIT 0," . $maxAllowed;
			}

			
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
                    WHERE `status` = '4'";
            $sql .= $addEndtime;
            $sql .= " ORDER BY `match`.end_time DESC";
            $sql .= $addLimit;

            $stmt = $pdo->prepare($sql);
			$stmt->bindParam(":endtime", $endTime);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            Monolog::getInstance()->addAlert('Error selecting matches, PDOException: ' . var_export($e, true));
        }
	}
}