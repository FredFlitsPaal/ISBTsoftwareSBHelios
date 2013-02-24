<?php

/**
 * Bevat de methodes voor algoritmes die de rest van het programma kan aanroepen.
 * Controleert ook de gegeven invoer en sorteert deze voor de algoritmes.
 */
class LadderGenerator
{
    private $bHasBye = false;
    private $aTeams;
    private $iRound;
    private $aPlayedMatches;
    private $oGraph;
        
    /**
     * Genereert, gegeven een lijst van teams en een ronde nummer, de best mogelijke combinatie van wedstrijden
     * Hierbij wordt gelet op de afstand op de ladder tussen de teams, speelt elk team maximaal 1 keer niet en 
     * speelt elk team maximaal 1 keer tegen elk ander team.
     * 
     * @param array $aInputTeams De lijst van teams die tegen elkaar spelen
     * @param int $iRound Het rondenummer
     * @param array $aInputPlayedGames De wedstrijden die al gespeeld zijn
     * @return array De best mogelijke lijst van wedstrijden voor deze ronde of null bij een ongeldige invoer.
     */
    public function LadderGenerator($aTeams, $iRound, $aPlayedMatches)
    {
        $this->aTeams = $aTeams;
        $this->iRound = $iRound;
        $this->aPlayedMatches = $aPlayedMatches;
    }
    
	public function generate(/*, $aTeamNotPlaying*/)
	{   
        // Voegt een bye team toe indien nodig
        $this->addByeTeam();

		// Controleer of de data juist is
		if (!$this->hasValidLadderData())
		{
            // LOG
			return array();
		}

        $this->oGraph = $this->createGraph();
        
        // Verwijder al gespeelde wedstrijden uit de graaf
        $this->removePlayedMatches();
        
        // Verwijder wedstrijden tegen zichzelf
        $this->removeMatchesWithEqualTeams();
		
		// Verwijder alle al gespeelde wedstrijden met bye$aTeams
		//$this->removeMatchesWithByeTeams();
        
		//Bepaalt vanaf welke index alle volgende $aTeams inactief of bye zijn.
		$iInactiveIndex = count($this->aTeams);

		for ($i = 0; $i < count($this->aTeams); $i++)
		{
			if ($this->aTeams[$i]["IsInOperative"] || $this->aTeams[$i]["id"] == -1)
			{
				$iInactiveIndex = $i;
				break;
			}
		}

		//Zet alle onderlinge mogelijkheden tussen inactieve en bye$aTeams op true.
//		for ($i = $iInactiveIndex; $i < count($aTeams); $i++)
//		{
//			for ($j = $i + 1; $j < count($aTeams); $j++)
//			{
//				$oGraph->AddUndirectedEdge($i, $j);
//			}
//		}

		//Genereer de nieuwe wedstrijdindeling.
		// LOG
        $oLadder = new Ladder($this->aTeams, $this->oGraph, $this->iRound, $iInactiveIndex);

        return $oLadder->generate();

		/*
		//Verwijdert bye team en geeft aan welk team niet speelt.
		$aTeamNotPlaying = null;

		if ($bHasBye)
		{
			$aToRemove = null;

			//Zoek de wedstrijd met het byeteam.
			foreach ($aResult as $aMatch)
			{
				if ($aMatch["team1"] == -1 || $aMatch["team2"] == -1)
				{
					//Dit is de wedstrijd met het bye team
					$aToRemove = $aMatch;
					break;
				}
			}
			//Zet het team dat tegen de bye heeft gespeeld.
			if ($aToRemove["team1"] != -1)
			{
				$aTeamNotPlaying = $aToRemove["team1"];
			}
			else if ($aToRemove["team2"] != -1)
			{
				$aTeamNotPlaying = $aToRemove["team2"];
			}

// !!!!!!!!!!!!!!!! MOET GECONTROLEERD WORDEN -- Kijken of het overeenkomt met het Ladder object..
			$iKeyToRemove = array_search($aToRemove, $aResult);
			unset($aResult[$iKeyToRemove]);
			// $aResult->Remove($aToRemove["id"]);
			
			//Als het andere team actief is, dan telt het als een halve wedstrijd.
			if ($aTeamNotPlaying["IsInOperative"])
				$aTeamNotPlaying = null;
		}
		*/
	}
    
