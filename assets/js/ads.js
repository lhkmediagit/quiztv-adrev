/**
 * QuizTv Ads Module
 * Centralized Google Ad Manager (GPT) integration.
 * Handles banner ad initialization, refresh, rewarded ads, and cleanup.
 * Adheres to GAM policies: min 30s refresh, user-initiated rewarded ads, proper labeling.
 */

(function () {
    'use strict';

    // Prevent double-init
    if (window.QuizTvAds) return;

    const QuizTvAds = {
        initialized: false,
        slots: {},
        refreshTimers: {},
        lastRefreshTime: {},
        config: null,

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

            // Load GPT library if not already present
            this._loadGPT(() => {
                googletag.cmd.push(() => {
                    googletag.pubads().addEventListener('slotRenderEnded', (event) => {
                        const slotId = event.slot.getSlotElementId();
                        const wrapper = document.getElementById(slotId + '-wrapper');
                        if (wrapper) {
                            if (event.isEmpty) {
                                wrapper.setAttribute('data-ad-empty', 'true');
                                wrapper.style.display = 'none';
                            } else {
                                wrapper.setAttribute('data-ad-empty', 'false');
                                if (wrapper.getAttribute('data-ad-hide') === 'true') {
                                    wrapper.style.display = 'none';
                                } else {
                                    wrapper.style.display = 'flex';
                                }
                            }
                        }
                    });
                });
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

                // Enable SRA (Single Request Architecture) for better performance
                googletag.pubads().enableSingleRequest();
                googletag.pubads().collapseEmptyDivs();
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
         * Define a single GPT ad slot.
         */
        _defineSlot(slotPath, divId, width, height) {
            try {
                const size = this.config.banner.size === 'responsive'
                    ? this._getResponsiveSizes(width, height)
                    : [[width, height]];

                const slot = googletag.defineSlot(slotPath, size, divId);
                if (slot) {
                    slot.addService(googletag.pubads());
                    this.slots[divId] = slot;
                    this.lastRefreshTime[divId] = 0; // Initialize to 0 so the first dynamic refresh is not throttled
                }
            } catch (e) {
                console.warn('[QuizTvAds] Error defining slot:', divId, e);
            }
        },

        _getResponsiveSizes(defaultWidth, defaultHeight) {
            const vw = window.innerWidth;
            
            // If the ad is a horizontal banner (leaderboard)
            if (defaultWidth >= 728) {
                if (vw < 480) {
                    return [[320, 50], [300, 50]];
                } else if (vw < 768) {
                    return [[468, 60], [320, 50]];
                } else {
                    return [[728, 90], [468, 60]];
                }
            }
            
            // If the ad is a square / rectangle (e.g. sidebar, results screen)
            if (vw < 480) {
                return [[300, 250]];
            } else {
                return [[defaultWidth, defaultHeight], [300, 250], [336, 280]];
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
                console.log(`[QuizTvAds] Refresh blocked for ${divId} — minimum 30s interval.`);
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
         */
        _isElementVisible(el) {
            const rect = el.getBoundingClientRect();
            return (
                rect.top < window.innerHeight &&
                rect.bottom > 0 &&
                rect.width > 0 &&
                rect.height > 0
            );
        },

        showRewarded(slotPath, onReward, onClose) {
            if (!this.initialized || !slotPath) {
                if (onClose) onClose(false);
                return;
            }

            let resolved = false;
            const safeClose = (status) => {
                if (resolved) return;
                resolved = true;
                if (onClose) onClose(status);
            };

            // Set safety fallback timeout (2.5 seconds) in case GPT fails to load/render the ad
            const safetyTimeout = setTimeout(() => {
                console.warn('[QuizTvAds] Rewarded ad loading timed out. Proceeding...');
                safeClose(false);
            }, 2500);

            googletag.cmd.push(() => {
                try {
                    // Define rewarded ad slot
                    const rewardedSlot = googletag.defineOutOfPageSlot(
                        slotPath,
                        googletag.enums.OutOfPageFormat.REWARDED
                    );

                    if (!rewardedSlot) {
                        console.warn('[QuizTvAds] Could not create rewarded slot.');
                        clearTimeout(safetyTimeout);
                        safeClose(false);
                        return;
                    }

                    rewardedSlot.addService(googletag.pubads());

                    // Listen for rewarded ad events
                    googletag.pubads().addEventListener('rewardedSlotReady', (event) => {
                        clearTimeout(safetyTimeout); // Clear safety timeout since ad is ready to show
                        event.makeRewardedVisible();
                    });

                    googletag.pubads().addEventListener('rewardedSlotGranted', () => {
                        // User earned the reward
                        if (onReward) onReward();
                    });

                    googletag.pubads().addEventListener('rewardedSlotClosed', () => {
                        // Cleanup
                        clearTimeout(safetyTimeout);
                        googletag.destroySlots([rewardedSlot]);
                        safeClose(true);
                    });

                    googletag.enableServices();
                    googletag.display(rewardedSlot);

                } catch (e) {
                    console.warn('[QuizTvAds] Rewarded ad error:', e);
                    clearTimeout(safetyTimeout);
                    safeClose(false);
                }
            });
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
