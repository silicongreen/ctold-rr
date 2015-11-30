﻿/*
 * FCKeditor - The text editor for Internet - http://www.fckeditor.net
 * Copyright (C) 2003-2008 Frederico Caldeira Knabben
 *
 * == BEGIN LICENSE ==
 *
 * Licensed under the terms of any of the following licenses at your
 * choice:
 *
 *  - GNU General Public License Version 2 or later (the "GPL")
 *    http://www.gnu.org/licenses/gpl.html
 *
 *  - GNU Lesser General Public License Version 2.1 or later (the "LGPL")
 *    http://www.gnu.org/licenses/lgpl.html
 *
 *  - Mozilla Public License Version 1.1 or later (the "MPL")
 *    http://www.mozilla.org/MPL/MPL-1.1.html
 *
 * == END LICENSE ==
 *
 * FCKRemoveFormatCommand Class: controls the execution of a core style. Core
 * styles are usually represented as buttons in the toolbar., like Bold and
 * Italic.
 */

 var FCKRemoveFormatCommand = function()
 {
 	this.Name = 'RemoveFormat' ;
 }

 FCKRemoveFormatCommand.prototype =
 {
	Execute : function()
	{
		FCKStyles.RemoveAll() ;

		FCK.Focus() ;
		FCK.Events.FireEvent( 'OnSelectionChange' ) ;
	},

	GetState : function()
	{
		return FCK.EditorWindow ? FCK_TRISTATE_OFF : FCK_TRISTATE_DISABLED ;
	}
 };
