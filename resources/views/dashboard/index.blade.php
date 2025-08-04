<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SkullCave - Dashboard</title>
    
    <!-- Fonte Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- CSS -->
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <!-- Header da Sidebar -->
            <div class="sidebar-header">
                <div class="logo">
                    <img src="{{ asset('images/logo-skullcave2.png') }}" alt="Logo SkullCave" class="img-fluid">
                </div>
            </div>

            <div class="sidebar-menu">
                <!-- Menu Principal -->
                <div class="menu-section">
                    <div class="menu-title">Menu</div>
                    <a href="#" class="menu-item active">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </div>

                <!-- Disciplinas -->
                <div class="menu-section">
                    <div class="menu-title">
                        Disciplinas
                        <button class="add-btn" onclick="openModal('disciplinaModal')">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    
                    <div id="disciplinasList">
                        @forelse($disciplinas ?? [] as $disciplina)
                            <div class="disciplina-item" data-id="{{ $disciplina->id_disciplina }}">
                                <div class="disciplina-header">
                                    <i class="fas fa-book"></i>
                                    <span>{{ $disciplina->nome }}</span>
                                    
                                    <!-- BOTÕES DE AÇÃO DA DISCIPLINA -->
                                    <div class="disciplina-actions">
                                        <button class="action-btn delete-btn" 
                                                onclick="removerDisciplina({{ $disciplina->id_disciplina }})"
                                                title="Remover disciplina">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <button class="toggle-btn" onclick="toggleDisciplina({{ $disciplina->id_disciplina }})">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                                
                                <div class="topicos-list" id="topicos-{{ $disciplina->id_disciplina }}">
                                    @forelse($disciplina->topicos ?? [] as $topico)
                                        <div class="topico-item" data-id="{{ $topico->id_topico }}">
                                            <i class="fas fa-bookmark"></i>
                                            <span>{{ $topico->nome }}</span>
                                            <span class="count">{{ $topico->flashcards->count() ?? 0 }}</span>
                                            <div class="topico-actions">
                                                <button class="action-btn delete-btn" 
                                                        onclick="removerTopico({{ $topico->id_topico }})"
                                                        title="Remover tópico">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="empty-state">Nenhum tópico</div>
                                    @endforelse
                                    
                                    <button class="add-topico-btn" onclick="openTopicoModal({{ $disciplina->id_disciplina }})">
                                        <i class="fas fa-plus"></i>
                                        <span>Novo Tópico</span>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">Nenhuma disciplina criada</div>
                        @endforelse
                    </div>

                    <button class="add-button" onclick="openModal('disciplinaModal')">
                        <i class="fas fa-plus"></i>
                        <span>Nova Disciplina</span>
                    </button>
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
                <!-- LOGOUT NO FINAL -->
                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Sair</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Conteúdo Principal -->
        <div class="main-content">
            <div class="content-header">
                <h1>Bem-vindo ao SkullCave!</h1>
                <p>Gerencie suas disciplinas e flashcards</p>
            </div>

            <!-- Cards de Estatísticas -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ count($disciplinas ?? []) }}</div>
                        <div class="stat-label">Disciplinas</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-bookmark"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $totalTopicos ?? 0 }}</div>
                        <div class="stat-label">Tópicos</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $totalFlashcards ?? 0 }}</div>
                        <div class="stat-label">Flashcards</div>
                    </div>
                </div>
            </div>

            <!-- Conteúdo Principal -->
            <div class="main-section">
                @if(count($disciplinas ?? []) == 0)
                    <div class="welcome-section">
                        <div class="welcome-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h2>Comece sua jornada de estudos!</h2>
                        <p>Crie sua primeira disciplina para organizar seus estudos.</p>
                        <button class="cta-button" onclick="openModal('disciplinaModal')">
                            <i class="fas fa-plus"></i>
                            Criar Primeira Disciplina
                        </button>
                    </div>
                @else
                    <div class="content-grid">
                        <div class="content-card">
                            <div class="card-header">
                                <h3>Disciplinas Recentes</h3>
                            </div>
                            <div class="card-content">
                                @foreach(($disciplinas ?? []) as $disciplina)
                                    <div class="disciplina-card">
                                        <i class="fas fa-book"></i>
                                        <div>
                                            <h4>{{ $disciplina->nome }}</h4>
                                            <p>{{ count($disciplina->topicos ?? []) }} tópicos</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Disciplina -->
    <div id="disciplinaModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Nova Disciplina</h3>
                <button class="close-btn" onclick="closeModal('disciplinaModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="disciplinaForm">
                    @csrf
                    <div class="form-group">
                        <label for="nome">Nome da Disciplina</label>
                        <input type="text" id="nome" name="nome" required>
                    </div>
                    <div class="form-group">
                        <label for="descricao">Descrição (opcional)</label>
                        <textarea id="descricao" name="descricao" rows="3"></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-secondary" onclick="closeModal('disciplinaModal')">Cancelar</button>
                        <button type="submit" class="btn-primary">Criar Disciplina</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Tópico -->
    <div id="topicoModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Novo Tópico</h3>
                <button class="close-btn" onclick="closeModal('topicoModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="topicoForm">
                    @csrf
                    <input type="hidden" id="disciplina_id" name="disciplina_id">
                    <div class="form-group">
                        <label for="topico_nome">Nome do Tópico</label>
                        <input type="text" id="topico_nome" name="nome" required>
                    </div>
                    <div class="form-group">
                        <label for="topico_descricao">Descrição (opcional)</label>
                        <textarea id="topico_descricao" name="descricao" rows="3"></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-secondary" onclick="closeModal('topicoModal')">Cancelar</button>
                        <button type="submit" class="btn-primary">Criar Tópico</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="{{ asset("js/dashboard_independente/script.js") }}"></script>
    <script src="{{ asset("js/dashboard_independente/disciplinas.js") }}"></script>
    <script src="{{ asset("js/dashboard_independente/topicos.js") }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            console.log("Dashboard carregado!");
            
            // INICIALIZA O FORMULÁRIO DE DISCIPLINA
            const disciplinaForm = document.getElementById("disciplinaForm");
            if (disciplinaForm) {
                disciplinaForm.addEventListener("submit", criarDisciplina);
            }

            // INICIALIZA O FORMULÁRIO DE TÓPICO
            const topicoForm = document.getElementById("topicoForm");
            if (topicoForm) {
                topicoForm.addEventListener("submit", criarTopico);
            }
        });
    </script>
</body>
</html>