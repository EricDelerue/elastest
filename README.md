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

- All publishers:
		/publishers/list

- A specific publisher by ID:
		/publishers/{id}
	
Authors 

- All authors:
		/authors/list

- A specific author by ID:
		/authors/{id}

Books 

- All featured/highlighted items:
		/books/highlighted

- Get a specific book by ID:
		/books/{id} 
	
- Search books by keyword ( optional offset / limit ):
		/books/search/{keyword} 
		/books/search/{keyword}/{offset}/{limit}
	

## Requirements

  - PHP 7.2 with PDO drivers for MySQL
  - Apache server
  - MySQL 5.6 / MariaDB 10.0 or higher

## Installation

Upload "elastest" content in a "name_of_your_choice" directory somewhere on your web server.

Verify that .htaccess file is in /name_of_your_choice/ 

Modify the configuration file with your values in:

    /name_of_your_choice/api/elastest.api.v1.0.ini.php

Run the installer file

    /name_of_your_choice/installer.php 
    
Test the script by opening the following URL:

    http://localhost/name_of_your_choice/books/list or http://your.web.server/name_of_your_choice/books/list

## .htaccess

The request $_REQUEST['request'] contains endpoint/id or endpoint/verb/ or endpoint/search/keyword/

.htaccess puts everything after /name_of_your_choice/ directory inside the querystring "request" key

		RewriteRule ^(.*)$  api/v1.0/index.php?request=$1 [QSA,NC,L]
		RewriteRule ^(.*)/$ api/v1.0/index.php?request=$1 [QSA,NC,L]

## Configuration

Dont forget to modify the configuration file with your values in:

    /name_of_your_choice/api/elastest.api.v1.0.ini.php

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
- api_base_url = https://ericdelerue.com/elastest
- api_base_directory = /api/
- api_base_version = v1.0

Open these two files and modify with your values:

		/name_of_your_choice/api/v1.0/development.php
		/name_of_your_choice/api/v1.0/production.php




## Cache

Edit the following lines in the file "/api/v1.0/Api/ElastestAPI.class.php":

			$this->config = array_merge(array(  
			    'request_timeout' => 10,   
			    'csrf_token' => false,
			    
			    'cache' => true,     		    
			    'cache_type' => "TempFile" (default) or "Memcache" or "Memcached",
			    'cache_timeout' => 10,
			    'cache_path' => C:\Path\To\Your\Cache\Directory\cache, 		
			    	    
			    'offset' => 0,
			    'limit' => 50,    
			                         
			), $this->config);

Notable: \Elastest\Cache\TempFileCache.class.php

The method used for caching is faster than Redis, Memcache, APC, and other PHP caching solutions because all those solutions must serialize and unserialize objects, generally using PHP’s serialize or json_encode functions. 
By storing PHP objects - not string - in file cache memory across requests, we can avoid serialization completely.


## Code brief description	


index.php

Head of the API

		$ElastestAPI = new ElastestAPI();

		$ElastestAPI->handleHttpRequest(  HttpRequest::buildRequestFromGlobals()  )->send();

ElastestAPI.class.php

This class manage the API configuration . This class will act as a wrapper for all of the custom endpoints that our API will be using. 
 
The class will pass the request information on to a method of the class HttpController to actually perform the work. 
 
HttpController will: 

- take in our request, 
- grab the data provided in the headers or in the URI,
- grab the endpoint from the "request" key in the query string (see .htaccess), 
- detect the HTTP method (GET, HEAD, POST, PUT, PATCH, OPTIONS, DELETE) and 
- check and assemble any additional data provided in the header or in the URI. 
- pass the data ResourceController. 

It then return to this class which will handle the HTTP response and send it back to the client.

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
