$(function() {

    $('select.materialize-select').material_select();

    // datepicker
    var date_picker_options = {
        format: 'm-d-Y',
        readonly_element: false,
        onSelect: function() {
            $(this).trigger("change");
        }
    };

    $('input.date_picker').Zebra_DatePicker(date_picker_options);

    $('#login-form').on('beforeSubmit', function(e) {
        var form = $(this);
        var formData = form.serialize();
        var btns = form.find("[type=submit]");

        form.find(".form-summary").empty();
        btns.prop("disabled", "disabled");
        $("#login-progress").show();

        $.ajax({
            url: form.attr("action"),
            type: form.attr("method"),
            data: formData,
            dataType: "json",
            success: function (data) {
                var success = parseInt(data.success);

                $("#login-progress").hide();

                if(success) {
                    var successLabel = $("<div/>", {"class": "help-block help-block-success", "text": data.message ? data.message : "Form is submitted successfully"});
                    form.find(".form-summary").append(successLabel);
                    setTimeout(function() {
                        window.location = "/admin/";
                    }, 1000);
                } else {

                    btns.prop("disabled", "");

                    for(e in data.errors) {
                        var field = form.find(".field-" + e);
                        var errorLabel = $("<div/>", {"class": "help-block help-block-error", "text": data.errors[e]});
                        if(field.length) {
                            field.addClass("error").removeClass("has-success");
                            field.find(".input-field").append(errorLabel);
                        } else {
                            form.find(".form-summary").append(errorLabel);
                        }
                    }
                }
            },
            error: function () {

            }
        });

    }).on('submit', function(e){

        e.preventDefault();

    });

    $('#edit-form').on('beforeSubmit', function(e) {
        var form = $(this);
        var formData = form.serialize();
        var btns = form.find("[type=submit]");

        form.find(".form-summary").empty();
        btns.prop("disabled", "disabled");

        $("#edit-loader").addClass("active");

        $.ajax({
            url: form.attr("action"),
            type: form.attr("method"),
            data: formData,
            dataType: "json",
            success: function (data) {
                var success = parseInt(data.success);

                $("#edit-loader").removeClass("active");

                if(success) {
                    var successLabel = $("<div/>", {"class": "help-block help-block-success", "text": data.message ? data.message : "Form is submitted successfully"});
                    form.find(".form-summary").append(successLabel);

                    if(data.unblock) {
                        btns.prop("disabled", "");
                    }

                    if(data.redirectUrl) {
                        window.location = data.redirectUrl;
                    }

                } else {

                    btns.prop("disabled", "");

                    for(e in data.errors) {
                        var field = form.find(".field-" + e);
                        var errorLabel = $("<div/>", {"class": "help-block help-block-error", "text": data.errors[e]});
                        if(field.length) {
                            field.addClass("error").removeClass("has-success");
                            field.find(".input-field").append(errorLabel);
                        } else {
                            form.find(".form-summary").append(errorLabel);
                        }
                    }
                }
            },
            error: function () {
                console.log("Something went wrong");
            }
        });
    }).on('submit', function(e){
        e.preventDefault();
    });

    // search-form
    $("#search-form__type").change(function() {
        var val = $(this).val();
        $("#search-form").find("[data-type='" + val + "']").show().siblings().hide();
    });

    $("#search-form__type").trigger("change");

    // action on item
    $("#itemall").on("click", function() {
        var el = $(this);
        var checked = el.prop("checked");
        var table = el.parents("table:eq(0)").children("tbody");
        var rows = table.children("tr");
        var checkboxes = table.find(".item-checkbox");

        if(checked) {
            rows.addClass("selected");
            checkboxes.prop("checked", "checked");
        } else {
            rows.removeClass("selected");
            checkboxes.prop("checked", "");
        }

        showActions(table);
    });

    $(".table-content").on("click", ".item-checkbox", function() {
        var el = $(this);
        var parent = el.parents("tr:eq(0)");
        var checked = el.prop("checked");

        if(checked) {
            parent.addClass("selected");
        } else {
            parent.removeClass("selected");
        }

        showActions(parent.parents("table:eq(0)").children("tbody"));
    });

    function showActions(table) {
        var actions = table.parent().parent().prev();
        var selected = table.children("tr.selected");

        if(selected.length > 0) {
            actions.addClass("table-actions_active");
        } else {
            actions.removeClass("table-actions_active");
        }

        actions.find(".selected_count").text(selected.length);
    }
});