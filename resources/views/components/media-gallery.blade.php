{{-- Image Gallery Component with Lightbox --}}
{{-- 
     Usage:
     <x-media-gallery 
          :items="$items" 
          class="gallery-3col"
          modal-id="campus-gallery"
     />
     
     Expected $items format:
     [
          ['image' => 'path/to/image.jpg', 'alt' => 'description', 'title' => 'Image Title', 'caption' => 'Optional caption'],
          ...
     ]
--}}

@php($galleryId = $modalId ?? 'gallery-' . uniqid())
<div class="media-gallery {{ $class ?? 'gallery-3col' }}" id="{{ $galleryId }}" aria-label="{{ $ariaLabel ?? 'Image gallery' }}">
     @forelse ($items ?? [] as $index => $item)
          <button type="button" class="gallery-item" data-lightbox-index="{{ $index }}" data-lightbox-modal="{{ $galleryId }}" aria-label="Open {{ $item['title'] ?? 'gallery image' }} in lightbox">
               <img 
                    class="gallery-item-image" 
                    src="{{ asset($item['image'] ?? '') }}" 
                    alt="{{ $item['alt'] ?? 'Gallery image' }}"
                    loading="lazy"
               >
               <div class="gallery-item-overlay">
                    <i class="bi bi-search gallery-item-overlay-icon"></i>
               </div>
               @if (isset($item['title']) || isset($item['caption']))
                    <div class="gallery-item-caption">
                         @if (isset($item['title']))
                              <h3 class="gallery-item-caption-title">{{ $item['title'] }}</h3>
                         @endif
                         @if (isset($item['caption']))
                              <p class="gallery-item-caption-text">{{ $item['caption'] }}</p>
                         @endif
               @endif
          </button>
     @empty
          <div class="alert alert-info" role="alert">
               <i class="bi bi-info-circle me-2"></i> No gallery items available.
          </div>
     @endforelse
</div>

{{-- Lightbox Modal --}}
<div class="gallery-lightbox" id="lightbox-{{ $galleryId }}" role="dialog" aria-modal="true" aria-label="Image gallery viewer">
     <div class="gallery-lightbox-content">
          <button class="gallery-lightbox-close" aria-label="Close gallery">&times;</button>
          <img class="gallery-lightbox-image" src="" alt="">
          
          @if (count($items ?? []) > 1)
               <button class="gallery-lightbox-prev" aria-label="Previous image">&#10094;</button>
               <button class="gallery-lightbox-next" aria-label="Next image">&#10095;</button>
          @endif

          <div class="gallery-lightbox-info">
               <span class="lightbox-counter">1 / {{ count($items ?? []) }}</span>
          </div>
     </div>
</div>

<script>
(function() {
     const modalId = '{{ $galleryId }}';
     const galleryItems = document.querySelectorAll(`[data-lightbox-modal="${modalId}"]`);
     const lightbox = document.getElementById(`lightbox-${modalId}`);
     const lightboxImage = lightbox.querySelector('.gallery-lightbox-image');
     const lightboxClose = lightbox.querySelector('.gallery-lightbox-close');
     const prevBtn = lightbox.querySelector('.gallery-lightbox-prev');
     const nextBtn = lightbox.querySelector('.gallery-lightbox-next');
     const counter = lightbox.querySelector('.lightbox-counter');
     
     let currentIndex = 0;

     function openLightbox(index) {
          currentIndex = index;
          const item = galleryItems[index];
          const img = item.querySelector('.gallery-item-image');
          
          lightboxImage.src = img.src;
          lightboxImage.alt = img.alt;
          lightbox.classList.add('active');
          
          if (counter) {
               counter.textContent = `${index + 1} / ${galleryItems.length}`;
          }
     }

     function closeLightbox() {
          lightbox.classList.remove('active');
     }

     function showNext() {
          currentIndex = (currentIndex + 1) % galleryItems.length;
          openLightbox(currentIndex);
     }

     function showPrev() {
          currentIndex = (currentIndex - 1 + galleryItems.length) % galleryItems.length;
          openLightbox(currentIndex);
     }

     // Event listeners
     galleryItems.forEach((item, index) => {
          item.addEventListener('click', () => openLightbox(index));
     });

     lightboxClose.addEventListener('click', closeLightbox);
     lightbox.addEventListener('click', (e) => {
          if (e.target === lightbox) closeLightbox();
     });

     if (prevBtn) prevBtn.addEventListener('click', showPrev);
     if (nextBtn) nextBtn.addEventListener('click', showNext);

     // Keyboard navigation
     document.addEventListener('keydown', (e) => {
          if (lightbox.classList.contains('active')) {
               if (e.key === 'ArrowLeft' && prevBtn) showPrev();
               if (e.key === 'ArrowRight' && nextBtn) showNext();
               if (e.key === 'Escape') closeLightbox();
          }
     });

})();
</script>
