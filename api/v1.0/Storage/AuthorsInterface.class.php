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
 * should retrieve authors information
 */
interface AuthorsInterface {
	
	/**                                                                                                               
	 * Get all the authors.                                                                                                             
	 *                                                                                                                                                                                                                               
	 * @return an array of arrays with all authors details                                                                                                                                                                                                      
	 */                                                                                                               
	public function getAuthorsList();                                                                     
	
	/**                                                                                                               
	 * Get all the highlighted authors.                                                                                                             
	 *                                                                                                                                                                                                                            
	 * @return an array of arrays with all highlighted authors details                                                                                                
	 *                                                                                                                                     
	 */                                                                                                               
	public function getHighlightedAuthors();                                                                     
    	
	/**                                                                                                               
	 * Get the information associated with a author_id.                                                                                                             
	 *                                                                                                                
	 * @param $author_id                                                                                              
	 * Author identifier to be check with.                                                                            
	 *                                                                                                                
	 * @return an array with author details                                                                                                
	 *                                                                                                           
	 */                                                                                                               
	public function getAuthorDetails(int $author_id);                                                                     
	
	/**                                                                                                               
	 * Search through the authors with keyword.                                                                                                             
	 *                                                                                                                
	 * @param string $keyword                                                                                              
	 * @param integer $offset  OPTIONAL Default 0                                                                      
	 * @param integer $limit   OPTIONAL Default 50                                                                                                                                                                           
	 *                                                                                                                
	 */         
	public function getAuthorsByKeyword(string $keyword, int $offset = 0, int $limit = 50);                                                                     
    
}
