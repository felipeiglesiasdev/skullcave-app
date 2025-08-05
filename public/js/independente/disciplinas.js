// ===== FUNÇÕES DE DISCIPLINAS =====

// FUNÇÃO PARA SELECIONAR UMA DISCIPLINA
function selecionarDisciplina(disciplinaId) {
    // ATUALIZA A VARIÁVEL GLOBAL COM O ID DA DISCIPLINA SELECIONADA
    disciplinaSelecionada = disciplinaId;
    // LIMPA A SELEÇÃO DE TÓPICO
    topicoSelecionado = null;
    
    // REMOVE A CLASSE 'active' DE TODOS OS CARTÕES DE DISCIPLINA
    document.querySelectorAll(".disciplina-card").forEach(card => {
        card.classList.remove("active");
    });
    
    // SELECIONA O CARTÃO DA DISCIPLINA CORRESPONDENTE AO ID
    const disciplinaCard = document.querySelector(`[data-id="${disciplinaId}"]`);
    // VERIFICA SE O CARTÃO DA DISCIPLINA EXISTE
    if (disciplinaCard) {
        // ADICIONA A CLASSE 'active' AO CARTÃO DA DISCIPLINA SELECIONADA
        disciplinaCard.classList.add("active");
        
        // ATUALIZA O BREADCRUMB COM O NOME DA DISCIPLINA
        const nomeDisciplina = disciplinaCard.querySelector("h6").textContent;
        atualizarBreadcrumb(nomeDisciplina);
        
        // MOSTRA O BOTÃO DE ADICIONAR TÓPICO E ESCONDE O DE ADICIONAR FLASHCARD
        atualizarBotoesAcao(true, false);
    }
    
    // MOSTRA A VIEW DE TÓPICOS
    mostrarView("topicos-view");
    
    // CARREGA OS TÓPICOS DA DISCIPLINA SELECIONADA
    carregarTopicos(disciplinaId);
}