    private function createGraph()
    {		
        //Maak de graaf met mogelijke wedstrijden.
		$oGraph = Graph::Create(count($this->aTeams));
		$oGraph->Fill();
        
        return $oGraph;
    }

    private function removePlayedMatches()
    {
		foreach ($this->aPlayedMatches as $aPlayedMatch)
		{
			$this->oGraph->RemoveUndirectedEdge($this->getIndexOfTeam($aPlayedMatch["team1"]), $this->indexOfTeam($aPlayedMatch["team2"]));
		}
    }
    
    private function removeMatchesWithEqualTeams()
    {
		// Een team kan niet tegen zichzelf spelen.
		for ($i = 0; $i < count($this->aTeams) - 1; $i++)
		{
			$this->oGraph->RemoveUndirectedEdge($i, $i);
		}
    }
    
    private function removeMatchesWithByeTeams()
    {
        if($this->bHasBye)
		{
			foreach ($this->aTeams as $aTeam)
			{
				$iTeamIndex = $this->getIndexOfTeam($aTeam);
				$iPlayedmatches = 0;

				// Tel het aantal gespeelde matches, inclusief tegen inactieve teams
				foreach ($this->aPlayedMatches as $aMatch)
                {
					if ($aMatch["team1"] == $aTeam["id"] || $aMatch["team2"] == $aTeam["id"])
                    {
						$iPlayedmatches++;
                    }
                }

				if($iPlayedmatches != $this->iRound - 1)
                {
					$this->oGraph->RemoveUndirectedEdge(count($this->aTeams) - 1, $iTeamIndex);
                }
			}
		}
    }
    
	/// <summary>
	/// Zoekt de index van een team in een lijst.
	/// </summary>
	/// <param name="$aTeams">De array van teams om in te zoeken.</param>
	/// <param name="$oTeamToCheck">Het team om de index van te zoeken.</param>
	/// <returns>De index van het team, of -1 bij niet gevonden.</returns>
	public function getIndexOfTeam($iTeamToCheck)
	{
		for ($i = 0; $i < count($this->aTeams); $i++)
		{
			if ($this->aTeams[$i]["id"] == $iTeamToCheck)
			{
				return $i;
			}
		}
        
        // LOG
		return -1;
	}

    private function addByeTeam()
    {
    	if ((count($this->aTeams) & 1) != 0)
		{
			$this->aTeams[] = array("id" => "-1", "IsInOperative" => "0");
			$this->bHasBye = true;
		}
    }
    
	/// <summary>
	/// Controleert of het rondenummer van de ladder mogelijk is en alle teams uit de wedstrijden
	/// in de lijst van teams zitten.
	/// </summary>
	/// <param name="$aTeams">De $aTeams.</param>
	/// <param name="$aPlayedmatches">De gespeelde westrijden.</param>
	/// <param name="$iRound">Het rondenummer.</param>
	/// <returns>Een boolean die de correctheid vand e data aangeeft.</returns>
	private function hasValidLadderData()
	{
		//Rond af naar volgende even waarde.
		$iTeamAmountToCheck = count($this->aTeams) + (count($this->aTeams) & 1);
        
        // Ongeldig rondenummer
		if ($this->iRound < 1 || $this->iRound > $iTeamAmountToCheck - 1)
		{
			return false;
		}

        return $this->hasValidMatches();
	}
    
    private function hasValidMatches()
    {
        // Er komen teams voor in de gespeelde wedstrijden die niet bestaan
        foreach ($this->aPlayedMatches as $aPlayedMatch)
        {
            if (getIndexOfTeam($aPlayedMatch["team1"]) == -1 || getIndexOfTeam($aPlayedMatch["team2"]) == -1)
            {
            	return false;
            }
        }

	    return true;
    }
}