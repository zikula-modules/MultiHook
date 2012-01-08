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
 * paged needle
 * @param $args['nid'] needle id
 * @return array()
 */
function MultiHook_needleapi_paged($args)
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
    // set the default for errors of all kind
    $result = '<em title="' . DataUtil::formatForDisplay(__f('Error! Could not read needle data for \'%1$s\' or module \'%2$s\' is not active.', array($nid, 'PagEd'), $dom)) . '">PAGED' . $nid . '</em>';
    if(!isset($cache[$nid])) {
        // not in cache array
        // set the default
        $cache[$nid] = $result;
        if(pnModAvailable('PagEd')) {
            // nid is like P_## or T_##
            $temp = explode('-', $nid);
            $type = '';
            if(is_array($temp) && count($temp)==2) {
                $type = $temp[0];
                $id   = $temp[1];
            }

            pnModDBInfoLoad('PagEd');
            switch($type) {
                case 'P':
                    $obj = DBUtil::selectObjectByID('paged_titles', $id, 'page_id');
                    if($obj <> false) {
                        $url   = DataUtil::formatForDisplay('modules.php?op=modload&name=PagEd&file=index&page_id=' . $obj['page_id']);
                        $title = DataUtil::formatForDisplay($obj['title']);
                        $cache[$nid] = '<a href="' . $url . '" title="' . $title . '">' . $title . '</a>';
                    } else {
                        $cache[$nid] = 'PagEd: unknown publication ' . DataUtil::formatForDisplay($id);
                    }
                    break;
                case 'T':
                    $obj = DBUtil::selectObjectByID('paged_topics', $id, 'topic_id');
                    if($obj <> false) {
                        $url   = DataUtil::formatForDisplay('modules.php?op=modload&name=PagEd&file=index&topic_id=' . $obj['topic_id']);
                        $title = DataUtil::formatForDisplay($obj['topic_title']);
                        $desc  = DataUtil::formatForDisplay($obj['topic_description']);
                        $cache[$nid] = '<a href="' . $url . '" title="' . $desc . '">' . $title . '</a>';
                    } else {
                        $cache[$nid] = 'PagEd: unknown topic ' . DataUtil::formatForDisplay($id);
                    }
                    break;
                default:
                    // default already set before
            }
        }
        $result = $cache[$nid];
    }
    return $result;

}
