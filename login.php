<?php

	/**
	 * phpGroupWare
	 *
	 * phpgroupware base
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2013 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package phpgroupware
	 * @version $Id$
	 */

	require_once 'phpgwapi/inc/class.login.inc.php';

	$phpgwlogin = new phpgwapi_login;

	if (!empty($_GET['create_account']))
	{
		$phpgwlogin->create_account();
	}
	else if (!empty($_GET['create_mapping']))
	{
		$phpgwlogin->create_mapping();
	}
	else
	{
		$phpgwlogin->login();
	}

