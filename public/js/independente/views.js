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

// FUNÇÃO PARA VOLTAR PARA A VIEW DE DISCIPLINAS
function voltarParaDisciplinas() {
    // LIMPA AS SELEÇÕES ATUAIS
    disciplinaSelecionada = null;
    topicoSelecionado = null;
    // REMOVE A CLASSE 'active' DE TODOS OS CARTÕES
    document.querySelectorAll(".disciplina-card, .topico-card").forEach(card => {
        card.classList.remove("active");
    });
    mostrarView("welcome-state");
    // DESATIVA OS BOTÕES DE ADICIONAR TÓPICO E FLASHCARD
    atualizarBotoesAcao(false, false);
}

// FUNÇÃO PARA VOLTAR PARA A VIEW DE TÓPICOS
function voltarParaTopicos() {
    // VERIFICA SE UMA DISCIPLINA ESTÁ SELECIONADA
    if (!disciplinaSelecionada) {
        voltarParaDisciplinas();
        return;
    }
    // LIMPA A SELEÇÃO DE TÓPICO
    topicoSelecionado = null;
    // REMOVE A CLASSE 'active' DE TODOS OS CARTÕES DE TÓPICO
    document.querySelectorAll(".topico-card").forEach(card => {
        card.classList.remove("active");
    });
    // MOSTRA A VIEW DE TÓPICOS
    mostrarView("topicos-view");
    atualizarBotoesAcao(true, false);
}

