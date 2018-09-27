<?php
spl_autoload_register('classLoader');

function classLoader($class_name) {
	
	//echo "__autoload class_name: ".$class_name."<br>\n";
	//echo "__autoload LANG: ".LANG."<br>\n";

	$file_name = trim(str_replace('\\','/',$class_name),'/').'.class.php';
	//echo "__autoload file_name: ".$file_name."<br>\n";

	$file_path = DIR_CLASSES. DIRECTORY_SEPARATOR . $file_name;
	//echo "__autoload file_path: ".$file_path."<br>\n";

	// You should not have to use require_once inside the autoloader, as if the class is not found it wouldn't be trying to look for it by using the autoloader.
	// Just use require(), which will be better on performance as well as it does not have to check if it is unique.

	if ( file_exists( $file_path ) ) {
		return require( $file_path );
	}

	$file_path = DIR_API. DIRECTORY_SEPARATOR . $file_name;
	//echo "__autoload file_path: ".$file_path."<br>\n";

	// You should not have to use require_once inside the autoloader, as if the class is not found it wouldn't be trying to look for it by using the autoloader.
	// Just use require(), which will be better on performance as well as it does not have to check if it is unique.

	if ( file_exists( $file_path ) ) {
		return require( $file_path );
	}

	$file_path = DIR_ELASTEST. DIRECTORY_SEPARATOR . $file_name;
	//echo "__autoload file_path: ".$file_path."<br>\n";

	// You should not have to use require_once inside the autoloader, as if the class is not found it wouldn't be trying to look for it by using the autoloader.
	// Just use require(), which will be better on performance as well as it does not have to check if it is unique.

	if ( file_exists( $file_path ) ) {
		return require( $file_path );
	}

	return false;
}

/*

Deprecated: __autoload() is deprecated, use spl_autoload_register() instead in C:\Users\Surface\xampp\htdocs\elastique\api\v2.0\autoloader.php on line 3

function __autoload($class_name) {
	
	//echo "__autoload class_name: ".$class_name."<br>\n";
	//echo "__autoload LANG: ".LANG."<br>\n";

	$file_name = trim(str_replace('\\','/',$class_name),'/').'.class.php';
	//echo "__autoload file_name: ".$file_name."<br>\n";

	$file_path = DIR_CLASSES. '/' . $file_name;
	//echo "__autoload file_path: ".$file_path."<br>\n";

	// You should not have to use require_once inside the autoloader, as if the class is not found it wouldn't be trying to look for it by using the autoloader.
	// Just use require(), which will be better on performance as well as it does not have to check if it is unique.

	if ( file_exists( $file_path ) ) {
		return require( $file_path );
	}

	return false;
}

*/
