<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'QuizTv') ?></title>
    <!-- Dynamic Favicon -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>⚡</text></svg>">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
    <!-- Main Style Sheet -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body class="auth-body">
    <!-- Centered Card Container -->
    <div class="auth-container">
        <div class="auth-header">
            <a href="<?= site_url() ?>" class="logo-link auth-logo">
                <span class="logo-icon">⚡</span>
                <span class="logo-text">QuizTv</span>
            </a>
        </div>
        
        <!-- Alerts -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="toast toast-success static-alert">
                <span class="toast-text"><?= esc(session()->getFlashdata('success')) ?></span>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="toast toast-error static-alert">
                <span class="toast-text"><?= esc(session()->getFlashdata('error')) ?></span>
            </div>
        <?php endif; ?>

        <div class="auth-card">
            <?= $this->renderSection('content') ?>
        </div>
    </div>
</body>
</html>
