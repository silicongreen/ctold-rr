$(document).ready(function() {

	adminicaUi();
	adminicaForms();
	adminicaMobile();
	adminicaDataTables();
	adminicaCalendar();
	adminicaCharts();
	adminicaGallery();
	adminicaWizard();
	adminicaVarious();

        if ( $("a.pjax.pjax_on").length > 0 )
        {
            $("a.pjax.pjax_on").pjax("body",{
                    fragment:"#pjax",
                    timeout:"5000",
                    beforeSend: function(){
                            showLoadingOverlay();
                    },
                    complete: function(){
                            hideLoadingOverlay();
                    },
                    success: function(){
                            adminicaUi();
                            adminicaForms();
                            adminicaMobile();
                            adminicaDataTables();
                            adminicaCalendar();
                            adminicaCharts();
                            adminicaGallery();
                            adminicaWizard();
                            adminicaVarious();
                            pjaxToggle();
                    },
                    error: function(){
                    }
            });
            
            pjaxToggle();
        }

	

	$('#pjax_switch #dynamic_on').on("change",function() {
		$("a.pjax").addClass("pjax_on");
		$.cookie('pjax_on', true);
	});

	$('#pjax_switch #dynamic_off').on("change",function() {
		$("a.pjax").removeClass("pjax_on");
		$.cookie('pjax_on', false);
	});
});

$(window).load(function(){

	adminicaInit();

});

function pjaxToggle() {
	if ( $.cookie('pjax_on') === "true" ){
		$('#pjax_switch #dynamic_on').trigger("click");
		$("a.pjax").addClass("pjax_on");
	}
}