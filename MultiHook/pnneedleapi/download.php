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
 * download needle
 * @param $args['nid'] needle id
 * @return array()
 */
function MultiHook_needleapi_download($args)
{
    // Get arguments from argument array
    $nid = $args['nid'];
    unset($args);

    // cache the results
    static $cache;
    if(!isset($cache)) {
        $cache = array();
    } 

    $result = '<em title="' . pnVarPrepForDisplay(sprintf(_MH_NEEDLEDATAERROR, $nid, 'Downloads')) . '">DOWNLOAD' . $nid . '</em>';
    if(!empty($nid)) {
        if(!isset($cache[$nid])) {
            // not in cache array
            if(pnModAvailable('Downloads')) {
                pnModLangLoad('MultiHook', 'download');
                $modinfo = pnModGetInfo(pnModGetIDFromName('Downloads'));
                // check for the version of the Downloads module
                // if >=2.0 -> true
                // if  <2.0 -> false
                $is_dl20 = version_compare($modinfo['version'], '2.0', '>=');
                // nid is like C_##, D_## or L_##
                $temp = explode('_', $nid);
                $type = '';
                if(is_array($temp) && count($temp)==2) {
                    $type = $temp[0];
                    $id   = $temp[1];
                }
            
                pnModDBInfoLoad('Downloads');
                
                switch($type) {
                    case 'C':
                        if($is_dl20) {
                            // Downloads 2.0 or later
                            if(pnSecAuthAction(0, 'Downloads::Category', $id . '::', ACCESS_READ)) {
                                $dl20categoryinfo = pnModAPIFunc('Downloads', 'user', 'category_info',
                                                                 array('cid' => $id));
                                if(is_array($dl20categoryinfo)) {
                                    list($url,
                                         $title,
                                         $desc) = pnVarPrepForDisplay(pnModURL('Downloads', 'user', 'view', array('cid' => $id)),
                                                                      $dl20categoryinfo['title'],
                                                                      $dl20categoryinfo['description']);
                                    $cache[$nid] = '<a href="' . $url . '" title="' . $desc . '">' . $title . '</a>';
                                } else {
                                    $cache[$nid] = '<em>' . pnVarPrepForDisplay(_MH_DL_UNKNOWNCATEGORY . ' (' . $id . ')') .'</em>';
                                }
                            } else {
                                $cache[$nid] = '<em>' . pnVarPrepForDisplay(_MH_DL_NOAUTHFORCATEGORY . ' (' . $id . ')') .'</em>';
                            }
                        } else {
                            $dbconn =& pnDBGetConn(true);
                            $pntable =& pnDBGetTables();
                            $tbldlcats = $pntable['downloads_categories'];
                            $coldlcats = $pntable['downloads_categories_column'];
                            
                            $sql = 'SELECT ' . $coldlcats['title'] . ', ' . $coldlcats['cdescription'] . ' FROM ' . $tbldlcats . ' WHERE ' . $coldlcats['cid'] . '=' . pnVarPrepForStore($id);
                            $res = $dbconn->Execute($sql);
                            if($dbconn->ErrorNo()==0 && !$res->EOF) {
                                list($title, $desc) = $res->fields;
	                            if(pnSecAuthAction(0, 'Downloads::Category', $title . '::' . $id, ACCESS_READ)) {
                                    list($url,
                                         $title,
                                         $desc) = pnVarPrepForDisplay('index.php?name=Downloads&req=viewdownload&cid=' . $id,
                                                                      $title,
                                                                      $desc);
                                    $cache[$nid] = '<a href="' . $url . '" title="' . $desc . '">' . $title . '</a>';
                                } else {
                                    $cache[$nid] = '<em>' . pnVarPrepForDisplay(_MH_DL_NOAUTHFORCATEGORY . ' (' . $id . ')') .'</em>';
                                }
                            } else {
                                $cache[$nid] = '<em>' . pnVarPrepForDisplay(_MH_DL_UNKNOWNCATEGORY . ' (' . $id . ')') .'</em>';
                            }
                        }
                        break;
                    case 'D':
                    case 'L':
                        if($is_dl20) {
                            // Downloads 2.0 or later
                            $dl20downloadinfo = pnModAPIFunc('Downloads','user','get_download_info',
                        									  array('lid' => $id,
                        									  		'cid' => 0,
                        									  		'sort_active' => false, 
                        									  		'sortby' => 0,  
                        									  		'cclause' => 0,  
                        									  		'get_by_cid' => false, 
                        									  		'get_by_lid' => true,
                        											'get_by_date' => false,
                        											'sort_date' => 0));
                            if(is_array($dl20downloadinfo) && count($dl20downloadinfo)>0) {
                                // securedownload (==captcha) is enabled we cannot use type=L, we have to force D instead
                                if($type=='D' || pnModGetVar('downloads', 'securedownload')=='yes') {
                                    $url = pnModURL('Downloads', 'user', 'display', array('lid' => $id));
                                } else {
                                    $url = pnModURL('Downloads', 'user', 'prep_hand_out', array('lid'    => $id,
                                                                                                'authid' => pnSecGenAuthKey('Downloads')));
                                }
                                list($url,
                                     $title,
                                     $desc) = pnVarPrepForDisplay($url,
                                                                  $dl20downloadinfo[0]['title'],
                                                                  $dl20downloadinfo[0]['description']);
                                $cache[$nid] = '<a href="' . $url . '" title="' . $desc . '">' . $title . '</a>';
                            } else {
                                $cache[$nid] = '<em>' . pnVarPrepForDisplay(_MH_DL_UNKNOWNDOWNLOAD . ' (' . $id . ')') . '</em>';
                            }
                        } else {
                            $dbconn =& pnDBGetConn(true);
                            $pntable =& pnDBGetTables();
                            $tbldls = $pntable['downloads_downloads'];
                            $coldls = $pntable['downloads_downloads_column'];
                            
                            $sql = 'SELECT ' . $coldls['title'] . ', ' . $coldls['description'] . ' FROM ' . $tbldls . ' WHERE ' . $coldls['lid'] . '=' . pnVarPrepForStore($id);
                            $res = $dbconn->Execute($sql);
                            if($dbconn->ErrorNo()==0 && !$res->EOF) {
                                list($title, $desc) = $res->fields;
                                if(pnSecAuthAction(0, 'Downloads::Item', $title . '::' . $id, ACCESS_READ)) {
                                    if($type=='D') {
                                        $url = 'index.php?name=Downloads&req=viewdownload&cid=' . $id;
                                    } else {
                                        $url = 'index.php?name=Downloads&req=getit&lid=' . $id;
                                    }
                                    list($url,
                                         $title,
                                         $desc)  = pnVarPrepForDisplay($url, $title, $desc);
                                    $cache[$nid] = '<a href="' . $url . '" title="' . $desc . '">' . $title . '</a>';
                                } else {
                                    $cache[$nid] = '<em>' . pnVarPrepForDisplay(_MH_DL_NOAUTHFORDOWNLOAD . ' (' . $id . ')') .'</em>';
                                }
                            } else { 
                                $cache[$nid] = '<em>' . pnVarPrepForDisplay(_MH_DL_UNKNOWNDOWNLOAD . ' (' . $id . ')') .'</em>';
                            }
                        }
                        break;
                    default:
                        $cache[$nid] = '<em>' . pnVarPrepForDisplay(_MH_DL_UNKNOWNTYPE) . '</em>';
                }
            } else {
                $cache[$nid] = '<em>' . pnVarPrepForDisplay(_MH_DL_NOTAVAILABLE) . '</em>';
            }
            $result = $cache[$nid];
        }
    } else {
        $result = '<em>' . pnVarPrepForDisplay(_MH_DL_NONEEDLEID) . '</em>';
    }
    return $result;
}

?>