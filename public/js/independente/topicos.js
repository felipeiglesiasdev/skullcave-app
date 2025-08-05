// ===== FUNÇÕES DE TÓPICOS =====

// FUNÇÃO PARA CARREGAR OS TÓPICOS DE UMA DISCIPLINA ESPECÍFICA
function carregarTopicos(disciplinaId) {
    // VERIFICA SE O ID DA DISCIPLINA FOI FORNECIDO
    if (!disciplinaId) {
        // REGISTRA UM ERRO NO CONSOLE
        console.error("ID da disciplina não fornecido");
        return; // ENCERRA A FUNÇÃO
    }
    
    // FAZ UMA REQUISIÇÃO FETCH PARA A API DE TÓPICOS DA DISCIPLINA
    fetch(`./api/independente/disciplinas/${disciplinaId}/topicos`, {
        method: "GET", // MÉTODO HTTP GET
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
            // SE A OPERAÇÃO FOI BEM-SUCEDIDA, RENDERIZA OS TÓPICOS
            renderizarTopicos(data.data || data.topicos || []);
        } else {
            // SE HOUVE ERRO, MOSTRA UMA MENSAGEM DE ERRO
            mostrarErro(data.message || "Erro ao carregar tópicos");
        }
    })
    .catch(error => {
        // TRATA ERROS DE REDE OU OUTROS ERROS DURANTE A REQUISIÇÃO
        console.error("Erro ao carregar tópicos:", error);
        // MOSTRA UMA MENSAGEM DE ERRO MAIS DETALHADA
        mostrarErro("Erro ao carregar tópicos: " + error.message);
    });
}

