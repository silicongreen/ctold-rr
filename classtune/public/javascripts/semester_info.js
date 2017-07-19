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
            
            jq("#name").focus();                        
            jq("#name").val("");
            jq("#duration").val("");
            
            save_method = 'add';
            jq('#form')[0].reset(); // reset form on modals
            jq('.form-group').removeClass('has-error'); // clear error class
            jq('.help-block').empty(); // clear error string
            jq('#modal_form').modal('show'); // show bootstrap modal
            jq('.modal-title').text('New Semester'); // Set Title to Bootstrap modal title
            
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
            
            var url = "/setup/edit"; 
            var formData = {
                    'id'  : id,
                    'type':'season'
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
                    
                    jq("#name").val(data.season.name);                    
                    jq('#duration').val(data.season.duration);
                    
                    
                    jq('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                    jq('.modal-title').text('Edit Semester'); // Set title to Bootstrap modal title
                    
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
            var semester_data = "";
            
            if(save_method == 'add') {
                url = "/setup/season";
                semester_data += jq('[name="name"]').val() + "++" + jq('[name="duration"]').val() + ",,";
            } else {
                url = "/setup/edit_season";
                semester_data += jq('[name="name"]').val() + "++" + jq('[name="duration"]').val() + "++" + jq('#edit_id').val() + ",,";
            }
            
            
            
            var formData = {
                    'semester_data'  : semester_data
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
                            var insert_id = a_data[1];
                            var counter = jq("#counter").val();
                            var nextCounter = parseInt(counter) + 1;
                            jq("#counter").attr('value', nextCounter);

                            var aiNew = oTable.row.add( ['', '', '', ''] ).draw();
                            nRow = oTable.row().node();

                            oTable.cell(nRow, 0).data(counter + '<input type="hidden" name="id" value="'+insert_id+'">');                                
                            oTable.cell(nRow, 1).data(jq('[name="name"]').val());
                            oTable.cell(nRow, 2).data(jq('[name="duration"]').val()+ " Months");
                            oTable.cell(nRow, 3).data('<a class="edit btn btn-xs btn-primary" id="edit_person" href="javascript:void(0);" title="Edit"><i class="glyphicon glyphicon-pencil"></i>Edit </a><a class="delete btn btn-xs btn-danger" id="delete_person" href="javascript:void(0);" title="Delete"><i class="glyphicon glyphicon-trash"></i>Delete </a>');
                            oTable.draw();
                        }else {
                            oTable.cell(nRow, 1).data(jq('[name="name"]').val());
                            oTable.cell(nRow, 2).data(jq('[name="duration"]').val()+ " Months");
                            
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
                
                var url = "/setup/delete"; 
                var formData = {
                        'id'  : id,
                        'type'  : 'season'
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
            if ( jq.trim(jq("#name").val()).length == 0 )
            {
                check = false;
                if(jq("#name").parent().find(".error").length == 0)
                {
                    jq("#name").css("border","1px solid red");
                    jq("#name").after( "<p class='error'>Insert a name</p>" );
                }
                
            }
            else
            {
                if(jq("#name").parent().find(".error").length == 1)
                {
                    jq("#name").css("border","1px solid #ccc");
                    jq("#name").parent().find(".error").remove();
                }
            }
            if ( jq.trim(jq("#duration").val()).length == 0 )
            {
                check = false;
                if(jq("#duration").parent().find(".error").length == 0)
                {
                    jq("#duration").css("border","1px solid red");
                    jq("#duration").after( "<p class='error'>Insert a duration</p>" );
                }
                
            }
            else
            {
                if(jq("#duration").parent().find(".error").length == 1)
                {
                    jq("#duration").css("border","1px solid #ccc");
                    jq("#duration").parent().find(".error").remove();
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