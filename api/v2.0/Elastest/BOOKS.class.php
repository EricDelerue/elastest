<?php

class Books {
		
		protected $api_key;
		protected $api_secret;
		
		protected $api_configuration_array = array();
								
	  protected $dbi_connection;	  

	  protected $action_type;
	  protected $search_keyword;
	  protected $search_offset = 0;
	  protected $search_limit = 50;	  
	  
	  protected $book_id;
	  protected $book_title;
	  protected $book_description;
	  protected $book_cover_url;
	  protected $book_isbn;
	  protected $book_highlighted;
	  
	  protected $author_id;
	  protected $author_first_name;
	  protected $author_last_name;
	  
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

		public function setApiKey(string $api_key) {
			// Sets an authentication token to use instead of the session variable
			$this->api_key = $api_key;
			return $this;
		}

		public function getApiKey() : string {
			// Get current authentication token to use instead of the session variable
			return $this->api_key;
		}

		public function setActionType(string $action_type) {
			$this->action_type = $action_type;
			return $this;
		}

		public function getActionType() : string {
			return $this->action_type;
		}

		public function setBookId(int $book_id) {
			$this->book_id = $book_id;
			return $this;
		}

		public function getBookId() : int {
			return $this->book_id;
		}

		public function setSearchKeyword(string $search_keyword) {
			$this->search_keyword = $search_keyword;
			return $this;
		}

		public function getSearchKeyword() : string {
			return $this->search_keyword;
		}

		public function setSearchOffset(int $search_offset) {
			$this->search_offset = $search_offset;
			return $this;
		}

		public function getSearchOffset() : int {
			return $this->search_offset;
		}

		public function setSearchLimit(int $search_limit) {
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
        '.$this->tables['books_table'].'.id, '.$this->tables['books_table'].'.title, '.$this->tables['books_table'].'.description, '.$this->tables['books_table'].'.isbn, '.$this->tables['books_table'].'.cover_url, 
        '.$this->tables['authors_table'].'.id AS a_id, '.$this->tables['authors_table'].'.first_name, '.$this->tables['authors_table'].'.last_name, 
        '.$this->tables['publishers_table'].'.id AS p_id, '.$this->tables['publishers_table'].'.name         
        FROM '.$this->tables['books_table'].' 
        LEFT JOIN '.$this->tables['authors_table'].' ON '.$this->tables['books_table'].'.author_id = '.$this->tables['authors_table'].'.id 
        LEFT JOIN '.$this->tables['publishers_table'].' ON '.$this->tables['books_table'].'.publisher_id = '.$this->tables['publishers_table'].'.id         
        ORDER BY '.$this->tables['books_table'].'.id ASC 
        LIMIT '.$this->getSearchOffset().', '.$this->getSearchLimit().';';
        
        //echo "sql: ".$sql."\n<br>";

				$results = $this->dbi_connection->queryDb($sql); // or trigger_error($this->dbi_connection->error."[$sql]");
			  if(!$results){				
				    return array("success" => false, "message" => "Can't run this query: ".$sql);				
			  }		
			  				
			  $books_array = $this->dbi_connection->selectFromDb($results, 'array');
			  
        if ( empty($books_array) ) {
            return array("success" => true, "books" => array());
        }
  			  
				$i = 0;
				$books = array();
				foreach ($books_array as $key => $book) {
				            
				    //echo "{$i}\n<br>";
				    //echo "{$key} => {$book}\n<br>";
    
		        $author = array();
		        $publisher = array();
		      					
				    $author['id']          = $book['a_id'];       unset($book['a_id']);
				    $author['first_name']  = $book['first_name']; unset($book['first_name']);
				    $author['last_name']   = $book['last_name'];  unset($book['last_name']);
				    
				    $publisher['id']       = $book['p_id'];  unset($book['p_id']);
				    $publisher['name']     = $book['name'];  unset($book['name']);

        		$book['author']        = $author;
        		$book['publisher']     = $publisher;
        		
        		$books[$i] = $book;
        		$i++;
				}
	      /*  
	      echo "<pre>";
	      print_r($books);
	      echo "</pre>";
	      */
			  return array("success" => true, "books" => $books); 
			  	  
		} 
		
