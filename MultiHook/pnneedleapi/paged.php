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
    $result = '<em title="' . DataUtil::formatForDisplay(sprintf(_MH_NEEDLEDATAERROR, $nid, 'PagEd')) . '">PAGED' . $nid . '</em>';
    if(!isset($cache[$nid])) {
        // not in cache array
        // set the default
        $cache[$nid] = $result;
        if(pnModAvailable('PagEd')) {
            // nid is like P_## or T_##
            $temp = explode('-', $nid);
            $type = '';
            if(is_array($temp) && count($temp)==2) {
                $type = $temp[0];
                $id   = $temp[1];
            }
    
            pnModDBInfoLoad('PagEd');
            switch($type) {
                case 'P':
                    $obj = DBUtil::selectObjectByID('paged_titles', $id, 'page_id')
                    if($obj <> false) {
                        $url   = DataUtil::formatForDisplay('modules.php?op=modload&name=PagEd&file=index&page_id=' $obj['page_id']);
                        $title = DataUtil::formatForDisplay($obj['title']);
                        $cache[$nid] = '<a href="' . $url . '" title="' . $title . '">' . $title . '</a>';
                    } else {
                        $cache[$nid] = 'PagEd: unknown publication ' . DataUtil::formatForDisplay($id);
                    }
                    break;
                case 'T':
                    $obj = DBUtil::selectObjectByID('paged_topics', $id, 'topic_id')
                    if($obj <> false) {
                        $url   = DataUtil::formatForDisplay('modules.php?op=modload&name=PagEd&file=index&topic_id=' . $obj['topic_id']);
                        $title = DataUtil::formatForDisplay($obj['topic_title']);
                        $desc  = DataUtil::formatForDisplay($obj['topic_description']);
                        $cache[$nid] = '<a href="' . $url . '" title="' . $desc . '">' . $title . '</a>';
                    } else {
                        $cache[$nid] = 'PagEd: unknown topic ' . DataUtil::formatForDisplay($id);
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