<?php
include('crud.php');
class User extends BaseCrud {
    private $remember_token;
    private $role_id;

    public function __construct($db) {
        parent::__construct($db, 'users');
    }

    public function sign($data) {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['remember_token'] = bin2hex(random_bytes(32));
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
         $this->create($data);
    }

    public function login($username, $password) {
        $query = "SELECT * FROM {$this->table} WHERE username = :username OR email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $username);
        $stmt->execute();

        if($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if(password_verify($password, $user['password'])) {
                return $user;
            }
        }
        return false;
    }

    public function updateRememberToken($user_id, $token) {
        return $this->update($user_id, ['remember_token' => $token]);
    }

    public function findByRememberToken($token) {
        $query = "SELECT * FROM {$this->table} WHERE remember_token = :token";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>