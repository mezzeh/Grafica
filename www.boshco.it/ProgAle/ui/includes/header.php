<?php
session_start();

// Pagine accessibili a tutti (anche senza login)
$public_pages = ['index.php', 'login.php', 'register.php', 'view_piano.php', 'view_esame.php', 'view_argomento.php', 'view_esercizio.php', 'view_sottoargomento.php'];

// Verifica se la pagina corrente è riservata agli utenti autenticati
$current_page = basename($_SERVER['PHP_SELF']);
$requires_auth = !in_array($current_page, $public_pages);

// Se la pagina richiede autenticazione e l'utente non è loggato, reindirizza al login
if($requires_auth && !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Determina il percorso base corretto in base al nome del file$base_path = "";
$current_path = $_SERVER['PHP_SELF'];
$project_root = "/ProgAle/"; // Imposta qui la cartella base del tuo progetto

// Conta quanti livelli di directory ci sono dopo la radice del progetto
$path_after_root = substr($current_path, strpos($current_path, $project_root) + strlen($project_root));
$dir_levels = count(explode('/', trim($path_after_root, '/'))) - 1;

// Genera il percorso base corretto
$base_path = str_repeat("../", $dir_levels);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Gestione Piani di Studio</title>
    <style>
<?php
  // Leggi il contenuto del file CSS e incorporalo direttamente
  $css_file = $_SERVER['DOCUMENT_ROOT'] . '/ProgAle/ui/css/style.css';
  if (file_exists($css_file)) {
    echo file_get_contents($css_file);
  } else {
    // Fallback se il percorso diretto non funziona
    $relative_path = $base_path . 'ui/css/style.css';
    $relative_file = realpath(dirname(__FILE__) . '/' . $relative_path);
    if (file_exists($relative_file)) {
      echo file_get_contents($relative_file);
    }
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
                    <li><a href="<?php echo $base_path; ?>pages/index.php">Home</a></li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li><a href="<?php echo $base_path; ?>pages/my_piani.php">I Miei Piani</a></li>
                        <li><a href="<?php echo $base_path; ?>pages/esami.php">Esami</a></li>
                        <li><a href="<?php echo $base_path; ?>pages/argomenti.php">Argomenti</a></li>
                        <li><a href="<?php echo $base_path; ?>pages/sottoargomenti.php">Sottoargomenti</a></li>
                        <li><a href="<?php echo $base_path; ?>pages/esercizi.php">Esercizi</a></li>
                        <li><a href="<?php echo $base_path; ?>pages/formule.php">Formule</a></li>
                        <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                            <li><a href="<?php echo $base_path; ?>pages/admin/users.php">Gestione Utenti</a></li>
                        <?php endif; ?>
                        <li><a href="<?php echo $base_path; ?>pages/logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo $base_path; ?>pages/login.php">Accedi</a></li>
                        <li><a href="<?php echo $base_path; ?>pages/register.php">Registrati</a></li>
                    <?php endif; ?>
                    <li class="search-item">
                        <form action="<?php echo $base_path; ?>pages/search.php" method="GET" class="search-form">
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