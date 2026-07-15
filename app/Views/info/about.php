<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="section-container" style="max-width: 800px; margin: 40px auto; padding: 0 16px;">
    <div class="legacy-card" style="background-color: var(--white); border: 1px solid var(--border); border-radius: var(--radius-xl); box-shadow: var(--shadow-sm); padding: 48px 32px; text-align: center;">
        <span class="overlay-icon" style="font-size: 64px; display: block; margin-bottom: 20px;">⚡</span>
        <h1 style="font-size: 32px; font-weight: 800; margin-bottom: 16px; background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; display: inline-block;">About QuizTv</h1>
        <p class="overlay-desc" style="font-size: 18px; color: var(--text-muted); line-height: 1.6; margin-bottom: 30px;">
            Welcome to <strong>QuizTv</strong>, a premium educational and general knowledge quiz platform designed for self-challenge, entertainment, and rapid learning.
        </p>

        <div style="text-align: left; margin-top: 40px;">
            <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 12px; color: var(--text);">Our Vision</h3>
            <p style="font-size: 15px; color: var(--text-muted); line-height: 1.6; margin-bottom: 24px;">
                Our mission is to make learning engaging and interactive. We believe in providing challenging tests that stimulate curiosity, scalability across difficulties, and zero friction, page-reload-free quiz execution.
            </p>

            <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 12px; color: var(--text);">Key Platform Features</h3>
            <ul style="list-style: none; padding: 0; margin-bottom: 24px;">
                <li style="display: flex; align-items: flex-start; gap: 12px; margin-bottom: 12px; font-size: 15px; color: var(--text-muted);">
                    <span style="color: var(--primary); font-weight: bold;">⚡</span>
                    <div>
                        <strong>Zero Reload Engine</strong>: Interactive state-driven quiz mechanics without irritating page refreshes.
                    </div>
                </li>
                <li style="display: flex; align-items: flex-start; gap: 12px; margin-bottom: 12px; font-size: 15px; color: var(--text-muted);">
                    <span style="color: var(--primary); font-weight: bold;">❤️</span>
                    <div>
                        <strong>Lifeline (Lives) System</strong>: Gain 2 lives per attempt. Revive yourself using ad-rewards if you get stuck!
                    </div>
                </li>
                <li style="display: flex; align-items: flex-start; gap: 12px; font-size: 15px; color: var(--text-muted);">
                    <span style="color: var(--primary); font-weight: bold;">📊</span>
                    <div>
                        <strong>Dynamic Explanation Checkpoints</strong>: Real-time correct/incorrect display and detail cards to learn on the fly.
                    </div>
                </li>
            </ul>
        </div>
        
        <div style="margin-top: 40px; padding-top: 24px; border-top: 1px solid var(--border);">
            <a href="<?= base_url() ?>" class="btn btn-primary btn-lg">Explore Quizzes</a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
