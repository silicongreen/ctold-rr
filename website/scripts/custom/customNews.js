/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function() {

    $('.fileUpload').liteUploader({
        script: $("#base_url").val() + "admin/news/attach_file/",
        params: {
            tds_csrf: $('input[name$="tds_csrf"]').val()
        }
    }).on('lu:success', function(e, response) {
        $("#prograssbar").hide("slow");
        if (response == 0)
        {
            alert("Invalid file type");
        }
        else
        {
            var upload_path = "upload/attach_file/";
            var $title = "<a style='float:left;margin-top:15px' href='" + $("#base_url").val() + upload_path + response + "'>" + response + "</a>";
            var $captionbox = "<input style='float:left;margin-top:15px;margin-left:15px;' type='text' name='attach_caption[]' size='40'>";
            var $select = '<select style="float:left;margin-left:20px;margin-top:14px;" name="attach_checked[]"><option value="1">Show</option><option value="0">Hide</option></select>'
            var $html = '<div style="float:left;height:30px; padding:5px;clear:both; width:100%;">' + $title + $select+$captionbox+'<input type="hidden" name="attach[]" value="' + upload_path + response + '"><a style="float:right;position:relative;" class="text-remove"></a></div>';
            $("#file_div_box").append($html);
        }

    }).on('lu:progress', function(e, percentage) {

        $("#prograssbar").show();
        $("#prograssbar").css("width", percentage + "%");
        $("#prograssbar").html(percentage + "%");
        
    });
    
    
    $('.videoUpload').liteUploader({
        script: $("#base_url").val() + "admin/news/attach_video/",
        params: {
            tds_csrf: $('input[name$="tds_csrf"]').val()
        }
    }).on('lu:success', function(e, response) {
        $("#prograssbarvideo").hide("slow");
        if (response == 0)
        {
            alert("Invalid file type");
        }
        else
        {
            var upload_path = "upload/attach_video/";
            var $title = '<video style="float:left; margin-bottom:5px;" width="200px" controls><source src="' + $("#base_url").val() + upload_path + response +'" type="video/mp4">Browser unsuportad</video>';
            
            var $html = '<div style="float:left;padding:5px;clear:both; width:100%;">' + $title +'<input type="hidden" name="video_file" value="' + upload_path + response + '"><a style="float:right;position:relative;" class="text-remove"></a></div>';
            $("#video_div_box").html($html);
        }

    }).on('lu:progress', function(e, percentage) {

        $("#prograssbarvideo").show();
        $("#prograssbarvideo").css("width", percentage + "%");
        $("#prograssbarvideo").html(percentage + "%");
        
    });




    if ($('input[name$="id"]').val() == 0)
    {

//        $('#valid_check_news').sayt({
//            'autosave': true, 
//            'autorecover': true, 
//            'days': 1
//        });

    }

    function setRadio(id)
    {
        var radio = $('#' + id);
        radio[0].checked = true;
        radio.button("refresh");
    }
    function priority_check(priority)
    {
        if (priority == 1)
        {
            setRadio("carousel_news");
        }
        else if (priority == 2)
        {
            setRadio("main_news");
        }
        else if (priority == 3)
        {
            setRadio("other_news");
        }
        else if (priority == 4)
        {
            setRadio("more_news");
        }
        else
        {
            setRadio("na_news");
        }
    }

    if ($.cookie("publish_date_cookie_value") && $("#published_date").val() == "")
    {
        var date_string = $.cookie("publish_date_cookie_value");
        var date_array = date_string.split(" ");
        date_string = date_array[0] + " 00:00";
        $("#published_date").val(date_string);
    }
    else if ($("#published_date").val() == "")
    {
        $("#published_date").val($("#current_date_for_publish").val());
    }

    if ($.cookie("publish_date_cookie_value") && $("#published_date_button").val() == "")
    {
        var date_string_button = $.cookie("publish_date_cookie_value");
        var date_array_button = date_string_button.split(" ");
        date_string_button = date_array_button[0] + " 00:00";
        $("#published_date_button").val(date_string_button);
    }
    else if ($("#published_date_button").val() == "")
    {
        $("#published_date_button").val($("#current_date_for_publish").val());
    }

    $(document).on("click", "#published_date_button", function()
    {

        if ($("#publish_date_div").css("display") == "none")
        {
            $("#publish_date_div").show();
        }
        else
        {
            $("#publish_date_div").hide();
        }
    });
    $(document).on("change", "#category_id_for_subcategory", function()
    {
        var id = $(this).val();
        $.ajax({
                    type: "GET",
                    url: $("#base_url").val() + "admin/news/getsubcategory/"+id,
                    async: false,
                    success: function(data) {
                       $("#subcategory_id_div").html(data);
                    }
                });
        
    });
    
    
    $(document).on("change", "#published_date", function()
    {
        $("#published_date_button").val($("#published_date").val());
    });







    $(document).on("click", "#Save", function()
    {


        if ($("#valid_check_news").valid())
        {
            if (confirm("Do you realy want to Proceed"))
            {
                $("#loading_overlay").show();
                $(".loading_message").show();

                var key = [];
                var val = [];
                $("input, select, textarea", $("#valid_check_news")).each(function()
                {
                    if (this.type != 'button' && this.name.length != 0)
                    {
                        var bPopulate = true;
                        if (this.type == 'radio' || this.type == 'checkbox')
                        {
                            if (!this.checked)
                            {
                                bPopulate = false;
                            }
                        }
                        if (bPopulate)
                        {
                            key.push(this.name);
                            val.push(this.value);
                        }
                    }
                });
                $.ajax({
                    type: "POST",
                    url: $("#base_url").val() + "admin/news/newsSave/",
                    data: {
                        data_keys: key,
                        data_values: val,
                        content: CKEDITOR.instances.content.getData(),
                        mobile_content: CKEDITOR.instances.mobile_content.getData(),
                        user_agent: navigator.userAgent,
                        tds_csrf: $('input[name$="tds_csrf"]').val()

                    },
                    async: false,
                    success: function(data) {
                        var adata = data.split("|||");
                        var priority = 5;
                        if (adata.length > 1)
                        {
                            data = adata[0];
                            priority = adata[1];

                        }

                        if (Math.floor(data) == data && $.isNumeric(data))
                        {
                            $("#loading_overlay").hide();
                            $(".loading_message").hide();

                            var editurl = $("#base_url").val() + "admin/news/edit/" + data + "/updated";
                            var theDialog = "dialog_news_saved";

                            var date_expire = new Date();
                            date_expire.setTime(date_expire.getTime() + (24 * 60 * 60 * 1000));
                            $.cookie('publish_date_cookie_value', $("#published_date").val(), {
                                expires: date_expire
                            });



//                            $('#valid_check_news').sayt({
//                                'erase': true
//                            });

                            var currentPageUrl = "";
                            if (typeof this.href === "undefined") {
                                currentPageUrl = document.location.toString().toLowerCase();
                            }
                            else {
                                currentPageUrl = this.href.toString().toLowerCase();
                            }
                            var arUrl = currentPageUrl.split("/");

                            if ($.inArray("edit", arUrl) != -1)
                            {
                                //priority_check(priority);
                                $("#updated_messege").show();
                                $("#updated_messege").animate({opacity: 100}, 'slow', function()
                                {
                                    $(this).slideDown();
                                });
                                $("#validation_error").html("");
                            }
                            else
                            {

                                //window.location = $("#base_url").val()+"admin/news/";
                            }
//                            alert(currentPageUrl);

                            window.location = editurl;
                            $("#" + theDialog + " .link_button").attr("data-link", editurl);
                            $("#" + theDialog).dialog("open"); // the #dialog element activates the modal box specified above
                            return false;

                        }
                        else
                        {
                            $("#loading_overlay").hide();
                            $(".loading_message").hide();

                            $("#validation_error").html(data);
                            $("#validation_error div.alert_red").css("width", "100%");
                            $("#updated_messege").hide();

                        }
                    }
                });
            }
        }

    });

//    $(".dialog_content_preview").dialog({
//        autoOpen: false,
//        resizable: false,
//        show: "fade",
//        hide: "fade",
//        modal: true,
//        width: "1000",
//        show:{
//            effect: "fade",
//            duration: 500
//        },
//        hide:{
//            effect: "fade",
//            duration: 500
//        },
//        create: function() {
//            $('.dialog_content_preview.no_dialog_titlebar').dialog('option', 'dialogClass', 'no_dialog_titlebar');
//        },
//        open: function() {
//            setTimeout(columnHeight, 100);
//        }
//    });
    
    
    

//    $(document).on("click", "#preview", function()
//    {
//        $("#shoulder_preview").html($("#shoulder").val());
//
//        $("#headline_preview").html($("#headline").val());
//
//        $("#subhead_preview").html($("#sub_head").val());
//
//        $("#byline_preview").html($("#byline_id").val());
//
//        if ($("#published_date").val())
//        {
//            var dateinformat = parseDate($("#published_date").val());
//            if (dateinformat)
//            {
//                $("#publish_preview").html(dateinformat);
//
//            }
//        }
//
//
//        $("#content_preview").html(CKEDITOR.instances.content.getData());
//
//        $("#dialog_news_preview").dialog("open");
//
//
//    });

    function parseDate(value)
    {
        var aDateTime = value.split(" ");

        var aDate = aDateTime[0].split("-");

        var aTime = aDateTime[1].split(":");

        if (aDate[2] && aTime[1])
        {
            var objDate = new Date(Date.UTC(aDate[0], aDate[1], aDate[2], aTime[0], aTime[1], 0));

            return objDate.toDateString() + " " + aDateTime[1];
        }

        return null;
    }

    $(document).on("click", "#publish", function()
    {


        if ($("#valid_check_news").valid())
        {
            if (confirm("Do you realy want to Proceed"))
            {
                $("#loading_overlay").show();
                $(".loading_message").show();

                var key = [];
                var val = [];
                $("input, select, textarea", $("#valid_check_news")).each(function()
                {
                    if (this.type != 'button' && this.name.length != 0)
                    {
                        var bPopulate = true;
                        if (this.type == 'radio' || this.type == 'checkbox')
                        {
                            if (!this.checked)
                            {
                                bPopulate = false;
                            }
                        }
                        if (bPopulate)
                        {
                            key.push(this.name);
                            val.push(this.value);
                        }
                    }
                });
                $.ajax({
                    type: "POST",
                    url: $("#base_url").val() + "admin/news/publishNews/",
                    data: {
                        data_keys: key,
                        data_values: val,
                        content: CKEDITOR.instances.content.getData(),
                        mobile_content: CKEDITOR.instances.mobile_content.getData(),
                        user_agent: navigator.userAgent,
                        tds_csrf: $('input[name$="tds_csrf"]').val()

                    },
                    async: false,
                    success: function(data) {
                        var adata = data.split("|||");
                        if (adata.length > 1)
                        {
                            data = adata[0];
                            var priority = adata[1];

                        }
                        if (Math.floor(data) == data && $.isNumeric(data))
                        {
                            $("#loading_overlay").hide();
                            $(".loading_message").hide();

                            var date_expire = new Date();
                            date_expire.setTime(date_expire.getTime() + (24 * 60 * 60 * 1000));
                            $.cookie('publish_date_cookie_value', $("#published_date").val(), {
                                expires: date_expire
                            });
//                            $('#valid_check_news').sayt({
//                                'erase': true
//                            });

                            var editurl = $("#base_url").val() + "admin/news/edit/" + data + "/updated";
                            var theDialog = "dialog_news_saved";


                            var currentPageUrl = "";
                            if (typeof this.href === "undefined")
                            {
                                currentPageUrl = document.location.toString().toLowerCase();
                            }
                            else
                            {
                                currentPageUrl = this.href.toString().toLowerCase();
                            }
                            var arUrl = currentPageUrl.split("/");

                            if ($.inArray("edit", arUrl) != -1)
                            {
                                //priority_check(priority);
                                $("#updated_messege").show();

                                $("#updated_messege").animate({opacity: 100}, 'slow', function()
                                {
                                    $(this).slideDown();
                                });
                                if ($("#publish span").html() == "Unpublish")
                                {
                                    $("#publish span").html("Publish");
                                }
                                else
                                {
                                    $("#publish span").html("Unpublish");
                                }
                                $("#validation_error").html("");
                            }
                            else
                            {
                                window.location = editurl;//$("#base_url").val()+"admin/news/";
                                $("#" + theDialog + " .link_button").attr("data-link", editurl);
                                $("#" + theDialog).dialog("open"); // the #dialog element activates the modal box specified above

                            }



                            //window.location = editurl;
                            //$("#"+theDialog+" .link_button").attr("data-link",editurl);
                            // $("#"+theDialog).dialog( "open" ); // the #dialog element activates the modal box specified above
                            return false;

                        }
                        else
                        {
                            $("#loading_overlay").hide();
                            $(".loading_message").hide();

                            $("#validation_error").html(data);
                            $("#validation_error div.alert_red").css("width", "100%");
                            $("#updated_messege").hide();
                        }
                    }
                });
            }
        }

    });






    $('.datetimepicker_class').datetimepicker
            ({
                timeFormat: "HH:mm",
                dateFormat: "yy-mm-dd"
            });

    var current_language = $("#language_div").html();



    $(document).on("click", "#clear_reference", function()
    {
        $("#reference_filter").val("");
        $("#referance_id").val(0);
        $("#publish_date_div_with_button").show();
        $("#category_div").show();
        $("#grade_div").show();
        $("#show_in_block_div").show();
        $("#featured_block_div").show();
        $("#featured_block_div_position").show();
        $("#type_div").show();

        $("#reference_filter").attr("readonly", false);

        $("#language_div").html(current_language);

    });

    var cache_reference = {};
    var previous_publish_date = "";
    $("#reference_filter").autocomplete({
        minLength: 2,
        source: function(request, response) {
            var term = request.term;
            if (term in cache) {
                response(cache_releated[ term ]);
                return;
            }
            $.getJSON($("#base_url").val() + "admin/news/reference_news", request, function(data, status, xhr) {
                cache_reference[ term ] = data;
                response(data);
            });
        },
        select: function(event, ui) {
            var terms = ui.item.value;

            $.ajax({
                type: "POST",
                url: $("#base_url").val() + "admin/news/reference_add/",
                data: {
                    term: terms,
                    tds_csrf: $('input[name$="tds_csrf"]').val()
                },
                async: false,
                success: function(data) {
                    if (data == 0)
                    {
                        $("#referance_id").val("");
                    }
                    else
                    {

                        $("#referance_id").val(data);

                        $("#publish_date_div_with_button").hide();
                        $("#category_div").hide();
                        $("#grade_div").hide();
                        $("#show_in_block_div").hide();
                        $("#featured_block_div").hide();
                        $("#featured_block_div_position").hide();
                        $("#type_div").hide();







                        $("#reference_filter").attr("readonly", true);

                        $.ajax({
                            type: "POST",
                            url: $("#base_url").val() + "admin/news/getajaxlanguage/",
                            data: {
                                id: data,
                                tds_csrf: $('input[name$="tds_csrf"]').val()
                            },
                            async: false,
                            success: function(optiondata) {
                                $("#language_div").html(optiondata);

                            }
                        });
                    }
                }
            });
        }
    });

    var cache_releated = {};
    $("#releated_search").autocomplete({
        minLength: 2,
        source: function(request, response) {
           
            $.getJSON($("#base_url").val() + "admin/news/releated_news?term="+request.term+"&related_post_type="+$("#related_post_type").val(),"", function(data, status, xhr) {
                response(data);
            });
        }
    });


    var cache = {};
    $("#byline_id").autocomplete({
        minLength: 2,
        source: function(request, response) {
            var term = request.term;
            if (term in cache) {
                response(cache[ term ]);
                return;
            }
            $.getJSON($("#base_url").val() + "admin/news/byline", request, function(data, status, xhr) {
                cache[ term ] = data;
                response(data);
            });
        }
    });



    function checkURL(value) {
        var urlregex = new RegExp("^(http:\/\/www.|https:\/\/www.|http:\/\/archive.|http:\/\/bd.|https:\/\/archive.|https:\/\/bd.|ftp:\/\/www.|www.){1}([0-9A-Za-z]+\.)");
        if (urlregex.test(value)) {
            return (true);
        }
        return (false);
    }
    function limitString(str, limit)
    {
        if (str.length > limit)
        {
            str = str.substring(0, limit) + "...";
        }
        return str;
    }
    function split(val) {
        return val.split(/,\s*/);
    }
    function extractLast(term) {
        return split(term).pop();
    }

    $("#country_filter")
            // don't navigate away from the field on tab when selecting an item
            .bind("keydown", function(event) {
                if (event.keyCode === $.ui.keyCode.TAB &&
                        $(this).data("ui-autocomplete").menu.active) {
                    event.preventDefault();
                }
            })
            .autocomplete({
                source: function(request, response) {
                    $.getJSON($("#base_url").val() + "admin/news/country", {
                        term: extractLast(request.term)
                    }, response);
                },
                search: function() {
                    // custom minLength
                    var term = extractLast(this.value);
                    if (term.length < 2) {
                        return false;
                    }
                },
                focus: function() {
                    // prevent value inserted on focus
                    return false;
                },
                select: function(event, ui) {
                    var terms = split(this.value);

                    // remove the current input
                    terms.pop();
                    // add the selected item
                    terms.push(ui.item.value);
                    // add placeholder to get the comma-and-space at the end
                    terms.push("");
                    this.value = terms.join(", ");
                    return false;
                }
            });

    $("#tags")
            // don't navigate away from the field on tab when selecting an item
            .bind("keydown", function(event) {
                if (event.keyCode === $.ui.keyCode.TAB &&
                        $(this).data("ui-autocomplete").menu.active) {
                    event.preventDefault();
                }
            })
            .autocomplete({
                source: function(request, response) {
                    $.getJSON($("#base_url").val() + "admin/news/tags", {
                        term: extractLast(request.term)
                    }, response);
                },
                search: function() {
                    // custom minLength
                    var term = extractLast(this.value);
                    if (term.length < 2) {
                        return false;
                    }
                },
                focus: function() {
                    // prevent value inserted on focus
                    return false;
                },
                select: function(event, ui) {
                    var terms = split(this.value);
                    // remove the current input
                    terms.pop();
                    // add the selected item
                    terms.push(ui.item.value);
                    // add placeholder to get the comma-and-space at the end
                    terms.push("");
                    this.value = terms.join(", ");
                    return false;
                }
            });


    $("#keywords")
            // don't navigate away from the field on tab when selecting an item
            .bind("keydown", function(event) {
                if (event.keyCode === $.ui.keyCode.TAB &&
                        $(this).data("ui-autocomplete").menu.active) {
                    event.preventDefault();
                }
            })
            .autocomplete({
                source: function(request, response) {
                    $.getJSON($("#base_url").val() + "admin/news/keywords", {
                        term: extractLast(request.term)
                    }, response);
                },
                search: function() {
                    // custom minLength
                    var term = extractLast(this.value);
                    if (term.length < 2) {
                        return false;
                    }
                },
                focus: function() {
                    // prevent value inserted on focus
                    return false;
                },
                select: function(event, ui) {
                    var terms = split(this.value);
                    // remove the current input
                    terms.pop();
                    // add the selected item
                    terms.push(ui.item.value);
                    // add placeholder to get the comma-and-space at the end
                    terms.push("");
                    this.value = terms.join(", ");
                    return false;
                }
            });



    $(document).on("click", ".text-remove", function() {

        $(this).parents('div:eq(0)').remove();

    });

    $(document).on("click", "#add_releated", function() {

        if ($.trim($("#releated_search").val()) == "")
        {
            alert("Plaese add a releated news first");
        }
        else
        {
            $.post($("#base_url").val() + "admin/news/related_add", {
                term: $.trim($("#releated_search").val()),
                tds_csrf: $('input[name$="tds_csrf"]').val()
            })
                    .done(function(data) {
                        if (data == 0)
                        {
                            alert("Invalid News")
                        }
                        else
                        {
                            $("#related_news_box").append(data);
                            $("#releated_search").val("");
                        }
                    });
        }

    });

    $(document).on("click", "#add_releated_link", function()
    {
        if ($.trim($("#releated_title").val()) == "")
        {
            alert("Plaese add the news title");
            $("#releated_title").focus();
            return false;
        }
        else if ($.trim($("#releated_link").val()) == "")
        {
            alert("Plaese add news link");
            $("#releated_link").focus();
            return false;
        }
        else if (!checkURL($.trim($("#releated_link").val())))
        {
            alert("Invalid news link");
            $("#releated_link").focus();
            return false;
        }
        else
        {
            var $str_related_news = '<div class="text-button"><input type="hidden" name="related_title[]" value="' + $.trim($("#releated_title").val()) + '"><input type="hidden" name="related_link[]" value="' + $.trim($("#releated_link").val()) + '"><input type="hidden" name="related_published_date[]" value="0"><span class="text-label">' + limitString($.trim($("#releated_title").val()), 500) + '</span> <a class="text-remove"></a></div>';
            $("#releated_link").val("");
            $("#releated_title").val("");
            $("#related_news_box").append($str_related_news);
        }

    });

    $(document).on("click", "#trash", function() {

        if (confirm("Do you really want to delete?"))
        {

            $.ajax({
                type: "POST",
                url: $("#base_url").val() + "admin/news/delete/",
                data: {
                    primary_id: $('input[name$="id"]').val(),
                    user_agent: navigator.userAgent,
                    tds_csrf: $('input[name$="tds_csrf"]').val()

                },
                async: false,
                success: function(data) {
                    window.location = $("#base_url").val() + "admin/" + $("#controllername").val() + "/trash/";
                }
            });


        }

    });
    var width_to_maintain = 800;
    var height_to_maintain = 400;
    var check_image = 0;

    function check_image_property(width, height)
    {
        if (check_image == 1)
        {
            if (width != width_to_maintain && width_to_maintain != 0)
            {
                return false;
            }
            if (height != height_to_maintain && height_to_maintain != 0)
            {
                return false;
            }
        }
        return true;
    }

    function checkURLImage(url)
    {
        return(url.match(/\.(jpeg|jpg|gif|png)$/) != null);
    }



    $(document).on("click", "#select_media_mobile", function()
    {

        window.KCFinder =
                {
                    callBack: function(url)
                    {
                        window.KCFinder = null;
                        var $title = "";
                        var $html = "";
                        


                            var a_caption_source;
                            var value = url.substring(url.lastIndexOf('/') + 1);

                            var img = document.createElement("img");
                            img.src = url;

                            img.onload = function() {


                                var w = img.width;
                                var h = img.height;



                                var $title_name = "name";
                                var $required = "required";
                                if (checkURLImage(url) && check_image_property(w, h))
                                {
                                    $title = '<img src="' + url + '" width="70">';
                                    $title_name = "caption";
                                    $required = "";


                                    $.ajax({
                                        type: "POST",
                                        url: $("#base_url").val() + "admin/news/get_caption/",
                                        data: {
                                            url: url,
                                            tds_csrf: $('input[name$="tds_csrf"]').val()

                                        },
                                        async: false,
                                        success: function(data) {

                                            a_caption_source = data.split("||");

                                        }
                                    });






                                    $html = '<div class="gallery_image"><fieldset class="label_side top"><label for="required_field">' + $title + '</label><div class="noborder"><fieldset  class="top noborder"><label for="required_field">' + $title_name + '<span>Use for mobile ad type post</span></label><div><input id="caption_mobile[]" name="caption_mobile[]" value="' + a_caption_source[0] + '"  class="text"  type="text"  ></div></fieldset> <fieldset class="top noborder"><label for="required_field">Source<span>Use for mobile ad type post link. use http://</span></label><div><input id="source_mobile[]" name="source_mobile[]"  value="' + a_caption_source[1] + '" class="text"  type="text"  ></div></fieldset>  </div></fieldset><input type="hidden" name="related_img_mobile[]" value="' + url + '"><a class="text-remove"></a></div>';
                                    $("#gallery_box_mobile").append($html);
                                    
                                }
                                else if (!checkURLImage(url))
                                {
                                    alert("Invalid Image");
                                }
                                else
                                {
                                    alert("Invalid Image size, Image size must be "+width_to_maintain+"*"+height_to_maintain);
                                }
                                parent.$.fancybox.close();
                                   
                            }

                        

                        

                    }
                };
        $.fancybox({
            'width': "80%",
            'height': "70%",
            'autoScale': true,
            'href': base_url + 'browse.php?type=files',
            'title': false,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'type': 'iframe'

        });




    });



    $(document).on("click", "#select_media", function()
    {

        window.KCFinder =
                {
                    callBackMultiple: function(files)
                    {
                        window.KCFinder = null;
                        var $title = "";
                        var $html = "";

                        for (var i = 0; i < files.length; i++)
                        {
                            var url = files[i];


                            var a_caption_source;
                            var value = url.substring(url.lastIndexOf('/') + 1);

                            var $title_name = "name";
                            var $required = "required";
                            if (checkURLImage(url))
                            {
                                $title = '<img src="' + url + '" width="70">';
                                $title_name = "caption";
                                $required = "";
                            }
                            else
                                $title = '<a href="' + url + '">' + value + '</a>';

                            $.ajax({
                                type: "POST",
                                url: $("#base_url").val() + "admin/news/get_caption/",
                                data: {
                                    url: url,
                                    tds_csrf: $('input[name$="tds_csrf"]').val()

                                },
                                async: false,
                                success: function(data) {

                                    a_caption_source = data.split("||");

                                }
                            });




                            $html = '<div class="gallery_image"><fieldset class="label_side top"><label for="required_field">' + $title + '</label><div class="noborder"><fieldset  class="top noborder"><label for="required_field">' + $title_name + '</label><div><input id="caption[]" name="caption[]" value="' + a_caption_source[0] + '"  class="text"  type="text"  ></div></fieldset> <fieldset class="top noborder"><label for="required_field">Source</label><div><input id="source[]" name="source[]"  value="' + a_caption_source[1] + '" class="text"  type="text"  ></div></fieldset>  </div></fieldset><input type="hidden" name="related_img[]" value="' + url + '"><a class="text-remove"></a></div>';
                            $("#gallery_box").append($html);
                        }

                        parent.$.fancybox.close();

                    }
                };
        window.open(base_url + 'browse.php?type=files&dir=files/public',
                'kcfinder_multiple', 'status=0, toolbar=0, location=0, menubar=0, ' +
                'directories=0, resizable=1, scrollbars=0, width=800, height=700'
                );

//        $.fancybox({
//            'width'		        : "80%",
//            'height'                    : "70%",
//            'autoScale'                 : true,
//            'href'			: base_url + 'browse.php?type=files',
//            'title'                     : false,
//            'transitionIn'		: 'none',
//            'transitionOut'		: 'none',
//            'type'		        : 'iframe'
//                    
//        });



    });

    $(document).on("click", "#select_mobile_image", function()
    {

        window.KCFinder =
                {
                    callBack: function(url)
                    {

                        var $title = "";

                        var value = url.substring(url.lastIndexOf('/') + 1);

                        var a_caption_source;


                        if (checkURLImage(url))
                        {
                            window.KCFinder = null;
                            $title = '<img src="' + url + '" width="70">';
                        }
                        else
                        {
                            alert("You can add only image for mobile image");
                            return false;
                        }


                        var url_main = url.replace($("#base_url").val(), '');

                        var $html = '<div>' + $title + '<input type="hidden" name="mobile_image" value="' + url_main + '"><a class="text-remove"></a></div>';

                        $("#mobile_image_box").html($html);
                        parent.$.fancybox.close();

                    }
                };

        $.fancybox({
            'width': "80%",
            'height': "70%",
            'autoScale': true,
            'href': base_url + 'browse.php?type=files',
            'title': false,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'type': 'iframe'

        });



    });
    
    $(document).on("click", "#select_inside_image", function()
    {

        window.KCFinder =
                {
                    callBack: function(url)
                    {

                        var $title = "";

                        var value = url.substring(url.lastIndexOf('/') + 1);

                        var a_caption_source;


                        if (checkURLImage(url))
                        {
                            window.KCFinder = null;
                            $title = '<img src="' + url + '" width="70">';
                        }
                        else
                        {
                            alert("You can add only image for inside image");
                            return false;
                        }
                        

                        var url_main = url.replace($("#base_url").val(), '');


                        

                        var $html = '<div>' + $title + '<input type="hidden" name="inside_image" value="' + url_main + '"><a class="text-remove"></a></div>';

                        $("#inside_image_box").html($html);
                        parent.$.fancybox.close();

                    }
                };

        $.fancybox({
            'width': "80%",
            'height': "70%",
            'autoScale': true,
            'href': base_url + 'browse.php?type=files',
            'title': false,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'type': 'iframe'

        });



    });


    $(document).on("click", "#select_lead_material", function()
    {

        window.KCFinder =
                {
                    callBack: function(url)
                    {

                        var $title = "";

                        var value = url.substring(url.lastIndexOf('/') + 1);

                        var a_caption_source;


                        if (checkURLImage(url))
                        {
                            window.KCFinder = null;
                            $title = '<img src="' + url + '" width="70">';
                        }
                        else
                        {
                            alert("You can add only image for lead material");
                            return false;
                        }
                        $.ajax({
                            type: "POST",
                            url: $("#base_url").val() + "admin/news/get_caption/",
                            data: {
                                url: url,
                                tds_csrf: $('input[name$="tds_csrf"]').val()

                            },
                            async: false,
                            success: function(data) {

                                a_caption_source = data.split("||");

                            }
                        });

                        var url_main = url.replace($("#base_url").val(), '');


                        $("#lead_caption").val(a_caption_source[0]);

                        $("#lead_source").val(a_caption_source[1]);

                        var $html = '<div>' + $title + '<input type="hidden" name="lead_material" value="' + url_main + '"><a class="text-remove"></a></div>';

                        $("#lead_material_box").html($html);
                        parent.$.fancybox.close();

                    }
                };

        $.fancybox({
            'width': "80%",
            'height': "70%",
            'autoScale': true,
            'href': base_url + 'browse.php?type=files',
            'title': false,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'type': 'iframe'

        });



    });
    var allcheck1 = true;
    $("#tree2nd ul li input").each(function()
    {
        if (this.checked)
        {

        }
        else
        {
            allcheck1 = false;
        }

    });
    if (allcheck1)
    {
        $('#checkallgrade').prop('checked', true);
    }
    else
    {
        $('#checkallgrade').prop('checked', false);
    }

    $(document).on("change", "#tree2nd ul li input", function()
    {
        var allcheck = true;
        $("#tree2nd ul li input").each(function()
        {
            if (this.checked)
            {

            }
            else
            {
                allcheck = false;
            }

        });
        if (allcheck)
        {
            $('#checkallgrade').prop('checked', true);
        }
        else
        {
            $('#checkallgrade').prop('checked', false);
        }
    });
    $(document).on("click", "#clear_country", function()
    {
        $("#country_filter").val("");

    });

//    $(document).on("change", "#game_type", function()
//    {
//        if (this.checked)
//        {
//            $('#game_category').show();
//
//        }
//        else
//        {
//            $('#game_category').hide();
//            $('#game_category').val(0);
//        }
//    });


    $(document).on("change", "#checkallgrade", function()
    {
        if (this.checked)
        {
            $('#tree2nd ul li input').prop('checked', true);

        }
        else
        {
            $('#tree2nd ul li input').prop('checked', false);
        }
    });


    var allcheck2 = true;
    $("#tree3rd ul li input").each(function()
    {
        if (this.checked)
        {

        }
        else
        {
            allcheck2 = false;
        }

    });
    if (allcheck2)
    {
        $('#checkalltype').prop('checked', true);
    }
    else
    {
        $('#checkalltype').prop('checked', false);
    }

    $(document).on("change", "#tree3rd ul li input", function()
    {
        var allcheck = true;
        $("#tree3rd ul li input").each(function()
        {
            if (this.checked)
            {

            }
            else
            {
                allcheck = false;
            }

        });
        if (allcheck)
        {
            $('#checkalltype').prop('checked', true);
        }
        else
        {
            $('#checkalltype').prop('checked', false);
        }
    });

    $(document).on("change", "#checkalltype", function()
    {
        if (this.checked)
        {
            $('#tree3rd ul li input').prop('checked', true);
        }
        else
        {
            $('#tree3rd ul li input').prop('checked', false);
        }
    });
//    
//
});