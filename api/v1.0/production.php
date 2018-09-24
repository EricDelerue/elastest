<?php
define('DEBUG', 0);

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
	
define('ELASTIQUE_DIRECTORY', $_SERVER['DOCUMENT_ROOT'].'/elastique');  // C:/Users/Surface/xampp/htdocs/elastique
//echo "ELASTIQUE_DIRECTORY: ".ELASTIQUE_DIRECTORY."<br>\n";
	
define('ELASTEST_DIRECTORY', ELASTIQUE_DIRECTORY.'/Elastest');  // C:/Users/Surface/xampp/htdocs/elastique
//echo "ELASTEST_DIRECTORY: ".ELASTEST_DIRECTORY."<br>\n";

define('PUBLIC_URL', 'http://127.0.0.1/elastique/'); 

function loadClassWithNamespaces($className) {     
	                                                        
    $fileName = '';                                                                          
    $namespace = '';  
    
    echo "loadClass className: ".$className."<br>\n";                                                                       
                                                                                             
    // Sets the include path as the "src" directory                                          
    //$includePath = dirname(__FILE__).DIRECTORY_SEPARATOR.'src';  
    $includePath = ELASTIQUE_DIRECTORY;                              
    echo "loadClass includePath: ".$includePath."<br>\n";
                                                                                             
    if (false !== ($lastNsPos = strripos($className, '\\'))) {                               
        $namespace = substr($className, 0, $lastNsPos);                                      
        $className = substr($className, $lastNsPos + 1);                                     
        $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }       
    
    echo "loadClass namespace: ".$namespace."<br>\n";
    echo "loadClass className: ".$className."<br>\n";
    echo "loadClass fileName: ".$fileName."<br>\n";
                                                                                     
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.class.php';   
    echo "loadClass fileName: ".$fileName."<br>\n";              
    $fullFileName = $includePath . DIRECTORY_SEPARATOR . $fileName;          
    echo "loadClass fullFileName: ".$fullFileName."<br>\n";                
                                                                                             
    if (file_exists($fullFileName)) {                                                        
        require $fullFileName;                                                               
    } else {                                                                                 
        echo 'Class "'.$className.'" does not exist.';                                       
    }                                                                                        
}   

// Registers the autoloader loadClassWithNamespaces                                                                                           
spl_autoload_register('loadClassWithNamespaces');          
