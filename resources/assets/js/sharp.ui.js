$(window).load(function () {

    // ---
    // Manage links on row click in entity list
    // ---
    $("body.sharp-list table .entity-row .entity-data").each(function () {
        if ($(this).data("link")) {
            $(this).click(function () {
                if ($("#entity-list.reorder").length) return;
                window.location = $(this).data("link");
            });

        } else {
            $(this).parents(".entity-row").addClass("inactive");
        }
    });

    // ---
    // Switch entity list to reorder mode
    // ---
    $("body.sharp-list #sharp-reorder").click(function () {
        $("body").addClass("reorder");

        $("table#entity-list tbody").sortable({
            items: '.entity-row',
            handle: ".reorder-handle",
            axis: "y",
            helper: function (e, tr) {
                var $originals = tr.children();
                var $helper = tr.clone();
                $helper.children().each(function (index) {
                    // Set helper cell sizes to match the original sizes
                    $(this).width($originals.eq(index).outerWidth());
                });
                return $helper;
            }
        });
    });

    // ---
    // Ajax reorder call and switch back to normal mode
    // ---
    $("body.sharp-list #sharp-reorder-ok").click(function (e) {

        // Out of reorder mode.
        e.preventDefault();
        $("body").removeClass("reorder");

        // Ajax call
        var url = $(this).attr("href");
        var tabIds = [];
        $("#entity-list .entity-row").each(function () {
            tabIds.push($(this).data("entity_id"));
        });

        $.post(url, {
            entities: tabIds,
            _token: getPostToken()
        }, function (data) {
            if (data.err) {

            }
        }, "json");
    });

    // ---
    // Show confirm on commands
    // ---
    $("a[data-confirm]").click(function() {
        return confirm($(this).data("confirm"));
    });

    // ---
    // Show confirm on delete entity click (with form post)
    // ---
    $("body.sharp-list .sharp-delete").click(function () {
        if (confirm($(this).data("confirmdelete"))) {
            $("form#" + $(this).data("form")).submit();
        }
    });

    // ---
    // Manage ajax calls for .ajax links
    // ---
    $("body#sharp .ajax").click(function (e) {
        e.preventDefault();

        var link = $(this);
        var url = link.attr("href");
        var success = link.data("success");
        var failure = link.data("failure");

        $.post(url, {
            _token: getPostToken()

        }, function (data) {
            if (data.err) {

            } else {
                window[success](link, data);
            }
        }, "json");
    });

    // ---
    // Manage form creation for commands with form
    // ---
    $("body.sharp-list .sharp-command.with-form").click(function (e) {
        e.preventDefault();

        var url = $(this).attr("href");

        $.get(url, function(formData) {
            var $modal = $(formData);
            $("#contenu").append($modal);
            $modal.modal({}).show();
        });

    });

});

function activate($source, jsonData) {
    $source.parents(".state").removeClass("state-inactive").addClass("state-active");
}

function deactivate($source, jsonData) {
    $source.parents(".state").removeClass("state-active").addClass("state-inactive");
}

function getPostToken() {
    return $("#formToken input[name=_token]").val();
}