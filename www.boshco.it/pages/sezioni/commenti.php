<?php
function gestioneCommentiArgomenti($db, $esame_id, $argomento_id) {
    // Verifica se l'utente è loggato
    if (!isset($_SESSION['user_id'])) {
        return ['message' => 'Accesso negato', 'message_class' => 'error'];
    }

    $commento = new Commento($db);

    // Aggiunta commento
    if (isset($_POST['add_comment'])) {
        $nuovo_commento = new Commento($db);
        $nuovo_commento->user_id = $_SESSION['user_id'];
        $nuovo_commento->tipo_elemento = 'argomento';
        $nuovo_commento->elemento_id = $argomento_id;
        $nuovo_commento->testo = $_POST['comment_text'];
        
        if ($nuovo_commento->create()) {
            return [
                'message' => 'Commento aggiunto con successo', 
                'message_class' => 'success',
                'redirect' => "argomenti.php?esame_id={$esame_id}&comment_added=1"
            ];
        }
    }

    // Aggiornamento commento
    if (isset($_POST['update_comment'])) {
        $commento->id = $_POST['comment_id'];
        $commento->user_id = $_SESSION['user_id'];
        $commento->testo = $_POST['comment_text'];
        
        // Verifica se è l'autore o admin
        $commento_esistente = $commento->readOne();
        if ($commento_esistente['user_id'] == $_SESSION['user_id'] || 
            (isset($_SESSION['is_admin']) && $_SESSION['is_admin'])) {
            
            if ($commento->update()) {
                return [
                    'message' => 'Commento aggiornato con successo', 
                    'message_class' => 'success',
                    'redirect' => "argomenti.php?esame_id={$esame_id}&comment_updated=1"
                ];
            }
        }
    }

    // Eliminazione commento
    if (isset($_GET['delete_comment'])) {
        $commento->id = $_GET['delete_comment'];
        $commento_esistente = $commento->readOne();
        
        // Verifica se è l'autore o admin
        if ($commento_esistente['user_id'] == $_SESSION['user_id'] || 
            (isset($_SESSION['is_admin']) && $_SESSION['is_admin'])) {
            
            if ($commento->delete()) {
                return [
                    'message' => 'Commento eliminato con successo', 
                    'message_class' => 'success',
                    'redirect' => "argomenti.php?esame_id={$esame_id}&comment_deleted=1"
                ];
            }
        }
    }

    return null;
}

function renderCommentiArgomenti($db, $esame_id, $argomento_id) {
    $commento = new Commento($db);
    $comment_count = $commento->countByElemento('argomento', $argomento_id);
    ?>
    <div class="comments-section">
        <h3>Commenti (<?php echo $comment_count; ?>)</h3>
        
        <?php if(isset($_SESSION['user_id'])): ?>
        <div class="comment-form">
            <form action="" method="POST">
                <input type="hidden" name="comment_tipo" value="argomento">
                <input type="hidden" name="comment_elemento_id" value="<?php echo $argomento_id; ?>">
                <textarea name="comment_text" placeholder="Aggiungi un commento..." required></textarea>
                <button type="submit" name="add_comment" class="btn-primary">Commenta</button>
            </form>
        </div>
        <?php else: ?>
        <p><a href="login.php">Accedi</a> per aggiungere un commento.</p>
        <?php endif; ?>
        
        <div class="comments-list">
            <?php
            $stmt_commenti = $commento->readByElemento('argomento', $argomento_id);
            
            if($stmt_commenti->rowCount() > 0) {
                while($row_commento = $stmt_commenti->fetch(PDO::FETCH_ASSOC)) {
                    echo '<div class="comment-item" id="comment-'.$row_commento['id'].'">';
                    echo '<div class="comment-header">';
                    echo '<span class="comment-author">'.htmlspecialchars($row_commento['username']).'</span>';
                    echo '<span class="comment-date">'.date('d/m/Y H:i', strtotime($row_commento['data_creazione'])).'</span>';
                    echo '</div>';
                    echo '<div class="comment-content">'.htmlspecialchars($row_commento['testo']).'</div>';
                    
                    // Opzioni per modificare/eliminare
                    if(isset($_SESSION['user_id']) && 
                       ($_SESSION['user_id'] == $row_commento['user_id'] || 
                        (isset($_SESSION['is_admin']) && $_SESSION['is_admin']))) {
                        echo '<div class="comment-actions">';
                        echo '<a href="#" class="edit-comment-btn" data-id="'.$row_commento['id'].'">Modifica</a> | ';
                        echo '<a href="?esame_id='.$esame_id.'&delete_comment='.$row_commento['id'].'" 
                              onclick="return confirm(\'Sei sicuro di voler eliminare questo commento?\');">Elimina</a>';
                        echo '</div>';
                        
                        // Form nascosto per la modifica
                        echo '<div class="edit-comment-form" id="edit-form-'.$row_commento['id'].'" style="display:none;">';
                        echo '<form action="" method="POST">';
                        echo '<input type="hidden" name="comment_id" value="'.$row_commento['id'].'">';
                        echo '<textarea name="comment_text" required>'.htmlspecialchars($row_commento['testo']).'</textarea>';
                        echo '<button type="submit" name="update_comment" class="btn-primary">Aggiorna</button>';
                        echo '<button type="button" class="cancel-edit btn-secondary">Annulla</button>';
                        echo '</form>';
                        echo '</div>';
                    }
                    
                    echo '</div>';
                }
            } else {
                echo '<p>Nessun commento presente. Sii il primo a commentare!</p>';
            }
            ?>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.edit-comment-btn');
        editButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const commentId = this.getAttribute('data-id');
                const commentContent = document.querySelector('#comment-' + commentId + ' .comment-content');
                const editForm = document.querySelector('#edit-form-' + commentId);
                
                if (commentContent && editForm) {
                    commentContent.style.display = 'none';
                    editForm.style.display = 'block';
                }
            });
        });
        
        const cancelButtons = document.querySelectorAll('.cancel-edit');
        cancelButtons.forEach(button => {
            button.addEventListener('click', function() {
                const form = this.closest('.edit-comment-form');
                if (form) {
                    const commentId = form.id.replace('edit-form-', '');
                    const commentContent = document.querySelector('#comment-' + commentId + ' .comment-content');
                    
                    if (commentContent) {
                        form.style.display = 'none';
                        commentContent.style.display = 'block';
                    }
                }
            });
        });
    });
    </script>
    <?php
}
