<?php 
include_once "api-context.php";

/*	 
Head of the API, entry point

1 What should be always done at every request?

Wrap all of the custom endpoints/verbs that the API will be using

- X-MICROTIME request timeout
- Get the headers 
- Take in the request, 
- grab the endpoint, verb and eventually search types from the URI string, 
- detect the HTTP method (GET, POST, PUT, DELETE) and 
- assemble any additional data provided in the header or in the URI. 

Security checks (App authentication)

- Check headers values (App Key and X-PUBLIC, App Key and origin, App Key and APP Secret, X-HASH)
- Validate the csrf token
- Then, decrypt the request
- Validate the decrypted request
- unset the 'encrypted_request' key in querystring   

*/

class ElastestAPI extends API {

    protected $api_base_url;
    protected $api_base_directory;
    protected $api_base_version;

    // Specify the path of the configuration file that defines the autentication codes of the app for each OAuth server types.
    protected $api_configuration_file = '../elastest.api.v2.0.ini.php';
    protected $api_configuration_array = array();

    protected $dbi_connection;
    
    // Identify the origin of the app request
    protected $origin;

    // Identify the current app using the elastest
    protected $app_connected;
    
    protected $csrf_token;

    protected $error = '';
    protected $errors = array();
    protected $parameters = array();
    
    protected $debug_mode = false;
    protected $debug_output = '';
    protected $debug_prefix = 'elastest api head: ';
    
	  protected $Application;	 

	  protected $Books;	  
    protected $Authors;    
    protected $Publishers; 


    public function __construct($request, $origin) {  
    	
    	  // Send request to abstract class API.class.php
        parent::__construct($request);
        
        /* Here we know:
        
        $this->server_timestamp
        $this->headers
        $this->http_accept 
        $this->endpoint
        $this->verb
	      $this->search_type
	      $this->method
	      $this->query_string
	      $this->file

     	  */     
    				           	   
				/*** INI FILE ***/
				//Parse and store the api ini file, this will return an associative array
				$this->api_configuration_array = parse_ini_file($this->api_configuration_file, true);	

        $this->api_base_url = $this->api_configuration_array[ENVIRONMENT.'_url_info']['api_base_url'];
        $this->api_base_directory = $this->api_configuration_array[ENVIRONMENT.'_url_info']['api_base_directory'];
        $this->api_base_version = $this->api_configuration_array[ENVIRONMENT.'_url_info']['api_base_version'];
        $this->api_base_url .= $this->api_base_directory.$this->api_base_version.'/';    
        //$this->api_base_url .= $this->api_base_directory;
        
				$this->dbi_connection = new MysqliConnection($this->api_configuration_array[ENVIRONMENT.'_db_info']); 
				$this->dbi_connection->connectToDb();
				
				$this->origin = $origin;
        
        /*** AUTHENTICATE APPLICATION / CLIENT ***/
         
        $Application = new Application($this->dbi_connection);
              
        $this->app_connected = $Application->authenticateApp($this->headers,$this->query_string,$this->origin);
        
        // This below if we don't want to use the app/authenticate endpoint but go directly to user/authenticate endpoint    
        if(!$this->app_connected["app_authenticated"]){
        	
        	$this->errors = $this->app_connected["error"];        	  
          throw new ElastestException("Application not authenticated", 400, null, $this->errors);
          
        }
        
        // If request is encrypted, we decrypt the request and return a cleaned query string
        $this->query_string = $Application->checkAppRequest($this->query_string);

        /*** OUR TEST CLASSES' OBJECTS ***/
            
        // We create an instance of Books class
        $Books = new Books($this->dbi_connection,$this->api_configuration_array); 
        
        // We create an instance of Authors class
        $Authors = new Authors($this->dbi_connection,$this->api_configuration_array); 
        
        // We create an instance of Publishers class
        $Publishers = new Publishers($this->dbi_connection,$this->api_configuration_array); 

        $this->Application = $Application; 
     
        $this->Books = $Books;  
        $this->Authors = $Authors;   
        $this->Publishers = $Publishers; 

    }

    protected function setError($message){
			$this->error = $message;
			if($this->debug_mode) $this->showErrors('Error: '.$message);
			return false;
	  }

    protected function setPHPError($message, &$php_error_message){
			if(isset($php_error_message) && strlen($php_error_message)) $message.=": ".$php_error_message;
			return $this->setError($message);
	  }

