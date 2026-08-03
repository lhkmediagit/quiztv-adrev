# AI Agent Guide: Document Service & Ad Optimization Integration

This document serves as a complete technical guide and prompt package. You can copy and paste this directly into another AI assistant's prompt window to replicate the exact same configuration, files, and optimizations in a sibling CodeIgniter 4 quiz codebase.

---

## Copy-Paste Prompt for the AI Agent

```markdown
You are an expert AI software engineer. Your task is to integrate the LHK Media Document Service API and optimize the Google Publisher Tag (GPT) advertisement integration in a CodeIgniter 4 codebase. 

Follow the specifications below to implement the credentials setup, helper functions, controller modifications, and frontend scripts.

---

### PART 1: LHK Media Document Service Integration

We need to replace local filesystem storage with remote hosting for three features:
1. User Profile Avatars
2. Quiz Thumbnails
3. Question Visual Clues/Images

Do NOT change the temporary CSV Question Importer; it should continue to use local temporary file storage.

#### Step 1.1: Environment Configuration
Add the following credentials to the project's `.env` configuration file:
```env
# LHK Media Document Service Integration
DOCSERVICE_BASE_URL = 'https://docservice.lhkmedia.io/'
DOCSERVICE_API_KEY = 'dsk_0e7169c837cb2fddf7253973d23100052a2793611eb8e268913e23abac7d3f5f'
DOCSERVICE_SENDER_DOMAIN = 'quiz.yashstudy.in'
```

#### Step 1.2: Create the Upload Helper
Create `app/Helpers/docservice_helper.php` to handle multipart uploads via cURL:
```php
<?php

if (!function_exists('upload_to_docservice')) {
    /**
     * Upload a CodeIgniter 4 UploadedFile object to LHK Media Document Service.
     *
     * @param \CodeIgniter\HTTP\Files\UploadedFile $file The uploaded file instance
     * @param string $filepath The target subdirectory path (e.g. 'quizhive/avatars')
     * @return string|null The public inline view URL on success, or null on failure
     */
    function upload_to_docservice(\CodeIgniter\HTTP\Files\UploadedFile $file, string $filepath): ?string
    {
        $baseUrl = env('DOCSERVICE_BASE_URL', 'https://docservice.lhkmedia.io/');
        $apiKey = env('DOCSERVICE_API_KEY', '');
        $senderDomain = env('DOCSERVICE_SENDER_DOMAIN', '');

        if (empty($apiKey) || empty($senderDomain)) {
            log_message('error', '[DocService] API Key or Sender Domain is missing in .env config.');
            return null;
        }

        $uploadUrl = rtrim($baseUrl, '/') . '/api/upload';
        $tempPath = $file->getTempName();
        $mimeType = $file->getClientMimeType();
        $originalName = $file->getClientName();
        $filename = $file->getRandomName();

        $postFields = [
            'file'          => new \CURLFile($tempPath, $mimeType, $originalName),
            'filepath'      => $filepath,
            'filename'      => $filename,
            'sender_domain' => $senderDomain,
        ];

        $ch = curl_init($uploadUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ["X-API-Key: {$apiKey}"],
            CURLOPT_POSTFIELDS     => $postFields,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 201) {
            $data = json_decode($response, true);
            if (isset($data['success']) && $data['success'] && isset($data['data']['url'])) {
                // Return 'url' (the inline preview URL), NOT 'download_url'
                return $data['data']['url'];
            }
        }

        log_message('error', "[DocService] Upload failed. HTTP Code: {$httpCode}. Response: {$response}");
        return null;
    }
}
```

#### Step 1.3: Autoload the Helper globally
In `app/Controllers/BaseController.php`, add `'docservice'` to the `$helpers` class array so it is loaded on every page:
```php
protected $helpers = ['text', 'url', 'ad', 'script', 'docservice'];
```

#### Step 1.4: Wire up Controller File Uploads

##### A. User Profile Avatars (`app/Controllers/User/DashboardController.php`)
Locate the avatar file upload block. Replace the local directory movement and clean up older avatars *only* if they are local:
```php
$avatarFile = $this->request->getFile('avatar');
if ($avatarFile && $avatarFile->isValid() && !$avatarFile->hasMoved()) {
    $avatarUrl = upload_to_docservice($avatarFile, 'quizhive/avatars');
    if ($avatarUrl === null) {
        return redirect()->back()->withInput()->with('errors', ['avatar' => 'Failed to upload avatar to Document Service.']);
    }

    // Clean up old avatar image file locally ONLY if it was stored locally
    if ($user->avatar) {
        $isLocal = str_contains($user->avatar, base_url('uploads/avatars/'));
        if ($isLocal) {
            $oldFilename = basename($user->avatar);
            $uploadPath = FCPATH . 'uploads/avatars/';
            if (file_exists($uploadPath . $oldFilename)) {
                @unlink($uploadPath . $oldFilename);
            }
        }
    }

    $data['avatar'] = $avatarUrl;
}
```

##### B. Quiz Cover Thumbnails (`app/Controllers/Admin/QuizController.php`)
Update the `store()` and `update()` methods. Use the remote upload helper:
```php
// Inside store()
$thumbnail = $this->request->getFile('thumbnail');
if ($thumbnail && $thumbnail->isValid() && !$thumbnail->hasMoved()) {
    $thumbnailUrl = upload_to_docservice($thumbnail, 'quizhive/quizzes');
    if ($thumbnailUrl === null) {
        return redirect()->back()->withInput()->with('errors', ['thumbnail' => 'Failed to upload quiz thumbnail to Document Service.']);
    }
    $data['thumbnail'] = $thumbnailUrl;
}

