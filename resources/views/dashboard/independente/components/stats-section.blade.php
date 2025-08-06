<!-- Estatísticas -->
<div class="stats-section">
    <div class="stats-header">
        <h6>Estatísticas</h6>
    </div>
    <div class="stats-grid">
        <div class="stat-item">
            <div class="stat-icon">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-info">
                <span class="stat-number" id="totalDisciplinas">{{ $totalDisciplinas ?? 0 }}</span>
                <span class="stat-label">Disciplinas</span>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon">
                <i class="fas fa-bookmark"></i>
            </div>
            <div class="stat-info">
                <span class="stat-number" id="totalTopicos">{{ $totalTopicos ?? 0 }}</span>
                <span class="stat-label">Tópicos</span>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="stat-info">
                <span class="stat-number" id="totalFlashcards">{{ $totalFlashcards ?? 0 }}</span>
                <span class="stat-label">Flashcards</span>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon">
                <i class="fas fa-question-circle"></i>
            </div>
            <div class="stat-info">
                <span class="stat-number" id="totalPerguntas">{{ $totalPerguntas ?? 0 }}</span>
                <span class="stat-label">Perguntas</span>
            </div>
        </div>
    </div>
</div>

