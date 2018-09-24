<?php

/**
 * Elastest - September 2018
 * Assignment from Elastique: Build a RESTful API (ref.: Assignment Backend - Elastique.pdf)
 * Eric Delerue delerue_eric@hotmail.com
 * https://github.com/EricDelerue/elastest (private repository)
 */

namespace Elastest\Controllers;

use \Elastest\Http\HttpRequestInterface;
use \Elastest\Http\HttpResponseInterface;

use \Elastest\Exceptions\InvalidArgumentException AS InvalidArgumentException;

class VerifyHttpRequestController implements VerifyHttpRequestControllerInterface {
	
	protected $connection;
	
	protected $request;
	protected $response;
	
	protected $client_timestamp;
	protected $server_timestamp;	
	protected $request_timeout;	
	
	//protected $public_key;
	//protected $secret_key;
	
	protected $use_csrf_token;
	protected $csrf_token;

	protected $endpoint;
	protected $verb;
	protected $keyword;
	protected $offset;
	protected $limit;
	protected $id;
	
	protected $allowed_origins = "localhost, 127.0.0.1, ericdelerue.com, dev.ericdelerue.com"; // Could be in config
	protected $allowed_endpoints = Array("books","publishers","authors"); // Could be in config
	protected $allowed_verbs = Array("list","highlighted","search");  // Could be in config

  protected $errors = Array();  
    
	/** 
		 * VERIFY HTTP REQUEST CONTROLLER.  
		 *	                                                                                      
		 * First controller: Verify Http request.   
		 * ALWAYS DONE: 
		 * 							- Check request timeout, method, csrf_token (state), endpoints/verbs/..., etc...                                                           
		 *              - Set $this->resource_types and $this->response_types   
		 *                                                                         
		 * @param HttpRequestInterface  $request  - Request object               
		 * @param HttpResponseInterface $response - Response object
		 *
		 * @return true or throws error
		 **/   
	public function __construct(HttpRequestInterface $request, HttpResponseInterface $response = null){
    
    $this->request = $request;
    //$this->response = $response;	
    
    $this->client_timestamp = $request->getClientTimestamp();
    $this->server_timestamp = $request->getServerTimestamp();
    
    $this->origin = $request->getOrigin();
		//$this->public_key = $request->getPubliKey();

    $this->csrf_token = $request->getCsrfToken();
    
    	
	}
	
	
	//public function handleVerifyHttpRequest(HttpRequestInterface $request, HttpResponseInterface $response){
	public function handleVerifyHttpRequest(array $config = array()){
    
    date_default_timezone_set('UTC');
    $now_for_mysql_date_time = date('Y-m-d H:i:s T', time()); 
    
    //$this->config = $config;
    
    $this->request_timeout = $config['request_timeout'] ?? 10; 
    $this->use_csrf_token = $config['use_csrf_token'] ?? false; 
    
    $this->checkRequestTimeout();
    $this->checkRequestOrigin(); // check Client's origin (i.e.: ericdelerue.com)
    $this->checkCsrfToken();
    //$this->checkPublicKey(); // check with Client's registered secret key
    
    $this->setRoute();

    $endpoint = $this->getEndPoint();
    $verb = $this->getVerb(); // check for resource id too 
    $id = $this->getResourceId();
    $keyword = $this->getSearchKeyword();
    $offset = $this->getSearchOffset();
    $limit = $this->getSearchLimit();

    $this->route['endpoint'] = $endpoint;	
    $this->route['verb'] = $verb;
    $this->route['id'] = $id;
    $this->route['keyword'] = $keyword;
    $this->route['offset'] = $offset;
    $this->route['limit'] = $limit;
    
	  return $this->route;


	}
	
  
  protected function checkRequestTimeout(){

  	if(isset($this->client_timestamp) && !is_null($this->client_timestamp) && !empty($this->client_timestamp)){

  	  if(($this->server_timestamp - $this->client_timestamp) > $this->request_timeout){

        $this->errors = array('error' => 400, 'title' => 'Request took too long', 'description' => 'The request to the API took too many time (more than 10 seconds). Please wait a few minutes and try again.', "script" => "HttpRequest.class.php", "line" => __LINE__);
        throw new RequestTimeoutException("Request took too long", 400, null, $this->errors);	 
          	  	
  	  }
  	  
    }
    
  }
  
