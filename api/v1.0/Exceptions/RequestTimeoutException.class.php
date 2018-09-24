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
class RequestTimeoutException extends ElastestException {

		public function __construct($message = null, $code = 0, ElastestException $previous = null, $args = array()) {

				$message = $message ?: 'Request timeout.'; 
				parent::__construct($message, $code, $previous, $args);
				
		}
		
}