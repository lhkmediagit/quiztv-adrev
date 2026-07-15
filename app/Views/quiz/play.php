<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="play-wrapper">
    <div class="play-container" id="quiz-container">

        <!-- Legacy hidden elements for script fallback compatibility -->
        <div class="progress-container" style="display: none !important;">
            <div class="progress-bar" id="quiz-progress" style="width: 0%"></div>
        </div>
        <div class="quiz-status-header" id="quiz-status-header" style="display: none !important;">
            <span class="round-badge" id="quiz-round-badge">Round 1</span>
            <span class="question-counter" id="quiz-question-counter">Question 1 of 10</span>
            <span class="score-display">Score: <strong id="quiz-live-score">0</strong></span>
            <span class="lives-display" id="quiz-lives-wrapper" style="margin-left: 15px;">Lives: <strong id="quiz-lives" style="color: var(--error);">❤️❤️</strong></span>
        </div>

        <!-- NEW COMPACT ROUND-WISE PROGRESS CONTAINER -->
        <div class="compact-progress-container" id="compact-progress-container" style="display: none;">
            <div class="compact-progress-header">
                <span class="compact-round-label" id="compact-round-label">Round 1</span>
                <span class="compact-stage-title" id="compact-stage-title">Stage Name</span>
            </div>
            <div class="compact-progress-steps-wrapper">
                <div class="compact-progress-line">
                    <div class="compact-progress-line-fill" id="compact-progress-line-fill" style="width: 0%;"></div>
                </div>
                <div class="compact-progress-steps" id="compact-progress-steps">
                    <!-- Dynamically filled step nodes -->
                </div>
            </div>
        </div>

        <!-- Main Card Section (Injecting Questions) -->
        <div class="quiz-card-container" id="quiz-card-container" style="display: none;">
            <div class="quiz-play-title-top" id="quiz-play-title-top" style="text-align: center; color: #d74338; font-weight: 800; font-size: 14px; text-transform: uppercase; margin-bottom: 20px; letter-spacing: 0.5px; font-family: 'Outfit', sans-serif;"></div>
            
            <div class="quiz-play-card fade-in" id="quiz-play-card">
                <!-- Question block -->
                <div class="question-box" style="width: 100%;">
                    <div class="question-label" id="quiz-question-label" style="display: flex; align-items: center; gap: 8px; font-weight: 700; color: #475569; font-size: 16px; margin-bottom: 12px; font-family: 'Outfit', sans-serif;">
                        <span style="font-size: 18px;">💬</span>
                        <span>Question <span id="quiz-question-number-span">1</span></span>
                    </div>
                    <h2 class="quiz-play-question" id="quiz-question-text" style="font-size: 24px; font-weight: 800; color: #1e293b; line-height: 1.4; font-family: 'Outfit', sans-serif; margin-bottom: 0;">Question text goes here?</h2>
                </div>

                <!-- Ad 1: Between Question and Photo/Image -->
                <?= render_banner_slot('play_mid', 'ad-play-mid') ?>

                <!-- Visual Clue/Image Container -->
                <div class="quiz-visual-container" id="quiz-visual" style="display: none; border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm); width: 100%; margin-top: 15px; margin-bottom: 15px;"></div>

                <div class="quiz-options-list" id="quiz-options-list">
                    <!-- Option buttons will be dynamically injected here -->
                </div>

                <!-- Ad 2: Between Options and Next Button -->
                <?= render_banner_slot('play_question', 'ad-play-question') ?>

                <div class="explanation-box" id="explanation-box" style="display: none;">
                    <div class="explanation-title" id="explanation-title">Correct answer 👍</div>
                    <p class="explanation-text" id="explanation-text">Explanation goes here...</p>
                </div>

                <div class="quiz-action-footer" style="margin-bottom: 15px;">
                    <button class="btn btn-primary btn-lg" id="next-question-btn" style="display: none;">Next Question ❯</button>
                </div>

                <!-- Ad 3: Below Next Button (For all quizzes) -->
                <?= render_banner_slot('play_bottom', 'ad-play-banner') ?>

                <!-- Custom Stats Box (QuizTv Slug Only) -->
                <?php if ($quiz->slug === 'quiztv'): ?>
                    <div class="ndtv-stats-header" style="border-top: 1.5px solid var(--border); padding-top: 24px; margin-top: 20px;">
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
                <?php endif; ?>
            </div>
        </div>

        <!-- INTERSTITIAL READY OVERLAY -->
        <div class="quiz-overlay" id="ready-overlay" style="display: none;">
            <div class="overlay-card text-center fade-in">
                <span class="overlay-icon">🚦</span>
                <h2 class="overlay-title">Get Ready!</h2>
                <p class="overlay-desc">You are about to start <strong><?= esc($quiz->title) ?></strong>.</p>
                <p class="overlay-meta">Difficulty: <?= ucfirst(esc($quiz->difficulty)) ?> | Estimated time:
                    <?= esc($quiz->duration_minutes) ?> minutes</p>
                <button class="btn btn-primary btn-lg" id="start-continue-btn">Start Quiz</button>
            </div>
        </div>

        <!-- ROUND COMPLETE OVERLAY -->
        <div class="quiz-overlay" id="round-overlay" style="display: none;">
            <div class="overlay-card text-center fade-in">
                <span class="overlay-icon">🏆</span>
                <h2 class="overlay-title" id="round-title">Round Complete!</h2>
                <p class="overlay-desc" id="round-desc">You scored Y this round.</p>
                <?php $adConfig = get_ad_config(); ?>
                <?php if ($adConfig['rewarded']['enabled'] && !empty($adConfig['rewarded']['slot'])): ?>
                    <button class="btn ad-rewarded-btn" id="rewarded-ad-btn" style="display: none;">
                        <span class="ad-rewarded-icon">🎬</span> Watch Ad for Bonus
                    </button>
                    <p class="ad-rewarded-status" id="rewarded-status" style="display: none;"></p>
                <?php endif; ?>
                <button class="btn btn-primary btn-lg" id="round-continue-btn" style="width: 100%;">Continue to Next Round</button>
                <button class="btn btn-success btn-lg" id="round-results-btn" style="width: 100%; margin-top: 12px; font-family: 'Outfit', sans-serif; font-weight: 700;">See Results ❯</button>
            </div>
        </div>

        <!-- CONFIRM RESTART OVERLAY -->
        <div class="quiz-overlay" id="confirm-overlay" style="display: none;">
            <div class="overlay-card text-center fade-in">
                <span class="overlay-icon">🔄</span>
                <h2 class="overlay-title">Restart Quiz?</h2>
                <p class="overlay-desc">Your current quiz progress will be cleared. Are you sure you want to restart?
                </p>
                <div class="overlay-buttons">
                    <button class="btn btn-outline" id="confirm-cancel-btn">Cancel</button>
                    <button class="btn btn-danger" id="confirm-ok-btn">Yes, Restart</button>
                </div>
            </div>
        </div>

        <!-- OUT OF LIVES OVERLAY -->
        <div class="quiz-overlay" id="lives-overlay" style="display: none;">
            <div class="overlay-card text-center fade-in">
                <span class="overlay-icon">💔</span>
                <h2 class="overlay-title">Out of Lifelines!</h2>
                <p class="overlay-desc">You gave 2 incorrect answers. Watch an ad to refill your lifelines and continue, or restart the quiz.</p>
                <div class="overlay-buttons" style="flex-direction: column; gap: 12px;">
                    <button class="btn btn-primary btn-lg" id="lives-ad-btn" style="width: 100%;">🎬 Watch Ad to Continue</button>
                    <button class="btn btn-outline btn-lg" id="lives-restart-btn" style="width: 100%;">🔄 Restart Quiz</button>
                </div>
            </div>
        </div>

        <!-- LEAD CAPTURE OVERLAY -->
        <div class="quiz-overlay" id="lead-overlay" style="display: none;">
            <div class="overlay-card text-center fade-in">
                <span class="overlay-icon">🏆</span>
                <h2 class="overlay-title">Quiz Completed!</h2>
                <p class="overlay-desc">Enter your details to view your results and unlock your score statistics.</p>
                <form id="lead-capture-form" style="display: flex; flex-direction: column; gap: 14px; width: 100%; margin-top: 15px;">
                    <div style="display: flex; flex-direction: column; gap: 4px; text-align: left;">
                        <label for="lead-input-name" style="font-size: 13px; font-weight: 700; color: var(--text);">Name</label>
                        <input type="text" id="lead-input-name" required placeholder="Your Name" style="padding: 12px; font-size: 14px; border: 1.5px solid var(--border); border-radius: var(--radius-md); outline: none;">
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 4px; text-align: left;">
                        <label for="lead-input-phone" style="font-size: 13px; font-weight: 700; color: var(--text);">Phone Number</label>
                        <input type="tel" id="lead-input-phone" required placeholder="e.g. 9876543210" style="padding: 12px; font-size: 14px; border: 1.5px solid var(--border); border-radius: var(--radius-md); outline: none;">
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 4px; text-align: left;">
                        <label for="lead-input-email" style="font-size: 13px; font-weight: 700; color: var(--text);">Email Address</label>
                        <input type="email" id="lead-input-email" required placeholder="yourname@example.com" style="padding: 12px; font-size: 14px; border: 1.5px solid var(--border); border-radius: var(--radius-md); outline: none;">
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg" id="lead-submit-btn" style="width: 100%; border-radius: var(--radius-md); margin-top: 10px;">Get Results ➔</button>
                </form>
            </div>
        </div>

        <!-- FINAL RESULT OVERLAY -->
        <div class="quiz-overlay" id="result-overlay" style="display: none;">
            <div class="overlay-card text-center fade-in result-card">
                <span class="result-badge-icon" id="result-badge-icon">🎉</span>
                <h2 class="overlay-title" id="result-title-greeting">Quiz Completed!</h2>

                <div class="result-score-circle">
                    <span class="result-score-number" id="result-score-display">8 / 10</span>
                    <span class="result-score-pct" id="result-pct-display">80%</span>
                </div>

                <div class="result-status-badge" id="result-status-badge">PASS</div>


                <!-- Recommendations panel -->
                <div class="result-recommendations" id="result-recommendations-wrapper" style="display: none;">
                    <h3>Recommended Quizzes for You</h3>
                    <div class="rec-mini-grid" id="rec-mini-grid">
                        <!-- Recommended items injected here -->
                    </div>
                </div>

                <div class="result-actions">
                    <button class="btn btn-primary" id="result-restart-btn">Restart Quiz</button>
                    <a href="<?= site_url() ?>" class="btn btn-outline">Back to Home</a>
                </div>

                <!-- Ad Banner: Results Screen -->
                <?= render_banner_slot('play_result', 'ad-play-result') ?>
            </div>
        </div>

    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Inject state credentials securely into javascript context -->
