<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="admin-action-row">
    <a href="<?= site_url('admin/categories/create') ?>" class="btn btn-primary">+ Create New Category</a>
</div>

<div class="admin-section-card">
    <h2 class="admin-card-title">All Categories</h2>
    <?php if (!empty($categories)): ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Quizzes</th>
                        <th style="width: 200px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><strong><?= esc($cat->name) ?></strong></td>
                            <td><code class="text-xs"><?= esc($cat->slug) ?></code></td>
                            <td><span class="badge-pct"><?= esc($cat->quiz_count) ?> quizzes</span></td>
                            <td>
                                <div class="admin-actions-cell">
                                    <a href="<?= site_url('admin/categories/edit/' . esc($cat->id)) ?>" class="btn btn-success btn-sm">Edit</a>
                                    
                                    <form action="<?= site_url('admin/categories/delete/' . esc($cat->id)) ?>" method="POST" class="inline-form delete-form">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="admin-empty-state">
            <p>No categories registered. Start by creating one!</p>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
