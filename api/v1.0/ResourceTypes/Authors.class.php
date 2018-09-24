<?php

/**
 * Elastest - September 2018
 * Assignment from Elastique: Build a RESTful API (ref.: Assignment Backend - Elastique.pdf)
 * Eric Delerue delerue_eric@hotmail.com
 * https://github.com/EricDelerue/elastest (private repository)
 * 
 */

namespace Elastest\ResourceTypes;

use Elastest\Storage\AuthorsInterface;

use Exception;

/**
 * 
 */
class Authors implements ResourceTypeInterface {
    /**
     * @var AuthorsInterface storage
     */
    protected $storage;

    /**
     * @param AuthorsInterface $storage - REQUIRED Storage class for retrieving authors information
     */
    public function __construct(AuthorsInterface $storage){
    	
        $this->storage = $storage;
        
    }

    /**
     * @return string
     */
    public function getResourceIdentifier() {
        return 'authors';
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

    	  // instance of Pdo / Authors interface_exists
    	  
    	  
    	  
    }

     
    public function _list(array $route = null) : array {

        return $this->storage->getAuthorsList();
    	
    }
    

     
    public function _highlighted(array $route = null) : array {

        return $this->storage->getHighlightedAuthors();
    	
    }
    
    
    //public function _id(int $author_id) : array {
    public function _id(array $route = null) : array {
    	
    	  $author_id = $route['id'];

        return $this->storage->getAuthorDetails($author_id);
    	
    }
    

    //public function _search(string $keyword, int $offset = 0, int $limit = 50) : array {
    public function _search(array $route = null) : array {
    		
    	  $keyword = $route['keyword'];
    	  $offset = $route['offset'];
    	  $limit = $route['limit'];
        
        return $this->storage->getAuthorsByKeyword($keyword, $offset, $limit);

    }
        
}

    