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

    $dom = ZLanguage::getModuleDomain('MultiHook');
    if(!empty($nid)) {
        if(!isset($cache[$nid])) {
            // not in cache array

            if(pnModAvailable('htmlpages')) {
                // nid is the pid

                pnModDBInfoLoad('htmlpages');
                $pntable = pnDBGetTables();

                $permfilter[] = array ('realm'            =>  0,
                                       'component_left'   =>  'htmlpages',
                                       'component_middle' =>  '',
                                       'component_right'  =>  '',
                                       'instance_left'    =>  'title',
                                       'instance_middle'  =>  '',
                                       'instance_right'   =>  'pid',
                                       'level'            =>  ACCESS_READ);

                $obj = DBUtil::selectObjectByID('htmlpages', $nid, 'pid', null, $permFilter);

                if($obj <> false) {
                    $url   = DataUtil::formatForDisplay(pnModURL('htmlpages', 'user', 'display', array('pid' => $nid)));
                    $title = DataUtil::formatForDisplay($obj['title']);
                    $cache[$nid] = '<a href="' . $url . '" title="' . $title . '">' . $title . '</a>';
                } else {
                    $cache[$nid] = '<em>' . DataUtil::formatForDisplay(__('Unkown page', $dom) . ' (' . $nid . ')') . '</em>';
                }

            } else {
                $cache[$nid] = '<em>' . DataUtil::formatForDisplay(__('htmlpages not available', $dom)) . '</em>';
            }
        }
        $result = $cache[$nid];
    } else {
        $result = '<em>' . DataUtil::formatForDisplay(__('no needle id', $dom)) . '</em>';
    }
    return $result;

}
