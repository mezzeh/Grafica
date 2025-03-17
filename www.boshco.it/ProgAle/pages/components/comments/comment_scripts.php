<?php
// File: pages/components/comments/comment_scripts.php
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestione pulsanti di modifica
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
    
    // Gestione pulsanti annulla modifica
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