<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="admin-section-card max-w-lg">
    <h2 class="admin-card-title">Modify Quiz Configuration</h2>

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

    <form action="<?= site_url('admin/quizzes/update/' . esc($quiz->id)) ?>" method="POST" enctype="multipart/form-data" class="admin-form">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="title" class="form-label">Quiz Title</label>
            <input type="text" name="title" id="title" class="form-control" value="<?= esc(old('title', $quiz->title)) ?>" required>
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Short Description</label>
            <textarea name="description" id="description" class="form-control" rows="3" required><?= esc(old('description', $quiz->description)) ?></textarea>
        </div>

        <div class="form-group">
            <label for="about_quiz" class="form-label">About the Quiz (Detailed Background)</label>
            <textarea name="about_quiz" id="about_quiz" class="form-control" rows="4" placeholder="Detailed background and info about this quiz..."><?= esc(old('about_quiz', $quiz->about_quiz ?? '')) ?></textarea>
            <span class="form-help-text">Displayed under the "About the quiz" section on the play page. Defaults to short description if empty.</span>
        </div>

        <div class="form-row">
            <div class="form-group col-6">
                <label for="category_id" class="form-label">Category</label>
                <select name="category_id" id="category_id" class="form-control" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= esc($cat->id) ?>" <?= old('category_id', $quiz->category_id) == $cat->id ? 'selected' : '' ?>><?= esc($cat->name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group col-6">
                <label for="difficulty" class="form-label">Difficulty</label>
                <select name="difficulty" id="difficulty" class="form-control" required>
                    <option value="easy" <?= old('difficulty', $quiz->difficulty) == 'easy' ? 'selected' : '' ?>>Easy</option>
                    <option value="medium" <?= old('difficulty', $quiz->difficulty) == 'medium' ? 'selected' : '' ?>>Medium</option>
                    <option value="hard" <?= old('difficulty', $quiz->difficulty) == 'hard' ? 'selected' : '' ?>>Hard</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-6">
                <label for="pass_rate" class="form-label">Pass Rate (%)</label>
                <input type="number" name="pass_rate" id="pass_rate" class="form-control" step="0.01" min="0" max="100" value="<?= esc(old('pass_rate', $quiz->pass_rate)) ?>" required>
            </div>

            <div class="form-group col-6">
                <label for="duration_minutes" class="form-label">Duration (minutes)</label>
                <input type="number" name="duration_minutes" id="duration_minutes" class="form-control" min="1" value="<?= esc(old('duration_minutes', $quiz->duration_minutes)) ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label for="thumbnail" class="form-label">Thumbnail Image size - 16:9 (eg. 800*450)</label>
            <?php if ($quiz->thumbnail): ?>
                <div class="form-current-thumbnail" style="margin-bottom: 12px;">
                    <img src="<?= (str_starts_with($quiz->thumbnail, 'http://') || str_starts_with($quiz->thumbnail, 'https://')) ? esc($quiz->thumbnail) : base_url('uploads/quizzes/' . esc($quiz->thumbnail)) ?>" alt="Current Banner" class="admin-thumbnail-medium" style="max-height: 120px; border-radius: var(--radius-sm); border: 1px solid var(--border); display: block; margin-bottom: 6px;">
                    <p class="form-help-text">Current thumbnail.</p>
                </div>
            <?php endif; ?>
            <input type="file" name="thumbnail" id="thumbnail" accept="image/*" class="form-control-file">
            <span class="form-help-text">Upload a new file from your device. Maximum file size: 2MB.</span>

            <div style="display: flex; align-items: center; justify-content: center; margin: 12px 0;">
                <span style="height: 1px; flex: 1; background-color: var(--border);"></span>
                <span style="padding: 0 10px; font-size: 11px; font-weight: 600; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.5px;">OR</span>
                <span style="height: 1px; flex: 1; background-color: var(--border);"></span>
            </div>

            <label for="thumbnail_url" class="form-label">Thumbnail URL</label>
            <input type="url" name="thumbnail_url" id="thumbnail_url" class="form-control" value="<?= old('thumbnail_url', (str_starts_with($quiz->thumbnail, 'http://') || str_starts_with($quiz->thumbnail, 'https://')) ? $quiz->thumbnail : '') ?>" placeholder="https://example.com/image.jpg">
            <span class="form-help-text">Or enter a direct URL to an external image.</span>
        </div>

        <!-- Recommended Quizzes panel -->
        <div class="form-group border-top">
            <label class="form-label font-bold">Recommended Quizzes (Shown at result screen)</label>
            <div class="checkbox-list">
                <?php if (!empty($otherQuizzes)): ?>
                    <?php foreach ($otherQuizzes as $oq): ?>
                        <label class="checkbox-item">
                            <input type="checkbox" name="recommended_quizzes[]" value="<?= esc($oq->id) ?>" <?= in_array($oq->id, $recommendedIds) ? 'checked' : '' ?>>
                            <?= esc($oq->title) ?>
                        </label>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-subtle">No other active quizzes available to recommend.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-group form-checkbox">
            <label class="form-label-checkbox">
                <input type="checkbox" name="is_active" value="1" <?= old('is_active', $quiz->is_active) == '1' ? 'checked' : '' ?>>
                Make this quiz active immediately
            </label>
        </div>

        <div class="form-actions-footer">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="<?= site_url('admin/quizzes') ?>" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
