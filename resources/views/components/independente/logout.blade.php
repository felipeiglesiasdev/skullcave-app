<!-- Componente Logout Independente -->
<form method="POST" action="{{ route('logout') }}" class="logout-form">
    @csrf
    <button type="submit" class="btn-logout">
        <i class="fas fa-sign-out-alt"></i>
        <span>Sair</span>
    </button>
</form>

