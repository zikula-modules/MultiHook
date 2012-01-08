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
 * weblink needle
 * @param $args['nid'] needle id
 * @return array()
 */
function MultiHook_needleapi_weblink($args)
{
    $dom = ZLanguage::getModuleDomain('MultiHook');
    // Get arguments from argument array
    $nid = $args['nid'];
    unset($args);

    // cache the results
    static $cache;
    if(!isset($cache)) {
        $cache = array();
    }

    if(!empty($nid)) {
        if(!isset($cache[$nid])) {
            // not in cache array
            if(pnModAvailable('Web_Links')) {
                // nid is like C_##, D_## or L_##
                $temp = explode('-', $nid);
                $type = '';
                if(is_array($temp)) {
                    $type = $temp[0];
                    $id   = $temp[1];
                }

                pnModDBInfoLoad('Web_Links');
                $dbconn =& pnDBGetConn(true);
                $pntable =& pnDBGetTables();

                switch($type) {
                    case 'C':
                        $tblwlcats = $pntable['links_categories'];
                        $colwlcats = $pntable['links_categories_column'];

                        $sql = 'SELECT ' . $colwlcats['title'] . ', ' . $colwlcats['cdescription'] . ' FROM ' . $tblwlcats . ' WHERE ' . $colwlcats['cat_id'] . '=' . pnVarPrepForStore($id);
                        $res = $dbconn->Execute($sql);
                        if($dbconn->ErrorNo()==0 && !$res->EOF) {
                            list($title, $desc) = $res->fields;
                            if(SecurityUtil::checkPermission('Web Links::Category', $title . '::' . $id, ACCESS_READ)) {
                                $url   = DataUtil::formatForDisplay('index.php?name=Web_Links&req=viewlink&cid=' . $id);
                                $title = DataUtil::formatForDisplay($title);
                                $desc  = DataUtil::formatForDisplay($desc);
                                $cache[$nid] = '<a href="' . $url . '" title="' . $desc . '">' . $title . '</a>';
                            } else {
                                $cache[$nid] = '<em>' . DataUtil::formatForDisplay(__('No authorisation for Web link category', $dom) . ' (' . $id . ')') .'</em>';
                            }
                        } else {
                            $cache[$nid] = '<em>' . DataUtil::formatForDisplay(__('Unknown Web link category', $dom) . ' (' . $id . ')') .'</em>';
                        }
                        break;
                    case 'D':
                    case 'L':
                        $tblwls = $pntable['links_links'];
                        $colwls = $pntable['links_links_column'];

                        $sql = 'SELECT ' . $colwls['title'] . ', ' . $colwls['description'] . ' FROM ' . $tblwls . ' WHERE ' . $colwls['lid'] . '=' . pnVarPrepForStore($id);
                        $res = $dbconn->Execute($sql);
                        if($dbconn->ErrorNo()==0 && !$res->EOF) {
                            list($title, $desc) = $res->fields;
                            if (SecurityUtil::checkPermission('Web Links::Link', ':' . $title . ':' . $id, ACCESS_READ)) {
                                if($type=='D') {
                                    $url = 'index.php?name=Web_Links&req=viewlinkdetails&lid=' . $id;
                                } else {
                                    $url = 'index.php?name=Web_Links&req=visit&lid=' . $id;
                                }
                                $url   = DataUtil::formatForDisplay($url);
                                $title = DataUtil::formatForDisplay($title);
                                $desc  = DataUtil::formatForDisplay($desc);
                                $cache[$nid] = '<a href="' . $url . '" title="' . $desc . '">' . $title . '</a>';
                            } else {
                                $cache[$nid] = '<em>' . DataUtil::formatForDisplay(__('No authorisation for Web link', $dom) . ' (' . $id . ')') .'</em>';
                            }
                        } else {
                            $cache[$nid] = '<em>' . DataUtil::formatForDisplay(__('Unknown Web link', $dom) . ' (' . $id . ')') .'</em>';
                        }
                        break;
                    case 'S':
                        // show link to main page
                        $cache[$nid] = '<a href="index.php?name=Web_Links" title="' . DataUtil::formatForDisplay(__('weblinks', $dom)) . '">' . DataUtil::formatForDisplay(__('weblinks', $dom)) . '</a>';
                        break;
                    default:
                        $cache[$nid] = '<em>' . DataUtil::formatForDisplay(__('Unknown parameter at pos.1 (C, D, L or S)', $dom)) . '</em>';
                }
            } else {
                $cache[$nid] = '<em>' . DataUtil::formatForDisplay(__('Web_Links not available', $dom)) . '</em>';
            }
        }
        $result = $cache[$nid];
    } else {
        $result = '<em>' . DataUtil::formatForDisplay(__('No needle ID', $dom)) . '</em>';
    }
    return $result;
}
