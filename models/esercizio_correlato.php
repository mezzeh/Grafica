<?php
class EsercizioCorrelato {
    private $conn;
    private $table_name = "esercizio_correlato";
    
    public $id;
    public $esercizio_id;
    public $esercizio_correlato_id;
    public $tipo_relazione;
    public $data_creazione;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Crea un nuovo collegamento tra esercizi
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET esercizio_id = :esercizio_id, 
                      esercizio_correlato_id = :esercizio_correlato_id,
                      tipo_relazione = :tipo_relazione";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizzazione
        $this->esercizio_id = htmlspecialchars(strip_tags($this->esercizio_id));
        $this->esercizio_correlato_id = htmlspecialchars(strip_tags($this->esercizio_correlato_id));
        $this->tipo_relazione = htmlspecialchars(strip_tags($this->tipo_relazione));
        
        // Binding
        $stmt->bindParam(":esercizio_id", $this->esercizio_id);
        $stmt->bindParam(":esercizio_correlato_id", $this->esercizio_correlato_id);
        $stmt->bindParam(":tipo_relazione", $this->tipo_relazione);
        
        return $stmt->execute();
    }
    
    // Legge tutti i collegamenti di un esercizio
    public function readByEsercizio($esercizio_id) {
        $query = "SELECT ec.*, e.titolo as esercizio_correlato_titolo 
                  FROM " . $this->table_name . " ec
                  JOIN esercizi e ON ec.esercizio_correlato_id = e.id
                  WHERE ec.esercizio_id = :esercizio_id
                  ORDER BY ec.tipo_relazione, e.titolo";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":esercizio_id", $esercizio_id);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Legge un singolo collegamento
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE id = :id 
                  LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->esercizio_id = $row['esercizio_id'];
            $this->esercizio_correlato_id = $row['esercizio_correlato_id'];
            $this->tipo_relazione = $row['tipo_relazione'];
            $this->data_creazione = $row['data_creazione'];
            return true;
        }
        
        return false;
    }
    
    // Elimina un collegamento
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        
        return $stmt->execute();
    }
    
    // Rimuove tutti i collegamenti di un esercizio
    public function deleteAllByEsercizio($esercizio_id) {
        $query = "DELETE FROM " . $this->table_name . " 
                  WHERE esercizio_id = :esercizio_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":esercizio_id", $esercizio_id);
        
        return $stmt->execute();
    }

    // Verifica se un collegamento esiste già
    public function exists() {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " 
                  WHERE esercizio_id = :esercizio_id 
                  AND esercizio_correlato_id = :esercizio_correlato_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":esercizio_id", $this->esercizio_id);
        $stmt->bindParam(":esercizio_correlato_id", $this->esercizio_correlato_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] > 0;
    }
}
?>