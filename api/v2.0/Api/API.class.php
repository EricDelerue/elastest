<?php 
/**
 * API abstract class
 *
 * 1 - This abstract class will act as a wrapper for all of the custom endpoints that our API will be using. 
 * 
 * To that extent, the abstract class will: 
 * - take in our request, 
 * - grab the endpoint from the URI string, 
 * - detect the HTTP method (GET, POST, PUT, DELETE) and 
 * - assemble any additional data provided in the header or in the URI. 
 * 
 * 2 - Once that's done, the abstract class will pass the request information on to a method in the concrete class ElastestAPI to actually perform the work. 
 * 
 * 3 - We then return to the abstract class which will handle forming a HTTP response back to the client.
 * 
 */
abstract class API {
	
    protected $prefix = "elastest_";
    
    protected $server_timestamp;
    
    protected $request_timeout = 10;
            
    protected $http_accept = 'json';
     
    protected $headers = Array();
    
    /**
     * Property: args
     * Any additional URI components after the endpoint and verb have been removed, in our
     * case, an integer ID for the resource. eg: /<endpoint>/<verb>/<arg0>/<arg1>
     * or /<endpoint>/<arg0>
     */
    protected $args = Array();
        
    protected $is_ajax_request = false;
    
    protected $query_string = '';
    
    /**
     * Property: method
     * The HTTP method this request was made in, either GET, POST, PUT or DELETE
     */
    protected $method = '';
    
    protected $valid_endpoints    = array("books","authors","publishers");
    protected $valid_verbs        = array("list","highlighted","search","[0-9]"); // We'll use preg_match for type control

    protected $valid_keywords     = "[A-Za-z0-9]"; // We'll use preg_match for type control
        
    /**
     * Property: endpoint
     * The Model requested in the URI. eg: /files
     */
    protected $endpoint = '';
    
    /**
     * Property: verb
     * An additional descriptor about the endpoint, 
     * used for requested content type. eg: /books/list/ or /books/highlighted/ or /books/search/ or /books/{id}
     */
    protected $verb = '';
    
    /**
     * Property: search_keyword
     * An optional additional descriptor about the search endpoint, 
     * Used for search by keyword. eg: /books/search/{keywords}
     */    
    protected $search_keyword = '';
    
    protected $id;
    protected $offset = 0;
    protected $limit = 50;

    /**
     * Property: file
     * Stores the input of the PUT request
     */
    protected $file = null;
     
    protected $errors = array();

