<?php 

require_once 'BaseCrud.php';
require_once 'like.php';
require_once 'article.php';  // If you have an Article class
// If you have a User class
class User {
    private $crud;

    public function __construct() {
        $this->crud = new BaseCrud('users');
    }

    public function create($data) {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->crud->create($data);
    }

    public function login($username, $password) {
        $query = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->crud->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (password_verify($password, $user['password'])) {
                return $user;
            }
        }
        return false;
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