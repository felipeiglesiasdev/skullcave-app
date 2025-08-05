// JavaScript específico para Dashboard Independente

let disciplinaSelecionada = null;
let topicoSelecionado = null;

document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard Independente carregado!');
    
    // Carregar disciplinas ao iniciar
    carregarDisciplinas();
    
    // Se há disciplinas, selecionar a primeira após carregamento
    setTimeout(() => {
        const primeiraDisciplina = document.querySelector('.disciplina-card.active');
        if (primeiraDisciplina) {
            const disciplinaId = primeiraDisciplina.dataset.id;
            selecionarDisciplina(disciplinaId);
        }
    }, 1000);
});

// ===== FUNÇÕES DE DISCIPLINAS =====

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
        const disciplinaNomeElement = document.getElementById('disciplina-nome');
        if (disciplinaNomeElement) {
            disciplinaNomeElement.textContent = nomeDisciplina;
        }
        
        // Mostrar botão de adicionar tópico
        const btnAddTopico = document.getElementById('btn-add-topico');
        if (btnAddTopico) {
            btnAddTopico.style.display = 'flex';
        }
    }
    
    // Carregar tópicos da disciplina
    carregarTopicos(disciplinaId);
    
    // Limpar área de flashcards
    limparFlashcards();
}

function carregarDisciplinas() {
    fetch(`./api/independente/disciplinas`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            renderizarDisciplinas(data.data || data.disciplinas || []);
        } else {
            mostrarErro(data.message || 'Erro ao carregar disciplinas');
        }
    })
    .catch(error => {
        console.error('Erro ao carregar disciplinas:', error);
        mostrarErro('Erro ao carregar disciplinas: ' + error.message);
    });
}

function renderizarDisciplinas(disciplinas) {
    const container = document.getElementById('disciplinasList');
    if (!container) {
        console.error('Container disciplinasList não encontrado');
        return;
    }
    
    if (!disciplinas || disciplinas.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-book-open"></i>
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
             data-id="${disciplina.id_disciplina}"
             onclick="selecionarDisciplina(${disciplina.id_disciplina})">
            <div class="disciplina-icon">
                <i class="fas fa-book"></i>
            </div>
            <div class="disciplina-info">
                <h6>${disciplina.nome}</h6>
                <span class="topicos-count">${disciplina.topicos ? disciplina.topicos.length : 0} tópicos</span>
            </div>
            <div class="disciplina-actions">
                <button class="btn-action" onclick="event.stopPropagation(); editarDisciplina(${disciplina.id_disciplina})" title="Editar">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn-action btn-delete" onclick="event.stopPropagation(); removerDisciplina(${disciplina.id_disciplina})" title="Excluir">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `).join('');
}

function abrirModalDisciplina() {
    const modalElement = document.getElementById('disciplinaModal');
    if (modalElement) {
        // Limpar formulário
        const form = document.getElementById('disciplinaForm');
        if (form) {
            form.reset();
        }
        
        // Abrir modal usando Bootstrap 5
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    } else {
        console.error('Modal disciplinaModal não encontrado');
    }
}

function criarDisciplina() {
    const form = document.getElementById('disciplinaForm');
    if (!form) {
        mostrarErro('Formulário não encontrado');
        return;
    }
    
    const formData = new FormData(form);
    
    const data = {
        nome: formData.get('nome'),
        descricao: formData.get('descricao') || ''
    };
    
    // Validação básica
    if (!data.nome || data.nome.trim().length < 3) {
        mostrarErro('Nome da disciplina deve ter pelo menos 3 caracteres');
        return;
    }
    
    fetch(`./api/independente/disciplinas`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            mostrarSucesso(data.message || 'Disciplina criada com sucesso!');
            
            // Fechar modal
            const modalElement = document.getElementById('disciplinaModal');
            if (modalElement) {
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
            }
            
            // Limpar formulário
            form.reset();
            
            // Recarregar disciplinas
            carregarDisciplinas();
        } else {
            mostrarErro(data.message || 'Erro ao criar disciplina');
        }
    })
    .catch(error => {
        console.error('Erro ao criar disciplina:', error);
        mostrarErro('Erro ao criar disciplina: ' + error.message);
    });
}

