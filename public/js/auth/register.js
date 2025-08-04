document.addEventListener("DOMContentLoaded", function() {
    const userTypeSelect = document.getElementById("user_type");
    const escolaFields = document.getElementById("escola-fields");
    const independenteFields = document.getElementById("independente-fields");

    // ====================== TOGGLE CAMPOS BASEADO NO TIPO ======================
    if (userTypeSelect) {
        userTypeSelect.addEventListener("change", function() {
            const isEscola = this.value === "escola";

            // Mostrar/ocultar campos
            if (isEscola) {
                escolaFields.classList.add("show");
                independenteFields.style.display = "none";
            } else {
                escolaFields.classList.remove("show");
                independenteFields.style.display = "block";
            }

            // Tornar campos obrigatórios/opcionais
            const escolaInputs = escolaFields.querySelectorAll("input");
            const independenteInputs = independenteFields.querySelectorAll("input");

            escolaInputs.forEach(input => {
                input.required = isEscola;
            });

            independenteInputs.forEach(input => {
                input.required = !isEscola;
            });
        });

        // Trigger inicial se já houver valor selecionado
        if (userTypeSelect.value) {
            userTypeSelect.dispatchEvent(new Event("change"));
        }
    }

    // ====================== TOGGLE DE VISUALIZAÇÃO DE SENHA ======================
    document.querySelectorAll(".toggle-password").forEach(btn => {
        btn.addEventListener("click", function() {
            const input = this.closest(".password-container").querySelector("input");
            const icon = this.querySelector("i");

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");
            }
        });
    });

    // ====================== MÁSCARA E LIMITE PARA CNPJ ======================
    const cnpjInput = document.getElementById("escola_cnpj");
    if (cnpjInput) {
        cnpjInput.addEventListener("input", function(e) {
            let value = e.target.value.replace(/\D/g, ""); // Remove tudo que não é dígito

            // Limita o número de dígitos brutos para 14 (CNPJ completo)
            if (value.length > 14) {
                value = value.substring(0, 14);
            }

            // Aplica a máscara
            if (value.length > 12) {
                value = value.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, "$1.$2.$3/$4-$5");
            } else if (value.length > 8) {
                value = value.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})/, "$1.$2.$3/$4");
            } else if (value.length > 5) {
                value = value.replace(/^(\d{2})(\d{3})(\d{3})/, "$1.$2.$3");
            } else if (value.length > 2) {
                value = value.replace(/^(\d{2})(\d{3})/, "$1.$2");
            } else if (value.length > 0) {
                value = value.replace(/^(\d{2})/, "$1");
            }

            e.target.value = value;
        });
    }

    // ====================== MÁSCARA E LIMITE PARA TELEFONE ======================
    const telefoneInput = document.getElementById("telefone");
    if (telefoneInput) {
        telefoneInput.addEventListener("input", function(e) {
            let value = e.target.value.replace(/\D/g, ""); // Remove tudo que não é dígito

            // Limita o número de dígitos brutos para 11 (telefone com DDD e 9)
            if (value.length > 11) {
                value = value.substring(0, 11);
            }

            // Aplica a máscara
            if (value.length > 10) {
                value = value.replace(/^(\d{2})(\d{5})(\d{4})/, "($1) $2-$3");
            } else if (value.length > 6) {
                value = value.replace(/^(\d{2})(\d{4})(\d{4})/, "($1) $2-$3");
            } else if (value.length > 2) {
                value = value.replace(/^(\d{2})(\d{4})/, "($1) $2");
            } else if (value.length > 0) {
                value = value.replace(/^(\d{2})/, "($1");
            }

            e.target.value = value;
        });
    }

    // ====================== MODAL DE SUCESSO PARA ESCOLA ======================
    // @if(session("escola_success"))
    //     const modal = new bootstrap.Modal(document.getElementById("escolaSuccessModal"));
    //     modal.show();

    //     // Redirecionar para home quando modal for fechado
    //     document.getElementById("escolaSuccessModal").addEventListener("hidden.bs.modal", function() {
    //         window.location.href = "/";
    //     });
    // @endif
});