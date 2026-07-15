<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="dashboard-wrapper">
    <div class="dashboard-container">
        
        <!-- Dashboard Welcome Banner -->
        <header class="dashboard-hero">
            <div class="user-profile-meta">
                <div class="user-avatar-large">
                    <?php 
                    $db = \Config\Database::connect();
                    $userRow = $db->table('users')->where('id', session()->get('user_id'))->get()->getRow();
                    if ($userRow && $userRow->avatar): 
                    ?>
                        <img src="<?= esc($userRow->avatar) ?>" alt="<?= esc($userRow->name) ?>" class="avatar-img">
                    <?php else: ?>
                        <span class="avatar-char"><?= strtoupper(substr(session()->get('name'), 0, 1)) ?></span>
                    <?php endif; ?>
                </div>
                <div>
                    <h1 class="dashboard-welcome-title">Welcome back, <?= esc(session()->get('name')) ?>!</h1>
                    <p class="dashboard-welcome-subtitle">Track your performance and improve your knowledge.</p>
                </div>
            </div>
            
            <a href="<?= site_url('user/profile') ?>" class="btn btn-outline">Edit Profile</a>
        </header>

        <!-- Analytics Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📝</div>
                <div class="stat-info">
                    <span class="stat-label">Quizzes Taken</span>
                    <span class="stat-value"><?= esc($totalAttempts) ?></span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">🎯</div>
                <div class="stat-info">
                    <span class="stat-label">Average Score</span>
                    <span class="stat-value"><?= esc($avgPercentage) ?>%</span>
                </div>
            </div>
        </div>

        <!-- Recent Activity Section -->
        <section class="dashboard-section">
            <div class="section-header">
                <h2 class="section-title">Recent Activity</h2>
                <a href="<?= site_url('user/history') ?>" class="btn btn-link">View All History &rarr;</a>
            </div>

            <?php if (!empty($recentAttempts)): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Quiz</th>
                                <th>Score</th>
                                <th>Percentage</th>
                                <th>Date Completed</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentAttempts as $attempt): ?>
                                <tr>
                                    <td data-label="Quiz"><strong><?= esc($attempt->quiz_title) ?></strong></td>
                                    <td data-label="Score"><?= esc($attempt->score) ?> / <?= esc($attempt->total_questions) ?></td>
                                    <td data-label="Percentage">
                                        <span class="badge-score"><?= esc($attempt->percentage) ?>%</span>
                                    </td>
                                    <td data-label="Date Completed"><?= $attempt->completed_at ? date('d M Y, h:i A', strtotime($attempt->completed_at)) : 'In Progress' ?></td>
                                    <td data-label="Action">
                                        <a href="<?= site_url('quiz/' . esc($attempt->quiz_slug) . '/play') ?>" class="btn btn-outline btn-sm">Retake</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <p class="empty-text">You haven't taken any quizzes yet!</p>
                    <a href="<?= site_url() ?>" class="btn btn-primary">Browse Quizzes</a>
                </div>
            <?php endif; ?>
        </section>

    </div>
</div>
<?= $this->endSection() ?>
