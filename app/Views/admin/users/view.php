<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="admin-action-row">
    <a href="<?= site_url('admin/users') ?>" class="btn btn-outline">&larr; Back to Users</a>
</div>

<div class="admin-grid-two-cols">
    <!-- User Profile Details Card -->
    <div class="admin-section-card">
        <h2 class="admin-card-title">User Account Info</h2>
        <div class="admin-user-detail-header">
            <div class="admin-avatar-large">
                <?php if ($user->avatar): ?>
                    <img src="<?= esc($user->avatar) ?>" alt="Avatar" class="avatar-img">
                <?php else: ?>
                    <span class="avatar-char"><?= strtoupper(substr($user->name, 0, 1)) ?></span>
                <?php endif; ?>
            </div>
            <div class="admin-user-meta">
                <h3><?= esc($user->name) ?></h3>
                <p class="text-subtle"><?= esc($user->email) ?></p>
            </div>
        </div>

        <div class="admin-detail-rows mt-lg">
            <div class="detail-row">
                <span class="detail-label font-bold">Account Status:</span>
                <?php if ((int)$user->is_banned === 1): ?>
                    <span class="badge-status status-inactive">Banned</span>
                <?php else: ?>
                    <span class="badge-status status-active">Active</span>
                <?php endif; ?>
            </div>

            <div class="detail-row">
                <span class="detail-label font-bold">Quizzes Completed:</span>
                <span><?= esc($user->total_quizzes_taken) ?> attempts</span>
            </div>

            <div class="detail-row">
                <span class="detail-label font-bold">Registered On:</span>
                <span><?= date('d M Y, h:i A', strtotime($user->created_at)) ?></span>
            </div>
        </div>
    </div>

    <!-- User Quiz History Card -->
    <div class="admin-section-card">
        <h2 class="admin-card-title">Attempt Performance History</h2>
        <?php if (!empty($attempts)): ?>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Quiz</th>
                            <th>Score</th>
                            <th>Percentage</th>
                            <th>Status</th>
                            <th>Date Taken</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attempts as $attempt): ?>
                            <tr>
                                <td><strong><?= esc($attempt->quiz_title) ?></strong></td>
                                <td><?= esc($attempt->score) ?> / <?= esc($attempt->total_questions) ?></td>
                                <td><?= esc($attempt->percentage) ?>%</td>
                                <td>
                                    <?php 
                                    $db = \Config\Database::connect();
                                    $quiz = $db->table('quizzes')->where('id', $attempt->quiz_id)->get()->getRow();
                                    $isPassed = $quiz && ($attempt->percentage >= $quiz->pass_rate);
                                    ?>
                                    <?php if ($isPassed): ?>
                                        <span class="badge-status status-active">Pass</span>
                                    <?php else: ?>
                                        <span class="badge-status status-inactive">Fail</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $attempt->completed_at ? date('d M Y', strtotime($attempt->completed_at)) : 'In Progress' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="admin-empty-state">
                <p>No attempts recorded for this user.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
