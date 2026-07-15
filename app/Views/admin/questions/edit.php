<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
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

    <form action="<?= site_url('admin/questions/update/' . esc($question->id)) ?>" method="POST" class="admin-form">
        <?= csrf_field() ?>

        <div class="form-row">
            <div class="form-group col-6">
                <label for="round_number" class="form-label">Round Number</label>
                <input type="number" name="round_number" id="round_number" class="form-control" min="1" value="<?= esc(old('round_number', $question->round_number)) ?>" required>
            </div>
            
            <div class="form-group col-6">
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
            <label for="explanation" class="form-label">Explanation</label>
            <textarea name="explanation" id="explanation" class="form-control" rows="3" required><?= esc(old('explanation', $question->explanation)) ?></textarea>
        </div>

        <div class="form-actions-footer">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="<?= site_url('admin/questions/' . esc($question->quiz_id)) ?>" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
