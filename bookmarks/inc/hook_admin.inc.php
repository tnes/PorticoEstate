<?php
	/**
	* Bookmarks admin hook
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package bookmarks
	* @version $Id: hook_admin.inc.php,v 1.11 2006/09/09 09:04:48 skwashd Exp $
	*/

	$file = Array
	(
		'Site Configuration' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index', 'appname' => 'bookmarks') ),
		'Global Categories' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index', 'appname' => 'bookmarks') )
	);
	$GLOBALS['phpgw']->common->display_mainscreen($appname,$file);
?>
