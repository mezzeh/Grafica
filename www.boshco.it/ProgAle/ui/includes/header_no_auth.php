<?php
session_start();

// Calcola il percorso base in base alla posizione della pagina
$base_path = '';
$current_path = $_SERVER['PHP_SELF'];
if (strpos($current_path, '/pages/view_') !== false) {
    $base_path = '../../'; // Per pagine in sottocartelle
} elseif (strpos($current_path, '/pages/components/') !== false) {
    $base_path = '../../../'; // Per componenti ancora più in profondità
} else {
    $base_path = '../'; // Percorso standard per pagine in /pages/
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Gestione Piani di Studio</title>
    <style>
<?php
  // Anche questo usa lo stesso percorso assoluto
  $css_file = $_SERVER['DOCUMENT_ROOT'] . '/ProgAle/ui/css/style.css';
  if (file_exists($css_file)) {
    echo file_get_contents($css_file);
  }
?>
</style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Sistema Gestione Piani di Studio</h1>
            <!-- Menu semplificato per pagine che non richiedono autenticazione -->
            <nav>
                <ul class="main-menu">
                    <li><a href="../pages/index.php">Home</a></li>
                    <li><a href="login.php">Accedi</a></li>
                    <li><a href="register.php">Registrati</a></li>
                </ul>
            </nav>
        </header>
        <main>