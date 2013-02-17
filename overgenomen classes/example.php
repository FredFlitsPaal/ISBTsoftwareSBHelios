<?php

const('ISBT_DSN', '');
const('ISBT_USER', '');
const('ISBT_PWD', '');

class Example
{
	private function getTeams($poule)
	{
        $pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD, array(PDO::ATTR_PERSISTENT => true));
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$sql = "SELECT *
				FROM `team`
				WHERE `poule` = :poule";
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(":poule", $poule['id']);
		$stmt->execute();
	
		return $stmt->fetchAll(PDO::FETCH_ASSOC);    
	}
	
    public static function getMatches($poule)
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
				WHERE poule.id = :poule";

        $stmt = $pdo->prepare($sql);
		$stmt->bindParam(":poule", $poule);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}