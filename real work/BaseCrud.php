<?php
// If you have a User class
require_once 'config.php';

require_once 'like.php';
require_once 'article.php';  // If you have an Article class
require_once 'user.php';  // If you have a User class
class BaseCrud {
    protected $db;
    protected $table;

    public function __construct($table) {
       include('config.php');
        $this->db = (new Database())->connect();
        $this->table = $table;
    }

    // Create
    public function create($data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));          // kta5d lindex mnax tbda 3ad mnax tsali w3ad l3mara  

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));

        return $this->db->lastInsertId();
    }

    // Read
    public function read($id = null) {
        $sql = "SELECT * FROM {$this->table}" . ($id ? " WHERE id = ?" : "");
        $stmt = $this->db->prepare($sql);

        if ($id) {
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);    // fetch row wahdl argument kixd how lfetch style wax obj array assoc array 
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update 
    public function update($id, $data) {
        $setClause = implode(', ', array_map(fn($col) => "{$col} = ?", array_keys($data)));              // map kn3jno fiha dak larray dfirst paramether callbackfunction fiha xno hydar  w parametre tani how larray li hn5dmo

        $sql = "UPDATE {$this->table} SET {$setClause} WHERE id_voiture = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([...array_values($data), $id]);

        return $stmt->rowCount();
    }

    // Delete
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->rowCount();
    }
}
