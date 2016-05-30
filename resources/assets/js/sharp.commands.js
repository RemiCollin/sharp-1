$(window).load(function () {

    // ---
    // Ajax command call
    // ---
    $("body.sharp-list a.command").click(function (e) {
        e.preventDefault();

        if($(this).data("confirm") && !confirm($(this).data("confirm"))) {
            return;
        }

        var url = $(this).attr("href");
        var $form = $(".form-command-" + $(this).data("command"));

        if($form.length) {
            // There's a form attach to this command.
            var $modal = $form.modal({});

            // Form init
            $modal.find('form').prop("action", url);
            $modal.find(".validation-error").remove();
            $modal.find(".has-error").removeClass("has-error");
            $modal.find('form')[0].reset();

            // Show modal form
            $modal.show();
            return;
        }

        sendCommand(url)
    });

    // ---
    // Ajax command call after filling form
    // ---
    $("body.sharp-list .form-command").submit(function (e) {
        e.preventDefault();

        var $form = $(this);
        var $modal = $(this).parents(".modal");

        $form.find(".validation-error").remove();
        $form.find(".has-error").removeClass("has-error");

        sendCommand(
            $(this).attr("action"),
            $(this).serialize(),
            function(data) {
                $modal.modal('hide');
                return true;

            }, function(jqXhr, json, errorThrown) {
                if (jqXhr.status == 422) {
                    var errors = jqXhr.responseJSON;

                    $.each(errors, function (key, value) {
                        var $field = $form.find(".sf-" + key);
                        $field.addClass("has-error");
                        $field.append('<span class="validation-error">' + value[0] + '</span>');
                    });
                }
                return true;

            });
    });
});

function sendCommand(url, params, successCallback, errorCallback) {

    showPageOverlay();

    $.ajax({
        url: url,
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr("content")
        },
        data: params,
        dataType: 'json',

        success: function(data) {
            if(!successCallback || successCallback(data)) {
                hidePageOverlay();
                window["handleCommandReturn_" + data.type](data);
            }
        },
        error: function (jqXhr, json, errorThrown) {
            if(!errorCallback || errorCallback(jqXhr, json, errorThrown)) {
                hidePageOverlay();
            }
        }
    });
}

function handleCommandReturn_ALERT(data) {
    sweetAlert(data.title, data.message, data.level);
}

function handleCommandReturn_VIEW(data) {
    showPageOverlay();

    var $body = $("body");
    var $cmdViewPanel = $("#command_view_panel");

    if(!$cmdViewPanel.length) {
        $cmdViewPanel = $('<div id="command_view_panel"><iframe style="width:100%; height:100%"></iframe></div>');
        $body.append($cmdViewPanel);
    }

    $cmdViewPanel.find("iframe").contents().find("body").html(data.html);

    $cmdViewPanel.animate({
        left:'2vw'
    });

    var $overlay = $(".sharp-page-overlay");

    var handler = function(event) {

        // If keydown event, only handle ESC
        if(event.type == "keydown" && event.which != 27) {
            return true;
        }

        // Hide panel
        $cmdViewPanel.animate({
            left:'110vw'
        }, 'fast');

        // Unbind event
        $overlay.unbind("click", handler);
        $body.unbind("keydown", handler);

        hidePageOverlay();
    };

    $overlay.bind("click", handler);
    $body.bind("keydown", handler);
}

function handleCommandReturn_RELOAD() {
    window.location.reload();
}

function handleCommandReturn_DOWNLOAD(data) {
    var $dllink = $("#sharp_command_download_link");

    $dllink.prop("href", $dllink.data("base") + "/" + data.file_path);
    $dllink[0].click();
}