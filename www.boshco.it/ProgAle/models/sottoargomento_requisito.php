<?php
class SottoargomentoRequisito {
    private $conn;
    private $table_name = "sottoargomento_requisito";
    
    public $id;
    public $sottoargomento_id;
    public $requisito_tipo;
    public $requisito_id;
    public $data_creazione;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Aggiunge un requisito a un sottoargomento
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET sottoargomento_id = :sottoargomento_id, 
                      requisito_tipo = :requisito_tipo,
                      requisito_id = :requisito_id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizzazione
        $this->sottoargomento_id = htmlspecialchars(strip_tags($this->sottoargomento_id));
        $this->requisito_tipo = htmlspecialchars(strip_tags($this->requisito_tipo));
        $this->requisito_id = htmlspecialchars(strip_tags($this->requisito_id));
        
        // Binding
        $stmt->bindParam(":sottoargomento_id", $this->sottoargomento_id);
        $stmt->bindParam(":requisito_tipo", $this->requisito_tipo);
        $stmt->bindParam(":requisito_id", $this->requisito_id);
        
        return $stmt->execute();
    }
    
    // Ottiene tutti i requisiti di un sottoargomento
    public function readBySottoargomento($sottoargomento_id) {
        $query = "SELECT sr.*, 
                    CASE 
                        WHEN sr.requisito_tipo = 'argomento' THEN a.titolo
                        WHEN sr.requisito_tipo = 'sottoargomento' THEN s.titolo
                        ELSE NULL
                    END as requisito_nome
                  FROM " . $this->table_name . " sr
                  LEFT JOIN argomenti a ON sr.requisito_tipo = 'argomento' AND sr.requisito_id = a.id
                  LEFT JOIN sottoargomenti s ON sr.requisito_tipo = 'sottoargomento' AND sr.requisito_id = s.id
                  WHERE sr.sottoargomento_id = :sottoargomento_id
                  ORDER BY sr.data_creazione ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":sottoargomento_id", $sottoargomento_id);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Legge un singolo requisito
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE id = :id 
                  LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->sottoargomento_id = $row['sottoargomento_id'];
            $this->requisito_tipo = $row['requisito_tipo'];
            $this->requisito_id = $row['requisito_id'];
            $this->data_creazione = $row['data_creazione'];
            return true;
        }
        
        return false;
    }
    
    // Elimina un requisito
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        
        return $stmt->execute();
    }
    
    // Rimuove tutti i requisiti di un sottoargomento
    public function deleteAllBySottoargomento($sottoargomento_id) {
        $query = "DELETE FROM " . $this->table_name . " 
                  WHERE sottoargomento_id = :sottoargomento_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":sottoargomento_id", $sottoargomento_id);
        
        return $stmt->execute();
    }

    // Verifica se un requisito esiste già
    public function exists() {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " 
                  WHERE sottoargomento_id = :sottoargomento_id 
                  AND requisito_tipo = :requisito_tipo 
                  AND requisito_id = :requisito_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":sottoargomento_id", $this->sottoargomento_id);
        $stmt->bindParam(":requisito_tipo", $this->requisito_tipo);
        $stmt->bindParam(":requisito_id", $this->requisito_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] > 0;
    }
}
?>