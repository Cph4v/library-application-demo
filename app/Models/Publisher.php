<?php

class Publisher extends Model
{
    public function all()
    {
        $stmt = Base::getInstance()->get('DB')->prepare('SELECT publishers.*,
            COUNT(books.publisher_id) AS books_count FROM publishers
            JOIN books ON publishers.id = books.publisher_id
            GROUP BY publishers.id');
        $stmt->execute();
        $publishers = [];
        foreach ($stmt->fetchAll() as $row) {
            $publishers[] = new Publisher(
                array_intersect_key(
                    $row,
                    array_flip(['id', 'name', 'books_count'])
                )
            );
        }

        return $publishers;
    }

    public function count()
    {
        $sql = 'SELECT publishers.*,
            COUNT(books.publisher_id) AS books_count FROM publishers
            JOIN books ON publishers.id = books.publisher_id
            GROUP BY publishers.id
            HAVING books_count > 0';
        $result = Base::getInstance()->get('DB')->prepare($sql); 
        $result->execute(); 
        $count = $result->fetchColumn();

        return $count;
    }
}
