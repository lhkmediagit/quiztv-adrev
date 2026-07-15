<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Hero Section -->
<div class="hub-hero">
    <div class="hub-hero__copy">
        <h1>Quick quizzes. <span>Sharper mind.</span></h1>
        <p>Test your brain, personality, memory, and world knowledge with quick quizzes that take around 6 minutes. Pick one and get your result instantly.</p>
    </div>
    <div class="hub-hero__stats">
        <div class="hub-stat">
            <span class="hub-stat__icon hub-stat__icon--mint">
                <svg class="hub-stat__svg" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            </span>
            <div>
                <strong>50+</strong>
                <span class="hub-stat__label">Quizzes</span>
            </div>
        </div>
        <div class="hub-stat">
            <span class="hub-stat__icon hub-stat__icon--blue">
                <svg class="hub-stat__svg" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            </span>
            <div>
                <strong>6 min</strong>
                <span class="hub-stat__label">Avg. time</span>
            </div>
        </div>
        <div class="hub-stat">
            <span class="hub-stat__icon hub-stat__icon--violet">
                <svg class="hub-stat__svg" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87m-4-12a4 4 0 0 1 0 7.75"/></svg>
            </span>
            <div>
                <strong>85,000+</strong>
                <span class="hub-stat__label">Players</span>
            </div>
        </div>
        <div class="hub-stat">
            <span class="hub-stat__icon hub-stat__icon--gold">
                <svg class="hub-stat__svg" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg>
            </span>
            <div>
                <strong>Instant</strong>
                <span class="hub-stat__label">Results</span>
            </div>
        </div>
    </div>
</div>

<!-- Ad Banner: Home Top (between hero and categories) -->
<?= render_banner_slot('home_top', 'ad-home-top') ?>



<!-- Active Quizzes List -->
<section class="section-container">
    <div class="section-header">
        <h2 class="section-title">Active Quizzes</h2>
    </div>
    <div class="hub-quiz-grid">
        <?php if (!empty($quizzes)): 
            $gradients = [
                'linear-gradient(135deg, #f43f5e 0%, #ec4899 100%)', // Pink / Red
                'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)', // Blue / Royal Indigo
                'linear-gradient(135deg, #10b981 0%, #059669 100%)', // Mint / Emerald
                'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)', // Amber / Orange
                'linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%)', // Violet / Purple
                'linear-gradient(135deg, #06b6d4 0%, #0891b2 100%)', // Cyan / Teal
                'linear-gradient(135deg, #e11d48 0%, #9f1239 100%)', // Rose / Crimson
                'linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)', // Indigo / Purple-Blue
            ];
            $gradient_index = 0;
            foreach ($quizzes as $quiz): 
                $card_gradient = $gradients[$gradient_index % count($gradients)];
                $gradient_index++;
        ?>
                <a href="<?= site_url('quiz/' . esc($quiz->slug)) ?>" class="hub-quiz-card">
                    <div class="hub-quiz-card__banner" style="background: <?= $card_gradient ?>;">
                        <?php if ($quiz->thumbnail): ?>
                            <img src="<?= (str_starts_with($quiz->thumbnail, 'http://') || str_starts_with($quiz->thumbnail, 'https://')) ? esc($quiz->thumbnail) : base_url('uploads/quizzes/' . esc($quiz->thumbnail)) ?>" alt="<?= esc($quiz->title) ?>">
                        <?php else: ?>
                            <div class="quiz-card-img-placeholder" style="width: 100%; height: 100%;">
                                <span class="placeholder-icon">🧠</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="hub-quiz-card__body">
                        <div class="hub-quiz-card__meta">
                            <span class="hub-chip"><?= esc($quiz->difficulty) ?></span>
                            <span>🏷️ <?= esc($quiz->category_name) ?></span>
                        </div>
                        <h3><?= esc($quiz->title) ?></h3>
                        <p><?= character_limiter(esc($quiz->description), 95, '...') ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state" style="grid-column: 1 / -1; text-align: center; padding: 40px; border: 1px dashed var(--border); border-radius: var(--radius-md);">
                <p>No active quizzes found. Check back later!</p>
            </div>
        <?php endif; ?>
    </div>
</section>
<?= $this->endSection() ?>
