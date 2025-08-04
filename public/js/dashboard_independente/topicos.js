// FUNÇÕES PARA TÓPICOS

// FUNÇÃO PARA ABRIR MODAL DE TÓPICO
function openTopicoModal(disciplinaId) {
    const disciplinaIdInput = document.getElementById("disciplina_id");
    if (disciplinaIdInput) {
        disciplinaIdInput.value = disciplinaId;
    }
    
    openModal("topicoModal");
}

// FUNÇÃO PARA CRIAR TÓPICO
async function criarTopico(e) {
    e.preventDefault();

    console.log("=== INICIANDO CRIAÇÃO DE TÓPICO ===");

    const form = e.target;
    const nome = form.querySelector("#topico_nome").value;
    const descricao = form.querySelector("#topico_descricao").value;
    const disciplinaId = form.querySelector("#disciplina_id").value;

    console.log("Nome do Tópico:", nome);
    console.log("Descrição do Tópico:", descricao);
    console.log("ID da Disciplina:", disciplinaId);

    const csrfToken = document.querySelector("meta[name=\"csrf-token\"]").getAttribute("content");

    try {
        console.log("Fazendo requisição para criar tópico...");

        const response = await fetch("./api/topicos", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "X-Requested-With": "XMLHttpRequest"
            },
            body: JSON.stringify({
                nome: nome,
                descricao: descricao,
                disciplina_id: disciplinaId
            })
        });

        console.log("Response status:", response.status);

        const responseText = await response.text();
        console.log("Response text:", responseText);

        let data;
        try {
            data = JSON.parse(responseText);
            console.log("Response JSON:", data);
        } catch (parseError) {
            console.error("Erro ao fazer parse do JSON:", parseError);
            mostrarPopupErro("Erro: Resposta não é JSON válida. Verifique o console.");
            return;
        }

        if (data.success) {
            mostrarPopupSucesso("Tópico criado com sucesso!");
            closeModal("topicoModal");
            form.reset();
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            mostrarPopupErro("Erro: " + data.message);
        }

    } catch (error) {
        console.error("Erro na requisição:", error);
        mostrarPopupErro("Erro de conexão: " + error.message);
    }
}

// FUNÇÃO PARA REMOVER TÓPICO COM POPUP DE CONFIRMAÇÃO
function removerTopico(topicoId) {
    mostrarPopupConfirmacao(
        "Remover Tópico",
        "Tem certeza que deseja remover este tópico? Esta ação não pode ser desfeita e todos os flashcards serão perdidos.",
        function() {
            executarRemocaoTopico(topicoId);
        }
    );
}

// FUNÇÃO QUE EXECUTA A REMOÇÃO DO TÓPICO
async function executarRemocaoTopico(topicoId) {
    console.log("=== INICIANDO REMOÇÃO DE TÓPICO ===");
    console.log("ID do tópico:", topicoId);
    
    const csrfToken = document.querySelector("meta[name=\"csrf-token\"]").getAttribute("content");
    
    try {
        console.log("Fazendo requisição de remoção...");
        
        const response = await fetch(`./api/topicos/${topicoId}`, {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "X-Requested-With": "XMLHttpRequest"
            }
        });
        
        console.log("Response status:", response.status);
        
        const responseText = await response.text();
        console.log("Response text:", responseText);
        
        let data;
        try {
            data = JSON.parse(responseText);
            console.log("Response JSON:", data);
        } catch (parseError) {
            console.error("Erro ao fazer parse do JSON:", parseError);
            mostrarPopupErro("Erro: Resposta não é JSON válida. Verifique o console.");
            return;
        }
        
        if (data.success) {
            mostrarPopupSucesso("Tópico removido com sucesso!");
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            mostrarPopupErro("Erro: " + data.message);
        }
        
    } catch (error) {
        console.error("Erro na requisição:", error);
        mostrarPopupErro("Erro de conexão: " + error.message);
    }
}