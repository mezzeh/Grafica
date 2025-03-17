<?php
class Esame {
    private $conn;
    private $table_name = "esami";
    
    public $id;
    public $piano_id;
    public $nome;
    public $codice;
    public $crediti;
    public $descrizione;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    public function readOne() {
    $query = "SELECT e.*, p.nome as piano_nome 
              FROM " . $this->table_name . " e
              LEFT JOIN piani_di_studio p ON e.piano_id = p.id
              WHERE e.id = :id 
              LIMIT 0,1";
    
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":id", $this->id);
    
    try {
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->piano_id = $row['piano_id'];
            $this->nome = $row['nome'];
            $this->codice = $row['codice'];
            $this->crediti = $row['crediti'];
            $this->descrizione = $row['descrizione'];
            return $row;
        }
        
        return null; // Restituisci null invece di un array vuoto
    } catch(PDOException $e) {
        // Gestisci eventuali errori di database
        error_log('Errore nel recupero dell\'esame: ' . $e->getMessage());
        return null;
    }
}
    // CREATE
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET piano_id=:piano_id, nome=:nome, codice=:codice, 
                  crediti=:crediti, descrizione=:descrizione";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizzazione
        $this->piano_id = htmlspecialchars(strip_tags($this->piano_id));
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->codice = htmlspecialchars(strip_tags($this->codice));
        $this->crediti = htmlspecialchars(strip_tags($this->crediti));
        $this->descrizione = htmlspecialchars(strip_tags($this->descrizione));
        
        // Binding
        $stmt->bindParam(":piano_id", $this->piano_id);
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":codice", $this->codice);
        $stmt->bindParam(":crediti", $this->crediti);
        $stmt->bindParam(":descrizione", $this->descrizione);
        
        return $stmt->execute();
    }
    
    // READ ALL
    public function readAll() {
        $query = "SELECT e.*, p.nome as piano_nome 
                  FROM " . $this->table_name . " e
                  LEFT JOIN piani_di_studio p ON e.piano_id = p.id
                  ORDER BY e.nome ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    // READ BY PIANO
    public function readByPiano($piano_id) {
        $query = "SELECT * FROM " . $this->table_name . "
                  WHERE piano_id = :piano_id
                  ORDER BY nome ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":piano_id", $piano_id);
        $stmt->execute();
        
        return $stmt;
    }
    // UPDATE
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET piano_id = :piano_id, nome = :nome, codice = :codice,
                  crediti = :crediti, descrizione = :descrizione
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizzazione
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->piano_id = htmlspecialchars(strip_tags($this->piano_id));
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->codice = htmlspecialchars(strip_tags($this->codice));
        $this->crediti = htmlspecialchars(strip_tags($this->crediti));
        $this->descrizione = htmlspecialchars(strip_tags($this->descrizione));
        
        // Binding
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":piano_id", $this->piano_id);
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":codice", $this->codice);
        $stmt->bindParam(":crediti", $this->crediti);
        $stmt->bindParam(":descrizione", $this->descrizione);
        
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
    $query = "SELECT e.*, p.nome as piano_nome
              FROM " . $this->table_name . " e
              LEFT JOIN piani_di_studio p ON e.piano_id = p.id
              WHERE e.nome LIKE :keywords 
              OR e.codice LIKE :keywords
              OR e.descrizione LIKE :keywords
              ORDER BY e.nome ASC";
    
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
    
    // COUNT BY PIANO
    public function countByPiano($piano_id) {
        $query = "SELECT COUNT(*) as total_rows FROM " . $this->table_name . " WHERE piano_id = :piano_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":piano_id", $piano_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total_rows'];
    }
}
?>