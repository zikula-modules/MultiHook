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
// Purpose of file:  MultiHook Needle API
// ----------------------------------------------------------------------

/**
 * pagesetter needle
 * @param $args['nid'] needle id
 * @return array()
 */
function MultiHook_needleapi_pagesetter($args)
{
    $nid = $args['nid'];
    unset($args);

    // cache the results
    static $cache;
    if(!isset($cache)) {
        $cache = array();
    } 
   
    if(!pnModAvailable('pagesetter')) {
        $result = '<em>PAGESETTER' . $nid . '</em>';
    } else {
        if(!isset($cache[$nid])) {
        
            // Get arguments from argument array
            
            // nid is like pid_tid
            $temp = explode('_', $nid);
            $pid = $temp[0];
            $tid = $temp[1];
            $pub = pnModAPIFunc('pagesetter',
                                'user',
                                'getPub',
                                array('tid'    => $tid,
                                      'pid'    => $pid,
                                      'format' => 'user'));
            if(!is_array($pub)) {
                $result = '<em>PAGESETTER' . $nid . '</em>';
            } else {          
                $url = pnModURL('pagesetter', 'user', 'viewpub',
                                array('tid' => $tid,
                                      'pid' => $pid));
                $pubtitle = pnVarPrepForDisplay($pub['title']);
                $cache[$nid] = '<a href="' . pnVarPrepForDisplay($url) . '" title="' . $pubtitle . '">' . $pubtitle . '</a>';
                $result = $cache[$nid];
            }
        } else {
            $result = $cache[$nid];
        }
               
    }
    return $result;

}

/**
 * pagesetter needle info
 * @param none
 * @return string with short usage description
 */
function MultiHook_needleapi_pagesetter_info($args)
{
    $info = 'PAGESETTER{tid}_{pid}';
    return $info;
}

?>