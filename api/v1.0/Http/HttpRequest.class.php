<?php

/**
 * Elastest - September 2018
 * Assignment from Elastique: Build a RESTful API (ref.: Assignment Backend - Elastique.pdf)
 * Eric Delerue delerue_eric@hotmail.com
 * https://github.com/EricDelerue/elastest (private repository)
 * 
 */

namespace Elastest\Http;

use \DomainException;
use \DateTime;

use \LogicException;
use \Elastest\Exceptions\RequestTimeoutException AS RequestTimeoutException;
use \Elastest\Exceptions\UnexpectedValueException AS UnexpectedValueException;
use \Elastest\Exceptions\InvalidArgumentException AS InvalidArgumentException;

	
 /**
   *
   * - get client_timestamp
   * - get origin
   * - get api_version
   * - get headers
   * - get single header value
   * - get http accept
   * - get content type
   * - get request method
   * - get query values (get, post, put, delete)
   * - get single query value
   * - get csrf token
   * - clean inputs
   **/
class HttpRequest implements HttpRequestInterface {
	
	protected $server_timestamp;
	protected $client_timestamp;
	// Yet in config
	//protected $request_timeout = 10;

	protected $headers;
  protected $server;
	
  /**
    * Property: post_query_string
    * The POST parameters expected in request
    * @param array  $post_query_string 
    **/  
  protected $post_query_string;
	
  /**
    * Property: get_query_string
    * The GET parameters expected in request
    * @param array  $get_query_string 
    **/    
  protected $get_query_string;
	
  /**
    * Property: query_string
    * The GET or POST  or PUT or DELETE parameters expected in request
    * @param array  $query_string 
    **/
	protected $query_string;
	
  /**
    * Property: files
    * The $_FILES parameters
    * @param array  $files 
    **/	
  protected $files;
	
  /**
    * Property: cookies
    * The $_COOKIE parameters
    * @param array  $cookies  
    **/
  protected $cookies; // 

  protected $content;
	
  /**
    * Property: attributes
    * @param array  $attributes - The request attributes (parameters parsed from the PATH_INFO, ...)
    **/
  protected $attributes;

  /**
    * Property: is_ajax_request
    * Detect if the request is an AJAX request
    * @param bool  $is_ajax_request  
    **/	
	protected $is_ajax_request = false;
	
  /**
    * Property: http_accept
    * The HTTP ACCEPT the request expects in response, either JSON or XML
    * @param string  $http_accept  
    **/
	protected $http_accept = 'json';
      	
	protected $api_version; 
		
  /**
    * Property: content_type
    * The CONTENT TYPE the request carries, either JSON or XML
    * @param string  $content_type  
    **/
	protected $content_type;
	
  /**
    * Property: method
    * The HTTP method this request was made in, either GET, POST, PUT, PATCH or DELETE
    * @param string  $method  
    **/
  protected $method = '';
   
	/**                                                                                    
	  * Property: origin                                                                  
	  * The URL of the authorized client / application. eg: www.ericdelerue.com                                         
    * @param string  origin  
    **/
  protected $origin = '';

	/**                                                                                    
	  * Property: csrfToken                                                                  
	  * The CSRF TOKEN                                         
    * @param string  $csrf_token  
    **/ 	
	protected $csrf_token;
                                
  /**
    * Property: file
    * Stores the input of the PUT request
    * @param $file  
    **/ 
  protected $file = null;
    
  protected $errors = Array();  

		
	/**                                                                                              
	 * HttpRequest Constructor.                                                                                  
	 *                                                                                               
	 * @param array  $get_query_string      - The GET parameters                                                
	 * @param array  $post_query_string    - The POST parameters                                               
	 * @param array  $attributes - The request attributes (parameters parsed from the $_SERVER['PATH_INFO'], contains trailing pathname information that follows an actual filename or non-existent file in an existing directory)
	 * @param array  $cookies    - The COOKIE parameters                                             
	 * @param array  $files      - The FILES parameters                                              
	 * @param array  $server     - The SERVER parameters                                             
	 * @param string $content    - The raw body data                                                 
	 * @param array  $headers    - The headers                                                       
	 *                                                                                                                                                                                   
	 */    
	                                                                                           
