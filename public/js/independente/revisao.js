// ===== FUNÇÕES DE REVISÃO DE FLASHCARDS =====

// VARIÁVEIS GLOBAIS PARA CONTROLE DA REVISÃO
let flashcardAtualRevisao = null;       // FLASHCARD SENDO REVISADO ATUALMENTE
let perguntasRevisao = [];              // ARRAY COM AS PERGUNTAS DO FLASHCARD
let perguntaAtualIndex = 0;             // ÍNDICE DA PERGUNTA ATUAL
let respostasCorretas = 0;              // CONTADOR DE RESPOSTAS CORRETAS
let respostasIncorretas = 0;            // CONTADOR DE RESPOSTAS INCORRETAS
let mostrandoResposta = false;          // FLAG PARA CONTROLAR SE A RESPOSTA ESTÁ SENDO MOSTRADA

// FUNÇÃO PARA INICIAR A REVISÃO DE UM FLASHCARD
function iniciarRevisao(flashcardId) {
    // BUSCAR OS DADOS DO FLASHCARD PARA INICIAR A REVISÃO
    fetch(`./api/independente/flashcards/${flashcardId}`, {
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
        if (data.success && data.flashcard.perguntas.length > 0) {
            // SE A OPERAÇÃO FOI BEM-SUCEDIDA E HÁ PERGUNTAS, INICIA A REVISÃO
            flashcardAtualRevisao = data.flashcard;
            perguntasRevisao = data.flashcard.perguntas;
            perguntaAtualIndex = 0;
            respostasCorretas = 0;
            respostasIncorretas = 0;
            mostrandoResposta = false;
            
            // ABRE O MODAL DE REVISÃO
            abrirModalRevisao();
        } else {
            // MOSTRA ERRO SE NÃO HÁ PERGUNTAS NO FLASHCARD
            mostrarErro("Este flashcard não possui perguntas para revisão");
        }
    })
    .catch(error => {
        // TRATA ERROS DE REDE OU OUTROS ERROS DURANTE A REQUISIÇÃO
        console.error("Erro ao carregar flashcard para revisão:", error);
        // MOSTRA UMA MENSAGEM DE ERRO MAIS DETALHADA
        mostrarErro("Erro ao carregar flashcard para revisão: " + error.message);
    });
}

