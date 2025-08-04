// FUNÇÕES PARA DISCIPLINAS

// FUNÇÃO PARA TOGGLE DE DISCIPLINA (EXPANDIR/RECOLHER TÓPICOS)
function toggleDisciplina(disciplinaId) {
    const topicosContainer = document.getElementById("topicos-" + disciplinaId);
    const toggleBtn = document.querySelector(`[onclick="toggleDisciplina(${disciplinaId})"]`);
    
    if (topicosContainer && toggleBtn) {
        topicosContainer.classList.toggle("show");
        toggleBtn.classList.toggle("active");
        console.log("Disciplina toggled:", disciplinaId);
    }
}

// FUNÇÃO PARA CRIAR DISCIPLINA
async function criarDisciplina(e) {
    e.preventDefault();
    
    console.log("=== INICIANDO CRIAÇÃO DE DISCIPLINA ===");
    
    const form = e.target;
    const nome = form.querySelector("#nome").value;
    const descricao = form.querySelector("#descricao").value;
    
    console.log("Nome:", nome);
    console.log("Descrição:", descricao);
    
    const csrfToken = document.querySelector("meta[name=\"csrf-token\"]").getAttribute("content");
    console.log("CSRF Token:", csrfToken);
    
    try {
        console.log("Fazendo requisição...");
        
        const response = await fetch("./api/disciplinas", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "X-Requested-With": "XMLHttpRequest"
            },
            body: JSON.stringify({
                nome: nome,
                descricao: descricao
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
            mostrarPopupSucesso("Disciplina criada com sucesso!");
            closeModal("disciplinaModal");
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

// FUNÇÃO PARA REMOVER DISCIPLINA COM POPUP DE CONFIRMAÇÃO
function removerDisciplina(disciplinaId) {
    mostrarPopupConfirmacao(
        "Remover Disciplina",
        "Tem certeza que deseja remover esta disciplina? Esta ação não pode ser desfeita e todos os tópicos e flashcards serão perdidos.",
        function() {
            executarRemocaoDisciplina(disciplinaId);
        }
    );
}

// FUNÇÃO QUE EXECUTA A REMOÇÃO DA DISCIPLINA
async function executarRemocaoDisciplina(disciplinaId) {
    console.log("=== INICIANDO REMOÇÃO DE DISCIPLINA ===");
    console.log("ID da disciplina:", disciplinaId);
    
    const csrfToken = document.querySelector("meta[name=\"csrf-token\"]").getAttribute("content");
    
    try {
        console.log("Fazendo requisição de remoção...");
        
        const response = await fetch(`./api/disciplinas/${disciplinaId}`, {
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
            mostrarPopupSucesso("Disciplina removida com sucesso!");
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
