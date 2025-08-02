// ====================== TOGGLE DE VISUALIZAÇÃO DE SENHA ======================
document.querySelector('.toggle-password').addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const icon = this.querySelector('i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
});

// ====================== VALIDAÇÃO DE FORMULÁRIO ======================
document.querySelector('.login-form').addEventListener('submit', function(e) {
    const userType = document.getElementById('user_type').value;
    if (!userType) {
        e.preventDefault();
        alert('Por favor, selecione um tipo de usuário');
        document.getElementById('user_type').focus();
    }
});