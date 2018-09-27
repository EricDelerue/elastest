<?php 

class MysqliConnection {
	
    private $db_connection;
    private $db_config;
    
    private $db_connection_active = false;
    private $mysqli_hostname;
    private $mysqli_user;
    private $mysqli_password;
    private $mysqli_database;
    private $mysqli_port;
    private $mysqli_socket;    
			 			 
    // Constructor - open DB connection
    function __construct($config_info) {

			$this->mysqli_hostname = $config_info['db_hostname'];
			$this->mysqli_user = $config_info['db_user'];
			$this->mysqli_password = $config_info['db_password'];
			$this->mysqli_database = $config_info['db_name']; 
			$this->mysqli_port = $config_info['db_port'];
			$this->mysqli_socket = $config_info['db_socket']; 		
	  }
	
		function connectToDB(){
			
			if(!isset($this->mysqli_hostname) || !isset($this->mysqli_user) || !isset($this->mysqli_password) || !isset($this->mysqli_database)) { 
				
				return false;
				exit();
								
			} else { 	
				
			  if(!$this->db_connection_active) {     	  
			  	
          $this->db_connection = new mysqli($this->mysqli_hostname, $this->mysqli_user, $this->mysqli_password, $this->mysqli_database);
					// Check connection
					if (mysqli_connect_errno()) {
						
					  printf("Connect failed: %s\n", mysqli_connect_error());
					  return false;
					  exit();
					  
					} else {
						
						$this->db_connection_active = true;
						$this->db_connection->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");  
						/*
						$this->db_connection->autocommit(FALSE);
						$stmt = $this->db_connection->prepare("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");    
						$stmt->execute();
						$stmt->close(); 
						*/
						return true; 
						
					}   
					       
        } else {
        	        
				  return true;  
				      
			  }      
        
      }
    }
	
		//funzione per l'esecuzione di una query  
		public function queryDb($sql) {   
			
			if($this->db_connection_active && !empty($sql)) {  
				
				//$sql = $this->db_connection->real_escape_string($sql); 
				//echo "queryDb sql= ".$sql."<br>";
        
				if ($resultset = $this->db_connection->query($sql)) {
					  /*
            echo "queryDb resultset= <pre>";
            print_r($resultset);
            echo "</pre>";
            */
				    return $resultset;  
				     
				} else {
				
				  return null;    
									
				}	

			} else {   
				
				return null;    
				
			}  
			
		} 
	  	
		//funzione per l'esecuzione di più query o call procedures
		public function multiQueryDb($sql) {   
			
			if($this->db_connection_active && !empty($sql)) {   
				
				//$sql = $this->db_connection->real_escape_string($sql); 

				if ($resultset = $this->db_connection->multi_query($sql, MYSQLI_USE_RESULT)) {

				    return $resultset;  
				     
				} else {
				
				  return null;    
									
				}	

			} else {   
				
				return null;    
				
			}  
			
		} 
	  
	  // 
	  public function getNumRows($resultset) {   
	  	if($this->db_connection_active && !is_null($resultset)) {   

	  		return $resultset->num_rows;
	  		
	  	} else {
	  		   
	  		return 0; 
	  		   
	  	}  
	  }	  
	  
	  // 
	  public function getAffectedRows() {   
	  	if($this->db_connection_active) {   

	      return $this->db_connection->affected_rows;
	  		
	  	} else {
	  		   
	  		return 0; 
	  		   
	  	}  
	  }	  
	  		    	  
	  // fetch row as array
	  public function fetchRowAsArray($resultset, $array_type = 'numeric'){
			
			if($this->db_connection_active && !is_null($resultset)) { 
					
				if($array_type == 'numeric'){   
					 					
					/* numeric array */
					$row = $resultset->fetch_array(MYSQLI_NUM);
					//printf ("%s (%s)\n", $row[0], $row[1]);
					
				}
				
				if($array_type == 'associative'){   
				
					/* associative array */
					$row = $resultset->fetch_array(MYSQLI_ASSOC);
					//printf ("%s (%s)\n", $row["Name"], $row["CountryCode"]);
						
				}
				
				if($array_type == 'both'){   
						
					/* associative and numeric array */
					$row = $resultset->fetch_array(MYSQLI_BOTH);
					//printf ("%s (%s)\n", $row[0], $row["CountryCode"]);
						
				}
				
				return $row;
				
			} else {
				
				return null;
						
			}
						      
	  }	  
	    			    	  
	  // fetch row as object
	  public function fetchRowAsObject($resultset){
			
			if($this->db_connection_active && !is_null($resultset)) { 
		
	      $row = $resultset->fetch_object();
				
				return $row;
				
			} else {
				
				return null;
						
			}
						      
	  }	    
	    			    	  
	  // free result set
	  public function freeResultSet($resultset){
      if(!is_null($resultset)) {  
      
        $resultset->close();
      
      }	
	  }	  
	      	  
