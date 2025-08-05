// JavaScript específico para Dashboard Aluno

let disciplinaSelecionada = null;
let tipoDisciplina = null; // 'minha' ou 'professor'
let topicoSelecionado = null;

document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard Aluno carregado!');
    
    // Carregar disciplinas ao iniciar
    carregarMinhasDisciplinas();
    carregarDisciplinasProfessor();
    
    // Se há disciplinas, selecionar a primeira
    setTimeout(() => {
        const primeiraDisciplina = document.querySelector('.disciplina-card.active');
        if (primeiraDisciplina) {
            const disciplinaId = primeiraDisciplina.dataset.id;
            const tipo = primeiraDisciplina.dataset.tipo;
            selecionarDisciplina(disciplinaId, tipo);
        }
    }, 500);
});

// ===== FUNÇÕES DE DISCIPLINAS =====

function selecionarDisciplina(disciplinaId, tipo) {
    disciplinaSelecionada = disciplinaId;
    tipoDisciplina = tipo;
    topicoSelecionado = null;
    
    // Atualizar visual das disciplinas
    document.querySelectorAll('.disciplina-card').forEach(card => {
        card.classList.remove('active');
    });
    
    const disciplinaCard = document.querySelector(`[data-id="${disciplinaId}"][data-tipo="${tipo}"]`);
    if (disciplinaCard) {
        disciplinaCard.classList.add('active');
        
        // Atualizar nome da disciplina no header
        const nomeDisciplina = disciplinaCard.querySelector('h6').textContent;
        document.getElementById('disciplina-nome').textContent = nomeDisciplina;
        
        // Atualizar badge do tipo
        const tipoBadge = document.getElementById('disciplina-tipo');
        if (tipo === 'minha') {
            tipoBadge.textContent = 'Minha';
            tipoBadge.className = 'tipo-badge';
            // Mostrar botão de adicionar tópico apenas para disciplinas próprias
            document.getElementById('btn-add-topico').style.display = 'flex';
        } else {
            tipoBadge.textContent = 'Professor';
            tipoBadge.className = 'tipo-badge professor';
            // Ocultar botão de adicionar tópico para disciplinas do professor
            document.getElementById('btn-add-topico').style.display = 'none';
        }
    }
    
    // Carregar tópicos da disciplina
    carregarTopicos(disciplinaId, tipo);
    
    // Limpar área de flashcards
    limparFlashcards();
}

function carregarMinhasDisciplinas() {
    fetch('/api/aluno/minhas-disciplinas')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderizarMinhasDisciplinas(data.disciplinas);
            } else {
                mostrarErro('Erro ao carregar suas disciplinas');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            mostrarErro('Erro ao carregar suas disciplinas');
        });
}

function carregarDisciplinasProfessor() {
    fetch('/api/aluno/disciplinas-professor')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderizarDisciplinasProfessor(data.disciplinas);
            } else {
                console.log('Nenhuma disciplina do professor encontrada');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
        });
}

