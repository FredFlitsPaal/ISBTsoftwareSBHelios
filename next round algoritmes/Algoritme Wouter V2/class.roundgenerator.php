<?php

// TODO! Built in byes

class RoundGenerator {

	const DeadlockRiskLevel = 2;

	public $aPossibleMatches = array();
	public $aPossibleDeadlockMatches = array();
	
	public $aPlayedMatches = array();
	public $aFictionalDeadlockPlayedMatches = array();
	
	public $aTeams = array();
	private $round;
	
	//private $recurseMatchResult = false;
	private $forbiddenDeadlockMatches = array();

	public function __construct($aTeams, $aPlayedMatches, $iRound)
	{
		$this->round = $iRound;
		
		$this->aPlayedMatches = $aPlayedMatches;
		$this->aTeams = $aTeams;
	}
	
	public function execute($ignoreMatchesAlreadyPlayed = false, $deadlockCheck = false, $deadlockFailed = false)
	{
		$this->aPossibleMatches = array();

		// Generate possible new round
		$i_NumTeams = count($this->aTeams);

		for($i = 0; $i < $i_NumTeams; $i++)
		{
		    $j = $i + 1;
		    if($this->recurseMatch($i, $j, $ignoreMatchesAlreadyPlayed) == false) {
		    	// recurseMatch returned false, this means the round it was building wasn't possible at a certain level
		    	
		    	// Remove a higher level and choose an lower opponent, then recheck if the round is possible
		    	//could return NULL
		    	$aLastMatchPossibleMatches = array_pop($this->aPossibleMatches);
		    	if(empty($this->aPossibleMatches) == true)
		    	{
		    		if($deadlockCheck == true && $deadlockFailed == false) {
			    		// The chosen next round will induce a deadlock, so prevent it!
			    		//hier komt ie nooit toch?? - omdat bovenstaande op regel 45 nooit empty zal zijn als $ignoreMatchesAlreadyPlayed = true; 
			    		//oke hij komt hier wel bij de deadlock check indd..
						$this->aPlayedMatches = $this->aFictionalDeadlockPlayedMatches;
						
						$this->forbiddenDeadlockMatches = array();
						foreach($this->aPossibleDeadlockMatches as $aDeadlockMatch) {
							array_push($this->forbiddenDeadlockMatches, array($aDeadlockMatch["team1"], $aDeadlockMatch["team2"]));
						}
						// You have to allow one of the forbidden matches, because otherwise it isn't possible to generate a new round anymore
						array_shift($this->forbiddenDeadlockMatches);
						
				    	$this->execute(false, true, true);
			    	}
			    	else
			    	{
			    		// in principe kan hij hier toch nooit komen als je twee rondes doorrekend naar voren??
			    		// Unavoidable deadlock!!!
			    		// It isn't possible to create a round without duplicate matches, so duplicate one WITH duplicate matches
				    	$this->execute(true, true);
			    	}

			    }
			    else
			    {
			    	// stel 3 tegen 4 en 3 tegen 5 heeft al gespeeld, dan stopt ie dus gewoon?
			    	$this->recurseMatch($aLastMatchPossibleMatches["team1"], $aLastMatchPossibleMatches["team2"] + 1, $ignoreMatchesAlreadyPlayed);
			    }
		    	//waarom?
		    	$i--;
		    }
		}
		
		if($this->isRoundBeforeDeadlockRisk() == true && $deadlockCheck == false) {
			// Check if a deadlock occures in the next round when the choosen round will be played
			$this->checkIfGeneratedRoundLeadsToDeadlock();
		}
		
		return $this->aPossibleMatches;
	}
	
	private function recurseMatch($currentTeamId, $opponentTeamId, $ignoreMatchesAlreadyPlayed = false)
	{
		//moeten we hier niet alsnog checken??, aangezien een team nu miss wel een wedstrijd heeft maar niet de best mogelijke???

		// check If current team is already in the possibleMatches array and check if array key penalty doesn't exist
	    if($this->idExistsInPossibleMatches($this->aTeams[$currentTeamId]["id"]) == false)
	    {
	    	// If the array id of the opponent exceeds the number of teams, reset it to zero
	    	
	    	//om onderstaande reden id van het byeteam overal gelijk trekken, m.a.w. aantal teams + 1 = id bye team
	    	//hier kunnen we ervoor zorgen dat byes uitgedeeld worden bij voorkeur aan het laagste team door het bye team een id te geven dat 1 hoger is dan het laatste team...
			//in dit geval is de hoogste waarschijnlijkheid van een bye tegen het hoogste team
			if($opponentTeamId >= count($this->aTeams))
			{
				$opponentTeamId = 0;
			}
			

	        // Penalty based on distance teams, this is absolute to prevent negative values
	        $iPenalty =  abs($opponentTeamId - $currentTeamId);
	        $aMatchOption = array("team1" => $this->aTeams[$currentTeamId]["id"], "team2" => $this->aTeams[$opponentTeamId]["id"], "penalty" => $iPenalty);

			if($this->matchIsValid($aMatchOption, $currentTeamId, $opponentTeamId) == true || $ignoreMatchesAlreadyPlayed == true)
			{
				// Match is possible, set it
	            $this->aPossibleMatches[] = $aMatchOption;
	            $brecurseMatchResult = true;
		    }
		    else
		    {
		    	if($this->aTeams[$currentTeamId]["id"] != $this->aTeams[$opponentTeamId]["id"])
		    	{
		    		// The match isn't possible between these opponents, but we can still try to match with an other opponent
		            $this->recurseMatch($currentTeamId, $opponentTeamId + 1, $ignoreMatchesAlreadyPlayed);
		        }
		        else
		        {
			        // a team can't play against his self, this check could be done first to optimize algorithm...
		            $brecurseMatchResult = false;
		        }
			}
	    }
	    else
	    {
	    	$brecurseMatchResult = false;
	    }
	    
	    //Jippie, match set!
	    return $brecurseMatchResult;
	}
	
