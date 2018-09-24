<?php

/**
 * Elastest - September 2018
 * Assignment from Elastique: Build a RESTful API (ref.: Assignment Backend - Elastique.pdf)
 * Eric Delerue delerue_eric@hotmail.com
 * https://github.com/EricDelerue/elastest (private repository)
 */

namespace Elastest\Api;

use \Elastest\Exceptions\NotFoundException;
use \Elastest\Exceptions\InvalidArgumentException;
use \Elastest\Exceptions\UnexpectedValueException;

use \Elastest\Http\HttpRequestInterface;
use \Elastest\Http\HttpRequest;

use \Elastest\Http\HttpResponseInterface;
use \Elastest\Http\HttpResponse;

use \Elastest\Controllers\HttpRequestControllerInterface;
use \Elastest\Controllers\HttpRequestController;

use \Elastest\Config\Config AS Config;
use \Elastest\Storage\Pdo AS Pdo;

/**
 * This class manage the API configuration . This class will act as a wrapper for all of the custom endpoints that our API will be using. 
 *
 * The class will pass the request information on to a method of the class HttpController to actually perform the work. 
 * 
 * HttpController will: 
 * - take in our request, 
 * - grab the data provided in the headers or in the URI,
 * - grab the endpoint from the "request" key in the query string (see .htaccess), 
 * - detect the HTTP method (GET, HEAD, POST, PUT, PATCH, OPTIONS, DELETE) and 
 * - check and assemble any additional data provided in the header or in the URI. 
 * - pass the data ResourceController. 
 * 
 * It then return to this class which will handle the HTTP response and send it back to the client.
 */
      
class ElastestAPI extends Config { // Config is in namespace \Elastest\Config\

  /**
   * @var array
   */  
  protected $config;
  protected $options;
  
	protected $request;
	protected $response;
  
  /**
   * @var Pdo
   */
	protected $storage;
	
	/**
   * @var Array
   */	
	protected $resource_types;
	
	/**
   * @var Array
   */	
	protected $response_types;

  
  /**
   * @var HttpRequestControllerInterface
   */
  protected $HttpRequestController;

    
  //public function __construct($request, $origin) {  
  public function __construct() {
	    
	    // No more, it is private __construct now (singleton class)
	    //parent::__construct();
	    
	    // Ensure an INI file exists  
			$this->config = $this->getInstance()->getConfigArray();  

			/*		
			echo "<pre>";
			print_r($this->config);  
			echo "</pre>";                                                                       
			*/ 
			
			$this->options = array();  
			  
      /** 
       * Create PDO-based storage object
       * 'development_db_info' could become a ENVIRONMENT constant, with 'production_db_info': ENVIRONMENT.'db_info'
       */
 
	    $this->storage = new Pdo($this->config, $this->options);
			
			/*	
			echo "ElastestAPI";	
			echo "<pre>";
			print_r($this->storage);  
			echo "</pre>";                                                                       
      */

			// Merge all config values. These get passed to the HttpRequestController object then to the other controller objects
			$this->config = array_merge(array(  
			    'request_timeout' => 10,   
			    'csrf_token' => false,
			    'cache' => true,     		    
			    'cache_type' => 'TempFile', // (default) or 'No',
			    'cache_timeout' => 10,
			    'cache_path' => 'C:\Users\Surface\xampp\htdocs\elastique\cache', 
			    'offset' => 0,
			    'limit' => 50,                         
			), $this->config);
			
			/*				 
			echo "<pre>";
			print_r($this->config);  
			echo "</pre>";                                                        
      */

	    $this->resource_types = Array();
   
	    $this->response_types = Array();

  }	
  
  /**
    * Idea: this controller can become any of the existing controllers based on the request content
    * It is just an empty recipient
    * @return HttpRequestController   
    **/
	protected function createDefaultHttpRequestController() : HttpRequestController {
		
	/**
		* Default one. Then set the right Controller based on the request content. BooksController, AuthorsController, PublishersController 	
		* @param a storage object or array of storage objects to the HttpRequestController class
		* @param a config array
		* @param a resource_types array	?	
		*
		**/
		return new HttpRequestController($this->storage, $this->config, $this->resource_types);
		
	}
	  
  /**
    * Getter
    * @return HttpRequestController
    **/
  public function getHttpRequestController() : HttpRequestController {
    	
    if (is_null($this->HttpRequestController)) {
      $this->HttpRequestController = $this->createDefaultHttpRequestController();
    }
        
    return $this->HttpRequestController;
        
  }
  
  /** 
    * Setter
    * @param HttpRequestControllerInterface $HttpRequestController
    **/
  public function setHttpRequestController(HttpRequestControllerInterface $HttpRequestController) : void {
  	
        $this->HttpRequestController = $HttpRequestController;
        
  }
  
  /**
    * Main method
    *
    * @param HttpRequestInterface, $HttpResponseInterface  
    * @return HttpResponse
    *
    **/  
  public function handleHttpRequest(HttpRequestInterface $request, HttpResponseInterface $response = null) : HttpResponse {

  	$this->response = is_null($response) ? new HttpResponse() : $response;
  	// $this->response = $response ?? new HttpResponse();
  	
  	/**
  	  * We do all the stuff from here
  	  * This controller wraps any of the existing controllers, based on the request content
  	  * new HttpRequestController($this->storage, $this->config)->handleHttpRequest($request, $this->response)
  	  **/
  	$this->getHttpRequestController()->handleHttpRequest($request, $this->response);
  	
  	// Then just return the response
  	// The response will be send then $this->response->send() -> echo json_encode()
  	return $this->response; 
  	
  }

    
    
    
    
    

	
	
}

