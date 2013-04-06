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

//switch to the namespace the class in UniversalClassLoader.php is in
use Symfony\Component\ClassLoader\UniversalClassLoader;

class initialiseISBT{

	public function __construct(){
		//load monolog with symphony classloader
		$this->loadMonolog();

		//autoload classes
		$this->autoloadLibDir();
	}

	private function loadMonolog(){
		//root dir of the project
		define('ROOT_DIR', __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR);

		//dir with all the pages available
		define('PAGE_DIR', ROOT_DIR.'pages'.DIRECTORY_SEPARATOR);

		//dir with all the classes written for the project
		define('LIB_DIR', ROOT_DIR.'php'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR);

		//dir with all the packages used in this project
		define('PACKAGE_DIR', ROOT_DIR.'php'.DIRECTORY_SEPARATOR.'packages'.DIRECTORY_SEPARATOR);

		//initialise autoloader
		//package used: Symfony2 ClassLoader component, https://github.com/symfony/ClassLoader
	    require_once(PACKAGE_DIR.'ClassLoader'.DIRECTORY_SEPARATOR.'UniversalClassLoader.php');
	    $L_o_loader = new UniversalClassLoader();
	    $L_o_loader->register();
	    
	    //initialise logging and load the config
	    //register namespaces for package
	    //package used: Monolog, https://github.com/Seldaek/monolog
	    $L_o_loader->registerNamespace('Monolog', PACKAGE_DIR.'Monolog'.DIRECTORY_SEPARATOR.'src');
	    require_once(LIB_DIR.'class.Monolog.php');
		
		//initialise the logging and load the config - the parameter true is passed b/c we want to force reset the Monolog object for each request!
		Monolog::getInstance(true)->addDebug('initialisation finished successfully - autoloader en Monolog initialised, config loaded');
	}

	private function autoloadLibDir(){
		foreach (glob(LIB_DIR."*.php") as $filename)
		{
		    require_once($filename);
		} 
        foreach (glob(LIB_DIR."*/*.php") as $filename)
		{
		    require_once($filename);
		}
	}
}