<?php 

class Application {
	
	  protected $dbi_connection;
	  protected $application_id;
	  protected $application_name;
	  protected $application_key;
	  protected $application_secret;
	  protected $application_redirect_uri;
	  protected $application_origin;
	  protected $headers = Array();
	  protected $query_string = Array();
	  protected $origin;
	  
	  protected $errors;

    public function __construct($dbi_connection) {   
    	 
        $this->dbi_connection = $dbi_connection;     
        
    }

    public function setAppKey($application_key){    	
    	$this->application_key = $application_key;
    	return $this;    	
    }
       
    public function getAppKey(){    	
    	return $this->application_key;    	
    }
       
    public function setAppSecret($application_secret){    	
    	$this->application_secret = $application_secret;
    	return $this;   	
    }
       
    public function getAppSecret(){    	
    	return $this->application_secret;    	
    }

		public function setRedirectUri ($redirect_uri) {
			$this->redirect_uri = $redirect_uri;
    	return $this;			
		}

		public function getRedirectUri () {
			return $this->redirect_uri;
		}
 		    
    protected function verifyKey($apikey, $origin) {

    	/*
    	- control in the database if token exists and if it's not expired
    	*/
    	$strSQL="SELECT application_id, application_name, application_key, application_secret FROM applications WHERE application_key='".$apikey."' AND application_origin = '".$origin."' AND application_is_active=-1 LIMIT 1;";

			$objResultSet = $this->dbi_connection->queryDb($strSQL);
			if (!$objResultSet) {
				
				//echo 'Could not run this query: '.$strSQL;                                                                                                                                                                                                                                                                                                                                                                               
				$fd = fopen(DIR_ERRORS . "elastest.api.v2.0.errors.log", "a");
				fwrite($fd, "No corresponding application: ".$strSQL."\n\n");
				fclose($fd);	

				//$mail_headers = "From: me@ericdelerue.com";
				//if(ENVIRONMENT === 'production') mail("me@ericdelerue.com", "No corresponding application", "On ".date('[Y-m-d H:i:s e] ').", mysql could not run this query: ".$strSQL.". In script /api/v2.0/Application.class.php on line 58.", $mail_headers); 	        

			}

		  $objRow = $this->dbi_connection->fetchRowAsArray($objResultSet,'associative');
		  $this->dbi_connection->freeResultSet($objResultSet);	
		  
    	if ($objRow["application_id"]>0) { // if application_id
    		
    		$this->application_id = $objRow["application_id"];
    		$this->application_name = $objRow["application_name"];
    		$this->setAppKey($objRow["application_key"]);
    		$this->setAppSecret($objRow["application_secret"]);
    		
    		return true;
    		
    	} else {
    		
    		$this->application_id = null;
    		$this->application_name = null;
    		$this->application_key = null;
    		$this->application_secret = null;

				$fd = fopen(DIR_ERRORS . "elastest.api.v2.0.errors.log", "a");
				fwrite($fd, "No corresponding application secret. ".$strSQL."\n\n");
				fclose($fd);	

				//$mail_headers = "From: me@ericdelerue.com";
				//if(ENVIRONMENT === 'production') mail("me@ericdelerue.com", "No corresponding application secret", "On ".gmdate('D, d M Y H:i:s T', time()).", no corresponding application secret. ".$strSQL.". In script /api/v2.0/Application.class.php on line ".__LINE__.".", $mail_headers); 	        

    		return false;
    		
    	}
					
			$strSQL = '';
			$objResultSet = null;  
			$objRow = null;			
    }
        
