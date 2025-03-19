<?php
class Esercizio {
    private $conn;
    private $table_name = "esercizi";
    
    public $id;
    public $sottoargomento_id;
    public $titolo;
    public $testo;
    public $soluzione;
    public $difficolta;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // CREATE
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET sottoargomento_id=:sottoargomento_id, titolo=:titolo, 
                  testo=:testo, soluzione=:soluzione, difficolta=:difficolta";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizzazione
        $this->sottoargomento_id = htmlspecialchars(strip_tags($this->sottoargomento_id));
        $this->titolo = htmlspecialchars(strip_tags($this->titolo));
        $this->testo = htmlspecialchars(strip_tags($this->testo));
        $this->soluzione = htmlspecialchars(strip_tags($this->soluzione));
        $this->difficolta = htmlspecialchars(strip_tags($this->difficolta));
        
        // Binding
        $stmt->bindParam(":sottoargomento_id", $this->sottoargomento_id);
        $stmt->bindParam(":titolo", $this->titolo);
        $stmt->bindParam(":testo", $this->testo);
        $stmt->bindParam(":soluzione", $this->soluzione);
        $stmt->bindParam(":difficolta", $this->difficolta);
        
        return $stmt->execute();
    }
    
    // READ ALL
    public function readAll() {
        $query = "SELECT e.*, s.titolo as sottoargomento_titolo 
                  FROM " . $this->table_name . " e
                  LEFT JOIN sottoargomenti s ON e.sottoargomento_id = s.id
                  ORDER BY e.difficolta ASC, e.titolo ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    // READ BY SOTTOARGOMENTO
    public function readBySottoArgomento($sottoargomento_id) {
        $query = "SELECT * FROM " . $this->table_name . "
                  WHERE sottoargomento_id = :sottoargomento_id
                  ORDER BY difficolta ASC, titolo ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":sottoargomento_id", $sottoargomento_id);
        $stmt->execute();
        
        return $stmt;
    }
    
    // READ ONE
    public function readOne() {
        $query = "SELECT e.*, s.titolo as sottoargomento_titolo 
                  FROM " . $this->table_name . " e
                  LEFT JOIN sottoargomenti s ON e.sottoargomento_id = s.id
                  WHERE e.id = :id 
                  LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->sottoargomento_id = $row['sottoargomento_id'];
            $this->titolo = $row['titolo'];
            $this->testo = $row['testo'];
            $this->soluzione = $row['soluzione'];
            $this->difficolta = $row['difficolta'];
            return $row;
        }
        
        return [];
    }
    
    // UPDATE
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET sottoargomento_id = :sottoargomento_id, titolo = :titolo,
                  testo = :testo, soluzione = :soluzione, difficolta = :difficolta
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizzazione
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->sottoargomento_id = htmlspecialchars(strip_tags($this->sottoargomento_id));
        $this->titolo = htmlspecialchars(strip_tags($this->titolo));
        $this->testo = htmlspecialchars(strip_tags($this->testo));
        $this->soluzione = htmlspecialchars(strip_tags($this->soluzione));
        $this->difficolta = htmlspecialchars(strip_tags($this->difficolta));
        
        // Binding
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":sottoargomento_id", $this->sottoargomento_id);
        $stmt->bindParam(":titolo", $this->titolo);
        $stmt->bindParam(":testo", $this->testo);
        $stmt->bindParam(":soluzione", $this->soluzione);
        $stmt->bindParam(":difficolta", $this->difficolta);
        
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
    public function search($keywords) {
        $query = "SELECT e.*, s.titolo as sottoargomento_titolo
                  FROM " . $this->table_name . " e
                  LEFT JOIN sottoargomenti s ON e.sottoargomento_id = s.id
                  WHERE e.titolo LIKE :keywords 
                  OR e.testo LIKE :keywords
                  OR e.soluzione LIKE :keywords
                  ORDER BY e.difficolta ASC, e.titolo ASC";
        
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
    
    // COUNT BY SOTTOARGOMENTO
    public function countBySottoArgomento($sottoargomento_id) {
        $query = "SELECT COUNT(*) as total_rows FROM " . $this->table_name . " WHERE sottoargomento_id = :sottoargomento_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":sottoargomento_id", $sottoargomento_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total_rows'];
    }
}
?>