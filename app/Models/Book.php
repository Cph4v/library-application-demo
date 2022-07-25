<?php

class Book extends Model
{
    public function all()
    {
        $stmt = Base::getInstance()->get('DB')->prepare('SELECT quantity, books.id AS book_id,
            books.name AS book_name, authors.id AS author_id, authors.name AS author_name,
            publishers.id AS publisher_id, publishers.name AS publisher_name,
            (SELECT cc FROM (SELECT authors.id AS authors_id, COUNT(books.author_id) AS cc FROM authors
            JOIN books ON authors.id = books.author_id GROUP BY authors.id) x WHERE authors_id = author_id) AS author_books_count,
            (SELECT cc FROM (SELECT publishers.id AS publishers_id, COUNT(books.publisher_id) AS cc FROM publishers
            JOIN books ON publishers.id = books.publisher_id GROUP BY publishers.id) x WHERE publishers_id = publisher_id) AS publisher_books_count
            FROM books
            JOIN authors ON books.author_id = authors.id
            JOIN publishers ON books.publisher_id = publishers.id');
        $stmt->execute();
        $books = [];
        foreach ($stmt->fetchAll() as $row) {
            $author = new Author([
                'id' => $row['author_id'],
                'name' => $row['author_name'],
                'books_count' => $row['author_books_count']
            ]);
            $publisher = new Publisher([
                'id' => $row['publisher_id'],
                'name' => $row['publisher_name'],
                'books_count' => $row['publisher_books_count']
            ]);
            $book = new Book([
                'id' => $row['book_id'],
                'name' => $row['book_name'],
                'author' => $author,
                'publisher' => $publisher,
                'quantity' => $row['quantity']
            ]);
            $books[] = $book;
        }

        return $books;
    }

    public function count()
    {
        $sql = 'SELECT COUNT(*) FROM books'; 
        $result = Base::getInstance()->get('DB')->prepare($sql); 
        $result->execute(); 
        $count = $result->fetchColumn();

        return $count;
    }

    public function find($id)
    {
        $stmt = Base::getInstance()->get('DB')->prepare('SELECT quantity, books.id AS book_id,
            books.name AS book_name, authors.id AS author_id, authors.name AS author_name,
            publishers.id AS publisher_id, publishers.name AS publisher_name,
            (SELECT cc FROM (SELECT authors.id AS authors_id, COUNT(books.author_id) AS cc FROM authors
            JOIN books ON authors.id = books.author_id GROUP BY authors.id) x WHERE authors_id = author_id) AS author_books_count,
            (SELECT cc FROM (SELECT publishers.id AS publishers_id, COUNT(books.publisher_id) AS cc FROM publishers
            JOIN books ON publishers.id = books.publisher_id GROUP BY publishers.id) x WHERE publishers_id = publisher_id) AS publisher_books_count
            FROM books
            JOIN authors ON books.author_id = authors.id
            JOIN publishers ON books.publisher_id = publishers.id
            WHERE books.id = :id');
        $stmt->execute([':id' => $id]);
        if ($stmt->rowCount() == 0) {
            throw new Exception('Book not found');
        }
        $row = $stmt->fetch();
        $author = new Author([
            'id' => $row['author_id'],
            'name' => $row['author_name'],
            'books_count' => $row['author_books_count']
        ]);
        $publisher = new Publisher([
            'id' => $row['publisher_id'],
            'name' => $row['publisher_name'],
            'books_count' => $row['publisher_books_count']
        ]);
        $book = new Book([
            'id' => $row['book_id'],
            'name' => $row['book_name'],
            'author' => $author,
            'publisher' => $publisher,
            'quantity' => $row['quantity']
        ]);

        return $book;
    }

    public function save()
    {
        $stmt = Base::getInstance()->get('DB')->prepare(
            'UPDATE books SET name = :name, author_id = :author_id,
            publisher_id = :publisher_id, quantity = :quantity WHERE id = :id'
        );
        $stmt->execute([
            ':id' => $this->id,
            ':name' => $this->name,
            ':author_id' => $this->author->id,
            ':publisher_id' => $this->publisher->id,
            ':quantity' => $this->quantity
        ]);
        $book = $this->find($this->id);
        $this->author = $book->author;
        $this->publisher = $book->publisher;
    }

    public function delete()
    {
        $stmt = Base::getInstance()->get('DB')->prepare('DELETE FROM books WHERE id = :id');
        $stmt->execute([':id' => $this->id]);
    }
}