    protected function showErrors($message){
			if($this->debug_mode){
				$message = $this->debug_prefix.$message;
				$this->debug_output .= $message."\n";
				error_log($message);
			}
			return true;
	  }
		
    /**
     * App/Client Endpoint
     */     

		protected function app($parameters) {                                                              
		                                                                                                   
		   if (!is_array($parameters)) {                                                                   
		     $this->parameters = json_decode($parameters, true);                                                 
		   } else {                                                                                        
		   	$this->parameters = $parameters;                                                              
		   } 
		                                                                                                   
		   if ($this->method == 'GET') {                                                                   
		   	                                                                                              
		   	 switch($this->verb){                                                                          
		   		case 'connect':  // authenticate and authorize app/client to keep on                                                                           
		                                                                                                   
		         // Returns array "app_connected" with "app_authenticated" true or false                                                                   
		         // return $this->Application->authenticateApp($this->headers,$this->query_string,$this->origin);
		         return $this->app_connected;		
		         	                                                        
			    break;  		    		         					                                                                                
			    default:                                                                                    
			                                                                                                
                                                                                    
		     }                                                                                             
		                                                                                                   
		   }                                                                                               
		                                                                                                   
		}                                                                                                  

         
    /**
     * Books Endpoint
     * @param array $parameters The decrypted query string parameters
     */
     
		protected function books($parameters) {                                          
			                                                                                   
			  if (!is_array($parameters)) {                                                   
			    $this->parameters = json_decode($parameters);                                 
			  } else {                                                                        
			   $this->parameters = $parameters;                                              
			  }                                        
 
				                                                    
				$this->Books->setActionType($this->verb);  
				$this->Books->setBookId($this->id);  
				$this->Books->setSearchKeyword($this->search_keyword);  
				$this->Books->setSearchOffset($this->offset);  
				$this->Books->setSearchLimit($this->limit); 

        if ($this->method == 'GET') {
        	
        	//return $this->Books->{'_'.$this->verb}();

        	switch($this->verb){
        		case 'list':

	            // Returns "array".
	            return $this->Books->_list();   
	                 		
        		break;		
        		case 'highlighted':
        		  
        		  //return Array();
        		  return $this->Books->_highlighted();   
	                 		
        		break;	    
        		case 'search':
        		  
        		  //return Array();
        		  return $this->Books->_search();   
	                 		
        		break;        		      				          		  
				    default:
        		  
        		  //return Array();
        		  return $this->Books->_id();   
	               
	        }

        }
     	  
        if ($this->method == 'POST') {

        	switch($this->verb){
        		case 'new':
        		  
        		  //return Array();
        		  return $this->Books->_new();   
	                 		
        		break;	
        		case 'update':
        		  
        		  //return Array();
        		  return $this->Books->_update();   
	                 		
        		break;	          		      					          		  
				    default:
				    
				    break;
	        }
        
        }
       

     }
         
    /**
     * Authors Endpoint
     * @param array $parameters The decrypted query string parameters
     */
     
		protected function authors($parameters) {                                          
			                                                                                   
			  if (!is_array($parameters)) {                                                   
			    $this->parameters = json_decode($parameters);                                 
			  } else {                                                                        
			   $this->parameters = $parameters;                                              
			  }                                        
				                                                    
				$this->Authors->setActionType($this->verb);  
				$this->Authors->setAuthorId($this->id);  
				$this->Authors->setSearchKeyword($this->search_keyword);  
				$this->Authors->setSearchOffset($this->offset);  
				$this->Authors->setSearchLimit($this->limit); 

        if ($this->method == 'GET') {
        	
        	//return $this->Authors->{'_'.$this->verb}();

        	switch($this->verb){
        		case 'list':

	            // Returns "array".
	            return $this->Authors->_list();   
	                 		
        		break;		
        		case 'highlighted':
        		  
        		  //return Array();
        		  return $this->Authors->_highlighted();   
	                 		
        		break;	    
        		case 'search':
        		  
        		  //return Array();
        		  return $this->Authors->_search();   
	                 		
        		break;        		      				          		  
				    default:
        		  
        		  //return Array();
        		  return $this->Authors->_id();   
	               
	        }

        }
     	  
        if ($this->method == 'POST') {

        	switch($this->verb){
        		case 'new':
        		  
        		  //return Array();
        		  return $this->Authors->_new();   
	                 		
        		break;	
        		case 'update':
        		  
        		  //return Array();
        		  return $this->Authors->_update();   
	                 		
        		break;	          		      					          		  
				    default:
				    
				    break;
	        }
        
        }
       

     }
         
