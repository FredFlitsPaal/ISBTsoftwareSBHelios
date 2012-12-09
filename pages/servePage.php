<?php

//initialise some cool stuff
require_once(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."php".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."class.initialiseISBT.php");
new initialiseISBT();

if(isset($_POST["page"])){
    $l_sPage = $_POST["page"];
} 

Monolog::getInstance()->addDebug('calling pageController now!');
new pageController($l_sPage);