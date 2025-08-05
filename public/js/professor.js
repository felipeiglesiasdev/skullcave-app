// JavaScript específico para Dashboard Professor

let turmaSelecionada = null;
let disciplinaSelecionada = null;
let topicoSelecionado = null;
let alunosTurma = [];

document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard Professor carregado!');
    
    // Carregar turmas ao iniciar
    carregarTurmas();
    
    // Se há turmas, selecionar a primeira
    setTimeout(() => {
        const primeiraTurma = document.querySelector('.turma-card.active');
        if (primeiraTurma) {
            const turmaId = primeiraTurma.dataset.id;
            selecionarTurma(turmaId);
        }
    }, 500);
});

// ===== FUNÇÕES DE TURMAS =====

function selecionarTurma(turmaId) {
    turmaSelecionada = turmaId;
    disciplinaSelecionada = null;
    topicoSelecionado = null;
    
    // Atualizar visual das turmas
    document.querySelectorAll('.turma-card').forEach(card => {
        card.classList.remove('active');
    });
    
    const turmaCard = document.querySelector(`[data-id="${turmaId}"]`);
    if (turmaCard) {
        turmaCard.classList.add('active');
        
        // Atualizar nome da turma no badge
        const nomeTurma = turmaCard.querySelector('h6').textContent;
        document.getElementById('turma-badge').textContent = nomeTurma;
    }
    
    // Carregar disciplinas da turma
    carregarDisciplinasTurma(turmaId);
    
    // Carregar alunos da turma
    carregarAlunosTurma(turmaId);
    
    // Mostrar seção de disciplinas
    document.getElementById('disciplinasSection').style.display = 'block';
    document.getElementById('turma-nome').textContent = `Disciplinas - ${turmaCard.querySelector('h6').textContent}`;
    
    // Limpar áreas de tópicos e flashcards
    limparTopicos();
    limparFlashcards();
}

function carregarTurmas() {
    fetch('/api/professor/turmas')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderizarTurmas(data.turmas);
            } else {
                mostrarErro('Erro ao carregar turmas');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            mostrarErro('Erro ao carregar turmas');
        });
}

function renderizarTurmas(turmas) {
    const container = document.getElementById('turmasList');
    
    if (turmas.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <p>Nenhuma turma atribuída</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = turmas.map((turma, index) => `
        <div class="turma-card ${index === 0 ? 'active' : ''}" 
             data-id="${turma.id_turma}"
             onclick="selecionarTurma(${turma.id_turma})">
            <div class="turma-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="turma-info">
                <h6>${turma.nome}</h6>
                <span class="alunos-count">${turma.alunos ? turma.alunos.length : 0} alunos</span>
                <span class="disciplinas-count">${turma.disciplinas ? turma.disciplinas.length : 0} disciplinas</span>
            </div>
        </div>
    `).join('');
}

// ===== FUNÇÕES DE DISCIPLINAS =====

function carregarDisciplinasTurma(turmaId) {
    fetch(`/api/professor/turmas/${turmaId}/disciplinas`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderizarDisciplinas(data.disciplinas);
            } else {
                mostrarErro('Erro ao carregar disciplinas');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            mostrarErro('Erro ao carregar disciplinas');
        });
}

