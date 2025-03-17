<?php
// Includi header (senza richiedere autenticazione)
include_once '../ui/includes/header_no_auth.php';

// Includi file di configurazione e modelli
include_once '../config/database.php';
include_once '../models/user.php';

// Inizializza variabili per messaggi
$message = "";
$message_class = "";

// Se l'utente è già loggato, reindirizza alla homepage
if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Gestione della registrazione
if(isset($_POST['register'])) {
    $database = new Database();
    $db = $database->getConnection();
    
    $user = new User($db);
    $user->username = $_POST['username'];
    $user->password = $_POST['password'];
    $user->email = $_POST['email'];
    $user->role = "user"; // Default role
    
    // Verifica se la password e la conferma coincidono
    if($_POST['password'] != $_POST['confirm_password']) {
        $message = "Le password non coincidono.";
        $message_class = "error";
    } else {
        // Verifica se l'username è già in uso
        $check_query = "SELECT id FROM users WHERE username = :username LIMIT 1";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->bindParam(":username", $user->username);
        $check_stmt->execute();
        
        if($check_stmt->rowCount() > 0) {
            $message = "Username già in uso.";
            $message_class = "error";
        } else {
            // Verifica se l'email è già in uso
            $check_query = "SELECT id FROM users WHERE email = :email LIMIT 1";
            $check_stmt = $db->prepare($check_query);
            $check_stmt->bindParam(":email", $user->email);
            $check_stmt->execute();
            
            if($check_stmt->rowCount() > 0) {
                $message = "Email già in uso.";
                $message_class = "error";
            } else {
                // Tenta di creare l'utente
                if($user->create()) {
                    $message = "Registrazione completata con successo! Ora puoi accedere.";
                    $message_class = "success";
                } else {
                    $message = "Impossibile completare la registrazione.";
                    $message_class = "error";
                }
            }
        }
    }
}
?>

<div class="container">
    <div class="auth-form">
        <h2>Registrati</h2>
        
        <?php if(!empty($message)): ?>
            <div class="message <?php echo $message_class; ?>"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <form action="" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Conferma Password</label>
                <input type="password" name="confirm_password" required>
            </div>
            
            <button type="submit" name="register" class="btn-primary">Registrati</button>
        </form>
        
        <div class="auth-links">
            <p>Hai già un account? <a href="login.php">Accedi</a></p>
        </div>
    </div>
</div>

<?php include_once '../ui/includes/footer.php'; ?>