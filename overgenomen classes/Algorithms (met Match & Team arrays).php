<?php
/// <summary>
/// Bevat de methodes voor algoritmes die de rest van het programma kan aanroepen.
/// Controleert ook de gegeven invoer en sorteert deze voor de algoritmes.
/// </summary>
class Algorithms
{
	/// <summary>
	/// Genereert, gegeven een lijst van teams en een ronde nummer, de best mogelijke combinatie van wedstrijden
	/// Hierbij wordt gelet op de afstand op de ladder tussen de teams, speelt elk team maximaal 1 keer niet en 
	/// speelt elk team maximaal 1 keer tegen elk ander team.
	/// </summary>
	/// <param name="$aInputTeams">De lijst van teams die tegen elkaar spelen</param>
	/// <param name="$iRound">Het ronde nummer</param>
	/// <param name="$aInputPlayedgames">De wedstrijden die al gespeeld zijn in de voorgaande rondes</param>
	/// <param name="$aTeamNotPlaying">Het team dat niet ingeeld is, of null als deze niet bestaat.</param>
	/// <returns>De best mogelijke lijst van wedstrijden voor deze ronde of null bij een ongeldige invoer.</returns>
	public static function GenerateLadder(array $aInputTeams, int $iRound, array $aInputPlayedgames, $aTeamNotPlaying)
	{
		//Controleer data
		if (!$this->VerifyLadderData($aInputTeams, $aInputPlayedgames, $iRound))
		{
			$aTeamNotPlaying = null;
			//Logger.Write("Algoritmes", string.Format("Ongeldige ladder invoer van ronde {0}, met {1} $aTeams en {2} gespeelde wedstrijden.", $iRound, $aInputTeams.Count, $aInputPlayedgames.Count));
			return null;
		}

		$aTeams = $aInputTeams;
		$aPlayedGames = $aInputPlayedgames;

		//Voeg een mogelijk ByeTeam toe.
		$bHasBye = false;

		if ((count($aTeams) & 1) != 0)
		{
			$aTeams[] = array("id" => -1);
			$bHasBye = true;
		}

		//Maak de graaf met mogelijke wedstrijden.
		$oPossibilities = Graph::Create(count($aTeams));
		$oPossibilities->Fill();

		foreach ($aPlayedGames as $aPlayedMatch)
		{
			$oPossibilities->RemoveUndirectedEdge($this->indexOfTeam($aTeams, $aPlayedMatch["team1"]), $this->indexOfTeam($aTeams, $oPlayedMatch["team2"]));
		}

		//Een team kan niet tegen zichzelf spelen.
		for ($i = 0; $i < count($aTeams) - 1; $i++)
		{
			$oPossibilities->RemoveUndirectedEdge($i, $i);
		}

		//Verwijder alle al gespeelde wedstrijden met bye$aTeams
		if ($bHasBye)
		{
			foreach ($aTeams as $aTeam)
			{
				$iTeamIndex = $this->indexOfTeam($aTeams, $aTeam);
				$iPlayedmatches = 0;

				//Tel het aantal gespeelde matches, inclusief tegen inactieve teams
				foreach ($aPlayedGames as $aMatch)
					if ($aMatch["team1"] == $oTeam["id"] || $aMatch["team2"] == $oTeam["id"])
						$iPlayedmatches++;

				if ($iPlayedmatches != $iRound - 1)
					$oPossibilities->RemoveUndirectedEdge(count($aTeams) - 1, $iTeamIndex);

			}
		}
		//Bepaalt vanaf welke index alle volgende $aTeams inactief of bye zijn.
		$iInactiveIndex = count($aTeams);

		for ($i = 0; $i < count($aTeams); $i++)
		{
			if ($aTeams[$i]["IsInOperative"] || $aTeams[$i]["id"] == -1)
			{
				$iInactiveIndex = $i;
				break;
			}
		}

		//Zet alle onderlinge mogelijkheden tussen inactieve en bye$aTeams op true.
		for ($i = $iInactiveIndex; $i < count($aTeams); $i++)
		{
			for ($j = $i + 1; $j < count($aTeams); $j++)
			{
				$oPossibilities->AddUndirectedEdge($i, $j);
			}
		}

		//Genereer de nieuwe wedstrijdindeling.
		$oLadder = new Ladder();
		//Logger.Write("Algoritmes", string.Format("Begin ladder algoritme in ronde {0}, met {1} $aTeams en {2} gespeelde wedstrijden.", $iRound, $aInputTeams.Count, $aInputPlayedgames.Count));
		$aResult = $oLadder->GenerateLadder($aTeams, $oPossibilities, $iRound, $iInactiveIndex);

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
			$iKeyToRemove = array_search($aResult, $aToRemove);
			unset($aResult[$iKeyToRemove]);
			// $aResult->Remove($aToRemove["id"]);
			
			//Als het andere team actief is, dan telt het als een halve wedstrijd.
			if ($aTeamNotPlaying["IsInOperative"])
				$aTeamNotPlaying = null;
		}

		return $aResult;
	}

	/// <summary>
	/// Zoekt de index van een team in een lijst.
	/// </summary>
	/// <param name="$aTeams">De array van teams om in te zoeken.</param>
	/// <param name="$oTeamToCheck">Het team om de index van te zoeken.</param>
	/// <returns>De index van het team, of -1 bij niet gevonden.</returns>
	public static function indexOfTeam(array $aTeams, $aTeamToCheck)
	{
		for ($i = 0; $i < count($aTeams); $i++)
		{
			if ($aTeams[$i]["id"] == $oTeamToCheck["id"])
				return $i;
		}

		return -1;
	}

	/// <summary>
	/// Controleert of het rondenummer van de ladder mogelijk is en alle teams uit de wedstrijden
	/// in de lijst van teams zitten.
	/// </summary>
	/// <param name="$aTeams">De $aTeams.</param>
	/// <param name="$aPlayedmatches">De gespeelde westrijden.</param>
	/// <param name="$iRound">Het rondenummer.</param>
	/// <returns>Een boolean die de correctheid vand e data aangeeft.</returns>
	public static function VerifyLadderData(array $aTeams, array $aPlayedMatches, int $iRound)
	{
		//Rond af naar volgende even waarde.
		$iTeamAmountToCheck = count($aTeams) + (count($aTeams) & 1);
        //Ongeldig rondenummer
		if ($iRound < 1 || $iRound > $iTeamAmountToCheck - 1)
			return false;

        //Er komen teams voor in de gespeelde wedstrijden die niet bestaan
        foreach ($aPlayedmatches as $aPlayedMatch )
        {
            if ($this->indexOfTeam($aTeams,$aPlayedMatch["team1"]) == -1 || $this->indexOfTeam($aTeams,$oPlayedMatch["team2"]) == -1)
                return false;
        }

	    return true;
	}
}