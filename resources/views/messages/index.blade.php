@extends('layouts.app')

@section('title', 'Tima Chat')
@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Tima Chat</li>
@endsection

@section('content')
<div class="messages-page" id="messagesApp" data-current-user="{{ auth()->id() }}" data-is-student="{{ auth()->user()->isStudent() ? '1' : '0' }}">
    <div id="messagesAlert" class="messages-alert" role="alert" hidden></div>

    <div class="messages-layout">
        <aside class="messages-sidebar" aria-label="Conversations and contacts">
            <div class="messages-sidebar-header">
                <div>
                    <span class="messages-section-label">Inbox</span>
                    <h2>Conversations</h2>
                </div>
                <button class="messages-icon-button" id="newMessageButton" type="button" title="Find a contact" aria-label="Find a contact">
                    <i class="bi bi-pencil-square" aria-hidden="true"></i>
                </button>
            </div>
            <label class="messages-search">
                <i class="bi bi-search" aria-hidden="true"></i>
                <span class="visually-hidden">Search contacts</span>
                <input id="contactSearch" type="search" placeholder="Search contacts" autocomplete="off">
            </label>
            <div id="contactResults" class="messages-contact-results" hidden></div>
            <div id="conversationList" class="messages-conversation-list" aria-live="polite">
                <div class="messages-loading"><span class="messages-spinner"></span> Loading conversations</div>
            </div>
            <div class="messages-sidebar-footer">
                <button type="button" class="messages-secondary-button" id="friendsButton" hidden><i class="bi bi-people" aria-hidden="true"></i> Friends & requests</button>
            </div>
        </aside>

        <section class="messages-chat" aria-label="Private chat">
            <div id="chatEmpty" class="messages-empty-state">
                <div class="messages-empty-icon"><i class="bi bi-chat-square-heart" aria-hidden="true"></i></div>
                <h2>Your private inbox</h2>
                <p>Select a conversation or find an authorized contact to start a secure chat.</p>
            </div>
            <div id="chatPanel" class="messages-chat-panel" hidden>
                <header class="messages-chat-header">
                    <button type="button" class="messages-back-button" id="chatBackButton" aria-label="Back to conversations"><i class="bi bi-arrow-left" aria-hidden="true"></i></button>
                    <div class="messages-chat-person">
                        <img id="chatAvatar" src="" alt="" class="messages-avatar">
                        <div><h2 id="chatName"></h2></div>
                    </div>
                </header>
                <div id="chatMessages" class="messages-chat-body" aria-live="polite" aria-label="Message history"></div>
                <form id="messageForm" class="messages-composer" novalidate>
                    <label for="messageInput" class="visually-hidden">Write a message</label>
                    <div class="messages-composer-field">
                        <textarea id="messageInput" rows="1" maxlength="5000" placeholder="Write a private message..." required></textarea>
                    </div>
                    <div class="messages-composer-actions">
                        <button class="messages-send-button messages-action-button" type="button" id="composerActionButton" aria-label="Record a voice note"><i class="bi bi-mic" aria-hidden="true"></i><span>Voice note</span></button>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <section id="friendsPanel" class="messages-friends-panel" hidden aria-label="Friends and friend requests">
        <div class="messages-friends-heading"><div><span class="messages-section-label">Student network</span><h2>Friends & requests</h2></div><button class="messages-icon-button" id="closeFriendsButton" type="button" aria-label="Close friends panel"><i class="bi bi-x-lg" aria-hidden="true"></i></button></div>
        <label class="messages-search messages-friend-search"><i class="bi bi-search" aria-hidden="true"></i><span class="visually-hidden">Find a student</span><input id="friendSearch" type="search" placeholder="Find a student to add" autocomplete="off"></label>
        <div id="friendSearchResults" class="messages-contact-results" hidden></div>
        <div class="messages-friends-columns"><div><h3>Requests</h3><div id="requestList"></div></div><div><h3>Accepted friends</h3><div id="friendList"></div></div></div>
    </section>
</div>
@endsection

@push('styles')
<style>
:root {
    --chat-shell-bg: #f3f6fb;
    --chat-panel-bg: #ffffff;
    --chat-sidebar-bg: #f8fafc;
    --chat-border: rgba(15, 23, 42, .08);
    --chat-soft: rgba(15, 23, 42, .04);
    --chat-shadow: 0 20px 55px rgba(15, 23, 42, .10);
    --chat-accent: #25d366;
    --chat-accent-strong: #128c7e;
    --chat-accent-deep: #075e54;
    --chat-text: #111827;
    --chat-muted: #64748b;
    --chat-unread: #db2777;
    --chat-bubble-out: #dcf8c6;
    --chat-bubble-in: #f1f5f9;
    --chat-send-glow: rgba(37, 211, 102, .25);
}

.main-wrapper:has(.messages-page) {
    height: 100vh;
    max-height: 100vh;
}

.main-wrapper:has(.messages-page) .page-content {
    padding: .75rem .75rem 0;
    overflow: hidden;
    min-height: 0;
    display: flex;
    flex-direction: column;
}

.messages-page {
    --messages-page-height: calc(100vh - 11.5rem);
    width: 100%;
    max-width: 1600px;
    margin: 0 auto;
    padding: 0 0 0.5rem;
    box-sizing: border-box;
    height: var(--messages-page-height);
    min-height: 0;
    display: flex;
    flex-direction: column;
    flex: 1 1 auto;
}