// Inside update()
$thumbnail = $this->request->getFile('thumbnail');
if ($thumbnail && $thumbnail->isValid() && !$thumbnail->hasMoved()) {
    $thumbnailUrl = upload_to_docservice($thumbnail, 'quizhive/quizzes');
    if ($thumbnailUrl === null) {
        return redirect()->back()->withInput()->with('errors', ['thumbnail' => 'Failed to upload quiz thumbnail to Document Service.']);
    }

    // Delete old file locally only if it was stored locally
    if ($quiz->thumbnail) {
        $isLocal = str_contains($quiz->thumbnail, base_url('uploads/quizzes/'));
        if ($isLocal) {
            $oldFilename = basename($quiz->thumbnail);
            $uploadPath = FCPATH . 'uploads/quizzes/';
            if (file_exists($uploadPath . $oldFilename)) {
                @unlink($uploadPath . $oldFilename);
            }
        }
    }

    $data['thumbnail'] = $thumbnailUrl;
}
```

##### C. Question Visual Clues (`app/Controllers/Admin/QuestionController.php`)
Update the `store()` and `update()` methods. Save the resulting URL inside the standard HTML image tag:
```php
// Inside store()
$visualHtml = null;
$visualFile = $this->request->getFile('visual');
if ($visualFile && $visualFile->isValid() && !$visualFile->hasMoved()) {
    $visualUrl = upload_to_docservice($visualFile, 'quizhive/questions');
    if ($visualUrl === null) {
        return redirect()->back()->withInput()->with('errors', ['visual' => 'Failed to upload visual image to Document Service.']);
    }
    $visualHtml = '<img class="legacy-question-image" src="' . htmlspecialchars($visualUrl) . '" alt="Visual Clue" />';
}

