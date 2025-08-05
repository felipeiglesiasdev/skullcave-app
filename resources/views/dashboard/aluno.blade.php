<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SkullCave - Dashboard Aluno</title>
    
    <!-- Fonte Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- CSS -->
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('css/aluno.css') }}" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Esquerda - Disciplinas -->
        <div class="sidebar-left">
            <div class="sidebar-header">
                <div class="logo">
                    <img src="{{ asset('images/logo-skullcave2.png') }}" alt="Logo SkullCave" class="img-fluid">
                </div>
                <div class="student-info">
                    <h5 class="user-type">Aluno</h5>
                    <div class="school-info">
                        <p class="school-name">{{ $escola->nome ?? 'Escola não definida' }}</p>
                        <p class="teacher-name">Prof. {{ $professor->nome ?? 'Professor não definido' }}</p>
                    </div>
                </div>
            </div>

            <div class="disciplinas-section">
                <!-- Minhas Disciplinas -->
                <div class="section-header">
                    <h6>Minhas Disciplinas</h6>
                    <button class="btn-add" onclick="openModal('disciplinaModal')">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                
                <div id="minhasDisciplinasList" class="disciplinas-list">
                    @forelse($minhasDisciplinas ?? [] as $disciplina)
                        <div class="disciplina-card minha-disciplina {{ $loop->first ? 'active' : '' }}" 
                             data-id="{{ $disciplina->id_disciplina }}"
                             data-tipo="minha"
                             onclick="selecionarDisciplina({{ $disciplina->id_disciplina }}, 'minha')">
                            <div class="disciplina-icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="disciplina-info">
                                <h6>{{ $disciplina->nome }}</h6>
                                <span class="topicos-count">{{ count($disciplina->topicos ?? []) }} tópicos</span>
                                <span class="disciplina-badge">Minha</span>
                            </div>
                            <div class="disciplina-actions">
                                <button class="btn-action" onclick="editarDisciplina({{ $disciplina->id_disciplina }})" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-action btn-delete" onclick="removerDisciplina({{ $disciplina->id_disciplina }})" title="Excluir">
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

                <!-- Disciplinas do Professor -->
                <div class="section-header mt-4">
                    <h6>Disciplinas do Professor</h6>
                </div>
                
                <div id="professorDisciplinasList" class="disciplinas-list">
                    @forelse($disciplinasProfessor ?? [] as $disciplina)
                        <div class="disciplina-card professor-disciplina" 
                             data-id="{{ $disciplina->id_disciplina_professor }}"
                             data-tipo="professor"
                             onclick="selecionarDisciplina({{ $disciplina->id_disciplina_professor }}, 'professor')">
                            <div class="disciplina-icon professor-icon">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <div class="disciplina-info">
                                <h6>{{ $disciplina->nome }}</h6>
                                <span class="topicos-count">{{ count($disciplina->topicos ?? []) }} tópicos</span>
                                <span class="disciplina-badge professor-badge">Professor</span>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <p>Nenhuma disciplina do professor</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Footer da Sidebar -->
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user-graduate"></i>
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

        <!-- Área Central - Tópicos -->
        <div class="content-center">
            <div class="topicos-header">
                <h5 id="disciplina-nome">Selecione uma disciplina</h5>
                <div class="header-actions">
                    <span id="disciplina-tipo" class="tipo-badge"></span>
                    <button class="btn-add" id="btn-add-topico" onclick="openModal('topicoModal')" style="display: none;">
                        <i class="fas fa-plus"></i>
                        Novo Tópico
                    </button>
                </div>
            </div>

            <div id="topicosList" class="topicos-list">
                <div class="empty-state">
                    <i class="fas fa-bookmark"></i>
                    <p>Selecione uma disciplina para ver os tópicos</p>
                </div>
            </div>
        </div>

        <!-- Área Direita - Flashcards -->
        <div class="content-right">
            <div class="flashcards-header">
                <h5 id="topico-nome">Selecione um tópico</h5>
                <div class="header-actions">
                    <button class="btn-add" id="btn-add-flashcard" onclick="openModal('flashcardModal')" style="display: none;">
                        <i class="fas fa-plus"></i>
                        Novo Flashcard
                    </button>
                    <button class="btn-study" id="btn-study" onclick="iniciarEstudo()" style="display: none;">
                        <i class="fas fa-play"></i>
                        Estudar
                    </button>
                </div>
            </div>

            <div id="flashcardsList" class="flashcards-list">
                <div class="empty-state">
                    <i class="fas fa-layer-group"></i>
                    <p>Selecione um tópico para ver os flashcards</p>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- JavaScript -->
    <script src="{{ asset('js/aluno.js') }}"></script>
</body>
</html>