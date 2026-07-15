<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
$emoji = '🧠';
if (str_contains($quiz->slug, 'medicine')) $emoji = '🩺';
elseif (str_contains($quiz->slug, 'navy')) $emoji = '⚓';
elseif (str_contains($quiz->slug, 'airforce')) $emoji = '✈️';
elseif (str_contains($quiz->slug, 'connection')) $emoji = '🔮';
elseif (str_contains($quiz->slug, 'memory')) $emoji = '🧠';
elseif (str_contains($quiz->slug, 'iq')) $emoji = '🧩';
elseif (str_contains($quiz->slug, 'tools')) $emoji = '🔧';
elseif (str_contains($quiz->slug, 'vision')) $emoji = '👁️';
elseif (str_contains($quiz->slug, 'zodiac')) $emoji = '🌟';
elseif (str_contains($quiz->slug, 'grammar')) $emoji = '📚';
elseif (str_contains($quiz->slug, 'history')) $emoji = '⏳';
?>

<div class="section-container" style="max-width: 800px; margin: 40px auto;">
    <!-- Custom NDTV Stats Header (for quiztv slug) -->
    <?php if ($quiz->slug === 'quiztv'): ?>
        <div class="ndtv-stats-header">
            <div class="ndtv-stats-item">
                <p style="margin: 0; font-weight: 500; font-style: italic; color: #64748b;">More than</p>
                <h2>21074</h2>
                <p style="margin: 0; font-weight: 600; color: #334155;">Have Played this quiz</p>
            </div>
            <div class="ndtv-stats-divider">
                <p style="margin: 0; font-weight: 500; font-style: italic; color: #64748b;">Just</p>
                <h2>285</h2>
                <p style="margin: 0; font-weight: 600; color: #334155;">Are Smarter (for now)</p>
            </div>
        </div>

        <!-- Custom NDTV Title Card -->
        <div class="legacy-card legacy-start" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border: 1px solid #1e293b; border-radius: var(--radius-xl); box-shadow: var(--shadow-sm); margin-bottom: 30px; overflow: hidden; padding: 0; text-align: center;">
            <div style="background: linear-gradient(135deg, #3a9f9d 0%, #2b7a78 100%); padding: 50px 24px; text-align: center; color: white;">
                <h1 style="color: white; font-size: 38px; font-weight: 800; margin: 0; font-family: 'Outfit', sans-serif; letter-spacing: -0.5px; text-shadow: 0 2px 4px rgba(0,0,0,0.1);"><?= esc($quiz->title) ?></h1>
            </div>
            <div style="padding: 32px 24px;">
                <a href="<?= site_url('quiz/' . esc($quiz->slug) . '/play') ?>" class="legacy-primary" id="start-quiz-btn" style="text-decoration: none; display: inline-block; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; padding: 16px 48px; font-size: 18px; font-weight: 800; border-radius: 50px; box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3); transition: all 0.2s ease; border: none; font-family: 'Outfit', sans-serif;">Start Quiz ▶</a>
                <p class="legacy-ad-note" style="margin-top: 14px; font-size: 12px; color: #94a3b8; font-weight: 500;"><span class="legacy-shield" style="color: #10b981; margin-right: 4px;">✓</span> 100% Free & Instant Results</p>
            </div>
        </div>
    <?php else: ?>
        <!-- Original Legacy Card -->
        <div class="legacy-card legacy-start" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border: 1px solid #1e293b; border-radius: var(--radius-xl); box-shadow: var(--shadow-sm); margin-bottom: 30px; text-align: center; padding: 48px 32px;">
            <span class="legacy-badge"><?= $emoji ?></span>
            <h1 style="color: #ffffff; font-size: 32px; font-weight: 800; margin-bottom: 12px; line-height: 1.3; font-family: 'Outfit', sans-serif;"><?= esc($quiz->title) ?></h1>
            <p class="legacy-sub" style="color: #94a3b8; font-size: 16px; max-width: 580px; margin: 0 auto 24px; line-height: 1.6;"><?= esc($quiz->description) ?></p>
            
            <div class="legacy-social" style="display: flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: 24px;">
                <div class="legacy-avatars" style="display: flex;">
                    <div class="legacy-avatar" style="background-image: url('https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=80&fit=crop&q=60'); width: 28px; height: 28px; border-radius: 50%; border: 2px solid #1e293b; margin-right: -8px;"></div>
                    <div class="legacy-avatar" style="background-image: url('https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=80&fit=crop&q=60'); width: 28px; height: 28px; border-radius: 50%; border: 2px solid #1e293b; margin-right: -8px;"></div>
                    <div class="legacy-avatar" style="background-image: url('https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=80&fit=crop&q=60'); width: 28px; height: 28px; border-radius: 50%; border: 2px solid #1e293b;"></div>
                </div>
                <span class="legacy-social__text" style="font-size: 14px; text-align: left;">
                    <span class="legacy-social__primary" style="color: #ffffff; font-weight: 700;"><?= number_format($quiz->total_attempts) ?>+</span> 
                    <span class="legacy-social__muted" style="color: #94a3b8;">people tried this challenge</span>
                </span>
            </div>

            <a href="<?= site_url('quiz/' . esc($quiz->slug) . '/play') ?>" class="legacy-primary" id="start-quiz-btn" style="text-decoration: none; display: inline-block; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; padding: 16px 48px; font-size: 18px; font-weight: 800; border-radius: 50px; box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3); transition: all 0.2s ease; border: none; font-family: 'Outfit', sans-serif;">Start Quiz ▶</a>
            <p class="legacy-ad-note" style="margin-top: 14px; font-size: 12px; color: #94a3b8; font-weight: 500;"><span class="legacy-shield" style="color: #10b981; margin-right: 4px;">✓</span> 100% Free & Instant Results</p>
        </div>
    <?php endif; ?>

    <!-- Ad Banner: Quiz Landing Top -->
    <?= render_banner_slot('quiz_sidebar', 'ad-quiz-sidebar') ?>

    <!-- About Section Content (Crawl Dynamic) -->
    <?php if ($quiz->slug === 'quiztv'): ?>
        <div style="background-color: var(--white); border: 1px solid var(--border); border-radius: var(--radius-xl); box-shadow: var(--shadow-sm); padding: 32px; margin-top: 30px; text-align: left;">
            <h2 style="font-size: 28px; font-weight: 800; color: #1e293b; margin: 0 0 16px; font-family: 'Outfit', sans-serif;">About the quiz</h2>
            <p style="font-size: 16px; color: #475569; margin: 0; line-height: 1.6; font-weight: 500;">Some questions look simple—until you realize they’re not asking what you thought they were. It’s not about what you know, but how carefully you think. Let’s see how many tricks you can catch.</p>
        </div>
    <?php elseif (!empty($aboutHtml)): ?>
        <div class="quiz-about-panel-wrapper" style="margin-top: 30px;">
            <?= $aboutHtml ?>
        </div>
    <?php else: ?>
        <!-- Fallback Info Detail Panels -->
        <div class="legacy-card" style="background-color: var(--white); border: 1px solid var(--border); border-radius: var(--radius-xl); box-shadow: var(--shadow-sm); padding: 32px; margin-top: 30px; text-align: left;">
            <div style="display: flex; gap: 16px; margin-bottom: 24px; align-items: flex-start;">
                <span style="font-size: 28px;">⏱</span>
                <div>
                    <h3 style="font-size: 16px; font-weight: 700; margin: 0 0 4px; color: var(--text);">Duration & Structure</h3>
                    <p style="font-size: 14px; color: var(--text-muted); margin: 0; line-height: 1.5;">This challenge is estimated to take around <?= esc($quiz->duration_minutes) ?> minutes. It features dynamic stage checkpoints with explanations for incorrect answers to help you learn as you progress.</p>
                </div>
            </div>

            <div style="display: flex; gap: 16px; margin-bottom: 24px; align-items: flex-start;">
                <span style="font-size: 28px;">📊</span>
                <div>
                    <h3 style="font-size: 16px; font-weight: 700; margin: 0 0 4px; color: var(--text);">Difficulty Level: <span class="hub-chip"><?= ucfirst(esc($quiz->difficulty)) ?></span></h3>
                    <p style="font-size: 14px; color: var(--text-muted); margin: 0; line-height: 1.5;">The questions scale from accessible concepts in the initial stages to complex patterns and scenarios in the final rounds.</p>
                </div>
            </div>

            <div style="display: flex; gap: 16px; align-items: flex-start;">
                <span style="font-size: 28px;">🎯</span>
                <div>
                    <h3 style="font-size: 16px; font-weight: 700; margin: 0 0 4px; color: var(--text);">Target Pass Rate: <?= esc($quiz->pass_rate) ?>%</h3>
                    <p style="font-size: 14px; color: var(--text-muted); margin: 0; line-height: 1.5;">Compare your final performance with previous participants and unlock custom performance dimension reviews.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Recommendations Grid -->
    <?php if (!empty($recommendations)): ?>
        <section style="margin-top: 50px;">
            <h2 style="font-size: 22px; font-weight: 800; color: var(--text); margin-bottom: 24px; text-align: left;">Try Another Quiz</h2>
            <div class="hub-quiz-grid">
                <?php foreach ($recommendations as $rec): ?>
                    <a href="<?= site_url('quiz/' . esc($rec->slug)) ?>" class="hub-quiz-card">
                        <div class="hub-quiz-card__banner" style="background: var(--primary-gradient);">
                            <?php if ($rec->thumbnail): ?>
                                <img src="<?= (str_starts_with($rec->thumbnail, 'http://') || str_starts_with($rec->thumbnail, 'https://')) ? esc($rec->thumbnail) : base_url('uploads/quizzes/' . esc($rec->thumbnail)) ?>" alt="<?= esc($rec->title) ?>">
                            <?php else: ?>
                                <div class="quiz-card-img-placeholder" style="width: 100%; height: 100%;">
                                    <span class="placeholder-icon">🧠</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="hub-quiz-card__body">
                            <div class="hub-quiz-card__meta">
                                <span class="hub-chip"><?= esc($rec->difficulty) ?></span>
                                <span>🏷️ <?= esc($rec->category_name) ?></span>
                            </div>
                            <h3><?= esc($rec->title) ?></h3>
                            <p><?= character_limiter(esc($rec->description), 95, '...') ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const startBtn = document.getElementById('start-quiz-btn');
    if (!startBtn) return;

    let clickCount = 0;
    let proceedCalled = false;
    const playUrl = startBtn.href;

    const proceedToPlay = () => {
        if (proceedCalled) return;
        proceedCalled = true;
        window.location.href = playUrl;
    };

    startBtn.addEventListener('click', function(e) {
        const adConfig = <?= json_encode(get_ad_config()) ?>;
        
        if (adConfig && adConfig.enabled && adConfig.rewarded && adConfig.rewarded.enabled && adConfig.rewarded.slot && window.QuizTvAds) {
            e.preventDefault();
            clickCount++;

            if (clickCount >= 3) {
                console.log('[QuizTvAds] Ad bypassed by click limit (3 clicks). Navigating to play.');
                proceedToPlay();
                return;
            }

            const remaining = 3 - clickCount;
            startBtn.innerHTML = `Loading Ad... (${remaining} click${remaining > 1 ? 's' : ''} to skip)`;

            QuizTvAds.showRewarded(
                adConfig.rewarded.slot,
                null,
                () => {
                    startBtn.innerHTML = 'Start Quiz ▶';
                    proceedToPlay();
                }
            );
        }
    });
});
</script>
<?= $this->endSection() ?>
