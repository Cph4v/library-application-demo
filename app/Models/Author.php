<?php

class Author extends Model
{
    public function all()
    {
        $stmt = Base::getInstance()->get('DB')->prepare('SELECT authors.*,
            COUNT(books.author_id) AS books_count FROM authors
            JOIN books ON authors.id = books.author_id
            GROUP BY authors.id');
        $stmt->execute();
        $authors = [];
        foreach ($stmt->fetchAll() as $row) {
            $authors[] = new Author(
                array_intersect_key(
                    $row,
                    array_flip(['id', 'name', 'books_count'])
                )
            );
        }

        return $authors;
    }

    public function count()
    {
        $sql = 'SELECT authors.*,
            COUNT(books.author_id) AS books_count FROM authors
            JOIN books ON authors.id = books.author_id
            GROUP BY authors.id
            HAVING books_count > 0';
        $result = Base::getInstance()->get('DB')->prepare($sql); 
        $result->execute(); 
        $count = $result->fetchColumn();

        return $count;
    }
}
