<?php

/**
 * Elastest - September 2018
 * Eric Delerue delerue_eric@hotmail.com
 * https://github.com/EricDelerue/elastest (private repository)
 *  
 * The method used for caching is faster than Redis, Memcache, APC, and other PHP caching solutions because all those solutions must serialize and unserialize objects, generally using PHP’s serialize or json_encode functions. 
 * By storing PHP objects - not string - in file cache memory across requests, we can avoid serialization completely.
 * 
 */

namespace Elastest\Cache;

class TempFileCache implements CacheInterface {

    private $cache_path;
    private $cache_timeout;
    private $key;

    public function __construct(string $key, array $config) { 
    	  
    	  $this->key = $key;
    		$this->cache_path = $config['cache_path'];
    		$this->cache_timeout = (int) $config['cache_timeout']; // seconds

    }	

    public function set(array $value) : bool {
			
	  		$value = var_export($value, true);
				// HHVM fails at __set_state, so just use object cast for now
				$value = str_replace('stdClass::__set_state', '(object)', $value);
				// Write to temp file first to ensure atomicity
				$tmp = $this->cache_path."/$this->key." . uniqid('', true) . '.tmp';
				$set = file_put_contents($tmp, '<?php $value = ' . $value . ';', LOCK_EX);
				rename($tmp, $this->cache_path."/$this->key");
				
				return $set;
				
    }	

    public function get() /*: bool*/ {
				
			  date_default_timezone_set("UTC"); 
			  
			  $cached_file_name = $this->cache_path."/$this->key";
			  $cache_timeout    = (int) $this->cache_timeout;
			  
			  if ( file_exists($cached_file_name) ){

						$diff_in_secs = $this->getDifferenceInSeconds();
						//echo "cache_get diff_in_secs: ".$diff_in_secs."\n";	
						
						if ( $diff_in_secs < $cache_timeout ){ 			
								
								//echo "From cache\n";		
								
								@include $cached_file_name;
								// from '<?php $value = ' . $value . ';'
								//return isset($value) ? $value : false;
								return $value ?? false;
								
			      } else {
			      	
			      	// clear/clean
			      	
			      	  return false;
			      }
			      
			  } 
			  
			  return false;
				
    }	
	
    private function getDifferenceInSeconds(): int {
			    	
			// Get timestamp
			$filemod = (int) filemtime($this->cache_path."/$this->key"); 
			$now     = (int) time(); // timestamp(); 

			// Get seconds 
			return $seconds = ($now - $filemod); 
			    	
    }
    
    private function clean(bool $all) /*: void*/ {
    	
    	  // TO DO: clean all the cache directory: if $all === true
    	
    	  if (is_file($this->cache_path."/$this->key")) {
    	  	
    	  	  unlink($this->cache_path."/$this->key");
    	  	  
    	  }

    }

    public function clear(): bool {

        if (!file_exists($this->cache_path."/$this->key") || !is_dir($this->cache_path)) {
            return false;
        }
        
        $this->clean(false);
        
        return true;
        
    }
}

