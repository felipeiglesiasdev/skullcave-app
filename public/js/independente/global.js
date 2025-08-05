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


