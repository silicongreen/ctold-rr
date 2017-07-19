var jq = jQuery.noConflict();
var oTable;
var TableCourses = function () {

    var handleTable = function () {

        oTable = table.DataTable({
            "lengthMenu": [
                [5, 10, 20, -1],
                [5, 10, 20, "All"] // change per page values here
            ],
            "pageLength": 10,

            "language": {
                "lengthMenu": " _MENU_ records"
            },
            "columnDefs": [{ // set default column settings
                'orderable': true,
                'targets': [0]
            }, {
                "searchable": true,
                "targets": [0]
            }],
            "order": [
                [0, "asc"]
            ] 
        });
        var save_method; 
        var nRow; 
        var nEditing = null;
        var nNew = false;
        jq(document).on("click","#new_department",function(e){
            e.preventDefault();
            
            jq('#form').find('p').remove();
            jq('#form').find('input , .select2-container').css("border","1px solid #ccc");
            
            jq("#course-name").focus();            
            jq("#edit_id").val("");            
            jq("#department-name-course-select").select2("val", "");
            jq("#course-group-select").select2("val", "");
            jq("#course-name").val("");
            jq("#course-code").val("");
            jq("#max-classes").val("0");            
            jq("#total-credit-hours").val("0");            
            jq("#total_capacity").val("0");            
            jq("#prerequisite").html("");
            
            save_method = 'add';
            jq('#form')[0].reset(); // reset form on modals
            jq('.form-group').removeClass('has-error'); // clear error class
            jq('.help-block').empty(); // clear error string
            jq('#modal_form').modal('show'); // show bootstrap modal
            jq('.modal-title').text('New Course'); // Set Title to Bootstrap modal title
            
            jq('#btnSave').text('Save');
            jq('#btnSave').attr('disabled',false);
            
        });
        
        table.on('click', '.edit', function (e) {
            e.preventDefault();
            jq('#form').find('p').remove();
            jq('#form').find('input , .select2-container').css("border","1px solid #ccc");
            
            
            nRow = jq(this).parents('tr')[0];
            var jqInputs = jq('input', nRow);            
            var id = jqInputs[0].value;
            
            
            
            save_method = 'update';
            jq('#form')[0].reset(); // reset form on modals
            jq('.form-group').removeClass('has-error'); // clear error class
            jq('.help-block').empty(); // clear error string
            
            var url = "/setup/course_by_id"; 
            var formData = {
                    'course_id'  : id
            };
            //Ajax Load data from ajax
            jq.ajax({
                url : url,
                type: "POST",
                dataType: "JSON",
                data: formData,
                success: function(data)
                {                                        
                    jq('#edit_id').val(id);
                    jq("#department-name-course-select").select2("val", "");
                    jq('#department-name-course-select').val(data[0].subject.department_id); // Change the value or make some change to the internal state
                    jq('#department-name-course-select').trigger('change.select2'); // Notify only Select2 of changes
                    
                    
                    jq("#course-group-select").select2("val", "");
                    jq('#course-group-select').val(data[0].subject.subject_group_id); // Change the value or make some change to the internal state
                    jq('#course-group-select').trigger('change.select2'); // Notify only Select2 of changes
                    
                    jq.ajax({
                        type: 'POST' ,
                        url: "/setup/get_subjects",
                        async: false,
                        data : {
                          department_id: data[0].subject.department_id,
                          course_id: id
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
                    
                    jq('#course-name').val(data[0].subject.name);                   
                    jq('#course-code').val(data[0].subject.code);                   
                    jq('#max-classes').val(data[0].subject.max_weekly_classes);                   
                    jq('#total-credit-hours').val(data[0].subject.credit_hours); 
                    jq('#total_capacity').val(data[0].subject.capacity); 
                    if(data[0].subject.is_optional == 0)
                    {
                        jq("#is_optional").parent('[class*="icheckbox_flat-green"]').removeClass("checked");
                    }
                    
                    jq('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                    jq('.modal-title').text('Edit Course'); // Set title to Bootstrap modal title
                    
                    jq('#btnSave').text('Save');
                    jq('#btnSave').attr('disabled',false);

                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error get data from ajax');
                }
            });
        });
        
        jq(document).on("click","#btnSave",function(e){
            e.preventDefault();
            if(courseValidation())
            { 
                jq('#btnSave').text('Saving...'); 
                jq('#btnSave').attr('disabled',true);
                var url;
                var course_data = "";
                var checked = jq("#is_optional").parent('[class*="icheckbox_flat-green"]').hasClass("checked");

                if(save_method == 'add') {
                    url = "/dashboards/save_courses";
                    course_data += jq('[name="department-name-select"]').val() + "++" + jq('[name="course-name"]').val() + "++" + jq('[name="course-code"]').val() + "++" + checked + "++" + jq('[name="max-classes"]').val() + "++" + jq('[name="total-credit-hours"]').val() + "++" + jq('[name="total_capacity"]').val() + "++" + jq('[name="course-group-select"]').val()  + "++" + jq('[name="prerequisite-course"]').val() + ",,";
                } else {
                    url = "/setup/update_courses";
                    course_data += jq('[name="department-name-select"]').val() + "++" + jq('[name="course-name"]').val() + "++" + jq('[name="course-code"]').val() + "++" + checked + "++" + jq('[name="max-classes"]').val() + "++" + jq('[name="total-credit-hours"]').val() + "++" + jq('[name="total_capacity"]').val() + "++" + jq('[name="course-group-select"]').val()  + "++" + jq('[name="prerequisite-course"]').val() + "++" + jq('#edit_id').val() + ",,";
                }            

                var formData = {
                        'courses_data'  : course_data
                };
                //return false;
                jq.ajax({
                    url : url,
                    type: "POST",
                    data: formData,
                    async: false,
                    success: function(data)
                    {   
                        var a_data = data.split("++");  
                        if ( a_data[0] == "SAVE" )          
                        {
                            jq('#modal_form').modal('hide');
                            if(save_method == 'add') {      
                                var insert_id = a_data[3];
                                var counter = jq("#counter").val();
                                var nextCounter = parseInt(counter) + 1;
                                jq("#counter").attr('value', nextCounter);

                                var aiNew = oTable.row.add( ['', '', '', '', '', '', '', '', ''] ).draw();
                                nRow = oTable.row().node();
                                
                                var fff = jq('[name="department-name-select"]').select2('data');
                                var ttt = jq('[name="course-group-select"]').select2('data');

                                oTable.cell(nRow, 0).data(counter + '\n<input type="hidden" value="'+insert_id+'">');                                
                                oTable.cell(nRow, 1).data(fff[0].text + '\n<input type="hidden" value="'+jq('[name="department-name-select"]').val()+'">');
                                oTable.cell(nRow, 2).data(ttt[0].text);
                                oTable.cell(nRow, 3).data(jq('[name="course-code"]').val());
                                oTable.cell(nRow, 4).data(jq('[name="course-name"]').val());
                                oTable.cell(nRow, 5).data(jq('[name="max-classes"]').val());
                                oTable.cell(nRow, 6).data(jq('[name="total-credit-hours"]').val());
                                oTable.cell(nRow, 7).data(jq('[name="total_capacity"]').val());
                                oTable.cell(nRow, 8).data('<a class="edit btn btn-xs btn-primary" id="edit_person" href="javascript:void(0);" title="Edit"><i class="glyphicon glyphicon-pencil"></i>Edit </a><a class="delete btn btn-xs btn-danger" id="delete_person" href="javascript:void(0);" title="Delete"><i class="glyphicon glyphicon-trash"></i>Delete </a>');
                                oTable.draw();
                            }else {
                                var fff = jq('[name="department-name-select"]').select2('data');
                                var ttt = jq('[name="course-group-select"]').select2('data');
                                
                                oTable.cell(nRow, 1).data(fff[0].text);
                                oTable.cell(nRow, 2).data(ttt[0].text);
                                oTable.cell(nRow, 3).data(jq('[name="course-code"]').val());
                                oTable.cell(nRow, 4).data(jq('[name="course-name"]').val());
                                oTable.cell(nRow, 5).data(jq('[name="max-classes"]').val());
                                oTable.cell(nRow, 6).data(jq('[name="total-credit-hours"]').val());
                                oTable.cell(nRow, 7).data(jq('[name="total_capacity"]').val());

                                oTable.draw();
                            }
                        }
                        else if ( a_data[0] == "NOT_SAVE" )
                        {
                            alert("There is an error saving the data, please try again later");
                        }

                        jq('#btnSave').text('Save');
                        jq('#btnSave').attr('disabled',false);


                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        alert('Error adding / update data');
                        console.log(textStatus);
                        console.log(errorThrown);
                        jq('#btnSave').text('Svae');
                        jq('#btnSave').attr('disabled',false);

                    }
                });
            
            }
            
        });
        
        jq(document).on("click",".delete",function(e){
            e.preventDefault();
            
            if(confirm('Are you sure delete this data?'))
            {
                var nRow = jq(this).parents('tr')[0];
                var jqInputs = jq('input', nRow);
                var id = jqInputs[0].value;
                
                var url = "/setup/delete_courses"; 
                var formData = {
                        'id'  : id
                };
                jq.ajax({
                    url : url,
                    type: "POST",                    
                    data: formData,
                    success: function(data)
                    { 
                        oTable.rows( nRow ).remove().draw();
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        alert('Error deleting data');
                    }
                });
            }            
        });
    }
    function courseValidation()
    {
        var all_data_present = true;
        
        if ( jq.trim(jq('[name="department-name-select"]').val()).length == 0 )
        {
            all_data_present = false;
            if(jq(".select2-container").first().parent().find(".error").length == 0)
            {
                jq(".select2-container").first().css("border","1px solid red");
                jq(".select2-container").first().after( "<p class='error'>Select a department</p>" );
            }
        }
        else
        {
            if(jq(".select2-container").first().parent().find(".error").length == 1)
            {
                jq(".select2-container").first().css("border","1px solid #ccc");
                jq(".select2-container").first().parent().find(".error").remove();
            }
        }
        
        if ( jq.trim(jq('[name="course-group-select"]').val()).length == 0 )
        {
            all_data_present = false;
            if(jq(".select2-container:eq( 1 )").parent().find(".error").length == 0)
            {
                jq(".select2-container:eq( 1 )").css("border","1px solid red");
                jq(".select2-container:eq( 1 )").after( "<p class='error'>Select a Course Group</p>" );
            }
        }
        else
        {
            if(jq(".select2-container:eq( 1 )").parent().find(".error").length == 1)
            {
                jq(".select2-container:eq( 1 )").css("border","1px solid #ccc");
                jq(".select2-container:eq( 1 )").parent().find(".error").remove();
            }
        }

        if (  jq.trim(jq("#course-name").val()).length == 0 )
        {
            all_data_present = false;
            if(jq("#course-name").parent().find(".error").length == 0)
            {
                jq("#course-name").css("border","1px solid red");
                jq("#course-name").after( "<p class='error'>Insert a course name</p>" );
            }
        }
        else
        {
            if(jq("#course-name").parent().find(".error").length == 1)
            {
                jq("#course-name").css("border","1px solid #ccc");
                jq("#course-name").parent().find(".error").remove();
            }
        }

        if (  jq.trim(jq("#course-code").val()).length == 0 )
        {
            all_data_present = false;
            if(jq("#course-code").parent().find(".error").length == 0)
            {
                jq("#course-code").css("border","1px solid red");
                jq("#course-code").after( "<p class='error'>Insert a course code</p>" );
            }
        }
        else
        {
            if(jq("#course-code").parent().find(".error").length == 1)
            {
                jq("#course-code").css("border","1px solid #ccc");
                jq("#course-code").parent().find(".error").remove();
            }
        }

        if (  jq.trim(jq("#max-classes").val()).length == 0 )
        {
            all_data_present = false;
            if(jq("#max-classes").parent().find(".error").length == 0)
            {
                jq("#max-classes").css("border","1px solid red");
                jq("#max-classes").after( "<p class='error'>Insert a Max Weekly Class number</p>" );
            }
        }
        else if ( isNaN(parseInt(jq.trim(jq("#max-classes").val()))) )
        {
            all_data_present = false;
            if(jq("#max-classes").parent().find(".error").length == 1 && jq("#max-classes").parent().find(".error").length == 0)
            {
                jq("#max-classes").css("border","1px solid red");
                jq("#max-classes").after( "<p class='error'>Max Weekly Class number is invalid</p>" );
            }
            else
            {
                jq("#max-classes").parent().find(".error").html( "Max Weekly Class number is invalid" );
            }
        }
        else if (jq("#max-classes").parent().find(".error").length == 1 &&  parseInt(jq.trim(jq("#max-classes").val())) <= 0 )
        {
            all_data_present = false;
            if(jq("#max-classes").parent().find(".error").length == 0)
            {
                jq("#max-classes").css("border","1px solid red");
                jq("#max-classes").parent().find(".error").html( "<p class='error'>Max Weekly Class number is invalid</p>" );
            }
            else
            {
                jq("#max-classes").parent().find(".error").html( "Max course number is invalid" );
            }
        }
        else
        {
            if(jq("#max-classes").parent().find(".error").length == 1)
            {
                jq("#max-classes").css("border","1px solid #ccc");
                jq("#max-classes").parent().find(".error").remove();
            }
        }

        if (  jq.trim(jq("#total-credit-hours").val()).length == 0 )
        {
            all_data_present = false;
            if(jq("#total-credit-hours").parent().find(".error").length == 0)
            {
                jq("#total-credit-hours").css("border","1px solid red");
                jq("#total-credit-hours").after( "<p class='error'>Insert a Total Credit Hours number</p>" );
            }
        }
        else if ( isNaN(parseInt(jq.trim(jq("#total-credit-hours").val()))) )
        {
            all_data_present = false;
            if(jq("#total-credit-hours").parent().find(".error").length == 1 && jq("#max-classes").parent().find(".error").length == 0)
            {
                jq("#total-credit-hours").css("border","1px solid red");
                jq("#total-credit-hours").after( "<p class='error'>Total Credit Hours number is invalid</p>" );
            }
            else
            {
                jq("#total-credit-hours").parent().find(".error").html( "Total Credit Hours number is invalid" );
            }
        }
        else if ( parseInt(jq.trim(jq("#total-credit-hours").val())) <= 0 )
        {
            all_data_present = false;
            if(jq("#total-credit-hours").parent().find(".error").length == 0)
            {
                jq("#total-credit-hours").css("border","1px solid red");
                jq("#total-credit-hours").parent().find(".error").html( "<p class='error'>Total Credit Hours number is invalid</p>" );
            }
            else
            {
                jq("#total-credit-hours").parent().find(".error").html( "Total Credit Hours number is invalid" );
            }
        }
        else
        {
            if(jq("#total-credit-hours").parent().find(".error").length == 1)
            {
                jq("#total-credit-hours").css("border","1px solid #ccc");
                jq("#total-credit-hours").parent().find(".error").remove();
            }
        }
        
        if (  jq.trim(jq("#total_capacity").val()).length == 0 )
        {
            all_data_present = false;
            if(jq("#total_capacity").parent().find(".error").length == 0)
            {
                jq("#total_capacity").css("border","1px solid red");
                jq("#total_capacity").after( "<p class='error'>Insert a Total Capacity number</p>" );
            }
        }
        else if ( isNaN(parseInt(jq.trim(jq("#total_capacity").val()))) )
        {
            all_data_present = false;
            if(jq("#total_capacity").parent().find(".error").length == 1 && jq("#max-classes").parent().find(".error").length == 0)
            {
                jq("#total_capacity").css("border","1px solid red");
                jq("#total_capacity").after( "<p class='error'>Capacity number is invalid</p>" );
            }
            else
            {
                jq("#total_capacity").parent().find(".error").html( "Capacity number is invalid" );
            }
        }
        else if ( parseInt(jq.trim(jq("#total_capacity").val())) <= 0 )
        {
            all_data_present = false;
            if(jq("#total_capacity").parent().find(".error").length == 0)
            {
                jq("#total_capacity").css("border","1px solid red");
                jq("#total_capacity").parent().find(".error").html( "<p class='error'>Capacity number is invalid</p>" );
            }
            else
            {
                jq("#total_capacity").parent().find(".error").html( "Capacity number is invalid" );
            }
        }
        else
        {
            if(jq("#total_capacity").parent().find(".error").length == 1)
            {
                jq("#total_capacity").css("border","1px solid #ccc");
                jq("#total_capacity").parent().find(".error").remove();
            }
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
                
                var dept_data = jq.trim(dt[0][1]);                
                var course_name = jq.trim(dt[0][4]);
                
                var idd = jq.trim(dt[0][0]);
                console.log(idd);
                var dd = idd.split("\n");
                //console.log(dd[1]);
                var pattern = /<input(.*?)(.*)value=\"(.*?)\"/i;
                var mid = 0;
                if(dd.length == 2)
                {
                    mid = pattern.exec(dd[1])[3];
                }
                
                
                if ( code == jq.trim( jq("#course-code").val()) )
                {     
                      if(jq('#edit_id').val())
                      {
                          if(mid != jq('#edit_id').val())
                          {
                              b_code_found = true;break;
                          }
                      }
                      else
                      {
                          b_code_found = true;break;
                      }
                      
                      
                      
                }
                else
                {
                    /*var dept_arr = dept_data.split("\n");console.log(dept_data);
                    var pattern = /<input(.*?)(.*)value=\"(.*?)\"/i;
                    
                    var dept_id = pattern.exec(dept_arr[2])[3];
                 
                    if ( course_name == jq.trim( jq("#course-name").val()) && dept_id == jq('[name="department-name-select"]').val() )
                    {
                      if(jq('#edit_id').val())
                      {
                            if(mid != jq('#edit_id').val())
                          {
                              b_found = true;break;
                          }
                      }
                      else
                      {
                          b_found = true;break;
                      }
                    }*/
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
              
              return true;
            }
            else
            {
                return true;
            }
            

        }

    }
    
    return {
        init: function (table) {
            handleTable();
        }

    };

}();

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
        
    }
}

