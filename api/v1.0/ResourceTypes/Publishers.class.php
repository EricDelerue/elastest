<?php

/**
 * Elastest - September 2018
 * Assignment from Elastique: Build a RESTful API (ref.: Assignment Backend - Elastique.pdf)
 * Eric Delerue delerue_eric@hotmail.com
 * https://github.com/EricDelerue/elastest (private repository)
 * 
 */

namespace Elastest\ResourceTypes;

use Elastest\Storage\PublishersInterface;

use Exception;

/**
 * 
 */
class Publishers implements ResourceTypeInterface {
    /**
     * @var AuthorsInterface storage
     */
    protected $storage;

    /**
     * @param PublishersInterface $storage - REQUIRED Storage class for retrieving publishers information
     */
    public function __construct(PublishersInterface $storage){
    	
        $this->storage = $storage;
        
    }

    /**
     * @return string
     */
    public function getResourceIdentifier() {
        return 'publishers';
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

    	  // instance of Pdo / Publishers interface_exists
    	  
    	  
    	  
    }

     
    public function _list(array $route = null) : array {

        return $this->storage->getPublishersList();
    	
    }
    

     
    public function _highlighted(array $route = null) : array {

        return $this->storage->getHighlightedPublishers();
    	
    }
    
    
    //public function _id(int $publisher_id) : array {
    public function _id(array $route = null) : array {
    	
    	  $author_id = $route['id'];

        return $this->storage->getPublisherDetails($author_id);
    	
    }
    

    //public function _search(string $keyword, int $offset = 0, int $limit = 50) : array {
    public function _search(array $route = null) : array {
    		
    	  $keyword = $route['keyword'];
    	  $offset = $route['offset'];
    	  $limit = $route['limit'];
        
        return $this->storage->getPublishersByKeyword($keyword, $offset, $limit);

    }
        
}

    