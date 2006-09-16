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
 * pnforum needle
 * @param $args['nid'] needle id
 * @return array()
 */
function MultiHook_needleapi_pnforum($args)
{
    // Get arguments from argument array
    $nid = $args['nid'];
    unset($args);
    
    // cache the results
    static $cache;
    if(!isset($cache)) {
        $cache = array();
    } 

    $result = '<em title="' . pnVarPrepForDisplay(sprintf(_MH_NEEDLEDATAERROR, $nid, 'pnForum')) . '">PNFORUM' . $nid . '</em>';
    if(!isset($cache[$nid])) {
        // not in cache array
        // set the default
        $cache[$nid] = $result;
        if(pnModAvailable('pnForum')) {
            
            // nid is like C_## or T_##
            $temp = explode('_', $nid);
            $type = '';
            if(is_array($temp) && count($temp)==2) {
                $type = $temp[0];
                $id   = $temp[1];
            }
            
            pnModDBInfoLoad('pnForum');
            $dbconn =& pnDBGetConn(true);
            $pntable =& pnDBGetTables();

            switch($type) {
                case 'F':
                    $tblforums = $pntable['pnforum_forums'];
                    $colforums = $pntable['pnforum_forums_column'];
                    
                    $sql = 'SELECT ' . $colforums['forum_name'] . ' FROM ' . $tblforums . ' WHERE ' . $colforums['forum_id'] . '=' . pnVarPrepForStore($id);
                    $res = $dbconn->Execute($sql);
                    if($dbconn->ErrorNo()==0 && !$res->EOF) {
                        list($title) = $res->fields;
                        $url   = pnVarPrepForDisplay(pnModURL('pnForum', 'user', 'viewforum', array('forum' => $id)));
                        $title = pnVarPrepForDisplay($title);
                        $cache[$nid] = '<a href="' . $url . '" title="' . $title . '">' . $title . '</a>';
                    }
                    break;
                case 'T':
                    $tbltopics = $pntable['pnforum_topics'];
                    $coltopics = $pntable['pnforum_topics_column'];
                    
                    $sql = 'SELECT ' . $coltopics['topic_title'] . ' FROM ' . $tbltopics . ' WHERE ' . $coltopics['topic_id'] . '=' . pnVarPrepForStore($id);
                    $res = $dbconn->Execute($sql);
                    if($dbconn->ErrorNo()==0 && !$result->EOF) {
                        list($title) = $res->fields;
                        $url   = pnVarPrepForDisplay(pnModURL('pnForum', 'user', 'viewtopic', array('topic' => $id)));
                        $title = pnVarPrepForDisplay($title);
                        $cache[$nid] = '<a href="' . $url . '" title="' . $title . '">' . $title . '</a>';
                    }
                    break;
                default:
                    // $cache[$nid] = $result; is already done
            }
        }
        $result = $cache[$nid];
    }
    return $result;
    
}

?>