<?php

class generateNextRound{

	private $aTeams = array();
	private $aPlayedMatches = array();
	private $iCurrentRound;
	private $iNumTeams;

	private $aNextMatches = array();
	private $iNextRoundIterator;
	private $aMatchedUp = array();

	//private $aPossibleNewRounds = array();

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
		for($i=0; $i < $this->iNumTeams; $i++)
		{
			//add a new set of possible new matches, so set the NextRoundIterator, in order to keep all already created new rounds
			$this->iNextRoundIterator = $i;

			//start again, so empty array and start tracking already matched up teams again
			$this->aMatchedUp = array();

			//try all possibilities, so shift the order of the teams every time
			$aFirstTeam = array_shift($this->aTeams);
			$this->aTeams[] = $aFirstTeam;

			//ga alle teams af per nieuwe set
			for($j=0; $j < $this->iNumTeams; $j++)
			{
				$k = $j + 1;
				if($k < $this->iNumTeams)
				{
					//when returned false, this means the round it was building wasn't possible at a certain level
					if($this->matchUpTeam($j, $k) == false)
					{
						/*
						//dit is omdat de laatste wedstrijd de boel kan blokkeren. Dit kan ook een andere reden hebben, dus poppen doen we maar 1 keer per mogelijke volgende ronde
						$aLastFoundMatch = array_pop($this->aNextMatches[$this->iNextRoundIterator]);

						//check of array_pop geen NULL opleverde, want als dat zo is, dan waren er al geen wedstrijden gemaakt en heeft doorgaan geen zin
						if($aLastFoundMatch != NULL)
						{
							//haal laatste twee teams ook uit de array aMatchedUp, laatste twee teams = result van array_pop, maar zijn ook gewoon de laatste twee in de array
							array_pop($this->aMatchedUp);
							array_pop($this->aMatchedUp);

							//roep matchUpTeam opnieuw aan met een tegenstander verder weg
							$l = $k + 1;
							if($l < $this->iNumTeams)
							{
								$this->matchUpTeam($j, $l);
								//weer niet gelukt, break ???
								//wel gelukt, dan verder gaan ???
							}
						}
						else
						{
							break;
						}
						*/
						for($l=2; $l < $this->iNumTeams; $l++)
						{ 

							$this->matchUpTeam($j, $l);
						}
					}
				}
			}
		}
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
}