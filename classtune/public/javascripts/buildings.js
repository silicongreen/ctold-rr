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
            
            jq("#building-name").focus();            
            jq("#campus-name-select").select2("val", "");
            jq("#building-name").val("");
            
            save_method = 'add';
            jq('#form')[0].reset(); // reset form on modals
            jq('.form-group').removeClass('has-error'); // clear error class
            jq('.help-block').empty(); // clear error string
            jq('#modal_form').modal('show'); // show bootstrap modal
            jq('.modal-title').text('New Building'); // Set Title to Bootstrap modal title
            
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
            
            var url = "/setup/building_by_id"; 
            var formData = {
                    'building_id'  : id
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
                    
                    jq("#campus-name-select").select2("val", "");
                    jq('#campus-name-select').val(data.building.campus_id); // Change the value or make some change to the internal state
                    jq('#campus-name-select').trigger('change.select2'); // Notify only Select2 of changes
                    
                    jq('[name="building-name"]').val(data.building.name);
                    
                    
                    jq('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                    jq('.modal-title').text('Edit Building'); // Set title to Bootstrap modal title
                    
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
            
            jq('#btnSave').text('Saving...'); 
            jq('#btnSave').attr('disabled',true);
            var url;
            var building_data = "";

            if(save_method == 'add') {
                url = "/setup/save_building";
                building_data += jq('[name="campus-name-select"]').val() + "++" + jq('[name="building-name"]').val() + ",,";
            } else {
                url = "/setup/update_building";
                building_data += jq('[name="campus-name-select"]').val() + "++" + jq('[name="building-name"]').val() + "++" + jq('#edit_id').val() + ",,";
            }
            
            
            
            var formData = {
                    'building_data'  : building_data
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
                            
                            var ttt = jq('[name="campus-name-select"]').select2('data');

                            oTable.cell(nRow, 0).data(counter + '<input type="hidden" name="id" value="'+insert_id+'">');                                
                            oTable.cell(nRow, 1).data(ttt[0].text);
                            oTable.cell(nRow, 2).data(jq('[name="building-name"]').val());
                            oTable.cell(nRow, 3).data('<a class="edit btn btn-xs btn-primary" id="edit_person" href="javascript:void(0);" title="Edit"><i class="glyphicon glyphicon-pencil"></i>Edit </a><a class="delete btn btn-xs btn-danger" id="delete_person" href="javascript:void(0);" title="Delete"><i class="glyphicon glyphicon-trash"></i>Delete </a>');
                            oTable.draw();
                        }else {
                            
                            var ttt = jq('[name="campus-name-select"]').select2('data');
                            
                            oTable.cell(nRow, 1).data(ttt[0].text);
                            oTable.cell(nRow, 2).data(jq('[name="building-name"]').val());
                            
                            oTable.draw();
                        }
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
                
                var url = "/setup/delete_building"; 
                var formData = {
                        'building_id'  : id
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
    
    return {
        init: function (table) {
            handleTable();
        }

    };

}();