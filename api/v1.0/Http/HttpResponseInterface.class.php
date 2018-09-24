<?php

/**
 * Elastest - September 2018
 * Assignment from Elastique: Build a RESTful API (ref.: Assignment Backend - Elastique.pdf)
 * Eric Delerue delerue_eric@hotmail.com
 * https://github.com/EricDelerue/elastest (private repository)
 * 
 */

namespace Elastest\Http;

/**
 * Interface which represents an object response.  
 * Handle and display the API Responses for errors and successes
 *
 */
interface HttpResponseInterface {

	/**                                                                            
	 * @param array $parameters                                                    
	 */                                                                            
	public function addParameters(array $parameters);                              
	                                                                               
	/**                                                                            
	 * @param array $http_headers                                                   
	 */                                                                            
	public function addHttpHeaders(array $http_headers);                            
	                                                                               
	/**                                                                            
	 * @param int $status_code                                                      
	 */                                                                            
	public function setStatusCode($status_code);                                    
	                                                                               
	/**                                                                            
	 * @param int    $status_code                                                   
	 * @param string $error_title                                                         
	 * @param string $error_description                                                  
	 * @param string $error_uri                                                          
	 * @return mixed                                                               
	 */                                                                            
	public function setError($status_code, $error_title, $error_description = null, $error_uri = null);

  //public function send();

  /**
   * @param string $parameter_name
   * @return mixed
   */
  public function getParameter($parameter_name);
}