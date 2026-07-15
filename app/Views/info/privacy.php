<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="section-container" style="max-width: 800px; margin: 40px auto; padding: 0 16px; text-align: left;">
    <div class="legacy-card" style="background-color: var(--white); border: 1px solid var(--border); border-radius: var(--radius-xl); box-shadow: var(--shadow-sm); padding: 48px 32px; line-height: 1.7; color: var(--text-muted);">
        <h1 style="font-size: 32px; font-weight: 800; margin-bottom: 24px; color: var(--text); background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; display: inline-block;">Privacy Policy</h1>
        
        <p style="margin-bottom: 20px;">Last updated: <?= date('F d, Y') ?></p>
        
        <p style="margin-bottom: 24px;">At QuizTv, accessible from <?= base_url() ?>, one of our main priorities is the privacy of our visitors. This Privacy Policy document contains types of information that is collected and recorded by QuizTv and how we use it.</p>
        
        <h2 style="font-size: 20px; font-weight: 700; margin: 28px 0 12px; color: var(--text);">1. Information We Collect</h2>
        <p style="margin-bottom: 16px;">We collect data to provide better service and experience. This includes:</p>
        <ul style="margin-left: 20px; margin-bottom: 24px;">
            <li style="margin-bottom: 8px;"><strong>Account Details</strong>: If you register, we collect credentials (name, email, encrypted passwords) to manage your attempt logs and score history.</li>
            <li style="margin-bottom: 8px;"><strong>Attempt Logs & Scores</strong>: We store quiz progression statistics, round levels reached, and answers submitted, linked either to your registered profile or temporary guest tokens.</li>
            <li style="margin-bottom: 8px;"><strong>Device & Log Data</strong>: IP address, browser type, referrer details, timestamps, and page request telemetry.</li>
        </ul>

        <h2 style="font-size: 20px; font-weight: 700; margin: 28px 0 12px; color: var(--text);">2. Cookies and Advertising Disclosures</h2>
        <p style="margin-bottom: 16px;">QuizTv uses standard cookies to maintain session states (e.g. keeping track of your guest tokens and attempts) and prevent CSRF tokens security vulnerabilities.</p>
        <p style="margin-bottom: 24px;"><strong>Google Ad Manager & GPT</strong>: We utilize Google Publisher Tags (GPT) to render third-party banner advertisements. These vendors use cookies (such as the DART cookie) to serve ads to our users based on their visits to our site and other sites on the internet. You can manage your ad personalization settings inside Google Ad settings page.</p>

        <h2 style="font-size: 20px; font-weight: 700; margin: 28px 0 12px; color: var(--text);">3. How We Use Your Information</h2>
        <p style="margin-bottom: 16px;">We use collected details to:</p>
        <ul style="margin-left: 20px; margin-bottom: 24px;">
            <li style="margin-bottom: 8px;">Operate and maintain state-driven quiz engines.</li>
            <li style="margin-bottom: 8px;">Store scores and provide performance statistics dashboards.</li>
            <li style="margin-bottom: 8px;">Protect against security threats, bots, and double attempts.</li>
        </ul>

        <h2 style="font-size: 20px; font-weight: 700; margin: 28px 0 12px; color: var(--text);">4. Security of Data</h2>
        <p style="margin-bottom: 24px;">The security of your data is important to us, but remember that no method of transmission over the Internet, or method of electronic storage is 100% secure. While we strive to protect your credentials, we cannot guarantee absolute database security.</p>
    </div>
</div>
<?= $this->endSection() ?>
