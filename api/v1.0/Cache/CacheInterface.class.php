<?php

/**
 * Elastest - September 2018
 * Assignment from Elastique: Build a RESTful API (ref.: Assignment Backend - Elastique.pdf)
 * Eric Delerue delerue_eric@hotmail.com
 * https://github.com/EricDelerue/elastest (private repository)
 *
 */

namespace Elastest\Cache;

interface CacheInterface {
	
    public function set(array $value) : bool;
    public function get() /*: bool*/;
    public function clear() : bool;
    
}

