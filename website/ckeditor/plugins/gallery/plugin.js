// Register a new CKEditor plugin.
// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.resourceManager.html#add

CKEDITOR.plugins.add( 'gallery',
{
    init: function( editor )
    {
        editor.addCommand( 'insertGallery',
        {
            exec : function( editor )
            {    
                var gallery = "[[gallery]]";
                editor.insertHtml( gallery );
            }
        });

        editor.ui.addButton( 'Timestamp',
        {
                label: 'Insert Gallery',
                command: 'insertGallery',
                icon: this.path + 'images/icon.png'
        } );
    }
} );

