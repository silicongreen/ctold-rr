
CKEDITOR.plugins.add( 'solution',
{
	// The plugin initialization logic goes inside this method.
	// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.pluginDefinition.html#init
	init: function( editor )
	{
		// Create an editor command that stores the dialog initialization command.
		// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.command.html
		// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.dialogCommand.html
		editor.addCommand( 'solutionDialog', new CKEDITOR.dialogCommand( 'solutionDialog' ) );
 
		// Create a toolbar button that executes the plugin command defined above.
		// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.ui.html#addButton
		editor.ui.addButton( 'Solution',
		{
			// Toolbar button tooltip.
			label: 'Insert a Solution',
			// Reference to the plugin command name.
			command: 'solutionDialog',
			// Button's icon file path.
			icon: this.path + 'images/icon.png'
		} );
 
		// Add a new dialog window definition containing all UI elements and listeners.
		// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.dialog.html#.add
		// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.dialog.dialogDefinition.html
		CKEDITOR.dialog.add( 'solutionDialog', function( editor )
		{
			return {
				// Basic properties of the dialog window: title, minimum size.
				// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.dialog.dialogDefinition.html
				title : 'Solution Properties',
				minWidth : 400,
				minHeight : 200,
				// Dialog window contents.
				// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.dialog.definition.content.html
				contents :
				[
					{
						// Definition of the Settings dialog window tab (page) with its id, label and contents.
						// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.dialog.contentDefinition.html
						id : 'general',
						label : 'Settings',
						elements :
						[
							// Dialog window UI element: HTML code field.
							// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.ui.dialog.html.html
							{
								type : 'html',
								// HTML code to be shown inside the field.
								// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.ui.dialog.html.html#constructor
								html : 'Add Solution'
							},
							// Dialog window UI element: a textarea field for the link text.
							// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.ui.dialog.textarea.html
							{
								type : 'textarea',
								id : 'solution',
								// Text that labels the field.
								// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.ui.dialog.labeledElement.html#constructor
								label : 'Solution',
                                                                
								// Validation checking whether the field is not empty.
								validate : CKEDITOR.dialog.validate.notEmpty( 'Solution field cannot be empty.' ),
								// This field is required.
								required : true,
								// Function to be run when the commitContent method of the parent dialog window is called.
								// Get the value of this field and save it in the data object attribute.
								// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.dom.element.html#getValue
								commit : function( data )
								{
									data.solution = this.getValue();
								}
							}
							
							
						]
					}
				],
				onOk : function()
				{
                                    
                                    var dialog = this,
						data = {};
                                    this.commitContent( data );  
                                    
                                    var buttonsolution = '<p id="solution-p"><input name="Solution" id="solution-button" type="button" value="Solution" /></p>';
                                    
                                 
                                    
                                    var solutiontext = buttonsolution+'<div id="solution-text" style="display:none;"><hr /><p>'+data.solution+'</p></div>';
                                    editor.insertHtml( solutiontext );
                                        
				}
			};
		} );
	}
} );