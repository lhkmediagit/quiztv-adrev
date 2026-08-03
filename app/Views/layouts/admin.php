<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <script>
        // Prevent zoom-in or zoom-out issues (mouse wheel + keyboard + touch gestures)
        (function() {
            // Prevent Ctrl + Mouse Wheel Zoom
            document.addEventListener('wheel', function(e) {
                if (e.ctrlKey) {
                    e.preventDefault();
                }
            }, { passive: false });

            // Prevent Keyboard Zooming shortcuts (Ctrl + Plus, Ctrl + Minus, Ctrl + 0)
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey && (e.key === '=' || e.key === '-' || e.key === '+' || e.key === '0' || e.keyCode === 187 || e.keyCode === 189 || e.keyCode === 48 || e.keyCode === 96 || e.keyCode === 107 || e.keyCode === 109)) {
                    e.preventDefault();
                }
            });

            // Prevent Pinch-to-Zoom on mobile devices
            document.addEventListener('touchmove', function(e) {
                if (e.touches.length > 1) {
                    e.preventDefault();
                }
            }, { passive: false });

            // Prevent Double-Tap Zoom
            let lastTouchEnd = 0;
            document.addEventListener('touchend', function(e) {
                const now = (new Date()).getTime();
                if (now - lastTouchEnd <= 300) {
                    e.preventDefault();
                }
                lastTouchEnd = now;
            }, false);
        })();
    </script>
    <title><?= esc($title ?? 'Admin Dashboard') ?></title>
    <!-- Dynamic Favicon -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>⚡</text></svg>">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
    <!-- Admin stylesheet -->
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css?v=' . time()) ?>">
</head>
<body>
    <!-- Mobile header bar -->
    <div class="admin-mobile-top">
        <a href="<?= site_url('admin') ?>" class="admin-logo">⚡ QuizTv Admin</a>
        <button class="sidebar-toggle" id="sidebar-toggle">☰</button>
    </div>

    <!-- Admin Sidebar -->
    <aside class="admin-sidebar" id="admin-sidebar">
        <div class="sidebar-header">
            <a href="<?= site_url('admin') ?>" class="admin-logo">⚡ QuizTv Admin</a>
        </div>
        <nav class="sidebar-nav">
            <a href="<?= site_url('admin') ?>" class="nav-link-item <?= current_url() == site_url('admin') ? 'active' : '' ?>">
                <span class="icon">📊</span> Dashboard
            </a>
            <a href="<?= site_url('admin/categories') ?>" class="nav-link-item <?= strpos(current_url(), 'admin/categories') !== false ? 'active' : '' ?>">
                <span class="icon">🏷️</span> Categories
            </a>
            <a href="<?= site_url('admin/quizzes') ?>" class="nav-link-item <?= strpos(current_url(), 'admin/quizzes') !== false || strpos(current_url(), 'admin/questions') !== false ? 'active' : '' ?>">
                <span class="icon">📝</span> Quizzes
            </a>
            <a href="<?= site_url('admin/users') ?>" class="nav-link-item <?= strpos(current_url(), 'admin/users') !== false ? 'active' : '' ?>">
                <span class="icon">👥</span> Users
            </a>
            <a href="<?= site_url('admin/players') ?>" class="nav-link-item <?= strpos(current_url(), 'admin/players') !== false ? 'active' : '' ?>">
                <span class="icon">🎮</span> Player Info
            </a>
            <a href="<?= site_url('admin/ad-settings') ?>" class="nav-link-item <?= strpos(current_url(), 'admin/ad-settings') !== false ? 'active' : '' ?>">
                <span class="icon">💰</span> Ad Manager
            </a>
            <a href="<?= site_url('admin/script-settings') ?>" class="nav-link-item <?= strpos(current_url(), 'admin/script-settings') !== false ? 'active' : '' ?>">
                <span class="icon">💻</span> Custom Scripts
            </a>
            <hr class="nav-divider">
            <a href="<?= site_url() ?>" class="nav-link-item">
                <span class="icon">🏠</span> Back to App
            </a>
            <a href="<?= site_url('logout') ?>" class="nav-link-item text-danger">
                <span class="icon">🚪</span> Logout
            </a>
        </nav>
    </aside>

    <!-- Admin Main Content Container -->
    <div class="admin-main-wrapper">
        <!-- Toast Alerts -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success" id="admin-alert">
                <span class="alert-text"><?= esc(session()->getFlashdata('success')) ?></span>
                <button class="alert-close" onclick="document.getElementById('admin-alert').remove()">×</button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger" id="admin-alert">
                <span class="alert-text"><?= esc(session()->getFlashdata('error')) ?></span>
                <button class="alert-close" onclick="document.getElementById('admin-alert').remove()">×</button>
            </div>
        <?php endif; ?>

        <header class="admin-content-header">
            <h1 class="admin-page-title"><?= esc($title ?? 'Admin Panel') ?></h1>
            <div class="admin-user-info">
                <span>Logged in as: <strong><?= esc(session()->get('name')) ?></strong></span>
            </div>
        </header>

        <section class="admin-content-body">
            <?= $this->renderSection('content') ?>
        </section>
    </div>

    <script src="<?= base_url('assets/js/admin.js') ?>"></script>
    <script>
        // Toggle mobile sidebar
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const adminSidebar = document.getElementById('admin-sidebar');
        if (sidebarToggle && adminSidebar) {
            sidebarToggle.addEventListener('click', () => {
                adminSidebar.classList.toggle('open');
            });
        }

        // Auto close alert
        const adminAlert = document.getElementById('admin-alert');
        if (adminAlert) {
            setTimeout(() => {
                adminAlert.style.opacity = '0';
                setTimeout(() => adminAlert.remove(), 400);
            }, 5000);
        }
    </script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
