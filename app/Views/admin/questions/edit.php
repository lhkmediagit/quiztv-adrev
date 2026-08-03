<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
$currentImageUrl = '';
if (!empty($question->visual) && $question->visual !== 'none') {
    if (preg_match('/src="([^"]+)"/', $question->visual, $matches)) {
        $src = $matches[1];
        if (strpos($src, 'uploads/questions/') === false) {
            $currentImageUrl = $src;
        }
    }
}
?>
<div class="admin-section-card max-w-lg">
    <h2 class="admin-card-title">Modify Question Details</h2>

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

    <form action="<?= site_url('admin/questions/update/' . esc($question->id)) ?>" method="POST" enctype="multipart/form-data" class="admin-form">
        <?= csrf_field() ?>

        <div class="form-row">
            <div class="form-group col-12">
                <label for="order_index" class="form-label">Order Index</label>
                <input type="number" name="order_index" id="order_index" class="form-control" min="1" value="<?= esc(old('order_index', $question->order_index)) ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label for="question" class="form-label">Question Text</label>
            <textarea name="question" id="question" class="form-control" rows="3" required><?= esc(old('question', $question->question)) ?></textarea>
        </div>


        <div class="form-group">
            <label class="form-label">Options</label>
            <div class="option-inputs">
                <div class="option-input-row">
                    <label class="option-label">Option 1</label>
                    <input type="text" name="option1" class="form-control" value="<?= esc(old('option1', $question->option1)) ?>" required>
                </div>
                <div class="option-input-row">
                    <label class="option-label">Option 2</label>
                    <input type="text" name="option2" class="form-control" value="<?= esc(old('option2', $question->option2)) ?>" required>
                </div>
                <div class="option-input-row">
                    <label class="option-label">Option 3</label>
                    <input type="text" name="option3" class="form-control" value="<?= esc(old('option3', $question->option3)) ?>" required>
                </div>
                <div class="option-input-row">
                    <label class="option-label">Option 4</label>
                    <input type="text" name="option4" class="form-control" value="<?= esc(old('option4', $question->option4)) ?>" required>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Correct Option</label>
            <div class="radio-group">
                <label class="radio-inline">
                    <input type="radio" name="correct_option" value="1" <?= old('correct_option', $question->correct_option) == '1' ? 'checked' : '' ?> required> Option 1
                </label>
                <label class="radio-inline">
                    <input type="radio" name="correct_option" value="2" <?= old('correct_option', $question->correct_option) == '2' ? 'checked' : '' ?>> Option 2
                </label>
                <label class="radio-inline">
                    <input type="radio" name="correct_option" value="3" <?= old('correct_option', $question->correct_option) == '3' ? 'checked' : '' ?>> Option 3
                </label>
                <label class="radio-inline">
                    <input type="radio" name="correct_option" value="4" <?= old('correct_option', $question->correct_option) == '4' ? 'checked' : '' ?>> Option 4
                </label>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Question Image size - 2:1 (eg. 760*360))</label>
            <?php if (!empty($question->visual) && $question->visual !== 'none'): ?>
                <div class="image-preview-card" style="border: 1.5px solid var(--border); border-radius: var(--radius-md); padding: 20px; background: #fff; margin-bottom: 15px;">
                    <p style="margin-top: 0; margin-bottom: 15px; font-weight: 600; font-size: 14px; color: #475569;">Current Image Preview:</p>
                    <div style="margin-bottom: 15px;">
                        <?= $question->visual ?>
                    </div>
                    <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; font-size: 14px; color: #e11d48; cursor: pointer; user-select: none; margin-bottom: 0;">
                        <input type="checkbox" name="remove_visual" value="1" style="width: 16px; height: 16px; cursor: pointer;">
                        Remove current image
                    </label>
                </div>
            <?php elseif ($question->visual !== 'none' && !empty($quiz->thumbnail)): ?>
                <div class="image-preview-card" style="border: 1.5px solid var(--border); border-radius: var(--radius-md); padding: 20px; background: #fff; margin-bottom: 15px;">
                    <p style="margin-top: 0; margin-bottom: 15px; font-weight: 600; font-size: 14px; color: #475569;">Current Image Preview:</p>
                    <div style="margin-bottom: 15px;">
                        <img src="<?= (str_starts_with($quiz->thumbnail, 'http://') || str_starts_with($quiz->thumbnail, 'https://')) ? esc($quiz->thumbnail) : base_url('uploads/quizzes/' . esc($quiz->thumbnail)) ?>" alt="Quiz Default Thumbnail" />
                    </div>
                    <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; font-size: 14px; color: #e11d48; cursor: pointer; user-select: none; margin-bottom: 0;">
                        <input type="checkbox" name="remove_visual" value="1" style="width: 16px; height: 16px; cursor: pointer;">
                        Remove current image
                    </label>
                </div>
            <?php endif; ?>

            <?php if ($question->visual === 'none'): ?>
                <div style="margin-bottom: 15px; padding: 12px; background: #f1f5f9; border-radius: var(--radius-sm); display: inline-block;">
                    <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; font-size: 13px; color: #475569; cursor: pointer; user-select: none; margin-bottom: 0;">
                        <input type="checkbox" name="restore_default" value="1" style="width: 15px; height: 15px; cursor: pointer; accent-color: #3b82f6;">
                        Restore default quiz thumbnail fallback
                    </label>
                </div>
            <?php endif; ?>

            <input type="file" name="visual" id="visual" accept="image/*" class="form-control-file">
            <span class="form-help-text" style="display: block; margin-bottom: 8px;">Upload a new photo to replace or add a question image.</span>

            <div style="position: relative; text-align: center; margin: 15px 0;">
                <hr style="border: none; border-top: 1px solid var(--border); margin: 0;">
                <span style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: var(--white); padding: 0 10px; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">OR</span>
            </div>

            <div class="form-group" style="margin-top: 15px; margin-bottom: 0;">
                <label for="image_url" class="form-label" style="font-size: 13px; font-weight: 600; color: var(--text-muted);">Load Image from URL</label>
                <input type="url" name="image_url" id="image_url" class="form-control" placeholder="https://example.com/image.jpg" value="<?= esc(old('image_url', $currentImageUrl)) ?>">
                <span class="form-help-text">Enter a direct image URL to load or update the image.</span>
            </div>
        </div>

        <style>
            .image-preview-card img {
                max-width: 100%;
                max-height: 350px;
                height: auto;
                display: block;
                border-radius: 8px;
                object-fit: contain;
            }
        </style>

        <div class="form-group">
            <label for="explanation" class="form-label">Explanation</label>
            <textarea name="explanation" id="explanation" class="form-control" rows="3"><?= esc(old('explanation', $question->explanation)) ?></textarea>
        </div>

        <div class="form-actions-footer">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="<?= site_url('admin/questions/' . esc($question->quiz_id)) ?>" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
