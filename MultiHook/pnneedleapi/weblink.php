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
// Purpose of file:  MultiHook needle API
// ----------------------------------------------------------------------

/**
 * weblink needle
 * @param $args['nid'] needle id
 * @return array()
 */
function MultiHook_needleapi_weblink($args)
{
    // Get arguments from argument array
    $nid = $args['nid'];
    unset($args);

    // cache the results
    static $cache;
    if(!isset($cache)) {
        $cache = array();
    } 

    $result = '<em title="' . pnVarPrepForDisplay(sprintf(_MH_NEEDLEDATAERROR, $nid, 'Web_Links')) . '">WEBLINK' . $nid . '</em>';
    if(!isset($cache[$nid])) {
        // not in cache array
        // set the default
        $cache[$nid] = $result;
        if(pnModAvailable('Web_Links')) {
            // nid is like C_##, D_## or L_##
            $temp = explode('_', $nid);
            $type = '';
            if(is_array($temp) && count($temp)==2) {
                $type = $temp[0];
                $id   = $temp[1];
            }
        
            pnModDBInfoLoad('Web_Links');
            $dbconn =& pnDBGetConn(true);
            $pntable =& pnDBGetTables();
            
            switch($type) {
                case 'C':
                    $tblwlcats = $pntable['links_categories'];
                    $colwlcats = $pntable['links_categories_column'];
                    
                    $sql = 'SELECT ' . $colwlcats['title'] . ', ' . $colwlcats['cdescription'] . ' FROM ' . $tblwlcats . ' WHERE ' . $colwlcats['cat_id'] . '=' . pnVarPrepForStore($id);
                    $res = $dbconn->Execute($sql);
                    if($dbconn->ErrorNo()==0 && !$res->EOF) {
                        list($title, $desc) = $res->fields;
                        list($url,
                             $title,
                             $desc) = pnVarPrepForDisplay('index.php?name=Web_Links&req=viewlink&cid=' . $id,
                                                          $title,
                                                          $desc);
                        $cache[$nid] = '<a href="' . $url . '" title="' . $desc . '">' . $title . '</a>';
                    }
                    break;
                case 'D':
                case 'L':
                    $tblwls = $pntable['links_links'];
                    $colwls = $pntable['links_links_column'];
                    
                    $sql = 'SELECT ' . $colwls['title'] . ', ' . $colwls['description'] . ' FROM ' . $tblwls . ' WHERE ' . $colwls['lid'] . '=' . pnVarPrepForStore($id);
                    $res = $dbconn->Execute($sql);
                    if($dbconn->ErrorNo()==0 && !$res->EOF) {
                        list($title, $desc) = $res->fields;
                        if($type=='D') {
                            $url = 'index.php?name=Web_Links&req=viewlinkdetails&lid=' . $id;
                        } else {
                            $url = 'index.php?name=Web_Links&req=visit&lid=' . $id;
                        }
                        list($url,
                             $title,
                             $desc)  = pnVarPrepForDisplay($url, $title, $desc);
                        $cache[$nid] = '<a href="' . $url . '" title="' . $desc . '">' . $title . '</a>';
                    }
                    break;
                default:
                    // default already set before
            }
        }
        $result = $cache[$nid];
    }
    return $result;
}

?>