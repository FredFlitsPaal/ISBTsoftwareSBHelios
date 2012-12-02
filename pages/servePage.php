<?php

//load classes
require_once("../config/config.php");
require_once(LIB_DIR."class.pagecontroller.php");

if(isset($_POST["page"])){
    $l_sPage = $_POST["page"];
}

new pageController($l_sPage);