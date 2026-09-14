<div class="tima-ai-shell" data-ai-shell hidden>
    <div class="tima-ai-backdrop" data-ai-close></div>
    <aside class="tima-ai-panel" role="dialog" aria-modal="true" aria-labelledby="tima-ai-title">
        <div class="tima-ai-panel-header">
            <div class="d-flex align-items-center gap-2">
                <span class="tima-ai-panel-mark" aria-hidden="true"><i class="bi bi-stars"></i></span>
                <div><span class="tima-ai-eyebrow">Tima AI</span><h2 id="tima-ai-title">How can I help today?</h2></div>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <button type="button" class="tima-ai-settings" data-ai-settings title="Voice settings" aria-label="Voice settings" style="background: none; border: none; color: inherit; cursor: pointer; font-size: 1.2em;"><i class="bi bi-gear"></i></button>
                <button type="button" class="tima-ai-close" data-ai-close aria-label="Close Tima AI"><i class="bi bi-x-lg"></i></button>
            </div>
        </div>
        <div class="tima-ai-panel-body">
            <p class="tima-ai-context"><i class="bi bi-shield-check me-1" aria-hidden="true"></i> Answers use your role and existing portal access.</p>
            <div class="tima-ai-message tima-ai-message-system">Tima AI is ready to help you find your way around the university portal.</div>
            <div class="tima-ai-mode" role="group" aria-label="Tima AI input mode">
                <button type="button" class="active" data-ai-mode="text" aria-pressed="true"><i class="bi bi-keyboard me-1"></i> Type</button>
                <button type="button" data-ai-mode="voice" aria-pressed="false"><i class="bi bi-mic me-1"></i> Talk</button>
            </div>
            <div class="tima-ai-suggestions tima-ai-panel-suggestions" aria-label="Suggested questions">
                <button type="button" data-ai-suggestion="What should I pay attention to today?">Today's priorities</button>
                <button type="button" data-ai-suggestion="Summarize the important updates on this page.">Important updates</button>
                <button type="button" data-ai-suggestion="Help me find the information I need.">Find information</button>
            </div>
            <div class="tima-ai-response" data-ai-response role="status" aria-live="polite" hidden></div>
            <div class="tima-ai-status" data-ai-status role="status" aria-live="polite">Ready</div>
            <button type="button" class="tima-ai-stop" data-ai-stop hidden><i class="bi bi-stop-circle me-1"></i> Stop</button>
        </div>
        <form class="tima-ai-form" data-ai-form>
            <label class="visually-hidden" for="tima-ai-input">Ask Tima AI</label>
            <input id="tima-ai-input" type="text" maxlength="500" placeholder="Ask about this portal page..." autocomplete="off">
            <button type="button" class="tima-ai-mic" data-ai-mic aria-label="Start voice input" hidden><i class="bi bi-mic"></i></button>
            <button type="submit" aria-label="Send question"><i class="bi bi-arrow-up"></i></button>
        </form>
        <div class="tima-ai-settings-panel" data-ai-settings-panel hidden style="padding: 1rem; border-top: 1px solid #e0e0e0; background: #fafafa; border-radius: 0 0 8px 8px;">
            <div style="margin-bottom: 1rem;">
                <label for="tima-ai-voice-select" style="display: block; font-weight: 500; margin-bottom: 0.5rem; font-size: 0.9em;">Voice</label>
                <select id="tima-ai-voice-select" data-ai-voice-select style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px; font-size: 0.9em;">
                    <option value="">Loading voices...</option>
                </select>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label for="tima-ai-rate" style="display: block; font-weight: 500; margin-bottom: 0.5rem; font-size: 0.9em;">Speed</label>
                    <input type="range" id="tima-ai-rate" data-ai-rate min="0.5" max="2" step="0.1" value="1" style="width: 100%;">
                    <span data-ai-rate-display style="font-size: 0.8em; color: #666;">1.0x</span>
                </div>
                <div>
                    <label for="tima-ai-pitch" style="display: block; font-weight: 500; margin-bottom: 0.5rem; font-size: 0.9em;">Pitch</label>
                    <input type="range" id="tima-ai-pitch" data-ai-pitch min="0.5" max="2" step="0.1" value="1" style="width: 100%;">
                    <span data-ai-pitch-display style="font-size: 0.8em; color: #666;">1.0</span>
                </div>
            </div>
            <div style="margin-bottom: 1rem;">
                <label for="tima-ai-volume" style="display: block; font-weight: 500; margin-bottom: 0.5rem; font-size: 0.9em;">Volume</label>
                <input type="range" id="tima-ai-volume" data-ai-volume min="0" max="1" step="0.1" value="1" style="width: 100%;">
                <span data-ai-volume-display style="font-size: 0.8em; color: #666;">100%</span>
            </div>
            <button type="button" data-ai-test-voice style="width: 100%; padding: 0.5rem; background: #2196F3; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.9em;">Test Voice</button>
        </div>
        <p class="tima-ai-disclaimer">Tima AI only answers from information your existing role can access.</p>
    </aside>
</div>
<button type="button" class="tima-ai-fab" data-ai-open aria-label="Open Tima AI"><i class="bi bi-stars" aria-hidden="true"></i><span>Tima AI</span></button>
