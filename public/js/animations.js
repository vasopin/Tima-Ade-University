/* ==========================================================================
   PHASE 3: SCROLL REVEAL & ANIMATION CONTROLLER
   Handles scroll-triggered animations, micro-interactions, and smooth effects
   ========================================================================== */

(function() {
     'use strict';

     // Configuration
     const CONFIG = {
          scrollThreshold: 0.1, // Reveal when 10% of element is visible
          debounceDelay: 100,
          rootMargin: '0px 0px -100px 0px'
     };

     // Scroll Reveal Observer
     const revealObserver = new IntersectionObserver((entries) => {
          entries.forEach(entry => {
               if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    // Optional: Stop observing after revealed (performance optimization)
                    // revealObserver.unobserve(entry.target);
               }
          });
     }, {
          threshold: CONFIG.scrollThreshold,
          rootMargin: CONFIG.rootMargin
     });

     // Initialize Scroll Reveals
     function initScrollReveals() {
          document.querySelectorAll('.reveal-on-scroll').forEach(element => {
               revealObserver.observe(element);
          });
     }

     // Staggered Animation Controller
     class StaggeredReveal {
          constructor(containerSelector, childSelector = '.reveal-on-scroll') {
               this.container = document.querySelector(containerSelector);
               this.children = this.container?.querySelectorAll(childSelector) || [];
               this.init();
          }

          init() {
               this.children.forEach((child, index) => {
                    child.style.transitionDelay = `${index * 100}ms`;
                    revealObserver.observe(child);
               });
          }
     }

     // Smooth Counter Animation (for statistics)
     class CounterAnimator {
          constructor(element) {
               this.element = element;
               this.target = parseInt(element.dataset.target) || parseInt(element.textContent);
               this.duration = parseInt(element.dataset.duration) || 2000;
               this.isAnimating = false;
          }

          animate() {
               if (this.isAnimating) return;
               this.isAnimating = true;

               const start = 0;
               const increment = this.target / (this.duration / 16); // 60fps
               let current = start;

               const counter = setInterval(() => {
                    current += increment;
                    if (current >= this.target) {
                         this.element.textContent = this.target.toLocaleString();
                         clearInterval(counter);
                         this.isAnimating = false;
                    } else {
                         this.element.textContent = Math.floor(current).toLocaleString();
                    }
               }, 16);
          }
     }

     // Initialize Counters
     function initCounters() {
          const counterObserver = new IntersectionObserver((entries) => {
               entries.forEach(entry => {
                    if (entry.isIntersecting && !entry.target.dataset.animated) {
                         const animator = new CounterAnimator(entry.target);
                         animator.animate();
                         entry.target.dataset.animated = 'true';
                    }
               });
          }, { threshold: 0.5 });

          document.querySelectorAll('[data-target]').forEach(counter => {
               counterObserver.observe(counter);
          });
     }

     // Parallax Scroll Effect
     class ParallaxScroll {
          constructor() {
               this.elements = document.querySelectorAll('.parallax-slow');
               this.init();
          }

          init() {
               window.addEventListener('scroll', () => this.update());
          }

          update() {
               this.elements.forEach(element => {
                    const rect = element.getBoundingClientRect();
                    const speed = element.dataset.speed || 0.5;
                    const yPos = window.pageYOffset * speed;
                    element.style.backgroundPosition = `center ${yPos}px`;
               });
          }
     }

     // Micro-interaction: Button States
     class ButtonInteraction {
          constructor() {
               this.initButtons();
          }

          initButtons() {
               document.querySelectorAll('.btn').forEach(btn => {
                    btn.addEventListener('mouseenter', (e) => this.onHover(e));
                    btn.addEventListener('mouseleave', (e) => this.onLeave(e));
               });
          }

          onHover(e) {
               const btn = e.target.closest('.btn');
               if (btn) {
                    btn.style.transform = 'translateY(-2px)';
               }
          }

          onLeave(e) {
               const btn = e.target.closest('.btn');
               if (btn) {
                    btn.style.transform = 'translateY(0)';
               }
          }
     }

     // Navigation Highlight on Scroll
     class NavHighlight {
          constructor(navSelector = '.navbar-nav', sectionSelector = '[id]') {
               this.nav = document.querySelector(navSelector);
               this.sections = document.querySelectorAll(sectionSelector);
               this.init();
          }

          init() {
               window.addEventListener('scroll', () => this.update());
               this.update();
          }

          update() {
               let current = '';

               this.sections.forEach(section => {
                    const sectionTop = section.offsetTop;
                    if (pageYOffset >= sectionTop - 200) {
                         current = section.getAttribute('id');
                    }
               });

               if (this.nav) {
                    this.nav.querySelectorAll('a').forEach(link => {
                         link.classList.remove('active');
                         if (link.getAttribute('href') === `#${current}`) {
                              link.classList.add('active');
                         }
                    });
               }
          }
     }

     // Form Input Animation
     class FormInteraction {
          constructor() {
               this.inputs = document.querySelectorAll('input, textarea, select');
               this.init();
          }

          init() {
               this.inputs.forEach(input => {
                    input.addEventListener('focus', () => this.onFocus(input));
                    input.addEventListener('blur', () => this.onBlur(input));
               });
          }

          onFocus(input) {
               input.parentElement?.classList.add('focused');
          }

          onBlur(input) {
               if (!input.value) {
                    input.parentElement?.classList.remove('focused');
               }
          }
     }

     // Debounce Helper
     function debounce(fn, delay) {
          let timeoutId;
          return function(...args) {
               clearTimeout(timeoutId);
               timeoutId = setTimeout(() => fn.apply(this, args), delay);
          };
     }

     // Throttle Helper
     function throttle(fn, delay) {
          let lastCall = 0;
          return function(...args) {
               const now = Date.now();
               if (now - lastCall > delay) {
                    fn.apply(this, args);
                    lastCall = now;
               }
          };
     }

     // Initialize Everything
     function init() {
          if (document.readyState === 'loading') {
               document.addEventListener('DOMContentLoaded', initAll);
          } else {
               initAll();
          }
     }

     function initAll() {
          // Initialize all animation systems
          initScrollReveals();
          initCounters();

          // Initialize interactive components
          new ButtonInteraction();
          new FormInteraction();

          // Optional: Initialize parallax if elements exist
          if (document.querySelectorAll('.parallax-slow').length > 0) {
               new ParallaxScroll();
          }

          // Optional: Initialize nav highlight if sections exist
          if (document.querySelectorAll('[id]').length > 0) {
               new NavHighlight();
          }

          // Log initialization in development
          console.log('✓ Animation system initialized');
     }

     // Export for use in other scripts
     window.AnimationSystem = {
          StaggeredReveal,
          CounterAnimator,
          ParallaxScroll,
          NavHighlight,
          ButtonInteraction,
          FormInteraction,
          debounce,
          throttle
     };

     // Initialize
     init();
})();

