<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    {{-- Menú hamburguesa (sidebar toggle) --}}
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
    </ul>

    {{-- Espacio para otros elementos --}}
    <ul class="navbar-nav ml-auto">

        {{-- Nombre del usuario --}}
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-user"></i> {{ Auth::user()->name ?? 'Invitado' }}
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="#" class="dropdown-item disabled">
                    <i class="fas fa-id-badge mr-2"></i> Perfil
                </a>
                <div class="dropdown-divider"></div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="dropdown-item text-danger" type="submit">
                        <i class="fas fa-sign-out-alt mr-2"></i> Cerrar sesión
                    </button>
                </form>
            </div>
        </li>

    </ul>
</nav>
