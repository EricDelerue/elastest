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
 * should retrieve books information
 */
interface BooksInterface {
	
	/**                                                                                                               
	 * Get all the books.                                                                                                             
	 *                                                                                                                                                                                                                               
	 * @return an array of arrays with all books details                                                                                                
	 *                           
	 *               return array(
	 *							 "books" => array(                                                                                    
	 *               "id" => INTEGER,       
	 *               "title"    => STRING,                             
	 *               "description"  => STRING,             
	 *               "cover_url"      => URL/STRING,
	 *               "isbn"        => STRING,     
	 *               "publisher"        => ARRAY,     
	 *               "author"        => ARRAY,     	        
	 *                ),
	 *                ...
	 *								,"offset"=>0,"limit"=>50,"total"=>101);                                                                                                                                                                             
	 *                                                                                                                
	 */                                                                                                               
	public function getBooksList();                                                                     
	
	/**                                                                                                               
	 * Get all the highlighted books.                                                                                                             
	 *                                                                                                                                                                                                                            
	 * @return an array of arrays with all highlighted books details                                                                                                
	 *                           
	 *               return array(
	 *							 "books" => array(                                                                                    
	 *               "id" => INTEGER,       
	 *               "title"    => STRING,                             
	 *               "description"  => STRING,             
	 *               "cover_url"      => URL/STRING,
	 *               "isbn"        => STRING,     
	 *               "publisher"        => ARRAY,     
	 *               "author"        => ARRAY,     	        
	 *                ),
	 *                ...
	 *								,"offset"=>0,"limit"=>50,"total"=>101);                                                                                                                                                                             
	 *                                                                                                                
	 */                                                                                                               
	public function getHighlightedBooks();                                                                     
    	
	/**                                                                                                               
	 * Get the information associated with a book_id.                                                                                                             
	 *                                                                                                                
	 * @param $book_id                                                                                              
	 * Book identifier to be check with.                                                                            
	 *                                                                                                                
	 * @return an array with book details                                                                                                
	 *                           
	 *               return array(                                                                                    
	 *               "id" => INTEGER,       
	 *               "title"    => STRING,                             
	 *               "description"  => STRING,             
	 *               "cover_url"      => URL/STRING,
	 *               "isbn"        => STRING,     
	 *               "publisher"        => ARRAY,     
	 *               "author"        => ARRAY,     	        
	 *               );                                                                                                                                                                             
	 *                                                                                                                
	 */                                                                                                               
	public function getBookDetails(int $book_id);                                                                     
	
	/**                                                                                                               
	 * Search through the books with keyword.                                                                                                             
	 *                                                                                                                
	 * @param string $keyword                                                                                              
	 * @param integer $offset  OPTIONAL Default 0                                                                      
	 * @param integer $limit   OPTIONAL Default 50    
	                                                                                                                                                                                                 
	 * @return an array of arrays with all highlighted books details                                                                                                
	 *                           
	 *               return array(
	 *							 "books" => array(                                                                                    
	 *               "id" => INTEGER,       
	 *               "title"    => STRING,                             
	 *               "description"  => STRING,             
	 *               "cover_url"      => URL/STRING,
	 *               "isbn"        => STRING,     
	 *               "publisher"        => ARRAY,     
	 *               "author"        => ARRAY,     	        
	 *                ),
	 *                ...
	 *								,"offset"=>0,"limit"=>50,"total"=>101);                                                                                                                                                                             
	 *                                                                                                                
	 */         
	public function getBooksByKeyword(string $keyword, int $offset = 0, int $limit = 50);                                                                     
    
}
