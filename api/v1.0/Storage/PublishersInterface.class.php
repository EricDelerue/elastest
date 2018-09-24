<?php

/**
 * Elastest - September 2018
 * Assignment from Elastique: Build a RESTful API (ref.: Assignment Backend - Elastique.pdf)
 * Eric Delerue delerue_eric@hotmail.com
 * https://github.com/EricDelerue/elastest (private repository)
 */

namespace Elastest\Storage;

/**
 * Implement this interface to specify where the Elastest Server
 * should retrieve publishers information
 */
interface PublishersInterface {
	
	/**                                                                                                               
	 * Get all the publishers.                                                                                                             
	 *                                                                                                                                                                                                                               
	 * @return an array of arrays with all publishers details                                                                                                                                                                                                      
	 */                                                                                                               
	public function getPublishersList();                                                                     
	
	/**                                                                                                               
	 * Get all the highlighted publishers.                                                                                                             
	 *                                                                                                                                                                                                                            
	 * @return an array of arrays with all highlighted publishers details                                                                                                
	 *                                                                                                                                     
	 */                                                                                                               
	public function getHighlightedPublishers();                                                                     
    	
	/**                                                                                                               
	 * Get the information associated with a publisher_id.                                                                                                             
	 *                                                                                                                
	 * @param $publisher_id                                                                                              
	 * Author identifier to be check with.                                                                            
	 *                                                                                                                
	 * @return an array with publisher details                                                                                                
	 *                                                                                                           
	 */                                                                                                               
	public function getPublisherDetails(int $publisher_id);                                                                     
	
	/**                                                                                                               
	 * Search through the publishers with keyword.                                                                                                             
	 *                                                                                                                
	 * @param string $keyword                                                                                              
	 * @param integer $offset  OPTIONAL Default 0                                                                      
	 * @param integer $limit   OPTIONAL Default 50                                                                                                                                                                           
	 *                                                                                                                
	 */         
	public function getPublishersByKeyword(string $keyword, int $offset = 0, int $limit = 50);                                                                     
    
}
