/**
 * Meditate Florida — Homepage Hero
 *
 * Responsibilities:
 *  1. Lazy-load the Google Maps Places JS API
 *  2. Initialise Places Autocomplete on the city/zip input (Florida only)
 *  3. Capture lat/lng from the autocomplete selection → populate hidden fields
 *     so Listdom's circle-proximity search fires automatically
 *  4. Animate the hero background on load
 */
(function () {
    'use strict';

    /* Config injected via wp_localize_script() in functions.php */
    var cfg = window.mflHeroConfig || {};

    /* ── DOM references ──────────────────────────────────────────────────── */
    var form       = document.getElementById('mfl-hero-search-form');
    var cityInput  = document.getElementById('mfl-hero-city');
    var latInput   = document.getElementById('mfl-hero-lat');
    var lngInput   = document.getElementById('mfl-hero-lng');
    var radiusInput = document.getElementById('mfl-hero-radius');
    var shapeInput = document.getElementById('mfl-hero-shape');
    var hero       = document.querySelector('.mfl-hero');

    /* ── Hero background entrance ────────────────────────────────────────── */
    if (hero) {
        requestAnimationFrame(function () {
            hero.classList.add('is-loaded');
        });
    }

    /* ── Places Autocomplete ─────────────────────────────────────────────── */

    /**
     * Called by the Google Maps script tag's callback parameter once loaded.
     * We expose it on window so the async script tag can invoke it.
     */
    window.mflInitPlaces = function () {
        if (!cityInput || typeof google === 'undefined') return;

        // Restrict suggestions to Florida's bounding box for relevance.
        var floridaBounds = new google.maps.LatLngBounds(
            new google.maps.LatLng(24.3963, -87.6349), // SW
            new google.maps.LatLng(31.0010, -80.0314)  // NE
        );

        var autocomplete = new google.maps.places.Autocomplete(cityInput, {
            types:                ['(cities)'],
            componentRestrictions: { country: 'us' },
            bounds:               floridaBounds,
            strictBounds:         false,
            fields:               ['geometry', 'name', 'formatted_address'],
        });

        autocomplete.addListener('place_changed', function () {
            var place = autocomplete.getPlace();

            if (!place.geometry || !place.geometry.location) {
                // User typed something but didn't pick a suggestion — clear coords.
                resetGeoFields();
                return;
            }

            var lat = place.geometry.location.lat();
            var lng = place.geometry.location.lng();

            latInput.value    = lat.toFixed(6);
            lngInput.value    = lng.toFixed(6);
            radiusInput.value = cfg.defaultRadius || 30;   // km
            shapeInput.value  = 'circle';
        });

        // Clear geo fields whenever the user edits the city input manually.
        cityInput.addEventListener('input', resetGeoFields);
    };

    function resetGeoFields() {
        if (latInput)   latInput.value   = '';
        if (lngInput)   lngInput.value   = '';
        if (shapeInput) shapeInput.value = '';
    }

    /**
     * Dynamically load the Google Maps JS API with the Places library.
     * We do this lazily so it doesn't block page render.
     */
    function loadGoogleMapsScript() {
        if (!cfg.apiKey) return;
        if (typeof google !== 'undefined' && google.maps && google.maps.places) {
            window.mflInitPlaces();
            return;
        }

        var script   = document.createElement('script');
        script.src   = 'https://maps.googleapis.com/maps/api/js'
                     + '?key='       + encodeURIComponent(cfg.apiKey)
                     + '&libraries=places'
                     + '&callback=mflInitPlaces'
                     + '&loading=async';
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
    }

    /* ── Form behaviour ──────────────────────────────────────────────────── */

    if (form) {
        form.addEventListener('submit', function (e) {
            // If the user typed a city but never picked from autocomplete,
            // remove the shape param so Listdom does a keyword search instead
            // of a circle search at 0,0.
            if (latInput && !latInput.value) {
                if (shapeInput) shapeInput.value = '';
            }
        });
    }

    /* ── Boot ────────────────────────────────────────────────────────────── */

    // Start loading Maps API once the hero search field is focused,
    // or after 2 seconds — whichever comes first.
    if (cityInput) {
        var apiLoaded = false;

        function bootOnce() {
            if (apiLoaded) return;
            apiLoaded = true;
            loadGoogleMapsScript();
        }

        cityInput.addEventListener('focus', bootOnce, { once: true });
        cityInput.addEventListener('click', bootOnce, { once: true });
        setTimeout(bootOnce, 2000);
    }

}());