<script>
    // Global error diagnostic tracker for debugging
    window.onerror = function(message, source, lineno, colno, error) {
        alert("JS ERROR: " + message + " (Line: " + lineno + " in " + source.split('/').pop() + ")");
        return false;
    };

    window.quizConfig = {
        quizId: <?= json_encode($quiz->id) ?>,
        title: <?= json_encode($quiz->title) ?>,
        userId: <?= json_encode($userId) ?>,
        guestToken: <?= json_encode($guestToken) ?>,
        baseUrl: <?= json_encode(site_url() . '/') ?>,
        csrfHeaderName: '<?= csrf_header() ?>',
        csrfToken: '<?= csrf_hash() ?>',
        adConfig: <?= json_encode(get_ad_config()) ?>,
        stages: <?= json_encode(json_decode($quiz->stages) ?: []) ?>,
        thumbnail: <?= json_encode((empty($quiz->thumbnail)) ? '' : ((str_starts_with($quiz->thumbnail, 'http://') || str_starts_with($quiz->thumbnail, 'https://')) ? $quiz->thumbnail : base_url('uploads/quizzes/' . $quiz->thumbnail))) ?>
    };
</script>
<script src="<?= base_url('assets/js/quiz.js?v=' . time()) ?>"></script>
<?= $this->endSection() ?>