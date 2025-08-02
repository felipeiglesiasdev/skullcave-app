<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <!-- ====================== METADADOS ====================== -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkullCave - Login</title>
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">
    
    <!-- ====================== CSS ====================== -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/auth/login.css') }}" rel="stylesheet">
</head>
<body>
    <!-- ====================== CONTAINER PRINCIPAL ====================== -->
    <div class="login-container">
        <!-- ====================== CABEÇALHO ====================== -->
        <div class="login-header">
            <img src="{{ asset('images/logo-skullcave2.png') }}" alt="Logo SkullCave" class="img-fluid">
            <p>Sistema de Flashcards Educacional</p>
        </div>
        
        <!-- ====================== FORMULÁRIO DE LOGIN ====================== -->
        <form class="login-form" method="POST" action="{{ route('login') }}">
            @csrf
            
            <!-- ====================== TIPO DE USUÁRIO ====================== -->
            <div class="form-group">
                <label for="user_type">Tipo de usuário</label>
                <select id="user_type" name="user_type" class="form-select" required>
                    <option value="" disabled selected>Selecione...</option>
                    <option value="independente">
                        <i class="bi bi-person"></i> Acesso individual
                    </option>
                    <option value="aluno">
                        <i class="bi bi-mortarboard"></i> Estudante
                    </option>
                    <option value="professor">
                        <i class="bi bi-easel"></i> Professor
                    </option>
                </select>
            </div>
            
            <!-- ====================== EMAIL ====================== -->
            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-envelope"></i>
                    </span>
                    <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="exemplo@gmail.com">
                </div>
            </div>
            
            <!-- ====================== SENHA ====================== -->
            <div class="form-group">
                <label for="password">Senha</label>
                <div class="input-group password-container">
                    <span class="input-group-text">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input id="password" type="password" name="password" class="form-control" required placeholder="••••••••">
                    <button type="button" class="toggle-password btn btn-sm">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>
            
            <!-- ====================== BOTÃO DE ENTRAR ====================== -->
            <button type="submit" class="login-button btn btn-primary">
                <i class="bi bi-box-arrow-in-right"></i> Entrar
            </button>
        </form>
        
        <!-- ====================== RODAPÉ ====================== -->
        <div class="login-footer">
            <p>Não tem uma conta? 
                <a href="{{ route('register') }}">
                    <i class="bi bi-person-plus"></i> Cadastre-se
                </a>
            </p>
        </div>
    </div>

    <!-- ====================== JAVASCRIPT ====================== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/auth/login.js') }}"></script>
</body>
</html>