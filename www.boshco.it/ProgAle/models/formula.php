<?php
class Formula {
    private $conn;
    private $table_name = "formule";
    private $relation_table = "formula_argomento";
    
    public $id;
    public $nome;
    public $espressione;
    public $descrizione;
    public $immagine;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // CREATE
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET nome=:nome, espressione=:espressione, 
                  descrizione=:descrizione, immagine=:immagine";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizzazione
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->espressione = htmlspecialchars(strip_tags($this->espressione));
        $this->descrizione = htmlspecialchars(strip_tags($this->descrizione));
        $this->immagine = htmlspecialchars(strip_tags($this->immagine));
        
        // Binding
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":espressione", $this->espressione);
        $stmt->bindParam(":descrizione", $this->descrizione);
        $stmt->bindParam(":immagine", $this->immagine);
        
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    // READ ALL
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " 
                  ORDER BY nome ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    // READ ONE
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE id = :id 
                  LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->nome = $row['nome'];
            $this->espressione = $row['espressione'];
            $this->descrizione = $row['descrizione'];
            $this->immagine = $row['immagine'];
            return true;
        }
        
        return false;
    }
    
    // UPDATE
    public function update() {
        $query = "";
        
        // Se c'è una nuova immagine, aggiornala
        if (!empty($this->immagine)) {
            $query = "UPDATE " . $this->table_name . "
                      SET nome = :nome, espressione = :espressione,
                      descrizione = :descrizione, immagine = :immagine
                      WHERE id = :id";
        } else {
            // Altrimenti mantieni l'immagine esistente
            $query = "UPDATE " . $this->table_name . "
                      SET nome = :nome, espressione = :espressione,
                      descrizione = :descrizione
                      WHERE id = :id";
        }
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizzazione
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->espressione = htmlspecialchars(strip_tags($this->espressione));
        $this->descrizione = htmlspecialchars(strip_tags($this->descrizione));
        if (!empty($this->immagine)) {
            $this->immagine = htmlspecialchars(strip_tags($this->immagine));
        }
        
        // Binding
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":espressione", $this->espressione);
        $stmt->bindParam(":descrizione", $this->descrizione);
        if (!empty($this->immagine)) {
            $stmt->bindParam(":immagine", $this->immagine);
        }
        
        return $stmt->execute();
    }
    
    // DELETE
    public function delete() {
        // Prima elimina tutte le relazioni con gli argomenti
        $this->removeAllArgomenti($this->id);
        
        // Poi elimina la formula
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // DELETE MULTIPLE
    public function deleteMultiple($ids) {
        if (empty($ids)) return false;
        
        // Prima elimina tutte le relazioni con gli argomenti per tutte le formule
        foreach ($ids as $id) {
            $this->removeAllArgomenti($id);
        }
        
        // Poi elimina le formule
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
        $query = "SELECT * FROM " . $this->table_name . "
                  WHERE nome LIKE :keywords 
                  OR espressione LIKE :keywords
                  OR descrizione LIKE :keywords
                  ORDER BY nome ASC";
        
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
    
    // Associa una formula a un argomento
    public function addArgomento($formula_id, $argomento_id) {
        $query = "INSERT INTO " . $this->relation_table . "
                  SET formula_id = :formula_id, argomento_id = :argomento_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":formula_id", $formula_id);
        $stmt->bindParam(":argomento_id", $argomento_id);
        
        return $stmt->execute();
    }
    
    // Rimuovi l'associazione tra una formula e un argomento
    public function removeArgomento($formula_id, $argomento_id) {
        $query = "DELETE FROM " . $this->relation_table . "
                  WHERE formula_id = :formula_id AND argomento_id = :argomento_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":formula_id", $formula_id);
        $stmt->bindParam(":argomento_id", $argomento_id);
        
        return $stmt->execute();
    }
    
    // Rimuovi tutte le associazioni per una formula
    public function removeAllArgomenti($formula_id) {
        $query = "DELETE FROM " . $this->relation_table . "
                  WHERE formula_id = :formula_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":formula_id", $formula_id);
        
        return $stmt->execute();
    }
    
    // Ottieni tutti gli argomenti associati a una formula
    public function getAssociatedArgomenti($formula_id) {
        $query = "SELECT a.id, a.titolo
                  FROM argomenti a
                  JOIN " . $this->relation_table . " fa ON a.id = fa.argomento_id
                  WHERE fa.formula_id = :formula_id
                  ORDER BY a.titolo ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":formula_id", $formula_id);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Verifica se un argomento è associato a una formula
    public function isArgomentoAssociated($formula_id, $argomento_id) {
        $query = "SELECT COUNT(*) as count
                  FROM " . $this->relation_table . "
                  WHERE formula_id = :formula_id AND argomento_id = :argomento_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":formula_id", $formula_id);
        $stmt->bindParam(":argomento_id", $argomento_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($row['count'] > 0);
    }
}
?>