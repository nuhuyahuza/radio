<?php
use App\Utils\Session;

// Get current user from session
$currentUser = Session::get('user');
$userRole = $currentUser['role'] ?? 'guest';
$userName = $currentUser['name'] ?? 'User';
$userEmail = $currentUser['email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Dashboard' ?> - Zaa Radio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 70px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sidebar-brand {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .sidebar-brand:hover {
            color: white;
            text-decoration: none;
        }

        .sidebar-brand.collapsed {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        .sidebar-toggle {
            background: none;
            border: none;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 8px;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .sidebar-toggle:hover {
            background: rgba(255,255,255,0.1);
        }

        .sidebar-nav {
            padding: 20px 0;
            height: calc(100vh - 80px);
            overflow-y: auto;
        }

        .nav-item {
            margin-bottom: 5px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 0 25px 25px 0;
            margin-right: 20px;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            text-decoration: none;
        }

        .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        .nav-link i {
            width: 20px;
            margin-right: 12px;
            text-align: center;
        }

        .nav-link.collapsed i {
            margin-right: 0;
        }

        .nav-text {
            transition: all 0.3s ease;
        }

        .nav-text.collapsed {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            transition: all 0.3s ease;
            min-height: 100vh;
        }

        .main-content.sidebar-collapsed {
            margin-left: var(--sidebar-collapsed-width);
        }

        .topbar {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .topbar-left {
            display: flex;
            align-items: center;
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
        }

        .topbar-right {
            display: flex;
            align-items: center;
        }

        .user-dropdown {
            position: relative;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .user-avatar:hover {
            transform: scale(1.05);
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 10px 0;
            min-width: 200px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #495057;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background: #f8f9fa;
            color: var(--primary-color);
            text-decoration: none;
        }

        .dropdown-item i {
            width: 20px;
            margin-right: 10px;
        }

        .content-area {
            padding: 30px;
        }

        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border-left: 4px solid var(--primary-color);
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 15px;
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .stats-label {
            color: #6c757d;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px 25px;
            border: none;
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin: 0;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            color: #495057;
            padding: 15px;
        }

        .table td {
            padding: 15px;
            vertical-align: middle;
        }

        .badge {
            padding: 8px 12px;
            border-radius: 20px;
            font-weight: 600;
        }

        .badge-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }

        .badge-warning {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        }

        .badge-danger {
            background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
        }

        .badge-info {
            background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .main-content.sidebar-collapsed {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="/<?= $userRole ?>" class="sidebar-brand" id="sidebarBrand">
                <i class="fas fa-radio me-2"></i>
                <span class="nav-text">Zaa Radio</span>
            </a>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
        <nav class="sidebar-nav">
            <?php if ($userRole === 'admin'): ?>
                <!-- Admin Navigation -->
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="/admin" class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                            <i class="fas fa-tachometer-alt"></i>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/admin/users" class="nav-link <?= $currentPage === 'users' ? 'active' : '' ?>">
                            <i class="fas fa-users"></i>
                            <span class="nav-text">Users</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/admin/slots" class="nav-link <?= $currentPage === 'slots' ? 'active' : '' ?>">
                            <i class="fas fa-calendar-alt"></i>
                            <span class="nav-text">All Slots</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/admin/bookings" class="nav-link <?= $currentPage === 'bookings' ? 'active' : '' ?>">
                            <i class="fas fa-clipboard-list"></i>
                            <span class="nav-text">All Bookings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/admin/reports" class="nav-link <?= $currentPage === 'reports' ? 'active' : '' ?>">
                            <i class="fas fa-chart-bar"></i>
                            <span class="nav-text">Reports</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/admin/audit-logs" class="nav-link <?= $currentPage === 'audit-logs' ? 'active' : '' ?>">
                            <i class="fas fa-history"></i>
                            <span class="nav-text">Audit Logs</span>
                        </a>
                    </li>
                </ul>
            <?php elseif ($userRole === 'station_manager'): ?>
                <!-- Station Manager Navigation -->
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="/manager" class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                            <i class="fas fa-tachometer-alt"></i>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/manager/slots" class="nav-link <?= $currentPage === 'slots' ? 'active' : '' ?>">
                            <i class="fas fa-calendar-alt"></i>
                            <span class="nav-text">Manage Slots</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/manager/bookings" class="nav-link <?= $currentPage === 'bookings' ? 'active' : '' ?>">
                            <i class="fas fa-clipboard-list"></i>
                            <span class="nav-text">Manage Bookings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/manager/reports" class="nav-link <?= $currentPage === 'reports' ? 'active' : '' ?>">
                            <i class="fas fa-chart-bar"></i>
                            <span class="nav-text">Reports</span>
                        </a>
                    </li>
                </ul>
            <?php elseif ($userRole === 'advertiser'): ?>
                <!-- Advertiser Navigation -->
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="/advertiser" class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                            <i class="fas fa-tachometer-alt"></i>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/book" class="nav-link">
                            <i class="fas fa-plus-circle"></i>
                            <span class="nav-text">Book New Slot</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/advertiser/bookings" class="nav-link <?= $currentPage === 'bookings' ? 'active' : '' ?>">
                            <i class="fas fa-clipboard-list"></i>
                            <span class="nav-text">My Bookings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/advertiser/profile" class="nav-link <?= $currentPage === 'profile' ? 'active' : '' ?>">
                            <i class="fas fa-user"></i>
                            <span class="nav-text">Profile</span>
                        </a>
                    </li>
                </ul>
            <?php endif; ?>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Top Bar -->
        <div class="topbar">
            <div class="topbar-left">
                <h1 class="page-title"><?= $pageTitle ?? 'Dashboard' ?></h1>
            </div>
            <div class="topbar-right">
                <div class="user-dropdown">
                    <div class="user-avatar" id="userAvatar">
                        <?= strtoupper(substr($userName, 0, 1)) ?>
                    </div>
                    <div class="dropdown-menu" id="userDropdown">
                        <div class="dropdown-item">
                            <i class="fas fa-user"></i>
                            <div>
                                <div class="fw-bold"><?= htmlspecialchars($userName) ?></div>
                                <small class="text-muted"><?= htmlspecialchars($userEmail) ?></small>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="/<?= $userRole ?>/profile" class="dropdown-item">
                            <i class="fas fa-cog"></i>
                            Settings
                        </a>
                        <a href="/logout" class="dropdown-item">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            <?php if (Session::hasFlash('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= htmlspecialchars(Session::getFlash('success')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (Session::hasFlash('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?= htmlspecialchars(Session::getFlash('error')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?= $content ?? '' ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarBrand = document.getElementById('sidebarBrand');
            const navTexts = document.querySelectorAll('.nav-text');
            const userAvatar = document.getElementById('userAvatar');
            const userDropdown = document.getElementById('userDropdown');

            // Toggle sidebar
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('sidebar-collapsed');
                
                // Toggle nav texts
                navTexts.forEach(text => {
                    text.classList.toggle('collapsed');
                });
            });

            // User dropdown toggle
            userAvatar.addEventListener('click', function(e) {
                e.stopPropagation();
                userDropdown.classList.toggle('show');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function() {
                userDropdown.classList.remove('show');
            });

            // Mobile sidebar toggle
            if (window.innerWidth <= 768) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('sidebar-collapsed');
            }

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('sidebar-collapsed');
                }
            });
        });
    </script>
</body>
</html>
