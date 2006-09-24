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
    if (!pnSecAuthAction(0, 'MultiHook::', "::", ACCESS_ADD)) {
        pnSessionSetVar('errormsg', _MH_NOAUTH);
        return false;
    }

    // Get arguments from argument array
    extract($args);

    // Argument check - make sure that all required arguments are present,
    // if not then set an appropriate error message and return
    if ((!isset($short)) ||
        (!isset($long)) ||
        (!isset($title)) ||
        (!isset($type)) ||
        (!isset($language))) {
        pnSessionSetVar('errormsg', _MODARGSERROR . ' in MultiHook_adminapi_create()');
        return false;
    }

    // Get database setup
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $multihooktable = $pntable['multihook'];
    $multihookcolumn = &$pntable['multihook_column'];

    // Get next ID in table
    $nextId = $dbconn->GenId($multihooktable);

    // Add item
    $sql = "INSERT INTO $multihooktable (
              $multihookcolumn[aid],
              $multihookcolumn[short],
              $multihookcolumn[title],
              $multihookcolumn[long],
              $multihookcolumn[type],
              $multihookcolumn[language])
            VALUES (
              $nextId,
              '" . pnVarPrepForStore($short) . "',
              '" . pnVarPrepForStore($title) . "',
              '" . pnVarPrepForStore($long) . "',
              '" . pnVarPrepForStore($type) . "',
              '" . pnVarPrepForStore($language) . "')";
    $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', _MH_CREATEFAILED);
        return false;
    }

    // Get the ID of the item that we inserted
    $aid = $dbconn->PO_Insert_ID($multihooktable, $multihookcolumn['aid']);

    // Let any hooks know that we have created a new abbrviation
    pnModCallHooks('item', 'create', $aid, 'aid');

    // Return the id of the newly created abbr to the calling process
    return $aid;
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
    if (!pnSecAuthAction(0, 'MultiHook::', '', ACCESS_ADMIN)) {
        pnSessionSetVar('errormsg', _MH_NOAUTH);
        return false;
    }
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($aid)) {
        pnSessionSetVar('errormsg', _MODARGSERROR . ' in MultiHook_adminapi_delete() [aid]');
        return false;
    }

    // The user API function is called
    $abac = pnModAPIFunc('MultiHook',
                         'user',
                         'get',
                         array('aid' => $aid));

    if ($abac == false) {
        return false;
    }

    // Get database setup
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $multihooktable = $pntable['multihook'];
    $multihookcolumn = &$pntable['multihook_column'];

    // Delete the item
    $sql = "DELETE FROM $multihooktable
            WHERE $multihookcolumn[aid] = '".pnVarPrepForStore($aid)."'";
    $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', _MH_DELETEFAILED);
        return false;
    }

    // Let any hooks know that we have deleted a abbr
    pnModCallHooks('item', 'delete', $aid, '');

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
    if (!pnSecAuthAction(0, 'MultiHook::', '', ACCESS_EDIT)) {
        pnSessionSetVar('errormsg', _MH_NOAUTH);
        return false;
    }

    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($aid)) ||
        (!isset($short)) ||
        (!isset($title)) ||
        (!isset($long)) ||
        (!isset($type)) ||
        (!isset($language))) {
        pnSessionSetVar('errormsg', _MODARGSERROR . ' in MultiHook_adminapi_update()');
        return false;
    }

    // The user API function is called
    $abac = pnModAPIFunc('MultiHook',
                         'user',
                         'get',
                         array('aid' => $aid));

    if ($abac == false) {
        return false;
    }

    // Get database setup
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $multihooktable = $pntable['multihook'];
    $multihookcolumn = &$pntable['multihook_column'];

    // Update the abbr
    $sql = "UPDATE $multihooktable
            SET $multihookcolumn[short] = '" . pnVarPrepForStore($short) . "',
                $multihookcolumn[long] = '" . pnVarPrepForStore($long) . "',
                $multihookcolumn[title] = '" . pnVarPrepForStore($title) . "',
                $multihookcolumn[type] = '" . pnVarPrepForStore($type) . "',
                $multihookcolumn[language] = '" . pnVarPrepForStore($language) . "'
            WHERE $multihookcolumn[aid] = '".pnVarPrepForStore($aid)."'";
    $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', _MH_UPDATEFAILED);
        return false;
    }

    // Let the calling process know that we have finished successfully
    return $aid;
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

?>