// FUNÇÕES GERAIS

// FUNÇÃO PARA MOSTRAR POPUP DE SUCESSO
function mostrarPopupSucesso(mensagem) {
    let popup = document.getElementById("successPopup");
    if (!popup) {
        popup = document.createElement("div");
        popup.id = "successPopup";
        popup.className = "success-popup";
        popup.innerHTML = `
            <div class="popup-content">
                <i class="fas fa-check-circle"></i>
                <span class="popup-message"></span>
            </div>
        `;
        document.body.appendChild(popup);
    }
    
    const messageElement = popup.querySelector(".popup-message");
    messageElement.textContent = mensagem;
    popup.classList.add("show");
    
    setTimeout(() => {
        popup.classList.remove("show");
    }, 3000);
}

// FUNÇÃO PARA MOSTRAR POPUP DE ERRO
function mostrarPopupErro(mensagem) {
    let popup = document.getElementById("errorPopup");
    if (!popup) {
        popup = document.createElement("div");
        popup.id = "errorPopup";
        popup.className = "error-popup";
        popup.innerHTML = `
            <div class="popup-content">
                <i class="fas fa-exclamation-circle"></i>
                <span class="popup-message"></span>
            </div>
        `;
        document.body.appendChild(popup);
    }
    
    const messageElement = popup.querySelector(".popup-message");
    messageElement.textContent = mensagem;
    popup.classList.add("show");
    
    setTimeout(() => {
        popup.classList.remove("show");
    }, 4000);
}

// FUNÇÃO PARA MOSTRAR POPUP DE CONFIRMAÇÃO
function mostrarPopupConfirmacao(titulo, mensagem, callback) {
    let popup = document.getElementById("confirmPopup");
    if (!popup) {
        popup = document.createElement("div");
        popup.id = "confirmPopup";
        popup.className = "confirm-popup";
        popup.innerHTML = `
            <div class="confirm-overlay"></div>
            <div class="confirm-content">
                <div class="confirm-header">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h3 class="confirm-title"></h3>
                </div>
                <div class="confirm-body">
                    <p class="confirm-message"></p>
                </div>
                <div class="confirm-actions">
                    <button class="btn-cancel" onclick="fecharPopupConfirmacao()">Cancelar</button>
                    <button class="btn-confirm">Confirmar</button>
                </div>
            </div>
        `;
        document.body.appendChild(popup);
    }
    
    const titleElement = popup.querySelector(".confirm-title");
    const messageElement = popup.querySelector(".confirm-message");
    const confirmBtn = popup.querySelector(".btn-confirm");
    
    const newConfirmBtn = confirmBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
    
    newConfirmBtn.addEventListener("click", function() {
        fecharPopupConfirmacao();
        if (callback) callback();
    });
    
    titleElement.textContent = titulo;
    messageElement.textContent = mensagem;
    popup.classList.add("show");
}

// FUNÇÃO PARA FECHAR POPUP DE CONFIRMAÇÃO
function fecharPopupConfirmacao() {
    const popup = document.getElementById("confirmPopup");
    if (popup) {
        popup.classList.remove("show");
    }
}

// FUNÇÃO PARA ABRIR MODAL
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add("show");
    }
}

// FUNÇÃO PARA FECHAR MODAL
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove("show");
    }
}

// FUNÇÃO PARA FECHAR MODAL CLICANDO FORA DELE
document.addEventListener("click", function(e) {
    if (e.target.classList.contains("modal")) {
        e.target.classList.remove("show");
    }
    
    if (e.target.classList.contains("confirm-overlay")) {
        fecharPopupConfirmacao();
    }
});

// FUNÇÃO PARA FECHAR MODAL COM A TECLA ESC
document.addEventListener("keydown", function(e) {
    if (e.key === "Escape") {
        const modals = document.querySelectorAll(".modal.show");
        modals.forEach(modal => {
            modal.classList.remove("show");
        });
        
        fecharPopupConfirmacao();
    }
});