    /**
     * Publishers Endpoint
     * @param array $parameters The decrypted query string parameters
     */
     
		protected function publishers($parameters) {                                          
			                                                                                   
			  if (!is_array($parameters)) {                                                   
			    $this->parameters = json_decode($parameters);                                 
			  } else {                                                                        
			   $this->parameters = $parameters;                                              
			  }                                        
                                         
				$this->Publishers->setActionType($this->verb);  
				$this->Publishers->setPublisherId($this->id);  
				$this->Publishers->setSearchKeyword($this->search_keyword);  
				$this->Publishers->setSearchOffset($this->offset);  
				$this->Publishers->setSearchLimit($this->limit); 

        if ($this->method == 'GET') {
        	
        	//return $this->Publishers->{'_'.$this->verb}();

        	switch($this->verb){
        		case 'list':

	            // Returns "array".
	            return $this->Publishers->_list();   
	                 		
        		break;		
        		case 'highlighted':
        		  
        		  //return Array();
        		  return $this->Publishers->_highlighted();   
	                 		
        		break;	    
        		case 'search':
        		  
        		  //return Array();
        		  return $this->Publishers->_search();   
	                 		
        		break;        		      				          		  
				    default:
        		  
        		  //return Array();
        		  return $this->Publishers->_id();   
	               
	        }

        }
     	  
        if ($this->method == 'POST') {

        	switch($this->verb){
        		case 'new':
        		  
        		  //return Array();
        		  return $this->Publishers->_new();   
	                 		
        		break;	
        		case 'update':
        		  
        		  //return Array();
        		  return $this->Publishers->_update();   
	                 		
        		break;	          		      					          		  
				    default:
				    
				    break;
	        }
        
        }
       

     }

    
        
    
 }

/* 
echo "API processAPI:<br>";
echo "<pre>";
print_r($_SERVER);
echo "</pre>";

echo "_SERVER['HTTP_ORIGIN']: ".$_SERVER['HTTP_ORIGIN']."\n<br>";
echo "_SERVER['HTTP_REFERER']: ".$_SERVER['HTTP_REFERER']."\n<br>";
echo "_SERVER['SERVER_NAME']: ".$_SERVER['SERVER_NAME']."\n<br>";
*/
		
// Requests from the same server don't have a HTTP_ORIGIN header
if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
    $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}
				     
try {

    $API = new ElastestAPI($_REQUEST['request'], $_SERVER['HTTP_ORIGIN']);
    //echo $API->processAPI();
    echo str_replace('\\/', '/', $API->processAPI());

    //$API->processAPI() returns a json objet (see API.class processAPI() method);
    //$API->processAPI();
    /*
    echo "<br>------------------------------------<br>";		   
		echo "API processAPI:<br>";
		//echo "<pre>";
		print_r($API->processAPI());
		//echo "</pre>";
    echo "<br>------------------------------------<br>";		    
    */
    //////////$data = str_replace('\\/', '/', $API->processAPI());
    //////////echo $data;
    
    //echo $_REQUEST['request'];
    //echo "get type of data: ".gettype($data)."\n\n";
    //echo json_encode('{"data":'.$data.'}');
    //$bom = pack('H*','EFBBBF');                  
	  //$data = preg_replace("/^$bom/", '', $data);
    //var_dump(json_decode($data,true));
    //echo utf8_encode($data);
    //echo $data;
		/*    
		// Define the errors.
		$constants = get_defined_constants(true);
		$json_errors = array();
		foreach ($constants["json"] as $name => $value) {
		    if (!strncmp($name, "JSON_ERROR_", 11)) {
		        $json_errors[$value] = $name;
		    }
		}
		
		// Show the errors for different depths.
		foreach (range(4, 3, -1) as $depth) {
		    var_dump(json_decode($data, true, $depth));
		    echo 'Last error: ', $json_errors[json_last_error()], PHP_EOL, PHP_EOL;
		}
		*/

} catch (ElastestException $e) {
	
    //echo json_encode(Array('error' => $e->errorMessage()));
    echo str_replace('\\/', '/',$e->errorMessage()); 
    
}

?>