var jq = jQuery.noConflict();

var TablePrograms = function () {

    var handleTable = function () {

        var oTable = table.DataTable({
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
        jq(document).on("click",".new_program",function(e){
            e.preventDefault();
            
            jq('#form').find('p').remove();
            jq('#form').find('input , .select2-container').css("border","1px solid #ccc");
            
            jq("#program-name").focus();            
            jq("#department-name-select").select2("val", "");
            jq("#program-name").val("");
            jq("#program-short").val("");
            jq("#program-code").val("");
            jq("#semester_no").val("");
            jq("#credit-hour").val("4");
            
            save_method = 'add';
            jq('#form')[0].reset(); // reset form on modals
            jq('.form-group').removeClass('has-error'); // clear error class
            jq('.help-block').empty(); // clear error string
            jq('#modal_form').modal('show'); // show bootstrap modal
            jq('.modal-title').text('New Program'); // Set Title to Bootstrap modal title
            
            jq('#btnSave').text('Svae');
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
            
            var url = "/setup/program_by_id"; 
            var formData = {
                    'program_id'  : id
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
                    
                    jq("#department-name-select").select2("val", "");
                    jq('#department-name-select').val(data.program.department_id); // Change the value or make some change to the internal state
                    jq('#department-name-select').trigger('change.select2'); // Notify only Select2 of changes
                    
                    jq('[name="program-name"]').val(data.program.name);
                    jq('[name="program-short"]').val(data.program.program_short);
                    jq('[name="program-code"]').val(data.program.code);
                    jq('[name="semester_no"]').val(data.program.default_semester_no);
                    jq('[name="credit-hour"]').val(data.program.total_credit_hours);
                    
                    jq('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                    jq('.modal-title').text('Edit Program'); // Set title to Bootstrap modal title
                    
                    
                    jq('#btnSave').text('Svae');
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
            
            if(!checkValidation())
            {
                return ;
            }
            
            jq('#btnSave').text('Saving...'); 
            jq('#btnSave').attr('disabled',true);
            var url;
            var programs_data = "";

            if(save_method == 'add') {
                url = "/dashboards/save_program";
                programs_data += jq('[name="department-name-select"]').val() + "++" + jq('[name="program-name"]').val() + "++" + jq('[name="program-short"]').val() + "++" + jq('#program-code').val() + "++" + jq('#semester_no').val() + "++" + jq('#credit-hour').val() + ",,";
            } else {
                url = "/setup/update_program";
                programs_data += jq('[name="department-name-select"]').val() + "++" + jq('[name="program-name"]').val() + "++" + jq('[name="program-short"]').val() + "++" + jq('#program-code').val() + "++" + jq('#semester_no').val() + "++" + jq('#credit-hour').val() + "++" + jq('#edit_id').val() + ",,";
            }
            
            
            
            var formData = {
                    'program_data'  : programs_data
            };

            jq.ajax({
                url : url,
                type: "POST",
                data: formData,
                async: false,
                /*data: jq('#form').serialize(),
                dataType: "JSON",*/
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

                            var aiNew = oTable.row.add( ['', '', '', '', '', '', '', ''] ).draw();
                            nRow = oTable.row().node();
                            
                            var ttt = jq('[name="department-name-select"]').select2('data');

                            oTable.cell(nRow, 0).data(counter + '<input type="hidden" name="id" value="'+insert_id+'">');                                
                            oTable.cell(nRow, 1).data(ttt[0].text);
                            oTable.cell(nRow, 2).data(jq('[name="program-name"]').val());
                            oTable.cell(nRow, 3).data(jq('[name="program-short"]').val());
                            oTable.cell(nRow, 4).data(jq('[name="program-code"]').val());
                            oTable.cell(nRow, 5).data(jq('[name="semester_no"]').val());
                            oTable.cell(nRow, 6).data(jq('[name="credit-hour"]').val());
                            oTable.cell(nRow, 7).data('<a class="edit btn btn-xs btn-primary" id="edit_person" href="javascript:void(0);" title="Edit"><i class="glyphicon glyphicon-pencil"></i>Edit </a><a class="delete btn btn-xs btn-danger" id="delete_person" href="javascript:void(0);" title="Delete"><i class="glyphicon glyphicon-trash"></i>Delete </a>');
                            oTable.draw();
                        }else {
                            var ttt = jq('[name="department-name-select"]').select2('data');
                            
                            oTable.cell(nRow, 1).data(ttt[0].text);
                            oTable.cell(nRow, 2).data(jq('[name="program-name"]').val());
                            oTable.cell(nRow, 3).data(jq('[name="program-short"]').val());
                            oTable.cell(nRow, 4).data(jq('[name="program-code"]').val());
                            oTable.cell(nRow, 5).data(jq('[name="semester_no"]').val());
                            oTable.cell(nRow, 6).data(jq('[name="credit-hour"]').val());
                            
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
                    jq('#btnSave').text('Save');
                    jq('#btnSave').attr('disabled',false);

                }
            });
            
        });
        
        jq(document).on("click",".delete",function(e){
            e.preventDefault();
            
            if(confirm('Are you sure delete this data?'))
            {
                var nRow = jq(this).parents('tr')[0];
                var jqInputs = jq('input', nRow);
                var id = jqInputs[0].value;
                
                var url = "/setup/delete_program"; 
                var formData = {
                        'program_id'  : id
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
        
        function checkValidation()
        {
            
            var check = true;
            if ( jq.trim(jq('[name="department-name-select"]').val()).length == 0 )
            {
                all_data_present = false;
                if(jq(".select2-container").parent().find(".error").length == 0)
                {
                    jq(".select2-container").css("border","1px solid red");
                    jq(".select2-container").after( "<p class='error'>Select a department</p>" );
                }
            }
            else
            {
                if(jq(".select2-container").parent().find(".error").length == 1)
                {
                    jq(".select2-container").css("border","1px solid #ccc");
                    jq(".select2-container").parent().find(".error").remove();
                }
            }
            
            if ( jq.trim(jq("#program-name").val()).length == 0 )
            {
                check = false;
                if(jq("#program-name").parent().find(".error").length == 0)
                {
                    jq("#program-name").css("border","1px solid red");
                    jq("#program-name").after( "<p class='error'>Insert a Program Name</p>" );
                }          
            }
            else
            {
                if(jq("#program-name").parent().find(".error").length == 1)
                {
                    jq("#program-name").css("border","1px solid #ccc");
                    jq("#program-name").parent().find(".error").remove();
                }
            }
            if ( jq.trim(jq("#program-short").val()).length == 0 )
            {
                check = false;
                if(jq("#program-short").parent().find(".error").length == 0)
                {
                    jq("#program-short").css("border","1px solid red");
                    jq("#program-short").after( "<p class='error'>Insert a Program short name</p>" );
                }          
            }
            else
            {
                if(jq("#program-short").parent().find(".error").length == 1)
                {
                    jq("#program-short").css("border","1px solid #ccc");
                    jq("#program-short").parent().find(".error").remove();
                }
            }
            
            if ( jq.trim(jq("#program-code").val()).length == 0 )
            {
                check = false;
                if(jq("#program-code").parent().find(".error").length == 0)
                {
                    jq("#program-code").css("border","1px solid red");
                    jq("#program-code").after( "<p class='error'>Insert a Program Code</p>" );
                }          
            }
            else
            {
                if(jq("#program-code").parent().find(".error").length == 1)
                {
                    jq("#program-code").css("border","1px solid #ccc");
                    jq("#program-code").parent().find(".error").remove();
                }
            }            
            
            if (  jq.trim(jq("#credit-hour").val()).length == 0 )
            {
                check = false;
                if(jq("#credit-hour").parent().find(".error").length == 0)
                {
                    jq("#credit-hour").css("border","1px solid red");
                    jq("#credit-hour").after( "<p class='error'>Insert a Total Credit Hours</p>" );
                }
            }
            else if ( isNaN(parseInt(jq.trim(jq("#credit-hour").val()))) )
            {
                check = false;
                if(jq("#credit-hour").parent().find(".error").length == 1 && jq("#credit-hour").parent().find(".error").length == 0)
                {
                    jq("#credit-hour").css("border","1px solid red");
                    jq("#credit-hour").after( "<p class='error'>Total Credit Hours is invalid</p>" );
                }
                else
                {
                    jq("#credit-hour").parent().find(".error").html( "Total Credit Hours is invalid" );
                }
            }
            else if (jq("#credit-hour").parent().find(".error").length == 1 &&  parseInt(jq.trim(jq("#credit-hour").val())) <= 0 )
            {
                check = false;
                if(jq("#credit-hour").parent().find(".error").length == 0)
                {
                    jq("#credit-hour").css("border","1px solid red");
                    jq("#credit-hour").parent().find(".error").html( "<p class='error'>Total Credit Hours is invalid</p>" );
                }
                else
                {
                    jq("#credit-hour").parent().find(".error").html( "Total Credit Hours is invalid" );
                }
            }
            else
            {
                if(jq("#credit-hour").parent().find(".error").length == 1)
                {
                    jq("#credit-hour").css("border","1px solid #ccc");
                    jq("#credit-hour").parent().find(".error").remove();
                }
            }
            return check;
        }
    }
    
    return {
        init: function (table) {
            handleTable();
        }

    };

}();