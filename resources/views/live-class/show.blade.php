@extends('layouts.app')

@section('title', 'Live Classroom')

@section('content')
<div class="container-fluid py-3" id="live-classroom" data-media-url="{{ route('live-classes.media.update', $liveClass) }}" data-provider="{{ $classroomMeta['provider'] }}" data-provider-url="{{ $classroomMeta['provider_url'] ?? '' }}" data-join-token="{{ $classroomMeta['join_token'] ?? '' }}">
    <div class="row g-3">
        <div class="col-xl-9">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="badge {{ $liveClass->status === 'live' ? 'bg-danger' : 'bg-secondary' }}">{{ strtoupper($liveClass->status) }}</div>
                                @if($classroomMeta['is_demo'])
                                    <span class="badge bg-warning text-dark">Development mock</span>
                                @else
                                    <span class="badge bg-success">Production provider</span>
                                @endif
                                <span class="small text-white-50" id="livekitConnectionStatus" aria-live="polite"></span>
                            </div>
                            <h3 class="mb-1">{{ $liveClass->title }}</h3>
                            <p class="mb-0 text-muted">{{ $liveClass->subject->name }} • {{ $liveClass->schoolClass->name }} • {{ $liveClass->teacher->name }}</p>
                        </div>
                        @if($liveClass->status === 'live' && auth()->user()->isTeacher() && auth()->user()->id === $liveClass->teacher_id)
                            <form method="POST" action="{{ route('live-classes.end', $liveClass) }}">
                                @csrf
                                <button class="btn btn-outline-danger">End Class</button>
                            </form>
                        @endif
                    </div>

                    <div class="bg-dark rounded p-3" style="min-height: 360px;">
                        <div class="row g-3 h-100 align-items-stretch">
                            <div class="col-md-8">
                                    <div class="bg-secondary rounded h-100 d-flex align-items-center justify-content-center text-white text-center p-4 position-relative">
                                        <video id="localVideo" class="w-100 h-100 rounded d-none" autoplay muted playsinline></video>
                                    <div>
                                        <i class="bi bi-person-video3 display-4 d-block mb-2"></i>
                                        <div class="fw-bold">Teacher Camera</div>
                                        <small class="text-white-50">{{ $liveClass->teacher->name }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row g-2 h-100">
                                    <div id="livekitRemoteTracks" class="row g-2"></div>
                                    @forelse($liveClass->participants->where('is_active', true) as $participant)
                                        <div class="col-12 participant-tile" data-participant-id="{{ $participant->id }}">
                                            <div class="bg-light rounded h-100 d-flex align-items-center justify-content-center text-dark p-3 text-center">
                                                <div>
                                                    <i class="bi {{ $participant->camera_on ? 'bi-camera-video' : 'bi-camera-video-off' }} d-block mb-2"></i>
                                                    <strong class="d-block">{{ $participant->user->name }}</strong>
                                                    <small>{{ $participant->is_muted ? 'Muted' : 'Microphone on' }}</small>
                                                    @if(auth()->user()->isTeacher() && $participant->user_id !== auth()->id())
                                                        <div class="d-flex justify-content-center gap-1 mt-2">
                                                            <form method="POST" action="{{ route('live-classes.participants.mute', [$liveClass, $participant]) }}">@csrf<button class="btn btn-sm btn-outline-secondary" type="submit">{{ $participant->is_muted ? 'Unmute' : 'Mute' }}</button></form>
                                                            <form method="POST" action="{{ route('live-classes.participants.remove', [$liveClass, $participant]) }}">@csrf<button class="btn btn-sm btn-outline-danger" type="submit">Remove</button></form>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12"><div class="bg-light rounded h-100 d-flex align-items-center justify-content-center text-dark p-3 text-center">No participants have joined yet.</div></div>
                                    @endforelse
                                    <div class="col-12" data-mock-participant-placeholder>
                                        <div class="bg-light rounded h-100 d-flex align-items-center justify-content-center text-dark p-3 text-center">
                                            <div>
                                                <i class="bi bi-mic-mute d-block mb-2"></i>
                                                Participant tile
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-3">
                        @if(auth()->user()->isStudent() || auth()->user()->isTeacher())
                            <button class="btn btn-outline-secondary btn-sm" id="toggleMic" type="button" aria-pressed="false"><i class="bi bi-mic"></i> Mute</button>
                            <button class="btn btn-outline-secondary btn-sm" id="toggleCamera" type="button" aria-pressed="true"><i class="bi bi-camera-video"></i> Camera</button>
                            <button class="btn btn-outline-secondary btn-sm" id="toggleScreen" type="button" aria-pressed="false"><i class="bi bi-display"></i> Share screen</button>
                            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#classroomChat"><i class="bi bi-chat-left-text"></i> Chat</button>
                            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#participantPanel"><i class="bi bi-people"></i> Participants</button>
                            <form method="POST" action="{{ route('live-classes.leave', $liveClass) }}">@csrf<button class="btn btn-outline-danger btn-sm" type="submit"><i class="bi bi-box-arrow-left"></i> Leave</button></form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3">
            <div class="card shadow-sm border-0 h-100 collapse show" id="classroomChat">
                <div class="card-header bg-white">
                    <strong>Classroom Chat</strong>
                </div>
                <div class="card-body p-0">
                    <div class="chat-panel p-3" style="max-height: 340px; overflow-y: auto;">
                        @forelse($liveClass->messages as $message)
                            <div class="mb-2">
                                <div class="small text-muted">{{ $message->user_name ?? optional($message->user)->name }} • {{ $message->created_at->format('g:i A') }}</div>
                                <div class="border rounded p-2 bg-light">{{ $message->message }}</div>
                            </div>
                        @empty
                            <div class="text-muted small">No messages yet.</div>
                        @endforelse
                    </div>
                    <div class="border-top p-3">
                        <form method="POST" action="{{ route('live-classes.messages.store', $liveClass) }}">
                            @csrf
                            <div class="input-group">
                                <input type="text" name="message" class="form-control" maxlength="1000" placeholder="Type a message..." required>
                                <button class="btn btn-primary" type="submit">Send</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm border-0 mt-3 collapse show" id="participantPanel">
                <div class="card-header bg-white"><strong>Participants ({{ $liveClass->participants->where('is_active', true)->count() }})</strong></div>
                <ul class="list-group list-group-flush small">
                    @foreach($liveClass->participants->where('is_active', true) as $participant)
                        <li class="list-group-item d-flex justify-content-between"><span>{{ $participant->user->name }}</span><span class="text-muted">{{ $participant->is_muted ? 'Muted' : 'Audio on' }}</span></li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const room = document.getElementById('live-classroom');
    if (!room || !navigator.mediaDevices) return;

    if (room.dataset.provider === 'livekit' && room.dataset.joinToken && room.dataset.providerUrl) {
        import('https://cdn.jsdelivr.net/npm/livekit-client@2.22.1/+esm').then(({Room, RoomEvent, Track}) => {
            const livekitRoom = new Room();
            const localVideo = document.getElementById('localVideo');
            const toggleMic = document.getElementById('toggleMic');
            const toggleCamera = document.getElementById('toggleCamera');
            const toggleScreen = document.getElementById('toggleScreen');
            const connectionStatus = document.getElementById('livekitConnectionStatus');
            const chatForm = document.querySelector('form[action*="/messages"]');
            const chatPanel = document.querySelector('.chat-panel');
            const providerUrl = room.dataset.providerUrl.replace(/^http/, 'ws');
            if (!localVideo || !toggleMic || !toggleCamera || !toggleScreen || !connectionStatus || !chatForm || !chatPanel) return;

            const remoteTracks = document.getElementById('livekitRemoteTracks');
            if (!remoteTracks) return;
            room.querySelectorAll('.participant-tile, [data-mock-participant-placeholder]').forEach((element) => element.remove());
            const attachLocalTrack = (track) => {
                const element = track.attach();
                element.classList.add('w-100', 'h-100', 'rounded');
                if (track.kind === Track.Kind.Video) {
                    localVideo.replaceWith(element);
                }
            };
            const attachRemoteTrack = (track, participant) => {
                const tile = document.createElement('div');
                tile.className = 'col-12 bg-light rounded p-2';
                tile.dataset.participantIdentity = participant.identity;
                const label = document.createElement('div');
                label.className = 'small fw-semibold mb-1';
                label.textContent = participant.name || participant.identity;
                tile.append(label, track.attach());
                remoteTracks.append(tile);
            };
            livekitRoom.on(RoomEvent.TrackSubscribed, (track, publication, participant) => attachRemoteTrack(track, participant));
            livekitRoom.on(RoomEvent.TrackUnsubscribed, (track) => track.detach().forEach((element) => element.remove()));
            livekitRoom.on(RoomEvent.LocalTrackPublished, (publication) => publication.track && attachLocalTrack(publication.track));
            livekitRoom.on(RoomEvent.ParticipantDisconnected, (participant) => remoteTracks.querySelector(`[data-participant-identity="${CSS.escape(participant.identity)}"]`)?.remove());
            livekitRoom.on(RoomEvent.Connected, () => { connectionStatus.textContent = 'Connected'; });
            livekitRoom.on(RoomEvent.Reconnecting, () => { connectionStatus.textContent = 'Reconnecting...'; });
            livekitRoom.on(RoomEvent.Reconnected, () => { connectionStatus.textContent = 'Connected'; });
            livekitRoom.on(RoomEvent.Disconnected, () => { connectionStatus.textContent = 'Disconnected'; });
            livekitRoom.on(RoomEvent.DataReceived, (payload, participant, kind, topic) => {
                if (topic !== 'chat') return;
                const message = JSON.parse(new TextDecoder().decode(payload));
                const item = document.createElement('div');
                item.className = 'mb-2';
                item.innerHTML = `<div class="small text-muted"></div><div class="border rounded p-2 bg-light"></div>`;
                item.firstElementChild.textContent = `${message.name} • ${message.time}`;
                item.lastElementChild.textContent = message.text;
                chatPanel.append(item);
                chatPanel.scrollTop = chatPanel.scrollHeight;
            });
            livekitRoom.connect(providerUrl, room.dataset.joinToken).then(async () => {
                await livekitRoom.localParticipant.enableCameraAndMicrophone();
                livekitRoom.localParticipant.videoTrackPublications.forEach(({track}) => track && attachLocalTrack(track));
                toggleMic.onclick = () => livekitRoom.localParticipant.setMicrophoneEnabled(!livekitRoom.localParticipant.isMicrophoneEnabled);
                toggleCamera.onclick = () => livekitRoom.localParticipant.setCameraEnabled(!livekitRoom.localParticipant.isCameraEnabled);
                toggleScreen.onclick = () => livekitRoom.localParticipant.setScreenShareEnabled(!livekitRoom.localParticipant.isScreenShareEnabled);
                chatForm.addEventListener('submit', async (event) => {
                    event.preventDefault();
                    const input = chatForm.querySelector('input[name="message"]');
                    const text = input.value.trim();
                    if (!text) return;
                    await livekitRoom.localParticipant.publishData(new TextEncoder().encode(JSON.stringify({name: @json(auth()->user()->name), text, time: new Date().toLocaleTimeString([], {hour: 'numeric', minute: '2-digit'})})), {reliable: true, topic: 'chat'});
                    input.value = '';
                });
            }).catch(() => {
                connectionStatus.textContent = 'Connection failed';
                room.dataset.connectionState = 'failed';
            });
        }).catch(() => {
            document.getElementById('livekitConnectionStatus').textContent = 'Provider unavailable';
            room.dataset.connectionState = 'provider-unavailable';
        });
        return;
    }

    let stream;
    let screenStream;
    const localVideo = document.getElementById('localVideo');
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    const toggleMic = document.getElementById('toggleMic');
    const toggleCamera = document.getElementById('toggleCamera');
    const toggleScreen = document.getElementById('toggleScreen');
    if (!localVideo || !csrfToken || !toggleMic || !toggleCamera || !toggleScreen) return;
    const csrf = csrfToken.content;

    const persist = (state) => fetch(room.dataset.mediaUrl, {
        method: 'PATCH',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json'},
        body: JSON.stringify(state)
    });

    const setButton = (id, active, onText, offText) => {
        const button = document.getElementById(id);
        button.setAttribute('aria-pressed', active ? 'true' : 'false');
        button.lastChild.textContent = active ? onText : offText;
    };

    navigator.mediaDevices.getUserMedia({audio: true, video: true}).then((mediaStream) => {
        stream = mediaStream;
        localVideo.srcObject = stream;
        localVideo.classList.remove('d-none');
        localVideo.nextElementSibling?.classList.add('d-none');
    }).catch(() => {
        document.getElementById('localVideo').setAttribute('aria-label', 'Camera and microphone unavailable');
    });

    toggleMic.addEventListener('click', () => {
        if (!stream) return;
        const track = stream.getAudioTracks()[0];
        track.enabled = !track.enabled;
        setButton('toggleMic', !track.enabled, 'Unmute', 'Mute');
        persist({is_muted: !track.enabled});
    });
    toggleCamera.addEventListener('click', () => {
        if (!stream) return;
        const track = stream.getVideoTracks()[0];
        track.enabled = !track.enabled;
        setButton('toggleCamera', track.enabled, 'Camera on', 'Camera off');
        persist({camera_on: track.enabled});
    });
    toggleScreen.addEventListener('click', async () => {
        if (screenStream) {
            screenStream.getTracks().forEach(track => track.stop());
            screenStream = null;
            setButton('toggleScreen', false, 'Stop sharing', 'Share screen');
            persist({screen_sharing: false});
            return;
        }
        try {
            screenStream = await navigator.mediaDevices.getDisplayMedia({video: true});
            setButton('toggleScreen', true, 'Stop sharing', 'Share screen');
            persist({screen_sharing: true});
            screenStream.getVideoTracks()[0].addEventListener('ended', () => {
                screenStream = null;
                setButton('toggleScreen', false, 'Stop sharing', 'Share screen');
                persist({screen_sharing: false});
            });
        } catch (error) {
            persist({screen_sharing: false});
        }
    });
})();
</script>
@endpush
