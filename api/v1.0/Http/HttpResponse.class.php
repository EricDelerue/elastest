<?php 

/**
 * Elastest - September 2018
 * Assignment from Elastique: Build a RESTful API (ref.: Assignment Backend - Elastique.pdf)
 * Eric Delerue delerue_eric@hotmail.com
 * https://github.com/EricDelerue/elastest (private repository)
 * 
 */

namespace Elastest\Http;

use \DomainException;
use \DateTime;

use \Elastest\Exceptions\UnexpectedValueException;
use \Elastest\Exceptions\InvalidArgumentException;


class HttpResponse implements HttpResponseInterface {
	
	protected $error = Array();  
	
	protected $http_version;
	
	protected $headers;   
	
	protected $status_code;
	protected $status_title;
	
	protected $parameters = Array();

	//protected $redirect_uri;   
	
  /**
   * @var array
   */
	public static $status_texts = array(          
	    100 => 'Continue',                       
	    101 => 'Switching Protocols',            
	    200 => 'OK',                             
	    201 => 'Created',                        
	    202 => 'Accepted',                       
	    203 => 'Non-Authoritative Information',  
	    204 => 'No Content',                     
	    205 => 'Reset Content',                  
	    206 => 'Partial Content',                
	    300 => 'Multiple Choices',               
	    301 => 'Moved Permanently',              
	    302 => 'Found',                          
	    303 => 'See Other',                      
	    304 => 'Not Modified',                   
	    305 => 'Use Proxy',                      
	    307 => 'Temporary Redirect',             
	    400 => 'Bad Request',                    
	    401 => 'Unauthorized',                   
	    402 => 'Payment Required',               
	    403 => 'Forbidden',                      
	    404 => 'Not Found',                      
	    405 => 'Method Not Allowed',             
	    406 => 'Not Acceptable',                 
	    407 => 'Proxy Authentication Required',  
	    408 => 'Request Timeout',                
	    409 => 'Conflict',                       
	    410 => 'Gone',                           
	    411 => 'Length Required',                
	    412 => 'Precondition Failed',            
	    413 => 'Request Entity Too Large',       
	    414 => 'Request-URI Too Long',           
	    415 => 'Unsupported Media Type',         
	    416 => 'Requested Range Not Satisfiable',
	    417 => 'Expectation Failed',             
	    418 => 'I\'m a teapot',                  
	    500 => 'Internal Server Error',          
	    501 => 'Not Implemented',                
	    502 => 'Bad Gateway',                    
	    503 => 'Service Unavailable',            
	    504 => 'Gateway Timeout',                
	    505 => 'HTTP Version Not Supported',     
	);                                           


  public function __construct($parameters = Array(), $status_code = 200, $headers = Array()){
  	
    $this->setParameters($parameters);
    $this->setStatusCode($status_code);
    $this->setHttpHeaders($headers);
    $this->http_version = '1.1';
        
  }
  
  /*
  public function setError($error_code, $error_title, $error_description){
  	
  	$this->error['error_code'] = $error_code;
  	$this->error['error_title'] = $error_title;
  	$this->error['error_description'] = $error_description;

  }
  */
  
  public function getErrorDetail($error_key){
  	
  	return $this->error[$error_key];

  }

	/**                                                                                                                    
	 * @param int $status_code                                                                                              
	 * @param string $error                                                                                                
	 * @param string $error_description                                                                                     
	 * @param string $error_uri                                                                                             
	 * @return mixed                                                                                                       
	 * @throws InvalidArgumentException                                                                                    
	 */                                                                                                                    
	public function setError($status_code, $error_title, $error_description = null, $error_uri = null){                             

	    $parameters = array(                                                                                               
	        'error' => $error_title,                                                                                             
	        'error_description' => $error_description,                                                                      
	    );                                                                                                                 
	                                                                                                                       
	    if (!is_null($error_uri)) {                                                                                         
	        if (strlen($error_uri) > 0 && $error_uri[0] == '#') {                                                            
	            // we are referencing an oauth bookmark (for brevity)                                                      
	            $error_uri = 'http://tools.ietf.org/html/rfc6749' . $error_uri;                                              
	        }                                                                                                              
	        $parameters['error_uri'] = $error_uri;                                                                          
	    }                                                                                                                  
	                                                                                                                       
	    $http_headers = array(                                                                                              
	        'Cache-Control' => 'no-store'                                                                                  
	    );                                                                                                                 
	                                                                                                                       
	    $this->setStatusCode($status_code);                                                                                 
	    $this->addParameters($parameters);                                                                                 
	    $this->addHttpHeaders($http_headers);                                                                               
	                                                                                                                       
	    if (!$this->isClientError() && !$this->isServerError()) {
	    	
        	$this->errors = array('error' => 405, 'title' => 'Unexpected Status Code', 'description' => sprintf('The HTTP status code is not an error ("%s" given).', $status_code).'. Please verify and try again.', 'header' => $this->headers['X-METHOD'], "script" => "HttpRequest.class.php", "line" => __LINE__);
        	throw new InvalidArgumentException("Unexpected Status Code", 405, null, $this->errors);  
       
	    }                  
	                                                                                                    
	}                                                                                                                      

