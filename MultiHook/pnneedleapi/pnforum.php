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

    if(!pnModAvailable('pnForum')) {
        $result = '<em>PNFORUM' . $nid . '</em>';
    } else {
        // Get arguments from argument array
        
        // nid is like pid_tid
        $temp = explode('_', $nid);
        $type = $temp[0];
        $id   = $temp[1];

        pnModDBInfoLoad('pnForum');
        $dbconn =& pnDBGetConn(true);
        $pntable =& pnDBGetTables();
        
        switch($type) {
            case 'F':
                if(!isset($cache[$nid])) {
                    // not in cache array
                    $tblforums = $pntable['pnforum_forums'];
                    $colforums = $pntable['pnforum_forums_column'];
                    
                    $sql = 'SELECT ' . $colforums['forum_name'] . ' FROM ' . $tblforums . ' WHERE ' . $colforums['forum_id'] . '=' . pnVarPrepForStore($id);
                    $res = $dbconn->Execute($sql);
                    if($dbconn->ErrorNo() != 0) {
                        $result = '<em>PNFORUM' . $nid . '</em>';
                    } else {
                        list($title) = $res->fields;
                        $url   = pnVarPrepForDisplay(pnModURL('pnForum', 'user', 'viewforum', array('forum' => $id)));
                        $title = pnVarPrepForDisplay($title);
                        $cache[$nid] = '<a href="' . $url . '" title="' . $title . '">' . $title . '</a>';
                        $result = $cache[$nid];
                    }
                } else {
                    $result = $cache[$nid];
                }
                break;
            case 'T':
                if(!isset($cache[$nid])) {
                    // not in cache array
                    $tbltopics = $pntable['pnforum_topics'];
                    $coltopics = $pntable['pnforum_topics_column'];
                    
                    $sql = 'SELECT ' . $coltopics['topic_title'] . ' FROM ' . $tbltopics . ' WHERE ' . $coltopics['topic_id'] . '=' . pnVarPrepForStore($id);
                    $res = $dbconn->Execute($sql);
                    if($dbconn->ErrorNo() != 0) {
                        $result = '<em>PNFORUM' . $nid . '</em>';
                    } else {
                        list($title) = $res->fields;
                        $url   = pnVarPrepForDisplay(pnModURL('pnForum', 'user', 'viewtopic', array('topic' => $id)));
                        $title = pnVarPrepForDisplay($title);
                        $cache[$nid] = '<a href="' . $url . '" title="' . $title . '">' . $title . '</a>';
                        $result = $cache[$nid];
                    }
                } else {
                    $result = $cache[$nid];
                }
                break;
            default:
                $result = '<em>PNFORUM' . $nid . '</em>';
        }
    }
    return $result;
    
}

/**
 * pnforum needle info
 * @param none
 * @return string with short usage description
 */
function MultiHook_needleapi_pnforum_info($args)
{
    $info = 'PNFORUM{F_forumid|T_topicid}';
    return $info;
}

?>