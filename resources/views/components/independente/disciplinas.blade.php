<!-- Componente Disciplinas Independente -->
<div class="disciplinas-section">
    <div class="section-header">
        <h6>Minhas Disciplinas</h6>
        <button class="btn-add" onclick="abrirModalDisciplina()" title="Adicionar Disciplina">
            <i class="fas fa-plus"></i>
        </button>
    </div>
    
    <div id="disciplinasList" class="disciplinas-list">
        @forelse($disciplinas ?? [] as $disciplina)
            <div class="disciplina-card {{ $loop->first ? 'active' : '' }}" 
                 data-id="{{ $disciplina->id_disciplina }}"
                 onclick="selecionarDisciplina({{ $disciplina->id_disciplina }})">
                <div class="disciplina-icon">
                    <i class="fas fa-book"></i>
                </div>
                <div class="disciplina-info">
                    <h6>{{ $disciplina->nome }}</h6>
                    <span class="topicos-count">{{ count($disciplina->topicos ?? []) }} tópicos</span>
                </div>
                <div class="disciplina-actions">
                    <button class="btn-action btn-edit" onclick="event.stopPropagation(); editarDisciplina({{ $disciplina->id_disciplina }}, '{{ $disciplina->nome }}', '{{ $disciplina->descricao }}')" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-action btn-delete" onclick="event.stopPropagation(); removerDisciplina({{ $disciplina->id_disciplina }})" title="Excluir">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-book-open"></i>
                <p>Nenhuma disciplina criada</p>
            </div>
        @endforelse
    </div>
</div>

