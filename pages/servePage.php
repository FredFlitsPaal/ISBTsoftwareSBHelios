<?php

//initialise some cool stuff
require_once(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."php".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."class.initialiseISBT.php");
new initialiseISBT();

if(isset($_POST["page"])){
    $l_sPage = $_POST["page"];
	
	if(substr($l_sPage, 0, 10) == "caroussel_")
	{
		Monolog::getInstance()->addDebug('Calling carousselController now!');
		new carousselController(substr($l_sPage, 10));
		
		exit;
	
	}
} 

Monolog::getInstance()->addDebug('calling pageController now!');
new pageController($l_sPage);