    /**
     * Constructor: __construct
     * Allow for CORS, assemble and pre-process the data
     */
    public function __construct($request) {

		    /**
		     *  BEGIN API CALLER REQUEST SIMULATION
		     *
		     * Client request simulation variables
		     * Added to the incoming request
		     *
		     * HERE WE JUST SIMULATE A REQUEST FROM A CLIENT / APPLICATION (API CALLER) CONTAINING: 
		     *
		     * - an encrypted request: if client choose the encrypted mode, the request parameters (GET, POST, PUT, DELETE) are encrypted and put in a key "encrypted_request".
		     * - app_key (public key) to confront with the corresponding secret_key in the table of autorized applications / clients
		     * - an origin (url) to confront with the corresponding origin in the table of autorized applications / clients
		     * - a client timestamp (header 'X-MICROTIME: '.$client_timestamp) to confront with the server timestamp: if the difference is more than 10 seconds it throws an error;
		     * - a client hash (header 'X-HASH') made before the call this way: $client_hash = hash_hmac('sha256', $app_key.$client_timestamp, $secret_key);
		     * - csrf_token
		     *
		     * public and secret keys have been created with bin2hex(random_bytes(SODIUM_CRYPTO_SECRETBOX_KEYBYTES))
		     * These keys have been then registered in the elastest database in the table applications 
		     * AND in two separated files public.key and secret.key in the directory keys/ for this demonstration
		     */

        // We set $_SERVER with fake Clinet / Application headers 
        
        //$_SERVER['ORIGIN'] = "127.0.0.1";
        
        $_SERVER['HTTP_ACCEPT'] = "application/json; charset: UTF-8";
        $_SERVER['CONTENT_TYPE'] = "application/json; charset=utf-8";	  
				$_SERVER['HTTP_ACCEPT_CHARSET'] = "utf-8"; 
				$_SERVER['HTTP_ACCEPT_ENCODING'] = "gzip, deflate";
				$_SERVER['HTTP_ACCEPT_LANGUAGE'] = "en-US,en";  
				$_SERVER['HTTP_ACCEPT_LANGUAGE'] = "en-US";
				$_SERVER['HTTP_CONTENT_LANGUAGE'] = "en-US,en";				
				$_SERVER['HTTP_CONNECTION'] = ">Keep-Alive";

				require "safeEncryptDecryptWithLibsodium.php"; 

				$fh = fopen(DIR_KEYS . "public.key","r");
				$test_public_key = trim(fgets($fh));
				fclose($fh);
				//echo "public_key ".$public_key."\n<br>";
				
				$fh = fopen(DIR_KEYS . "secret.key","r");
        $test_secret_key = trim(fgets($fh));
        fclose($fh);
        //echo "secret_key ".$secret_key."\n<br>";		
        
        $test_prefix = "elastest_";
				$test_state = uniqid($test_prefix);    
				
				/**
				 * CSRF Token 
				 * Usually, the server set a secure cookie HTTPOnly with CSRF Token AND the same CSRF token in a hidden POST form field, for example.
				 * Here, for test purposes, we just compare the CSRF token added to the query string with the one in the header 'X-CSRF-TOKEN'
				 * if($this->headers['X-CSRF-TOKEN'] === $this->query_string['csrf_token']) ...
				 */
				$test_csrf_token = bin2hex(random_bytes(32));
				/* PHP 5.3+:
		    if (function_exists('mcrypt_create_iv')) {
		        $test_csrf_token = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
		    } else {
		        $test_csrf_token = bin2hex(openssl_random_pseudo_bytes(32));
		    }
		    */
    
				$test_parameters = json_encode(array('state' => $test_state, 'csrf_token' => $test_csrf_token));
        
        $_GET['app_key'] = $test_public_key;
        $_GET['csrf_token'] = $test_csrf_token;
        $_GET['encrypted_request'] = safeEncrypt($test_parameters,hex2bin($test_secret_key));

				$test_client_timestamp = microtime(true);

				$test_client_hash = hash_hmac('sha256', $test_public_key.$test_client_timestamp, $test_secret_key);
        
        // Add request simulation variables to $_SERVER super global
        $_SERVER['X-MICROTIME']  = $test_client_timestamp;
        $_SERVER['X-HASH']       = $test_client_hash;
        $_SERVER['X-PUBLIC']     = $test_public_key;
				$_SERVER['X-CSRF-TOKEN'] = $test_csrf_token;				
				
        /*
         *********** END API CALLER SECURITY SIMULATION *********
        **/

		    $this->server_timestamp = microtime(true) + floatval(0.3); // added 0.3 to simulate the client request
  		      		        
        // Get headers set by APICaller CURL call
		    foreach($_SERVER as $i=>$val) {
		      $name = str_replace(array('HTTP_', '_'), array('', '-'), $i);		      
		      $this->headers[$name] = $val;
		    }
        
        // First control: check the time the request took to reach the API
		    if(isset($this->headers['X-MICROTIME']) && !is_null($this->headers['X-MICROTIME'])){
		    	
		      $client_timestamp = $this->headers['X-MICROTIME'];

		      if(($this->server_timestamp - $client_timestamp) > $this->request_timeout){
		      	
						$fd = fopen(DIR_ERRORS . "elastest.api.v2.0.errors.log", "a");
						fwrite($fd, "Request Timeout: ".($this->server_timestamp - $client_timestamp)." > 10\n\n");
						fclose($fd);	
						
						$headers = "From: me@ericdelerue.com";
					  if(ENVIRONMENT === 'production') mail("me@ericdelerue.com", "Request Timeout", "On ".gmdate('D, d M Y H:i:s T', time()).", the request to the API took too many time (more than 10 seconds). In script /api/v2.0/API.class.php on line ".__LINE__, $headers); 	        

	          $this->errors = array('error' => 408, 'title' => 'Request Timeout', 'description' => 'The request to the API took too many time (more than 10 seconds). Please wait a few minutes and try again.', "script" => "API.class.php", "line" => __LINE__);
	          throw new ElastestException("Request Timeout", 408, null, $this->errors);	        	
		      
		      }
		    }  	 
		    
		    /**
		     *
		     * The class now grabs the request information
		     *
		     */       

        /*** AJAX request? ***/
        
				$this->is_ajax_request = ( (array_key_exists('X-REQUESTED-WITH',$this->headers)) && ('XMLHttpRequest' == @$this->headers['X-REQUESTED-WITH']) );
        
        /*** ACCEPT ***/
        
        $this->http_accept = (strpos($this->headers['ACCEPT'], 'xml')) ? 'xml' : 'json';
        
        /*** URI ***/
            
        // All URI components after api/v2.0 have been removed
        $this->args = explode('/', rtrim($request, '/')); /* The rtrim() function removes whitespace or other predefined characters from the right side of a string. */
      
        // Valid endpoints: "app","books","authors","publishers"
        $this->endpoint = array_shift($this->args); /* The array_shift() function removes the first element from an array, and returns the value of the removed element. */

        if(false === in_array($this->endpoint,$this->valid_endpoints)){
          $this->errors = array('error' => 400, 'title' => 'Wrong endpoint', 'description' => 'Requested endpoint doesn\'t exist or was typed wrong. Expected \'app\', \'books\', \'authors\' or \'publishers\' endpoints. Please verify and try again.', 'verb' => 'api/v2.0/'.$this->endpoint, "script" => "API.class.php", "line" => __LINE__);
          throw new ElastestException("Wrong endpoint", 400, null, $this->errors);
        } 

        // Valid verbs: "connect","list","highlighted","search","[0-9]" to use in a regexp $pattern with preg_match($pattern, $subject, $matches)
        if (array_key_exists(0, $this->args)) { // && !is_numeric($this->args[0])) {
 	
	          $this->verb = array_shift($this->args);
            
            $pattern = "/^[1-9][0-9]*$/";
            
            if ( !!preg_match($pattern, $this->verb, $matches) && filter_var($this->verb, FILTER_VALIDATE_INT) ) {

									$this->verb = "id";
									$this->id = (int) $matches[0];

						}
						
						$valid_verbs = array("list","highlighted","search");									 
						$pattern = "/".implode("|", $this->valid_verbs)."/i";

						if ( !!preg_match($pattern, $this->verb, $matches) ){

								$this->verb = $matches[0];
								$this->id = -1;
 
						}
						
        } else {

            $this->errors = array('error' => 400, 'title' => 'Wrong verb after endpoint', 'description' => 'Expected \'connect\', \'list\', \'highlighted\', \'search\' or \'{id}\' after \'app\', \'books\', \'authors\' or \'publishers\' endpoints. Please verify and try again.', 'verb' => 'api/v2.0/'.$this->endpoint.'/'.$this->verb, "script" => "API.class.php", "line" => __LINE__);
            throw new ElastestException("Wrong verb after endpoint", 400, null, $this->errors);

        }  

        // Keyword, if any
        if ($this->verb === "search"){ // && !is_numeric($this->args[0])) {
        	
        		if (array_key_exists(0, $this->args) ){
			        		
			          $this->search_keyword = array_shift($this->args);

			          // Only necessary for search endpoint. If empty we throw an error
			          if( !!preg_match("/".$this->valid_keywords."/i", $this->search_keyword, $matches) ){
			          	  
			          	  $this->id = -1;
			          	  
			          } else {
			          	
			          $this->errors = array('error' => 400, 'title' => 'Expected keyword after search verb', 'description' => 'Expected keyword after search verb. Please verify and try again.', 'endpoint' => 'api/v2.0/'.$this->endpoint.'/'.$this->verb.'/'.$this->search_keyword, "script" => "API.class.php", "line" => __LINE__);
			          throw new ElastestException("Expected keyword after search verb", 400, null, $this->errors);

			          }     
 	
			      } else {
			          	
			          $this->errors = array('error' => 400, 'title' => 'Expected keyword after search verb', 'description' => 'Expected keyword after search verb. Please verify and try again.', 'endpoint' => 'api/v2.0/'.$this->endpoint.'/'.$this->verb.'/'.$this->search_keyword, "script" => "API.class.php", "line" => __LINE__);
			          throw new ElastestException("Expected keyword after search verb", 400, null, $this->errors);

			      }     
                       
        }
        
        // Paging offset, limit, if any
        if ($this->verb === "search" && array_key_exists(0, $this->args)) { 
        	
        	  $this->id = "";
        	
        	  $this->offset = array_shift($this->args);
            
            if( !preg_match("/[0-9]/", $this->offset, $matches) ){
            	
            	  $this->offset = 0;
            	
            }
           
        }
        
        if ($this->verb === "search" && array_key_exists(0, $this->args)) { 
        	
        	  $this->id = "";
        	        	
        	  $this->limit = array_shift($this->args);
            
            if( !preg_match("/[0-9]/", $this->limit, $matches) ){
            	
            	  $this->limit = 50;
            	
            }
           
        }
                       
        /*** REQUEST METHOD ***/
        
        $this->method = $this->headers['REQUEST-METHOD'];

        //if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
        if ($this->method == 'POST' && array_key_exists('X-METHOD', $this->headers)) {
            if ($this->headers['X-METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } else if ($this->headers['X-METHOD'] == 'PUT') {
                $this->method = 'PUT';
            } else {
            	  //throw new Exception("Unexpected method");
            	  //$this->_response("Unexpected method: ".$_SERVER['HTTP_X_HTTP_METHOD'], 405, "application/json"); 
                $this->errors = array('error' => 405, 'title' => 'Unexpected method', 'description' => 'Expected DELETE or PUT methods in HTTP_X_HTTP_METHOD header when doing a POST request. Please verify and try again.', 'header' => $this->headers['X-METHOD'], "script" => "API.class.php", "line" => __LINE__);
                throw new ElastestException("Unexpected method", 405, null, $this->errors);            	                 
            }
        }

        switch($this->method) {
        case 'DELETE':
        case 'POST':
            $this->query_string = self::_cleanInputs($_POST);
            break;
        case 'GET':        
            $this->query_string = self::_cleanInputs($_GET);
            break;
        case 'PUT':
            $this->query_string = self::_cleanInputs($_GET);
            $this->file = file_get_contents("php://input");
						/*
						$_PUT  = array();
						parse_str(file_get_contents('php://input'), $_PUT);
						// basically, we read a string from PHP's special input location,
						// and then parse it out into an array via parse_str... per the PHP docs:
						// Parses str as if it were the query string passed via a URL and sets
						// variables in the current scope.
						parse_str(file_get_contents('php://input'), $put_vars);
						$data = $put_vars;
						*/
            break;
        default:
            //$this->_response("Invalid Method: ".$this->method, 405, "application/json");
            $this->errors = array('error' => 405, 'title' => 'Invalid Method', 'description' => 'Only GET, POST, PUT and DELETE methods are valid. Please verify and try again.', 'method' => $this->method, "script" => "API.class.php", "line" => __LINE__);
            throw new ElastestException("Invalid Method", 405, null, $this->errors);
            break;
        }
        
        //print_r($this->args)."\n";
        //print_r($this->endpoint)."\n";
        //echo "API this->query_string\n<pre>";
        //print_r($this->query_string)."\n";
        //echo "</pre>";
    }
    
    public function processAPI() {
    	
    	// If "books" or "authors" or "user" methods exist
    	// echo "this->endpoint: ".$this->endpoint."\n<br>";
      if ((int) method_exists($this, $this->endpoint) > 0) {
      	
        // Here is the magic: http://127.0.0.1/api/v2.0/{endpoint}($parameters) like calling a function endpoint($parameters){}		        	          
        return $this->_response($this->{$this->endpoint}($this->query_string), 200, "application/json; charset=UTF-8"); 
        
      } else {

	      $this->errors = array('error' => 405, 'title' => 'Method Not Allowed', 'description' => 'Requested method \''.$this->endpoint.'\' doesn\'t exist or was typed wrong. Please verify and try again.', 'endpoint' => 'api/v2.0/'.$this->endpoint, "script" => "API.class.php", "line" => __LINE__);        	  
	      throw new ElastestException("Method Not Allowed", 405, null, $this->errors);
	      
      } 
    
    }
    
    public function processError($message = "Internal server error", $status = 500, $content_type = "application/json; charset=UTF-8") {  
    	  //echo "Error message= ".$message;    
        //return $this->_response($message, $status, $content_type);
    }
    
    private function _response($data = Array(), $status = 200, $content_type = "application/json; charset=UTF-8") {
    	
    	  // We build the header
        header("HTTP/1.1 " . $status . " " . $this->_requestStatus($status));
        header("Access-Control-Allow-Orgin: *");   // 127.0.0.1
        header("Access-Control-Allow-Methods: *"); // 'GET, POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS', 'HEAD'        
        header("Access-Control-Allow-Headers: *"); // X-Requested-With, Content-Type, Authorization, X-MICROTIME, X-HASH, X-PUBLIC, X-CSRF-TOKEN    
           
        /*
				if (false === $this->is_ajax_request) { 
				    header('Content-Type: text/html; charset=UTF-8;'); 
				} else {
				    header("Content-Type: application/json; charset=UTF-8");   
				    header("Cache-Control: no-store, no-cache, must-revalidate");
				} 
				*/  				 
				
				// Set Content Type
        header("Content-Type: ".$content_type);
        
        /*
         * CSRF TOKEN
         * ALWAYS create a new csrf token (nonce) in response header as a secure cookie httponly		
         * We'll then check it with the csrf token sent along with the POST or GET request	
         *
         */	
				$expires = gmdate('D, d M Y H:i:s T', time()+3600); // 1 hour
        header("Set-Cookie: csrf_token=".$this->createCSRFToken()."; Domain=127.0.0.1; Expires=".$expires."; Path=/; Secure; HttpOnly");  
 
        //$data = array_map("utf8_encode", $data);

        //return json_encode($data, JSON_UNESCAPED_UNICODE); 
        return json_encode($data); 
    
    }
		
		private function createCSRFToken(){ 
			
      // mcrypt_encrypt: deprecated and removed from PHP 7.2
			//$this->csrf_token = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->Application->getAppSecret(), $state, MCRYPT_MODE_ECB));
			
	    if (function_exists('mcrypt_create_iv')) {
	        $csrf_token = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
	    } else {
	        $csrf_token = bin2hex(openssl_random_pseudo_bytes(32));
	    }

      return $csrf_token;
    } 

		private function _getmicrotime(){ 
			/*
			Restituisce la stringa "msec sec" dove sec è l'attuale orario misurato nel numero di secondi dalla Unix Epoch (0:00:00 January 1, 1970 GMT), 
			e msec è la parte in microsecondi. Questa funzione è disponibile solo su sistemi operativi che supportano la chiamata di sistema gettimeofday().
			Entrambi le parti della stringa sono restituite in unità di secondi.  
			*/
	    list($usec, $sec) = explode(" ",microtime()); 
	    return ((float)$usec + (float)$sec); 
    } 
    
    public static function _cleanInputs($data) {
        $clean_input = Array();
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $clean_input[$k] = self::_cleanInputs($v);
            }
        } else {
            $clean_input = trim(strip_tags($data));
        }
        return $clean_input;
    }

    private function _requestStatus($status){
		// these could be stored in a .ini file and loaded
		// via parse_ini_file()... however, this will suffice
		// for an example
		$codes = Array(
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
		    306 => '(Unused)',
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
		    440 => 'Voucher Blocked',
		    441 => 'Voucher Expired',
		    442 => 'Voucher Used',
		    443 => 'Unassigned Voucher',
		    444 => 'Object not connected',
		    500 => 'Internal Server Error',
		    501 => 'Not Implemented',
		    502 => 'Bad Gateway',
		    503 => 'Service Unavailable',
		    504 => 'Gateway Timeout',
		    505 => 'HTTP Version Not Supported'
		);

		return (isset($codes[$status])) ? $codes[$status] : $codes[500];
	}    
}

?>
