<?php
/**
 * Multihook
 *
 * @copyright (c) 2001-now, Multihook Development Team
 * @link http://code.zikula.org/multihook
 * @version $Id: weblink.php 221 2009-12-09 07:46:02Z herr.vorragend $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Multihook
 */

class MultiHook_Needles_WebLink extends Zikula_AbstractHelper
{
    public function info()
    {
        $info = array('info'          => 'WEBLINK{C-weblinkcategoryid|D-weblinkid|L-weblinkid|S}',
                      'description'   => $this->__('Insert a link from the Web_Links module'),
                      'casesensitive' => true,
                      'needle'        => 'WEBLINK',
                      'inspect'       => false);
        return $info;
    }

    /**
     * weblink needle
     * @param $args['nid'] needle id
     * @return array()
     */
    public static function needle($args)
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
                if(ModUtil::available('Web_Links')) {
                    // nid is like C_##, D_## or L_##
                    $temp = explode('-', $nid);
                    $type = '';
                    if(is_array($temp)) {
                        $type = $temp[0];
                        $id   = $temp[1];
                    }
    
                    ModUtil::dbInfoLoad('Web_Links');
                    $dbconn = DBConnectionStack::getConnection(true);
                    $pntable = DBUtil::getTables();
    
                    switch($type) {
                        case 'C':
                            $tblwlcats = $pntable['links_categories'];
                            $colwlcats = $pntable['links_categories_column'];
    
                            $sql = 'SELECT ' . $colwlcats['title'] . ', ' . $colwlcats['cdescription'] . ' FROM ' . $tblwlcats . ' WHERE ' . $colwlcats['cat_id'] . '=' . DataUtil::formatForStore($id);
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
    
                            $sql = 'SELECT ' . $colwls['title'] . ', ' . $colwls['description'] . ' FROM ' . $tblwls . ' WHERE ' . $colwls['lid'] . '=' . DataUtil::formatForStore($id);
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
}