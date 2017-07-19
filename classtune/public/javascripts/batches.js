var jq = jQuery.noConflict();
var oTable;
var TableBatch = function () {

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
        jq(document).on("click","#new_batch",function(e){
            e.preventDefault();
            jq('#form').find('p').remove();
            jq('#form').find('input , .select2-container').css("border","1px solid #ccc");
            jq("#batch-name").focus();            
            jq("#edit_id").val("");            
            jq("#program-name-select").select2("val", "");
            jq("#batch-name").val("");
            jq("#batch-code").val("");
            jq("#min_credit_hour").val("");            
            jq("#max_credit_hour").val("");            
            
            
            save_method = 'add';
            jq('#form')[0].reset(); // reset form on modals
            jq('.form-group').removeClass('has-error'); // clear error class
            jq('.help-block').empty(); // clear error string
            jq('#modal_form').modal('show'); // show bootstrap modal
            jq('.modal-title').text('New Batch'); // Set Title to Bootstrap modal title
            
            jq('#btnSave').text('Svae');
            jq('#btnSave').attr('disabled',false);
            
        });
        
        table.on('click', '.edit', function (e) {
            e.preventDefault();
            nRow = jq(this).parents('tr')[0];
            var jqInputs = jq('input', nRow);            
            var id = jqInputs[0].value;
            
            
            
            save_method = 'update';
            jq('#form')[0].reset(); // reset form on modals
            jq('.form-group').removeClass('has-error'); // clear error class
            jq('.help-block').empty(); // clear error string
            
            var url = "/setup/batch_by_id"; 
            var formData = {
                    'batch_id'  : id
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
                    jq("#program-name-select").select2("val", "");
                    jq('#program-name-select').val(data.batch.program_id); // Change the value or make some change to the internal state
                    jq('#program-name-select').trigger('change.select2'); // Notify only Select2 of changes
                    
                    jq('#batch-name').val(data.batch.name);                   
                    jq('#batch-code').val(data.batch.batch_code);                   
                    jq('#min_credit_hour').val(data.batch.min_credit_hours);                   
                    jq('#max_credit_hour').val(data.batch.max_credit_hours); 
                    jq('#semester_no').val(data.batch.no_of_sections); 
                    
                    jq('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                    jq('.modal-title').text('Edit Batch'); // Set title to Bootstrap modal title
                    
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
            if(courseValidation())
            { 
                jq('#btnSave').text('saving...'); 
                jq('#btnSave').attr('disabled',true);
                var url;
                var batch_data = "";
                var checked = jq("#is_optional").parent('[class*="icheckbox_flat-green"]').hasClass("checked");

                if(save_method == 'add') {
                    url = "/dashboards/save_batches";
                    batch_data += jq('[name="program-name-select"]').val() + "++" + jq('[name="batch-name"]').val() + "++" + jq('[name="batch-code"]').val() + "++" + jq('[name="semester_no"]').val() + "++" + jq('[name="min_credit_hour"]').val() + "++" + jq('[name="max_credit_hour"]').val() + ",,";
                } else {
                    url = "/setup/update_batch";
                    batch_data += jq('[name="program-name-select"]').val() + "++" + jq('[name="batch-name"]').val() + "++" + jq('[name="batch-code"]').val() + "++" + jq('[name="semester_no"]').val() + "++" + jq('[name="min_credit_hour"]').val() + "++" + jq('[name="max_credit_hour"]').val() + "++" + jq('#edit_id').val() + ",,";
                }            

                var formData = {
                        'batch_data'  : batch_data
                };

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

                                var aiNew = oTable.row.add( ['', '', '', '', '', '', ''] ).draw();
                                nRow = oTable.row().node();
                                
                                var ttt = jq('[name="program-name-select"]').select2('data');

                                oTable.cell(nRow, 0).data(counter + '\n<input type="hidden" value="'+insert_id+'">');                                
                                oTable.cell(nRow, 1).data(ttt[0].text + '\n<input type="hidden" value="'+jq('[name="program-name-select"]').val()+'">');
                                oTable.cell(nRow, 2).data(jq('[name="batch-code"]').val());
                                oTable.cell(nRow, 3).data(jq('[name="batch-name"]').val());
                                oTable.cell(nRow, 4).data(jq('[name="min_credit_hour"]').val());
                                oTable.cell(nRow, 5).data(jq('[name="max_credit_hour"]').val());
                                oTable.cell(nRow, 6).data('<a class="edit btn btn-xs btn-primary" id="edit_person" href="javascript:void(0);" title="Edit"><i class="glyphicon glyphicon-pencil"></i>Edit </a><a class="delete btn btn-xs btn-danger" id="delete_person" href="javascript:void(0);" title="Delete"><i class="glyphicon glyphicon-trash"></i>Delete </a>');
                                oTable.draw();
                            }else {
                                
                                var ttt = jq('[name="program-name-select"]').select2('data');
                                
                                oTable.cell(nRow, 1).data(ttt[0].text);
                                oTable.cell(nRow, 2).data(jq('[name="batch-code"]').val());
                                oTable.cell(nRow, 3).data(jq('[name="batch-name"]').val());
                                oTable.cell(nRow, 4).data(jq('[name="min_credit_hour"]').val());
                                oTable.cell(nRow, 5).data(jq('[name="max_credit_hour"]').val());

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
            
            }
            
        });
        
        jq(document).on("click",".delete",function(e){
            e.preventDefault();
            
            if(confirm('Are you sure delete this data?'))
            {
                var nRow = jq(this).parents('tr')[0];
                var jqInputs = jq('input', nRow);
                var id = jqInputs[0].value;
                
                var url = "/setup/delete_batch"; 
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
        
        if ( jq.trim(jq('[name="program-name-select"]').val()).length == 0 )
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

        if (  jq.trim(jq("#batch-name").val()).length == 0 )
        {
            all_data_present = false;
            if(jq("#batch-name").parent().find(".error").length == 0)
            {
                jq("#batch-name").css("border","1px solid red");
                jq("#batch-name").after( "<p class='error'>Insert a batch name</p>" );
            }
        }
        else
        {
            if(jq("#batch-name").parent().find(".error").length == 1)
            {
                jq("#batch-name").css("border","1px solid #ccc");
                jq("#batch-name").parent().find(".error").remove();
            }
        }

        if (  jq.trim(jq("#batch-code").val()).length == 0 )
        {
            all_data_present = false;
            if(jq("#batch-code").parent().find(".error").length == 0)
            {
                jq("#batch-code").css("border","1px solid red");
                jq("#batch-code").after( "<p class='error'>Insert a batch code</p>" );
            }
        }
        else
        {
            if(jq("#batch-code").parent().find(".error").length == 1)
            {
                jq("#batch-code").css("border","1px solid #ccc");
                jq("#batch-code").parent().find(".error").remove();
            }
        }

        if (  jq.trim(jq("#min_credit_hour").val()).length == 0 )
        {
            all_data_present = false;
            if(jq("#min_credit_hour").parent().find(".error").length == 0)
            {
                jq("#min_credit_hour").css("border","1px solid red");
                jq("#min_credit_hour").after( "<p class='error'>Insert a Minimum Credit Hours</p>" );
            }
        }
        else if ( isNaN(parseInt(jq.trim(jq("#min_credit_hour").val()))) )
        {
            all_data_present = false;
            if(jq("#min_credit_hour").parent().find(".error").length == 1 && jq("#min_credit_hour").parent().find(".error").length == 0)
            {
                jq("#min_credit_hour").css("border","1px solid red");
                jq("#min_credit_hour").after( "<p class='error'>Minimum Credit Hours is invalid</p>" );
            }
            else
            {
                jq("#min_credit_hour").parent().find(".error").html( "Minimum Credit Hours is invalid" );
            }
        }
        else if (jq("#min_credit_hour").parent().find(".error").length == 1 &&  parseInt(jq.trim(jq("#min_credit_hour").val())) <= 0 )
        {
            all_data_present = false;
            if(jq("#min_credit_hour").parent().find(".error").length == 0)
            {
                jq("#min_credit_hour").css("border","1px solid red");
                jq("#min_credit_hour").parent().find(".error").html( "<p class='error'>Minimum Credit Hours is invalid</p>" );
            }
            else
            {
                jq("#min_credit_hour").parent().find(".error").html( "Minimum Credit Hours is invalid" );
            }
        }
        else
        {
            if(jq("#min_credit_hour").parent().find(".error").length == 1)
            {
                jq("#min_credit_hour").css("border","1px solid #ccc");
                jq("#min_credit_hour").parent().find(".error").remove();
            }
        }

        if (  jq.trim(jq("#max_credit_hour").val()).length == 0 )
        {
            all_data_present = false;
            if(jq("#max_credit_hour").parent().find(".error").length == 0)
            {
                jq("#max_credit_hour").css("border","1px solid red");
                jq("#max_credit_hour").after( "<p class='error'>Insert a Maximum Credit Hours</p>" );
            }
        }
        else if ( isNaN(parseInt(jq.trim(jq("#max_credit_hour").val()))) )
        {
            all_data_present = false;
            if(jq("#max_credit_hour").parent().find(".error").length == 1 && jq("#max_credit_hour").parent().find(".error").length == 0)
            {
                jq("#max_credit_hour").css("border","1px solid red");
                jq("#max_credit_hour").after( "<p class='error'>Maximum Credit Hours is invalid</p>" );
            }
            else
            {
                jq("#max_credit_hour").parent().find(".error").html( "Maximum Credit Hours is invalid" );
            }
        }
        else if ( parseInt(jq.trim(jq("#max_credit_hour").val())) <= 0 )
        {
            all_data_present = false;
            if(jq("#max_credit_hour").parent().find(".error").length == 0)
            {
                jq("#max_credit_hour").css("border","1px solid red");
                jq("#max_credit_hour").parent().find(".error").html( "<p class='error'>Maximum Credit Hours is invalid</p>" );
            }
            else
            {
                jq("#max_credit_hour").parent().find(".error").html( "Maximum Credit Hours is invalid" );
            }
        }
        else
        {
            if(jq("#max_credit_hour").parent().find(".error").length == 1)
            {
                jq("#max_credit_hour").css("border","1px solid #ccc");
                jq("#max_credit_hour").parent().find(".error").remove();
            }
        }
        if ( jq.trim(jq("#semester_no").val()).length == 0 )
        {
            all_data_present = false;
            if(jq("#semester_no").parent().find(".error").length == 0)
            {
                jq("#semester_no").css("border","1px solid red");
                jq("#semester_no").after( "<p class='error'>Insert a semester number</p>" );
            }

        }
        else
        {
            if(jq("#semester_no").parent().find(".error").length == 1)
            {
                jq("#semester_no").css("border","1px solid #ccc");
                jq("#semester_no").parent().find(".error").remove();
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
                var code = jq.trim(dt[0][2]);
                var program_data = jq.trim(dt[0][1]);                
                var batch_name = jq.trim(dt[0][3]);
                
                var idd = jq.trim(dt[0][0]);console.log(idd);
                dd = idd.split("\n");
                var pattern = /<input(.*?)(.*)value=\"(.*?)\"/i;
                var mid = pattern.exec(dd[1])[3];
                
                if ( code == jq.trim( jq("#batch-code").val()) )
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
                    
                    /*var program_arr = program_data.split("\n");
                    var pattern = /<input(.*?)(.*)value=\"(.*?)\"/i;
                    
                    var program_id = pattern.exec(program_arr[2])[3];
                    
                    if ( batch_name == jq.trim( jq("#batch-name").val()) && program_id == jq('[name="program-name-select"]').val() )
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
                alert("Batch with same name already exists");
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