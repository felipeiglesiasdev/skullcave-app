<!-- Componente Header Independente -->
<div class="content-header">
    <div class="welcome-header">
        <div class="welcome-icon">
            <i class="fas fa-graduation-cap"></i>
        </div>
        <div class="welcome-text">
            <h2>Bem-vindo ao seu Dashboard de Flashcards!</h2>
            <p>Organize seus estudos criando disciplinas, tópicos e flashcards personalizados.</p>
        </div>
    </div>
    <div class="content-actions">
        @if(isset($showTopicoButton) && $showTopicoButton)
            <button class="btn-add-content" id="btn-add-topico" onclick="abrirModalTopico()">
                <i class="fas fa-plus"></i>
                Novo Tópico
            </button>
        @endif
        @if(isset($showFlashcardButton) && $showFlashcardButton)
            <button class="btn-add-content" id="btn-add-flashcard" onclick="abrirModalFlashcard()">
                <i class="fas fa-plus"></i>
                Novo Flashcard
            </button>
        @endif
    </div>
</div>

