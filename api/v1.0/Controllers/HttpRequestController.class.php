<?php

/**
 * Elastest - September 2018
 * Assignment from Elastique: Build a RESTful API (ref.: Assignment Backend - Elastique.pdf)
 * Eric Delerue delerue_eric@hotmail.com
 * https://github.com/EricDelerue/elastest (private repository)
 */

namespace Elastest\Controllers;

use \Elastest\Http\HttpRequestInterface;
use \Elastest\Http\HttpResponse;
use \Elastest\Http\HttpResponseInterface;

use \Elastest\ResourceTypes\ResourceTypeInterface;
use \Elastest\ResourceTypes\ResourceFactory;
use \Elastest\ResourceTypes\Books AS BooksResourceType;
use \Elastest\ResourceTypes\Authors AS AuthorsResourceType;
use \Elastest\ResourceTypes\Publishers AS PublishersResourceType;


use \Elastest\Storage\Pdo;
/*
use Elastest\Storage\BooksInterface;
use Elastest\Storage\AuthorsInterface;
use Elastest\Storage\PublishersInterface;
*/

use \Elastest\ResponseTypes\ResponseTypeInterface;
use \Elastest\ResponseTypes\Books AS BooksResponseType;
use \Elastest\ResponseTypes\Authors AS AuthorsResponseType;
use \Elastest\ResponseTypes\Publishers AS PublishersResponseType;

use \Elastest\Exceptions\InvalidArgumentException AS InvalidArgumentException;
use \Elastest\Exceptions\ElastestException AS ElastestException;


/**
 * The idea: this controller can become any of the existing controllers based on the requested content (resource type)
 * 
 * Then passes the right storage Interface and ResourceType to (or a ResourceType object with its own storage and response type???):
 * 
 * ResourceController(ResourceType(Storage), config array, HttpResponse) then dig in the mine (Pdo or Cache) and give back the response
 * 
 * ResourceController is just an empty recipient for
 * 
 * ResourceType
 * ResourceController(ResourceType, config, HttpResponse) // config with cache : true or false and cache timeout
 * BooksController()
 * AuthorsController()
 * PublishersInfoController()
 * 
 * HttpRequestController class for Elastest api
 * This class serves as a convenience class which wraps the other Controller classes
 * 
 * @see \Elastest\Controllers\BooksController
 * @see \Elastest\Controllers\AuthorsController
 * @see \Elastest\Controllers\PublishersController
 */

