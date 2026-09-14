@extends('layouts.welcome')

@section('title', $roleProfile['title'])

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Welcome</li>
@endsection

@section('content')
    @php
        $roleLabel = $user->role?->name ?? 'User';
        $roleTitle = $roleProfile['title'];
    @endphp

    <div class="container-fluid px-0">
        <div class="role-welcome-page" style="--welcome-accent: {{ $roleProfile['accent'] }}; --welcome-soft: {{ $roleProfile['soft'] }}; --welcome-surface: {{ $roleProfile['surface'] }};">
            <div class="role-welcome-topbar">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <span class="role-welcome-badge"><i class="bi bi-shield-check me-1"></i> {{ $roleProfile['eyebrow'] }}</span>
                    <span class="text-secondary small"><i class="bi bi-person-circle me-1"></i> {{ $user->name }}</span>
                </div>
                <a href="{{ $dashboardRoute }}" class="btn btn-primary btn-lg role-dashboard-button">
                    <i class="bi bi-box-arrow-in-right me-2"></i> {{ $user->isStudent() ? 'Student Dashboard' : 'Go to Dashboard' }}
                </a>
            </div>

            <div class="role-welcome-hero">
                <div class="role-welcome-copy">
                    <span class="role-kicker">{{ $roleProfile['badge'] }}</span>
                    <h1>{{ $roleProfile['headline'] }}</h1>
                    <p>{{ $roleProfile['description'] }}</p>
                    <div class="role-welcome-meta">
                        <div>
                            <span class="meta-label">Role</span>
                            <strong>{{ $roleLabel }}</strong>
                        </div>
                        <div>
                            <span class="meta-label">Access</span>
                            <strong>Authenticated</strong>
                        </div>
                    </div>
                </div>

                <div class="role-welcome-panel">
                    <div class="mini-panel-header">{{ $roleProfile['stat'] }}</div>
                    <div class="mini-panel-number">{{ strtoupper(substr($roleLabel, 0, 1)) }}</div>
                    <ul>
                        <li><i class="bi bi-check-circle-fill"></i> Secure access granted</li>
                        <li><i class="bi bi-check-circle-fill"></i> Personalized dashboard ready</li>
                        <li><i class="bi bi-check-circle-fill"></i> University operations in view</li>
                    </ul>
                </div>
            </div>

            <div class="role-welcome-grid">
                @foreach($roleProfile['highlights'] as $highlight)
                    <div class="welcome-feature-card">
                        <span class="feature-icon"><i class="{{ $highlight['icon'] }}"></i></span>
                        <div>
                            <span class="feature-label">{{ $highlight['label'] }}</span>
                            <strong>{{ $highlight['value'] }}</strong>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @push('styles')
        <style>
                .role-welcome-page,
                .role-welcome-page * {
                    min-width: 0;
                }

                .role-welcome-page {
                    width: 100%;
                    max-width: none;
                    min-height: 100dvh;
                    margin: 0;
                    overflow: visible;
                    border-radius: 0;
                }

                .role-welcome-page {
                    --welcome-accent: #0f172a;
                --welcome-soft: #e2e8f0;
                --welcome-surface: #f8fafc;
                background: linear-gradient(135deg, rgba(255,255,255,0.98), rgba(248,250,252,0.9));
                border: 0;
                box-shadow: 0 20px 50px rgba(15, 23, 42, 0.12);
                padding: 32px;
            }

            .role-welcome-topbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
                flex-wrap: wrap;
                margin-bottom: 28px;
            }

            .role-welcome-topbar > .d-flex {
                min-width: 0;
                flex: 1 1 20rem;
            }

            .role-welcome-badge {
                display: inline-flex;
                align-items: center;
                background: var(--welcome-soft);
                border: 1px solid rgba(15, 23, 42, 0.08);
                color: var(--welcome-accent);
                border-radius: 999px;
                padding: 0.5rem 1rem;
                font-weight: 700;
                letter-spacing: 0.02em;
            }

            .role-dashboard-button {
                background: var(--welcome-accent);
                border-color: var(--welcome-accent);
                border-radius: 14px;
                font-weight: 700;
                padding: 0.8rem 1.25rem;
            }

            .role-dashboard-button:hover {
                filter: brightness(1.05);
            }

            .role-dashboard-button:focus-visible {
                outline: 3px solid rgba(37, 99, 235, 0.45);
                outline-offset: 3px;
            }

            .role-welcome-hero {
                display: grid;
                grid-template-columns: minmax(0, 1.5fr) minmax(260px, 0.95fr);
                gap: 22px;
                padding: 26px;
                border-radius: 22px;
                background: linear-gradient(135deg, var(--welcome-surface), color-mix(in srgb, var(--welcome-soft) 28%, white));
                border: 1px solid rgba(148, 163, 184, 0.18);
                margin-bottom: 26px;
            }

            .role-welcome-copy {
                padding: 8px 6px;
            }

            .role-kicker {
                display: inline-block;
                margin-bottom: 14px;
                padding: 0.42rem 0.8rem;
                background: rgba(255,255,255,0.7);
                border: 1px solid rgba(15, 23, 42, 0.07);
                border-radius: 999px;
                color: var(--welcome-accent);
                font-size: 0.78rem;
                font-weight: 700;
                letter-spacing: 0.08em;
                text-transform: uppercase;
            }

            .role-welcome-copy h1 {
                margin: 0;
                font-size: clamp(2.1rem, 4vw, 3.25rem);
                line-height: 1.08;
                font-weight: 800;
                color: #0f172a;
                overflow-wrap: anywhere;
            }

            .role-welcome-copy p {
                margin-top: 1rem;
                max-width: 56ch;
                color: rgba(15, 23, 42, 0.78);
                font-size: 1.06rem;
                line-height: 1.7;
                overflow-wrap: anywhere;
            }

            .role-welcome-meta {
                display: flex;
                flex-wrap: wrap;
                gap: 18px;
                margin-top: 1.5rem;
            }

            .role-welcome-meta > div {
                min-width: 140px;
                padding: 0.9rem 1rem;
                border-radius: 14px;
                background: rgba(255,255,255,0.7);
                border: 1px solid rgba(148, 163, 184, 0.18);
            }

            .meta-label {
                display: block;
                font-size: 0.72rem;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                color: rgba(15, 23, 42, 0.6);
                margin-bottom: 0.4rem;
            }

            .role-welcome-panel {
                background: linear-gradient(180deg, var(--welcome-accent), color-mix(in srgb, var(--welcome-accent) 86%, black));
                color: white;
                border-radius: 20px;
                padding: 22px 18px;
                position: relative;
                overflow: hidden;
                min-height: 290px;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .role-welcome-panel::before {
                content: "";
                position: absolute;
                inset: auto -40px -40px auto;
                width: 160px;
                height: 160px;
                background: rgba(255,255,255,0.08);
                border-radius: 50%;
            }

            .mini-panel-header {
                position: relative;
                z-index: 1;
                font-size: 0.72rem;
                letter-spacing: 0.12em;
                text-transform: uppercase;
                opacity: 0.9;
            }

            .mini-panel-number {
                position: relative;
                z-index: 1;
                font-size: 4.1rem;
                line-height: 1;
                font-weight: 800;
                margin: 1rem 0;
                letter-spacing: -0.06em;
            }

            .role-welcome-panel ul {
                position: relative;
                z-index: 1;
                list-style: none;
                padding: 0;
                margin: 0;
                display: grid;
                gap: 0.8rem;
                color: rgba(255,255,255,0.92);
                font-size: 0.96rem;
            }

            .role-welcome-panel li {
                display: flex;
                align-items: center;
                gap: 0.7rem;
                min-width: 0;
                overflow-wrap: anywhere;
            }

            .role-welcome-grid {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 18px;
            }

            .welcome-feature-card {
                display: flex;
                align-items: center;
                gap: 14px;
                background: rgba(255,255,255,0.72);
                border: 1px solid rgba(148, 163, 184, 0.2);
                border-radius: 18px;
                padding: 1rem 1rem 1.1rem;
                overflow-wrap: anywhere;
            }

            .feature-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 48px;
                height: 48px;
                border-radius: 14px;
                background: var(--welcome-soft);
                color: var(--welcome-accent);
                font-size: 1.2rem;
            }

            .feature-label {
                display: block;
                font-size: 0.72rem;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                opacity: 0.7;
                margin-bottom: 0.25rem;
            }

            .welcome-feature-card strong {
                font-size: 0.98rem;
                color: #0f172a;
                overflow-wrap: anywhere;
            }

            @media (max-width: 1100px) {
                .role-welcome-hero {
                    grid-template-columns: 1fr;
                }

                .role-welcome-grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            @media (max-width: 768px) {
                .role-welcome-page {
                    padding: 20px;
                }

                .role-welcome-topbar {
                    align-items: stretch;
                    margin-bottom: 22px;
                }

                .role-welcome-topbar > .d-flex {
                    flex-basis: 100%;
                }

                .role-dashboard-button {
                    width: 100%;
                    min-height: 52px;
                }

                .role-welcome-hero {
                    gap: 12px;
                    padding: 16px;
                    margin-bottom: 20px;
                }

                .role-welcome-copy h1 {
                    font-size: clamp(1.75rem, 7vw, 2.4rem);
                }

                .role-welcome-copy p {
                    margin-top: 0.7rem;
                    font-size: 0.92rem;
                    line-height: 1.45;
                }

                .role-welcome-panel {
                    min-height: 0;
                    padding: 16px;
                }

                .role-welcome-grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                    gap: 12px;
                }

                .role-welcome-panel ul {
                    gap: 0.45rem;
                    font-size: 0.86rem;
                }

                .mini-panel-number {
                    margin: 0.55rem 0;
                    font-size: 3.2rem;
                }
            }

            @media (max-width: 576px) {
                .role-welcome-page {
                    padding: 12px;
                }

                .role-welcome-topbar {
                    gap: 12px;
                }

                .role-welcome-topbar > .d-flex {
                    gap: 8px !important;
                    flex-direction: row;
                    align-items: flex-start !important;
                }

                .role-welcome-badge {
                    max-width: 100%;
                    padding: 0.45rem 0.8rem;
                    font-size: 0.88rem;
                }

                .role-welcome-hero {
                    gap: 10px;
                    padding: 12px;
                    border-radius: 16px;
                }

                .role-welcome-copy {
                    padding: 2px;
                }

                .role-kicker {
                    max-width: 100%;
                    margin-bottom: 7px;
                    padding: 0.3rem 0.55rem;
                    font-size: 0.62rem;
                }

                .role-welcome-copy h1 {
                    font-size: clamp(1.45rem, 7vw, 2rem);
                }

                .role-welcome-copy p {
                    margin-top: 0.5rem;
                    font-size: 0.82rem;
                    line-height: 1.35;
                }

                .role-welcome-meta {
                    gap: 6px;
                    margin-top: 0.7rem;
                }

                .role-welcome-meta > div {
                    flex: 1 1 90px;
                    min-width: 0;
                    padding: 0.5rem 0.6rem;
                }

                .role-welcome-panel {
                    border-radius: 16px;
                    padding: 12px;
                }

                .mini-panel-number {
                    margin: 0.35rem 0;
                    font-size: 2.7rem;
                }

                .role-welcome-grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                    gap: 8px;
                }

                .welcome-feature-card {
                    gap: 7px;
                    padding: 0.6rem;
                    border-radius: 12px;
                }

                .feature-icon {
                    flex: 0 0 32px;
                    width: 32px;
                    height: 32px;
                    border-radius: 9px;
                    font-size: 0.9rem;
                }

                .feature-label {
                    font-size: 0.58rem;
                }

                .welcome-feature-card strong {
                    font-size: 0.72rem;
                    line-height: 1.15;
                }

                .role-welcome-panel ul {
                    gap: 0.3rem;
                    font-size: 0.72rem;
                }

                .role-welcome-panel li {
                    gap: 0.4rem;
                }
            }

            @media (max-height: 700px) {
                .role-welcome-page {
                    padding: 12px;
                }

                .role-welcome-topbar {
                    gap: 8px;
                    margin-bottom: 10px;
                }

                .role-welcome-hero {
                    gap: 10px;
                    padding: 12px;
                    margin-bottom: 10px;
                }

                .role-welcome-copy h1 {
                    font-size: clamp(1.35rem, 4vw, 2rem);
                }

                .role-welcome-copy p {
                    margin-top: 0.45rem;
                    font-size: 0.82rem;
                    line-height: 1.3;
                }

                .role-welcome-meta {
                    gap: 6px;
                    margin-top: 0.65rem;
                }

                .role-welcome-meta > div {
                    padding: 0.45rem 0.6rem;
                }

                .role-welcome-panel {
                    padding: 12px;
                }

                .role-welcome-panel ul {
                    gap: 0.3rem;
                    font-size: 0.76rem;
                }

                .mini-panel-number {
                    margin: 0.35rem 0;
                    font-size: 2.6rem;
                }

                .role-welcome-grid {
                    gap: 8px;
                }

                .welcome-feature-card {
                    padding: 0.6rem;
                }
            }

            @media (max-width: 576px) and (max-height: 700px) {
                .role-welcome-page {
                    padding: 8px;
                }

                .role-welcome-topbar {
                    display: grid;
                    grid-template-columns: minmax(0, 1fr) auto;
                    gap: 6px;
                    margin-bottom: 6px;
                }

                .role-welcome-topbar > .d-flex {
                    grid-column: 1;
                    align-self: center;
                    flex-wrap: nowrap;
                }

                .role-welcome-topbar > .d-flex .text-secondary {
                    max-width: 8rem;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    white-space: nowrap;
                }

                .role-welcome-badge {
                    padding: 0.3rem 0.5rem;
                    font-size: 0.62rem;
                    white-space: nowrap;
                }

                .role-dashboard-button {
                    grid-column: 2;
                    width: auto;
                    min-height: 42px;
                    padding: 0.45rem 0.65rem;
                    font-size: 0.7rem;
                    white-space: nowrap;
                }

                .role-welcome-hero {
                    gap: 8px;
                    padding: 8px;
                    margin-bottom: 6px;
                    border-radius: 12px;
                }

                .role-welcome-copy h1 {
                    font-size: clamp(1.2rem, 6vw, 1.55rem);
                    line-height: 1.05;
                }

                .role-welcome-copy p {
                    margin-top: 0.35rem;
                    font-size: 0.68rem;
                    line-height: 1.2;
                }

                .role-welcome-meta {
                    gap: 4px;
                    margin-top: 0.45rem;
                }

                .role-welcome-meta > div {
                    padding: 0.35rem 0.45rem;
                }

                .meta-label {
                    margin-bottom: 0.15rem;
                    font-size: 0.55rem;
                }

                .role-welcome-meta strong {
                    font-size: 0.68rem;
                }

                .role-welcome-panel {
                    padding: 8px;
                    border-radius: 12px;
                }

                .mini-panel-header {
                    font-size: 0.55rem;
                }

                .mini-panel-number {
                    margin: 0.2rem 0;
                    font-size: 2rem;
                }

                .role-welcome-panel ul {
                    gap: 0.2rem;
                    font-size: 0.58rem;
                }

                .role-welcome-panel li {
                    gap: 0.3rem;
                }

                .role-welcome-grid {
                    gap: 5px;
                }

                .welcome-feature-card {
                    gap: 5px;
                    padding: 0.4rem;
                    border-radius: 9px;
                }

                .feature-icon {
                    flex-basis: 26px;
                    width: 26px;
                    height: 26px;
                    border-radius: 7px;
                    font-size: 0.75rem;
                }

                .feature-label {
                    margin-bottom: 0.1rem;
                    font-size: 0.48rem;
                }

                .welcome-feature-card strong {
                    font-size: 0.6rem;
                    line-height: 1.05;
                }
            }

            @media (max-height: 700px) and (orientation: landscape) {
                .role-welcome-topbar > .d-flex {
                    flex-basis: auto;
                }

                .role-dashboard-button {
                    width: auto;
                }

                .role-welcome-hero {
                    grid-template-columns: minmax(0, 1.25fr) minmax(190px, 0.75fr);
                }

                .role-welcome-grid {
                    grid-template-columns: repeat(4, minmax(0, 1fr));
                }

                .welcome-feature-card {
                    gap: 6px;
                }

                .feature-icon {
                    flex-basis: 28px;
                    width: 28px;
                    height: 28px;
                    font-size: 0.8rem;
                }

                .feature-label {
                    font-size: 0.52rem;
                }

                .welcome-feature-card strong {
                    font-size: 0.66rem;
                }
            }

            @media (max-width: 576px) and (max-height: 700px) and (orientation: landscape) {
                .role-welcome-page {
                    padding: 6px;
                }

                .role-welcome-topbar {
                    margin-bottom: 4px;
                }

                .role-welcome-hero {
                    gap: 6px;
                    padding: 6px;
                    margin-bottom: 4px;
                }

                .role-welcome-copy h1 {
                    font-size: 1rem;
                }

                .role-welcome-copy p {
                    margin-top: 0.2rem;
                    font-size: 0.58rem;
                    line-height: 1.1;
                }

                .role-welcome-meta {
                    margin-top: 0.3rem;
                }

                .role-welcome-meta > div {
                    padding: 0.2rem 0.35rem;
                }

                .meta-label {
                    margin-bottom: 0.05rem;
                    font-size: 0.45rem;
                }

                .role-welcome-meta strong {
                    font-size: 0.56rem;
                }

                .role-welcome-panel {
                    padding: 6px;
                }

                .mini-panel-number {
                    margin: 0.1rem 0;
                    font-size: 1.65rem;
                }

                .role-welcome-panel ul {
                    gap: 0.12rem;
                    font-size: 0.48rem;
                }

                .role-welcome-panel li {
                    gap: 0.2rem;
                }

                .welcome-feature-card {
                    gap: 4px;
                    padding: 0.25rem;
                }

                .feature-icon {
                    flex-basis: 22px;
                    width: 22px;
                    height: 22px;
                    font-size: 0.65rem;
                }

                .feature-label {
                    font-size: 0.42rem;
                }

                .welcome-feature-card strong {
                    font-size: 0.52rem;
                }
            }

            @media (min-width: 577px) and (max-width: 1100px) and (max-height: 800px) and (orientation: landscape) {
                .role-welcome-page {
                    padding: 16px;
                }

                .role-welcome-topbar {
                    margin-bottom: 12px;
                }

                .role-welcome-topbar > .d-flex {
                    flex-basis: auto;
                }

                .role-dashboard-button {
                    width: auto;
                }

                .role-welcome-hero {
                    grid-template-columns: minmax(0, 1.25fr) minmax(240px, 0.75fr);
                    gap: 14px;
                    padding: 16px;
                    margin-bottom: 12px;
                }

                .role-welcome-copy h1 {
                    font-size: clamp(1.7rem, 3.2vw, 2.3rem);
                }

                .role-welcome-copy p {
                    margin-top: 0.65rem;
                    font-size: 0.9rem;
                    line-height: 1.35;
                }

                .role-welcome-panel {
                    min-height: 0;
                    padding: 14px;
                }

                .mini-panel-number {
                    margin: 0.5rem 0;
                    font-size: 3rem;
                }

                .role-welcome-panel ul {
                    gap: 0.35rem;
                    font-size: 0.82rem;
                }

                .role-welcome-grid {
                    grid-template-columns: repeat(4, minmax(0, 1fr));
                    gap: 10px;
                }

                .welcome-feature-card {
                    gap: 7px;
                    padding: 0.65rem;
                }

                .feature-icon {
                    flex-basis: 32px;
                    width: 32px;
                    height: 32px;
                }

                .feature-label {
                    font-size: 0.55rem;
                }

                .welcome-feature-card strong {
                    font-size: 0.72rem;
                }
            }

            @media (min-width: 577px) and (max-height: 500px) and (orientation: landscape) {
                .role-welcome-page {
                    padding: 8px;
                }

                .role-welcome-topbar {
                    gap: 6px;
                    margin-bottom: 6px;
                }

                .role-welcome-badge {
                    padding: 0.3rem 0.55rem;
                    font-size: 0.7rem;
                }

                .role-dashboard-button {
                    min-height: 40px;
                    padding: 0.4rem 0.7rem;
                    font-size: 0.72rem;
                }

                .role-welcome-hero {
                    gap: 8px;
                    padding: 8px;
                    margin-bottom: 6px;
                    border-radius: 12px;
                }

                .role-welcome-copy {
                    padding: 2px;
                }

                .role-kicker {
                    margin-bottom: 5px;
                    padding: 0.25rem 0.5rem;
                    font-size: 0.58rem;
                }

                .role-welcome-copy h1 {
                    font-size: clamp(1.2rem, 3.2vw, 1.65rem);
                    line-height: 1.05;
                }

                .role-welcome-copy p {
                    margin-top: 0.3rem;
                    font-size: 0.68rem;
                    line-height: 1.18;
                }

                .role-welcome-meta {
                    gap: 4px;
                    margin-top: 0.35rem;
                }

                .role-welcome-meta > div {
                    padding: 0.3rem 0.45rem;
                }

                .meta-label {
                    margin-bottom: 0.1rem;
                    font-size: 0.5rem;
                }

                .role-welcome-meta strong {
                    font-size: 0.65rem;
                }

                .role-welcome-panel {
                    padding: 8px;
                    border-radius: 12px;
                }

                .mini-panel-header {
                    font-size: 0.58rem;
                }

                .mini-panel-number {
                    margin: 0.15rem 0;
                    font-size: 2rem;
                }

                .role-welcome-panel ul {
                    gap: 0.18rem;
                    font-size: 0.58rem;
                }

                .role-welcome-panel li {
                    gap: 0.3rem;
                }

                .role-welcome-grid {
                    gap: 6px;
                }

                .welcome-feature-card {
                    gap: 5px;
                    padding: 0.35rem;
                    border-radius: 9px;
                }

                .feature-icon {
                    flex-basis: 26px;
                    width: 26px;
                    height: 26px;
                    border-radius: 7px;
                    font-size: 0.72rem;
                }

                .feature-label {
                    margin-bottom: 0.08rem;
                    font-size: 0.46rem;
                }

                .welcome-feature-card strong {
                    font-size: 0.58rem;
                    line-height: 1.05;
                }
            }
        </style>
    @endpush
@endsection
