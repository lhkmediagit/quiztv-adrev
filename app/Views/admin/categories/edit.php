<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="admin-section-card max-w-lg">
    <h2 class="admin-card-title">Modify Category Configuration</h2>

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

    <form action="<?= site_url('admin/categories/update/' . esc($category->id)) ?>" method="POST" class="admin-form">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="name" class="form-label">Category Name</label>
            <input type="text" name="name" id="name" class="form-control" value="<?= esc(old('name', $category->name)) ?>" required>
        </div>



        <div class="form-actions-footer">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="<?= site_url('admin/categories') ?>" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
