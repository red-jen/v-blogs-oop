<?php

abstract class CRUD {
    protected $conn;
    protected $table;

    public function __construct($db, $table) {
        $this->conn = $db;
        $this->table = $table;
    }

    public function getOne($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll() {
        $query = "SELECT * FROM {$this->table}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add($data) {
        $columns = implode(', ', array_keys($data));
        $values = ':' . implode(', :', array_keys($data));
        
        $query = "INSERT INTO {$this->table} ({$columns}) VALUES ({$values})";
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute($data);
    }

    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}

class User extends CRUD {
    public function __construct($db) {
        parent::__construct($db, 'users');
    }

    public function create($data) {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['created_at'] = date('Y-m-d H:i:s');
        return parent::add($data);
    }

    public function login($username, $password) {
        $query = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $user;
            // if(password_verify($password, $user['password'])) {
            //     return $user;
            // }
        }
        return false;
    }
}

class Article extends CRUD {
    public function __construct($db) {
        parent::__construct($db, 'articles');
    }

    public function create($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return parent::add($data);
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
                ORDER BY a.updated_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id) {
        // Start transaction
        $this->conn->beginTransaction();
        
        try {
            // Delete related records
            $tables = ['comments', 'likes', 'article_categories'];
            foreach($tables as $table) {
                $query = "DELETE FROM {$table} WHERE article_id = :id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
            }

            // Delete the article
            parent::delete($id);
            
            $this->conn->commit();
            return true;
        } catch(Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}

class Comment extends CRUD {
    public function __construct($db) {
        parent::__construct($db, 'comments');
    }

    public function create($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return parent::add($data);
    }

    public function getByArticle($article_id) {
        $query = "SELECT c.*, 
                    COALESCE(u.username, c.author_name) as commenter_name
                FROM comments c
                LEFT JOIN users u ON c.author_id = u.id
                WHERE c.article_id = :article_id
                ORDER BY c.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':article_id', $article_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

class Category extends CRUD {
    public function __construct($db) {
        parent::__construct($db, 'categories');
    }

    public function attachToArticle($article_id, $category_id) {
        $query = "INSERT INTO article_categories (article_id, category_id) 
                 VALUES (:article_id, :category_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':article_id', $article_id);
        $stmt->bindParam(':category_id', $category_id);
        return $stmt->execute();
    }

    public function getArticles($category_id) {
        $query = "SELECT a.* FROM articles a
                 JOIN article_categories ac ON a.id = ac.article_id
                 WHERE ac.category_id = :category_id
                 ORDER BY a.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

class Like extends CRUD {
    public function __construct($db) {
        parent::__construct($db, 'likes');
    }

    public function toggle($article_id, $user_id) {
        $query = "SELECT id FROM likes 
                WHERE article_id = :article_id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':article_id', $article_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $query = "DELETE FROM likes 
                    WHERE article_id = :article_id AND user_id = :user_id";
        } else {
            $query = "INSERT INTO likes (article_id, user_id, created_at) 
                    VALUES (:article_id, :user_id, NOW())";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':article_id', $article_id);
        $stmt->bindParam(':user_id', $user_id);
        return $stmt->execute();
    }
}