function renderizarDisciplinas(disciplinas) {
    const container = document.getElementById('disciplinasList');
    
    if (disciplinas.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-book"></i>
                <p>Nenhuma disciplina criada</p>
                <button class="btn btn-primary btn-sm" onclick="abrirModalDisciplina()">
                    Criar primeira disciplina
                </button>
            </div>
        `;
        return;
    }
    
    container.innerHTML = disciplinas.map((disciplina, index) => `
        <div class="disciplina-card ${index === 0 ? 'active' : ''}" 
             data-id="${disciplina.id_disciplina_professor}"
             onclick="selecionarDisciplina(${disciplina.id_disciplina_professor})">
            <div class="disciplina-icon">
                <i class="fas fa-book"></i>
            </div>
            <div class="disciplina-info">
                <h6>${disciplina.nome}</h6>
                <span class="topicos-count">${disciplina.topicos ? disciplina.topicos.length : 0} tópicos</span>
            </div>
        </div>
    `).join('');
    
    // Se há disciplinas, selecionar a primeira
    if (disciplinas.length > 0) {
        selecionarDisciplina(disciplinas[0].id_disciplina_professor);
    }
}

function selecionarDisciplina(disciplinaId) {
    disciplinaSelecionada = disciplinaId;
    topicoSelecionado = null;
    
    // Atualizar visual das disciplinas
    document.querySelectorAll('.disciplina-card').forEach(card => {
        card.classList.remove('active');
    });
    
    const disciplinaCard = document.querySelector(`[data-id="${disciplinaId}"]`);
    if (disciplinaCard) {
        disciplinaCard.classList.add('active');
        
        // Atualizar nome da disciplina no header
        const nomeDisciplina = disciplinaCard.querySelector('h6').textContent;
        document.getElementById('disciplina-nome').textContent = nomeDisciplina;
        
        // Mostrar botão de adicionar tópico
        document.getElementById('btn-add-topico').style.display = 'flex';
    }
    
    // Carregar tópicos da disciplina
    carregarTopicos(disciplinaId);
    
    // Limpar área de flashcards
    limparFlashcards();
}

function abrirModalDisciplina() {
    if (!turmaSelecionada) {
        mostrarErro('Selecione uma turma primeiro');
        return;
    }
    
    document.getElementById('turma_id').value = turmaSelecionada;
    const modal = new bootstrap.Modal(document.getElementById('disciplinaModal'));
    modal.show();
}

function criarDisciplina() {
    const form = document.getElementById('disciplinaForm');
    const formData = new FormData(form);
    
    const data = {
        nome: formData.get('nome'),
        descricao: formData.get('descricao'),
        turma_id: formData.get('turma_id')
    };
    
    fetch('/api/professor/disciplinas', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarSucesso(data.message);
            bootstrap.Modal.getInstance(document.getElementById('disciplinaModal')).hide();
            form.reset();
            carregarDisciplinasTurma(turmaSelecionada);
        } else {
            mostrarErro(data.message || 'Erro ao criar disciplina');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        mostrarErro('Erro ao criar disciplina');
    });
}

// ===== FUNÇÕES DE TÓPICOS =====

function carregarTopicos(disciplinaId) {
    fetch(`/api/professor/disciplinas/${disciplinaId}/topicos`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderizarTopicos(data.topicos);
            } else {
                mostrarErro('Erro ao carregar tópicos');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            mostrarErro('Erro ao carregar tópicos');
        });
}

function renderizarTopicos(topicos) {
    const container = document.getElementById('topicosList');
    
    if (topicos.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-bookmark"></i>
                <p>Nenhum tópico criado</p>
                <button class="btn btn-primary btn-sm" onclick="abrirModalTopico()">
                    Criar primeiro tópico
                </button>
            </div>
        `;
        return;
    }
    
    container.innerHTML = topicos.map((topico, index) => `
        <div class="topico-card ${index === 0 ? 'active' : ''}" 
             data-id="${topico.id_topico_professor}"
             onclick="selecionarTopico(${topico.id_topico_professor})">
            <div class="topico-icon">
                <i class="fas fa-bookmark"></i>
            </div>
            <div class="topico-info">
                <h6>${topico.nome}</h6>
                <span class="flashcards-count">${topico.flashcards ? topico.flashcards.length : 0} flashcards</span>
            </div>
        </div>
    `).join('');
    
    // Se há tópicos, selecionar o primeiro
    if (topicos.length > 0) {
        selecionarTopico(topicos[0].id_topico_professor);
    }
}

