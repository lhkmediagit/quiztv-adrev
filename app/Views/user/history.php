<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="dashboard-wrapper">
    <div class="dashboard-container">
        
        <header class="dashboard-hero">
            <div>
                <h1 class="dashboard-welcome-title">My Quiz History</h1>
                <p class="dashboard-welcome-subtitle">Review all your previous attempts and scores.</p>
            </div>
            <a href="<?= site_url('user/dashboard') ?>" class="btn btn-outline">&larr; Dashboard</a>
        </header>

        <section class="dashboard-section">
            <?php if (!empty($attempts)): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Quiz</th>
                                <th>Score</th>
                                <th>Percentage</th>
                                <th>Result</th>
                                <th>Date Taken</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($attempts as $attempt): ?>
                                <tr>
                                    <td data-label="Quiz">
                                        <strong><?= esc($attempt->quiz_title) ?></strong>
                                    </td>
                                    <td data-label="Score"><?= esc($attempt->score) ?> / <?= esc($attempt->total_questions) ?></td>
                                    <td data-label="Percentage"><strong><?= esc($attempt->percentage) ?>%</strong></td>
                                    <td data-label="Result">
                                        <?php 
                                        $db = \Config\Database::connect();
                                        $quiz = $db->table('quizzes')->where('id', $attempt->quiz_id)->get()->getRow();
                                        $isPassed = $quiz && ($attempt->percentage >= $quiz->pass_rate);
                                        ?>
                                        <?php if ($isPassed): ?>
                                            <span class="badge-status status-pass">Pass</span>
                                        <?php else: ?>
                                            <span class="badge-status status-fail">Fail</span>
                                        <?php endif; ?>
                                    </td>
                                    <td data-label="Date Taken"><?= $attempt->completed_at ? date('d M Y, h:i A', strtotime($attempt->completed_at)) : 'In Progress' ?></td>
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
                    <p class="empty-text">You haven't attempted any quizzes yet.</p>
                    <a href="<?= site_url() ?>" class="btn btn-primary">Take Your First Quiz</a>
                </div>
            <?php endif; ?>
        </section>

    </div>
</div>
<?= $this->endSection() ?>
