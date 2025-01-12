<?php

class Article extends BaseCrud {
    public function __construct($db) {
        parent::__construct($db, 'articles');
    }

    public function create($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        return parent::create($data);
    }

    public function readAll($filters = []) {
        $query = "SELECT 
                    a.*, u.username as author_name, c.name as category_name,
                    (SELECT COUNT(*) FROM likes WHERE article_id = a.id) as likes_count
                FROM {$this->table} a
                JOIN users u ON a.user_id = u.id
                JOIN categories c ON a.category_id = c.id";
        
        if(!empty($filters)) {
            $conditions = [];
            foreach($filters as $key => $value) {
                $conditions[] = "$key = :$key";
            }
            $query .= " WHERE " . implode(' AND ', $conditions);
        }
        
        $query .= " ORDER BY a.created_at DESC";
        $stmt = $this->conn->prepare($query);
        
        if(!empty($filters)) {
            foreach($filters as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id) {
        // First delete related records
        $this->deleteRelatedRecords($id);
        return parent::delete($id);
    }

    private function deleteRelatedRecords($id) {
        $tables = ['comments', 'likes'];
        foreach($tables as $table) {
            $query = "DELETE FROM {$table} WHERE article_id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        }
    }
}

?>