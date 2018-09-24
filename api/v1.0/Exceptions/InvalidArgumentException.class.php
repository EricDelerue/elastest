<?php

/**
 * Elastest - September 2018
 * Assignment from Elastique: Build a RESTful API (ref.: Assignment Backend - Elastique.pdf)
 * Eric Delerue delerue_eric@hotmail.com
 * https://github.com/EricDelerue/elastest (private repository)
 * 
 */

namespace Elastest\Exceptions;

use \Elastest\Exceptions\ElastestException;

// We extends ElastestException that extends the global Exception class
class InvalidArgumentException extends ElastestException {
    
    // throw new InvalidArgumentException("Wrong endpoint.", 400, null, $this->errors);
		public function __construct($message = null, $code = 0, ElastestException $previous = null, $args = array()) {

				$message = $message ?: 'Argument of the wrong type.'; 
				parent::__construct($message, $code, $previous, $args);
				
		}

}