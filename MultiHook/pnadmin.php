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
// Purpose of file:  MultiHook administration display functions
// ----------------------------------------------------------------------

/**
 * the main administration function
 */
function MultiHook_admin_main()
{
    if(!SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_ADMIN)) {
        LogUtil::registerError(_MH_NOAUTH);
        return pnRedirect('index.php');
    }
    
    $pnr = new pnRender('MultiHook', false);
    $hmods = pnModAPIFunc('modules', 'admin', 'gethookedmodules', array('hookmodname' => 'MultiHook'));
    foreach($hmods as $hmod => $dummy) {
        $modid = pnModGetIDFromName($hmod);
        $moddata = pnModGetInfo($modid);
        $moddata['id'] = $modid;
        $hookedmodules[] = $moddata;
    }
    $pnr->assign('hookedmodules', $hookedmodules);
    return $pnr->fetch("mh_admin_main.html");
}

/**
 * add new item
 */
function MultiHook_admin_edit($args)
{
    // Security check
    if (!SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_ADD)) {
        LogUtil::registerError(_MH_NOAUTH);
        return pnRedirect(pnModURL('MultiHook', 'admin', 'main'));
    }

    // aid = -1 means add a new entry
    $aid = (int)FormUtil::getPassedValue('aid', (isset($args['aid'])) ? $args['aid'] : -1, 'GETPOST');

    if(($aid==-1) ) {
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
            LogUtil::registerError(_MH_NOSUCHITEM);
            return pnRedirect(pnModURL('MultiHook', 'admin', 'main'));
        }
            // set permission flags
            $abac['edit'] = false;
            $abac['delete'] = false;

            if (SecurityUtil::checkPermission('MultiHook::', "$abac[short]::$abac[aid]", ACCESS_EDIT)) {
                $abac['edit'] = true;
                if (SecurityUtil::checkPermission('MultiHook::', "$abac[short]::$abac[aid]", ACCESS_DELETE)) {
                    $abac['delete'] = true;
                }
            } else {
                LogUtil::registerError(_MH_NOAUTH);
                return pnRedirect(pnModURL('MultiHook','admin','main'));
            }

    }
    $pnr = new pnRender('MultiHook', false);
    $pnr->assign('abac', $abac);
    $pnr->assign('types', array( _MH_TYPEABBREVIATION,
                                 _MH_TYPEACRONYM,
                                 _MH_TYPELINK,
                                 _MH_TYPEILLEGALWORD));
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
function MultiHook_admin_store()
{
    if(!SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_ADD)) {
        LogUtil::registerError(_MH_NOAUTH);
        return pnRedirect(pnModURL('MultiHook', 'admin', 'main'));
    }

    // Confirm authorisation code.
    if (!pnSecConfirmAuthKey()) {
        LogUtil::registerError(_BADAUTHKEY);
        return pnRedirect(pnModURL('MultiHook', 'admin', 'main'));
    }

    // Get parameters from whatever input we need
    $aid       = (int)FormUtil::getPassedValue('mh_aid',      -1, 'GETPOST');
    $short     =      FormUtil::getPassedValue('mh_short',    '', 'GETPOST');
    $long      =      FormUtil::getPassedValue('mh_long',     '', 'GETPOST');
    $title     =      FormUtil::getPassedValue('mh_title',    '', 'GETPOST');
    $type      = (int)FormUtil::getPassedValue('mh_type',     0,  'GETPOST');
    $language  =      FormUtil::getPassedValue('mh_language', '', 'GETPOST');
    $mh_delete =      FormUtil::getPassedValue('mh_delete',   '', 'GETPOST');

    if(!empty($mh_delete) && ($mh_delete=='1') ) {
        // The API function is called
        if (pnModAPIFunc('MultiHook',
                         'admin',
                         'delete',
                         array('aid' => $aid))) {
            // Success
            LogUtil::registerStatus(_MH_DELETED);
        } else {
            LogUtil::registerError(_MH_DELETEFAILED);
        }
        return pnRedirect(pnModURL('MultiHook', 'admin', 'view', array('filter' => $abac['type'])));
    }
    // no deletion, further checks needed
    if(empty($short)) {
        LogUtil::registerError(_MH_SHORTEMPTY );
        return pnRedirect(pnModURL('MultiHook', 'admin', 'main'));
    }
    if(empty($long) && ($type<>3)) {
        LogUtil::registerError(_MH_LONGEMPTY );
        return pnRedirect(pnModURL('MultiHook', 'admin', 'main'));
    }
    if(($type<0) || ($type>3)) {
        LogUtil::registerError(_MH_TYPEEMPTY . "($type)" );
        return pnRedirect(pnModURL('MultiHook', 'admin', 'main'));
    }
    if($type==2 && empty($title)) {
        LogUtil::registerError(_MH_TITLEEMPTY );
        return pnRedirect(pnModURL('MultiHook', 'admin', 'main'));
    }

    if(empty($language)) {
        LogUtil::registerError(_MH_LANGUAGEEMPTY );
        return pnRedirect(pnModURL('MultiHook', 'admin', 'main'));
    }

    // The API function is called
    if($aid == -1) {
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
            LogUtil::registerStatus( _MH_CREATED);
        } else {
            LogUtil::registerError(_MH_CREATEDFAILED);
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
            LogUtil::registerStatus(_MH_UPDATED);
        } else {
            LogUtil::registerError(_MH_UPDATEFAILED);
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
    if (!SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_ADMIN)) {
        LogUtil::registerError(_MH_NOAUTH);
        return pnRedirect(pnModURL('MultiHook', 'admin', 'main'));
    }

    // Get parameters from whatever input we need
    $startnum = (int)FormUtil::getPassedValue('startnum', 0, 'GETPOST');
    $filter   = (int)FormUtil::getPassedValue('filter', -1, 'GETPOST');

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

        if (SecurityUtil::checkPermission('MultiHook::', "$abacs[$cnt][short]::$abacs[$cnt][aid]", ACCESS_EDIT)) {
            $abacs[$cnt]['edit'] = true;
            if (SecurityUtil::checkPermission('MultiHook::', "$abacs[$cnt][short]::$abacs[$cnt][aid]", ACCESS_DELETE)) {
                $abacs[$cnt]['delete'] = true;
            }
        }
    }
    $titles = array( _MH_VIEWABBR,
                     _MH_VIEWACRONYMS,
                     _MH_VIEWLINKS,
                     _MH_VIEWILLEGALWORDS );

    // Create output object
    $pnr = new pnRender('MultiHook', false);
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

    if (!SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_ADMIN)) {
        LogUtil::registerError(_MH_NOAUTH);
        return pnRedirect(pnModURL('MultiHook', 'admin', 'main'));
    }

    $submit = FormUtil::getPassedValue('submit', null, 'GETPOST');

    if(!$submit) {
        $pnr = new pnRender('MultiHook', false);
        $pnr->add_core_data();
        return $pnr->fetch('mh_admin_config.html');
    } else {  // submit is set
        if (!pnSecConfirmAuthKey()) {
            LogUtil::registerError(_BADAUTHKEY);
            return pnRedirect(pnModURL('MultiHook', 'admin', 'main'));
        }

        $abacfirst         = FormUtil::getPassedValue('abacfirst', 0, 'GETPOST');
        $mhincodetags      = FormUtil::getPassedValue('mhincodetags', '', 'GETPOST');
        $mhlinktitle       = FormUtil::getPassedValue('mhlinktitle', '', 'GETPOST');
        $mhreplaceabbr     = FormUtil::getPassedValue('mhreplaceabbr', '', 'GETPOST');
        $mhshoweditlink    = FormUtil::getPassedValue('mhshoweditlink', '', 'GETPOST');
        $externallinkclass = FormUtil::getPassedValue('externallinkclass', '', 'GETPOST');
        $itemsperpage      = (int)FormUtil::getPassedValue('itemsperpage', 20, 'GETPOST');

        pnModSetVar('MultiHook', 'abacfirst', $abacfirst);
        pnModSetVar('MultiHook', 'mhincodetags', $mhincodetags);
        pnModSetVar('MultiHook', 'mhlinktitle', $mhlinktitle);
        pnModSetVar('MultiHook', 'mhreplaceabbr', $mhreplaceabbr);
        pnModSetVar('MultiHook', 'mhshoweditlink', $mhshoweditlink);
        pnModSetVar('MultiHook', 'itemsperpage', $itemsperpage);
        pnModSetVar('MultiHook', 'externallinkclass', $externallinkclass);

        LogUtil::registerStatus(_MH_UPDATEDCONFIG);
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
    if(SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_ADD)) {
        pnModLangLoad('MultiHook', 'admin');
        $pnr = new pnRender('MultiHook', false);
        $out = $pnr->fetch('mh_dynamic_hiddenform.html');
    }
    return $out;
}

