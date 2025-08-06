// ===== FUNÇÕES DE FLASHCARDS =====
// ***************************************************************************************************************
// ***************************************************************************************************************
// ***************************************************************************************************************

//FUNCIONANDO
// FUNÇÃO PARA CARREGAR OS FLASHCARDS DE UM TÓPICO ESPECÍFICO
function carregarFlashcards(topicoId) {
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
// ***************************************************************************************************************
// ***************************************************************************************************************
// ***************************************************************************************************************


//FUNCIONANDO
// FUNÇÃO PARA RENDERIZAR OS FLASHCARDS NA INTERFACE
function renderizarFlashcards(flashcards) {
    // SELECIONA O CONTÊINER ONDE OS FLASHCARDS SERÃO EXIBIDOS
    const container = document.getElementById("flashcardsList");
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
            <div class="flashcard-header">
                <div class="flashcard-info">
                    <h6>${flashcard.titulo}</h6>
                    <p class="flashcard-description">${flashcard.descricao || ""}</p>
                    <span class="perguntas-count">${flashcard.perguntas ? flashcard.perguntas.length : 0} perguntas</span>
                </div>
                <div class="flashcard-actions">
                    <button class="btn-action btn-primary" onclick="iniciarRevisao(${flashcard.id_flashcard})" title="Iniciar Revisão">
                        <i class="fas fa-play"></i>
                    </button>
                    <button class="btn-action" onclick="gerenciarPerguntas(${flashcard.id_flashcard})" title="Gerenciar Perguntas">
                        <i class="fas fa-cog"></i>
                    </button>
                    <button class="btn-action" onclick="editarFlashcard(${flashcard.id_flashcard})" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-action btn-delete" onclick="excluirFlashcard(${flashcard.id_flashcard})" title="Excluir">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            ${flashcard.perguntas && flashcard.perguntas.length > 0 ? `
                <div class="flashcard-perguntas">
                    <h6>Perguntas e Respostas:</h6>
                    ${flashcard.perguntas.map((pergunta, index) => `
                        <div class="pergunta-item">
                            <div class="pergunta-numero">Q${index + 1}</div>
                            <div class="pergunta-conteudo">
                                <div class="pergunta-texto">
                                    <strong>P:</strong> ${pergunta.pergunta}
                                </div>
                                <div class="resposta-texto">
                                    <strong>R:</strong> ${pergunta.resposta}
                                </div>
                            </div>
                        </div>
                    `).join("")}
                </div>
                <div class="flashcard-footer">
                    <button class="btn btn-success btn-sm" onclick="iniciarRevisao(${flashcard.id_flashcard})">
                        <i class="fas fa-play"></i> Iniciar Revisão
                    </button>
                </div>
            ` : `
                <div class="flashcard-footer">
                    <p class="text-muted">Adicione perguntas para poder iniciar a revisão</p>
                </div>
            `}
        </div>
    `).join(""); // UNE OS ELEMENTOS DO ARRAY EM UMA ÚNICA STRING HTML
    atualizarBotoesAcao(false, true);
}
// ***************************************************************************************************************
// ***************************************************************************************************************
// ***************************************************************************************************************

//FUNCIONANDO
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
// ***************************************************************************************************************
// ***************************************************************************************************************
// ***************************************************************************************************************

