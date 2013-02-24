<?php
error_reporting(E_ALL);
include("LadderGenerator.php");
include("Graph.php");
include("Graph32.php");
include("Ladder.php");



//PDO credentials isbt db
define('ISBT_DSN', 'mysql:dbname=isbt;host=127.0.0.1');
define('ISBT_USER', 'isbt');
define('ISBT_PWD', 'wrAn6wrEhedr');

class Example
{
	public function Example()
	{
        $ladderGenerator = new LadderGenerator($this->getTestTeams(), 1, $this->getTestMatches(), null);
		print_r($ladderGenerator->generate());
        //print_r($this->getTeams(1));exit;
		//print_r(Algorithms::GenerateLadder($this->getTeams(1), 2, $this->getMatches(1), null));
		//print_r(Algorithms::GenerateLadder($this->getTeams(1), 1, array(), null));

		//TODO
		// - aan het eind moeten de juiste id's nog terug gerekend worden... m.b.v. de getTeams array
		// - wanneer er gerekend wordt met een poule met een oneven aantal teams dan gaat het mis, dit komt denk ik doordat er gewerkt wordt met een id van -1 voor een bye team...
	}

    private function getTestTeams()
    {
        return array(
            array("id" => 1, "IsInOperative" => 0),
            array("id" => 2, "IsInOperative" => 0),
            array("id" => 3, "IsInOperative" => 0),
            array("id" => 4, "IsInOperative" => 0),
            array("id" => 5, "IsInOperative" => 0),
            array("id" => 6, "IsInOperative" => 0),
            array("id" => 7, "IsInOperative" => 0),
            array("id" => 8, "IsInOperative" => 0),
            array("id" => 9, "IsInOperative" => 0),
            array("id" => 10, "IsInOperative" => 0),
            array("id" => 11, "IsInOperative" => 0),
            array("id" => 12, "IsInOperative" => 0),
            array("id" => 13, "IsInOperative" => 0),
            array("id" => 14, "IsInOperative" => 0),
            array("id" => 15, "IsInOperative" => 0),
            array("id" => 16, "IsInOperative" => 0),
            array("id" => 17, "IsInOperative" => 0),
            array("id" => 18, "IsInOperative" => 0),
            array("id" => 19, "IsInOperative" => 0),
            array("id" => 20, "IsInOperative" => 0),
            array("id" => 21, "IsInOperative" => 0),
            array("id" => 22, "IsInOperative" => 0),
            array("id" => 23, "IsInOperative" => 0),
            array("id" => 24, "IsInOperative" => 0),
            array("id" => 25, "IsInOperative" => 0),
            array("id" => 26, "IsInOperative" => 0),
            array("id" => 27, "IsInOperative" => 0),
            array("id" => 28, "IsInOperative" => 0),
            array("id" => 29, "IsInOperative" => 0),
            array("id" => 30, "IsInOperative" => 0)
        );
    }
    
    private function getTestMatches()
    {
        return array();
    }
    
	private function getTeams($poule)
	{
        $pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD, array(PDO::ATTR_PERSISTENT => true));
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$sql = "SELECT `id`, `IsInOperative`
				FROM `team`
				WHERE `poule` = :poule";
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(":poule", $poule);
		$stmt->execute();
	
		return $stmt->fetchAll(PDO::FETCH_ASSOC);    
	}
	
    public function getMatches($poule)
    {
        $pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD, array(PDO::ATTR_PERSISTENT => true));
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
        $sql = "SELECT 
					`match`.`id`, `team1`, `team2`
                FROM `match` 
                LEFT JOIN `team` `team1` ON(`team1`.`id` = `match`.`team1`) 
                LEFT JOIN `team` `team2` ON(`team2`.`id` = `match`.`team2`) 
				WHERE `team1`.`poule` = :poule || `team2`.`poule` = :poule";

        $stmt = $pdo->prepare($sql);
		$stmt->bindParam(":poule", $poule);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
new Example();