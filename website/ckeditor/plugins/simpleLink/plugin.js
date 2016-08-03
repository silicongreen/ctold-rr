// Register a new CKEditor plugin.
// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.resourceManager.html#add

var base_url_related_news = document.getElementById("base_url").value+"images/relatednews.jpg";
CKEDITOR.plugins.add( 'simpleLink',
{
	// The plugin initialization logic goes inside this method.
	// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.pluginDefinition.html#init
	init: function( editor )
	{
		// Create an editor command that stores the dialog initialization command.
		// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.command.html
		// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.dialogCommand.html
		editor.addCommand( 'simpleLinkDialog', new CKEDITOR.dialogCommand( 'simpleLinkDialog' ) );
 
		// Create a toolbar button that executes the plugin command defined above.
		// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.ui.html#addButton
		editor.ui.addButton( 'SimpleLink',
		{
			// Toolbar button tooltip.
			label: 'Insert a Link',
			// Reference to the plugin command name.
			command: 'simpleLinkDialog',
			// Button's icon file path.
			icon: this.path + 'images/icon.png'
		} );
 
		// Add a new dialog window definition containing all UI elements and listeners.
		// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.dialog.html#.add
		// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.dialog.dialogDefinition.html
		CKEDITOR.dialog.add( 'simpleLinkDialog', function( editor )
		{
			return {
				// Basic properties of the dialog window: title, minimum size.
				// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.dialog.dialogDefinition.html
				title : 'Related News Properties',
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
								html : 'Add related news box'
							},
							// Dialog window UI element: a textarea field for the link text.
							// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.ui.dialog.textarea.html
							{
								type : 'text',
								id : 'height',
								// Text that labels the field.
								// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.ui.dialog.labeledElement.html#constructor
								label : 'Related News Box Height',
                                                                
                                                                'default' : '200',
								// Validation checking whether the field is not empty.
								validate : CKEDITOR.dialog.validate.notEmpty( 'News Box height field cannot be empty.' ),
								// This field is required.
								required : true,
								// Function to be run when the commitContent method of the parent dialog window is called.
								// Get the value of this field and save it in the data object attribute.
								// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.dom.element.html#getValue
								commit : function( data )
								{
									data.height = this.getValue();
								}
							},
							// Dialog window UI element: a text input field for the link URL.
							// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.ui.dialog.textInput.html
							{
								type : 'text',
								id : 'width',
                                                                'default' : '200',
								label : 'Related News Box width',
								validate : CKEDITOR.dialog.validate.notEmpty( 'News Box width field cannot be empty.' ),
								required : true,
								commit : function( data )
								{
									data.width = this.getValue();
								}
							},
                                                        {
								type : 'select',
								id : 'style',
								label : 'Style',
								// Items that will appear inside the selection field, in pairs of displayed text and value.
								// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.ui.dialog.select.html#constructor
								items : 
								[
									[ '<none>', '' ],
									[ 'Left', 'left' ],
									[ 'Right', 'right' ]
								],
								commit : function( data )
								{
									data.style = this.getValue();
								}
							}
							
						]
					}
				],
				onOk : function()
				{
					// Create a link element and an object that will store the data entered in the dialog window.
					// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.dom.document.html#createElement
					var dialog = this,
						data = {},
						img = editor.document.createElement( 'img' );
					// Populate the data object with data entered in the dialog window.
					// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.dialog.html#commitContent
					this.commitContent( data );

					// Set the URL (href attribute) of the link element.
					// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.dom.element.html#setAttribute
					img.setAttribute( 'src', base_url_related_news );
                                        
                                        img.setAttribute( 'class', "related_news_on_post" );
                                        
                                        img.setAttribute( 'width', data.width );
                                        
                                        img.setAttribute( 'height', data.height );
                                        switch( data.style )
					{
						case 'left' :
							img.setStyle( 'float', 'left' );
						break;
						case 'right' :
							img.setStyle( 'float', 'right' );
						break;
					}

					editor.insertElement( img );
				}
			};
		} );
	}
} );