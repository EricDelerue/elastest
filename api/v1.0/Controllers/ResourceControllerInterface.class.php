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

use \Elastest\ResourceTypes\ResourceTypeInterface;

/**
 *  This controller is called when a "resource" is requested.
 *  call verifyResourceRequest in order to determine if the request
 *  contains a valid resource type.
 *
 */
interface ResourceControllerInterface {
	
    /**
     * Verify the resource request
     *
		 * @param ResourceTypeInterface  $resourceType  - Resource object               
		 * @param HttpResponseInterface  $response       - Response object      
     * @return bool
     */
    
    public function validateResourceRequest(ResourceTypeInterface $resourceType, HttpResponseInterface $response);
	     
		/** 
		 * Main method
		 *                                                                                      
		 * Handle the resource request.                                                              
		 *                                                                                        
		 * @param ResourceTypeInterface  $resourceType  - Resource object               
		 * @param HttpResponseInterface  $response       - Response object                                   
		 */                                                                                 
		public function handleResourceRequest(ResourceTypeInterface $resourceType, HttpResponseInterface $response = null);
 
}
