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
                    <button class="btn-action btn-delete" onclick="removerFlashcard(${flashcard.id_flashcard})" title="Excluir">
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
    // CRIA O HTML DO MODAL DINAMICAMENTE PARA CRIAÇÃO DE PERGUNTA
    const modalHtml = `
        <div class="modal fade" id="criarPerguntaModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Adicionar Pergunta e Resposta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="criarPerguntaForm">
                            <div class="mb-3">
                                <label for="novaPergunta" class="form-label">Pergunta</label>
                                <input type="text" class="form-control" id="novaPergunta" required>
                            </div>
                            <div class="mb-3">
                                <label for="novaResposta" class="form-label">Resposta</label>
                                <textarea class="form-control" id="novaResposta" rows="3" required></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="salvarNovaPergunta(${flashcardId})">Salvar Pergunta</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // REMOVE QUALQUER MODAL DE CRIAÇÃO EXISTENTE PARA EVITAR DUPLICAÇÃO
    const existingModal = document.getElementById("criarPerguntaModal");
    if (existingModal) {
        existingModal.remove();
    }

    // ADICIONA O HTML DO MODAL AO CORPO DO DOCUMENTO
    document.body.insertAdjacentHTML("beforeend", modalHtml);
    // CRIA UMA NOVA INSTÂNCIA DO MODAL DO BOOTSTRAP 5 E O MOSTRA
    const modal = new bootstrap.Modal(document.getElementById("criarPerguntaModal"));
    modal.show();
}

// ===== FUNÇÃO PARA SALVAR NOVA PERGUNTA =====
function salvarNovaPergunta(flashcardId) {
    // OBTÉM OS VALORES DOS CAMPOS DO FORMULÁRIO
    const pergunta = document.getElementById("novaPergunta").value.trim();
    const resposta = document.getElementById("novaResposta").value.trim();

    // VALIDAÇÃO BÁSICA DOS CAMPOS
    if (!pergunta) {
        mostrarErro("Pergunta é obrigatória");
        return;
    }
    if (!resposta) {
        mostrarErro("Resposta é obrigatória");
        return;
    }

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
            
            // FECHA O MODAL
            const modal = bootstrap.Modal.getInstance(document.getElementById("criarPerguntaModal"));
            modal.hide();
            
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



// ===== FUNÇÃO PARA GERENCIAR PERGUNTAS DO FLASHCARD =====
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

// ===== FUNÇÃO PARA ABRIR MODAL DE GERENCIAMENTO DE PERGUNTAS =====
function abrirModalGerenciarPerguntas(flashcard) {
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
                            <h6>Perguntas e Respostas (${flashcard.perguntas.length})</h6>
                            <button type="button" class="btn btn-success btn-sm" onclick="adicionarNovaPerguntaGerenciar(${flashcard.id_flashcard})">
                                <i class="fas fa-plus"></i> Nova Pergunta
                            </button>
                        </div>
                        <div id="perguntasGerenciarContainer">
                            ${flashcard.perguntas.length > 0 ? flashcard.perguntas.map((pergunta, index) => `
                                <div class="pergunta-gerenciar-item mb-4 border p-3 rounded" data-id="${pergunta.id_pergunta_flashcard}">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-0">Pergunta ${index + 1}</h6>
                                        <div>
                                            <button type="button" class="btn btn-outline-primary btn-sm me-2" onclick="editarPerguntaItem(${pergunta.id_pergunta_flashcard})">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="excluirPerguntaItem(${pergunta.id_pergunta_flashcard})">
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
                                    <button type="button" class="btn btn-primary" onclick="adicionarNovaPerguntaGerenciar(${flashcard.id_flashcard})">
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

// ===== FUNÇÃO PARA ADICIONAR NOVA PERGUNTA NO GERENCIAMENTO =====
function adicionarNovaPerguntaGerenciar(flashcardId) {
    // FECHA O MODAL DE GERENCIAMENTO TEMPORARIAMENTE
    const gerenciarModal = bootstrap.Modal.getInstance(document.getElementById("gerenciarPerguntasModal"));
    gerenciarModal.hide();
    
    // ABRE O MODAL DE CRIAÇÃO DE PERGUNTA
    criarPerguntaResposta(flashcardId);
    
    // QUANDO O MODAL DE CRIAÇÃO FECHAR, REABRE O DE GERENCIAMENTO
    document.getElementById("criarPerguntaModal").addEventListener("hidden.bs.modal", function() {
        // RECARREGA O MODAL DE GERENCIAMENTO COM DADOS ATUALIZADOS
        gerenciarPerguntas(flashcardId);
    }, { once: true });
}

// ===== FUNÇÃO PARA EDITAR PERGUNTA INDIVIDUAL =====
function editarPerguntaItem(perguntaId) {
    // IMPLEMENTAR EDIÇÃO INDIVIDUAL DE PERGUNTA
    mostrarInfo("Funcionalidade de edição individual em desenvolvimento");
}

// ===== FUNÇÃO PARA EXCLUIR PERGUNTA INDIVIDUAL =====
function excluirPerguntaItem(perguntaId) {
    // CONFIRMAR AÇÃO DE EXCLUSÃO
    if (!confirm("Tem certeza que deseja excluir esta pergunta?")) {
        return;
    }
    
    // IMPLEMENTAR EXCLUSÃO INDIVIDUAL DE PERGUNTA
    mostrarInfo("Funcionalidade de exclusão individual em desenvolvimento");
}