// Inside update()
$visualFile = $this->request->getFile('visual');
if ($visualFile && $visualFile->isValid() && !$visualFile->hasMoved()) {
    $visualUrl = upload_to_docservice($visualFile, 'quizhive/questions');
    if ($visualUrl === null) {
        return redirect()->back()->withInput()->with('errors', ['visual' => 'Failed to upload visual image to Document Service.']);
    }

    // Delete old file locally only if it was local
    if ($question->visual && $question->visual !== 'none') {
        if (preg_match('/src="([^"]+)"/', $question->visual, $matches)) {
            $url = $matches[1];
            $isLocal = str_contains($url, base_url('uploads/questions/'));
            if ($isLocal) {
                $localPath = str_replace(base_url(), FCPATH, $url);
                if (is_file($localPath)) {
                    @unlink($localPath);
                }
            }
        }
    }

    $currentVisual = '<img class="legacy-question-image" src="' . htmlspecialchars($visualUrl) . '" alt="Visual Clue" />';
}
```

---

### PART 2: GPT Ad System Optimizations & Revenue Protection

We need to optimize the client-side Google Publisher Tag (GPT) system inside `assets/js/ads.js` and styling sheets to fix two critical revenue leaks:
1. **Empty Fill Ad Collapse Conflict:** Auto-refresh stops working once a banner has a temporary no-fill, because setting `display: none` drops layout heights/widths to 0. We will use a `.ad-collapsed` class instead.
2. **Rewarded Ad Skip Loophole:** Bypassing early-close restrictions under 4 seconds allows users to instantly skip ads while getting rewards. We will drop time overrides and rely 100% on the native SDK events.
3. **Fast Fallback Rotation (2s Timeouts):** If a rotated slot doesn't fill within 2 seconds, rotate to the next.

#### Step 2.1: Add CSS Styles for Banner Collapsing
In the project's global stylesheet (e.g. `assets/css/style.css`), define the `.ad-collapsed` rule for the parent ad container:
```css
/* Collapsed banner state to hide borders/margins but keep DOM scroll coordinates */
.ad-banner-container.ad-collapsed {
    min-height: 0 !important;
    height: 0 !important;
    padding: 0 !important;
    margin: 0 !important;
    border: none !important;
    visibility: hidden !important;
}
```

#### Step 2.2: Update the `slotRenderEnded` listener in `ads.js`
Modify the listener so it toggles the `.ad-collapsed` class on the parent wrapper instead of hiding the wrapper via `display = 'none'`:
```javascript
googletag.pubads().addEventListener('slotRenderEnded', (event) => {
    // Handle dynamic collapse of banner containers on empty fill
    const slotId = event.slot.getSlotElementId();
    if (slotId) {
        const wrapper = document.getElementById(`${slotId}-wrapper`);
        if (wrapper) {
            if (event.isEmpty) {
                wrapper.classList.add('ad-collapsed');
                console.log(`[QuizHiveAds] Ad slot ${slotId} rendered empty, collapsing wrapper.`);
            } else {
                wrapper.classList.remove('ad-collapsed');
                console.log(`[QuizHiveAds] Ad slot ${slotId} rendered successfully, expanding wrapper.`);
            }
        }
    }
    // ... rest of interstitial and rewarded end callbacks ...
});
```

#### Step 2.3: Update Visibility Checking in `ads.js`
Modify the `_isElementVisible(el)` function. It must check the visibility coordinates of the parent wrapper and allow coordinates tracking even when collapsed:
```javascript
_isElementVisible(el) {
    const wrapper = document.getElementById(`${el.id}-wrapper`);
    if (wrapper) {
        const rect = wrapper.getBoundingClientRect();
        const isCollapsed = wrapper.classList.contains('ad-collapsed');
        if (isCollapsed) {
            // Collapsed containers have 0 height/width, check vertical scroll bounds
            return rect.top < window.innerHeight && rect.top > -100;
        }
        return (
            rect.top < window.innerHeight &&
            rect.bottom > 0 &&
            rect.width > 0 &&
            rect.height > 0
        );
    }
    const rect = el.getBoundingClientRect();
    return (
        rect.top < window.innerHeight &&
        rect.bottom > 0 &&
        rect.width > 0 &&
        rect.height > 0
    );
}
```

#### Step 2.4: Implement Scroll-Triggered IntersectionObserver
In `_setupAutoRefresh()`, implement an `IntersectionObserver` to trigger an instant refresh when a collapsed banner scrolls into the viewport and its timer has expired:
```javascript
_setupAutoRefresh() {
    if (!this.config || !this.config.banner) return;

    const interval = Math.max(30, this.config.banner.refresh || 60) * 1000;

    // Clear any existing timers
    Object.values(this.refreshTimers).forEach(t => clearInterval(t));
    this.refreshTimers = {};

    if (this.observer) {
        this.observer.disconnect();
        this.observer = null;
    }

    // Set refresh timers for each slot
    Object.keys(this.slots).forEach(divId => {
        this.refreshTimers[divId] = setInterval(() => {
            const el = document.getElementById(divId);
            if (el && this._isElementVisible(el)) {
                this.refreshBanner(divId);
            }
        }, interval);
    });

    // Set up IntersectionObserver for scroll-triggered refreshes when entering viewport
    if ('IntersectionObserver' in window) {
        this.observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const wrapperId = entry.target.id;
                    const divId = wrapperId.replace('-wrapper', '');
                    const slot = this.slots[divId];
                    if (slot) {
                        const now = Date.now();
                        const lastRefresh = this.lastRefreshTime[divId] || 0;
                        if (now - lastRefresh >= interval) {
                            console.log(`[QuizHiveAds] Slot ${divId} scrolled into view and is due for refresh. Refreshing...`);
                            this.refreshBanner(divId);
                        }
                    }
                }
            });
        }, { threshold: 0 });

        // Observe each banner wrapper
        Object.keys(this.slots).forEach(divId => {
            const wrapper = document.getElementById(`${divId}-wrapper`);
            if (wrapper) {
                this.observer.observe(wrapper);
            }
        });
    }
}
```
*Note: Also ensure that in `destroyAll()`, you disconnect the observer: `if (this.observer) { this.observer.disconnect(); this.observer = null; }`.*

#### Step 2.5: Secure Rewarded Close Callback & Speed up Timeout Fallbacks
1. Update `rewardedSlotClosed` to rely 100% on the SDK's `rewardedSlotGranted` event. Remove any time-based bypasses:
```javascript
googletag.pubads().addEventListener('rewardedSlotClosed', () => {
    console.log('[QuizHiveAds] Rewarded slot closed event fired.');
    const callback = this.rewardedAdCallback;
    let wasGranted = this.rewardedGranted; // Decided purely by rewardedSlotGranted event

    this.rewardedAdEvent = null;
    this.rewardedAdCallback = null;
    this.rewardedAdRewardCallback = null;
    this.showRewardedPending = false;
    this.rewardedGranted = false;
    this.rewardedAdStartPlayTime = 0;
    if (this.rewardedTimeout) {
        clearTimeout(this.rewardedTimeout);
        this.rewardedTimeout = null;
    }

    // Rotate to the next slot
    this._rotateToNextRewardedSlot(false);

    if (callback) callback(wasGranted);
});
```

2. Change the loading failover timeouts from `5000` to `2000` (2 seconds) in both `showRewarded()` and `_rotateToNextRewardedSlot()` to speed up failures on slow connections:
```javascript
// In _rotateToNextRewardedSlot()
this.rewardedTimeout = setTimeout(() => {
    if (this.showRewardedPending) {
        console.warn('[QuizHiveAds] Timeout waiting for active slot.');
        this.rewardedTimeout = null;
        this._rotateToNextRewardedSlot(true, true);
    }
}, 2000); // 2 seconds

// In showRewarded()
this.rewardedTimeout = setTimeout(() => {
    if (this.showRewardedPending) {
        console.warn('[QuizHiveAds] Timeout waiting for active rewarded slot.');
        this.rewardedTimeout = null;
        this._rotateToNextRewardedSlot(true, true);
    }
}, 2000); // 2 seconds
```

---

### PART 3: Verification & Safety Nets

Verify that all front-end calls to `QuizHiveAds.showRewarded` in the quiz pages (Start Quiz, Lifeline Revives, Round Transitions) check for consecutive failures. 

Ensure they wrap the callback return value to allow proceeding if they click 3 times and get failures (so players aren't blocked by offline states):
```javascript
let tryCount = 0;
QuizHiveAds.showRewarded(slotPath, null, (success) => {
    tryCount++;
    if (success || tryCount >= 3) {
        // Proceed with game action (refill lives, start quiz, next round)
    } else {
        // Update UI to allow clicking "Try Again"
    }
});
```
This safety net must be present on all pages calling rewarded slots.
```