  protected function checkRequestOrigin(){
 
      /*** ORIGIN ALLOWED ? ***/

		  if(isset($this->origin) && !is_null($this->origin)){
				      
					foreach (explode(',',$this->allowed_origins) as $allowed_origin) {
						
							if (preg_match('/^'.str_replace('\*','.*',preg_quote(strtolower(trim($allowed_origin)))).'$/',$this->origin)) { 
								//header('Access-Control-Allow-Origin: '.$origin);
								//$this->response_headers[] = "Access-Control-Allow-Origin: ".$this->origin;
								break;
							}
							
					}
				    
		  }  	    
	  
	}    


  protected function checkCsrfToken(){
	 
	    /*** CSRF TOKEN EXISTS ? ***/

		  //$headers[]='Access-Control-Expose-Headers: X-XSRF-TOKEN');
		  //$headers[]='Access-Control-Allow-Headers: Content-Type, X-XSRF-TOKEN';

	    if($this->use_csrf_token){
				  if( isset($this->csrf_token) && !is_null($this->csrf_token) ){
				      
			      
				    
				  }  	
	    }

	}

  protected function setRoute(){
	  	
	  	/**
	  	  * WARNING: because we use array_shift() to define the route, ORDER IS IMPORTANT 
	  	  **/
	    
	  	$this->setEndPoint(); // "books","authors","publishers"
	  	$this->setVerb(); // "list","highlighted","search" or empty / if {id} $this->setResourceId();
	  	$this->setSearchKeyword();
	  	$this->setSearchOffset();
	  	$this->setSearchLimit();
	  	//$this->setId(); --> set in $this->setVerb();
	  	
	  	if($this->getVerb() === 'search' && is_null($this->getSearchKeyword()) ) {
		     	
		      $this->errors = array('error' => 400, 'title' => 'Missing search parameter', 'description' => 'Missing search parameter: there should be a search keyword after the search verb. Please verify and try again.', "script" => "VerifyHttpRequestController.class.php", "line" => __LINE__);
		      throw new InvalidArgumentException("Missing search parameter.", 400, null, $this->errors);
		      
	  	}

  }

	/**                                                                                                           
	  * Set the main EndPoint: the ResourceType Books or Publishers or Authors which will be requested                                                                      
	  *  
	  * @param none                                                                                                                                              
	  * @return void               
	  *
    *
	  * EndPoint: always GET method to define the route  	
	  * The request $_REQUEST['request'] contains endpoint/id or endpoint/verb/ or endpoint/search/keyword/
	  * .htaccess puts everything after /elastique/ directory inside querystring "request" key
	  * RewriteRule ^(.*)$  api/v1.0/index.php?request=$1 [QSA,NC,L]
	  * RewriteRule ^(.*)/$ api/v1.0/index.php?request=$1 [QSA,NC,L]
	  *                                                                                                                                                                                              
	  **/                    
  protected function setEndPoint() : void {
	  	
	  	if( !is_string($this->request->getQueryStringValue('request')) ){
		     	
		      $this->errors = array('error' => 400, 'title' => 'Empty route', 'description' => 'Requested route is empty. Please verify and try again.', "script" => "VerifyHttpRequestController.class.php", "line" => __LINE__);
		      throw new InvalidArgumentException("Empty route.", 400, null, $this->errors);
		      
	  	}

	  	//$args = explode('/', rtrim($_REQUEST['request'], '/'));
	  	$args = explode('/', rtrim($this->request->getQueryStringValue('request'), '/'));
	  	$this->args = array_map('trim', $args); 
	  	/*     
	  	echo "this->args\n";
	  	print_r($this->args); 
	  	echo "\n";
			*/  	 	
	    
	    /**
	     * 1 - Get the main endpoint: the ResourceType Books or Publishers or Authors
	     *
	     */
	    if (array_key_exists(0, $this->args)) {
			    
			    $this->endpoint = array_shift($this->args); 
			    // preg_match("/\/([a-zA-Z])+/", $this->endpoint, $matches) 
			    
			    $allowed_endpoints = array_map('trim', $this->allowed_endpoints);

			    if(!in_array($this->endpoint,$allowed_endpoints)){
				     	
				      $this->errors = array('error' => 400, 'title' => 'Wrong endpoint', 'description' => 'Requested endpoint doesn\'t exist or was typed wrong. Please verify and try again.', 'endpoint' => '/'.$this->endpoint, "script" => "VerifyHttpRequestController.class.php", "line" => __LINE__);
				      throw new InvalidArgumentException("Wrong endpoint.", 400, null, $this->errors);
				      
			    }     

      }
   	    
  }
  
