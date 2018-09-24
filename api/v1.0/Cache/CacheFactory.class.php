<?php

/**
 * Elastest - September 2018
 * Assignment from Elastique: Build a RESTful API (ref.: Assignment Backend - Elastique.pdf)
 * Eric Delerue delerue_eric@hotmail.com
 * https://github.com/EricDelerue/elastest (private repository)
 * 
 */

namespace Elastest\Cache;

class CacheFactory {
    
    const PREFIX = 'elastestapi-%s-';
    const SUFFIX = 'cache';
    
		public static function factory(
				string $cache_identifier, 
				array $route,
				array $config
		): CacheInterface {
			
        $key = sprintf(self::PREFIX, substr(md5(__FILE__), 0, 8));
        $key .= implode('-', array_filter($route));
        $key .= '-' . self::SUFFIX;
        //echo "key: ".$key."\n<br>";
        
			  /*
				switch ($cache_identifier) {
						case 'TempFile':
								return new TempFileCache($new_file_prefix, $cache_path);
            default:
                return new NoCache();				
				}
				
				$cache_identifier === 'TempFile' or 'No'
				*/

				$classname = __NAMESPACE__ . '\\' . $cache_identifier . 'Cache';
				
				if (!class_exists($classname)) {
						throw new \InvalidArgumentException('Wrong cache type.');
				}
				
				return new $classname($key, $config);
								
		}

}
