<?php

/**
 * Elastest - September 2018
 * Assignment from Elastique: Build a RESTful API (ref.: Assignment Backend - Elastique.pdf)
 * Eric Delerue delerue_eric@hotmail.com
 * https://github.com/EricDelerue/elastest (private repository)
 */

namespace Elastest\Controllers;

use \Elastest\Cache\CacheInterface;
use \Elastest\Cache\CacheFactory;
use \Elastest\Cache\TempFileCache;
use \Elastest\Cache\NoCache;

use \Elastest\Storage\Pdo;

use \Elastest\ResourceTypes\ResourceTypeInterface;

use \Elastest\Http\HttpResponseInterface;
use \Elastest\Http\HttpResponse;

use \Elastest\Exceptions\InvalidArgumentException;


/**
 * @see ResourceControllerInterface
 */
class ResourceController implements ResourceControllerInterface {

    /**
     * @var array
     */
    protected $config;

		protected $route;
		
		protected $resourceType;
		protected $cacheController;
		
		protected $cache;
		protected $cacheType;
		protected $cacheTimeout;
		protected $cachePath;

    /**
     * ResourceController Constructor
     *
     * @param array $config
     * @param array $route
     *
     */
    public function __construct(array $config = array(), array $route = array()) {

        $this->config = array_merge(array(
            'www_realm' => 'Service',
        ), $config);

		    $this->cache        = (bool) $this->config['cache'];    
				$this->cacheType    = (string) $this->config['cache_type'];    
				$this->cacheTimeout = (int) $this->config['cache_timeout'];     
				$this->cachePath    = (string) $this->config['cache_path'];    
				        
        $this->route = $route;

    }

    /**
     * Validate the resource request (again...)
     *
     * @return bool
     */
    //public function validateResourceRequest(HttpRequestInterface $request, HttpResponseInterface $response) { // protected??
    public function validateResourceRequest(ResourceTypeInterface $resourceType, HttpResponseInterface $response) {	
    
			  // If there's no storage object implemented so no resource requested    
		    if ( !isset($this->route) || empty($this->route) ) { 
		    	  
		    	  $this->errors = array('error' => 400, 'title' => 'Missing route', 'description' => 'You must supply a route array. Please verify and try again.', "script" => "HttpResourceController.class.php", "line" => __LINE__);
		    		throw new InvalidArgumentException("Missing route", 400, null, $this->errors);

		    }		 
		     	    
			  // If there's no storage object implemented so no resource requested    
		    if ( !isset($this->route['endpoint']) || empty($this->route['endpoint']) ) { 
		    	  
		    	  $this->errors = array('error' => 400, 'title' => 'Missing EndPoint', 'description' => 'You must supply an EndPoint in the route array. Please verify and try again.', "script" => "HttpResourceController.class.php", "line" => __LINE__);
		    		throw new InvalidArgumentException("Missing EndPoint", 400, null, $this->errors);

		    }

        // if object has requested method() _list() or _id() or _highlighted() or _search()
        if ( !method_exists($resourceType, '_'.$this->route['verb'] ) ){
		    	  
		    	  $this->errors = array('error' => 400, 'title' => 'Missing or wrong Verb', 'description' => 'The requested action doesn\'t exist. You must supply a verb in the route array. Please verify and try again.', "script" => "HttpResourceController.class.php", "line" => __LINE__);
		    		throw new InvalidArgumentException("Missing or wrong Verb", 400, null, $this->errors);

				}

        return (bool) 1;
    }

	/** 
	  * Main method
	  *                                                                                      
	  * Handle the resource request.                                                              
	  *                                                                                        
	  * @param ResourceTypeInterface  $resourceType  - Resource object               
	  * @param HttpResponseInterface $response       - Response object                                   
	  * @return mixed | HttpResponse                                                                                                                                    
	  */                                                                                  
	  
		public function handleResourceRequest(ResourceTypeInterface $resourceType, HttpResponseInterface $response = null) /* : HttpResponse */ { 

  	    $this->response = is_null($response) ? new HttpResponse() : $response;
  	    // $this->response = $response ?? new HttpResponse();

        /**
         * We re-validate. 
         */
        if (!$this->validateResourceRequest($resourceType, $response)) {
            return;
        }
        
        $this->resourceType = $resourceType;

        /* TO DO
        
        Build the ResponseType
        
        Get response type parameters from the requested resource type
        
        $responseType = $this->responseTypes[$this->response_type]->getResponseTypeParameters($this->resourceType);

        list($parameter1, $parameter2) = $responseType;

        Control parameters (headers, content-type, etc...)
        
        Pass the pararmeters to HttpResponse object
                
        */


        /**
         * Cache controller factory. 
         * TempFile or No Cache
         */
	      $this->cacheController = CacheFactory::factory(
					     $this->config['cache_type'], 
					     $this->route, // Use to build the key of the request
					     $this->config
					     );

				// try retrieving $resourceData from cache
				

				if ( !$resourceData = $this->cacheController->get() ) {
					
				    // $data is not found in cache, get it from the database
				    $resourceData = $this->resourceType->{'_'.$this->route['verb']}($this->route);
            //echo "From database\n";		
            
            // Remove the previous cached file
            $this->cacheController->clear();
            
				    // Store $resourceData in cache so that it can be retrieved next time (within $this->config['cache_timeout'] seconds)
				    $this->cacheController->set($resourceData);
				}
 
				// $resourceData is now available here

        /**
         * Build the response
         *
         *
         */ 
         
        $response->setStatusCode(200);
         
        //$response->addParameters(array("c'est" => "super"));
        //$response->addParameters(array("ça" => "marche"));

        $response->addParameters( $resourceData  );
         
        $response->addHttpHeaders(array(
                'Cache-Control' => 'no-store',
                'Pragma' => 'no-cache',
                'Content-Type' => 'application/json',
                'Content-Length', strlen(json_encode($resourceData, JSON_UNESCAPED_UNICODE))
        ));
         
        /**
         * Send back the response
         *
         *
         */ 
         
        return $response;

		}
    




}
