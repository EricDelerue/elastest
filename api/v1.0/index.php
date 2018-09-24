<?php

/**
 * Elastest - September 2018
 * Assignment from Elastique: Build a RESTful API (ref.: Assignment Backend - Elastique.pdf)
 * Eric Delerue delerue_eric@hotmail.com
 * https://github.com/EricDelerue/elastest (private repository)
 */

namespace Elastest;

require_once("api-context.php");

use \Elastest\Http\HttpRequest AS HttpRequest;
use \Elastest\Api\ElastestAPI;
use \Elastest\Exceptions\ElastestException;

try {


  $ElastestAPI = new ElastestAPI();
  
  /**
   * ElastestAPI's method handleHttpRequest(): returns an instance of HttpResponse
   * @param HttpRequest  
   * @return HttpResponse  
   * 
   * Stack trace:
   * #1 HttpRequest::buildRequestFromGlobals(GLOBALS_VARIABLES)
   * #2 HttpRequestController->handleHttpRequest(HttpRequest, HttpResponse) 
   *    - calls verifyHttpRequest(HttpRequest) which set the route: EndPoint, Verb, ...
   *		- calls ResourceTypeFactory which returns a ResourceType object with Storage object based on the route     
   * #3 ResourceController->handleResourceRequest(ResourceType, HttpResponse)  
   *    - defines the ResponseType   
   *    - defines the CacheType 
   *    - get the data from Cache or Pdo    
   *    - builds the final HttpResponse with data  
   * #4 Back to #1 sends the final HttpResponse     
   */
  $ElastestAPI->handleHttpRequest( HttpRequest::buildRequestFromGlobals() )->send(); 
  
  

} catch (ElastestException $e) {
	
  //echo json_encode(Array('error' => $e->errorMessage()));
  //echo "Houstique, we got a problem:\n\n"; 
  echo str_replace('\\/', '/',$e->errorMessage());
    
}	

