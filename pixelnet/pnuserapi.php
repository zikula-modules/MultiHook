<?php
// $Id$
// =======================================================================
// pixelnet (c) Frank Schummertz 2004
// ----------------------------------------------------------------------
// For POST-NUKE Content Management System
// Copyright (C) 2002 by the PostNuke Development Team.
// http://www.postnuke.com/
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WIthOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// =======================================================================

/**
 * startsession
 * starts a new session with pixelnet.de and sets the cart counter to 0
 *
 */
function pixelnet_userapi_startsession($args)
{
    extract($args);
    unset($args);
    
    $shopURL = "http://www.pixelnet.de/upload/";
    $vcode = pnModGetVar('pixelnet', 'vcode');

    pnSessionDelVar('pixelnet_sid');
    pnSessionDelVar('pixelnet_bid');
    pnSessionSetVar('pixelnet_imgno', 0);

    $returnURL = urlencode(pnModURL('pixelnet', 'user', 'sessionstarted', array('imgno' => 0 ))); 
    
	$request = $shopURL . "registersession.php3?vcode=$vcode&redirecturl=$returnURL";
    pnRedirect($request);
    return true;
}

?>