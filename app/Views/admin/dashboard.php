<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<!-- Statistics Overview Widgets -->
<div class="admin-stats-grid">
    <div class="admin-stat-card card-primary">
        <div class="stat-card-body">
            <span class="stat-icon">📝</span>
            <div class="stat-details">
                <h3><?= esc($totalQuizzes) ?></h3>
                <p>Total Quizzes</p>
            </div>
        </div>
    </div>

    <div class="admin-stat-card card-success">
        <div class="stat-card-body">
            <span class="stat-icon">👥</span>
            <div class="stat-details">
                <h3><?= esc($totalUsers) ?></h3>
                <p>Total Registered Users</p>
            </div>
        </div>
    </div>

    <div class="admin-stat-card card-warning">
        <div class="stat-card-body">
            <span class="stat-icon">⚡</span>
            <div class="stat-details">
                <h3><?= esc($totalAttempts) ?></h3>
                <p>Completed Attempts</p>
            </div>
        </div>
    </div>

    <div class="admin-stat-card card-info">
        <div class="stat-card-body">
            <span class="stat-icon">🎯</span>
            <div class="stat-details">
                <h3><?= esc($avgPercentage) ?>%</h3>
                <p>Average Performance</p>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity Block -->
<div class="admin-section-card">
    <h2 class="admin-card-title">Recent Quiz Takers</h2>
    <?php if (!empty($recentAttempts)): ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Quiz Name</th>
                        <th>Score</th>
                        <th>Percentage</th>
                        <th>Completed At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentAttempts as $attempt): ?>
                        <tr>
                            <td>
                                <strong><?= esc($attempt->user_name ?? 'Guest User') ?></strong>
                            </td>
                            <td><?= esc($attempt->quiz_title) ?></td>
                            <td><?= esc($attempt->score) ?> / <?= esc($attempt->total_questions) ?></td>
                            <td>
                                <span class="badge-pct"><?= esc($attempt->percentage) ?>%</span>
                            </td>
                             <td><?= $attempt->completed_at ? date('d M Y, h:i A', strtotime($attempt->completed_at)) : 'In Progress' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="admin-empty-state">
            <p>No recent quiz plays registered yet.</p>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
