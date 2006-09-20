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
 * htmlpage needle
 * @param $args['nid'] needle id
 * @return array()
 */
function MultiHook_needleapi_htmlpage($args)
{
    // Get arguments from argument array
    $nid = $args['nid'];
    unset($args);
    
    // cache the results
    static $cache;
    if(!isset($cache)) {
        $cache = array();
    } 

    $result = '<em title="' . pnVarPrepForDisplay(sprintf(_MH_NEEDLEDATAERROR, $nid, 'htmlpages')) . '">HTMLPAGE' . $nid . '</em>';
    if(!isset($cache[$nid])) {
        // not in cache array
        // set the default
        $cache[$nid] = $result;
        if(pnModAvailable('htmlpages')) {
            pnModLangLoad('MultiHook', 'htmlpage');
            // nid is the pid
            
            pnModDBInfoLoad('htmlpages');
            $dbconn = pnDBGetConn(true);
            $pntable = pnDBGetTables();
            $htmlpagestable = $pntable['htmlpages'];
            $htmlpagescolumn = &$pntable['htmlpages_column'];
        
            $sql = "SELECT $htmlpagescolumn[title]
                    FROM $htmlpagestable
                    WHERE $htmlpagescolumn[pid] = '" . (int)pnVarPrepForStore($nid) . "'";
            $res = $dbconn->Execute($sql);
            if($dbconn->ErrorNo()==0 && !$res->EOF) {
                list($title) = $res->fields;
                if(pnSecAuthAction(0, 'htmlpages::', "$title::$nid", ACCESS_READ)) {
                    $url   = pnVarPrepForDisplay(pnModURL('htmlpages', 'user', 'display', array('pid' => $nid)));
                    $title = pnVarPrepForDisplay($title);
                    $cache[$nid] = '<a href="' . $url . '" title="' . $title . '">' . $title . '</a>';
                } else {
                    $cache[$nid] = '<em>' . pnVarPrepForDisplay(_MH_HP_NOAUTHFORPAGE . ' (' . $nid . ')') . '</em>';
                }
            } else {
                $cache[$nid] = '<em>' . pnVarPrepForDisplay(_MH_HP_UNKNOWNPAGE . ' (' . $nid . ')') . '</em>';
            }

        } else {
            $cache[$nid] = '<em>' . pnVarPrepForDisplay(_MH_HP_NOTAVAILABLE) . '</em>';
        }
        $result = $cache[$nid];
    } else {
        $result = '<em>' . pnVarPrepForDisplay(_MH_HP_NONEEDLEID) . '</em>';
    }
    return $result;
    
}

?>