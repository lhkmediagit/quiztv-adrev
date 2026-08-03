/**
 * QuizTv Frontend Engine
 * Implements a state-driven, zero-page-reload quiz module.
 * Integrates interstitial overlays, transitions, dynamic options rendering, and AJAX completions.
 */

(function () {
    'use strict';

    // State Variables
    let attemptId = null;
    let quizId = null;
    let userId = null;
    let guestToken = null;
    let baseUrl = '';
    let csrfHeaderName = '';
    let csrfToken = '';

    let currentQuestion = null;
    let totalQuestions = 0;
    let scoreSoFar = 0;
    let questionsAnswered = 0;
    let currentRound = 1;
    let roundStartScore = 0;
    let lives = 2;

    // DOM Elements Cache
    const elements = {
        container: document.getElementById('quiz-container'),
        progress: document.getElementById('quiz-progress'),
        statusHeader: document.getElementById('quiz-status-header'),
        roundBadge: document.getElementById('quiz-round-badge'),
        questionCounter: document.getElementById('quiz-question-counter'),
        liveScore: document.getElementById('quiz-live-score'),
        quizLives: document.getElementById('quiz-lives'),
        cardContainer: document.getElementById('quiz-card-container'),
        
        // Compact Progress Bar Elements
        compactContainer: document.getElementById('compact-progress-container'),
        compactRoundLabel: document.getElementById('compact-round-label'),
        compactStageTitle: document.getElementById('compact-stage-title'),
        compactLineFill: document.getElementById('compact-progress-line-fill'),
        compactSteps: document.getElementById('compact-progress-steps'),
        playCard: document.getElementById('quiz-play-card'),
        questionText: document.getElementById('quiz-question-text'),
        visualContainer: document.getElementById('quiz-visual'),
        optionsList: document.getElementById('quiz-options-list'),
        explanationBox: document.getElementById('explanation-box'),
        explanationTitle: document.getElementById('explanation-title'),
        explanationText: document.getElementById('explanation-text'),
        nextBtn: document.getElementById('next-question-btn'),
        resultsBtn: document.getElementById('see-results-btn'),

        // Overlays
        readyOverlay: document.getElementById('ready-overlay'),
        startBtn: document.getElementById('start-continue-btn'),

        roundOverlay: document.getElementById('round-overlay'),
        roundTitle: document.getElementById('round-title'),
        roundDesc: document.getElementById('round-desc'),
        roundBtn: document.getElementById('round-continue-btn'),
        roundResultsBtn: document.getElementById('round-results-btn'),
        leadSkipBtn: document.getElementById('lead-skip-btn'),

        confirmOverlay: document.getElementById('confirm-overlay'),
        confirmOkBtn: document.getElementById('confirm-ok-btn'),
        confirmCancelBtn: document.getElementById('confirm-cancel-btn'),

        livesOverlay: document.getElementById('lives-overlay'),
        livesAdBtn: document.getElementById('lives-ad-btn'),
        livesRestartBtn: document.getElementById('lives-restart-btn'),

        resultOverlay: document.getElementById('result-overlay'),
        resultTitleGreeting: document.getElementById('result-title-greeting'),
        resultBadgeIcon: document.getElementById('result-badge-icon'),
        resultScore: document.getElementById('result-score-display'),
        resultPct: document.getElementById('result-pct-display'),
        resultStatus: document.getElementById('result-status-badge'),
        resultRestartBtn: document.getElementById('result-restart-btn'),
        resultRecsWrapper: document.getElementById('result-recommendations-wrapper'),
        recGrid: document.getElementById('rec-mini-grid'),

        // Rewarded Ad Elements
        rewardedAdBtn: document.getElementById('rewarded-ad-btn'),
        rewardedStatus: document.getElementById('rewarded-status')
    };

    /**
     * Show non-intrusive toast notifications on error.
     */
    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'toast toast-error';
        toast.style.position = 'fixed';
        toast.style.bottom = '24px';
        toast.style.right = '24px';
        toast.style.zIndex = '9999';

        toast.innerHTML = `
            <span class="toast-icon">⚠</span>
            <span class="toast-text">${escapeHtml(message)}</span>
            <button class="toast-close">×</button>
        `;

        document.body.appendChild(toast);

        toast.querySelector('.toast-close').addEventListener('click', () => toast.remove());

        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 400);
        }, 5000);
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    /**
     * Perform an API request with CSRF token inclusion.
     */
    async function apiRequest(endpoint, params = {}) {
        const url = `${baseUrl}api/quiz/${endpoint}`;

        const formData = new FormData();
        // Append all parameters
        for (const [key, val] of Object.entries(params)) {
            if (val !== null && val !== undefined) {
                formData.append(key, val);
            }
        }
        // Append CSRF token to form data
        formData.append(csrfHeaderName, csrfToken);

        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    [csrfHeaderName]: csrfToken
                }
            });

            // Update internal CSRF token if rotated in headers
            const nextToken = response.headers.get(csrfHeaderName);
            if (nextToken) {
                csrfToken = nextToken;
            }

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const json = await response.json();
            if (!json.success) {
                throw new Error(json.message || 'API operation failed.');
            }

            return json.data;
        } catch (error) {
            console.error('Request failed:', error);
            showToast('Something went wrong. Please check your network connection.');
            throw error;
        }
    }

    /**
     * Initialize event handlers and configurations.
     */
    function initQuiz() {
        if (!window.quizConfig) {
            console.error('Quiz configuration missing!');
            return;
        }

        // Load configs
        quizId = window.quizConfig.quizId;
        userId = window.quizConfig.userId;
        guestToken = window.quizConfig.guestToken;
        baseUrl = window.quizConfig.baseUrl;
        csrfHeaderName = window.quizConfig.csrfHeaderName;
        csrfToken = window.quizConfig.csrfToken;

        // Bind actions
        const titleTop = document.getElementById('quiz-play-title-top');
        if (titleTop && window.quizConfig && window.quizConfig.title) {
            titleTop.textContent = window.quizConfig.title.toUpperCase();
        }

        if (elements.startBtn) {
            elements.startBtn.addEventListener('click', callStart);
        }

        if (elements.nextBtn) {
            elements.nextBtn.addEventListener('click', handleNextClick);
        }

        function handleNextClick() {
            callNext();
        }

        if (elements.resultsBtn) {
            elements.resultsBtn.addEventListener('click', callComplete);
        }

        if (elements.roundResultsBtn) {
            elements.roundResultsBtn.addEventListener('click', () => {
                if (elements.roundOverlay) elements.roundOverlay.style.display = 'none';
                callComplete();
            });
        }

        if (elements.leadSkipBtn) {
            elements.leadSkipBtn.addEventListener('click', async () => {
                const leadOverlay = document.getElementById('lead-overlay');
                if (leadOverlay) leadOverlay.style.display = 'none';
                
                try {
                    const completeData = await apiRequest('complete', {
                        attempt_id: attemptId
                    });
                    renderResult(completeData);
                } catch (err) {
                    showToast('Failed to connect. Please check your network connection.');
                }
            });
        }

        if (elements.resultRestartBtn) {
            elements.resultRestartBtn.addEventListener('click', callRestart);
        }

        if (elements.confirmCancelBtn) {
            elements.confirmCancelBtn.addEventListener('click', () => {
                elements.confirmOverlay.style.display = 'none';
            });
        }

        if (elements.confirmOkBtn) {
            elements.confirmOkBtn.addEventListener('click', callRestart);
        }

        if (elements.livesAdBtn) {
            let livesAdClicks = 0;
            elements.livesAdBtn.addEventListener('click', () => {
                livesAdClicks++;
                
                const proceedRevive = () => {
                    lives = 2;
                    updateLivesDisplay();
                    if (elements.livesOverlay) elements.livesOverlay.style.display = 'none';
                    elements.cardContainer.style.display = 'flex';
                    elements.statusHeader.style.display = 'flex';
                    if (elements.livesAdBtn) {
                        elements.livesAdBtn.disabled = false;
                        elements.livesAdBtn.textContent = '🎬 Watch Ad to Continue';
                    }
                    livesAdClicks = 0;
                    callNext();
                };

                if (livesAdClicks >= 3) {
                    console.log('[QuizTvAds] Revive ad bypassed by click limit.');
                    proceedRevive();
                    return;
                }

                const adConfig = window.quizConfig?.adConfig;
                if (adConfig && adConfig.enabled && adConfig.rewarded && adConfig.rewarded.enabled && adConfig.rewarded.slot && window.QuizTvAds) {
                    if (elements.livesAdBtn) {
                        const remaining = 3 - livesAdClicks;
                        elements.livesAdBtn.textContent = `Loading Ad... (${remaining} click${remaining > 1 ? 's' : ''} to skip)`;
                    }

                    QuizTvAds.showRewarded(
                        adConfig.rewarded.slot,
                        null,
                        () => {
                            proceedRevive();
                        }
                    );
                } else {
                    proceedRevive();
                }
            });
        }

        if (elements.livesRestartBtn) {
            elements.livesRestartBtn.addEventListener('click', () => {
                if (elements.livesOverlay) elements.livesOverlay.style.display = 'none';
                callRestart();
            });
        }

        const leadForm = document.getElementById('lead-capture-form');
        if (leadForm) {
            leadForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const name = document.getElementById('lead-input-name').value.trim();
                const phone = document.getElementById('lead-input-phone').value.trim();
                const email = document.getElementById('lead-input-email').value.trim();
                
                if (!name || !phone || !email) {
                    showToast('Please fill out all required fields.');
                    return;
                }

                // Enforce exactly 10 digits for phone number
                const phoneRegex = /^[0-9]{10}$/;
                if (!phoneRegex.test(phone)) {
                    showToast('Phone number must be exactly 10 digits.');
                    return;
                }

                const submitBtn = document.getElementById('lead-submit-btn');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Saving...';
                }

                try {
                    await apiRequest('save-lead', {
                        attempt_id: attemptId,
                        lead_name: name,
                        lead_phone: phone,
                        lead_email: email
                    });

                    const leadOverlay = document.getElementById('lead-overlay');
                    if (leadOverlay) leadOverlay.style.display = 'none';
                    
                    const completeData = await apiRequest('complete', {
                        attempt_id: attemptId
                    });
                    renderResult(completeData);
                } catch (err) {
                    showToast('Failed to connect. Please check your network connection.');
                } finally {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Get Results ➔';
                    }
                }
            });
        }

        // Auto start the quiz attempt
        callStart();
    }

    /**
     * Start the quiz session API run.
     */
    async function callStart() {
        const proceedStart = async () => {
            // Fade out ready overlay
            if (elements.readyOverlay) {
                elements.readyOverlay.classList.add('fade-out');
                setTimeout(() => {
                    elements.readyOverlay.style.display = 'none';
                    elements.readyOverlay.classList.remove('fade-out');
                }, 200);
            }

            try {
                const data = await apiRequest('start', {
                    quiz_id: quizId,
                    user_id: userId,
                    guest_token: guestToken
                });

                attemptId = data.attempt_id;
                totalQuestions = data.total_questions;
                scoreSoFar = 0;
                questionsAnswered = 0;
                currentRound = 1;
                roundStartScore = 0;
                lives = 2;
                updateLivesDisplay();

                // Reveal Status Headers
                elements.statusHeader.style.display = 'flex';
                elements.cardContainer.style.display = 'flex';

                renderQuestion(data.question);
            } catch (err) {
                // Error handled inside apiRequest toast
            }
        };

        proceedStart();
    }

    /**
     * Render the active question to DOM.
     */
    function renderQuestion(q) {
        // Detect round transitions (transition directly without showing overlay)
        if (q.round_number !== currentRound && questionsAnswered > 0) {
            currentRound = q.round_number;
            roundStartScore = scoreSoFar;
        }

        currentQuestion = q;
        currentRound = q.round_number;

        // Reset display card
        elements.playCard.classList.remove('fade-in');
        void elements.playCard.offsetWidth; // Trigger reflow
        elements.playCard.classList.add('fade-in');

        // Update progress counters & legacy fallback
        const progressPercent = (questionsAnswered / totalQuestions) * 100;
        elements.progress.style.width = `${progressPercent}%`;
        elements.questionCounter.textContent = `Question ${questionsAnswered + 1} of ${totalQuestions}`;
        elements.roundBadge.textContent = `Round ${q.round_number}`;
        elements.liveScore.textContent = scoreSoFar;

        // Render Compact Progress Bar
        if (elements.compactContainer) {
            elements.compactContainer.style.display = 'block';
            elements.compactRoundLabel.textContent = `Round ${q.round_number}`;
            elements.compactStageTitle.textContent = q.stage_title || '';
            
            // Build circular nodes
            elements.compactSteps.innerHTML = '';
            const roundTotal = parseInt(q.round_total) || 1;
            const roundIndex = parseInt(q.round_index) || 1;
            
            for (let i = 1; i <= roundTotal; i++) {
                const node = document.createElement('div');
                node.className = 'step-node';
                
                if (i < roundIndex) {
                    node.classList.add('completed');
                    node.innerHTML = '✓';
                } else if (i === roundIndex) {
                    node.classList.add('active');
                    node.textContent = i;
                } else {
                    node.textContent = i;
                }
                elements.compactSteps.appendChild(node);
            }
            
            // Fill line percentage
            const lineFillPercent = roundTotal > 1 ? ((roundIndex - 1) / (roundTotal - 1)) * 100 : 0;
            elements.compactLineFill.style.width = `${lineFillPercent}%`;
        }

        // Set question text
        elements.questionText.textContent = q.question;

        const qNumSpan = document.getElementById('quiz-question-number-span');
        if (qNumSpan) {
            qNumSpan.textContent = questionsAnswered + 1;
        }
        const qNumSpanMobile = document.getElementById('quiz-question-number-span-mobile');
        if (qNumSpanMobile) {
            qNumSpanMobile.textContent = questionsAnswered + 1;
        }

        // Render visual clue if present, otherwise show no image
        if (elements.visualContainer) {
            const visualWrapper = document.getElementById('quiz-visual-wrapper');
            if (q.visual && q.visual !== 'none') {
                elements.visualContainer.innerHTML = q.visual;
                elements.visualContainer.style.display = 'block';
                if (visualWrapper) {
                    visualWrapper.classList.remove('no-image');
                    visualWrapper.style.display = 'block';
                }
            } else {
                elements.visualContainer.innerHTML = '';
                elements.visualContainer.style.display = 'none';
                if (visualWrapper) {
                    visualWrapper.classList.add('no-image');
                    visualWrapper.style.display = 'block';
                }
            }
        }

        // Clear and build options buttons list
        elements.optionsList.innerHTML = '';
        const options = [q.option1, q.option2, q.option3, q.option4].filter(opt => opt && opt.trim() !== '');

        options.forEach((opt, idx) => {
            const optNum = idx + 1;
            const btn = document.createElement('button');
            btn.type = 'button';
            
            // Use the QuizTv button layout and styles for ALL quizzes in the project
            btn.className = 'option-btn option-btn-quiztv';
            btn.innerHTML = `
                <span class="option-letter">${optNum}</span>
                <span class="option-text">${opt}</span>
            `;
            
            btn.addEventListener('click', () => submitAnswer(optNum));
            elements.optionsList.appendChild(btn);
        });

        // Hide explanation details and next buttons initially
        elements.explanationText.textContent = '';
        if (elements.explanationBox) {
            elements.explanationBox.style.display = 'none';
            elements.explanationBox.classList.remove('explanation-correct', 'explanation-wrong');
        }
        elements.nextBtn.style.display = 'none';
        if (elements.resultsBtn) elements.resultsBtn.style.display = 'none';

        // Hide ads on Question 1 and show/refresh them on subsequent questions
        const midAdWrapper = document.getElementById('gam-banner-play-mid-wrapper');
        const questionAdWrapper = document.getElementById('gam-banner-play-question-wrapper');
        const bottomAdWrapper = document.getElementById('gam-banner-play-bottom-wrapper');

        if (questionsAnswered === 0) {
            if (midAdWrapper) {
                midAdWrapper.setAttribute('data-ad-hide', 'true');
                midAdWrapper.style.setProperty('display', 'none', 'important');
            }
            if (questionAdWrapper) {
                questionAdWrapper.setAttribute('data-ad-hide', 'true');
                questionAdWrapper.style.setProperty('display', 'none', 'important');
            }
            if (bottomAdWrapper) {
                bottomAdWrapper.setAttribute('data-ad-hide', 'true');
                bottomAdWrapper.style.setProperty('display', 'none', 'important');
            }
        } else {
            if (midAdWrapper) {
                midAdWrapper.removeAttribute('data-ad-hide');
                midAdWrapper.style.display = 'flex';
            }
            if (questionAdWrapper) {
                questionAdWrapper.removeAttribute('data-ad-hide');
                questionAdWrapper.style.display = 'flex';
            }
            if (bottomAdWrapper) {
                bottomAdWrapper.removeAttribute('data-ad-hide');
                bottomAdWrapper.style.display = 'flex';
            }

            if (window.QuizTvAds && typeof window.QuizTvAds.refreshBanner === 'function') {
                window.QuizTvAds.refreshBanner('gam-banner-play-mid');
                window.QuizTvAds.refreshBanner('gam-banner-play-question');
                window.QuizTvAds.refreshBanner('gam-banner-play-bottom');
            }
        }

        // Scroll smoothly back to the top of the entire page for the next question
        setTimeout(() => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }, 100);
    }

    /**
     * Submit selected option choice to API evaluation.
     */
    async function submitAnswer(selected) {
        // Disable all choices immediately
        const buttons = elements.optionsList.querySelectorAll('.option-btn');
        buttons.forEach(btn => btn.disabled = true);

        // Highlight selected choice with active primary style
        const selectedBtn = buttons[selected - 1];
        selectedBtn.style.borderColor = 'var(--primary)';
        selectedBtn.style.backgroundColor = 'var(--primary-light)';

        try {
            const data = await apiRequest('answer', {
                attempt_id: attemptId,
                question_id: currentQuestion.id,
                selected_option: selected
            });

            scoreSoFar = data.score_so_far;
            questionsAnswered = data.questions_answered;
            elements.liveScore.textContent = scoreSoFar;

            // Clear temporary selection styles
            selectedBtn.style.borderColor = '';
            selectedBtn.style.backgroundColor = '';

            // Highlight answers based on API response
            if (data.is_correct) {
                selectedBtn.classList.add('option-correct');
                if (elements.explanationTitle) elements.explanationTitle.textContent = 'Correct answer 👍';
                if (elements.explanationBox) {
                    elements.explanationBox.classList.remove('explanation-wrong');
                    elements.explanationBox.classList.add('explanation-correct');
                }
            } else {
                selectedBtn.classList.add('option-wrong');
                
                const correctBtn = buttons[data.correct_option - 1];
                if (correctBtn) {
                    correctBtn.classList.add('option-correct');
                }
                
                if (elements.explanationTitle) elements.explanationTitle.textContent = 'Wrong answer 😔';
                if (elements.explanationBox) {
                    elements.explanationBox.classList.remove('explanation-correct');
                    elements.explanationBox.classList.add('explanation-wrong');
                }
            }

            // Populate and show explanation details unconditionally
            elements.explanationText.textContent = (data.explanation && data.explanation.trim() !== '') ? data.explanation : '';
            if (elements.explanationBox) {
                elements.explanationBox.style.display = 'block';
            }

            // Keep playing: show manual navigation button (Next Question)
            elements.nextBtn.style.display = 'block';

            // Scroll smoothly to next button so user doesn't have to scroll manually
            setTimeout(() => {
                if (elements.nextBtn) {
                    elements.nextBtn.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }, 100);

        } catch (err) {
            // Enable options back if failed to submit so they can try again
            buttons.forEach(btn => btn.disabled = false);
            selectedBtn.style.borderColor = '';
            selectedBtn.style.backgroundColor = '';
        }
    }

    /**
     * Fetch next question of attempt.
     */
    async function callNext() {
        try {
            const data = await apiRequest('next', {
                attempt_id: attemptId,
                current_order_index: currentQuestion.order_index
            });

            if (data.is_last) {
                // Should not happen as complete checks take over, but backup trigger
                callComplete();
            } else {
                renderQuestion(data.question);
            }
        } catch (err) {
            // Error managed
        }
    }

    /**
     * Render the round completion overlay.
     */
    function showRoundEndOverlay(roundNum) {
        return new Promise((resolve) => {
            elements.roundTitle.textContent = `Round ${roundNum} Complete!`;

            const roundQuestionsCount = questionsAnswered; // questions answered in this round so far
            const roundScore = scoreSoFar - roundStartScore;

            elements.roundDesc.textContent = `You scored ${roundScore} correct answers in this round.`;

            // Hide questions during round overlay to prevent leakage/clipping
            elements.cardContainer.style.display = 'none';
            elements.statusHeader.style.display = 'none';

            elements.roundOverlay.style.display = 'flex';

            // Show appropriate buttons based on whether it is the last question of the quiz
            const isLast = (questionsAnswered >= totalQuestions);
            if (isLast) {
                if (elements.roundBtn) elements.roundBtn.style.display = 'none';
                if (elements.roundResultsBtn) elements.roundResultsBtn.style.display = 'block';
            } else {
                if (elements.roundBtn) elements.roundBtn.style.display = 'block';
                if (elements.roundResultsBtn) elements.roundResultsBtn.style.display = 'block';
            }

            // Ensure custom buttons/status elements are hidden if present
            if (elements.rewardedAdBtn) {
                elements.rewardedAdBtn.style.display = 'none';
            }
            if (elements.rewardedStatus) {
                elements.rewardedStatus.style.display = 'none';
            }

            const handleContinue = () => {
                const proceedContinue = () => {
                    elements.roundOverlay.style.display = 'none';

                    // Show questions back
                    elements.cardContainer.style.display = 'flex';
                    elements.statusHeader.style.display = 'flex';

                    elements.roundBtn.removeEventListener('click', handleContinue);
                    resolve();
                };

                const adConfig = window.quizConfig?.adConfig;
                if (adConfig && adConfig.enabled && adConfig.rewarded && adConfig.rewarded.enabled && adConfig.rewarded.slot && window.QuizTvAds) {
                    elements.roundBtn.disabled = true;
                    elements.roundBtn.textContent = 'Loading Ad...';

                    QuizTvAds.showRewarded(
                        adConfig.rewarded.slot,
                        null,
                        () => {
                            elements.roundBtn.disabled = false;
                            elements.roundBtn.textContent = 'Continue to Next Round';
                            proceedContinue();
                        }
                    );
                } else {
                    proceedContinue();
                }
            };

            elements.roundBtn.addEventListener('click', handleContinue);
        });
    }

    async function callComplete() {
        // Hide active card HUD, headers, and round completed overlays to clean the view
        if (elements.cardContainer) elements.cardContainer.style.display = 'none';
        if (elements.statusHeader) elements.statusHeader.style.display = 'none';
        if (elements.roundOverlay) elements.roundOverlay.style.display = 'none';
        if (elements.compactContainer) elements.compactContainer.style.display = 'none';

        // Show lead capture form overlay immediately and scroll to top
        const leadOverlay = document.getElementById('lead-overlay');
        if (leadOverlay) {
            leadOverlay.style.display = 'flex';
            window.scrollTo(0, 0);
        } else {
            // Fallback if lead overlay not present
            try {
                const data = await apiRequest('complete', {
                    attempt_id: attemptId
                });
                renderResult(data);
            } catch (err) {
                // Managed
            }
        }
    }

    /**
     * Render final score results page with custom recommendations.
     */
    function renderResult(data) {
        // Hide active card HUD
        elements.cardContainer.style.display = 'none';
        elements.statusHeader.style.display = 'none';
        elements.progress.style.width = '100%';

        // Setup results card data
        elements.resultScore.textContent = `${data.score} / ${data.total_questions}`;
        elements.resultPct.textContent = `${data.percentage}%`;

        // Render name greeting
        if (elements.resultTitleGreeting) {
            if (data.lead_name) {
                elements.resultTitleGreeting.textContent = `${data.lead_name}, your result is ready!`;
            } else {
                elements.resultTitleGreeting.textContent = 'Quiz Completed!';
            }
        }

        // Render pass fail statuses
        elements.resultStatus.textContent = data.pass_fail_label.toUpperCase();
        elements.resultStatus.className = 'result-status-badge';
        if (data.pass_fail_label.toLowerCase() === 'pass') {
            elements.resultStatus.classList.add('pass');
            elements.resultBadgeIcon.textContent = '🎉';
        } else {
            elements.resultStatus.classList.add('fail');
            elements.resultBadgeIcon.textContent = '💀';
        }

        // Render round wise scores breakdown
        const roundsBreakdown = document.getElementById('result-rounds-breakdown');
        if (roundsBreakdown && data.round_stats) {
            roundsBreakdown.innerHTML = '';
            data.round_stats.forEach(stat => {
                const row = document.createElement('div');
                row.style.display = 'flex';
                row.style.justifyContent = 'space-between';
                row.style.alignItems = 'center';
                row.style.padding = '10px 14px';
                row.style.backgroundColor = '#f8fafc';
                row.style.borderRadius = 'var(--radius-md)';
                row.style.borderLeft = '4px solid var(--primary)';
                row.style.fontSize = '14px';
                row.style.boxShadow = 'var(--shadow-sm)';

                row.innerHTML = `
                    <span style="font-weight: 700; color: var(--text);">${escapeHtml(stat.stage_title)}</span>
                    <span style="font-weight: 800; color: var(--primary);">${stat.round_score} / ${stat.round_total}</span>
                `;
                roundsBreakdown.appendChild(row);
            });
            const breakdownWrapper = document.getElementById('result-breakdown-wrapper');
            if (breakdownWrapper) breakdownWrapper.style.display = 'block';
        }

        // Render mini recommended grid
        if (data.recommended && data.recommended.length > 0) {
            elements.recGrid.innerHTML = '';
            data.recommended.forEach(rec => {
                const link = document.createElement('a');
                link.href = `${baseUrl}quiz/${rec.slug}`;
                link.className = 'rec-mini-card';

                link.innerHTML = `
                    <span class="rec-mini-icon">🧠</span>
                    <span class="rec-mini-title">${escapeHtml(rec.title)}</span>
                `;
                elements.recGrid.appendChild(link);
            });
            elements.resultRecsWrapper.style.display = 'block';
        } else {
            elements.resultRecsWrapper.style.display = 'none';
        }

        // Display results overlay
        elements.resultOverlay.style.display = 'flex';
        window.scrollTo({ top: 0, behavior: 'smooth' });

        // Initialize the result banner ad dynamically
        const adConfig = window.quizConfig?.adConfig;
        if (adConfig && adConfig.banner && adConfig.banner.enabled && adConfig.banner.play_slot && window.QuizTvAds) {
            const resultAdDiv = document.getElementById('gam-banner-play-result');
            if (resultAdDiv) {
                QuizTvAds.initBanner(
                    adConfig.banner.play_slot,
                    'gam-banner-play-result',
                    336, 280
                );
            }
        }
    }

    /**
     * Trigger confirm restart display.
     */
    function confirmRestart() {
        elements.confirmOverlay.style.display = 'flex';
    }

    /**
     * Restart current quiz state.
     */
    async function callRestart() {
        if (elements.confirmOverlay) elements.confirmOverlay.style.display = 'none';
        if (elements.resultOverlay) elements.resultOverlay.style.display = 'none';

        try {
            const data = await apiRequest('restart', {
                quiz_id: quizId,
                old_attempt_id: attemptId
            });

            attemptId = data.attempt_id;
            totalQuestions = data.total_questions;
            scoreSoFar = 0;
            questionsAnswered = 0;
            currentRound = 1;
            roundStartScore = 0;
            lives = 2;
            updateLivesDisplay();

            // Reset progress
            elements.progress.style.width = '0%';
            if (elements.statusHeader) elements.statusHeader.style.display = 'flex';
            if (elements.cardContainer) elements.cardContainer.style.display = 'flex';

            renderQuestion(data.question);
        } catch (err) {
            // Managed
        }
    }

    function updateLivesDisplay() {
        if (!elements.quizLives) return;
        let hearts = '';
        for (let i = 0; i < 2; i++) {
            if (i < lives) {
                hearts += '❤️';
            } else {
                hearts += '🖤';
            }
        }
        elements.quizLives.textContent = hearts;
    }

    // Trigger execution on load
    document.addEventListener('DOMContentLoaded', initQuiz);

})();
