<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="ad-settings-page">

    <div class="ad-settings-intro">
        <div class="intro-icon">💰</div>
        <div class="intro-text">
            <h2>Google Ad Manager Integration</h2>
            <p>Configure your GAM network code, ad unit slots, and control which ad formats appear on your QuizTv app. All changes take effect immediately.</p>
        </div>
    </div>

    <form action="<?= site_url('admin/ad-settings/update') ?>" method="post" class="ad-settings-form" id="ad-settings-form">
        <?= csrf_field() ?>

        <!-- ═══════════ GLOBAL CONTROLS ═══════════ -->
        <div class="settings-section">
            <div class="section-heading">
                <span class="section-icon">⚡</span>
                <div>
                    <h3>Global Controls</h3>
                    <p class="section-desc">Master switch to enable or disable all advertisements across QuizTv.</p>
                </div>
            </div>
            <div class="settings-card">
                <div class="form-row toggle-row">
                    <div class="toggle-info">
                        <label class="form-label-main">Enable Ads Globally</label>
                        <span class="form-hint">When disabled, no ads will be shown anywhere on the site.</span>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="ads_enabled" value="1" <?= ($settings['ads_enabled'] ?? '0') === '1' ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- ═══════════ GAM NETWORK CONFIGURATION ═══════════ -->
        <div class="settings-section">
            <div class="section-heading">
                <span class="section-icon">🔗</span>
                <div>
                    <h3>GAM Network Configuration</h3>
                    <p class="section-desc">Your Google Ad Manager network code. Find it in your GAM dashboard under Admin → Global Settings.</p>
                </div>
            </div>
            <div class="settings-card">
                <div class="form-row">
                    <label for="gam_network_code" class="form-label">Network Code</label>
                    <input type="text" id="gam_network_code" name="gam_network_code"
                           class="form-input"
                           value="<?= esc($settings['gam_network_code'] ?? '') ?>"
                           placeholder="e.g., 12345678">
                    <span class="form-hint">This is the numeric code from your GAM account (e.g., 12345678).</span>
                </div>
            </div>
        </div>

        <!-- ═══════════ BANNER ADS ═══════════ -->
        <div class="settings-section">
            <div class="section-heading">
                <span class="section-icon">🖼️</span>
                <div>
                    <h3>Banner Ads</h3>
                    <p class="section-desc">Configure banner ad units for different pages. Enter the full ad unit path (e.g., /12345678/Homepage_Banner).</p>
                </div>
            </div>
            <div class="settings-card">
                <div class="form-row toggle-row">
                    <div class="toggle-info">
                        <label class="form-label-main">Enable Banner Ads</label>
                        <span class="form-hint">Show banner advertisements on public pages.</span>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="banner_enabled" value="1" <?= ($settings['banner_enabled'] ?? '0') === '1' ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <hr class="form-divider">

                <div class="form-row">
                    <label for="banner_home_slot" class="form-label">🏠 Home Page — Ad Unit Path</label>
                    <input type="text" id="banner_home_slot" name="banner_home_slot"
                           class="form-input"
                           value="<?= esc($settings['banner_home_slot'] ?? '') ?>"
                           placeholder="/12345678/QuizTv_Home_Banner">
                    <span class="form-hint">Banner shown on the home page (between hero and quiz grid).</span>
                </div>

                <div class="form-row">
                    <label for="banner_quiz_slot" class="form-label">📝 Quiz Landing — Ad Unit Path</label>
                    <input type="text" id="banner_quiz_slot" name="banner_quiz_slot"
                           class="form-input"
                           value="<?= esc($settings['banner_quiz_slot'] ?? '') ?>"
                           placeholder="/12345678/QuizTv_Quiz_Banner">
                    <span class="form-hint">Banner shown on quiz detail/landing pages (sidebar area).</span>
                </div>

                <div class="form-row">
                    <label for="banner_play_slot" class="form-label">🎮 Quiz Play — Ad Unit Path</label>
                    <input type="text" id="banner_play_slot" name="banner_play_slot"
                           class="form-input"
                           value="<?= esc($settings['banner_play_slot'] ?? '') ?>"
                           placeholder="/12345678/QuizTv_Play_Banner">
                    <span class="form-hint">Banner shown on quiz results screen after completion.</span>
                </div>

                <hr class="form-divider">

                <div class="form-grid-2">
                    <div class="form-row">
                        <label for="banner_size" class="form-label">Default Banner Size</label>
                        <select id="banner_size" name="banner_size" class="form-input form-select">
                            <option value="responsive" <?= ($settings['banner_size'] ?? '') === 'responsive' ? 'selected' : '' ?>>Responsive (Auto)</option>
                            <option value="728x90" <?= ($settings['banner_size'] ?? '') === '728x90' ? 'selected' : '' ?>>Leaderboard (728×90)</option>
                            <option value="336x280" <?= ($settings['banner_size'] ?? '') === '336x280' ? 'selected' : '' ?>>Large Rectangle (336×280)</option>
                            <option value="300x250" <?= ($settings['banner_size'] ?? '') === '300x250' ? 'selected' : '' ?>>Medium Rectangle (300×250)</option>
                            <option value="320x50" <?= ($settings['banner_size'] ?? '') === '320x50' ? 'selected' : '' ?>>Mobile Banner (320×50)</option>
                        </select>
                    </div>

                    <div class="form-row">
                        <label for="banner_refresh_seconds" class="form-label">Auto-Refresh Interval (seconds)</label>
                        <input type="number" id="banner_refresh_seconds" name="banner_refresh_seconds"
                               class="form-input"
                               value="<?= esc($settings['banner_refresh_seconds'] ?? '60') ?>"
                               min="30" step="1">
                        <span class="form-hint">Minimum 30 seconds per Google Ad Manager policy.</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══════════ REWARDED ADS ═══════════ -->
        <div class="settings-section">
            <div class="section-heading">
                <span class="section-icon">🎬</span>
                <div>
                    <h3>Rewarded Ads</h3>
                    <p class="section-desc">Rewarded ads are shown between quiz rounds. Users voluntarily click "Watch Ad" — fully GAM compliant.</p>
                </div>
            </div>
            <div class="settings-card">
                <div class="form-row toggle-row">
                    <div class="toggle-info">
                        <label class="form-label-main">Enable Rewarded Ads</label>
                        <span class="form-hint">Show optional rewarded ad button during quiz round transitions.</span>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="rewarded_enabled" value="1" <?= ($settings['rewarded_enabled'] ?? '0') === '1' ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <hr class="form-divider">

                <div class="form-row">
                    <label for="rewarded_slot" class="form-label">Rewarded Ad Unit Path</label>
                    <input type="text" id="rewarded_slot" name="rewarded_slot"
                           class="form-input"
                           value="<?= esc($settings['rewarded_slot'] ?? '') ?>"
                           placeholder="/12345678/QuizTv_Rewarded">
                    <span class="form-hint">The ad unit path configured in your GAM account for rewarded ads.</span>
                </div>

                <div class="form-row">
                    <label for="rewarded_message" class="form-label">Reward Completion Message</label>
                    <input type="text" id="rewarded_message" name="rewarded_message"
                           class="form-input"
                           value="<?= esc($settings['rewarded_message'] ?? 'Great job watching the ad! Keep going!') ?>"
                           placeholder="Thanks for watching! Keep up the great work!">
                    <span class="form-hint">Message displayed to the user after they complete watching a rewarded ad.</span>
                </div>
            </div>
        </div>

        <!-- ═══════════ INTERSTITIAL ADS ═══════════ -->
        <div class="settings-section">
            <div class="section-heading">
                <span class="section-icon">🔲</span>
                <div>
                    <h3>Interstitial Ads</h3>
                    <p class="section-desc">Configure interstitial (full-screen) ad unit for transitions (e.g., quiz completion transitions).</p>
                </div>
            </div>
            <div class="settings-card">
                <div class="form-row toggle-row">
                    <div class="toggle-info">
                        <label class="form-label-main">Enable Interstitial Ads</label>
                        <span class="form-hint">Show full-screen interstitial ads during transitions.</span>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="interstitial_enabled" value="1" <?= ($settings['interstitial_enabled'] ?? '0') === '1' ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <hr class="form-divider">

                <div class="form-row">
                    <label for="interstitial_slot" class="form-label">Interstitial Ad Unit Path</label>
                    <input type="text" id="interstitial_slot" name="interstitial_slot"
                           class="form-input"
                           value="<?= esc($settings['interstitial_slot'] ?? '') ?>"
                           placeholder="/12345678/QuizTv_Interstitial">
                    <span class="form-hint">The ad unit path configured in your GAM account for full-screen interstitial ads.</span>
                </div>
            </div>
        </div>

        <!-- Policy Info Box -->
        <div class="ad-policy-info">
            <h4>📋 GAM Policy Compliance Notes</h4>
            <ul>
                <li>Banner auto-refresh is enforced at a minimum of <strong>30 seconds</strong>.</li>
                <li>No ads are displayed during active quiz question answering.</li>
                <li>Rewarded ads are always <strong>user-initiated</strong> (opt-in button click).</li>
                <li>All ad containers include an "Advertisement" label as required.</li>
                <li>Disabling "Enable Ads Globally" immediately hides all ads site-wide.</li>
            </ul>
        </div>

        <!-- ═══════════ SUBMIT ═══════════ -->
        <div class="settings-submit-bar">
            <button type="submit" class="btn btn-primary btn-lg" id="save-ad-settings-btn">
                <span class="btn-icon">💾</span> Save Ad Settings
            </button>
        </div>

    </form>
</div>
<?= $this->endSection() ?>
