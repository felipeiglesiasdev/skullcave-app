// JAVASCRIPT ESPECÍFICO PARA DASHBOARD INDEPENDENTE - LAYOUT 2 COLUNAS

// DECLARAÇÃO DE VARIÁVEIS GLOBAIS PARA CONTROLE DE SELEÇÃO
let disciplinaSelecionada = null; // ID DA DISCIPLINA ATUALMENTE SELECIONADA
let topicoSelecionado = null; // ID DO TÓPICO ATUALMENTE SELECIONADO

// ADICIONA UM OUVINTE DE EVENTO PARA QUANDO O DOCUMENTO ESTIVER TOTALMENTE CARREGADO
document.addEventListener("DOMContentLoaded", function() {
    // REGISTRA NO CONSOLE QUE O DASHBOARD FOI CARREGADO
    console.log("Dashboard Independente carregado!");
    
    // CHAMA A FUNÇÃO PARA CARREGAR AS DISCIPLINAS AO INICIAR A PÁGINA
    carregarDisciplinas();
    
    // CHAMA A FUNÇÃO PARA CARREGAR AS ESTATÍSTICAS AO INICIAR A PÁGINA
    carregarEstatisticas();
    
    // DEFINE UM TEMPORIZADOR PARA SELECIONAR A PRIMEIRA DISCIPLINA APÓS UM CURTO ATRASO
    setTimeout(() => {
        // BUSCA O PRIMEIRO CARTÃO DE DISCIPLINA ATIVO
        const primeiraDisciplina = document.querySelector(".disciplina-card.active");
        // VERIFICA SE UMA DISCIPLINA FOI ENCONTRADA
        if (primeiraDisciplina) {
            // OBTÉM O ID DA DISCIPLINA DO ATRIBUTO data-id
            const disciplinaId = primeiraDisciplina.dataset.id;
            // CHAMA A FUNÇÃO PARA SELECIONAR A DISCIPLINA
            selecionarDisciplina(disciplinaId);
        }
    }, 1000); // ATRASO DE 1 SEGUNDO
});

// ===== FUNÇÕES DE CONTROLE DE VIEWS =====

// FUNÇÃO PARA MOSTRAR UMA VIEW ESPECÍFICA E ESCONDER AS OUTRAS
function mostrarView(viewId) {
    // SELECIONA TODOS OS ELEMENTOS COM AS CLASSES 'content-view' OU 'welcome-state'
    document.querySelectorAll(".content-view, .welcome-state").forEach(view => {
        // ESCONDE CADA VIEW DEFININDO SEU ESTILO DE EXIBIÇÃO COMO 'none'
        view.style.display = "none";
    });
    
    // SELECIONA O ELEMENTO DA VIEW ALVO PELO SEU ID
    const targetView = document.getElementById(viewId);
    // VERIFICA SE A VIEW ALVO EXISTE
    if (targetView) {
        // MOSTRA A VIEW ALVO DEFININDO SEU ESTILO DE EXIBIÇÃO COMO 'block'
        targetView.style.display = "block";
    }
}

// FUNÇÃO PARA ATUALIZAR O BREADCRUMB (NAVEGAÇÃO HIERÁRQUICA)
function atualizarBreadcrumb(disciplinaNome, topicoNome = null) {
    // SELECIONA O ELEMENTO ONDE O NOME DA DISCIPLINA SERÁ EXIBIDO
    const disciplinaElement = document.getElementById("disciplina-nome");
    // SELECIONA O ELEMENTO ONDE O NOME DO TÓPICO SERÁ EXIBIDO
    const topicoElement = document.getElementById("topico-nome");
    // SELECIONA O ELEMENTO SEPARADOR DO BREADCRUMB
    const separatorElement = document.getElementById("breadcrumb-separator");
    
    // VERIFICA SE O ELEMENTO DA DISCIPLINA EXISTE
    if (disciplinaElement) {
        // ATUALIZA O TEXTO DO ELEMENTO DA DISCIPLINA
        disciplinaElement.textContent = disciplinaNome;
    }
    
    // VERIFICA SE UM NOME DE TÓPICO FOI FORNECIDO
    if (topicoNome) {
        // VERIFICA SE O ELEMENTO DO TÓPICO EXISTE
        if (topicoElement) {
            // ATUALIZA O TEXTO DO ELEMENTO DO TÓPICO
            topicoElement.textContent = topicoNome;
            // MOSTRA O ELEMENTO DO TÓPICO
            topicoElement.style.display = "inline";
        }
        // VERIFICA SE O ELEMENTO SEPARADOR EXISTE
        if (separatorElement) {
            // MOSTRA O ELEMENTO SEPARADOR
            separatorElement.style.display = "inline";
        }
    } else {
        // SE NENHUM NOME DE TÓPICO FOI FORNECIDO, ESCONDE O ELEMENTO DO TÓPICO
        if (topicoElement) {
            topicoElement.style.display = "none";
        }
        // SE NENHUM NOME DE TÓPICO FOI FORNECIDO, ESCONDE O ELEMENTO SEPARADOR
        if (separatorElement) {
            separatorElement.style.display = "none";
        }
    }
}

