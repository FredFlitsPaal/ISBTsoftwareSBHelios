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
	
	private $recurseMatchResult = false;
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
		for($i = 0; $i < count($this->aTeams); $i++) {
		    $j = $i + 1;
		    if(!$this->recurseMatch($i, $j, $ignoreMatchesAlreadyPlayed)) {
		    	// recurseMatch returned false, this means the round it was building wasn't possible at a certain level
		    	
		    	// Remove a higher level and choose an lower opponent, then recheck if the round is possible
		    	$aLastMatchPossibleMatches = array_pop($this->aPossibleMatches);
		    	if(empty($this->aPossibleMatches)) {
		    		if($deadlockCheck && $deadlockFailed) {
		    			// Unavoidable deadlock!!!
			    		// It isn't possible to create a round without duplicate matches, so duplicate one WITH duplicate matches
				    	$this->execute(true, true);
			    	} else {
			    		// The chosen next round will induce a deadlock, so prevent it!
						$this->aPlayedMatches = $this->aFictionalDeadlockPlayedMatches;
						
						$this->forbiddenDeadlockMatches = array();
						foreach($this->aPossibleDeadlockMatches as $aDeadlockMatch) {
							array_push($this->forbiddenDeadlockMatches, array($aDeadlockMatch["team1"], $aDeadlockMatch["team2"]));
						}
						// You have to allow one of the forbidden matches, because otherwise it isn't possible to generate a new round anymore
						array_shift($this->forbiddenDeadlockMatches);
						
				    	$this->execute(false, true, true);
			    	}
			    } else {
			    	$this->recurseMatch($aLastMatchPossibleMatches["team1"], $aLastMatchPossibleMatches["team2"] + 1, $ignoreMatchesAlreadyPlayed);
			    }
		    	
		    	$i--;
		    }
		}
		
		if($this->isRoundBeforeDeadlockRisk() && !$deadlockCheck) {
			// Check if a deadlock occures in the next round when the choosen round will be played
			$this->checkIfGeneratedRoundLeadsToDeadlock();
		}
		
		return $this->aPossibleMatches;
	}
	
	private function recurseMatch($currentTeamId, $opponentTeamId, $ignoreMatchesAlreadyPlayed = false)
	{
		// If current team already in possibleMatches it doesn't have to be set anymore
	    if(!$this->idExistsInPossibleMatches($this->aTeams[$currentTeamId]["id"])) {
	    	// If the array id of the opponent exceeds the number of teams, reset it to nill
			if($opponentTeamId >= count($this->aTeams)) $opponentTeamId = 0;

	        // Penalty based on distance teams, this is absolute to prevent negative values
	        $iPenalty =  abs($opponentTeamId - $currentTeamId);
	        $aMatchOption = array("team1" => $this->aTeams[$currentTeamId]["id"], "team2" => $this->aTeams[$opponentTeamId]["id"], "penalty" => $iPenalty);

			if($this->matchIsValid($aMatchOption, $currentTeamId, $opponentTeamId) || $ignoreMatchesAlreadyPlayed == true) {
				// Match is possible, set it
	            $this->aPossibleMatches[] = $aMatchOption;
	            $this->recurseMatchResult = true;
		    } else {
		    	if($this->aTeams[$currentTeamId]["id"] != $this->aTeams[$opponentTeamId]["id"]) {
		    		// The match isn't possible between these opponents, but we can still try to match with an other opponent
		            $this->recurseMatch($currentTeamId, $opponentTeamId + 1, $ignoreMatchesAlreadyPlayed);
		        } else {
			        // Match cannot be matched in this possibility with the current upper matchlevels, no other opponents left
		            $this->recurseMatchResult = false;
		        }
			}
	    }
	    
	    //Jippie, match set!
	    return $this->recurseMatchResult;
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
		
	    foreach($this->aPlayedMatches as $aPlayedMatch) {
	        if(count(array_intersect($aPlayedMatch, $aMatch)) == count($aMatch)) {
	            // ID already in possible matches array
	            return true;
	        }
	    }
	    
	    return false;
	}

	private function matchExistsInForbiddenMatches($aMatch)
	{
		unset($aMatch["penalty"]);
		
	    foreach($this->forbiddenDeadlockMatches as $aForbiddenMatch) {
	        if(count(array_intersect($aForbiddenMatch, $aMatch)) == count($aMatch)) {
	            // ID already in possible matches array
	            return true;
	        }
	    }
	    
	    return false;
	}
	
	private function idExistsInPossibleMatches($id)
	{
	    $bResult = false;
	    foreach($this->aPossibleMatches as $aPossibleMatch) {
	        if(in_array($id, $aPossibleMatch) && array_search($id, $aPossibleMatch) != "penalty") {
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