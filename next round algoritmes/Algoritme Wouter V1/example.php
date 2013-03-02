<?php
class Example
{
	const pouleId = 2;
	
	private $iCurrentRound;
	private $aTeams = array();
	private $aTeamIDs = array();
	private $aAllreadyPlayedMatches = array();
	
	private $aPossibleMatches = array();
	
	public function Example()
	{echo'hoi';exit;
        $pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD, array(PDO::ATTR_PERSISTENT => true));
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$this->aTeams = $this->getTeams($pdo, array("id" => self::pouleId));
		$this->iCurrentRound = $this->findCurrentRound();
		$this->aAllreadyPlayedMatches = $this->getAlreadyPlayedMatches($pdo, array("id" => self::pouleId));
		
		//To test what happens with an uneven array
		//array_pop($this->aTeams);
		
		if(count($this->aTeams) % 2) {
			//Add bye team and bye team matches if there is an uneven number of teams
			$this->addByeTeam();
			$this->addByeTeamMatches();
			Monolog::getInstance()->addDebug('added a bye team'));
		}
		//print_r($this->aAllreadyPlayedMatches);
		exit;
		$this->aPossibleMatches = array();
		if(sizeof($this->aTeams) > 1)
		{
			// Just input the team id's to the algorithm
			$this->setTeamIDs();
//            Monolog::getInstance()->addDebug('New round : ' . ($poule['round'] + 1));
			$oRoundGenerator = new RoundGenerator($this->aTeamIDs, $this->aAllreadyPlayedMatches, $this->iCurrentRound);
			$this->aPossibleMatches = $oRoundGenerator->execute();
		}

		if(count($this->aPossibleMatches) > 0) {
			//Remove all bye team matches
			$this->removeByeTeamMatches();
			
			// add found matches here, be alert that the matches array just contains team id's and not any other info about a team
			//$this->addMatches($pdo, $matches, $poule);
		} else {
//			Monolog::getInstance()->addWarning('New round started, but no new matches were generated because all different combinations of matches are already played...');
		}		
	}
	
	public function getPossibleMatches()
	{
		return $this->aPossibleMatches;
	}
    
	private function getTeams($pdo, $poule)
	{
		$sql = "SELECT `id`, `matches_played`
				FROM `team`
				WHERE `poule` = :poule
				ORDER BY `average_sets_won` DESC, `points_balance` DESC";
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(":poule", $poule['id']);
		$stmt->execute();
		
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	private function addByeTeam()
	{
		$this->aTeams[] = array("id" => "10000000", "matches_played" => $this->iCurrentRound - 1);
	}
	
	private function addByeTeamMatches()
	{
		foreach($this->aTeams as $aTeam) {
			if($aTeam["matches_played"] < $this->iCurrentRound) array_push($this->aAllreadyPlayedMatches, array("team1" => $aTeam["id"], "team2" => "-1"));
		}
	}

	private function removeByeTeamMatches()
	{
		foreach($this->aPossibleMatches as $sKey=>$aPossibleMatch) {
			if(in_array("10000000", $aPossibleMatch)) unset($this->aPossibleMatches[$sKey]);
		}
	}
	
	private function setTeamIDs() {
		foreach($this->aTeams as $aTeam) {
			$this->aTeamIDs[]["id"] = $aTeam["id"];
		}
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
	
	private function findCurrentRound()
	{
		$i = 0;
		foreach($this->aTeams as $aTeam)
		{
			if($aTeam["matches_played"] > $i)
			{
				$i = $aTeam["matches_played"];	
			} 
		}
		
		return $i;
	}
}

include("class.roundgenerator.php");

error_reporting(E_ALL);
ini_set("display_errors", 1);

//PDO credentials isbt db
define('ISBT_DSN', 'mysql:dbname=isbt;host=127.0.0.1');
define('ISBT_USER', 'isbt');
define('ISBT_PWD', 'wrAn6wrEhedr');

echo "<pre>";
$oExample = new Example();
//$aPossibleMatches = $oExample->getPossibleMatches();
//var_dump($aPossibleMatches);