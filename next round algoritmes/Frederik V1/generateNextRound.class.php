<?php

class generateNextRound{

	private $aTeams = array();
	private $aPlayedMatches = array();
	private $iCurrentRound;
	private $iNumTeams;

	private $aNextMatches = array();
	private $iNextRoundIterator;
	private $aMatchedUp = array();

	private $aTeamsLeft = array();

	private $aValidNewRounds = array();

	public function generateNextRound($aTeams, $aPlayedMatches, $iCurrentRound)
	{
		//set input values
		$this->aTeams = $aTeams;
		$this->aPlayedMatches = $aPlayedMatches;
		$this->iCurrentRound = $iCurrentRound;
		$this->iNumTeams = count($aTeams);
	}

	public function execute()
	{
		if($this->iCurrentRound == 0)
		{
			$this->GenerateFirstRound();
			return $this->aNextMatches;
		}
		else
		{
			//generate possible new rounds
			$this->GeneratePossibleNewRounds();
			Monolog::getInstance()->addDebug('$this->aNextMatches = '.var_export($this->aNextMatches, true));

			//check for valid new rounds
			$this->CheckForValidNewRounds();
			Monolog::getInstance()->addDebug('$this->aValidNewRounds = '.var_export($this->aValidNewRounds, true));
			echo 'aantal mogelijke volgende rondes = '.count($this->aValidNewRounds).PHP_EOL;

			//calculate total penalty for possible new rounds
			//sort new possible rounds by penalty desc.
			//pick new round with lowest penalty
			//check if deadlock check is needed
				//do deadlock check if needed
					//if deadlock is detected, dispose this possible new round and pick the next one, check if deadlock check is needed, etc..

			//no deadlock detected or no need to check? - return new round
			echo 'einde!';
			exit;
		}
	}

	private function GenerateFirstRound()
	{
		for($i=0; $i < $this->iNumTeams; $i++)
		{
			if($i % 2 == false)
			{
				$this->aNextMatches[] = array("team1" => $this->aTeams[$i]["id"], "team2" => $this->aTeams[$i + 1]["id"]);
			}
		}
	}

	private function GeneratePossibleNewRounds()
	{
		$this->iNextRoundIterator = 0;

		for($i=0; $i < $this->iNumTeams - 1; $i++)
		{
			//add a new set of possible new matches, so set the NextRoundIterator, in order to keep all already created new rounds
			$this->iNextRoundIterator = $this->iNextRoundIterator + 1;

			//start again, so empty array and start tracking already matched up teams again
			$this->aMatchedUp = array();

			if($this->matchUpTeam(0, $i + 1) == true)
			{
				for($j=0; $j < $this->iNumTeams - 3; $j++)
				{
					//add a new set of possible new matches, so set the NextRoundIterator, in order to keep all already created new rounds
					$this->iNextRoundIterator = $this->iNextRoundIterator + 1;

					//start again, so empty array and start tracking already matched up teams again
					$this->aMatchedUp = array();

					$this->matchUpTeam(0, $i + 1);
					$iNotLocked = $this->findNotLocked(0, $i + 1);
					if($j == 0)
					{
						$this->aTeamsLeft = array();
						$this->aTeamsLeft = $this->findTeamsLeft(0, $i + 1, $iNotLocked);
					}
					else
					{
						$iFirstTeam = array_shift($this->aTeamsLeft);
						$this->aTeamsLeft[] = $iFirstTeam;
					}
					
					$this->matchUpTeam($iNotLocked, $this->aTeamsLeft[0]);
					for($k=0; $k < $this->iNumTeams - 4; $k++)
					{
						if($k % 2 == false)
						{
							$this->matchUpTeam($this->aTeamsLeft[$k + 1], $this->aTeamsLeft[$k + 2]);
						}
					}
				}
			}
		}
	}

	private function findNotLocked($first, $second)
	{
		for($i=0; $i < $this->iNumTeams; $i++)
		{
			if($i != $first && $i != $second)
			{
				return $i;
			}
		}
	}

	private function findTeamsLeft($first, $second, $third)
	{
		$aTeamsLeft = array();
		for($i = 0; $i < $this->iNumTeams; $i++)
		{
			if($i != $first && $i != $second && $i != $third)
			{
				$aTeamsLeft[] = $i;
			}
		}
		return $aTeamsLeft;
	}

	private function matchUpTeam($iTeamID, $iTeamIDOpponent)
	{
		//a team can't play against his self
		if($iTeamID != $iTeamIDOpponent)
		{
			if($this->teamAlreadyMatchedUp($iTeamID) == false)
			{
				if($this->matchIsValid($iTeamID, $iTeamIDOpponent) == true)
				{
					//Match is possible, set it!
					$aMatch = array("team1" => $this->aTeams[$iTeamID]["id"], "team2" => $this->aTeams[$iTeamIDOpponent]["id"]);
					$this->aNextMatches[$this->iNextRoundIterator][] = $aMatch;
					$this->aMatchedUp[] = $this->aTeams[$iTeamID]["id"];
					$this->aMatchedUp[] = $this->aTeams[$iTeamIDOpponent]["id"];
					$bResult = true;
				}
				else
				{
					//The match isn't possible between these opponents, but we can still try to match with an other opponent
					/*
					$iTeamIDNewOpponent = $iTeamIDOpponent + 1;
					if($iTeamIDNewOpponent < $this->iNumTeams)
					{
						$bResult = $this->matchUpTeam($iTeamID, $iTeamIDNewOpponent);
					}
					*/
					$bResult = false;
				}
			}
			else
			{
				$bResult = false;
			}
		}
		else
		{
			$bResult = false;
		}

		return $bResult;
	}

	private function teamAlreadyMatchedUp($iTeamID)
	{
        if(in_array($this->aTeams[$iTeamID]["id"], $this->aMatchedUp) == true)
        {
            $bResult = true;
        }
        else
        {
        	$bResult = false;
        }

	    return $bResult;		
	}

	private function matchIsValid($iTeamID, $iTeamIDOpponent)
	{
		if($this->teamAlreadyMatchedUp($iTeamIDOpponent) == true)
		{
			return false;
		}

		if($this->matchAlreadyPlayed($iTeamID, $iTeamIDOpponent) == true)
		{
			return false;
		}

		return true;
	}

	private function matchAlreadyPlayed($iTeamID, $iTeamIDOpponent)
	{
		foreach ($this->aPlayedMatches as $aPlayedMatch)
		{
			if(count(array_intersect($aPlayedMatch, array("0" => $this->aTeams[$iTeamID]["id"], $this->aTeams[$iTeamIDOpponent]["id"]))) == 2)
			{
				return true;
			}
		}

		return false;
	}

	private function CheckForValidNewRounds()
	{
		foreach($this->aNextMatches as $aNextMatches)
		{
			if(count($aNextMatches) == ($this->iNumTeams / 2))
			{
				$this->aValidNewRounds[] = $aNextMatches;
			}
		}
	}
}