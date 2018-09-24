<?php

/**
 * Elastest - September 2018
 * Assignment from Elastique: Build a RESTful API (ref.: Assignment Backend - Elastique.pdf)
 * Eric Delerue delerue_eric@hotmail.com
 * https://github.com/EricDelerue/elastest (private repository)
 * 
 */

namespace Elastest\Exceptions;

use Exception;

/**
* Define a custom exception class
*/

class ElastestException extends Exception {
	
  protected $args = array();

  // Redefine the exception so the message is not optional
  public function __construct($message, $code = 0, Exception $previous = null, $args = array()) {

    // Custom shape of the exception you want to achieve ...
    $this->args['description'] = $message;
    $this->args = array_merge($this->args, $args);

    // We make sure that everything has been properly assigned
    parent::__construct($message, $code, $previous);
  }

  // Custom string representing the object
  public function __toString() {
    return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
  }

  public function errorMessage() { 
    // We build the header
    header("HTTP/1.1 " . $this->code . " " . $this->_requestStatus($this->code));
    header("Access-Control-Allow-Orgin: *");
    header("Access-Control-Allow-Methods: *");
    header("Content-Type: application/json");   
        
		// Servers don't always have a signature turned on (this is an apache directive "ServerSignature On")
		$signature = ($_SERVER['SERVER_SIGNATURE'] == '') ? $_SERVER['SERVER_SOFTWARE'] . ' Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_SIGNATURE'];
		$signature = trim(strip_tags($signature));

    $error_message = array('status' => $this->code, 'message' => $this->code.": ".$this->_requestStatus($this->code), 'signature' => $signature, 'verbose' => $this->args);
    //$error_message['verbose'] = $this->args;
    return json_encode($error_message);    

  }
  
  // $this->errors = array('error' => 400, 'title' => 'Wrong Endpoint', 'description' => 'Requested endpoint doesn\'t exist or was typed wrong. Please verify and try again.', 'endpoint' => 'http://www.itineranda.com/api/v1.0/'.$this->endpoint);        	    
  // $this->errors = array('error' => 400, 'title' => 'Wrong Verb after Endpoint', 'description' => 'Expected \'oauth\', \'details\', \'photos\' or \'videos\' verbs after \'user\' or \'media\' endpoints. Please verify and try again.', 'verb' => 'http://www.itineranda.com/api/v1.0/'.$this->endpoint.'/'.$this->verb);
  // $this->errors = array('error' => 400, 'title' => 'Wrong Search Type after Endpoint', 'description' => 'Expected \'recent\' or \'tags\' search types after verbs. Please verify and try again.', 'search_type' => 'http://www.itineranda.com/api/v1.0/'.$this->endpoint.'/'.$this->search_type);
  // $this->errors = array('error' => 405, 'title' => 'Unexpected Header', 'description' => 'Expected DELETE or PUT methods in HTTP_X_HTTP_METHOD header when doing a POST request. Please verify and try again.', 'header' => $_SERVER['HTTP_X_HTTP_METHOD']);
  // $this->errors = array('error' => 405, 'title' => 'Invalid Method', 'description' => 'Only GET, POST, PUT and DELETE methods are valid. Please verify and try again.', 'method' => $this->method);
  // $this->errors = array('error' => 400, 'title' => 'No API Key provided', 'description' => 'It is necessary to provide an API Key to use this API. Please verify and try again, or ask one.');        	  
  // $this->errors = array('error' => 400, 'title' => 'Invalid API Key', 'description' => 'The given API Key doesn\'t match any corresponding API Key in the database with the origin '.$origin.'. Please verify and try again', 'apikey' => $this->query_string['apikey']);        	  
  // $this->errors = array('error' => 400, 'title' => 'Invalid User Token', 'description' => 'The given Access Token doesn\'t match any corresponding Access Token in the database. Please verify and try again', 'access_token' => $this->query_string['token']);        	  
  // $this->errors = array('error' => 401, 'title' => 'User Token has expired', 'description' => 'The given Access Token has expired. Please login and try again', 'access_token' => $this->query_string['token']);        	  

    private function _requestStatus($status){
		// These could be stored in a .ini file and loaded via parse_ini_file()
		$codes = Array(
		    100 => 'Continue',
		    101 => 'Switching Protocols',
		    190 => 'Facebook OAuthException',
		    200 => 'OK',
		    201 => 'Created',
		    202 => 'Accepted',
		    203 => 'Non-Authoritative Information',
		    204 => 'No Content',
		    205 => 'Reset Content',
		    206 => 'Partial Content',
		    300 => 'Multiple Choices',
		    301 => 'Moved Permanently',
		    302 => 'Found',
		    303 => 'See Other',
		    304 => 'Not Modified',
		    305 => 'Use Proxy',
		    306 => '(Unused)',
		    307 => 'Temporary Redirect',
		    400 => 'Bad Request',
		    401 => 'Unauthorized',
		    402 => 'Payment Required',
		    403 => 'Forbidden',
		    404 => 'Not Found',
		    405 => 'Method Not Allowed',
		    406 => 'Not Acceptable',
		    407 => 'Proxy Authentication Required',
		    408 => 'Request Timeout',
		    409 => 'Conflict',
		    410 => 'Gone',
		    411 => 'Length Required',
		    412 => 'Precondition Failed',
		    413 => 'Request Entity Too Large',
		    414 => 'Request-URI Too Long',
		    415 => 'Unsupported Media Type',
		    416 => 'Requested Range Not Satisfiable',
		    417 => 'Expectation Failed',
		    500 => 'Internal Server Error',
		    501 => 'Not Implemented',
		    502 => 'Bad Gateway',
		    503 => 'Service Unavailable',
		    504 => 'Gateway Timeout',
		    505 => 'HTTP Version Not Supported'
		);

		return (isset($codes[$status])) ? $codes[$status] : $codes[500];
	}      
}