/**
 * viewneedles
 *
 * shows a list of all needles supported by the MultiHook
 */
function MultiHook_admin_viewneedles()
{
    // todo: scan for needles and show them
    if (!SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_ADMIN)) {
        LogUtil::registerError(_MH_NOAUTH);
        return pnRedirect(pnModURL('MultiHook', 'admin', 'main'));
    }
    
    $needles = pnModAPIFunc('MultiHook', 'admin', 'collectneedles');
    
    $needles = array();
    $needledir = 'modules/MultiHook/pnneedleapi/';
    $dh = opendir($needledir);
    while($file = readdir($dh)) {
        if((is_file($needledir . $file)) &&
                ($file != '.') &&
                ($file != '..') &&
                ($file != 'index.html') &&
                (stristr($file, '_info.php'))) {
            include_once($needledir . $file);
            $needle = str_replace('_info.php', '', $file);
            $infofunc = 'MultiHook_needleapi_' . $needle . '_info';
            if(function_exists($infofunc)){
                list($module, $description) = $infofunc();
            } else {
                $description = _MH_NODESCRIPTIONFOUND;
                $module      = _MH_NOMODULEFOUND;
            }
            $needles[] = array('module'      => $module,
                               'needle'      => $needle,
                               'description' => $description);
        }
    }
    
    // store the needlesarray now
    pnModSetVar('MultiHook', 'needles', serialize($needles));
    
    
    $pnr = new pnRender('MultiHook', false);
    $pnr->assign('needles', $needles);
    return $pnr->fetch('mh_admin_viewneedles.html');    
}
?>