// FUNÇÃO PARA ATUALIZAR A VISIBILIDADE DOS BOTÕES DE AÇÃO (ADICIONAR TÓPICO/FLASHCARD)
function atualizarBotoesAcao(mostrarTopico = false, mostrarFlashcard = false) {
    // SELECIONA O BOTÃO DE ADICIONAR TÓPICO
    const btnAddTopico = document.getElementById("btn-add-topico");
    // SELECIONA O BOTÃO DE ADICIONAR FLASHCARD
    const btnAddFlashcard = document.getElementById("btn-add-flashcard");
    
    // VERIFICA SE O BOTÃO DE ADICIONAR TÓPICO EXISTE
    if (btnAddTopico) {
        // DEFINE O ESTILO DE EXIBIÇÃO DO BOTÃO DE ADICIONAR TÓPICO
        btnAddTopico.style.display = mostrarTopico ? "flex" : "none";
    }
    
    // VERIFICA SE O BOTÃO DE ADICIONAR FLASHCARD EXISTE
    if (btnAddFlashcard) {
        // DEFINE O ESTILO DE EXIBIÇÃO DO BOTÃO DE ADICIONAR FLASHCARD
        btnAddFlashcard.style.display = mostrarFlashcard ? "flex" : "none";
    }
}



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
                <button class="btn-action" onclick="event.stopPropagation(); removerTopico(${topico.id_topico})" title="Excluir">
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

// ===== FUNÇÕES DE FLASHCARDS =====

// FUNÇÃO PARA CARREGAR OS FLASHCARDS DE UM TÓPICO ESPECÍFICO
function carregarFlashcards(topicoId) {
    // VERIFICA SE O ID DO TÓPICO FOI FORNECIDO
    if (!topicoId) {
        // REGISTRA UM ERRO NO CONSOLE
        console.error("ID do tópico não fornecido");
        return; // ENCERRA A FUNÇÃO
    }
    
    // FAZ UMA REQUISIÇÃO FETCH PARA A API DE FLASHCARDS DO TÓPICO
    fetch(`./api/independente/topicos/${topicoId}/flashcards`, {
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
            // SE A OPERAÇÃO FOI BEM-SUCEDIDA, RENDERIZA OS FLASHCARDS
            renderizarFlashcards(data.data || data.flashcards || []);
        } else {
            // SE HOUVE ERRO, MOSTRA UMA MENSAGEM DE ERRO
            mostrarErro(data.message || "Erro ao carregar flashcards");
        }
    })
    .catch(error => {
        // TRATA ERROS DE REDE OU OUTROS ERROS DURANTE A REQUISIÇÃO
        console.error("Erro ao carregar flashcards:", error);
        // MOSTRA UMA MENSAGEM DE ERRO MAIS DETALHADA
        mostrarErro("Erro ao carregar flashcards: " + error.message);
    });
}

