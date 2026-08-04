# NDTV Trivia — Mobile Ad Architecture Specification

This document details the complete ad layout, slots, sizes, sticky anchoring, refreshing logic, and rewarded ad integration present on NDTV Trivia (`https://www.ndtv.com/smart-picks/trivia/quiz`) in **Mobile View**.

---

## 1. Summary of Mobile Ad Placements

On Mobile view (viewport width < 1024px), NDTV Trivia implements **5 distinct mobile display ad slots** including a persistent **Sticky Bottom Anchor Banner** plus an optional **Rewarded Ad** trigger.

| Ad Slot Key | GAM / Code Name | Position | Dimensions | CSS Wrapper / Visibility |
| :--- | :--- | :--- | :--- | :--- |
| **Mobile Top Banner** | `StickyTopMobile` / `NewTriviaTop` | Top Header Area (Above Title) | `320x50` / `300x100` | `block lg:hidden text-center my-2` |
| **Mobile Middle Banner** | `MiddleMobileShort` / `MiddleMobile` | Inside Question Card (After Question Text) | `300x250` | `block lg:hidden my-3 flex justify-center` |
| **Mobile Pre-Option Banner**| `TriviaBottomTop` / `BottomTopMobile` | Above Option Buttons / Action Bar | `300x250` / `320x50` | `block lg:hidden my-2 flex justify-center` |
| **Mobile Bottom Banner** | `BottomMobile` | Below Quiz Card Container | `300x250` | `block lg:hidden my-4 text-center` |
| **Sticky Bottom Anchor Ad** | `TriviaSticky` / `StickyBottomMobile` | Fixed Bottom Viewport Overlay (Sticky) | `320x50` / `300x50` | `hide-above-1024 fixed bottom-0 left-0 right-0 z-50 bg-white border-t` |

---

## 2. Mobile Responsive Grid & HTML Structure

```html
<!-- 1. Mobile Top Banner (StickyTopMobile) -->
<div class="ad-container-mobile-top block lg:hidden my-2 flex flex-col items-center" data-ad-id="StickyTopMobile">
  <span class="text-[10px] text-gray-400 uppercase tracking-wider mb-1">ADVERTISEMENT</span>
  <div id="gam-mobile-top" data-ad-slot="/23351137437/ndtv.mobile.top" data-ad-width="320" data-ad-height="50"></div>
</div>

<!-- Main Mobile Quiz Container -->
<div class="quiz-card-mobile w-full px-4 py-3 bg-white rounded-lg shadow-sm">

  <!-- Question Heading -->
  <h2 class="question-title text-lg font-bold text-gray-900 mb-3">...</h2>

  <!-- 2. Mobile In-Card Middle Ad (MiddleMobileShort) -->
  <div class="ad-container-mobile-middle block lg:hidden my-3 flex flex-col items-center min-h-[250px]" data-ad-id="MiddleMobileShort">
    <span class="text-[10px] text-gray-400 uppercase tracking-wider mb-1">ADVERTISEMENT</span>
    <div id="gam-mobile-mid" data-ad-slot="/23351137437/ndtv.mobile.mid" data-ad-width="300" data-ad-height="250"></div>
  </div>

  <!-- 3. Pre-Option Mobile Ad (TriviaBottomTop) -->
  <div class="ad-container-mobile-preoption block lg:hidden my-2 flex flex-col items-center" data-ad-id="TriviaBottomTop">
    <div id="gam-mobile-preoption" data-ad-slot="/23351137437/ndtv.mobile.preoption" data-ad-width="300" data-ad-height="250"></div>
  </div>

  <!-- Quiz Answer Options -->
  <div class="options-container flex flex-col gap-2.5">
    <button class="option-btn">Option A</button>
    <button class="option-btn">Option B</button>
  </div>

</div>

<!-- 4. Mobile Content Bottom Ad (BottomMobile) -->
<div class="ad-container-mobile-bottom block lg:hidden my-4 flex flex-col items-center" data-ad-id="BottomMobile">
  <div id="gam-mobile-bottom" data-ad-slot="/23351137437/ndtv.mobile.bottom" data-ad-width="300" data-ad-height="250"></div>
</div>

<!-- Spacer div so sticky bottom ad doesn't obscure content footer -->
<div class="hide-above-1024 styles_stickySpacer__hHgC4 h-[60px]"></div>

<!-- 5. Sticky Bottom Footer Ad (TriviaSticky / StickyBottomMobile) -->
<div class="hide-above-1024 fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-gray-200 shadow-lg flex flex-col items-center py-1" data-ad-id="TriviaSticky" data-vad-id="StickyBottomMobile">
  <span class="text-[9px] text-gray-400">ADVERTISEMENT</span>
  <div id="gam-mobile-sticky-bottom" data-ad-slot="/23351137437/ndtv.mobile.stickybottom" data-ad-width="320" data-ad-height="50"></div>
</div>
```

---

## 3. Mobile Auto-Refresh & Scroll Logic

On Mobile question transitions, NDTV refreshes active mobile ad slots:

```javascript
// NDTV Mobile Question Navigation Refresh Trigger
function onMobileNextQuestion() {
  if (!config.noRefreshAdsLayout) {
    googletag.pubads().refresh([
      slotMap['MiddleMobileShort'],
      slotMap['TriviaBottomTop'],
      slotMap['BottomMobile'],
      slotMap['TriviaSticky'] // Sticky bottom footer anchor
    ]);
  }
}
```

### Banner Collapse (`.ad-collapsed`)
If `TriviaSticky` or `MiddleMobileShort` renders empty, NDTV adds `.ad-collapsed` (`display: none` / `height: 0`) to prevent blank white boxes or layout breaks on mobile screens.

---

## 4. Mobile Rewarded Ad Trigger

Mobile users triggering hints or revives invoke the native GAM Out-Of-Page rewarded flow:

```javascript
// Mobile Rewarded Ad Call
window.triggerRewardedAd = function() {
  if (typeof window.googletag !== 'undefined' && googletag.apiReady) {
    var rewardedSlot = googletag.defineOutOfPageSlot(
      '/23351137437/ndtv.mobile.rewarded',
      googletag.enums.OutOfPageFormat.REWARDED
    );
    if (rewardedSlot) {
      rewardedSlot.addService(googletag.pubads());
      googletag.enableServices();
      googletag.display(rewardedSlot);
    }
  }
};
```
