<?php
ob_start();

// Include path utilities
require_once dirname(__DIR__) . '/config/paths.php';

// Includi header (senza richiedere autenticazione)
include_once getAbsolutePath('ui/includes/header_no_auth.php');

// Includi file di configurazione e modelli
include_once getAbsolutePath('config/database.php');
include_once getAbsolutePath('models/user.php');

// Inizializza variabili per messaggi
$message = "";
$message_class = "";

// Se l'utente è già loggato, reindirizza alla homepage
if(isset($_SESSION['user_id'])) {
    header("Location: " . getUrlPath('pages/index.php'));
    exit;
}

// Gestione del login
if(isset($_POST['login'])) {
    $database = new Database();
    $db = $database->getConnection();
    
    $user = new User($db);
    $user->username = $_POST['username'];
    $user->password = $_POST['password'];
    
    if($user->login()) {
        // Imposta le variabili di sessione
        $_SESSION['user_id'] = $user->id;
        $_SESSION['username'] = $user->username;
        $_SESSION['is_admin'] = $user->isAdmin();
        
        // Redirect alla homepage
        header("Location: " . getUrlPath('pages/index.php'));
        exit;
    } else {
        $message = "Username o password non validi.";
        $message_class = "error";
    }
}
ob_end_flush();
?>

<div class="container">
    <div class="auth-form">
        <h2>Accedi</h2>
        
        <?php if(!empty($message)): ?>
            <div class="message <?php echo $message_class; ?>"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <form action="" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" required>
            </div>
            
            <button type="submit" name="login" class="btn-primary">Accedi</button>
        </form>
        
        <div class="auth-links">
            <p>Non hai un account? <a href="<?php echo getUrlPath('pages/register.php'); ?>">Registrati</a></p>
        </div>
    </div>
</div>

<?php include_once getAbsolutePath('ui/includes/footer.php'); ?>