# elastest (solution one)

- *Elastique REST API test v1*
- *September 2018*
- *Eric Delerue delerue_eric@hotmail.com*
- *https://github.com/EricDelerue/elastest*

## What is it about?

This RESTful API allows the user to 

- display the list of all books, all featured books, all authors or all publishers
- search through all books by KEYWORD
- and get a specific book, author or publisher by ID

Enpoints

The following enpoints are available:

Publishers 

• All publishers:
- /publishers/list
• A specific publisher by ID:
- /publishers/{id}
	
Authors 

• All authors:
- /authors/list
• A specific author by ID:
- /authors/{id}

Books 

• All featured/highlighted items:
- /books/highlighted
• Get a specific book by ID:
- /books/{id} 
• Search books by keyword ( optional offset / limit ):
- /books/search/{keyword}
- /books/search/{keyword}/{offset}/{limit}
	

## Requirements

  - PHP 7.0 or higher with PDO drivers for MySQL
  - MySQL 5.6 / MariaDB 10.0 or higher

## Installation

Upload "elastest" content in an "name_of_your_choice" directory somewhere on your web server.

Modify the configuration file with your values in:

    /name_of_your_choice/api/elastest.api.v1.0.ini.php

Run the installer file

    /name_of_your_choice/installer.php 
    
Test the script by opening the following URL:

    http://localhost/name_of_your_choice/books/list or http://your.web.server/name_of_your_choice/books/list

## Configuration

Dont forget to modify the configuration file with your values in:

    /api/elastest.api.v1.0.ini.php

These are the main configuration options and their default value:

[version_info]
- version_number = 1.0
- version_stable = 1.0
- last_updated = 05/09/2018

[development_db_info]
- db_hostname = Hostname of the database server ("localhost", "127.0.01")
- db_name = Database the connecting is made to (elastest)
- db_user = Username of the user connecting to the database (no default)
- db_password = Password of the user connecting to the database (no default)
- db_port = TCP port of the database server (defaults to driver default 3306)
- db_socket = 

[development_url_info]
- api_base_url = https://127.0.0.1/elastique
- api_base_directory = /api/
- api_base_version = v1.0

[production_db_info]
- db_hostname = Hostname of the database server 
- db_name = Database the connecting is made to (elastest)
- db_user = Username of the user connecting to the database (no default)
- db_password = Password of the user connecting to the database (no default)
- db_port = TCP port of the database server (defaults to driver default 3306)
- db_socket = 

[production_url_info]
- api_base_url = https://itineranda.com/elastest
- api_base_directory = /api/
- api_base_version = v1.0

## Cache

Edit the following lines in the file "/api/v1.0/Api/ElastestAPI.class.php":

			$this->config = array_merge(array(  
			    'request_timeout' => 10,   
			    'csrf_token' => false,
			    
			    'cache' => true,     		    
			    'cache_type' => "TempFile" (default) or "Memcache" or "Memcached",
			    'cache_timeout' => 10,
			    'cache_path' => C:\Users\Surface\xampp\htdocs\elastique\cache, 		
			    	    
			    'offset' => 0,
			    'limit' => 50,    
			                         
			), $this->config);


## Code brief description	


index.php

Head of the API

		$ElastestAPI = new ElastestAPI();

		$ElastestAPI->handleHttpRequest(  HttpRequest::buildRequestFromGlobals()  )->send();

ElastestAPI.class.php

		public function handleHttpRequest(HttpRequestInterface $request, HttpResponseInterface $response) {

				$this->getHttpRequestController()->handleHttpRequest($request, $response);
				return $response;
		}
	
HttpRequestController.class.php

		public function handleHttpRequest (HttpRequestInterface $request, HttpResponseInterface $response) {

				// Check request data
				// Build the resource type object 
				// Send to Resource Controller

				$this->getResourceController()->handleResourceRequest ($resourceType, $config, $response)
				return $response;
				
		}
	
ResourceController.class.php

		public function handleResourceRequest(ResourceTypeInterface $resourceType, HttpResponseInterface $response) {

		// Get data from Cache or Database
		// Build the response 
		// Send the response object ( $ElastestAPI->handleHttpRequest(  HttpRequest::buildRequestFromGlobals()  )->send(); )

		}
