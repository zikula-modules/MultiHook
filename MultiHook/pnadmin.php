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
// Purpose of file:  MultiHook administration display functions
// ----------------------------------------------------------------------

/**
 * the main administration function
 */
function MultiHook_admin_main()
{
    $pnr =& new pnRender("MultiHook");
    $pnr->caching = false;
    return $pnr->fetch("mh_admin_main.html");
}

/**
 * add new item
 */
function MultiHook_admin_edit($args)
{
    // Security check
    if (!pnSecAuthAction(0, 'MultiHook::', '::', ACCESS_ADD)) {
        return pnVarPrepForDisplay(_MH_NOAUTH);
    }

    $aid = pnVarCleanFromInput('aid');
    extract($args);

    if( (!isset($aid)) || ($aid==-1) ) {
        $abac = array( 'aid'   => -1,
                       'short' => '',
                       'long'  => '',
                       'title' => '',
                       'type'  => 0,
                       'language' => pnUserGetLang() );
    } else {
        $abac = pnModAPIFunc('MultiHook',
                             'user',
                             'get',
                             array('aid' => $aid));

        if ($abac == false) {
            pnSessionSetVar('errormsg', _MH_NOSUCHITEM);
            pnRedirect(pnModURL('MultiHook', 'admin', 'main'));
            return true;
        }
    }
    $pnr =& new pnRender("MultiHook");
    $pnr->caching = false;
    $pnr->assign('abac', $abac);
    $pnr->assign('abbrstorelink', pnModURL('MultiHook', 'admin', 'store'));
    $pnr->assign('types', array( _MH_TYPEABBREVIATION,
                                 _MH_TYPEACRONYM,
                                 _MH_TYPELINK ));
    return $pnr->fetch("mh_admin_edit.html");
}

/**
 * This is a standard function that is called with the results of the
 * form supplied by MultiHook_admin_edit() to create a new item or
 * update an existing item
 * @param 'aid' the item id (-1 if it is a new item)
 * @param 'short' the short name of the item to be created
 * @param 'long' the long name of the item to be created
 * @param 'type' the type of the item to be created
 * @param 'language' the language of the item to be created
 */
function MultiHook_admin_store($args)
{
    // Get parameters from whatever input we need
    list($aid,
         $short,
         $long,
         $title,
         $type,
         $language) = pnVarCleanFromInput('abbr_aid',
                                          'abbr_short',
                                          'abbr_long',
                                          'abbr_title',
                                          'abbr_type',
                                          'abbr_language');
    extract($args);

    // Confirm authorisation code.

    if (!pnSecConfirmAuthKey()) {
        pnSessionSetVar('errormsg', _BADAUTHKEY);
        pnRedirect(pnModURL('MultiHook', 'admin', 'main'));
        return true;
    }

    // Check arguments
    if( (isset($aid)) && (!is_numeric($aid)) ) {
        pnSessionSetVar( 'errormsg', _MODARGSERROR );
        pnRedirect(pnModURL('MultiHook', 'admin', 'main'));
        return true;
    }

    if (!isset($short)) {
        pnSessionSetVar( 'errormsg', _MH_SHORTEMPTY );
        pnRedirect(pnModURL('MultiHook', 'admin', 'main'));
        return true;
    }
    if (!isset($long)) {
        pnSessionSetVar( 'errormsg', _MH_LONGEMPTY );
        pnRedirect(pnModURL('MultiHook', 'admin', 'main'));
        return true;
    }
    if (!isset($type)) {
        pnSessionSetVar( 'errormsg', _MH_TYPEEMPTY );
        pnRedirect(pnModURL('MultiHook', 'admin', 'main'));
        return true;
    }
    if ($type==2 && !isset($title)) {
        pnSessionSetVar( 'errormsg', _MH_TITLEEMPTY );
        pnRedirect(pnModURL('MultiHook', 'admin', 'main'));
        return true;
    }

    if (!isset($language)) {
        pnSessionSetVar( 'errormsg', _MH_LANGUAGEEMPTY );
        pnRedirect(pnModURL('MultiHook', 'admin', 'main'));
        return true;
    }

    // The API function is called
    if( $aid == -1 ) {
        $aid = pnModAPIFunc('MultiHook',
                            'admin',
                            'create',
                            array('short' => $short,
                                  'long' => $long,
                                  'title' => $title,
                                  'type' => $type,
                                  'language' => $language));

        if ($aid != false) {
            // Success
            pnSessionSetVar('statusmsg', _MH_CREATED);
        } else {
            pnSessionSetVar('errormsg', _MH_CREATEDFAILED);
        }
    } else {
        if(pnModAPIFunc('MultiHook',
                        'admin',
                        'update',
                        array('aid' => $aid,
                              'short' => $short,
                              'title' => $title,
                              'long' => $long,
                              'type' => $type,
                              'language' => $language))) {
            // Success
            pnSessionSetVar('statusmsg', _MH_UPDATED);
        } else {
            pnSessionSetVar('errormsg', _MH_UPDATEFAILED);
        }
    }

    pnRedirect(pnModURL('MultiHook', 'admin', 'view', array('filter'=>$type)));
    return true;
}

/**
 * delete item
 * @param 'aid' the id of the item to be deleted
 * @param 'confirmation' confirmation that this item can be deleted
 */
