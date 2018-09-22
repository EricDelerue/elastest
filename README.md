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

Edit the following lines in the bottom of the file "api.php":

    $config = new Config([
        'username' => 'xxx',
        'password' => 'xxx',
        'database' => 'xxx',
    ]);

These are all the configuration options and their default value between brackets:

- "driver": "mysql"
- "address": Hostname of the database server ("localhost", "127.0.01")
- "port": TCP port of the database server (defaults to driver default)
- "username": Username of the user connecting to the database (no default)
- "password": Password of the user connecting to the database (no default)
- "database": Database the connecting is made to (no default)
- "cacheType": "TempFile" (default), "Redis", "Memcache" or "Memcached"
- "cachePath": Path/address of the cache (defaults to system's "temp" directory)
- "cacheTime": Number of seconds the cache is valid (10)


## Code brief description	


index.php

		$ElastestAPI = new ElastestAPI();

		$ElastestAPI->handleHttpRequest(  HttpRequest::buildRequestFromGlobals()  )->send();

ElastestAPI.class.php

		public function handleHttpRequest() {

				$this->getHttpRequestController()->handleHttpRequest($request, $this->response);
				return $this->response;
		}
	
HttpRequestController.class.php

		public function handleHttpRequest (HttpRequestInterface $request, HttpResponseInterface $response) {
		
				$this->getResourceController()->handleResourceRequest ($this->resourceType, $this->config, $this->response)
				return $this->response;
				
		}
	
ResourceController.class.php

		public function handleResourceRequest(ResourceTypeInterface $resourceType, HttpResponseInterface $response) {

		// Get data from Cache or Database
		// Build the response 

		}