	private function matchIsValid($aMatchOption, $currentTeamId, $opponentTeamId)
	{
		if($this->idExistsInPossibleMatches($this->aTeams[$opponentTeamId]["id"])) return false;
		// This one is important because it stops the loop when the id returns to itself, so it recurses the complete array just once
		if($this->aTeams[$currentTeamId]["id"] == $this->aTeams[$opponentTeamId]["id"]) return false;
		
		if($this->matchExistsInPlayedMatches($aMatchOption)) return false;
		if($this->matchExistsInForbiddenMatches($aMatchOption)) return false;
		
		return true;
	}
	
	private function matchExistsInPlayedMatches($aMatch)
	{
		unset($aMatch["penalty"]);
		
	    foreach($this->aPlayedMatches as $aPlayedMatch)
	    {
	    	//2 = 2 if true
	        if(count(array_intersect($aPlayedMatch, $aMatch)) == count($aMatch))
	        {
	            // ID already in possible aPlayedMatches array
	            return true;
	        }
	    }
	    
	    return false;
	}

	private function matchExistsInForbiddenMatches($aMatch)
	{
		unset($aMatch["penalty"]);
		
	    foreach($this->forbiddenDeadlockMatches as $aForbiddenMatch)
	    {
	    	//2 = 2 if true
	        if(count(array_intersect($aForbiddenMatch, $aMatch)) == count($aMatch))
	        {
	            // ID already in forbiddenDeadlockMatches array
	            return true;
	        }
	    }
	    
	    return false;
	}
	
	private function idExistsInPossibleMatches($id)
	{
	    $bResult = false;
	    foreach($this->aPossibleMatches as $aPossibleMatch)
	    {
	    	//?? penalty checken??
	        if(in_array($id, $aPossibleMatch) && array_search($id, $aPossibleMatch) != "penalty")
	        {
	            // ID already in possible matches array
	            $bResult = true;
	        }
	    }
	    return $bResult;
	}
	
	private function isRoundBeforeDeadlockRisk()
	{
		$iMaxRounds = count($this->aTeams) - 1;
		$iRoundsLeftAfterThisRound = $iMaxRounds - $this->round;
		
		if($iRoundsLeftAfterThisRound == self::DeadlockRiskLevel){
			// There is a risk to generate a deadlock this round, after this round you're save
			return true;
		}
	}
	
	//onderstaande kunnen we op deze manier waarsch. wel doen, echter snap ik het niet helemaal meer nu :-P
	//wat makkelijker / overzichtelijker is, is om hier niet weer twee hele rondes door te gaan rekenen, want dat kan ook, en dat gebeuerd nu,
	//maar om hier te kijken of er oneven eilandjes zijn, want als dat zo is dan kan het een deadlock zijn, maar hoeft niet.
	//als het zo kan zijn, dan zeggen we dat het een deadlock is...
	private function checkIfGeneratedRoundLeadsToDeadlock() {
		// Make backup of new generated round
		$this->aPossibleDeadlockMatches = $this->aPossibleMatches;
		$this->aFictionalDeadlockPlayedMatches = $this->aPlayedMatches;
		
		foreach($this->aPossibleMatches as $aPossibleMatch) {
			// Add possible matches to fictional previous matches
			array_push($this->aPlayedMatches, array($aPossibleMatch["team1"], $aPossibleMatch["team2"]));
			
			// Certain deadlock with this played matches
			//$this->aPlayedMatches = array(array(1,4), array(1,5), array(2,3), array(2,4), array(3,6), array(5,6), array(1,3), array(2,5), array(4,6));
		}

		// Create fictional next round
		$this->execute(false, true);
	}
}

/*
// aTeam may just contain team id's
$aTeams = array(array("id"=> "1"), array("id"=> "2"), array("id"=> "3"), array("id"=> "4"), array("id"=> "5"), array("id"=> "6"));
// aPlayedMatches contains team id's
// PreDeathlock (algorithm chooses correct): $aPlayedMatches = array(array("team1" => 1, "team2" => 2), array("team1" => 3, "team2" => 4), array("team1" => 5, "team2" => 6), array("team1" => 1, "team2" => 6), array("team1" => 2, "team2" => 3), array("team1" => 4, "team2" => 5));
//PreDeathlock (algorithm chooses wrong in first instance, but correct after deadlock check): 
$aPlayedMatches = array(array("team1" => 1, "team2" => 4), array("team1" => 1, "team2" => 6), array("team1" => 2, "team2" => 3), array("team1" => 2, "team2" => 5), array("team1" => 3, "team2" => 6), array("team1" => 4, "team2" => 5));

$oRoundGenerator = new RoundGenerator($aTeams, $aPlayedMatches, 3);
$aPossibleMatches = $oRoundGenerator->execute();

echo "<pre>";
var_dump($aPossibleMatches);
echo "</pre>";
*/