  // $request = new HttpRequest($_GET, $_POST, array(), $_COOKIE, $_FILES, $_SERVER, null, null);
  public function __construct(
    Array $get_query_string = Array(), // $_GET
    Array $post_query_string = Array(), // $_POST 
    Array $attributes = Array(), // Array()
    Array $cookies = Array(), // $_COOKIE
    Array $files = Array(), // $_FILES
    Array $server = Array(), // $_SERVER
    ?string $content,  // Can be null
    ?array $headers)   // Can be null
    {
	  	
	  	$this->server_timestamp = microtime(true); 
	   	
	  	$this->server = $server;
	    $this->post_query_string = $post_query_string;
	    $this->get_query_string = $get_query_string;
	    
	    $this->attributes = $attributes;

	    $this->cookies = $cookies;
	    $this->files = $files;
	    
	    $this->content = $content;        	    
	    $this->headers = $headers;    

  }
  
  /*** HEADERS ***/
    
  private function getHeadersFromServer(array $server = null): array {
	  	
	  	$headers = array();
	  	
		  foreach($server as $i=>$val) {
			  $header_name = str_replace(array('HTTP_', '_'), array('', '-'), $i);		      
			  $headers[$header_name] = $val;
			}  

			return $headers;

  } 

  private function setHeaders(){
  	  	
    	//$this->headers = is_null($this->headers) ? $this->getHeadersFromServer($this->server) : $headers; 
    	$this->headers = $this->headers ?? $this->getHeadersFromServer($this->server);
    	
  }
         
  public function getHeaders() : array {
  			
			return $this->headers;

  }
  
  public function getHeaderValue(string $header_name = null, string $default = '') : string {

  		return $this->headers[$header_name] ?? $default;

  }
  
  /*** CONTENT TYPE ***/
    
  private function setContentType() : void {
	
  	$this->content_type = $this->getHeaderValue('CONTENT-TYPE');

  }
         
  public function getContentType() : string {
  			
			return $this->content_type;

  }

  
  /*** HTTP ACCEPT ***/
    
  private function setHttpAccept() : void {
	
    $this->http_accept = (strpos($this->getHeaderValue('ACCEPT'), 'json')) ? 'json' : 'xml';  	

  }
         
  public function getHttpAccept() : string {
  			
			return $this->http_accept;

  }

  
  /*** API VERSION ***/
    
  private function setApiVersion() : void {
	
  	$this->api_version = $this->getHeaderValue('ACCEPT');

  }
         
  public function getApiVersion() : string {
  			
			return $this->api_version;

  }

   
  /*** ORIGIN ***/
  
  private function setOrigin() : void {

  		//$this->origin = (isset($this->headers['HTTP-ORIGIN']) && !is_null($this->headers['HTTP-ORIGIN'])) ? $this->headers['HTTP-ORIGIN'] : $this->headers['SERVER-NAME'];
  		$this->origin = $this->headers['HTTP-ORIGIN'] ?? $this->headers['SERVER-NAME'];
  	
  }
  
  public function getOrigin() : string {

  	return $this->origin;

  }
  
  /*** CSRF TOKEN ***/
  
  private function setCsrfToken() : void {

  		$this->csrf_token = (isset($this->headers['X-XSRF-TOKEN']) && !is_null($this->headers['X-XSRF-TOKEN'])) ? $this->headers['X-XSRF-TOKEN'] : null;
  		//$this->csrf_token = $this->headers['X-XSRF-TOKEN'] ?? null;
  	
  }
  
  public function getCsrfToken() : ?string {

  	return $this->csrf_token;

  }
    
  /*** AJAX ***/
    
  public function isAjaxRequest() : bool {

		// Is it an AJAX request?                                                                                                                       			
		return (bool)( (array_key_exists('X-REQUESTED-WITH',$this->headers)) && ('XMLHttpRequest' == @$this->headers['X-REQUESTED-WITH']) );

  }
   
  private function setIsAjaxRequest(){
  	
  	$this->is_ajax_request = $this->isAjaxRequest();
  	
  }
   
  /*** METHOD ***/
  
