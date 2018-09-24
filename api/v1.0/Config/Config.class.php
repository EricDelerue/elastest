<?php 

/**
 * Elastest - September 2018
 * Assignment from Elastique: Build a RESTful API (ref.: Assignment Backend - Elastique.pdf)
 * Eric Delerue delerue_eric@hotmail.com
 * https://github.com/EricDelerue/elastest (private repository)
 */

namespace Elastest\Config;

use \Elastest\Exceptions\NotFoundException;
use \Elastest\Exceptions\InvalidArgumentException;

class Config {
    
		private $config;
		
		private $format = 'ini';	
		
		private $api_configuration_json_file = __DIR__ . '/../../elastest.api.v1.0.ini.json';
		/* Using .php extension, we deny direct access to ini file (<?php die() ?>) */
		private $api_configuration_ini_file  = __DIR__ . '/../../elastest.api.v1.0.ini.php';
		private $api_configuration_array;

		private static $instance;

    private $dsn;
		private $db_hostname;
		private $db_name;
    private $username;
    private $password;
		private $db_port;
		private $db_socket;
		
		private function __construct() {
			
				// JSON FILE
				// Parse and store the api json file, this will return an associative array        
        if($this->format === 'json'){
		        
		        if( !file_exists($this->api_configuration_json_file) ) { 

								$this->errors = array('error' => 400, 'title' => 'INI file not found.', 'description' => 'INI file not found. Please verify and try again.', "script" => "Config.class.php", "line" => __LINE__);	
								throw new NotFoundException("INI file not found.", 400, null, $this->errors);
					
						} 

						$json_file = file_get_contents($this->api_configuration_json_file);
						$this->api_configuration_array = json_decode($json_file, true);

				}
			
				// INI FILE
				// Parse and store the api ini file, this will return an associative array
        if($this->format === 'ini'){
				    
		        if( !file_exists($this->api_configuration_ini_file) ) { 

								$this->errors = array('error' => 400, 'title' => 'INI file not found.', 'description' => 'INI file not found. Please verify and try again.', "script" => "Config.class.php", "line" => __LINE__);	
								throw new NotFoundException("INI file not found.", 400, null, $this->errors);
					
						} 
						
						$this->api_configuration_array = parse_ini_file($this->api_configuration_ini_file, true);

				}
				
						        	
				$this->db_hostname = $this->api_configuration_array[ENVIRONMENT.'_db_info']['db_hostname'];
				$this->db_name = $this->api_configuration_array[ENVIRONMENT.'_db_info']['db_name'];
				$this->db_user = $this->api_configuration_array[ENVIRONMENT.'_db_info']['db_user'];
				$this->db_password = $this->api_configuration_array[ENVIRONMENT.'_db_info']['db_password'];
				$this->db_port = $this->api_configuration_array[ENVIRONMENT.'_db_info']['db_port'];
				$this->db_socket = $this->api_configuration_array[ENVIRONMENT.'_db_info']['db_socket'];
				
				$this->dsn = "mysql:dbname=$this->db_name;host=$this->db_hostname;port=$this->db_port;socket=$this->db_socket";
							
				$this->api_configuration_array[ENVIRONMENT.'_db_info'] = array_merge(
				array('dsn' => $this->dsn, 
				      'username' => $this->db_user, 
				      'password' => $this->db_password), 
				$this->api_configuration_array[ENVIRONMENT.'_db_info']
				);
				
				$this->config = array();
				$this->config['db_info'] = $this->api_configuration_array[ENVIRONMENT.'_db_info'];
				$this->config['cache_info'] = $this->api_configuration_array[ENVIRONMENT.'_cache_info'];
				$this->config['url_info'] = $this->api_configuration_array[ENVIRONMENT.'_url_info'];
				/*			 
				echo "config<pre>";
				print_r($this->config);  
				echo "</pre>";                                                        
	      */        
		}
		
		public static function getInstance(){
					
				if (self::$instance == null) {
					
						self::$instance = new Config();
				
				}
				
				return self::$instance;
		
		}
		
		public function getConfigArray() : array {
					
				if (!isset($this->api_configuration_array)) {
						
						$this->errors = array('error' => 400, 'title' => 'Config array not set.', 'description' => 'Config array not set. Please verify and try again.', "script" => "Config.class.php", "line" => __LINE__);	
						throw new InvalidArgumentException("Config array not set.", 400, null, $this->errors);
						
				}
					
				if (!is_array($this->api_configuration_array)) {
						
						$this->errors = array('error' => 400, 'title' => 'Array requested.', 'description' => 'Array requested. Please verify and try again.', "script" => "Config.class.php", "line" => __LINE__);	
						throw new InvalidArgumentException("Array requested.", 400, null, $this->errors);

				}
									
				//return $this->api_configuration_array;
				return $this->config;
		
		}
		
		public function getConfigValue($key) {
								
				//if (!isset($this->api_configuration_array[$key])) {
				if (!isset($this->config[$key])) {
						
						$this->errors = array('error' => 400, 'title' => "Key not in config array.", 'description' => "Key $key not in config array. Please verify and try again.", "script" => "Config.class.php", "line" => __LINE__);	
						throw new InvalidArgumentException("Key not in config array.", 400, null, $this->errors);

				}
				
				//return $this->api_configuration_array[$key];
				return $this->config[$key];
			
		}
		
		

}
