<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="section-container" style="max-width: 800px; margin: 40px auto; padding: 0 16px; text-align: left;">
    <div class="legacy-card" style="background-color: var(--white); border: 1px solid var(--border); border-radius: var(--radius-xl); box-shadow: var(--shadow-sm); padding: 48px 32px; line-height: 1.7; color: var(--text-muted);">
        <h1 style="font-size: 32px; font-weight: 800; margin-bottom: 24px; color: var(--text); background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; display: inline-block;">Disclaimer</h1>
        
        <p style="margin-bottom: 20px;">Last updated: <?= date('F d, Y') ?></p>
        
        <p style="margin-bottom: 24px;">All informational content, testing, evaluation, scoring scales, difficulty classifications, and round stage levels provided on the QuizTv website (<?= base_url() ?>) are published in good faith and for general educational, self-challenge, and entertainment purposes only.</p>
        
        <h2 style="font-size: 20px; font-weight: 700; margin: 28px 0 12px; color: var(--text);">1. No Warranty or Endorsement</h2>
        <p style="margin-bottom: 24px;">QuizTv makes no warranties about the completeness, reliability, or 100% accuracy of these questions and answers. Any action you take upon the information you find on this website is strictly at your own risk. QuizTv will not be liable for any losses or damages in connection with the use of our site.</p>
        
        <h2 style="font-size: 20px; font-weight: 700; margin: 28px 0 12px; color: var(--text);">2. Scores Are Estimates</h2>
        <p style="margin-bottom: 24px;"><strong>Not Official Credentials</strong>: QuizTv is an unofficial educational test platform. All quiz outcomes (such as military entrance exam practice, medical/health checks, IQ assessments, or grammar levels) are automatic calculations and estimates. They do not constitute official clinical, medical, legal, admissions, employment, or military recruitment decisions.</p>

        <h2 style="font-size: 20px; font-weight: 700; margin: 28px 0 12px; color: var(--text);">3. Third Party Links & Advertisements</h2>
        <p style="margin-bottom: 24px;">Our website may display third-party advertisements or external links (e.g. via Google Ad Manager or GPT). We do not control or endorse the content, policies, or products offered in advertisements, nor do we accept liability for actions you take after clicking outgoing links.</p>
    </div>
</div>
<?= $this->endSection() ?>
