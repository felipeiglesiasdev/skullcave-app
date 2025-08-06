// ===== FUNÇÕES UTILITÁRIAS =====
// ***************************************************************************************************************
// ***************************************************************************************************************
// ***************************************************************************************************************


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
// ***************************************************************************************************************
// ***************************************************************************************************************
// ***************************************************************************************************************


// FUNÇÃO PARA ATUALIZAR AS ESTATÍSTICAS NA INTERFACE
function atualizarEstatisticas(estatisticas) {
    // ATUALIZA O NÚMERO TOTAL DE DISCIPLINAS
    const totalDisciplinas = document.getElementById("totalDisciplinas");
    if (totalDisciplinas) {
        totalDisciplinas.textContent = estatisticas.totalDisciplinas || 0;
    }
    
    // ATUALIZA O NÚMERO TOTAL DE TÓPICOS
    const totalTopicos = document.getElementById("totalTopicos");
    if (totalTopicos) {
        totalTopicos.textContent = estatisticas.totalTopicos || 0;
    }
    
    // ATUALIZA O NÚMERO TOTAL DE FLASHCARDS
    const totalFlashcards = document.getElementById("totalFlashcards");
    if (totalFlashcards) {
        totalFlashcards.textContent = estatisticas.totalFlashcards || 0;
    }
    
    // ATUALIZA O NÚMERO TOTAL DE PERGUNTAS
    const totalPerguntas = document.getElementById("totalPerguntas");
    if (totalPerguntas) {
        totalPerguntas.textContent = estatisticas.totalPerguntas || 0;
    }
}
// ***************************************************************************************************************
// ***************************************************************************************************************
// ***************************************************************************************************************


// FUNÇÃO PARA MOSTRAR MENSAGEM DE SUCESSO
function mostrarSucesso(mensagem) {
    // VERIFICA SE A FUNÇÃO showToast EXISTE (DEFINIDA EM toast.js)
    if (typeof showToast === "function") {
        // CHAMA A FUNÇÃO showToast COM TIPO 'success'
        showToast(mensagem, "success");
    } else {
        // SE NÃO EXISTE, USA UM ALERT SIMPLES
        alert("Sucesso: " + mensagem);
    }
}
// ***************************************************************************************************************
// ***************************************************************************************************************
// ***************************************************************************************************************


// FUNÇÃO PARA MOSTRAR MENSAGEM DE ERRO
function mostrarErro(mensagem) {
    // VERIFICA SE A FUNÇÃO showToast EXISTE (DEFINIDA EM toast.js)
    if (typeof showToast === "function") {
        // CHAMA A FUNÇÃO showToast COM TIPO 'error'
        showToast(mensagem, "error");
    } else {
        // SE NÃO EXISTE, USA UM ALERT SIMPLES
        alert("Erro: " + mensagem);
    }
}
// ***************************************************************************************************************
// ***************************************************************************************************************
// ***************************************************************************************************************


// FUNÇÃO PARA MOSTRAR MENSAGEM DE AVISO
function mostrarAviso(mensagem) {
    // VERIFICA SE A FUNÇÃO showToast EXISTE (DEFINIDA EM toast.js)
    if (typeof showToast === "function") {
        // CHAMA A FUNÇÃO showToast COM TIPO 'warning'
        showToast(mensagem, "warning");
    } else {
        // SE NÃO EXISTE, USA UM ALERT SIMPLES
        alert("Aviso: " + mensagem);
    }
}
// ***************************************************************************************************************
// ***************************************************************************************************************
// ***************************************************************************************************************


// FUNÇÃO PARA MOSTRAR MENSAGEM DE INFORMAÇÃO
function mostrarInfo(mensagem) {
    // VERIFICA SE A FUNÇÃO showToast EXISTE (DEFINIDA EM toast.js)
    if (typeof showToast === "function") {
        // CHAMA A FUNÇÃO showToast COM TIPO 'info'
        showToast(mensagem, "info");
    } else {
        // SE NÃO EXISTE, USA UM ALERT SIMPLES
        alert("Info: " + mensagem);
    }
}
// ***************************************************************************************************************
// ***************************************************************************************************************
// ***************************************************************************************************************


// FUNÇÃO PARA FECHAR MODAL
function fecharModal(modalId) {
    // SELECIONA O MODAL PELO ID
    const modalElement = document.getElementById(modalId);
    // VERIFICA SE O MODAL EXISTE
    if (modalElement) {
        // OBTÉM A INSTÂNCIA DO MODAL DO BOOTSTRAP
        const modal = bootstrap.Modal.getInstance(modalElement);
        // VERIFICA SE A INSTÂNCIA EXISTE
        if (modal) {
            // FECHA O MODAL
            modal.hide();
        }
    } else {
        // REGISTRA UM ERRO NO CONSOLE SE O MODAL NÃO FOR ENCONTRADO
        console.error(`Modal '${modalId}' não encontrado`);
    }
}