function MultiHook_admin_delete($args)
{
    // Get parameters from whatever input we need
    list($aid,
         $obid,
         $confirmation) = pnVarCleanFromInput('aid',
                                              'obid',
                                              'confirmation');
    extract($args);

     if (!empty($obid)) {
         $aid = $obid;
     }

    // The user API function is called
    $abac = pnModAPIFunc('MultiHook',
                         'user',
                         'get',
                         array('aid' => $aid));

    if ($abac == false) {
        pnSessionSetVar('errormsg', _MH_NOSUCHITEM);
        pnRedirect(pnModURL('MultiHook','admin','main'));
        return true;
    }

    // Security check
    if (!pnSecAuthAction(0, 'MultiHook::Item', "$abac[short]::$aid", ACCESS_DELETE)) {
        pnSessionSetVar('errormsg', _MH_NOAUTH);
        pnRedirect(pnModURL('MultiHook','admin','main'));
        return true;
    }

    // Check for confirmation.
    if (empty($confirmation)) {
        // No confirmation yet

        // Create output object
        $pnr =& new pnRender('MultiHook');
        $pnr->caching = false;
        $pnr->assign('abac', $abac);
        return $pnr->fetch('mh_admin_delete.html');
    }

    // If we get here it means that the user has confirmed the action

    // Confirm authorisation code
    if (!pnSecConfirmAuthKey()) {
        pnSessionSetVar('errormsg', _BADAUTHKEY);
        pnRedirect(pnModURL('MultiHook', 'admin', 'view', array('filter'=>$abac['type'])));
        return true;
    }

    // The API function is called
    if (pnModAPIFunc('MultiHook',
                     'admin',
                     'delete',
                     array('aid' => $aid))) {
        // Success
        pnSessionSetVar('statusmsg', _MH_DELETED);
    } else {
        pnSessionSetVar('errormsg', _MH_DELETEFAILED);
    }

    pnRedirect(pnModURL('MultiHook', 'admin', 'view', array('filter'=>$abac['type'])));
    return true;
}

/**
 * view items
 *
 *@params filter (int) 0=abbr, 1=acronyms, 2=links
 */
function MultiHook_admin_view()
{
    // Get parameters from whatever input we need
    $startnum = (int)pnVarCleanFromInput('startnum');
    $filter   = (int)pnVarCleanFromInput('filter');

    if (!pnSecAuthAction(0, 'MultiHook::', '::', ACCESS_EDIT)) {
        return _MH_NOAUTH;
    }

    // The user API function is called
    $abacs = pnModAPIFunc('MultiHook',
                          'user',
                          'getall',
                          array('startnum' => $startnum,
                                'filter'   => $filter,
                                'numitems' => pnModGetVar('MultiHook',
                                                          'itemsperpage')));
    // set permission flags
    for($cnt=0; $cnt<count($abacs); $cnt++ ) {
        $abacs[$cnt]['edit'] = false;
        $abacs[$cnt]['delete'] = false;

        if (pnSecAuthAction(0, 'MultiHook::', "$abacs[$cnt][short]::$abacs[$cnt][aid]", ACCESS_EDIT)) {
            $abacs[$cnt]['edit'] = true;
            if (pnSecAuthAction(0, 'MultiHook::', "$abacs[$cnt][short]::$abacs[$cnt][aid]", ACCESS_DELETE)) {
                $abacs[$cnt]['delete'] = true;
            }
        }
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
    $pnr->assign('abacscount', pnModAPIFunc('MultiHook', 'user', 'countitems', array('filter' => $filter)));
    $pnr->assign('itemsperpage', pnModGetVar('MultiHook', 'itemsperpage'));
    return $pnr->fetch('mh_admin_view.html');
}

/**
 * modify configuration
 */
function MultiHook_admin_modifyconfig()
{

    if (!pnSecAuthAction(0, 'MultiHook::', '::', ACCESS_ADMIN)) {
        return _MH_NOAUTH;
    }
    $pnr =& new pnRender('MultiHook');
    $pnr->caching = false;
    $pnr->assign('abacfirst', pnModGetVar('MultiHook', 'abacfirst'));
    $pnr->assign('mhincodetags', pnModGetVar('MultiHook', 'mhincodetags'));
    $pnr->assign('mhlinktitle', pnModGetVar('MultiHook', 'mhlinktitle'));
    $pnr->assign('mhreplaceabbr', pnModGetVar('MultiHook', 'mhreplaceabbr'));
    $pnr->assign('itemsperpage', pnModGetVar('MultiHook', 'itemsperpage'));
    $pnr->assign('externallinkclass', pnModGetVar('MultiHook', 'externallinkclass'));
    return $pnr->fetch('mh_admin_config.html');
}

/**
 * update configuration
 */
function MultiHook_admin_updateconfig()
{
    list($abacfirst,
         $mhincodetags,
         $mhlinktitle,
         $mhreplaceabbr,
         $externallinkclass,
         $itemsperpage)= pnVarCleanFromInput('abacfirst',
                                             'mhincodetags',
                                             'mhlinktitle',
                                             'mhreplaceabbr',
                                             'externallinkclass',
                                             'itemsperpage');

    if (!pnSecConfirmAuthKey()) {
        pnSessionSetVar('errormsg', _BADAUTHKEY);
        pnRedirect(pnModURL('MultiHook', 'admin', 'main'));
        return true;
    }

    if (!isset($abacfirst)) {
        $abacfirst = 0;
    }
    pnModSetVar('MultiHook', 'abacfirst', $abacfirst);

    if (!isset($itemsperpage)) {
        $itemsperpage = 20;
    }

    pnModSetVar('MultiHook', 'mhincodetags', $mhincodetags);
    pnModSetVar('MultiHook', 'mhlinktitle', $mhlinktitle);
    pnModSetVar('MultiHook', 'mhreplaceabbr', $mhreplaceabbr);
    pnModSetVar('MultiHook', 'itemsperpage', $itemsperpage);
    pnModSetVar('MultiHook', 'externallinkclass', $externallinkclass);

    pnSessionSetVar('statusmsg', _MH_UPDATEDCONFIG);
    pnRedirect(pnModURL('MultiHook', 'admin', 'main'));
    return true;
}


?>