// ===== FUNÇÕES DE TÓPICOS =====

function carregarTopicos(disciplinaId) {
    if (!disciplinaId) {
        console.error('ID da disciplina não fornecido');
        return;
    }
    
    fetch(`./api/independente/disciplinas/${disciplinaId}/topicos`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            renderizarTopicos(data.data || data.topicos || []);
        } else {
            mostrarErro(data.message || 'Erro ao carregar tópicos');
        }
    })
    .catch(error => {
        console.error('Erro ao carregar tópicos:', error);
        mostrarErro('Erro ao carregar tópicos: ' + error.message);
    });
}

function renderizarTopicos(topicos) {
    const container = document.getElementById('topicosList');
    if (!container) {
        console.error('Container topicosList não encontrado');
        return;
    }
    
    if (!topicos || topicos.length === 0) {
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
             data-id="${topico.id_topico}"
             onclick="selecionarTopico(${topico.id_topico})">
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
        selecionarTopico(topicos[0].id_topico);
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
        const topicoNomeElement = document.getElementById('topico-nome');
        if (topicoNomeElement) {
            topicoNomeElement.textContent = nomeTopico;
        }
        
        // Mostrar botão de adicionar flashcard
        const btnAddFlashcard = document.getElementById('btn-add-flashcard');
        if (btnAddFlashcard) {
            btnAddFlashcard.style.display = 'flex';
        }
    }
    
    // Carregar flashcards do tópico
    carregarFlashcards(topicoId);
}

function abrirModalTopico() {
    if (!disciplinaSelecionada) {
        mostrarErro('Selecione uma disciplina primeiro');
        return;
    }
    
    const modalElement = document.getElementById('topicoModal');
    if (modalElement) {
        // Limpar formulário
        const form = document.getElementById('topicoForm');
        if (form) {
            form.reset();
        }
        
        // Definir disciplina_id no formulário
        const disciplinaIdInput = document.getElementById('disciplina_id');
        if (disciplinaIdInput) {
            disciplinaIdInput.value = disciplinaSelecionada;
        }
        
        // Abrir modal usando Bootstrap 5
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    } else {
        console.error('Modal topicoModal não encontrado');
    }
}

function criarTopico() {
    const form = document.getElementById('topicoForm');
    if (!form) {
        mostrarErro('Formulário não encontrado');
        return;
    }
    
    const formData = new FormData(form);
    
    const data = {
        nome: formData.get('nome'),
        descricao: formData.get('descricao') || '',
        disciplina_id: formData.get('disciplina_id') || disciplinaSelecionada
    };
    
    // Validação básica
    if (!data.nome || data.nome.trim().length < 3) {
        mostrarErro('Nome do tópico deve ter pelo menos 3 caracteres');
        return;
    }
    
    if (!data.disciplina_id) {
        mostrarErro('Disciplina não selecionada');
        return;
    }
    
    fetch(`./api/independente/topicos`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            mostrarSucesso(data.message || 'Tópico criado com sucesso!');
            
            // Fechar modal
            const modalElement = document.getElementById('topicoModal');
            if (modalElement) {
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
            }
            
            // Limpar formulário
            form.reset();
            
            // Recarregar tópicos
            carregarTopicos(disciplinaSelecionada);
        } else {
            mostrarErro(data.message || 'Erro ao criar tópico');
        }
    })
    .catch(error => {
        console.error('Erro ao criar tópico:', error);
        mostrarErro('Erro ao criar tópico: ' + error.message);
    });
}

// ===== FUNÇÕES DE FLASHCARDS =====

function carregarFlashcards(topicoId) {
    if (!topicoId) {
        console.error('ID do tópico não fornecido');
        return;
    }
        fetch(`./api/independente/topicos/${topicoId}/flashcards`, {  method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            renderizarFlashcards(data.data || data.flashcards || []);
        } else {
            mostrarErro(data.message || 'Erro ao carregar flashcards');
        }
    })
    .catch(error => {
        console.error('Erro ao carregar flashcards:', error);
        mostrarErro('Erro ao carregar flashcards: ' + error.message);
    });
}

