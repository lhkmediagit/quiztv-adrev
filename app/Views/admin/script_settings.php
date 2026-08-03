<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="ad-settings-page">

    <div class="ad-settings-intro" style="background: linear-gradient(135deg, rgba(14, 165, 233, 0.06) 0%, rgba(56, 189, 248, 0.1) 100%); border-color: rgba(14, 165, 233, 0.15);">
        <div class="intro-icon">💻</div>
        <div class="intro-text">
            <h2>Custom Header, Body & Footer Scripts</h2>
            <p>Inject custom HTML, JS, or CSS code directly into your public pages. Perfect for analytics tracking codes, GTM body code, meta tags, verification scripts, or layout adjustments. These scripts will not execute in the administration area.</p>
        </div>
    </div>

    <form action="<?= site_url('admin/script-settings/update') ?>" method="post" class="ad-settings-form" id="script-settings-form">
        <?= csrf_field() ?>

        <!-- ═══════════ HEADER SCRIPTS ═══════════ -->
        <div class="settings-section">
            <div class="section-heading">
                <span class="section-icon">⬆️</span>
                <div>
                    <h3>Header Scripts</h3>
                    <p class="section-desc">Injected at the bottom of the <code>&lt;head&gt;</code> element on all public pages.</p>
                </div>
            </div>
            <div class="settings-card">
                <div class="form-row">
                    <label for="header_scripts" class="form-label">Head Tag Scripts (HTML/CSS/JS)</label>
                    <textarea id="header_scripts" name="header_scripts" class="form-textarea" placeholder="&lt;!-- Example: Google Tag (gtag.js) --&gt;&#10;&lt;script async src=&quot;https://www.googletagmanager.com/gtag/js?id=UA-XXXXX-Y&quot;&gt;&lt;/script&gt;&#10;&lt;script&gt;&#10;  window.dataLayer = window.dataLayer || [];&#10;  function gtag(){dataLayer.push(arguments);}&#10;  gtag('js', new Date());&#10;  gtag('config', 'UA-XXXXX-Y');&#10;&lt;/script&gt;"><?= esc($settings['header_scripts'] ?? '') ?></textarea>
                    <span class="form-hint">Enter your analytics tags, custom stylesheets (wrapped in <code>&lt;style&gt;</code>), or scripts to run immediately on load.</span>
                </div>
            </div>
        </div>

        <!-- ═══════════ BODY SCRIPTS ═══════════ -->
        <div class="settings-section">
            <div class="section-heading">
                <span class="section-icon">🔲</span>
                <div>
                    <h3>Body Scripts</h3>
                    <p class="section-desc">Injected immediately after the opening <code>&lt;body&gt;</code> tag on all public pages.</p>
                </div>
            </div>
            <div class="settings-card">
                <div class="form-row">
                    <label for="body_scripts" class="form-label">Body Scripts (HTML/JS)</label>
                    <textarea id="body_scripts" name="body_scripts" class="form-textarea" placeholder="&lt;!-- Example: Google Tag Manager (noscript) --&gt;&#10;&lt;noscript&gt;&lt;iframe src=&quot;https://www.googletagmanager.com/ns.html?id=GTM-XXXX&quot;&#10;height=&quot;0&quot; width=&quot;0&quot; style=&quot;display:none;visibility:hidden&quot;&gt;&lt;/iframe&gt;&lt;/noscript&gt;"><?= esc($settings['body_scripts'] ?? '') ?></textarea>
                    <span class="form-hint">Best for scripts that must be placed immediately at the beginning of the body (e.g. Google Tag Manager noscript code).</span>
                </div>
            </div>
        </div>

        <!-- ═══════════ FOOTER SCRIPTS ═══════════ -->
        <div class="settings-section">
            <div class="section-heading">
                <span class="section-icon">⬇️</span>
                <div>
                    <h3>Footer Scripts</h3>
                    <p class="section-desc">Injected right before the closing <code>&lt;/body&gt;</code> tag on all public pages.</p>
                </div>
            </div>
            <div class="settings-card">
                <div class="form-row">
                    <label for="footer_scripts" class="form-label">Body Tag Scripts (HTML/JS)</label>
                    <textarea id="footer_scripts" name="footer_scripts" class="form-textarea" placeholder="&lt;script&gt;&#10;  console.log('QuizTv loaded!');&#10;&lt;/script&gt;"><?= esc($settings['footer_scripts'] ?? '') ?></textarea>
                    <span class="form-hint">Recommended for low-priority widgets, non-blocking javascript libraries, or tracking pixels.</span>
                </div>
            </div>
        </div>

        <!-- Policy / Safe Info Box -->
        <div class="ad-policy-info" style="background: linear-gradient(135deg, rgba(14, 165, 233, 0.05) 0%, rgba(14, 165, 233, 0.1) 100%); border-color: rgba(14, 165, 233, 0.2);">
            <h4 style="color: var(--primary-hover);">💡 Script Injection Best Practices</h4>
            <ul>
                <li>Always ensure tags are properly closed (e.g. <code>&lt;/script&gt;</code> or <code>&lt;/style&gt;</code>) to avoid parsing bugs.</li>
                <li>CSS styles must be wrapped inside a <code>&lt;style&gt;</code> tag.</li>
                <li>JS code must be wrapped inside a <code>&lt;script&gt;</code> tag.</li>
                <li>These scripts are loaded globally on all user pages, auth pages, and public quizzes. They are completely disabled on admin URLs.</li>
            </ul>
        </div>

        <!-- ═══════════ SUBMIT ═══════════ -->
        <div class="settings-submit-bar">
            <button type="submit" class="btn btn-primary btn-lg" id="save-script-settings-btn" style="background: var(--primary);">
                <span class="btn-icon">💾</span> Save Custom Scripts
            </button>
        </div>

    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('script-settings-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Function to safely encode UTF-8 text strings to Base64
            function safeBtoa(str) {
                try {
                    return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function(match, p1) {
                        return String.fromCharCode(parseInt(p1, 16));
                    }));
                } catch (err) {
                    console.error('[QuizTv] UTF-8 Base64 encoding error:', err);
                    return btoa(str); // Fallback
                }
            }

            const headerEl = document.getElementById('header_scripts');
            const bodyEl = document.getElementById('body_scripts');
            const footerEl = document.getElementById('footer_scripts');

            if (headerEl) {
                headerEl.value = 'base64:' + safeBtoa(headerEl.value);
            }
            if (bodyEl) {
                bodyEl.value = 'base64:' + safeBtoa(bodyEl.value);
            }
            if (footerEl) {
                footerEl.value = 'base64:' + safeBtoa(footerEl.value);
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
