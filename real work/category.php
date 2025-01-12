<?php

require_once 'BaseCrud.php';

class Category {
    private $crud;

    public function __construct() {
        $this->crud = new BaseCrud('categories');
    }

    public function attachToArticle($article_id, $category_id) {
        $query = "INSERT INTO article_categories (article_id, category_id) 
                 VALUES (:article_id, :category_id)";
        $stmt = $this->crud->db->prepare($query);
        $stmt->bindParam(':article_id', $article_id);
        $stmt->bindParam(':category_id', $category_id);
        return $stmt->execute();
    }

    public function getArticles($category_id) {
        $query = "SELECT a.* FROM articles a
                 JOIN article_categories ac ON a.id = ac.article_id
                 WHERE ac.category_id = :category_id
                 ORDER BY a.created_at DESC";
        
        $stmt = $this->crud->db->prepare($query);
        $stmt->bindParam(':category_id', $category_id);
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