// FUNÇÃO PARA RENDERIZAR (EXIBIR) OS FLASHCARDS NA INTERFACE
function renderizarFlashcards(flashcards) {
    // SELECIONA O CONTÊINER ONDE OS FLASHCARDS SERÃO EXIBIDOS
    const container = document.getElementById("flashcardsList");
    // VERIFICA SE O CONTÊINER FOI ENCONTRADO
    if (!container) {
        // REGISTRA UM ERRO NO CONSOLE SE O CONTÊINER NÃO FOR ENCONTRADO
        console.error("Container flashcardsList não encontrado");
        return; // ENCERRA A FUNÇÃO
    }
    
    // VERIFICA SE NÃO HÁ FLASHCARDS OU SE A LISTA ESTÁ VAZIA
    if (!flashcards || flashcards.length === 0) {
        // DEFINE O CONTEÚDO HTML PARA EXIBIR UM ESTADO VAZIO (NENHUM FLASHCARD CRIADO)
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-clone"></i>
                <p>Nenhum flashcard criado para este tópico</p>
                <button class="btn btn-primary btn-sm" onclick="abrirModalFlashcard()">
                    Criar primeiro flashcard
                </button>
            </div>
        `;
        return; // ENCERRA A FUNÇÃO
    }
    
    // GERA O HTML PARA CADA FLASHCARD E INSERE NO CONTÊINER
    container.innerHTML = flashcards.map(flashcard => `
        <div class="flashcard-card" data-id="${flashcard.id_flashcard}">
            <div class="flashcard-info">
                <h6>${flashcard.titulo}</h6>
                <span class="perguntas-count">${flashcard.perguntas ? flashcard.perguntas.length : 0} perguntas</span>
            </div>
            <div class="flashcard-actions">
                <button class="btn-action" onclick="editarFlashcard(${flashcard.id_flashcard})" title="Editar">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn-action btn-delete" onclick="removerFlashcard(${flashcard.id_flashcard})" title="Excluir">
                    <i class="fas fa-trash"></i>
                </button>
                <button class="btn-action" onclick="criarPerguntaResposta(${flashcard.id_flashcard})" title="Adicionar Pergunta">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
    `).join(""); // UNE OS ELEMENTOS DO ARRAY EM UMA ÚNICA STRING HTML
}

// FUNÇÃO PARA ABRIR O MODAL DE CRIAÇÃO DE FLASHCARD
function abrirModalFlashcard() {
    // VERIFICA SE UM TÓPICO ESTÁ SELECIONADO
    if (!topicoSelecionado) {
        // MOSTRA UM ERRO SE NENHUM TÓPICO ESTIVER SELECIONADO
        mostrarErro("Selecione um tópico primeiro");
        return; // ENCERRA A FUNÇÃO
    }
    
    // SELECIONA O ELEMENTO DO MODAL
    const modalElement = document.getElementById("flashcardModal");
    // VERIFICA SE O MODAL FOI ENCONTRADO
    if (modalElement) {
        // SELECIONA O FORMULÁRIO DENTRO DO MODAL
        const form = document.getElementById("flashcardForm");
        // VERIFICA SE O FORMULÁRIO EXISTE
        if (form) {
            // LIMPA OS CAMPOS DO FORMULÁRIO
            form.reset();
        }
        
        // SELECIONA O CAMPO OCULTO PARA O ID DO TÓPICO
        const topicoIdInput = document.getElementById("topico_id");
        // VERIFICA SE O CAMPO EXISTE
        if (topicoIdInput) {
            // DEFINE O VALOR DO CAMPO COM O ID DO TÓPICO SELECIONADO
            topicoIdInput.value = topicoSelecionado;
        }
        
        // CRIA UMA NOVA INSTÂNCIA DO MODAL DO BOOTSTRAP 5 E O MOSTRA
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    } else {
        // REGISTRA UM ERRO NO CONSOLE SE O MODAL NÃO FOR ENCONTRADO
        console.error("Modal flashcardModal não encontrado");
    }
}

// FUNÇÃO PARA CRIAR UM NOVO FLASHCARD
function criarFlashcard() {
    // SELECIONA O FORMULÁRIO DE FLASHCARD
    const form = document.getElementById("flashcardForm");
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
        titulo: formData.get("titulo"), // OBTÉM O VALOR DO CAMPO 'titulo'
        descricao: formData.get("descricao") || "", // OBTÉM O VALOR DO CAMPO 'descricao' OU UMA STRING VAZIA
        topico_id: formData.get("topico_id") || topicoSelecionado // OBTÉM O ID DO TÓPICO OU USA O SELECIONADO
    };
    
    // VALIDAÇÃO BÁSICA DO TÍTULO DO FLASHCARD
    if (!data.titulo || data.titulo.trim().length < 3) {
        // MOSTRA UM ERRO SE O TÍTULO FOR INVÁLIDO
        mostrarErro("Título do flashcard deve ter pelo menos 3 caracteres");
        return; // ENCERRA A FUNÇÃO
    }
    
    // VALIDAÇÃO SE O TÓPICO FOI SELECIONADO
    if (!data.topico_id) {
        // MOSTRA UM ERRO SE O TÓPICO NÃO FOI SELECIONADO
        mostrarErro("Tópico não selecionado");
        return; // ENCERRA A FUNÇÃO
    }
    
    // FAZ UMA REQUISIÇÃO FETCH PARA A API DE CRIAÇÃO DE FLASHCARDS
    fetch(`./api/independente/flashcards`, {
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
            mostrarSucesso(data.message || "Flashcard criado com sucesso!");
            
            // FECHA O MODAL APÓS A CRIAÇÃO BEM-SUCEDIDA
            const modalElement = document.getElementById("flashcardModal");
            if (modalElement) {
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
            }
            
            // LIMPA O FORMULÁRIO
            form.reset();
            
            // RECARREGA A LISTA DE FLASHCARDS PARA ATUALIZAR A INTERFACE
            carregarFlashcards(topicoSelecionado);
        } else {
            // MOSTRA UMA MENSAGEM DE ERRO
            mostrarErro(data.message || "Erro ao criar flashcard");
        }
    })
    .catch(error => {
        // TRATA ERROS DE REDE OU OUTROS ERROS DURANTE A REQUISIÇÃO
        console.error("Erro ao criar flashcard:", error);
        // MOSTRA UMA MENSAGEM DE ERRO MAIS DETALHADA
        mostrarErro("Erro ao criar flashcard: " + error.message);
    });
}

// ===== FUNÇÕES DE ESTATÍSTICAS =====

// FUNÇÃO PARA CARREGAR ESTATÍSTICAS DO SERVIDOR
function carregarEstatisticas() {
    // FAZ UMA REQUISIÇÃO FETCH PARA A API DE ESTATÍSTICAS
    fetch(`./api/independente/estatisticas`, {
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
            // ATUALIZA OS ELEMENTOS HTML COM OS DADOS DAS ESTATÍSTICAS
            document.getElementById("totalDisciplinas").textContent = data.estatisticas.total_disciplinas;
            document.getElementById("totalTopicos").textContent = data.estatisticas.total_topicos;
            document.getElementById("totalFlashcards").textContent = data.estatisticas.total_flashcards;
            document.getElementById("totalPerguntas").textContent = data.estatisticas.total_perguntas;
            
            // RENDERIZA AS DISCIPLINAS RECENTES
            renderizarDisciplinasRecentes(data.estatisticas.disciplinas_recentes);
            // RENDERIZA OS FLASHCARDS RECENTES
            renderizarFlashcardsRecentes(data.estatisticas.flashcards_recentes);
        } else {
            // MOSTRA UMA MENSAGEM DE ERRO
            mostrarErro(data.message || "Erro ao carregar estatísticas");
        }
    })
    .catch(error => {
        // TRATA ERROS DE REDE OU OUTROS ERROS DURANTE A REQUISIÇÃO
        console.error("Erro ao carregar estatísticas:", error);
        // MOSTRA UMA MENSAGEM DE ERRO MAIS DETALHADA
        mostrarErro("Erro ao carregar estatísticas: " + error.message);
    });
}

// FUNÇÃO PARA RENDERIZAR AS DISCIPLINAS MAIS RECENTES
function renderizarDisciplinasRecentes(disciplinas) {
    // SELECIONA O CONTÊINER ONDE AS DISCIPLINAS RECENTES SERÃO EXIBIDAS
    const container = document.getElementById("recentDisciplinasList");
    // VERIFICA SE O CONTÊINER FOI ENCONTRADO
    if (!container) {
        // REGISTRA UM ERRO NO CONSOLE
        console.error("Container recentDisciplinasList não encontrado");
        return; // ENCERRA A FUNÇÃO
    }
    
    // VERIFICA SE NÃO HÁ DISCIPLINAS RECENTES OU SE A LISTA ESTÁ VAZIA
    if (!disciplinas || disciplinas.length === 0) {
        // DEFINE O CONTEÚDO HTML PARA EXIBIR UMA MENSAGEM DE NENHUMA ATIVIDADE
        container.innerHTML = `<p>Nenhuma atividade recente.</p>`;
        return; // ENCERRA A FUNÇÃO
    }
    
    // GERA O HTML PARA CADA DISCIPLINA RECENTE E INSERE NO CONTÊINER
    container.innerHTML = disciplinas.map(disciplina => `
        <li>
            <i class="fas fa-book"></i> ${disciplina.nome} 
            <span class="badge bg-primary">${disciplina.topicos ? disciplina.topicos.length : 0} Tópicos</span>
        </li>
    `).join(""); // UNE OS ELEMENTOS DO ARRAY EM UMA ÚNICA STRING HTML
}

// FUNÇÃO PARA RENDERIZAR A ATIVIDADE RECENTE DE FLASHCARDS
function renderizarFlashcardsRecentes(count) {
    // SELECIONA O ELEMENTO ONDE A CONTAGEM DE FLASHCARDS RECENTES SERÁ EXIBIDA
    const element = document.getElementById("recentFlashcardsCount");
    // VERIFICA SE O ELEMENTO FOI ENCONTRADO
    if (element) {
        // ATUALIZA O TEXTO DO ELEMENTO COM A CONTAGEM
        element.textContent = `${count} flashcards criados na última semana`;
    }
}

// ===== FUNÇÕES DE MENSAGENS (SUCESSO/ERRO) =====

// FUNÇÃO PARA MOSTRAR MENSAGENS DE SUCESSO
function mostrarSucesso(mensagem) {
    // SELECIONA O ELEMENTO DE ALERTA DE SUCESSO
    const alertDiv = document.getElementById("successAlert");
    // VERIFICA SE O ELEMENTO FOI ENCONTRADO
    if (alertDiv) {
        // DEFINE O TEXTO DA MENSAGEM
        alertDiv.querySelector(".alert-message").textContent = mensagem;
        // MOSTRA O ALERTA
        alertDiv.style.display = "block";
        // ESCONDE O ALERTA APÓS 3 SEGUNDOS
        setTimeout(() => {
            alertDiv.style.display = "none";
        }, 3000);
    }
}

// FUNÇÃO PARA MOSTRAR MENSAGENS DE ERRO
function mostrarErro(mensagem) {
    // SELECIONA O ELEMENTO DE ALERTA DE ERRO
    const alertDiv = document.getElementById("errorAlert");
    // VERIFICA SE O ELEMENTO FOI ENCONTRADO
    if (alertDiv) {
        // DEFINE O TEXTO DA MENSAGEM
        alertDiv.querySelector(".alert-message").textContent = mensagem;
        // MOSTRA O ALERTA
        alertDiv.style.display = "block";
        // ESCONDE O ALERTA APÓS 5 SEGUNDOS
        setTimeout(() => {
            alertDiv.style.display = "none";
        }, 5000);
    }
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

// ===== FUNÇÃO PARA EXCLUIR FLASHCARD =====
function removerFlashcard(flashcardId) {
    // CONFIRMAR AÇÃO DE EXCLUSÃO COM O USUÁRIO
    if (!confirm("Tem certeza que deseja excluir este flashcard e todas as suas perguntas associadas?")) {
        return; // SE O USUÁRIO CANCELAR, ENCERRA A FUNÇÃO
    }

    // FAZER REQUISIÇÃO DELETE PARA EXCLUIR O FLASHCARD
    fetch(`./api/independente/flashcards/${flashcardId}`, {
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
            mostrarSucesso(data.message || "Flashcard excluído com sucesso!");
            // RECARREGA A LISTA DE FLASHCARDS PARA ATUALIZAR A INTERFACE
            carregarFlashcards(topicoSelecionado);
        } else {
            // MOSTRA UMA MENSAGEM DE ERRO
            mostrarErro(data.message || "Erro ao excluir flashcard");
        }
    })
    .catch(error => {
        // TRATA ERROS DE REDE OU OUTROS ERROS DURANTE A REQUISIÇÃO
        console.error("Erro ao excluir flashcard:", error);
        // MOSTRA UMA MENSAGEM DE ERRO MAIS DETALHADA
        mostrarErro("Erro ao excluir flashcard: " + error.message);
    });
}

// ===== FUNÇÃO PARA CRIAR PERGUNTA E RESPOSTA EM UM FLASHCARD =====
function criarPerguntaResposta(flashcardId) {
    // SOLICITA A PERGUNTA AO USUÁRIO ATRAVÉS DE UM PROMPT
    const pergunta = prompt("Digite a pergunta:");
    // SE O USUÁRIO CANCELAR OU DEIXAR EM BRANCO, ENCERRA A FUNÇÃO
    if (!pergunta) return;

    // SOLICITA A RESPOSTA AO USUÁRIO ATRAVÉS DE UM PROMPT
    const resposta = prompt("Digite a resposta:");
    // SE O USUÁRIO CANCELAR OU DEIXAR EM BRANCO, ENCERRA A FUNÇÃO
    if (!resposta) return;

    // FAZ UMA REQUISIÇÃO POST PARA CRIAR A PERGUNTA E RESPOSTA
    fetch(`./api/independente/perguntas`, {
        method: "POST", // MÉTODO HTTP POST
        headers: { // CABEÇALHOS DA REQUISIÇÃO
            "Content-Type": "application/json", // TIPO DE CONTEÚDO JSON
            "X-CSRF-TOKEN": document.querySelector("meta[name=\"csrf-token\"]")?.getAttribute("content") || "", // TOKEN CSRF
            "Accept": "application/json" // ACEITA RESPOSTAS JSON
        },
        body: JSON.stringify({ // CORPO DA REQUISIÇÃO EM FORMATO JSON
            id_flashcard: flashcardId,
            pergunta: pergunta,
            resposta: resposta
        })
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
            mostrarSucesso(data.message || "Pergunta e resposta criadas com sucesso!");
            // RECARREGA OS FLASHCARDS PARA ATUALIZAR AS PERGUNTAS NA INTERFACE
            carregarFlashcards(topicoSelecionado);
        } else {
            // MOSTRA UMA MENSAGEM DE ERRO
            mostrarErro(data.message || "Erro ao criar pergunta e resposta");
        }
    })
    .catch(error => {
        // TRATA ERROS DE REDE OU OUTROS ERROS DURANTE A REQUISIÇÃO
        console.error("Erro ao criar pergunta e resposta:", error);
        // MOSTRA UMA MENSAGEM DE ERRO MAIS DETALHADA
        mostrarErro("Erro ao criar pergunta e resposta: " + error.message);
    });
}

// ===== FUNÇÃO PARA EDITAR FLASHCARD =====
function editarFlashcard(flashcardId) {
    // BUSCAR OS DADOS ATUAIS DO FLASHCARD PARA PREENCHER O FORMULÁRIO DE EDIÇÃO
    fetch(`./api/independente/flashcards/${flashcardId}`, { // FAZ UMA REQUISIÇÃO GET PARA OBTER OS DETALHES DO FLASHCARD
        method: "GET", // MÉTODO HTTP GET
        headers: { // CABEÇALHOS DA REQUISIÇÃO
            "Content-Type": "application/json", // TIPO DE CONTEÚDO JSON
            "X-CSRF-TOKEN": document.querySelector("meta[name=\"csrf-token\"]")?.getAttribute("content") || "", // TOKEN CSRF
            "Accept": "application/json" // ACEITA RESPOSTAS JSON
        }
    })
        .then(response => {
            // VERIFICAR SE A RESPOSTA FOI BEM-SUCEDIDA
            if (!response.ok) {
                // SE NÃO FOI BEM-SUCEDIDA, LANÇA UM ERRO
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            // RETORNA A RESPOSTA COMO JSON
            return response.json();
        })
        .then(data => {
            // PROCESSAR RESPOSTA DO SERVIDOR
            if (data.success) {
                // SE A OPERAÇÃO FOI BEM-SUCEDIDA, ABRE O MODAL DE EDIÇÃO COM OS DADOS DO FLASHCARD
                abrirModalEdicaoFlashcard(data.flashcard);
            } else {
                // SE HOUVE ERRO, MOSTRA UMA MENSAGEM DE ERRO
                mostrarErro(data.message || "Erro ao buscar flashcard para edição");
            }
        })
        .catch(error => {
            // TRATAR ERROS DE REDE OU OUTROS ERROS
            console.error("Erro ao buscar flashcard para edição:", error);
            // MOSTRA UMA MENSAGEM DE ERRO MAIS DETALHADA
            mostrarErro("Erro ao buscar flashcard para edição: " + error.message);
        });
}

// ===== FUNÇÃO PARA ABRIR MODAL DE EDIÇÃO DE FLASHCARD =====
function abrirModalEdicaoFlashcard(flashcard) {
    // CRIA O HTML DO MODAL DINAMICAMENTE PARA EDIÇÃO
    const modalHtml = `
        <div class="modal fade" id="editFlashcardModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Flashcard</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editFlashcardForm">
                            <div class="mb-3">
                                <label for="editTitulo" class="form-label">Título</label>
                                <input type="text" class="form-control" id="editTitulo" value="${flashcard.titulo}" required>
                            </div>
                            <div class="mb-3">
                                <label for="editDescricao" class="form-label">Descrição</label>
                                <textarea class="form-control" id="editDescricao" rows="3">${flashcard.descricao || ""}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Perguntas e Respostas</label>
                                <div id="perguntasContainer">
                                    ${flashcard.perguntas.map((pergunta, index) => `
                                        <div class="pergunta-item mb-3 border p-3 rounded" data-id="${pergunta.id_pergunta_flashcard}">
                                            <div class="mb-2">
                                                <label class="form-label">Pergunta ${index + 1}</label>
                                                <input type="text" class="form-control pergunta-input" value="${pergunta.pergunta}" required>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Resposta ${index + 1}</label>
                                                <textarea class="form-control resposta-input" rows="2" required>${pergunta.resposta}</textarea>
                                            </div>
                                            <button type="button" class="btn btn-danger btn-sm" onclick="removerPerguntaItem(this)">
                                                <i class="fas fa-trash"></i> Remover
                                            </button>
                                        </div>
                                    `).join("")}
                                </div>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="adicionarNovaPergunta()">
                                    <i class="fas fa-plus"></i> Adicionar Pergunta
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="salvarEdicaoFlashcard(${flashcard.id_flashcard})">Salvar Alterações</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // REMOVE QUALQUER MODAL DE EDIÇÃO EXISTENTE PARA EVITAR DUPLICAÇÃO
    const existingModal = document.getElementById("editFlashcardModal");
    if (existingModal) {
        existingModal.remove();
    }

    // ADICIONA O HTML DO MODAL AO CORPO DO DOCUMENTO
    document.body.insertAdjacentHTML("beforeend", modalHtml);
    // CRIA UMA NOVA INSTÂNCIA DO MODAL DO BOOTSTRAP 5 E O MOSTRA
    const modal = new bootstrap.Modal(document.getElementById("editFlashcardModal"));
    modal.show();
}

// ===== FUNÇÃO PARA ADICIONAR NOVA PERGUNTA NO MODAL DE EDIÇÃO =====
function adicionarNovaPergunta() {
    // SELECIONA O CONTÊINER ONDE AS PERGUNTAS SERÃO ADICIONADAS
    const container = document.getElementById("perguntasContainer");
    // CALCULA O NÚMERO DA PRÓXIMA PERGUNTA
    const perguntaCount = container.children.length + 1;
    
    // CRIA O HTML PARA UMA NOVA PERGUNTA E RESPOSTA
    const novaPerguntaHtml = `
        <div class="pergunta-item mb-3 border p-3 rounded" data-id="">
            <div class="mb-2">
                <label class="form-label">Pergunta ${perguntaCount}</label>
                <input type="text" class="form-control pergunta-input" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Resposta ${perguntaCount}</label>
                <textarea class="form-control resposta-input" rows="2" required></textarea>
            </div>
            <button type="button" class="btn btn-danger btn-sm" onclick="removerPerguntaItem(this)">
                <i class="fas fa-trash"></i> Remover
            </button>
        </div>
    `;
    
    // INSERE O NOVO HTML NO FINAL DO CONTÊINER DE PERGUNTAS
    container.insertAdjacentHTML("beforeend", novaPerguntaHtml);
}

// ===== FUNÇÃO PARA REMOVER PERGUNTA DO MODAL DE EDIÇÃO =====
function removerPerguntaItem(button) {
    // ENCONTRA O ELEMENTO PAI MAIS PRÓXIMO COM A CLASSE 'pergunta-item' E O REMOVE
    button.closest(".pergunta-item").remove();
}

// ===== FUNÇÃO PARA SALVAR EDIÇÃO DO FLASHCARD =====
function salvarEdicaoFlashcard(flashcardId) {
    // COLETAR O VALOR DO CAMPO DE TÍTULO
    const titulo = document.getElementById("editTitulo").value;
    // COLETAR O VALOR DO CAMPO DE DESCRIÇÃO
    const descricao = document.getElementById("editDescricao").value;
    
    // COLETAR TODAS AS PERGUNTAS E RESPOSTAS DO FORMULÁRIO
    const perguntasItems = document.querySelectorAll(".pergunta-item");
    // MAPEIA OS ELEMENTOS PARA UM ARRAY DE OBJETOS COM PERGUNTA E RESPOSTA
    const perguntas = Array.from(perguntasItems).map(item => ({
        id_pergunta_flashcard: item.dataset.id || null, // OBTÉM O ID DA PERGUNTA OU NULL SE FOR NOVA
        pergunta: item.querySelector(".pergunta-input").value, // OBTÉM O VALOR DA PERGUNTA
        resposta: item.querySelector(".resposta-input").value // OBTÉM O VALOR DA RESPOSTA
    }));

    // VALIDAÇÃO BÁSICA: VERIFICA SE O TÍTULO NÃO ESTÁ VAZIO
    if (!titulo.trim()) {
        // MOSTRA UM ERRO SE O TÍTULO FOR INVÁLIDO
        mostrarErro("Título é obrigatório");
        return; // ENCERRA A FUNÇÃO
    }

    // FAZ UMA REQUISIÇÃO PUT PARA ATUALIZAR O FLASHCARD
    fetch(`./api/independente/flashcards/${flashcardId}`, {
        method: "PUT", // MÉTODO HTTP PUT
        headers: { // CABEÇALHOS DA REQUISIÇÃO
            "Content-Type": "application/json", // TIPO DE CONTEÚDO JSON
            "X-CSRF-TOKEN": document.querySelector("meta[name=\"csrf-token\"]")?.getAttribute("content") || "", // TOKEN CSRF
            "Accept": "application/json" // ACEITA RESPOSTAS JSON
        },
        body: JSON.stringify({ // CORPO DA REQUISIÇÃO EM FORMATO JSON
            titulo: titulo,
            descricao: descricao,
            perguntas: perguntas // ENVIA O ARRAY DE PERGUNTAS E RESPOSTAS
        })
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
            mostrarSucesso(data.message || "Flashcard atualizado com sucesso!");
            
            // FECHA O MODAL DE EDIÇÃO
            const modal = bootstrap.Modal.getInstance(document.getElementById("editFlashcardModal"));
            modal.hide();
            
            // RECARREGA OS FLASHCARDS PARA MOSTRAR AS ALTERAÇÕES NA INTERFACE
            carregarFlashcards(topicoSelecionado);
        } else {
            // MOSTRA UMA MENSAGEM DE ERRO
            mostrarErro(data.message || "Erro ao atualizar flashcard");
        }
    })
    .catch(error => {
        // TRATA ERROS DE REDE OU OUTROS ERROS DURANTE A REQUISIÇÃO
        console.error("Erro ao atualizar flashcard:", error);
        // MOSTRA UMA MENSAGEM DE ERRO MAIS DETALHADA
        mostrarErro("Erro ao atualizar flashcard: " + error.message);
    });
}


