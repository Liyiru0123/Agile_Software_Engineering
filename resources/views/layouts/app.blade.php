<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'English Reading Platform')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Global base styles */
        body {
            background-color: #5C1A1A;
            color: #F8F4E9;
            font-family: 'Microsoft YaHei', 'Georgia', serif;
            margin: 0;
            padding: 0;
        }

        /* Top navigation bar */
        .top-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 999;
            background-color: #F8F4E9 !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            border-bottom: 1px solid #D4B483;
        }
        .top-nav h5 {
            color: #5C1A1A !important;
            font-weight: 600;
        }
        .top-nav .form-control {
            background-color: #FAF6ED;
            border-color: #D4B483;
            color: #5C1A1A;
        }
        .top-nav .form-control:focus {
            border-color: #8B4513;
            box-shadow: 0 0 0 0.2rem rgba(139, 69, 19, 0.25);
        }
        .top-nav .btn-primary {
            background-color: #8B4513 !important;
            border-color: #8B4513 !important;
            color: #F8F4E9 !important;
        }
        .top-nav .btn-primary:hover {
            background-color: #A0522D !important;
            border-color: #A0522D !important;
        }
        .top-nav .btn-outline-secondary {
            color: #5C1A1A !important;
            border-color: #D4B483 !important;
        }
        .top-nav .btn-outline-secondary:hover {
            background-color: #FAF6ED !important;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 70px;
            left: 0;
            bottom: 0;
            width: 240px;
            background-color: #5C1A1A !important;
            color: #F8F4E9 !important;
            padding-top: 20px;
            z-index: 998;
            border-right: 1px solid #7A2020;
        }
        .sidebar .nav-link {
            color: #D4B483 !important;
            padding: 10px 15px;
            border-radius: 4px;
            margin: 0 10px 5px;
        }
        .sidebar .nav-link.active,
        .sidebar .nav-link:hover {
            color: #F8F4E9 !important;
            background-color: #7A2020 !important;
            font-weight: 500;
        }

        /* Main content area */
        .main-content {
            margin-left: 240px;
            margin-top: 80px;
            padding: 20px;
            min-height: calc(100vh - 80px);
            background-color: #5C1A1A !important;
        }
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
        }

        /* Interactive elements */
        .avatar-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #8B4513 !important;
            color: #F8F4E9 !important;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
        }
        .avatar-btn:hover {
            background-color: #A0522D !important;
        }
        .auth-dropdown {
            position: absolute;
            top: 60px;
            right: 20px;
            width: 200px;
            background-color: #F8F4E9 !important;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            border-radius: 8px;
            padding: 10px;
            display: none;
            z-index: 1000;
            border: 1px solid #D4B483;
        }
        .auth-dropdown.show {
            display: block;
        }
        .auth-dropdown a {
            display: block;
            padding: 8px 12px;
            text-decoration: none;
            color: #5C1A1A !important;
            border-radius: 4px;
        }
        .auth-dropdown a:hover {
            background-color: #FAF6ED !important;
            color: #8B4513 !important;
        }
        .auth-dropdown a.text-primary {
            color: #8B4513 !important;
            font-weight: 500;
        }
        .auth-dropdown .btn-outline-danger {
            color: #8B4513 !important;
            border-color: #8B4513 !important;
        }
        .auth-dropdown .btn-outline-danger:hover {
            background-color: #8B4513 !important;
            color: #F8F4E9 !important;
        }

        /* Common components */
        .loading-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #F8F4E9;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Answer area styles */
        .question-card {
            border-left: 4px solid #8B4513 !important;
            margin-bottom: 15px;
            background-color: #F8F4E9 !important;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .option-item {
            margin-bottom: 8px;
            cursor: pointer;
        }
        .option-item label {
            background-color: #FAF6ED !important;
            border-color: #D4B483 !important;
            color: #5C1A1A !important;
        }
        .option-item input:checked + label {
            background-color: #E8D4B8 !important;
            border-color: #8B4513 !important;
            color: #5C1A1A !important;
        }
        .result-correct {
            color: #8B4513 !important;
            font-weight: bold;
        }
        .result-incorrect {
            color: #7A2020 !important;
            font-weight: bold;
        }

        /* Card component styles */
        .card {
            background-color: #F8F4E9 !important;
            border-color: #D4B483 !important;
            color: #3A2618 !important;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .card-title, .card h1, .card h5, .card h6 {
            color: #5C1A1A !important;
            font-weight: 600;
        }
        .card-header {
            background-color: #F8F4E9 !important;
            border-bottom: 1px solid #D4B483 !important;
        }
        .card .badge {
            background-color: #D4B483 !important;
            color: #5C1A1A !important;
        }
        .card .badge.bg-success,
        .card .badge.bg-primary,
        .card .badge.bg-danger {
            background-color: #8B4513 !important;
            color: #F8F4E9 !important;
        }
        .card .btn-outline-primary {
            color: #8B4513 !important;
            border-color: #8B4513 !important;
        }
        .card .btn-outline-primary:hover {
            background-color: #8B4513 !important;
            color: #F8F4E9 !important;
        }
        .card .btn-primary {
            background-color: #8B4513 !important;
            border-color: #8B4513 !important;
            color: #F8F4E9 !important;
        }
        .card .btn-primary:hover {
            background-color: #A0522D !important;
            border-color: #A0522D !important;
        }
    </style>