function renderizarMinhasDisciplinas(disciplinas) {
    const container = document.getElementById('minhasDisciplinasList');
    
    if (disciplinas.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-book-open"></i>
                <p>Nenhuma disciplina criada</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = disciplinas.map((disciplina, index) => `
        <div class="disciplina-card minha-disciplina ${index === 0 ? 'active' : ''}" 
             data-id="${disciplina.id_disciplina}"
             data-tipo="minha"
             onclick="selecionarDisciplina(${disciplina.id_disciplina}, 'minha')">
            <div class="disciplina-icon">
                <i class="fas fa-book"></i>
            </div>
            <div class="disciplina-info">
                <h6>${disciplina.nome}</h6>
                <span class="topicos-count">${disciplina.topicos ? disciplina.topicos.length : 0} tópicos</span>
                <span class="disciplina-badge">Minha</span>
            </div>
            <div class="disciplina-actions">
                <button class="btn-action" onclick="editarDisciplina(${disciplina.id_disciplina})" title="Editar">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn-action btn-delete" onclick="removerDisciplina(${disciplina.id_disciplina})" title="Excluir">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `).join('');
}

function renderizarDisciplinasProfessor(disciplinas) {
    const container = document.getElementById('professorDisciplinasList');
    
    if (disciplinas.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-chalkboard-teacher"></i>
                <p>Nenhuma disciplina do professor</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = disciplinas.map(disciplina => `
        <div class="disciplina-card professor-disciplina" 
             data-id="${disciplina.id_disciplina_professor}"
             data-tipo="professor"
             onclick="selecionarDisciplina(${disciplina.id_disciplina_professor}, 'professor')">
            <div class="disciplina-icon professor-icon">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="disciplina-info">
                <h6>${disciplina.nome}</h6>
                <span class="topicos-count">${disciplina.topicos ? disciplina.topicos.length : 0} tópicos</span>
                <span class="disciplina-badge professor-badge">Professor</span>
            </div>
        </div>
    `).join('');
}

function abrirModalDisciplina() {
    const modal = new bootstrap.Modal(document.getElementById('disciplinaModal'));
    modal.show();
}

function criarDisciplina() {
    const form = document.getElementById('disciplinaForm');
    const formData = new FormData(form);
    
    const data = {
        nome: formData.get('nome'),
        descricao: formData.get('descricao')
    };
    
    fetch('/api/aluno/disciplinas', {
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
            carregarMinhasDisciplinas();
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

function carregarTopicos(disciplinaId, tipo) {
    fetch(`/api/aluno/disciplinas/${disciplinaId}/topicos/${tipo}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderizarTopicos(data.topicos, tipo);
            } else {
                mostrarErro('Erro ao carregar tópicos');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            mostrarErro('Erro ao carregar tópicos');
        });
}

function renderizarTopicos(topicos, tipo) {
    const container = document.getElementById('topicosList');
    
    if (topicos.length === 0) {
        const mensagem = tipo === 'minha' ? 
            'Nenhum tópico criado' : 
            'Nenhum tópico do professor';
            
        const botao = tipo === 'minha' ? 
            '<button class="btn btn-primary btn-sm" onclick="abrirModalTopico()">Criar primeiro tópico</button>' : 
            '';
            
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-bookmark"></i>
                <p>${mensagem}</p>
                ${botao}
            </div>
        `;
        return;
    }
    
    const classeTopico = tipo === 'professor' ? 'professor-topico' : '';
    
    container.innerHTML = topicos.map((topico, index) => `
        <div class="topico-card ${classeTopico} ${index === 0 ? 'active' : ''}" 
             data-id="${topico.id_topico || topico.id_topico_professor}"
             onclick="selecionarTopico(${topico.id_topico || topico.id_topico_professor})">
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
        selecionarTopico(topicos[0].id_topico || topicos[0].id_topico_professor);
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
        
        // Mostrar botões baseado no tipo de disciplina
        if (tipoDisciplina === 'minha') {
            document.getElementById('btn-add-flashcard').style.display = 'flex';
        } else {
            document.getElementById('btn-add-flashcard').style.display = 'none';
        }
        
        // Sempre mostrar botão de estudar
        document.getElementById('btn-study').style.display = 'flex';
    }
    
    // Carregar flashcards do tópico
    carregarFlashcards(topicoId, tipoDisciplina);
}

function abrirModalTopico() {
    if (!disciplinaSelecionada || tipoDisciplina !== 'minha') {
        mostrarErro('Você só pode criar tópicos em suas próprias disciplinas');
        return;
    }
    
    document.getElementById('disciplina_id').value = disciplinaSelecionada;
    const modal = new bootstrap.Modal(document.getElementById('topicoModal'));
    modal.show();
}

function criarTopico() {
    const form = document.getElementById('topicoForm');
    const formData = new FormData(form);
    
    const data = {
        nome: formData.get('nome'),
        descricao: formData.get('descricao'),
        disciplina_id: formData.get('disciplina_id')
    };
    
    fetch('/api/aluno/topicos', {
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
            carregarTopicos(disciplinaSelecionada, tipoDisciplina);
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

function carregarFlashcards(topicoId, tipo) {
    fetch(`/api/aluno/topicos/${topicoId}/flashcards/${tipo}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderizarFlashcards(data.flashcards, tipo);
            } else {
                mostrarErro('Erro ao carregar flashcards');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            mostrarErro('Erro ao carregar flashcards');
        });
}

function renderizarFlashcards(flashcards, tipo) {
    const container = document.getElementById('flashcardsList');
    
    if (flashcards.length === 0) {
        const mensagem = tipo === 'minha' ? 
            'Nenhum flashcard criado' : 
            'Nenhum flashcard do professor';
            
        const botao = tipo === 'minha' ? 
            '<button class="btn btn-primary btn-sm" onclick="abrirModalFlashcard()">Criar primeiro flashcard</button>' : 
            '';
            
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-layer-group"></i>
                <p>${mensagem}</p>
                ${botao}
            </div>
        `;
        return;
    }
    
    const classeFlashcard = tipo === 'professor' ? 'professor-flashcard' : '';
    
    container.innerHTML = flashcards.map(flashcard => `
        <div class="flashcard-card ${classeFlashcard}" data-id="${flashcard.id_flashcard || flashcard.id_flashcard_professor}">
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
    document.getElementById('btn-study').style.display = 'none';
}

function abrirModalFlashcard() {
    if (!topicoSelecionado || tipoDisciplina !== 'minha') {
        mostrarErro('Você só pode criar flashcards em seus próprios tópicos');
        return;
    }
    
    document.getElementById('topico_id').value = topicoSelecionado;
    const modal = new bootstrap.Modal(document.getElementById('flashcardModal'));
    modal.show();
}

function criarFlashcard() {
    const form = document.getElementById('flashcardForm');
    const formData = new FormData(form);
    
    const data = {
        titulo: formData.get('titulo'),
        descricao: formData.get('descricao'),
        topico_id: formData.get('topico_id')
    };
    
    fetch('/api/aluno/flashcards', {
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
            carregarFlashcards(topicoSelecionado, tipoDisciplina);
        } else {
            mostrarErro(data.message || 'Erro ao criar flashcard');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        mostrarErro('Erro ao criar flashcard');
    });
}

function iniciarEstudo() {
    if (!topicoSelecionado) {
        mostrarErro('Selecione um tópico para estudar');
        return;
    }
    
    // Implementar funcionalidade de estudo
    console.log('Iniciar estudo do tópico:', topicoSelecionado);
    mostrarSucesso('Funcionalidade de estudo será implementada em breve!');
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

function editarDisciplina(disciplinaId) {
    console.log('Editar disciplina:', disciplinaId);
}

function removerDisciplina(disciplinaId) {
    if (confirm('Tem certeza que deseja remover esta disciplina?')) {
        console.log('Remover disciplina:', disciplinaId);
    }
}