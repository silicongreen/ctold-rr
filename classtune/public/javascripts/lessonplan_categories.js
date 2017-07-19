var jq = jQuery.noConflict();
  


var TableEditable = function () {

    var handleTable = function () {

        function restoreRow(oTable, nRow) {
            var aData = oTable.row( nRow ).data();
            var jqTds = jq('>td', nRow);
            
            

            for (var i = 0, iLen = jqTds.length; i < iLen; i++) {                
                var d = aData[i];
                oTable.row( nRow ).data( aData ).draw();
            }
            
            if(nNew)
            {
                oTable.rows( nRow ).remove().draw();
                nNew = false;
            }
            
        }

        function editRow(oTable, nRow) 
        {
            var aData = oTable.row( nRow ).data();
            var jqTds = jq('>td', nRow);
            //jqTds[0].innerHTML = '<input type="text" class="form-control input-small" value="' + aData[0] + '">';
            jqTds[1].innerHTML = '<input type="text" id="name" class="form-control input-small" value="' + aData[1] + '">';
            jqTds[2].innerHTML = '<a class="edit btn btn-xs btn-primary" href=""><i class="glyphicon glyphicon-pencil"></i>Save</a>';
            jqTds[3].innerHTML = '<a class="cancel btn btn-xs btn-danger" href=""><i class="glyphicon glyphicon-trash"></i>Cancel</a>';
        }

        function saveRow(oTable, nRow) 
        {
            var jqInputs = jq('input', nRow);

            if(jqInputs.length == 1)
            {
                    var insert_id = ajaxSaveData(jqInputs[0].value);                    
                    var counter = jq("#counter").val();
                    nextCounter = parseInt(counter) + 1;

                    jq("#counter").attr('value', nextCounter);
                    oTable.cell(nRow, 0).data(counter + '<input type="hidden" name="id" value="'+insert_id+'">');                                
                    oTable.cell(nRow, 1).data(jqInputs[0].value);
            }
            else
            {
                    ajaxUpdateData(jqInputs[0].value,jqInputs[1].value);
                    oTable.cell(nRow, 1).data(jqInputs[1].value);
            }

            oTable.cell(nRow, 2).data('<a class="edit btn btn-xs btn-primary" href=""><i class="glyphicon glyphicon-pencil"></i>Edit</a>');

            oTable.cell(nRow, 3).data('<a class="delete btn btn-xs btn-danger" href=""><i class="glyphicon glyphicon-trash"></i>Delete</a>');
            oTable.draw();
        }
        
        function cancelEditRow(oTable, nRow) 
        {
            var jqInputs = jq('input', nRow);

            oTable.cell(nRow, 1).data(jqInputs[1].value);            
            oTable.cell(nRow, 2).data('<a class="edit btn btn-xs btn-primary" href=""><i class="glyphicon glyphicon-pencil"></i>Edit</a>');
            oTable.draw();
        }

        function ajaxUpdateData(id, name)
        {
            var url = "/lessonplan/category_update/" + id; // the script where you handle the form input.
            var formData = {
                    'name'  : name
            };

            jq.ajax({
                type: "POST",
                url: url,
                data: formData, // serializes the form's elements.				   			   
            })
            .done(function(data) {
                var a_data = data.split("++");
                if ( a_data[0] == "SAVE_ERROR" )
                {
                    alert("There is an error updating the data, please try again later");
                }  
            }).fail(function(error) {
                console.log(error);               
            });
        }

        function ajaxSaveData(name)
        {
            var url = "/lessonplan/category_add"; // the script where you handle the form input.        
            var formData = {
                    'category_name'  : name
            };
            var insert_id = 0; 

            jq.ajax({
                type: "POST",
                url: url,
                data: formData, // serializes the form's elements.                          
                async : false
            })
            .done(function(data) {
                var a_data = data.split("++");  

                if ( a_data[0] == "NO_DATA_TO_SAVE" )
                {
                    alert("No data to save, please check before continue")
                }
                else if ( a_data[0] == "SAVE_ERROR" )
                {
                    alert("There is an error saving the data, please try again later");
                }
                else
                {
                    insert_id = a_data[1];
                }                
            }).fail(function(error) {
                console.log(error);               
            });

            return insert_id;
        }

        function ajaxDeleteData(id)
        {
            var url = "/lessonplan/category_delete/" + id; 
            jq.ajax({
                type: "POST",
                url: url
            }).done(function(data) {});
        }

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
                ] // set first column as a default sort by asc
            });

        var nEditing = null;
        var nNew = false;

        newRow.click(function (e) {
            e.preventDefault();

            if (nNew && nEditing) {
                if (confirm("Previose row not saved. Do you want to save it ?")) {
                    if(checkValidation())
                    {
                        saveRow(oTable, nEditing); // save
                        jq(nEditing).find("td:first").html("Untitled");
                        nEditing = null;
                        nNew = false;
                    }
                    else
                    {
                        return;
                    }

                } else {                    
                    oTable.rows( nEditing ).remove().draw();
                    nEditing = null;
                    nNew = false;

                    return;
                }
            }
            else if(nEditing)
            {
                restoreRow(oTable, nEditing);
                nEditing = null;
            }
            
            var aiNew = oTable.row.add( ['', '', '', '', '', ''] ).draw();
            var nRow = oTable.row().node();
            editRow(oTable, nRow);
            nEditing = nRow;
            nNew = true;
        });

        table.on('click', '.delete', function (e) {
            e.preventDefault();

            if (confirm("Are you sure to delete this row ?") == false) {
                return;
            }			
            var nRow = jq(this).parents('tr')[0];
            var jqInputs = jq('input', nRow);
            ajaxDeleteData(jqInputs[0].value);	
            oTable.rows( nRow ).remove().draw();
        });

        table.on('click', '.cancel', function (e) {
            e.preventDefault();
            if (nNew) {
                oTable.rows( nEditing ).remove().draw();
                nEditing = null;
                nNew = false;
            } else {
                restoreRow(oTable, nEditing);
                nEditing = null;
            }
        });

        table.on('click', '.edit', function (e) {
            e.preventDefault();

            /* Get the row as a parent of the link that was clicked on */
            var nRow = jq(this).parents('tr')[0];

            if (nEditing !== null && nEditing != nRow) {
                /* Currently editing - but not this row - restore the old before continuing to edit mode */
                restoreRow(oTable, nEditing);
                editRow(oTable, nRow);
                nEditing = nRow;
            } else if (nEditing == nRow && this.textContent == "Save") {
                /* Editing this row and want to save it */               
                if(checkValidation())
                {
                    saveRow(oTable, nEditing);
                    nEditing = null;
                }
                //alert("Updated! Do not forget to do some ajax to sync with backend :)");
            } else {
                /* No edit in progress - let's start one */
                editRow(oTable, nRow);
                nEditing = nRow;
            }
        });
        
        function checkValidation()
        {
            var check = true;
            if ( jq.trim(jq("#name").val()).length == 0 )
            {
                check = false;
                jq("#name").css("border","1px solid red");
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