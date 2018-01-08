(function () {
	var input = document.getElementById("images"), 
        formdata = false;

	function showUploadedItem (source) {
  		alert(1);
                var list = document.getElementById("image-list"),	  	
                li   = document.createElement("li"),
	  	img  = document.createElement("img");                
  		img.src = source;                 
                li.appendChild(img);		
		list.appendChild(li);
	}   
        
        if (window.FormData) {
  		formdata = new FormData();
	}
	
 	input.addEventListener("change", function (evt) { 		
		
 		var i = 0, len = this.files.length, img, reader, file;
                $('#image-list li').remove();
		for ( ; i < len; i++ ) {
                        
			file = this.files[i];
                        
                        //console.log(file);
                        
			if (!!file.type.match(/image.*/)) {
				/*if ( window.FileReader ) {
					reader = new FileReader();
					reader.onloadend = function (e) { 
						document.getElementById("response").innerHTML = $("#base_url").val() + file.name;                                                
                                                showUploadedItem(e.target.result, file.fileName);
					};
					reader.readAsDataURL(file);
				}*/
				if (formdata) {
					formdata.append("images[]", file);
                                        formdata.append("tds_csrf", $('input[name$="tds_csrf"]').val());
				}
			}	
		}
                
		if (formdata) {
                    var r = $('input[name$="tds_csrf"]').val();
                        $.ajax({
                            type: 'POST',                            
                            url: "http://www.thedailystar_0_0_6.dev/admin/ad/ajaxupload",
                            data: formdata ,
                            processData: false,
                            contentType: false,
                            success: function(data) {                                
                                document.getElementById("response").innerHTML = data; 
                            },
                            error: function(e) {
                                document.getElementById("response").innerHTML = "Upload Errorr."; 
                        }
                    });
		}
                
	}, false);
}());
