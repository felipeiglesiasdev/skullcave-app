<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <!-- ====================== METADADOS BÁSICOS ====================== -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | SkullCave</title>
    
    <!-- ====================== BOOTSTRAP CSS ====================== -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- ====================== ESTILOS PERSONALIZADOS ====================== -->
    <style>
        /* ====================== ESTILOS GERAIS ====================== */
        body {
            background-color: #f8f9fa;
            padding-top: 5rem;
        }
        
        /* ====================== CARD DE BOAS-VINDAS ====================== */
        .welcome-card {
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <!-- ====================== CONTEÚDO PRINCIPAL ====================== -->
    <div class="container">
        <div class="card welcome-card">
            <div class="card-header bg-primary text-white">
                <h3 class="text-center">DASHBOARD</h3>
            </div>
            
            <div class="card-body text-center">
                <!-- ====================== MENSAGEM DE BOAS-VINDAS ====================== -->
                <h4 class="mb-4">SEJA BEM-VINDO(A), {{ Auth::user()->nome }}!</h4>
                <p class="text-muted">Você está logado como usuário independente.</p>
                
                <!-- ====================== BOTÃO DE LOGOUT ====================== -->
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-box-arrow-left"></i> SAIR
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ====================== BOOTSTRAP JS ====================== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>