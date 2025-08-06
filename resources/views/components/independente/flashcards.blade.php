<!-- Componente Flashcards Independente -->
<div id="flashcards-view" class="content-view" style="display: none;">
    <div class="view-header">
        <h3>Flashcards</h3>
        <p>Seus flashcards para estudo</p>
        <button class="btn btn-sm btn-outline-secondary" onclick="voltarParaTopicos()">
            <i class="fas fa-arrow-left"></i> Voltar para Tópicos
        </button>
    </div>
    <div id="flashcardsList" class="items-grid">
        @forelse($topico->flashcards ?? [] as $flashcard)
            <div class="flashcard-card" data-id="{{ $flashcard->id_flashcard }}">
                <div class="flashcard-header">
                    <h6 class="flashcard-title">{{ $flashcard->titulo }}</h6>
                    <div class="flashcard-actions">
                        <button class="btn-action" onclick="event.stopPropagation(); editarFlashcard({{ $flashcard->id_flashcard }})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-action btn-delete" onclick="event.stopPropagation(); removerFlashcard({{ $flashcard->id_flashcard }})" title="Excluir">
                            <i class="fas fa-trash"></i>
                        </button>
                        <button class="btn-action" onclick="event.stopPropagation(); criarPerguntaResposta({{ $flashcard->id_flashcard }})" title="Adicionar Pergunta">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button class="btn-action" onclick="abrirModalAdicionarPergunta({{ $flashcard->id_flashcard }})" title="Adicionar Pergunta">
                            <i class="fas fa-plus"></i>
                        </button>

                        <button class="btn-action" onclick="gerenciarPerguntas({{ $flashcard->id_flashcard }})" title="Gerenciar Perguntas">
                            <i class="fas fa-cog"></i>
                        </button>
                    </div>
                </div>
                @if($flashcard->descricao)
                    <p class="flashcard-description">{{ $flashcard->descricao }}</p>
                @endif
                @if($flashcard->perguntas->count() > 0)
                    <div class="flashcard-perguntas">
                        <h6>Perguntas e Respostas:</h6>
                        @foreach($flashcard->perguntas as $pergunta)
                            <div class="pergunta-item">
                                <div class="pergunta-texto">
                                    <strong>P:</strong> {{ $pergunta->pergunta }}
                                </div>
                                <div class="resposta-texto">
                                    <strong>R:</strong> {{ $pergunta->resposta }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="flashcard-footer">
                        <button class="btn btn-success btn-sm" onclick="iniciarRevisao({{ $flashcard->id_flashcard }})">
                            <i class="fas fa-play"></i> Iniciar Revisão
                        </button>
                    </div>
                @else
                    <div class="flashcard-footer">
                        <p class="text-muted">Adicione perguntas para poder iniciar a revisão</p>
                    </div>
                @endif
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-layer-group"></i>
                <p>Nenhum flashcard criado para este tópico</p>
                <button class="btn btn-primary btn-sm" onclick="abrirModalFlashcard()">
                    Criar primeiro flashcard
                </button>
            </div>
        @endforelse
    </div>
</div>

