<?php
class Like extends BaseCrud {
    public function __construct($db) {
        parent::__construct($db, 'likes');
    }

    public function toggle($article_id, $user_id) {
        if($this->exists($article_id, $user_id)) {
            return $this->deleteLike($article_id, $user_id);
        }
        return $this->create([
            'article_id' => $article_id,
            'user_id' => $user_id
        ]);
    }

    private function exists($article_id, $user_id) {
        $query = "SELECT id FROM {$this->table} 
                WHERE article_id = :article_id AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':article_id', $article_id);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    private function deleteLike($article_id, $user_id) {
        $query = "DELETE FROM {$this->table} 
                WHERE article_id = :article_id AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':article_id', $article_id);
        $stmt->bindValue(':user_id', $user_id);
        
        return $stmt->execute();
    }
}
?>