/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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

function executeAjaxRequest( $wiz )
{
  if ( currentProgress == 0 )
  {
    return ( oTable.rows().count() > 0 ) ? executeFaculty() : false;
  }
  else if ( currentProgress == 1 )
  {
    return ( oTable.rows().count() > 0 ) ? executeDepartment() : false;
  }
  else if ( currentProgress == 2 )
  {
    return ( oTable.rows().count() > 0 ) ? executeProgram() : false;
  }
  else if ( currentProgress == 3 )
  {
    return ( oTable.rows().count() > 0 ) ? executeBatch() : false;
  }
  else if ( currentProgress == 4 )
  {
    return ( oTable.rows().count() > 0 ) ? executeSemester() : false;
  }
  else if ( currentProgress == 5 )
  {
    return ( oTable.rows().count() > 0 ) ? executeCourse() : false;
  }
  else if ( currentProgress == 6 )
  {
    return goToLastStep();
  }
  else
  {
    return false;
  }
}

function init( current_step, current )
{
    current_step_progress = current_step;
    currentProgress = current;
}

function getTableHash()
{
    var tableStruct = [];
    var i = 0;
    jq(".wizard_table thead th").each(function(){
        if ( jq(this).closest(".step-content").css('display') == 'block' )
        {
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
        }
    });
    
    return tableStruct;
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

function reArrangeSetup()
{
    var l = 0;
    var found = false;
    var make_select = false;
    jq(".wizard_steps li").each(function(){
        if ( ! found )
        {
          jq(this).children("a").addClass("done");  
        }
        l++;
        if ( ! found && l >= currentProgress )
        {
          found = true;
          jq(this).children("a").addClass("selected");  
          jq(this).children("a").removeClass("disabled");  
          return true;
        }
        else if ( found && ! make_select )
        {
          make_select = true;
          jq(this).children("a").addClass("selected");  
          jq(this).children("a").removeClass("disabled");  
          return true;
        }
    });
    if (currentProgress == 7)
    {
        jq('#setup-info').css('display',"block");
        jq('#setup-wizard').addClass("col-md-8");
        jq('#setup-wizard').removeClass("col-md-12");
    }
}

function createDatatable()
{
    jq(".wizard_table").each(function(){
        if ( jq(this).closest(".step-content").css('display') != 'none' )
        {
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
                columns: getTableHash(),
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
        }
    });
    numinput();
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
function dtPreDrawCallback(settings)
{
    
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

function get_subject(e)
{
    if (e.params.data.text.indexOf("Select a Department") == -1)
    {
        jq.ajax({
            type: 'POST' ,
            url: "/dashboards/get_subjects",
            async: false,
            data : {
              department_id: e.params.data.id
            },
            success : function(data) {
              jq("#prerequisite").html(data);
              jq(".prerequisite-course").select2({
                  maximumSelectionLength: 4,
                  placeholder: "Select Prerequisite Course",
                  allowClear: true
              });
            }
        });
        reloadCourse(e.params.data.id);
    }
}

function reloadCourse(dept_id)
{
    jq("#course_list").dataTable().fnDestroy();
        
    jq.ajax({
        type: 'POST' ,
        url: "/dashboards/get_subjects_tables",
        async: false,
        data : {
          department_id: dept_id
        },
        success : function(data) {
          jq("#course_list").html(data);
          oTable = jq('#course_list').DataTable({
              bDestroy: true,
              deferRender: true,
              pagingType: 'simple',
              bLengthChange: false,
              columns: [
                { visible: false }, { visible: false }, null, null, { visible: false }, null, { visible: false }, { orderable: false }
              ],
              initComplete: function(settings, json) {
                jq('#course_list_wrapper').children(".row").first().hide();
              }
          });
          jq('#course_list tbody').on( 'click', 'td a.delete', function () {
              var rowIdx = oTable.cell( jq(this).parent() ).index().row;
              oTable.rows( rowIdx ).remove().draw();
          } );
        }
    });
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

function init_tags(e)
{
    if (e.params.data.text.indexOf("Select a Batch") == -1)
    {
        jq.ajax({
            type: 'POST' ,
            url: "/dashboards/get_semester",
            async: false,
            data : {
              batch_id: e.params.data.id
            },
            success : function(data) {
              jq("#semeter_div").html(data);
              jq('.tags_input').tagsInput({
                    width: 'auto',
                    defaultText: '+ Section'
              });
            }
        });
    }
}

function get_batches(e)
{
    if (e.params.data.text.indexOf("Select a Program") == -1)
    {
        jq.ajax({
            type: 'POST' ,
            url: "/dashboards/get_batches",
            async: false,
            data : {
              program_id: e.params.data.id
            },
            success : function(data) {
              jq("#batch-div").html(data);
              createCombo();
            }
        });
    }
}

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

function createSortable()
{
    if ( jq(".items tbody").length > 0 )
    {
        jq(".items tbody").sortable({
            helper: fixHelper,
            connectWith: ".linked tbody" // this is like magic
        }).disableSelection();
    }
    
    if ( jq(".semester_table tbody, .projects tbody").length > 0 )
    {
        jq(".semester_table tbody, .projects tbody").sortable({
                helper: fixHelper,
                cancel: ".dataTables_empty",
                connectWith: ".items tbody" // this is like magic
        }).disableSelection();
    }
}

jq(document).ready(function() {
    jq(document).off("keydown",".number_input").on("keydown",".number_input", function(e){
        // Allow: backspace, delete, tab, escape, enter and .
        if (jq.inArray(e.keyCode, [46, 8, 9, 27, 110, 190]) !== -1 ||
             // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) ||
             // Allow: Ctrl+C
            (e.keyCode == 67 && e.ctrlKey === true) ||
             // Allow: Ctrl+X
            (e.keyCode == 88 && e.ctrlKey === true) ||
             // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }

        if (e.keyCode == 13) {
            e.preventDefault();
        }
    });

    jq(document).on("blur",".number_input", function(){
        if ( isNaN(parseInt(this.value)) )
        {
          jq(this).val("0")
        }
        if ( typeof(jq(this).data('max')) != 'undefined' )
        {
            if ( parseInt(jq(this).val()) > parseInt(jq(this).data('max')) )
            {
                jq(this).val(jq(this).data('max'));
            }
        }
        if ( typeof(jq(this).data('min')) != 'undefined' )
        {
            if ( parseInt(jq(this).val()) < parseInt(jq(this).data('min')) )
            {
                jq(this).val(jq(this).data('min'));
            }
        }
    });

    jq(document).on("blur",".min_credit_hour", function(e){
        if ( this.value > jq("#total_credit_hours").val() )
        {
            e.preventDefault();
            alert("Credit hours can't exceed the total credit hour of this batch");
        }
    });

    jq(document).on("blur",".max_credit_hour", function(e){
        if ( this.value > jq("#total_credit_hours").val() )
        {
            e.preventDefault();
            alert("Credit hours can't exceed the total credit hour of this batch");
        }
    });

    jq(document).on("keydown","#faculty-name",function(e){
        if ( e.keyCode == 13 )
        {
            jq("#faculty").trigger("click");
        }
    });

    jq(document).on("keydown","#department-name",function(e){
        if ( e.keyCode == 13 )
        {
            jq("#department").trigger("click");
        }
    });

    jq(document).on("keydown","#program-name",function(e){
        if ( e.keyCode == 13 )
        {
            jq("#program").trigger("click");
        }
    });

    jq(document).off("click","#faculty").on("click","#faculty",function(){
        if ( jq.trim(jq("#faculty-name").val()).length > 0 )
        {
          if ( oTable.rows().count() > 0 )
          {
            var b_found = false;
            for(var i=0; i<oTable.rows().count(); i++)
            {
              var dt = oTable.rows(i).data();
              if ( jq.trim(jq("#faculty-name").val()) == jq.trim(dt[0][0]))
              {
                b_found = true;
                break;
              }       
            }

            if ( b_found )
            {
              alert("Faculty with same name already exists");
              return ;
            }
          }
          oTable.row.add( [ jq("#faculty-name").val(), "<a href='javascript:;' class='btn btn-danger btn-xs delete'>Delete</a>"]).draw();
          jq("#faculty-name").val("");
        }

        return false;
    });

    jq(document).off("click","#department").on("click","#department",function(){
        if ( jq.trim(jq("#faculty-name-select").val()).length > 0 && jq.trim(jq("#department-name").val()).length > 0 )
        {
          if ( oTable.rows().count() > 0 )
          {
            var b_found = false;
            for(var i=0; i<oTable.rows().count(); i++)
            {
              var dt = oTable.rows(i).data();
              if ( (jq.trim(jq("#faculty-name-select option:selected").text()) == jq.trim(dt[0][1])) && jq.trim(jq("#department-name").val()) == jq.trim(dt[0][2]))
              {
                b_found = true;
                break;
              }       
            }

            if ( b_found )
            {
              alert("Department with same name already exists");
              return ;
            }
          }
          oTable.row.add( [ jq("#faculty-name-select").val(), jq("#faculty-name-select option:selected").text(), jq("#department-name").val(), "<a href='javascript:;' class='btn btn-danger btn-xs delete'>Delete</a>"]).draw();
          jq("#department-name").val("");
        }

        return false;
    });

    jq(document).off("click","#program").on("click","#program",function(){
        var error = 0;
        var all_data_present = true;
        var program_code_present = true;
        var program_name_present = true;
        var txt_message = "Required Field Missing, Please check before continue...\n\n";
        if (  jq.trim(jq("#credit-hour").val()).length == 0 )
        {
            error++;
            all_data_present = false;
        }

        if ( isNaN(parseInt(jq.trim(jq("#credit-hour").val()))) )
        {
            error++;
            all_data_present = false;
        }
        
        if (  jq.trim(jq("#program-code").val()).length == 0 )
        {
            error++;
            program_code_present = false;
        }
        
        if (  jq.trim(jq("#program-name").val()).length == 0 )
        {
            error++;
            program_name_present = false;
        }
        l = 1;
        if ( ! all_data_present )
        {
            txt_message += l + ". Invalid Credit Hours, Please check before continue... \n";
            l++;
        }
        
        if ( ! program_code_present )
        {
            txt_message += l + ". Invalid Program Code, Please check before continue... \n";
            l++;
        }
        
        if ( ! program_name_present )
        {
            txt_message += l + ". Invalid Program Name, Please check before continue... \n";
            l++;
        }
        
        if ( error == 0 && jq.trim(jq("#department-name-select").val()).length > 0 && jq.trim(jq("#program-name").val()).length > 0  && jq.trim(jq("#program-code").val()).length > 0 )
        {
          if ( oTable.rows().count() > 0 )
          {
            var b_found = false;
            for(var i=0; i<oTable.rows().count(); i++)
            {
              var dt = oTable.rows(i).data();
              if ( (jq.trim(jq("#department-name-select option:selected").text()) == jq.trim(dt[0][1])) && jq.trim(jq("#program-name").val()) == jq.trim(dt[0][2]))
              {
                b_found = true;
                break;
              }       
            }

            if ( b_found )
            {
              alert("Program with same name already exists");
              return ;
            }
          }
          if ( oTable.rows().count() > 0 )
          {
            var b_found = false;
            for(var i=0; i<oTable.rows().count(); i++)
            {
              var dt = oTable.rows(i).data();
              if ( (jq.trim(jq("#program-code").val()) == jq.trim(dt[0][3])))
              {
                b_found = true;
                break;
              }       
            }

            if ( b_found )
            {
              alert("Program with same code already exists");
              return ;
            }
          }
          oTable.row.add( [ jq("#department-name-select").val(), 
                            jq("#department-name-select option:selected").text(), 
                            jq("#program-name").val(), jq("#program-code").val(), 
                            jq("#semester_no").val(), jq("#credit-hour").val(), 
                            "<a href='javascript:;' class='btn btn-danger btn-xs delete'>Delete</a>"]).draw();
          jq("#program-name").val("");
        }
        else
        {
            alert(txt_message);
        }

        return false;
    });

    jq(document).off("click","#batch").on("click","#batch",function(){
        var all_data_present = true;

        if ( jq.trim(jq("#program-name-select").val()).length == 0 )
        {
            message = "";
            all_data_present = false;
        }

        if (  jq.trim(jq("#batch-name").val()).length == 0 )
        {
            all_data_present = false;
        }

        if (  jq.trim(jq("#batch-code").val()).length == 0 )
        {
            all_data_present = false;
        }
        
        if ( all_data_present )
        {
          if ( oTable.rows().count() > 0 )
          {
            var b_found = false;
            var b_code_found = false;
            for(var i=0; i<oTable.rows().count(); i++)
            {
              var dt = oTable.rows(i).data();
              var code = jq.trim(dt[0][3]);
              var program_id = jq.trim(dt[0][0]);
              var name = jq.trim(dt[0][1]);
              var batch_name = jq.trim(dt[0][2]);
              if ( code == jq.trim( jq("#batch-code").val()) )
              {
                    b_code_found = true;
                    break;
              }
              else
              {
                  if ( batch_name == jq.trim( jq("#batch-name").val()) && program_id == jq("#program-name-select").val() )
                  {
                    b_found = true;
                    break;
                  }
              }
            }

            if ( b_code_found )
            {
              alert("Duplicate Code, Already exists");
              return
            }
            if ( b_found )
            {
              alert("Batch with same name already exists");
              return ;
            }
          }
          oTable.row.add( [ jq("#program-name-select").val(), 
                                 jq("#program-name-select option:selected").text(), 
                                 jq("#batch-name").val(), jq("#batch-code").val(), 
                                 jq("#section_no").val(), jq("#min_credit_hour").val(), jq("#max_credit_hour").val(),
                                 "<a href='javascript:;' class='btn btn-danger btn-xs delete'>Delete</a>"]).draw();
          jq("#batch-name").val("");
          jq("#batch-code").val("");
        }
        else
        {
            alert("Please check data before continue... ");
        }

        return false;
    });

    jq(document).off("click","#semester").on("click","#semester",function(){
        var num_semester = jq("#total_semester_no").val();
        var min_exceed = false;
        var max_exceed = false;
        var b_found_batch = false;

        if ( oTable.rows().count() > 0 )
        {
            for(var i=0; i<oTable.rows().count(); i++)
            {
                var dt = oTable.rows(i).data();
                var batch_id = jq.trim(dt[0][0]);

                if ( batch_id == jq.trim( jq("#batch-name-select").val()) )
                {
                      b_found_batch = true;
                      break;
                }
            }
        }

        if ( b_found_batch )
        {
          alert("You already save semster info for this batch, please manually remove them and try again");
          return ;
        }
        
        var error = false, min_exceed = false, max_exceed = false, i = 1;
        jq(".tags_input").each(function(){
            if (  jq.trim(jq("#min_credit_hour_" + i).val()).length == 0 )
            {
                error = true;
                return false;
            }

            if ( !error && isNaN(parseInt(jq.trim(jq("#min_credit_hour_" + i).val()))) )
            {
                error = true;
                return false;
            }
            
            if ( !error && parseInt(jq.trim(jq("#min_credit_hour_" + i).val())) >  jq("#total_credit_hours").val() )
            {
                min_exceed = true;
                error = true;
            }
            
            if ( !error && !min_exceed && jq.trim(jq("#max_credit_hour_" + i).val()).length == 0 )
            {
                error = true;
            }
            
            if ( !error && !min_exceed && isNaN(parseInt(jq.trim(jq("#max_credit_hour_" + i).val()))) )
            {
                error = true;
            }
            
            if ( !error && !min_exceed && parseInt(jq.trim(jq("#max_credit_hour_" + i).val())) >  jq("#total_credit_hours").val() )
            {
                max_exceed = true;
                error = true;
            }
            i++;
        });
        
        if ( error )
        {
            if ( min_exceed )
            {
                alert("Min Credit hours exceed the Total Credit hour for this Batch, please try decreasing the min credit hours");
            }
            else if ( max_exceed )
            {
                alert("Max Credit hours exceed the Total Credit hour for this Batch, please try decreasing the min credit hours");
            }
            else
            {
                alert("Invalid Minimum or Maximum Credit Hours");
            }
            return false;
        }
        
        var i = 1;
        jq(".tags_input").each(function(){
            oTable.row.add( [   jq("#batch-name-select").val(), 
                                jq("#batch-name-select option:selected").text(), 
                                "Semester " + i, jq("#min_credit_hour_" + i).val(), 
                                jq("#max_credit_hour_" + i).val(), i, jq(this).val(),
                                "<a href='javascript:;' class='btn btn-danger btn-xs delete'>Delete</a>"]).draw();
            i++;

        });
        
        jq("#batch-div").html('<label class="control-label col-md-4 col-sm-4" for="faculty-name">\n\
                               Batch <span class="required">*</span></label><div class="col-md-6 col-sm-6">\n\
                               <select class="batch-name-combo form-control" tabindex="-1" id="batch-name-select" name="faculty-name-select" style="width: 100%;">\n\
                               <option></option>');

        jq(".batch-name-combo").select2({});    

        jq("#semeter_div").html("");

        jq("#program-name-semester").val("").trigger("change");
        
        jq(window).scrollTop(50);

        return false;
    });

    jq(document).off("click","#course").on("click","#course",function(){
        var all_data_present = true;

        if ( jq.trim(jq("#department-name-course-select").val()).length == 0 )
        {
            all_data_present = false;
        }

        if (  jq.trim(jq("#course-name").val()).length == 0 )
        {
            all_data_present = false;
        }

        if (  jq.trim(jq("#course-code").val()).length == 0 )
        {
            all_data_present = false;
        }

        if (  jq.trim(jq("#max-classes").val()).length == 0 )
        {
            all_data_present = false;
        }

        if ( isNaN(parseInt(jq.trim(jq("#max-classes").val()))) )
        {
            all_data_present = false;
        }
        else if ( parseInt(jq.trim(jq("#max-classes").val())) <= 0 )
        {
            all_data_present = false;
        }

        if (  jq.trim(jq("#total-credit-hours").val()).length == 0 )
        {
            all_data_present = false;
        }

        if ( isNaN(parseInt(jq.trim(jq("#total-credit-hours").val()))) )
        {
            all_data_present = false;
        }
        else if ( parseInt(jq.trim(jq("#total-credit-hours").val())) <= 0 )
        {
            all_data_present = false;
        }

        if ( all_data_present )
        {
          if ( oTable.rows().count() > 0 )
          {
            var b_found = false;
            var b_code_found = false;
            for(var i=0; i<oTable.rows().count(); i++)
            {
              var dt = oTable.rows(i).data();
              var code = jq.trim(dt[0][3]);
              var dept_id = jq.trim(dt[0][0]);
              var dept_name = jq.trim(dt[0][1]);
              var course_name = jq.trim(dt[0][2]);
              if ( code == jq.trim( jq("#course-code").val()) )
              {
                    b_code_found = true;
                    break;
              }
              else
              {
                  if ( course_name == jq.trim( jq("#course-name").val()) && dept_id == jq("#department-name-course-select").val() )
                  {
                    b_found = true;
                    break;
                  }
              }
            }

            if ( b_code_found )
            {
              alert("Duplicate Code, Already exists");
              return
            }
            if ( b_found )
            {
              alert("Course with same name already exists");
              return ;
            }
          }
          oTable.row.add( [ jq("#department-name-course-select").val(), 
                                 jq("#department-name-course-select option:selected").text(), 
                                 jq("#course-name").val(), jq("#course-code").val(), 
                                 jq("#max-classes").val(), jq("#total-credit-hours").val(), 
                                 jq("#prerequisite-course").val(), 
                                 "<a href='javascript:;' class='btn btn-danger btn-xs delete'>Delete</a>"]).draw();

          jq("#course-name").val("");
          jq("#course-code").val("");

          jq("#max-classes").val("0");
          jq("#total-credit-hours").val("0");
          jq("#prerequisite-course").val("").trigger("change");
          //jq("#prerequisite").html("");

          //jq("#department-name-course-select").val("").trigger("change");
        }
        else
        {
            alert("Please check data before continue... ");
        }

        return false;
    });

    jq(document).off("keydown",".no_enter_return").on("keydown",".no_enter_return",function(e){
        if ( e.keyCode == 13 )
        {
            return false;
        }
    });
    
});

function executeFaculty()
{
  var b_excute = false;
  var count_faculty = oTable.rows().count();
  if ( count_faculty > 0 )
  {
      var faculties = "";
      for(var i=0; i<oTable.rows().count(); i++)
      {
        var dt = oTable.rows(i).data();
        faculties += jq.trim(dt[0][0]) + ",";
      }
      faculties = faculties.substr(0, faculties.length - 1);
      jq("#" + currentDatatable).removeClass('wizard_table');
      jq.ajax({
          type: 'POST' ,
          url: "/dashboards/save_faculty",
          async: false,
          data : {
            faculties: faculties
          },
          success : function(data) {
            var a_data = data.split("++");
            if ( typeof(a_data[0]) != 'undefined' )
            {
              if ( a_data[0] == "NOT_SAVE" )
              {
                  alert("No data to Save, please check and try again");
              }
              else if ( a_data[0] == "SAVE" )
              {
                  jq(".count").html(a_data[1] + "%");
                  jq('.progress .progress-bar').data("transitiongoal", a_data[1]);
                  jq('.progress .progress-bar').data("valuenow", a_data[1]);
                  jq('.progress .progress-bar').css("width", a_data[1] + "%");
                  jq("#current_item").html(a_data[2]);
                  newProgress = Math.round(a_data[1] / 10);
                  
                  if ( newProgress > currentProgress )
                  {
                      currentProgress = newProgress;
                      jq.ajax({
                          type: 'POST' ,
                          url: "/dashboards/getStep",
                          async: false,
                          data : {
                            currentProgress: newProgress
                          },
                          success : function(data) {
                            var currentStep = jq('#wizard_verticle').smartWizard('currentStep') + 1;
                            jq("#step-" + currentStep + currentStep).html(data);
                            var tShow = setInterval(function(){
                                if ( jq("#step-" + currentStep + currentStep).find(".wizard_table").css("display") != 'none' && jq("#step-" + currentStep + currentStep).css('display') == 'block' )
                                {
                                    createDatatable();
                                    reArrangeSetup();
                                    createCombo();
                                    createDate();
                                    jq(window).scrollTop(50);
                                    clearInterval(tShow);
                                }
                            }, 100);
                            b_excute = true;
                          }
                      });
                      b_excute = true;
                  }
              }
            }
          }
      });
  }
  return b_excute;
}

function executeDepartment()
{
  var b_excute = false;
  var count_department = oTable.rows().count();
  if ( count_department > 0 )
  {
      var departments = "";
      for(var i=0; i<oTable.rows().count(); i++)
      {
        var dt = oTable.rows(i).data();
        departments += jq.trim(dt[0][0]) + "++" + jq.trim(dt[0][2]) + ",,";
      }
      departments = departments.substr(0, departments.length - 2);
      jq("#" + currentDatatable).removeClass('wizard_table');
      jq.ajax({
          type: 'POST' ,
          url: "/dashboards/save_department",
          async: false,
          data : {
            dept_data: departments
          },
          success : function(data) {
            var a_data = data.split("++");
            if ( typeof(a_data[0]) != 'undefined' )
            {
              if ( a_data[0] == "NOT_SAVE" )
              {
                  alert("No data to Save, please check and try again");
              }
              else if ( a_data[0] == "SAVE" )
              {
                  jq(".count").html(a_data[1] + "%");
                  jq('.progress .progress-bar').data("transitiongoal", a_data[1]);
                  jq('.progress .progress-bar').data("valuenow", a_data[1]);
                  jq('.progress .progress-bar').css("width", a_data[1] + "%");
                  jq("#current_item").html(a_data[2]);
                  newProgress = Math.round(a_data[1] / 10);
                  if ( newProgress > currentProgress )
                  {
                      currentProgress = newProgress;
                      jq.ajax({
                          type: 'POST' ,
                          url: "/dashboards/getStep",
                          async: false,
                          data : {
                            currentProgress: newProgress
                          },
                          success : function(data) {
                            var currentStep = jq('#wizard_verticle').smartWizard('currentStep') + 1;
                            jq("#step-" + currentStep + currentStep).html(data);
                             var tShow = setInterval(function(){
                                if ( jq("#step-" + currentStep + currentStep).find(".wizard_table").css("display") != 'none' && jq("#step-" + currentStep + currentStep).css('display') == 'block' )
                                {
                                    createDatatable();
                                    reArrangeSetup();
                                    createCombo();
                                    createDate();
                                    jq(window).scrollTop(50);
                                    clearInterval(tShow);
                                }
                            }, 100);
                            b_excute = true;
                          }
                      });
                      b_excute = true;
                  }
              }
            }
          }
      });
  }
  return b_excute;
}

function executeProgram()
{
  var b_excute = false;
  var countProgram = oTable.rows().count();

  if ( countProgram > 0 )
  {
      var programs = "";
      for(var i=0; i<oTable.rows().count(); i++)
      {
        var dt = oTable.rows(i).data();
        programs += jq.trim(dt[0][0]) + "++" + jq.trim(dt[0][2]) + "++" + jq.trim(dt[0][3]) + "++" + jq.trim(dt[0][4]) + "++" + jq.trim(dt[0][5]) + ",,";
      }
      programs = programs.substr(0, programs.length - 2);
      jq.ajax({
          type: 'POST' ,
          url: "/dashboards/save_program",
          async: false,
          data : {
            program_data: programs
          },
          success : function(data) {
            var a_data = data.split("++");
            if ( typeof(a_data[0]) != 'undefined' )
            {
              if ( a_data[0] == "NOT_SAVE" )
              {
                  alert("No data to Save, please check and try again");
              }
              else if ( a_data[0] == "SAVE" )
              {
                  jq(".count").html(a_data[1] + "%");
                  jq('.progress .progress-bar').data("transitiongoal", a_data[1]);
                  jq('.progress .progress-bar').data("valuenow", a_data[1]);
                  jq('.progress .progress-bar').css("width", a_data[1] + "%");
                  jq("#current_item").html(a_data[2]);
                  newProgress = Math.round(a_data[1] / 10);
                  if ( newProgress > currentProgress )
                  {
                      currentProgress = newProgress;
                      jq.ajax({
                          type: 'POST' ,
                          url: "/dashboards/getStep",
                          async: false,
                          data : {
                            currentProgress: newProgress
                          },
                          success : function(data) {
                            var currentStep = jq('#wizard_verticle').smartWizard('currentStep') + 1;
                            jq("#step-" + currentStep + currentStep).html(data);
                            var tShow = setInterval(function(){
                                if ( jq("#step-" + currentStep + currentStep).find(".wizard_table").css("display") != 'none' && jq("#step-" + currentStep + currentStep).css('display') == 'block' )
                                {
                                    createDatatable();
                                    reArrangeSetup();
                                    createCombo();
                                    createDate();
                                    jq(window).scrollTop(50);
                                    clearInterval(tShow);
                                }
                            }, 100);
                            b_excute = true;
                          }
                      });
                      b_excute = true;
                  }
              }
            }
          }
      });
  }
  return b_excute;
}

function executeBatch()
{
  var b_excute = false;
  var count_batch = oTable.rows().count();

  if ( count_batch > 0 )
  {
      var batches = "";
      for(var i=0; i<oTable.rows().count(); i++)
      {
        var dt = oTable.rows(i).data();
        batches += jq.trim(dt[0][0]) + "++" + jq.trim(dt[0][2]) + "++" + jq.trim(dt[0][3]) + "++" + jq.trim(dt[0][4]) + "++" + jq.trim(dt[0][5]) + "++" + jq.trim(dt[0][6]) + ",,";
      }
      batches = batches.substr(0, batches.length - 2);
      jq.ajax({
          type: 'POST' ,
          url: "/dashboards/save_batches",
          async: false,
          data : {
            batch_data: batches
          },
          success : function(data) {
            var a_data = data.split("++");
            if ( typeof(a_data[0]) != 'undefined' )
            {
              if ( a_data[0] == "NOT_SAVE" )
              {
                  alert("No data to Save, please check and try again");
              }
              else if ( a_data[0] == "SAVE" )
              {
                  jq(".count").html(a_data[1] + "%");
                  jq('.progress .progress-bar').data("transitiongoal", a_data[1]);
                  jq('.progress .progress-bar').data("valuenow", a_data[1]);
                  jq('.progress .progress-bar').css("width", a_data[1] + "%");
                  jq("#current_item").html(a_data[2]);
                  newProgress = Math.round(a_data[1] / 10);
                  if ( newProgress > currentProgress )
                  {
                      currentProgress = newProgress;
                      jq.ajax({
                          type: 'POST' ,
                          url: "/dashboards/getStep",
                          async: false,
                          data : {
                            currentProgress: newProgress
                          },
                          success : function(data) {
                            var currentStep = jq('#wizard_verticle').smartWizard('currentStep') + 1;
                            jq("#step-" + currentStep + currentStep).html(data);
                            var tShow = setInterval(function(){
                                if ( jq("#step-" + currentStep + currentStep).find(".wizard_table").css("display") != 'none' && jq("#step-" + currentStep + currentStep).css('display') == 'block' )
                                {
                                    createDatatable();
                                    reArrangeSetup();
                                    createCombo();
                                    createDate();
                                    jq(window).scrollTop(50);
                                    clearInterval(tShow);
                                }
                            }, 100);
                            b_excute = true;
                          }
                      });
                      b_excute = true;
                  }
              }
            }
          }
      });
  }
  return b_excute;
}

function executeSemester()
{
  var b_excute = false;
  var countSemester = oTable.rows().count();

  if ( countSemester > 0 )
  {
      var semesters = "";
      for(var i=0; i<oTable.rows().count(); i++)
      {
        var dt = oTable.rows(i).data();
        semesters += jq.trim(dt[0][0]) + "++" + jq.trim(dt[0][5]) + "++" + jq.trim(dt[0][3]) + "++" + jq.trim(dt[0][4]) + "++" + jq.trim(dt[0][6]) + ",,";
      }
      semesters = semesters.substr(0, semesters.length - 2);
      jq.ajax({
          type: 'POST' ,
          url: "/dashboards/save_semesters",
          async: false,
          data : {
            semesters_data: semesters
          },
          success : function(data) {
            var a_data = data.split("++");
            if ( typeof(a_data[0]) != 'undefined' )
            {
              if ( a_data[0] == "NOT_SAVE" )
              {
                  alert("No data to Save, please check and try again");
              }
              else if ( a_data[0] == "SAVE" )
              {
                  jq(".count").html(a_data[1] + "%");
                  jq('.progress .progress-bar').data("transitiongoal", a_data[1]);
                  jq('.progress .progress-bar').data("valuenow", a_data[1]);
                  jq('.progress .progress-bar').css("width", a_data[1] + "%");
                  jq("#current_item").html(a_data[2]);
                  newProgress = Math.round(a_data[1] / 10);
                  if ( newProgress > currentProgress )
                  {
                      currentProgress = newProgress;
                      jq.ajax({
                          type: 'POST' ,
                          url: "/dashboards/getStep",
                          async: false,
                          data : {
                            currentProgress: newProgress
                          },
                          success : function(data) {
                            var currentStep = jq('#wizard_verticle').smartWizard('currentStep') + 1;
                            jq("#step-" + currentStep + currentStep).html(data);
                            var tShow = setInterval(function(){
                                if ( jq("#step-" + currentStep + currentStep).find(".wizard_table").css("display") != 'none' && jq("#step-" + currentStep + currentStep).css('display') == 'block' )
                                {
                                    createDatatable();
                                    reArrangeSetup()
                                    createCombo();
                                    createDate();
                                    jq(window).scrollTop(50);
                                    clearInterval(tShow);
                                }
                            }, 100);
                            b_excute = true;
                          }
                      });
                      b_excute = true;
                  }
              }
            }
          }
      });
  }
  return b_excute;
}

function executeCourse()
{
  var b_excute = false;
  var countCourse = oTable.rows().count();
  
  if ( countCourse > 0 )
  {
      var courses = "";
      for(var i=0; i<oTable.rows().count(); i++)
      {
        var dt = oTable.rows(i).data();
        courses += jq.trim(dt[0][0]) + "++" + jq.trim(dt[0][2]) + "++" + jq.trim(dt[0][3]) + "++0++" + jq.trim(dt[0][4]) + "++" + jq.trim(dt[0][5]) + "++" + jq.trim(dt[0][6]) + ",,";
      }
      courses = courses.substr(0, courses.length - 2);
      jq.ajax({
          type: 'POST' ,
          url: "/dashboards/save_courses",
          async: false,
          data : {
            courses_data: courses
          },
          success : function(data) {
            var a_data = data.split("++");
            if ( typeof(a_data[0]) != 'undefined' )
            {
              if ( a_data[0] == "NOT_SAVE" )
              {
                  alert("No data to Save, please check and try again");
              }
              else if ( a_data[0] == "SAVE" )
              {
                  jq(".count").html(a_data[1] + "%");
                  jq('.progress .progress-bar').data("transitiongoal", a_data[1]);
                  jq('.progress .progress-bar').data("valuenow", a_data[1]);
                  jq('.progress .progress-bar').css("width", a_data[1] + "%");
                  jq("#current_item").html(a_data[2]);
                  newProgress = Math.round(a_data[1] / 10);
                  if ( newProgress > currentProgress )
                  {
                      currentProgress = newProgress;
                      jq.ajax({
                          type: 'POST' ,
                          url: "/dashboards/getStep",
                          async: false,
                          data : {
                            currentProgress: currentProgress
                          },
                          success : function(data) {
                            var currentStep = jq('#wizard_verticle').smartWizard('currentStep') + 1;
                            jq("#step-" + currentStep + currentStep).html(data);
                            jq('#setup-info').css('display',"none");
                            jq('#setup-wizard').removeClass("col-md-8");
                            jq('#setup-wizard').addClass("col-md-12");
                            var tShow = setInterval(function(){
                                if ( jq("#step-" + currentStep + currentStep).find(".wizard_table").css("display") != 'none' && jq("#step-" + currentStep + currentStep).css('display') == 'block' )
                                {
                                    createDatatable();
                                    reArrangeSetup();
                                    createCombo();
                                    createDate();
                                    jq(window).scrollTop(50);
                                    clearInterval(tShow);
                                }
                            }, 100);
                            b_excute = true;
                          }
                      });
                      b_excute = true;
                  }
                  else
                  {
                      jq(window).scrollTop(50);
                      alert("Courses Saved Successfully")
                      reloadCourse(jq("#department-name-course-select").val());
                  }
              }
            }
          }
      });
  }
  return b_excute;
}

function getCurrentProgress()
{
    return currentProgress;
}

function goToLastStep()
{
    var b_excute = false;
    jq.ajax({
        type: 'POST' ,
        url: "/dashboards/isSettingsDone",
        async: false,
        success : function(data) {
          if ( data != "not_done" )
          {
                currentProgress++;
                jq.ajax({
                    type: 'POST' ,
                    url: "/dashboards/getStep",
                    async: false,
                    data : {
                      currentProgress: currentProgress
                    },
                    success : function(data) {
                        var currentStep = jq('#wizard_verticle').smartWizard('currentStep') + 1;
                        jq("#step-" + currentStep + currentStep).html(data);
                        jq(window).scrollTop(50);
                        jq('#setup-info').css('display',"block");
                        jq('#setup-wizard').removeClass("col-md-12");
                        jq('#setup-wizard').addClass("col-md-8");
                        jq('.buttonFinish').css('display',"none");
                        jq('.buttonNext').css('display','none');
                        jq('.buttonPrevious').css('display','none');
                    }
                });
                
                b_excute = true;
          }
        }
    });
    return b_excute;
}

function executeCourseOutline()
{
  var b_excute = false;
  var len = jq(".semester_table").length;
  if ( len == 0 )
  {
    return false;
  }
  else
  {
      var total_record = 0;
      var data = "";
      jq(".semester_table").each(function(){
          var id = this.id;
          var semester_no = this.id.replace("semester_table_","");
          if ( jq.trim(id).length > 0 )
          {
              var dtCourseOutline = jq("#" + id + " tbody tr").length;
              if ( parseInt(dtCourseOutline) > 0 )
              {
                  var i_course_id = 0;
                  var s_courses = "";
                  jq("#" + id + " tbody tr").each(function(){
                        var course_id = jq(this).children("td").first().html();
                        if ( course_id.length > 0)
                        {
                            total_record++;
                            s_courses += course_id + "+";
                            i_course_id++;
                        }
                  });
              }
              if ( i_course_id > 0 )
              {
                  s_courses = s_courses.substr(0, s_courses.length - 1);
                  data += semester_no + "||";
                  data += s_courses + ",,";
                  i_course_id = "";
                  s_courses = ""

              }
          }
      });
  }
  if ( total_record > 0 )
  {
      b_excute = false;
      data = data.substr(0, data.length - 2);
      jq.ajax({
          type: 'POST' ,
          url: "/dashboards/save_course_outline",
          async: false,
          data : {
            courses_data: data,
            program_id: jq("#program_id").val()
          },
          success : function(data) {
            var a_data = data.split("++");
            if ( typeof(a_data[0]) != 'undefined' )
            {
              if ( a_data[0] == "NOT_SAVE" )
              {
                  alert("No data to Save, please check and try again");
              }
              else if ( a_data[0] == "SAVE" )
              {
                  jq(".count").html(a_data[1] + "%");
                  jq('.progress .progress-bar').data("transitiongoal", a_data[1]);
                  jq('.progress .progress-bar').data("valuenow", a_data[1]);
                  jq('.progress .progress-bar').css("width", a_data[1] + "%");
                  jq("#current_item").html(a_data[2]);
                  newProgress = Math.round(a_data[1] / 10);
                  if ( newProgress > currentProgress )
                  {
                      jq.ajax({
                          type: 'POST' ,
                          url: "/dashboards/getStep",
                          async: false,
                          data : {
                            currentProgress: currentProgress
                          },
                          success : function(data) {
                            var currentStep = jq('#wizard_verticle').smartWizard('currentStep') + 1;
                            jq("#step-" + currentStep + currentStep).html(data);
                            jq(window).scrollTop(50);
                            createDatatable();
                            reArrangeSetup();
                            createCombo();
                            createDate();
                          }
                      });
                      b_excute = true;
                  }
              }
            }
          }
      });
  }
  else
  {
      return false;
  }
  return b_excute;
}


jQuery(function($) {

  var _oldShow = $.fn.show;

  $.fn.show = function(speed, oldCallback) {
    return $(this).each(function() {
      var obj         = $(this),
          newCallback = function() {
            if ($.isFunction(oldCallback)) {
              oldCallback.apply(obj);
            }
            obj.trigger('afterShow');
          };

      // you can trigger a before show if you want
      obj.trigger('beforeShow');

      // now use the old function to show the element passing the new callback
      _oldShow.apply(obj, [speed, newCallback]);
    });
  }
});