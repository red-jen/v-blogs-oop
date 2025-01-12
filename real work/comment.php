<?php

require_once 'BaseCrud.php';
require_once 'like.php';
require_once 'article.php';  // If you have an Article class
require_once 'user.php';  // If you have a User class
class Comment {
    private $crud;

    public function __construct() {
        $this->crud = new BaseCrud('comments');
    }

    public function create($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->crud->create($data);
    }

    public function getByArticle($article_id) {
        $query = "SELECT c.*, 
                    COALESCE(u.username, c.author_name) as commenter_name
                FROM comments c
                LEFT JOIN users u ON c.author_id = u.id
                WHERE c.article_id = :article_id
                ORDER BY c.created_at DESC";

        $stmt = $this->crud->db->prepare($query);
        $stmt->bindParam(':article_id', $article_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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