</head>
<body>
    <!-- Top navigation bar (fixed) -->
    <div class="top-nav py-2 px-4 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <h5 class="mb-0 me-4">English Reader</h5>
            <!-- Mobile sidebar toggle button -->
            <button class="btn btn-outline-secondary d-md-none me-2" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <!-- Search form -->
            <form action="{{ route('articles.index') }}" method="GET" class="d-flex w-50">
                <input type="text" name="search" class="form-control me-2" 
                       placeholder="Search articles..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
        <!-- Avatar + auth dropdown -->
        <div class="position-relative">
            <button class="avatar-btn" id="avatarBtn">
                <i class="fas fa-user"></i>
            </button>
            <!-- Auth dropdown menu -->
            <div class="auth-dropdown" id="authDropdown">
                @guest
                    <a href="{{ route('login') }}" class="mb-2">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </a>
                    <a href="{{ route('register') }}">
                        <i class="fas fa-user-plus me-2"></i>Register
                    </a>
                @else
                    <div class="text-center mb-2">
                        <small class="text-muted">Welcome, {{ auth()->user()->name }}</small>
                    </div>
                    <button type="button" id="logoutBtn" class="btn btn-sm btn-outline-danger w-100 mb-2">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </button>
                    <a href="{{ route('favorites.index') }}" class="mb-2">
                        <i class="fas fa-star me-2"></i>My Collections
                    </a>
                    <a href="{{ route('history.index') }}" class="mb-2">
                        <i class="fas fa-history me-2"></i>Reading History
                    </a>
                    <a href="{{ route('wrong-questions.index') }}" class="mb-2">
                        <i class="fas fa-exclamation-circle me-2"></i>My Wrong Questions
                    </a>
                    @if(auth()->user()->is_admin)
                        <a href="{{ route('admin.articles.index') }}" class="mt-2 text-primary">
                            <i class="fas fa-cog me-2"></i>Admin Panel
                        </a>
                    @endif
                @endguest
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="px-3 mb-4">
            <ul class="nav flex-column">
                <li class="nav-item mb-2">
                    <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active text-white' : 'text-white-50' }}">
                        <i class="fas fa-home me-2"></i>Home
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="{{ route('articles.index') }}" class="nav-link {{ request()->routeIs('articles.index') ? 'active text-white' : 'text-white-50' }}">
                        <i class="fas fa-list me-2"></i>Article List
                    </a>
                </li>
                @auth
                    <li class="nav-item mb-2">
                        <a href="{{ route('favorites.index') }}" class="nav-link {{ request()->routeIs('favorites.index') ? 'active text-white' : 'text-white-50' }}">
                            <i class="fas fa-star me-2"></i>My Collections
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ route('history.index') }}" class="nav-link {{ request()->routeIs('history.index') ? 'active text-white' : 'text-white-50' }}">
                            <i class="fas fa-history me-2"></i>Reading History
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ route('wrong-questions.index') }}" class="nav-link {{ request()->routeIs('wrong-questions.index') ? 'active text-white' : 'text-white-50' }}">
                            <i class="fas fa-exclamation-circle me-2"></i>My Wrong Questions
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>

    <!-- Main content area -->
    <div class="main-content">
        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        // Sidebar toggle
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('show');
        });

        // Avatar dropdown toggle
        document.getElementById('avatarBtn').addEventListener('click', function(e) {
            e.stopPropagation();
            document.getElementById('authDropdown').classList.toggle('show');
        });

        // Close dropdown when click outside
        document.addEventListener('click', function() {
            document.getElementById('authDropdown').classList.remove('show');
        });

        // Prevent dropdown close when click inside
        document.getElementById('authDropdown').addEventListener('click', function(e) {
            e.stopPropagation();
        });

        // Logout logic
        const logoutBtn = document.getElementById('logoutBtn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function() {
                const originalHtml = this.innerHTML;
                this.innerHTML = '<span class="loading-spinner me-2"></span>Processing...';
                this.disabled = true;

                fetch("{{ route('logout') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin',
                    redirect: 'manual'
                })
                .then(response => {
                    if (response.ok || response.status === 302) {
                        return response.json().catch(() => ({ 
                            redirect: "{{ route('home') }}" 
                        }));
                    }
                    throw new Error(`Request failed: ${response.status}`);
                })
                .then(data => {
                    window.location.href = data.redirect || "{{ route('home') }}";
                })
                .catch(error => {
                    console.error('Logout error: ', error);
                    alert('Logout failed! Please refresh and try again.');
                    this.innerHTML = originalHtml;
                    this.disabled = false;
                });
            });
        }
    </script>
    @yield('scripts')
</body>
</html>