.messages-heading {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 1rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.messages-heading h1,
.messages-heading p,
.messages-sidebar h2,
.messages-chat h2,
.messages-friends-panel h2,
.messages-friends-panel h3 {
    margin: 0;
}

.messages-heading h1 {
    color: var(--navy-900);
    font-size: clamp(1.8rem, 2.8vw, 2.45rem);
    font-weight: 800;
    letter-spacing: -.04em;
}

.messages-heading p {
    margin-top: .4rem;
    color: var(--text-muted);
    font-size: .9rem;
    max-width: 48rem;
}

.messages-kicker,
.messages-section-label {
    display: block;
    color: var(--crimson-700);
    font-size: .72rem;
    font-weight: 800;
    letter-spacing: .14em;
    text-transform: uppercase;
    margin-bottom: .4rem;
}

.messages-transport {
    font-size: .75rem;
    color: var(--text-muted);
    white-space: nowrap;
}

.messages-layout {
    display: grid;
    grid-template-columns: minmax(260px, 340px) minmax(0, 1fr);
    flex: 1 1 auto;
    height: 100%;
    min-height: 0;
    max-height: 100%;
    overflow: hidden;
    border-radius: 24px;
    background: var(--chat-panel-bg);
    border: 1px solid var(--chat-border);
    box-shadow: var(--chat-shadow);
}

.messages-sidebar,
.messages-chat,
.messages-chat-panel,
.messages-sidebar > *,
.messages-chat > * {
    min-height: 0;
}

.messages-sidebar {
    display: flex;
    flex-direction: column;
    min-width: 0;
    height: 100%;
    border-right: 1px solid var(--chat-border);
    background: linear-gradient(180deg, #fbfdff, #f3f5fb);
}

.messages-sidebar-header,
.messages-chat-header,
.messages-friends-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.messages-sidebar-header {
    padding: 1rem 1rem .9rem;
    flex-shrink: 0;
}

.messages-sidebar h2 {
    font-size: 1.15rem;
    color: var(--navy-900);
    font-weight: 800;
}

.messages-icon-button,
.messages-secondary-button,
.messages-action-button,
.messages-send-button,
.messages-back-button {
    transition: transform .18s ease, box-shadow .18s ease, background-color .18s ease, border-color .18s ease, opacity .18s ease;
}

.messages-icon-button:hover,
.messages-secondary-button:hover,
.messages-action-button:hover,
.messages-send-button:hover,
.messages-back-button:hover {
    transform: translateY(-1px);
}

.messages-icon-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2.25rem;
    height: 2.25rem;
    border: 1px solid var(--chat-border);
    background: #fff;
    border-radius: 12px;
    color: var(--chat-text);
    box-shadow: 0 6px 16px rgba(15,23,42,.03);
}

.messages-search {
    display: flex;
    align-items: center;
    gap: .7rem;
    margin: 0 1rem .75rem;
    padding: .74rem .8rem;
    border: 1px solid var(--chat-border);
    border-radius: 16px;
    background: #fff;
    color: var(--chat-muted);
    box-shadow: inset 0 1px 0 rgba(255,255,255,.8);
}

.messages-search input {
    width: 100%;
    border: 0;
    outline: 0;
    background: transparent;
    font-size: .86rem;
    color: var(--chat-text);
}

.messages-search input::placeholder {
    color: var(--chat-muted);
}

.messages-search:focus-within {
    border-color: rgba(15, 118, 110, .42);
    box-shadow: 0 0 0 3px rgba(15,118,110,.08);
}

.messages-conversation-list,
.messages-contact-results,
.messages-chat-body {
    scrollbar-width: thin;
    scrollbar-color: rgba(15,23,42,.22) transparent;
}

.messages-conversation-list {
    flex: 1 1 auto;
    overflow-y: auto;
    overflow-x: hidden;
    padding: .3rem .5rem .75rem;
    min-height: 0;
}

.messages-conversation {
    display: flex;
    align-items: center;
    gap: .8rem;
    width: 100%;
    text-align: left;
    padding: .7rem .7rem;
    border: 1px solid transparent;
    border-radius: 16px;
    background: transparent;
    color: var(--chat-text);
    margin: 0 0 .35rem;
    cursor: pointer;
}

.messages-conversation:hover {
    background: rgba(15,23,42,.02);
    border-color: rgba(15,23,42,.04);
}

.messages-conversation.active {
    background: linear-gradient(180deg, rgba(15,118,110,.08), rgba(15,118,110,.03));
    border-color: rgba(15,118,110,.18);
}

.messages-avatar {
    width: 2.6rem;
    height: 2.6rem;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
    background: linear-gradient(135deg,#dbeafe,#bbf7d0);
    border: 2px solid rgba(255,255,255,.8);
}

.messages-conversation-main {
    display: flex;
    flex-direction: column;
    min-width: 0;
    flex: 1;
}

.messages-conversation-main strong {
    font-size: .92rem;
    line-height: 1.25;
    color: var(--chat-text);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.messages-conversation-main small,
.messages-time {
    font-size: .72rem;
    color: var(--chat-muted);
}

.messages-conversation-main small {
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
}

.messages-conversation-meta {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: .38rem;
    min-width: 2.7rem;
}

.messages-unread {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 1.25rem;
    height: 1.25rem;
    border-radius: 999px;
    background: var(--chat-unread);
    color: #fff;
    font-size: .65rem;
    font-weight: 700;
    padding: 0 .32rem;
}

.messages-time {
    font-size: .68rem;
    line-height: 1;
    color: var(--chat-muted);
}

.messages-contact-results {
    padding: .4rem .5rem .75rem;
    max-height: 240px;
    overflow: auto;
    border-bottom: 1px solid var(--chat-border);
    background: #fff;
}

.messages-contact {
    display: flex;
    align-items: center;
    gap: .8rem;
    width: 100%;
    text-align: left;
    padding: .65rem .7rem;
    border: 1px solid transparent;
    border-radius: 14px;
    background: transparent;
    color: var(--chat-text);
    cursor: pointer;
}

.messages-contact:hover {
    background: rgba(15,23,42,.02);
    border-color: rgba(15,23,42,.04);
}

.messages-contact span {
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.messages-contact strong {
    font-size: .9rem;
    line-height: 1.3;
    color: var(--chat-text);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.messages-contact small {
    font-size: .72rem;
    color: var(--chat-muted);
}

.messages-sidebar-footer {
    padding: .65rem 1rem 1rem;
}

.messages-secondary-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .48rem;
    padding: .72rem .9rem;
    border-radius: 12px;
    border: 1px solid var(--chat-border);
    background: #fff;
    color: var(--chat-text);
    font-weight: 700;
    font-size: .8rem;
}

.messages-chat {
    position: relative;
    display: flex;
    flex-direction: column;
    min-width: 0;
    height: 100%;
    background: #efeae2;
}

.messages-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 1.5rem;
    color: var(--chat-muted);
    height: 100%;
}

.messages-empty-state h2 {
    font-size: 1.5rem;
    color: var(--navy-900);
    font-weight: 800;
    margin-bottom: .5rem;
}

.messages-empty-state p {
    max-width: 34rem;
    line-height: 1.6;
}

.messages-empty-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 5rem;
    height: 5rem;
    border-radius: 20px;
    background: linear-gradient(135deg, rgba(15,118,110,.12), rgba(59,130,246,.08));
    color: var(--chat-accent);
    font-size: 2rem;
    box-shadow: 0 10px 30px rgba(15,118,110,.08);
    margin-bottom: 1rem;
}

.messages-chat-panel {
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 0;
}

.messages-chat-header {
    padding: .85rem .95rem;
    border-bottom: 1px solid var(--chat-border);
    background: #075e54;
    color: #fff;
    border-bottom-color: rgba(0,0,0,.12);
}

.messages-chat-header .messages-chat-person h2,
.messages-chat-header .messages-chat-person span {
    color: #fff;
}

.messages-back-button {
    display: none;
    align-items: center;
    justify-content: center;
    width: 2.35rem;
    height: 2.35rem;
    border-radius: 12px;
    border: 1px solid var(--chat-border);
    background: rgba(255,255,255,.14);
    color: #fff;
    border-color: rgba(255,255,255,.22);
}

.messages-chat-person {
    display: flex;
    align-items: center;
    gap: .8rem;
    min-width: 0;
}

.messages-chat-person h2 {
    font-size: 1.05rem;
    color: var(--navy-900);
    font-weight: 800;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.messages-chat-person span {
    font-size: .72rem;
    color: var(--chat-muted);
}

.messages-chat-body {
    display: flex;
    flex-direction: column;
    gap: .7rem;
    flex: 1 1 auto;
    overflow-y: auto;
    overflow-x: hidden;
    padding: .9rem .8rem .9rem;
    min-height: 0;
    background-color: #efeae2;
    background-image:
        radial-gradient(circle at 12px 12px, rgba(255,255,255,.22) 0 1px, transparent 1.5px),
        radial-gradient(circle at 42px 32px, rgba(0,0,0,.035) 0 1px, transparent 1.5px);
    background-size: 54px 54px;
}

.messages-no-results {
    padding: 1rem .8rem;
    color: var(--chat-muted);
    font-size: .84rem;
    text-align: center;
    background: transparent;
}

.messages-bubble {
    max-width: min(72%, 26rem);
    display: flex;
    flex-direction: column;
    gap: .35rem;
    align-self: flex-start;
    padding: .48rem .62rem .38rem;
    border-radius: 7px 7px 7px 2px;
    background: #fff;
    border: 0;
    box-shadow: 0 1px 1px rgba(15,23,42,.12);
    color: var(--chat-text);
    word-wrap: break-word;
    overflow-wrap: anywhere;
    white-space: pre-wrap;
}

.messages-message-sender {
    font-size: .68rem;
    font-weight: 800;
    color: var(--chat-muted);
    white-space: nowrap;
}

.messages-bubble.mine {
    align-self: flex-end;
    border-radius: 7px 7px 2px 7px;
    background: #d9fdd3;
    color: var(--chat-text);
    border-color: transparent;
    box-shadow: 0 1px 1px rgba(15,23,42,.12);
}

.messages-bubble time {
    font-size: .65rem;
    opacity: .8;
    display: block;
    text-align: right;
}

.messages-bubble.mine time {
    color: rgba(17,24,39,.58);
}

.messages-bubble.mine .messages-message-sender {
    color: #128c7e;
}

.messages-bubble-audio {
    max-width: min(78%, 28rem);
    width: min(100%, 24rem);
}

.messages-audio-card {
    display: flex;
    flex-direction: column;
    gap: .55rem;
    padding: .25rem 0 .1rem;
}

.messages-audio-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .6rem;
    font-size: .68rem;
    color: inherit;
}

.messages-audio-label {
    display: inline-flex;
    align-items: center;
    gap: .42rem;
    font-weight: 700;
    min-width: 0;
}

.messages-audio-duration {
    font-weight: 700;
    opacity: .8;
    white-space: nowrap;
}

.messages-bubble audio {
    display: block;
    width: 100%;
    max-width: 100%;
    height: 2.5rem;
    border-radius: 10px;
    background: rgba(15,23,42,.04);
}

.messages-bubble.mine audio {
    background: rgba(255,255,255,.08);
}

.messages-composer {
    display: flex;
    align-items: flex-end;
    gap: .65rem;
    padding: .75rem .8rem .8rem;
    border-top: 1px solid var(--chat-border);
    background: #f0f2f5;
    border-top-color: rgba(0,0,0,.08);
    backdrop-filter: none;
    position: sticky;
    bottom: 0;
    z-index: 2;
}

.messages-composer-field {
    flex: 1 1 auto;
    display: flex;
    align-items: flex-end;
    min-width: 0;
}

.messages-composer textarea {
    width: 100%;
    max-height: 10rem;
    min-height: 48px;
    resize: none;
    overflow-y: auto;
    border: 0;
    border-radius: 22px;
    padding: .8rem .95rem;
    background: #fff;
    color: var(--chat-text);
    font-size: .96rem;
    line-height: 1.45;
    box-shadow: none;
    outline: none;
    transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
}

.messages-composer textarea:focus {
    border-color: rgba(37,211,102,.55);
    box-shadow: 0 0 0 3px rgba(37,211,102,.12);
}

.messages-composer-actions {
    display: flex;
    align-items: center;
    gap: .5rem;
    flex-shrink: 0;
    justify-content: flex-end;
}

.messages-action-button,
.messages-send-button {
    border: 0;
    border-radius: 14px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .46rem;
    cursor: pointer;
    min-height: 44px;
    min-width: 44px;
    padding: .75rem .9rem;
    transition: transform .2s ease, box-shadow .2s ease, background .2s ease, opacity .2s ease;
}

.messages-action-button:hover,
.messages-send-button:hover {
    transform: translateY(-1px);
}

.messages-action-button:focus-visible,
.messages-send-button:focus-visible,
.messages-composer textarea:focus-visible {
    outline: 3px solid rgba(37,211,102,.18);
    outline-offset: 2px;
}

.messages-action-button {
    background: #fff;
    color: var(--chat-text);
    border: 1px solid rgba(18, 140, 126, .18);
}

.messages-action-button.is-recording {
    background: linear-gradient(135deg,#ef4444,#dc2626);
    border-color: rgba(220,38,38,.2);
    color: white;
    box-shadow: 0 10px 20px rgba(220,38,38,.18);
}

.messages-action-button-muted {
    background: #f8fafc;
}

.messages-send-button.messages-action-button-muted {
    background: #f8fafc;
    color: var(--chat-text);
    box-shadow: none;
}

.messages-send-button {
    background: #128c7e;
    color: #fff;
    box-shadow: 0 14px 25px var(--chat-send-glow);
    min-width: 104px;
}

.messages-send-button[hidden] {
    display: none;
}

.messages-action-button[hidden] {
    display: none;
}

.messages-friends-panel {
    background: #fff;
    border-radius: 20px;
    border: 1px solid var(--chat-border);
    padding: 1rem;
    box-shadow: var(--chat-shadow);
    margin-top: 1rem;
}

.messages-friends-heading {
    padding-bottom: .8rem;
}

.messages-friends-columns {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.messages-friends-columns h3 {
    font-size: .9rem;
    color: var(--navy-900);
    font-weight: 800;
    margin-bottom: .55rem;
}

.messages-friend-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .8rem;
    padding: .7rem .75rem;
    border: 1px solid var(--chat-border);
    border-radius: 12px;
    background: #f8fafc;
    margin-bottom: .55rem;
}

.messages-friend-actions {
    display: flex;
    gap: .45rem;
    flex-wrap: wrap;
}

.messages-friend-actions button {
    padding: .45rem .58rem;
    border-radius: 10px;
    border: 1px solid var(--chat-border);
    background: #fff;
    cursor: pointer;
    font-size: .75rem;
    color: var(--chat-text);
}

.messages-alert {
    margin: 0 0 .8rem;
    padding: .75rem .9rem;
    border-radius: 12px;
    background: rgba(220,38,38,.08);
    color: #991b1b;
    border: 1px solid rgba(220,38,38,.12);
    font-size: .85rem;
}

.messages-alert[hidden] {
    display: none;
}

.messages-no-results {
    border: 1px dashed rgba(15,23,42,.12);
    border-radius: 12px;
    background: rgba(15,23,42,.01);
}

@media (max-width: 920px) {
    .messages-page {
        --messages-page-height: calc(100dvh - 8.5rem);
        height: var(--messages-page-height);
        padding: 0 0 .5rem;
    }

    .messages-layout {
        grid-template-columns: 1fr;
        height: 100%;
        min-height: 0;
        max-height: 100%;
        position: relative;
    }

    .messages-sidebar {
        display: flex;
    }

    .messages-layout.chat-open .messages-sidebar {
        display: none;
    }

    .messages-layout:not(.chat-open) .messages-chat {
        display: none;
    }

    .messages-layout.chat-open .messages-chat {
        display: flex;
    }

    .messages-chat-panel {
        height: 100%;
    }

    .messages-back-button {
        display: inline-flex;
    }
}

@media (max-width: 651px) {
    .messages-page {
        --messages-page-height: calc(100dvh - 7.25rem);
        height: var(--messages-page-height);
        padding: 0 0 .5rem;
    }

    .messages-heading {
        margin-bottom: .75rem;
    }

    .messages-heading h1 {
        font-size: 1.8rem;
    }

    .messages-heading p {
        font-size: .8rem;
    }

    .messages-transport {
        display: block;
        width: 100%;
    }

    .messages-layout {
        border-radius: 18px;
        box-shadow: 0 14px 32px rgba(15,23,42,.06);
        min-height: 0;
        height: 100%;
        max-height: 100%;
    }

    .messages-chat-body {
        padding: .75rem .65rem .9rem;
        gap: .6rem;
    }

    .messages-bubble {
        max-width: min(82%, 24rem);
        padding: .5rem .62rem .42rem;
    }

    .messages-bubble-audio {
        max-width: min(84%, 28rem);
    }

    .messages-composer {
        padding: .65rem .65rem .7rem;
        gap: .45rem;
    }

    .messages-composer textarea {
        min-height: 44px;
        padding: .7rem .75rem;
        font-size: .9rem;
    }

    .messages-composer-actions {
        gap: .4rem;
    }

    .messages-action-button,
    .messages-send-button {
        min-height: 42px;
        padding: .65rem .7rem;
        font-size: .78rem;
    }

    .messages-send-button {
        min-width: 92px;
    }

    .messages-conversation-main strong {
        font-size: .86rem;
    }

    .messages-conversation-meta {
        min-width: 2.3rem;
    }

    .messages-friends-columns {
        grid-template-columns: 1fr;
    }
}

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
        scroll-behavior: auto !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
(function () {
    const root = document.getElementById('messagesApp');
    if (!root) return;

    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const currentUser = Number(root.dataset.currentUser);
    const isStudent = root.dataset.isStudent === '1';
    const layout = root.querySelector('.messages-layout');
    const list = document.getElementById('conversationList');
    const contacts = document.getElementById('contactResults');
    const search = document.getElementById('contactSearch');
    const alertBox = document.getElementById('messagesAlert');
    const sendButton = document.getElementById('composerActionButton');
    const composerInput = document.getElementById('messageInput');

    let activeId = null;
    let activeRecipient = null;
    let refreshTimer = null;

    const voiceState = {
        isRecording: false,
        recorder: null,
        chunks: [],
        recordingStartedAt: null,
        recordingTimer: null,
    };

    const api = async (url, options = {}) => {
        const response = await fetch(url, {
            credentials: 'same-origin',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Content-Type': 'application/json',
            },
            ...options,
        });

        const data = await response.json().catch(() => ({}));
        if (!response.ok) throw new Error(data.message || 'The request could not be completed.');
        return data;
    };

    const showError = (message) => {
        alertBox.textContent = message;
        alertBox.hidden = false;
    };

    const clearError = () => {
        alertBox.hidden = true;
    };

    const escape = (value) => { const div = document.createElement('div'); div.textContent = value || ''; return div.innerHTML; };
    const formatTime = (value) => value ? new Date(value).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';
    const formatDuration = (totalSeconds) => {
        const safe = Math.max(0, Number(totalSeconds) || 0);
        const minutes = String(Math.floor(safe / 60)).padStart(2, '0');
        const seconds = String(safe % 60).padStart(2, '0');
        return `${minutes}:${seconds}`;
    };
    const avatarUrl = (person) => person.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(person.name || 'User')}&background=0f766e&color=fff&size=96`;

    async function loadConversations() {
        try {
            const data = await api('{{ route('messages.conversations') }}');
            list.innerHTML = data.conversations.length ? data.conversations.map((conversation) => {
                const person = conversation.recipient;
                if (!person) return '';
                return `<button class="messages-conversation ${conversation.id === activeId ? 'active' : ''}" data-conversation="${conversation.id}" data-recipient='${JSON.stringify(person).replace(/'/g,'&#39;')}'><img class="messages-avatar" src="${avatarUrl(person)}" alt=""><span class="messages-conversation-main"><strong>${escape(person.name)}</strong><small>${escape(conversation.last_message || 'No messages yet')}</small></span><span class="messages-conversation-meta">${conversation.unread_count ? `<span class="messages-unread">${conversation.unread_count}</span>` : ''}<small class="messages-time">${formatTime(conversation.last_message_at)}</small></span></button>`;
            }).join('') : '<div class="messages-no-results">Your private inbox is empty.</div>';

            list.querySelectorAll('[data-conversation]').forEach((button) => {
                button.addEventListener('click', () => openConversation(Number(button.dataset.conversation), JSON.parse(button.dataset.recipient)));
            });
        } catch (error) {
            list.innerHTML = '<div class="messages-no-results">Unable to load conversations.</div>';
            showError(error.message);
        }
    }

    async function loadContacts(query) {
        if (!query.trim()) {
            contacts.hidden = true;
            contacts.innerHTML = '';
            return;
        }

        try {
            const data = await api(`{{ route('messages.contacts') }}?q=${encodeURIComponent(query)}`);
            contacts.hidden = false;
            contacts.innerHTML = data.contacts.length ? data.contacts.map((person) => `<button class="messages-contact" data-contact="${person.id}" data-name="${escape(person.name)}" data-avatar="${avatarUrl(person)}"><img class="messages-avatar" src="${avatarUrl(person)}" alt=""><span><strong>${escape(person.name)}</strong><small>${escape(person.role || 'Authorized contact')}</small></span></button>`).join('') : '<div class="messages-no-results">No authorized contacts found.</div>';

            contacts.querySelectorAll('[data-contact]').forEach((button) => {
                button.addEventListener('click', () => {
                    startConversation(Number(button.dataset.contact), {
                        id: Number(button.dataset.contact),
                        name: button.dataset.name,
                        avatar: button.dataset.avatar,
                    });
                });
            });
        } catch (error) {
            showError(error.message);
        }
    }

    async function startConversation(recipientId, person) {
        try {
            clearError();
            const data = await api('{{ route('messages.conversations.start') }}', {
                method: 'POST',
                body: JSON.stringify({ recipient_id: recipientId }),
            });

            contacts.hidden = true;
            search.value = '';
            await loadConversations();
            openConversation(data.conversation.id, person);
        } catch (error) {
            showError(error.message);
        }
    }

    async function openConversation(id, person) {
        activeId = id;
        activeRecipient = person;
        layout.classList.add('chat-open');
        document.getElementById('chatEmpty').hidden = true;
        document.getElementById('chatPanel').hidden = false;
        document.getElementById('chatName').textContent = person.name;
        document.getElementById('chatAvatar').src = avatarUrl(person);
        document.getElementById('chatAvatar').alt = person.name;
        await loadMessages();
    }

    function renderTextMessage(message) {
        const sender = escape(message.sender_name || (Number(message.sender_id) === currentUser ? 'You' : 'Participant'));
        return `<div class="messages-bubble ${message.sender_id === currentUser ? 'mine' : ''}"><span class="messages-message-sender">${sender}</span>${escape(message.body)}<time>${formatTime(message.created_at)}</time></div>`;
    }

    function renderVoiceMessage(message) {
        const audioUrl = message.audio_url || `/api/voice-messages/${message.id}/file`;
        const isMine = Number(message.sender_id) === currentUser;
        const duration = formatDuration(Number(message.duration_seconds || 0));

        return `
            <div class="messages-bubble messages-bubble-audio ${isMine ? 'mine' : ''}">
                <div class="messages-audio-card">
                    <span class="messages-message-sender">${escape(message.sender_name || (isMine ? 'You' : 'Participant'))}</span>
                    <div class="messages-audio-header">
                        <span class="messages-audio-label"><i class="bi bi-mic-fill" aria-hidden="true"></i> Voice note</span>
                        <span class="messages-audio-duration">${duration}</span>
                    </div>
                    <audio controls preload="metadata" aria-label="Voice note from ${escape(message.sender_name || (isMine ? 'you' : 'participant'))}, ${duration}" src="${audioUrl}"></audio>
                </div>
                <time>${formatTime(message.created_at)}</time>
            </div>
        `;
    }

    async function loadMessages() {
        if (!activeId) return;

        try {
            const data = await api(`/api/messages/conversations/${activeId}/messages`);
            const body = document.getElementById('chatMessages');
            const combined = (data.messages || []).map((message) => ({
                ...message,
                kind: message.type === 'voice' ? 'voice' : 'text',
            }));

            body.innerHTML = combined.length ? combined.map((item) => item.kind === 'voice' ? renderVoiceMessage(item) : renderTextMessage(item)).join('') : '<div class="messages-no-results">No messages yet. Start the conversation privately.</div>';
            body.scrollTop = body.scrollHeight;
            await loadConversations();
        } catch (error) {
            showError(error.message);
        }
    }

    document.getElementById('messageForm').addEventListener('submit', async (event) => {
        event.preventDefault();
        const input = document.getElementById('messageInput');
        const body = input.value.trim();
        if (!body || !activeId) return;

        const button = event.submitter || document.querySelector('#messageForm button[type="submit"]');

        try {
            clearError();
            if (button) button.disabled = true;
            await api(`/api/messages/conversations/${activeId}/messages`, {
                method: 'POST',
                body: JSON.stringify({ body }),
            });
            input.value = '';
            await loadMessages();
        } catch (error) {
            showError(error.message);
        } finally {
            if (button) button.disabled = false;
        }
    });

    document.getElementById('chatBackButton').addEventListener('click', () => layout.classList.remove('chat-open'));
    document.getElementById('newMessageButton').addEventListener('click', () => search.focus());
    search.addEventListener('input', () => loadContacts(search.value));

    if (isStudent) {
        const button = document.getElementById('friendsButton');
        button.hidden = false;
        button.addEventListener('click', loadFriends);

        const friendSearch = document.getElementById('friendSearch');
        friendSearch.addEventListener('input', () => loadFriendStudents(friendSearch.value));
    }

    async function loadFriendStudents(query) {
        const result = document.getElementById('friendSearchResults');
        if (!query.trim()) {
            result.hidden = true;
            return;
        }

        try {
            const data = await api(`{{ route('messages.students') }}?q=${encodeURIComponent(query)}`);
            result.hidden = false;
            result.innerHTML = data.students.length ? data.students.map((person) => `<button class="messages-contact" data-add-friend="${person.id}"><img class="messages-avatar" src="${avatarUrl(person)}" alt=""><span><strong>${escape(person.name)}</strong><small>Add friend</small></span></button>`).join('') : '<div class="messages-no-results">No students found.</div>';

            result.querySelectorAll('[data-add-friend]').forEach((button) => {
                button.addEventListener('click', async () => {
                    try {
                        await api('{{ route('messages.friend-requests.store') }}', {
                            method: 'POST',
                            body: JSON.stringify({ recipient_id: Number(button.dataset.addFriend) }),
                        });
                        document.getElementById('friendSearch').value = '';
                        result.hidden = true;
                        await loadFriends();
                    } catch (error) {
                        showError(error.message);
                    }
                });
            });
        } catch (error) {
            showError(error.message);
        }
    }

    async function loadFriends() {
        document.getElementById('friendsPanel').hidden = false;

        try {
            const data = await api('{{ route('messages.friend-requests') }}');
            const pending = (data.requests || []).filter((request) => request.status === 'pending');
            const accepted = (data.requests || []).filter((request) => request.status === 'accepted');

            document.getElementById('requestList').innerHTML = pending.length ? pending.map((request) => {
                const incoming = Number(request.recipient_id) === currentUser;
                const person = incoming ? request.requester : request.recipient;
                return `<div class="messages-friend-row"><span>${escape(person.name)}</span>${incoming ? `<span class="messages-friend-actions"><button data-friend-action="accepted" data-request="${request.id}">Accept</button><button data-friend-action="declined" data-request="${request.id}">Decline</button></span>` : '<small>Pending</small>'}</div>`;
            }).join('') : '<div class="messages-no-results">No pending requests.</div>';

            document.getElementById('friendList').innerHTML = accepted.length ? accepted.map((request) => {
                const person = Number(request.requester_id) === currentUser ? request.recipient : request.requester;
                return `<div class="messages-friend-row"><span>${escape(person.name)}</span><span class="messages-friend-actions"><button data-friend-chat="${person.id}" data-friend-name="${escape(person.name)}" data-friend-avatar="${avatarUrl(person)}">Tima Chat</button><button data-remove-friend="${request.id}">Remove</button></span></div>`;
            }).join('') : '<div class="messages-no-results">Accepted friends appear here.</div>';

            document.querySelectorAll('[data-friend-action]').forEach((button) => {
                button.addEventListener('click', async () => {
                    await api(`/api/messages/friend-requests/${button.dataset.request}/${button.dataset.friendAction}`, { method: 'PATCH' });
                    await loadFriends();
                });
            });

            document.querySelectorAll('[data-remove-friend]').forEach((button) => {
                button.addEventListener('click', async () => {
                    await api(`/api/messages/friend-requests/${button.dataset.removeFriend}`, { method: 'DELETE' });
                    await loadFriends();
                });
            });

            document.querySelectorAll('[data-friend-chat]').forEach((button) => {
                button.addEventListener('click', () => {
                    startConversation(Number(button.dataset.friendChat), {
                        id: Number(button.dataset.friendChat),
                        name: button.dataset.friendName,
                        avatar: button.dataset.friendAvatar,
                    });
                });
            });
        } catch (error) {
            showError(error.message);
        }
    }


    async function ensureMicrophone() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            throw new Error('This browser does not support microphone capture.');
        }

        try {
            return await navigator.mediaDevices.getUserMedia({ audio: true });
        } catch (error) {
            throw new Error('Microphone permission was denied. Please allow mic access to record a voice note.');
        }
    }

    function recordingTimeLabel() {
        if (!voiceState.recordingStartedAt) return '00:00';
        const seconds = Math.floor((Date.now() - voiceState.recordingStartedAt) / 1000);
        return formatDuration(seconds);
    }

    function syncComposerAction() {
        const hasText = composerInput.value.trim().length > 0;
        const isRecording = voiceState.isRecording;

        if (isRecording) {
            sendButton.hidden = false;
            sendButton.classList.add('is-recording');
            sendButton.classList.remove('messages-action-button-muted');
            sendButton.setAttribute('aria-label', 'Stop recording voice note');
            sendButton.innerHTML = '<i class="bi bi-stop-fill" aria-hidden="true"></i><span>Stop</span>';
            return;
        }

        sendButton.classList.remove('is-recording');
        sendButton.classList.add('messages-action-button-muted');
        sendButton.setAttribute('aria-label', hasText ? 'Send message' : 'Record a voice note');
        sendButton.innerHTML = hasText
            ? '<i class="bi bi-send-fill" aria-hidden="true"></i><span>Send</span>'
            : '<i class="bi bi-mic" aria-hidden="true"></i><span>Voice note</span>';
        sendButton.hidden = false;
    }

    async function recordVoiceNote() {
        if (!activeId) {
            showError('Open a conversation before recording a voice note.');
            return;
        }

        try {
            clearError();

            if (voiceState.isRecording) {
                if (voiceState.recorder && voiceState.recorder.state !== 'inactive') {
                    voiceState.recorder.stop();
                }
                return;
            }

            const stream = await ensureMicrophone();
            voiceState.chunks = [];

            let mimeType = 'audio/webm';
            const supportedMimeTypes = [
                'audio/webm;codecs=opus',
                'audio/webm',
                'audio/ogg',
                'audio/mp4',
            ];

            for (const type of supportedMimeTypes) {
                if (MediaRecorder.isTypeSupported(type)) {
                    mimeType = type;
                    break;
                }
            }

            const recorder = new MediaRecorder(stream, { mimeType });
            voiceState.recorder = recorder;
            voiceState.recordingStartedAt = Date.now();
            voiceState.isRecording = true;
            syncComposerAction();
            clearError();

            const timer = setInterval(() => {
                if (!voiceState.isRecording) {
                    clearInterval(timer);
                }
            }, 250);
            voiceState.recordingTimer = timer;

            recorder.ondataavailable = (event) => {
                if (event.data.size > 0) {
                    voiceState.chunks.push(event.data);
                }
            };

            recorder.onstop = async () => {
                clearInterval(voiceState.recordingTimer);
                voiceState.recordingTimer = null;

                const streamTracks = stream.getTracks ? stream.getTracks() : [];
                streamTracks.forEach((track) => track.stop());

                const blob = new Blob(voiceState.chunks, { type: mimeType });
                const ext = mimeType.includes('ogg') ? 'ogg' : mimeType.includes('mp4') ? 'm4a' : 'webm';
                const file = new File([blob], `voice-note-${Date.now()}.${ext}`, { type: mimeType });
                const form = new FormData();
                form.append('audio', file);
                form.append('duration_seconds', String(Math.max(1, Math.floor((Date.now() - voiceState.recordingStartedAt) / 1000))));

                try {
                    const response = await fetch(`/api/conversations/${activeId}/voice-messages`, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            Accept: 'application/json',
                        },
                        body: form,
                    });

                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) throw new Error(data.message || 'Voice note upload failed.');

                    const body = document.getElementById('chatMessages');
                    const wrapper = document.createElement('div');
                    wrapper.className = `messages-bubble messages-bubble-audio ${Number(data.voice_message.sender_id) === currentUser ? 'mine' : ''}`;

                    const card = document.createElement('div');
                    card.className = 'messages-audio-card';

                    const header = document.createElement('div');
                    header.className = 'messages-audio-header';

                    const label = document.createElement('span');
                    label.className = 'messages-audio-label';
                    label.innerHTML = '<i class="bi bi-mic-fill" aria-hidden="true"></i> Voice note';

                    const duration = document.createElement('span');
                    duration.className = 'messages-audio-duration';
                    duration.textContent = formatDuration(Number(data.voice_message.duration_seconds || 0));

                    header.appendChild(label);
                    header.appendChild(duration);

                    const audio = document.createElement('audio');
                    audio.controls = true;
                    audio.preload = 'metadata';
                    audio.src = `/api/voice-messages/${data.voice_message.id}/file`;

                    card.appendChild(header);
                    card.appendChild(audio);

                    const time = document.createElement('time');
                    time.textContent = formatTime(new Date().toISOString());

                    wrapper.appendChild(card);
                    wrapper.appendChild(time);
                    body.appendChild(wrapper);
                    body.scrollTop = body.scrollHeight;
                    clearError();
                } catch (error) {
                    showError(error.message);
                } finally {
                    voiceState.isRecording = false;
                    voiceState.recordingStartedAt = null;
                    voiceState.recorder = null;
                    syncComposerAction();
                }
            };

            recorder.start();
        } catch (error) {
            voiceState.isRecording = false;
            voiceState.recordingStartedAt = null;
            voiceState.recorder = null;
            syncComposerAction();
            showError(error.message);
        }
    }

    const syncPageHeight = () => {
        const page = document.querySelector('.messages-page');
        if (!page) return;

        const viewportHeight = window.visualViewport ? window.visualViewport.height : window.innerHeight;
        const safeOffset = window.innerWidth <= 920 ? 118 : 160;
        const computedHeight = Math.max(420, viewportHeight - safeOffset);
        page.style.setProperty('--messages-page-height', `${computedHeight}px`);
    };

    const autoResizeTextarea = () => {
        composerInput.style.height = 'auto';
        composerInput.style.height = `${Math.min(composerInput.scrollHeight, 160)}px`;
    };

    composerInput.addEventListener('input', () => {
        autoResizeTextarea();
        syncComposerAction();
    });

    syncPageHeight();
    window.addEventListener('resize', syncPageHeight);
    if (window.visualViewport) {
        window.visualViewport.addEventListener('resize', syncPageHeight);
        window.visualViewport.addEventListener('scroll', syncPageHeight);
    }

    composerInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && !event.shiftKey && !event.altKey && !event.ctrlKey && !event.metaKey) {
            event.preventDefault();
            const form = document.getElementById('messageForm');
            if (form && composerInput.value.trim()) {
                form.requestSubmit();
            }
        }
    });

    sendButton.addEventListener('click', () => {
        if (voiceState.isRecording) {
            recordVoiceNote();
            return;
        }

        if (!composerInput.value.trim()) {
            recordVoiceNote();
            return;
        }

        document.getElementById('messageForm').requestSubmit();
    });

    document.getElementById('closeFriendsButton').addEventListener('click', () => document.getElementById('friendsPanel').hidden = true);
    autoResizeTextarea();
    syncComposerAction();

    loadConversations();
    refreshTimer = setInterval(() => {
        loadConversations();
        if (activeId) loadMessages();
    }, 10000);

    window.addEventListener('beforeunload', () => clearInterval(refreshTimer));
})();
</script>
@endpush
