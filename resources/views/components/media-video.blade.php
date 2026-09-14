{{-- Video Player Component --}}
{{-- 
     Usage:
     <x-media-video 
          video-src="path/to/video.mp4"
          poster-src="path/to/poster.jpg"
          title="Video Title"
          description="Video description text"
          :controls="true"
          :autoplay="false"
          :muted="false"
     />
--}}

<div class="video-container">
     {{-- Embedded iframe (for YouTube, Vimeo, etc.) --}}
     @if (isset($embedUrl) && $embedUrl)
          <iframe 
               class="video-player"
               src="{{ $embedUrl }}"
               title="{{ $title ?? 'University Video' }}"
               frameborder="0"
               allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
               allowfullscreen
               loading="lazy"
          ></iframe>
     @else
          {{-- HTML5 Video Player --}}
          <video 
               class="video-player"
               @if ($poster ?? false)
                    poster="{{ asset($poster) }}"
               @endif
               @if ($controls ?? true)
                    controls
               @endif
               @if ($autoplay ?? false)
                    autoplay
               @endif
               @if ($muted ?? false)
                    muted
               @endif
               @if ($loop ?? false)
                    loop
               @endif
          >
               @if (isset($videoSrc) && $videoSrc)
                    <source src="{{ asset($videoSrc) }}" type="video/mp4">
               @endif
               Your browser does not support the video tag.
          </video>

          {{-- Fallback Poster Play Button (if no native controls) --}}
          @if (!($controls ?? true) && ($poster ?? false))
               <img class="video-poster" src="{{ asset($poster) }}" alt="{{ $title ?? 'Video thumbnail' }}">
               <button class="video-play-button" aria-label="Play video">
                    <i class="bi bi-play-fill video-play-icon"></i>
               </button>
          @endif
     @endif
</div>

{{-- Video Metadata --}}
@if (isset($title) || isset($description))
     <div class="mt-3">
          @if (isset($title))
               <h3 class="video-title">{{ $title }}</h3>
          @endif
          @if (isset($description))
               <p class="video-description">{{ $description }}</p>
          @endif
     </div>
@endif

<script>
(function() {
     const videoContainer = document.currentScript.closest('.video-container');
     const playButton = videoContainer?.querySelector('.video-play-button');
     const videoElement = videoContainer?.querySelector('video');
     
     if (playButton && videoElement) {
          playButton.addEventListener('click', () => {
               videoElement.play();
               playButton.style.opacity = '0';
               playButton.style.pointerEvents = 'none';
          });

          videoElement.addEventListener('pause', () => {
               playButton.style.opacity = '1';
               playButton.style.pointerEvents = 'auto';
          });
     }

     // Lazy load iframe videos
     const iframes = videoContainer?.querySelectorAll('iframe');
     if (iframes) {
          iframes.forEach(iframe => {
               iframe.loading = 'lazy';
          });
     }
})();
</script>
