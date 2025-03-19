<?php
// Includi il file di configurazione dei percorsi
require_once __DIR__ . '/config/paths.php';

// Reindirizza alla pagina principale
header("Location: " . getUrlPath('pages/index.php'));
exit;
?>