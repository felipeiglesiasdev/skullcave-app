<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <!-- ====================== METADADOS ====================== -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkullCave - Cadastro</title>
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">
    
    <!-- ====================== FONTES ====================== -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- ====================== CSS ====================== -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/auth/register.css') }}" rel="stylesheet">
</head>
<body>
    <!-- ====================== CONTAINER PRINCIPAL ====================== -->
    <div class="auth-container">
        <div class="auth-card">
            <!-- ====================== CABEÇALHO ====================== -->
            <div class="auth-header">
                <img src="{{ asset('images/logo-skullcave2.png') }}" alt="Logo SkullCave" class="img-fluid">
                <h1>Criar conta</h1>
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
            
            <!-- ====================== FORMULÁRIO DE CADASTRO ====================== -->
            <form method="POST" action="{{ route('register') }}">
                @csrf
                
                <!-- ====================== TIPO DE CADASTRO ====================== -->
                <div class="form-group">
                    <label for="user_type" class="form-label">Tipo de cadastro</label>
                    <select id="user_type" name="user_type" class="form-select" required>
                        <option value="" disabled selected>Selecione o tipo de cadastro...</option>
                        <option value="independente" {{ old('user_type') == 'independente' ? 'selected' : '' }}>
                            <i class="bi bi-person"></i> Acesso Individual
                        </option>
                        <option value="escola" {{ old('user_type') == 'escola' ? 'selected' : '' }}>
                            <i class="bi bi-building"></i> Escola
                        </option>
                    </select>
                </div>
                
                <!-- ====================== NOME COMPLETO ====================== -->
                <div class="form-group">
                    <label for="name" class="form-label">Nome completo</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-person-badge"></i>
                        </span>
                        <input id="name" type="text" name="name" class="form-control" 
                               value="{{ old('name') }}" required placeholder="Seu nome completo">
                    </div>
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
                
                <!-- ====================== CAMPOS PARA ACESSO INDIVIDUAL ====================== -->
                <div id="independente-fields">
                    <!-- ====================== SENHA ====================== -->
                    <div class="form-group">
                        <label for="password" class="form-label">Senha</label>
                        <div class="input-group password-container">
                            <span class="input-group-text">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input id="password" type="password" name="password" class="form-control" 
                                   placeholder="••••••••">
                            <button type="button" class="toggle-password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- ====================== CONFIRMAR SENHA ====================== -->
                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Confirmar senha</label>
                        <div class="input-group password-container">
                            <span class="input-group-text">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input id="password_confirmation" type="password" name="password_confirmation" 
                                   class="form-control" placeholder="••••••••">
                            <button type="button" class="toggle-password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- ====================== CAMPOS ADICIONAIS PARA ESCOLA ====================== -->
                <div id="escola-fields" class="escola-fields">
                    <!-- ====================== CARGO ====================== -->
                    <div class="form-group">
                        <label for="cargo" class="form-label">Cargo que ocupa</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-briefcase"></i>
                            </span>
                            <input id="cargo" type="text" name="cargo" class="form-control" 
                                   value="{{ old('cargo') }}" placeholder="Ex: Diretor, Coordenador pedagógico">
                        </div>
                    </div>
                    
                    <!-- ====================== NOME DA ESCOLA ====================== -->
                    <div class="form-group">
                        <label for="escola_nome" class="form-label">Nome da escola</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-building"></i>
                            </span>
                            <input id="escola_nome" type="text" name="escola_nome" class="form-control" 
                                   value="{{ old('escola_nome') }}" placeholder="Nome da instituição">
                        </div>
                    </div>
                    
                    <!-- ====================== CNPJ ====================== -->
                    <div class="form-group">
                        <label for="escola_cnpj" class="form-label">CNPJ</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-file-earmark-text"></i>
                            </span>
                            <input id="escola_cnpj" type="text" name="escola_cnpj" class="form-control" 
                                   value="{{ old('escola_cnpj') }}" placeholder="00.000.000/0000-00">
                        </div>
                    </div>
                    
                    <!-- ====================== TELEFONE ====================== -->
                    <div class="form-group">
                        <label for="telefone" class="form-label">Telefone</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-telephone"></i>
                            </span>
                            <input id="telefone" type="text" name="telefone" class="form-control" 
                                   value="{{ old('telefone') }}" placeholder="(00) 00000-0000">
                        </div>
                    </div>
                </div>
                
                <!-- ====================== BOTÃO DE CADASTRO ====================== -->
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-person-plus me-2"></i>Criar conta
                </button>
            </form>
            
            <!-- ====================== RODAPÉ ====================== -->
            <div class="auth-footer">
                <p>Já tem uma conta? 
                    <a href="{{ route('login') }}">
                        <i class="bi bi-box-arrow-in-right"></i> Faça login aqui
                    </a>
                </p>
            </div>
        </div>
    </div>

    <!-- ====================== MODAL DE SUCESSO PARA ESCOLA ====================== -->
    <div class="modal fade" id="escolaSuccessModal" tabindex="-1" aria-labelledby="escolaSuccessModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="escolaSuccessModalLabel">
                        <i class="bi bi-check-circle me-2"></i>Interesse registrado!
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="success-icon">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <h4>Obrigado pelo seu interesse!</h4>
                    <p class="mb-0">Recebemos suas informações e entraremos em contato em breve para apresentar nossa solução educacional.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                        <i class="bi bi-house me-2"></i>Voltar ao início
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ====================== JAVASCRIPT ====================== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/auth/register.js') }}"></script>
    
    <!-- ====================== MODAL DE SUCESSO PARA ESCOLA ====================== -->
    @if(session('escola_success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = new bootstrap.Modal(document.getElementById('escolaSuccessModal'));
                modal.show();
                
                // Redirecionar para home quando modal for fechado
                document.getElementById('escolaSuccessModal').addEventListener('hidden.bs.modal', function() {
                    window.location.href = '/';
                });
            });
        </script>
    @endif
</body>
</html>