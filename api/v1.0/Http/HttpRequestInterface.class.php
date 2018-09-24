<?php

/**
 * Elastest - September 2018
 * Assignment from Elastique: Build a RESTful API (ref.: Assignment Backend - Elastique.pdf)
 * Eric Delerue delerue_eric@hotmail.com
 * https://github.com/EricDelerue/elastest (private repository)
 * 
 */

namespace Elastest\Http;

interface HttpRequestInterface {

  public function getHeaders();
  
  public function getHeaderValue(string $header_name = null, string $default = '');
  
  public function getContentType();
  
  public function getHttpAccept();
  
  public function getApiVersion();
  
  public function getOrigin();
  
  public function getCsrfToken();
  
  public function isAjaxRequest();
            
  public function getMethod();
  
  public function getQueryString();
 
  public function getQueryStringValue(string $key_name = null, string $default = '');
  
  public function getClientTimestamp();
  
  public static function buildRequestFromGlobals();

}