// ====================== TOGGLE CAMPOS DE ESCOLA ======================
document.addEventListener('DOMContentLoaded', function() {
    const userTypeSelect = document.getElementById('user_type');
    const escolaFields = document.getElementById('escola-fields');
    
    if (userTypeSelect && escolaFields) {
        userTypeSelect.addEventListener('change', function() {
            escolaFields.style.display = this.value === 'escola' ? 'block' : 'none';
            
            // Tornar campos obrigatórios/opcionais conforme o tipo
            const escolaInputs = escolaFields.querySelectorAll('input');
            escolaInputs.forEach(input => {
                input.required = this.value === 'escola';
            });
        });
    }

    // ====================== TOGGLE DE VISUALIZAÇÃO DE SENHA ======================
    document.querySelectorAll('.toggle-password').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.closest('.password-container').querySelector('input');
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
    });

    // ====================== MÁSCARA PARA CNPJ ======================
    const cnpjInput = document.getElementById('escola_cnpj');
    if (cnpjInput) {
        cnpjInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            if (value.length > 12) {
                value = value.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
            } else if (value.length > 8) {
                value = value.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})/, '$1.$2.$3/$4');
            } else if (value.length > 5) {
                value = value.replace(/^(\d{2})(\d{3})(\d{3})/, '$1.$2.$3');
            } else if (value.length > 2) {
                value = value.replace(/^(\d{2})(\d{3})/, '$1.$2');
            }
            
            e.target.value = value;
        });
    }
});