  /** HEADERS ***/

  public function getHttpHeaders(){
  	
  	return $this->http_headers;

  }

	/**                                                
	 * @param array $httpHeaders                       
	 */                                                
	public function setHttpHeaders(Array $http_headers){
		
	    $this->http_headers = $http_headers;           
	      
	}                                                  

  public function setHttpHeader($header_name, $header_value){
  	
  	$this->http_headers[$header_name] = $header_value;

  }

	/**                                                                    
	 * @param array $httpHeaders                                           
	 */                                                                    
	public function addHttpHeaders(Array $http_headers){                    
		                                                                     
	    $this->http_headers = array_merge($this->http_headers, $http_headers);
	                                                                       
	}                                                                      
		    
	/**                                           
	 * Returns the build header line.             
	 *                                            
	 * @param string $header_name  The header name       
	 * @param string $header_value The header value      
	 *                                            
	 * @return string The built header line       
	 */                                           
	protected function buildHttpHeader($header_name, $header_value){    
		                                         
	    return sprintf("%s: %s\n", $header_name, $header_value);
	    
	}                                             

  /** STATUS ***/
  
	/**                                           
	 * @return int                                
	 */                 	                           
	public function getStatusCode(){ 
		             
	  return $this->status_code;   
	                  
	}   
                                                                                                               
	/**                                                                                                             
	 * @param int $status_code                                                                                       
	 * @param string $text                                                                                          
	 * @throws InvalidArgumentException                                                                             
	 */                                                                                                             
	public function setStatusCode($status_code, $status_title = null){ 
		                                                      
	  $this->status_code = (int) $status_code;   
	                                                                     
	  if ($this->isInvalid()) {       
	  	                                                                            
	    throw new InvalidArgumentException(sprintf('The HTTP status code "%s" is not valid.', $status_code));  
	      
	  }                                                                                                           
	                                                                                                                
	  $this->status_title = false === $status_title ? '' : (null === $status_title ? self::$status_texts[$this->status_code] : $status_title);
	    
	}                                                                                                               
	                                                                                                                
	/**                                                                                                             
	 * @return string                                                                                               
	 */                                                                                                             
	public function getStatusTitle(){            
		                                                                    
	  return $this->status_title;         
	                                                                              
	} 
  
  /** PARAMETERS ***/
	                                                                                                             
	/**                                                                                                
	 * @return array                                                                                   
	 */                                                                                                
	public function getParameters(){                                                                   
		                                                                                                 
	  return $this->parameters;                                                                        
	                                                                                                   
	}                                                                                                  
	                                                                                                   
	/**                                                                                                
	 * @param array $parameters                                                                        
	 */                                                                                                
	public function setParameters(Array $parameters){                                                  
		                                                                                                 
	  $this->parameters = $parameters;                                                                 
	                                                                                                   
	}                                                                                                  
	                                                                                                   
	/**                                                                                                
	 * @param array $parameters                                                                        
	 */                                                                                                
	public function addParameters(Array $parameters){                                                  
		                                                                                                 
	  $this->parameters = array_merge($this->parameters, $parameters);                                 
	                                                                                                   
	}                                                                                                  
	                                                                                                   
	/**                                                                                                
	 * @param string $parameter_name                                                                   
	 * @param mixed  $default                                                                          
	 * @return mixed                                                                                   
	 */                                                                                                
	public function getParameter($parameter_name, $default = null){                                    
		                                                                                                 
	  return isset($this->parameters[$parameter_name]) ? $this->parameters[$parameter_name] : $default;
	                                                                                                   
	}                                                                                                  
	                                                                                                   
