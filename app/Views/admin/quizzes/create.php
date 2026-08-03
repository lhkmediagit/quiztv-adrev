<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="admin-section-card max-w-lg">
    <h2 class="admin-card-title">New Quiz Configuration</h2>

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

    <form action="<?= site_url('admin/quizzes/store') ?>" method="POST" enctype="multipart/form-data" class="admin-form">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="title" class="form-label">Quiz Title</label>
            <input type="text" name="title" id="title" class="form-control" value="<?= old('title') ?>" placeholder="e.g. Human Anatomy Quiz" required>
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control" rows="4" placeholder="Brief summary of what the quiz covers..." required><?= old('description') ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group col-6">
                <label for="category_id" class="form-label">Category</label>
                <select name="category_id" id="category_id" class="form-control" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= esc($cat->id) ?>" <?= old('category_id') == $cat->id ? 'selected' : '' ?>><?= esc($cat->name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group col-6">
                <label for="difficulty" class="form-label">Difficulty</label>
                <select name="difficulty" id="difficulty" class="form-control" required>
                    <option value="easy" <?= old('difficulty') == 'easy' ? 'selected' : '' ?>>Easy</option>
                    <option value="medium" <?= old('difficulty') == 'medium' ? 'selected' : '' ?>>Medium</option>
                    <option value="hard" <?= old('difficulty') == 'hard' ? 'selected' : '' ?>>Hard</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-6">
                <label for="pass_rate" class="form-label">Pass Rate (%)</label>
                <input type="number" name="pass_rate" id="pass_rate" class="form-control" step="0.01" min="0" max="100" value="<?= old('pass_rate', '50.00') ?>" required>
            </div>

            <div class="form-group col-6">
                <label for="duration_minutes" class="form-label">Duration (minutes)</label>
                <input type="number" name="duration_minutes" id="duration_minutes" class="form-control" min="1" value="<?= old('duration_minutes', '5') ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label for="thumbnail" class="form-label">Thumbnail Image size - 16:9 (eg. 800*450)</label>
            <input type="file" name="thumbnail" id="thumbnail" accept="image/*" class="form-control-file">
            <span class="form-help-text">Upload a file from your device. Maximum size: 2MB.</span>

            <div style="display: flex; align-items: center; justify-content: center; margin: 12px 0;">
                <span style="height: 1px; flex: 1; background-color: var(--border);"></span>
                <span style="padding: 0 10px; font-size: 11px; font-weight: 600; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.5px;">OR</span>
                <span style="height: 1px; flex: 1; background-color: var(--border);"></span>
            </div>

            <label for="thumbnail_url" class="form-label">Thumbnail URL</label>
            <input type="url" name="thumbnail_url" id="thumbnail_url" class="form-control" value="<?= old('thumbnail_url') ?>" placeholder="https://example.com/image.jpg">
            <span class="form-help-text">Or enter a direct URL to an external image.</span>
        </div>

        <div class="form-group form-checkbox">
            <label class="form-label-checkbox">
                <input type="checkbox" name="is_active" value="1" <?= old('is_active', '1') == '1' ? 'checked' : '' ?>>
                Make this quiz active immediately
            </label>
        </div>

        <div class="form-actions-footer">
            <button type="submit" class="btn btn-primary">Save Quiz</button>
            <a href="<?= site_url('admin/quizzes') ?>" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
