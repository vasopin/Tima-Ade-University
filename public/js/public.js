/**
 * Tima-Ade University — Public Website Scripts & Interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    const homeVideo = document.querySelector('.home-hero-video');
    if (homeVideo && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        homeVideo.pause();
        homeVideo.removeAttribute('autoplay');
    }

    const desktopDropdownQuery = window.matchMedia('(min-width: 992px)');
    const dropdownParents = document.querySelectorAll('.public-header .navbar-nav > .dropdown > .dropdown-toggle');
    const closeDropdowns = function(except) {
        dropdownParents.forEach(function(toggle) {
            if (toggle !== except) {
                toggle.parentElement.classList.remove('is-hover-open');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });
    };
    dropdownParents.forEach(function(toggle) {
        const parent = toggle.parentElement;
        parent.addEventListener('mouseenter', function() {
            if (!desktopDropdownQuery.matches) return;
            parent.classList.remove('is-escape-closed');
            closeDropdowns(toggle);
            parent.classList.add('is-hover-open');
            toggle.setAttribute('aria-expanded', 'true');
        });
        parent.addEventListener('mouseleave', function() {
            if (!desktopDropdownQuery.matches) return;
            parent.classList.remove('is-hover-open');
            toggle.setAttribute('aria-expanded', 'false');
        });
        toggle.addEventListener('click', function(event) {
            if (desktopDropdownQuery.matches && event.detail > 0) {
                event.preventDefault();
                window.location.assign(toggle.href);
            }
        });
    });
    document.addEventListener('keydown', function(event) {
        if (event.key !== 'Escape') return;
        const openToggle = Array.from(dropdownParents).find(function(toggle) {
            return toggle.parentElement.classList.contains('is-hover-open') || toggle.getAttribute('aria-expanded') === 'true';
        });
        if (!openToggle) return;
        event.preventDefault();
        event.stopPropagation();
        openToggle.parentElement.classList.remove('is-hover-open');
        openToggle.parentElement.classList.add('is-escape-closed');
        openToggle.parentElement.querySelector('.dropdown-menu')?.classList.remove('show');
        window.bootstrap?.Dropdown.getInstance(openToggle)?.hide();
        openToggle.setAttribute('aria-expanded', 'false');
        openToggle.focus();
    }, true);

    // Keep public navigation usable if the optional Bootstrap bundle is unavailable.
    if (!window.bootstrap) {
        document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(function(toggle) {
            toggle.addEventListener('click', function(event) {
                event.preventDefault();
                const menu = toggle.parentElement?.querySelector('.dropdown-menu');
                if (!menu) return;
                const isOpen = menu.classList.toggle('show');
                toggle.setAttribute('aria-expanded', String(isOpen));
            });
        });

        document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(function(toggle) {
            toggle.addEventListener('click', function() {
                const target = document.querySelector(toggle.getAttribute('data-bs-target'));
                if (!target) return;
                const isOpen = target.classList.toggle('show');
                toggle.setAttribute('aria-expanded', String(isOpen));
            });
        });
    }

    // 1. Auto-dismiss alert notifications after 6 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            try {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            } catch (e) {}
        }, 6000);
    });

    // 2. Sticky Header elevation on scroll
    const header = document.querySelector('.public-header');
    if (header) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 40) {
                header.classList.add('shadow-md');
            } else {
                header.classList.remove('shadow-md');
            }
        });
    }

    // 3. Animated Number Counter for Stats Strip
    const counters = document.querySelectorAll('.history-stat-value[data-target]');
    if (counters.length > 0) {
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        const animateCounter = (el, target) => {
            if (prefersReducedMotion) {
                el.textContent = String(target);
                return;
            }

            const startTime = performance.now();
            const duration = 1200;

            const tick = (currentTime) => {
                const progress = Math.min((currentTime - startTime) / duration, 1);
                const easedProgress = 1 - Math.pow(1 - progress, 3);
                const currentValue = Math.round(target * easedProgress);
                el.textContent = String(currentValue);

                if (progress < 1) {
                    requestAnimationFrame(tick);
                } else {
                    el.textContent = String(target);
                }
            };

            requestAnimationFrame(tick);
        };

        const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const targetVal = Number(el.dataset.target);
                    if (Number.isFinite(targetVal)) {
                        animateCounter(el, targetVal);
                    }
                    obs.unobserve(el);
                }
            });
        }, { threshold: 0.45 });

        counters.forEach(c => observer.observe(c));
    }

    const historyCards = document.querySelectorAll('.history-stat-card.reveal-on-scroll');
    if (historyCards.length > 0) {
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (prefersReducedMotion) {
            historyCards.forEach(card => card.classList.add('is-visible'));
        } else if ('IntersectionObserver' in window) {
            const revealObserver = new IntersectionObserver((entries, obs) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        obs.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.2, rootMargin: '0px 0px -20px 0px' });

            historyCards.forEach(card => revealObserver.observe(card));
        } else {
            historyCards.forEach(card => card.classList.add('is-visible'));
        }
    }

    // 4. Reveal-on-scroll for About page cards and panels
    const revealElements = document.querySelectorAll('.about-reveal');
    if (revealElements.length > 0) {
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (prefersReducedMotion) {
            revealElements.forEach(el => el.classList.add('is-visible'));
        } else if ('IntersectionObserver' in window) {
            const revealObserver = new IntersectionObserver((entries, obs) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        obs.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.18, rootMargin: '0px 0px -20px 0px' });

            revealElements.forEach(el => revealObserver.observe(el));
        } else {
            revealElements.forEach(el => el.classList.add('is-visible'));
        }
    }

    // 5. Smooth scroll for internal hash links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId && targetId !== '#' && document.querySelector(targetId)) {
                e.preventDefault();
                document.querySelector(targetId).scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
});

// Home-only gallery and reveal interactions.
(function () {
    const home = document.querySelector('.home-page');
    if (!home) return;
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const storySections = home.querySelectorAll('main > section:not(.hero-section):not(.stats-counter-strip)');
    storySections.forEach((section, index) => {
        section.classList.add('home-section-reveal');
        section.style.setProperty('--section-reveal-delay', `${Math.min(index, 4) * 45}ms`);
    });
    if (reducedMotion || !('IntersectionObserver' in window)) {
        storySections.forEach(section => section.classList.add('is-visible'));
    } else {
        const sectionObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            });
        }, { threshold: .08, rootMargin: '0px 0px -40px' });
        storySections.forEach(section => sectionObserver.observe(section));
    }
    const revealItems = home.querySelectorAll('.home-image-card, .home-support-card, .home-research-feature, .home-research-side, .home-event-card, .home-testimonial-card, .home-gallery-item');
    revealItems.forEach((item, index) => { item.classList.add('home-reveal'); item.style.setProperty('--reveal-delay', `${Math.min(index % 6, 5) * 55}ms`); });
    if (reducedMotion || !('IntersectionObserver' in window)) { revealItems.forEach(item => item.classList.add('is-visible')); } else {
        const revealObserver = new IntersectionObserver((entries, observer) => { entries.forEach(entry => { if (entry.isIntersecting) { entry.target.classList.add('is-visible'); observer.unobserve(entry.target); } }); }, { threshold: .12, rootMargin: '0px 0px -30px' });
        revealItems.forEach(item => revealObserver.observe(item));
    }

    const parallaxVideos = home.querySelectorAll('[data-video-parallax]');
    if (parallaxVideos.length && !reducedMotion) {
        const updateParallax = () => {
            parallaxVideos.forEach((video) => {
                const rect = video.getBoundingClientRect();
                const drift = Math.max(-18, Math.min(18, (window.innerHeight - rect.top) * 0.04 - 14));
                video.style.transform = `translate3d(0, ${drift}px, 0) scale(1.04)`;
            });
        };
        updateParallax();
        window.addEventListener('scroll', updateParallax, { passive: true });
        window.addEventListener('resize', updateParallax);
    }
    const lightbox = document.getElementById('homeLightbox');
    const lightboxImage = document.getElementById('homeLightboxImage');
    const lightboxCaption = document.getElementById('homeLightboxCaption');
    let lastTrigger = null;
    const closeLightbox = () => { if (!lightbox) return; lightbox.classList.remove('is-open'); lightbox.setAttribute('aria-hidden', 'true'); if (lastTrigger) lastTrigger.focus(); };
    home.querySelectorAll('[data-gallery-src]').forEach(trigger => trigger.addEventListener('click', () => { lastTrigger = trigger; lightboxImage.src = trigger.dataset.gallerySrc; lightboxImage.alt = trigger.dataset.galleryAlt || ''; lightboxCaption.textContent = trigger.dataset.galleryAlt || ''; lightbox.classList.add('is-open'); lightbox.setAttribute('aria-hidden', 'false'); }));
    home.querySelector('[data-lightbox-close]')?.addEventListener('click', closeLightbox);
    lightbox?.addEventListener('click', event => { if (event.target === lightbox) closeLightbox(); });
    document.addEventListener('keydown', event => { if (event.key === 'Escape' && lightbox?.classList.contains('is-open')) closeLightbox(); });
})();

// Academics-only section reveal.
(function () {
    const academics = document.querySelector('.academics-page');
    if (!academics) return;
    const items = academics.querySelectorAll('.academics-reveal');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reducedMotion || !('IntersectionObserver' in window)) { items.forEach(item => item.classList.add('is-visible')); return; }
    const observer = new IntersectionObserver((entries, obs) => { entries.forEach(entry => { if (entry.isIntersecting) { entry.target.classList.add('is-visible'); obs.unobserve(entry.target); } }); }, { threshold: .14, rootMargin: '0px 0px -25px' });
    items.forEach(item => observer.observe(item));
})();
// Facilities-only reveal and image detail interaction.
(function () {
    const facilities = document.querySelector('.facilities-page');
    if (!facilities) return;
    const mapFrame = facilities.querySelector('[data-facility-map]');
    if (mapFrame) {
        const status = mapFrame.querySelector('[data-map-status]');
        const fallback = mapFrame.querySelector('[data-map-fallback]');
        const canvas = mapFrame.querySelector('[data-map-canvas]');
        const latitude = Number(mapFrame.dataset.campusLatitude);
        const longitude = Number(mapFrame.dataset.campusLongitude);
        const campusName = mapFrame.dataset.campusName || 'Tima-Ade University — Hargeisa Campus';
        const campusCity = mapFrame.dataset.campusCity || 'Hargeisa, Somaliland';
        const directionsUrl = `https://www.google.com/maps/dir/?api=1&destination=${latitude},${longitude}`;
        const showMapFallback = () => { status?.setAttribute('hidden', ''); if (fallback) fallback.hidden = false; };
        const directionsLink = mapFrame.querySelector('[data-directions-link]');
        if (directionsLink) directionsLink.href = directionsUrl;

        if (window.L && canvas && Number.isFinite(latitude) && Number.isFinite(longitude)) {
            const map = window.L.map(canvas, { scrollWheelZoom: true, dragging: true, touchZoom: true, doubleClickZoom: true, keyboard: true, zoomControl: true, tap: true }).setView([latitude, longitude], 15);
            canvas.style.touchAction = 'none';
            map.dragging.disable();
            let pointerDrag = null;
            canvas.addEventListener('pointerdown', event => {
                if ((event.button !== 0 && event.pointerType !== 'touch') || event.target.closest('.leaflet-control, .leaflet-marker-icon, .leaflet-popup')) return;
                pointerDrag = { id: event.pointerId, x: event.clientX, y: event.clientY };
                canvas.setPointerCapture(event.pointerId);
                canvas.classList.add('leaflet-grabbing');
                event.preventDefault();
            });
            canvas.addEventListener('pointermove', event => {
                if (!pointerDrag || pointerDrag.id !== event.pointerId) return;
                const offset = [event.clientX - pointerDrag.x, event.clientY - pointerDrag.y];
                pointerDrag.x = event.clientX;
                pointerDrag.y = event.clientY;
                map.panBy(offset, { animate: false });
                event.preventDefault();
            });
            const finishPointerDrag = event => {
                if (!pointerDrag || pointerDrag.id !== event.pointerId) return;
                if (canvas.hasPointerCapture(event.pointerId)) canvas.releasePointerCapture(event.pointerId);
                pointerDrag = null;
                canvas.classList.remove('leaflet-grabbing');
            };
            canvas.addEventListener('pointerup', finishPointerDrag);
            canvas.addEventListener('pointercancel', finishPointerDrag);
            const standard = window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap contributors' });
            const satellite = window.L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19, attribution: 'Tiles &copy; Esri' });
            standard.addTo(map);
            const markerIcon = window.L.divIcon({ className: 'facility-map-pin', html: '<span></span>', iconSize: [26, 34], iconAnchor: [13, 34], popupAnchor: [0, -30] });
            const marker = window.L.marker([latitude, longitude], { icon: markerIcon, title: campusName, alt: campusName }).addTo(map);
            let routeDirectionsUrl = directionsUrl;
            let userLocation = null;
            let userMarker = null;
            let routeLayer = null;
            let routeRequest = 0;
            let routeAbortController = null;
            let watchId = null;
            let lastRoutedLocation = null;
            let lastRouteRequestedAt = 0;
            let routeReloadTimer = null;
            let routeMode = 'walking';
            const navigationPanel = mapFrame.querySelector('[data-navigation-panel]');
            const routeSummary = mapFrame.querySelector('[data-route-summary]');
            const routeNext = mapFrame.querySelector('[data-route-next]');
            const routeNextText = routeNext?.querySelector('strong');
            const routeSteps = mapFrame.querySelector('[data-route-steps]');
            const routeStart = mapFrame.querySelector('[data-route-start]');
            const routeStop = mapFrame.querySelector('[data-route-stop]');
            let active = false;
            const routeModes = mapFrame.querySelectorAll('[data-route-mode]');
            const setRouteSummary = message => { if (routeSummary) routeSummary.textContent = message; };
            const locationStatus = mapFrame.querySelector('[data-location-status]');
            const setStatus = message => { if (locationStatus) { locationStatus.textContent = message; locationStatus.hidden = !message; } };
            const formatDistance = distance => distance >= 1000 ? `${(distance / 1000).toFixed(1)} km` : `${Math.round(distance)} m`;
            const formatDuration = duration => { const minutes = Math.max(1, Math.round(duration / 60)); return minutes >= 60 ? `${Math.floor(minutes / 60)} hr ${minutes % 60} min` : `${minutes} min`; };
            const stepInstruction = step => { const maneuver = step.maneuver || {}; const action = maneuver.type === 'depart' ? 'Start' : maneuver.type === 'arrive' ? 'Arrive at Tima-Ade University' : `${maneuver.type || 'Continue'}${maneuver.modifier ? ` ${maneuver.modifier}` : ''}`; return `${action}${step.name ? ` on ${step.name}` : ''}`; };
            const distanceBetween = (first, second) => { const earthRadius = 6371000; const latDelta = (second[0] - first[0]) * Math.PI / 180; const lonDelta = (second[1] - first[1]) * Math.PI / 180; const a = Math.sin(latDelta / 2) ** 2 + Math.cos(first[0] * Math.PI / 180) * Math.cos(second[0] * Math.PI / 180) * Math.sin(lonDelta / 2) ** 2; return earthRadius * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a)); };
            const routeEndpoint = mode => mode === 'walking' ? 'https://routing.openstreetmap.de/routed-foot/route/v1/foot' : 'https://router.project-osrm.org/route/v1/driving';
            const loadRoute = async () => {
                if (!userLocation) return;
                const requestId = ++routeRequest;
                routeAbortController?.abort();
                routeAbortController = new AbortController();
                lastRoutedLocation = [...userLocation];
                lastRouteRequestedAt = Date.now();
                setRouteSummary(`Calculating ${routeMode} route...`);
                const coordinates = `${userLocation[1]},${userLocation[0]};${longitude},${latitude}`;
                try {
                    const response = await fetch(`${routeEndpoint(routeMode)}/${coordinates}?overview=full&geometries=geojson&steps=true`, { signal: routeAbortController.signal });
                    if (!response.ok) throw new Error('Route request failed');
                    const data = await response.json();
                    const route = data.routes?.[0];
                    if (!route || requestId !== routeRequest) throw new Error('No route found');
                    if (active && distanceBetween(userLocation, [latitude, longitude]) <= 35) {
                        setRouteSummary('Arrived at Tima-Ade University');
                        if (routeNext && routeNextText) { routeNextText.textContent = 'You have arrived'; routeNext.hidden = false; }
                        if (watchId !== null && navigator.geolocation.clearWatch) navigator.geolocation.clearWatch(watchId);
                        watchId = null;
                        return;
                    }
                    if (routeLayer) routeLayer.remove();
                    routeLayer = window.L.geoJSON(route.geometry, { style: { color: '#d12f3d', weight: 6, opacity: .82, lineCap: 'round', lineJoin: 'round' } }).addTo(map);
                    const summary = `${routeMode === 'walking' ? 'Walking' : 'Driving'} · ${formatDistance(route.distance)} · ${formatDuration(route.duration)} to Tima-Ade University`;
                    setRouteSummary(active ? summary.replace(' to Tima-Ade University', '') : summary);
                    if (routeStart) routeStart.disabled = false;
                    if (routeSteps) {
                        routeSteps.innerHTML = '';
                        const steps = route.legs?.flatMap(leg => leg.steps || []).slice(0, 8) || [];
                        const nextStep = steps.find(step => !['depart', 'arrive'].includes(step.maneuver?.type)) || steps.find(step => step.maneuver?.type === 'arrive') || steps[0];
                        if (routeNext && routeNextText && nextStep) { routeNextText.textContent = stepInstruction(nextStep); routeNext.hidden = false; }
                        steps.forEach(step => { const item = document.createElement('li'); item.textContent = `${stepInstruction(step)}${step.distance ? ` · ${formatDistance(step.distance)}` : ''}`; routeSteps.appendChild(item); });
                        routeSteps.hidden = routeSteps.children.length === 0;
                    }
                    if (!active) map.fitBounds(routeLayer.getBounds(), { padding: [42, 42], maxZoom: 16, animate: true });
                } catch (error) {
                    if (error.name === 'AbortError') return;
                    if (requestId === routeRequest) { setRouteSummary('Unable to calculate a real route right now. Check your connection and try again.'); setStatus('The routing service is unavailable. No estimated distance or time is being shown.'); if (routeStart) routeStart.disabled = true; if (routeSteps) routeSteps.hidden = true; if (routeNext) routeNext.hidden = true; }
                }
            };
            const universityPopup = () => `<div class="facility-map-popup"><strong>Tima-Ade University</strong><span>Hargeisa Campus</span><span>${campusCity}</span><small>Latitude: ${latitude}<br>Longitude: ${longitude}</small><a href="${routeDirectionsUrl}" target="_blank" rel="noopener">Get Directions <i class="bi bi-arrow-up-right"></i></a></div>`;
            marker.bindPopup(universityPopup());
            window.L.control.layers({ 'Standard map': standard, 'Satellite view': satellite }, null, { position: 'topright', collapsed: true }).addTo(map);
            const locationControl = window.L.control({ position: 'bottomleft' });
            locationControl.onAdd = () => {
                const container = window.L.DomUtil.create('div', 'facility-map-location-control');
                const button = window.L.DomUtil.create('button', 'facility-map-location-button', container);
                button.type = 'button';
                button.setAttribute('aria-label', 'Show my location');
                button.innerHTML = '<i class="bi bi-crosshair2" aria-hidden="true"></i><span>My Location</span>';
                button.addEventListener('click', event => {
                    event.preventDefault();
                    event.stopPropagation();
                    if (!navigator.geolocation) { setStatus('Location is not available in this browser.'); return; }
                    if (!window.isSecureContext && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
                        setStatus('Location requires a secure connection. Open this page over HTTPS.');
                        return;
                    }
                    button.disabled = true;
                    button.classList.add('is-loading');
                    setStatus('Finding your location...');
                    const finishLocationRequest = () => { button.disabled = false; button.classList.remove('is-loading'); };
                    navigator.geolocation.getCurrentPosition(position => {
                        try {
                            userLocation = [position.coords.latitude, position.coords.longitude];
                            const userIcon = window.L.divIcon({ className: 'facility-map-user-pin', html: '<span></span>', iconSize: [22, 22], iconAnchor: [11, 11] });
                            if (userMarker) userMarker.setLatLng(userLocation); else userMarker = window.L.marker(userLocation, { icon: userIcon, title: 'Your location', alt: 'Your location' }).addTo(map);
                            userMarker.bindPopup('<div class="facility-map-popup"><strong>Your Location</strong><span>Current browser location</span><a href="' + `https://www.google.com/maps/dir/?api=1&origin=${userLocation[0]},${userLocation[1]}&destination=${latitude},${longitude}` + '" target="_blank" rel="noopener">Get Directions <i class="bi bi-arrow-up-right"></i></a></div>');
                            routeDirectionsUrl = `https://www.google.com/maps/dir/?api=1&origin=${userLocation[0]},${userLocation[1]}&destination=${latitude},${longitude}`;
                            marker.setPopupContent(universityPopup());
                            if (navigationPanel) navigationPanel.hidden = false;
                            navigationPanel?.classList.remove('is-active');
                            if (routeStart) routeStart.disabled = true;
                            loadRoute();
                            map.fitBounds(window.L.latLngBounds([userLocation, [latitude, longitude]]), { padding: [42, 42], maxZoom: 15, animate: true });
                            button.setAttribute('aria-label', 'Update my location');
                            setStatus('Your location is shown in blue.');
                        } catch (error) {
                            setStatus('Your location was found, but the map could not display it. Please try again.');
                        } finally {
                            finishLocationRequest();
                        }
                    }, error => {
                        finishLocationRequest();
                        const message = error.code === 1
                            ? 'Location permission is blocked. Allow location access for this site in Chrome, then try again.'
                            : error.code === 3
                                ? 'Location request timed out. Check your device location services and try again.'
                                : 'Your location is currently unavailable.';
                        setStatus(message);
                    }, { enableHighAccuracy: true, maximumAge: 0, timeout: 15000 });
                });
                window.L.DomEvent.disableClickPropagation(container);
                window.L.DomEvent.disableScrollPropagation(container);
                return container;
            };
            locationControl.addTo(map);
            routeModes.forEach(modeButton => modeButton.addEventListener('click', () => { if (!userLocation) return; routeModes.forEach(button => button.classList.toggle('is-active', button === modeButton)); routeMode = modeButton.dataset.routeMode; loadRoute(); }));
            routeStart?.addEventListener('click', () => {
                if (!userLocation || routeStart.disabled) return;
                active = true;
                navigationPanel?.classList.add('is-active');
                setRouteSummary(routeSummary?.textContent.replace(' to Tima-Ade University', '') || 'Navigation active');
                if (!navigator.geolocation.watchPosition) {
                    setStatus('Live location updates are unavailable in this browser. The current real location remains shown.');
                    return;
                }
                if (watchId === null) {
                    watchId = navigator.geolocation.watchPosition(nextPosition => {
                        const nextLocation = [nextPosition.coords.latitude, nextPosition.coords.longitude];
                        const movedDistance = lastRoutedLocation ? distanceBetween(lastRoutedLocation, nextLocation) : Infinity;
                        userLocation = nextLocation;
                        userMarker?.setLatLng(userLocation);
                        routeDirectionsUrl = `https://www.google.com/maps/dir/?api=1&origin=${userLocation[0]},${userLocation[1]}&destination=${latitude},${longitude}`;
                        marker.setPopupContent(universityPopup());
                        if (movedDistance < 15 || Date.now() - lastRouteRequestedAt < 5000) {
                            if (movedDistance >= 15 && !routeReloadTimer) {
                                routeReloadTimer = window.setTimeout(() => { routeReloadTimer = null; loadRoute(); }, 5000);
                            }
                            return;
                        }
                        loadRoute();
                    }, () => {
                        setStatus('Unable to update your current location. Check location permissions and services.');
                    }, { enableHighAccuracy: true, maximumAge: 0, timeout: 15000 });
                }
            });
            routeStop?.addEventListener('click', () => { if (watchId !== null && navigator.geolocation.clearWatch) navigator.geolocation.clearWatch(watchId); if (routeReloadTimer) window.clearTimeout(routeReloadTimer); routeReloadTimer = null; routeAbortController?.abort(); watchId = null; active = false; lastRoutedLocation = null; if (routeLayer) { routeLayer.remove(); routeLayer = null; } navigationPanel?.classList.remove('is-active'); if (navigationPanel) navigationPanel.hidden = true; if (routeNext) routeNext.hidden = true; if (routeSteps) routeSteps.hidden = true; setStatus('Navigation ended.'); });
            status?.setAttribute('hidden', '');
        } else {
            showMapFallback();
        }
    }
    const revealItems = facilities.querySelectorAll('.facilities-reveal');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reducedMotion || !('IntersectionObserver' in window)) { revealItems.forEach(item => item.classList.add('is-visible')); } else {
        const observer = new IntersectionObserver((entries, obs) => { entries.forEach(entry => { if (entry.isIntersecting) { entry.target.classList.add('is-visible'); obs.unobserve(entry.target); } }); }, { threshold: .12, rootMargin: '0px 0px -25px' });
        revealItems.forEach(item => observer.observe(item));
    }
    const lightbox = document.getElementById('facilityLightbox');
    const image = document.getElementById('facilityLightboxImage');
    const title = document.getElementById('facilityLightboxTitle');
    const description = document.getElementById('facilityLightboxDescription');
    let lastTrigger = null;
    const close = () => { if (!lightbox) return; lightbox.classList.remove('is-open'); lightbox.setAttribute('aria-hidden', 'true'); if (lastTrigger) lastTrigger.focus(); };
    facilities.querySelectorAll('[data-facility-image]').forEach(trigger => trigger.addEventListener('click', () => { lastTrigger = trigger; image.src = trigger.dataset.facilityImage; image.alt = `${trigger.dataset.facilityTitle} at Tima-Ade University`; title.textContent = trigger.dataset.facilityTitle; description.textContent = trigger.dataset.facilityDescription; lightbox.classList.add('is-open'); lightbox.setAttribute('aria-hidden', 'false'); document.querySelector('[data-facility-close]')?.focus(); }));
    facilities.querySelector('[data-facility-close]')?.addEventListener('click', close);
    lightbox?.addEventListener('click', event => { if (event.target === lightbox) close(); });
    document.addEventListener('keydown', event => { if (event.key === 'Escape' && lightbox?.classList.contains('is-open')) close(); });
})();