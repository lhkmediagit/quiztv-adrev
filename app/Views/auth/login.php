<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<h2 class="auth-title">Welcome Back</h2>
<p class="auth-subtitle">Sign in to your QuizTv account to save your scores and track attempt history.</p>

<form action="<?= site_url('login') ?>" method="POST" class="auth-form">
    <?= csrf_field() ?>

    <div class="form-group">
        <label for="email" class="form-label">Email Address</label>
        <input type="email" name="email" id="email" class="form-control" placeholder="name@example.com" value="<?= old('email') ?>" required>
    </div>

    <div class="form-group">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
    </div>

    <button type="submit" class="btn btn-primary btn-block btn-lg">Sign In</button>
</form>

<div class="auth-footer-text">
    <p>Don't have an account? <a href="<?= site_url('register') ?>">Sign up here</a></p>
</div>
<?= $this->endSection() ?>
