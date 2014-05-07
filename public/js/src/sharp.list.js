(function($)
{
    $.fn.sharp_list=function(options)
    {
        var defauts=
        {
            "sortable": false,
            "addable": false,
            "removable": false,
            "sortableItems": ".sharp-list-item",
            "addButton": {
                className: "btn",
                text: "add item"
            },
            "onDeleteItem": function(item) { return true; },
            "itemDeleted": function() { },
            "itemCreated": function() { },
            "itemsSorted": function(list) { }
        };

        var params = $.extend(defauts, options);

        return this.each(function()
        {
            var list = $(this);

            if(params.addable)
            {
                // Retrieve template
                var template = $(this).find(".sharp-list-item.template");

                // Add disabled attr to prevent sending the template data to the server
                template.find("input, select, textarea").attr("disabled", "disabled");

                // Hide template
                template.hide();

                // Create button "Add"
                var $addBtn = $('<a/>').addClass(params.addButton.className).addClass("sharp-list-add").html(params.addButton.text);
                var $addBtnListItem = $('<li/>').addClass("list-group-item sharp-list-item-add").append($addBtn);
                template.before($addBtnListItem);

                // Add an item
                $addBtn.click(function(e) {
                    e.preventDefault();
                    var $newItem = template.clone();
                    $newItem.addClass("sharp-list-item");

                    var key = "N_"+Math.random().toString(36).substr(2, 9);
                    $newItem.find("input, select, textarea").each(function() {
                        $(this).removeAttr("disabled");
                        var name = $(this).attr("name");
                        name = name.replace(/(--N--)/i, key);
                        $(this).attr("name", name);
                    });

                    $newItem.find(".sharp-list-item-id").val(key);

                    $addBtnListItem.before($newItem);
                    $newItem.slideDown();

                    // Call user callback
                    params.itemCreated($newItem);
                });
            }

            if(params.removable)
            {
                // Remove an item
                $(this).on("click", ".sharp-list-remove", function(e) {
                    e.preventDefault();
                    $item = $(this).parents(".sharp-list-item");
                    if(params.onDeleteItem($item)) {
                        $item.fadeOut("normal", function() {
                            // Call user callback
                            params.itemDeleted($item);
                        });
                    }
                });
            }

            // Sort items (uses jquery-ui sortable plugin)
            if(params.sortable)
            {
                $(this).sortable({
                    items: params.sortableItems,
                    helper: function(e, tr)
                    {
                        var $originals = tr.children();
                        var $helper = tr.clone();
                        $helper.children().each(function(index)
                        {
                            // Set helper cell sizes to match the original sizes
                            $(this).width($originals.eq(index).width());
                        });
                        return $helper;
                    },
                    update: function( event, ui )
                    {
                        params.itemsSorted(list);
                    }
                });
            }
        });
    };
})(jQuery);

$(window).load(function() {

    $('.sharp-list').each(function() {

        var params = {};
        if($(this).data("sortable") != undefined) params.sortable = $(this).data("sortable")==1;
        if($(this).data("removable") != undefined) params.removable = $(this).data("removable")==1;
        if($(this).data("addable") != undefined) params.addable = $(this).data("addable")==1;
        if($(this).data("add_button_text") != undefined) params.addButton = {
            text:'<i class="fa fa-plus"></i> ' + $(this).data("add_button_text"),
            className: 'btn'
        };

        $(this).sharp_list(
            $.extend(params, {
                itemCreated: function(item) {

                    // Manage sharp-file in the item
                    item.find('.sharp-file-template').each(function() {
                        $(this).removeClass('sharp-file-template').addClass('sharp-file');
                        createSharpFile($(this));
                    });

                    // Manage sharp-markdown in the file item
                    item.find('.sharp-markdown-template').each(function() {
                        $(this).removeClass('sharp-markdown-template').addClass('sharp-markdown');
                        createSharpMarkdown($(this));
                    });

                    // Manage sharp-date in the file item
                    item.find('.sharp-date-template').each(function() {
                        $(this).removeClass('sharp-date-template').addClass('sharp-date');
                        createSharpDate($(this));
                    });

                    // Manage sharp-ref in the file item
                    item.find('.sharp-ref-template').each(function() {
                        $(this).removeClass('sharp-ref-template').addClass('sharp-ref');
                        createSharpRef($(this));
                    });

                },
                itemDeleted: function(item) {
                    item.remove();
                }
            })
        );
    });
});