    public function authenticateApp($headers = Array(),$query_string = Array(),$origin = null){
    	
    	$this->headers = $headers;
    	$this->query_string = $query_string;
    	$this->origin = $origin;
     
			if( !array_key_exists('app_key', $this->query_string) ) {
				
				$this->setAppKey(null); 
				 
				$fd = fopen(DIR_ERRORS . "elastest.api.v2.0.errors.log", "a");
				fwrite($fd, "No APP Key provided.\n\n");
				fclose($fd);		 

				//$mail_headers = "From: me@ericdelerue.com";
				//if(ENVIRONMENT === 'production') mail("me@ericdelerue.com", "No APP Key provided", "On ".gmdate('D, d M Y H:i:s T', time()).", no APP Key provided. In script /api/v2.0/Application.class.php on line ".__LINE__.".", $mail_headers); 	        				

			  //$this->errors = array('error' => 400, 'title' => 'No APP Key provided', 'description' => 'It is necessary to provide an APP Key to use this API. Please verify and try again, or ask one.', "script" => "Application.class.php", "line" => "87");        	  
			  //throw new ElastestException("No APP Key provided", 400, null, $this->errors);	

			  return array("app_authenticated" => false, "error" => array('error' => 400, 'title' => 'No APP Key provided', 'description' => 'It is necessary to provide an APP Key to use this API. Please verify and try again, or ask one.', "script" => "Application.class.php", "line" => __LINE__));		  

			} else { 

        $this->setAppKey($query_string['app_key']);

		  }
	    
			if( isset($this->headers['X-PUBLIC']) && !is_null($this->headers['X-PUBLIC']) ){				                                                                                                                                                                                                                                                                                                                                                         

				$client_key = $this->headers['X-PUBLIC'];
				
				if( $this->getAppKey() !== $client_key ){   
					
					$this->setAppKey(null);    
					
					$fd = fopen(DIR_ERRORS . "elastest.api.v2.0.errors.log", "a");
					fwrite($fd, "Inconsistent keys.\n\n");
					fclose($fd);	
					
					//$mail_headers = "From: me@ericdelerue.com";
					//if(ENVIRONMENT === 'production') mail("me@ericdelerue.com", "Inconsistent keys", "On ".gmdate('D, d M Y H:i:s T', time()).", the given APP Keys don\'t match. In script /api/v2.0/Application.class.php on line ".__LINE__.".", $mail_headers);						 					                                                                                                                                                                                                                                                                                               

			  	//$this->errors = array('error' => 400, 'title' => 'Inconsistent keys', 'description' => 'The given APP Keys don\'t match. Please verify and try again', 'app_key' => $query_string['app_key'], "script" => "Application.class.php", "line" => __LINE__);        	                                                                                                       
			    //throw new ElastestException("Inconsistent keys", 400, null, $this->errors); 

			    return array("app_authenticated" => false, "error" => array('error' => 400, 'title' => 'Inconsistent keys', 'description' => 'The given APP Keys don\'t match. Please verify and try again', 'app_key' => $query_string['app_key'], "script" => "Application.class.php", "line" => __LINE__));  			     							                                                                                                                                                                                                                                                               

				}                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          

			}                                                                                                                                                                                                                                                                                                                                                          
			               			        
			if( !$this->verifyKey($this->getAppKey(), $this->origin) ) { 

				$this->setAppKey(null);      

				$fd = fopen(DIR_ERRORS . "elastest.api.v2.0.errors.log", "a");
				fwrite($fd, "Invalid origin.\n\n");
				fclose($fd);		 

				//$mail_headers = "From: me@ericdelerue.com";
				//if(ENVIRONMENT === 'production') mail("me@ericdelerue.com", "Invalid origin", "On ".gmdate('D, d M Y H:i:s T', time()).", invalid APP origin. In script /api/v2.0/Application.class.php on line ".__LINE__.".", $mail_headers); 	   					 				 	

			  //$this->errors = array('error' => 400, 'title' => 'Invalid APP Key', 'description' => 'The given origin doesn\'t match the given app key. Please verify and try again', 'app_key' => $this->query_string['app_key'], "script" => "Application.class.php", "line" => __LINE__);        	  
			  //throw new ElastestException("Invalid origin", 400, null, $this->errors);	

			  return array("app_authenticated" => false, "error" => array('error' => 400, 'title' => 'Invalid origin', 'description' => 'The given origin doesn\'t match the given app key. Please verify and try again. Please verify and try again', 'app_key' => $this->query_string['app_key'], "script" => "Application.class.php", "line" => __LINE__));		  

			} else {

        $this->setAppKey($this->query_string['app_key']);

		  }
                                                                                                                                                                                                                                                                                                                                            
			if(!$this->getAppSecret()){  

				$this->setAppSecret(null);   

				$fd = fopen(DIR_ERRORS . "elastest.api.v2.0.errors.log", "a");
				fwrite($fd, "Invalid APP Secret.\n\n");
				fclose($fd);		 		

				//$mail_headers = "From: me@ericdelerue.com";
				//if(ENVIRONMENT === 'production') mail("me@ericdelerue.com", "Invalid APP Secret", "On ".gmdate('D, d M Y H:i:s T', time()).", invalid APP secret. In script /api/v2.0/Application.class.php on line ".__LINE__.".", $mail_headers);										                                                                                                                                                                                                                                                                                                                           

			  //$this->errors = array('error' => 400, 'title' => 'Invalid APP Secret', 'description' => 'The given APP Key doesn\'t correspond to any APP Secret in the database. Please verify and try again', 'app_key' => $this->query_string['app_key'], "script" => "Application.class.php", "line" => __LINE__);        	                                                                                                 
			  //throw new ElastestException("Invalid APP Secret", 400, null, $this->errors);

			  return array("app_authenticated" => false, "error" => array('error' => 400, 'title' => 'Invalid APP Secret', 'description' => 'The given APP Key doesn\'t correspond to any APP Secret in the database. Please verify and try again', 'app_key' => $this->query_string['app_key'], "script" => "Application.class.php", "line" => __LINE__));			    	                                                                                                                                                                                                                                                                       

			}                                                                                                                                                                                                                                                                                                                                                        
					    
			if( isset($this->headers['X-HASH']) && !is_null($this->headers['X-HASH']) ){									                                                                                                                                                                                                                                                                                                                                                     

				$client_hash = $this->headers['X-HASH']; 
				$client_timestamp = $this->headers['X-MICROTIME'];                                                                                                                                                                                                                                                                                                                
			  //echo "client_hash= ".$client_hash."<br>"; 
			  $client_key = $this->headers['X-PUBLIC'];                                                                                                                                                                                                                                                                                                             
			  $client_secret  = $this->getAppSecret();                                                                                                                                                                                                                                                                                                               
			  $server_hash = hash_hmac('sha256', $client_key.$client_timestamp, $client_secret);		                                                                                                                                                                                                                                                                   
			  //echo "server_hash= ".$server_hash."<br>";	                                                                                                                                                                                                                                                                                                             

			  if ($client_hash !== $server_hash){ 
			  	 
			  	$this->setAppKey(null);  
			  	$this->setAppSecret(null); 
			  	
					$fd = fopen(DIR_ERRORS . "elastest.api.v2.0.errors.log", "a");
					fwrite($fd, "Hashes are not equal.\n\n");
					fclose($fd);	

					//$mail_headers = "From: me@ericdelerue.com";
					//if(ENVIRONMENT === 'production') mail("me@ericdelerue.com", "Hashes are not equal", "On ".gmdate('D, d M Y H:i:s T', time()).", hashes are not equal. In script /api/v2.0/Application.class.php on line ".__LINE__.".", $mail_headers);							 					  	                                                                                                                                                                                                                                                                                                                    

			    //$this->errors = array('error' => 400, 'title' => 'Hashes are not equal', 'description' => 'The client hash is different from the server hash. Please wait a few minutes and try again.', 'verb' => 'Client time stamp: '.$client_timestamp.' | Server time stamp: '.$server_timestamp.' | Client hash: '.$client_hash.' | Server hash: '.$server_hash, "script" => "Application.class.php", "line" => __LINE__);
			    //throw new ElastestException("Hashes are not equal", 400, null, $this->errors);	   

			    return array("app_authenticated" => false, "error" => array('error' => 400, 'title' => 'Hashes are not equal', 'description' => 'The client hash is different from the server hash. Please wait a few minutes and try again.', 'verb' => 'Client time stamp: '.$client_timestamp.' | Server time stamp: '.$server_timestamp.' | Client hash: '.$client_hash.' | Server hash: '.$server_hash, "script" => "Application.class.php", "line" => __LINE__));                                                                                                                                                                                                                                                                      

			  }                                                                                                                                                                                                                                                                                                                                    

			}        

      // If first request, check if csrf token is bound to the state in request
      if (!$this->checkCSRFToken()){ 
      	
        $this->errors = array('error' => 400, 'title' => 'Possible Cross-Site Request Forgery Attack', 'description' => 'Possible Cross-Site Request Forgery Attack. Please try again later.', "script" => "Application.class.php", "line" => __LINE__);
        throw new ElastestException("Possible Cross-Site Request Forgery Attack", 400, null, $this->errors);

      }
        
	    return array("app_authenticated" => true, "error" => null);	

    }
    
