var oTable;
var oTableTrash;
var sortindex = 0;
var sorttype = "asc";
var param = 0;

$(document).ready(function() {
    
    if($("#sortIndex").length>0) 
    {
        sortindex = $("#sortIndex").val();
    }
    if($("#sorttype").length>0) 
    {
        sorttype = $("#sorttype").val();
    }
    if ( $('.mytable_gallery').html() != null )
    {
        
        
        oTable = $('.mytable_gallery').dataTable( {
            "bJQueryUI": true,
            "sScrollX": "",
            "sScrollY": "100px",
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": $("#base_url").val()+"admin/"+$("#controllername").val()+"/datatable/",
            "bSortClasses": false,
            "aaSorting": [[sortindex,sorttype]],
            "bAutoWidth": true,
            "bInfo": true,
            "bScrollCollapse": true,
            "sPaginationType": "full_numbers",
            "bRetrieve": true,
            "fnInitComplete": function () {

                $(".mytable_gallery .dataTables_length > label > select").uniform();
                $(".mytable_gallery .dataTables_filter input[type=text]").addClass("text");
                $(".mytable_gallery").css("visibility","visible");
                this.fnAdjustColumnSizing(true);

            },
            'fnServerData': function(sSource, aoData, fnCallback)
            {
                $.ajax
                ({
                    'dataType': 'json',
                    'type'    : 'POST',
                    'url'     : sSource,
                    'data'    : aoData,
                    'success' : fnCallback
                });
            } 
        });
    }
    if ( $('#dt1 .trash_table').html() != null )
    {        
        oTable = $('#dt1 .trash_table').dataTable( {
            "bJQueryUI": true,
            "sScrollX": "",
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": $("#base_url").val()+"admin/"+$("#controllername").val()+"/datatable_trash/",
            "bSortClasses": false,
            "aaSorting": [[sortindex,sorttype]],
            "bAutoWidth": true,
            "bInfo": true,
            "sScrollX": "101%",
            "bScrollCollapse": true,
            "sPaginationType": "full_numbers",
            "bRetrieve": true,
            "fnInitComplete": function () {
                oTable.fnAdjustColumnSizing();
                $("#dt1 .dataTables_length > label > select").uniform();
                $("#dt1 .dataTables_filter input[type=text]").addClass("text");
                $(".mytable").css("visibility","visible");

            },
            'fnServerData': function(sSource, aoData, fnCallback)
            {
                $.ajax
                ({
                    'dataType': 'json',
                    'type'    : 'POST',
                    'url'     : sSource,
                    'data'    : aoData,
                    'success' : fnCallback
                });
            } 
        });
    }
    if ( $('#dt1 .menuad_table').html() != null )
    {        
        oTable = $('#dt1 .menuad_table').dataTable( {
            "bJQueryUI": true,
            "sScrollX": "",
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": $("#base_url").val()+"admin/"+$("#controllername").val()+"/datatable_menuad/",
            "bSortClasses": false,
            "aaSorting": [[sortindex,sorttype]],
            "bAutoWidth": true,
            "bInfo": true,
            "sScrollX": "101%",
            "bScrollCollapse": true,
            "sPaginationType": "full_numbers",
            "bRetrieve": true,
            "fnInitComplete": function () {
                oTable.fnAdjustColumnSizing();
                $("#dt1 .dataTables_length > label > select").uniform();
                $("#dt1 .dataTables_filter input[type=text]").addClass("text");
                $(".mytable").css("visibility","visible");

            },
            'fnServerData': function(sSource, aoData, fnCallback)
            {
                $.ajax
                ({
                    'dataType': 'json',
                    'type'    : 'POST',
                    'url'     : sSource,
                    'data'    : aoData,
                    'success' : fnCallback
                });
            } 
        });
    }
	if ( $('#dt1 .sectionad_table').html() != null )
    {        
        oTable = $('#dt1 .sectionad_table').dataTable( {
            "bJQueryUI": true,
            "sScrollX": "",
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": $("#base_url").val()+"admin/"+$("#controllername").val()+"/datatable_sectionad/",
            "bSortClasses": false,
            "aaSorting": [[sortindex,sorttype]],
            "bAutoWidth": true,
            "bInfo": true,
            "sScrollX": "101%",
            "bScrollCollapse": true,
            "sPaginationType": "full_numbers",
            "bRetrieve": true,
            "fnInitComplete": function () {
                oTable.fnAdjustColumnSizing();
                $("#dt1 .dataTables_length > label > select").uniform();
                $("#dt1 .dataTables_filter input[type=text]").addClass("text");
                $(".mytable").css("visibility","visible");

            },
            'fnServerData': function(sSource, aoData, fnCallback)
            {
                $.ajax
                ({
                    'dataType': 'json',
                    'type'    : 'POST',
                    'url'     : sSource,
                    'data'    : aoData,
                    'success' : fnCallback
                });
            } 
        });
    } 	
    if ( $('#dt1 .mytable').html() != null )
    {
        var extra = "";
        if($("#school_id_feed").length>0)
        {
            if($("#school_id_feed").val()!=0)
            {
               extra =  $("#school_id_feed").val();
            }    
        } 
        oTable = $('#dt1 .mytable').dataTable( {
            "bJQueryUI": true,
            "sScrollX": "",
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": $("#base_url").val()+"admin/"+$("#controllername").val()+"/datatable/"+extra,
            "bSortClasses": false,
            "aaSorting": [[sortindex,sorttype]],
            "bAutoWidth": true,
            "bInfo": true,
            "sScrollX": "100%",
            "bScrollCollapse": true,
            "sPaginationType": "full_numbers",
            "bRetrieve": true,
            "fnInitComplete": function () {
                oTable.fnAdjustColumnSizing();
                $("#dt1 .dataTables_length > label > select").uniform();
                $("#dt1 .dataTables_filter input[type=text]").addClass("text");
                $(".mytable").css("visibility","visible");

            },
            'fnServerData': function(sSource, aoData, fnCallback)
            {
                $.ajax
                ({
                    'dataType': 'json',
                    'type'    : 'POST',
                    'url'     : sSource,
                    'data'    : aoData,
                    'success' : fnCallback
                });
            } 
        });
    }
    
    if ( $('#dt1 .pdf_table').html() != null )
    {
   
         oTable = $('#dt1 .pdf_table').dataTable( {
            "bJQueryUI": true,
            "sScrollX": "",
            "sScrollY": "100px",
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": $("#base_url").val()+"admin/"+$("#controllername").val()+"/datatable_pdf/"+$("#category_id_pdf").val(),
            "bSortClasses": false,
            "aaSorting": [[sortindex,sorttype]],
            "bAutoWidth": true,
            "bInfo": true,
            "bScrollCollapse": true,
            "sPaginationType": "full_numbers",
            "bRetrieve": true,
            "fnInitComplete": function () {

                $(".mytable_gallery .dataTables_length > label > select").uniform();
                $(".mytable_gallery .dataTables_filter input[type=text]").addClass("text");
                $(".mytable_gallery").css("visibility","visible");
                this.fnAdjustColumnSizing(true);

            },
            'fnServerData': function(sSource, aoData, fnCallback)
            {
                $.ajax
                ({
                    'dataType': 'json',
                    'type'    : 'POST',
                    'url'     : sSource,
                    'data'    : aoData,
                    'success' : fnCallback
                });
            } 
        });
        
    }
    
    if ( $('#dt1 .members_table').html() != null )
    {
         oTable = $('#dt1 .members_table').dataTable( {
            "bJQueryUI": true,
            "sScrollX": "",
            "sScrollY": "300px",
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": $("#base_url").val()+"admin/"+$("#controllername").val()+"/datatable_members/"+$("#school_id").val(),
            "bSortClasses": false,
            "aaSorting": [[sortindex,sorttype]],
            "bAutoWidth": true,
            "bInfo": true,
            "bScrollCollapse": true,
            "sPaginationType": "full_numbers",
            "bRetrieve": true,
            "fnInitComplete": function () {

                $(".mytable_gallery .dataTables_length > label > select").uniform();
                $(".mytable_gallery .dataTables_filter input[type=text]").addClass("text");
                $(".mytable_gallery").css("visibility","visible");
                this.fnAdjustColumnSizing(true);

            },
            'fnServerData': function(sSource, aoData, fnCallback)
            {
                $.ajax
                ({
                    'dataType': 'json',
                    'type'    : 'POST',
                    'url'     : sSource,
                    'data'    : aoData,
                    'success' : fnCallback
                });
            } 
        });
        
    }
    
    if ( $('#dt1 .question_table').html() != null )
    {
         oTable = $('#dt1 .question_table').dataTable( {
            "bJQueryUI": true,
            "sScrollX": "",
            "sScrollY": "300px",
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": $("#base_url").val()+"admin/"+$("#controllername").val()+"/datatable_question/"+$("#assessment_id").val(),
            "bSortClasses": false,
            "aaSorting": [[sortindex,sorttype]],
            "bAutoWidth": true,
            "bInfo": true,
            "bScrollCollapse": true,
            "sPaginationType": "full_numbers",
            "bRetrieve": true,
            "fnInitComplete": function () {

                $(".mytable_gallery .dataTables_length > label > select").uniform();
                $(".mytable_gallery .dataTables_filter input[type=text]").addClass("text");
                $(".mytable_gallery").css("visibility","visible");
                this.fnAdjustColumnSizing(true);

            },
            'fnServerData': function(sSource, aoData, fnCallback)
            {
                $.ajax
                ({
                    'dataType': 'json',
                    'type'    : 'POST',
                    'url'     : sSource,
                    'data'    : aoData,
                    'success' : fnCallback
                });
            } 
        });
        
    }
    
    
    if ( $('#dt1 .question_table_science').html() != null )
    {
         oTable = $('#dt1 .question_table_science').dataTable( {
            "bJQueryUI": true,
            "sScrollX": "",
            "sScrollY": "300px",
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": $("#base_url").val()+"admin/"+$("#controllername").val()+"/datatable_question/"+$("#topic_id").val(),
            "bSortClasses": false,
            "aaSorting": [[sortindex,sorttype]],
            "bAutoWidth": true,
            "bInfo": true,
            "bScrollCollapse": true,
            "sPaginationType": "full_numbers",
            "bRetrieve": true,
            "fnInitComplete": function () {

                $(".mytable_gallery .dataTables_length > label > select").uniform();
                $(".mytable_gallery .dataTables_filter input[type=text]").addClass("text");
                $(".mytable_gallery").css("visibility","visible");
                this.fnAdjustColumnSizing(true);

            },
            'fnServerData': function(sSource, aoData, fnCallback)
            {
                $.ajax
                ({
                    'dataType': 'json',
                    'type'    : 'POST',
                    'url'     : sSource,
                    'data'    : aoData,
                    'success' : fnCallback
                });
            } 
        });
        
    }
    
    
    if ( $('#dt1 .photo_table').html() != null )
    {
        oTable = $('#dt1 .photo_table').dataTable( {
            "bJQueryUI": true,
            "sScrollX": "",
            "sScrollY": "100px",
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": $("#base_url").val()+"admin/"+$("#controllername").val()+"/datatable_photo/"+$("#category_id_photo").val(),
            "bSortClasses": false,
            "aaSorting": [[sortindex,sorttype]],
            "bAutoWidth": true,
            "bInfo": true,
            "bScrollCollapse": true,
            "sPaginationType": "full_numbers",
            "bRetrieve": true,
            "fnInitComplete": function () {

                $(".mytable_gallery .dataTables_length > label > select").uniform();
                $(".mytable_gallery .dataTables_filter input[type=text]").addClass("text");
                $(".mytable_gallery").css("visibility","visible");
                this.fnAdjustColumnSizing(true);

            },
            'fnServerData': function(sSource, aoData, fnCallback)
            {
                $.ajax
                ({
                    'dataType': 'json',
                    'type'    : 'POST',
                    'url'     : sSource,
                    'data'    : aoData,
                    'success' : fnCallback
                });
            } 
        });
   
        
    }
    $(document).on("click", "button.model", function(){
         
            
        $.fancybox({
            'width'		        : $("#modelwidth").val(),
            'height'                    : $("#modelheight").val(),
            'autoScale'                 : true,
            'href'			: this.id,
            'title'                     : false,
            'transitionIn'		: 'none',
            'transitionOut'		: 'none',
            'type'		        : 'iframe'
                    
        });
    
    });
        
    $(document).on("click", "button.model2", function(){
           
            
        $.fancybox({
            'width'		        : $("#modelwidth2").val(),
            'height'                    : $("#modelheight2").val(),
            'autoScale'                 : true,
            'href'			: this.id,
            'title'                     : false,
            'transitionIn'		: 'none',
            'transitionOut'		: 'none',
            'type'		        : 'iframe'
                    
        });
    
    });
    
    $(document).on("click", "button.category_model", function(){
   
        $.fancybox({
            'width'		        : $("#categoryphotomodelwidth").val(),
            'height'                    : $("#categoryphotomodelheight").val(),
            'autoScale'                 : true,
            'href'			: this.id,
            'title'                     : false,
            'transitionIn'		: 'none',
            'transitionOut'		: 'none',
            'type'		        : 'iframe'
                    
        });
    
    });
        
        
    $(document).on("click", "button.noModel", function(){
           
        window.location = this.id;
    
    });
    
    $(document).on("click", "button.new_window", function(){
           
        window.open(this.id,"_blank");
    
    });
    

        
    $(document).on("click", "button.ajax", function(){ 
          
        var $confirm_messege = "Do you really want to delete?";
           
        if($(this).html()=="Change status")
            $confirm_messege = "Do you really want to change status?";
        
        if($(this).html()=="Set home tommorow")
            $confirm_messege = "Do you really want to add this news to home page of tommorow?";
          
        if($(this).html()=="Approve")
            $confirm_messege = "Do you really want to approve the member?";
          
        if($(this).html()=="Deny")
            $confirm_messege = "Do you really want to deny the member?";
          
        if(confirm($confirm_messege))
        {
            var id = this.id;
            $.post($("#base_url").val()+"admin/"+$("#controllername").val()+"/"+$(this).attr('executeFunction')+"/", {
                primary_id:this.id, 
                user_agent: navigator.userAgent,
                tds_csrf: $('input[name$="tds_csrf"]').val()
            })
            .done(function(data) {
                if ( data == "image_exists_on_gallery" )
                {
                    var c = confirm("Image exists on Gallery please delete those images\nbefore you want to delete this gallery\n\nDo you want to delete this Gallery anyway?");
                    if ( c )
                    {
                        $.post($("#base_url").val()+"admin/"+$("#controllername").val()+"/deleteall/", {
                            primary_id:id,
                            tds_csrf: $('input[name$="tds_csrf"]').val()
                        })
                        .done(function(data) {
                            
                            oTable.fnClearTable(true);
                         
                        });
                    }
                }
                else
                {
                   
                    oTable.fnClearTable(true);
                   
                }
            });
        }
    
    });
        
    $(document).on("keyup", "input.filter", function(){ 
             
        var idFilter = this.id;
        var $filterIdArray =  idFilter.split("_");
        oTable.fnFilter($.trim($(this).val()),$filterIdArray[1]);
    
    });
         
    $(document).on("change", "select.filter", function(){ 
        var idFilter = this.id;
        var $filterIdArray =  idFilter.split("_");
        oTable.fnFilter($(this).val(),$filterIdArray[1],'eq');
           
    
    });
    $(document).on("change", "select.group_concate", function(){ 
        var idFilter = this.id;
        var $filterIdArray =  idFilter.split("_");
        oTable.fnFilter($(this).val(),$filterIdArray[1],'group_concate');
             
    
    });
         
    $(document).on("change", "input.filter_datepicker", function(){ 
        var idFilter = this.id;
        var $filterIdArray =  idFilter.split("_");
        oTable.fnFilter($(this).val(),$filterIdArray[1]);
             
    
    });
    $(document).on("click", "button.ajax_restore", function(){ 
          
        if(confirm("Do you really want to restore this news?"))
        {
               
            $.post($("#base_url").val()+"admin/"+$("#controllername").val()+"/"+$(this).attr('executeFunction')+"/", {
                primary_id:this.id, 
                user_agent: navigator.userAgent,
                tds_csrf: $('input[name$="tds_csrf"]').val()
            })
            .done(function(data) {
                oTable.fnClearTable(true);
            });
        }
    
    });
    if($('.dateranger').length>0)  
    {
        $('.dateranger').daterangepicker(
        {
            startDate: moment(),
            endDate: moment(),
            minDate: '01/01/2014',
            maxDate: '12/31/2050',
            dateLimit: {
                years: 10 
            },
            showDropdowns: true,
            showWeekNumbers: true,
            timePicker: false,
            timePickerIncrement: 1,
            timePicker12Hour: true,
            ranges: {
                'Today': [moment(), moment()],
                'Next Day': [moment().add('days', 1), moment().add('days', 1)],
                'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
                'Next 7 Day': [moment(), moment().add('days', 6)],
                'Last 7 Days': [moment().subtract('days', 6), moment()],
                'Last 30 Days': [moment().subtract('days', 29), moment()],
                'This Week': [moment().startOf('week'), moment().endOf('week')],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
            },
            opens: 'right',
            buttonClasses: ['btn btn-default'],
            applyClass: 'btn-small btn-primary',
            cancelClass: 'btn-small',
            format: 'MM/DD/YYYY',
            separator: ' to ',
            locale: {
                applyLabel: 'Submit',
                fromLabel: 'From',
                toLabel: 'To',
                customRangeLabel: 'Custom Range',
                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr','Sa'],
                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                firstDay: 1
            }
        },
        function(start, end) {
            var idFilter = $('.dateranger').attr("id");
            var $filterIdArray =  idFilter.split("_");
            $('.dateranger span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            oTable.fnFilter(start.format('YYYY-MM-DD 00:00:00') + ' - ' + end.format('YYYY-MM-DD 59:00:00'),$filterIdArray[1],'between');
          
        
        }
        );
        
        $('.dateranger span').html(moment().subtract('years', 1).format('MMMM D, YYYY') + ' - ' + moment().add('years', 1).format('MMMM D, YYYY'));

      
        var idFilter = $('.dateranger').attr("id");
        var $filterIdArray =  idFilter.split("_");
     
        
        oTable.fnFilter(moment().subtract('years', 1).format('YYYY-MM-DD 00:00:00') + ' - ' + moment().add('years', 1).format('YYYY-MM-DD 59:00:00'),$filterIdArray[1],'between');   
    } 
        
        
} );        