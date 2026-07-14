(function ($)
{
    "use strict";

    function getMessageBox($form)
    {
        let $box = $form.find(".listdomer-logister-message");
        if (!$box.length)
        {
            $box = $('<div class="listdomer-logister-message"></div>');
            $form.prepend($box);
        }
        return $box;
    }

    function renderMessage($form, message, type)
    {
        const $box = getMessageBox($form);
        if (!message)
        {
            $box.html("");
            return;
        }

        let typeClass = "lsdr-info";
        if (type === "error") typeClass = "lsdr-error";
        if (type === "success") typeClass = "lsdr-success";

        const html = '<div class="lsdr-alert ' + typeClass + '">' + message + "</div>";
        $box.html(html);
    }

    function submitLogister($form)
    {
        const data = $form.serialize() + "&action=lsdrc_logister";

        renderMessage($form, "", "");
        $form.addClass("listdomer-loading");

        $.ajax({
            type: "POST",
            url: (window.lsdrcLogister && lsdrcLogister.ajaxUrl) ? lsdrcLogister.ajaxUrl : "",
            data: data,
            dataType: "json",
        }).done(function (response)
        {
            if (response && response.success)
            {
                if (response.data && response.data.message)
                {
                    renderMessage($form, response.data.message, "success");
                }

                if (response.data && response.data.redirect)
                {
                    setTimeout(function ()
                    {
                        window.location.href = response.data.redirect;
                    }, 2000);
                }
                return;
            }

            const message = response && response.data && response.data.message ? response.data.message : "";
            renderMessage($form, message, "error");
        }).fail(function (xhr, status, error)
        {
            renderMessage($form, error || "", "error");
        }).always(function ()
        {
            $form.removeClass("listdomer-loading");
        });
    }

    $(function ()
    {
        $(document).on("submit", "form[name=listdomer-loginform], form[name=listdomer-registerform]", function (event)
        {
            event.preventDefault();
            submitLogister($(this));
        });
    });
})(jQuery);