    public function checkAppRequest($query_string = array()){

				if (array_key_exists('encrypted_request', $query_string)) {     
						                                                                                                                                                                                                                        
					  // Get the encrypted request                                                                                                                                                                                                                                                               
					  $encrypted_request = $query_string['encrypted_request'];				                                                                                                                                                                                                                    
		                                                                                                                                                                                                                                                                                     
					  // Decrypt the request   
					  //$decrypted_request = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->getAppSecret(), base64_decode($encrypted_request), MCRYPT_MODE_ECB));
					  $decrypted_request = trim(safeDecrypt($encrypted_request,hex2bin($this->getAppSecret())));                                                                                                                                                                                                                                                                  
                                                                                                                                                                                                                           
					  //clean the request		                                                                                                                                                                                                                                                                    
					  $decrypted_request = API::_cleanInputs($decrypted_request);                                                                                                                                                                                                                             
                                                                                                                                                                                                                                                                                             
					  $parameters = json_decode($decrypted_request,true); 
                                                                                                                                                                                                                                                                                      
					  //check if the request is valid by checking if it's an array and looking for the controller and action                                                                                                                                                                                    
					  if( $parameters == false || !is_array($parameters)) {   
					  	
							$fd = fopen(DIR_ERRORS . "elastest.api.v2.0.errors.log", "a");
							fwrite($fd, "Invalid Request.\n\n");
							fclose($fd);

							//$mail_headers = "From: me@ericdelerue.com";
							//if(ENVIRONMENT === 'production') mail("me@ericdelerue.com", "Invalid Request", "On ".gmdate('D, d M Y H:i:s T', time()).", invalid Request. The given parameters are in the wrong format: expected array. In script /api/v2.0/Application.class.php on line ".__LINE__.".", $headers);							 					  	                                                                                                                                                                                                                                  

					    $this->errors = array('error' => 400, 'title' => 'Invalid Request', 'description' => 'The given parameters are in the wrong format: expected array. Please verify and try again', 'parameters' => $parameters, 'script' => 'Application.class.php', 'line' => ".__LINE__.");        	                                                              
					    throw new ElastestException("Invalid Request", 400, null, $this->errors); 
					                                                                                                                                                                                                                  
					  }                                                                                                                                                                                                                                                                                         
					                                                                                                                                                                                                                                                                                              
					  $query_string = array_merge($query_string,$parameters);		
  		              					  		                                                                                                                                                                                                                
					  unset($query_string['encrypted_request']);                                                                                                                                                                                                                                          
		                                                                                                                                                                                                                                                                                              
				}  
			
				return $query_string;                                                                                                                                                                                                                                                                                            
				
    }
	
		protected function checkCSRFToken(){ 
			
			$csrf_token_is_valid = false;

			$header_csrf_token = $this->headers['X-CSRF-TOKEN'];
      $querystring_csrf_token = $this->query_string['csrf_token'];

      if ($header_csrf_token !== $querystring_csrf_token){ 
        $csrf_token_is_valid = false;
      } else {
	      $csrf_token_is_valid = true;
	    }
	    
	    /*  
	    if (hash_equals($header_csrf_token, $querystring_csrf_token)) {
	    		$csrf_token_is_valid = false;
	    } else {
	        $csrf_token_is_valid = true;
	    }
	    */
	    
	    return $csrf_token_is_valid;


    } 
    	    			    
    
    
}


?>