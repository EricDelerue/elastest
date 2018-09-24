<?php

namespace Elastest\Cache;

class NoCache implements CacheInterface {
	
    public function __construct() {
    	
    }

    public function set(array $value) : bool {
        return true;
    }

    public function get() /*: bool*/ {
        return '';
    }

    public function clear(): bool {
        return true;
    }
    
    
    
}
