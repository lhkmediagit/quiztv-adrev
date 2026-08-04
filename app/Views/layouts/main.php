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
    <title><?= esc($title ?? 'QuizTv') ?></title>
    <!-- Dynamic Favicon -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>⚡</text></svg>">
    <!-- Google Fonts for typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
    <!-- Main Style Sheet -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css?v=' . time()) ?>">
    <?php if (is_ads_enabled()): ?>
    <!-- Google Publisher Tags (GPT) -->
    <script async src="https://securepubads.g.doubleclick.net/tag/js/gpt.js"></script>
    <?php endif; ?>
    <!-- Custom Header Scripts -->
    <?= get_custom_script('header') ?>
</head>
<body>
    <!-- Custom Body Scripts -->
    <?= get_custom_script('body') ?>
    <!-- Top Navigation -->
    <header class="hub-header">
        <div class="hub-header__inner">
            <a href="<?= site_url() ?>" class="hub-brand">
                <span class="hub-brand__mark">⚡</span>
                <span class="hub-brand__name">QuizTv</span>
            </a>
            
            <div class="nav-links" id="nav-links">
                <a href="<?= site_url() ?>" class="nav-item">Home</a>
                <?php if (session()->has('admin_id')): ?>
                    <a href="<?= site_url('admin') ?>" class="nav-item admin-badge">Admin Panel</a>
                    <div class="user-menu-wrapper">
                        <span class="nav-welcome">Hi, Admin</span>
                        <a href="<?= site_url('logout') ?>" class="btn btn-outline btn-sm">Logout</a>
                    </div>
                <?php elseif (session()->has('user_id')): ?>
                    <a href="<?= site_url('user/dashboard') ?>" class="nav-item">Dashboard</a>
                    <a href="<?= site_url('user/history') ?>" class="nav-item">History</a>
                    <a href="<?= site_url('user/profile') ?>" class="nav-item">Profile</a>
                    <div class="user-menu-wrapper">
                        <span class="nav-welcome">Hi, <?= esc(session()->get('name')) ?></span>
                        <a href="<?= site_url('logout') ?>" class="btn btn-outline btn-sm">Logout</a>
                    </div>
                <?php else: ?>
                    <a href="<?= site_url('login') ?>" class="nav-item">Login</a>
                    <a href="<?= site_url('register') ?>" class="btn btn-primary btn-sm">Sign Up</a>
                <?php endif; ?>
            </div>

            <button class="menu-toggle" id="menu-toggle" aria-label="Toggle Menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </header>

    <!-- Toast Notifications -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="toast toast-success" id="toast-message">
            <span class="toast-icon">✓</span>
            <span class="toast-text"><?= esc(session()->getFlashdata('success')) ?></span>
            <button class="toast-close" onclick="document.getElementById('toast-message').remove()">×</button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="toast toast-error" id="toast-message">
            <span class="toast-icon">⚠</span>
            <span class="toast-text"><?= esc(session()->getFlashdata('error')) ?></span>
            <button class="toast-close" onclick="document.getElementById('toast-message').remove()">×</button>
        </div>
    <?php endif; ?>

    <!-- NDTV-style Sticky Top Mobile Banner (hidden on desktop) -->
    <?php if (is_ads_enabled()): ?>
    <div class="sticky-top-ad hide-above-1024">
        <?= render_banner_slot('sticky_top_mobile') ?>
    </div>
    <?php endif; ?>

    <!-- Main Content Container with Skyscraper Side Ads -->
    <div class="app-layout-container">
        <aside class="skyscraper-ad left-skyscraper">
            <?php if (is_ads_enabled()): ?>
                <?= render_banner_slot('left_rail') ?>
            <?php endif; ?>
        </aside>

        <main class="main-content">
            <?= $this->renderSection('content') ?>
        </main>

        <aside class="skyscraper-ad right-skyscraper">
            <?php if (is_ads_enabled()): ?>
                <?= render_banner_slot('right_rail') ?>
            <?php endif; ?>
        </aside>
    </div>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="site-footer__inner">
            <div class="site-footer__brand">
                <a href="<?= site_url() ?>" class="site-footer__logo">
                    <span>⚡</span><strong>QuizTv</strong>
                </a>
                <p>Fast educational quizzes for entertainment, self-challenge, and general learning. Scores are estimates and are not official academic, clinical, employment, or admissions results.</p>
            </div>
            <nav class="site-footer__nav" aria-label="Company / Legal">
                <div>
                    <h2>Company</h2>
                    <a href="<?= site_url() ?>">Home</a>
                    <a href="<?= site_url('info/about') ?>">About</a>
                    <a href="<?= site_url('info/contact') ?>">Contact</a>
                </div>
                <div>
                    <h2>Legal</h2>
                    <a href="<?= site_url('info/privacy') ?>">Privacy Policy</a>
                    <a href="<?= site_url('info/terms') ?>">Terms of Use</a>
                    <a href="<?= site_url('info/disclaimer') ?>">Disclaimer</a>
                </div>
            </nav>
            <div class="site-footer__bottom">
                <p>&copy; <?= date('Y') ?> QuizTv. All rights reserved. Powered by CodeIgniter 4.</p>
            </div>
        </div>
    </footer>

    <!-- NDTV-style Sticky Bottom Mobile Anchor Ad (hidden on desktop) -->
    <?php if (is_ads_enabled()): ?>
    <div class="sticky-bottom-spacer hide-above-1024"></div>
    <div class="sticky-bottom-ad hide-above-1024">
        <?= render_banner_slot('sticky_bottom_mobile') ?>
    </div>
    <?php endif; ?>

    <script>
        // Toggle mobile navigation
        const menuToggle = document.getElementById('menu-toggle');
        const navLinks = document.getElementById('nav-links');
        if (menuToggle && navLinks) {
            menuToggle.addEventListener('click', () => {
                menuToggle.classList.toggle('open');
                navLinks.classList.toggle('open');
            });
        }
        
        // Auto remove toast notifications
        const toast = document.getElementById('toast-message');
        if (toast) {
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 400);
            }, 5000);
        }
    </script>
    <?php if (is_ads_enabled()): ?>
    <script src="<?= base_url('assets/js/ads.js?v=' . time()) ?>"></script>
    <script>
        // Initialize QuizTv Ads with server-side config
        document.addEventListener('DOMContentLoaded', function() {
            if (window.QuizTvAds) {
                QuizTvAds.init(<?= json_encode(get_ad_config()) ?>);
            }
        });
    </script>
    <?php endif; ?>
    <?= $this->renderSection('scripts') ?>
    <!-- Custom Footer Scripts -->
    <?= get_custom_script('footer') ?>
</body>
</html>