  public function getEndPoint() : ?string {
      
  	  return (isset($this->endpoint) && !is_null($this->endpoint)) ? $this->endpoint : null;

  }

	/**                                                                                                           
	  * Set the Verb following the EndPoint: the ResourceType Books or Publishers or Authors which will be requested                                                                      
	  *  
	  * @param none                                                                                                                                              
	  * @return void               
	  *
    *
	  * EndPoint: always GET method to define the route  	
	  * The request $_REQUEST['request'] contains endpoint/id or endpoint/verb/ or endpoint/search/keyword/
	  * .htaccess puts everything after /elastique/ directory inside querystring "request" key
	  * RewriteRule ^(.*)$  api/v1.0/index.php?request=$1 [QSA,NC,L]
	  * RewriteRule ^(.*)/$ api/v1.0/index.php?request=$1 [QSA,NC,L]
	  *                                                                                                                                                                                              
	  **/       
  protected function setVerb() : void {

	    // Verb: can be a string or an integer
	    if ( array_key_exists(0, $this->args) ) {
        	
        	// Edit or cancel or update action requested: to associate with method parameter     	  
        	// preg_match("/$this->endpoint\/([0-9])+/", $this->args[0], $matches)    	
	    	  if ( is_numeric($this->args[0]) ){ 
	    	  		
	    	    	$this->setResourceId();
	    	    	$this->verb = "id"; // _id() is the method called for CREATE, UPDATE, DELETE actions along with the method POST, PUT/PATCH, DELETE
	    	    	
	    		}

	    	  if ( array_key_exists(0, $this->args) && is_string($this->args[0]) ){
	    	  	
	    	  	  $this->verb = array_shift($this->args); 
              $this->id = null;
    
			        // "list","highlighted","search"
			        if(!in_array($this->verb,$this->allowed_verbs)){
      	
					        $this->errors = array('error' => 400, 'title' => 'Wrong verb after endpoint', 'description' => 'Wrong verb after endpoint. Please verify and try again.', 'verb' => '/'.$this->endpoint.'/'.$this->verb, "script" => "VerifyHttpRequestController.class.php", "line" => __LINE__);
					        throw new InvalidArgumentException("Wrong verb after endpoint", 400, null, $this->errors);
					       
			        }  
			        
	    	  }

	    } else {
					  	  	      	
					$this->errors = array('error' => 400, 'title' => 'No verb or id after endpoint', 'description' => 'THere must be a verb or an id after endpoint. Please verify and try again.', 'endpoint' => '/'.$this->endpoint.'/', "script" => "VerifyHttpRequestController.class.php", "line" => __LINE__);
					throw new InvalidArgumentException("No verb or id after endpoint", 400, null, $this->errors);
					 
	    }
      
  }
  
  public function getVerb() : ?string {

  	return (isset($this->verb) && !is_null($this->verb)) ? $this->verb : null;

  }
  
  
    
