<?php
// $Id: pnadminapi.php 73 2006-07-16 09:21:42Z landseer $
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
// Purpose of file:  MultiHook needle API
// ----------------------------------------------------------------------

/**
 * download needle
 * @param $args['nid'] needle id
 * @return array()
 */
function MultiHook_needleapi_download($args)
{
    // Get arguments from argument array
    $nid = $args['nid'];
    unset($args);

    // cache the results
    static $cache;
    if(!isset($cache)) {
        $cache = array();
    } 

    if(!pnModAvailable('Downloads')) {
        $result = '<em>DOWNLOAD' . $nid . '</em>';
    } else {
        // Get arguments from argument array
        $temp = explode('_', $nid);
        $type = $temp[0];
        $id   = $temp[1];
        
        pnModDBInfoLoad('Downloads');
        $dbconn =& pnDBGetConn(true);
        $pntable =& pnDBGetTables();
        
        switch($type) {
            case 'C':
                if(!isset($cache[$nid])) {
                    // not in cache array
                    $tbldlcats = $pntable['downloads_categories'];
                    $coldlcats = $pntable['downloads_categories_column'];
                    
                    $sql = 'SELECT ' . $coldlcats['title'] . ' FROM ' . $tbldlcats . ' WHERE ' . $coldlcats['cid'] . '=' . pnVarPrepForStore($id);
                    $res = $dbconn->Execute($sql);
                    if($dbconn->ErrorNo() != 0) {
                        $result = '<em>DOWNLOAD' . $nid . '</em>';
                    } else {
                        list($title) = $res->fields;
                        $url   = pnVarPrepForDisplay('index.php?name=Downloads&req=viewdownload&cid=' . $id);
                        $title = pnVarPrepForDisplay($title);
                        $cache[$nid] = '<a href="' . $url . '" title="' . $title . '">' . $title . '</a>';
                        $result = $cache[$nid];
                    }
                } else {
                    $result = $cache[$nid];
                }
                break;
            case 'D':
            case 'L':
                if(!isset($cache[$nid])) {
                    // not in cache array
                    $tbldls = $pntable['downloads_downloads'];
                    $coldls = $pntable['downloads_downloads_column'];
                    
                    $sql = 'SELECT ' . $coldls['title'] . ' FROM ' . $tbldls . ' WHERE ' . $coldls['lid'] . '=' . pnVarPrepForStore($id);
                    $res = $dbconn->Execute($sql);
                    if($dbconn->ErrorNo() != 0) {
                        $result = '<em>DOWNLOAD' . $nid . '</em>';
                    } else {
                        list($title) = $res->fields;
                        if($type=='D') {
                            $url   = pnVarPrepForDisplay('index.php?name=Downloads&req=viewdownload&cid=' . $id);
                        } else {
                            $url   = pnVarPrepForDisplay('index.php?name=Downloads&req=getit&lid=' . $id);
                        }
                        $title = pnVarPrepForDisplay($title);
                        $cache[$nid] = '<a href="' . $url . '" title="' . $title . '">' . $title . '</a>';
                        $result = $cache[$nid];
                    }
                } else {
                    $result = $cache[$nid];
                }
                break;
            default:
                $result = '<em>DOWNLOAD' . $nid . '</em>';
        }
    }
    return $result;
}

?>