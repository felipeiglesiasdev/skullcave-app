// ===== FUNÇÕES DE TOAST =====
// ***************************************************************************************************************
// ***************************************************************************************************************
// ***************************************************************************************************************


// FUNÇÃO PARA CRIAR E EXIBIR UM TOAST
function showToast(mensagem, tipo) {
    // DEFINE AS CLASSES CSS BASEADAS NO TIPO DE TOAST
    let classeToast = "";
    let icone = "";
    
    switch (tipo) {
        case "success":
            classeToast = "bg-success text-white";
            icone = "fas fa-check-circle";
            break;
        case "error":
            classeToast = "bg-danger text-white";
            icone = "fas fa-exclamation-circle";
            break;
        case "warning":
            classeToast = "bg-warning text-dark";
            icone = "fas fa-exclamation-triangle";
            break;
        case "info":
            classeToast = "bg-info text-white";
            icone = "fas fa-info-circle";
            break;
        default:
            classeToast = "bg-secondary text-white";
            icone = "fas fa-bell";
    }
    
    // GERA UM ID ÚNICO PARA O TOAST
    const toastId = "toast-" + Date.now();
    
    // CRIA O HTML DO TOAST
    const toastHtml = `
        <div id="${toastId}" class="toast ${classeToast}" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header ${classeToast}">
                <i class="${icone} me-2"></i>
                <strong class="me-auto">Sistema</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                ${mensagem}
            </div>
        </div>
    `;
    
    // VERIFICA SE O CONTÊINER DE TOASTS EXISTE, SE NÃO, CRIA UM
    let toastContainer = document.getElementById("toast-container");
    if (!toastContainer) {
        toastContainer = document.createElement("div");
        toastContainer.id = "toast-container";
        toastContainer.className = "toast-container position-fixed top-0 end-0 p-3";
        toastContainer.style.zIndex = "9999";
        document.body.appendChild(toastContainer);
    }
    
    // ADICIONA O TOAST AO CONTÊINER
    toastContainer.insertAdjacentHTML("beforeend", toastHtml);
    
    // INICIALIZA E MOSTRA O TOAST
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, {
        autohide: true,
        delay: 5000 // 5 SEGUNDOS
    });
    toast.show();
    
    // REMOVE O TOAST DO DOM APÓS SER OCULTADO
    toastElement.addEventListener("hidden.bs.toast", function() {
        toastElement.remove();
    });
}
