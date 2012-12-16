<?php

//before we could call the package, switch to the right namespace
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

    class Monolog{
    
        public static function getInstance($p_bReset = false) {
            if($p_bReset === true){
                self::setConfig();
                //the parameter true is passed b/c we need to initialise Monolog once
                $l_oLogger = self::initMonolog();
                //store object in the session
                $_SESSION['logger'] = $l_oLogger;
                Monolog::getInstance()->addDebug('the Monolog object was forced to reset, we want this to happen at the beginning of each new request');
            }
            
            $l_oLogger = $_SESSION['logger'];
            return $l_oLogger;
        }

        private static function setConfig(){
            //require config
            require_once(ROOT_DIR.'config'.DIRECTORY_SEPARATOR.'config.php');
        }
        
        private static function initMonolog(){
            // Create the logger
            $l_oLogger = new Logger('ISBT');
            
            //add handlers to write the logging records
            //config.php setting
            if(SAVE_LOG != 1){
                //activate file based logging
                try{
                    $l_oHandler = new StreamHandler(LOG_FILE_DESTINATION, LOG_LEVEL);
                    $l_oLogger->pushHandler($l_oHandler);
                    $l_oLogger->addDebug('file based log activated!');
                    
                }catch(Exception $exception){
                    echo 'failed to activate filebased log, an unexpected error occurred in the function \'initMonolog()\', exception message: '.var_export($exception, true);
                }
            }
            
            //processors toevoegen!!
            
            if(SAVE_LOG != 2){
        
                $l_aParams = array(
                        'dsn'     => ISBT_DSN,
                        'username' => ISBT_USER,
                        'password' => ISBT_PWD
                        );
                
                try {
                    //autoloader?? wut?
                    require_once(LIB_DIR.'PDOHandler.php');
                    $l_oHandler = new PDOHandler(new PDO($l_aParams['dsn'], $l_aParams['username'], $l_aParams['password'], array(PDO::ATTR_PERSISTENT => true)), LOG_LEVEL);
                    $l_oLogger->pushHandler($l_oHandler);

                } catch (PDOException $e) {
                    echo 'failed to activate db based log, an unexpected error occurred in the function \'initMonolog()\', exception message: '.var_export($e, true);
                }
            }
            
            return $l_oLogger;
        }
    }