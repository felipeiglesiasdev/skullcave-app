<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SkullCave - Dashboard Independente</title>
    
    <!-- Fonte Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- CSS -->
    <link href="{{ asset('css/toast.css') }}" rel="stylesheet">
    <link href="{{ asset('css/independente.css') }}" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Esquerda - Disciplinas -->
        <div class="sidebar-disciplinas">
            <div class="sidebar-header">
                <div class="logo">
                    <img src="{{ asset('images/logo-skullcave3.png') }}" alt="Logo SkullCave">
                </div>
                
            </div>

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

            <!-- Footer da Sidebar -->
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="user-details">
                        <span class="user-name">{{ Auth::user()->nome ?? 'Usuário' }}</span>
                        <span class="user-email">{{ Auth::user()->email ?? '' }}</span>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Sair</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Área Principal - Conteúdo Dinâmico -->
        <div class="main-content">
            <div class="content-header">
                <div class="breadcrumb">
                    <span class="breadcrumb-item" id="disciplina-nome">Selecione uma disciplina</span>
                    <span class="breadcrumb-separator" id="breadcrumb-separator" style="display: none;"> / </span>
                    <span class="breadcrumb-item" id="topico-nome" style="display: none;">Tópico</span>
                </div>
                <div class="content-actions">
                    <button class="btn-add-content" id="btn-add-topico" onclick="abrirModalTopico()" style="display: none;">
                        <i class="fas fa-plus"></i>
                        Novo Tópico
                    </button>
                    <button class="btn-add-content" id="btn-add-flashcard" onclick="abrirModalFlashcard()" style="display: none;">
                        <i class="fas fa-plus"></i>
                        Novo Flashcard
                    </button>
                </div>
            </div>

            <!-- Área de Conteúdo -->
            <div class="content-area">
                <!-- Estado Inicial -->
                <div id="welcome-state" class="welcome-state" style="display: none;">
                    <div class="welcome-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h2>Bem-vindo ao seu Dashboard de Flashcards!</h2>
                    <p>Organize seus estudos criando disciplinas, tópicos e flashcards personalizados.</p>
                    <button class="btn-primary" onclick="abrirModalDisciplina()">
                        <i class="fas fa-plus"></i>
                        Criar primeira disciplina
                    </button>
                </div>

                <!-- Lista de Tópicos -->
                <div id="topicos-view" class="content-view" style="display: none;">
                    <div class="view-header">
                        <h3>Tópicos</h3>
                        <p>Selecione um tópico para ver seus flashcards</p>
                        <button class="btn btn-sm btn-outline-secondary" onclick="voltarParaDisciplinas()">
                            <i class="fas fa-arrow-left"></i> Voltar para Disciplinas
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
                                    <span class="flashcards-count">{{ count($topico->flashcards ?? []) }} tópicos</span>
                                </div>
                                <div class="topico-actions">
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

                <!-- Lista de Flashcards -->
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
            </div>
        </div>
    </div>

    <!-- Modal Disciplina -->
    <div id="disciplinaModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nova Disciplina</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="disciplinaForm">
                        @csrf
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome da Disciplina</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição (opcional)</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="criarDisciplina()">Criar Disciplina</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tópico -->
    <div id="topicoModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Novo Tópico</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="topicoForm">
                        @csrf
                        <input type="hidden" id="disciplina_id" name="disciplina_id">
                        <div class="mb-3">
                            <label for="topico_nome" class="form-label">Nome do Tópico</label>
                            <input type="text" class="form-control" id="topico_nome" name="nome" required>
                        </div>
                        <div class="mb-3">
                            <label for="topico_descricao" class="form-label">Descrição (opcional)</label>
                            <textarea class="form-control" id="topico_descricao" name="descricao" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="criarTopico()">Criar Tópico</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Flashcard -->
    <div id="flashcardModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Novo Flashcard</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="flashcardForm">
                        @csrf
                        <input type="hidden" id="topico_id" name="topico_id">
                        <div class="mb-3">
                            <label for="flashcard_titulo" class="form-label">Título do Flashcard</label>
                            <input type="text" class="form-control" id="flashcard_titulo" name="titulo" required>
                        </div>
                        <div class="mb-3">
                            <label for="flashcard_descricao" class="form-label">Descrição (opcional)</label>
                            <textarea class="form-control" id="flashcard_descricao" name="descricao" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="criarFlashcard()">Criar Flashcard</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS e dependências -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    
    <!-- JS -->
    <script src="{{ asset('js/independente/toast.js') }}"></script>
    <script src="{{ asset('js/independente/global.js') }}"></script>
    <script src="{{ asset('js/independente/utils.js') }}"></script>
    <script src="{{ asset('js/independente/views.js') }}"></script>
    <script src="{{ asset('js/independente/disciplinas.js') }}"></script>
    <script src="{{ asset('js/independente/topicos.js') }}"></script>
    <script src="{{ asset('js/independente/flashcards.js') }}"></script>
    <script src="{{ asset('js/independente/revisao.js') }}"></script>
</body>
</html>