function renderizarFlashcards(flashcards) {
    const container = document.getElementById('flashcardsList');
    if (!container) {
        console.error('Container flashcardsList não encontrado');
        return;
    }
    
    if (!flashcards || flashcards.length === 0) {
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
        <div class="flashcard-card" data-id="${flashcard.id_flashcard}">
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
    if (!container) return;
    
    container.innerHTML = `
        <div class="empty-state">
            <i class="fas fa-layer-group"></i>
            <p>Selecione um tópico para ver os flashcards</p>
        </div>
    `;
    
    const topicoNomeElement = document.getElementById('topico-nome');
    if (topicoNomeElement) {
        topicoNomeElement.textContent = 'Selecione um tópico';
    }
    
    const btnAddFlashcard = document.getElementById('btn-add-flashcard');
    if (btnAddFlashcard) {
        btnAddFlashcard.style.display = 'none';
    }
}

function abrirModalFlashcard() {
    if (!topicoSelecionado) {
        mostrarErro('Selecione um tópico primeiro');
        return;
    }
    
    const modalElement = document.getElementById('flashcardModal');
    if (modalElement) {
        // Limpar formulário
        const form = document.getElementById('flashcardForm');
        if (form) {
            form.reset();
        }
        
        // Definir topico_id no formulário
        const topicoIdInput = document.getElementById('topico_id');
        if (topicoIdInput) {
            topicoIdInput.value = topicoSelecionado;
        }
        
        // Abrir modal usando Bootstrap 5
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    } else {
        console.error('Modal flashcardModal não encontrado');
    }
}

function criarFlashcard() {
    const form = document.getElementById('flashcardForm');
    if (!form) {
        mostrarErro('Formulário não encontrado');
        return;
    }
    
    const formData = new FormData(form);
    
    const data = {
        titulo: formData.get('titulo'),
        descricao: formData.get('descricao') || '',
        topico_id: formData.get('topico_id') || topicoSelecionado
    };
    
    // Validação básica
    if (!data.titulo || data.titulo.trim().length < 3) {
        mostrarErro('Título do flashcard deve ter pelo menos 3 caracteres');
        return;
    }
    
    if (!data.topico_id) {
        mostrarErro('Tópico não selecionado');
        return;
    }
    
    fetch(`${window.APP_BASE_URL}/api/independente/flashcards`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            mostrarSucesso(data.message || 'Flashcard criado com sucesso!');
            
            // Fechar modal
            const modalElement = document.getElementById('flashcardModal');
            if (modalElement) {
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
            }
            
            // Limpar formulário
            form.reset();
            
            // Recarregar flashcards
            carregarFlashcards(topicoSelecionado);
        } else {
            mostrarErro(data.message || 'Erro ao criar flashcard');
        }
    })
    .catch(error => {
        console.error('Erro ao criar flashcard:', error);
        mostrarErro('Erro ao criar flashcard: ' + error.message);
    });
}



function editarDisciplina(disciplinaId) {
    // Implementar edição de disciplina
    console.log('Editar disciplina:', disciplinaId);
    mostrarSucesso('Funcionalidade de edição será implementada em breve!');
}

function removerDisciplina(disciplinaId) {
    if (confirm('Tem certeza que deseja remover esta disciplina? Todos os tópicos e flashcards relacionados também serão excluídos.')) {
        // Implementar remoção de disciplina
        console.log('Remover disciplina:', disciplinaId);
        mostrarSucesso('Funcionalidade de remoção será implementada em breve!');
    }
}

// ===== FUNÇÕES AUXILIARES =====

function mostrarSucesso(mensagem) {
    console.log('Sucesso:', mensagem);
    const toast = document.createElement('div');
    toast.className = 'toast-message toast-success';
    toast.innerHTML = `
        <i class="fas fa-check-circle"></i>
        <span>${mensagem}</span>
    `;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.classList.add('show');
    }, 100);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}

function mostrarErro(mensagem) {
    console.error('Erro:', mensagem);
    const toast = document.createElement('div');
    toast.className = 'toast-message toast-error';
    toast.innerHTML = `
        <i class="fas fa-exclamation-circle"></i>
        <span>${mensagem}</span>
    `;
    document.body.appendChild(toast);
        setTimeout(() => {
        toast.classList.add('show');
    }, 200);
        setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 6000);
}