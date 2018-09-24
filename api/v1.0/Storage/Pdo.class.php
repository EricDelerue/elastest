<?php

/**
 * Elastest - September 2018
 * Assignment from Elastique: Build a RESTful API (ref.: Assignment Backend - Elastique.pdf)
 * Eric Delerue delerue_eric@hotmail.com
 * https://github.com/EricDelerue/elastest (private repository)
 */

namespace Elastest\Storage;

use \Elastest\Exceptions\InvalidArgumentException;

class Pdo implements BooksInterface, 
                     AuthorsInterface,
                     PublishersInterface
                     { 
    /**
     * @var \PDO
     */	
	  protected $connection;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $tables = array(
				    'books_table'  => 'books',                    
				    'authors_table'  => 'authors',               
				    'publishers_table'  => 'publishers',  
            );        

   /**
     * @param mixed $connection
     * @param array $config
     *
     * @throws InvalidArgumentException
     **/
	  public function __construct(array $config = array(), /* $tables = array(), */ array $options = array()){
	  	
	  	  $connection = $config['db_info'];
									
				if (!$connection instanceof \PDO) {    
					  
					  // $dsn is the Data Source Name for the database, for example "mysql:dbname=my_db;host=localhost"                                                                                                              
				    if (is_string($connection)) {                                                                                                                      
				        $connection = array('dsn' => $connection);                                                                                                     
				    } 
				                                                                                                                                                     
				    if (!is_array($connection)) {
      					$this->errors = array('error' => 400, 'title' => 'Invalid argument', 'description' => 'First argument to Elastest\Storage\Pdo must be an instance of PDO, a DSN string, or a configuration array. Please verify and try again.', "script" => "Pdo.class.php", "line" => __LINE__);
      					throw new InvalidArgumentException("Invalid argument.", 400, null, $this->errors);				    	                                                                                                                      
				    } 
				                                                                                                                                                     
				    if (!isset($connection['dsn'])) {  
      					$this->errors = array('error' => 400, 'title' => 'Invalid argument', 'description' => 'Configuration array must contain "dsn". Please verify and try again.', "script" => "Pdo.class.php", "line" => __LINE__);
      					throw new InvalidArgumentException("Invalid argument.", 400, null, $this->errors);							    	                                                                                                                                                                                 
				    } 
		                                                                                                                                                 
				    // merge optional parameters                                                                                                                       
				    $connection = array_merge(array( 
				        'dsn' => null,                                                                                                                  
				        'username' => null,                                                                                                                            
				        'password' => null,                                                                                                                            
				        'options' => $options, // array(),                                                                                                                          
				    ), $connection);    
				                                                                                                                                   
				    $connection = new \PDO($connection['dsn'], $connection['username'], $connection['password'], $connection['options']);    
				    $connection->exec("set names utf8");                          
				}          
	                                                                                                                                            
				$this->connection = $connection;                                                                                                                               
				                                                                                                                                                       
				// Debugging                                                                                                                                           
				$this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);     

        $this->config = array_merge(array(
				    'books_table'  => 'books',                    
				    'authors_table'  => 'authors',               
				    'publishers_table'  => 'publishers',  
        ), $config);	
  
	  }



		/*** BOOKS ***/



    /**
     * @param none
     * @return array
     */
    public function getBooksList() : array {
    	
    	
    	
        $stmt = $this->connection->prepare('SELECT 
        '.$this->config['books_table'].'.id, '.$this->config['books_table'].'.title, '.$this->config['books_table'].'.description, '.$this->config['books_table'].'.isbn, '.$this->config['books_table'].'.cover_url, 
        '.$this->config['authors_table'].'.id AS a_id, '.$this->config['authors_table'].'.first_name, '.$this->config['authors_table'].'.last_name, 
        '.$this->config['publishers_table'].'.id AS p_id, '.$this->config['publishers_table'].'.name         
        FROM '.$this->config['books_table'].' 
        LEFT JOIN '.$this->config['authors_table'].' ON '.$this->config['books_table'].'.author_id = '.$this->config['authors_table'].'.id 
        LEFT JOIN '.$this->config['publishers_table'].' ON '.$this->config['books_table'].'.publisher_id = '.$this->config['publishers_table'].'.id         
        ORDER BY '.$this->config['books_table'].'.id ASC 
        LIMIT 0, 50;');

        $stmt->execute();
        
        if (!$results = $stmt->fetchAll(\PDO::FETCH_ASSOC)) {
            return array("books" => array());
        }
        /*     
	      echo "<pre>";
	      print_r($results);
	      echo "</pre>";
	      */
	      
        // Loop 
				//for($i = 0, $l = count($result);$i < $l;$i++){
				$i = 0;
				$books = array();
				foreach ($results as $key => $book) {
				            
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
	      
        return array("books" => $books);
    }

    /**
     * @param none
     * @return array
     */    
    public function getHighlightedBooks() : array {




        $stmt = $this->connection->prepare('SELECT 
        '.$this->config['books_table'].'.id, '.$this->config['books_table'].'.title, '.$this->config['books_table'].'.description, '.$this->config['books_table'].'.isbn, '.$this->config['books_table'].'.cover_url, 
        '.$this->config['authors_table'].'.id AS a_id, '.$this->config['authors_table'].'.first_name, '.$this->config['authors_table'].'.last_name, 
        '.$this->config['publishers_table'].'.id AS p_id, '.$this->config['publishers_table'].'.name         
        FROM '.$this->config['books_table'].' 
        LEFT JOIN '.$this->config['authors_table'].' ON '.$this->config['books_table'].'.author_id = '.$this->config['authors_table'].'.id 
        LEFT JOIN '.$this->config['publishers_table'].' ON '.$this->config['books_table'].'.publisher_id = '.$this->config['publishers_table'].'.id 
        WHERE highlighted = -1 
        ORDER BY '.$this->config['books_table'].'.id ASC 
        LIMIT 0, 50;');
        
        $stmt->execute();
        
        if (!$results = $stmt->fetchAll(\PDO::FETCH_ASSOC)) {
            return array("books" => array());
        }
        /*     
	      echo "<pre>";
	      print_r($results);
	      echo "</pre>";
	      */
	      
        // Loop 
				//for($i = 0, $l = count($result);$i < $l;$i++){
				$i = 0;
				$books = array();
				foreach ($results as $key => $book) {
				            
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
	      
        return array("books" => $books);
    }
    

    /**
     * @param integer $book_id
     * @return array
     */
    public function getBookDetails(int $book_id) : array {

        $stmt = $this->connection->prepare('SELECT 
        '.$this->config['books_table'].'.id, '.$this->config['books_table'].'.title, '.$this->config['books_table'].'.description, '.$this->config['books_table'].'.isbn, '.$this->config['books_table'].'.cover_url, 
        '.$this->config['authors_table'].'.id AS a_id, '.$this->config['authors_table'].'.first_name, '.$this->config['authors_table'].'.last_name, 
        '.$this->config['publishers_table'].'.id AS p_id, '.$this->config['publishers_table'].'.name         
        FROM '.$this->config['books_table'].' 
        LEFT JOIN '.$this->config['authors_table'].' ON '.$this->config['books_table'].'.author_id = '.$this->config['authors_table'].'.id 
        LEFT JOIN '.$this->config['publishers_table'].' ON '.$this->config['books_table'].'.publisher_id = '.$this->config['publishers_table'].'.id 
        WHERE '.$this->config['books_table'].'.id = :book_id  
        LIMIT 1;');
        
        
        $stmt->execute(compact('book_id'));

        if (!$result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            return array();
        }

        return $result;
        
    }

    public function getBooksByKeyword(string $keyword, int $offset = 0, int $limit = 50) : array {
    	
        $stmt = $this->connection->prepare('SELECT 
        '.$this->config['books_table'].'.id, '.$this->config['books_table'].'.title, '.$this->config['books_table'].'.description, '.$this->config['books_table'].'.isbn, '.$this->config['books_table'].'.cover_url, 
        '.$this->config['authors_table'].'.id AS a_id, '.$this->config['authors_table'].'.first_name, '.$this->config['authors_table'].'.last_name, 
        '.$this->config['publishers_table'].'.id AS p_id, '.$this->config['publishers_table'].'.name         
        FROM '.$this->config['books_table'].' 
        LEFT JOIN '.$this->config['authors_table'].' ON '.$this->config['books_table'].'.author_id = '.$this->config['authors_table'].'.id 
        LEFT JOIN '.$this->config['publishers_table'].' ON '.$this->config['books_table'].'.publisher_id = '.$this->config['publishers_table'].'.id     	
    	  WHERE 
    	  '.$this->config['books_table'].'.title LIKE "%'.$keyword.'%" OR 
    	  '.$this->config['books_table'].'.description LIKE "%'.$keyword.'%" OR 
    	  '.$this->config['publishers_table'].'.name LIKE "%'.$keyword.'%" OR 
    	  '.$this->config['authors_table'].'.first_name LIKE "%'.$keyword.'%" OR 
    	  '.$this->config['authors_table'].'.last_name LIKE "%'.$keyword.'%" 
        ORDER BY '.$this->config['books_table'].'.id ASC 
        LIMIT '.$offset.', '.$limit.';');
        
        $stmt->execute();
        
        if (!$results = $stmt->fetchAll(\PDO::FETCH_ASSOC)) {
            return array("books" => array());
        }
        /*     
	      echo "<pre>";
	      print_r($results);
	      echo "</pre>";
	      */
	      
        // Loop 
				//for($i = 0, $l = count($result);$i < $l;$i++){
				$i = 0;
				$books = array();
				foreach ($results as $key => $book) {
				            
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
	      
        return array("books" => $books);
    }



		/*** AUTHORS ***/



    /**
     * @param none
     * @return array
     */
    public function getAuthorsList() : array {

        $stmt = $this->connection->prepare('SELECT 
        '.$this->config['authors_table'].'.id, '.$this->config['authors_table'].'.first_name, '.$this->config['authors_table'].'.last_name 
        FROM '.$this->config['authors_table'].'         
        ORDER BY '.$this->config['authors_table'].'.id ASC 
        LIMIT 0, 50;');

        $stmt->execute();
        
        if (!$results = $stmt->fetchAll(\PDO::FETCH_ASSOC)) {
            return array("authors" => array());
        }
        /*     
	      echo "<pre>";
	      print_r($results);
	      echo "</pre>";
	      */

        return array("authors" => $results);
    }

    /**
     * @param none
     * @return array
     */    
    public function getHighlightedAuthors() : array {

        $authors = array();
	      
        return array("authors" => $authors);
    }
    

    /**
     * @param integer $author_id
     * @return array
     */
    public function getAuthorDetails(int $author_id) : array {

        $stmt = $this->connection->prepare('SELECT 
        '.$this->config['authors_table'].'.id, '.$this->config['authors_table'].'.first_name, '.$this->config['authors_table'].'.last_name 
        FROM '.$this->config['authors_table'].'         
        ORDER BY '.$this->config['authors_table'].'.id ASC 
        WHERE '.$this->config['authors_table'].'.id = :author_id  
        LIMIT 1;');
        
        
        $stmt->execute(compact('author_id'));

        if (!$result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            return array();
        }

        return $result;
        
    }

    public function getAuthorsByKeyword(string $keyword, int $offset = 0, int $limit = 50) : array {

        $authors = array();
	      
        return array("authors" => $authors);
    }



		/*** PUBLISHERS ***/



    /**
     * @param none
     * @return array
     */
    public function getPublishersList() : array {

        $stmt = $this->connection->prepare('SELECT 
        '.$this->config['publishers_table'].'.id, '.$this->config['publishers_table'].'.name  
        FROM '.$this->config['publishers_table'].'         
        ORDER BY '.$this->config['publishers_table'].'.id ASC 
        LIMIT 0, 50;');

        $stmt->execute();
        
        if (!$results = $stmt->fetchAll(\PDO::FETCH_ASSOC)) {
            return array("publishers" => array());
        }
        /*     
	      echo "<pre>";
	      print_r($results);
	      echo "</pre>";
	      */

        return array("publishers" => $results);
    }

    /**
     * @param none
     * @return array
     */    
    public function getHighlightedPublishers() : array {

        $authors = array();
	      
        return array("publishers" => $publishers);
    }
    

    /**
     * @param integer $publisher_id
     * @return array
     */
    public function getPublisherDetails(int $publisher_id) : array {

        $stmt = $this->connection->prepare('SELECT 
        '.$this->config['publishers_table'].'.id, '.$this->config['publishers_table'].'.name  
        FROM '.$this->config['publishers_table'].'         
        ORDER BY '.$this->config['publishers_table'].'.id ASC 
        WHERE '.$this->config['publishers_table'].'.id = :publisher_id  
        LIMIT 1;');
        
        
        $stmt->execute(compact('publisher_id'));

        if (!$result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            return array();
        }

        return $result;
        
    }

    public function getPublishersByKeyword(string $keyword, int $offset = 0, int $limit = 50) : array {

        $authors = array();
	      
        return array("publishers" => $publishers);
    }




    /**
     * DDL to create elastest database and tables for PDO storage
     *
     * @see https://github.com/EricDelerue/elastest
     *
     * @param string $dbName
     * @return string
     */
    public function getBuildSql($dbName = 'elastest') {
        $sql = "
-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Set 24, 2018 alle 01:16
-- Versione del server: 10.1.34-MariaDB
-- Versione PHP: 7.2.8

SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = \"+00:00\";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `elastest`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `authors`
--

CREATE TABLE `authors` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='authors table';

--
-- Dump dei dati per la tabella `authors`
--

INSERT INTO `authors` (`id`, `first_name`, `last_name`) VALUES
(1, 'Ingeborg', 'Pos'),
(2, 'Patrick', 'Welling'),
(3, 'Eric', 'Delerue');

-- --------------------------------------------------------

--
-- Struttura della tabella `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `cover_url` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `isbn` int(13) NOT NULL,
  `author_id` int(11) NOT NULL,
  `publisher_id` int(11) NOT NULL,
  `highlighted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='books table';

--
-- Dump dei dati per la tabella `books`
--

INSERT INTO `books` (`id`, `title`, `description`, `cover_url`, `isbn`, `author_id`, `publisher_id`, `highlighted`) VALUES
(1, 'Dead or Scrum', 'Dead or Scrum', '', 123456789, 1, 1, -1),
(2, 'Metallica applied to PHP', 'Metallica applied to PHP', '', 123456789, 2, 1, -1),
(3, 'Once upon a time in the south', 'Once upon a time in the south', '', 123456789, 3, 2, 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `publishers`
--

CREATE TABLE `publishers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Publishers table';

--
-- Dump dei dati per la tabella `publishers`
--

INSERT INTO `publishers` (`id`, `name`) VALUES
(1, 'Elastique'),
(2, 'Ink salad');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `publishers`
--
ALTER TABLE `publishers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `authors`
--
ALTER TABLE `authors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT per la tabella `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT per la tabella `publishers`
--
ALTER TABLE `publishers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

        ";

        return $sql;
    }
	
	
	
	
	
}
