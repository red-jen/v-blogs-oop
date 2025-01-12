<?php

require_once 'BaseCrud.php';

require_once 'article.php';  // If you have an Article class
require_once 'user.php';  // If you have a User class
class Like {
    private $crud;

    public function __construct() {
        $this->crud = new BaseCrud('likes');
    }

    public function toggle($article_id, $user_id) {
        $query = "SELECT id FROM likes 
                WHERE article_id = :article_id AND user_id = :user_id";
        $stmt = $this->crud->db->prepare($query);
        $stmt->bindParam(':article_id', $article_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $query = "DELETE FROM likes 
                    WHERE article_id = :article_id AND user_id = :user_id";
        } else {
            $query = "INSERT INTO likes (article_id, user_id, created_at) 
                    VALUES (:article_id, :user_id, NOW())";
        }

        $stmt = $this->crud->db->prepare($query);
        $stmt->bindParam(':article_id', $article_id);
        $stmt->bindParam(':user_id', $user_id);
        return $stmt->execute();
    }

    public function getOne($id) {
        return $this->crud->read($id);
    }

    public function getAll() {
        return $this->crud->read();
    }

    public function delete($id) {
        return $this->crud->delete($id);
    }
}