// FUNÇÃO PARA CARREGAR AS DISCIPLINAS DO SERVIDOR
function carregarDisciplinas() {
    // FAZ UMA REQUISIÇÃO FETCH PARA A API DE DISCIPLINAS
    fetch(`./api/independente/disciplinas`, {
        method: "GET", // MÉTODO HTTP GET
        headers: { // CABEÇALHOS DA REQUISIÇÃO
            "Content-Type": "application/json", // TIPO DE CONTEÚDO JSON
            "X-CSRF-TOKEN": document.querySelector("meta[name=\"csrf-token\"]")?.getAttribute("content") || "", // TOKEN CSRF PARA SEGURANÇA
            "Accept": "application/json" // ACEITA RESPOSTAS JSON
        }
    })
    .then(response => {
        // VERIFICA SE A RESPOSTA DA REQUISIÇÃO FOI BEM-SUCEDIDA
        if (!response.ok) {
            // SE NÃO FOI BEM-SUCEDIDA, LANÇA UM ERRO
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        // RETORNA A RESPOSTA COMO JSON
        return response.json();
    })
    .then(data => {
        // PROCESSA OS DADOS RECEBIDOS DO SERVIDOR
        if (data.success) {
            // SE A OPERAÇÃO FOI BEM-SUCEDIDA, RENDERIZA AS DISCIPLINAS
            renderizarDisciplinas(data.data || data.disciplinas || []);
        } else {
            // SE HOUVE ERRO, MOSTRA UMA MENSAGEM DE ERRO
            mostrarErro(data.message || "Erro ao carregar disciplinas");
        }
    })
    .catch(error => {
        // TRATA ERROS DE REDE OU OUTROS ERROS DURANTE A REQUISIÇÃO
        console.error("Erro ao carregar disciplinas:", error);
        // MOSTRA UMA MENSAGEM DE ERRO MAIS DETALHADA
        mostrarErro("Erro ao carregar disciplinas: " + error.message);
    });
}

// FUNÇÃO PARA RENDERIZAR (EXIBIR) AS DISCIPLINAS NA INTERFACE
function renderizarDisciplinas(disciplinas) {
    // SELECIONA O CONTÊINER ONDE AS DISCIPLINAS SERÃO EXIBIDAS
    const container = document.getElementById("disciplinasList");
    // VERIFICA SE O CONTÊINER FOI ENCONTRADO
    if (!container) {
        // REGISTRA UM ERRO NO CONSOLE SE O CONTÊINER NÃO FOR ENCONTRADO
        console.error("Container disciplinasList não encontrado");
        return; // ENCERRA A FUNÇÃO
    }
    
    // VERIFICA SE NÃO HÁ DISCIPLINAS OU SE A LISTA ESTÁ VAZIA
    if (!disciplinas || disciplinas.length === 0) {
        // DEFINE O CONTEÚDO HTML PARA EXIBIR UM ESTADO VAZIO (NENHUMA DISCIPLINA CRIADA)
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-book-open"></i>
                <p>Nenhuma disciplina criada</p>
                <button class="btn btn-primary btn-sm" onclick="abrirModalDisciplina()">
                    Criar primeira disciplina
                </button>
            </div>
        `;
        
        // MOSTRA O ESTADO DE BOAS-VINDAS SE NÃO HOUVER DISCIPLINAS
        mostrarView("welcome-state");
        // ESCONDE OS BOTÕES DE ADICIONAR TÓPICO E FLASHCARD
        atualizarBotoesAcao(false, false);
        return; // ENCERRA A FUNÇÃO
    }
    
    // SE HÁ DISCIPLINAS, ESCONDE O ESTADO DE BOAS-VINDAS E RENDERIZA NORMALMENTE
    // MOSTRA A VIEW DE TÓPICOS COMO PADRÃO APÓS CARREGAR DISCIPLINAS
    mostrarView("topicos-view");
    // GERA O HTML PARA CADA DISCIPLINA E INSERE NO CONTÊINER
    container.innerHTML = disciplinas.map((disciplina, index) => `
        <div class="disciplina-card ${index === 0 ? "active" : ""}" 
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
                <button class="btn-action btn-delete" onclick="event.stopPropagation(); removerDisciplina(${disciplina.id_disciplina})" title="Excluir">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `).join(""); // UNE OS ELEMENTOS DO ARRAY EM UMA ÚNICA STRING HTML
    
    // SE HOUVER DISCIPLINAS, SELECIONA A PRIMEIRA AUTOMATICAMENTE
    if (disciplinas.length > 0) {
        selecionarDisciplina(disciplinas[0].id_disciplina);
    }
}

// FUNÇÃO PARA ABRIR O MODAL DE CRIAÇÃO/EDIÇÃO DE DISCIPLINA
function abrirModalDisciplina() {
    // SELECIONA O ELEMENTO DO MODAL
    const modalElement = document.getElementById("disciplinaModal");
    // VERIFICA SE O MODAL FOI ENCONTRADO
    if (modalElement) {
        // SELECIONA O FORMULÁRIO DENTRO DO MODAL
        const form = document.getElementById("disciplinaForm");
        // VERIFICA SE O FORMULÁRIO EXISTE
        if (form) {
            // LIMPA OS CAMPOS DO FORMULÁRIO
            form.reset();
        }
        
        // CRIA UMA NOVA INSTÂNCIA DO MODAL DO BOOTSTRAP 5 E O MOSTRA
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    } else {
        // REGISTRA UM ERRO NO CONSOLE SE O MODAL NÃO FOR ENCONTRADO
        console.error("Modal disciplinaModal não encontrado");
    }
}

// FUNÇÃO PARA CRIAR UMA NOVA DISCIPLINA
function criarDisciplina() {
    // SELECIONA O FORMULÁRIO DE DISCIPLINA
    const form = document.getElementById("disciplinaForm");
    // VERIFICA SE O FORMULÁRIO FOI ENCONTRADO
    if (!form) {
        // MOSTRA UM ERRO SE O FORMULÁRIO NÃO FOR ENCONTRADO
        mostrarErro("Formulário não encontrado");
        return; // ENCERRA A FUNÇÃO
    }
    
    // CRIA UM OBJETO FormData A PARTIR DO FORMULÁRIO
    const formData = new FormData(form);
    
    // EXTRAI OS DADOS DO FORMULÁRIO PARA UM OBJETO JAVASCRIPT
    const data = {
        nome: formData.get("nome"), // OBTÉM O VALOR DO CAMPO 'nome'
        descricao: formData.get("descricao") || "" // OBTÉM O VALOR DO CAMPO 'descricao' OU UMA STRING VAZIA
    };
    
    // VALIDAÇÃO BÁSICA DO NOME DA DISCIPLINA
    if (!data.nome || data.nome.trim().length < 3) {
        // MOSTRA UM ERRO SE O NOME FOR INVÁLIDO
        mostrarErro("Nome da disciplina deve ter pelo menos 3 caracteres");
        return; // ENCERRA A FUNÇÃO
    }
    
    // FAZ UMA REQUISIÇÃO FETCH PARA A API DE CRIAÇÃO DE DISCIPLINAS
    fetch(`./api/independente/disciplinas`, {
        method: "POST", // MÉTODO HTTP POST
        headers: { // CABEÇALHOS DA REQUISIÇÃO
            "Content-Type": "application/json", // TIPO DE CONTEÚDO JSON
            "X-CSRF-TOKEN": document.querySelector("meta[name=\"csrf-token\"]")?.getAttribute("content") || "", // TOKEN CSRF
            "Accept": "application/json" // ACEITA RESPOSTAS JSON
        },
        body: JSON.stringify(data) // CORPO DA REQUISIÇÃO EM FORMATO JSON
    })
    .then(response => {
        // VERIFICA SE A RESPOSTA DA REQUISIÇÃO FOI BEM-SUCEDIDA
        if (!response.ok) {
            // SE NÃO FOI BEM-SUCEDIDA, LANÇA UM ERRO
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        // RETORNA A RESPOSTA COMO JSON
        return response.json();
    })
    .then(data => {
        // PROCESSA OS DADOS RECEBIDOS DO SERVIDOR
        if (data.success) {
            // MOSTRA UMA MENSAGEM DE SUCESSO
            mostrarSucesso(data.message || "Disciplina criada com sucesso!");
            
            // FECHA O MODAL APÓS A CRIAÇÃO BEM-SUCEDIDA
            const modalElement = document.getElementById("disciplinaModal");
            if (modalElement) {
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
            }
            
            // LIMPA O FORMULÁRIO
            form.reset();
            
            // RECARREGA A LISTA DE DISCIPLINAS PARA ATUALIZAR A INTERFACE
            carregarDisciplinas();
        } else {
            // MOSTRA UMA MENSAGEM DE ERRO
            mostrarErro(data.message || "Erro ao criar disciplina");
        }
    })
    .catch(error => {
        // TRATA ERROS DE REDE OU OUTROS ERROS DURANTE A REQUISIÇÃO
        console.error("Erro ao criar disciplina:", error);
        // MOSTRA UMA MENSAGEM DE ERRO MAIS DETALHADA
        mostrarErro("Erro ao criar disciplina: " + error.message);
    });
}

// ===== FUNÇÃO PARA EXCLUIR DISCIPLINA =====
function removerDisciplina(disciplinaId) {
    // CONFIRMAR AÇÃO DE EXCLUSÃO COM O USUÁRIO
    if (!confirm("Tem certeza que deseja excluir esta disciplina e todos os seus tópicos e flashcards associados?")) {
        return; // SE O USUÁRIO CANCELAR, ENCERRA A FUNÇÃO
    }

    // FAZER REQUISIÇÃO DELETE PARA EXCLUIR A DISCIPLINA
    fetch(`./api/independente/disciplinas/${disciplinaId}`, {
        method: "DELETE", // MÉTODO HTTP DELETE
        headers: { // CABEÇALHOS DA REQUISIÇÃO
            "Content-Type": "application/json", // TIPO DE CONTEÚDO JSON
            "X-CSRF-TOKEN": document.querySelector("meta[name=\"csrf-token\"]")?.getAttribute("content") || "", // TOKEN CSRF
            "Accept": "application/json" // ACEITA RESPOSTAS JSON
        }
    })
    .then(response => {
        // VERIFICA SE A RESPOSTA DA REQUISIÇÃO FOI BEM-SUCEDIDA
        if (!response.ok) {
            // SE NÃO FOI BEM-SUCEDIDA, LANÇA UM ERRO
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        // RETORNA A RESPOSTA COMO JSON
        return response.json();
    })
    .then(data => {
        // PROCESSA OS DADOS RECEBIDOS DO SERVIDOR
        if (data.success) {
            // MOSTRA UMA MENSAGEM DE SUCESSO
            mostrarSucesso(data.message || "Disciplina excluída com sucesso!");
            // RECARREGA A LISTA DE DISCIPLINAS PARA ATUALIZAR A INTERFACE
            carregarDisciplinas();
        } else {
            // MOSTRA UMA MENSAGEM DE ERRO
            mostrarErro(data.message || "Erro ao excluir disciplina");
        }
    })
    .catch(error => {
        // TRATA ERROS DE REDE OU OUTROS ERROS DURANTE A REQUISIÇÃO
        console.error("Erro ao excluir disciplina:", error);
        // MOSTRA UMA MENSAGEM DE ERRO MAIS DETALHADA
        mostrarErro("Erro ao excluir disciplina: " + error.message);
    });
}
