<?php

/**
 * Elastest - September 2018
 * Assignment from Elastique: Build a RESTful API (ref.: Assignment Backend - Elastique.pdf)
 * Eric Delerue delerue_eric@hotmail.com
 * https://github.com/EricDelerue/elastest (private repository)
 * 
 */

namespace Elastest\ResourceTypes;

/**
 * Interface for all Resources Types (Books, Authors, Publishers)
 */
interface ResourceTypeInterface {
	
    /**
     * Get resosurce identifier
     *
     * @return string
     */
    public function getResourceIdentifier();

    /**
     * @param 
     * @param 
     * @return mixed
     */
    public function validateRequest();
    
    
    public function _list();
    
    
    public function _highlighted();
    
    
    //public function _id(int $book_id);
    public function _id(array $route = null) : array;

    //public function _search(string $keyword, int $offset = 0, int $limit = 50);
    public function _search(array $route = null) : array;
    

}
