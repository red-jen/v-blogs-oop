<?php


require_once 'BaseCrud.php';
require_once 'like.php';
require_once 'article.php';  // If you have an Article class
require_once 'user.php';  // If you have a User class
class Article {
    private $crud;

    public function __construct() {
        $this->crud = new BaseCrud('articles');
    }

    public function create($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->crud->create($data);
    }

    public function getWithDetails() {
        $query = "SELECT 
                    a.*,
                    u.username as author_name,
                    (SELECT COUNT(*) FROM likes WHERE article_id = a.id) as likes_count,
                    GROUP_CONCAT(c.name) as categories
                FROM articles a
                LEFT JOIN users u ON a.author_id = u.id
                LEFT JOIN article_categories ac ON a.id = ac.article_id
                LEFT JOIN categories c ON ac.category_id = c.id
                GROUP BY a.id
                ORDER BY a.created_at DESC";
        
        $stmt = $this->crud->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id) {
        // Start transaction
        $this->crud->db->beginTransaction();
        
        try {
            // Delete related records
            $tables = ['comments', 'likes', 'article_categories'];
            foreach ($tables as $table) {
                $query = "DELETE FROM {$table} WHERE article_id = :id";
                $stmt = $this->crud->db->prepare($query);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
            }

            // Delete the article
            $this->crud->delete($id);
            
            $this->crud->db->commit();
            return true;
        } catch (Exception $e) {
            $this->crud->db->rollBack();
            return false;
        }
    }

    public function getOne($id) {
        return $this->crud->read($id);
    }

    public function getAll() {
        return $this->crud->read();
    }
}