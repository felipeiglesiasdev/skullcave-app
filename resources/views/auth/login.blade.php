<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <!-- ====================== METADADOS ====================== -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkullCave - Login</title>
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">
    
    <!-- ====================== FONTES ====================== -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- ====================== CSS ====================== -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/auth/login.css') }}" rel="stylesheet">
</head>
<body>
    <!-- ====================== CONTAINER PRINCIPAL ====================== -->
    <div class="auth-container">
        <div class="auth-card">
            <!-- ====================== CABEÇALHO ====================== -->
            <div class="auth-header">
                <img src="{{ asset('images/logo-skullcave2.png') }}" alt="Logo SkullCave" class="img-fluid">
                <h1>Bem-vindo de volta!</h1>
                <p>Sistema de Flashcards Educacional</p>
            </div>

            <!-- ====================== EXIBIÇÃO DE ERROS ====================== -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <!-- ====================== FORMULÁRIO DE LOGIN ====================== -->
            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <!-- ====================== TIPO DE USUÁRIO ====================== -->
                <div class="form-group">
                    <label for="user_type" class="form-label">Tipo de usuário</label>
                    <select id="user_type" name="user_type" class="form-select" required>
                        <option value="" disabled selected>Selecione seu tipo de acesso...</option>
                        <option value="independente" {{ old('user_type') == 'independente' ? 'selected' : '' }}>
                            <i class="bi bi-person"></i> Acesso Individual
                        </option>
                        <option value="aluno" {{ old('user_type') == 'aluno' ? 'selected' : '' }}>
                            <i class="bi bi-mortarboard"></i> Estudante
                        </option>
                        <option value="professor" {{ old('user_type') == 'professor' ? 'selected' : '' }}>
                            <i class="bi bi-easel"></i> Professor
                        </option>
                    </select>
                </div>
                
                <!-- ====================== EMAIL ====================== -->
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input id="email" type="email" name="email" class="form-control" 
                               value="{{ old('email') }}" required placeholder="seu@email.com">
                    </div>
                </div>
                
                <!-- ====================== SENHA ====================== -->
                <div class="form-group">
                    <label for="password" class="form-label">Senha</label>
                    <div class="input-group password-container">
                        <span class="input-group-text">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input id="password" type="password" name="password" class="form-control" 
                               required placeholder="••••••••">
                        <button type="button" class="toggle-password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                
                <!-- ====================== BOTÃO DE ENTRAR ====================== -->
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Entrar
                </button>
            </form>
            
            <!-- ====================== RODAPÉ ====================== -->
            <div class="auth-footer">
                <p>Não tem uma conta? 
                    <a href="{{ route('register') }}">
                        <i class="bi bi-person-plus"></i> Cadastre-se aqui
                    </a>
                </p>
            </div>
        </div>
    </div>

    <!-- ====================== JAVASCRIPT ====================== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/auth/login.js') }}"></script>
</body>
</html>
