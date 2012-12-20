<?php

class ParticipantController 
{

    public function actionIndex()
    {
		ob_start();
		
		if(!empty($_POST['action']) && $_POST['action'] == "start-match")
		{
			$message = $this->startMatch($_POST['match-id']);
		}
		
		if(!empty($_POST['action']) && $_POST['action'] == "pause-match")
		{
			$this->pauseMatch($_POST['match-id'], true);
		}
		
		if(!empty($_POST['action']) && $_POST['action'] == "play-match")
		{
			$this->pauseMatch($_POST['match-id'], false);
		}
		
		if(!empty($_POST['action']) && $_POST['action'] == "end-match")
		{
			$message = $this->endMatch($_POST['match-id']);
		}
		
		$participants = $this->getParticipants();

        include_once(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "pages" . DIRECTORY_SEPARATOR . "participants" . DIRECTORY_SEPARATOR . "index.html");
		
		return ob_get_clean();
    }
	
	private function getParticipants()
	{
		try
        {
            $pdo = new PDO(ISBT_DSN, ISBT_USER, ISBT_PWD, array(PDO::ATTR_PERSISTENT => true));
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
            $sql = "SELECT * FROM `user`";

            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            Monolog::getInstance()->addAlert('Error selecting matches, PDOException: ' . var_export($e, true));
        }

        return array();
	}
}