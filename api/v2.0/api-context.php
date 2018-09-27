<?php
/*
|--------------------------------------------------------------------------
| Run Environment-specific configuration settings
|--------------------------------------------------------------------------
*/

$host = substr($_SERVER['HTTP_HOST'], 0, 5);            
if (in_array($host, array('local', '127.0', '192.1'))) {
	$local = true;                                        
} else {                                                
	$local = false;                                       
}                                                       
if ($local) {  
	define('ENVIRONMENT', 'development');                                         
	require_once 'development.php';                           
} else {                 
	define('ENVIRONMENT', 'production');                                      
	require_once 'production.php';                            
}	                                                      
