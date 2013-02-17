<?php

//debug mode?
define('DEBUG_MODE', true);

if(DEBUG_MODE == true){
    error_reporting(E_ALL);
}else{
    error_reporting(0);
}

date_default_timezone_set('Europe/Amsterdam');

//PDO credentials isbt db
define('ISBT_DSN', 'mysql:dbname=isbt;host=127.0.0.1');
define('ISBT_USER', 'isbt');
define('ISBT_PWD', 'wrAn6wrEhedr');

//monolog logging

//filter logging records
//see: https://github.com/Seldaek/monolog#core-concepts
define('LOG_LEVEL', Monolog\Logger::DEBUG);

//how to write the log?
// 1. write to the db, 2. for debugging mode, write it to a file, 3. do both!
define('SAVE_LOG', 3);

//monolog file log destination; 'isbt.log' at the folder 'log'
define('LOG_FILE_DESTINATION', ROOT_DIR.'log'.DIRECTORY_SEPARATOR.'isbt.log');

//match status
define('MATCH_NOT_YET_STARTED', 0);
define('MATCH_STARTED', 1);
define('MATCH_PAUSED', 2);
define('MATCH_ENDED', 3);
define('MATCH_FINISHED', 4);
