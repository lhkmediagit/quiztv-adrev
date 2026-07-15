<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="admin-action-row">
    <div>
        <a href="<?= site_url('admin/quizzes') ?>" class="btn btn-outline">&larr; Back to Quizzes</a>
        <a href="<?= site_url('admin/questions/create/' . esc($quiz->id)) ?>" class="btn btn-primary">+ Add Single Question</a>
    </div>
</div>

<div class="admin-section-card">
    <h2 class="admin-card-title">Questions in: <?= esc($quiz->title) ?></h2>
    <?php if (!empty($questions)): ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 80px;">Order</th>
                        <th style="width: 80px;">Round</th>
                        <th>Question Text</th>
                        <th>Options</th>
                        <th style="width: 100px;">Correct</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($questions as $q): ?>
                        <tr>
                            <td><strong>#<?= esc($q->order_index) ?></strong></td>
                            <td>Round <?= esc($q->round_number) ?></td>
                            <td>
                                <div class="admin-question-text"><?= esc($q->question) ?></div>
                                <div class="admin-explanation-text"><em>Explanation:</em> <?= esc($q->explanation) ?></div>
                            </td>
                            <td>
                                <ul class="admin-options-preview">
                                    <li class="<?= $q->correct_option == 1 ? 'text-success' : '' ?>">1: <?= esc($q->option1) ?></li>
                                    <li class="<?= $q->correct_option == 2 ? 'text-success' : '' ?>">2: <?= esc($q->option2) ?></li>
                                    <li class="<?= $q->correct_option == 3 ? 'text-success' : '' ?>">3: <?= esc($q->option3) ?></li>
                                    <li class="<?= $q->correct_option == 4 ? 'text-success' : '' ?>">4: <?= esc($q->option4) ?></li>
                                </ul>
                            </td>
                            <td>
                                <span class="badge-status status-active">Option <?= esc($q->correct_option) ?></span>
                            </td>
                            <td>
                                <div class="admin-actions-cell">
                                    <a href="<?= site_url('admin/questions/edit/' . esc($q->id)) ?>" class="btn btn-success btn-sm">Edit</a>
                                    
                                    <form action="<?= site_url('admin/questions/delete/' . esc($q->id)) ?>" method="POST" class="inline-form delete-form">
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
            <p>No questions added to this quiz yet.</p>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
