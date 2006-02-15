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
            return pnRedirect(pnModURL('MultiHook', 'admin', 'main'));
        }
        // set permission flags
            $abac['edit'] = false;
            $abac['delete'] = false;
        
            if (pnSecAuthAction(0, 'MultiHook::', "$abac[short]::$abac[aid]", ACCESS_EDIT)) {
                $abac['edit'] = true;
                if (pnSecAuthAction(0, 'MultiHook::', "$abac[short]::$abac[aid]", ACCESS_DELETE)) {
                    $abac['delete'] = true;
                }
            } else {
                pnSessionSetVar('errormsg', _MH_NOAUTH);
                return pnRedirect(pnModURL('MultiHook','admin','main'));
            }

    }
    $pnr =& new pnRender("MultiHook");
    $pnr->caching = false;
    $pnr->assign('abac', $abac);
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
         $language,
         $mh_delete) = pnVarCleanFromInput('abbr_aid',
                                           'abbr_short',
                                           'abbr_long',
                                           'abbr_title',
                                           'abbr_type',
                                           'abbr_language',
                                           'mh_delete');
    extract($args);

    // Confirm authorisation code.

    if (!pnSecConfirmAuthKey()) {
        pnSessionSetVar('errormsg', _BADAUTHKEY);
        return pnRedirect(pnModURL('MultiHook', 'admin', 'main'));
    }

    // Check arguments
    
    
    if( (isset($aid)) && (!is_numeric($aid)) ) {
        pnSessionSetVar( 'errormsg', _MODARGSERROR );
        return pnRedirect(pnModURL('MultiHook', 'admin', 'main'));
    }

    if(!empty($mh_delete) && ($mh_delete=="1") ) {
        $abac = pnModAPIFunc('MultiHook',
                             'user',
                             'get',
                             array('aid' => $aid));
    
        if ($abac == false) {
            pnSessionSetVar('errormsg', _MH_NOSUCHITEM);
            return pnRedirect(pnModURL('MultiHook','admin','main'));
        }
    
        // Security check
        if (!pnSecAuthAction(0, 'MultiHook::Item', "$abac[short]::$aid", ACCESS_DELETE)) {
            pnSessionSetVar('errormsg', _MH_NOAUTH);
            return pnRedirect(pnModURL('MultiHook','admin','main'));
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
        return pnRedirect(pnModURL('MultiHook', 'admin', 'view', array('filter' => $abac['type'])));
    }

    // no deletion, further checks needed
    if (!isset($short)) {
        pnSessionSetVar( 'errormsg', _MH_SHORTEMPTY );
        return pnRedirect(pnModURL('MultiHook', 'admin', 'main'));
    }
    if (!isset($long)) {
        pnSessionSetVar( 'errormsg', _MH_LONGEMPTY );
        return pnRedirect(pnModURL('MultiHook', 'admin', 'main'));
    }
    if (!isset($type)) {
        pnSessionSetVar( 'errormsg', _MH_TYPEEMPTY );
        return pnRedirect(pnModURL('MultiHook', 'admin', 'main'));
    }
    if ($type==2 && !isset($title)) {
        pnSessionSetVar( 'errormsg', _MH_TITLEEMPTY );
        return pnRedirect(pnModURL('MultiHook', 'admin', 'main'));
    }

    if (!isset($language)) {
        pnSessionSetVar( 'errormsg', _MH_LANGUAGEEMPTY );
        return pnRedirect(pnModURL('MultiHook', 'admin', 'main'));
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
                              'language' => $language))<>false) {
            // Success
            pnSessionSetVar('statusmsg', _MH_UPDATED);
        } else {
            pnSessionSetVar('errormsg', _MH_UPDATEFAILED);
        }
    }

    return pnRedirect(pnModURL('MultiHook', 'admin', 'view', array('filter'=>$type)));
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
    $pnr->add_core_data();
    $pnr->assign('abacs', $abacs);
    $pnr->assign('title', $titles[$filter]);
    $pnr->assign('filter', $filter);
    $pnr->assign('abacscount', pnModAPIFunc('MultiHook', 'user', 'countitems', array('filter' => $filter)));
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
    
    $submit = pnVarCleanFromInput('submit');
    
    if(!$submit) {
    
        $pnr =& new pnRender('MultiHook');
        $pnr->caching = false;
        $pnr->add_core_data();
        return $pnr->fetch('mh_admin_config.html');

    } else {  // submit is set
    
        if (!pnSecConfirmAuthKey()) {
            pnSessionSetVar('errormsg', _BADAUTHKEY);
            return pnRedirect(pnModURL('MultiHook', 'admin', 'main'));
        }

        list($abacfirst,
             $mhincodetags,
             $mhlinktitle,
             $mhreplaceabbr,
             $mhshoweditlink,
             $externallinkclass,
             $itemsperpage)= pnVarCleanFromInput('abacfirst',
                                                 'mhincodetags',
                                                 'mhlinktitle',
                                                 'mhreplaceabbr',
                                                 'mhshoweditlink',
                                                 'externallinkclass',
                                                 'itemsperpage');
        
        
        if (empty($abacfirst)) {
            $abacfirst = 0;
        }
        pnModSetVar('MultiHook', 'abacfirst', $abacfirst);
        
        if (empty($itemsperpage)) {
            $itemsperpage = 20;
        }
        
        pnModSetVar('MultiHook', 'mhincodetags', $mhincodetags);
        pnModSetVar('MultiHook', 'mhlinktitle', $mhlinktitle);
        pnModSetVar('MultiHook', 'mhreplaceabbr', $mhreplaceabbr);
        pnModSetVar('MultiHook', 'mhshoweditlink', $mhshoweditlink);
        pnModSetVar('MultiHook', 'itemsperpage', $itemsperpage);
        pnModSetVar('MultiHook', 'externallinkclass', $externallinkclass);
        
        pnSessionSetVar('statusmsg', _MH_UPDATEDCONFIG);
    }
    return pnRedirect(pnModURL('MultiHook', 'admin', 'main'));
}

/**
 * helper
 *
 * Implements hidden divs and javascript for Ajax usage. Used in the 
 * multihookhelper plugin, can also be called from legacy themes or AutoThemes
 * if necessary.
 */
function MultiHook_admin_helper()
{
    $out = '';
    if(pnSecAuthAction(0, 'MultiHook::', '::', ACCESS_ADD)) { 
        pnModLangLoad('MultiHook', 'admin');
        $pnr = new pnRender('MultiHook', false);
        $out = $pnr->fetch('mh_dynamic_hiddenform.html');
    }
    return $out;
}

?>