	  // funzione per l'estrazione dei record  
	  public function selectFromDb($resultset, $object_type = 'object') {  
	 
	  	if($this->db_connection_active && !is_null($resultset)) {   
	  		
	  		if($object_type == 'array') {   
	  			
	  		  for ($object = array(); $row = $resultset->fetch_array(MYSQLI_ASSOC);) $object[] = $row;
	  		  //$object = $resultset->fetch_array(MYSQLI_ASSOC);
	  		  
	  		
	  		}
		  		
	  		if($object_type == 'object') {
	  			
	  			//for ($object = array(); $row = $resultset->fetch_object();) $object[] = $row;
	  			//$object = (object)$object;
	  		  //$object = $resultset->fetch_object();
	  		  //i.e.: then $obj->CountryName, $obj->CountryCode in a while loop
	  		  $object = $resultset->fetch_fields();
	  		  /*
	  		  foreach ($object as $val) {
			        printf("Name:     %s\n", $val->name);
			        printf("Table:    %s\n", $val->table);
			        printf("max. Len: %d\n", $val->max_length);
			        printf("Flags:    %d\n", $val->flags);
			        printf("Type:     %d\n\n", $val->type);
			    }
			    */
	  		}
				 	  		
	  		return $object;
					
				$this->freeResultSet($resultset);
	 		
	  	} else {
	  		   
	  		return null; 
	  		   
	  	}  
	  	
	  }
	      	  
	  // funzione per l'estrazione dei record  
	  public function selectOneRow($resultset, $object_type = 'object') {  
	  	 
	  	if($this->db_connection_active && !is_null($resultset)) {   
	  		
	  		if($object_type == 'array') {   

	  		  $object = $resultset->fetch_array(MYSQLI_ASSOC);
	  		
	  		}
		  		
	  		if($object_type == 'object') {
	  			
	  			$object = $resultset->fetch_object();
	  			
	  		}
				 	  		
	  		return $object;
					
				$this->freeResultSet($resultset);
	 		
	  	} else {
	  		   
	  		return null; 
	  		   
	  	}  
	  	
	  }
			
		//funzione per l'inserimento dei dati in tabella     
		public function insertIntoDb($table = null,$columns = null,$values = null) {    
			      
			if($this->db_connection_active && !is_null($table) && !is_null($columns) && !is_null($values)) { 
				                        
				$sql = 'INSERT INTO '.$table;             
				if($columns != null) {                 
					$sql .= ' ('.$columns.')';
				}               
				for($i = 0; $i < count($values); $i++) {                 
					if(is_string($values[$i])) $values[$i] = '"'.$values[$i].'"';             
				}             
				$values = implode(',',$values);             
				$sql .= ' VALUES ('.$values.')';  
       
				$this->db_connection->real_query($sql);    
				
				return true; 
				          
			} else {    
				             
				return false;     
				        
			}      
			   
		}
	
		//funzione per l'inserimento dei dati in tabella     
		public function getLastInsertId() { 
			
			if($this->db_connection_active){
			   
			  return $this->db_connection->insert_id; 
				          
			} else {    
				             
				return 0;     
				        
			}      
			   
		}
			  		
		private function store_array (&$data, $table) {
			
		  $cols = implode(',', array_keys($data));
		    
		  foreach (array_values($data) as $value){
		    	
		    isset($vals) ? $vals .= ',' : $vals = '';
		    $vals .= '\''.$this->db_connection->real_escape_string($value).'\'';
		      
		  }
		    
		  $this->db_connection->real_query('INSERT INTO '.$table.' ('.$cols.') VALUES ('.$vals.')');
		    
		}
	
		/* Ricordare che ad ogni post è associato un valore relativo alla data che utilizza il formato "aaaa-mm-dd", 
		se si desidera riformattare la data in modo da utilizzare la disposizione consueta nei paesi mediterranei, 
		"gg-mm-aaaa", sarà possibile creare una piccola funzione che suddivida la data nei tre diversi componenti 
		e li riunisca nell'ordine desiderato: */
		
		// funzione per la formattazione della data per le zone latine
		public function formatDateForLatinZones($d) {   
			$vet = explode("-", $d);  
			// dal formato "aaaa-mm-dd" al formato "gg/mm/aaaa"   
			$df = $vet[2]."/".$vet[1]."/".$vet[0];    
			return $df;  
		}
		
		// funzione per la formattazione della data per le zone latine
		public function formatDateForAngloSaxonZones($d) {   
			$vet = explode("-", $d);  
			// dal formato "aaaa-mm-dd" al formato "mm/gg/aaaa"   
			$df = $vet[1]."/".$vet[2]."/".$vet[0];    
			return $df;  
		}
				
		// funzione per la formattazione della data per mysql
		public function formatDateForMySQL($d) {   
			// converte la data in timestamp   
			$vet = strtotime($d);    
			// converte il timestamp della variabile $vet in data formattata    
			$df = strftime('%Y-%m-%d', $vet);    
			return $df; 
		}		
			  					  			
		// funzione per la chiusura della connessione 
		public function disconnectFromDb() {         
			if($this->db_connection_active) {                
				if($this->db_connection->close()) {      
					$this->db_connection_active = false;                   
					return true;                  
				}else{
					return false;
				}         
			}  
		}
	 
    // Destructor - close DB connection
    function __destruct() {
			if($this->db_connection_active) {                 
				if($this->db_connection->close()) {          
					$this->db_connection_active = false;               
					return true;                  
				}else{
					return false;
				}         
			}  
    }
 
}
?>