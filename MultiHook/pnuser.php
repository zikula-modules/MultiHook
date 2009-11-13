<?php
// $Id$
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
// Original Author of file: Frank Schummertz
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
    $dom = ZLanguage::getModuleDomain('MultiHook');

    // Get parameters from whatever input we need
    $startnum = (int)FormUtil::getPassedValue('startnum', 0, 'GETPOST');
    $filter   = (int)FormUtil::getPassedValue('filter', -1, 'GETPOST');

    if (!SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_READ)) {
        return LogUtil::registerPermissionError('index.php');
    }

    // no censor!
    if($filter>=0 && $filter<=2) {
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
        $abacscount = 0;
        $filter = -1;
    }
    $titles = array( __('View abbreviations', $dom),
                     __('View acronyms', $dom),
                     __('View links', $dom));

    // Create output object
    $pnr = pnRender::getInstance('MultiHook', false);
    $pnr->add_core_data();
    $pnr->assign('abacs', $abacs);
    $pnr->assign('title', $titles[$filter]);
    $pnr->assign('filter', $filter);
    $pnr->assign('abacscount', $abacscount );
    return $pnr->fetch('mh_user_main.html');
}
