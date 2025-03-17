<?php
class User {
    private $conn;
    private $table_name = "users";
    
    public $id;
    public $username;
    public $password;
    public $email;
    public $role;
    public $created_at;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Registrazione utente
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET username=:username, password=:password, email=:email, role=:role";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizzazione
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->role = htmlspecialchars(strip_tags($this->role));
        
        // Hash della password
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
        
        // Binding
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":role", $this->role);
        
        return $stmt->execute();
    }
    
    // Login
    public function login() {
        $query = "SELECT id, username, password, role FROM " . $this->table_name . " 
                  WHERE username = :username LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $this->username);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(password_verify($this->password, $row['password'])) {
                $this->id = $row['id'];
                $this->role = $row['role'];
                return true;
            }
        }
        
        return false;
    }
    
    // Verifica se è admin
    public function isAdmin() {
        return $this->role === 'admin';
    }
    
    // Ottieni tutti gli utenti
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY username ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    // Leggi un utente specifico
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->role = $row['role'];
            $this->created_at = $row['created_at'];
            return true;
        }
        return false;
    }
    
    // Aggiorna un utente
    public function update() {
        // Se viene fornita una password, la aggiorniamo
        if(!empty($this->password)) {
            $query = "UPDATE " . $this->table_name . "
                      SET username=:username, password=:password, email=:email, role=:role
                      WHERE id = :id";
                      
            $this->password = password_hash($this->password, PASSWORD_BCRYPT);
        } else {
            // Altrimenti aggiorniamo solo gli altri campi
            $query = "UPDATE " . $this->table_name . "
                      SET username=:username, email=:email, role=:role
                      WHERE id = :id";
        }
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizzazione
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->role = htmlspecialchars(strip_tags($this->role));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        // Binding
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":role", $this->role);
        $stmt->bindParam(":id", $this->id);
        
        // Binding della password solo se è stata fornita
        if(!empty($this->password)) {
            $stmt->bindParam(":password", $this->password);
        }
        
        return $stmt->execute();
    }
    
    // Elimina un utente
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }
}
?>