// FUNÇÃO PARA RENDERIZAR (EXIBIR) OS TÓPICOS NA INTERFACE
function renderizarTopicos(topicos) {
    // SELECIONA O CONTÊINER ONDE OS TÓPICOS SERÃO EXIBIDOS
    const container = document.getElementById("topicosList");
    // VERIFICA SE O CONTÊINER FOI ENCONTRADO
    if (!container) {
        // REGISTRA UM ERRO NO CONSOLE SE O CONTÊINER NÃO FOR ENCONTRADO
        console.error("Container topicosList não encontrado");
        return; // ENCERRA A FUNÇÃO
    }
    
    // VERIFICA SE NÃO HÁ TÓPICOS OU SE A LISTA ESTÁ VAZIA
    if (!topicos || topicos.length === 0) {
        // DEFINE O CONTEÚDO HTML PARA EXIBIR UM ESTADO VAZIO (NENHUM TÓPICO CRIADO)
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-bookmark"></i>
                <p>Nenhum tópico criado para esta disciplina</p>
                <button class="btn btn-primary btn-sm" onclick="abrirModalTopico()">
                    Criar primeiro tópico
                </button>
            </div>
        `;
        return; // ENCERRA A FUNÇÃO
    }
    
    // GERA O HTML PARA CADA TÓPICO E INSERE NO CONTÊINER
    container.innerHTML = topicos.map(topico => `
        <div class="topico-card" 
             data-id="${topico.id_topico}"
             onclick="selecionarTopico(${topico.id_topico})">
            <div class="topico-icon">
                <i class="fas fa-bookmark"></i>
            </div>
            <div class="topico-info">
                <h6>${topico.nome}</h6>
                <span class="flashcards-count">${topico.flashcards ? topico.flashcards.length : 0} flashcards</span>
            </div>
            <div class="topico-actions">
                <button class="btn-action btn-delete" onclick="event.stopPropagation(); removerTopico(${topico.id_topico})" title="Excluir">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `).join(""); // UNE OS ELEMENTOS DO ARRAY EM UMA ÚNICA STRING HTML
}

// FUNÇÃO PARA SELECIONAR UM TÓPICO
function selecionarTopico(topicoId) {
    // ATUALIZA A VARIÁVEL GLOBAL COM O ID DO TÓPICO SELECIONADO
    topicoSelecionado = topicoId;
    
    // REMOVE A CLASSE 'active' DE TODOS OS CARTÕES DE TÓPICO
    document.querySelectorAll(".topico-card").forEach(card => {
        card.classList.remove("active");
    });
    
    // SELECIONA O CARTÃO DO TÓPICO CORRESPONDENTE AO ID
    const topicoCard = document.querySelector(`[data-id="${topicoId}"]`);
    // VERIFICA SE O CARTÃO DO TÓPICO EXISTE
    if (topicoCard) {
        // ADICIONA A CLASSE 'active' AO CARTÃO DO TÓPICO SELECIONADO
        topicoCard.classList.add("active");
        
        // ATUALIZA O BREADCRUMB COM O NOME DA DISCIPLINA E DO TÓPICO
        const nomeTopico = topicoCard.querySelector("h6").textContent;
        const disciplinaNome = document.getElementById("disciplina-nome").textContent;
        atualizarBreadcrumb(disciplinaNome, nomeTopico);
        
        // MOSTRA OS BOTÕES DE ADICIONAR TÓPICO E FLASHCARD
        atualizarBotoesAcao(true, true);
    }
    
    // MOSTRA A VIEW DE FLASHCARDS
    mostrarView("flashcards-view");
    
    // CARREGA OS FLASHCARDS DO TÓPICO SELECIONADO
    carregarFlashcards(topicoId);
}

// FUNÇÃO PARA ABRIR O MODAL DE CRIAÇÃO DE TÓPICO
function abrirModalTopico() {
    // VERIFICA SE UMA DISCIPLINA ESTÁ SELECIONADA
    if (!disciplinaSelecionada) {
        // MOSTRA UM ERRO SE NENHUMA DISCIPLINA ESTIVER SELECIONADA
        mostrarErro("Selecione uma disciplina primeiro");
        return; // ENCERRA A FUNÇÃO
    }
    
    // SELECIONA O ELEMENTO DO MODAL
    const modalElement = document.getElementById("topicoModal");
    // VERIFICA SE O MODAL FOI ENCONTRADO
    if (modalElement) {
        // SELECIONA O FORMULÁRIO DENTRO DO MODAL
        const form = document.getElementById("topicoForm");
        // VERIFICA SE O FORMULÁRIO EXISTE
        if (form) {
            // LIMPA OS CAMPOS DO FORMULÁRIO
            form.reset();
        }
        
        // SELECIONA O CAMPO OCULTO PARA O ID DA DISCIPLINA
        const disciplinaIdInput = document.getElementById("disciplina_id");
        // VERIFICA SE O CAMPO EXISTE
        if (disciplinaIdInput) {
            // DEFINE O VALOR DO CAMPO COM O ID DA DISCIPLINA SELECIONADA
            disciplinaIdInput.value = disciplinaSelecionada;
        }
        
        // CRIA UMA NOVA INSTÂNCIA DO MODAL DO BOOTSTRAP 5 E O MOSTRA
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    } else {
        // REGISTRA UM ERRO NO CONSOLE SE O MODAL NÃO FOR ENCONTRADO
        console.error("Modal topicoModal não encontrado");
    }
}

// FUNÇÃO PARA CRIAR UM NOVO TÓPICO
function criarTopico() {
    // SELECIONA O FORMULÁRIO DE TÓPICO
    const form = document.getElementById("topicoForm");
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
        descricao: formData.get("descricao") || "", // OBTÉM O VALOR DO CAMPO 'descricao' OU UMA STRING VAZIA
        disciplina_id: formData.get("disciplina_id") || disciplinaSelecionada // OBTÉM O ID DA DISCIPLINA OU USA A SELECIONADA
    };
    
    // VALIDAÇÃO BÁSICA DO NOME DO TÓPICO
    if (!data.nome || data.nome.trim().length < 3) {
        // MOSTRA UM ERRO SE O NOME FOR INVÁLIDO
        mostrarErro("Nome do tópico deve ter pelo menos 3 caracteres");
        return; // ENCERRA A FUNÇÃO
    }
    
    // VALIDAÇÃO SE A DISCIPLINA FOI SELECIONADA
    if (!data.disciplina_id) {
        // MOSTRA UM ERRO SE A DISCIPLINA NÃO FOI SELECIONADA
        mostrarErro("Disciplina não selecionada");
        return; // ENCERRA A FUNÇÃO
    }
    
    // FAZ UMA REQUISIÇÃO FETCH PARA A API DE CRIAÇÃO DE TÓPICOS
    fetch(`./api/independente/topicos`, {
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
            mostrarSucesso(data.message || "Tópico criado com sucesso!");
            
            // FECHA O MODAL APÓS A CRIAÇÃO BEM-SUCEDIDA
            const modalElement = document.getElementById("topicoModal");
            if (modalElement) {
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
            }
            
            // LIMPA O FORMULÁRIO
            form.reset();
            
            // RECARREGA A LISTA DE TÓPICOS PARA ATUALIZAR A INTERFACE
            carregarTopicos(disciplinaSelecionada);
        } else {
            // MOSTRA UMA MENSAGEM DE ERRO
            mostrarErro(data.message || "Erro ao criar tópico");
        }
    })
    .catch(error => {
        // TRATA ERROS DE REDE OU OUTROS ERROS DURANTE A REQUISIÇÃO
        console.error("Erro ao criar tópico:", error);
        // MOSTRA UMA MENSAGEM DE ERRO MAIS DETALHADA
        mostrarErro("Erro ao criar tópico: " + error.message);
    });
}

// ===== FUNÇÃO PARA EXCLUIR TÓPICO =====
function removerTopico(topicoId) {
    // CONFIRMAR AÇÃO DE EXCLUSÃO COM O USUÁRIO
    if (!confirm("Tem certeza que deseja excluir este tópico e todos os seus flashcards associados?")) {
        return; // SE O USUÁRIO CANCELAR, ENCERRA A FUNÇÃO
    }

    // FAZER REQUISIÇÃO DELETE PARA EXCLUIR O TÓPICO
    fetch(`./api/independente/topicos/${topicoId}`, {
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
            mostrarSucesso(data.message || "Tópico excluído com sucesso!");
            // RECARREGA A LISTA DE TÓPICOS PARA ATUALIZAR A INTERFACE
            carregarTopicos(disciplinaSelecionada);
        } else {
            // MOSTRA UMA MENSAGEM DE ERRO
            mostrarErro(data.message || "Erro ao excluir tópico");
        }
    })
    .catch(error => {
        // TRATA ERROS DE REDE OU OUTROS ERROS DURANTE A REQUISIÇÃO
        console.error("Erro ao excluir tópico:", error);
        // MOSTRA UMA MENSAGEM DE ERRO MAIS DETALHADA
        mostrarErro("Erro ao excluir tópico: " + error.message);
    });
}
