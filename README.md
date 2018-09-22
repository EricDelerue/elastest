# elastest

*Elastique REST API test*


This RESTful API 

- displays the list of all books, featured books, all authors and all publishers
- allows the user to search through all books
- and show a specific book, author or publisher by ID

Enpoints

The following enpoints are available:

Publishers
• All publishers
- /publishers/list
• A specific publisher by ID:
- /publishers/{id}
	
Authors
• All authors
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