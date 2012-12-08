<?

class ToolBox{

    public static function getMatchStatusLabel($status)
    {
        switch($status)
        {
            case MATCH_STARTED: 
                return '<button class="btn btn-small btn-info disabled">In progress...</button>';
            case MATCH_ENDED:
                return '<button class="btn btn-small btn-warning disabled">Ended, awaiting score</button>';
            case MATCH_NOT_YET_STARTED:
                return '<button class="btn btn-small disabled">In cue to start</button>';
            case MATCH_FINISHED:
                return '<button class="btn btn-small btn-error disabled">Ended</button>';
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
    	$pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD);

    	$sql = "SELECT `category`.* FROM category ORDER BY id ASC";

    	$stmt = $pdo->prepare($sql);
    	$stmt->bindParam(":poule", $poule);
    	$stmt->execute();

    	return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function calculateMatchScore($match)
    {
        $team1Score = 0;
        $team2Score = 0;

        if(!empty($match) && !empty($match['team1_set1_score']))
        {
            if($match['team1_set1_score'] > $match['team2_set1_score'])
                $team1Score++;
            else
                $team2Score++;
        }
        else
        {
            return "";
        }

        if(!empty($match['team1_set2_score']))
        {
            if($match['team1_set2_score'] > $match['team2_set2_score'])
                $team1Score++;
            else
                $team2Score++;
        }

        if(!empty($match['team1_set3_score']))
        {
            if($match['team1_set3_score'] > $match['team2_set3_score'])
                $team1Score++;
            else
                $team2Score++;
        }

        return $team1Score . " - " . $team2Score;
    }
}