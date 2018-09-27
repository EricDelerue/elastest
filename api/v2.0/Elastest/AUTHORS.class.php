<?php

class Authors {
		
		protected $api_key;
		protected $api_secret;
		
		protected $api_configuration_array = array();
								
	  protected $dbi_connection;	  

	  protected $action_type;
	  protected $search_keyword;
	  protected $search_offset = 0;
	  protected $search_limit = 50;	  

	  protected $author_id;
	  protected $author_first_name;
	  protected $author_last_name;

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

		public function setAuthorId($author_id) {
			$this->author_id = $author_id;
			return $this;
		}

		public function getAuthorId() : int {
			return $this->author_id;
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
        '.$this->tables['authors_table'].'.id, '.$this->tables['authors_table'].'.first_name, '.$this->tables['authors_table'].'.last_name 
        FROM '.$this->tables['authors_table'].'         
        ORDER BY '.$this->tables['authors_table'].'.id ASC 
        LIMIT '.$this->getSearchOffset().', '.$this->getSearchLimit().';';
        
        //echo "sql: ".$sql."\n<br>";

				$results = $this->dbi_connection->queryDb($sql); // or trigger_error($this->dbi_connection->error."[$sql]");
			  if(!$results){				
				    return array("success" => false, "message" => "Can't run this query: ".$sql);				
			  }		
			  				
			  $authors_array = $this->dbi_connection->selectFromDb($results, 'array');
			  
        if ( empty($authors_array) ) {
            return array("success" => true, "authors" => array());
        }

			  return array("success" => true, "authors" => $authors_array); 
			  	  
		} 
		
		public function _highlighted() : array {
				
				$sql = '';
        
        $authors = array();
        
			  return array("success" => true, "authors" => $authors); 
		  	
		} 
		
		public function _search() : array {
			  
			  $searchKeyword = $this->getSearchKeyword();
				
				$sql = '';
        
        $authors = array();
        
			  return array("success" => true, "authors" => $authors); 
			  	  	
		} 
		
		public function _id() : array {
			
			  $author_id = $this->getAuthorId();

        $sql = 'SELECT 
        '.$this->tables['authors_table'].'.id, '.$this->tables['authors_table'].'.first_name, '.$this->tables['authors_table'].'.last_name 
        FROM '.$this->tables['authors_table'].'         
        WHERE '.$this->tables['authors_table'].'.id = '.$author_id.'  
        ORDER BY '.$this->tables['authors_table'].'.id ASC         
        LIMIT 1;';
        
			  $result = $this->dbi_connection->queryDb($sql);
			  if(!$result){				
				    return array("success" => false, "message" => "Can't run this query: ".$sql);				
			  }		
			  				
			  $author_array = $this->dbi_connection->fetchRowAsArray($result, 'associative');
			  
        if ( empty($author_array) ) {
            return array("success" => true, "authors" => array());
        }

			  return array("success" => true, "authors" => $author_array); 
					
		} 

		
		
		
}
