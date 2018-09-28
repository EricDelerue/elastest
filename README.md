# elastest (solution one and two)

- *Elastique REST API test v1.0 and v2.0*
- *September 2018*
- *Eric Delerue delerue_eric@hotmail.com*
- *https://github.com/EricDelerue/elastest*

## What is it about?

These two versions of the RESTful API allows the user to 

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

## Installation, Configuration, Structure and Code brief description

See respective README.md files:

		/elastest/api/v1.0/README.md

		/elastest/api/v2.0/README.md
