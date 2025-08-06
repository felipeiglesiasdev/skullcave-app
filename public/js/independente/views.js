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
    
    // LIMPA O BREADCRUMB
    atualizarBreadcrumb("", null);
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
    
    
    // ATUALIZA O BREADCRUMB APENAS COM A DISCIPLINA
    const disciplinaCard = document.querySelector(`[data-id="${disciplinaSelecionada}"]`);
    if (disciplinaCard) {
        const nomeDisciplina = disciplinaCard.querySelector("h6").textContent;
        atualizarBreadcrumb(nomeDisciplina);
    }
}