  protected function setResourceId() : void {

    if (array_key_exists(0, $this->args) && is_numeric($this->args[0])) {
      
      $this->id = intval(array_shift($this->args));

      if(!is_int($this->id) || intval($this->id) <= 0){
       	
        $this->errors = array('error' => 400, 'title' => 'Wrong id after endpoint', 'description' => 'Wrong id after endpoint/id. Please verify and try again.', 'endpoint' => '/'.$this->endpoint.'/', "script" => "VerifyHttpRequestController.class.php", "line" => __LINE__);
        throw new InvalidArgumentException("Wrong id after endpoint", 400, null, $this->errors);
        
      }     
                   
    }
    
  }       
  
  public function getResourceId(){

  	return (isset($this->id) && !is_null($this->id)) ? intval($this->id) : null;

  }
    
  /**
    * Third tag: keyword
    *
    *
    **/
    
  protected function setSearchKeyword() : void {

    if (array_key_exists(0, $this->args) && !is_numeric($this->args[0])) {
      
      $this->keyword = array_shift($this->args);

      //preg_match('/[^a-z_\-0-9àâéêèìôùûç]+/i', "Oh là c'est bien!", $matched);
      //preg_match('/^[\w-]+$/i', "Oh là c'est bien!", $matched);
      //preg_match('/^[\p{L}\p{N}_-]+$/u', "Oh là c'est bien!", $matched);
      //print_r($matched);

      if ( !preg_match('/^[a-z_\-0-9àâéêèìôùûç]+$/i', $this->keyword) ){
     	
        $this->errors = array('error' => 400, 'title' => 'Missing or wrong keyword after endpoint/search', 'description' => 'Missing or wrong keyword after endpoint/search. Please verify and try again.', 'endpoint' => '/'.$this->endpoint.'/'.$this->verb.'/', "script" => "VerifyHttpRequestController.class.php", "line" => __LINE__);
        throw new InvalidArgumentException("Missing or wrong keyword after endpoint/search", 400, null, $this->errors);
        
      }     
                   
    }
    
  }
  
  public function getSearchKeyword() : ?string {

  	return (isset($this->keyword) && !is_null($this->keyword)) ? $this->keyword : null;

  }

  /**
    * Fourth tag: offset
    *
    *
    **/
    
  protected function setSearchOffset() : void {

    if (array_key_exists(0, $this->args) && is_numeric($this->args[0])) {
      
      $this->offset = array_shift($this->args);

      if ( intval($this->offset) < 0 ){
      	
      		$this->offset = 0;
      		
      }
      
     	//if ( intval($this->offset) < 0 ){
     		
      //  $this->errors = array('error' => 400, 'title' => 'Wrong offset value', 'description' => 'Wrong offset value. Please verify and try again.', 'endpoint' => '/'.$this->endpoint.'/'.$this->verb.'/'.$this->keyword.'/'.$this->offset, "script" => "VerifyHttpRequestController.class.php", "line" => __LINE__);
      //  throw new InvalidArgumentException("Wrong offset value", 400, null, $this->errors);
        
      //}     
                   
    }
    
  }
  
  public function getSearchOffset() : ?int {

  	return (isset($this->offset) && !is_null($this->offset)) ? $this->offset : 0;

  }

  /**
    * Fifth tag: limit
    *
    *
    **/
    
  protected function setSearchLimit() : void {

    if (array_key_exists(0, $this->args) && is_numeric($this->args[0])) {
      
      $this->limit = array_shift($this->args);

      if ( intval($this->limit) < intval($this->offset) ){
      	
      		$this->limit = intval($this->offset) + 1;
      		
      }

      if ( intval($this->limit) < intval($this->offset) ){
     	
        $this->errors = array('error' => 400, 'title' => 'Wrong limit value', 'description' => 'Wrong limit value. Please verify and try again.', 'endpoint' => '/'.$this->endpoint.'/'.$this->verb.'/'.$this->keyword.'/'.$this->limit, "script" => "VerifyHttpRequestController.class.php", "line" => __LINE__);
        throw new InvalidArgumentException("Wrong limit value", 400, null, $this->errors);
        
      }     
                   
    }
    
  }
  
  public function getSearchLimit() : ?int {

  	return (isset($this->limit) && !is_null($this->limit)) ? $this->limit : 50;

  }
                    	  	
   	
	
	
}