// JAVASCRIPT MELHORADO - SKULLCAVE DASHBOARD COM POPUP DE CONFIRMAÇÃO
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard carregado!');
    
    // INICIALIZA O FORMULÁRIO DE DISCIPLINA
    const form = document.getElementById('disciplinaForm');
    if (form) {
        form.addEventListener('submit', criarDisciplina);
    }
});

// FUNÇÃO PARA MOSTRAR POPUP DE SUCESSO
function mostrarPopupSucesso(mensagem) {
    // CRIA O POPUP SE NÃO EXISTIR
    let popup = document.getElementById('successPopup');
    if (!popup) {
        popup = document.createElement('div');
        popup.id = 'successPopup';
        popup.className = 'success-popup';
        popup.innerHTML = `
            <div class="popup-content">
                <i class="fas fa-check-circle"></i>
                <span class="popup-message"></span>
            </div>
        `;
        document.body.appendChild(popup);
    }
    
    // DEFINE A MENSAGEM E MOSTRA O POPUP
    const messageElement = popup.querySelector('.popup-message');
    messageElement.textContent = mensagem;
    popup.classList.add('show');
    
    // REMOVE O POPUP APÓS 3 SEGUNDOS
    setTimeout(() => {
        popup.classList.remove('show');
    }, 3000);
}

// FUNÇÃO PARA MOSTRAR POPUP DE ERRO
function mostrarPopupErro(mensagem) {
    // CRIA O POPUP SE NÃO EXISTIR
    let popup = document.getElementById('errorPopup');
    if (!popup) {
        popup = document.createElement('div');
        popup.id = 'errorPopup';
        popup.className = 'error-popup';
        popup.innerHTML = `
            <div class="popup-content">
                <i class="fas fa-exclamation-circle"></i>
                <span class="popup-message"></span>
            </div>
        `;
        document.body.appendChild(popup);
    }
    
    // DEFINE A MENSAGEM E MOSTRA O POPUP
    const messageElement = popup.querySelector('.popup-message');
    messageElement.textContent = mensagem;
    popup.classList.add('show');
    
    // REMOVE O POPUP APÓS 4 SEGUNDOS
    setTimeout(() => {
        popup.classList.remove('show');
    }, 4000);
}

// FUNÇÃO PARA MOSTRAR POPUP DE CONFIRMAÇÃO
function mostrarPopupConfirmacao(titulo, mensagem, callback) {
    // CRIA O POPUP SE NÃO EXISTIR
    let popup = document.getElementById('confirmPopup');
    if (!popup) {
        popup = document.createElement('div');
        popup.id = 'confirmPopup';
        popup.className = 'confirm-popup';
        popup.innerHTML = `
            <div class="confirm-overlay"></div>
            <div class="confirm-content">
                <div class="confirm-header">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h3 class="confirm-title"></h3>
                </div>
                <div class="confirm-body">
                    <p class="confirm-message"></p>
                </div>
                <div class="confirm-actions">
                    <button class="btn-cancel" onclick="fecharPopupConfirmacao()">Cancelar</button>
                    <button class="btn-confirm">Confirmar</button>
                </div>
            </div>
        `;
        document.body.appendChild(popup);
    }
    
    // DEFINE O TÍTULO E MENSAGEM
    const titleElement = popup.querySelector('.confirm-title');
    const messageElement = popup.querySelector('.confirm-message');
    const confirmBtn = popup.querySelector('.btn-confirm');
    
    titleElement.textContent = titulo;
    messageElement.textContent = mensagem;
    
    // REMOVE LISTENERS ANTERIORES E ADICIONA O NOVO
    const newConfirmBtn = confirmBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
    
    newConfirmBtn.addEventListener('click', function() {
        fecharPopupConfirmacao();
        if (callback) callback();
    });
    
    // MOSTRA O POPUP
    popup.classList.add('show');
}

// FUNÇÃO PARA FECHAR POPUP DE CONFIRMAÇÃO
function fecharPopupConfirmacao() {
    const popup = document.getElementById('confirmPopup');
    if (popup) {
        popup.classList.remove('show');
    }
}

// FUNÇÃO PARA ABRIR MODAL
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('show');
    }
}

// FUNÇÃO PARA FECHAR MODAL
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
    }
}

// FUNÇÃO PARA TOGGLE DE DISCIPLINA (EXPANDIR/RECOLHER TÓPICOS)
function toggleDisciplina(disciplinaId) {
    const topicosContainer = document.getElementById('topicos-' + disciplinaId);
    const toggleBtn = document.querySelector(`[onclick="toggleDisciplina(${disciplinaId})"]`);
    
    if (topicosContainer && toggleBtn) {
        // ALTERNA A VISIBILIDADE DOS TÓPICOS
        topicosContainer.classList.toggle('show');
        
        // ALTERNA A CLASSE DO BOTÃO PARA ROTACIONAR O ÍCONE
        toggleBtn.classList.toggle('active');
        
        console.log('Disciplina toggled:', disciplinaId);
    }
}

