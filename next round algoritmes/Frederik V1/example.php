<?php
require_once("generateNextRound.class.php");
require_once(dirname(__FILE__) . "/../../php/classes/class.initialiseISBT.php");
new initialiseISBT();

echo "<pre>";
$oExample = new Example(2);

$aPossibleMatches = $oExample->getPossibleMatches();
var_dump($aPossibleMatches);

class Example
{
	private $iPouleId;
	private $iByeTeamId;
	
	private $iCurrentRound;
	private $aTeams = array();
	private $aTeamIDs = array();
	private $aAllreadyPlayedMatches = array();
	
	private $aBestNextMatches = array();
	
	public function Example($p_iPouleId)
	{
		$this->iPouleId = $p_iPouleId;
		$this->iByeTeamId = 999;

		try
		{
	        $pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD, array(PDO::ATTR_PERSISTENT => true));
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch(PDOException $e)
        {
            Monolog::getInstance()->addAlert('Error initialising PDO object, PDOException: ' . var_export($e, true));
        }
		
		$this->aTeams = $this->getTeams($pdo, $this->iPouleId);
		$this->iCurrentRound = $this->findCurrentRound();
		$this->aAllreadyPlayedMatches = $this->getAllreadyPlayedMatches($pdo, $this->iPouleId);
		
		//DEV
		Monolog::getInstance()->addDebug('$this->aTeams = '.var_export($this->aTeams, true));
		Monolog::getInstance()->addDebug('$$this->iCurrentRound = '.$this->iCurrentRound);
		Monolog::getInstance()->addDebug('$this->aAllreadyPlayedMatches = '.var_export($this->aAllreadyPlayedMatches, true));
		
		if(count($this->aTeams) % 2)
		{
			//Add bye team and bye team matches if there is an uneven number of teams
			$this->addByeTeam();
			$this->addByeTeamMatches();
			Monolog::getInstance()->addDebug('added bye team and matches');
			Monolog::getInstance()->addDebug('$this->aTeams = '.var_export($this->aTeams, true));
			Monolog::getInstance()->addDebug('$this->aAllreadyPlayedMatches = '.var_export($this->aAllreadyPlayedMatches, true));
		}

		if(count($this->aTeams) > 1)
		{
			$this->setTeamIDs();
			Monolog::getInstance()->addDebug('$this->aTeamIDs = '.var_export($this->aTeamIDs, true));
			$oNextRound = new generateNextRound($this->aTeamIDs, $this->aAllreadyPlayedMatches, $this->iCurrentRound);
			$this->aBestNextMatches = $oNextRound->execute();
			Monolog::getInstance()->addDebug('$this->aBestNextMatches = '.var_export($this->aBestNextMatches, true));
			exit;
		}
		else
		{
			Monolog::getInstance()->AddError('something went wrong, the poule only contains one team. $aTeams = ' . var_export($this->aTeams, true));
			exit;
		}

		$iNumberOfMatches = count($this->aTeamIDs) / 2;
		if(count($this->aBestNextMatches) == $iNumberOfMatches)
		{
			//Remove all bye team matches
			$this->removeByeTeamMatches();
			
			// add found matches here, be alert that the matches array just contains team id's and not any other info about a team
			//$this->addMatches($pdo, $matches, $poule);
		}
		else
		{
			Monolog::getInstance()->AddAlert('something went wrong, no or not enough new matches were generated. $aBestNextMatches = ' . var_export($this->aBestNextMatches, true));

			//genereer alsnog wedstrijden, dikke faal, mag hier gewoon niet komen
		}	
	}
    
	private function getTeams($pdo, $poule)
	{
		try
		{
			$sql = "SELECT `id`, `matches_played`
					FROM `team`
					WHERE `poule` = :poule
					ORDER BY `average_sets_won` DESC, `points_balance` DESC";
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(":poule", $poule);
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e)
        {
            Monolog::getInstance()->addAlert('Error @ getTeams, PDOException: ' . var_export($e, true));
        }
        return $result;
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

	private function getAllreadyPlayedMatches($pdo, $pouleId)
	{
		try
        {
            $sql = "SELECT `team1`, `team2`
                    FROM `match` 
                    INNER JOIN `team` `team1` ON(team1.id = match.team1) 
                    INNER JOIN `poule` ON(poule.id = team1.poule)
                    WHERE poule.id = :poule";

            $stmt = $pdo->prepare($sql);
			$stmt->bindParam(":poule", $pouleId);
            $stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            Monolog::getInstance()->addAlert('Error selecting already played matches, PDOException: ' . var_export($e, true));
        }
        return $result;
	}
	
	private function addByeTeam()
	{
		if($this->iCurrentRound != 0)
		{
			$this->aTeams[] = array("id" => $this->iByeTeamId, "matches_played" => $this->iCurrentRound - 1);
		}
		else
		{
			$this->aTeams[] = array("id" => $this->iByeTeamId, "matches_played" => 0);
		}
	}
	
	private function addByeTeamMatches()
	{
		foreach($this->aTeams as $aTeam)
		{
			if($aTeam["matches_played"] < $this->iCurrentRound && $aTeam["id"] != $this->iByeTeamId) 
			{
				array_push($this->aAllreadyPlayedMatches, array("team1" => $aTeam["id"], "team2" => $this->iByeTeamId));
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
	
	private function setTeamIDs()
	{
		foreach($this->aTeams as $aTeam)
		{
			$this->aTeamIDs[]["id"] = $aTeam["id"];
		}
	}

	public function getPossibleMatches()
	{
		return $this->aPossibleMatches;
	}
}