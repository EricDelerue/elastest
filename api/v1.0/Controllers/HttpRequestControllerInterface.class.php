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



interface HttpRequestControllerInterface {
	
	// Put in VerifyHttpRequestControllerInterface now
	//public function handleVerifyHttpRequest(HttpRequestInterface $request, HttpResponseInterface $response);              
		
	/**                                                                                                         
	 * Handle the http request                                                                                 
	 *                                                                                                          
	 * @param HttpRequestInterface $request   - The current http request                                            
	 * @param HttpResponseInterface $response - An instance of Elastest\HttpResponseInterface to contain the response data
	 */                                                                                                         
	public function handleHttpRequest(HttpRequestInterface $request, HttpResponseInterface $response);                 


}
	