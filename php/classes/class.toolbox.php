<?php
/* ===========================================================
 * Copyright 2012 SB Helios, Netherlands.
 *
 * Licensed under the Apache License, Version 2.0 (the "License")
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */

class ToolBox{

    public static function getMessage($message)
    {
    	if(!empty($message)) {
			if($message['type'] == "alert-success") $message['title'] = "Well done!";
			elseif($message['type'] == "alert-info") $message['title'] = "Heads up!";
			elseif($message['type'] == "alert-danger") $message['title'] = "Oh snap!";
			else $message['title'] = "Warning!";

	    	return '<div class="alert ' . $message['type'] . '"><button type="button" class="close" data-dismiss="alert">×</button><b>' . $message['title'] . '</b> ' . $message['text'] .'</div>';
    	}
	}
	    
    public static function getMatchStatusLabel($match, $overrule_by_participants = false)
    {
    	if($overrule_by_participants == true)
    	{
    		return '<button class="btn btn-small btn-danger disabled">Postponed</button>';
    	}

		if(self::hasPostponedPlayers($match) && $match['status'] == MATCH_NOT_YET_STARTED)
		{
			return '<button class="btn btn-small btn-danger disabled">Postponed</button>';
		}
		
		// TODO: Clean dirty code of delayed players
		if(self::hasDelayedPlayers($match) && $match['status'] == MATCH_NOT_YET_STARTED){
			$delayed = self::getDelayedPlayers($match);
			
			if(count($delayed) > 1) $player = 'several players';
			elseif(count($delayed) > 0) $player = $delayed[0];

			return '<button class="btn btn-small btn-warning disabled">Delayed, <b>'.$player.'</b> already on a court</button>';
		}
				
        switch($match['status'])
        {
            case MATCH_STARTED: 
                return '<button class="btn btn-small btn-info disabled">In progress on court <b>'.$match['court'].'</b></button>';
            case MATCH_ENDED:
                return '<button class="btn btn-small btn-danger disabled">Ended, awaiting score</button>';
            case MATCH_NOT_YET_STARTED:
                return '<button class="btn btn-small disabled">In queue to start</button>';
            case MATCH_FINISHED:
                return '<button class="btn btn-small btn-success disabled">Ended</button>';
            case MATCH_PAUSED:
                return '<button class="btn btn-small btn-warning disabled">Paused</button>';
            default:
                return "";
        }
    }

    public static function getCategoryLabel($categoryId)
    {
    	$categories = self::getCategories();
    	$order = 0;
    	$name = "";
    	$level = "";

    	foreach($categories as $key => $category)
    	{

    		if($categoryId == $category['id'])
    		{
    			$order = $key;
    			$name = $category['name'];
    			$level = $category['level'];
    			break;
    		}
    	}

    	switch($order)
    	{
    		case 0:
    			return '<span class="label label-success">' . $name . '  ' . $level . '</span>';
    		case 1:
    			return '<span class="label label-warning">' . $name . '  ' . $level . '</span>';
    		case 2:
    			return '<span class="label label-important">' . $name . '  ' . $level . '</span>';
    		case 3:
    			return '<span class="label label-error">' . $name . '  ' . $level . '</span>';
    		case 4:
    			return '<span class="label label-info">' . $name . '  ' . $level . '</span>';
    		default:
    			return '<span class="label label-inverse">' . $name . '  ' . $level . '</span>';
    	}
    }

