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

function pixelnet_admin_main()
{
    $pnr =& new pnRender('pixelnet');
    $pnr->caching = false;
    $pnr->assign('vcode', pnModGetVar('pixelnet', 'vcode'));
    return $pnr->fetch('pixelnet_admin_main.html');
}

function pixelnet_admin_update()
{
    $vcode = pnVarCleanFromInput('vcode');
    pnModSetVar('pixelnet', 'vcode', pnVarPrepForStore($vcode));
    pnSessionSetVar('statusmsg', 'Konfiguration geändert');
    pnRedirect(pnModURL('pixelnet','admin','main'));
    return true;
}

?>