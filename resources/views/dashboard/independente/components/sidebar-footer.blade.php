<!-- Footer da Sidebar -->
<div class="sidebar-footer">
    <div class="user-info">
        <div class="user-avatar">
            <i class="fas fa-user"></i>
        </div>
        <div class="user-details">
            <span class="user-name">{{ Auth::user()->nome ?? 'Usuário' }}</span>
            <span class="user-email">{{ Auth::user()->email ?? '' }}</span>
        </div>
    </div>
    <form method="POST" action="{{ route('logout') }}" class="logout-form">
        @csrf
        <button type="submit" class="btn-logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>Sair</span>
        </button>
    </form>
</div>

