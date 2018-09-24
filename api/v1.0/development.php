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
ini_set("error_log", "C:/Users/Surface/xampp/htdocs/elastique/logs/php-errors.log");
  
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

define('SERVER_ROOT', $_SERVER['DOCUMENT_ROOT']);
//echo "SERVER_ROOT: ".SERVER_ROOT."<br>\n";

define('ELASTEST_DIRECTORY', dirname(__FILE__));
//echo "ELASTEST_DIRECTORY: ".ELASTEST_DIRECTORY."<br>\n";

define('API_DIRECTORY', dirname(ELASTEST_DIRECTORY));
//echo "API_DIRECTORY: ".API_DIRECTORY."<br>\n";

define('ELASTIQUE_DIRECTORY', dirname(API_DIRECTORY));
//echo "ELASTIQUE_DIRECTORY: ".ELASTIQUE_DIRECTORY."<br>\n";

define('BASE_URL', 'http://127.0.0.1/elastique/'); // Then books/ or authors/ or publishers/
//echo "BASE_URL: ".PUBLIC_URL."<br>\n";

// PSR-0 Class loader
function loadClassWithNamespaces(?string $className) : void {     
		
		/* For PSR-4 Class loader
    return array(
    'Elastest\\Api\\'				  => array(ELASTEST_DIRECTORY . '/Api/'),
    'Elastest\\Config\\' 			=> array(ELASTEST_DIRECTORY . '/Config/'),
    'Elastest\\Controllers\\' => array(ELASTEST_DIRECTORY . '/Controllers/'),
    'Elastest\\Exceptions\\' 	=> array(ELASTEST_DIRECTORY . '/Exceptions/'),
    'Elastest\\Http\\' 				=> array(ELASTEST_DIRECTORY . '/Http/'),
    'Elastest\\Storage\\' 		=> array(ELASTEST_DIRECTORY . '/Storage/'),
    'Elastest\\Utils\\' 		=> array(ELASTEST_DIRECTORY . '/Utils/')
    );
	  */
	                                                        
    $fileName = '';                                                                          
    $namespace = '';  
                                                                                       
    // Sets the include path as the "root" directory                                           
    $includePath = ELASTEST_DIRECTORY;                              

    $parts = explode('\\', $className);
    $class = array_pop($parts);
    $folder = array_pop($parts);   
    
    $fileName .= $class . '.class.php';   
    $fullFileName = $includePath . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $fileName;
                                                                                             
    if (file_exists($fullFileName)) {                                                        
        require $fullFileName;                                                               
    } else {                                                                                 
        echo 'Class "'.$className.'" does not exist.';                                       
    }                                                                                        
}   

// Registers the autoloader loadClassWithNamespaces                                                                                           
spl_autoload_register('loadClassWithNamespaces');          
