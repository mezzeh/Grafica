<?php
// Verifica se il form è stato inviato
if(isset($_POST["password"])) {
    $password = $_POST["password"]; // Password in chiaro
    $hashed_password = password_hash($password, PASSWORD_BCRYPT); // Genera l'hash

    echo "<p>Hash generato: " . $hashed_password . "</p>"; // Visualizza l'hash generato
    
    // Mostra anche la query SQL completa da eseguire
    echo "<p>Query SQL da eseguire:</p>";
    echo "<code>INSERT INTO users (username, password, email, role) VALUES ('admin', '" . $hashed_password . "', 'admin@example.com', 'admin');</code>";
}
?>

<!-- Form per inserire la password da hashare -->
<form action="" method="POST">
    <label for="password">Password:</label>
    <input type="text" name="password" required>
    <button type="submit">Genera Hash</button>
</form>