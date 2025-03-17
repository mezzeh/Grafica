<?php
class Commento {
    private $conn;
    private $table_name = "commenti";
    
    public $id;
    public $user_id;
    public $tipo_elemento;
    public $elemento_id;
    public $testo;
    public $data_creazione;
    public $data_modifica;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // CREATE
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET user_id=:user_id, tipo_elemento=:tipo_elemento, 
                  elemento_id=:elemento_id, testo=:testo";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizzazione
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->tipo_elemento = htmlspecialchars(strip_tags($this->tipo_elemento));
        $this->elemento_id = htmlspecialchars(strip_tags($this->elemento_id));
        $this->testo = htmlspecialchars(strip_tags($this->testo));
        
        // Binding
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":tipo_elemento", $this->tipo_elemento);
        $stmt->bindParam(":elemento_id", $this->elemento_id);
        $stmt->bindParam(":testo", $this->testo);
        
        return $stmt->execute();
    }
    
    // READ BY ELEMENTO
    public function readByElemento($tipo_elemento, $elemento_id) {
        $query = "SELECT c.*, u.username 
                  FROM " . $this->table_name . " c
                  LEFT JOIN users u ON c.user_id = u.id
                  WHERE c.tipo_elemento = :tipo_elemento 
                  AND c.elemento_id = :elemento_id
                  ORDER BY c.data_creazione DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":tipo_elemento", $tipo_elemento);
        $stmt->bindParam(":elemento_id", $elemento_id);
        $stmt->execute();
        
        return $stmt;
    }
    
    // READ ONE
    public function readOne() {
        $query = "SELECT c.*, u.username 
                  FROM " . $this->table_name . " c
                  LEFT JOIN users u ON c.user_id = u.id
                  WHERE c.id = :id 
                  LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->user_id = $row['user_id'];
            $this->tipo_elemento = $row['tipo_elemento'];
            $this->elemento_id = $row['elemento_id'];
            $this->testo = $row['testo'];
            $this->data_creazione = $row['data_creazione'];
            $this->data_modifica = $row['data_modifica'];
            return $row;
        }
        
        return false;
    }
    
    // UPDATE
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET testo = :testo
                  WHERE id = :id AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizzazione
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->testo = htmlspecialchars(strip_tags($this->testo));
        
        // Binding
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":testo", $this->testo);
        
        return $stmt->execute();
    }
    
    // DELETE
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // DELETE BY ADMIN (senza verifica user_id)
    public function deleteByAdmin() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // COUNT BY ELEMENTO
    public function countByElemento($tipo_elemento, $elemento_id) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
                  WHERE tipo_elemento = :tipo_elemento 
                  AND elemento_id = :elemento_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":tipo_elemento", $tipo_elemento);
        $stmt->bindParam(":elemento_id", $elemento_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
?>