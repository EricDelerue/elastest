<?php 

/*
|--------------------------------------------------------------------------
| Run Environment-specific configuration settings
|--------------------------------------------------------------------------
*/

$http_host = substr($_SERVER['HTTP_HOST'], 0, 5);            
if (in_array($http_host, array('local', '127.0', '192.1'))) {
	$http_local = true;                                        
} else {                                                
	$http_local = false;                                       
}                                                       
if ($http_local) {  	      
  define('ENVIRONMENT', 'development');                                              
	require_once 'development.php';                           
} else {        
  define('ENVIRONMENT', 'production');                                                
	require_once 'production.php';                            
}	                                                      

			