// ===== FUNÇÃO PARA EDITAR TÓPICO (ABRIR MODAL E SALVAR) =====
function editarTopico(topicoId) {
    // SELECIONA O MODAL DE EDIÇÃO DE TÓPICO
    const modalElement = document.getElementById("topicoModal");
    // VERIFICA SE O MODAL EXISTE
    if (modalElement) {
        // SELECIONA O FORMULÁRIO DENTRO DO MODAL
        const form = document.getElementById("topicoForm");
        // VERIFICA SE O FORMULÁRIO EXISTE
        if (form) {
            // BUSCA OS DADOS DO TÓPICO PELO ID
            fetch(`./api/independente/topicos/${topicoId}`, {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector("meta[name=\"csrf-token\"]")?.getAttribute("content") || "",
                    "Accept": "application/json"
                }
            })
            .then(response => response.json())
            .then(data => {
                // VERIFICA SE A BUSCA FOI BEM-SUCEDIDA
                if (data.success) {
                    // PREENCHE OS CAMPOS DO FORMULÁRIO COM OS DADOS DO TÓPICO
                    form.querySelector("#nomeTopico").value = data.topico.nome;
                    form.querySelector("#descricaoTopico").value = data.topico.descricao || "";
                    // DEFINE UM CAMPO OCULTO PARA O ID DO TÓPICO SENDO EDITADO
                    let idInput = form.querySelector("#topicoIdEdit");
                    if (!idInput) {
                        idInput = document.createElement("input");
                        idInput.type = "hidden";
                        idInput.id = "topicoIdEdit";
                        form.appendChild(idInput);
                    }
                    idInput.value = topicoId;
                    
                    // ALTERA O BOTÃO DE SUBMISSÃO PARA 'SALVAR ALTERAÇÕES'
                    const submitButton = form.querySelector("button[type=\"submit\"]");
                    if (submitButton) {
                        submitButton.textContent = "Salvar Alterações";
                        submitButton.onclick = function() { salvarEdicaoTopico(topicoId); return false; }; // MUDA A AÇÃO DO BOTÃO
                    }
                    
                    // ALTERA O TÍTULO DO MODAL
                    const modalTitle = modalElement.querySelector(".modal-title");
                    if (modalTitle) {
                        modalTitle.textContent = "Editar Tópico";
                    }
                    
                    // ABRE O MODAL
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                } else {
                    // MOSTRA ERRO SE NÃO CONSEGUIR BUSCAR O TÓPICO
                    mostrarErro(data.message || "Erro ao carregar tópico para edição");
                }
            })
            .catch(error => {
                // TRATA ERROS DE REDE
                console.error("Erro ao carregar tópico para edição:", error);
                mostrarErro("Erro ao carregar tópico para edição: " + error.message);
            });
        }
    } else {
        // REGISTRA ERRO SE O MODAL NÃO FOR ENCONTRADO
        console.error("Modal topicoModal não encontrado");
    }
}

