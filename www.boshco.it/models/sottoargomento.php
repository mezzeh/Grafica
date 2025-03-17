<?php
class SottoArgomento {
    private $conn;
    private $table_name = "sottoargomenti";
    
    public $id;
    public $argomento_id;
    public $titolo;
    public $descrizione;
    public $livello_profondita;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // CREATE
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET argomento_id=:argomento_id, titolo=:titolo, 
                  descrizione=:descrizione, livello_profondita=:livello_profondita";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizzazione
        $this->argomento_id = htmlspecialchars(strip_tags($this->argomento_id));
        $this->titolo = htmlspecialchars(strip_tags($this->titolo));
        $this->descrizione = htmlspecialchars(strip_tags($this->descrizione));
        $this->livello_profondita = htmlspecialchars(strip_tags($this->livello_profondita));
        
        // Binding
        $stmt->bindParam(":argomento_id", $this->argomento_id);
        $stmt->bindParam(":titolo", $this->titolo);
        $stmt->bindParam(":descrizione", $this->descrizione);
        $stmt->bindParam(":livello_profondita", $this->livello_profondita);
        
        return $stmt->execute();
    }
    
    // READ ALL
    public function readAll() {
        $query = "SELECT s.*, a.titolo as argomento_titolo 
                  FROM " . $this->table_name . " s
                  LEFT JOIN argomenti a ON s.argomento_id = a.id
                  ORDER BY s.livello_profondita ASC, s.titolo ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    // READ BY ARGOMENTO
    public function readByArgomento($argomento_id) {
        $query = "SELECT * FROM " . $this->table_name . "
                  WHERE argomento_id = :argomento_id
                  ORDER BY livello_profondita ASC, titolo ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":argomento_id", $argomento_id);
        $stmt->execute();
        
        return $stmt;
    }
    
    // READ ONE
    public function readOne() {
        $query = "SELECT s.*, a.titolo as argomento_titolo 
                  FROM " . $this->table_name . " s
                  LEFT JOIN argomenti a ON s.argomento_id = a.id
                  WHERE s.id = :id 
                  LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->argomento_id = $row['argomento_id'];
            $this->titolo = $row['titolo'];
            $this->descrizione = $row['descrizione'];
            $this->livello_profondita = $row['livello_profondita'];
            return $row;
        }
        
        return [];
    }
    
    // UPDATE
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET argomento_id = :argomento_id, titolo = :titolo,
                  descrizione = :descrizione, livello_profondita = :livello_profondita
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizzazione
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->argomento_id = htmlspecialchars(strip_tags($this->argomento_id));
        $this->titolo = htmlspecialchars(strip_tags($this->titolo));
        $this->descrizione = htmlspecialchars(strip_tags($this->descrizione));
        $this->livello_profondita = htmlspecialchars(strip_tags($this->livello_profondita));
        
        // Binding
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":argomento_id", $this->argomento_id);
        $stmt->bindParam(":titolo", $this->titolo);
        $stmt->bindParam(":descrizione", $this->descrizione);
        $stmt->bindParam(":livello_profondita", $this->livello_profondita);
        
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
        $query = "SELECT s.*, a.titolo as argomento_titolo
                  FROM " . $this->table_name . " s
                  LEFT JOIN argomenti a ON s.argomento_id = a.id
                  WHERE s.titolo LIKE :keywords 
                  OR s.descrizione LIKE :keywords
                  ORDER BY s.livello_profondita ASC, s.titolo ASC";
        
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
    
    // COUNT BY ARGOMENTO
    public function countByArgomento($argomento_id) {
        $query = "SELECT COUNT(*) as total_rows FROM " . $this->table_name . " WHERE argomento_id = :argomento_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":argomento_id", $argomento_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total_rows'];
    }
    
    // ... codice esistente ...
    
    // NUOVI METODI PER GESTIRE LE RELAZIONI DEI PREREQUISITI
    
    // Aggiunge un argomento come prerequisito per questo sottoargomento
    public function addArgomentoPrerequisito($sottoargomento_id, $argomento_id) {
        $query = "INSERT INTO sottoargomento_argomento_prerequisito 
                  (sottoargomento_id, argomento_id) 
                  VALUES (:sottoargomento_id, :argomento_id)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sottoargomento_id', $sottoargomento_id);
        $stmt->bindParam(':argomento_id', $argomento_id);
        
        return $stmt->execute();
    }
    
    // Aggiunge un altro sottoargomento come prerequisito per questo sottoargomento
    public function addSottoargomentoPrerequisito($sottoargomento_id, $prerequisito_id) {
        $query = "INSERT INTO sottoargomento_sottoargomento_prerequisito 
                  (sottoargomento_id, prerequisito_id) 
                  VALUES (:sottoargomento_id, :prerequisito_id)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sottoargomento_id', $sottoargomento_id);
        $stmt->bindParam(':prerequisito_id', $prerequisito_id);
        
        return $stmt->execute();
    }
    
    // Rimuove un argomento prerequisito
    public function removeArgomentoPrerequisito($sottoargomento_id, $argomento_id) {
        $query = "DELETE FROM sottoargomento_argomento_prerequisito 
                  WHERE sottoargomento_id = :sottoargomento_id 
                  AND argomento_id = :argomento_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sottoargomento_id', $sottoargomento_id);
        $stmt->bindParam(':argomento_id', $argomento_id);
        
        return $stmt->execute();
    }
    
    // Rimuove un sottoargomento prerequisito
    public function removeSottoargomentoPrerequisito($sottoargomento_id, $prerequisito_id) {
        $query = "DELETE FROM sottoargomento_sottoargomento_prerequisito 
                  WHERE sottoargomento_id = :sottoargomento_id 
                  AND prerequisito_id = :prerequisito_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sottoargomento_id', $sottoargomento_id);
        $stmt->bindParam(':prerequisito_id', $prerequisito_id);
        
        return $stmt->execute();
    }
    
    // Rimuove tutti gli argomenti prerequisiti
    public function removeAllArgomentiPrerequisiti($sottoargomento_id) {
        $query = "DELETE FROM sottoargomento_argomento_prerequisito 
                  WHERE sottoargomento_id = :sottoargomento_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sottoargomento_id', $sottoargomento_id);
        
        return $stmt->execute();
    }
    
    // Rimuove tutti i sottoargomenti prerequisiti
    public function removeAllSottoargomentiPrerequisiti($sottoargomento_id) {
        $query = "DELETE FROM sottoargomento_sottoargomento_prerequisito 
                  WHERE sottoargomento_id = :sottoargomento_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sottoargomento_id', $sottoargomento_id);
        
        return $stmt->execute();
    }
    
    // Ottiene tutti gli argomenti prerequisiti
    public function getArgomentiPrerequisiti($sottoargomento_id) {
        $query = "SELECT a.id, a.titolo, a.descrizione, a.esame_id 
                  FROM argomenti a
                  JOIN sottoargomento_argomento_prerequisito sap 
                  ON a.id = sap.argomento_id
                  WHERE sap.sottoargomento_id = :sottoargomento_id
                  ORDER BY a.titolo";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sottoargomento_id', $sottoargomento_id);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Ottiene tutti i sottoargomenti prerequisiti
    public function getSottoargomentiPrerequisiti($sottoargomento_id) {
        $query = "SELECT s.id, s.titolo, s.descrizione, s.argomento_id 
                  FROM sottoargomenti s
                  JOIN sottoargomento_sottoargomento_prerequisito ssp 
                  ON s.id = ssp.prerequisito_id
                  WHERE ssp.sottoargomento_id = :sottoargomento_id
                  ORDER BY s.titolo";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sottoargomento_id', $sottoargomento_id);
        $stmt->execute();
        
        return $stmt;
    }

}
?>