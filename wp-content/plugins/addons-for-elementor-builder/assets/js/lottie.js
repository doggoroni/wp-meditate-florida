(function ($, elementor) {
    'use strict';
    let afeb_widgetLottie = function ($scope, $) {
        let init = function ($scope, $) {
            let $lottie = $scope.find('.afeb-lottie');
            if (!$lottie.length) return;

            let settings = $lottie.data('settings');
            let $lottie_by_id = document.getElementById($($lottie).attr('id'));
            let jsn_pth = settings.lti_src == 'mda' ? settings.lti_mda.url : settings.lti_url.url;
            let anim = lottie.loadAnimation({
                container: $lottie_by_id,
                path: jsn_pth,
                renderer: settings.lti_rndr,
                loop: settings.lti_lop == 'yes' ? true : false,
            });

            URL.revokeObjectURL(jsn_pth);

            anim.addEventListener('DOMLoaded', function (e) {
                let frst_frm = anim.firstFrame;
                let ttal_frm = anim.totalFrames;
                let get_frm_num_by_prcnt = function (prcnt) {
                    prcnt = Math.min(100, Math.max(0, prcnt));
                    return frst_frm + (ttal_frm - frst_frm) * prcnt / 100;
                }

                let strt_pnt_sze = parseInt(settings.lti_strt_pnt.size);
                let end_pnt_sze = parseInt(settings.lti_end_pnt.size);

                strt_pnt_sze = !isNaN(strt_pnt_sze) ? strt_pnt_sze : 0;
                end_pnt_sze = !isNaN(end_pnt_sze) ? end_pnt_sze : 100;

                let strt_pnt = get_frm_num_by_prcnt(strt_pnt_sze);
                let end_pnt = get_frm_num_by_prcnt(end_pnt_sze);

                anim.playSegments([strt_pnt, end_pnt], true);
            });

            let spd = parseFloat(settings.lti_ply_spd.size);
            spd = !isNaN(spd) ? spd : 1;

            anim.setSpeed(spd);
            anim.goToAndPlay(0);
        }

        init($scope, $);
    }

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/afeb_lottie.default', afeb_widgetLottie);
    });
}(jQuery, window.elementorFrontend));
