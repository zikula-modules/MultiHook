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

    pnModLangLoad('MultiHook', 'pnforum');
    if(!empty($nid)) {
        if(!isset($cache[$nid])) {
            // not in cache array
            // set the default
            $cache[$nid] = $result;
            if(pnModAvailable('pnForum')) {
                
                // nid is like F_## or T_##
                $temp = explode('-', $nid);
                $type = '';
                if(is_array($temp) && count($temp)==2) {
                    $type = $temp[0];
                    $id   = $temp[1];
                }
                
                include_once 'modules/pnForum/common.php';
                pnModDBInfoLoad('pnForum');
                $dbconn =& pnDBGetConn(true);
                $pntable =& pnDBGetTables();
        
                switch($type) {
                    case 'F':
                        $tblforums = $pntable['pnforum_forums'];
                        $colforums = $pntable['pnforum_forums_column'];
                        
                        $sql = 'SELECT ' . $colforums['forum_name'] . ',
                                       ' . $colforums['cat_id'] . '
                                FROM   ' . $tblforums . '
                                WHERE  ' . $colforums['forum_id'] . '=' . (int)pnVarPrepForStore($id);
                        $res = $dbconn->Execute($sql);
                        if($dbconn->ErrorNo()==0 && !$res->EOF) {
                            list($title, $cat_id) = $res->fields;
                            if(allowedtoreadcategoryandforum($cat_id, $id)) {
                                $url   = pnVarPrepForDisplay(pnModURL('pnForum', 'user', 'viewforum', array('forum' => $id)));
                                $title = pnVarPrepForDisplay($title);
                                $cache[$nid] = '<a href="' . $url . '" title="' . $title . '">' . $title . '</a>';
                            } else {
                                $cache[$nid] = '<em>' . pnVarPrepForDisplay(_MH_PNF_NOAUTHFORFORUM . ' (' . $id . ')') . '</em>';
                            }
                        } else {
                            $cache[$nid] = '<em>' . pnVarPrepForDisplay(_MH_PNF_UNKNOWNFORUM . ' (' . $id . ')') . '</em>';
                        }
                        break;
                    case 'T':
                        $tbltopics = $pntable['pnforum_topics'];
                        $coltopics = $pntable['pnforum_topics_column'];
                        $tblforums = $pntable['pnforum_forums'];
                        $colforums = $pntable['pnforum_forums_column'];
                        
                        $sql = 'SELECT    ' . $coltopics['topic_title'] . ',
                                          ' . $coltopics['forum_id'] . ',
                                          ' . $colforums['cat_id'] . ' 
                                FROM      ' . $tbltopics . '
                                LEFT JOIN ' . $tblforums . '
                                ON        ' . $colforums['forum_id'] . '=' . $coltopics['forum_id'] . '
                                WHERE     ' . $coltopics['topic_id'] . '=' . pnVarPrepForStore($id);
                        $res = $dbconn->Execute($sql);
                        if($dbconn->ErrorNo()==0 && !$result->EOF) {
                            list($title, $forum_id, $cat_id) = $res->fields;
                            if(allowedtoreadcategoryandforum($cat_id, $forum_id)) {
                                $url   = pnVarPrepForDisplay(pnModURL('pnForum', 'user', 'viewtopic', array('topic' => $id)));
                                $title = pnVarPrepForDisplay($title);
                                $cache[$nid] = '<a href="' . $url . '" title="' . $title . '">' . $title . '</a>';
                            } else {
                                $cache[$nid] = '<em>' . pnVarPrepForDisplay(_MH_PNF_NOAUTHFORTOPIC . ' (' . $id . ')') . '</em>';
                            }
                        } else {
                            $cache[$nid] = '<em>' . pnVarPrepForDisplay(_MH_PNF_UNKNOWNTOPIC . ' (' . $id . ')') . '</em>';
                        }
                        break;
                    default:
                        $cache[$nid] = '<em>' . pnVarPrepForDisplay(_MH_PNF_UNKNOWNTYPE) . '</em>';
                }
            } else {
                $cache[$nid] = '<em>' . pnVarPrepForDisplay(_MH_PNF_NOTAVAILABLE) . '</em>';
            }    
        }
        $result = $cache[$nid];
    } else {
        $result = '<em>' . pnVarPrepForDisplay(_MH_PNF_NONEEDLEID) . '</em>';
    }
    return $result;
    
}

?>