		public function _highlighted() : array {
				
				$sql = 'SELECT 
        '.$this->tables['books_table'].'.id, '.$this->tables['books_table'].'.title, '.$this->tables['books_table'].'.description, '.$this->tables['books_table'].'.isbn, '.$this->tables['books_table'].'.cover_url, 
        '.$this->tables['authors_table'].'.id AS a_id, '.$this->tables['authors_table'].'.first_name, '.$this->tables['authors_table'].'.last_name, 
        '.$this->tables['publishers_table'].'.id AS p_id, '.$this->tables['publishers_table'].'.name         
        FROM '.$this->tables['books_table'].' 
        LEFT JOIN '.$this->tables['authors_table'].' ON '.$this->tables['books_table'].'.author_id = '.$this->tables['authors_table'].'.id 
        LEFT JOIN '.$this->tables['publishers_table'].' ON '.$this->tables['books_table'].'.publisher_id = '.$this->tables['publishers_table'].'.id 
        WHERE highlighted = -1          
        ORDER BY '.$this->tables['books_table'].'.id ASC 
        LIMIT '.$this->getSearchOffset().', '.$this->getSearchLimit().';';
        
        //echo "sql: ".$sql."\n<br>";

				$results = $this->dbi_connection->queryDb($sql); // or trigger_error($this->dbi_connection->error."[$sql]");
			  if(!$results){				
				    return array("success" => false, "message" => "Can't run this query: ".$sql);				
			  }		
			  				
			  $books_array = $this->dbi_connection->selectFromDb($results, 'array');
			  
        if ( empty($books_array) ) {
            return array("success" => true, "books" => array());
        }

				$i = 0;
				$books = array();
				foreach ($books_array as $key => $book) {
				            
				    //echo "{$i}\n<br>";
				    //echo "{$key} => {$book}\n<br>";
    
		        $author = array();
		        $publisher = array();
		      					
				    $author['id']          = $book['a_id'];       unset($book['a_id']);
				    $author['first_name']  = $book['first_name']; unset($book['first_name']);
				    $author['last_name']   = $book['last_name'];  unset($book['last_name']);
				    
				    $publisher['id']       = $book['p_id'];  unset($book['p_id']);
				    $publisher['name']     = $book['name'];  unset($book['name']);

        		$book['author']        = $author;
        		$book['publisher']     = $publisher;
        		
        		$books[$i] = $book;
        		$i++;
				}
	      /*  
	      echo "<pre>";
	      print_r($books);
	      echo "</pre>";
	      */
			  return array("success" => true, "books" => $books); 
			  	  	
		} 
		
