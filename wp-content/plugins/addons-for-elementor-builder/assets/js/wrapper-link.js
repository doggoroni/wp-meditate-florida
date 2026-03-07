jQuery(document).ready(function ($) {
    $('body').on('click', '.afeb-wrapper-link', function () {
        let $elm = $(this),
            settings = $elm.data('afeb-wrapper-link'),
            data = settings,
            id = 'afeb-wrapper-link-' + $elm.data('id');
        if ($('#' + id).length === 0) {
            $('body').append(
                $(document.createElement('a')).prop({
                    target: data.is_external ? '_blank' : '_self',
                    href: data.url,
                    style: 'display:none;',
                    id: id,
                    rel: data.nofollow ? 'nofollow noreferer' : ''
                })
            );
        }
        $('#' + id)[0].click();
    });
});
