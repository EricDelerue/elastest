<?php
define('DEBUG', 1);

/** PHP ERRORS  ***/
if(DEBUG){
	error_reporting(E_ALL);
} else {
	error_reporting(E_ALL^E_WARNING^E_NOTICE);
}

ini_set("display_errors", 1);
ini_set("log_errors", 1);
ini_set("error_log", "C:/Users/Surface/xampp/htdocs/elastique/logs/elastest.api.v2.0.errors.log");

define("LOG_FILE","/var/www/html/elastique/logs/elastest.api.v2.0.errors.log");
  
$script_timezone='UTC';
  
//if(gettype($script_timezone) == 'undefined' || is_null($script_timezone) || $script_timezone == ''){
if(function_exists('date_default_timezone_set')) { 
	date_default_timezone_set('UTC'); 
	$script_timezone='UTC';
}	

/** END TIME ZONE  ***/

/*** REWRITE OR IIS REWRITE ***/
if (isset($_SERVER['HTTP_X_ORIGINAL_URL'])) {
	$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_ORIGINAL_URL'];
	//echo "SERVER['HTTP_X_ORIGINAL_URL']= ".$_SERVER['HTTP_X_ORIGINAL_URL']."<br />";
} else if (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
	$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_REWRITE_URL'];
	//echo "SERVER['HTTP_X_REWRITE_URL']= ".$_SERVER['HTTP_X_REWRITE_URL']."<br />";		
}


if(!isset($_SERVER['DOCUMENT_ROOT'])){                                                                                                             
	if(isset($_SERVER['SCRIPT_FILENAME'])){                                                                                                          
		$_SERVER['DOCUMENT_ROOT'] = str_replace( '', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 0-strlen($_SERVER['PHP_SELF'])));                     
  }                                                                                                                                                
}                                                                                                                                                  
                                                                                                                                                   
if(!isset($_SERVER['DOCUMENT_ROOT'])){                                                                                                             
	if(isset($_SERVER['PATH_TRANSLATED'])){                                                                                                          
		$_SERVER['DOCUMENT_ROOT'] = str_replace( '', '/', substr(str_replace('', '', $_SERVER['PATH_TRANSLATED']), 0, 0-strlen($_SERVER['PHP_SELF'])));
  }                                                                                                                                                
}                                                                                                                                                  

/*
define('DIR_CLASSES', str_replace('\\','/',dirname(__FILE__))); // C:/Users/Surface/xampp/htdocs/elastique/api/v2.0
//echo "DIR_CLASSES: ".DIR_CLASSES."<br>\n";
*/
  
define('DIR_CLASSES', str_replace('\\','/',dirname(__FILE__))); // C:/Users/Surface/xampp/htdocs/elastique/api/v2.0
//echo "DIR_CLASSES: ".DIR_CLASSES."<br>\n"; 

define('DIR_API', DIR_CLASSES . DIRECTORY_SEPARATOR . 'Api'); // C:/Users/Surface/xampp/htdocs/elastique/api/v2.0/Api
//echo "DIR_API: ".DIR_API."<br>\n"; 
  
define('DIR_ELASTEST', DIR_CLASSES . DIRECTORY_SEPARATOR . 'Elastest'); // C:/Users/Surface/xampp/htdocs/elastique/api/v2.0/Elastest
//echo "DIR_ELASTEST: ".DIR_ELASTEST."<br>\n"; 

define('ROOT_URI', $_SERVER['DOCUMENT_ROOT'].'/elastique/');  // C:/Users/Surface/xampp/htdocs/elastique/
//echo "ROOT_URI: ".ROOT_URI."<br>\n";

define('DIR_ERRORS', ROOT_URI.'logs/');  // C:/Users/Surface/xampp/htdocs/elastique/logs/
//echo "DIR_ERRORS: ".DIR_ERRORS."<br>\n";

define('DIR_KEYS', ROOT_URI.'keys/');  // C:/Users/Surface/xampp/htdocs/elastique/keys/
//echo "DIR_KEYS: ".DIR_KEYS."<br>\n";

define('PUBLIC_URL', 'http://127.0.0.1/elastique/'); 


require_once("autoloader.php");