		public function _search() : array {
			  
			  $searchKeyword = $this->getSearchKeyword();
				
				$sql = 'SELECT 
        '.$this->tables['books_table'].'.id, '.$this->tables['books_table'].'.title, '.$this->tables['books_table'].'.description, '.$this->tables['books_table'].'.isbn, '.$this->tables['books_table'].'.cover_url, 
        '.$this->tables['authors_table'].'.id AS a_id, '.$this->tables['authors_table'].'.first_name, '.$this->tables['authors_table'].'.last_name, 
        '.$this->tables['publishers_table'].'.id AS p_id, '.$this->tables['publishers_table'].'.name         
        FROM '.$this->tables['books_table'].' 
        LEFT JOIN '.$this->tables['authors_table'].' ON '.$this->tables['books_table'].'.author_id = '.$this->tables['authors_table'].'.id 
        LEFT JOIN '.$this->tables['publishers_table'].' ON '.$this->tables['books_table'].'.publisher_id = '.$this->tables['publishers_table'].'.id 
    	  WHERE 
    	  '.$this->tables['books_table'].'.title LIKE "%'.$searchKeyword.'%" OR 
    	  '.$this->tables['books_table'].'.description LIKE "%'.$searchKeyword.'%" OR 
    	  '.$this->tables['publishers_table'].'.name LIKE "%'.$searchKeyword.'%" OR 
    	  '.$this->tables['authors_table'].'.first_name LIKE "%'.$searchKeyword.'%" OR 
    	  '.$this->tables['authors_table'].'.last_name LIKE "%'.$searchKeyword.'%"   
        ORDER BY '.$this->tables['books_table'].'.id ASC 
        LIMIT '.$this->getSearchOffset().', '.$this->getSearchLimit().';';
        
        //echo "sql: ".$sql."\n<br>";

				$results = $this->dbi_connection->queryDb($sql); // or trigger_error($this->dbi_connection->error."[$sql]");
			  if(!$results){				
				    return array("success" => false, "message" => "Can't run this query: ".$sql);				
			  }		
			  				
			  $books_array = $this->dbi_connection->selectFromDb($results, 'array');
			  
        if ( empty($books_array) ) {
            return array("success" => true, "books" => array());
        }
  
				$i = 0;
				$books = array();
				foreach ($books_array as $key => $book) {
				            
				    //echo "{$i}\n<br>";
				    //echo "{$key} => {$book}\n<br>";
    
		        $author = array();
		        $publisher = array();
		      					
				    $author['id']          = $book['a_id'];       unset($book['a_id']);
				    $author['first_name']  = $book['first_name']; unset($book['first_name']);
				    $author['last_name']   = $book['last_name'];  unset($book['last_name']);
				    
				    $publisher['id']       = $book['p_id'];  unset($book['p_id']);
				    $publisher['name']     = $book['name'];  unset($book['name']);

        		$book['author']        = $author;
        		$book['publisher']     = $publisher;
        		
        		$books[$i] = $book;
        		$i++;
				}
	      /*  
	      echo "<pre>";
	      print_r($books);
	      echo "</pre>";
	      */
			  return array("success" => true, "books" => $books); 
			  	  	
		} 
		
		public function _id() : array {
			
			  $book_id = $this->getBookId();

        $sql = 'SELECT 
        '.$this->tables['books_table'].'.id, '.$this->tables['books_table'].'.title, '.$this->tables['books_table'].'.description, '.$this->tables['books_table'].'.isbn, '.$this->tables['books_table'].'.cover_url, 
        '.$this->tables['authors_table'].'.id AS a_id, '.$this->tables['authors_table'].'.first_name, '.$this->tables['authors_table'].'.last_name, 
        '.$this->tables['publishers_table'].'.id AS p_id, '.$this->tables['publishers_table'].'.name         
        FROM '.$this->tables['books_table'].' 
        LEFT JOIN '.$this->tables['authors_table'].' ON '.$this->tables['books_table'].'.author_id = '.$this->tables['authors_table'].'.id 
        LEFT JOIN '.$this->tables['publishers_table'].' ON '.$this->tables['books_table'].'.publisher_id = '.$this->tables['publishers_table'].'.id 
        WHERE '.$this->tables['books_table'].'.id = '.$book_id.'  
        LIMIT 1;';
        
        
			  $result = $this->dbi_connection->queryDb($sql);
			  if(!$result){
				
				    return array("success" => false, "message" => "Can't run this query: ".$sql);
				
			  }		
			  				
			  $book_array = $this->dbi_connection->fetchRowAsArray($result, 'associative');
			  
        if ( empty($book_array) ) {
            return array("success" => true, "books" => array());
        }

				$book = array();
		    $author = array();
		    $publisher = array();
		      					
				$author['id']          = $book_array['a_id'];       unset($book_array['a_id']);
				$author['first_name']  = $book_array['first_name']; unset($book_array['first_name']);
				$author['last_name']   = $book_array['last_name'];  unset($book_array['last_name']);
				    
				$publisher['id']       = $book_array['p_id'];  unset($book_array['p_id']);
				$publisher['name']     = $book_array['name'];  unset($book_array['name']);

        $book_array['author']        = $author;
        $book_array['publisher']     = $publisher;
        		
        $book = $book_array;
 
			  return array("success" => true, "books" => $book); 
					
		} 

		
		
		
}
