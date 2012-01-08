<?php
/**
 * Multihook
 *
 * @copyright (c) 2001-now, Multihook Development Team
 * @link http://code.zikula.org/multihook
 * @version $Id: htmlpage.php 221 2009-12-09 07:46:02Z herr.vorragend $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Multihook
 */

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

            if(ModUtil::available('htmlpages')) {
                // nid is the pid

                ModUtil::dbInfoLoad('htmlpages');
                $pntable = DBUtil::getTables();

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
                    $url   = DataUtil::formatForDisplay(ModUtil::url('htmlpages', 'user', 'display', array('pid' => $nid)));
                    $title = DataUtil::formatForDisplay($obj['title']);
                    $cache[$nid] = '<a href="' . $url . '" title="' . $title . '">' . $title . '</a>';
                } else {
                    $cache[$nid] = '<em>' . DataUtil::formatForDisplay(__('Unknown page', $dom) . ' (' . $nid . ')') . '</em>';
                }

            } else {
                $cache[$nid] = '<em>' . DataUtil::formatForDisplay(__('htmlpages not available', $dom)) . '</em>';
            }
        }
        $result = $cache[$nid];
    } else {
        $result = '<em>' . DataUtil::formatForDisplay(__('No needle ID', $dom)) . '</em>';
    }
    return $result;

}
