/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var base_url_category_ckfinder = $("#base_url").val() + "ckeditor/kcfinder/";
$(document).ready(function() {

    var width_logo = 225;
    var height_logo = 225;
    var width_cover = 954;
    var height_cover = 310;
    var width_picture = 300;
    var height_picture = 200;
    var check_image = 0;
    function check_image_property_picture(width, height)
    {
        if (check_image == 1)
        {
            if (width != width_picture && width_picture != 0)
            {
                return false;
            }
            if (height != height_picture && height_picture != 0)
            {
                return false;
            }
        }
        return true;
    }
    function check_image_property_cover(width, height)
    {
        if (check_image == 1)
        {
            if (width != width_cover && width_cover != 0)
            {
                return false;
            }
            if (height != height_cover && height_cover != 0)
            {
                return false;
            }
        }
        return true;
    }

    function check_image_property_logo(width, height)
    {
        if (check_image == 1)
        {
            if (width != width_logo && width_logo != 0)
            {
                return false;
            }
            if (height != height_logo && height_logo != 0)
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
    $('.datetimepicker_class').datetimepicker
            ({
                timeFormat: "HH:mm",
                dateFormat: "yy-mm-dd"
            });
    $(document).on("click", "#select_logo", function()
    {

        window.KCFinder =
                {
                    callBack: function(url)
                    {

                        var $title = "";


                        var value = url.substring(url.lastIndexOf('/') + 1);
                        var img = document.createElement("img");
                        img.src = url;

                        img.onload = function() {


                            var w = img.width;
                            var h = img.height;

                            if (checkURLImage(url) && check_image_property_logo(w, h))
                            {
                                window.KCFinder = null;
                                $title = '<img src="' + url + '" width="50">';
                                var url_main = url.replace($("#base_url").val(), '');


                                var $html = '<div>' + $title + '<input type="hidden" name="logo" value="' + url_main + '"><a class="text-remove"></a></div>';

                                $("#select_logo_box").html($html);
                            }
                            else if (!checkURLImage(url))
                            {
                                alert("Invalid Image");
                            }
                            else
                            {
                                alert("Invalid Image size, Image size must be " + width_logo + "*" + height_logo);
                            }



                            $.fancybox.close();
                        }
                    }
                };

        $.fancybox({
            'width': "85%",
            'height': "90%",
            'autoScale': true,
            'href': base_url_category_ckfinder + 'browse.php?type=files',
            'title': false,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'type': 'iframe'

        });



    });
    $(document).on("click", "#select_picture", function()
    {

        window.KCFinder =
                {
                    callBack: function(url)
                    {

                        var $title = "";


                        var value = url.substring(url.lastIndexOf('/') + 1);
                        var img = document.createElement("img");
                        img.src = url;

                        img.onload = function() {


                            var w = img.width;
                            var h = img.height;

                            if (checkURLImage(url) && check_image_property_picture(w, h))
                            {
                                window.KCFinder = null;
                                $title = '<img src="' + url + '" width="50">';
                                var url_main = url.replace($("#base_url").val(), '');


                                var $html = '<div>' + $title + '<input type="hidden" name="picture" value="' + url_main + '"><a class="text-remove"></a></div>';

                                $("#select_picture_box").html($html);
                            }
                            else if (!checkURLImage(url))
                            {
                                alert("Invalid Image");
                            }
                            else
                            {
                                alert("Invalid Image size, Image size must be " + width_picture + "*" + height_picture);
                            }



                            $.fancybox.close();
                        }
                    }
                };

        $.fancybox({
            'width': "85%",
            'height': "90%",
            'autoScale': true,
            'href': base_url_category_ckfinder + 'browse.php?type=files',
            'title': false,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'type': 'iframe'

        });



    });
    $(document).on("click", "#select_cover", function()
    {

        window.KCFinder =
                {
                    callBack: function(url)
                    {

                        var $title = "";


                        var value = url.substring(url.lastIndexOf('/') + 1);
                        var img = document.createElement("img");
                        img.src = url;

                        img.onload = function() {


                            var w = img.width;
                            var h = img.height;

                            if (checkURLImage(url) && check_image_property_cover(w, h))
                            {
                                window.KCFinder = null;
                                $title = '<img src="' + url + '" width="50">';
                                var url_main = url.replace($("#base_url").val(), '');


                                var $html = '<div>' + $title + '<input type="hidden" name="cover" value="' + url_main + '"><a class="text-remove"></a></div>';

                                $("#select_cover_box").html($html);
                            }
                            else if (!checkURLImage(url))
                            {
                                alert("Invalid Image");
                            }
                            else
                            {
                                alert("Invalid Image size, Image size must be " + width_cover + "*" + height_cover);
                            }



                            $.fancybox.close();
                        }
                    }
                };

        $.fancybox({
            'width': "85%",
            'height': "90%",
            'autoScale': true,
            'href': base_url_category_ckfinder + 'browse.php?type=files',
            'title': false,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'type': 'iframe'

        });



    });



    $(document).on("click", ".text-remove", function()
    {

        $(this).parents('div:eq(0)').remove();

    });



});