class HttpRequestController implements HttpRequestControllerInterface,
																			 VerifyHttpRequestControllerInterface,
																			 ResourceControllerInterface /*,  
                                       BooksControllerInterface,
                                       AuthorsControllerInterface, 
                                       PublishersInfoControllerInterface*/
                                       {
	
	protected $request;
	protected $response;

	/**                      
	 * @var array            
	 */ 	
	protected $storages;

	/**                      
	 * @var array            
	 */ 	     	
	protected $route;  
	   
	/**                      
	 * @var array            
	 */ 	     	
	protected $resource_types;  
	                         
	/**                      
	 * @var array            
	 */                      
	protected $response_types;

	/**                                                                     
	 * @var array                                                           
	 */                                                                     
	protected $storage_map = array(     
		'books' => 'Elastest\Storage\BooksInterface',                
		'authors' => 'Elastest\Storage\AuthorsInterface',
		'publishers' => 'Elastest\Storage\PublishersInterface'			
	);                                                                      

	protected $resource_types_map = array(                  
	  // Books               
	  'books' => 'Elastest\ResourceTypes\Books',
	  // Authors    
	  'authors' => 'Elastest\ResourceTypes\Authors',	
	  // Publishers
	  'publishers' => 'Elastest\ResourceTypes\Publishers'
	);                                                             
  
  // Temporary, the response types can be json or xml too 
  // Or it names the response array? "books", "authors", "publishers"                                                         
	protected $response_types_map = array(                             
	  'books' => 'Elastest\ResponseTypes\BooksInterface',       
	  'authors' => 'Elastest\ResponseTypes\AuthorsInterface',
	  'publishers' => 'Elastest\ResponseTypes\PublishersInterface',  
	);                                                             
	           
	
	protected $VerifyHttpRequestController;		

  /**
   * @var ResourceTypeInterface
   */
	protected $resourceType;

  /**
   * @var ResourceControllerInterface
   */
  protected $resourceController;

	/**                                                                                                                                                  
	 * @param mixed                        $storage               (array or Elastest\Storage) - single object or array of objects implementing the           
	 *                                                            required storage types (BooksInterface or AuthorsInterface or PublishersInterface as a minimum)
	 * @param array                        $config                specify a different token lifetime, token header name, etc                               
	 * @param array                        $resource_types        An array of Elastest\ResourceTypes\ResourceTypeInterface to use for asking access to the requested resource        
	 * @param array                        $response_types        Response types to use. array keys should be "json" or "xml" (HTTP ACCEPT)                                                                                                                                 
   *
	 */	                                                                                                                                                 

	public function __construct(
			$storage = null, 
			$config = array()/*, 
			array $resource_types = array(), 
			array $response_types = array())*/
			)
			{

  	/**
  	 * Elastest\Storage\Pdo or array
  	 * single object (Pdo object) or array of objects implementing the required storage types (BooksInterface, AuthorsInterface and/or PublishersInterface: one of these minimum)
  	 *
  	 **/
    $storage = is_array($storage) ? $storage : array($storage);

    /*
		echo "storage<pre>";
		print_r($storage);  
		echo "</pre>";   
		*/
			   
    $this->storages = array();
    
    foreach ($storage as $key => $service) {
      $this->registerStorage($service, $key);      
    }

		// Merge all config values.  These get passed to our controller objects
		$this->config = array_merge(array(  
					'show_errors' => true,                                   
			    'request_timeout' => 10,   
			    'csrf_token' => false,
			    'default_resource' => 'books',
			    /*'cache' => true,     
			    'cache_timeout' => 3600, 			    
			    'offset' => 0,
			    'limit' => 50,  */                       
		), $config);   

	}
  

	/**  
   * Main method	                                                                                     
	 * Handle the http request.                                                              
	 *                                                                                        
	 * @param HttpRequestInterface  $request  - Request object               
	 * @param HttpResponseInterface $response - Response object  
	 * @return HttpResponse                                 
	 */                                                                                       
	public function handleHttpRequest(HttpRequestInterface $request, HttpResponseInterface $response = null) : HttpResponse { 

      $this->request = $request;
      
  	  $this->response = is_null($response) ? new HttpResponse() : $response;
  	  // $this->response = $response ?? new HttpResponse();

			/** 
			 * VERIFY HTTP REQUEST CONTROLLER.  OR ROUTER CLASS?
			 *	                                                                                      
			 * First controller: Verify Http request.   
			 * ALWAYS DONE: 
			 * 							- Check request timeout, method, csrf_token (state), endpoints/verbs/..., etc...                                                           
			 *              - Set $this->resource_types and $this->response_types    
			 *                                                                         
			 * @return the route array or throws error
			 */    
	    if( !$this->route = $this->handleVerifyHttpRequest($this->config) ){
	    	
	        $this->errors = array('error' => 400, 'title' => 'Invalid request', 'description' => 'The request to the API is not correct. Please verify and try again.', "script" => "HttpRequestController.class.php", "line" => __LINE__);
	        throw new ElastestException("Invalid request", 400, null, $this->errors);	 
	         	
	    }

	    // We now know the route, so we can build the right resource type

		  // If there's no storage object implemented so no resource requested    
	    if ( !isset($this->route['endpoint']) || empty($this->route['endpoint']) ) { 
	    	  
	    	  $this->errors = array('error' => 400, 'title' => 'Missing EndPoint', 'description' => 'You must supply an EndPoint in the route array. Please verify and try again.', "script" => "HttpRequestController.class.php", "line" => __LINE__);
	    		throw new InvalidArgumentException("Missing EndPoint", 400, null, $this->errors);

	    }

	    $resource_type_identifier = strtolower($this->route['endpoint']);
	    //echo "resource_type_identifier: ".$resource_type_identifier."\n";
	    
		  // If there's no storage object implemented so no resource requested    
	    if ( !isset($this->storages) || empty($this->storages) ) { 
	    	  
	    	  $this->errors = array('error' => 400, 'title' => 'Missing storage object', 'description' => 'You must supply a storage object implementing Elastest\Storage\BooksInterface or/and Elastest\Storage\AuthorsInterface or/and Elastest\Storage\Publishers Interface to use the resource server. Please verify and try again.', "script" => "HttpRequestController.class.php", "line" => __LINE__);
	    		throw new InvalidArgumentException("Missing storage object", 400, null, $this->errors);

	    }

	    if (!isset($this->storages[$resource_type_identifier]) || !$this->storages[$resource_type_identifier] instanceof Pdo  )	{
	    	  
	    	  $this->errors = array('error' => 400, 'title' => 'Missing storage object', 'description' => 'You must supply a storage object implementing Elastest\Storage\BooksInterface or/and Elastest\Storage\AuthorsInterface or/and Elastest\Storage\Publishers Interface to use the resource server. Please verify and try again.', "script" => "HttpRequestController.class.php", "line" => __LINE__);
	    		throw new InvalidArgumentException("Missing storage object", 400, null, $this->errors);

	    } 	 
	       
		  /** 
			 * RESOURCE TYPE FACTORY  
			 *
			 * Returns the right ResourceType based on the $resource_type_identifier 
			 * Couldn't it be a kind of token or ticket with all the necessary info inside it to proceed with the resource Controller?
			 * Say: the source, the storage object, the requested action (edit, update, cancel, list, search) and corresponding info?	   
			 *                                                                                                                                                                
			 * @param string $resource_type_identifier 
			 * @param Pdo $this->storages[$resource_type_identifier] registered in $this->storages with registerStorage($service, $key);     
			 *
			 * @return ResourceType object (Books, Authors or Publishers) along with the proper storage object or throws error
			 *
			 */   
	    $this->resourceType = ResourceFactory::factory(
					$resource_type_identifier, 
					$this->storages[$resource_type_identifier]
					);

	    return $this->getResourceController()->handleResourceRequest($this->resourceType, $this->response, $this->route);
                                                                         
	}                                                                                         
    
	/** 
		 * VERIFY HTTP REQUEST CONTROLLER.  
		 *	                                                                                      
		 * First controller: Verify Http Request data.                                                              
		 *                                                                                        
		 * @param HttpRequestInterface  $request  - Request object               
		 * @param HttpResponseInterface $response - Response object                  
		 **/           
  protected function getVerifyHttpRequestController() : VerifyHttpRequestController {
    	
    if (is_null($this->VerifyHttpRequestController)) { 
    	   	
    	//$this->VerifyHttpRequestController = new VerifyHttpRequestController();
      $this->VerifyHttpRequestController = new VerifyHttpRequestController( $this->request, $this->response);
      
    }
        
    return $this->VerifyHttpRequestController;
      
  }
    
	/**                                                                                                                 
	 * @param HttpRequestInterface  $request  - Request object                                                              
	 * @param HttpResponseInterface $response - Response object                                                                                                                                   
	 * @return TRUE or throws an error                                                                                                        
	 */  
	// public function handleVerifyHttpRequest(HttpRequestInterface  $request, HttpResponseInterface $response = null) {                                                                                                                
	public function handleVerifyHttpRequest(array $config = null) {
	                                           
		  //$this->getVerifyHttpRequestController()->handleVerifyHttpRequest($this->request, $this->response);
		  return $this->getVerifyHttpRequestController()->handleVerifyHttpRequest($config);              
	                                                                                                               
	}                                                                                                                   
     
  /**
    * RESOURCE CONTROLLER
    * 
    * of any known endpoint 
    *
    * @return ResourceController
    *
    * This controller can become any of the existing controllers based on the request content
    * This is the where and the how the data is extracted and formated
    *
    * Usage: $this->getResourceController()->handleResourceRequest(ResourceTypeInterface $resourceType, HttpResponseInterface $response);
    *
    * @return ResourceController   
    **/
	protected function createDefaultResourceController() : ResourceController {
				
		/**
			* Default one. Then set the right ResourceController based on the request content. BooksController, AuthorsController, PublishersController 	
			* @param the ResourceType object along with its corresponding storage object
			* @param the config array
			* @param the HttpResponseInterface object
			*
			**/
			return new ResourceController($this->config, $this->route);
				
	}

  /**
    * Getter
    * @return ResourceControllerInterface
    **/
  public function getResourceController() : ResourceController {
	    	
	    if (is_null($this->resourceController)) {
	      		$this->resourceController = $this->createDefaultResourceController();
	    }
	        
	    return $this->resourceController;
	        
  }
  
  /** 
    * Setter
    * @param ResourceControllerInterface $resourceController
    **/
  public function setHttpRequestController(ResourceControllerInterface $resourceController) : void {
  	
        $this->resourceController = $resourceController;
        
  }

	/**                                                                                                                                                                 
	 * Set a storage object for the server                                                                                                                              
	 *                                                                                                                                                                  
	 * @param object $storage - An object implementing one of the Storage interfaces                                                                                    
	 * @param mixed $key - If null, the storage is set to the key of each storage interface it implements                                                               
	 *                                                                                                                                                                  
	 * @throws InvalidArgumentException                                                                                                                                 
	 * @see storage_map                                                                                                                                                                                                                                                                                                                  
  */  
  public function registerStorage($storage, $key = null) : void {
					                                                                                                                                                        
			$set = false;                                                                                                                                                                             
			                                                                                                                                                                                          
			foreach ($this->storage_map as $type => $interface) {          
				  //echo "\n<br>type ".$type."\n<br>";
				  //echo "\n<br>interface ".$interface."\n<br>";                                                                                                                         
			    if ($storage instanceof $interface) {                                                                                                                                                 
			        $this->storages[$type] = $storage;                                                                                                                                                
			        $set = true;                                                                                                                                                                      
			    }                                                                                                                                                                                     
			}                                                                                                                                                                                         
			  		                                                                                                                                                                                    
			if (!$set) { 
				                                                                                                                                                                             
	        $this->errors = array('error' => 400, 'title' => 'Invalid storage object', 'description' => sprintf('Storage of class "%s" must implement one of [%s]', get_class($storage), implode(', ', $this->storage_map)).'. Please wait a few minutes and try again.', "script" => "HttpRequestController.class.php", "line" => __LINE__);
	        throw new InvalidArgumentException("Invalid storage object", 400, null, $this->errors);	 			                                                                                                                                                                                          
                                                                                                                                                                                         
			} 

  }         

	/**                                                                            
	 * @param ResourceTypeInterface    $resource_type                                        
	 * @param mixed (integer | string) $key                                              
	 */                                                                            
	public function addResourceType(ResourceTypeInterface $resource_type, $identifier = null){           
		                                                                   
	    if (!is_string($identifier)) { 
	    	                                            
	        $identifier = $resource_type->getQueryStringIdentifier();    
	                      
	    }                                                                          
	                                                                               
	    $this->resource_types[$identifier] = $resource_type;                               
                                                                
	}                                                                              

	/**                                                                                                                                                                                  
	 * @param ResponseTypeInterface $responseType                                                                                                                                        
	 * @param mixed                 $key                                                                                                                                                 
	 *                                                                                                                                                                                   
	 * @throws InvalidArgumentException                                                                                                                                                  
	 */                                                                                                                                                                                  
	public function addResponseType(ResponseTypeInterface $response_type, $key = null){        
		                                                                                                                                                                            
	    $key = $this->normalizeResponseType($key);                                                                                                                                       
	                                                                                                                                                                                     
	    if (isset($this->response_type_map[$key])) {      
	    	                                                                                                                                 
	        if (!$response_type instanceof $this->response_type_map[$key]) {                                                                                                                
	            throw new InvalidArgumentException( sprintf('Response Type of type "%s" must implement interface "%s"', $key, $this->response_type_map[$key]).". Line ".__LINE__.".", 400);                             
	        }       
	                                                                                                                                                                             
	        $this->response_types[$key] = $response_type; 
	                                                                                                                                         
	    } elseif (!is_null($key) && !is_numeric($key)) {  
	    	                                                                                                                               
	        throw new InvalidArgumentException( sprintf('Unknown Response Type key "%s", must be one of [%s]', $key, implode(', ', array_keys($this->response_type_map))).". Line ".__LINE__.".", 400);   
	                      
	    } else { 
	    	                                                                                                                                                                        
	        $set = false; 
	                                                                                                                                                                       
	        foreach ($this->response_type_map as $type => $interface) {   
	        	                                                                                                                 
	            if ($response_type instanceof $interface) {                                                                                                                               
	                $this->response_types[$type] = $response_type;                                                                                                                         
	                $set = true;                                                                                                                                                         
	            }         
	                                                                                                                                                                           
	        }                                                                                                                                                                            
	                                                                                                                                                                                     
	        if (!$set) {                                                                                                                                                                 
	            throw new InvalidArgumentException( sprintf('Unknown Response Type %s.  Please implement one of [%s]', get_class($response_type), implode(', ', $this->response_type_map)).". Line ".__LINE__.".", 400);
	        }   
	                                                                                                                                                                                 
	    }                                                                                                                                                                                
	}                                                                                                                                                                                    

	/**                                                                
	 * @param string $name                                             
	 * @return string                                                  
	 */                                                                
	protected function normalizeResponseType($name){                                                                  
	    // for multiple-valued response types - make them alphabetical 
	    if (!empty($name) && false !== strpos($name, ' ')) {           
	        $types = explode(' ', $name);                              
	        sort($types);                                              
	        $name = implode(' ', $types);                              
	    }                                                              
	                                                                   
	    return $name;                                                  
	}                                                                                                                                                                                                                                                                                               

	/**                                                                                                                                                                                      
	 * @return array                                                                                                                                                                         
	 * @throws InvalidArgumentException                                                                                                                                                      
	 */                                                                                                                                                                                      
	protected function getDefaultResourceTypes() {                                                                                                                                              
		                                                                                                                                                                                       
	  $resource_types = array();   
	  $config = array();                                                                                                                                                                
	                                                                                                                                                                                         
	  if (isset($this->storages['books'])) {                                                                                                                                      
	                                                                                                                                                                                         
	    $resource_types['books'] = new BooksResourceType($this->storages['books'], $config);                                                                                                 
	                                                                                                                                                                                         
	  }                                                                                                                                                                                      
	                                                                                                                                                                                         
	  if (isset($this->storages['authors'])) {                                                                                                                                    
                                                                           
	    $resource_types['authors'] = new AuthorsResourceType($this->storages['authors'], $config);                                                                          
	                                                                                                                                                                                         
	  }                                                                                                                                                                                      
	                                                                                                                                                                                         
	  if (isset($this->storages['publishers'])) {                                                                                                                                         
                                       
	    $resource_types['publishers'] = new PublishersResourceType($this->storages['publishers'], $config);                                                                                         
	                                                                                                                                                                                         
	  }                                                                                                                                                                                      
                                                                                                                                                                                     
	  if (count($resource_types) == 0) {                                                                                                                                                        
	    	                                                                                                                                                                                   
	    throw new InvalidArgumentException('Unable to build default resource types - You must supply an array of resource types in the constructor');                                              
	                                                                                                                                                                                         
	  }                                                                                                                                                                                      
	                                                                                                                                                                                         
	  return $resource_types;                                                                                                                                                                   
	                                                                                                                                                                                         
	}                                                                                                                                                                                        

	/**                                                                                                                                                                                                                                                                                                          
	 * @return array                                                                                                                                                                                                                                                                                             
	 * @throws InvalidArgumentException                                                                                                                                                                                                                                                                          
	 */                                                                                                                                                                                                                                                                                                          
	protected function getDefaultResponseTypes() {                                                                                                                                                                                                                                                               
		                                                                                                                                                                                                                                                                                                           
	  $response_types = array();                                                                                                                                                                                                                                                                                 
	                                                                                                                                                                                                                                                                                                             
    // TO DO                                                                                                                                                                                                                                                                                                     
	                                                                                                                                                                                                                                                                                                             
	  return $response_types;                                                                                                                                                                                                                                                                                    
	                                                                                                                                                                                                                                                                                                             
	}                                                                                                                                                                                                                                                                                                            
                                                                                                                                                
  /********** IMPORTANT *****************/	                                                                                                                                                    
	                                                                                                                                                    
	/**                                                                                                                                                 
	 * @param ResourceTypeInterface $resourceType  - Resource object               
	 * @param HttpResponseInterface $response       - Response object                                                                                                                                                                                 
	 * @return mixed                                                                                                                                    
	 */                                                                                                                                                 
	public function validateResourceRequest(ResourceTypeInterface $resourceType, HttpResponseInterface $response) {                       
			                                                                                                                                                  
		  $this->response = is_null($response) ? new HttpResponse() : $response;                                                                            
		  $value = $this->getResourceController()->validateResourceRequest($request, $this->response);                                                
		  return $value;                                                                                                                                    
		                                                                                                                                                    
	}                                                                                                                                                   
	                                                                                                                                                    
	/**                                                                                                                                                 
	 * @param ResourceTypeInterface $resourceType  - Resource object               
	 * @param HttpResponseInterface $response       - Response object                                                                                                                                                                                   
	 * @return mixed                                                                                                                                    
	 */                                                                                                                                                 
	public function handleResourceRequest(ResourceTypeInterface $resourceType, HttpResponseInterface $response = null) {    
		                   
        $this->response = $response;
        $this->getResourceController()->handleResourceRequest($resourceType, $this->response);
        return $this->response;
                                                                                                                                               
	}                                                                                                                                                   

	                                                                                                                                                    
  /********** ******************* *****************/	                                                                                                                                                    
	
	/**                                                                      
	 * @param string $name                                                   
	 * @param mixed $value                                                   
	 */                                                                      
	public function setConfig($name, $value) {                               
		                                                                       
	    $this->config[$name] = $value;                                       
	                                                                         
	}                                                                        
	                                                                         
	/**                                                                      
	 * @param string $name                                                   
	 * @param mixed $default                                                 
	 * @return mixed                                                         
	 */                                                                      
	public function getConfig($name, $default = null) {                      
		                                                                       
	    return isset($this->config[$name]) ? $this->config[$name] : $default;
	                                                                         
	}                                                                        

    
    
    
	
}