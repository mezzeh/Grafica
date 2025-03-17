<?php
class Requisito {
    private $conn;
    private $table_name = "requisiti";
    private $relation_table = "requisito_argomento";
    
    public $id;
    public $esercizio_id;
    public $descrizione;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // CREATE
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET esercizio_id=:esercizio_id, descrizione=:descrizione";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizzazione
        $this->esercizio_id = htmlspecialchars(strip_tags($this->esercizio_id));
        $this->descrizione = htmlspecialchars(strip_tags($this->descrizione));
        
        // Binding
        $stmt->bindParam(":esercizio_id", $this->esercizio_id);
        $stmt->bindParam(":descrizione", $this->descrizione);
        
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }
    
    // READ ALL
    public function readAll() {
        $query = "SELECT r.*, e.titolo as esercizio_titolo 
                  FROM " . $this->table_name . " r
                  LEFT JOIN esercizi e ON r.esercizio_id = e.id
                  ORDER BY e.titolo ASC, r.id ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    // READ BY ESERCIZIO
    public function readByEsercizio($esercizio_id) {
        $query = "SELECT * FROM " . $this->table_name . "
                  WHERE esercizio_id = :esercizio_id
                  ORDER BY id ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":esercizio_id", $esercizio_id);
        $stmt->execute();
        
        return $stmt;
    }
    
    // READ ONE
    public function readOne() {
        $query = "SELECT r.*, e.titolo as esercizio_titolo 
                  FROM " . $this->table_name . " r
                  LEFT JOIN esercizi e ON r.esercizio_id = e.id
                  WHERE r.id = :id 
                  LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->esercizio_id = $row['esercizio_id'];
            $this->descrizione = $row['descrizione'];
            return true;
        }
        
        return false;
    }
    
    // UPDATE
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET esercizio_id = :esercizio_id, descrizione = :descrizione
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizzazione
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->esercizio_id = htmlspecialchars(strip_tags($this->esercizio_id));
        $this->descrizione = htmlspecialchars(strip_tags($this->descrizione));
        
        // Binding
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":esercizio_id", $this->esercizio_id);
        $stmt->bindParam(":descrizione", $this->descrizione);
        
        return $stmt->execute();
    }
    
    // DELETE
    public function delete() {
        // Prima rimuovi tutte le associazioni con argomenti
        $this->removeAllArgomenti($this->id);
        
        // Poi elimina il requisito
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // DELETE MULTIPLE
    public function deleteMultiple($ids) {
        if (empty($ids)) return false;
        
        // Prima rimuovi tutte le associazioni con argomenti per tutti i requisiti
        foreach ($ids as $id) {
            $this->removeAllArgomenti($id);
        }
        
        // Poi elimina i requisiti
        $inQuery = implode(',', array_fill(0, count($ids), '?'));
        $query = "DELETE FROM " . $this->table_name . " WHERE id IN ($inQuery)";
        $stmt = $this->conn->prepare($query);
        foreach ($ids as $index => $id) {
            $stmt->bindValue($index + 1, $id, PDO::PARAM_INT);
        }
        return $stmt->execute();
    }
    
    // SEARCH
    public function search($keywords) {
        $query = "SELECT r.*, e.titolo as esercizio_titolo
                  FROM " . $this->table_name . " r
                  LEFT JOIN esercizi e ON r.esercizio_id = e.id
                  WHERE r.descrizione LIKE :keywords
                  ORDER BY e.titolo ASC, r.id ASC";
        
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
    
    // COUNT BY ESERCIZIO
    public function countByEsercizio($esercizio_id) {
        $query = "SELECT COUNT(*) as total_rows FROM " . $this->table_name . " WHERE esercizio_id = :esercizio_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":esercizio_id", $esercizio_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total_rows'];
    }
    
    // NUOVI METODI PER GESTIRE LE RELAZIONI CON GLI ARGOMENTI
    
    // Associa un requisito a un argomento
    public function addArgomento($requisito_id, $argomento_id) {
        $query = "INSERT INTO " . $this->relation_table . "
                  SET requisito_id = :requisito_id, argomento_id = :argomento_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":requisito_id", $requisito_id);
        $stmt->bindParam(":argomento_id", $argomento_id);
        
        return $stmt->execute();
    }
    
    // Rimuovi l'associazione tra un requisito e un argomento
    public function removeArgomento($requisito_id, $argomento_id) {
        $query = "DELETE FROM " . $this->relation_table . "
                  WHERE requisito_id = :requisito_id AND argomento_id = :argomento_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":requisito_id", $requisito_id);
        $stmt->bindParam(":argomento_id", $argomento_id);
        
        return $stmt->execute();
    }
    
    // Rimuovi tutte le associazioni per un requisito
    public function removeAllArgomenti($requisito_id) {
        $query = "DELETE FROM " . $this->relation_table . "
                  WHERE requisito_id = :requisito_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":requisito_id", $requisito_id);
        
        return $stmt->execute();
    }
    
    // Ottieni tutti gli argomenti associati a un requisito
    public function getAssociatedArgomenti($requisito_id) {
        $query = "SELECT a.id, a.titolo, a.descrizione, a.esame_id
                  FROM argomenti a
                  JOIN " . $this->relation_table . " ra ON a.id = ra.argomento_id
                  WHERE ra.requisito_id = :requisito_id
                  ORDER BY a.titolo ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":requisito_id", $requisito_id);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Verifica se un argomento è associato a un requisito
    public function isArgomentoAssociated($requisito_id, $argomento_id) {
        $query = "SELECT COUNT(*) as count
                  FROM " . $this->relation_table . "
                  WHERE requisito_id = :requisito_id AND argomento_id = :argomento_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":requisito_id", $requisito_id);
        $stmt->bindParam(":argomento_id", $argomento_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($row['count'] > 0);
    }
}
?>