// FUNCIONANDO 
// FUNÇÃO PARA CRIAR UM NOVO FLASHCARD
function adicionarFlashcard() {
    // SELECIONA O FORMULÁRIO DE FLASHCARD
    const form = document.getElementById("flashcardForm");
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
// ***************************************************************************************************************
// ***************************************************************************************************************
// ***************************************************************************************************************


// FUNCIONANDO 
// FUNÇÃO PARA EDITAR FLASHCARD
function editarFlashcard(flashcardId) {
    // BUSCAR OS DADOS ATUAIS DO FLASHCARD PARA PREENCHER O FORMULÁRIO DE EDIÇÃO
    fetch(`./api/independente/flashcards/${flashcardId}`, {
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
// ***************************************************************************************************************
// ***************************************************************************************************************
// ***************************************************************************************************************

// ALIAS PARA MANTER COMPATIBILIDADE
function criarFlashcard() {
    adicionarFlashcard();
}
// ***************************************************************************************************************
// ***************************************************************************************************************
// ***************************************************************************************************************

// ALIAS PARA MANTER COMPATIBILIDADE
function removerFlashcard(flashcardId) {
    excluirFlashcard(flashcardId);
}
// ***************************************************************************************************************
// ***************************************************************************************************************
// ***************************************************************************************************************


//FUNCIONANDO 
// FUNÇÃO PARA EXCLUIR FLASHCARD
function excluirFlashcard(flashcardId) {
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

// ***************************************************************************************************************
// ***************************************************************************************************************
// ***************************************************************************************************************


// FUNCIONANDO 
// FUNÇÃO PARA ABRIR MODAL DE EDIÇÃO DE FLASHCARD (APENAS NOME E DESCRIÇÃO)
function abrirModalEdicaoFlashcard(flashcard) {
    // CRIA O HTML DO MODAL DINAMICAMENTE PARA EDIÇÃO
    const modalHtml = `
        <div class="modal fade" id="editFlashcardModal" tabindex="-1">
            <div class="modal-dialog">
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

// FUNCIONANDO 
// FUNÇÃO PARA SALVAR EDIÇÃO DO FLASHCARD (APENAS NOME E DESCRIÇÃO)
function salvarEdicaoFlashcard(flashcardId) {
    // COLETAR O VALOR DO CAMPO DE TÍTULO
    const titulo = document.getElementById("editTitulo").value;
    // COLETAR O VALOR DO CAMPO DE DESCRIÇÃO
    const descricao = document.getElementById("editDescricao").value;

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
            descricao: descricao
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
// ***************************************************************************************************************
// ***************************************************************************************************************
// ***************************************************************************************************************

// FUNCIONANDO --> VAI PRA ABERTURA DO MODAL
// FUNÇÃO PARA GERENCIAR PERGUNTAS DO FLASHCARD
function gerenciarPerguntas(flashcardId) {
    // BUSCAR OS DADOS ATUAIS DO FLASHCARD PARA PREENCHER O MODAL DE GERENCIAMENTO
    fetch(`./api/independente/flashcards/${flashcardId}`, {
        method: "GET",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector("meta[name=\"csrf-token\"]")?.getAttribute("content") || "",
            "Accept": "application/json"
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
            abrirModalGerenciarPerguntas(data.flashcard);
        } else {
            mostrarErro(data.message || "Erro ao buscar flashcard");
        }
    })
    .catch(error => {
        console.error("Erro ao buscar flashcard:", error);
        mostrarErro("Erro ao buscar flashcard: " + error.message);
    });
}
// ***************************************************************************************************************
// ***************************************************************************************************************
// ***************************************************************************************************************


// FUNCIONANDO --> O MODAL ABRE CORRETAMENTE
// FUNÇÃO PARA ABRIR MODAL DE GERENCIAMENTO DE PERGUNTAS
function abrirModalGerenciarPerguntas(flashcard) {
    // GARANTE QUE O ARRAY DE PERGUNTAS EXISTE, MESMO QUE VAZIO
    const perguntas = flashcard.perguntas || [];
    
    // CRIA O HTML DO MODAL DINAMICAMENTE PARA GERENCIAMENTO DE PERGUNTAS
    const modalHtml = `
        <div class="modal fade" id="gerenciarPerguntasModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Gerenciar Perguntas - ${flashcard.titulo}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6>Perguntas e Respostas (${perguntas.length})</h6>
                            <button type="button" class="btn btn-success btn-sm" onclick="adicionarPerguntaDoGerenciador(${flashcard.id_flashcard})">
                                <i class="fas fa-plus"></i> Nova Pergunta
                            </button>
                        </div>
                        <div id="perguntasGerenciarContainer">
                            ${perguntas.length > 0 ? perguntas.map((pergunta, index) => `
                                <div class="pergunta-gerenciar-item mb-4 border p-3 rounded" data-id="${pergunta.id_pergunta_flashcard}">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-0">Pergunta ${index + 1}</h6>
                                        <div>
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="excluirPergunta(${pergunta.id_pergunta_flashcard})">
                                                <i class="fas fa-trash"></i> Excluir
                                            </button>
                                        </div>
                                    </div>
                                    <div class="pergunta-conteudo">
                                        <div class="mb-2">
                                            <strong>Pergunta:</strong>
                                            <p class="mb-1">${pergunta.pergunta}</p>
                                        </div>
                                        <div>
                                            <strong>Resposta:</strong>
                                            <p class="mb-0">${pergunta.resposta}</p>
                                        </div>
                                    </div>
                                </div>
                            `).join("") : `
                                <div class="text-center py-4">
                                    <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Nenhuma pergunta criada ainda</p>
                                    <button type="button" class="btn btn-primary" onclick="adicionarPerguntaDoGerenciador(${flashcard.id_flashcard})">
                                        Criar primeira pergunta
                                    </button>
                                </div>
                            `}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // REMOVE QUALQUER MODAL DE GERENCIAMENTO EXISTENTE PARA EVITAR DUPLICAÇÃO
    const existingModal = document.getElementById("gerenciarPerguntasModal");
    if (existingModal) {
        existingModal.remove();
    }

    // ADICIONA O HTML DO MODAL AO CORPO DO DOCUMENTO
    document.body.insertAdjacentHTML("beforeend", modalHtml);
    // CRIA UMA NOVA INSTÂNCIA DO MODAL DO BOOTSTRAP 5 E O MOSTRA
    const modal = new bootstrap.Modal(document.getElementById("gerenciarPerguntasModal"));
    modal.show();
}
// ***************************************************************************************************************
// ***************************************************************************************************************
// ***************************************************************************************************************


// O GERENCIADO ABRE NORMALMENTE
// FUNÇÕES DE ADICIOANR PERGUNTA E REMOVER PERGUNTA NÃO FUNCIONAM
// DAQUI PRA BAIXO, ACHO QUE TEM MUITAS FUNÇÕES REDUNDANTES

// FUNÇÃO UNIFICADA PARA ADICIONAR PERGUNTA (substitui várias funções redundantes)
function abrirModalAdicionarPergunta(flashcardId) {
    const modalHtml = `
        <div class="modal fade" id="adicionarPerguntaModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Adicionar Pergunta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="perguntaForm">
                            <input type="hidden" id="flashcardId" value="${flashcardId}">
                            <div class="mb-3">
                                <label for="perguntaTexto" class="form-label">Pergunta</label>
                                <textarea class="form-control" id="perguntaTexto" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="respostaTexto" class="form-label">Resposta</label>
                                <textarea class="form-control" id="respostaTexto" rows="3" required></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="salvarPergunta()">Salvar Pergunta</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Remove modal existente se houver
    const existingModal = document.getElementById("adicionarPerguntaModal");
    if (existingModal) existingModal.remove();

    document.body.insertAdjacentHTML("beforeend", modalHtml);
    const modal = new bootstrap.Modal(document.getElementById("adicionarPerguntaModal"));
    modal.show();
}

// FUNÇÃO PARA SALVAR PERGUNTA (unificada)
function salvarPergunta() {
    const flashcardId = document.getElementById("flashcardId").value;
    const pergunta = document.getElementById("perguntaTexto").value.trim();
    const resposta = document.getElementById("respostaTexto").value.trim();

    if (!pergunta || !resposta) {
        mostrarErro("Preencha tanto a pergunta quanto a resposta");
        return;
    }

    fetch(`./api/independente/perguntas`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector("meta[name=\"csrf-token\"]")?.getAttribute("content") || "",
            "Accept": "application/json"
        },
        body: JSON.stringify({
            id_flashcard: flashcardId,
            pergunta: pergunta,
            resposta: resposta
        })
    })
    .then(handleResponse)
    .then(data => {
        if (data.success) {
            mostrarSucesso("Pergunta adicionada com sucesso!");
            const modal = bootstrap.Modal.getInstance(document.getElementById("adicionarPerguntaModal"));
            modal.hide();
            
            // Atualiza a view de flashcards
            carregarFlashcards(topicoSelecionado);
            
            // Se o modal de gerenciamento estiver aberto, recarrega ele também
            if (document.getElementById("gerenciarPerguntasModal")) {
                gerenciarPerguntas(flashcardId);
            }
        }
    })
    .catch(handleError);
}

// FUNÇÃO MELHORADA PARA GERENCIAR PERGUNTAS
function gerenciarPerguntas(flashcardId) {
    fetch(`./api/independente/flashcards/${flashcardId}`, {
        method: "GET",
        headers: getDefaultHeaders()
    })
    .then(handleResponse)
    .then(data => {
        if (data.success) {
            renderizarModalGerenciamento(data.flashcard);
        }
    })
    .catch(handleError);
}

function renderizarModalGerenciamento(flashcard) {
    const perguntas = flashcard.perguntas || [];
    
    const modalHtml = `
        <div class="modal fade" id="gerenciarPerguntasModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Perguntas: ${flashcard.titulo}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span>Total: ${perguntas.length} perguntas</span>
                            <button class="btn btn-sm btn-primary" onclick="abrirModalAdicionarPergunta(${flashcard.id_flashcard})">
                                <i class="fas fa-plus"></i> Nova Pergunta
                            </button>
                        </div>
                        <div id="listaPerguntas">
                            ${perguntas.length > 0 ? perguntas.map((pergunta, index) => `
                                <div class="card mb-2" data-id="${pergunta.id_pergunta}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="card-title">Pergunta ${index + 1}</h6>
                                                <p class="card-text"><strong>P:</strong> ${pergunta.pergunta}</p>
                                                <p class="card-text"><strong>R:</strong> ${pergunta.resposta}</p>
                                            </div>
                                            <button class="btn btn-sm btn-danger" onclick="confirmarExclusaoPergunta(${pergunta.id_pergunta}, ${flashcard.id_flashcard})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `).join('') : '<p class="text-center text-muted">Nenhuma pergunta cadastrada</p>'}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Remove modal existente
    const existingModal = document.getElementById("gerenciarPerguntasModal");
    if (existingModal) existingModal.remove();

    document.body.insertAdjacentHTML("beforeend", modalHtml);
    const modal = new bootstrap.Modal(document.getElementById("gerenciarPerguntasModal"));
    modal.show();
}

// FUNÇÃO PARA CONFIRMAR EXCLUSÃO DE PERGUNTA
function confirmarExclusaoPergunta(perguntaId, flashcardId) {
    if (confirm("Tem certeza que deseja excluir esta pergunta?")) {
        excluirPergunta(perguntaId, flashcardId);
    }
}

// FUNÇÃO MELHORADA PARA EXCLUIR PERGUNTA
function excluirPergunta(perguntaId, flashcardId) {
    fetch(`./api/independente/perguntas/${perguntaId}`, {
        method: "DELETE",
        headers: getDefaultHeaders()
    })
    .then(handleResponse)
    .then(data => {
        if (data.success) {
            mostrarSucesso("Pergunta excluída com sucesso!");
            
            // Remove o item da lista
            const item = document.querySelector(`[data-id="${perguntaId}"]`);
            if (item) item.remove();
            
            // Atualiza contador
            const total = document.querySelectorAll("#listaPerguntas .card").length;
            document.querySelector("#gerenciarPerguntasModal .modal-body span").textContent = `Total: ${total} perguntas`;
            
            // Se não houver mais perguntas, mostra mensagem
            if (total === 0) {
                document.getElementById("listaPerguntas").innerHTML = '<p class="text-center text-muted">Nenhuma pergunta cadastrada</p>';
            }
        }
    })
    .catch(handleError);
}

// FUNÇÕES AUXILIARES
function getDefaultHeaders() {
    return {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector("meta[name=\"csrf-token\"]")?.getAttribute("content") || "",
        "Accept": "application/json"
    };
}

function handleResponse(response) {
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
    return response.json();
}

function handleError(error) {
    console.error("Erro:", error);
    mostrarErro(error.message || "Ocorreu um erro");
}