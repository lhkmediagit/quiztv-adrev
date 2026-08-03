<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="admin-stacked-layout" style="max-width: 800px; margin: 0 auto;">
    
    <!-- Single Question Form -->
    <div class="admin-section-card">
        <h2 class="admin-card-title">Add Single Question</h2>
        
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

        <form action="<?= site_url('admin/questions/store') ?>" method="POST" enctype="multipart/form-data" class="admin-form">
            <?= csrf_field() ?>
            <input type="hidden" name="quiz_id" value="<?= esc($quiz->id) ?>">

            <div class="form-row">
                <div class="form-group col-12">
                    <label for="order_index" class="form-label">Order Index (sorting)</label>
                    <input type="number" name="order_index" id="order_index" class="form-control" min="1" value="<?= old('order_index', $nextOrder) ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="question" class="form-label">Question Text</label>
                <textarea name="question" id="question" class="form-control" rows="3" placeholder="e.g. Which organ is responsible for blood filtration?" required><?= old('question') ?></textarea>
            </div>


            <div class="form-group">
                <label class="form-label">Options</label>
                <div class="option-inputs">
                    <div class="option-input-row">
                        <label class="option-label">Option 1</label>
                        <input type="text" name="option1" class="form-control" value="<?= old('option1') ?>" placeholder="Choice 1" required>
                    </div>
                    <div class="option-input-row">
                        <label class="option-label">Option 2</label>
                        <input type="text" name="option2" class="form-control" value="<?= old('option2') ?>" placeholder="Choice 2" required>
                    </div>
                    <div class="option-input-row">
                        <label class="option-label">Option 3</label>
                        <input type="text" name="option3" class="form-control" value="<?= old('option3') ?>" placeholder="Choice 3" required>
                    </div>
                    <div class="option-input-row">
                        <label class="option-label">Option 4</label>
                        <input type="text" name="option4" class="form-control" value="<?= old('option4') ?>" placeholder="Choice 4" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Correct Option</label>
                <div class="radio-group">
                    <label class="radio-inline">
                        <input type="radio" name="correct_option" value="1" <?= old('correct_option') == '1' ? 'checked' : '' ?> required> Option 1
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="correct_option" value="2" <?= old('correct_option', '2') == '2' ? 'checked' : '' ?>> Option 2
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="correct_option" value="3" <?= old('correct_option') == '3' ? 'checked' : '' ?>> Option 3
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="correct_option" value="4" <?= old('correct_option') == '4' ? 'checked' : '' ?>> Option 4
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label for="visual" class="form-label">Question Image size - 2:1 (eg. 760*360)</label>
                <input type="file" name="visual" id="visual" accept="image/*" class="form-control-file">
                <span class="form-help-text" style="display: block; margin-bottom: 8px;">Select a local image to upload.</span>
                
                <div style="position: relative; text-align: center; margin: 15px 0;">
                    <hr style="border: none; border-top: 1px solid var(--border); margin: 0;">
                    <span style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: var(--white); padding: 0 10px; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">OR</span>
                </div>

                <div class="form-group" style="margin-top: 15px; margin-bottom: 0;">
                    <label for="image_url" class="form-label" style="font-size: 13px; font-weight: 600; color: var(--text-muted);">Load Image from URL</label>
                    <input type="url" name="image_url" id="image_url" class="form-control" placeholder="https://example.com/image.jpg" value="<?= old('image_url') ?>">
                    <span class="form-help-text">Enter a direct image URL (e.g. JPG, PNG, WEBP).</span>
                </div>
            </div>

            <div class="form-group">
                <label for="explanation" class="form-label">Explanation</label>
                <textarea name="explanation" id="explanation" class="form-control" rows="3" placeholder="This feedback is displayed immediately to players after choosing an option."><?= old('explanation') ?></textarea>
            </div>

            <div class="form-actions-footer">
                <button type="submit" class="btn btn-primary">Save Question</button>
                <a href="<?= site_url('admin/questions/' . esc($quiz->id)) ?>" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>

    <!-- CSV Import Card -->
    <div class="admin-section-card">
        <h2 class="admin-card-title">CSV Bulk Import</h2>
        <div class="import-help-box">
            <p>Upload a comma-separated <strong>.csv</strong> file to add multiple questions instantly. The file must match the column order exactly:</p>
            <code class="csv-structure-code">question,explanation,option1,option2,option3,option4,correct_option,round_number,order_index,image_url</code>
            
            <ul class="csv-rules-list">
                <li><strong>correct_option</strong> must be: 1, 2, 3, or 4</li>
                <li>Ensure quotes surround fields containing commas</li>
                <li>Do not include a header row, or skip it programmatically</li>
                <li><strong>image_url</strong> (Optional): The full URL of the image to show alongside this question (e.g. <code>https://example.com/image.jpg</code>)</li>
            </ul>
        </div>

        <form action="<?= site_url('admin/questions/import/' . esc($quiz->id)) ?>" method="POST" enctype="multipart/form-data" class="admin-form mt-lg">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="csv_file" class="form-label">Select CSV File</label>
                <input type="file" name="csv_file" id="csv_file" accept=".csv" class="form-control-file" required>
            </div>

            <button type="submit" class="btn btn-success btn-block">Start Import Process</button>
        </form>
    </div>

</div>
<?= $this->endSection() ?>
