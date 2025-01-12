<?php

include('crud.php');
class Category extends BaseCrud {
    public function __construct($db) {
        parent::__construct($db, 'categories');
    }

    public function getArticles($category_id) {
        $query = "SELECT a.* FROM articles a 
                WHERE a.category_id = :category_id 
                ORDER BY a.created_at DESC";
                
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>