// FUNÇÃO PARA ABRIR O MODAL DE REVISÃO
function abrirModalRevisao() {
    // CRIA O HTML DO MODAL DE REVISÃO DINAMICAMENTE
    const modalHtml = `
        <div class="modal fade" id="revisaoModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Revisão: ${flashcardAtualRevisao.titulo}</h5>
                        <button type="button" class="btn-close" onclick="encerrarRevisao()"></button>
                    </div>
                    <div class="modal-body">
                        <div class="revisao-header mb-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" style="width: 0%" id="progressoRevisao"></div>
                                    </div>
                                    <small class="text-muted">Pergunta <span id="perguntaAtual">1</span> de <span id="totalPerguntas">${perguntasRevisao.length}</span></small>
                                </div>
                                <div class="col-md-6 text-end">
                                    <span class="badge bg-success me-2">Corretas: <span id="contadorCorretas">0</span></span>
                                    <span class="badge bg-danger">Incorretas: <span id="contadorIncorretas">0</span></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="revisao-content">
                            <div class="pergunta-container mb-4">
                                <h6 class="mb-3">Pergunta:</h6>
                                <div class="pergunta-texto p-3 bg-light rounded" id="perguntaTexto">
                                    ${perguntasRevisao[0].pergunta}
                                </div>
                            </div>
                            
                            <div class="resposta-container" id="respostaContainer" style="display: none;">
                                <h6 class="mb-3">Resposta:</h6>
                                <div class="resposta-texto p-3 bg-info bg-opacity-10 rounded" id="respostaTexto">
                                    ${perguntasRevisao[0].resposta}
                                </div>
                            </div>
                            
                            <div class="avaliacao-container mt-4" id="avaliacaoContainer" style="display: none;">
                                <h6 class="mb-3">Como você se saiu?</h6>
                                <div class="d-flex gap-2 justify-content-center">
                                    <button class="btn btn-danger" onclick="marcarIncorreta()">
                                        <i class="fas fa-times"></i> Incorreta
                                    </button>
                                    <button class="btn btn-success" onclick="marcarCorreta()">
                                        <i class="fas fa-check"></i> Correta
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="encerrarRevisao()">Encerrar Revisão</button>
                        <button type="button" class="btn btn-primary" id="btnMostrarResposta" onclick="mostrarResposta()">
                            Mostrar Resposta
                        </button>
                        <button type="button" class="btn btn-primary" id="btnProximaPergunta" onclick="proximaPergunta()" style="display: none;">
                            Próxima Pergunta
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // REMOVE QUALQUER MODAL DE REVISÃO EXISTENTE PARA EVITAR DUPLICAÇÃO
    const existingModal = document.getElementById("revisaoModal");
    if (existingModal) {
        existingModal.remove();
    }

    // ADICIONA O HTML DO MODAL AO CORPO DO DOCUMENTO
    document.body.insertAdjacentHTML("beforeend", modalHtml);
    
    // CRIA UMA NOVA INSTÂNCIA DO MODAL DO BOOTSTRAP 5 E O MOSTRA
    const modal = new bootstrap.Modal(document.getElementById("revisaoModal"));
    modal.show();
    
    // ATUALIZA O PROGRESSO INICIAL
    atualizarProgressoRevisao();
}

// FUNÇÃO PARA MOSTRAR A RESPOSTA DA PERGUNTA ATUAL
function mostrarResposta() {
    // VERIFICA SE A RESPOSTA JÁ ESTÁ SENDO MOSTRADA
    if (mostrandoResposta) return;
    
    // MARCA QUE A RESPOSTA ESTÁ SENDO MOSTRADA
    mostrandoResposta = true;
    
    // MOSTRA O CONTÊINER DA RESPOSTA
    document.getElementById("respostaContainer").style.display = "block";
    // MOSTRA O CONTÊINER DE AVALIAÇÃO
    document.getElementById("avaliacaoContainer").style.display = "block";
    
    // ESCONDE O BOTÃO "MOSTRAR RESPOSTA"
    document.getElementById("btnMostrarResposta").style.display = "none";
}

// FUNÇÃO PARA MARCAR A RESPOSTA COMO CORRETA
function marcarCorreta() {
    // INCREMENTA O CONTADOR DE RESPOSTAS CORRETAS
    respostasCorretas++;
    // ATUALIZA O DISPLAY DO CONTADOR
    document.getElementById("contadorCorretas").textContent = respostasCorretas;
    
    // PROCESSA A PRÓXIMA PERGUNTA OU FINALIZA A REVISÃO
    processarProximaEtapa();
}

// FUNÇÃO PARA MARCAR A RESPOSTA COMO INCORRETA
function marcarIncorreta() {
    // INCREMENTA O CONTADOR DE RESPOSTAS INCORRETAS
    respostasIncorretas++;
    // ATUALIZA O DISPLAY DO CONTADOR
    document.getElementById("contadorIncorretas").textContent = respostasIncorretas;
    
    // PROCESSA A PRÓXIMA PERGUNTA OU FINALIZA A REVISÃO
    processarProximaEtapa();
}

// FUNÇÃO PARA PROCESSAR A PRÓXIMA ETAPA DA REVISÃO
function processarProximaEtapa() {
    // ESCONDE O CONTÊINER DE AVALIAÇÃO
    document.getElementById("avaliacaoContainer").style.display = "none";
    
    // VERIFICA SE HÁ MAIS PERGUNTAS
    if (perguntaAtualIndex < perguntasRevisao.length - 1) {
        // SE HÁ MAIS PERGUNTAS, MOSTRA O BOTÃO "PRÓXIMA PERGUNTA"
        document.getElementById("btnProximaPergunta").style.display = "inline-block";
    } else {
        // SE NÃO HÁ MAIS PERGUNTAS, FINALIZA A REVISÃO
        finalizarRevisao();
    }
}

// FUNÇÃO PARA AVANÇAR PARA A PRÓXIMA PERGUNTA
function proximaPergunta() {
    // INCREMENTA O ÍNDICE DA PERGUNTA ATUAL
    perguntaAtualIndex++;
    // MARCA QUE A RESPOSTA NÃO ESTÁ SENDO MOSTRADA
    mostrandoResposta = false;
    
    // OBTÉM A PRÓXIMA PERGUNTA
    const proximaPergunta = perguntasRevisao[perguntaAtualIndex];
    
    // ATUALIZA O TEXTO DA PERGUNTA
    document.getElementById("perguntaTexto").textContent = proximaPergunta.pergunta;
    // ATUALIZA O TEXTO DA RESPOSTA
    document.getElementById("respostaTexto").textContent = proximaPergunta.resposta;
    
    // ESCONDE O CONTÊINER DA RESPOSTA
    document.getElementById("respostaContainer").style.display = "none";
    // ESCONDE O CONTÊINER DE AVALIAÇÃO
    document.getElementById("avaliacaoContainer").style.display = "none";
    
    // MOSTRA O BOTÃO "MOSTRAR RESPOSTA"
    document.getElementById("btnMostrarResposta").style.display = "inline-block";
    // ESCONDE O BOTÃO "PRÓXIMA PERGUNTA"
    document.getElementById("btnProximaPergunta").style.display = "none";
    
    // ATUALIZA O PROGRESSO DA REVISÃO
    atualizarProgressoRevisao();
}

// FUNÇÃO PARA ATUALIZAR O PROGRESSO DA REVISÃO
function atualizarProgressoRevisao() {
    // CALCULA A PORCENTAGEM DE PROGRESSO
    const progresso = ((perguntaAtualIndex + 1) / perguntasRevisao.length) * 100;
    
    // ATUALIZA A BARRA DE PROGRESSO
    document.getElementById("progressoRevisao").style.width = progresso + "%";
    // ATUALIZA O NÚMERO DA PERGUNTA ATUAL
    document.getElementById("perguntaAtual").textContent = perguntaAtualIndex + 1;
}

// FUNÇÃO PARA FINALIZAR A REVISÃO
function finalizarRevisao() {
    // CALCULA A PORCENTAGEM DE ACERTOS
    const totalPerguntas = perguntasRevisao.length;
    const porcentagemAcertos = totalPerguntas > 0 ? Math.round((respostasCorretas / totalPerguntas) * 100) : 0;
    
    // DETERMINA A MENSAGEM DE FEEDBACK BASEADA NA PERFORMANCE
    let mensagemFeedback = "";
    let classeAlerta = "";
    
    if (porcentagemAcertos >= 80) {
        mensagemFeedback = "Excelente! Você domina bem este conteúdo!";
        classeAlerta = "alert-success";
    } else if (porcentagemAcertos >= 60) {
        mensagemFeedback = "Bom trabalho! Continue praticando para melhorar ainda mais.";
        classeAlerta = "alert-warning";
    } else {
        mensagemFeedback = "Continue estudando! A prática leva à perfeição.";
        classeAlerta = "alert-danger";
    }
    
    // CRIA O HTML DO RESULTADO FINAL
    const resultadoHtml = `
        <div class="text-center">
            <h4 class="mb-4">Revisão Concluída!</h4>
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5>${respostasCorretas}</h5>
                            <small>Corretas</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h5>${respostasIncorretas}</h5>
                            <small>Incorretas</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5>${porcentagemAcertos}%</h5>
                            <small>Acertos</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="alert ${classeAlerta}" role="alert">
                ${mensagemFeedback}
            </div>
        </div>
    `;
    
    // SUBSTITUI O CONTEÚDO DO MODAL PELO RESULTADO
    document.querySelector("#revisaoModal .modal-body").innerHTML = resultadoHtml;
    
    // ATUALIZA OS BOTÕES DO FOOTER
    document.querySelector("#revisaoModal .modal-footer").innerHTML = `
        <button type="button" class="btn btn-success" onclick="reiniciarRevisao()">
            <i class="fas fa-redo"></i> Revisar Novamente
        </button>
        <button type="button" class="btn btn-secondary" onclick="encerrarRevisao()">
            Fechar
        </button>
    `;
}

// FUNÇÃO PARA REINICIAR A REVISÃO DO MESMO FLASHCARD
function reiniciarRevisao() {
    // REINICIA AS VARIÁVEIS DE CONTROLE
    perguntaAtualIndex = 0;
    respostasCorretas = 0;
    respostasIncorretas = 0;
    mostrandoResposta = false;
    
    // FECHA O MODAL ATUAL
    const modal = bootstrap.Modal.getInstance(document.getElementById("revisaoModal"));
    modal.hide();
    
    // ABRE UM NOVO MODAL DE REVISÃO
    setTimeout(() => {
        abrirModalRevisao();
    }, 300);
}

// FUNÇÃO PARA ENCERRAR A REVISÃO
function encerrarRevisao() {
    // CONFIRMA SE O USUÁRIO DESEJA REALMENTE ENCERRAR
    if (perguntaAtualIndex < perguntasRevisao.length - 1 && respostasCorretas + respostasIncorretas > 0) {
        if (!confirm("Tem certeza que deseja encerrar a revisão? Seu progresso será perdido.")) {
            return;
        }
    }
    
    // FECHA O MODAL DE REVISÃO
    const modal = bootstrap.Modal.getInstance(document.getElementById("revisaoModal"));
    if (modal) {
        modal.hide();
    }
    
    // LIMPA AS VARIÁVEIS DE CONTROLE
    flashcardAtualRevisao = null;
    perguntasRevisao = [];
    perguntaAtualIndex = 0;
    respostasCorretas = 0;
    respostasIncorretas = 0;
    mostrandoResposta = false;
    
    // REMOVE O MODAL DO DOM APÓS UM PEQUENO ATRASO
    setTimeout(() => {
        const modalElement = document.getElementById("revisaoModal");
        if (modalElement) {
            modalElement.remove();
        }
    }, 300);
}