// ===== FUNÇÃO PARA SALVAR EDIÇÃO DE TÓPICO =====
function salvarEdicaoTopico(topicoId) {
    // SELECIONA O FORMULÁRIO DE TÓPICO
    const form = document.getElementById("topicoForm");
    // VERIFICA SE O FORMULÁRIO EXISTE
    if (!form) {
        mostrarErro("Formulário não encontrado");
        return;
    }
    
    // CRIA UM OBJETO FormData A PARTIR DO FORMULÁRIO
    const formData = new FormData(form);
    
    // EXTRAI OS DADOS DO FORMULÁRIO PARA UM OBJETO JAVASCRIPT
    const data = {
        nome: formData.get("nome"),
        descricao: formData.get("descricao") || ""
    };
    
    // VALIDAÇÃO BÁSICA DO NOME DO TÓPICO
    if (!data.nome || data.nome.trim().length < 3) {
        mostrarErro("Nome do tópico deve ter pelo menos 3 caracteres");
        return;
    }
    
    // FAZ UMA REQUISIÇÃO FETCH PARA A API DE EDIÇÃO DE TÓPICOS (MÉTODO PUT)
    fetch(`./api/independente/topicos/${topicoId}`, {
        method: "PUT", // MÉTODO HTTP PUT
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector("meta[name=\"csrf-token\"]")?.getAttribute("content") || "",
            "Accept": "application/json"
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        // VERIFICA SE A RESPOSTA DA REQUISIÇÃO FOI BEM-SUCEDIDA
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        // PROCESSA OS DADOS RECEBIDOS DO SERVIDOR
        if (data.success) {
            mostrarSucesso(data.message || "Tópico atualizado com sucesso!");
            
            // FECHA O MODAL
            const modalElement = document.getElementById("topicoModal");
            if (modalElement) {
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
            }
            
            // RECARREGA OS TÓPICOS PARA ATUALIZAR A INTERFACE
            carregarTopicos(disciplinaSelecionada);
        } else {
            mostrarErro(data.message || "Erro ao atualizar tópico");
        }
    })
    .catch(error => {
        console.error("Erro ao atualizar tópico:", error);
        mostrarErro("Erro ao atualizar tópico: " + error.message);
    });
}
