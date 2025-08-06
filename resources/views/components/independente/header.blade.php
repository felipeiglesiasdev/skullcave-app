<!-- Componente Header Independente -->
<div class="content-header">
    <div class="breadcrumb">
        <span class="breadcrumb-item" id="disciplina-nome">{{ $breadcrumb['disciplina'] ?? 'Selecione uma disciplina' }}</span>
        @if(isset($breadcrumb['topico']))
            <span class="breadcrumb-separator"> / </span>
            <span class="breadcrumb-item" id="topico-nome">{{ $breadcrumb['topico'] }}</span>
        @endif
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

