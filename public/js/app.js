/**
 * Tima-Ade University — Main Application JavaScript
 */

(function() {
    const sidebar = document.getElementById('sidebar');
    const sidebarNav = sidebar ? sidebar.querySelector('.sidebar-nav') : null;
    const topbarToggle = document.getElementById('topbarToggle');
    const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const sidebarScrollKey = 'tima_ade_sidebar_scroll_top';
    const aiShell = document.querySelector('[data-ai-shell]');
    const aiInput = document.getElementById('tima-ai-input');
    const aiResponse = document.querySelector('[data-ai-response]');
    const aiStatus = document.querySelector('[data-ai-status]');
    const aiMic = document.querySelector('[data-ai-mic]');
    const aiStop = document.querySelector('[data-ai-stop]');
    const aiForm = document.querySelector('[data-ai-form]');
    const aiModes = document.querySelectorAll('[data-ai-mode]');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const aiSettingsBtn = document.querySelector('[data-ai-settings]');
    const aiSettingsPanel = document.querySelector('[data-ai-settings-panel]');
    const aiVoiceSelect = document.querySelector('[data-ai-voice-select]');
    const aiRateSlider = document.querySelector('[data-ai-rate]');
    const aiRateDisplay = document.querySelector('[data-ai-rate-display]');
    const aiPitchSlider = document.querySelector('[data-ai-pitch]');
    const aiPitchDisplay = document.querySelector('[data-ai-pitch-display]');
    const aiVolumeSlider = document.querySelector('[data-ai-volume]');
    const aiVolumeDisplay = document.querySelector('[data-ai-volume-display]');
    const aiTestVoiceBtn = document.querySelector('[data-ai-test-voice]');
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    let aiMode = 'text';
    let recognition = null;
    let navigationPending = false;
    let continuousVoice = false;
    let processingVoiceCommand = false;
    let restartListenTimer = null;
    let selectedVoice = null;
    let availableVoices = [];
    const voicePrefKey = 'tima_ai_voice_preference';
    const voiceSettingsKey = 'tima_ai_voice_settings';
    const continuousVoiceKey = 'tima_ai_continuous_voice';
    const defaultVoiceSettings = {
        rate: 1.0,
        pitch: 1.0,
        volume: 1.0
    };

    document.addEventListener('shown.bs.modal', () => {
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    });

    document.addEventListener('hidden.bs.modal', () => {
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    });

    const normalizeModalBody = () => {
        if (document.body.classList.contains('modal-open')) {
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        }
    };

    new MutationObserver(normalizeModalBody).observe(document.body, {
        attributes: true,
        attributeFilter: ['class', 'style']
    });

    function setAiOpen(isOpen) {
        if (!aiShell) return;
        aiShell.hidden = !isOpen;
        document.body.classList.toggle('tima-ai-open', isOpen);
        if (isOpen) aiInput?.focus();
    }

    document.querySelectorAll('[data-ai-open]').forEach(button => {
        button.addEventListener('click', () => setAiOpen(true));
    });
    document.querySelectorAll('[data-ai-close]').forEach(button => {
        button.addEventListener('click', () => setAiOpen(false));
    });
    document.querySelectorAll('[data-ai-suggestion]').forEach(button => {
        button.addEventListener('click', () => {
            setAiOpen(true);
            if (aiInput) aiInput.value = button.dataset.aiSuggestion || '';
            aiInput?.focus();
        });
    });

    function setAiStatus(text) {
        if (aiStatus) aiStatus.textContent = text;
    }

    function getVoiceSettings() {
        const stored = localStorage.getItem(voiceSettingsKey);
        return stored ? JSON.parse(stored) : defaultVoiceSettings;
    }

    function saveVoiceSettings(settings) {
        localStorage.setItem(voiceSettingsKey, JSON.stringify(settings));
    }

    function detectAvailableVoices() {
        return new Promise((resolve) => {
            const voices = window.speechSynthesis?.getVoices() || [];
            if (voices.length > 0) {
                resolve(voices);
                return;
            }
            if (!window.speechSynthesis) {
                resolve([]);
                return;
            }
            const handleVoicesChanged = () => {
                window.speechSynthesis.removeEventListener('voiceschanged', handleVoicesChanged);
                resolve(window.speechSynthesis.getVoices() || []);
            };
            window.speechSynthesis.addEventListener('voiceschanged', handleVoicesChanged);
            setTimeout(() => {
                window.speechSynthesis.removeEventListener('voiceschanged', handleVoicesChanged);
                resolve(window.speechSynthesis.getVoices() || []);
            }, 2000);
        });
    }

    function selectBestVoice(voices) {
        if (!voices || voices.length === 0) return null;
        const englishVoices = voices.filter(v => v.lang.startsWith('en'));
        if (englishVoices.length === 0) return voices[0];
        const naturalVoices = englishVoices.filter(v => 
            v.name.toLowerCase().includes('google') ||
            v.name.toLowerCase().includes('natural') ||
            v.name.toLowerCase().includes('neural') ||
            v.name.toLowerCase().includes('samantha') ||
            v.name.toLowerCase().includes('moira') ||
            v.name.toLowerCase().includes('victoria') ||
            v.name.toLowerCase().includes('karen') ||
            v.name.toLowerCase().includes('fiona') ||
            v.name.toLowerCase().includes('marcus') ||
            v.name.toLowerCase().includes('zira') ||
            v.name.toLowerCase().includes('guy') ||
            v.name.toLowerCase().includes('aria')
        );
        if (naturalVoices.length > 0) return naturalVoices[0];
        return englishVoices[0];
    }

    async function initializeVoice() {
        if (!window.speechSynthesis) return;
        try {
            availableVoices = await detectAvailableVoices();
            const savedPref = localStorage.getItem(voicePrefKey);
            if (savedPref && availableVoices.some(v => v.name === savedPref)) {
                selectedVoice = availableVoices.find(v => v.name === savedPref);
            } else {
                selectedVoice = selectBestVoice(availableVoices);
                if (selectedVoice) {
                    localStorage.setItem(voicePrefKey, selectedVoice.name);
                }
            }
            
            // Restore continuous voice mode if it was active before page reload
            if (sessionStorage.getItem(continuousVoiceKey) === 'true') {
                continuousVoice = true;
                if (aiStop) aiStop.hidden = false;
                if (aiInput) aiInput.hidden = true;
                if (aiMic) aiMic.hidden = false;
                startRecognition();
            }
        } catch (error) {
            console.error('Voice initialization error:', error);
        }
    }

    function setVoice(voiceName) {
        if (!voiceName || !availableVoices) return;
        const voice = availableVoices.find(v => v.name === voiceName);
        if (voice) {
            selectedVoice = voice;
            localStorage.setItem(voicePrefKey, voiceName);
        }
    }

    function speakAiResponse(text, onComplete = null) {
        let completed = false;
        const complete = () => {
            if (completed) return;
            completed = true;
            onComplete?.();
        };
        if (!text || !('speechSynthesis' in window)) {
            complete();
            if (continuousVoice && !navigationPending) {
                scheduleListeningRestart();
            } else {
                setAiStatus('Ready');
            }
            return;
        }
        try {
            const utterance = new SpeechSynthesisUtterance(text);
            const settings = getVoiceSettings();
            utterance.rate = settings.rate;
            utterance.pitch = settings.pitch;
            utterance.volume = settings.volume;
            if (selectedVoice) {
                utterance.voice = selectedVoice;
            }
            utterance.lang = 'en-US';
            utterance.onstart = () => {
                setAiStatus('Speaking');
            };
            utterance.onend = () => {
                complete();
                if (continuousVoice && !navigationPending) {
                    scheduleListeningRestart();
                } else {
                    setAiStatus('Ready');
                }
            };
            utterance.onerror = () => {
                complete();
                if (continuousVoice && !navigationPending) {
                    scheduleListeningRestart();
                } else {
                    setAiStatus('Ready');
                }
            };
            window.speechSynthesis.speak(utterance);
        } catch (error) {
            complete();
            if (continuousVoice && !navigationPending) {
                scheduleListeningRestart();
            } else {
                setAiStatus('Ready');
            }
        }
    }

    function scheduleListeningRestart() {
        if (!continuousVoice) return;
        window.clearTimeout(restartListenTimer);
        restartListenTimer = window.setTimeout(() => {
            if (continuousVoice && !processingVoiceCommand && !recognition) {
                startRecognition();
            }
        }, 100);
    }

    async function askTimaAi(message, shouldSpeak = false) {
        if (!message || !aiResponse) return;
        aiResponse.hidden = false;
        aiResponse.textContent = 'Tima AI is processing your request...';
        setAiStatus('Processing');
        try {
            const response = await fetch('/tima-ai/respond', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken || '' },
                body: JSON.stringify({ message })
            });
            const payload = await response.json();
            if (!response.ok) throw new Error(payload.message || 'Unable to get a response.');
            aiResponse.textContent = payload.response;
            if (payload.action?.type === 'navigate' && payload.action.url) {
                navigationPending = true;
                setAiStatus('Opening ' + (payload.action.label || 'page'));
                const navigate = () => {
                    processingVoiceCommand = false;
                    if (navigationPending) window.location.assign(payload.action.url);
                };
                if (shouldSpeak) void speakAiResponse(payload.response, navigate);
                else window.setTimeout(navigate, 350);
            } else {
                setAiStatus(shouldSpeak ? 'Responding' : 'Response ready');
                if (shouldSpeak) {
                    void speakAiResponse(payload.response, () => {
                        processingVoiceCommand = false;
                    });
                }
                else if (continuousVoice) scheduleListeningRestart();
            }
        } catch (error) {
            aiResponse.textContent = error.message || 'Tima AI could not process that request.';
            setAiStatus('Unable to respond');
            processingVoiceCommand = false;
            if (continuousVoice) scheduleListeningRestart();
        }
    }

    function stopListening() {
        continuousVoice = false;        sessionStorage.removeItem(continuousVoiceKey);        navigationPending = false;
        window.clearTimeout(restartListenTimer);
        recognition?.stop();
        recognition = null;
        processingVoiceCommand = false;
        if ('speechSynthesis' in window) window.speechSynthesis.cancel();
        aiMic?.classList.remove('listening');
        if (aiMic) aiMic.setAttribute('aria-label', 'Start voice input');
        if (aiStop) aiStop.hidden = true;
        setAiStatus('Stopped');
    }

    function startRecognition() {
        if (!SpeechRecognition) {
            setAiStatus('Voice input is not supported in this browser.');
            return;
        }
        recognition = new SpeechRecognition();
        recognition.lang = document.documentElement.lang || 'en-US';
        recognition.continuous = false;
        recognition.interimResults = false;
        recognition.maxAlternatives = 1;
        recognition.onstart = () => {
            aiMic?.classList.add('listening');
            aiMic?.setAttribute('aria-label', 'Stop voice input');
            setAiStatus('Listening');
        };
        recognition.onresult = event => {
            const transcript = event.results[0][0].transcript.trim();
            if (aiInput) aiInput.value = transcript;
            if (/\b(stop listening|end conversation|end voice conversation|please stop|stop)\b[.! ]*$/i.test(transcript)) {
                stopListening();
                return;
            }
            processingVoiceCommand = true;
            recognition.stop();
            recognition = null;
            askTimaAi(transcript, true);
        };
        recognition.onerror = event => {
            setAiStatus(event.error === 'not-allowed' ? 'Microphone permission was denied.' : 'Voice input could not be started.');
        };
        recognition.onend = () => {
            recognition = null;
            aiMic?.classList.remove('listening');
            aiMic?.setAttribute('aria-label', 'Start voice input');
            if (continuousVoice) {
                scheduleListeningRestart();
            }
        };
        try { recognition.start(); } catch (error) {
            recognition = null;
            setAiStatus('Voice input could not be started.');
        }
    }

    function startListening() {
        if (!SpeechRecognition) {
            setAiStatus('Voice input is not supported in this browser.');
            return;
        }
        continuousVoice = true;
        sessionStorage.setItem(continuousVoiceKey, 'true');
        if (aiStop) aiStop.hidden = false;
        setAiStatus('Ready');
        startRecognition();
    }

    aiModes.forEach(button => button.addEventListener('click', () => {
        aiMode = button.dataset.aiMode || 'text';
        aiModes.forEach(modeButton => {
            const active = modeButton === button;
            modeButton.classList.toggle('active', active);
            modeButton.setAttribute('aria-pressed', active ? 'true' : 'false');
        });
        if (aiInput) aiInput.hidden = aiMode === 'voice';
        if (aiMic) aiMic.hidden = aiMode !== 'voice';
        if (aiMode !== 'voice') stopListening();
        setAiStatus(aiMode === 'voice' ? (SpeechRecognition ? 'Ready' : 'Voice input is not supported in this browser.') : 'Ready');
    }));
    aiMic?.addEventListener('click', startListening);
    aiStop?.addEventListener('click', stopListening);
    aiForm?.addEventListener('submit', event => {
        event.preventDefault();
        const message = aiInput?.value.trim();
        if (!message) return;
        askTimaAi(message, false);
        if (aiInput) aiInput.value = '';
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && aiShell && !aiShell.hidden) setAiOpen(false);
    });

    function restoreSidebarScroll() {
        if (!sidebar) return;

        const savedScrollTop = Number.parseInt(sessionStorage.getItem(sidebarScrollKey) || '0', 10);
        let targetScrollTop = Number.isFinite(savedScrollTop) ? savedScrollTop : 0;

        const hasSavedPosition = sessionStorage.getItem(sidebarScrollKey) !== null;
        const activeLink = sidebar.querySelector('.nav-link.active');
        if (!hasSavedPosition && activeLink && sidebarNav) {
            const sidebarBounds = sidebar.getBoundingClientRect();
            const linkBounds = activeLink.getBoundingClientRect();
            const currentScrollTop = sidebar.scrollTop;
            const linkTop = linkBounds.top - sidebarBounds.top + currentScrollTop;
            const linkBottom = linkBounds.bottom - sidebarBounds.top + currentScrollTop;
            const viewportHeight = sidebar.clientHeight;

            if (linkTop < targetScrollTop) {
                targetScrollTop = linkTop;
            } else if (linkBottom > targetScrollTop + viewportHeight) {
                targetScrollTop = linkBottom - viewportHeight;
            }
        }

        const maxScrollTop = Math.max(0, sidebar.scrollHeight - sidebar.clientHeight);
        sidebar.scrollTop = Math.max(0, Math.min(targetScrollTop, maxScrollTop));
    }

    function restoreSidebarScrollIfNeeded() {
        if (!sidebar) return;
        if (sidebar.dataset.sidebarScrollRestored === 'true') return;

        const previousBehavior = sidebar.style.scrollBehavior;
        sidebar.style.scrollBehavior = 'auto';
        restoreSidebarScroll();
        sidebar.style.scrollBehavior = previousBehavior;
        sidebar.dataset.sidebarScrollRestored = 'true';
    }

    // Restore desktop preference before measuring the sidebar for scroll restoration.
    if (window.innerWidth >= 992 && localStorage.getItem('moon_sidebar_collapsed') === '1') {
        document.body.classList.add('sidebar-collapsed');
    }
    document.documentElement.classList.remove('sidebar-collapsed-preload');

    if (sidebar) {
        sidebar.addEventListener('scroll', function() {
            sessionStorage.setItem(sidebarScrollKey, String(sidebar.scrollTop));
        }, { passive: true });

        window.addEventListener('pagehide', function() {
            sessionStorage.setItem(sidebarScrollKey, String(sidebar.scrollTop));
        }, { passive: true });

        sidebar.addEventListener('click', function(event) {
            const link = event.target.closest('a.nav-link');
            if (!link || link.target === '_blank' || link.origin !== window.location.origin) return;
            sessionStorage.setItem(sidebarScrollKey, String(sidebar.scrollTop));
            closeMobileSidebar();
        });

        sidebar.addEventListener('pointerdown', function(event) {
            const link = event.target.closest('a.nav-link');
            if (!link || link.target === '_blank' || link.origin !== window.location.origin) return;
            sessionStorage.setItem(sidebarScrollKey, String(sidebar.scrollTop));
        }, { passive: true });

        sidebar.addEventListener('keydown', function(event) {
            if (event.key !== 'Enter' && event.key !== ' ') return;
            const link = event.target.closest('a.nav-link');
            if (!link || link.target === '_blank' || link.origin !== window.location.origin) return;
            sessionStorage.setItem(sidebarScrollKey, String(sidebar.scrollTop));
        }, { passive: true });

        restoreSidebarScrollIfNeeded();
    }

    function toggleSidebar() {
        if (window.innerWidth < 992) {
            // Mobile behavior
            if (sidebar) {
                sidebar.classList.toggle('show');
                if (sidebarOverlay) {
                    sidebarOverlay.style.display = sidebar.classList.contains('show') ? 'block' : 'none';
                }
            }
        } else {
            // Desktop collapsed behavior
            document.body.classList.toggle('sidebar-collapsed');
            const isCollapsed = document.body.classList.contains('sidebar-collapsed');
            localStorage.setItem('moon_sidebar_collapsed', isCollapsed ? '1' : '0');
        }
    }

    function closeMobileSidebar() {
        if (window.innerWidth >= 992 || !sidebar) return;
        sidebar.classList.remove('show');
        if (sidebarOverlay) sidebarOverlay.style.display = 'none';
    }

    if (topbarToggle) {
        topbarToggle.addEventListener('click', toggleSidebar);
    }
    if (sidebarToggleBtn) {
        sidebarToggleBtn.addEventListener('click', toggleSidebar);
    }
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            closeMobileSidebar();
        });
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') closeMobileSidebar();
    });

    window.addEventListener('resize', closeMobileSidebar, { passive: true });

    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            try {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            } catch (e) {}
        }, 5000);
    });

    // Initialize voice system for natural speech
    initializeVoice().catch(error => console.warn('Voice initialization warning:', error));

    // Voice settings UI
    function populateVoiceSelector() {
        if (!aiVoiceSelect || !availableVoices || availableVoices.length === 0) return;
        const currentVoice = localStorage.getItem(voicePrefKey);
        const options = availableVoices
            .filter(v => v.lang.startsWith('en'))
            .map(v => `<option value="${v.name}" ${v.name === currentVoice ? 'selected' : ''}>${v.name}</option>`)
            .join('');
        if (options) {
            aiVoiceSelect.innerHTML = options;
        }
    }

    function updateVoiceSettingsDisplay() {
        const settings = getVoiceSettings();
        if (aiRateSlider) aiRateSlider.value = settings.rate;
        if (aiRateDisplay) aiRateDisplay.textContent = settings.rate.toFixed(1) + 'x';
        if (aiPitchSlider) aiPitchSlider.value = settings.pitch;
        if (aiPitchDisplay) aiPitchDisplay.textContent = settings.pitch.toFixed(1);
        if (aiVolumeSlider) aiVolumeSlider.value = settings.volume;
        if (aiVolumeDisplay) aiVolumeDisplay.textContent = Math.round(settings.volume * 100) + '%';
        populateVoiceSelector();
    }

    function testVoice() {
        const testText = "Hello! This is Tima AI speaking. How can I help you today?";
        speakAiResponse(testText);
    }

    if (aiSettingsBtn) {
        aiSettingsBtn.addEventListener('click', () => {
            if (aiSettingsPanel) {
                aiSettingsPanel.hidden = !aiSettingsPanel.hidden;
                if (!aiSettingsPanel.hidden) {
                    updateVoiceSettingsDisplay();
                }
            }
        });
    }

    if (aiVoiceSelect) {
        aiVoiceSelect.addEventListener('change', (e) => {
            setVoice(e.target.value);
        });
    }

    if (aiRateSlider) {
        aiRateSlider.addEventListener('input', (e) => {
            const settings = getVoiceSettings();
            settings.rate = parseFloat(e.target.value);
            saveVoiceSettings(settings);
            if (aiRateDisplay) aiRateDisplay.textContent = settings.rate.toFixed(1) + 'x';
        });
    }

    if (aiPitchSlider) {
        aiPitchSlider.addEventListener('input', (e) => {
            const settings = getVoiceSettings();
            settings.pitch = parseFloat(e.target.value);
            saveVoiceSettings(settings);
            if (aiPitchDisplay) aiPitchDisplay.textContent = settings.pitch.toFixed(1);
        });
    }

    if (aiVolumeSlider) {
        aiVolumeSlider.addEventListener('input', (e) => {
            const settings = getVoiceSettings();
            settings.volume = parseFloat(e.target.value);
            saveVoiceSettings(settings);
            if (aiVolumeDisplay) aiVolumeDisplay.textContent = Math.round(settings.volume * 100) + '%';
        });
    }

    if (aiTestVoiceBtn) {
        aiTestVoiceBtn.addEventListener('click', () => {
            testVoice();
        });
    }
})();
