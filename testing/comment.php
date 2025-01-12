<?php
class Comment extends BaseCrud {
    public function __construct($db) {
        parent::__construct($db, 'comments');
    }

    public function create($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        return parent::create($data);
    }

    public function readByArticle($article_id) {
        $query = "SELECT 
                    c.*, COALESCE(u.username, c.visitor_name) as commenter_name
                FROM {$this->table} c
                LEFT JOIN users u ON c.user_id = u.id
                WHERE article_id = :article_id
                ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':article_id', $article_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>