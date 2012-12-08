<?php

include_once(__DIR__ . DIRECTORY_SEPARATOR . "class.toolbox.php");

class matchScoreController {

    public function actionIndex()
    {
        $matches = $this->getMatches();

        ob_start();
        include_once(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "pages" . DIRECTORY_SEPARATOR . "match-scores" . DIRECTORY_SEPARATOR . "index.html");
        return ob_get_clean();
    }

    private function getMatches()
    {
        try
        {
            $pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD);

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
                    WHERE poule.round = match.round";

            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            print_r($e);
        }

        return array();      
    }

}