<?php

/**
 * Elastest - September 2018
 * Assignment from Elastique: Build a RESTful API (ref.: Assignment Backend - Elastique.pdf)
 * Eric Delerue delerue_eric@hotmail.com
 * https://github.com/EricDelerue/elastest (private repository)
 * 
 */

namespace Elastest\ResourceTypes;

use Elastest\Storage\BooksInterface;

use Exception;

/**
 * 
 */
class Books implements ResourceTypeInterface {
    /**
     * @var BooksInterface storage
     */
    protected $storage;

    /**
     * @param BooksInterface $storage - REQUIRED Storage class for retrieving books information
     */
    public function __construct(BooksInterface $storage){
    	
        $this->storage = $storage;
        
    }

    /**
     * @return string
     */
    public function getResourceIdentifier() {
        return 'books';
    }

    /**
     * Validate the ResourceType request
     *
     * @param HttpRequestInterface  $request
     * @param HttpResponseInterface $response
     * @return bool
     * @throws Exception
     */
    public function validateRequest() {

    	  // instance of Pdo / Books interface_exists
    	  
    	  
    	  
    }

     
    public function _list(array $route = null) : array {

        return $this->storage->getBooksList();
    	
    }
    

     
    public function _highlighted(array $route = null) : array {

        return $this->storage->getHighlightedBooks();
    	
    }
    
    
    //public function _id(int $book_id) : array {
    public function _id(array $route = null) : array {
    	
    	  $book_id = $route['id'];

        return $this->storage->getBookDetails($book_id);
    	
    }
    

    //public function _search(string $keyword, int $offset = 0, int $limit = 50) : array {
    public function _search(array $route = null) : array {
    		
    	  $keyword = $route['keyword'];
    	  $offset = $route['offset'];
    	  $limit = $route['limit'];
        
        return $this->storage->getBooksByKeyword($keyword, $offset, $limit);

    }
        
            
}

    