    public static function getCategories()
    {
    	try
    	{
	    	$pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD, array(PDO::ATTR_PERSISTENT => true));
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
	    	$sql = "SELECT `category`.* FROM category ORDER BY id ASC";

	    	$stmt = $pdo->prepare($sql);
	    	$stmt->bindParam(":poule", $poule);
	    	$stmt->execute();

	    	return $stmt->fetchAll(PDO::FETCH_ASSOC);
	    }
	    catch(PDOException $e)
	    {
	    	Monolog::getInstance()->addAlert('Error selecting categories, PDOException: ' . var_export($e, true));
	    }
    }

    public static function calculateMatchScore($match)
    {
        $team1Score = 0;
        $team2Score = 0;
        $scores_set = false;

        if(isset($match['team1_set1_score']) == true && isset($match['team2_set1_score']) == true)
        {
            if($match['team1_set1_score'] > $match['team2_set1_score'])
            {
                $team1Score++;
            }
            else
            {
                $team2Score++;
            }
            $scores_set = true;
        }

        if(isset($match['team1_set2_score']) == true && isset($match['team2_set2_score']) == true)
        {
            if($match['team1_set2_score'] > $match['team2_set2_score'])
            {
                $team1Score++;
            }
            else
            {
                $team2Score++;
            }
            $scores_set = true;
        }

        /*
        // we need this when supporting matches with 3 sets, for now don't look at it. b/c at a lot of places the 3rd set is set hardcoded 0 - 0
        if(isset($match['team1_set3_score']) == true && isset($match['team2_set3_score']) == true && $team1Score < 2 && $team2Score < 2)
        {
            if($match['team1_set3_score'] > $match['team2_set3_score'])
            {
                $team1Score++;
            }
            else
            {
                $team2Score++;
            }
        }
		*/

		if($scores_set == true)
		{
			return $team1Score . " - " . $team2Score;
		}
        else
        {
        	return "";
        }
    }
	
	public static function getScore($match, $set, $team)
	{
		if($match['team' . $team . '_set' . $set . '_score'] == '')
		{
			return "";
		}
		
		return $match['team' . $team . '_set' . $set . '_score'];
	}
	
	public static function getTeamResults($team, $match)
	{
		list($team1_sets, $team2_sets) = explode(" - ", self::calculateMatchScore($match));
		list($points_won, $points_lost) = self::getPoints($team, $match);
		
		$matches_won = 0;
		$matches_draw = 0;
		$matches_lost = 0;
		
		if($team == 1)
		{
			$sets_won = $team1_sets;
			$sets_lost = $team2_sets;
		}
		else
		{
			$sets_won = $team2_sets;
			$sets_lost = $team1_sets;
		}
		if($team1_sets == $team2_sets)
			$matches_draw = 1;
		else if($team == 1 && $team1_sets > $team2_sets)
			$matches_won = 1;
		else if($team == 1 && $team1_sets < $team2_sets)
			$matches_lost = 1;
		else if($team == 2 && $team2_sets > $team1_sets)
			$matches_won = 1;
		else
			$matches_lost = 1;
		
		return array(
			"matches_won" => $matches_won,
			"matches_draw" => $matches_draw,
			"matches_lost" => $matches_lost,
			"sets_won" =>  $sets_won,
			"sets_lost" => $sets_lost,
			"points_won" => $points_won,
			"points_lost" => $points_lost
		);
		
		
	}
	
	public static function getPoints($team, $match)
	{
		$team1 = $match['team1_set1_score'] + $match['team1_set2_score'] + $match['team1_set3_score'];
		$team2 = $match['team2_set1_score'] + $match['team2_set2_score'] + $match['team2_set3_score'];
		
		if($team == 1)
			return array($team1, $team2);
		
		return array($team2, $team1);
	}
	
	public static function getAssignCourtLabel($availableCourts, $match)
	{
		// If there are no courts available, the field will be disabled
		if(sizeof($availableCourts) == 0)
		{
			$disabled = ' disabled';
			$icon = '';
			$btn_success = '';
			$btn_inverse = '';
			$data_target_startpostponed = '';
			$data_target_startmatch = '';
			$data_target_startdelayed  = '';
		}else{
			$disabled = '';
			$icon = ' icon-white';
			$btn_success = ' btn-success';
			$btn_inverse = ' btn-inverse';
			$data_target_startpostponed = " data-target='#startpostponed-" . $match['id'] . "'";
			$data_target_startmatch = " data-target='#startmatch-" . $match['id'] . "'";
			$data_target_startdelayed  = " data-target='#startdelayed-" . $match['id'] . "'";
		}
		
		// Checks whether there is a player who is postponed
		if(self::hasPostponedPlayers($match) == true)
		{
			return "<button class='btn btn-small pull-right".$disabled.$btn_inverse."' data-toggle='modal'".$data_target_startpostponed."><span class='icon-forward".$icon."'></span> Overrule, assign to court</button>";
		}

		// Checks whether there is a player who is delayed
		if(self::hasDelayedPlayers($match) == true){
			return "<button class='btn btn-small pull-right".$disabled.$btn_inverse."' data-toggle='modal'".$data_target_startdelayed."><span class='icon-forward".$icon."'></span> Overrule, assign to court</button>";
		}

		// TODO: Implement time check
		
		return "<button class='btn btn-small pull-right".$disabled.$btn_success."' data-toggle='modal'".$data_target_startmatch."><span class='icon-play".$icon."'></span> Assign to court</button>";
	}
	
	public static function hasPostponedPlayers($match)
	{
		return sizeof(self::getPostponedPlayers($match)) > 0;
	}
	
	public static function getPostponedPlayers($match)
	{
		$players = array();
		
		if(!empty($match['user1_postponed']) && $match['user1_postponed'] == 1)
		{
			$players[] = $match['team1_user1'];
		}
		if(!empty($match['user2_postponed']) && $match['user2_postponed'] == 1)
		{
			$players[] = $match['team1_user2'];
		}
		if(!empty($match['user3_postponed']) && $match['user3_postponed'] == 1)
		{
			$players[] = $match['team2_user1'];
		}
		if(!empty($match['user4_postponed']) && $match['user4_postponed'] == 1)
		{
			$players[] = $match['team2_user2'];
		}
		
		return $players;
	}

	public static function hasDelayedPlayers($match)
	{
		return count(self::getDelayedPlayers($match)) > 0;
	}

	public static function getDelayedTeams($match){
		// TODO: test this for teams with only one player
		// TODO: optimize this dirty piece of code

		//check for delayed teams
		$allTeams = self::getTeamsOnCourts();
		$delayed = array();

		//
		if(in_array($match['team1'], $allTeams) == true){
			$delayed[] = $match['team1_user1'];

			if(!empty($match['team1_user2'])){
				$delayed[] = $match['team1_user2'];
			}
		}

		if(in_array($match['team2'], $allTeams) == true){
			$delayed[] = $match['team2_user1'];

			if(!empty($match['team2_user2'])){
				$delayed[] = $match['team2_user2'];
			}
		}

		return $delayed;
	}

	public static function getDelayedPlayers($match){
		// TODO: test this for teams with only one player
		// TODO: optimize this dirty piece of code

		//check for delayed teams
		$playersOnCourts = self::getPlayersOnCourts();
		
		$delayed = array();
		if(isset($match['team1_user1'])) {
			if(in_array($match['team1_user1'], $playersOnCourts) == true) $delayed[] = $match['team1_user1'];
		}
		if(isset($match['team1_user2'])) {
			if(in_array($match['team1_user2'], $playersOnCourts) == true) $delayed[] = $match['team1_user2'];
		}
		if(isset($match['team2_user1'])) {
			if(in_array($match['team2_user1'], $playersOnCourts) == true) $delayed[] = $match['team2_user1'];
		}
		if(isset($match['team2_user2'])) {
			if(in_array($match['team2_user2'], $playersOnCourts) == true) $delayed[] = $match['team2_user2'];
		}

		return $delayed;
	}

	public static function getTeamsOnCourts(){
		try
    	{
	    	$pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD, array(PDO::ATTR_PERSISTENT => true));
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
	    	$sql = "SELECT `team1`, `team2` 
	    			FROM `match` 
	    			WHERE `court` != 'NULL'";

	    	$stmt = $pdo->prepare($sql);
	    	$stmt->execute();

	    	$teamsOnCourts = $stmt->fetchAll(PDO::FETCH_ASSOC);
	    	$allTeams = array();
	    	
	    	foreach ($teamsOnCourts as $team) {
	    		$allTeams[] = $team['team1'];
	    		$allTeams[] = $team['team2'];
	    	}
	    	
	    	return $allTeams;
	    }
	    catch(PDOException $e)
	    {
	    	Monolog::getInstance()->addAlert('Error selecting teams on courts, PDOException: ' . var_export($e, true));
	    }
	}
	
	public static function getPlayersOnCourts(){
		try
    	{
	    	$pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD, array(PDO::ATTR_PERSISTENT => true));
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
            $sql = "SELECT 
						`user1`.name as `team1_user1`,
						`user2`.name `team1_user2`,
						`user3`.name as `team2_user1`,
						`user4`.name as `team2_user2`
                    FROM `match` 
                    INNER JOIN `team` `team1` ON(team1.id = match.team1) 
                    INNER JOIN `team` `team2` ON(team2.id = match.team2) 
                    INNER JOIN `user` `user1` ON(team1.user1 = user1.id) 
                    LEFT JOIN `user` `user2` ON(team1.user2 = user2.id) 
                    INNER JOIN `user` `user3` ON(team2.user1 = user3.id) 
                    LEFT JOIN `user` `user4` ON(team2.user2 = user4.id)
                    WHERE `court` != 'NULL'";

	    	$stmt = $pdo->prepare($sql);
	    	$stmt->execute();

	    	$playersOnCourts = $stmt->fetchAll(PDO::FETCH_ASSOC);

	    	$allPlayers = array();
	    	foreach ($playersOnCourts as $player) {
	    		$allPlayers[] = $player['team1_user1'];
	    		$allPlayers[] = $player['team1_user2'];
	    		$allPlayers[] = $player['team2_user1'];
	    		$allPlayers[] = $player['team2_user2'];
	    	}
	    	
	    	return $allPlayers;
	    }
	    catch(PDOException $e)
	    {
	    	Monolog::getInstance()->addAlert('Error selecting teams on courts, PDOException: ' . var_export($e, true));
	    }
	}

	public static function getStartNextRoundButton($poule){
		if(self::hasFinishedRound($poule) == true)
		{
			return '<button class="span12 btn btn-primary" data-toggle="modal" data-target="#startnextround-poule-x">Start next round</button>';
		}
		else
		{
			return '<button class="span12 btn btn-primary disabled" data-toggle="modal">Start next round</button>';
		}
	}

	public static function getFinishPouleButton($poule){
		if(self::hasFinishedRound($poule) == true)
		{
			return '<button class="span12 btn btn-danger" data-toggle="modal" data-target="#finish-poule-x">Finish poule</button>';
		}
		else
		{
			return '<button class="span12 btn btn-danger disabled" data-toggle="modal">Finish poule</button>';
		}
	}

	public static function hasFinishedRound($poule){
		try
        {
            $pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD, array(PDO::ATTR_PERSISTENT => true));
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
            $sql = "SELECT match.id
                    FROM `match` 
                    INNER JOIN `team` `team1` ON(team1.id = match.team1) 
                    INNER JOIN `poule` ON(poule.id = team1.poule)
                    WHERE poule.round = match.round
					AND poule.id = :poule
					AND `status` != '4'";

            $stmt = $pdo->prepare($sql);
			$stmt->bindParam(":poule", $poule);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            Monolog::getInstance()->addAlert('Error checking if all matches are done for the current round, PDOException: ' . var_export($e, true));
        }

        if(count($result) > 0)
        {
        	return false;
        }
        else
        {
        	return true;
        }
	}
	
	public static function getCheckedStatusPlayer($sPlayer, $aMatch)
	{
		$aPostponedPlayers = self::getPostponedPlayers($aMatch);
		$aDelayedPlayers = self::getDelayedPlayers($aMatch);
		
		if(!is_bool(array_search($sPlayer, $aPostponedPlayers))) {
		  return "<span class=\"red\"><b>" . $sPlayer . "</b></span>";
		} elseif(!is_bool(array_search($sPlayer, $aDelayedPlayers))) {
		  return "<span class=\"yellow\"><b>" . $sPlayer . "</b></span>";
		} else {
			return $sPlayer;
		} 
	}
}