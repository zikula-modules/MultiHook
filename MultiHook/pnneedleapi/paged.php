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
 * paged needle
 * @param $args['nid'] needle id
 * @return array()
 */
function MultiHook_needleapi_paged($args)
{
    // Get arguments from argument array
    $nid = $args['nid'];
    unset($args); 
    
    // cache the results
    static $cache;
    if(!isset($cache)) {
        $cache = array();
    } 

    // set the default for errors of all kind
    $result = '<em title="' . pnVarPrepForDisplay(sprintf(_MH_NEEDLEDATAERROR, $nid, 'PagEd')) . '">PAGED' . $nid . '</em>';
    if(!isset($cache[$nid])) {
        // not in cache array
        // set the default
        $cache[$nid] = $result;
        if(pnModAvailable('PagEd')) {
            // nid is like P_## or T_##
            $temp = explode('_', $nid);
            $type = '';
            if(is_array($temp) && count($temp)==2) {
                $type = $temp[0];
                $id   = $temp[1];
            }
    
            pnModDBInfoLoad('PagEd');
            $dbconn =& pnDBGetConn(true);
            $pntable =& pnDBGetTables();
            
            switch($type) {
                case 'P':
                    $titlestable = $pntable['paged_titles'];
                    $sql = 'SELECT title, topic_id from ' . $titlestable . ' WHERE page_id=' . pnVarPrepForStore($id);
                    $res = $dbconn->Execute($sql);
                    if($dbconn->ErrorNo()==0 && !$res->EOF) {
                        list($title, $topic_id) = $res->fields;
                        $url   = pnVarPrepForDisplay('modules.php?op=modload&name=PagEd&file=index&page_id=' . pnVarPrepForDisplay($id));
                        $title = pnVarPrepForDisplay($title);
                        $cache[$nid] = '<a href="' . $url . '" title="' . $title . '">' . $title . '</a>';
                    } else {
                        $cache[$nid] = 'PagEd: unknown publication ' . pnVarPrepForDisplay($id);
                    }
                    break;
                case 'T':
                    $topicstable = $pntable['paged_topics'];
                    $sql = 'SELECT topic_title, topic_description from ' . $topicstable . ' WHERE topic_id=' . pnVarPrepForStore($id);
                    $res = $dbconn->Execute($sql);
                    if($dbconn->ErrorNo()==0 && !$res->EOF) {
                        list($title, $desc) = $res->fields;
                        $url   = pnVarPrepForDisplay('modules.php?op=modload&name=PagEd&file=index&topic_id=' . pnVarPrepForDisplay($id));
                        $title = pnVarPrepForDisplay($title);
                        $desc  = pnVarPrepForDisplay($desc);
                        $cache[$nid] = '<a href="' . $url . '" title="' . $desc . '">' . $title . '</a>';
                    } else {
                        $cache[$nid] = 'PagEd: unknown topic ' . pnVarPrepForDisplay($id);
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