<?php
require_once("class.roundgenerator.php");
require_once(dirname(__FILE__) . "/../php/classes/class.initialiseISBT.php");
new initialiseISBT();

error_reporting(E_ALL);
ini_set("display_errors", 1);

//PDO credentials isbt db
//define('ISBT_DSN', 'mysql:dbname=isbt;host=127.0.0.1');
//define('ISBT_USER', 'isbt');
//define('ISBT_PWD', 'wrAn6wrEhedr');

echo "<pre>";
$oExample = new Example();
$aPossibleMatches = $oExample->getPossibleMatches();
var_dump($aPossibleMatches);

class Example
{
	const pouleId = 2;
	
	private $iCurrentRound;
	private $aTeams = array();
	private $aTeamIDs = array();
	private $aAllreadyPlayedMatches = array();
	
	private $aPossibleMatches = array();
	
	public function Example()
	{
        $pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD, array(PDO::ATTR_PERSISTENT => true));
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$this->aTeams = $this->getTeams($pdo, self::pouleId);
		$this->iCurrentRound = $this->findNextRound();
		$this->aAllreadyPlayedMatches = $this->getAlreadyPlayedMatches($pdo, array("id" => self::pouleId));
		//DEV
		Monolog::getInstance()->addDebug('$this->aTeams = '.var_export($this->aTeams, true));
		Monolog::getInstance()->addDebug('$$this->iCurrentRound = '.$this->iCurrentRound);
		Monolog::getInstance()->addDebug('$this->aAllreadyPlayedMatches = '.var_export($this->aAllreadyPlayedMatches, true));
		//print_r($this->aAllreadyPlayedMatches);exit;
		//To test what happens with an uneven array
		//array_pop($this->aTeams);
		
		if(count($this->aTeams) % 2) {
			//Add bye team and bye team matches if there is an uneven number of teams
			$this->addByeTeam();
			$this->addByeTeamMatches();
			Monolog::getInstance()->addDebug('added bye team and matches');
			Monolog::getInstance()->addDebug('$this->aTeams = '.var_export($this->aTeams, true));
			Monolog::getInstance()->addDebug('$this->aAllreadyPlayedMatches = '.var_export($this->aAllreadyPlayedMatches, true));
		}
		//$this->aPossibleMatches = array();

		if(count($this->aTeams) > 1)
		{
			// Just input the team id's to the algorithm
			$this->setTeamIDs();
			Monolog::getInstance()->addDebug('$this->aTeamIDs = '.var_export($this->aTeamIDs, true));
			$oRoundGenerator = new RoundGenerator($this->aTeamIDs, $this->aAllreadyPlayedMatches, $this->iCurrentRound);
			$this->aPossibleMatches = $oRoundGenerator->execute();
		}
		else
		{
			Monolog::getInstance()->AddError('something went wrong, the poule only contains one team. $aTeams = ' . var_export($this->aTeams, true));
			exit;
		}

		if(count($this->aPossibleMatches) > 0)
		{
			//Remove all bye team matches
			$this->removeByeTeamMatches();
			
			// add found matches here, be alert that the matches array just contains team id's and not any other info about a team
			//$this->addMatches($pdo, $matches, $poule);
		}
		else
		{
			Monolog::getInstance()->AddAlert('something went wrong, no new matches were generated. $aPossibleMatches = ' . var_export($this->aPossibleMatches, true));
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
		$stmt->bindParam(":poule", $poule);
		$stmt->execute();
		
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	private function addByeTeam()
	{
		if($this->iCurrentRound != 0)
		{
			$this->aTeams[] = array("id" => "10000000", "matches_played" => $this->iCurrentRound - 1);
		}
		else
		{
			$this->aTeams[] = array("id" => "10000000", "matches_played" => 0);
		}
	}
	
	private function addByeTeamMatches()
	{
		//nog aanpassen dat id van byeTeam hier hetzelfde is als het byeteam? = nu -1 en 10000000..???
		foreach($this->aTeams as $aTeam)
		{
			if($aTeam["matches_played"] < $this->iCurrentRound && $aTeam["id"] != 10000000) 
			{
				array_push($this->aAllreadyPlayedMatches, array("team1" => $aTeam["id"], "team2" => "-1"));
			}
		}
	}

	private function removeByeTeamMatches()
	{
		foreach($this->aPossibleMatches as $sKey => $aPossibleMatch)
		{
			if(in_array("10000000", $aPossibleMatch))
			{
			 	unset($this->aPossibleMatches[$sKey]);
			}
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
	
	private function findNextRound()
	{
		$i = 0;
		foreach($this->aTeams as $aTeam) {
			if($aTeam["matches_played"] > $i) $i = $aTeam["matches_played"];
		}
		
		return $i;
	}
}