(function ($, elementor) {
    'use strict';
    let afeb_widget_map = function ($scope, $) {
        let init = function ($scope, $) {
            let map = $scope.find('.afeb-map');
            if (!map.length) return;

            let settings = map.data('settings');
            let mrkrs = settings.mrkrs;
            let opts = settings.opts;
            let mp_prvdr = typeof opts.mp_prvdr != undefined ? opts.mp_prvdr : 'gmap';

            if (mp_prvdr == 'gmap') {
                gmap(opts, mrkrs);
            } else {
                let id = opts.el.replace('#', '');
                osmap(id, opts, mrkrs);
            }
        }

        let gmap = function (opts, mrkrs) {
            let gmap_obj = new GMaps(opts);

            for (let i in mrkrs) {
                gmap_obj.addMarker({
                    lat: mrkrs[i].lat,
                    lng: mrkrs[i].lng,
                    title: mrkrs[i].ttl,
                    infoWindow: {
                        content: '<strong>' + mrkrs[i].ttl + '</strong><br>' + mrkrs[i].cntnt
                    }
                });
            }

            if (mrkrs.length > 1)
                gmap_obj.fitZoom();
        }

        let osmap = function (id, opts, mrkrs) {
            try {
                let osmap = L.map(id, opts).setView(['', ''], 0);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(osmap,);
                lyrs(osmap, mrkrs);
            } catch (exc) {
                // alert("Error drawing map: " + exc);
            }
        }

        let lyrs = function (osmap, mrkrs) {
            mrkrs = gt_mrkrs(mrkrs);
            let objcts = [];

            for (let i = 0; i < mrkrs.length; i++) {
                let mrkr = mrkrs[i];
                let ttl = (mrkr.length > 2 && mrkr[2] != null) ? mrkr[2] : 'Marker #' + (i + 1);
                let dtls = (mrkr.length > 3 && mrkr[3] != null) ? mrkr[3] : '';
                let obj = drw_mrkr(osmap, mrkr[0], mrkr[1], ttl, dtls);

                objcts.push(obj);
            };

            map_zoom(osmap, objcts);
        }

        let gt_mrkrs = function (mrkrs) {
            let data = [];

            for (let i in mrkrs) {
                data.push([
                    mrkrs[i].lat,
                    mrkrs[i].lng,
                    mrkrs[i].ttl,
                    mrkrs[i].cntnt
                ]);
            }

            return data;
        }

        let drw_mrkr = function (osmap, lat, lng, ttl, dtls) {
            let cntnt = '<b>' + ttl + '</b><br/>' + dtls;
            let mrkr = L.marker([lat, lng]).addTo(osmap).bindPopup(cntnt);

            return mrkr;
        }

        let map_zoom = function (osmap, objcts) {
            let grp = new L.featureGroup(objcts);
            osmap.fitBounds(grp.getBounds());
        }

        init($scope, $);
    }

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/afeb_map.default', afeb_widget_map);
    });
}(jQuery, window.elementorFrontend));
