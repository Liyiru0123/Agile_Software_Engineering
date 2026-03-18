<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel - English Reading Platform')</title>
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
            background-color: #5C1A1A !important;
            color: #F8F4E9 !important;
            font-family: 'Microsoft YaHei', 'Georgia', serif;
            margin: 0;
            padding: 0;
        }

        /* Admin top navigation */
        .admin-top-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 999;
            background-color: #F8F4E9 !important;
            color: #5C1A1A !important;
            padding: 10px 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            border-bottom: 1px solid #D4B483;
        }
        .admin-top-nav h5 {
            color: #5C1A1A !important;
            font-weight: 600;
        }
        .admin-top-nav span {
            color: #3A2618 !important;
        }
        .admin-top-nav .btn-outline-light {
            color: #5C1A1A !important;
            border-color: #D4B483 !important;
            background-color: #FAF6ED !important;
        }
        .admin-top-nav .btn-outline-light:hover {
            background-color: #E8D4B8 !important;
        }
        .admin-top-nav .btn-outline-danger {
            color: #F8F4E9 !important;
            border-color: #8B4513 !important;
            background-color: #8B4513 !important;
        }
        .admin-top-nav .btn-outline-danger:hover {
            background-color: #A0522D !important;
            border-color: #A0522D !important;
        }
        .admin-top-nav .btn-outline-light.d-md-none {
            color: #5C1A1A !important;
            border-color: #D4B483 !important;
        }

        /* Admin sidebar */
        .admin-sidebar {
            position: fixed;
            top: 60px;
            left: 0;
            bottom: 0;
            width: 220px;
            background-color: #5C1A1A !important;
            color: #F8F4E9 !important;
            padding-top: 20px;
            z-index: 998;
            border-right: 1px solid #7A2020;
        }
        .admin-sidebar .nav-link {
            color: #D4B483 !important;
            padding: 10px 15px;
            border-radius: 4px;
            margin: 0 10px 5px;
        }
        .admin-sidebar .nav-link.active,
        .admin-sidebar .nav-link:hover {
            color: #F8F4E9 !important;
            background-color: #7A2020 !important;
            font-weight: 500;
            border-color: transparent !important;
        }
        .admin-sidebar .nav-link.active.bg-primary {
            background-color: #7A2020 !important;
        }

        /* Admin main content */
        .admin-main {
            margin-left: 220px;
            margin-top: 70px;
            padding: 20px;
            min-height: calc(100vh - 70px);
            background-color: #5C1A1A !important;
        }
        @media (max-width: 992px) {
            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .admin-sidebar.show {
                transform: translateX(0);
            }
            .admin-main {
                margin-left: 0;
            }
        }

        /* Admin component styles */
        .alert-success {
            background-color: #FAF6ED !important;
            border-color: #D4B483 !important;
            color: #5C1A1A !important;
        }
        .alert-danger {
            background-color: #7A2020 !important;
            border-color: #8B4513 !important;
            color: #F8F4E9 !important;
        }
        .alert .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        .option-group {
            border: 1px solid #D4B483 !important;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #F8F4E9 !important;
            color: #5C1A1A !important;
        }

        .card {
            background-color: #F8F4E9 !important;
            border-color: #D4B483 !important;
            color: #3A2618 !important;
        }
        .card-header {
            background-color: #F8F4E9 !important;
            border-bottom: 1px solid #D4B483 !important;
            color: #5C1A1A !important;
            font-weight: 600;
        }
        .form-control, .form-select {
            background-color: #FAF6ED !important;
            border-color: #D4B483 !important;
            color: #5C1A1A !important;
        }
        .form-control:focus, .form-select:focus {
            border-color: #8B4513 !important;
            box-shadow: 0 0 0 0.2rem rgba(139, 69, 19, 0.25);
        }
        .btn-primary {
            background-color: #8B4513 !important;
            border-color: #8B4513 !important;
            color: #F8F4E9 !important;
        }
        .btn-primary:hover {
            background-color: #A0522D !important;
            border-color: #A0522D !important;
        }
        .btn-outline-primary {
            color: #8B4513 !important;
            border-color: #8B4513 !important;
        }
        .btn-outline-primary:hover {
            background-color: #8B4513 !important;
            color: #F8F4E9 !important;
        }
        .btn-outline-secondary {
            color: #5C1A1A !important;
            border-color: #D4B483 !important;
        }
        .btn-outline-secondary:hover {
            background-color: #FAF6ED !important;
        }
        .table {
            color: #3A2618 !important;
            background-color: #F8F4E9 !important;
        }
        .table th {
            color: #5C1A1A !important;
            border-bottom: 2px solid #D4B483 !important;
        }
        .table td {
            border-color: #E8D4B8 !important;
        }
        .table-hover > tbody > tr:hover {
            background-color: #FAF6ED !important;
        }
        .badge {
            background-color: #D4B483 !important;
            color: #5C1A1A !important;
        }
        .badge.bg-info {
            background-color: #8B4513 !important;
            color: #F8F4E9 !important;
        }
        .badge.bg-warning {
            background-color: #A0522D !important;
            color: #F8F4E9 !important;
        }
    </style>
</head>
<body>
    <!-- Admin top navigation -->
    <div class="admin-top-nav d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <button class="btn btn-outline-light d-md-none me-3" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h5 class="mb-0">English Reading Platform - Admin Panel</h5>
        </div>
        <div>
            <span class="me-3">Welcome, {{ auth()->user()->name }}</span>
            <a href="{{ route('home') }}" class="btn btn-outline-light btn-sm me-2">
                <i class="fas fa-home me-1"></i>Frontend Home
            </a>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Admin sidebar -->
    <div class="admin-sidebar">
        <ul class="nav flex-column px-3">
            <li class="nav-item mb-2">
                <a href="{{ route('admin.articles.index') }}" class="nav-link {{ request()->routeIs('admin.articles.index') ? 'active bg-primary' : 'text-white' }}">
                    <i class="fas fa-list me-2"></i>Article Management
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('admin.articles.create') }}" class="nav-link {{ request()->routeIs('admin.articles.create') ? 'active bg-primary' : 'text-white' }}">
                    <i class="fas fa-plus me-2"></i>Add New Article
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('admin.questions.index') }}" class="nav-link {{ request()->routeIs('admin.questions.index') ? 'active bg-primary' : 'text-white' }}">
                    <i class="fas fa-question-circle me-2"></i>Question Management
                </a>
            </li>
        </ul>
    </div>

    <!-- Admin main content -->
    <div class="admin-main">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar toggle
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.admin-sidebar').classList.toggle('show');
        });
    </script>
    @yield('scripts')
</body>
</html>