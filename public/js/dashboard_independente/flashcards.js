// FUNÇÕES PARA FLASHCARDS

// FUNÇÃO PARA ABRIR MODAL DE FLASHCARD
function openFlashcardModal(topicoId) {
    const topicoIdInput = document.getElementById("topico_id");
    if (topicoIdInput) {
        topicoIdInput.value = topicoId;
    }
    openModal("flashcardModal");
}

// FUNÇÃO PARA CRIAR FLASHCARD
async function criarFlashcard(e) {
    e.preventDefault();

    console.log("=== INICIANDO CRIAÇÃO DE FLASHCARD ===");

    const form = e.target;
    const titulo = form.querySelector("#flashcard_titulo").value;
    const descricao = form.querySelector("#flashcard_descricao").value;
    const topicoId = form.querySelector("#topico_id").value;

    console.log("Título do Flashcard:", titulo);
    console.log("Descrição do Flashcard:", descricao);
    console.log("ID do Tópico:", topicoId);

    const csrfToken = document.querySelector("meta[name=\"csrf-token\"]").getAttribute("content");

    try {
        console.log("Fazendo requisição para criar flashcard...");

        const response = await fetch("./api/flashcards", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "X-Requested-With": "XMLHttpRequest"
            },
            body: JSON.stringify({
                titulo: titulo,
                descricao: descricao,
                topico_id: topicoId
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
            mostrarPopupSucesso("Flashcard criado com sucesso!");
            closeModal("flashcardModal");
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

// FUNÇÃO PARA REMOVER FLASHCARD COM POPUP DE CONFIRMAÇÃO
function removerFlashcard(flashcardId) {
    mostrarPopupConfirmacao(
        "Remover Flashcard",
        "Tem certeza que deseja remover este flashcard? Esta ação não pode ser desfeita e todas as perguntas serão perdidas.",
        function() {
            executarRemocaoFlashcard(flashcardId);
        }
    );
}

// FUNÇÃO QUE EXECUTA A REMOÇÃO DO FLASHCARD
async function executarRemocaoFlashcard(flashcardId) {
    console.log("=== INICIANDO REMOÇÃO DE FLASHCARD ===");
    console.log("ID do flashcard:", flashcardId);
    
    const csrfToken = document.querySelector("meta[name=\"csrf-token\"]").getAttribute("content");
    
    try {
        console.log("Fazendo requisição de remoção...");
        
        const response = await fetch(`./api/flashcards/${flashcardId}`, {
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
            mostrarPopupSucesso("Flashcard removido com sucesso!");
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
