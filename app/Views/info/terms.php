<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="section-container" style="max-width: 800px; margin: 40px auto; padding: 0 16px; text-align: left;">
    <div class="legacy-card" style="background-color: var(--white); border: 1px solid var(--border); border-radius: var(--radius-xl); box-shadow: var(--shadow-sm); padding: 48px 32px; line-height: 1.7; color: var(--text-muted);">
        <h1 style="font-size: 32px; font-weight: 800; margin-bottom: 24px; color: var(--text); background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; display: inline-block;">Terms of Use</h1>
        
        <p style="margin-bottom: 20px;">Last updated: <?= date('F d, Y') ?></p>
        
        <p style="margin-bottom: 24px;">Welcome to QuizTv. By accessing or using our website, you agree to comply with and be bound by the following Terms of Use. Please review these terms carefully.</p>
        
        <h2 style="font-size: 20px; font-weight: 700; margin: 28px 0 12px; color: var(--text);">1. Acceptance of Agreement</h2>
        <p style="margin-bottom: 24px;">You agree to the terms and conditions outlined in this Terms of Use Agreement with respect to our site. This Agreement constitutes the entire and only agreement between us and you, and supersedes all prior agreements, representations, and understandings with respect to the site.</p>
        
        <h2 style="font-size: 20px; font-weight: 700; margin: 28px 0 12px; color: var(--text);">2. Description of Service</h2>
        <p style="margin-bottom: 24px;">QuizTv provides users with access to an interactive, zero-reload quiz engine, leaderboard logs, stage-wise checks, and dynamic feedback checklists. All services are provided "as-is" and for educational, self-challenge, and general learning purposes.</p>

        <h2 style="font-size: 20px; font-weight: 700; margin: 28px 0 12px; color: var(--text);">3. Disclaimer of Quiz Scores</h2>
        <p style="margin-bottom: 24px;"><strong>No Professional Evaluation</strong>: All scores, grading percentages, passed/failed labels, and stage checkpoints are automatic calculations meant for entertainment, self-reflection, and quick learning checks. QuizTv scores do NOT represent official academic results, entrance qualifications, clinical diagnoses, employment eligibility, or military recruitment decisions.</p>

        <h2 style="font-size: 20px; font-weight: 700; margin: 28px 0 12px; color: var(--text);">4. Proper Use</h2>
        <p style="margin-bottom: 24px;">You agree not to bypass, reverse-engineer, or attempt to query our internal AJAX endpoints (`/api/quiz/`) using script automated loops, scrape our question databases, or execute DDoS flooding attacks against our server infrastructure.</p>
    </div>
</div>
<?= $this->endSection() ?>
