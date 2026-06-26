<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            <i class="bi bi-book-fill"></i>
            Perpustakaan
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            @auth
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                            <i class="bi bi-house-door"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('buku*') ? 'active' : '' }}" href="{{ route('buku.index') }}">
                            <i class="bi bi-book"></i> Buku
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('kategori*') ? 'active' : '' }}" href="{{ route('kategori.index') }}">
                            <i class="bi bi-bookmark"></i> Kategori
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('anggota*') ? 'active' : '' }}" href="{{ route('anggota.index') }}">
                            <i class="bi bi-people"></i> Anggota
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('transaksi*') ? 'active' : '' }}" href="{{ route('transaksi.index') }}">
                            <i class="bi bi-arrow-left-right"></i> Transaksi
                        </a>
                    </li>
                </ul>

                {{-- Global Search --}}
                <form class="d-flex me-3" method="GET" action="{{ route('search') }}">
                    <input type="search" name="q" class="form-control form-control-sm" style="min-width: 220px;"
                           placeholder="Pencarian global..." value="{{ request('q') }}">
                    <button type="submit" class="btn btn-light btn-sm ms-1">
                        <i class="bi bi-search"></i>
                    </button>
                </form>

                <ul class="navbar-nav align-items-lg-center">
                    <li class="nav-item">
                        <button type="button" id="darkModeToggle" class="btn btn-sm btn-outline-light me-2" title="Mode Gelap/Terang">
                            <i class="bi bi-moon-stars-fill"></i>
                        </button>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="userMenu" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            @else
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item">
                        <button type="button" id="darkModeToggle" class="btn btn-sm btn-outline-light me-2" title="Mode Gelap/Terang">
                            <i class="bi bi-moon-stars-fill"></i>
                        </button>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('login') ? 'active' : '' }}" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('register') ? 'active' : '' }}" href="{{ route('register') }}">
                            <i class="bi bi-person-plus"></i> Register
                        </a>
                    </li>
                </ul>
            @endauth
        </div>
    </div>
</nav>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('darkModeToggle');
    if (!toggleBtn) return;

    const setIcon = () => {
        const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        toggleBtn.innerHTML = isDark
            ? '<i class="bi bi-sun-fill"></i>'
            : '<i class="bi bi-moon-stars-fill"></i>';
    };

    setIcon();

    toggleBtn.addEventListener('click', function () {
        const current = document.documentElement.getAttribute('data-bs-theme');
        const next = current === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-bs-theme', next);
        localStorage.setItem('theme', next);
        setIcon();
    });
});
</script>
@endpush
