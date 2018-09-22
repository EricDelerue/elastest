# elastest (solution one)

- *Elastique REST API test v1*
- *September 2018*
- *Eric Delerue delerue_eric@hotmail.com*
- *https://github.com/EricDelerue/elastest*


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
	
	
Installation





How it works?	
	