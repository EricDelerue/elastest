<?php

class Publishers {
		
		protected $api_key;
		protected $api_secret;
		
		protected $api_configuration_array = array();
								
	  protected $dbi_connection;	  

	  protected $action_type;
	  protected $search_keyword;
	  protected $search_offset = 0;
	  protected $search_limit = 50;	  

	  protected $publisher_id;
	  protected $publisher_name;

	  protected $tables = array(
	  		'books_table' => 'books',
	  		'authors_table' => 'authors',
	  		'publishers_table' => 'publishers'  
	  );

    public function __construct($dbi_connection = null, $api_configuration_array = array()) {
    
      $this->dbi_connection = $dbi_connection;         
      $this->api_configuration_array = $api_configuration_array;

    }

		public function setApiKey($api_key) {
			// Sets an authentication token to use instead of the session variable
			$this->api_key = $api_key;
			return $this;
		}

		public function getApiKey() : string {
			// Get current authentication token to use instead of the session variable
			return $this->api_key;
		}

		public function setActionType($action_type) {
			$this->action_type = $action_type;
			return $this;
		}

		public function getActionType() : string {
			return $this->action_type;
		}

		public function setPublisherId($publisher_id) {
			$this->publisher_id = $publisher_id;
			return $this;
		}

		public function getPublisherId() : int {
			return $this->publisher_id;
		}

		public function setSearchKeyword($search_keyword) {
			$this->search_keyword = $search_keyword;
			return $this;
		}

		public function getSearchKeyword() : string {
			return $this->search_keyword;
		}

		public function setSearchOffset($search_offset) {
			$this->search_offset = $search_offset;
			return $this;
		}

		public function getSearchOffset() : int {
			return $this->search_offset;
		}

		public function setSearchLimit($search_limit) {
			$this->search_limit = $search_limit;
			return $this;
		}

		public function getSearchLimit() : int {
			return $this->search_limit;
		}
							

		protected function array_push_assoc($array, $key, $value){
				$array[$key] = $value;
				return $array;
		} 
		
		public function _list() : array {
				
				$sql = 'SELECT 
        '.$this->tables['publishers_table'].'.id, '.$this->tables['publishers_table'].'.name 
        FROM '.$this->tables['publishers_table'].'         
        ORDER BY '.$this->tables['publishers_table'].'.id ASC 
        LIMIT '.$this->getSearchOffset().', '.$this->getSearchLimit().';';
        
        //echo "sql: ".$sql."\n<br>";

				$results = $this->dbi_connection->queryDb($sql); // or trigger_error($this->dbi_connection->error."[$sql]");
			  if(!$results){				
				    return array("success" => false, "message" => "Can't run this query: ".$sql);				
			  }		
			  				
			  $publishers_array = $this->dbi_connection->selectFromDb($results, 'array');
			  
        if ( empty($publishers_array) ) {
            return array("success" => true, "publishers" => array());
        }

			  return array("success" => true, "publishers" => $publishers_array); 
			  	  
		} 
		
		public function _highlighted() : array {
				
				$sql = '';
        
        $publishers_array = array();
        
			  return array("success" => true, "publishers" => $publishers_array); 
		  	
		} 
		
		public function _search() : array {
			  
			  $searchKeyword = $this->getSearchKeyword();
				
				$sql = '';
        
        $publishers_array = array();
        
			  return array("success" => true, "publishers" => $publishers_array); 
			  	  	
		} 
		
		public function _id() : array {
			
			  $publisher_id = $this->getPublisherId();

        $sql = 'SELECT 
        '.$this->tables['publishers_table'].'.id, '.$this->tables['publishers_table'].'.name 
        FROM '.$this->tables['publishers_table'].'         
        WHERE '.$this->tables['publishers_table'].'.id = '.$publisher_id.'  
        ORDER BY '.$this->tables['publishers_table'].'.id ASC         
        LIMIT 1;';
        
			  $result = $this->dbi_connection->queryDb($sql);
			  if(!$result){				
				    return array("success" => false, "message" => "Can't run this query: ".$sql);				
			  }		
			  				
			  $publisher_array = $this->dbi_connection->fetchRowAsArray($result, 'associative');
			  
        if ( empty($publisher_array) ) {
            return array("success" => true, "publishers" => array());
        }

			  return array("success" => true, "publishers" => $publisher_array); 
					
		} 

		
		
		
}