// FUNÇÃO PARA ABRIR MODAL DE TÓPICO
function openTopicoModal(disciplinaId) {
    // DEFINE O ID DA DISCIPLINA NO CAMPO HIDDEN
    const disciplinaIdInput = document.getElementById('disciplina_id');
    if (disciplinaIdInput) {
        disciplinaIdInput.value = disciplinaId;
    }
    
    // ABRE O MODAL
    openModal('topicoModal');
}

// FUNÇÃO PARA CRIAR DISCIPLINA
async function criarDisciplina(e) {
    e.preventDefault();
    
    console.log('=== INICIANDO CRIAÇÃO DE DISCIPLINA ===');
    
    const form = e.target;
    const nome = form.querySelector('#nome').value;
    const descricao = form.querySelector('#descricao').value;
    
    console.log('Nome:', nome);
    console.log('Descrição:', descricao);
    
    // PEGA O TOKEN CSRF
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    console.log('CSRF Token:', csrfToken);
    
    try {
        console.log('Fazendo requisição...');
        
        const response = await fetch('./api/disciplinas', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                nome: nome,
                descricao: descricao
            })
        });
        
        console.log('Response status:', response.status);
        
        const responseText = await response.text();
        console.log('Response text:', responseText);
        
        let data;
        try {
            data = JSON.parse(responseText);
            console.log('Response JSON:', data);
        } catch (parseError) {
            console.error('Erro ao fazer parse do JSON:', parseError);
            mostrarPopupErro('Erro: Resposta não é JSON válida. Verifique o console.');
            return;
        }
        
        if (data.success) {
            // MOSTRA POPUP DE SUCESSO EM VEZ DE ALERT
            mostrarPopupSucesso('Disciplina criada com sucesso!');
            closeModal('disciplinaModal');
            // LIMPA O FORMULÁRIO
            form.reset();
            // RECARREGA A PÁGINA PARA MOSTRAR A NOVA DISCIPLINA
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            // MOSTRA POPUP DE ERRO EM VEZ DE ALERT
            mostrarPopupErro('Erro: ' + data.message);
        }
        
    } catch (error) {
        console.error('Erro na requisição:', error);
        // MOSTRA POPUP DE ERRO EM VEZ DE ALERT
        mostrarPopupErro('Erro de conexão: ' + error.message);
    }
}

// FUNÇÃO PARA REMOVER DISCIPLINA COM POPUP DE CONFIRMAÇÃO
function removerDisciplina(disciplinaId) {
    // MOSTRA POPUP DE CONFIRMAÇÃO EM VEZ DE CONFIRM NATIVO
    mostrarPopupConfirmacao(
        'Remover Disciplina',
        'Tem certeza que deseja remover esta disciplina? Esta ação não pode ser desfeita e todos os tópicos e flashcards serão perdidos.',
        function() {
            // CALLBACK EXECUTADO QUANDO O USUÁRIO CONFIRMA
            executarRemocaoDisciplina(disciplinaId);
        }
    );
}

// FUNÇÃO QUE EXECUTA A REMOÇÃO DA DISCIPLINA
async function executarRemocaoDisciplina(disciplinaId) {
    console.log('=== INICIANDO REMOÇÃO DE DISCIPLINA ===');
    console.log('ID da disciplina:', disciplinaId);
    
    // PEGA O TOKEN CSRF
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    try {
        console.log('Fazendo requisição de remoção...');
        
        const response = await fetch(`./api/disciplinas/${disciplinaId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        console.log('Response status:', response.status);
        
        const responseText = await response.text();
        console.log('Response text:', responseText);
        
        let data;
        try {
            data = JSON.parse(responseText);
            console.log('Response JSON:', data);
        } catch (parseError) {
            console.error('Erro ao fazer parse do JSON:', parseError);
            mostrarPopupErro('Erro: Resposta não é JSON válida. Verifique o console.');
            return;
        }
        
        if (data.success) {
            // MOSTRA POPUP DE SUCESSO
            mostrarPopupSucesso('Disciplina removida com sucesso!');
            // RECARREGA A PÁGINA PARA ATUALIZAR A LISTA
            setTimeout(() => {
                location.reload();
            }, 100);
        } else {
            // MOSTRA POPUP DE ERRO
            mostrarPopupErro('Erro: ' + data.message);
        }
        
    } catch (error) {
        console.error('Erro na requisição:', error);
        // MOSTRA POPUP DE ERRO
        mostrarPopupErro('Erro de conexão: ' + error.message);
    }
}

// FUNÇÃO PARA FECHAR MODAL CLICANDO FORA DELE
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
        e.target.classList.remove('show');
    }
    
    // FECHA POPUP DE CONFIRMAÇÃO CLICANDO NO OVERLAY
    if (e.target.classList.contains('confirm-overlay')) {
        fecharPopupConfirmacao();
    }
});

// FUNÇÃO PARA FECHAR MODAL COM A TECLA ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modals = document.querySelectorAll('.modal.show');
        modals.forEach(modal => {
            modal.classList.remove('show');
        });
        
        // FECHA POPUP DE CONFIRMAÇÃO COM ESC
        fecharPopupConfirmacao();
    }
});
