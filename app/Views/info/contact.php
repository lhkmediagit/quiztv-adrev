<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="section-container" style="max-width: 800px; margin: 40px auto; padding: 0 16px; text-align: left;">
    <div class="legacy-card" style="background-color: var(--white); border: 1px solid var(--border); border-radius: var(--radius-xl); box-shadow: var(--shadow-sm); padding: 48px 32px; line-height: 1.7; color: var(--text-muted);">
        <h1 style="font-size: 32px; font-weight: 800; margin-bottom: 8px; color: var(--text); background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; display: inline-block;">Contact QuizTv</h1>
        
        <p style="margin-bottom: 28px;">Use this page for editorial, privacy, accessibility, or support questions.</p>
        
        <h2 style="font-size: 20px; font-weight: 700; margin: 24px 0 12px; color: var(--text);">General Contact</h2>
        <p style="margin-bottom: 16px;">For general support, editorial questions, accessibility issues, privacy requests, quiz corrections, advertising questions, or technical problems, use the email address shown below. Please include the page URL and enough detail for us to understand the issue.</p>
        <p style="margin-bottom: 24px;">
            Email: <a href="mailto:support@quiztv.com" style="color: var(--primary); text-decoration: none; font-weight: 600;">support@quiztv.com</a>
        </p>
        
        <h2 style="font-size: 20px; font-weight: 700; margin: 24px 0 12px; color: var(--text);">Privacy Requests</h2>
        <p style="margin-bottom: 24px;">If you are contacting us about privacy rights, include the country or state you are writing from, the browser/device used, and the request you want us to review. Do not send sensitive documents unless we specifically ask for them.</p>

        <h2 style="font-size: 20px; font-weight: 700; margin: 24px 0 12px; color: var(--text);">Quiz Corrections</h2>
        <p style="margin-bottom: 24px;">To report a question issue, include the quiz name, question number, the answer you selected, and why you believe the prompt or explanation should be reviewed.</p>

        <h2 style="font-size: 20px; font-weight: 700; margin: 24px 0 12px; color: var(--text);">Advertising or Technical Issues</h2>
        <p style="margin-bottom: 16px;">For ad, loading, or display problems, include the page URL, approximate time, device, browser, and screenshots if available.</p>
    </div>
</div>
<?= $this->endSection() ?>
