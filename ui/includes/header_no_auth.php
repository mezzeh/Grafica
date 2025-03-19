<?php
session_start();

// Include the path utilities
require_once dirname(dirname(__DIR__)) . '/config/paths.php';

// If user is already logged in, redirect to homepage
if(isset($_SESSION['user_id'])) {
    header("Location: " . getUrlPath('pages/index.php'));
    exit;
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
  // Usa il percorso assoluto per caricare il CSS
  $css_file = getAbsolutePath('ui/css/style.css');
  if (file_exists($css_file)) {
    echo file_get_contents($css_file);
  } else {
    echo "/* CSS file not found: $css_file */";
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
                    <li><a href="<?php echo getUrlPath('pages/index.php'); ?>">Home</a></li>
                    <li><a href="<?php echo getUrlPath('pages/login.php'); ?>">Accedi</a></li>
                    <li><a href="<?php echo getUrlPath('pages/register.php'); ?>">Registrati</a></li>
                </ul>
            </nav>
        </header>
        <main>