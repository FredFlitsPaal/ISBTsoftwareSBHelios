<?php
require_once("php/classes/class.pagecontroller.php");
require_once("php/classes/class.toolbox.php");

if(isset($_POST["page"])){
    $l_sPage = $_POST["page"];
}/*else{
    $l_sPage = 'tournament';
}*/

$bla = new pageController;
$bla->page($l_sPage);