function selecionarTopico(topicoId) {
    topicoSelecionado = topicoId;
    
    // Atualizar visual dos tópicos
    document.querySelectorAll('.topico-card').forEach(card => {
        card.classList.remove('active');
    });
    
    const topicoCard = document.querySelector(`[data-id="${topicoId}"]`);
    if (topicoCard) {
        topicoCard.classList.add('active');
        
        // Atualizar nome do tópico no header
        const nomeTopico = topicoCard.querySelector('h6').textContent;
        document.getElementById('topico-nome').textContent = nomeTopico;
        
        // Mostrar botões de ação
        document.getElementById('btn-add-flashcard').style.display = 'flex';
        document.getElementById('btn-assign').style.display = 'flex';
    }
    
    // Carregar flashcards do tópico
    carregarFlashcards(topicoId);
    
    // Mostrar seção de alunos
    document.getElementById('alunosSection').style.display = 'block';
}

function limparTopicos() {
    const container = document.getElementById('topicosList');
    container.innerHTML = `
        <div class="empty-state">
            <i class="fas fa-bookmark"></i>
            <p>Selecione uma turma e disciplina para ver os tópicos</p>
        </div>
    `;
    
    document.getElementById('disciplina-nome').textContent = 'Selecione uma disciplina';
    document.getElementById('btn-add-topico').style.display = 'none';
}

function abrirModalTopico() {
    if (!disciplinaSelecionada) {
        mostrarErro('Selecione uma disciplina primeiro');
        return;
    }
    
    document.getElementById('disciplina_professor_id').value = disciplinaSelecionada;
    const modal = new bootstrap.Modal(document.getElementById('topicoModal'));
    modal.show();
}

function criarTopico() {
    const form = document.getElementById('topicoForm');
    const formData = new FormData(form);
    
    const data = {
        nome: formData.get('nome'),
        descricao: formData.get('descricao'),
        disciplina_professor_id: formData.get('disciplina_professor_id')
    };
    
    fetch('/api/professor/topicos', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarSucesso(data.message);
            bootstrap.Modal.getInstance(document.getElementById('topicoModal')).hide();
            form.reset();
            carregarTopicos(disciplinaSelecionada);
        } else {
            mostrarErro(data.message || 'Erro ao criar tópico');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        mostrarErro('Erro ao criar tópico');
    });
}

// ===== FUNÇÕES DE FLASHCARDS =====

function carregarFlashcards(topicoId) {
    fetch(`/api/professor/topicos/${topicoId}/flashcards`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderizarFlashcards(data.flashcards);
            } else {
                mostrarErro('Erro ao carregar flashcards');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            mostrarErro('Erro ao carregar flashcards');
        });
}

