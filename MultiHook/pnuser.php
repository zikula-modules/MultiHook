<?php
// $Id$
// ----------------------------------------------------------------------
// PostNuke Content Management System
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
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------
// Original Author of file: Jim McDonald
// Purpose of file:  MultiHook user functions
// ----------------------------------------------------------------------

/**
 * the main function
 *
 *@params $filter (int)
 *@params $startnum (int)
 */
function MultiHook_user_main()
{
    // Get parameters from whatever input we need
    $startnum = (int)pnVarCleanFromInput('startnum');
    $filter   = pnVarCleanFromInput('filter');

    if (!pnSecAuthAction(0, 'MultiHook::', '::', ACCESS_READ)) {
        return _MH_NOAUTH;
    }

    // Load API
    if (!pnModAPILoad('MultiHook', 'user')) {
        pnSessionSetVar('errormsg', _LOADFAILED);
        pnRedirect("index.php");
        return true;
    }

    if(is_numeric($filter) && $filter>=0 && $filter<=2) {
        $abacs = pnModAPIFunc('MultiHook',
                              'user',
                              'getall',
                              array('startnum' => $startnum,
                                    'filter'   => $filter,
                                    'numitems' => pnModGetVar('MultiHook',
                                                              'itemsperpage')));
        $abacscount = pnModAPIFunc('MultiHook', 'user', 'countitems', array('filter' => $filter));
    } else {
        $abacs = array();
        $abascount = 0;
        $filter = -1;
    }
    $titles = array( _MH_VIEWABBR,
                     _MH_VIEWACRONYMS,
                     _MH_VIEWLINKS );

    // Create output object
    $pnr =& new pnRender('MultiHook');
    $pnr->caching = false;
    $pnr->assign('abacs', $abacs);
    $pnr->assign('title', $titles[$filter]);
    $pnr->assign('filter', $filter);
    $pnr->assign('abacscount', $abacscount );
    $pnr->assign('itemsperpage', pnModGetVar('MultiHook', 'itemsperpage'));
    return $pnr->fetch('mh_user_main.html');
}


?>