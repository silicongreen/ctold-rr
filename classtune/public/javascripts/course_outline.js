var current_step_progress, currentProgress, currentDatatable;
var oTable, oTableDept;
var jq = jQuery.noConflict();
var oTableProgram, oTableDepartment, oTableBatch, oTableSemester, 
    oTableCourse, oTableSubject, oTableSemesters;
var newProgress;

var optionSet1 = {
    startDate: moment().subtract(1, 'year'),
    endDate: moment(),
    dateLimit: {
      days: 365
    },
    format: 'YYYY-MM-DD',
    opens: 'left'
};
var fixHelper = function(e, ui) {
    var parent_id = ui.parent().parent().attr('id');
    if ( parent_id == "subject_list_outline" )
    {
        if ( jq(".semester_table tbody tr").find(".dataTables_empty").length > 0 )
        {
            jq(".semester_table tbody tr").find(".dataTables_empty").height("25px");
            jq(".semester_table tbody tr").find(".dataTables_empty").html("");
        }
    }

    ui.children().each(function() {
        jq(this).width(jq(this).width());
    });
    return ui;
};
function createDate()
{
    if ( jq('.batch_daterange').length > 0 )
    {
        jq('.batch_daterange span').html(moment().subtract(1, 'year').format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
        jq('#batch_daterange').daterangepicker(optionSet1);

        jq('#batch_daterange').on('apply.daterangepicker', function(ev, picker) {
            jq('#batch_daterange').show();
            jq("#from_date").val(picker.startDate.format('YYYY-MM-DD'));
            jq("#to_date").val(picker.endDate.format('YYYY-MM-DD'));
        });

        jq('#batch_daterange').on('cancel.daterangepicker', function(ev, picker) {
          jq('#batch_daterange').show();
        });

        jq('#batch_daterange').on('hide.daterangepicker', function(ev, picker) {
          ev.preventDefault();
        });
    }
}
function createDatatable2()
{
    jq(".wizard_table").each(function(){

            var id = jq(this).attr('id');
            currentDatatable = id;
            var fn = "";
            if ( typeof( jq(this).data("precall-func") ) != 'undefined' )
            {
                var func = jq(this).data('precall-func');
                var fn = window[func];
            }
            else
            {
                func = "dtPreDrawCallback"
                var fn = window[func];
            }
            var obj = jq('#' + id);
            oTable = jq('#' + id).DataTable({
                deferRender: true,
                pagingType: 'simple',
                columns: getTableHash2(),
                bLengthChange: (typeof( jq(this).data("length") ) == 'undefined') ? false : true,
                bPaginate: (typeof( jq(this).data("paginate") ) == 'undefined') ? false : true,
                bSort: (typeof( jq(this).data("sort") ) == 'undefined') ? false : true,
                initComplete: function(settings, json) {
                },
                preDrawCallback: fn
            });

            if ( typeof(jq('#' + id).data('use-var')) != 'undefined' )
            {
                if ( jq('#' + id).data('use-var') == 'dept' )
                {
                    oTableDept = oTable;
                    jq('#' + id).removeClass('wizard_table');
                }
            }

            jq(this).on( 'click', 'td a.delete', function () {
                var rowIdx = oTable.cell( jq(this).parent() ).index().row;
                oTable.rows( rowIdx ).remove().draw();
            } );
        
    });
    numinput();
}
function getTableHash2()
{
    var tableStruct = [];
    var i = 0;
    jq(".wizard_table thead th").each(function(){
        
            if ( typeof( jq(this).data("visible") ) == 'undefined' && typeof( jq(this).data("orderable") ) == 'undefined' )
            {
                tableStruct[i] = null;
            }
            else if ( typeof( jq(this).data("visible") ) == 'undefined' && typeof( jq(this).data("orderable") ) != 'undefined' )
            {
                tableStruct[i] = {orderable: jq(this).data("orderable") };
            }
            else if ( typeof( jq(this).data("visible") ) != 'undefined' && typeof( jq(this).data("orderable") ) == 'undefined' )
            {
                tableStruct[i] = {visible: jq(this).data("visible") };
            }
            else if ( typeof( jq(this).data("visible") ) != 'undefined' && typeof( jq(this).data("orderable") ) != 'undefined' )
            {
                tableStruct[i] = {visible: jq(this).data("visible") };
                tableStruct[i] = {orderable: jq(this).data("orderable") };
            }
            i++;
        
    });
    
    return tableStruct;
}
function createCombo()
{
    jq(".custom-combo").each(function(){
        if ( jq(this).closest(".step-content").css('display') != 'none' )
        {
            var id = jq(this).attr('id');
            jq("#" + id).select2({
                placeholder: jq('.custom-combo').data('placeholder'),
                allowClear: true
            });

            if ( typeof(jq("#" + id).data('func')) != 'undefined' )
            {
                var combofunc = jq("#" + id).data('func');
                var fn = window[combofunc];
                if(typeof fn === 'function') {
                    jq("#" + id).off("select2:select").on("select2:select", fn);
                }
            }
        }
    });
}
function get_semesters(e)
{
    if (e.params.data.text.indexOf("Select a Program") == -1)
    {
        jq.ajax({
            type: 'POST' ,
            url: "/dashboards/get_semester_accordian",
            async: false,
            data : {
              program_id: e.params.data.id
            },
            success : function(data) {
                jq("#semester_info").html(data);
                oTableDept.columns().search("").draw();
                oTable = jq('.semester_table').DataTable({
                    deferRender: true,
                    pagingType: 'simple',
                    bFilter: false,
                    bPaginate: false,
                    bLengthChange: false,
                    bInfo: false,
                    columns: [
                      { visible: true, orderable: false }, {orderable: false}, {orderable: false}, {orderable: false}
                    ]
                });
                jq(".items tbody, .projects tbody").sortable({
                    helper: fixHelper,
                    cancel: ".dataTables_empty, .not_draggable",
                    connectWith: ".items tbody",
                    receive: function( event, ui ) {
                        var drag_id = ui.sender.closest("table").attr("id");
                        var group = ui.item.parent().parent().attr("id");
                        var item_id = jq.trim(ui.item.children('td').html());
                        var program_id = jq("#program_id").val();
                        
                        if(group.indexOf("semester_table") == 0)
                        {
                            var type = "add";
                            var sData = group.split("_");
                            var semester_no = sData[2];
                            
                            var status = SubjectAsignExecute(program_id,item_id,semester_no,type);
                            var a_data = status.split("++");  
                            
                            if ( a_data[0] == "SAVE" ) 
                            {
                                /**/
                                if(jq('#subject_list_outline tbody tr').length==0)
                                {
                                    jq('#subject_list_outline tbody').append('<tr class="odd"><td valign="top" colspan="4" class="dataTables_empty"></td></tr>');
                                }
                                
                            }
                            else
                            {
                                ui.item.remove();                                
                                
                            }    
                        } 
                        if(group.indexOf("semester_table") == -1 )
                        {
                            var type = "remove";
                            var sData = drag_id.split("_");
                            var semester_no = sData[2];
                            
                            var status = SubjectAsignExecute(program_id,item_id,semester_no,type);
                            var a_data = status.split("++");  
                            if ( a_data[0] == "SAVE" ) 
                            {
                                /**/
                                if(jq('#'+drag_id+' .ui-sortable tr').length==0)
                                {
                                    jq('#'+drag_id+' .ui-sortable').append('<tr class="odd"><td valign="top" colspan="4" class="dataTables_empty"></td></tr>');
                                }
                            }
                            else
                            {
                                ui.item.remove();                           
                            }  
                        }
                    }
                }).disableSelection();

                jq(".buttonSave").on("click", function(){
                    executeCourseOutline();
                });
            }
        });
    }
    else
    {
        jq("#semester_info").html("");
        oTableDept.columns().search("").draw();
    }
}
function SubjectAsignExecute(program_id,subject_id,semester_no,type)
{
    var returntext = "", url = "";
    if(type == 'add') {
        url = "/setup/saveSubjectOfProgram";
    } else {
        url = "/setup/deleteSubjectOfProgram";    }  
    
    jq.ajax({
       type: 'POST' ,
       url: url,
       async: false,
       data : {
         subject_id: subject_id,
         program_id: program_id,
         semester_no: semester_no          
       },
       success : function(datareturn) {
           returntext = datareturn;

       }
   });
    return returntext;
}
function drawTable(e)
{
    //department-name-course-outline
    if (e.params.data.text.indexOf("Select a Department") == -1)
    {
        var department_id = e.params.data.id;
        oTableDept.columns(0).search(department_id).draw();
    }
    else
    {
        oTableDept.columns().search("").draw();
    }
}
function rearrange_semester(settings)
{
    var course_ids = "";
    jq(".semester_table").each(function(){
        var id = this.id;
        jq("#" + id + " tbody tr").each(function(){
            if ( ! jq(this).children("td").first().hasClass('dataTables_empty') )
            {
              var course_id = jq(this).children("td").first().html();
              if ( course_id.length > 0 )
              {
                  course_ids += jq.trim(course_id) + ",";
              }
            }
        });
    });
    var a_course_ids = [];
    if ( course_ids.indexOf(",") != -1 )
    {
      course_ids = course_ids.substr(0, course_ids.length - 1);
      a_course_ids = course_ids.split(",");
    }
    
    var api = this.api();
    for(  var j=0; j<a_course_ids.length; j++ )
    {
        rows = api.rows().data();
        api.column(0).data().each( function( text, rowNum, colStack ){
            var current_row = rows[rowNum];
            if (current_row[1] == a_course_ids[j])
            {
                api.rows( rowNum ).remove();
            }
        });
    }
}
function numinput()
{
    jq(document).off("click","#up-number").on("click","#up-number", function(){
        if ( typeof(jq(this).data('bind-input')) != 'undefined' )
        {
            var input = jq(this).data('bind-input');
            if ( jq("#" + input).length > 0 )
            {
                if ( typeof(jq(this).data('max')) != 'undefined' )
                {
                    max = parseInt(jq(this).data('max'));
                    current_val = parseInt(jq("#" + input).val());
                    if ( current_val < max )
                    {
                        current_val++;
                        jq("#" + input).val(current_val);
                    }
                }
            }
        }
    });
    
    jq(document).off("click","#down-number").on("click","#down-number", function(){
        if ( typeof(jq(this).data('bind-input')) != 'undefined' )
        {
            var input = jq(this).data('bind-input');
            if ( jq("#" + input).length > 0 )
            {
                if ( typeof(jq(this).data('min')) != 'undefined' )
                {
                    min = parseInt(jq(this).data('min'));
                    current_val = parseInt(jq("#" + input).val());
                    if ( current_val > min )
                    {
                        current_val--;
                        jq("#" + input).val(current_val);
                    }
                }
            }
        }
    });
}