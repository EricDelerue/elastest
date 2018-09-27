<?php
define('DEBUG', 0);

/** PHP ERRORS  ***/
if(DEBUG){
	error_reporting(E_ALL);
} else {
	error_reporting(E_ALL^E_WARNING^E_NOTICE);
}

ini_set("display_errors", "1");
ini_set("log_errors", 1);
ini_set("error_log", "/var/www/html/dev.ericdelerue.com/elastique/logs/elastest.api.v2.0.errors.log");

define("LOG_FILE","/var/www/html/dev.ericdelerue.com/elastique/logs/elastest.api.v2.0.errors.log");


date_default_timezone_set('UTC');
  
define('DIR_CLASSES', str_replace('\\','/',dirname(__FILE__))); //  /var/www/html/dev.ericdelerue.com/elastique/api/v2.0
//echo "DIR_CLASSES: ".DIR_CLASSES."<br>\n"; 

define('DIR_API', DIR_CLASSES . DIRECTORY_SEPARATOR . 'Api'); // /var/www/html/dev.ericdelerue.com/elastique/api/v2.0/Api
//echo "DIR_API: ".DIR_API."<br>\n"; 
  
define('DIR_ELASTEST', DIR_CLASSES . DIRECTORY_SEPARATOR . 'Elastest'); // /var/www/html/dev.ericdelerue.com/elastique/api/v2.0/Elastest
//echo "DIR_ELASTEST: ".DIR_ELASTEST."<br>\n"; 

define('ROOT_URI', $_SERVER['DOCUMENT_ROOT'].'elastique/');  // /var/www/html/dev.ericdelerue.com/elastique/
//echo "ROOT_URI: ".ROOT_URI."<br>\n";

define('DIR_ERRORS', ROOT_URI.'logs/');  // /var/www/html/dev.ericdelerue.com/elastique/logs/
//echo "DIR_ERRORS: ".DIR_ERRORS."<br>\n"; 

define('DIR_KEYS', ROOT_URI.'keys/');  // /var/www/html/dev.ericdelerue.com/elastique/keys/
//echo "DIR_KEYS: ".DIR_KEYS."<br>\n";

define('PUBLIC_URL', 'http://dev.ericdelerue.com/elastique/'); 


ini_set( "SMTP", "mail.ericdelerue.com" ); // ericdelerue.com
ini_set( "smtp_port", "25" );  
ini_set('sendmail_from', 'me@ericdelerue.com');



require_once("autoloader.php");
