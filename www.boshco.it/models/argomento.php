<?php
class Argomento {
    private $conn;
    private $table_name = "argomenti";
    
    public $id;
    public $esame_id;
    public $titolo;
    public $descrizione;
    public $livello_importanza;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // CREATE
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET esame_id=:esame_id, titolo=:titolo, 
                  descrizione=:descrizione, livello_importanza=:livello_importanza";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizzazione
        $this->esame_id = htmlspecialchars(strip_tags($this->esame_id));
        $this->titolo = htmlspecialchars(strip_tags($this->titolo));
        $this->descrizione = htmlspecialchars(strip_tags($this->descrizione));
        $this->livello_importanza = htmlspecialchars(strip_tags($this->livello_importanza));
        
        // Binding
        $stmt->bindParam(":esame_id", $this->esame_id);
        $stmt->bindParam(":titolo", $this->titolo);
        $stmt->bindParam(":descrizione", $this->descrizione);
        $stmt->bindParam(":livello_importanza", $this->livello_importanza);
        
        return $stmt->execute();
    }
    
    // READ ALL
    public function readAll() {
        $query = "SELECT a.*, e.nome as esame_nome 
                  FROM " . $this->table_name . " a
                  LEFT JOIN esami e ON a.esame_id = e.id
                  ORDER BY a.livello_importanza DESC, a.titolo ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    // READ BY ESAME
    public function readByEsame($esame_id) {
        $query = "SELECT * FROM " . $this->table_name . "
                  WHERE esame_id = :esame_id
                  ORDER BY livello_importanza DESC, titolo ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":esame_id", $esame_id);
        $stmt->execute();
        
        return $stmt;
    }
    
    // READ ONE
    public function readOne() {
        $query = "SELECT a.*, e.nome as esame_nome 
                  FROM " . $this->table_name . " a
                  LEFT JOIN esami e ON a.esame_id = e.id
                  WHERE a.id = :id 
                  LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->esame_id = $row['esame_id'];
            $this->titolo = $row['titolo'];
            $this->descrizione = $row['descrizione'];
            $this->livello_importanza = $row['livello_importanza'];
            return $row;
        }
        
        return [];
    }
    
    // UPDATE
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET esame_id = :esame_id, titolo = :titolo,
                  descrizione = :descrizione, livello_importanza = :livello_importanza
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizzazione
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->esame_id = htmlspecialchars(strip_tags($this->esame_id));
        $this->titolo = htmlspecialchars(strip_tags($this->titolo));
        $this->descrizione = htmlspecialchars(strip_tags($this->descrizione));
        $this->livello_importanza = htmlspecialchars(strip_tags($this->livello_importanza));
        
        // Binding
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":esame_id", $this->esame_id);
        $stmt->bindParam(":titolo", $this->titolo);
        $stmt->bindParam(":descrizione", $this->descrizione);
        $stmt->bindParam(":livello_importanza", $this->livello_importanza);
        
        return $stmt->execute();
    }
    
    // DELETE
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // DELETE MULTIPLE
    public function deleteMultiple($ids) {
        if (empty($ids)) return false;
        $inQuery = implode(',', array_fill(0, count($ids), '?'));
        $query = "DELETE FROM " . $this->table_name . " WHERE id IN ($inQuery)";
        $stmt = $this->conn->prepare($query);
        foreach ($ids as $index => $id) {
            $stmt->bindValue($index + 1, $id, PDO::PARAM_INT);
        }
        return $stmt->execute();
    }
    
    // SEARCH
   // SEARCH
public function search($keywords) {
    $query = "SELECT a.*, e.nome as esame_nome
              FROM " . $this->table_name . " a
              LEFT JOIN esami e ON a.esame_id = e.id
              WHERE a.titolo LIKE :keywords 
              OR a.descrizione LIKE :keywords
              ORDER BY a.livello_importanza DESC, a.titolo ASC";
    
    $stmt = $this->conn->prepare($query);
    $keywords = "%{$keywords}%";
    $stmt->bindParam(":keywords", $keywords);
    $stmt->execute();
    
    return $stmt;
}

    // COUNT
    public function count() {
        $query = "SELECT COUNT(*) as total_rows FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total_rows'];
    }
    
    // COUNT BY ESAME
    public function countByEsame($esame_id) {
        $query = "SELECT COUNT(*) as total_rows FROM " . $this->table_name . " WHERE esame_id = :esame_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":esame_id", $esame_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total_rows'];
    }
}
?>