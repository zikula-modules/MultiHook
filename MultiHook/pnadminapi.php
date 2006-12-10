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
// Purpose of file:  MultiHook administration API
// ----------------------------------------------------------------------

/**
 * create a new entry
 * @param $args['short'] short name of the item
 * @param $args['long'] long name of the item
 * @param $args['title'] title of the item
 * @param $args['type'] type of the item: 1=acronym, 0=abbreviation, 2=link
 * @param $args['language'] language of the item
 * @returns int
 * @return id on success, false on failure
 */
function MultiHook_adminapi_create($args)
{
    // Security check
    if (!SecurityUtil::checkPermission('MultiHook::', "::", ACCESS_ADD)) {
        return LogUtil::registerError(_MH_NOAUTH);
    }

    // Argument check - make sure that all required arguments are present,
    // if not then set an appropriate error message and return
    if ((!isset($args['short'])) ||
        (!isset($args['long'])) ||
        (!isset($args['title'])) ||
        (!isset($args['type'])) ||
        (!isset($args['language']))) {
        return LogUtil::registerError(_MODARGSERROR . ' in MultiHook_adminapi_create()');
    }

    $obj = DBUtil::insertObject($args, 'multihook', 'aid');
    if($obj == false) {
        return LogUtil::registerError(_MH_CREATEFAILED);
    }
    pnModCallHooks('item', 'create', $obj['aid'], 'aid');
    return $obj['aid'];
}

/**
 * delete an abbreviation
 * @param $args['aid'] ID of the abbr/acronym/link
 * @returns bool
 * @return true on success, false on failure
 */
function MultiHook_adminapi_delete($args)
{
    // Security check
    if (!SecurityUtil::checkPermission('MultiHook::', '', ACCESS_ADMIN)) {
        return LogUtil::registerError(_MH_NOAUTH);
    }

    // Argument check
    if (!isset($args['aid'])) {
        return LogUtil::registerError(_MODARGSERROR . ' in MultiHook_adminapi_delete() [aid]');
    }

    $res = DBUtil::deleteObjectByID ('multihook', (int)$args['aid'], 'aid');
    if($res==false) {
        return LogUtil::registerError(_MH_DELETEFAILED);
    }

    // Let any hooks know that we have deleted a abbr
    pnModCallHooks('item', 'delete', $args['aid'], '');

    // Let the calling process know that we have finished successfully
    return true;
}

/**
 * update an entry
 * @param $args['aid'] the id
 * @param $args['short'] short name
 * @param $args['title'] title
 * @param $args['long'] long name
 * @param $args['type'] type
 * @param $args['language'] language
 */
function MultiHook_adminapi_update($args)
{
    if (!SecurityUtil::checkPermission('MultiHook::', '', ACCESS_EDIT)) {
        return LogUtil::registerError(_MH_NOAUTH);
    }

    // Get arguments from argument array
    //extract($args);

    // Argument check
    if ((!isset($args['aid'])) ||
        (!isset($args['short'])) ||
        (!isset($args['title'])) ||
        (!isset($args['long'])) ||
        (!isset($args['type'])) ||
        (!isset($args['language']))) {
        return LogUtil::registerError(_MODARGSERROR . ' in MultiHook_adminapi_update()');
    }

    $res = DBUtil::updateObject($args, 'multihook', '', 'aid');
    if($res == false) {
        return LogUtil::registerError(_MH_UPDATEFAILED);
    }
    return $args['aid'];
}

/**
 * collectneedles
 * scans the pnneedleapi folder for needles and stores them in a module var
 *
 *@params none
 *@returns array of needles
 */
function MultiHook_adminapi_collectneedles()
{
    $needles = array();
    $needledir = 'modules/MultiHook/pnneedleapi/';
    $dh = opendir($needledir);
    if($dh) {
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
        closedir($dh);
    } 
    // store the needlesarray now
    pnModSetVar('MultiHook', 'needles', serialize($needles));
    return $needles;
}

/**
 * get available admin panel links
 *
 * @author Mark West
 * @return array array of admin links
 */
function MultiHook_adminapi_getlinks()
{
    $links = array();
    if (SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_ADMIN)) {
        $links[] = array('url' => pnModURL('MultiHook', 'admin', 'main'), 'text' => _MH_START);
        $links[] = array('url' => pnModURL('MultiHook', 'admin', 'edit', array('aid' => -1)), 'text' => _MH_ADD);
        $links[] = array('url' => pnModURL('MultiHook', 'admin', 'view', array('filter' => 0)), 'text' => _MH_ABBREVIATION, 'title' => _MH_VIEWABBR);
        $links[] = array('url' => pnModURL('MultiHook', 'admin', 'view', array('filter' => 1)), 'text' => _MH_ACRONYM, 'title' => _MH_VIEWACRONYMS);
        $links[] = array('url' => pnModURL('MultiHook', 'admin', 'view', array('filter' => 2)), 'text' => _MH_LINKS, 'title' => _MH_VIEWLINKS);
        $links[] = array('url' => pnModURL('MultiHook', 'admin', 'view', array('filter' => 3)), 'text' => _MH_CENSOR, 'title' => _MH_VIEWCENSOR);
        $links[] = array('url' => pnModURL('MultiHook', 'admin', 'viewneedles'), 'text' => _MH_NEEDLES, 'title' => _MH_VIEWNEEDLES);
        $links[] = array('url' => pnModURL('MultiHook', 'admin', 'modifyconfig'), 'text' => _MH_MODIFYCONFIG);
    }
    return $links;
}

?>