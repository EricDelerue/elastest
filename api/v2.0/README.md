# elastest (solution two)

- *Elastique REST API test v2.0*
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

    /name_of_your_choice/api/elastest.api.v2.0.ini.php

If not exists, create a database called: `elastest` and import/execute the following sql file:

    /name_of_your_choice/elastest.sql 
    
For version two of this API, an `applications` table were added to check Applications/Clients and Public/Secret keys (see /name_of_your_choice/elastest.sql). 

		CREATE TABLE `applications` (
		  `application_id` int(11) NOT NULL,
		  `application_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		  `application_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		  `application_secret` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		  `application_origin` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
		  `application_is_active` tinyint(4) NOT NULL DEFAULT '0'
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Applications / Clients Public / Secret keys table';


		INSERT INTO `applications` (`application_id`, `application_name`, `application_key`, `application_secret`, `application_origin`, `application_is_active`) VALUES
		(1, 'elastique', '64b62cf8af12ef490b37323027220cfbe7825f7f86bac7470afb131c5af22819', 'da254b2fa2eb38ce155b326792e5bc6df750110d8dcc389a007a58404af0c372', '127.0.0.1', -1);

		ALTER TABLE `applications`
		  ADD PRIMARY KEY (`application_id`);


Test the script by opening the following URL:

    http://localhost/name_of_your_choice/books/list or http://your.web.server/name_of_your_choice/books/list

## Routing with .htaccess

The request $_REQUEST['request'] contains endpoint/id or endpoint/verb/ or endpoint/search/keyword/

.htaccess puts everything after /name_of_your_choice/ directory inside the querystring "request" key

		RewriteRule ^(.*)$  api/v2.0/index.php?request=$1 [QSA,NC,L]
		RewriteRule ^(.*)/$ api/v2.0/index.php?request=$1 [QSA,NC,L]

## Configuration

Dont forget to modify the configuration file with your values in:

    /name_of_your_choice/api/elastest.api.v2.0.ini.php

These are the main configuration options and their default value:

[version_info]
- version_number = 2.0
- version_stable = 2.0
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
- api_base_version = v2.0

[production_db_info]
- db_hostname = Hostname of the database server 
- db_name = Database the connecting is made to (elastest)
- db_user = Username of the user connecting to the database (no default)
- db_password = Password of the user connecting to the database (no default)
- db_port = TCP port of the database server (defaults to driver default 3306)
- db_socket = 

[production_url_info]
- api_base_url = https://dev.ericdelerue.com/elastique
- api_base_directory = /api/
- api_base_version = v2.0

Open these two files and modify with your values:

		/name_of_your_choice/api/v2.0/development.php
		/name_of_your_choice/api/v2.0/production.php


## Cache

No cache implemented in this version. See version one.

## Autoload

The autoloader function is in /name_of_your_choice/api/v2.0/autoloader.php and is included in 

		/name_of_your_choice/api/v2.0/development.php  
		/name_of_your_choice/api/v1.0/production.php 

The autoloader function is called

		function classLoader($class_name) : void {}
	
It is then registered with:

		spl_autoload_register('classLoader');    

## Code brief description	

index.php

Head of the API

		$API = new ElastestAPI($_REQUEST['request'], $_SERVER['HTTP_ORIGIN']);
		echo str_replace('\\/', '/', $API->processAPI());

The declaration of class ElastestAPI extends API {} is in index.php
	
The abstract class API is in /name_of_your_choice/api/v2.0/Api/API.class.php

1 - This abstract class will act as a wrapper for all of the custom endpoints that our API will be using. 
 
To that extent, the abstract class will: 
- take in our request, 
- grab the endpoint from the URI string, 
- detect the HTTP method (GET, POST, PUT, DELETE) and 
- assemble any additional data provided in the header or in the URI. 
 
2 Once that's done, the abstract class will pass the request information on to a method in the concrete class ElastestAPI to actually perform the work. The concrete ElastestAPI class then calls the classes Books, Authors and Publishers respectively in Elastest/Books.class.php, Elastest/Authors.class.php, Elastest/Publishers.class.php

3 We then return to the abstract class which will handle forming a HTTP response back to the client.


## Structure

I use the api/v1.0/, api/v2.0/, etc.. structure to easily switch to a different version of the api. Modify it in .htaccess:

		RewriteRule ^(.*)$  api/v2.0/index.php?request=$1 [QSA,NC,L]
		RewriteRule ^(.*)/$ api/v2.0/index.php?request=$1 [QSA,NC,L]

The directories structure of the api is the following:

/name_of_your_choice/

/name_of_your_choice/logs/ 								  -> contains the errors log file 

/name_of_your_choice/keys/ 								  -> contains the public and secret keys  
  
/name_of_your_choice/api/										-> contains elastest.api.v2.0.ini.php   

/name_of_your_choice/api/v2.0/							-> contains index.php, autoloader.php, api-context.php, development.php, production.php, ElastestException.class.php, MysqliConnection.class.php

/name_of_your_choice/api/v1.0/Api						-> contains API and Application classes

/name_of_your_choice/api/v1.0/Elastest			-> contains Books, Authors and Publishers classes

## Errors

Modify the errors directory path in /name_of_your_choice/api/v2.0/development.php and /name_of_your_choice/api/v2.0/production.php 

		/** PHP ERRORS  ***/
		if(DEBUG){
			error_reporting(E_ALL);
		} else {
			error_reporting(E_ALL^E_WARNING^E_NOTICE);
		}

		ini_set("display_errors", 1);
		ini_set("log_errors", 1);
		ini_set("error_log", "Path/To/Errors/Directory/logs/php-errors.log");
		  
