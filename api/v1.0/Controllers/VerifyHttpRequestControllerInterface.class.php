<?php

/**
 * Elastest - September 2018
 * Assignment from Elastique: Build a RESTful API (ref.: Assignment Backend - Elastique.pdf)
 * Eric Delerue delerue_eric@hotmail.com
 * https://github.com/EricDelerue/elastest (private repository)
 */

namespace Elastest\Controllers;

//use \Elastest\Http\HttpRequestInterface;
//use \Elastest\Http\HttpResponseInterface;


interface VerifyHttpRequestControllerInterface {
	
  //public function handleVerifyHttpRequest(HttpRequestInterface $request, HttpResponseInterface $response);
  public function handleVerifyHttpRequest(array $config = array());

}
	