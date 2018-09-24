<?php

namespace Elastest\ResourceTypes;

use Elastest\ResourceTypes\ResourceTypeInterface;
use Elastest\Storage\Pdo;

class ResourceFactory {

		public static function factory(
				string $identifier,
				Pdo $storage = null
		): ResourceTypeInterface {
			  /*
				switch ($identifier) {
						case 'books':
								return new Books($storage);
						case 'authors':
								return new Authors($storage);
						case 'publishers':
								return new Publishers($storage);								
				}
				*/

				$classname = __NAMESPACE__ . '\\' . ucfirst($identifier);
				
				if (!class_exists($classname)) {
						throw new \InvalidArgumentException('Wrong type.');
				}
				
				//$storage = $storages[$identifier];
				
				return new $classname($storage);
								
		}

}