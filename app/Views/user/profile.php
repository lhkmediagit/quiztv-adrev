<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="dashboard-wrapper">
    <div class="dashboard-container max-w-md">
        
        <header class="dashboard-hero">
            <div>
                <h1 class="dashboard-welcome-title">Edit Profile</h1>
                <p class="dashboard-welcome-subtitle">Update your personal account credentials and profile picture.</p>
            </div>
            <a href="<?= site_url('user/dashboard') ?>" class="btn btn-outline">&larr; Dashboard</a>
        </header>

        <section class="profile-section-card">
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

            <form action="<?= site_url('user/profile/update') ?>" method="POST" enctype="multipart/form-data" class="profile-form">
                <?= csrf_field() ?>

                <!-- Current Avatar and File Input -->
                <div class="form-avatar-section">
                    <div class="form-avatar-preview">
                        <?php if ($user->avatar): ?>
                            <img src="<?= esc($user->avatar) ?>" alt="Avatar" id="avatar-preview-img" class="profile-avatar-large">
                        <?php else: ?>
                            <div class="avatar-placeholder-large" id="avatar-placeholder">
                                <?= strtoupper(substr($user->name, 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="form-avatar-upload">
                        <label for="avatar" class="form-label font-bold">Profile Picture</label>
                        <input type="file" name="avatar" id="avatar" accept="image/*" class="form-control-file">
                        <span class="form-help-text">JPG, PNG or WEBP. Max 2MB.</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="<?= esc(old('name', $user->name)) ?>" required>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?= esc(old('email', $user->email)) ?>" required>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">New Password (leave blank to keep current)</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••">
                    <span class="form-help-text">Must be at least 6 characters.</span>
                </div>

                <div class="form-actions-footer">
                    <button type="submit" class="btn btn-primary btn-lg">Save Settings</button>
                </div>
            </form>
        </section>

    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Live update preview avatar image
    const avatarInput = document.getElementById('avatar');
    const previewImg = document.getElementById('avatar-preview-img');
    const placeholder = document.getElementById('avatar-placeholder');

    if (avatarInput) {
        avatarInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (previewImg) {
                        previewImg.src = e.target.result;
                    } else if (placeholder) {
                        // Replace placeholder with img tag
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.id = 'avatar-preview-img';
                        img.className = 'profile-avatar-large';
                        placeholder.parentNode.replaceChild(img, placeholder);
                    }
                }
                reader.readAsDataURL(file);
            }
        });
    }
</script>
<?= $this->endSection() ?>
