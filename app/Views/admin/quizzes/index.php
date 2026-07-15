<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="admin-action-row">
    <a href="<?= site_url('admin/quizzes/create') ?>" class="btn btn-primary">+ Create New Quiz</a>
</div>

<div class="admin-section-card">
    <h2 class="admin-card-title">All Quizzes</h2>
    <?php if (!empty($quizzes)): ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Thumbnail</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Difficulty</th>
                        <th>Pass Rate</th>
                        <th>Attempts</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($quizzes as $quiz): ?>
                        <tr>
                            <td>
                                <?php if ($quiz->thumbnail): ?>
                                    <img src="<?= (str_starts_with($quiz->thumbnail, 'http://') || str_starts_with($quiz->thumbnail, 'https://')) ? esc($quiz->thumbnail) : base_url('uploads/quizzes/' . esc($quiz->thumbnail)) ?>" alt="Thumbnail" class="admin-thumbnail-small">
                                <?php else: ?>
                                    <div class="admin-thumbnail-placeholder">🧠</div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= esc($quiz->title) ?></strong>
                                <br>
                                <small class="text-subtle"><?= esc($quiz->slug) ?></small>
                            </td>
                            <td>🏷️ <?= esc($quiz->category_name) ?></td>
                            <td>
                                <span class="badge-difficulty diff-<?= esc($quiz->difficulty) ?>"><?= ucfirst(esc($quiz->difficulty)) ?></span>
                            </td>
                            <td><?= esc($quiz->pass_rate) ?>%</td>
                            <td><?= number_format($quiz->total_attempts) ?></td>
                            <td>
                                <?php if ((int)$quiz->is_active === 1): ?>
                                    <span class="badge-status status-active">Active</span>
                                <?php else: ?>
                                    <span class="badge-status status-inactive">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="admin-actions-cell">
                                    <a href="<?= site_url('admin/questions/' . esc($quiz->id)) ?>" class="btn btn-outline btn-sm">Questions</a>
                                    <a href="<?= site_url('admin/quizzes/edit/' . esc($quiz->id)) ?>" class="btn btn-success btn-sm">Edit</a>
                                    
                                    <form action="<?= site_url('admin/quizzes/delete/' . esc($quiz->id)) ?>" method="POST" class="inline-form delete-form">
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
            <p>No quizzes registered yet. Start by creating one!</p>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
