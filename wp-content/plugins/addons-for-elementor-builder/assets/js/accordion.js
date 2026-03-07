jQuery(document).ready(function ($) {
    $(document).on('click', '.afeb-accordion .afeb-items .afeb-item', function (e) {
        let el = $(this),
            description = el.find('.afeb-description'),
            itemsWrapper = el.closest('.afeb-items'),
            item = el.closest('.afeb-item')

        let isCurrentItem = item.hasClass('afeb-open')

        itemsWrapper.find('.afeb-open').removeClass('afeb-open')
        itemsWrapper.find('.afeb-item .afeb-description').slideUp()

        if (!isCurrentItem) {
            item.addClass('afeb-open')
            description.slideDown()
        }
    })
})