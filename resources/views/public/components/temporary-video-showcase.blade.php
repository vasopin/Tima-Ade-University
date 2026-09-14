<section class="temporary-video-showcase" aria-labelledby="temporary-video-showcase-title">
    <div class="container">
        <div class="temporary-video-showcase-heading">
            <div>
                <span class="temporary-video-kicker">Temporary media showcase</span>
                <h2 id="temporary-video-showcase-title">A closer look at campus life</h2>
                <p>Demo footage is shown temporarily while approved Tima-Ade University videos are being prepared.</p>
            </div>
            <span class="temporary-video-status"><i class="bi bi-info-circle" aria-hidden="true"></i> Demo footage</span>
        </div>

        @php($temporaryVideo = asset('videos/temporary-showcase-sample.mp4'))
        @php($temporaryVideos = [
            ['title' => 'Campus and university life', 'description' => 'A temporary visual sample for the campus showcase.', 'poster' => asset('images/home/campus.jpg')],
            ['title' => 'Learning and discovery', 'description' => 'A temporary visual sample for academic storytelling.', 'poster' => asset('images/home/classroom.jpg')],
            ['title' => 'Facilities and community', 'description' => 'A temporary visual sample for facilities and community highlights.', 'poster' => asset('images/home/library.jpg')],
        ])

        <div class="temporary-video-grid">
            @foreach($temporaryVideos as $index => $video)
                <article class="temporary-video-card">
                    <div class="temporary-video-frame">
                        <video
                            id="temporary-video-{{ $index }}"
                            preload="none"
                            controls
                            playsinline
                            poster="{{ $video['poster'] }}"
                            aria-label="Temporary demo video: {{ $video['title'] }}">
                            <source src="{{ $temporaryVideo }}" type="video/mp4">
                            Your browser does not support HTML5 video.
                        </video>
                        <button class="temporary-video-play" type="button" data-video-target="temporary-video-{{ $index }}" aria-controls="temporary-video-{{ $index }}" aria-label="Play {{ $video['title'] }}">
                            <i class="bi bi-play-fill" aria-hidden="true"></i>
                        </button>
                    </div>
                    <div class="temporary-video-card-copy">
                        <span class="temporary-video-card-label">Temporary demo</span>
                        <h3>{{ $video['title'] }}</h3>
                        <p>{{ $video['description'] }}</p>
                        <span class="temporary-video-caption-note"><i class="bi bi-badge-cc" aria-hidden="true"></i> Captions unavailable in supplied demo asset</span>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

<style>
.temporary-video-showcase{padding:5rem 0;background:linear-gradient(180deg,#eef5f1 0%,#f8fafc 100%);border-top:1px solid rgba(22,101,52,.12);border-bottom:1px solid rgba(22,101,52,.12)}
.temporary-video-showcase-heading{display:flex;align-items:end;justify-content:space-between;gap:2rem;margin-bottom:2rem}
.temporary-video-showcase-heading h2{max-width:650px;margin:.4rem 0 .7rem;color:#071b13;font-size:clamp(1.9rem,3vw,3rem);line-height:1.08;font-weight:800}
.temporary-video-showcase-heading p{max-width:650px;margin:0;color:#52645b;line-height:1.7}
.temporary-video-kicker,.temporary-video-card-label{display:block;color:#b4232f;font-size:.7rem;font-weight:800;letter-spacing:.14em;text-transform:uppercase}
.temporary-video-status{display:inline-flex;align-items:center;gap:.45rem;flex:0 0 auto;padding:.65rem .8rem;border:1px solid rgba(180,35,47,.2);color:#7f1d1d;background:#fff1f2;font-size:.75rem;font-weight:800}
.temporary-video-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1.25rem}
.temporary-video-card{overflow:hidden;border:1px solid rgba(148,163,184,.3);background:#fff;box-shadow:0 16px 36px rgba(15,23,42,.08)}
.temporary-video-frame{position:relative;aspect-ratio:16/10;background:#071b13}
.temporary-video-frame video{display:block;width:100%;height:100%;object-fit:cover}
.temporary-video-play{position:absolute;top:50%;left:50%;display:grid;place-items:center;width:3.4rem;height:3.4rem;border:1px solid rgba(255,255,255,.8);border-radius:50%;background:#fff;color:#b4232f;box-shadow:0 10px 24px rgba(0,0,0,.22);transform:translate(-50%,-50%);cursor:pointer}
.temporary-video-play:hover{background:#fff1f2}.temporary-video-play:focus-visible{outline:3px solid #fda4af;outline-offset:3px}.temporary-video-play i{font-size:1.55rem;margin-left:.15rem}
.temporary-video-card-copy{padding:1.25rem 1.25rem 1.35rem}.temporary-video-card-copy h3{margin:.4rem 0 .45rem;color:#10231d;font-size:1.15rem;line-height:1.25}.temporary-video-card-copy p{margin:0 0 .8rem;color:#617269;font-size:.88rem;line-height:1.6}.temporary-video-caption-note{display:flex;align-items:flex-start;gap:.4rem;color:#7b8790;font-size:.7rem;line-height:1.4}
@media (max-width:767.98px){.temporary-video-showcase{padding:3.75rem 0}.temporary-video-showcase-heading{display:block}.temporary-video-status{margin-top:1rem}.temporary-video-grid{grid-template-columns:1fr;gap:1rem}}
@media (prefers-reduced-motion:reduce){.temporary-video-play{transition:none}}
</style>

<script>
(function () {
    document.querySelectorAll('[data-video-target]').forEach(function (button) {
        var video = document.getElementById(button.getAttribute('data-video-target'));
        if (!video) return;
        var icon = button.querySelector('i');
        var title = video.getAttribute('aria-label').replace('Temporary demo video: ', '');
        var syncButton = function () {
            var playing = !video.paused && !video.ended;
            icon.className = playing ? 'bi bi-pause-fill' : 'bi bi-play-fill';
            button.setAttribute('aria-label', (playing ? 'Pause ' : 'Play ') + title);
            button.hidden = playing;
        };
        button.addEventListener('click', function () {
            if (video.paused) video.play().catch(function () {}); else video.pause();
            syncButton();
        });
        video.addEventListener('play', syncButton);
        video.addEventListener('pause', syncButton);
        video.addEventListener('ended', syncButton);
    });
})();
</script>
