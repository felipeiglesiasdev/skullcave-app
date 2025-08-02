<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <!-- ====================== METADADOS ====================== -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkullCave - Cadastro</title>
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
        
        <!-- ====================== FORMULÁRIO DE CADASTRO ====================== -->
        <form class="login-form" method="POST" action="{{ route('register') }}">
            @csrf
            
            <!-- ====================== TIPO DE USUÁRIO ====================== -->
            <div class="form-group">
                <label for="user_type">Tipo de cadastro</label>
                <select id="user_type" name="user_type" class="form-select" required>
                    <option value="" disabled selected>Selecione...</option>
                    <option value="independente">
                        <i class="bi bi-person"></i> Acesso individual
                    </option>
                    <option value="escola">
                        <i class="bi bi-building"></i> Escola
                    </option>
                </select>
            </div>
            
            <!-- ====================== NOME COMPLETO ====================== -->
            <div class="form-group">
                <label for="name">Nome completo</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-person-badge"></i>
                    </span>
                    <input id="name" type="text" name="name" class="form-control" required>
                </div>
            </div>
            
            <!-- ====================== EMAIL ====================== -->
            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-envelope"></i>
                    </span>
                    <input id="email" type="email" name="email" class="form-control" required placeholder="exemplo@gmail.com">
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
            
            <!-- ====================== CONFIRMAR SENHA ====================== -->
            <div class="form-group">
                <label for="password_confirmation">Confirmar senha</label>
                <div class="input-group password-container">
                    <span class="input-group-text">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required placeholder="••••••••">
                    <button type="button" class="toggle-password btn btn-sm">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>
            
            <!-- ====================== CAMPOS ADICIONAIS PARA ESCOLA ====================== -->
            <div id="escola-fields" style="display: none;">
                <div class="form-group">
                    <label for="escola_nome">Nome da escola</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-building"></i>
                        </span>
                        <input id="escola_nome" type="text" name="escola_nome" class="form-control">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="escola_cnpj">CNPJ</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-file-earmark-text"></i>
                        </span>
                        <input id="escola_cnpj" type="text" name="escola_cnpj" class="form-control">
                    </div>
                </div>
            </div>
            
            <!-- ====================== BOTÃO DE CADASTRO ====================== -->
            <button type="submit" class="login-button btn btn-primary">
                <i class="bi bi-person-plus"></i> Criar conta
            </button>
        </form>
        
        <!-- ====================== RODAPÉ ====================== -->
        <div class="login-footer">
            <p>Já tem uma conta? 
                <a href="{{ route('login') }}">
                    <i class="bi bi-box-arrow-in-right"></i> Faça login
                </a>
            </p>
        </div>
    </div>

    <!-- ====================== JAVASCRIPT ====================== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/auth/register.js') }}"></script>
</body>
</html>