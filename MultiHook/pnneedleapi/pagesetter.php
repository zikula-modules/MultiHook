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

    pnModLangLoad('MultiHook', 'pagesetter');
    if(!empty($nid)) {
        if(!isset($cache[$nid])) {
            // not in cache array
            if(pnModAvailable('pagesetter')) {
                // nid is like tid-pid or tid only
                $temp = explode('-', $nid);
                switch(count($temp)) {
                    case 1:
                        // $temp[0] is treated as tid
                        if(SecurityUtil::checkPermission('pagesetter', $temp[0] . '::', ACCESS_READ)) {
                            $pubInfo =  pnModAPIFunc('pagesetter',
                                                     'admin',
                                                     'getPubTypeInfo',
                                                     array('tid' => $temp[0]));
                            
                            if(is_array($pubInfo)) {
                                $url = DataUtil::formatForDisplay(pnModURL('pagesetter', 'user', 'view', array('tid' => $temp[0])));
                                $pubtitle = DataUtil::formatForDisplay($pubInfo['publication']['title']);
                                $pubdesc  = DataUtil::formatForDisplay($pubInfo['publication']['description']); 
                                $cache[$nid] = '<a href="' . $url . '" title="' . $pubdesc . '">' . $pubtitle . '</a>';
                            } else {
                                $cache[$nid] = '<em>' . DataUtil::formatForDisplay(_MH_PS_UNKNOWNTID . ' (' . $temp[0] . ')') . '</em>';
                            }
                        } else {
                            $cache[$nid] = '<em>' . DataUtil::formatForDisplay(_MH_PS_NOAUTHFORTID . ' (' . $temp[0] . ')') . '</em>';
                        }
                        break;
                    case 2:
                        // $temp[0] is treated as tid
                        // $temp[1] is treated as pid
                        if(SecurityUtil::checkPermission('pagesetter::', $temp[0] . ':' . $temp[1] . ':', ACCESS_READ)) {
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
                                $pubtitle = DataUtil::formatForDisplay($pub['title']);
                                $cache[$nid] = '<a href="' . DataUtil::formatForDisplay($url) . '" title="' . $pubtitle . '">' . $pubtitle . '</a>';
                            } else {
                                $cache[$nid] = '<em>' . DataUtil::formatForDisplay(_MH_PS_UNKNOWNPID . ' (' . $nid . ')') . '</em>';
                            }
                        } else {
                            $cache[$nid] = '<em>' . DataUtil::formatForDisplay(_MH_PS_NOAUTHFORPID . ' (' . $nid . ')') . '</em>';
                        }
                        break;
                    default:
                        $cache[$nid] = '<em>' . DataUtil::formatForDisplay(_MH_PS_WRONGNEEDLEID) . '</em>';
                }
            }
        }
        $result = $cache[$nid];
    } else {
        $result = '<em>' . DataUtil::formatForDisplay(_MH_PS_NONEEDLEID) . '</em>';
    }
    return $result;

}


?>