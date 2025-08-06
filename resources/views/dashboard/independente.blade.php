<!DOCTYPE html>
<html lang="pt-BR">
<head>
    @include('dashboard.independente.components.head')
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Esquerda - Disciplinas -->
        <x-independente.menu />

        <!-- Área Principal - Conteúdo Dinâmico -->
        <div class="main-content">
            <x-independente.header 
                :breadcrumb="$breadcrumb ?? []" 
                :showTopicoButton="$showTopicoButton ?? false"
                :showFlashcardButton="$showFlashcardButton ?? false" 
            />

            <!-- Área de Conteúdo -->
            <div class="content-area">
                @include('dashboard.independente.components.welcome-state')
                <x-independente.topicos />
                <x-independente.flashcards />
            </div>
        </div>
    </div>

    @include('dashboard.independente.components.modals')
    @include('dashboard.independente.components.scripts')
</body>
</html>
