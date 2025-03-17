<nav>
    <ul class="main-menu">
        <li><a href="index.php">Piani di Studio</a></li>
        <?php if(isset($_SESSION['user_id'])): ?>
            <li><a href="my_piani.php">I Miei Piani</a></li>
        <?php endif; ?>
        <li><a href="esami.php">Esami</a></li>
        <li><a href="argomenti.php">Argomenti</a></li>
        <li><a href="sottoargomenti.php">Sottoargomenti</a></li>
        <li><a href="esercizi.php">Esercizi</a></li>
        <li><a href="requisiti.php">Requisiti</a></li>
        <li><a href="formule.php">Formule</a></li>
        <?php if(isset($_SESSION['user_id']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
            <li><a href="admin/users.php">Gestione Utenti</a></li>
        <?php endif; ?>
        <?php if(isset($_SESSION['user_id'])): ?>
            <li><a href="logout.php">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php">Accedi</a></li>
            <li><a href="register.php">Registrati</a></li>
        <?php endif; ?>
        <li class="search-item">
            <form action="search.php" method="GET" class="search-form">
                <input type="text" name="q" placeholder="Cerca in tutto il sistema..." required>
                <button type="submit">Cerca</button>
            </form>
        </li>
    </ul>
</nav>