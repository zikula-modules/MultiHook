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
    $dom = ZLanguage::getModuleDomain('MultiHook');
    // Security check
    if (!SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_ADD)) {
        return LogUtil::registerPermissionError();
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
        return LogUtil::registerError(__('Error: entry creation failed', $dom));
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
    $dom = ZLanguage::getModuleDomain('MultiHook');
    // Security check
    if (!SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerPermissionError();
    }

    // Argument check
    if (!isset($args['aid'])) {
        return LogUtil::registerError(_MODARGSERROR . ' in MultiHook_adminapi_delete() [aid]');
    }

    $res = DBUtil::deleteObjectByID ('multihook', (int)$args['aid'], 'aid');
    if($res==false) {
        return LogUtil::registerError(__('Database deletion of entry failed', $dom));
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
    $dom = ZLanguage::getModuleDomain('MultiHook');
    if (!SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_EDIT)) {
        return LogUtil::registerPermissionError();
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
        return LogUtil::registerError(__('Database update of entry failed', $dom));
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
    $dom = ZLanguage::getModuleDomain('MultiHook');
    $needles = array();

    $modtypes = array(2 => 'modules', 3 => 'system');
    // get an array with modinfos of all active modules
    $allmods = pnModGetAllMods();
    if(is_array($allmods) && count($allmods)>0) {
        foreach($allmods as $mod) {
            $needledir = $modtypes[$mod['type']] . '/' . $mod['directory'] . '/pnneedleapi/';
            if(file_exists($needledir) && is_readable($needledir)) {
                $dh = opendir($needledir);
                if($dh) {
                    while($file = readdir($dh)) {
                        if((is_file($needledir . $file)) &&
                                ($file != '.') &&
                                ($file != '..') &&
                                ($file != '.svn') &&
                                ($file != 'index.html') &&
                                (stristr($file, '_info.php'))) {
                            Loader::includeOnce($needledir . $file);
                            $needle = str_replace('_info.php', '', $file);
                            $infofunc = $mod['name'] . '_needleapi_' . $needle . '_info';
                            if(function_exists($infofunc)){
                                $needleinfo = $infofunc();
                            } else {
                                $needleinfo['info']          = __('no description found', $dom);
                                $needleinfo['module']        = _MH_NOMODULEFOUND;
                                $needleinfo['inspect']       = false;
                            }
                            // check if the needle_info sets the 'needle' value
                            // if not, use the needle name
                            if (!array_key_exists('needle', $needleinfo)) {
                                $needleinfo['needle'] = $needle;
                            }
                            // check if the needle_info sets the 'function' value
                            // if not, use the needle name
                            if (!array_key_exists('function', $needleinfo)) {
                                $needleinfo['function'] = $needle;
                            }
                            // check if the needle_info sets the 'casesensitive' value
                            // if not, set it to true
                            if (!array_key_exists('casesensitive', $needleinfo)) {
                                $needleinfo['casesensitive'] = true;
                            }
                            $needleinfo['builtin'] = ($mod['name']=='MultiHook') ? true : false;
                            $needles[] = $needleinfo;
                        }
                    }
                    closedir($dh);
                }
            }
        }
        // sort needles by needle name
        uasort($needles, 'cmp_needleorder');
    }
    // store the needles array now
    pnModSetVar('MultiHook', 'needles', $needles);
    return $needles;
}

/**
 * sorting needles by module name
 *
 */
function cmp_needleorder ($a, $b)
{
    return $a['module'] > $b['module'];
}

/**
 * get available admin panel links
 *
 * @author Mark West
 * @return array array of admin links
 */
function MultiHook_adminapi_getlinks()
{
    $dom = ZLanguage::getModuleDomain('MultiHook');
    $links = array();
    if (SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_ADMIN)) {
        $links[] = array('url' => pnModURL('MultiHook', 'admin', 'main'), 'text' => __('Start', $dom));
        $links[] = array('url' => pnModURL('MultiHook', 'admin', 'edit', array('aid' => -1)), 'text' => __('Add item', $dom));
        $links[] = array('url' => pnModURL('MultiHook', 'admin', 'view', array('filter' => 0)), 'text' => __('Abbreviations', $dom), 'title' => __('View abbreviations', $dom));
        $links[] = array('url' => pnModURL('MultiHook', 'admin', 'view', array('filter' => 1)), 'text' => __('Acronyms', $dom), 'title' => __('View acronyms', $dom));
        $links[] = array('url' => pnModURL('MultiHook', 'admin', 'view', array('filter' => 2)), 'text' => __('Links', $dom), 'title' => __('View links', $dom));
        $links[] = array('url' => pnModURL('MultiHook', 'admin', 'view', array('filter' => 3)), 'text' => __('Censor', $dom), 'title' => __('View censored words', $dom));
        $links[] = array('url' => pnModURL('MultiHook', 'admin', 'viewneedles'), 'text' => __('Needles', $dom), 'title' => __('View needles', $dom));
        $links[] = array('url' => pnModURL('MultiHook', 'admin', 'modifyconfig'), 'text' => __('Modify Configuration', $dom));
    }
    return $links;
}
