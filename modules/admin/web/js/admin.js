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

    // drag
    $(".sortable").tableDnD({
        onDragClass: "ordering-progress",
        dragHandle: "ordering",
        onDrop: function(table, row) {

            var data = {
                "prev": $(row).prev().length ? $(row).prev().data("id"):0,
                "current": $(row).data("id"),
                "next": $(row).next().length ? $(row).next().data("id"):0
            };

            // Передаем данные на сервер
            $.post({
                url: $(table).data("href"),
                type: "POST",
                data: data,
                dataType: "json",
                success: function (data) {
                    for(d in data ) {
                        $(table).children("[data-id=" + data[d].id + "]").children(".priority").text(data[d].priority);
                    }
                    return false;
                },
                error: function () {
                    console.log("Something went wrong");
                    return false;
                }
            });
        }
    });

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
        var formData = new FormData(form[0]);
        //form.serialize();
        var btns = form.find("[type=submit]");

        form.find(".form-summary").empty();
        btns.prop("disabled", "disabled");

        $("#edit-loader").addClass("active");

        $.ajax({
            url: form.attr("action"),
            type: form.attr("method"),
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
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

    // activate links
    $(document).on("click", ".activate", function(e) {
        e.preventDefault();

        var el = $(this);
        var parent = el.parents("tr:eq(0)");
        var span = el.children("span");
        var data = [];

        var content = el.data("content");
        if(content > 0 && parent.hasClass("active")) {
            if (confirm(el.data("note")) == false) return false;
        }

        var csrfToken = $('meta[name="csrf-token"]').attr("content");

        data.push({
            name: "_csrf",
            value: csrfToken
        });

        $.post(el.attr("href"), data, function(r) {
            var row = el.parents("tr:eq(0)");
            var chx = row.find(".action-checkbox-el_active");

            if(r.status == 1) {
                span.text(span.data("active"));
                row.addClass("active");
            } else {
                span.text(span.data("nonactive"));
                row.removeClass("active");
            }

            if(chx.length) {
                var chx_el = chx.find("input:checkbox");
                var chx_label = chx.find(".action-checkbox-text");

                chx_el.prop("checked", r.status == 1 ? true:false);
                chx_label.text(chx_label.data(r.status == 1 ? "active":"nonactive"));
            }

            var rows = row.parent().find(".selected");
            if(row.length) {
                toggleActivateButton(rows);
                toggleDeActivateButton(rows);
            }


        }, "json");

        return false;
    });

    // action on item
    $("#itemall").on("click", function() {
        var el = $(this);

        if(el.hasClass("disabled")) {
            return false;
        }

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

    // action buttons

    $(".table-content").on("click", ".item-checkbox", function() {
        var el = $(this);
        var parent = el.parents("tr:eq(0)");
        var checked = el.prop("checked");

        if(parent.hasClass("edit") || parent.hasClass("locked")) {
            return false;
        }

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

            toggleActivateButton(selected);
            toggleDeActivateButton(selected);

        } else {
            $("#itemall").prop("checked", false);
            actions.removeClass("table-actions_active");
        }

        actions.find(".selected_count").text(selected.length);
    }

    // TODO lock dropdowns when edit

    $("#activate-action").on("click", function() {
        var el = $(this);
        var rows = findSelectedRows(el);

        if(!rows.length) {
            return;
        }

        var data = getIdsData(rows);

        $.post(el.data("href"), data, function(r) {

            rows.addClass("active");
            toggleActivateButton(rows);
            toggleDeActivateButton(rows);

            var activeLabels = rows.find(".action-checkbox-el_active").find(".action-checkbox-text");
            activeLabels.each(function(key, val) {
                $(val).text($(val).data("active"));

            });

            var activeLinks = rows.find(".activate");
            activeLinks.each(function(key, val) {
                var span = $(val).find("span");
                span.text(span.data("active"));
            });

        }, "json");
    });

    $("#deactivate-action").on("click", function() {
        var el = $(this);
        var rows = findSelectedRows(el);

        if(!rows.length) {
            return;
        }

        // find not empty categories
        var content = 0;

        rows.each(function(key, val) {
           content += parseInt($(val).data("content"));
        });

        if(content > 0) {
            if (confirm(el.data("note")) == false) return false;
        }

        var data = getIdsData(rows);

        $.post(el.data("href"), data, function(r) {

            rows.removeClass("active");
            toggleActivateButton(rows);
            toggleDeActivateButton(rows);

            var activeLabels = rows.find(".action-checkbox-el_active").find(".action-checkbox-text");
            activeLabels.each(function(key, val) {
                $(val).text($(val).data("nonactive"));
            });

            var activeLinks = rows.find(".activate");
            activeLinks.each(function(key, val) {
                var span = $(val).find("span");
                span.text(span.data("nonactive"));
            });

        }, "json");
    });

    $("#edit-action").on("click", function() {
        var el = $(this);
        var rows = findSelectedRows(el);

        if(!rows.length) {
            return;
        }

        rows.addClass("edit").siblings().addClass("locked");

        $("#itemall").addClass("disabled");

        el.parent().removeClass("table-actions__buttons_active").next().addClass("table-actions__buttons_active");

    });

    $("#cancel-edit-action").on("click", function() {
        var el = $(this);

        var rows = findSelectedRows(el);

        returnSelectedState(el, rows);

        var errorsContainer = el.parents(".table-actions:eq(0)").find(".table-actions__errors");
        errorsContainer.empty();

        // rollback
        rows.each(function(key, val) {

            var inputs = $(val).find(".action-input");
            inputs.each(function(ikey, ival) {
               $(ival).val($(ival).prev().val());
            });

            var chcks = $(val).find(".action-checkbox-el");
            chcks.each(function(ikey, ival) {
                var chk = $(ival).find(".action-checkbox");
                chk.prop("checked", chk.prev().val() == "1" ? "checked":"");
            });

        });
    });

    $("#apply-edit-action").on("click", function() {
        var el = $(this);
        var errorsContainer = el.parents(".table-actions:eq(0)").find(".table-actions__errors");
        var rows = findSelectedRows(el);

        var data = [];

        errorsContainer.empty();

        rows.each(function(key, val) {

            data.push({
                name: "ids[]",
                value: $(val).find(".item-checkbox").val()
            });

            var inputs = $(val).find(".action-input");
            inputs.each(function(ikey, ival) {
                data.push({
                    name: $(ival).attr("name") + "[]",
                    value: $(ival).val()
                });
            });

            var chcks = $(val).find(".action-checkbox-el");
            chcks.each(function(ikey, ival) {
                var chk = $(ival).find(".action-checkbox");
                data.push({
                    name: chk.attr("name") + "[]",
                    value: chk.prop("checked") ? 1:0
                });
            });

        });

        data.push({
            name: "_csrf",
            value: $('meta[name="csrf-token"]').attr("content")
        });

        $.post(el.data("href"), data, function(r) {
            if(r.success) {

                // apply changes
                rows.each(function(key, val) {
                    var activeLinkStatus = null;
                    var isActive = false;
                    var inputs = $(val).find(".action-input");
                    inputs.each(function(ikey, ival) {
                        var inputVal = $(ival).val();
                        $(ival).prev().val(inputVal).end().parent().next().text(inputVal);
                    });

                    var chcks = $(val).find(".action-checkbox-el");
                    chcks.each(function(ikey, ival) {
                        var chck = $(ival).find(".action-checkbox");
                        var chckLabel = $(ival).find(".action-checkbox-text");
                        var chckStatus = chck.prop("checked");

                        chck.prev().val(chckStatus ? 1:0);
                        chckLabel.text(chckLabel.data(chckStatus ? "active":"nonactive"));


                        if(chck.attr("name") == "active") {
                            activeLinkStatus = chckStatus ? "active":"nonactive";
                            isActive = chckStatus ? true:false;
                        }
                    });

                    var activeLink = $(val).find(".activate span");
                    activeLink.text(activeLink.data(activeLinkStatus));

                    if(isActive) {
                        $(val).addClass("active");
                    } else {
                        $(val).removeClass("active");
                    }
                });

                toggleActivateButton(rows);
                toggleDeActivateButton(rows);
                returnSelectedState(el, rows);

            } else {

                // display errors
                errorsContainer.html("<i class=\"table-actions__errors-close material-icons small\">clear</i>");

                for(e in r.errors) {
                    var errorContainer = $("<ul/>", {"class": "table-actions__errors-container"});

                    var errorHeader = $("<li/>", {"text": "ID: " + e, "class": "table-actions__errors-header"});
                    errorContainer.append(errorHeader);

                    for(key in r.errors[e]) {
                        var errorLabel = $("<li/>", {"text": r.errors[e][key], "class": "table-actions__errors-value"});
                        errorContainer.append(errorLabel)
                    }

                    errorsContainer.append(errorContainer);
                }

            }
        }, "json");
    });

    $(document).on("click", ".table-actions__errors-close", function() {
        var el = $(this);

        el.parent().empty();
    });

    function findSelectedRows(el) {
        return el.parents(".table-actions:eq(0)").next().find("tbody").find(".selected");
    }

    function getIdsData(rows) {
        var data = [];

        rows.each(function(key, val) {
            data.push({
                name: "ids[]",
                value: $(val).find(".item-checkbox").val()
            });
        });

        var csrfToken = $('meta[name="csrf-token"]').attr("content");

        data.push({
            name: "_csrf",
            value: csrfToken
        });

        return data;
    }

    function toggleActivateButton(rows) {
        var active = rows.filter(".active");

        if(active.length > 0) {
            $("#deactivate-action").show();
        } else {
            $("#deactivate-action").hide();
        }
    }


    function toggleDeActivateButton(rows) {
        var notActive = rows.filter(":not(.active)");

        if(notActive.length > 0) {
            $("#activate-action").show();
        } else {
            $("#activate-action").hide();
        }
    }

    function returnSelectedState(el, rows) {
        rows.removeClass("edit").siblings().removeClass("locked");

        el.parent().removeClass("table-actions__buttons_active").prev().addClass("table-actions__buttons_active");

        $("#itemall").removeClass("disabled");
    }
});