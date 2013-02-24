<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

include("LadderGenerator.php");
include("Graph.php");
include("Graph32.php");
include("Ladder.php");

//PDO credentials isbt db
define('ISBT_DSN', 'mysql:dbname=isbt;host=127.0.0.1');
define('ISBT_USER', 'root');
define('ISBT_PWD', '');

class Example
{
	public function Example()
	{
        echo "<pre>";
        print_r($this->getTeams(2));
        echo "<br /><br />";
        $ladderGenerator = new LadderGenerator($this->getTeams(2), 15, $this->getMatches(2));
		print_r($ladderGenerator->generate());
        //print_r($this->getTeams(1));exit;
		//print_r(Algorithms::GenerateLadder($this->getTeams(1), 2, $this->getMatches(1), null));
		//print_r(Algorithms::GenerateLadder($this->getTeams(1), 1, array(), null));

		//TODO
		// - aan het eind moeten de juiste id's nog terug gerekend worden... m.b.v. de getTeams array
		// - wanneer er gerekend wordt met een poule met een oneven aantal teams dan gaat het mis, dit komt denk ik doordat er gewerkt wordt met een id van -1 voor een bye team...
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