/* Additional Micro-Interaction Enhancements */
document.addEventListener('DOMContentLoaded', function() {
     // Smooth scroll for anchor links
     document.querySelectorAll('a[href^="#"]').forEach(anchor => {
          anchor.addEventListener('click', function(e) {
               const target = document.querySelector(this.getAttribute('href'));
               if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
               }
          });
     });

     // Active link styling for navigation
     const updateActiveLink = () => {
          const currentLocation = location.pathname;
          const menuItems = document.querySelectorAll('.navbar-nav a');
          
          menuItems.forEach(item => {
               item.classList.remove('active');
               if (item.getAttribute('href') === currentLocation) {
                    item.classList.add('active');
               }
          });
     };

     updateActiveLink();

     // Add loading state to forms
     document.querySelectorAll('form').forEach(form => {
          form.addEventListener('submit', function() {
               const submitBtn = this.querySelector('[type="submit"]');
               if (submitBtn && !submitBtn.disabled) {
                    submitBtn.disabled = true;
                    const originalText = submitBtn.textContent;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
                    
                    // Re-enable after 5 seconds (in case of error)
                    setTimeout(() => {
                         submitBtn.disabled = false;
                         submitBtn.textContent = originalText;
                    }, 5000);
               }
          });
     });

     // Reduce motion detection and compliance
     const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
     if (prefersReducedMotion) {
          document.documentElement.style.scrollBehavior = 'auto';
          document.querySelectorAll('[style*="animation"], [style*="transition"]').forEach(el => {
               el.style.animation = 'none';
               el.style.transition = 'none';
          });
     }
});
