<?php
session_start();

// Include the path utilities
require_once dirname(dirname(__DIR__)) . '/config/paths.php';

// Pagine accessibili a tutti (anche senza login)
$public_pages = ['index.php', 'login.php', 'register.php', 'view_piano.php', 'view_esame.php', 'view_argomento.php', 'view_esercizio.php', 'view_sottoargomento.php'];

// Verifica se la pagina corrente è riservata agli utenti autenticati
$current_page = basename($_SERVER['PHP_SELF']);
$requires_auth = !in_array($current_page, $public_pages);

// Se la pagina richiede autenticazione e l'utente non è loggato, reindirizza al login
if($requires_auth && !isset($_SESSION['user_id'])) {
    header("Location: " . getUrlPath('pages/login.php'));
    exit;
}

// Get the base path prefix for all resources
$base_path = getBasePathPrefix();
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
            
            <nav>
                <ul class="main-menu">
                    <li><a href="<?php echo getUrlPath('pages/index.php'); ?>">Home</a></li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li><a href="<?php echo getUrlPath('pages/my_piani.php'); ?>">I Miei Piani</a></li>
                        <li><a href="<?php echo getUrlPath('pages/esami.php'); ?>">Esami</a></li>
                        <li><a href="<?php echo getUrlPath('pages/argomenti.php'); ?>">Argomenti</a></li>
                        <li><a href="<?php echo getUrlPath('pages/sottoargomenti.php'); ?>">Sottoargomenti</a></li>
                        <li><a href="<?php echo getUrlPath('pages/esercizi.php'); ?>">Esercizi</a></li>
                        <li><a href="<?php echo getUrlPath('pages/formule.php'); ?>">Formule</a></li>
                        <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                            <li><a href="<?php echo getUrlPath('pages/admin/users.php'); ?>">Gestione Utenti</a></li>
                        <?php endif; ?>
                        <li><a href="<?php echo getUrlPath('pages/logout.php'); ?>">Logout</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo getUrlPath('pages/login.php'); ?>">Accedi</a></li>
                        <li><a href="<?php echo getUrlPath('pages/register.php'); ?>">Registrati</a></li>
                    <?php endif; ?>
                    <li class="search-item">
                        <form action="<?php echo getUrlPath('pages/search.php'); ?>" method="GET" class="search-form">
                            <input type="text" name="q" placeholder="Cerca in tutto il sistema..." required>
                            <button type="submit">Cerca</button>
                            <!-- Container per i risultati della ricerca in tempo reale -->
                            <div id="search-results" class="search-results-dropdown"></div>
                        </form>
                    </li>
                </ul>
            </nav>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <div class="user-info">
                    Benvenuto, <?php echo $_SESSION['username']; ?> 
                    [<?php echo isset($_SESSION['is_admin']) && $_SESSION['is_admin'] ? 'Admin' : 'Utente'; ?>]
                </div>
            <?php endif; ?>
        </header>
        <main>