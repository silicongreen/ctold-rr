function adminicaVarious(){if($.fn.sliderNav){$("#slider_list").sliderNav({height:"402"});$("#slider_list ul ul li a").live("click",function(){var e=$(this).find("span").text(),t=$(this).html().replace("<span>"+e+"</span>","");$("#contactName").text(t);$("#contactEmail").text(e)})}if($(".tinyeditor").length>0){new TINY.editor.edit("editor",{id:"tiny_input",height:200,cssclass:"te",controlclass:"tecontrol",rowclass:"teheader",dividerclass:"tedivider",controls:["bold","italic","underline","strikethrough","|","subscript","superscript","|","orderedlist","unorderedlist","|","outdent","indent","|","leftalign","centeralign","rightalign","blockjustify","|","unformat","|","undo","redo","n","image","hr","link","unlink","|","cut","copy","paste","print","|","font","size","style"],footer:!1,fonts:["Arial","Verdana","Georgia","Trebuchet MS"],xhtml:!0,bodyid:"editor",footerclass:"tefooter",toggle:{text:"source",activetext:"wysiwyg",cssclass:"toggler"},resize:{cssclass:"resize"}});new TINY.editor.edit("editor2",{id:"tiny_input2",height:200,cssclass:"te",controlclass:"tecontrol",rowclass:"teheader",dividerclass:"tedivider",controls:["bold","italic","underline","strikethrough","|","subscript","superscript","|","orderedlist","unorderedlist","|","outdent","indent","|","leftalign","centeralign","rightalign","blockjustify","|","unformat","|","undo","redo","n","image","hr","link","unlink","|","cut","copy","paste","print","|","font","size","style"],footer:!1,fonts:["Arial","Verdana","Georgia","Trebuchet MS"],xhtml:!0,bodyid:"editor",footerclass:"tefooter",toggle:{text:"source",activetext:"wysiwyg",cssclass:"toggler"},resize:{cssclass:"resize"}});$(".teheader select").uniform()}if($.fn.elfinder){var e=$("#finder").elfinder({url:"scripts/elfinder/connector.php",places:"",toolbar:[["back","reload"],["mkdir","copy","paste"],["remove","rename","info"],["icons","list"]]});$("#close,#open,#dock,#undock").click(function(){$("#finder").elfinder($(this).attr("id"))})}};