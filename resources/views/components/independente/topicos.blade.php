<!-- Componente Tópicos Independente -->
<div id="topicos-view" class="content-view" style="display: none;">
    <div class="view-header">
        <h3>Tópicos</h3>
        <p>Selecione um tópico para ver seus flashcards</p>
        <button class="btn btn-sm btn-outline-secondary" onclick="voltarParaDisciplinas()">
            <i class="fas fa-arrow-left"></i> Início
        </button>
    </div>
    <div id="topicosList" class="items-grid">
        @forelse($disciplina->topicos ?? [] as $topico)
            <div class="topico-card" 
                 data-id="{{ $topico->id_topico }}"
                 onclick="selecionarTopico({{ $topico->id_topico }})">
                <div class="topico-icon">
                    <i class="fas fa-bookmark"></i>
                </div>
                <div class="topico-info">
                    <h6>{{ $topico->nome }}</h6>
                    <span class="flashcards-count">{{ count($topico->flashcards ?? []) }} flashcards</span>
                </div>
                <div class="topico-actions">
                    <button class="btn-action btn-edit-topico" onclick="event.stopPropagation(); abrirModalTopico({{ $topico->id_topico }}, '{{ $topico->nome }}', '{{ $topico->descricao }}')" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-action" onclick="event.stopPropagation(); removerTopico({{ $topico->id_topico }})" title="Excluir">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-bookmark"></i>
                <p>Nenhum tópico criado para esta disciplina</p>
                <button class="btn btn-primary btn-sm" onclick="abrirModalTopico()">
                    Criar primeiro tópico
                </button>
            </div>
        @endforelse
    </div>
</div>