  // Return type void it means you cannot return anything or you will get an error 
  private function setMethod() : void {
  	
  	$this->method = $this->getHeaderValue('REQUEST-METHOD');
  	
    //if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
    if ($this->method == 'POST' && array_key_exists('X-METHOD', $this->headers)) {
      if ($this->headers['X-METHOD'] == 'DELETE') {
        $this->method = 'DELETE';
      } else if ($this->headers['X-METHOD'] == 'PUT') {
        $this->method = 'PUT';
      } else {

  	  	// TO DO:
  	    // We should use the response->setError here. Move this control into HttpRequestController.
  	    // Or check request->errors in HttpRequestController        	
        $this->errors = array('error' => 405, 'title' => 'Unexpected Header', 'description' => 'Expected DELETE or PUT methods in HTTP_X_HTTP_METHOD header when doing a POST request. Please verify and try again.', 'header' => $this->headers['X-METHOD'], "script" => "HttpRequest.class.php", "line" => __LINE__);
        throw new InvalidArgumentException("Unexpected Header", 405, null, $this->errors);  
                  	                 
      }
    }
  	
  }

  public function getMethod() : string {
  	
  	return $this->method; 

  }
   
  /*** QUERY STRING ***/
  
	/**                                                                                                           
	 * Returns the request body content.                                                                          
	 *                                                                                                            
	 * @param boolean $asResource - If true, a resource will be returned                                          
	 * @return string|resource    - The request body content or a resource to read the body stream.               
	 *                                                                                                            
	 * @throws LogicException                                                                                     
	 */                                                                                                           
	private function getContent(bool $as_resource = false) { 
		                                                                                                            
	    if (false === $this->content || (true === $as_resource && null !== $this->content)) { 
	    		    	                     
	        throw new \LogicException('getContent() can only be called once when using the resource return type.');	        
	    }                                                                                                         
	                                                                                                              
	    if (true === $as_resource) { 	    	                                                                              
	        $this->content = false;                                                                               	                                                                                                              
	        return fopen('php://input', 'rb');  	                                                                          
	    }                                                                                                         
	                                                                                                              
	    if (null === $this->content) { 	    	                                                                           
	        $this->content = file_get_contents('php://input');   	                                                         
	    }                                                                                                         
	                                                                                                              
	    return $this->content;                                                                                    
	}                                                                                                             

  private function setQueryString() : void {
	  	
	  	// TO DO: clean query string from endpoint/verb/keyword/id and get only $_POST or $_GET querystring
			  	
			switch(strtoupper($this->method)) {                                                      
			case 'DELETE':                                                               
			case 'POST':                                                                 
			    $this->query_string = $this->cleanInputs($this->post_query_string);                       
			break;                                                                   
			case 'GET':                                                                  
			    $this->query_string = $this->cleanInputs($this->get_query_string);  
			    // i.e.: Array ( [request] => books/search/elastique )                       
			break;                                                                   
			case 'PUT':                                                                  
			    $this->query_string = $this->cleanInputs($this->get_query_string);                        
			    //$this->file = file_get_contents("php://input");      
			    $this->file = $this->getContent(false);                    
					/*                                                                       
					$_PUT  = array();                                                        
					parse_str(file_get_contents('php://input'), $_PUT);                      
					// basically, we read a string from PHP's special input location,        
					// and then parse it out into an array via parse_str... per the PHP docs:
					// Parses str as if it were the query string passed via a URL and sets   
					// variables in the current scope.                                       
					parse_str(file_get_contents('php://input'), $put_vars);                  
					$data = $put_vars;                                                       
					*/                                                                       
	    break;
	    default:
	  	  	
	  	  // We should use the response->setError here and move this control into HttpRequestController.
	  	  // Or check request->errors in HttpRequestController    
	      $this->errors = array('error' => 405, 'title' => 'Invalid Method', 'description' => 'Only GET, POST, PUT and DELETE methods are valid. Please verify and try again.', 'method' => $this->method, "script" => "HttpRequest.class.php", "line" => __LINE__);
	      throw new InvalidArgumentException("Invalid Method", 405, null, $this->errors);
	      
	    }

	    // Shouldn't be better to get query string first when it is a GET request and then set the route?
	    //unset($this->query_string['request']);
	    
  }

