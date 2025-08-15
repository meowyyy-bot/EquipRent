<?php
class DBConnect {
    private $host = 'localhost';
    private $dbname = 'hackathondb';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname}", 
                $this->username, 
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // echo "Database connection established successfully";
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function validateLogin($email, $password) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password_hash'])) {
                return [
                    'success' => true,
                    'user' => $user
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Invalid email or password'
            ];
            
        } catch(PDOException $e) {
            return [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    public function close() {
        $this->conn = null;
    }
}
?>