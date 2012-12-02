<?php

//debug mode?
define('DEBUG_MODE', true);

if(DEBUG_MODE == true){
    error_reporting(E_ALL);
}else{
    error_reporting(0);
}

date_default_timezone_set('Europe/Amsterdam');

//root dir of the project
define('ROOT_DIR', __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR);

//dir with all the pages available
define('PAGE_DIR', ROOT_DIR.'pages'.DIRECTORY_SEPARATOR);

//dir with all the classes written for the project
define('LIB_DIR', ROOT_DIR.'php'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR);

//PDO credentials isbt db
define('ISBT_DSN', 'mysql:dbname=isbt;host=127.0.0.1');
define('ISBT_USER', 'isbt');
define('ISBT_PWD', 'wrAn6wrEhedr');

//monolog logging - not yet implemented (is voor later als het echt live gaat op de helios site)

//filter logging records
//see: https://github.com/Seldaek/monolog#core-concepts
//define('LOG_LEVEL', Monolog\Logger::DEBUG);

//how to write the log?
// 1. write to the db, 2. for debugging mode, write it to a file, 3. do both!
//define('SAVE_LOG', 2);

//monolog file log destination; 'isbt.log' at the folder 'log'
//define('LOG_FILE_DESTINATION', ROOT_DIR.'log'.DIRECTORY_SEPARATOR.'isbt.log');

//create token for refenrence to the db logging - timestamp + rand - md5 is good enough and lighter though
//$l_sToken = md5(microtime().rand(0,99999));
//define('LOG_TOKEN', $l_sToken);

//the monolog object has a name, adopt the 'action' name
//define('ACTION', 'action_fixed_value');