	/**                                                                                                           
	 * Returns the cleaned query string.                                                                          
	 *                                                                                                                                                
	 * @return string|array       - The query string as an array.               
	 *                                                                                                                                                                                             
	 */   
  public function getQueryString(){
  	
  	return $this->query_string; 

  }

	/**                                                                                                           
	 * Returns the value of a key in the query string.                                                                          
	 *                                                                                                                                                
	 * @return string|integer|array|mixed      - The value of the key.               
	 *
   * Usage: $request->getQueryStringValue('encrypted_request');  
   *	                                                                                                                                                                                              
	 */  
  public function getQueryStringValue($key_name = null, $default = '') {

  	return (isset($this->query_string[$key_name]) && !is_null($this->query_string[$key_name])) ? $this->query_string[$key_name] : $default;
  	
  	
  }
   
  /*** REQUEST TIMEOUT ***/

	/**                                                                                                           
	 * Set the client timestamp.                                                                          
	 *                                                                                                                                                
	 * @return type void, return nothing or error               
	 *                                                                                                                                                                                             
	 */  
  private function setClientTimestamp() : void {
    
    // For demo purpose only, we set X-MICROTIME header to $this->server_timestamp + floatval(0.2)
  	$this->client_timestamp = $this->getHeaderValue('X-MICROTIME') !== '' ? $this->getHeaderValue('X-MICROTIME') : $this->server_timestamp + floatval(0.2); // $this->getHeaderValue('X-MICROTIME') ?? floatval($this->server_timestamp);

  }

	/**                                                                                                           
	 * Get the client timestamp.                                                                          
	 *                                                                                                                                                
	 * @return float   - The client timestamp as a float.               
	 *                                                                                                                                                                                             
	 */   
  public function getClientTimestamp() : float {

  	return $this->client_timestamp; 

  }
  
  public function getServerTimestamp() : float {

  	return $this->server_timestamp; 

  }
  

	/**
	 * Build the request with values from PHP's super globals.                  
	 *                                                                              
	 * @return HttpRequest - A new request                                              
	 *                                                                                                                                                     
	 **/  
  public static function buildRequestFromGlobals() : HttpRequest {

		// HttpRequest class is called	
    $class = get_called_class(); 
    // request is now an instance of HttpRequest class   
    $request = new $class($_GET, $_POST, Array(), $_COOKIE, $_FILES, $_SERVER, null, null);

  	// Mind the order
    
  	$request->setHeaders(); // Was done in the __construct function before
  	
  	$request->setClientTimestamp();
 	  	
  	$request->setOrigin();

    $request->setHttpAccept();

  	$request->setContentType();

  	$request->setMethod();

  	$request->setQueryString();
  	
  	$request->setIsAjaxRequest();

	  if (0 === strpos($request->content_type, 'application/x-www-form-urlencoded') && in_array(strtoupper($request->method), array('PUT', 'DELETE'))) { 
	                                                                            
	    parse_str($request->getContent(), $data);                               
	    $request->query_string = $data;   
	                                                   
	  } elseif (0 === strpos($request->content_type, 'application/json') && in_array(strtoupper($request->method), array('POST', 'PUT', 'DELETE'))) { 
	                                                                            
	    $data = json_decode($request->getContent(), true);                      
	    $request->query_string = $data;   
	                                               
	  }  	
	  	
	  /*	
		 Here we know:       
		                       
		$this->server_timestamp
		$this->client_timestamp
		$this->headers         
		$this->http_accept  
		$this->api_version     
		$this->method          
		$this->query_string  
		this->csrf_token  
		$this->file
    $this->files
		*/             

  	
  	return $request;
  	
  }
	  
	private function cleanInputs($data) /* : array */ {                  
	    $clean_input = array();                            
	    if (is_array($data)) {                             
	        foreach ($data as $k => $v) {                  
	            $clean_input[$k] = $this->cleanInputs($v);
	        }                                              
	    } else {       
	    	  //$clean_input[0] = ??                                   
	        $clean_input = trim(strip_tags($data));        
	    } 
	    //print_r($clean_input)."\n";                                                 
	    return $clean_input;                               
	}                                                      


}
