<?php
/**
 * Multihook
 *
 * @copyright (c) 2001-now, Multihook Development Team
 * @link http://code.zikula.org/multihook
 * @version $Id: pagesetter.php 221 2009-12-09 07:46:02Z herr.vorragend $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Multihook
 */

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

    $dom = ZLanguage::getModuleDomain('MultiHook');
    if(!empty($nid)) {
        if(!isset($cache[$nid])) {
            // not in cache array
            if(ModUtil::available('pagesetter')) {
                // nid is like tid-pid or tid only
                $temp = explode('-', $nid);
                switch(count($temp)) {
                    case 1:
                        // $temp[0] is treated as tid
                        if(SecurityUtil::checkPermission('pagesetter', $temp[0] . '::', ACCESS_READ)) {
                            $pubInfo =  ModUtil::apiFunc('pagesetter',
                                                     'admin',
                                                     'getPubTypeInfo',
                                                     array('tid' => $temp[0]));

                            if(is_array($pubInfo)) {
                                $url = DataUtil::formatForDisplay(ModUtil::url('pagesetter', 'user', 'view', array('tid' => $temp[0])));
                                $pubtitle = DataUtil::formatForDisplay($pubInfo['publication']['title']);
                                $pubdesc  = DataUtil::formatForDisplay($pubInfo['publication']['description']);
                                $cache[$nid] = '<a href="' . $url . '" title="' . $pubdesc . '">' . $pubtitle . '</a>';
                            } else {
                                $cache[$nid] = '<em>' . DataUtil::formatForDisplay(__('Unknown Pagesetter TID', $dom) . ' (' . $temp[0] . ')') . '</em>';
                            }
                        } else {
                            $cache[$nid] = '<em>' . DataUtil::formatForDisplay(__('No authorisation for Pagesetter TID', $dom) . ' (' . $temp[0] . ')') . '</em>';
                        }
                        break;
                    case 2:
                        // $temp[0] is treated as tid
                        // $temp[1] is treated as pid
                        if(SecurityUtil::checkPermission('pagesetter::', $temp[0] . ':' . $temp[1] . ':', ACCESS_READ)) {
                            $pub = ModUtil::apiFunc('pagesetter',
                                                'user',
                                                'getPub',
                                                array('tid'    => $temp[0],
                                                      'pid'    => $temp[1],
                                                      'format' => 'user'));
                            if(is_array($pub)) {
                                $url = ModUtil::url('pagesetter', 'user', 'viewpub',
                                                array('tid' => $temp[0],
                                                      'pid' => $temp[1]));
                                $pubtitle = DataUtil::formatForDisplay($pub['title']);
                                $cache[$nid] = '<a href="' . DataUtil::formatForDisplay($url) . '" title="' . $pubtitle . '">' . $pubtitle . '</a>';
                            } else {
                                $cache[$nid] = '<em>' . DataUtil::formatForDisplay(__('Unknown Pagesetter PID', $dom) . ' (' . $nid . ')') . '</em>';
                            }
                        } else {
                            $cache[$nid] = '<em>' . DataUtil::formatForDisplay(__('No authorisation for Pagesetter PID', $dom) . ' (' . $nid . ')') . '</em>';
                        }
                        break;
                    default:
                        $cache[$nid] = '<em>' . DataUtil::formatForDisplay(__('Incorrect needle ID', $dom)) . '</em>';
                }
            }
        }
        $result = $cache[$nid];
    } else {
        $result = '<em>' . DataUtil::formatForDisplay(__('No needle ID', $dom)) . '</em>';
    }
    return $result;

}
