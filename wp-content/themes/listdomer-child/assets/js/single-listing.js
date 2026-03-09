(function () {
    'use strict';

    // ── Lightbox ─────────────────────────────────────────────────────────────

    var lb        = document.getElementById('mfl-lightbox');
    var lbImg     = document.getElementById('mfl-lb-img');
    var lbCounter = document.getElementById('mfl-lb-counter');
    var lbClose   = document.getElementById('mfl-lb-close');
    var lbPrev    = document.getElementById('mfl-lb-prev');
    var lbNext    = document.getElementById('mfl-lb-next');
    var thumbs    = Array.from(document.querySelectorAll('.mfl-sl-gallery-thumb'));
    var current   = 0;

    function openLightbox(index) {
        current = index;
        showSlide(current);
        lb.hidden = false;
        lb.focus();
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        lb.hidden = true;
        document.body.style.overflow = '';
        if (thumbs[current]) thumbs[current].focus();
    }

    function showSlide(index) {
        var thumb = thumbs[index];
        if (!thumb) return;
        lbImg.src = thumb.dataset.lightboxSrc || thumb.querySelector('img').src;
        lbImg.alt = thumb.querySelector('img').alt || '';
        lbCounter.textContent = (index + 1) + ' / ' + thumbs.length;
    }

    function prevSlide() { current = (current - 1 + thumbs.length) % thumbs.length; showSlide(current); }
    function nextSlide() { current = (current + 1) % thumbs.length; showSlide(current); }

    if (lb && thumbs.length) {
        thumbs.forEach(function (btn, i) {
            btn.addEventListener('click', function () { openLightbox(i); });
        });

        lbClose.addEventListener('click', closeLightbox);
        lbPrev.addEventListener('click',  prevSlide);
        lbNext.addEventListener('click',  nextSlide);

        lb.addEventListener('keydown', function (e) {
            if (e.key === 'Escape')     closeLightbox();
            if (e.key === 'ArrowLeft')  prevSlide();
            if (e.key === 'ArrowRight') nextSlide();
        });

        // Click outside image to close
        lb.addEventListener('click', function (e) {
            if (e.target === lb) closeLightbox();
        });
    }

    // ── Copy-link button ─────────────────────────────────────────────────────

    var copyBtn = document.querySelector('.mfl-sl-share__copy');
    if (copyBtn && navigator.clipboard) {
        copyBtn.addEventListener('click', function () {
            var url = copyBtn.dataset.copyUrl;
            navigator.clipboard.writeText(url).then(function () {
                var orig = copyBtn.textContent;
                copyBtn.textContent = 'Copied!';
                setTimeout(function () { copyBtn.textContent = orig; }, 2000);
            });
        });
    }

    // ── Auto-dismiss contact form notices ────────────────────────────────────

    var notice = document.querySelector('.mfl-sl-form-notice');
    if (notice) {
        setTimeout(function () {
            notice.style.transition = 'opacity 0.4s';
            notice.style.opacity = '0';
            setTimeout(function () { notice.remove(); }, 450);
        }, 5000);
    }

}());
