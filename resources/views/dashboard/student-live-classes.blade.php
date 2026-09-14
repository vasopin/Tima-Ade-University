<section class="student-live-classes mb-4" aria-labelledby="student-live-classes-heading" data-status-url="{{ route('student.live-classes.status') }}">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <span class="eyebrow-label">Your classroom schedule</span>
            <h4 class="dashboard-section-header mb-0" id="student-live-classes-heading">Live Classes</h4>
        </div>
        @if($liveClasses->where('status', 'live')->isNotEmpty())
            <span class="badge bg-danger-subtle text-danger px-3 py-2">{{ $liveClasses->where('status', 'live')->count() }} live now</span>
        @endif
    </div>

    @forelse($liveClasses as $liveClass)
        <div class="card {{ $liveClass->status === 'live' ? 'border-danger' : 'border-light' }} shadow-sm mb-3" data-live-class-id="{{ $liveClass->id }}" data-live-class-status="{{ $liveClass->status }}">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <span class="badge {{ $liveClass->status === 'live' ? 'bg-danger' : ($liveClass->status === 'ended' ? 'bg-secondary' : 'bg-warning text-dark') }} mb-2" data-live-class-badge>
                        {{ $liveClass->status === 'live' ? 'LIVE' : ucfirst($liveClass->status) }}
                    </span>
                    <h5 class="mb-1">{{ $liveClass->title }}</h5>
                    <div class="text-muted small">{{ $liveClass->subject->name }} • {{ $liveClass->teacher->name }}</div>
                    <div class="text-muted small mt-1">
                        <i class="bi bi-calendar3 me-1"></i>{{ $liveClass->scheduled_at?->format('M d, Y h:i A') }}
                        <span class="ms-2"><i class="bi bi-clock me-1"></i>{{ $liveClass->duration_minutes }} minutes</span>
                    </div>
                </div>
                <form method="POST" action="{{ route('live-classes.join', $liveClass) }}">
                    @csrf
                    <button type="submit" class="btn {{ $liveClass->status === 'live' ? 'btn-danger' : 'btn-outline-secondary' }} px-4" aria-label="Join {{ $liveClass->title }} live class" {{ $liveClass->status === 'live' ? '' : 'disabled' }} aria-disabled="{{ $liveClass->status === 'live' ? 'false' : 'true' }}">Join Live Class</button>
                </form>
            </div>
        </div>
    @empty
        <div class="alert student-live-empty mb-0">No live or upcoming classes are available for your enrolled courses.</div>
    @endforelse
</section>

<script>
(() => {
    const section = document.querySelector('.student-live-classes[data-status-url]');
    if (!section) return;

    const refreshStatuses = async () => {
        try {
            const response = await fetch(section.dataset.statusUrl, {headers: {'Accept': 'application/json'}});
            if (!response.ok) return;
            const statuses = await response.json();
            section.querySelectorAll('[data-live-class-status]').forEach((card) => {
                const status = statuses[card.dataset.liveClassId];
                if (!status) return;
                card.dataset.liveClassStatus = status;
                const badge = card.querySelector('[data-live-class-badge]');
                const button = card.querySelector('button[type="submit"]');
                if (badge) {
                    badge.textContent = status === 'live' ? 'LIVE' : status.charAt(0).toUpperCase() + status.slice(1);
                    badge.className = `badge mb-2 ${status === 'live' ? 'bg-danger' : (status === 'ended' ? 'bg-secondary' : 'bg-warning text-dark')}`;
                }
                if (button) {
                    const live = status === 'live';
                    button.disabled = !live;
                    button.className = `btn ${live ? 'btn-danger' : 'btn-outline-secondary'} px-4`;
                    button.setAttribute('aria-disabled', live ? 'false' : 'true');
                }
            });
        } catch (error) {
            // Keep the last server-confirmed state when polling is unavailable.
        }
    };

    refreshStatuses();
    window.setInterval(refreshStatuses, 15000);
})();
</script>
