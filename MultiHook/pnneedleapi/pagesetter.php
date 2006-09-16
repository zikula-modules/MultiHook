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
   
    $result = '<em title="' . pnVarPrepForDisplay(sprintf(_MH_NEEDLEDATAERROR, $nid, 'pagesetter')) . '">PAGESETTER' . $nid . '</em>';
    if(!isset($cache[$nid])) {
        // not in cache array
        // set the default
        $cache[$nid] = $result;
        if(pnModAvailable('pagesetter')) {
            // nid is like tid_pid or tid only
            $temp = explode('_', $nid);
            switch(count($temp)) {
                case 1:
                    // $temp[0] is treated as tid
                    $pubInfo =  pnModAPIFunc('pagesetter',
                                             'admin',
                                             'getPubTypeInfo',
                                             array('tid' => $temp[0]));

                    list($url,
                         $pubtitle,
                         $pubdesc) = pnVarPrepForDisplay(pnModURL('pagesetter', 'user', 'view',
                                                                  array('tid' => $temp[0])),
                                                         $pubInfo['publication']['title'],
                                                         $pubInfo['publication']['description']);
                    
                    $cache[$nid] = '<a href="' . $url . '" title="' . $pubdesc . '">' . $pubtitle . '</a>';
                    break;
                case 2:
                    // $temp[0] is treated as tid
                    // $temp[1] is treated as pid
                    $pub = pnModAPIFunc('pagesetter',
                                        'user',
                                        'getPub',
                                        array('tid'    => $temp[0],
                                              'pid'    => $temp[1],
                                              'format' => 'user'));
                    if(is_array($pub)) {
                        $url = pnModURL('pagesetter', 'user', 'viewpub',
                                        array('tid' => $temp[0],
                                              'pid' => $temp[1]));
                        $pubtitle = pnVarPrepForDisplay($pub['title']);
                        $cache[$nid] = '<a href="' . pnVarPrepForDisplay($url) . '" title="' . $pubtitle . '">' . $pubtitle . '</a>';
                    }
                    break;
                default:
            }

        }
        $result = $cache[$nid];
    }
    return $result;

}

?>