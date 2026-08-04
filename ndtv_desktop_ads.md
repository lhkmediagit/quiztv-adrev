# NDTV Trivia — Desktop Ad Architecture Specification

This document details the complete ad layout, slots, sizes, refreshing logic, and rewarded ad integration present on NDTV Trivia (`https://www.ndtv.com/smart-picks/trivia/quiz`) in **Desktop View**.

---

## 1. Summary of Desktop Ad Placements

On Desktop view (viewport width ≥ 1024px), NDTV Trivia implements **6 distinct display ad slots** plus an optional **Rewarded Ad** modal trigger.

| Ad Slot Key | GAM / Code Name | Position | Dimensions | CSS Wrapper / Visibility |
| :--- | :--- | :--- | :--- | :--- |
| **Top Banner** | `NewTriviaTop` | Above Quiz Container / Header | `728x90` / `970x90` | `desktop-only`, Centered |
| **Left Rail / Skyscraper** | `TriviaLeftRail` / `LeftRail` | Left Margin (Sticky Side Rail) | `160x600` / `300x600` | `hide-below-1024 fixed left-4 top-20` |
| **Right Rail / Skyscraper** | `TriviaRightRail` / `RightRail` | Right Margin (Sticky Side Rail) | `160x600` / `300x600` | `hide-below-1024 fixed right-4 top-20` |
| **Slim Side Rail** | `Skyscraper` / `SlimRail` | Secondary Side Margin | `160x600` | Desktop Layout Wrapper |
| **Middle Banner** | `NewTriviaMiddle` / `MiddleDesktop` | Inside Quiz Card (Between Question & Options) | `300x250` / `336x280` | Centered inside quiz content area |
| **Bottom Banner** | `NewTriviaBottom` / `BottomDesktop` | Below Quiz Card / Footer | `728x90` / `300x250` | Centered at bottom of content |

---

## 2. Desktop Responsive Grid & HTML Structure

```html
<!-- Desktop Top Leaderboard Banner -->
<div class="ad-container-desktop-top hidden lg:flex justify-center my-4" data-ad-id="NewTriviaTop">
  <span class="text-xs text-gray-400 block mb-1">ADVERTISEMENT</span>
  <div id="gam-desktop-top" data-ad-slot="/23351137437/ndtv.desktop.top" data-ad-width="728" data-ad-height="90"></div>
</div>

<div class="quiz-main-wrapper flex justify-center relative max-w-7xl mx-auto">
  
  <!-- Left Side Skyscraper (Desktop Only) -->
  <aside class="left-rail-ad hidden xl:block sticky top-24 h-[600px] w-[160px] mr-6" data-ad-id="TriviaLeftRail">
    <span class="text-xs text-gray-400 block mb-1">ADVERTISEMENT</span>
    <div id="gam-left-rail" data-ad-slot="/23351137437/ndtv.desktop.leftrail" data-ad-width="160" data-ad-height="600"></div>
  </aside>

  <!-- Central Quiz Content Box -->
  <main class="quiz-card-container w-full max-w-2xl bg-white rounded-xl shadow-md p-6">
    
    <!-- Question Header -->
    <div class="quiz-question-box">...</div>

    <!-- Desktop Middle In-Card Banner -->
    <div class="ad-container-desktop-middle my-4 flex flex-col items-center" data-ad-id="NewTriviaMiddle">
      <span class="text-xs text-gray-400 block mb-1">ADVERTISEMENT</span>
      <div id="gam-desktop-mid" data-ad-slot="/23351137437/ndtv.desktop.mid" data-ad-width="336" data-ad-height="280"></div>
    </div>

    <!-- Question Options -->
    <div class="quiz-options-grid">...</div>

  </main>

  <!-- Right Side Skyscraper (Desktop Only) -->
  <aside class="right-rail-ad hidden xl:block sticky top-24 h-[600px] w-[160px] ml-6" data-ad-id="TriviaRightRail">
    <span class="text-xs text-gray-400 block mb-1">ADVERTISEMENT</span>
    <div id="gam-right-rail" data-ad-slot="/23351137437/ndtv.desktop.rightrail" data-ad-width="160" data-ad-height="600"></div>
  </aside>

</div>

<!-- Desktop Bottom Banner -->
<div class="ad-container-desktop-bottom hidden lg:flex justify-center my-6" data-ad-id="NewTriviaBottom">
  <div id="gam-desktop-bottom" data-ad-slot="/23351137437/ndtv.desktop.bottom" data-ad-width="728" data-ad-height="90"></div>
</div>
```

---

## 3. Desktop Auto-Refresh Logic

When users navigate between quiz questions on Desktop, NDTV executes targeted ad refreshes using GAM `googletag.pubads().refresh()`:

```javascript
// NDTV Desktop Question Navigation Refresh Trigger
function onDesktopNextQuestion() {
  if (!config.noRefreshAdsLayout) {
    googletag.pubads().refresh([
      slotMap['NewTriviaTop'],
      slotMap['NewTriviaMiddle'],
      slotMap['NewTriviaBottom'],
      slotMap['TriviaLeftRail'],
      slotMap['TriviaRightRail'],
      slotMap['Skyscraper']
    ]);
  }
}
```

---

## 4. Desktop Out-of-Page / Rewarded Ad Setup

For hints, life revives, or extra quiz attempts, NDTV uses Out-of-Page Rewarded Ads:

```javascript
window.triggerRewardedAd = function() {
  googletag.cmd.push(function() {
    var rewardedSlot = googletag.defineOutOfPageSlot(
      '/23351137437/ndtv.rewarded',
      googletag.enums.OutOfPageFormat.REWARDED
    );
    if (rewardedSlot) {
      rewardedSlot.addService(googletag.pubads());
      googletag.enableServices();
      googletag.display(rewardedSlot);
    }
  });
};
```
