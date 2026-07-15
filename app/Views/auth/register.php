<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<h2 class="auth-title">Create Account</h2>
<p class="auth-subtitle">Join the QuizTv today to track your quiz performance and compete with others.</p>

<!-- Display Validation Errors -->
<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
        <ul class="error-list">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="<?= site_url('register') ?>" method="POST" class="auth-form">
    <?= csrf_field() ?>

    <div class="form-group">
        <label for="name" class="form-label">Full Name</label>
        <input type="text" name="name" id="name" class="form-control" placeholder="John Doe" value="<?= old('name') ?>" required>
    </div>

    <div class="form-group">
        <label for="email" class="form-label">Email Address</label>
        <input type="email" name="email" id="email" class="form-control" placeholder="john@example.com" value="<?= old('email') ?>" required>
    </div>

    <div class="form-group">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" id="password" class="form-control" placeholder="At least 6 characters" required>
    </div>

    <div class="form-group">
        <label for="confirm_password" class="form-label">Confirm Password</label>
        <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Repeat password" required>
    </div>

    <button type="submit" class="btn btn-primary btn-block btn-lg">Create Account</button>
</form>

<div class="auth-footer-text">
    <p>Already have an account? <a href="<?= site_url('login') ?>">Sign in here</a></p>
</div>
<?= $this->endSection() ?>