function renderizarFlashcards(flashcards) {
    const container = document.getElementById('flashcardsList');
    
    if (flashcards.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-layer-group"></i>
                <p>Nenhum flashcard criado</p>
                <button class="btn btn-primary btn-sm" onclick="abrirModalFlashcard()">
                    Criar primeiro flashcard
                </button>
            </div>
        `;
        return;
    }
    
    container.innerHTML = flashcards.map(flashcard => `
        <div class="flashcard-card" data-id="${flashcard.id_flashcard_professor}">
            <div class="flashcard-header">
                <h6 class="flashcard-title">${flashcard.titulo}</h6>
                <span class="perguntas-count">${flashcard.perguntas ? flashcard.perguntas.length : 0}</span>
            </div>
            ${flashcard.descricao ? `<p class="flashcard-description">${flashcard.descricao}</p>` : ''}
        </div>
    `).join('');
}

function limparFlashcards() {
    const container = document.getElementById('flashcardsList');
    container.innerHTML = `
        <div class="empty-state">
            <i class="fas fa-layer-group"></i>
            <p>Selecione um tópico para ver os flashcards</p>
        </div>
    `;
    
    document.getElementById('topico-nome').textContent = 'Selecione um tópico';
    document.getElementById('btn-add-flashcard').style.display = 'none';
    document.getElementById('btn-assign').style.display = 'none';
    document.getElementById('alunosSection').style.display = 'none';
}

function abrirModalFlashcard() {
    if (!topicoSelecionado) {
        mostrarErro('Selecione um tópico primeiro');
        return;
    }
    
    document.getElementById('topico_professor_id').value = topicoSelecionado;
    const modal = new bootstrap.Modal(document.getElementById('flashcardModal'));
    modal.show();
}

function criarFlashcard() {
    const form = document.getElementById('flashcardForm');
    const formData = new FormData(form);
    
    const data = {
        titulo: formData.get('titulo'),
        descricao: formData.get('descricao'),
        topico_professor_id: formData.get('topico_professor_id')
    };
    
    fetch('/api/professor/flashcards', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarSucesso(data.message);
            bootstrap.Modal.getInstance(document.getElementById('flashcardModal')).hide();
            form.reset();
            carregarFlashcards(topicoSelecionado);
        } else {
            mostrarErro(data.message || 'Erro ao criar flashcard');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        mostrarErro('Erro ao criar flashcard');
    });
}

// ===== FUNÇÕES DE ALUNOS =====

function carregarAlunosTurma(turmaId) {
    fetch(`/api/professor/turmas/${turmaId}/alunos`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alunosTurma = data.alunos;
                renderizarAlunos(data.alunos);
            } else {
                mostrarErro('Erro ao carregar alunos');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            mostrarErro('Erro ao carregar alunos');
        });
}

function renderizarAlunos(alunos) {
    const container = document.getElementById('alunosList');
    
    if (alunos.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-user-graduate"></i>
                <p>Nenhum aluno na turma</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = alunos.map(aluno => `
        <div class="aluno-card" data-id="${aluno.id_aluno}">
            <div class="aluno-avatar">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="aluno-info">
                <h6 class="aluno-name">${aluno.nome}</h6>
                <p class="aluno-email">${aluno.email}</p>
            </div>
        </div>
    `).join('');
}

// ===== FUNÇÕES DE ATRIBUIÇÃO =====

function atribuirFlashcards() {
    if (!topicoSelecionado) {
        mostrarErro('Selecione um tópico primeiro');
        return;
    }
    
    if (alunosTurma.length === 0) {
        mostrarErro('Nenhum aluno encontrado na turma');
        return;
    }
    
    // Renderizar checkboxes dos alunos
    const container = document.getElementById('alunosCheckboxList');
    container.innerHTML = alunosTurma.map(aluno => `
        <div class="aluno-checkbox">
            <input type="checkbox" id="aluno_${aluno.id_aluno}" value="${aluno.id_aluno}">
            <label for="aluno_${aluno.id_aluno}">${aluno.nome}</label>
        </div>
    `).join('');
    
    // Mostrar informações dos flashcards
    const flashcardsInfo = document.getElementById('flashcardsInfo');
    const flashcards = document.querySelectorAll('.flashcard-card');
    flashcardsInfo.innerHTML = `
        <p><strong>Tópico:</strong> ${document.getElementById('topico-nome').textContent}</p>
        <p><strong>Flashcards:</strong> ${flashcards.length} flashcard(s) serão atribuídos</p>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('atribuirModal'));
    modal.show();
}

function confirmarAtribuicao() {
    const checkboxes = document.querySelectorAll('#alunosCheckboxList input[type="checkbox"]:checked');
    const alunosIds = Array.from(checkboxes).map(cb => parseInt(cb.value));
    
    if (alunosIds.length === 0) {
        mostrarErro('Selecione pelo menos um aluno');
        return;
    }
    
    const data = {
        topico_professor_id: topicoSelecionado,
        alunos_ids: alunosIds
    };
    
    fetch('/api/professor/atribuir-flashcards', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarSucesso(data.message);
            bootstrap.Modal.getInstance(document.getElementById('atribuirModal')).hide();
        } else {
            mostrarErro(data.message || 'Erro ao atribuir flashcards');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        mostrarErro('Erro ao atribuir flashcards');
    });
}

// ===== FUNÇÕES AUXILIARES =====

function mostrarSucesso(mensagem) {
    console.log('Sucesso:', mensagem);
    alert(mensagem); // Temporário
}

function mostrarErro(mensagem) {
    console.error('Erro:', mensagem);
    alert(mensagem); // Temporário
}
