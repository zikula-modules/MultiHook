<?php
/**
 * Multihook
 *
 * @copyright (c) 2001-now, Multihook Development Team
 * @link http://code.zikula.org/multihook
 * @version $Id$
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Multihook
 */

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

    $dom = ZLanguage::getModuleDomain('MultiHook');

    if(!empty($nid)) {
        if(!isset($cache[$nid])) {
            // not in cache array
            if(pnModAvailable('Downloads')) {
                $modinfo = pnModGetInfo(pnModGetIDFromName('Downloads'));
                // check for the version of the Downloads module
                // if >=2.0 -> true
                // if  <2.0 -> false - not supported in MultiHook 5.0 or later!
                if(version_compare($modinfo['version'], '2.0', '>=')) {
                    // nid is like C-##, D-##, L-## or S
                    $temp = explode('-', $nid);
                    $type = '';
                    if(is_array($temp)) {
                        $type = $temp[0];
                        $id   = $temp[1];
                    }

                    pnModDBInfoLoad('Downloads', 'Downloads');
                    switch($type) {
                        case 'C':
                            if(SecurityUtil::checkPermission('Downloads::Category', $id . '::', ACCESS_READ)) {
                                $dl20categoryinfo = pnModAPIFunc('Downloads', 'user', 'category_info',
                                                                 array('cid' => $id));
                                if(is_array($dl20categoryinfo)) {
                                    $url   = DataUtil::formatForDisplay(pnModURL('Downloads', 'user', 'view', array('cid' => $id)));
                                    $title = DataUtil::formatForDisplay($dl20categoryinfo['title']);
                                    $desc  = DataUtil::formatForDisplay($dl20categoryinfo['description']);
                                    $cache[$nid] = '<a href="' . $url . '" title="' . $desc . '">' . $title . '</a>';
                                } else {
                                    $cache[$nid] = '<em>' . DataUtil::formatForDisplay(__('Unknown category', $dom) . ' (' . $id . ')') .'</em>';
                                }
                            } else {
                                $cache[$nid] = '<em>' . DataUtil::formatForDisplay(__('No authorisation for category', $dom) . ' (' . $id . ')') .'</em>';
                            }
                            break;
                        case 'D':
                        case 'L':
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
                                                                                                'authid' => SecurityUtil::generateAuthKey('Downloads')));
                                }
                                $url   = DataUtil::formatForDisplay($url);
                                $title = DataUtil::formatForDisplay($dl20downloadinfo[0]['title']);
                                $desc  = DataUtil::formatForDisplay($dl20downloadinfo[0]['description']);
                                $cache[$nid] = '<a href="' . $url . '" title="' . $desc . '">' . $title . '</a>';
                            } else {
                                $cache[$nid] = '<em>' . DataUtil::formatForDisplay(__('unknown download', $dom) . ' (' . $id . ')') . '</em>';
                            }
                            break;
                        case 'S':
                            // link to main page
                            $cache[$nid] = '<a href="index.php?name=Downloads" title="' . DataUtil::formatForDisplay(__('downloads', $dom)) . '">' . DataUtil::formatForDisplay(__('downloads', $dom)) . '</a>';
                            break;
                        default:
                            $cache[$nid] = '<em>' . DataUtil::formatForDisplay(__('Unknown parameter at pos.1 (C, D, L or S)', $dom)) . '</em>';
                    }
                } else {
                    // no Downloads 2.0 or later
                    $cache[$nid] = '<em>' . DataUtil::formatForDisplay(__('Downloads 2.0 or later needed', $dom)) . '</em>';
                }
            } else {
                $cache[$nid] = '<em>' . DataUtil::formatForDisplay(__('Downloads not available', $dom)) . '</em>';
            }
        }
        $result = $cache[$nid];
    } else {
        $result = '<em>' . DataUtil::formatForDisplay(__('No needle ID', $dom)) . '</em>';
    }
    return $result;
}
