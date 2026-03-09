/**
 * Meditate Florida — Listing Archive
 * Handles: filter AJAX, URL state, view toggle, Leaflet map, geolocation.
 *
 * Requires: window.mflArchive (set inline in archive-listdom-listing.php)
 *   { ajaxUrl, nonce, archiveUrl, filters, mapPins, totalPages, currentPage,
 *     leafletCss, leafletJs }
 */
(function () {
    'use strict';

    if (typeof window.mflArchive === 'undefined') return;

    var cfg      = window.mflArchive;
    var xhr      = null;        // current in-flight AJAX request
    var leaflet  = null;        // L (Leaflet namespace)
    var map      = null;        // Leaflet map instance
    var markers  = [];          // current pin layer
    var geoLat   = 0;
    var geoLng   = 0;

    // ── DOM references ────────────────────────────────────────────────────────

    var form         = document.getElementById('mfl-filter-form');
    var sortSelect   = document.getElementById('mfl-sort');
    var cardsWrap    = document.getElementById('mfl-cards-wrap');
    var pagination   = document.getElementById('mfl-arch-pagination');
    var loading      = document.getElementById('mfl-arch-loading');
    var mapPane      = document.getElementById('mfl-arch-map');
    var sidebar      = document.getElementById('mfl-arch-sidebar');
    var mobileBtn    = document.getElementById('mfl-mobile-filter-btn');
    var ratingInput  = document.getElementById('mfl-rating-input');
    var openNow      = document.getElementById('mfl-open-now');

    // ── State (mirrors URL params) ────────────────────────────────────────────

    var state = Object.assign({
        city: '', category: 0, type: '', rating: 0,
        open_now: 0, sort: 'newest', view: 'grid', paged: 1,
        user_lat: 0, user_lng: 0,
    }, cfg.filters);

    // ── View toggle (persisted to localStorage) ───────────────────────────────
    // URL param takes priority (sharable links); localStorage used only when
    // no explicit ?view= is present (so returning users keep their preference).

    var savedView  = localStorage.getItem('mfl_view');
    var urlHasView = new URLSearchParams(window.location.search).has('view');
    if (!urlHasView && savedView && ['grid', 'list', 'map'].indexOf(savedView) !== -1) {
        state.view = savedView;
    }
    applyView(state.view, false);

    document.querySelectorAll('.mfl-view-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var v = btn.dataset.view;
            if (v === state.view) return;
            state.view = v;
            localStorage.setItem('mfl_view', v);
            applyView(v, true);

            if (v === 'map') {
                initMap(cfg.mapPins);
            }
        });
    });

    function applyView(v, triggerSearch) {
        // Update toolbar button states
        document.querySelectorAll('.mfl-view-btn').forEach(function (b) {
            var active = b.dataset.view === v;
            b.classList.toggle('is-active', active);
            b.setAttribute('aria-pressed', active ? 'true' : 'false');
        });

        // Update cards wrap class
        cardsWrap.className = 'mfl-cards-wrap mfl-cards-wrap--' + v;

        // Show/hide map pane
        if (v === 'map') {
            mapPane.hidden = false;
        } else {
            mapPane.hidden = true;
        }

        if (triggerSearch) {
            state.paged = 1;
            doSearch();
        }
    }

    // ── Filter form: auto-submit on change (debounced) ────────────────────────

    var debounceTimer;
    function debounce(fn, ms) {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fn, ms);
    }

    if (form) {
        // City, category radios, type radios — immediate change
        form.querySelectorAll('select, input[type="radio"]').forEach(function (el) {
            el.addEventListener('change', function () {
                state.paged = 1;
                collectFormState();
                debounce(doSearch, 120);
            });
        });

        // Open Now toggle
        if (openNow) {
            openNow.addEventListener('change', function () {
                state.open_now = openNow.checked ? 1 : 0;
                state.paged = 1;
                debounce(doSearch, 120);
            });
        }

        // Prevent full form submit — we handle via AJAX
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            collectFormState();
            state.paged = 1;
            doSearch();
        });
    }

    // Sort bar
    if (sortSelect) {
        sortSelect.addEventListener('change', function () {
            state.sort = sortSelect.value;
            state.paged = 1;

            // Distance sort: request geolocation first
            if (state.sort === 'distance') {
                requestGeolocation(doSearch);
            } else {
                doSearch();
            }
        });
    }

    // ── Star picker (custom radio behaviour) ─────────────────────────────────

    var starPicker = document.querySelector('.mfl-star-picker');
    if (starPicker && ratingInput) {
        var starLabels = starPicker.querySelectorAll('.mfl-star-picker__star');
        var clearRating = starPicker.querySelector('[data-clear-rating]');

        starLabels.forEach(function (lbl, i) {
            var radio = lbl.querySelector('input[type="radio"]');
            lbl.addEventListener('click', function () {
                var val = radio.value;
                ratingInput.value = val;
                state.rating = parseFloat(val);
                highlightStars(i + 1);
                state.paged = 1;
                debounce(doSearch, 120);
            });

            // Hover preview
            lbl.addEventListener('mouseenter', function () { highlightStars(i + 1); });
            starPicker.addEventListener('mouseleave', function () {
                highlightStars(state.rating);
            });
        });

        if (clearRating) {
            clearRating.addEventListener('click', function () {
                ratingInput.value = '';
                state.rating = 0;
                highlightStars(0);
                // Remove the hidden input value so it's not sent
                ratingInput.name = '';
                state.paged = 1;
                debounce(doSearch, 120);
            });
        }

        function highlightStars(count) {
            starLabels.forEach(function (lbl, i) {
                lbl.classList.toggle('is-active', i < count);
            });
        }
    }

    // ── Pagination: intercept clicks ──────────────────────────────────────────

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.mfl-page-btn[data-page]');
        if (!btn) return;
        e.preventDefault();
        state.paged = parseInt(btn.dataset.page, 10);
        doSearch();
        window.scrollTo({ top: cardsWrap.getBoundingClientRect().top + window.scrollY - 80, behavior: 'smooth' });
    });

    // ── Mobile sidebar toggle ─────────────────────────────────────────────────

    var backdrop = createBackdrop();

    if (mobileBtn && sidebar) {
        mobileBtn.addEventListener('click', function () {
            openSidebar();
        });
        backdrop.addEventListener('click', closeSidebar);
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeSidebar();
        });
    }

    function openSidebar() {
        sidebar.classList.add('is-open');
        backdrop.classList.add('is-open');
        mobileBtn.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        sidebar.classList.remove('is-open');
        backdrop.classList.remove('is-open');
        mobileBtn.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    }
    function createBackdrop() {
        var el = document.createElement('div');
        el.className = 'mfl-sidebar-backdrop';
        document.body.appendChild(el);
        return el;
    }

    // ── Collect form state into `state` ───────────────────────────────────────

    function collectFormState() {
        if (!form) return;
        var data = new FormData(form);
        state.city     = data.get('city') || '';
        state.category = parseInt(data.get('category') || '0', 10);
        state.type     = data.get('type') || '';
        state.rating   = parseFloat(data.get('rating') || '0') || 0;
        state.open_now = openNow && openNow.checked ? 1 : 0;
    }

    // ── Main AJAX search ──────────────────────────────────────────────────────

    function doSearch() {
        // Abort any in-flight request
        if (xhr) { xhr.abort(); xhr = null; }

        setLoading(true);

        // Build payload
        var params = new URLSearchParams({
            action:   'mfl_listings_search',
            nonce:    cfg.nonce,
            city:     state.city,
            category: state.category,
            type:     state.type,
            rating:   state.rating,
            open_now: state.open_now,
            sort:     state.sort,
            view:     state.view,
            user_lat: geoLat,
            user_lng: geoLng,
            paged:    state.paged,
        });

        xhr = new XMLHttpRequest();
        xhr.open('POST', cfg.ajaxUrl);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            setLoading(false);
            if (xhr.status !== 200) return;
            try {
                var res = JSON.parse(xhr.responseText);
                if (!res.success) return;
                var d = res.data;
                updateCards(d.html, d.total, d.pages, d.paged);
                updateMapPins(d.map_pins);
                pushUrlState();
            } catch (e) { /* ignore */ }
        };
        xhr.onerror = function () { setLoading(false); };
        xhr.send(params.toString());
    }

    // ── Update cards + pagination ─────────────────────────────────────────────

    function updateCards(html, total, pages, paged) {
        cardsWrap.innerHTML = html;

        // Rebuild windowed pagination
        if (pagination) {
            if (pages <= 1) {
                pagination.innerHTML = '';
            } else {
                // Build windowed page list with ellipsis sentinels (-1)
                var show = [1, pages];
                for (var i = Math.max(1, paged - 2); i <= Math.min(pages, paged + 2); i++) {
                    show.push(i);
                }
                show = show.filter(function(v, idx, arr) { return arr.indexOf(v) === idx; });
                show.sort(function(a, b) { return a - b; });

                var winPages = [];
                var prevP = null;
                show.forEach(function(p) {
                    if (prevP !== null && p - prevP > 1) winPages.push(-1);
                    winPages.push(p);
                    prevP = p;
                });

                var pgHtml = '';

                // Prev button
                if (paged > 1) {
                    pgHtml += '<a href="#" class="mfl-page-btn mfl-page-btn--prev" data-page="' + (paged - 1) + '" aria-label="Previous page">\u2039 Prev</a>';
                } else {
                    pgHtml += '<span class="mfl-page-btn mfl-page-btn--disabled" aria-disabled="true">\u2039 Prev</span>';
                }

                winPages.forEach(function(pg) {
                    if (pg === -1) {
                        pgHtml += '<span class="mfl-page-ellipsis" aria-hidden="true">\u2026</span>';
                    } else if (pg === paged) {
                        pgHtml += '<span class="mfl-page-btn mfl-page-btn--current" aria-current="page">' + pg + '</span>';
                    } else {
                        pgHtml += '<a href="#" class="mfl-page-btn" data-page="' + pg + '">' + pg + '</a>';
                    }
                });

                // Next button
                if (paged < pages) {
                    pgHtml += '<a href="#" class="mfl-page-btn mfl-page-btn--next" data-page="' + (paged + 1) + '" aria-label="Next page">Next \u203a</a>';
                } else {
                    pgHtml += '<span class="mfl-page-btn mfl-page-btn--disabled" aria-disabled="true">Next \u203a</span>';
                }

                pagination.innerHTML = pgHtml;
            }
        }

        // Update header count
        var sub = document.querySelector('.mfl-arch-header__sub');
        if (sub) {
            sub.innerHTML = total
                ? '<strong>' + total.toLocaleString() + '</strong> location' + (total !== 1 ? 's' : '') + ' found'
                  + (state.city ? ' in <strong>' + escHtml(state.city) + '</strong>' : '')
                : 'No locations match your current filters';
        }
    }

    // ── URL state: pushState so pages are shareable ───────────────────────────

    function pushUrlState() {
        var params = new URLSearchParams();
        if (state.city)        params.set('city',     state.city);
        if (state.category)    params.set('category', state.category);
        if (state.type)        params.set('type',     state.type);
        if (state.rating > 0)  params.set('rating',   state.rating);
        if (state.open_now)    params.set('open_now', '1');
        if (state.sort && state.sort !== 'newest') params.set('sort', state.sort);
        if (state.view && state.view !== 'grid')   params.set('view', state.view);
        if (state.paged > 1)   params.set('paged',   state.paged);

        var url = cfg.archiveUrl + (params.toString() ? '?' + params.toString() : '');
        history.pushState(state, '', url);
    }

    // Restore state on browser back/forward
    window.addEventListener('popstate', function (e) {
        if (!e.state) return;
        state = e.state;
        collectStateToForm(state);
        doSearch();
    });

    function collectStateToForm(s) {
        if (!form) return;
        var cityEl = form.querySelector('[name="city"]');
        if (cityEl) cityEl.value = s.city || '';

        form.querySelectorAll('[name="category"]').forEach(function (el) {
            el.checked = parseInt(el.value, 10) === parseInt(s.category, 10);
        });
        form.querySelectorAll('[name="type"]').forEach(function (el) {
            el.checked = el.value === s.type;
        });
        if (openNow) openNow.checked = !!s.open_now;
        if (ratingInput) ratingInput.value = s.rating || '';
        if (sortSelect) sortSelect.value = s.sort || 'newest';
    }

    // ── Geolocation ───────────────────────────────────────────────────────────

    function requestGeolocation(callback) {
        if (geoLat && geoLng) { callback(); return; }

        if (!navigator.geolocation) {
            showGeoError('Your browser does not support geolocation.');
            callback();
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function (pos) {
                geoLat = pos.coords.latitude;
                geoLng = pos.coords.longitude;
                state.user_lat = geoLat;
                state.user_lng = geoLng;
                callback();
            },
            function () {
                showGeoError('Location access denied. Showing newest results instead.');
                state.sort = 'newest';
                if (sortSelect) sortSelect.value = 'newest';
                callback();
            },
            { timeout: 8000 }
        );
    }

    function showGeoError(msg) {
        var notice = document.createElement('div');
        notice.className = 'mfl-geo-notice';
        notice.style.cssText = 'background:#FEF9EC;border:1px solid #D4A84B;color:#7A5C10;padding:8px 14px;border-radius:8px;font-size:0.85rem;margin-bottom:12px;';
        notice.textContent = msg;
        cardsWrap.parentNode.insertBefore(notice, cardsWrap);
        setTimeout(function () { notice.remove(); }, 6000);
    }

    // ── Leaflet map ───────────────────────────────────────────────────────────

    function initMap(pins) {
        if (map) {
            // Already initialized — just update pins
            updateMapPins(pins);
            return;
        }

        if (!window.L) {
            loadLeaflet(function () { buildMap(pins); });
        } else {
            leaflet = window.L;
            buildMap(pins);
        }
    }

    function loadLeaflet(callback) {
        // Load CSS
        var cssLink = document.createElement('link');
        cssLink.rel  = 'stylesheet';
        cssLink.href = cfg.leafletCss;
        document.head.appendChild(cssLink);

        // Load JS
        var script = document.createElement('script');
        script.src = cfg.leafletJs;
        script.onload = function () {
            leaflet = window.L;
            callback();
        };
        document.head.appendChild(script);
    }

    function buildMap(pins) {
        var L = leaflet;

        map = L.map('mfl-leaflet-map', {
            center: [27.9944, -81.7603], // Florida centroid
            zoom: 7,
            zoomControl: true,
            scrollWheelZoom: false,
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 19,
        }).addTo(map);

        placeMarkers(pins);
        autoFit();
    }

    function updateMapPins(pins) {
        if (!map || !leaflet) return;
        // Clear existing
        markers.forEach(function (m) { map.removeLayer(m); });
        markers = [];
        placeMarkers(pins);
        autoFit();
    }

    function placeMarkers(pins) {
        if (!map || !leaflet || !pins || !pins.length) return;
        var L = leaflet;

        var icon = L.divIcon({
            html: '<div class="mfl-map-pin" aria-hidden="true"></div>',
            className: '',
            iconSize: [24, 24],
            iconAnchor: [12, 24],
            popupAnchor: [0, -26],
        });

        pins.forEach(function (pin) {
            if (!pin.lat || !pin.lng) return;

            var starsHtml = '';
            if (pin.rating) {
                for (var i = 1; i <= 5; i++) starsHtml += i <= Math.round(pin.rating) ? '★' : '☆';
            }

            var popupHtml = '<div class="mfl-map-popup">'
                + (pin.img ? '<img src="' + escHtml(pin.img) + '" alt="" class="mfl-map-popup__img" loading="lazy">' : '')
                + '<p class="mfl-map-popup__title"><a href="' + escHtml(pin.url) + '">' + escHtml(pin.title) + '</a></p>'
                + (pin.address ? '<p class="mfl-map-popup__addr">' + escHtml(pin.address) + '</p>' : '')
                + (pin.rating  ? '<p class="mfl-map-popup__rating">' + starsHtml + ' ' + pin.rating.toFixed(1) + '</p>' : '')
                + '</div>';

            var marker = L.marker([pin.lat, pin.lng], { icon: icon })
                .bindPopup(popupHtml, { maxWidth: 220 });

            // Highlight corresponding card on pin click
            marker.on('click', function () {
                var card = document.querySelector('[data-listing-id="' + pin.id + '"]');
                if (card) {
                    card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    card.classList.add('mfl-card--highlighted');
                    setTimeout(function () { card.classList.remove('mfl-card--highlighted'); }, 2000);
                }
            });

            marker.addTo(map);
            markers.push(marker);
        });
    }

    function autoFit() {
        if (!map || !markers.length || !leaflet) return;
        var group = leaflet.featureGroup(markers);
        map.fitBounds(group.getBounds().pad(0.15));
    }

    // If map view was persisted on load, init immediately
    if (state.view === 'map') {
        applyView('map', false);
        initMap(cfg.mapPins);
    }

    // ── Loading state ─────────────────────────────────────────────────────────

    function setLoading(on) {
        loading.hidden = !on;
        cardsWrap.classList.toggle('is-loading', on);
    }

    // ── Utility ───────────────────────────────────────────────────────────────

    function escHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    // ── Map pin CSS (injected once) ───────────────────────────────────────────

    var pinStyle = document.createElement('style');
    pinStyle.textContent = [
        '.mfl-map-pin{',
        '  width:24px;height:24px;',
        '  background:var(--mfl-sage,#7C9E87);',
        '  border:2px solid #fff;',
        '  border-radius:50% 50% 50% 0;',
        '  transform:rotate(-45deg);',
        '  box-shadow:0 2px 6px rgba(0,0,0,.25);',
        '}',
        '.mfl-card--highlighted{',
        '  outline:3px solid var(--mfl-sage,#7C9E87);',
        '  outline-offset:2px;',
        '}',
    ].join('');
    document.head.appendChild(pinStyle);

}());
