/**
 * QuizTv Ads Module — NDTV Trivia Architecture
 * =============================================
 * Centralized Google Ad Manager (GPT) integration matching
 * NDTV Trivia's exact ad flow:
 *
 * BANNERS:  enableSingleRequest + collapseEmptyDivs + responsive sizeMapping
 * REFRESH:  refreshAllAds(slotKeys) on question navigation
 * COLLAPSE: slotRenderEnded → data-ad-empty toggle + ad-collapsed class
 * REWARDED: triggerRewardedAd() / onRewardedAdFinished(result) globals
 *           with dual timeouts (GPT startup + ad response)
 */

(function () {
    'use strict';

    // Prevent double-init
    if (window.QuizTvAds) return;

    // ========================================================================
    // REWARDED AD SYSTEM (NDTV-style globals)
    // Standalone module — separate from banner management
    // ========================================================================
    var GPT_STARTUP_FALLBACK_TIMEOUT = 8000;  // 8s for GPT to process cmd
    var AD_RESPONSE_TIMEOUT = 10000;          // 10s for ad fill

    var rewardedSlot = null;
    var rewardAdEvent = null;
    var rewardGranted = false;
    var adResponseTimeout = null;
    var gptStartupFallbackTimeout = null;
    var flowFinished = false;
    var rewardedAdUnitPath = '';  // Set from config during init

    function clearAdResponseTimeout() {
        clearTimeout(adResponseTimeout);
        adResponseTimeout = null;
    }

    function clearGptStartupFallback() {
        clearTimeout(gptStartupFallbackTimeout);
        gptStartupFallbackTimeout = null;
    }

    function finishRewardFlow(result) {
        if (flowFinished) {
            return;
        }

        flowFinished = true;
        clearAdResponseTimeout();
        clearGptStartupFallback();

        if (typeof window.onRewardedAdFinished === 'function') {
            try {
                window.onRewardedAdFinished(result);
            } catch (err) {
                // The reward flow is already finished.
            }
        }
    }

    /**
     * NDTV-style triggerRewardedAd() — global function
     * Called from quiz.js when user needs to watch a rewarded ad
     */
    function triggerRewardedAd() {
        rewardGranted = false;
        rewardAdEvent = null;
        flowFinished = false;

        clearAdResponseTimeout();
        clearGptStartupFallback();

        // Covers only cases where GPT never loads or processes the command.
        gptStartupFallbackTimeout = setTimeout(function () {
            finishRewardFlow({
                status: 'timeout',
                rewardGranted: false,
                reason: 'gpt_startup_timeout'
            });
        }, GPT_STARTUP_FALLBACK_TIMEOUT);

        window.googletag.cmd.push(function () {
            clearGptStartupFallback();

            if (flowFinished) {
                return;
            }

            // Destroy previous slot if exists
            if (rewardedSlot) {
                googletag.destroySlots([rewardedSlot]);
                rewardedSlot = null;
            }

            rewardedSlot = googletag.defineOutOfPageSlot(
                rewardedAdUnitPath,
                googletag.enums.OutOfPageFormat.REWARDED
            );

            if (!rewardedSlot) {
                finishRewardFlow({
                    status: 'failed',
                    rewardGranted: false,
                    reason: 'slot_definition_failed'
                });
                return;
            }

            rewardedSlot.addService(googletag.pubads());

            // Covers no-fill or missing GPT ad response after startup.
            adResponseTimeout = setTimeout(function () {
                finishRewardFlow({
                    status: 'timeout',
                    rewardGranted: false,
                    reason: 'ad_response_timeout'
                });
            }, AD_RESPONSE_TIMEOUT);

            googletag.pubads().refresh([rewardedSlot]);
        });
    }

    function showRewardAd() {
        if (!rewardAdEvent) {
            return;
        }

        clearAdResponseTimeout();
        clearGptStartupFallback();

        try {
            rewardAdEvent.makeRewardedVisible();
        } catch (err) {
            finishRewardFlow({
                status: 'failed',
                rewardGranted: false,
                reason: 'make_visible_failed'
            });
        }

        rewardAdEvent = null;
    }

    // Expose rewarded globals (NDTV pattern)
    window.triggerRewardedAd = triggerRewardedAd;

    // ========================================================================
    // BANNER AD SYSTEM
    // ========================================================================

    const QuizTvAds = {
        initialized: false,
        slots: {},           // divId -> googletag.Slot
        refreshTimers: {},
        lastRefreshTime: {},
        config: null,
        rewardedListenersSetup: false,

        /**
         * Initialize the ads system with configuration from the server.
         * Called automatically when the page loads if ads are enabled.
         *
         * @param {Object} adConfig - Ad configuration object from PHP
         */
        init(adConfig) {
            if (!adConfig || !adConfig.enabled) return;
            if (this.initialized) return;

            this.config = adConfig;
            this.initialized = true;

            // Store rewarded ad unit path for the global triggerRewardedAd function
            if (adConfig.rewarded && adConfig.rewarded.slot) {
                rewardedAdUnitPath = adConfig.rewarded.slot;
            }

            // Load GPT library if not already present
            this._loadGPT(() => {
                this._setupRewardedListeners();
                this._initAllBanners();
            });
        },

        /**
         * Load the Google Publisher Tags library.
         * @param {Function} callback
         */
        _loadGPT(callback) {
            if (window.googletag && window.googletag.apiReady) {
                callback();
                return;
            }

            window.googletag = window.googletag || { cmd: [] };

            const script = document.createElement('script');
            script.src = 'https://securepubads.g.doubleclick.net/tag/js/gpt.js';
            script.async = true;
            script.onload = () => {
                googletag.cmd.push(callback);
            };
            script.onerror = () => {
                console.warn('[QuizTvAds] Failed to load GPT library.');
            };
            document.head.appendChild(script);
        },

        /**
         * Setup rewarded ad event listeners (once only).
         * NDTV-style: rewardedSlotReady, rewardedSlotGranted, rewardedSlotClosed,
         * slotRenderEnded (for rewarded empty check)
         */
        _setupRewardedListeners() {
            if (this.rewardedListenersSetup) return;
            this.rewardedListenersSetup = true;

            googletag.cmd.push(() => {
                // SRA + collapse
                googletag.pubads().enableSingleRequest();
                googletag.pubads().collapseEmptyDivs(true);

                // Rewarded slot ready — store event and auto-show
                googletag.pubads().addEventListener('rewardedSlotReady', function (event) {
                    if (!rewardedSlot || event.slot !== rewardedSlot) {
                        return;
                    }
                    rewardAdEvent = event;
                    showRewardAd();
                });

                // Rewarded slot granted — user earned the reward
                googletag.pubads().addEventListener('rewardedSlotGranted', function (event) {
                    if (!rewardedSlot || event.slot !== rewardedSlot) {
                        return;
                    }
                    rewardGranted = true;
                });

                // Rewarded slot closed — finalize flow
                googletag.pubads().addEventListener('rewardedSlotClosed', function (event) {
                    if (!rewardedSlot || event.slot !== rewardedSlot) {
                        return;
                    }
                    finishRewardFlow({
                        status: rewardGranted ? 'granted' : 'closed',
                        rewardGranted: rewardGranted,
                        reason: rewardGranted ? 'reward_granted' : 'user_closed_early'
                    });
                });

                // SlotRenderEnded — handle banner collapse AND rewarded empty check
                googletag.pubads().addEventListener('slotRenderEnded', (event) => {
                    const slotId = event.slot.getSlotElementId();

                    // Check if this is the rewarded slot
                    if (rewardedSlot && event.slot === rewardedSlot) {
                        if (event.isEmpty) {
                            finishRewardFlow({
                                status: 'failed',
                                rewardGranted: false,
                                reason: 'no_fill'
                            });
                        }
                        return;
                    }

                    // Banner ad collapse handling (NDTV pattern)
                    const wrapper = document.getElementById(slotId + '-wrapper');
                    if (wrapper) {
                        if (event.isEmpty) {
                            wrapper.setAttribute('data-ad-empty', 'true');
                            wrapper.classList.add('ad-collapsed');
                            wrapper.style.display = 'none';
                        } else {
                            wrapper.setAttribute('data-ad-empty', 'false');
                            wrapper.classList.remove('ad-collapsed');
                            wrapper.style.display = 'flex';
                        }
                    }
                });
            });
        },

        /**
         * Scan the page for all ad banner slot divs and initialize them.
         */
        _initAllBanners() {
            const adDivs = document.querySelectorAll('.ad-banner-slot');
            if (!adDivs.length) return;

            googletag.cmd.push(() => {
                adDivs.forEach(div => {
                    const slotPath = div.getAttribute('data-ad-slot');
                    const width = parseInt(div.getAttribute('data-ad-width')) || 728;
                    const height = parseInt(div.getAttribute('data-ad-height')) || 90;
                    const divId = div.id;

                    if (!slotPath || !divId) return;

                    this._defineSlot(slotPath, divId, width, height);
                });

                googletag.enableServices();

                // Display all defined slots
                adDivs.forEach(div => {
                    googletag.display(div.id);
                });

                // Setup auto-refresh if configured
                this._setupAutoRefresh();
            });
        },

        /**
         * Define a single GPT ad slot with NDTV-style responsive sizeMapping.
         */
        _defineSlot(slotPath, divId, width, height) {
            try {
                const slot = googletag.defineSlot(slotPath, [width, height], divId);
                if (slot) {
                    // Add responsive size mapping (NDTV pattern)
                    const mapping = this._buildSizeMapping(width, height);
                    if (mapping) {
                        slot.defineSizeMapping(mapping);
                    }
                    slot.addService(googletag.pubads());
                    this.slots[divId] = slot;
                    this.lastRefreshTime[divId] = 0;
                }
            } catch (e) {
                console.warn('[QuizTvAds] Error defining slot:', divId, e);
            }
        },

        /**
         * Build NDTV-style responsive size mapping for a slot.
         */
        _buildSizeMapping(width, height) {
            try {
                // Skyscraper (160x600) — show only on wide desktop
                if (width === 160 && height === 600) {
                    return googletag.sizeMapping()
                        .addSize([1200, 0], [160, 600])
                        .addSize([0, 0], [])
                        .build();
                }

                // Leaderboard (728x90) — responsive
                if (width >= 728) {
                    return googletag.sizeMapping()
                        .addSize([728, 0], [728, 90])
                        .addSize([468, 0], [[468, 60], [320, 50]])
                        .addSize([0, 0], [[320, 50], [300, 50]])
                        .build();
                }

                // Medium rectangle (336x280 or 300x250)
                if ((width === 336 && height === 280) || (width === 300 && height === 250)) {
                    return googletag.sizeMapping()
                        .addSize([768, 0], [[336, 280], [300, 250]])
                        .addSize([0, 0], [[300, 250]])
                        .build();
                }

                // Sticky banners (320x50) — mobile only
                if (width === 320 && height === 50) {
                    return googletag.sizeMapping()
                        .addSize([1024, 0], [])
                        .addSize([0, 0], [[320, 50], [300, 50]])
                        .build();
                }

                return null;
            } catch (e) {
                return null;
            }
        },

        /**
         * Initialize a single banner ad slot dynamically (for lazy-loaded ads).
         *
         * @param {string} slotPath  - Full GAM ad unit path
         * @param {string} divId     - DOM element ID
         * @param {number} width     - Ad width
         * @param {number} height    - Ad height
         */
        initBanner(slotPath, divId, width, height) {
            if (!this.initialized || !slotPath || !divId) return;

            const el = document.getElementById(divId);
            if (!el) return;

            googletag.cmd.push(() => {
                // Don't redefine if already exists
                if (this.slots[divId]) {
                    googletag.pubads().refresh([this.slots[divId]]);
                    return;
                }

                this._defineSlot(slotPath, divId, width, height);
                googletag.display(divId);
            });
        },

        /**
         * NDTV-style refreshAllAds — refresh specific slots by div ID array.
         * Called on question navigation.
         *
         * @param {string[]} [divIds] - Optional array of div IDs to refresh. Refreshes all if omitted.
         */
        refreshAllAds(divIds) {
            if (!this.initialized) return;

            googletag.cmd.push(() => {
                const now = Date.now();
                const minInterval = 30000; // GAM 30s minimum

                let slotsToRefresh;

                if (divIds && Array.isArray(divIds)) {
                    slotsToRefresh = divIds
                        .map(id => this.slots[id])
                        .filter(slot => !!slot);
                } else {
                    slotsToRefresh = Object.values(this.slots);
                }

                // Filter by 30s throttle
                slotsToRefresh = slotsToRefresh.filter(slot => {
                    const divId = slot.getSlotElementId();
                    const last = this.lastRefreshTime[divId] || 0;
                    return (now - last) >= minInterval;
                });

                if (slotsToRefresh.length > 0) {
                    googletag.pubads().refresh(slotsToRefresh);
                    slotsToRefresh.forEach(slot => {
                        this.lastRefreshTime[slot.getSlotElementId()] = now;
                    });
                }
            });
        },

        /**
         * Refresh a specific banner slot (respects 30s minimum).
         *
         * @param {string} divId - The div ID of the banner to refresh
         */
        refreshBanner(divId) {
            if (!this.initialized) return;

            const slot = this.slots[divId];
            if (!slot) return;

            // Enforce 30-second minimum refresh interval (GAM policy)
            const now = Date.now();
            const lastRefresh = this.lastRefreshTime[divId] || 0;
            const minInterval = 30000; // 30 seconds

            if (now - lastRefresh < minInterval) {
                return;
            }

            googletag.cmd.push(() => {
                googletag.pubads().refresh([slot]);
                this.lastRefreshTime[divId] = Date.now();
            });
        },

        /**
         * Setup auto-refresh for all banner slots.
         */
        _setupAutoRefresh() {
            if (!this.config || !this.config.banner) return;

            const interval = Math.max(30, this.config.banner.refresh || 60) * 1000;

            // Clear any existing timers
            Object.values(this.refreshTimers).forEach(t => clearInterval(t));
            this.refreshTimers = {};

            // Set refresh timers for each slot
            Object.keys(this.slots).forEach(divId => {
                this.refreshTimers[divId] = setInterval(() => {
                    // Only refresh if the ad container is visible
                    const el = document.getElementById(divId);
                    if (el && this._isElementVisible(el)) {
                        this.refreshBanner(divId);
                    }
                }, interval);
            });
        },

        /**
         * Check if an element is currently visible in the viewport.
         * NDTV-style: also checks collapsed wrappers by scroll position.
         */
        _isElementVisible(el) {
            const wrapper = document.getElementById(el.id + '-wrapper');
            if (wrapper) {
                const isCollapsed = wrapper.classList.contains('ad-collapsed');
                if (isCollapsed) {
                    // Collapsed containers have 0 height, check vertical scroll bounds
                    const rect = wrapper.getBoundingClientRect();
                    return rect.top < window.innerHeight && rect.top > -100;
                }
            }
            const rect = el.getBoundingClientRect();
            return (
                rect.top < window.innerHeight &&
                rect.bottom > 0 &&
                rect.width > 0 &&
                rect.height > 0
            );
        },

        /**
         * Show full-screen interstitial ad.
         */
        showInterstitial(slotPath, onClose) {
            if (!this.initialized || !slotPath) {
                if (onClose) onClose();
                return;
            }

            let resolved = false;
            const safeClose = () => {
                if (resolved) return;
                resolved = true;
                if (onClose) onClose();
            };

            // Set safety fallback timeout (8 seconds) in case GPT fails to load or close
            const safetyTimeout = setTimeout(() => {
                console.warn('[QuizTvAds] Interstitial ad loading/rendering timed out. Proceeding...');
                safeClose();
            }, 8000);

            googletag.cmd.push(() => {
                try {
                    const interstitialSlot = googletag.defineOutOfPageSlot(
                        slotPath,
                        googletag.enums.OutOfPageFormat.INTERSTITIAL
                    );

                    if (!interstitialSlot) {
                        console.warn('[QuizTvAds] Could not create interstitial slot.');
                        clearTimeout(safetyTimeout);
                        safeClose();
                        return;
                    }

                    interstitialSlot.addService(googletag.pubads());

                    googletag.pubads().addEventListener('slotVisibilityChanged', (event) => {
                        if (event.slot === interstitialSlot && event.inViewPercentage === 0) {
                            clearTimeout(safetyTimeout);
                            googletag.destroySlots([interstitialSlot]);
                            safeClose();
                        }
                    });

                    googletag.enableServices();
                    googletag.display(interstitialSlot);

                } catch (e) {
                    console.warn('[QuizTvAds] Interstitial ad error:', e);
                    clearTimeout(safetyTimeout);
                    safeClose();
                }
            });
        },

        /**
         * Destroy all ad slots and clear timers. Call on page unload.
         */
        destroyAll() {
            // Clear all refresh timers
            Object.values(this.refreshTimers).forEach(t => clearInterval(t));
            this.refreshTimers = {};

            // Destroy GPT slots
            if (window.googletag && googletag.apiReady) {
                googletag.cmd.push(() => {
                    googletag.destroySlots();
                });
            }

            this.slots = {};
            this.lastRefreshTime = {};
            this.initialized = false;
        }
    };

    // Expose globally
    window.QuizTvAds = QuizTvAds;

    // Cleanup on page unload
    window.addEventListener('beforeunload', () => {
        QuizTvAds.destroyAll();
    });

})();