	/**                                                                                                
	 * @param string $parameter_name                                                                   
	 * @param mixed  $parameter_value                                                                  
	 */                                                                                                
	public function setParameter($parameter_name, $parameter_value){                                   
		                                                                                                 
	  $this->parameters[$parameter_name] = $parameter_value;                                           
	                                                                                                   
	}                                                                                                  

    
	public function getResponseBody($accepted_format = 'json'){        
		                                                                                  
	    switch ($accepted_format) {                                                                     
	        case 'json':                                                                       
	            return $this->parameters ? json_encode($this->parameters) : '';                
	        case 'xml':                                                                        
	            // this only works for single-level arrays                                     
	            $xml = new \SimpleXMLElement('<response/>');                                   
	            foreach ($this->parameters as $key => $param) {                                
	                $xml->addChild($key, $param);                                              
	            }                                                                              
	                                                                                           
	            return $xml->asXML();                                                          
	    }                                                                                      
	                                                                                           
	    throw new InvalidArgumentException(sprintf('The format %s is not supported', $accepted_format));
	                                                                                           
	}                                                                                          

	public function send($accepted_format = 'json'){
			
		// headers have already been sent by the developer                                   
		if (headers_sent()) {                                                                  
		    return;                                                                            
		}                                                                                      
		                                                                                       
		switch ($accepted_format) {                                                            
		    case 'json':                                                                       
		        $this->setHttpHeader('Content-Type', 'application/json; charset=UTF-8;');                      
		        break;                                                                         
		    case 'xml':                                                                        
		        $this->setHttpHeader('Content-Type', 'text/xml; charset=UTF-8;');                              
		        break;                                                                         
		}                                                                                      
		                                                                                       
		// status                                                                              
		header(sprintf('HTTP/%s %s %s', $this->http_version, $this->status_code, $this->status_title));
    header("Access-Control-Allow-Orgin: *");
    header("Access-Control-Allow-Methods: *"); // 'GET, POST, PUT, DELETE'        
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Authorization");  	
    	                                                                                       
		foreach ($this->getHttpHeaders() as $header_name => $header_value) {                   
		    //header(sprintf('%s: %s', $header_name, $header_value));                          
		    header($this->buildHttpHeader($header_name, $header_value));                                    
		}   
		
		/*
		echo(sprintf('HTTP/%s %s %s', $this->version, $this->status_code, $this->status_title));
		
  	echo "sent headers:\n<br><pre>";
  	print_r($this->getHttpHeaders());
  	echo "</pre>";                                                                         
		*/
		                                                                                      
		echo $this->getResponseBody($accepted_format);                                         

  }                                            

	/**                                                            
	 * @return Boolean                                             
	 *                                                             
	 * @api                                                        
	 */                                                            
	public function isInformational(){ 
		                                                             
	    return $this->status_code >= 100 && $this->status_code < 200;
	    
	}                                                              
	                                                               
	/**                                                            
	 * @return Boolean                                             
	 *                                                             
	 * @api                                                        
	 */                                                            
	public function isSuccessful(){   
		                                                           
	    return $this->status_code >= 200 && $this->status_code < 300;
	    
	}                                                              
	                                                               
	/**                                                            
	 * @return Boolean                                             
	 *                                                             
	 * @api                                                        
	 */                                                            
	public function isRedirection(){ 
		                                                             
	    return $this->status_code >= 300 && $this->status_code < 400;
	    
	}                                                              

	/**                                                              
	 * @return Boolean                                               
	 *                                                               
	 * @api                                                          
	 *                                                               
	 * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html   
	 */                                                              
	public function isInvalid(){                                     
		                                                               
	    return $this->status_code < 100 || $this->status_code >= 600;
	                                                                 
	}                                                                

	/**                                                            
	 * @return Boolean                                             
	 *                                                             
	 * @api                                                        
	 */                                                            
	public function isClientError(){                               
		                                                             
	    return $this->status_code >= 400 && $this->status_code < 500;
	                                                               
	}                                                              
	                                                               
	/**                                                            
	 * @return Boolean                                             
	 *                                                             
	 * @api                                                        
	 */                                                            
	public function isServerError(){                               
		                                                             
	    return $this->status_code >= 500 && $this->status_code < 600;
	                                                               
	}                                                              

    
	   
    
    

}
