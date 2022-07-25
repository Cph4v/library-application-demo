<?php

class BooksController
{
    public function list()
    {
        render('books', ['books' => (new Book())->all()]);
    }

    public function add()
    {
        foreach (['name', 'author', 'publisher'] as $field_name) {
            if (empty($_POST[$field_name])) {
                Flash::set('danger', 'همه‌ی اطلاعات باید وارد شوند.');
                redirect('/books/add');
                return;
            }
        }
        $pdo = Base::getInstance()->get('DB');
        $author_id = null;
        $stmt = $pdo->prepare('SELECT * FROM authors
            WHERE name = :name');
        $stmt->execute([':name' => $_POST['author']]);
        if ($stmt->rowCount() == 0) {
            $stmt = $pdo->prepare('INSERT INTO authors (name) VALUES (:name)');
            $stmt->execute([
                ':name' => $_POST['author']
            ]);
            $author_id = $pdo->lastInsertId();
        } else {
            $author_id = $stmt->fetch()['id'];
        }
        $publisher_id = null;
        $stmt = $pdo->prepare('SELECT * FROM publishers
            WHERE name = :name');
        $stmt->execute([':name' => $_POST['publisher']]);
        if ($stmt->rowCount() == 0) {
            $stmt = $pdo->prepare('INSERT INTO publishers (name) VALUES (:name)');
            $stmt->execute([
                ':name' => $_POST['publisher']
            ]);
            $publisher_id = $pdo->lastInsertId();
        } else {
            $publisher_id = $stmt->fetch()['id'];
        }
        $stmt = $pdo->prepare('INSERT INTO books (name, author_id, publisher_id, quantity)
            VALUES (:name, :author_id, :publisher_id, :quantity)');
        $stmt->execute([
            ':name' => $_POST['name'],
            ':author_id' => $author_id,
            ':publisher_id' => $publisher_id,
            ':quantity' => 1
        ]);
        Flash::set('success', 'کتاب با موفقیت افزوده شد!');
        redirect('/books');
    }

    public function addForm()
    {
        render('books_add');
    }

    public function reserve($id)
    {
        $book = (new Book())->find($id);
        if ($book->quantity > 0) {
            $book->quantity--;
        } else {
            Flash::set('danger', 'کتاب موردنظر در حال حاضر موجود نیست.');
            redirect('/books');
            return;
        }
        $book->save();
        Flash::set('success', 'کتاب با موفقیت رزرو شد!');
        redirect('/books');
    }

    public function unreserve($id)
    {
        $book = (new Book())->find($id);
        $book->quantity++;
        $book->save();
        Flash::set('success', 'موجودی کتاب با موفقیت افزایش یافت!');
        redirect('/books');
    }

    public function delete($id)
    {
        (new Book())->find($id)->delete();
        Flash::set('success', 'کتاب با موفقیت حذف شد!');
        redirect('/books');
    }
}
